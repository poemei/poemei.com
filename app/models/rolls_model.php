<?php
// path: /app/models/rolls_model.php

class rolls_model extends model 
{
    public function get_all() 
    {
        return $this->query("SELECT * FROM rolls ORDER BY created_at DESC")->fetchAll();
    }

    public function get_by_slug_with_relations($slug) 
    {
        $roll = $this->query("SELECT * FROM rolls WHERE slug = ?", [$slug])->fetch();
        if (!$roll) return null;

        if (!empty($roll['supersedes_id'])) {
            $sup = $this->query("SELECT slug, title FROM rolls WHERE id = ?", [$roll['supersedes_id']])->fetch();
            $roll['superseded_by_slug'] = $sup['slug'] ?? null;
            $roll['superseded_by_title'] = $sup['title'] ?? null;
        }

        if (!empty($roll['parent_roll_id'])) {
            $parent = $this->query("SELECT slug, title FROM rolls WHERE roll_id = ?", [$roll['parent_roll_id']])->fetch();
            $roll['related_slug'] = $parent['slug'] ?? null;
            $roll['related_title'] = $parent['title'] ?? null;
        }
        return $roll;
    }

    public function save_new($data) 
    {
        $today = date('Y-m-d');
        $res = $this->query("SELECT COUNT(id) as total FROM rolls WHERE DATE(created_at) = ?", [$today])->fetch();
        $next_id = ($res['total'] ?? 0) + 1;

        return $this->query(
            "INSERT INTO rolls (roll_id, slug, title, content, parent_roll_id, status) VALUES (?, ?, ?, ?, ?, 'active')",
            [$today . '_' . $next_id, $data['slug'], $data['title'], $data['content'], $data['parent_roll_id']]
        );
    }

    public function update_roll($data)
    {
        return $this->query(
            "UPDATE rolls SET title = ?, slug = ?, content = ?, parent_roll_id = ?, supersedes_id = ?, status = ? WHERE id = ?",
            [
                $data['title'], 
                $data['slug'], 
                $data['content'], 
                $data['parent_roll_id'], 
                $data['supersedes_id'], 
                $data['status'], 
                $data['id']
            ]
        );
    }
}
