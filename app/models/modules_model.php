<?php
// path: /app/models/modules_model.php

class modules_model extends model
{
    public function get_all()
    {
        return $this->query("SELECT * FROM modules ORDER BY title ASC")->fetchAll();
    }
    
    /**
     * Fetch a module/page by slug using PDO 
     */
    public function get_by_slug($slug) {
        // Correcting the Call to undefined method PDOStatement::bind_param() 
        return $this->query("SELECT * FROM modules WHERE slug = ?", [$slug])->fetch();
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
