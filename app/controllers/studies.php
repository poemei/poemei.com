<?php
declare(strict_types=1);

class studies extends controller {
    // This serves the immersive landing page
    public function index() {
    	$modules = $this->model('modules_model');
        $this->view('public/studies/index');
    }
    
    public function dispatch() {
        $modules = $this->model('modules_model');
        $this->view('public/studies/dispatch');
    }
}
