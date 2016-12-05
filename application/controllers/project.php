<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() 
	{
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
	
	/*
	 * List all the Leads based on levels
	 * @access public
	 */
	public function index()
	{
		$data['page_heading'] = "Projects - Lists";
		$data['pm_accounts'] = array();
		$pjt_managers		 = $this->project_model->get_user_byrole(3);
		if(!empty($pjt_managers))
		$data['pm_accounts'] = $pjt_managers;
		$data['customers']   = $this->project_model->get_customers();
		$data['services']    = $this->project_model->get_services();
		$data['practices']   = $this->project_model->get_practices();
		$data['sales_divisions'] = $this->welcome_model->get_sales_divisions();
		$data['saved_search'] = $this->welcome_model->get_saved_search($this->userdata['userid'], $search_for=2);
		$db_fields 			  = $this->project_model->get_dashboard_field($this->userdata['userid']);
		if(!empty($db_fields) && count($db_fields)>0) {
			foreach($db_fields as $record) {
				$data['db_fields'][] = $record['column_name'];
			}
		}
		$this->load->view('projects/projects_view', $data);
    }
	
	/*
	 *Advanced Search For Projects
	 */
	public function advance_filter_search_pjt($search_type = false, $search_id = false)
	{
		echo "advance_filter_search_pjt"; exit;
		$filter 			=  array();
		
		$pjtstage 			= '';
		$cust     			= '';
		$service 			= '';
		$practice 			= '';
		$keyword  			= '';
		$datefilter 		= '';
		$from_date			= '';
		$to_date  			= '';
		$divisions  		= '';
		$data['val_export'] = 'no_search';

		if($search_type == 'search' && $search_id == false) {
			$inputData = real_escape_array($this->input->post());			
			// echo "<pre>"; print_r($inputData); exit;
			$pjtstage 	= $inputData['pjtstage'];
			$cust     	= $inputData['customer'];
			$service 	= $inputData['service'];
			$practice 	= $inputData['practice'];
			$keyword  	= $inputData['keyword'];
			$datefilter = $inputData['datefilter'];
			$from_date	= $inputData['from_date'];
			$to_date  	= $inputData['to_date'];
			$divisions  = $inputData['divisions'];
			
			$data['val_export']  = 'search';
			
		} else if ($search_type == 'search' && is_numeric($search_id)) {
			$wh_condn = array('search_id'=>$search_id, 'search_for'=>2, 'user_id'=>$this->userdata['userid']);
			$get_rec  = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
			
			unset($get_rec['stage']);
			unset($get_rec['worth']);
			unset($get_rec['owner']);
			unset($get_rec['leadassignee']);
			unset($get_rec['project']);
			unset($get_rec['regionname']);
			unset($get_rec['countryname']);
			unset($get_rec['statename']);
			unset($get_rec['locname']);
			unset($get_rec['lead_status']);
			unset($get_rec['lead_indi']);
			unset($get_rec['search_id']);
			unset($get_rec['search_for']);
			unset($get_rec['search_name']);
			unset($get_rec['user_id']);
			unset($get_rec['is_default']);
			unset($get_rec['month_year_from_date']);
			unset($get_rec['month_year_to_date']);
			
			if(!empty($get_rec)) {
				$data['val_export'] = $search_id;
				$inputData	  = real_escape_array($get_rec);
				
				$pjtstage 	= $inputData['pjtstage'];
				$cust     	= $inputData['customer'];
				$service 	= $inputData['service'];
				$practice 	= $inputData['practice'];
				$datefilter = $inputData['datefilter'];
				$from_date	= $inputData['from_date'];
				$to_date  	= $inputData['to_date'];
				$divisions  = $inputData['divisions'];
				
				if(!empty($pjtstage) && $pjtstage!='null')
				$pjtstage = @explode(",",$pjtstage);
				else
				$pjtstage = '';
				if(!empty($cust) && $cust!='null')
				$cust = @explode(",",$cust);
				else
				$cust = '';
				if(!empty($service) && $service!='null')
				$service = @explode(",",$service);
				else
				$service = '';
				if(!empty($practice) && $practice!='null')
				$practice = @explode(",",$practice);
				else
				$practice = '';
				if(!empty($divisions) && $divisions!='null')
				$divisions = @explode(",",$divisions);
				else
				$divisions = '';
			}
		} else {
			$wh_condn = array('search_for'=>2, 'user_id'=>$this->userdata['userid'], 'is_default'=>1);
			$get_rec  = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
			if(!empty($get_rec)) {
				$data['val_export'] = $get_rec['search_id'];
				unset($get_rec['stage']);
				unset($get_rec['worth']);
				unset($get_rec['owner']);
				unset($get_rec['project']);
				unset($get_rec['leadassignee']);
				unset($get_rec['regionname']);
				unset($get_rec['countryname']);
				unset($get_rec['statename']);
				unset($get_rec['locname']);
				unset($get_rec['lead_status']);
				unset($get_rec['lead_indi']);
				unset($get_rec['search_id']);
				unset($get_rec['search_for']);
				unset($get_rec['search_name']);
				unset($get_rec['user_id']);
				unset($get_rec['is_default']);
				unset($get_rec['month_year_from_date']);
				unset($get_rec['month_year_to_date']);
				$inputData = real_escape_array($get_rec);
				
				$pjtstage 	= $inputData['pjtstage'];
				$cust     	= $inputData['customer'];
				$service 	= $inputData['service'];
				$practice 	= $inputData['practice'];
				$datefilter = $inputData['datefilter'];
				$from_date	= $inputData['from_date'];
				$to_date  	= $inputData['to_date'];
				$divisions  = $inputData['divisions'];
				
				if(!empty($pjtstage) && $pjtstage!='null')
				$pjtstage = @explode(",",$pjtstage);
				else
				$pjtstage = '';
				if(!empty($cust) && $cust!='null')
				$cust = @explode(",",$cust);
				else
				$cust = '';
				if(!empty($service) && $service!='null')
				$service = @explode(",",$service);
				else
				$service = '';
				if(!empty($practice) && $practice!='null')
				$practice = @explode(",",$practice);
				else
				$practice = '';
				if(!empty($divisions) && $divisions!='null')
				$divisions = @explode(",",$divisions);
				else
				$divisions = '';
			}
		}

	    /*
		 *$pjtstage - lead_stage. $pm_acc - Project Manager Id. $cust - Customers Id.(custid_fk)
		 */
		if ($keyword == 'false' || $keyword == 'undefined') {
			$keyword = 'null';
		}
		$getProjects	   = $this->project_model->get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,false,$divisions);
		// echo "query".$this->db->last_query(); exit;
		// echo "<pre>"; print_r($getProjects); die;
		$data['pjts_data'] = $this->getProjectsDataByDefaultCurrency($getProjects);
		// echo "<pre>"; print_r($data['pjts_data']); die;
		//for field restriction
		$db_fields 			  = $this->project_model->get_dashboard_field($this->userdata['userid']);
		if(!empty($db_fields) && count($db_fields)>0) {
			foreach($db_fields as $record) {
				$data['db_fields'][] = $record['column_name'];
			}
		}
		$this->load->view('projects/projects_view_inprogress', $data);
	}
	
}
?>