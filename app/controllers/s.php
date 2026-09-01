<?php
declare(strict_types=1);

/**
 * Short URL Redirect Controller
 * Usage: /s/{code}
 */

final class s extends controller
{
    public function index($url_params = null): void
    {
        $code = (is_array($url_params) && isset($url_params[1])) ? trim((string)$url_params[1]) : '';

        if ($code === '' || $code === 's') {
            require_once APPROOT . '/controllers/error_handler.php';
            (new error_handler())->not_found();
            return;
        }

        $model = $this->model('short_urls_model');
        $short = $model->get_by_code($code);

        if (!$short) {
            require_once APPROOT . '/controllers/error_handler.php';
            (new error_handler())->not_found();
            return;
        }

        $model->increment_click((int)$short['id']);

        header("Location: " . $short['target_url'], true, 302);
        exit;
    }
}

