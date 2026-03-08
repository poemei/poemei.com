<?php

class offices_model extends model
{

    /**
     * Fetch all offices for administrative selection
     */
    public function get_all()
    {
        return $this->query("SELECT * FROM offices ORDER BY name ASC")->fetchAll();
    }
    
    public function get_all_active()
    {
        return $this->query(
            "SELECT * FROM offices WHERE is_active = 1 ORDER BY name ASC"
        )->fetchAll();
    }

    public function get_top_level()
    {
        return $this->query(
            "SELECT * FROM offices WHERE parent_id IS NULL AND is_active = 1 ORDER BY name ASC"
        )->fetchAll();
    }

    public function get_all_admin()
    {
        return $this->query(
            "SELECT * FROM offices ORDER BY created_at DESC"
        )->fetchAll();
    }

    public function get_by_slug($slug)
    {
        return $this->query(
            "SELECT * FROM offices WHERE slug = ? LIMIT 1",
            [$slug]
        )->fetch();
    }

    public function get_children(int $parent_id)
    {
        return $this->query(
            "SELECT * FROM offices WHERE parent_id = ? AND is_active = 1 ORDER BY name ASC",
            [$parent_id]
        )->fetchAll();
    }

    public function get_lessons_by_office($office_id)
    {
        return $this->query(
            "SELECT * FROM lessons WHERE office_id = ? ORDER BY created_at DESC",
            [$office_id]
        )->fetchAll();
    }

    public function create(array $data)
    {
        return $this->query(
            "INSERT INTO offices (name, slug, description, parent_id, is_active)
             VALUES (?, ?, ?, ?, 1)",
            [
                $data['name'], 
                $data['slug'], 
                $data['description'],
                !empty($data['parent_id']) ? (int)$data['parent_id'] : null
            ]
        );
    }

    public function toggle_active(int $id)
    {
        return $this->query(
            "UPDATE offices
             SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END
             WHERE id = ?",
            [$id]
        );
    }

    public function update_description(int $id, string $description)
    {
        return $this->query(
            "UPDATE offices SET description = ? WHERE id = ?",
            [$description, $id]
        );
    }

    public function get_director(int $office_id)
    {
        return $this->query(
            "SELECT username
             FROM accounts
             WHERE office_id = ? AND user_level >= 7
             LIMIT 1",
            [$office_id]
        )->fetch();
    }

    public function get_staff()
    {
        return $this->query(
            "SELECT id, username FROM accounts WHERE user_level >= 1"
        )->fetchAll();
    }

    public function assign_director(int $office_id, int $account_id)
    {
        return $this->query(
            "UPDATE accounts
             SET office_id = ?, user_level = 7
             WHERE id = ?",
            [$office_id, $account_id]
        );
    }
}
