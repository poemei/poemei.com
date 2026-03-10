<?php
// path: /app/controllers/auth.php

class auth extends controller {

    public function index($url = []) {
        $method = $url[1] ?? 'login';
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->login();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = $this->model('accounts_model');
            // Authenticating via USERNAME - No assumptions.
            $user = $model->authenticate($_POST['username'], $_POST['password']);

            if ($user) {
                session_regenerate_id();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_level'] = $user['user_level']; // RESTORED: This fixed the admin lockout
                $_SESSION['role'] = $user['role'];
                
                header("Location: /admin");
                exit;
            } else {
                $data['error'] = "Invalid Credentials.";
            }
        }
        $this->view('auth/login', $data ?? []);
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: /login");
        exit;
    }

    public function forgot_password() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $model = $this->model('accounts_model');
            
            $user = $model->fetch("SELECT id FROM accounts WHERE email_address = :email", ['email' => $email]);
            
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

                $model->query("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)", [
                    'email' => $email,
                    'token' => $token,
                    'expires' => $expires
                ]);

                $resetLink = "https://poemei.com/reset-password/" . $token;
                
                require_once '../app/lib/mailer.php';
                $mailerObj = new mailer();
                $mail = $mailerObj->create();

                try {
                    $mail->addAddress($email);
                    $mail->Subject = "Account Recovery";
                    $mail->Body = "Reset Link: <a href='$resetLink'>$resetLink</a>";
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                }
            }
            $data['success'] = "If that account exists, a recovery link has been sent.";
        }
        $this->view('auth/forgot_password', $data ?? []);
    }

    public function reset_password($params = []) {
        $token = $params[0] ?? '';
        $model = $this->model('accounts_model');
        
        $reset = $model->fetch("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()", ['token' => $token]);

        if (!$reset) {
            die("Invalid or expired token.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            
            $model->query("UPDATE accounts SET password_hash = :pass WHERE email_address = :email", [
                'pass' => $new_password,
                'email' => $reset['email']
            ]);

            $model->query("DELETE FROM password_resets WHERE email = :email", ['email' => $reset['email']]);

            header("Location: /login?reset=success");
            exit;
        }

        $this->view('auth/reset_password', ['token' => $token]);
    }
}
