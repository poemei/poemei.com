<?php
declare(strict_types=1);

/**
 * Short URL Redirect Controller
 * Usage: /s/{code}
 */

final class about extends controller
{
    public function index($url_params = null): void
    {
        $view = 'public/page/about';

        if (!$view || $view === '') {
            require_once APPROOT . '/controllers/error_handler.php';
            (new error_handler())->not_found();
            return;
        }
        $this->view($view);
    }
}

