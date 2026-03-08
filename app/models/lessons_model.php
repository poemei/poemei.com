<?php
// path: /app/models/lessons_model.php

class lessons_model extends model
{
    public function get_all_admin()
    {
        return $this->query(
            "SELECT lessons.*, offices.name AS office_name
             FROM lessons
             LEFT JOIN offices ON lessons.office_id = offices.id
             ORDER BY lessons.is_archived ASC, lessons.created_at DESC"
        )->fetchAll();
    }

    public function create(array $data)
    {
        return $this->query(
            "INSERT INTO lessons (office_id, title, slug, content) VALUES (?, ?, ?, ?)",
            [$data['office_id'], $data['title'], $data['slug'], $data['content']]
        );
    }

    public function update_lesson(array $data)
    {
        return $this->query(
            "UPDATE lessons SET title = ?, slug = ?, content = ?, office_id = ? WHERE id = ?",
            [$data['title'], $data['slug'], $data['content'], $data['office_id'], $data['id']]
        );
    }

    public function toggle_archive(int $id)
    {
        return $this->query(
            "UPDATE lessons SET is_archived = CASE WHEN is_archived = 1 THEN 0 ELSE 1 END WHERE id = ?",
            [$id]
        );
    }

    public function delete(int $id)
    {
        return $this->query("DELETE FROM lessons WHERE id = ?", [$id]);
    }
}
