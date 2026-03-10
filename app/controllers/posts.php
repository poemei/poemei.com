<?php
// path: /app/controllers/posts.php

class posts extends controller {

    public function index(): void {
        $model = $this->model('posts_model');
        $this->view('public/posts/index', ['items' => $model->get_public_feed()]);
    }

    public function show($params = null): void {
        $slug = is_array($params) ? ($params[0] ?? '') : (string)$params;
        
        if (empty($slug)) {
            header("Location: /posts");
            exit;
        }

        $model = $this->model('posts_model');
        $post = $model->get_post_with_image($slug);

        if (!$post) {
            $this->error_page("Post not found.");
            return;
        }

        $data = [
            'post' => $post,
            'comments' => $model->get_comments_by_post($post['id'])
        ];

        // Use the inherited markdown renderer
        $data['post']['body'] = $this->render_md->markdown($post['body']);

        $this->view('public/posts/show', $data);
    }

    public function admin($params = []) {
        // GATED: Redirect to login if no session
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $model = $this->model('posts_model');
        $action = $params[1] ?? null;
        $id = $params[2] ?? null;

        // Use the core model archive helper instead of hard delete
        if ($action === 'delete' && $id) {
            $model->archive('posts', "id = :id", ['id' => (int)$id]);
            header("Location: /admin/posts");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
            $payload = [
                'title'             => $_POST['title'],
                'slug'              => trim(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($_POST['title']))), '-'),
                'body'              => $_POST['body'],
                'featured_image_id' => !empty($_POST['featured_image_id']) ? (int)$_POST['featured_image_id'] : null,
                'published'         => isset($_POST['published']) ? 1 : 0
            ];

            if (!empty($_POST['id'])) {
                $model->update('posts', $payload, "id = :id", ['id' => $_POST['id']]);
            } else {
                $model->insert('posts', $payload);
            }
            header("Location: /admin/posts");
            exit;
        }

        $data['edit_item'] = ($action === 'edit' && $id) ? $model->get_by_id($id) : null;
        $data['items'] = $model->get_all();
        $data['media_items'] = $this->model('media_model')->get_all();
        
        $this->view('admin/posts', $data);
    }
    
    public function reply() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
        $model = $this->model('posts_model');

        $payload = [
            'post_id'     => (int)$_POST['post_id'],
            'body'        => trim($_POST['body']),
            'author_name' => $_SESSION['username'], // Matches your 'author_name' column
            'is_approved' => 1
        ];

        if ($model->insert('comments', $payload)) {
            // Redirect back to the post they just commented on
            $redirect = $_SERVER['HTTP_REFERER'] ?? '/';
            header("Location: " . $redirect);
            exit;
        }
    }
    $this->error_page("Unauthorized.");
}
}
