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

        // Generators to run
        $generators = ['sitemap', 'ror', 'llms'];

        foreach ($generators as $g) {

            // Load controller if not already loaded
            if (!class_exists($g)) {

                $path = APPROOT . '/controllers/' . $g . '.php';

                if (file_exists($path)) {
                    require_once $path;
                }
            }

            if (!class_exists($g)) {
                continue;
            }

            $instance = new $g();

            if (method_exists($instance, 'index')) {
                $instance->index();
            }
        }

        $_SESSION['admin_status'] = 'SEO indices refreshed.';

        header("Location: /admin");
        exit;
    }

    /**
     * Module Uninstaller
     */
    public function uninstall() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $module = $_POST['module'];
            
            // 1. Load the controller to check Core status
            $path = APPROOT . '/controllers/' . $module . '.php';
            if (file_exists($path)) {
                require_once $path;
                if (property_exists($module, 'is_core') && $module::$is_core) {
                    // Cannot uninstall core architecture
                    header("Location: /admin");
                    exit;
                }
            }

            // 2. Wipe Database Tables
            $this->db->query("DROP TABLE IF EXISTS " . $module);

            // 3. Wipe Backend Logic (Controller & Model)
            $backend = [
                APPROOT . "/controllers/" . $module . ".php",
                APPROOT . "/models/" . $module . "_model.php"
            ];
            foreach ($backend as $file) {
                if (file_exists($file)) unlink($file);
            }

            // 4. Wipe Admin View
            $admin_view = APPROOT . "/views/admin/" . $module . ".php";
            if (file_exists($admin_view)) unlink($admin_view);

            // 5. Wipe Nested Public View Directory
            $public_dir = APPROOT . "/views/public/" . $module;
            if (is_dir($public_dir)) {
                $this->recursive_rmdir($public_dir);
            }

            header("Location: /admin");
            exit;
        }
    }

    /**
     * Helper to ensure nested directories are fully purged
     */
    private function recursive_rmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        $this->recursive_rmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }

}
