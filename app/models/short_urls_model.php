<?php
declare(strict_types=1);

/**
 * Short URLs Model
 */

final class short_urls_model extends model
{
    public function get_by_code(string $code): ?array
    {
        $stmt = $this->query(
            "SELECT * FROM short_urls WHERE code=? AND is_active=1 LIMIT 1",
            [$code]
        );

        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function increment_click(int $id): void
    {
        $this->query(
            "UPDATE short_urls SET clicks = clicks + 1 WHERE id=? LIMIT 1",
            [$id]
        );
    }
}

