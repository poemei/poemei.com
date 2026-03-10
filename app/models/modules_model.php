<?php
// path: /app/models/modules_model.php

class modules_model extends model
{
    public function get_all()
    {
        return $this->query(
            "SELECT * FROM modules WHERE is_active = 1 ORDER BY title ASC"
        )->fetchAll();
    }
    
    public function get_by_slug($slug) {
    $sql = "SELECT p.*, m.file_path as image_path 
            FROM posts p 
            LEFT JOIN media m ON p.featured_image_id = m.id 
            WHERE p.slug = ? AND p.published = 1 LIMIT 1";
    return $this->fetch($sql, [$slug]);
    }

    public function create($data)
    {
        return $this->query(
            "INSERT INTO modules (slug, title, content, module_type, meta_data) VALUES (?, ?, ?, ?, ?)",
            [$data['slug'], $data['title'], $data['content'], $data['module_type'], $data['meta_data']]
        );
    }

    public function update_module($id, $data)
    {
        return $this->query(
            "UPDATE modules SET title = ?, slug = ?, content = ?, module_type = ?, meta_data = ? WHERE id = ?",
            [$data['title'], $data['slug'], $data['content'], $data['module_type'], $data['meta_data'], $id]
        );
    }

    public function delete($id)
    {
        return $this->query("DELETE FROM modules WHERE id = ?", [$id]);
    }
}
