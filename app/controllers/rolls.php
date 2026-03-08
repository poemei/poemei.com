<?php
// path: /app/controllers/rolls.php

class rolls extends controller 
{
    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true; 

    public function index($url_params = null) 
    {
        $model = $this->model('rolls_model');
        $slug = (is_array($url_params) && isset($url_params[1])) ? $url_params[1] : null;

        if (!$slug || $slug === 'rolls') {
            $data['rolls'] = $model->get_all();
            $this->view('public/rolls/index', $data);
            return;
        }

        $roll = $model->get_by_slug_with_relations($slug);
        if ($roll) {
            $this->view('public/rolls/detail', $roll);
        } else {
            require_once APPROOT . '/controllers/error_handler.php';
            (new error_handler())->not_found();
        }
    }

    public function admin($url = [])
    {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 7) {
            header("Location: /auth/login");
            exit;
        }

        $model = $this->model('rolls_model');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $model->save_new([
                    'title'   => trim($_POST['title']),
                    'slug'    => trim($_POST['slug']),
                    'content' => trim($_POST['content']),
                    'parent_roll_id' => trim($_POST['parent_roll_id'])
                ]);
            }

            if ($action === 'update') {
                $model->update_roll([
                    'id'             => (int)$_POST['id'],
                    'title'          => trim($_POST['title']),
                    'slug'           => trim($_POST['slug']),
                    'content'        => trim($_POST['content']),
                    'parent_roll_id' => trim($_POST['parent_roll_id']),
                    'supersedes_id'  => !empty($_POST['supersedes_id']) ? (int)$_POST['supersedes_id'] : null,
                    'status'         => $_POST['status']
                ]);
            }

            header("Location: /admin/rolls");
            exit;
        }

        $data['rolls'] = $model->get_all();
        $this->view('admin/rolls', $data);
    }
}
