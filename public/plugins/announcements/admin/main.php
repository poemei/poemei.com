<?php

declare(strict_types=1);

/**
 * Announcements plugin — Admin (DB version)
 * Self-contained CSRF + escaping so it does not depend on core helpers.
 *
 * Uses if available:
 * - e()
 * - flash()
 * - csrf_token()
 * - csrf_ok()
 *
 * Requires:
 * - global $db (instanceof db)
 */

(function (): void {
    global $db;

    // ------------------------------------------------------------
    // Session (for CSRF fallback)
    // ------------------------------------------------------------
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // ------------------------------------------------------------
    // Helpers (local fallbacks)
    // ------------------------------------------------------------
    $esc = static function (string $v): string {
        if (function_exists('e')) {
            return (string) e($v);
        }

        return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    };

    $flash_ok = static function (string $msg): void {
        if (function_exists('flash')) {
            flash('ok', $msg);
        }
    };

    $flash_err = static function (string $msg): void {
        if (function_exists('flash')) {
            flash('err', $msg);
        }
    };

    $csrf_token_local = static function (): string {
        if (!empty($_SESSION['csrf_announcements']) && is_string($_SESSION['csrf_announcements'])) {
            return $_SESSION['csrf_announcements'];
        }

        $_SESSION['csrf_announcements'] = bin2hex(random_bytes(16));
        return (string) $_SESSION['csrf_announcements'];
    };

    $csrf_ok_local = static function (string $token) use ($csrf_token_local): bool {
        $cur = $csrf_token_local();
        if ($token === '' || $cur === '') {
            return false;
        }

        return hash_equals($cur, $token);
    };

    // Prefer core CSRF if present, otherwise local CSRF
    $csrf_token = static function () use ($csrf_token_local): string {
        if (function_exists('csrf_token')) {
            $t = csrf_token();
            return is_string($t) ? $t : $csrf_token_local();
        }

        return $csrf_token_local();
    };

    $csrf_ok = static function (string $token) use ($csrf_ok_local): bool {
        if (function_exists('csrf_ok')) {
            return (bool) csrf_ok($token);
        }

        return $csrf_ok_local($token);
    };

    // ------------------------------------------------------------
    // DB check
    // ------------------------------------------------------------
    if (!$db instanceof db) {
        echo '<div class="container my-3"><div class="alert alert-danger">DB not available.</div></div>';
        return;
    }

    $conn = $db->connect();
    if (!$conn instanceof mysqli) {
        echo '<div class="container my-3"><div class="alert alert-danger">DB connection failed.</div></div>';
        return;
    }

    // Ensure table exists (safe even if install hook already did it)
    $conn->query("
        CREATE TABLE IF NOT EXISTS announcements (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            title VARCHAR(255) NOT NULL,
            body MEDIUMTEXT NOT NULL,
            published TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY idx_published_created (published, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $redirect = static function (): void {
        header('Location: /admin?action=plugin_admin&slug=announcements');
        exit;
    };

    // ------------------------------------------------------------
    // Create
    // ------------------------------------------------------------
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $token = (string) ($_POST['csrf'] ?? '');

        if (!$csrf_ok($token)) {
            $flash_err('Bad CSRF.');
            $redirect();
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $body  = trim((string) ($_POST['body'] ?? ''));
        $pub   = isset($_POST['published']) ? 1 : 0;

        if ($title === '' || $body === '') {
            $flash_err('Title and body are required.');
            $redirect();
        }

        $stmt = $conn->prepare("INSERT INTO announcements (title, body, published) VALUES (?, ?, ?)");
        if (!$stmt instanceof mysqli_stmt) {
            $flash_err('DB prepare failed.');
            $redirect();
        }

        $stmt->bind_param('ssi', $title, $body, $pub);

        if ($stmt->execute()) {
            $flash_ok('Saved.');
        } else {
            $flash_err('Save failed.');
        }

        $stmt->close();
        $redirect();
    }

    // ------------------------------------------------------------
    // List
    // ------------------------------------------------------------
    $items = [];
    $res = $conn->query("SELECT id, created_at, title, published FROM announcements ORDER BY created_at DESC LIMIT 250");
    if ($res instanceof mysqli_result) {
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $res->free();
    }

    $csrf_field = $esc($csrf_token());
    ?>
    <div class="container my-3">
        <small><a href="/admin">Admin</a> &gt;&gt; Announcements</small>
        <h2 class="h5 mt-2 mb-3">Announcements — Admin</h2>

        <form method="post" class="card card-body mb-4">
            <input type="hidden" name="csrf" value="<?= $csrf_field ?>">

            <div class="mb-2">
                <label class="form-label">Title</label>
                <input name="title" class="form-control form-control-sm" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Body</label>
                <textarea name="body" class="form-control form-control-sm" rows="3" required></textarea>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="published" id="pub">
                <label class="form-check-label" for="pub">Published</label>
            </div>

            <button class="btn btn-sm btn-primary">Add</button>
        </form>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td class="text-muted small"><?= $esc((string) ($it['created_at'] ?? '')) ?></td>
                        <td><?= $esc((string) ($it['title'] ?? '')) ?></td>
                        <td>
                            <?php if ((int) ($it['published'] ?? 0) === 1): ?>
                                <span class="badge text-bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="3" class="text-muted small">No announcements yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
})();

