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
	
	/*function index()
	{
		#$this->output->enable_profiler(TRUE);
		
		$data = array();
		$data['lead_stage'] = $this->welcome_model->get_lead_stage();
		$data['customers'] = $this->welcome_model->get_customers();
		$leadowner = $this->db->query("SELECT userid, first_name FROM crm_users");
		$data['lead_owner'] = $leadowner->result_array(); 
		
		
		
		if ($this->userdata['level'] == 4)
		{
			$sql = "SELECT *
					FROM `crm_jobs`, `crm_customers`
					WHERE `custid_fk` = `custid`
					AND `belong_to` = '{$this->userdata['sales_code']}'
					ORDER BY `job_status`, `job_title`";
		}
		else if ($this->userdata['level'] == 6)
		{
			$sql = "SELECT *
					FROM `crm_customers`, `crm_jobs` a
					RIGHT JOIN `crm_contract_jobs` ON `crm_contract_jobs`.`jobid_fk` = `a`.`jobid` AND `crm_contract_jobs`.`userid_fk` = '{$this->userdata['userid']}'
					WHERE `custid_fk` = `custid`
					ORDER BY `job_status`, `job_title`";
		}
		else
		{
			$sql = "SELECT *
					FROM `crm_jobs`, `crm_customers`
					WHERE `custid_fk` = `custid`
					AND `assigned_to` = '{$this->userdata['userid']}'
					ORDER BY `job_status`, `job_title`";
		}
				
		$q = $this->db->query($sql);
		
		if ($q->num_rows() > 0)
		{				
			$result = $q->result_array();
			$i = 0;
			foreach ($this->cfg['job_status'] as $k => $v)
			{
				while (isset($result[$i]) && $k == $result[$i]['job_status'])
				{
					$data['results'][$k][] = $result[$i];
					$i++;
				}
			}
		}
		
		$this->load->view('choice_view', $data);
		
    }
	*/
	function index()
	{
		$this->load->helper('text');
		$this->load->helper('fix_text');

		#$this->output->enable_profiler(TRUE);
		
		$data = array();
		$data['lead_stage'] = $this->welcome_model->get_lead_stage();
		$data['customers'] = $this->welcome_model->get_customers();
		$leadowner = $this->db->query("SELECT userid, first_name FROM crm_users order by first_name");
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
		
		/*
		if ($this->userdata['level'] == 4)
		{
			$sql = "SELECT *
					FROM `crm_jobs`, `crm_customers`
					WHERE `custid_fk` = `custid`
					AND `belong_to` = '{$this->userdata['sales_code']}'
					ORDER BY `job_status`, `job_title`";
		}
		else if ($this->userdata['level'] == 6)
		{
			$sql = "SELECT *
					FROM `crm_customers`, `crm_jobs` a
					RIGHT JOIN `crm_contract_jobs` ON `crm_contract_jobs`.`jobid_fk` = `a`.`jobid` AND `crm_contract_jobs`.`userid_fk` = '{$this->userdata['userid']}'
					WHERE `custid_fk` = `custid`
					ORDER BY `job_status`, `job_title`";
		}
		else
		{
			$sql = "SELECT *
					FROM `crm_jobs`, `crm_customers`
					WHERE `custid_fk` = `custid`
					AND `assigned_to` = '{$this->userdata['userid']}'
					ORDER BY `job_status`, `job_title`";
		}
				
		$q = $this->db->query($sql);		
		if ($q->num_rows() > 0)
		{				
			$result = $q->result_array();
			$i = 0;
			foreach ($this->cfg['job_status'] as $k => $v)
			{
				while (isset($result[$i]) && $k == $result[$i]['job_status'])
				{
					$data['results'][$k][] = $result[$i];
					$i++;
				}
			}
		}
		*/
		//mychanges
		$qqql = $this->db->query("SELECT `crm_tasks`.`created_by` FROM `crm_tasks`,`crm_users` WHERE `crm_tasks`.`userid_fk` = `crm_users`.`userid`");
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