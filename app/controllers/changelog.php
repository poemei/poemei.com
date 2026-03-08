<?php
class changelog extends controller {
    public function index() {
        $model = $this->model('changelog_model');
        $data['updates'] = $model->get_all_updates();
        
        // Passing the renderer and data to the view
        $this->view('public/changelog/index', $data);
    }
}
