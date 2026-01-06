<?php

declare(strict_types=1);

/**
 * Announcements plugin (DB version)
 * - Provides install/uninstall hooks
 * - Exposes admin entry
 */
 
 $docroot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '.', '/\\');
$front = $docroot . '/public/plugins/announcements/page.php';
if (is_file($front)) {
    require_once $front;
}

return [
    'admin' => 'admin/main.php',

    'install' => static function (db $db): bool {
        $conn = $db->connect();
        if (!$conn instanceof mysqli) {
            return false;
        }

        $sql = "
            CREATE TABLE IF NOT EXISTS announcements (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                title VARCHAR(255) NOT NULL,
                body MEDIUMTEXT NOT NULL,
                published TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                KEY idx_published_created (published, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";

        return $conn->query($sql) === true;
    },

    'uninstall' => static function (db $db): bool {
        $conn = $db->connect();
        if (!$conn instanceof mysqli) {
            return false;
        }

        return $conn->query("DROP TABLE IF EXISTS announcements") === true;
    },
];

