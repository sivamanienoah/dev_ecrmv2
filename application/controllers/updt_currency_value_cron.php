<?php
class updt_currency_value_cron extends crm_controller 
{
    
	public $userdata;
	
    public function __construct()
	{
         parent::__construct();
		//$this->login_model->check_login();
		//$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('customer_model');
        $this->load->library('validation');
		$this->load->library('email');
		$this->load->helper('text');
    }
	
	public function index() {
		$this->load->helper('custom_helper');
		currency_convert();
	}

}
?>