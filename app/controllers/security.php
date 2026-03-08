<?php
// path: /app/controllers/security.php

class security extends controller 
{
    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true; 

    public function index($url_params = null) 
    {
        $model = $this->model('modules_model');
        $this->view('security/index');
    }
    
    public function pgp_key($url_params = null)
    {
        $model = $this->model('modules_model');
        $this->view('security/pgp_key');
    }
    
    public function protocol($url_params = null)
    {
        $model = $this->model('modules_model');
        $this->view('security/protocol');
    }
}
