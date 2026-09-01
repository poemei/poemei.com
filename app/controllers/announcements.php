<?php
// path: /app/controllers/announcements.php

class announcements extends controller {

    public function index($url = []) {
        $data = ['items' => []];
        $model = $this->model('announcements_model');
        
        if (method_exists($model, 'get_all')) {
            $data['items'] = $model->get_all();
        }
        
        $this->view('public/announcements/index', $data);
    }

    public function admin($params = []) {
        $data = ['items' => []];
        
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 7) {
            header("Location: /auth/login");
            exit;
        }

        $model = $this->model('announcements_model');

        // ACTION: URL-based Deletion (e.g., /admin/announcements/delete/5)
        $action = $params[1] ?? null;
        $id     = $params[2] ?? null;

        if ($action === 'delete' && $id) {
            $model->delete('announcements', "id = " . (int)$id);
            header("Location: /admin/announcements");
            exit;
        }

        // ACTION: POST-based Deletion
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $model->delete('announcements', "id = " . (int)$_POST['delete_id']);
            header("Location: /admin/announcements");
            exit;
        }

        // ACTION: New Addition
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
            $model->insert('announcements', [
                'title' => $_POST['title'],
                'body' => $_POST['body'],
                'published' => isset($_POST['published']) ? 1 : 0
            ]);
            header("Location: /admin/announcements");
            exit;
        }

        if (method_exists($model, 'get_all')) {
            $data['items'] = $model->get_all();
        }
        
        $this->view('admin/announcements', $data);
    }
}
