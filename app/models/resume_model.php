<?php
// path: /app/models/covens_model.php

class resume_model extends model
{
    /**
     * Fetch all resume items.
     */
     public function get_resume(): array
    {
        $sql = "SELECT * FROM resume";
        $result = $this->fetchAll($sql); // Assuming your base model has a db property
    
        $resume = [];
        foreach ($result as $row) {
            $resume[$row['resume_key']] = $row['resume_description'];
        }
        return $resume;
    }
}
