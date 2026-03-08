<?php
// path: /app/controllers/admin.php

class admin extends controller
{

    public function index()
    {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 7) {
            header("Location: /auth/login");
            exit;
        }

        $this->view('admin/index');
    }


    public function refresh_indices()
    {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 9) {
            header("Location: /admin");
            exit;
        }

        $base_url = URLROOT;

        $excluded = [
            '.', '..',
            'admin.php',
            'auth.php',
            'health.php',
            'sentinel.php',
            'modules.php',
            'sitemap.php',
            'ror.php',
            'llms.php'
        ];

        $controllers = array_diff(
            scandir(APPROOT . '/controllers'),
            $excluded
        );

        $urls = [];

        // Controllers
        foreach ($controllers as $file) {

            if (substr($file, -4) !== '.php') {
                continue;
            }

            $name = str_replace('.php', '', $file);

            $urls[] = $base_url . '/' . $name;
        }

        // DB modules (optional)
        try {

            $modules = $this->model('modules_model')->get_all();

            foreach ($modules as $m) {

                if (!empty($m['slug']) && (int)$m['is_active'] === 1) {
                    $urls[] = $base_url . '/' . $m['slug'];
                }
            }

        } catch (\Throwable $e) {
            // modules table optional
        }

        $urls = array_unique($urls);

        /* =========================
           SITEMAP.XML
        ========================= */

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url}</loc>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>";

        file_put_contents(PUBROOT . '/sitemap.xml', $xml);


        /* =========================
           ROR.XML
        ========================= */

        $ror  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $ror .= "<rss version=\"2.0\" xmlns:ror=\"http://rorweb.com/0.1/\">\n";
        $ror .= "<channel>\n";

        foreach ($urls as $url) {

            $ror .= "<item>\n";
            $ror .= "<link>{$url}</link>\n";
            $ror .= "<ror:updatePeriod>weekly</ror:updatePeriod>\n";
            $ror .= "</item>\n";
        }

        $ror .= "</channel>\n";
        $ror .= "</rss>";

        file_put_contents(PUBROOT . '/ror.xml', $ror);


        /* =========================
           LLMS.TXT
        ========================= */

        $llms = "User-agent: *\n";
        $llms .= "Allow: /\n\n";

        foreach ($urls as $url) {
            $llms .= $url . "\n";
        }

        file_put_contents(PUBROOT . '/llms.txt', $llms);


        $_SESSION['admin_status'] = 'SEO indices refreshed.';
        header("Location: /admin");
        exit;
    }


    /**
     * Module Uninstaller
     */
    public function uninstall()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $module = $_POST['module'];

            $path = APPROOT . '/controllers/' . $module . '.php';

            if (file_exists($path)) {

                require_once $path;

                if (property_exists($module, 'is_core') && $module::$is_core) {
                    header("Location: /admin");
                    exit;
                }
            }

            $this->db->query("DROP TABLE IF EXISTS " . $module);

            $backend = [
                APPROOT . "/controllers/" . $module . ".php",
                APPROOT . "/models/" . $module . "_model.php"
            ];

            foreach ($backend as $file) {
                if (file_exists($file)) unlink($file);
            }

            $admin_view = APPROOT . "/views/admin/" . $module . ".php";

            if (file_exists($admin_view)) unlink($admin_view);

            $public_dir = APPROOT . "/views/public/" . $module;

            if (is_dir($public_dir)) {
                $this->recursive_rmdir($public_dir);
            }

            header("Location: /admin");
            exit;
        }
    }


    private function recursive_rmdir($dir)
    {
        if (is_dir($dir)) {

            $objects = scandir($dir);

            foreach ($objects as $object) {

                if ($object != "." && $object != "..") {

                    if (is_dir($dir . "/" . $object)) {
                        $this->recursive_rmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }

            rmdir($dir);
        }
    }
}
