<?php
// path: /app/models/covens_model.php

class covens_model extends model
{
    /**
     * Fetch all active covens.
     */
    public function get_active_covens()
    {
        $sql = "SELECT id, name, contact_email 
                FROM covens 
                WHERE is_active = 1";

        return $this->fetchAll($sql);
    }

    /**
     * Count structural members for a given coven.
     * Structural ranks: Neophyte through Prioress.
     */
    public function get_structural_count(int $covenId)
{
    $sql = "SELECT COUNT(*) as total
            FROM members
            WHERE coven_id = :coven_id
            AND rank_id BETWEEN :min_rank AND :max_rank";

    $result = $this->fetch($sql, [
        'coven_id' => $covenId,
        'min_rank' => 2,
        'max_rank' => 7
    ]);

    return (int) ($result['total'] ?? 0);
}

}
