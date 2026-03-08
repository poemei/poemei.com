<?php
// path: /app/controllers/error_handler.php

class error_handler extends controller 
{
    public function index()
    {
        $data = [
            'code'    => '404',
            'title'   => 'Record Not Found',
            'message' => 'The requested file is unreachable or does not exist.'
        ];
        
        $this->view('errors/error_page', $data);
    }
    
    /**
     * Standard 404 Response
     * Fixed: Passes required tokens to the view to stop Undefined Key warnings.
     */
    public function not_found() 
    {
        $data = [
            'code'    => '404',
            'title'   => 'Record Not Found',
            'message' => 'The requested file is unreachable or does not exist.'
        ];
        
        $this->view('errors/error_page', $data);
    }

    /**
     * Illegal Entity Response (e.g., ISRB) [cite: 2026-02-20]
     */
    public function illegal_entity() 
    {
        $data = [
            'code'    => '403',
            'title'   => 'Security Protocol Active',
            'message' => 'Access denied.'
        ];

        $this->view('errors/error_page', $data);
    }
}
