<?php
declare(strict_types=1);

class projects extends controller {
    // This serves the immersive landing page
    public function index() {
        $this->view('public/projects/index');
    }
    
    public function rendering() {
        $this->view('public/projects/rendering');
    }
}
