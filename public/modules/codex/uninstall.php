<?php

declare(strict_types=1);

/**
 * Codex module uninstaller.
 *
 * Destructive uninstall:
 * - Drops `codex` (depends on codex_topics via FK)
 * - Drops `codex_topics`
 *
 * @param mysqli $conn
 * @return array{ok:bool,error?:string}
 */
function codex_uninstall(mysqli $conn): array
{
    // Drop entries table first (FK dependency)
    if (!$conn->query('DROP TABLE IF EXISTS codex')) {
        return [
            'ok' => false,
            'error' => 'codex_uninstall: drop codex failed: ' . (string) $conn->error,
        ];
    }

    // Then drop topics table
    if (!$conn->query('DROP TABLE IF EXISTS codex_topics')) {
        return [
            'ok' => false,
            'error' => 'codex_uninstall: drop codex_topics failed: ' . (string) $conn->error,
        ];
    }

    return ['ok' => true];
}

