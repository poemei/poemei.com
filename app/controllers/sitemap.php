<?php
class sitemap extends controller {
    public function index() {
        $modules = $this->model('modules_model')->get_all();
        $base_url = URLROOT;

        $excluded = ['admin.php', 'auth.php', 'health.php', 'sentinel.php', 'modules.php', 'ror.php', 'llms.php', 'sitemap.php'];
        //$controllers = array_diff(scandir(APPROOT . '/controllers'), array_merge(['.', '..'], $excluded));
        $files = array_map('strtolower', scandir(APPROOT . '/controllers'));
        $controllers = array_diff($files, array_merge(['.', '..'], $excluded));
        

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($controllers as $file) {
            $name = str_replace('.php', '', $file);
            $xml .= "  <url><loc>$base_url/$name</loc><priority>0.7</priority></url>\n";
        }

        foreach ($modules as $m) {
            $xml .= "  <url><loc>$base_url/{$m['slug']}</loc><priority>0.8</priority></url>\n";
        }

        $xml .= "</urlset>";
        file_put_contents(PUBROOT . '/sitemap.xml', $xml);
        return true;
    }
}
