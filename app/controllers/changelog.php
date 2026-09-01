<?php
// path: /app/controllers/changelog.php

class changelog extends controller {

    public function index($url = []) {
        $model = $this->model('changelog_model');
        
        // Change 'items' to 'updates' to match what the view expects
        $data['updates'] = $model->get_all_updates(); 
        
        $this->view('public/changelog/index', $data);
    }

    public function admin($params = []) {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 7) {
            header("Location: /auth/login");
            exit;
        }

        $model = $this->model('changelog_model');
        $action = $params[1] ?? null;
        $id = $params[2] ?? null;
        $data['edit_item'] = null;

        // DELETE
        if ($action === 'delete' && $id) {
            $model->db->query("DELETE FROM changelog WHERE id = " . (int)$id);
            header("Location: /admin/changelog");
            exit;
        }

        // FETCH FOR EDIT
        if ($action === 'edit' && $id) {
            $data['edit_item'] = $model->db->query("SELECT * FROM changelog WHERE id = " . (int)$id)->fetch();
        }

        // SAVE (Create or Update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['version'])) {
            $payload = [
                'version' => $_POST['version'],
                'description' => $_POST['description'],
                'date_released' => $_POST['date_released'] ?? date('Y-m-d')
            ];

            if (!empty($_POST['id'])) {
                // UPDATE logic
                $model->db->query("UPDATE changelog SET version = ?, description = ?, date_released = ? WHERE id = ?", [
                    $payload['version'], $payload['description'], $payload['date_released'], (int)$_POST['id']
                ]);
            } else {
                // CREATE logic
                $model->insert('changelog', $payload);
            }
            header("Location: /admin/changelog");
            exit;
        }

        $data['items'] = $model->get_all_updates();
        $this->view('admin/changelog', $data);
    }
}
