<?php
declare(strict_types=1);

class learn extends controller {
    // This serves the immersive landing page
    public function index() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/index');
    }
    
    public function codex() {
        $modules = $this->model('modules_model');
        $this->view('public/learn/codex');
    }
    
    public function coven() {
        $modules = $this->model('modules_model');
        $this->view('public/learn/coven');
    }
    
    public function seeker() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/seeker');
    }
    
    public function neophyte() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/neophyte');
    }
    
    public function initiate() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/initiate');
    }
    
    public function adeptus_minor() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/adeptus_minor');
    }
    
    public function priestess() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/priestess');
    }
    
    public function adeptus() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/adeptus');
    }
    
    public function prioress() {
    	$modules = $this->model('modules_model');
        $this->view('public/learn/prioress');
    }
}
