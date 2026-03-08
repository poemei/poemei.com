<?php
class recruit_model {
    private $db;

    public function __construct($database_connection) {
        $this->db = $database_connection;
    }

    public function save_application($data) {
        $sql = "INSERT INTO applications (chosen_name, email, password, bio_sex, element, birth_number, rank_target, rank_max_lock, offense_check_passed, compliance_score) 
                VALUES (:name, :email, :pass, :sex, :elem, :num, :target, :lock, :offense, :score)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function create_account($data) {
        $sql = "INSERT INTO accounts (username, password, email, chosen_name, birth_number, element, bio_sex, assigned_rank, rank_lock) 
                VALUES (:user, :pass, :email, :name, :num, :elem, :sex, :rank, :lock)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}
