<?php
/* [AI: Gemini] [2026-03-16 11:45:00 PDT] [Human: Mei] */

class feed extends controller {
    
    protected $posts_model;

    public function __construct() {
        // Corrected: Using the model name defined in your posts controller
        $this->posts_model = $this->model('posts_model');
    }

    public function index() {
        // Corrected: Using the get_all() method from your model
        $posts = $this->posts_model->get_all();

        $data = [
            'posts' => $posts
        ];

        header("Content-Type: application/rss+xml; charset=utf-8");
        
        // Corrected: Path matches your uploaded index.php location
        $this->view('public/feed/index', $data);
    }
}
