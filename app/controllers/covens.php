<?php
// path: /app/controllers/covens.php

class announcements extends controller
{
    private $model;
    
    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = false;

    public function __construct()
    {
        $this->model = $this->model('announcements_model');
    }
    
    public function index()
    {
        $latest = $announcements->get_latest_single();

        $data = [
          'featured_announcement' => $latest
        ];

        $this->view('home/index', $data);
    }

    public function admin()
    {

        $this->view('admin/announcements', $data);
    }
}
