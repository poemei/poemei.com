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
            $user = $model->authenticate($_POST['username'], $_POST['password']);

            if ($user) {
                session_regenerate_id();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_level'] = $user['user_level']; 
                $_SESSION['role'] = $user['role']; 
                
                header("Location: /admin");
                exit;
            } else {
                $data['error'] = "Invalid Institutional Credentials.";
            }
        }
        $this->view('auth/login', $data ?? []);
    }
    
    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: /");
        exit;
    }
}
