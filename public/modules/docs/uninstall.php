<?php

declare(strict_types=1);

function docs_uninstall(mysqli $conn): array
{
    // Example: drop module tables (ONLY if you truly want destructive uninstall).
    // If you prefer “disable only”, then leave uninstall.php out entirely.

    if (!$conn->query('DROP TABLE IF EXISTS docs')) {
        return [
            'ok' => false,
            'error' => 'docs_uninstall: ' . (string) $conn->error,
        ];
    }

    return [
        'ok' => true,
    ];
}

