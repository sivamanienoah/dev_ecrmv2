<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notallowed extends CI_Controller {
    
    function Notallowed() {
        parent::__construct();
    }
    
    function index() {
        
        /*
        * destroy session
        * show login details
        */
        $data['notallowed'] = true;
        $this->load->view('login_view', $data);
        
    }
    
}

?>