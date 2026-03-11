<?php
class llms extends controller {
    public static $is_core = true;
    
    public function index() {
        $pages = $this->model('modules_model')->get_all();
        $host = "https://" . $_SERVER['HTTP_HOST'];

        $excluded = ['admin.php', 'auth.php', 'health.php', 'sentinel.php', 'modules.php', 'ror.php', 'llms.php', 'sitemap.php'];
        //$controllers = array_diff(scandir(APPROOT . '/controllers'), array_merge(['.', '..'], $excluded));
        $files = array_map('strtolower', scandir(APPROOT . '/controllers'));
        $controllers = array_diff($files, array_merge(['.', '..'], $excluded));

        $txt = "# Poe Mei Map\n\n## Controllers\n";
        foreach ($controllers as $file) {
            $name = str_replace('.php', '', $file);
            $txt .= "- [$name]($host/$name)\n";
        }

        $txt .= "\n## Modules\n";
        foreach ($pages as $p) {
            $txt .= "- [" . $p['title'] . "]($host/" . $p['slug'] . ")\n";
        }

        file_put_contents(PUBROOT . '/llms.txt', $txt);
        return true;
    }
}
