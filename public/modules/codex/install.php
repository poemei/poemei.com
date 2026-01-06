<?php

declare(strict_types=1);

/**
 * Codex module installer.
 *
 * Creates:
 * - codex_topics
 * - codex
 * Links:
 * - codex.topic_id -> codex_topics.id
 * Seeds:
 * - core dev topics
 *
 * @param mysqli $conn
 * @return array{ok:bool,error?:string}
 */
function codex_install(mysqli $conn): array
{
    // 1) Create codex_topics
    $sqlTopics = "
        CREATE TABLE IF NOT EXISTS codex_topics (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(190) NOT NULL,
            label VARCHAR(255) NOT NULL,
            sort_order INT(11) NOT NULL DEFAULT 0,
            is_public TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ";

    if (!$conn->query($sqlTopics)) {
        return ['ok' => false, 'error' => 'codex_install: create codex_topics failed: ' . (string) $conn->error];
    }

    // 2) Create codex table (if missing)
    $sqlCodex = "
        CREATE TABLE IF NOT EXISTS codex (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(190) NOT NULL,
            title VARCHAR(255) NOT NULL,
            body MEDIUMTEXT NOT NULL,
            format VARCHAR(16) NOT NULL DEFAULT 'md',
            topic_id INT(10) UNSIGNED DEFAULT NULL,
            status TINYINT(1) NOT NULL DEFAULT 1,
            visibility TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_slug (slug),
            KEY idx_codex_topic_id (topic_id),
            KEY idx_status_vis (status, visibility)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ";

    if (!$conn->query($sqlCodex)) {
        return ['ok' => false, 'error' => 'codex_install: create codex failed: ' . (string) $conn->error];
    }

    // 3) Backward-compatible upgrade: ensure topic_id exists (older installs)
    $hasCol = false;
    $res = $conn->query("SHOW COLUMNS FROM codex LIKE 'topic_id'");
    if ($res instanceof mysqli_result) {
        $hasCol = $res->num_rows > 0;
        $res->free();
    }

    if (!$hasCol) {
        if (!$conn->query("ALTER TABLE codex ADD COLUMN topic_id INT(10) UNSIGNED DEFAULT NULL AFTER format")) {
            return ['ok' => false, 'error' => 'codex_install: add codex.topic_id failed: ' . (string) $conn->error];
        }

        @ $conn->query("ALTER TABLE codex ADD KEY idx_codex_topic_id (topic_id)");
    }

    // 4) Add FK relation if not present
    $fkExists = false;

    $fkCheck = $conn->query("
        SELECT CONSTRAINT_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'codex'
          AND COLUMN_NAME = 'topic_id'
          AND REFERENCED_TABLE_NAME = 'codex_topics'
        LIMIT 1
    ");

    if ($fkCheck instanceof mysqli_result) {
        $fkExists = $fkCheck->num_rows > 0;
        $fkCheck->free();
    }

    if (!$fkExists) {
        // FK add can fail silently if storage engine or permissions differ; we don't hard-fail install.
        @ $conn->query("
            ALTER TABLE codex
            ADD CONSTRAINT fk_codex_topic
            FOREIGN KEY (topic_id) REFERENCES codex_topics(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
    }

    // 5) Seed required Codex topics
    $seed = "
        INSERT INTO codex_topics (slug, label, sort_order, is_public)
        VALUES
            ('module-development', 'Module Development', 10, 1),
            ('plugin-development', 'Plugin Development', 20, 1),
            ('theme-development',  'Theme Development',  30, 1)
        ON DUPLICATE KEY UPDATE
            label=VALUES(label),
            sort_order=VALUES(sort_order),
            is_public=VALUES(is_public)
    ";

    if (!$conn->query($seed)) {
        return ['ok' => false, 'error' => 'codex_install: seed topics failed: ' . (string) $conn->error];
    }

    return ['ok' => true];
}

