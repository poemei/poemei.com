<?php
declare(strict_types=1);

final class legal extends controller
{
    public function index($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/legal/index');
    }
    
    public function charter($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/legal/charter');
    }
    
    public function privacy($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/legal/privacy');
    }
    
    public function terms($url_params = null): void
    {
        $modules = $this->model('modules_model');
        $this->view('public/legal/terms');
    }
}
