<?php

declare(strict_types=1);

function docs_install(mysqli $conn): array
{
    $sql = "
        CREATE TABLE IF NOT EXISTS docs (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(190) NOT NULL,
            title VARCHAR(255) NOT NULL,
            body MEDIUMTEXT NOT NULL,
            status TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ";

    if (!$conn->query($sql)) {
        return [
            'ok' => false,
            'error' => 'docs_install: ' . (string) $conn->error,
        ];
    }

    return [
        'ok' => true,
    ];
}
