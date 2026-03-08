<?php
declare(strict_types=1);

final class resume extends controller
{
    public function index($url_params = null): void
    {
        $resume_model = $this->model('resume_model');
        $data = [
            'get_resume' => $resume_model->get_resume()
        ];
        
        $this->view('public/resume/index', $data);
    }
}
