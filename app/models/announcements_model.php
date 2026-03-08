<?php
// path: /app/models/announcements_model.php

class announcements_model extends model {

    protected $table = 'announcements';

    public function get_all() {
        // Now $this->table is properly defined for the query
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC")->fetchAll();
    }
    
    // Fetch only published rows
    public function get_active() {
        $sql = "SELECT * FROM announcements WHERE published = 1 ORDER BY created_at DESC";
        return $this->fetchAll($sql);
    }

    // Insert new announcement
    public function add($data) {
        return $this->insert('announcements', [
            'title' => $data['title'],
            'body'  => $data['body'],
            'published' => 1
        ]);
    }
    
    public function get_latest($limit = 5) {
        $sql = "SELECT * FROM announcements 
            WHERE published = 1 
            ORDER BY created_at DESC 
            LIMIT :limit";
    
        // Using your base model's fetchAll with the limit parameter
        return $this->fetchAll($sql, ['limit' => $limit]);
    }
    
    public function get_latest_single() {
        $sql = "SELECT * FROM announcements 
            WHERE published = 1 
            ORDER BY created_at DESC 
            LIMIT 1";
    
        return $this->fetch($sql);
    }
}
