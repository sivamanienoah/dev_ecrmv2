<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Choice extends CI_Controller {
	
	var $cfg;
	var $userdata;
	
	function Choice()
	{
		parent::__construct();
		$this->load->model('welcome_model');
		//$this->load->library('excel');
		$this->login_model->check_login();
		$this->load->model('regionsettings_model');
		$this->load->model('welcome_model');
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
	}
	
	function index()
	{
		$this->load->helper('text');
		$this->load->helper('fix_text');

		#$this->output->enable_profiler(TRUE);
		
		$data = array();
		$data['lead_stage'] = $this->welcome_model->get_lead_stage();
		$data['customers'] = $this->welcome_model->get_customers();
		$leadowner = $this->db->query("SELECT userid, first_name FROM ".$this->cfg['dbpref']."users order by first_name");
		$data['lead_owner'] = $leadowner->result_array(); 
		
		$data['lead_stage_pjt'] = $this->welcome_model->get_lead_stage_pjt();
		$data['regions'] = $this->regionsettings_model->region_list();
		$data['pm_accounts'] = array();
		//Here "WHERE" condition used for Fetching the Project Managers.
		$users = $this->db->get_where($this->cfg['dbpref'] . 'users',array('role_id'=>3));
		if ($users->num_rows() > 0)
		{
			$data['pm_accounts'] = $users->result_array();
		}
		
		//mychanges
		$qqql = $this->db->query("SELECT `".$this->cfg['dbpref']."tasks`.`created_by` FROM `".$this->cfg['dbpref']."tasks`,`".$this->cfg['dbpref']."users` WHERE `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`");
		//echo $this->db->last_query(); exit; 	
		$data['created_by'] = $qqql->result_array();
		//print_r($data['created_by']);	
		$data['user_accounts'] = array();
		$users = $this->db->get($this->cfg['dbpref'] . 'users');
		if ($users->num_rows() > 0)
		{
			$data['user_accounts'] = $users->result_array();
		}
		
		$this->load->view('choice_view', $data);
    }
	
	//For Countries
	public function loadCountrys($region_id)
	{
	    $output = '';
		$data = $this->welcome_model->getcountry_list($region_id);
		foreach($data as $country) {
		    $output .= '<option value="'.$country['countryid'].'">'.$country['country_name'].'</option>';
		}
		echo $output;
	}
	
	//For States
	public function loadStates($cnt_id)
	{
	    $output = '';
		$data = $this->welcome_model->getstate_list($cnt_id);
		foreach($data as $st) {
		    $output .= '<option value="'.$st['stateid'].'">'.$st['state_name'].'</option>';
		}
		echo $output;
	}
	
	//For Locations
	public function loadLocns($loc_id)
	{
	    $output = '';
		$data = $this->welcome_model->getlocation_list($loc_id);
		//print_r($data);
		foreach($data as $st) {
		    $output .= '<option value="'.$st['locationid'].'">'.$st['location_name'].'</option>';
		}
		echo $output;
	}

}