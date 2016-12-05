<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() 
	{
		echo "test"; exit;
		parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('project_model');
		$this->load->model('request_model');
		$this->load->model('welcome_model');
		$this->load->model('customer_model');
		$this->load->model('department_model'); //Mani.S
		$this->load->model('project_types_model'); //Mani.S
		$this->load->model('cost_center_model'); //Mani.S 
		$this->load->model('profit_center_model'); //Mani.S
		$this->load->model('regionsettings_model');
		$this->load->model('email_template_model');		
		$this->load->helper('text');
		$this->load->library('email');
		$this->load->helper('form');
		$this->email->set_newline("\r\n");
		$this->load->library('upload');
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stg_name = getLeadStageName();
		$this->stages = @implode('","', $this->stg);
		
		$this->load->helper('custom_helper');
		$this->load->model('report/report_lead_region_model');
		
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
	}
	
}
?>