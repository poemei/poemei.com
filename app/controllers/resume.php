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
        
        /**
         * Add conditions check
         * if not found -> 404 to the error_handler
         * [Human:Mei | 2026-03-13 02:35:00 UTC]
        */
        $view = 'public/resume/index';

        if (!file_exists(APPROOT . '/views/' . $view . '.php')) {
            (new error_handler())->not_found();
            return;
        }

        $this->view($view, $data);
    }
}
