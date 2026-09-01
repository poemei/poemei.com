<?php
/**
 * Letter Model
 * path: /app/models/letter_model.php
 */

class letter_model extends model {

    protected $table = 'letters';

    public function get_all() {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC")->fetchAll();
    }

    public function delete($table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->db->query($sql);
    }

    public function get_mailing_list() {
        // Pulls display_name and email_address from accounts table
        return $this->db->query("SELECT display_name, email_address FROM accounts")->fetchAll();
    }
}
