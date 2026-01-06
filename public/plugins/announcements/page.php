<?php

declare(strict_types=1);

/**
 * Announcements plugin (DB)
 * Front-end helper: render latest published announcement (ONE item)
 *
 * Usage in theme/home:
 *   <?php if (function_exists('announcements_latest')) { announcements_latest(); } ?>
 */

if (!function_exists('announcements_latest')) {
    function announcements_latest(): void
    {
        global $db;

        if (!$db instanceof db) {
            return;
        }

        $conn = $db->connect();
        if (!$conn instanceof mysqli) {
            return;
        }

        $sql = "
            SELECT title, body, created_at
            FROM announcements
            WHERE published = 1
            ORDER BY created_at DESC
            LIMIT 1
        ";

        $res = $conn->query($sql);
        if (!$res instanceof mysqli_result) {
            return;
        }

        $row = $res->fetch_assoc();
        $res->free();

        if (!is_array($row)) {
            return;
        }

        $title = function_exists('e') ? e((string) ($row['title'] ?? '')) : htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $date  = function_exists('e') ? e((string) ($row['created_at'] ?? '')) : htmlspecialchars((string) ($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8');

        $bodyRaw = (string) ($row['body'] ?? '');
        $bodyEsc = function_exists('e') ? e($bodyRaw) : htmlspecialchars($bodyRaw, ENT_QUOTES, 'UTF-8');
        $body    = nl2br($bodyEsc);

        echo '<section class="announcements-latest container my-4">';
        echo '  <div class="card">';
        echo '    <div class="card-body">';
        echo '      <div class="d-flex justify-content-between align-items-start mb-2">';
        echo '        <strong>' . $title . '</strong>';
        echo '        <small class="text-muted">' . $date . '</small>';
        echo '      </div>';
        echo '      <div class="small">' . $body . '</div>';
        echo '    </div>';
        echo '  </div>';
        echo '</section>';
    }
}

