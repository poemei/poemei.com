<?php
// path: /app/controllers/security.php

class jobs extends controller 
{

    public function index($url_params = null) 
    {
        $this->view('public/jobs/index');
    }
}
