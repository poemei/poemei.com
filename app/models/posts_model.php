<?php
// path: /app/models/posts_model.php

class posts_model extends model {

    protected $table = 'posts';

    public function get_all() {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC")->fetchAll();
    }

    public function get_by_id($id) {
        return $this->db->query("SELECT * FROM {$this->table} WHERE id = ?", [(int)$id])->fetch();
    }
    
    /**
     * Cite: 2026-0-08:23:29Z
     * join on post_id, post_id does not exists
    */
    public function get_post_with_image($slug)
    {
    $sql = "
        SELECT p.*, m.file_path AS image_path
        FROM posts p
        LEFT JOIN media m ON m.id = p.id
        WHERE p.slug = ?
        LIMIT 1
    ";

    return $this->query($sql, [$slug])->fetch();
    }
    
    public function get_public_feed() {
        $sql = "SELECT p.*, m.file_path as image_path 
                FROM posts p 
                LEFT JOIN media m ON p.featured_image_id = m.id 
                WHERE p.published = 1 
                ORDER BY p.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function get_comments_by_post($post_id)
{
    $sql = "
        SELECT *
        FROM comments
        WHERE post_id = ?
        ORDER BY created_at ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$post_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function update_post($id, $data) {
        $sql = "UPDATE {$this->table} SET title = :title, slug = :slug, body = :body, published = :published WHERE id = :id";
        $data['id'] = (int)$id;
        return $this->db->query($sql, $data);
    }
}
