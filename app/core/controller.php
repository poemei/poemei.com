<?php
// path: /app/core/controller.php
class controller {

    protected $render_md;
    /**
     * Global for render_md
     * to make render_md available for the entire site
     * render_md is MarkDown Rendering for eeasier formatting.
    */
    public function __construct() {
        // Instantiate once, available to all children
        $this->render_md = new render_md();
    }
    
    /**
     * Renders a view file and injects data
     */
    public function view($view, $data = []) {
        $file = APPROOT . '/views/' . $view . '.php';

        if (file_exists($file)) {
            // Localize the property so the view can see $render_md directly
            $render_md = $this->render_md;
            
            // If you want to use $data['title'] as just $title in the view
            extract($data); 

            require_once $file;
        } else {
            // Error handling for missing views
            $this->error_page("View '$view' is currently broken. Suck it up buttercup.");
        }
    }

    /**
     * Loads a model
     */
    public function model($model) {
        if (file_exists(APPROOT . '/models/' . $model . '.php')) {
            require_once APPROOT . '/models/' . $model . '.php';
            return new $model();
        }
        die("Model $model not found.");
    }
}
