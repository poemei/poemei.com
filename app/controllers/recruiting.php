<?php
/** 
 * [AI: Gemini | 2026-03-15 21:15:00 UTC]
 * [Human: Mei | 2026-03-15 21:20:00 UTC | APPROVE] 
 * Core Controller: Recruiting
 * Standardized to align with MVC index/method routing.
 */

class recruiting extends controller {
    
    protected $recruit_model;

    public function __construct() {
        // Initialize the model and mailer following Core standards
        $this->recruit_model = $this->model('recruiting_model');
        require_once APPROOT . '/lib/mailer.php';
    }

    /**
     * Default method: GET /recruiting
     */
    public function index() {
        $data = [
            'title' => 'Core Developer Recruitment'
        ];

        $this->view('recruiting/index', $data);
    }

    /**
     * Submission handler: POST /recruiting/submit
     */
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            // Standard Gatekeeper: Check for existing entries
            if ($this->recruit_model->exists_by_email($email)) {
                $this->error_page("Entry Denied. Identity already logged for audit.");
                return;
            }

            $data = [
                'uuid' => trim(bin2hex(random_bytes(16))),
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS),
                'email' => $email,
                'github_username' => filter_input(INPUT_POST, 'github', FILTER_SANITIZE_SPECIAL_CHARS),
                'project_history' => filter_input(INPUT_POST, 'projects', FILTER_SANITIZE_SPECIAL_CHARS),
                'is_approved' => 0,
                'status' => 'pending'
            ];

            if ($this->recruit_model->add_recruit($data)) {
                $this->send_confirmation($data);
                $this->view('recruiting/success');
            } else {
                $this->error_page("Intake Failed. Database integrity error.");
            }
        } else {
            // Redirect back to index if accessed via GET
            header('location: ' . URLROOT . '/recruiting');
        }
    }

    /**
     * Private helper for PHPMailer logic
     */
    private function send_confirmation($data) {
        try {
            $mailerFactory = new mailer();
            $mail = $mailerFactory->create();

            $mail->addAddress($data['email'], $data['name']);
            $mail->Subject = "Audit Logged: Recruitment Application Received";
            
            $mail->Body = "<h1>Audit Logged</h1>
                           <p>Your credentials have been logged for audit.</p>
                           <p><strong>Compliance is the watchword.</strong></p>";
            
            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
        }
    }
}
