<?php
// path: /app/controllers/goddeses.php

class goddesses extends controller 
{

    public function index($url_params = null) 
    {
        $this->view('public/goddesses/index');
    }
    
    public function lilith($url_params = null) 
    {
        $this->view('public/goddesses/lilith');
    }
    
    public function gaia($url_params = null) 
    {
        $this->view('public/goddesses/gaia');
    }
    
    public function hekate($url_params = null) 
    {
        $this->view('public/goddesses/hekate');
    }
    
    public function hedone($url_params = null) 
    {
        $this->view('public/goddesses/hedone');
    }
    
    public function kali($url_params = null) 
    {
        $this->view('public/goddesses/kali');
    }
    
    public function luna($url_params = null) 
    {
        $this->view('public/goddesses/luna');
    }
}
