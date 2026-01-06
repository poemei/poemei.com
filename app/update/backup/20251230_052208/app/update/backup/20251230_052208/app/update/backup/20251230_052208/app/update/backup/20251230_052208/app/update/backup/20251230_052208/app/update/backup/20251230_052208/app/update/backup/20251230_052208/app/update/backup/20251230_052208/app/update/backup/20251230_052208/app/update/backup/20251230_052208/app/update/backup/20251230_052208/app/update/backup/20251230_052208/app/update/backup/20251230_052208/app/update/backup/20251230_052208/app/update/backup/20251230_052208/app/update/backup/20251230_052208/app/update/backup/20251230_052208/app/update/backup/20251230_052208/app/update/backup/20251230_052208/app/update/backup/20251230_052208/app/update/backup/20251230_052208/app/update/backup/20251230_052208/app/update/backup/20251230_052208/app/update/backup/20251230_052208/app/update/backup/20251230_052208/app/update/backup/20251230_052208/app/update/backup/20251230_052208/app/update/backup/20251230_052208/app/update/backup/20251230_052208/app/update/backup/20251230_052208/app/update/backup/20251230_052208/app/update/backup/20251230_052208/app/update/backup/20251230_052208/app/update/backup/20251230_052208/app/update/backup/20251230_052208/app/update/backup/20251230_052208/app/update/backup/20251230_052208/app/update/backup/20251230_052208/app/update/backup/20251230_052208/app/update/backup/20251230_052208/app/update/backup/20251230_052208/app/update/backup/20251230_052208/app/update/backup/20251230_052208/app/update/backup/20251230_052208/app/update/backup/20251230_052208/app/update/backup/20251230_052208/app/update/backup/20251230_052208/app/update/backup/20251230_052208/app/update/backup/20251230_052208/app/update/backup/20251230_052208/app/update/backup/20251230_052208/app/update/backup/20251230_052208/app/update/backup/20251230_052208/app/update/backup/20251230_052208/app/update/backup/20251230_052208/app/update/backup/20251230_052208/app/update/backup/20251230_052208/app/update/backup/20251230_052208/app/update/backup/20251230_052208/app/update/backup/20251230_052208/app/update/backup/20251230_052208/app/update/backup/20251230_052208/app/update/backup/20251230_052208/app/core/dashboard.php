<?php

declare(strict_types=1);

/**
 * Chaos CMS DB — Admin Dashboard
 * /admin
 */

/**
 * Dashboard version info
 * Always define $ver so the view is safe
 */
$ver = null;

if (class_exists('version')) {
    $ver = version::get_status();
}

(function (): void {
    global $auth, $db;

    if (!$auth instanceof auth || !$db instanceof db) {
        http_response_code(500);
        echo '<div class="admin-wrap"><div class="admin-card"><h2>Admin Error</h2><p>Core auth/db missing.</p></div></div>';
        return;
    }

    if (!$auth->check()) {
        header('Location: /login');
        exit;
    }

    $u = $auth->user();
    $userId = (int)($u['id'] ?? 0);
    $roleId = (int)($u['role_id'] ?? 1);

    // Resolve role label from roles table (fallback if missing)
    $roleLabel = 'User';
    $rows = $db->fetch_all("SELECT id, label FROM roles");
    if (is_array($rows)) {
        foreach ($rows as $r) {
            if ((int)($r['id'] ?? 0) === $roleId) {
                $roleLabel = (string)($r['label'] ?? $roleLabel);
                break;
            }
        }
    }

    // Access rules:
    // - Admin (4): full admin
    // - Editor (2): limited admin (posts/media)
    // - Moderator (3): limited admin (posts/media)
    // - User (1): no admin
    $isAdmin = ($roleId === 4);
    $isEditor = ($roleId === 2);
    $isModerator = ($roleId === 3);

    if (!$isAdmin && !$isEditor && !$isModerator) {
        http_response_code(403);
        echo '<div class="admin-wrap"><div class="admin-card"><h2>Access denied</h2><p>You do not have admin access.</p></div></div>';
        return;
    }

    // Quick counts (safe + simple)
    $count = static function (string $table) use ($db): int {
        $row = $db->fetch("SELECT COUNT(*) AS c FROM {$table}");
        return (int)($row['c'] ?? 0);
    };

    $postsCount   = $count('posts');
    $mediaCount   = $count('media_files');
    $usersCount   = $count('users');
    $modulesCount = $count('modules');
    $pluginsCount = $count('plugins');
    $themesCount  = $count('themes');

    // Card renderer
    $card = static function (string $title, string $desc, string $href, string $meta = ''): void {
        $t = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $d = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
        $h = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
        $m = $meta !== '' ? '<div class="admin-card-meta">' . htmlspecialchars($meta, ENT_QUOTES, 'UTF-8') . '</div>' : '';
        echo '<a class="admin-card" href="' . $h . '">';
        echo '  <div class="admin-card-title">' . $t . '</div>';
        echo '  <div class="admin-card-desc">' . $d . '</div>';
        echo $m;
        echo '</a>';
    };
    
    if (is_array($ver)): ?>
    <section class="admin-card">
        <h3>Version</h3>
        <div class="small">
            Current: <?= htmlspecialchars($ver['current'] ?? 'unknown'); ?><br>
            Latest: <?= htmlspecialchars($ver['latest'] ?? 'unknown'); ?><br>
            Status: <?= htmlspecialchars($ver['status'] ?? 'unknown'); ?>
        </div>
    </section>
<?php endif; ?>
    <div class="admin-wrap">
        <div class="admin-head">
            <div>
                <h1 class="admin-h1">Dashboard</h1>
                <div class="admin-sub">
                    Logged in as <strong><?= htmlspecialchars(ucfirst((string)($u['username'] ?? 'user')), ENT_QUOTES, 'UTF-8'); ?></strong>
                    · Role: <strong><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>
        </div>

        <div class="admin-grid">
            <?php
            // Editors + Moderators get only these:
            $card('Posts', 'Write, publish, and manage posts.', '/admin?action=posts', (string)$postsCount);
            $card('Media', 'Upload and manage media library.', '/admin?action=media', (string)$mediaCount);

            // Admin-only cards
            if ($isAdmin) {
                $card('Pages', 'Create and manage site pages.', '/admin?action=pages');
                $card('Modules', 'Install/enable modules.', '/admin?action=modules', (string)$modulesCount);
                $card('Plugins', 'Install/enable plugins.', '/admin?action=plugins', (string)$pluginsCount);
                $card('Themes', 'Install/enable themes.', '/admin?action=themes', (string)$themesCount);
                $card('Users', 'Manage users and roles.', '/admin?action=users', (string)$usersCount);
                $card('Health', 'System status and checks.', '/admin?action=health');
                $card('Maintenance', 'Rebuild SEO, clear cache, tools.', '/admin?action=maintenance');
                $card('Settings', 'Core site settings.', '/admin?action=settings');
            }
            ?>
        </div>
    </div>

    <style>
        .admin-wrap { max-width: 980px; margin: 0 auto; padding: 18px 14px; }
        .admin-head { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 14px; }
        .admin-h1 { margin: 0; font-size: 1.45rem; }
        .admin-sub { margin-top: 6px; font-size: .9rem; opacity: .8; }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 12px;
        }
        @media (min-width: 700px) {
            .admin-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 980px) {
            .admin-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .admin-card {
            display: block;
            text-decoration: none;
            color: inherit;
            border: 1px solid rgba(0,0,0,.12);
            border-radius: 12px;
            padding: 14px 14px 12px;
            background: #fff;
        }
        .admin-card:hover { border-color: rgba(0,0,0,.25); }

        .admin-card-title { font-weight: 700; margin-bottom: 6px; }
        .admin-card-desc { font-size: .92rem; opacity: .85; line-height: 1.25rem; }
        .admin-card-meta {
            margin-top: 10px;
            font-size: .85rem;
            opacity: .75;
        }
    </style>
    <?php
})();

