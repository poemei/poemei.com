<?php
// path: /app/controllers/health.php

class health extends controller 
{

    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true;
    
    public function admin($url = []) 
    {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 9) {
            header("Location: /admin");
            exit;
        }
        $site_root = $_SERVER['DOCUMENT_ROOT'];
        $data['server'] = [
            'software' => $_SERVER['SERVER_SOFTWARE'],
            'php_version' => PHP_VERSION,
            'domain' => $_SERVER['HTTP_HOST'],
            'root' => $site_root,
            'disk_free' => round(disk_free_space("/") / (1024 * 1024 * 1024), 2) . " GB"
        ];

        // MySQL Check via basic model query
        $model = $this->model('modules_model');
        $db_info = $model->query("SELECT VERSION() as version")->fetch();
        $data['mysql'] = [
            'version' => $db_info['version'],
            'type' => 'MySQL/MariaDB Community'
        ];

        // Permissions Check - Corrected Path
        $logs_base = $_SERVER['DOCUMENT_ROOT'];
        $log_path = $logs_base . '/logs';
        $data['logs'] = [
            'exists' => file_exists($log_path),
            'writable' => is_writable($log_path),
            'path' => $log_path
        ];

        $this->view('admin/health', $data);
    }
}
