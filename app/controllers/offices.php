<?php

class offices extends controller
{

    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true;
    
    /**
     * Public listing + drill-down logic
     * Science -> Alchemy -> Lessons
     */
    public function index($url = [])
    {
        $model = $this->model('offices_model');
        // REMOVED: $this->model('content_renderer'); 
        // Core classes in /app/core/ are already available via autoloader.

        $slug = $url[1] ?? null;

        // STATE 1: Root Level - Display only Parent Offices
        if (!$slug) {
            $data['offices'] = $model->get_top_level();
            $this->view('public/offices/index', $data);
            return;
        }

        // STATE 2: Specific Office lookup
        $office = $model->get_by_slug($slug);

        if (!$office || (int)$office['is_active'] !== 1) {
            require_once APPROOT . '/controllers/error_handler.php';
            (new error_handler())->not_found();
            return;
        }

        $data['office'] = $office;
        
        // Check if this office has sub-departments
        $sub_offices = $model->get_children($office['id']);

        if (!empty($sub_offices)) {
            // Display sub-offices (e.g., viewing Science shows Alchemy)
            $data['offices'] = $sub_offices;
            $this->view('public/offices/index', $data);
        } else {
            // STATE 3: Leaf Level - Display Lessons
            $data['lessons']  = $model->get_lessons_by_office($office['id']);
            $data['director'] = $model->get_director($office['id']);
            $this->view('public/offices/detail', $data);
        }
    }

    /**
     * Admin surface
     */
    public function admin()
    {
        // Security check for Level 7+
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 7) {
            header("Location: /login");
            exit;
        }

        $model = $this->model('offices_model');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $model->create([
                    'name'        => trim($_POST['name']),
                    'slug'        => trim($_POST['slug']),
                    'description' => trim($_POST['description']),
                    'parent_id'   => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null
                ]);
            }

            if ($action === 'toggle') {
                $model->toggle_active((int)$_POST['id']);
            }

            if ($action === 'update') {
                $model->update_description(
                    (int)$_POST['id'],
                    trim($_POST['description'])
                );
            }

            if ($action === 'assign_director') {
                $model->assign_director(
                    (int)$_POST['office_id'],
                    (int)$_POST['account_id']
                );
            }

            header("Location: /admin/offices");
            exit;
        }

        $data['offices'] = $model->get_all_admin();
        $data['staff']   = $model->get_staff();

        $this->view('admin/offices', $data);
    }
}
