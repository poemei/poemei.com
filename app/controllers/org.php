<?php
declare(strict_types=1);

final class org extends controller
{
    public function index($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/page/org');
    }
    
    public function about($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/page/about');
    }
    
    public function jobs($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/page/jobs');
    }
    
    public function acknowledgements($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/page/acks');
    }
}
