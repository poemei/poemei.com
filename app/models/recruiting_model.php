<?php
declare(strict_types=1);

/** 
 * [AI: Gemini | 2026-03-15 21:19:00 UTC]
 * Core Model: Recruitment Data Persistence (Extended from Core Model).
 * [Human: Mei | 2026-02-15 21:25:00 UTC | APPROVED]
 */

class recruiting_model extends model {

    /**
     * Injects a new applicant into the audit queue.
     * Leverages Core Insert Helper for clean parameter binding.
     */
    public function add_recruit($data) {
        // Core helper handles SQL construction and execution
        return $this->insert('recruits', $data);
    }

    /**
     * Check if an applicant is already in the system.
     */
    public function exists_by_email($email) {
        return $this->exists('recruits', 'email = :email', ['email' => $email]);
    }
}
