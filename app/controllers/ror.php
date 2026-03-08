<?php
class ror extends controller {
    public static $is_core = true;
    
    public function index() {
        $baseUrl = URLROOT;
        $modules = $this->model('modules_model')->get_all();

        $excluded = ['admin.php', 'auth.php', 'health.php', 'sentinel.php', 'modules.php', 'ror.php', 'llms.php', 'sitemap.php'];
        $controllers = array_diff(scandir(APPROOT . '/controllers'), array_merge(['.', '..'], $excluded));

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<rss version="2.0">' . PHP_EOL;
        $xml .= '  <channel>' . PHP_EOL;
        $xml .= '    <title>Poe Mei</title>' . PHP_EOL;
        $xml .= '    <link>' . $baseUrl . '</link>' . PHP_EOL;

        foreach ($controllers as $file) {
            $name = str_replace('.php', '', $file);
            $xml .= "    <item>\n      <title>".ucfirst($name)."</title>\n      <link>$baseUrl/$name</link>\n    </item>\n";
        }

        foreach ($modules as $module) {
            $xml .= "    <item>\n      <title>".htmlspecialchars($module['title'])."</title>\n      <link>$baseUrl/".htmlspecialchars($module['slug'])."</link>\n    </item>\n";
        }

        $xml .= '  </channel></rss>';
        file_put_contents(PUBROOT . '/ror.xml', $xml);
        return true;
    }
}
