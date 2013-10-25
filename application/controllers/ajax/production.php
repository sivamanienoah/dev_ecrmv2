<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Production extends crm_controller {

	public $cfg;
	
	public function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('text');
		$this->load->library('email');
		$this->email->initialize($cfg);
		$this->email->set_newline("\r\n");
	}
    
    public function index()
    {
		
    }
	
	

	
	
	
	
	
}
