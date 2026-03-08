<?php
// path: /app/controllers/maps.php

class maps extends controller {
    protected $maps_model;
    
    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true;
    
    public function __construct() {
        $this->maps_model = $this->model('maps_model');
    }

    public function index() {
        $data['title'] = 'Regional Map';
        $data['pins'] = $this->maps_model->get_members_filtered(true);
        $data['covens'] = $this->maps_model->get_covens_data(false);
        $this->view('public/maps/index', $data);
    }

    public function admin() {
        $data['title'] = 'Tactical Command';
        // Enable Nexus anchoring for the Admin/Madam view
        $data['covens'] = $this->maps_model->get_covens_data(true);
        $data['members'] = $this->maps_model->get_members_filtered(false);
        $this->view('admin/maps', $data);
    }
    
}
