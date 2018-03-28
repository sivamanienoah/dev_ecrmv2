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
		$customer_type  	= '';
		$data['val_export'] = 'no_search';

		if($search_type == 'search' && $search_id == false) {
			$inputData = real_escape_array($this->input->post());			
			// echo "<pre>"; print_r($inputData); exit;
			$pjtstage 		= $inputData['pjtstage'];
			$cust     		= $inputData['customer'];
			$service 		= $inputData['service'];
			$practice 		= $inputData['practice'];
			$keyword  		= $inputData['keyword'];
			$datefilter 	= $inputData['datefilter'];
			$from_date		= $inputData['from_date'];
			$to_date  		= $inputData['to_date'];
			$divisions  	= $inputData['divisions'];
			$customer_type  = $inputData['customer_type'];
			
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
				
				$pjtstage 		= $inputData['pjtstage'];
				$cust     		= $inputData['customer'];
				$service 		= $inputData['service'];
				$practice 		= $inputData['practice'];
				$datefilter 	= $inputData['datefilter'];
				$from_date		= $inputData['from_date'];
				$to_date  		= $inputData['to_date'];
				$divisions  	= $inputData['divisions'];
				$customer_type  = $inputData['customer_type'];
				
				if(!empty($pjtstage) && $pjtstage!='null') {
					$pjtstage = @explode(",",$pjtstage);
				} else {
					$pjtstage = '';
				}
				if(!empty($cust) && $cust!='null') {
					$cust = @explode(",",$cust);
				} else {
					$cust = '';
				}
				if(!empty($service) && $service!='null') {
					$service = @explode(",",$service);
				} else {
					$service = '';
				}
				if(!empty($practice) && $practice!='null') {
					$practice = @explode(",",$practice);
				} else {
					$practice = '';
				}
				if(!empty($divisions) && $divisions!='null') {
					$divisions = @explode(",",$divisions);
				} else {
					$divisions = '';
				}
				if(!empty($customer_type) && $customer_type!='null') {
					$customer_type = @explode(",",$customer_type);
				} else {
					$customer_type = '';
				}
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
				
				$pjtstage 		= $inputData['pjtstage'];
				$cust     		= $inputData['customer'];
				$service 		= $inputData['service'];
				$practice 		= $inputData['practice'];
				$datefilter 	= $inputData['datefilter'];
				$from_date		= $inputData['from_date'];
				$to_date  		= $inputData['to_date'];
				$divisions  	= $inputData['divisions'];
				$customer_type  = $inputData['customer_type'];
				
				if(!empty($pjtstage) && $pjtstage!='null') {
					$pjtstage = @explode(",",$pjtstage);
				} else {
					$pjtstage = '';
				}
				if(!empty($cust) && $cust!='null') {
					$cust = @explode(",",$cust);
				} else {
					$cust = '';
				}
				if(!empty($service) && $service!='null') {
					$service = @explode(",",$service);
				} else {
					$service = '';
				}
				if(!empty($practice) && $practice!='null') {
					$practice = @explode(",",$practice);
				} else {
					$practice = '';
				}
				if(!empty($divisions) && $divisions!='null') {
					$divisions = @explode(",",$divisions);
				} else {
					$divisions = '';
				}
				if(!empty($customer_type) && $customer_type!='null') {
					$customer_type = @explode(",",$customer_type);
				} else {
					$customer_type = '';
				}
			}
		}

	    /*
		 *$pjtstage - lead_stage. $pm_acc - Project Manager Id. $cust - Customers Id.(custid_fk)
		 */
		if ($keyword == 'false' || $keyword == 'undefined') {
			$keyword = 'null';
		}
		$getProjects	   = $this->project_model->get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,false,$divisions,$customer_type);
		
		// echo $this->db->last_query(); die;

		$data['pjts_data'] = $this->getProjectsDataByDefaultCurrency($getProjects);

		//for field restriction
		$db_fields 			  = $this->project_model->get_dashboard_field($this->userdata['userid']);
		if(!empty($db_fields) && count($db_fields)>0) {
			foreach($db_fields as $record) {
				$data['db_fields'][] = $record['column_name'];
			}
		}
		$this->load->view('projects/projects_view_inprogress', $data);
	}
	
	/*
	 *Advanced Search For Projects
	 */
	public function advanceFilterMetrics($searchId = false)
	{
		$keyword = null;
		
		if($searchId != '' & is_numeric($searchId)) {
			$wh_condn = array('search_id'=>$searchId, 'search_for'=>2, 'user_id'=>$this->userdata['userid']);
			$get_rec  = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
			if(!empty($get_rec))
			$inputData	= real_escape_array($get_rec);
			$inputData['keyword']	= $this->input->post('keyword');

			if((!empty($inputData['pjtstage'])) && $inputData['pjtstage']!='null')
			$pjtstage = explode(",",$inputData['pjtstage']);
			else 
			$pjtstage = '';
			
			if((!empty($inputData['customer'])) && $inputData['customer']!='null')
			$cust = explode(",",$inputData['customer']);
			else
			$cust = '';
			
		} else {
			$inputData = real_escape_array($this->input->post());
			$keyword   = $inputData['keyword'];
			if((!empty($inputData['stages'])) && $inputData['stages']!='null')
			$pjtstage = explode(",",$inputData['stages']);
			else 
			$pjtstage = '';
			
			if((!empty($inputData['customers'])) && $inputData['customers']!='null')
			$cust = explode(",",$inputData['customers']);
			else
			$cust = '';
		}

		if((!empty($inputData['service'])) && $inputData['service']!='null')
		$service = explode(",",$inputData['service']);
		else
		$service = '';
		
		if((!empty($inputData['practice'])) && $inputData['practice']!='null')
		$practice = explode(",",$inputData['practice']);
		else
		$practice = '';
		
		if((!empty($inputData['divisions'])) && $inputData['divisions']!='null')
		$divisions = explode(",",$inputData['divisions']);
		else
		$divisions = '';
		
		if(!empty($inputData['from_date']))
		$from_date = $inputData['from_date'];
		else
		$from_date = '';
		
		if(!empty($inputData['to_date']))
		$to_date = $inputData['to_date'];
		else
		$to_date = '';
		
		if(!empty($inputData['dateinputData']))
		$datefilter = $inputData['dateinputData'];
		else
		$datefilter = '';
		
 		$posted_data=$this->input->post();
		if(!array_key_exists('metrics_year',$inputData))
		{
			$inputData['metrics_year']=$posted_data['metrics_year'];
		}
		if(!array_key_exists('metrics_month',$inputData))
		{
		   $inputData['metrics_month']=$posted_data['metrics_month'];
		}
		if(!empty($inputData['metrics_year'])) {
			$metrics_year = $inputData['metrics_year'];
			$metrics_date = date('Y-m-01', strtotime($inputData['metrics_year'].'-'.$inputData['metrics_month']));
		} else {
			$metrics_date = '';
		}
		
		if ($keyword == 'false' || $keyword == 'undefined') {
			$keyword = 'null';
		}
		
		$getProjects	   = $this->project_model->get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,$billing_type=2, $divisions);
		$data['pjts_data'] = $this->getProjectsDataByDefaultCurrency($getProjects,$project_type=3,$metrics_date);
		
		//for field restriction
		$db_fields 			  = $this->project_model->get_dashboard_field($this->userdata['userid']);
		if(!empty($db_fields) && count($db_fields)>0) {
			foreach($db_fields as $record) {
				$data['db_fields'][] = $record['column_name'];
			}
		}
		
		$this->load->view('projects/projects_view_inprogress_monthly', $data);
	}
	
		/*
	 * Display the Project
	 * @access public
	 * @param int $id - Job Id
	 */
	public function view_project($id = 0)
	{
		// ini_set("display_errors",1);
		// error_reporting(1);
        $this->load->helper('text');
		$this->load->helper('fix_text');
		$usernme = $this->session->userdata('logged_in_user');

		if ($usernme['role_id'] == 1 || $usernme['role_id'] == 2) {
			$data['chge_access'] = 1;
		} else {
			$data['chge_access'] = $this->project_model->get_access($id, $usernme['userid']);
		}
		
		$result = $this->project_model->get_quote_data($id);
		
		if(!empty($result)) {
			
			$data['quote_data']		= $result[0];
			$data['view_quotation'] = true;
			
			//get customers & company
			$data['company_det'] = $this->welcome_model->get_company_det($data['quote_data']['companyid']);
			$data['contact_det'] = $this->welcome_model->get_contact_det($data['quote_data']['companyid']);

			$data['timesheetProjectType']   = array();
			$data['timesheetProjectLead']   = array();
			$data['timesheetAssignedUsers'] = array();

			if (!strstr($data['quote_data']['log_view_status'], $this->userdata['userid'])) {
				$log_view_status['log_view_status'] = $data['quote_data']['log_view_status'] . ':' . $this->userdata['userid'];
				$logViewStatus = $this->project_model->updt_log_view_status($id, $log_view_status);
			}
			
			$data['user_accounts'] = $this->project_model->get_users();
			$user_details = array();
			if(!empty($data['user_accounts'])){
				foreach($data['user_accounts'] as $user=>$userdet){
					$user_details[strtolower($userdet['username'])] = $userdet;
				}
			}
			
			$data['practices'] 		 = $this->project_model->get_practices();
			$data['lead_services'] 	 = $this->project_model->get_lead_services();
			
			$data['pm_accounts'] = array();
			$pjt_managers = $this->project_model->get_user_byrole(3);
			
			if(!empty($pjt_managers))
			$data['pm_accounts'] = $pjt_managers;
			
			if ($data['quote_data']['payment_terms'] == 1)
			{
				$data['payment_data'] = $this->project_model->get_expect_payment_terms($data['quote_data']['lead_id']);
			}
			
			$deposits = $this->project_model->get_deposits_data($data['quote_data']['lead_id']);
			if (!empty($deposits))
			{
				$data['deposits_data'] = $deposits;
			}
			
			/**
			 * Get the files associated with this job
			 */
			$fcpath = UPLOAD_PATH; 
		    $f_dir = $fcpath . 'files/' . $id . '/'; 
			$get_parent_folder_id = $this->request_model->getParentFfolderId($id,$parent=0);
			
			$data['project_members'] = $this->request_model->get_project_members($id); // This array to get a project normal members(Developers) details
			
			if(!empty($get_parent_folder_id)){			
				$data['parent_ffolder_id'] = $get_parent_folder_id['folder_id'];
			} else {
				//creating files folder name
				$f_dir = UPLOAD_PATH.'files/';
				if (!is_dir($f_dir)) {
					mkdir($f_dir);
					chmod($f_dir, 0777);
				}
				
				//creating lead_id folder name
				$f_dir = $f_dir.$id;
				if (!is_dir($f_dir)) {
					mkdir($f_dir);
					chmod($f_dir, 0777);
				}
				$ins = array('lead_id'=>$id,'folder_name'=>$id,'parent'=>0,'created_by'=>$this->userdata['userid']);
				$data['parent_ffolder_id'] = $this->request_model->get_id_by_insert_row('file_management', $ins);
			}
			
			/**
			 * Get the URLs associated with this job
			 */
			$data['job_urls_html'] = $this->project_model->get_job_urls($id);

			$timesheet		 = array();
			$ts_team_members = array();
			$team_mem 		 = array();
			
			if(!empty($data['quote_data']['pjt_id'])) {
				$bill_type = $data['quote_data']['billing_type'];
				$timesheet = $this->project_model->get_timesheet_data($data['quote_data']['pjt_id'], $id, $bill_type, '', $groupby_type=2);
				// echo '<pre>'; print_r($timesheet); die;

				$data['timesheetProjectType']   = $this->project_model->get_timesheet_project_type($data['quote_data']['pjt_id']);
				$data['timesheetProjectLead']   = $this->project_model->get_timesheet_project_lead($data['quote_data']['pjt_id']);
				$timesheet_users = $this->project_model->get_timesheet_users($data['quote_data']['pjt_id']);
				if(count($timesheet_users['name'])>0) {
					$data['timesheetAssignedUsers'] = $timesheet_users['name'];
				}

				//Set the Project Manager in our CRM DB.
				if(!empty($data['timesheetProjectLead']) && count($data['timesheetProjectLead'])>0) {
					$proj_leader = $user_details[$data['timesheetProjectLead']['proj_leader']]['userid'];
					if($proj_leader != $data['quote_data']['assigned_to']){
						$condn = array('lead_id' => $data['quote_data']['lead_id']);
						$updt  = array('assigned_to' => $proj_leader);
						$setPM = $this->project_model->update_row('leads', $updt, $condn);
						$data['quote_data']['assigned_to'] = $proj_leader;
					}
				}
				
				$contract_users = $this->project_model->get_contract_users($id);
				if(!empty($contract_users) && count($contract_users)>0) {
					foreach($contract_users as $teamMem) {
						$team_mem[] = $teamMem['userid_fk'];
					}
				}
				
				if(!empty($timesheet_users['username']) && count($timesheet_users['username'])>0) {
					foreach($timesheet_users['username'] as $u_name) {
						if(!empty($user_details[strtolower($u_name)]['userid'])) {
							$ts_team_members[] = $user_details[strtolower($u_name)]['userid'];
						}
					}
				}
				
				//Set the Project Team Members in our CRM DB.
				$result = $this->identical_values($team_mem,$ts_team_members);
				if(!$result) {
					$wh_condn = array('jobid_fk'=>$data['quote_data']['lead_id']);
					$this->db->delete($this->cfg['dbpref'].'contract_jobs',$wh_condn);
					
					$inse['jobid_fk']  =  $data['quote_data']['lead_id'];
					foreach($ts_team_members as $ts){
						$inse['userid_fk'] 	 =  $ts;
						$inse['modified_by'] =  $this->userdata['userid'];
						$this->db->insert($this->cfg['dbpref'].'contract_jobs',$inse);
					}
				}
			}
			
			//For list the particular lead owner, project manager & lead assigned_to in the welcome_view_project page.
			$data['list_users'] 		 = $this->project_model->get_list_users($id);
			$data['category_listing_ls'] = $this->project_model->getTaskCategoryList();
			$data['task_stages'] 		 = $this->request_model->get_task_stages();
			
			//For list the particular project team member in the welcome_view_project page.
			$data['contract_users'] 	= $this->project_model->get_contract_users($id);
			$data['stake_holders']  	= $this->project_model->get_stake_holders($id);

			$rates = $this->get_currency_rates();

			$data['timesheet_data'] = array();
				
			if(count($timesheet)>0) {
				foreach($timesheet as $ts) {
					if(isset($ts['cost'])) {
						$financialYear      = get_current_financial_year($ts['yr'],$ts['month_name']);
						$max_hours_resource = get_practice_max_hour_by_financial_year($ts['practice_id'],$financialYear);
						
						$data['timesheet_data'][$ts['username']]['practice_id'] = $ts['practice_id'];
						$data['timesheet_data'][$ts['username']]['max_hours'] 	= $max_hours_resource->practice_max_hours;
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
						//$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
						$rateCostPerHr = $ts['cost'];
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
						
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']]['total_hours'] = get_timesheet_hours_by_user($ts['username'],$ts['yr'],$ts['month_name'],array('Leave','Hol'));
						// echo $ts['practice_id'].'-'.$financialYear.'-'.$ts['username'].'-'.$rateCostPerHr.'-'.get_timesheet_hours_by_user($ts['username'],$ts['yr'],$ts['month_name'],array('Leave','Hol')).'<br>';
					}
				}
			}

			$data['project_costs'] = array();
			
			if(!empty($data['timesheet_data'])) {
				// $res = $this->calcActualProjectCost($data['timesheet_data']);
				$res = $this->calcActualProjectCostBaseCurrency($data['timesheet_data'], $data['quote_data']['expect_worth_id']);
				if($res['total_cost']>0) {
					// $data['project_costs'] = $this->conver_currency($res['total_cost'], $rates[1][$data['quote_data']['expect_worth_id']]);
					$data['project_costs'] = $res['total_cost'];
				}
				if($res['total_hours']>0) {
					$data['actual_hour_data'] = $res['total_hours'];
				}
			}
			
			/*get the other cost*/
			// $data['othercost_val'] = getOtherCostByLeadIdBasedProjectCurrency($id, $this->default_cur_id);
			$data['othercost_val'] = getOtherCostByLeadIdBasedProjectCurrency($id, $data['quote_data']['expect_worth_id']);
		
			//Intially Get all the Milestone data
			$data['milestone_data'] = $this->project_model->get_milestone_terms($id);
			
			/**
			*@Initiate to get all departments data
			**/
			$data['departments'] = $this->department_model->get_departments_list(array('active'=>1));
			
			/**
			*@Initiate to get all project types data
			**/
			$data['project_types'] = $this->project_types_model->get_project_types_list(array('status'=>1));
			
			/**
			*@Initiate to get all project types data from timesheet database
			**/
			$data['timesheet_project_types'] = $this->project_model->get_timesheet_project_types();
			
			/**
			*@Initiate to get all cost center data
			**/
			$data['arr_cost_center'] = $this->cost_center_model->get_cost_center_list(array('status'=>1));
			
			/**
			*@Initiate to get all billing category data from timesheet database
			**/
			$data['billing_categories'] = $this->project_model->get_billing_types();
			
			/**
			*@Initiate to get all profit center data
			**/
			$data['arr_profit_center'] = $this->profit_center_model->get_profit_center_list(array('status'=>1));
			
			$data['all_users'] = $this->project_model->get_all_users();
			$currencies= $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$data['base_currency'][$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			
			$data['currencies'] = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));

			/**
			get the project variance report from timesheet
			**/
			$data['timesheet_variance'] = '';
			$timesheet_db = $this->load->database('timesheet', TRUE);		
			$project_code_ts = $data['quote_data']['pjt_id'];
		
			$qry_pv = $timesheet_db->query("SELECT tt.task_id,tt.name as taskName, sum(pte.prj_task_hours) As EstimatedHours, pe.prj_est_id,pe.proj_est_name,pte.prj_est_id,pte.proj_id,pte.task_id,(select sum(tim.duration)/60 from ".$timesheet_db->dbprefix('times')." As tim where tim.proj_id = tt.proj_id and tim.task_id = tt.task_id) As actualHours from ".$timesheet_db->dbprefix('task')." AS tt LEFT JOIN ".$timesheet_db->dbprefix('project_task_estimation')." AS pte ON pte.task_id = tt.task_id LEFT JOIN ".$timesheet_db->dbprefix('project_estimation')." AS pe ON pe.prj_est_id = pte.prj_est_id  left join ".$timesheet_db->dbprefix('project')." as prj on prj.proj_id = tt.proj_id WHERE prj.project_code='".$project_code_ts."' group by tt.task_id");
			// echo $timesheet_db->last_query();exit;
			if($qry_pv->num_rows()>0){
				$res_pv 					= $qry_pv->result();
				$data['timesheet_variance'] = $res_pv;
			}
			$timesheet_db->close();
			
			/**
			get the bug summary from the redmine
			**/
			$support_db = $this->load->database("redmine", true);
		
			$data['bug_status'] = '';
			$data['bug_severity'] = '';
			$data['bug_category'] = '';
			if($support_db){
				$support_db->select('id,name,parent_id,identifier');
				$qry = $support_db->get_where($support_db->dbprefix("projects"),array("identifier" => $data['quote_data']['pjt_id']));
				if($qry->num_rows()>0){
					$res 		= $qry->result();
					$pjtIds 	= array();					
					$pjtNames 	= array();					
					$pnames_arr = array();					
					foreach($res as $r){
						$pjtIds[] = $r->id;
					}
					$pjtIds = array_unique($pjtIds);
					echo '<pre>';print_r($pjtIds);exit;
					$AllPjtIds = implode(",",$pjtIds);
					
					$data['AllPjtIds'] = $pjtIds;
					$parent_proj =  $pjtIds[0];
					$parent_proj =  $pjtIds[0];
					
					$data['AllPjtIds_summary'] = $parent_proj;
					
					// get project wise report
					$support_db->select("id,name");
					$support_db->where_in("id",$pjtIds);
					$pNames = $support_db->get($support_db->dbprefix("projects"));
					if($pNames->num_rows()>0){
						$pNamesRes = $pNames->result();
						foreach($pNamesRes as $pnames){
							$pnames_arr[$pnames->id] = $pnames->name;
						}
						$data['project_names'] = $pnames_arr;
					}
					
					
					$qry_project = $support_db->query("SELECT project_id, status_id, COUNT( status_id ) AS bugcount FROM ".$support_db->dbprefix("issues")." where project_id in ($AllPjtIds) GROUP BY project_id, status_id ORDER BY project_id asc");
					if($qry_project->num_rows()>0) {
						$data['bug_project'] = $qry_project->result();
					}					
					
					//get all bug list based on the status
					$qry_status = $support_db->query("SELECT COUNT(id) as bugcount, status_id FROM ".$support_db->dbprefix("issues")." WHERE project_id IN ($AllPjtIds) GROUP BY status_id ORDER BY status_id") ;
					if($qry_status->num_rows()>0) {
						$data['bug_status'] = $qry_status->result();
					}
					
					// get all the bug list based on the severity
					$qry_severity = $support_db->query("SELECT project_id,COUNT(id) as bugcount, priority_id ,status_id FROM ".$support_db->dbprefix("issues")." WHERE project_id IN ($AllPjtIds) GROUP BY priority_id ,status_id ORDER BY project_id asc ");
					if($qry_severity->num_rows()>0){
						$data['bug_severity'] = $qry_severity->result();
					}
					
					// get all severity
					$qry_all_severity = $support_db->query("SELECT id,name FROM ".$support_db->dbprefix("enumerations")." WHERE type = 'IssuePriority' GROUP BY id ORDER BY id asc ");
					if($qry_all_severity->num_rows()>0){
						$all_severities = $qry_all_severity->result();
						$all_severities_array =array();
						foreach($all_severities as $all_severity){
							$all_severities_array[$all_severity->id] = $all_severity->name;
						}
						$data['all_severity'] = $all_severities_array;
					}
 					
					//get all bug list based on the category
					$qry_category = $support_db->query("SELECT COUNT(b.id) as bugcount, c.name AS category_name, b.category_id, b.status_id FROM ".$support_db->dbprefix("issues")." b JOIN ".$support_db->dbprefix("issue_categories")." AS c ON b.category_id=c.id WHERE b.project_id IN ($AllPjtIds) GROUP BY category_id, c.name, b.status_id ORDER BY category_id, c.name, b.status_id");
					if($qry_category->num_rows()>0){
						$data['bug_category'] = $qry_category->result_array();
					}
					
				}
				$support_db->close();	
			}
			
			$gantt_chart_data	= $this->check_gantt_chart_data($id);
			$milestones_data	= $this->check_milestones_data($id);
			
			if($gantt_chart_data==0) $data['show_gantt_chart']=0;
			else $data['show_gantt_chart']=1;
			
			if($milestones_data==0) $data['show_milestones']=0;
			else $data['show_milestones']=1;
			
			$user_id 					= $this->userdata['userid'];
			$data['email_templates'] 	= $this->project_model->get_user_email_templates($user_id);
			$data['email_signatures'] 	= $this->project_model->get_user_email_signatures($user_id);
			$data['default_signature'] 	= $this->project_model->get_user_default_signature($user_id);
			
            $this->load->view('projects/welcome_view_project', $data);
        }
        else
        {
			$this->session->set_flashdata('login_errors', array("Project does not exist."));
			redirect('project');
        }
    }
	
	/*
	* Get the Other Cost details
	*/
	public function getOtherCostData($project_id, $grid=false)
	{
		$data['currency_arr'] 	 = array();
		$data['project_id'] 	 = $project_id;
		$project_det 			 = $this->project_model->get_lead_det($project_id);
		$data['base_currency'] 	 = $project_det['expect_worth_id'];
		$data['currencies'] 	 = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		if(!empty($data['currencies'])) {
			foreach($data['currencies'] as $curr){
				$data['currency_arr'][$curr['expect_worth_id']] = $curr['expect_worth_name'];
			}
		}
		$data['other_cost_data'] = $this->project_model->getOtherCost($project_id);
		if($grid==true){
			$result = $this->load->view("projects/add_other_cost_grid", $data, true);
		} else {
			$result = $this->load->view("projects/add_other_cost", $data, true);
		}
		echo $result;
		exit;
	}
	
		
	/*
	* Get the Actual cost details(utilization cost + other cost) for the project
	*/
	public function getAcutalCostDataForProject()
	{
		$project_id			= $this->input->post('project_id');
		$data				= array();
		$cost_value			= array();
		$result 			= $this->project_model->get_quote_data($project_id);
		$data['quote_data'] = $result[0];
		$pjt_id 			= $data['quote_data']['pjt_id'];
		$bill_type 			= $data['quote_data']['billing_type'];
		$timesheet 			= $this->project_model->get_timesheet_data($pjt_id, $project_id, $bill_type, '', $groupby_type=2);
		$data['timesheet_data'] = array();
		
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				if(isset($ts['cost'])) {
					$financialYear = get_current_financial_year($ts['yr'],$ts['month_name']);
					$max_hours_resource = get_practice_max_hour_by_financial_year($ts['practice_id'],$financialYear);
					
					$data['timesheet_data'][$ts['username']]['practice_id'] = $ts['practice_id'];
					$data['timesheet_data'][$ts['username']]['max_hours'] = $max_hours_resource->practice_max_hours;
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $ts['cost'];
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
					
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']]['total_hours'] =get_timesheet_hours_by_user($ts['username'],$ts['yr'],$ts['month_name'],array('Leave','Hol'));
				}
			}
		}
		
		if(!empty($data['timesheet_data'])) {
			/*changed based on user max hours calculation*/
			$res = $this->calcActualProjectCostBaseCurrency($data['timesheet_data'], $data['quote_data']['expect_worth_id']);
			if($res['total_cost']>0) {
				$cost_value['utilization_cost'] = $res['total_cost'];
			}
			if($res['total_hours']>0) {
				$cost_value['actual_hour_data'] = $res['total_hours'];
			}
		}

		$data['othercost_val'] = getOtherCostByLeadIdBasedProjectCurrency($project_id, $data['quote_data']['expect_worth_id']);
		
		$cost_value['other_cost'] 			= (!empty($data['othercost_val'])) ? sprintf('%0.2f', $data['othercost_val']) : 0;
		$cost_value['utilization_cost'] 	= (!empty($cost_value['utilization_cost'])) ? sprintf('%0.2f', $cost_value['utilization_cost']) : 0;
		$cost_value['project_cost'] 		= $cost_value['utilization_cost'] + $cost_value['other_cost'];
		$varianceProjectVal 				= $data['quote_data']['actual_worth_amount'] - $cost_value['project_cost'];
		$cost_value['varianceProjectVal'] 	= sprintf('%0.2f', $varianceProjectVal);
		
		echo json_encode($cost_value);
		exit;
	}
	
	/*	
	* Inserting the other cost
	*/
	public function addOtherCost()
	{
		$ins_val = array();
		$ins_val['project_id'] 			= $this->input->post('project_id');
		$ins_val['description'] 		= $this->input->post('description');
		$ins_val['cost_incurred_date'] 	= ($this->input->post('cost_incurred_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('cost_incurred_date'))) : '';
		$ins_val['currency_type'] 		= $this->input->post('currency_type');
		$ins_val['value'] 				= $this->input->post('value');
		$ins_val['created_by'] 			= $this->userdata['userid'];
		$ins_val['created_on'] 			= date('Y-m-d H:i:s');
		$ins_val['modified_by'] 		= $this->userdata['userid'];
		$ins_val['modified_on'] 		= date('Y-m-d H:i:s');
		$insert_cost = $this->project_model->return_insert_id('project_other_cost', $ins_val);
		if($insert_cost) {
			//do log
			$currencies = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			$uploaded_file = $this->input->post('file_id');
			//map uploaded file, if exists
			if(!empty($uploaded_file) && count($uploaded_file)>0) {
				$oc_file 					= array();
				$oc_file['other_cost_id'] 	= $insert_cost;
				foreach($uploaded_file as $file_id) {
					$oc_file['file_id'] 	= $file_id;
					$this->project_model->insert_row("other_cost_attach_file", $oc_file);
				}
			}
			
			$log_detail = "Added Other Cost: \n";
			$log_detail .= "\nDescription: ".$this->input->post('description');
			$log_detail .= "\nCost Incurred Date: ".$this->input->post('cost_incurred_date');
			$log_detail .= "\nValue: ".$all_cur[$ins_val['currency_type']].' '.number_format($ins_val['value'], 2);
			$log = array();
			$log['jobid_fk']      = $this->input->post('project_id');
			$log['userid_fk']     = $this->userdata['userid'];
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$this->project_model->insert_row("logs", $log);
			echo "success";
		} else {
			echo "error";
		}
	}
	
	/*
	* editing the other cost
	*/
	public function getEditOtherCostData()
	{
		$data 		 = array();
		$editdata 	 = array();
		$data['msg'] = 'error'; 
		$wh_condn	 = array('id'=>$this->input->post('costid'),'project_id'=>$this->input->post('projectid'));
		$editdata['cost_data'] 	 = $this->project_model->get_data_by_id('project_other_cost', $wh_condn);
		if(!empty($editdata['cost_data']) && count($editdata['cost_data'])>0) 
		{
			$editdata['project_id']    = $this->input->post('projectid');
			$editdata['attached_file'] = $this->project_model->get_oc_attached_files($this->input->post('costid'));
			$editdata['currencies']    = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			$data['res'] = $this->load->view("projects/edit_other_cost_form", $editdata, true);
			$data['msg'] = 'success';
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	* editing the other cost data
	*/
	public function editOtherCost()
	{
		$updt_val = array();
		$updt_val['description'] 		= $this->input->post('description');
		$updt_val['cost_incurred_date'] = ($this->input->post('cost_incurred_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('cost_incurred_date'))) : '';
		$updt_val['currency_type'] 		= $this->input->post('currency_type');
		$updt_val['value'] 				= $this->input->post('value');
		$updt_val['modified_by'] 		= $this->userdata['userid'];
		$updt_val['modified_on'] 		= date('Y-m-d H:i:s');
		$condn = array('id'=>$this->input->post('cost_id'), 'project_id'=>$this->input->post('project_id'));
		$this->project_model->delete_row('other_cost_attach_file', array("other_cost_id"=>$this->input->post('cost_id')));
		$update_cost = $this->project_model->update_row('project_other_cost', $updt_val, $condn);
		$up_file = $this->input->post('file_id');
		if(!empty($up_file)){
			$attach_updt['other_cost_id'] 	= $this->input->post('cost_id');
			foreach($up_file as $ocfile) {
				$attach_updt['file_id'] 	= $ocfile;
				$this->project_model->insert_row('other_cost_attach_file', $attach_updt);
			}
		}
		
		if($update_cost){
			echo "success";
			//do log
			$currencies = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			$log_detail = "Updated the Other Cost: \n";
			$log_detail .= "\nDescription: ".$updt_val['description'];
			$log_detail .= "\nCost Incurred Date: ".$this->input->post('cost_incurred_date');
			$log_detail .= "\nValue: ".$all_cur[$updt_val['currency_type']].' '.number_format($updt_val['value'], 2);
			$log = array();
			$log['jobid_fk']      = $this->input->post('project_id');
			$log['userid_fk']     = $this->userdata['userid'];
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->project_model->insert_row("logs", $log);
		} else {
			echo "error";
		}
		exit;
	}
	
		/*
	* Deleting the other cost
	* @params costid & project id
	* return json encoded array.
	*/
	public function deleteOtherCostData()
	{
		//save the old values
		$wh_condn	 = array('id'=>$this->input->post('costid'),'project_id'=>$this->input->post('projectid'));
		$editdata 	 = $this->project_model->get_data_by_id('project_other_cost', $wh_condn);
		
		$data = array();
		$wh_condn = array('id'=>$this->input->post('costid'), 'project_id'=>$this->input->post('projectid'));
		$delOtherCost = $this->project_model->delete_row('project_other_cost', $wh_condn);
		if($delOtherCost) {
			//do log
			$currencies = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			$log_detail = "Deleted the Other Cost: \n";
			$log_detail .= "\nDescription: ".$editdata['description'];
			$log_detail .= "\nCost Incurred Date: ".date('d-m-Y', strtotime($editdata['cost_incurred_date']));
			$log_detail .= "\nValue: ".$all_cur[$editdata['currency_type']].' '.number_format($editdata['value'], 2);
			$log = array();
			$log['jobid_fk']      = $this->input->post('projectid');
			$log['userid_fk']     = $this->userdata['userid'];
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->project_model->insert_row("logs", $log);
			$data['res'] = 'success';
		} else {
			$data['res'] = 'failure';
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	*Check the two arrays
	*/
	function identical_values( $arrayA , $arrayB ) {
		sort( $arrayA );
		sort( $arrayB );
		return $arrayA == $arrayB;
	}
	
	function calcActualProjectCost($timesheet_data)
	{
		$total_billable_hrs		= 0;
		$total_non_billable_hrs = 0;
		$total_internal_hrs		= 0;
		$data['total_cost']		= 0;
		
		foreach($timesheet_data as $key1=>$value1) {
			$resource_name = $key1;
			$max_hours = $value1['max_hours'];
			if(is_array($value1) && count($value1)>0) {
				foreach($value1 as $key2=>$value2) {
					$year = $key2;
					if(is_array($value2) && count($value2)>0) {
						foreach($value2 as $key3=>$value3) {
							$individual_billable_hrs		= 0;
							$month		 	  = $key3;
							$billable_hrs	  = 0;
							$non_billable_hrs = 0;
							$internal_hrs	  = 0;
							if(is_array($value3) && count($value3)>0) {
								foreach($value3 as $key4=>$value4) {
									switch($key4) {
										case 'Billable':
											$rs_name			 = $value4['rs_name'];
											$rate				 = $value4['rateperhr'];
											$billable_hrs		 = $value4['duration'];
											$individual_billable_hrs += $billable_hrs;
											$total_billable_hrs 	+= $billable_hrs;
										break;
										case 'Non-Billable':
											$rs_name				 = $value4['rs_name'];
											$rate					 = $value4['rateperhr'];
											$non_billable_hrs		 = $value4['duration'];
											$individual_billable_hrs 	+= $non_billable_hrs;
											$total_non_billable_hrs 	+= $non_billable_hrs;
										break;
										case 'Internal':
											$rs_name			 = $value4['rs_name'];
											$rate				 = $value4['rateperhr'];
											$internal_hrs 		 = $value4['duration'];
											$individual_billable_hrs += $internal_hrs;
											$total_internal_hrs 	 += $internal_hrs;
										break;
									}
								}
							}
						
							$individual_billable_hrs = $value3['total_hours'];
							 
							// calculation for the utilization cost based on the master hours entered.
							$rate1 = $rate;
							if($individual_billable_hrs>$max_hours){
								$percentage = ($max_hours/$individual_billable_hrs);
								$rate1 = number_format(($percentage*$rate),2);
							}
							
							$data['total_cost'] += $rate1 * ($billable_hrs + $internal_hrs + $non_billable_hrs);
						}
					}
				}
			}
		}
		$data['total_billable_hrs']		= $total_billable_hrs;
		$data['total_internal_hrs']	    = $total_internal_hrs;
		$data['total_non_billable_hrs'] = $total_non_billable_hrs;
		$data['total_hours']			= $total_billable_hrs+$total_internal_hrs+$total_non_billable_hrs;
		return $data;
	}
	
	function calcActualProjectCostBaseCurrency($timesheet_data, $project_currency)
	{
		$month_nums = array('january' => '01', 'february'=>'02', 'march'=>'03', 'april'=>'04', 'may'=>'05', 'june'=>'06', 'july'=>'07', 'august'=>'08', 'september'=>'09', 'october'=>'10', 'november'=>'11', 'december'=>'12');
		$timesheet_currency 	= 1; //timesheet currency type - usd
		
		$bk_rates = get_book_keeping_rates(); //get all the book keeping rates
		
		$total_billable_hrs		= 0;
		$total_non_billable_hrs = 0;
		$total_internal_hrs		= 0;
		$data['total_cost']		= 0;
		
		foreach($timesheet_data as $key1=>$value1) {
			$resource_name = $key1;
			$max_hours = $value1['max_hours'];
			if(is_array($value1) && count($value1)>0) {
				foreach($value1 as $key2=>$value2) {
					if(is_array($value2) && count($value2)>0) {
						$year = $key2;
						foreach($value2 as $key3=>$value3) {
							$individual_billable_hrs		= 0;
							$month		 	  = $key3;
							$billable_hrs	  = 0;
							$non_billable_hrs = 0;
							$internal_hrs	  = 0;
							if(is_array($value3) && count($value3)>0) {
								foreach($value3 as $key4=>$value4) {
									switch($key4) {
										case 'Billable':
											$rs_name			 = $value4['rs_name'];
											$rate				 = $value4['rateperhr'];
											$billable_hrs		 = $value4['duration'];
											$individual_billable_hrs += $billable_hrs;
											$total_billable_hrs 	+= $billable_hrs;
										break;
										case 'Non-Billable':
											$rs_name				 = $value4['rs_name'];
											$rate					 = $value4['rateperhr'];
											$non_billable_hrs		 = $value4['duration'];
											$individual_billable_hrs 	+= $non_billable_hrs;
											$total_non_billable_hrs 	+= $non_billable_hrs;
										break;
										case 'Internal':
											$rs_name			 = $value4['rs_name'];
											$rate				 = $value4['rateperhr'];
											$internal_hrs 		 = $value4['duration'];
											$individual_billable_hrs += $internal_hrs;
											$total_internal_hrs 	 += $internal_hrs;
										break;
									}
								}
							}
						
							$individual_billable_hrs = $value3['total_hours'];
							 
							// calculation for the utilization cost based on the master hours entered.
							$rate1 = $rate;  // rate in usd, so we need to change to project based currrency
							
							$mon_name 		= strtolower($month);
							$mon 			= $month_nums[$mon_name];
							$mon 			= isset($mon) ? $mon : date('m');
							$input_date 	= $year.'-'.$mon.'-01';
							$financialYear  = getFiscalYearForDate(date("m/d/y", strtotime($input_date)),"4/1","3/31");
							$get_rate  		= $bk_rates[$financialYear][$timesheet_currency][$project_currency];
							
							$rate1 = $this->conver_currency($rate1, $get_rate); // converted rate value.
							
							if($individual_billable_hrs>$max_hours){
								$percentage = ($max_hours/$individual_billable_hrs);
								$rate1 = number_format(($percentage*$rate1),2);
							}
							$data['total_cost'] += $rate1 * ($billable_hrs + $internal_hrs + $non_billable_hrs);
						}
					}
				}
			}
		}
		$data['total_billable_hrs']		= $total_billable_hrs;
		$data['total_internal_hrs']	    = $total_internal_hrs;
		$data['total_non_billable_hrs'] = $total_non_billable_hrs;
		$data['total_hours']			= $total_billable_hrs+$total_internal_hrs+$total_non_billable_hrs;
		
		// echo "<pre>"; print_r($data); die;
		return $data;
		
	}
	
	/*
	*Get the logs
	*/
	function getLogs($id)
	{
		$data['log_html'] = '';
		$getLogsData = $this->project_model->get_logs($id);
		// echo "<pre>"; print_r($getLogsData); exit;
		$data['log_html'] .= '<table width="100%" id="lead_log_list" class="log-container logstbl">';
		$data['log_html'] .= '<thead><tr><th>&nbsp;</th></tr></thead><tbody>';
		
		if (!empty($getLogsData)) {
			$log_data = $getLogsData;
			$this->load->helper('url');
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			foreach ($log_data as $ld) {
				$wh_condn = array('userid'=>$ld['userid_fk']);
				$user_data = $this->project_model->get_user_data_by_id('users', $wh_condn);
				
				if (count($user_data) < 1) {
					echo '<!-- ', print_r($ld, TRUE), ' -->'; 
					continue;
				}
				
				$log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
				
				$fancy_date = date('l, jS F y h:iA', strtotime($ld['date_created']));
				
				$stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';
				
				$table ='<tr id="log" class="log'.$stick_class.'"><td id="log" class="log'.$stick_class.'">
						 <p class="data log'.$stick_class.'"><span class="log'.$stick_class.'">'.$fancy_date.'</span>'.$user_data[0]['first_name'].' '.$user_data[0]['last_name'].'</p>
						 <p class="desc log'.$stick_class.'">'.$ld['log_content'].'</p></td></tr>';
				$data['log_html'] .= $table;
				unset($table, $user_data, $user, $log_content);
			}
		}
		$data['log_html'] .= '</tbody></table>';
		echo $data['log_html'];
	}
	
	/*
	*@method set_practices
	*/
	public function update_title()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['lead_title'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data     = array('lead_title'=>$updt['lead_title']);
			$updt_id = $this->project_model->update_practice('leads', $data, $wh_condn);
			if($updt_id){				
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
	/*
	*@method set_customer
	*
	*/
	public function update_customer()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;
		
		if($updt['customer_id'] != $updt['customer_id_old']){
			$inser['log_content']  = "Customer has changed from ' ".$updt['customer_company_name_old']." ' to ' ".$updt['customer_company_name']." '";
			$inser['jobid_fk']     = $updt['lead_id'];
			$inser['userid_fk']    = $this->userdata['userid'];
			$insert_log			   = $this->welcome_model->insert_row('logs', $inser);
		}

		if (($updt['customer_id'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data     = array('custid_fk'=>$updt['customer_id']);
			$updt_id = $this->project_model->update_practice('leads', $data, $wh_condn);
			if($updt_id){				
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
		/*
	*@method set_practices
	*
	*/
	public function set_practices()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['practice'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data = array('practice' => $updt['practice']);
			$updt_id = $this->project_model->update_practice('leads', $data, $wh_condn);
			if($updt_id) {
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}

	/*
	*@method set_practices
	*
	*/
	public function set_currency()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['currency'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data = array('expect_worth_id' => $updt['currency']);
			$updt_id = $this->project_model->update_practice('leads', $data, $wh_condn);
			if($updt_id) {
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
	public function set_project_manager()
	{
		$updt = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		
		$project_code = $updt['project_code'];
		
		if ($updt['project_manager'] == "")
		{
			$data['error'] = 'Project Manager must not be Null value!';
		}
		else
		{
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$updt1 = array('assigned_to' => $updt['project_manager']);
			$updt_id = $this->project_model->update_row('leads', $updt1, $wh_condn);
			 
			$this->db->select("username");
			$qry = $this->db->get_where($this->cfg['dbpref']."users",array("userid" => $updt['project_manager']));
			$nos = $qry->num_rows();
					 
			if($nos){
				// update in timesheet project table
				$userrow = $qry->row();
				$timesheet_db = $this->load->database('timesheet', TRUE); 
				$timesheet_db->update($timesheet_db->dbprefix('project'),array("proj_leader" => strtolower($userrow->username)),array("project_code" => $project_code));
				$timesheet_db->close();
			}else{
				$data['error'] = 'Project Manager mismatch in Timesheet.';
			}
			
			if($updt_id==0)
			$data['error'] = 'Project Manager Not Updated.';
		}
		echo json_encode($data);
	}
	
	/*
	*Set Project Members in Crm & timesheet db
	*Timesheet DB - Update concept changed on - 23 sep 2016
	*/
	public function set_project_members()
	{
		$updt = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		$ins = array();
		
		$project_team_members 	= $this->input->post('project_team_members');
		$project_code 			= $this->input->post('project_code');
		$lead_id 				= $this->input->post('lead_id');
		
		if ($project_team_members == "")
		{
			$data['error'] = 'Project Members must not be Null value!';
		}
		else
		{
			//update in crm
			// delete the existing assigned users from the contract jobs table before inserting the new things.
			$this->db->delete($this->cfg['dbpref']."contract_jobs",array("jobid_fk" => $lead_id));
			if($project_team_members)
			{
				$ins['jobid_fk'] = $lead_id;
				$ptms = explode(",",$project_team_members);
				
				if(count($ptms)>0){
					// query to get the username from the selected users in crm users table.
					$this->db->select("username");
					$this->db->where_in("userid", $ptms);
					$res_cm_users = $this->db->get($this->cfg['dbpref']."users");
					$rs_cm_users = $res_cm_users->result();
					// echo "Select" . $this->db->last_query() . "<br>"; 
										
					//inserting the assigned users in the contract jobs table.
					foreach($ptms as $pmembers){
						$ins['userid_fk'] 	 = $pmembers;
						$ins['modified_by'] = $this->userdata['userid'];
						$insert = $this->project_model->insert_row('contract_jobs', $ins);
						
					}
					echo "Insert" . $this->db->last_query() . "<br>"; die;
				}
			}
			
			//update in timesheet starts
			// get the project id from project table of timesheet 
			$timesheet_db = $this->load->database('timesheet', TRUE); 
			$qry = $timesheet_db->get_where($timesheet_db->dbprefix('project'),array("project_code" => $project_code));
			
			if($qry->num_rows()) {
				
				// echo "I am here"; exit;
				
				// echo "select ".$this->db->last_query() . "<br>";
				$res_t 				= $qry->row();
				$timesheet_proj_id 	=  $res_t->proj_id;
				$time_ins 			= array();
				$time_ins_task		= array();
				
				/* changed on 23 Sep 2016 */
				$ts_wh_condn = array('proj_id' => $timesheet_proj_id);
				$ts_set_data = array('status' => 1);
				$timesheet_db->update($timesheet_db->dbprefix("assignments"), $ts_set_data, $ts_wh_condn); /*Inactive all the members in the project*/
				
				if(count($rs_cm_users) > 0) { /*set active the current project members in the project*/
					foreach($rs_cm_users as $muser) {
						
						$username = strtolower($muser->username);
						
						// SELECT * FROM $ASSIGNMENTS_TABLE WHERE proj_id='$proj_id' && username ='$username'
						$wh_condn_member = array('proj_id'=>$timesheet_proj_id, 'username'=>$username);
						$get_member = $timesheet_db->get_where($timesheet_db->dbprefix('assignments'), $wh_condn_member);
						if($get_member->num_rows()) { /*if user exist made user to be active*/
							// UPDATE $ASSIGNMENTS_TABLE SET status='0' WHERE proj_id='$proj_id' && username ='$username'
							$ts_set_data_user = array('status' => 0);
							$ts_wh_condn_user = array('username' => $username);
							$timesheet_db->update($timesheet_db->dbprefix("assignments"), $ts_set_data_user, $ts_wh_condn_user);
						} else {
							// INSERT INTO $ASSIGNMENTS_TABLE VALUES ($proj_id, '$username', 1,0)
							$time_ins['proj_id'] 	= $timesheet_proj_id;
							$time_ins['username'] 	= $username;
							$time_ins['rate_id'] 	= 1;
							$time_ins['status'] 	= 0;
							$timesheet_db->insert($timesheet_db->dbprefix("assignments"), $time_ins);
							// Added all the task for assigned users in timesheet 10/7/2015
							$this->project_model->task_timesheet_entry($timesheet_proj_id, $username);
						}						
					}
				}
				
			} else {
				$data['error'] = 'Project Members not Updated in Timesheet!';
			}
			$timesheet_db->close();
			//timesheet db end
			
			if($insert==0)
			$data['error'] = 'Project Members Not Updated.';
		}
		echo json_encode($data);		
	}
	
	public function set_stake_holders()
	{
		$data['error'] 	= FALSE;
		$new_stake_holder_insert = array();
		$new_stake_holder_delete = array();
		$stake_members 	= $this->input->post("stake_members");
		$lead_id 		= $this->input->post("lead_id");
		
		if ($stake_members == "") {
			$data['error'] = 'Stake Holders must not be Null value!';
			echo json_encode($data);
			exit;
		}
		$post_stake_members = @explode(",", $stake_members);
		
		$wh_condn 			= array("lead_id" => $lead_id);
		$exist_stake_member = $this->project_model->get_user_data_by_id('stake_holders', $wh_condn);
		if(is_array($exist_stake_member) && !empty($exist_stake_member) && (count($exist_stake_member)>0)) {
			foreach ($exist_stake_member as $exist_row) {
				$exist_stake_member_arr[] = $exist_row['user_id'];
			}
		}
		
		if(is_array($exist_stake_member_arr) && !empty($exist_stake_member_arr) && count($exist_stake_member_arr)>0) {
			$new_stake_holder_insert = array_diff($post_stake_members, $exist_stake_member_arr);
			$new_stake_holder_delete = array_diff($exist_stake_member_arr, $post_stake_members);
			$new_stake_holder_delete = array_values($new_stake_holder_delete);
		}
		
		$ins  = array();
		$this->db->delete($this->cfg['dbpref']."stake_holders", $wh_condn);
		if(count($post_stake_members) > 0) {
			$ins['lead_id'] = $lead_id;
			foreach($post_stake_members as $sm){
				$ins['user_id'] = $sm;
				$this->project_model->insert_row("stake_holders", $ins);				 
			}
		}
		
		if(!empty($new_stake_holder_insert) && count($new_stake_holder_insert)>0) {
			$get_mail_ids = $this->project_model->get_userlist($new_stake_holder_insert);
			foreach ($get_mail_ids as $m_row) {
				$ntfy_email = $this->project_model->sent_stake_holder_email($m_row['email'], $m_row['first_name'] .' '.$m_row['last_name'], $type='new', $lead_id);
			}
		}
		if(!empty($new_stake_holder_delete) && count($new_stake_holder_delete)>0) {
			$de_get_mail_ids = $this->project_model->get_userlist($new_stake_holder_delete);
			foreach ($de_get_mail_ids as $de_row) {
				$ntfy_email = $this->project_model->sent_stake_holder_email($de_row['email'], $de_row['first_name'] .' '.$de_row['last_name'], $type='delete', $lead_id);
			}
		}
		echo json_encode($data);
		exit;
	}
	
	function chkPjtIdFromdb()
	{	
		$data = real_escape_array($this->input->post());

		$wh_condn = array('pjt_id'=>$data['pjt_id']);
		$stat = $this->project_model->chk_status('leads', $wh_condn);
		if( $stat == 0 )
		echo 'Ok';
		else
		echo 'No';
	}
	
	public function set_project_id()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if ($updt['pjt_id'] == "")
		{
			$data['error'] = 'Id must not be Null value!';
		}
		else
		{
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$updt = array('pjt_id' => $updt['pjt_id']);
			$updt_id = $this->project_model->update_row('leads', $updt, $wh_condn);
			if($updt_id==0)
			$data['error'] = 'Project Id Not Updated.';
		}
		echo json_encode($data);
	}
	
	function chkPjtValFromdb()
	{
		$data = real_escape_array($this->input->post());

		$wh_condn = array('lead_id' => $data['lead_id'], 'actual_worth_amount'=>$data['pjt_val']);
		$stat = $this->project_model->chk_status('leads', $wh_condn);
		if( $stat == 0 )
		echo 'Ok';
		else
		echo 'No';
	}
	
	public function set_project_value()
	{
		$updt = real_escape_array($this->input->post());

		$data['error'] = FALSE;

		if ($updt['pjt_val'] == "")
		{
			$data['error'] = 'Value must not be Null value!';
		}
		else
		{
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$updt = array('actual_worth_amount' => $updt['pjt_val']);
			$updt_id = $this->project_model->update_row('leads', $updt, $wh_condn);
			if($updt_id==0)
			$data['error'] = 'Project Value Not Updated.';
		}
		echo json_encode($data);
	}
	
	/**
	 * Set the Project Status based on the request
	 */
	public function set_project_status() 
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;
		
		if ($updt['pjt_stat'] == "") {
			$data['error'] = 'Value must not be Null value!';
		} else {
			switch ($updt['pjt_stat']) {
				case 1:
					$log_status = 'The Project moved to In Progress';
				break;
				case 2:
					$log_status = 'The Project moved to Completed ';
				break;
				case 3:
					$log_status = 'The Project moved to Onhold';
				break;
				case 4:
					$log_status = 'The Project moved to Inactive';
				break;
			}
		}
		
		if( $data['error'] == FALSE ) {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data = array('pjt_status' => $updt['pjt_stat']);			
			$updt_pjt = $this->project_model->update_row('leads', $data, $wh_condn);
		}
		
		if($updt_pjt) {		
			$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id',$updt['lead_id'], 'pjt_id');		
			$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect		
			$ins['userid_fk'] = $this->userdata['userid'];
			$ins['jobid_fk'] = $this->input->post('lead_id');
			$ins['date_created'] = date('Y-m-d H:i:s');
			$ins['log_content'] = "Status Change:\n" . urldecode($log_status);
			$insert_logs = $this->project_model->insert_row('logs', $ins);
			$data['error'] = FALSE;
		}
		echo json_encode($data);
	}
	
	/**
	 * Update the job status based on the request
	 */
	function update_job_status()
	{
		$updt = real_escape_array($this->input->post());
		
		$json['error'] = FALSE;
		$lead_id = $updt['lead_id'];
		$thermometer_val = $updt['thermometer_val'];
		
		if (!is_numeric($lead_id) || $thermometer_val > 100)
		{
			$json['error'] = 'Invalid details supplied!';
		}
		else
		{
			$wh_condn = array('lead_id' => $lead_id);
			$updt = array('complete_status' => $thermometer_val);
			$updt_stat = $this->project_model->update_row('leads', $updt, $wh_condn);
			if($updt_stat==0)
			{
				$data['error'] = 'Project Completion Status Not Updated.';
			}
		}
		echo json_encode($json);
	}
	
	/**
	 * Set the Project team members for the project based on lead_id
	 */
	public function ajax_set_contractor_for_job()
	{
		$data = real_escape_array($this->input->post());
		
		if (isset($data['lead_id']) && !empty($data['contractors']))
		{	
			$contractors = explode(',', $data['contractors']);	
			$result = array();
			
			$wh_condn = array('jobid_fk'=>$data['lead_id']);
			$project_member = $this->project_model->get_user_data_by_id('contract_jobs', $wh_condn);
			foreach ($project_member as $project_mem)
			{
				$result[] = $project_mem['userid_fk'];
			}
			
			$new_project_member_insert = array_diff($contractors, $result);
			
			$new_project_member_delete = array_diff($result, $contractors);
			$new_project_member_delete = array_values($new_project_member_delete);

			if(!empty($new_project_member_insert))
			{
				foreach ($new_project_member_insert as $con) 
				{
					if (preg_match('/^[0-9]+$/', $con))
					{
						$ins['jobid_fk'] =  $data['lead_id'];
						$ins['userid_fk'] =  $con;
						$insert_contract_job = $this->project_model->insert_row('contract_jobs', $ins);
					}
				}
				
				$query_for_mail = $this->project_model->get_userlist($new_project_member_insert);
				foreach ($query_for_mail as $mail_id)
				{			
					$mail = $mail_id['email'];
					$first_name = $mail_id['first_name'] .' '.$mail_id['last_name'];
					$log_email = $this->get_user_mail($mail , $first_name, $type = "insert", $data['lead_id']);
					
				}
			}
			
			if(!empty($new_project_member_delete))
			{
				$query_for_mail = $this->project_model->get_userlist($new_project_member_delete);
				
				foreach ($query_for_mail as $mail_id)
				{
					$mail = $mail_id['email'];
					$first_name = $mail_id['first_name'] .' '.$mail_id['last_name'];
					$log_email = $this->get_user_mail($mail , $first_name, $type = "remove", $data['lead_id']);
				}
				
				$wh_condn = array('jobid_fk'=>$data['lead_id']);
				$del_contract_jobs = $this->project_model->delete_contract_job('contract_jobs', $wh_condn, $new_project_member_delete);
			}
			$data['status'] = 'OK';
			echo json_encode($data); 
			exit;
		}
		else if(empty($data['contractors']))
		{
			$query_for_mail = $this->project_model->get_userlist($data['project-mem']);
			
			foreach ($query_for_mail as $mail_id)
			{
				$mail = $mail_id['email'];
				$first_name = $mail_id['first_name'] .' '.$mail_id['last_name'];
				$log_email = $this->get_user_mail($mail , $first_name, $type = "remove", $data['lead_id']);
			}
			
			$wh_condn = array('jobid_fk'=>$data['lead_id']);
			$del_contract_jobs = $this->project_model->delete_row('contract_jobs', $wh_condn);
		}
		else
		{
			$data['error'] = 'Invalid job or userid supplied!';
			echo json_encode($data); 
			exit;
		}
	}
	
	public function get_user_mail($mail, $first_name, $mail_type, $lead_id)
	{	
		$project_name = $this->project_model->get_lead_det($lead_id);
		$project_name['lead_title'] = word_limiter($project_name['lead_title'], 4);
		
		if($mail_type == "insert") {
			$log_subject = 'New Project Assignment Notification';
		} else {
			$log_subject = 'Project Removal Notification';
		}
		
		if($mail_type == "insert") {
			$log_email_content = 'You are included as one of the project team members in the project - '.$project_name['lead_title'].'<br />';
		} else {
			$log_email_content = 'You are moved from this project - '.$project_name['lead_title'].'<br />';
		}
		
		$successful = '';
		
		$send_to = $mail;
		
		$print_fancydate = date('l, jS F y h:iA', strtotime(date('Y-m-d H:i:s')));
		
		//email sent by email template
		$param = array();

		$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'first_name'=>$first_name, 'log_email_content'=>$log_email_content);

		$param['to_mail'] = $send_to;
		$param['from_email'] = 'webmaster@enoahisolution.com';
		$param['from_email_name'] = 'Webmaster';
		$param['template_name'] = "Assign / Remove Project Members";
		$param['subject'] = $log_subject;
		
		if($this->email_template_model->sent_email($param)){
			$successful .= 'This log has been emailed to:<br />'.$send_to;
		}
	}

	/*
	 *Set the project manager for the project
	 */
	public function set_project_lead()
	{
		$data_pm = real_escape_array($this->input->post());

		$data['error'] = FALSE;
		
		$project_name = $this->project_model->get_lead_det($data_pm['lead_id']);
		$project_name = word_limiter($project_name['lead_title'], 4);
		
		$user_det = array();
		$pm_det = array();

		$wh_condn = array('userid'=>$data_pm['previous_pm']);
		$previous_manager = $this->project_model->get_user_data_by_id('users', $wh_condn);
		
		foreach($previous_manager as $pre_pm)
		{
			$pm_det['email'] = $pre_pm['email'];
			$pm_det['first_name'] = $pre_pm['first_name'];
			$pm_det['last_name'] = $pre_pm['last_name'];
		}
		$pm_name = $pre_pm['first_name'] . ' ' . $pre_pm['last_name'];
		
		if(!empty($pm_det))
		{
			$this->sent_to_manager($pm_det['email'], $pm_name, $project_name, $mail_type = "old_manager");
		}
		
		$wh_condn = array('userid'=>$data_pm['new_pm']);
		$new_manager = $this->project_model->get_user_data_by_id('users', $wh_condn);
		
		foreach($new_manager as $new_pm)
		{
			$user_det['email'] = $new_pm['email'];
			$user_det['first_name'] = $new_pm['first_name'];
			$user_det['last_name'] = $new_pm['last_name'];
		}
		$pm_name = $pre_pm['first_name'] . ' ' . $pre_pm['last_name'];

		$first_name = $user_det['first_name']; 
		$last_name = $user_det['last_name']; 
		$us_name = $first_name ." ". $last_name;
		if(!empty($user_det))
		{
			$this->sent_to_manager($user_det['email'], $us_name, $project_name, $mail_type = "new_manager");
		}
		
		if ($data_pm['new_pm'] == 0)
		{	
			$data['error'] = TRUE;
			$data['msg'] = 'Session Expired (or) DB Error Occured.';
		}
		else
		{
			$wh_condn = array('lead_id' => $data_pm['lead_id']);
			$updt	  = array('assigned_to' => $data_pm['new_pm']);
			$updt_stat = $this->project_model->update_row('leads', $updt, $wh_condn);
		}
		echo json_encode($data);
		exit;
	}
	
	public function sent_to_manager($email, $first_name, $project_name, $mail_type) 
	{	
		if($mail_type == "new_manager")
		{
			$email_content = "You have been assigned as the Project Manager for the project - '".$project_name."' ";
		}
		else 
		{
			$email_content = "You are moved from this project - '".$project_name."' ";
		}
		
		if($mail_type == "new_manager")
		{
			$log_subject = 'New Project Assignment Notification';
		}
		else
		{
			$log_subject = 'Project Removal Notification';
		}
		
		$print_fancydate = date('l, jS F y h:iA', strtotime(date('Y-m-d H:i:s')));
		
		$send_to = $email;
		
		//email sent by email template
		$param = array();

		$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'first_name'=>$first_name, 'email_content'=>$email_content);

		$param['to_mail'] = $send_to;
		$param['from_email'] = 'webmaster@enoahisolution.com';
		$param['from_email_name'] = 'Webmaster';
		$param['template_name'] = "Project Assignment / Removal Notification";
		$param['subject'] = $log_subject;
		
		$this->email_template_model->sent_email($param);
	}
	
	/*
	 *Set the Planned Project START & END Date.
	 */
	public function set_project_status_date()
	{	
		$updt_data = real_escape_array($this->input->post());
	
		$data['error'] = FALSE;

		$timestamp = strtotime($updt_data['date']);
		
		if ($updt_data['date_type'] != 'start' && $updt_data['date_type'] != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($updt_data['date_type'] == 'start')
			{	 
				$wh_condn = array('lead_id'=>$updt_data['lead_id'], 'date_due <'=>date('Y-m-d H:i:s', $timestamp));
				$chk_stat = $this->project_model->chk_status('leads', $wh_condn);
				if($chk_stat)
				{ 
					$data['error'] = 'Planned Project Start Date Must be Equal or Earlier than the Planned Project End Date!';
				}
				else 
				{ 
					$wh_condn = array('lead_id'=>$updt_data['lead_id']);
					$updt = array('date_start'=>date('Y-m-d H:i:s', $timestamp));
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
					// $this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
					
				}
			}
			else
			{	
				if ($updt_data['date_type'] == 'end') 
				{
					$chk_stat_start = $this->project_model->get_lead_det($updt_data['lead_id']);
					
					if (!empty($chk_stat_start['date_start']))
					{
						if($chk_stat_start['date_start'] > date('Y-m-d H:i:s', $timestamp))
						{
							$data['error'] = 'Planned Project End Date Must be Equal or Later than the Planned Project Start Date!';
						} 
						else 
						{
							$wh_condn = array('lead_id'=>$updt_data['lead_id']);
							$updt = array('date_due'=>date('Y-m-d H:i:s', $timestamp));
							$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
							// $this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
						}
					} 
					else 
					{
						$data['error'] = 'Planned Project Start Date Must be Filled!';
					}
					
				}
			}
		}
		echo json_encode($data);
	}
	
	
	/*
	 *Remove the Planned Project START & END Date.
	 */
	public function rm_project_status_date()
	{
		$updt_data = real_escape_array($this->input->post());

		$data['error'] = FALSE;
		
		$checkStat = $this->project_model->get_lead_det($updt_data['lead_id']);
		
		/* switch ($updt_data['date_type']) {
			case 'start';
				if(isset($checkStat['date_due'])) {
					$data['error'] = 'End Date must be delete';
				} else if(isset($checkStat['actual_date_start'])) {
					$data['error'] = 'Actual Start Date must be delete';
				} else {
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('date_start' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
				}
			break;
			case 'due';
				if(isset($checkStat['actual_date_due'])) {
					$data['error'] = 'Actual End Date must be deleted';
				} else {
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('date_due' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
				}
			break;
			case 'act-start';
				if(isset($checkStat['date_start'])) {
					$data['error'] = 'Planned Start Date must be deleted';
				} else {
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('actual_date_start' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
				}
			break;
			case 'act-due';
				if(isset($checkStat['actual_date_start'])) {
					$data['error'] = 'Actual Start Date must be deleted';
				} else if(isset($checkStat['date_due'])) {
					$data['error'] = 'Planned End Date must be deleted';
				} else {
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('actual_date_due' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
				}
			break;
		} */
		if($checkStat['pjt_status'] != 2) {
			switch ($updt_data['date_type']) {
				case 'start';
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('date_start' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
					$this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
				break;
				case 'due';
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('date_due' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
					$this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
				break;
				case 'act-start';
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('actual_date_start' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
					$this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
				break;
				case 'act-due';
					$wh_condn  = array('lead_id' => $updt_data['lead_id']);
					$updt	   = array('actual_date_due' => NULL);
					$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
					$this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
				break;
			}
		} else {
			$data['error'] = 'Completed Projects cannot be altered.';
		}
		echo json_encode($data);
	}
	
	
	/*
	 *Set the Actual Project START & END Date.
	 */
	public function actual_set_project_status_date()
	{
		$updt_data = real_escape_array($this->input->post());

		$data['error'] = FALSE;
		
		$timestamp = strtotime($updt_data['date']);
		
		if ($updt_data['date_type'] != 'start' && $updt_data['date_type'] != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp )
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			$chk_status = $this->project_model->get_lead_det($updt_data['lead_id']);
			
			if ($updt_data['date_type'] == 'start')
			{
				if (!empty($chk_status['date_start']))
				{
					if($chk_status['date_start'] > date('Y-m-d H:i:s', $timestamp))
					{
						$data['error'] = 'Actual Project Start Date Must be Equal or Later than the Planned Project Start Date!';
					}
					else
					{
						if (!empty($chk_status['actual_date_due']))
						{
							if ($chk_status['actual_date_due'] < date('Y-m-d H:i:s', $timestamp))
							{
								$data['error'] = 'Actual Project Start Date Must be Equal or Earlier than the Actual Project End Date!';
							}
						}
						else
						{
							$wh_condn = array('lead_id'=>$updt_data['lead_id']);
							$updt = array('actual_date_start'=>date('Y-m-d H:i:s', $timestamp));
							$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
							$this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
						}
					}
				} 
				else 
				{
					$data['error'] = 'Planned Project Start Date Must be Filled!';
				}
			}
			else
			{	
				if ($updt_data['date_type'] == 'end') 
				{
					if (!empty($chk_status['actual_date_start'])) 
					{
						if($chk_status['actual_date_start'] > date('Y-m-d H:i:s', $timestamp)) 
						{
							$data['error'] = 'Actual Project End Date Must be Equal or Later than the Actual Project Start Date!';
						} 
						else 
						{
							$wh_condn = array('lead_id'=>$updt_data['lead_id']);
							$updt = array('actual_date_due'=>date('Y-m-d H:i:s', $timestamp));
							$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
							$this->customer_model->update_date_to_timesheet_econnect($updt_data['lead_id']); // Update date to timesheet and econnect
						}
					} 
					else 
					{
						$data['error'] = 'Actual Project Start Date Must be Filled!';
					}
				}		
			}
		}
		echo json_encode($data);
	}
	
	/*
	 *For Expected Payment terms
	 */
	function retrieve_payment_terms($jid)
	{
		$expect_payment_terms = $this->project_model->get_expect_payment_terms($jid);
		
		$usernme = $this->session->userdata('logged_in_user');
		if ($usernme['role_id'] == 1 || $usernme['role_id'] == 2|| $usernme['role_id'] == 4) {
			$chge_access = 1;
		} else {
			$chge_access = $this->project_model->get_access($jid, $usernme['userid']);
		}
		
		$get_pjt_status = $this->project_model->get_lead_det($jid);
		
		$readonly_status = false;
		if($chge_access != 1)
		$readonly_status = true;
		if($get_pjt_status['pjt_status'] == 2)
		$readonly_status = true;
		
		$output = '';
		$total_amount_recieved = '';
		$output .= '<div class="payment-terms-mini-view2" style="float:left; margin-top: 5px;">';
		$expi = 1;
		$pt_select_box = '';
		$pt_select_box .= '<option value="0"> &nbsp; </option>';
		$output .= '<div align="left" style="background: none repeat scroll 0 0;">
					<h6>Agreed Payment Terms</h6>
					<div class=payment_legend>
					<div class="pull-left"><img src=assets/img/payment-received.jpg><span>Payment Received</span></div>
					<div class="pull-left"><img src=assets/img/payment-pending.jpg><span>Partial Payment</span></div>
					<div class="pull-left"><img src=assets/img/payment-due.jpg ><span>Payment Due</span></div>
					<div class="pull-left"><img src=assets/img/generate_invoice.png><span>Generate Invoice</span></div>
					<div class="pull-left"><img src=assets/img/invoice_raised.png><span>Invoice Raised</span></div>
					</div></div>';
		$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
		$output .= "<thead>";
		$output .= "<tr align='left'>";
		$output .= "<th class='header'>Payment Milestone</th>";
		$output .= "<th class='header'>Milestone Date</th>";
		$output .= "<th class='header'>For the Month & Year</th>";
		$output .= "<th class='header'>Amount</th>";
		$output .= "<th class='header'>Attachments</th>";
		$output .= "<th class='header'>Status</th>";
		$output .= "<th class='header'>Action</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		if (count($expect_payment_terms>0))
		{
			foreach ($expect_payment_terms as $exp)
			{
				$att_condn   = array("expectid"=>$exp['expectid']);
				$attachments = $this->customer_model->get_records_by_num("expected_payments_attach_file",$att_condn);
				$month_year     = ($exp['month_year']!='0000-00-00 00:00:00') ? date('F Y', strtotime($exp['month_year'])) :'';
				$payment_amount = number_format($exp['amount'], 2, '.', ',');
				$total_amount_recieved += $exp['amount'];
				$payment_received = '';
				$invoice_stat = '';
				$raised_invoice_stat = '';
				if ($exp['invoice_status'] == 1) {
					$raised_invoice_stat = "<img src='assets/img/invoice_raised.png' alt='Invoice-raised'>";
				}
				if ($exp['received'] == 0) {
					$payment_received = $raised_invoice_stat.'&nbsp;<img src="assets/img/payment-due.jpg" alt="Due" />';
				} else if ($exp['received'] == 1) {
					$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" />';
				} else {
					$payment_received = $raised_invoice_stat.'&nbsp;<img src="assets/img/payment-pending.jpg" alt="pending" />';
				}
				if ($readonly_status == false) {
					if ($exp['invoice_status'] == 0) {
						$invoice_stat = "<a title='Edit' onclick='paymentProfileEdit(".$exp['expectid']."); return false;' ><img src='assets/img/edit.png' alt='edit'> </a>
						<a title='Delete' onclick='paymentProfileDelete(".$exp['expectid']."); return false;'><img src='assets/img/trash.png' alt='delete' ></a>
						<a title='Generate Invoice' href='javascript:void(0)' onclick='generate_inv(".$exp['expectid']."); return false;'><img src='assets/img/generate_invoice.png' alt='Generate Invoice' ></a>";
					} else if ($exp['invoice_status'] == 1) {
						$invoice_stat = "<a title='Edit' onclick='paymentProfileEdit(".$exp['expectid']."); return false;' ><img src='assets/img/edit.png' alt='edit'> </a>
						<a title='Delete' class='readonly-status img-opacity' href='javascript:void(0)'><img src='assets/img/trash.png' alt='delete'></a>
						<a title='Generate Invoice' href='javascript:void(0)' class='readonly-status img-opacity'><img src='assets/img/generate_invoice.png' alt='Generate Invoice'></a>";
					}
				} else {
					$invoice_stat = "<a title='Edit' class='readonly-status img-opacity' href='javascript:void(0)'><img src='assets/img/edit.png' alt='edit'></a>
					<a title='Delete' class='readonly-status img-opacity' href='javascript:void(0)'><img src='assets/img/trash.png' alt='delete'></a>
					<a title='Generate Invoice' href='javascript:void(0)' class='readonly-status img-opacity'><img src='assets/img/generate_invoice.png' alt='Generate Invoice'></a>";
				}
				$att = "";
				if($attachments>0) {
					$att = "<img src='assets/img/attachment_icon.png' alt='Attachments' >";
				}
				$output .= "<tr>";
				$output .= "<td align='left'>".$exp['project_milestone_name']."</td>";
				$output .= "<td align='left'>".date('d-m-Y', strtotime($exp['expected_date']))."</td>";
				$output .= "<td align='left'>".$month_year."</td>";
				$output .= "<td align='left'> ".$exp['expect_worth_name'].' '.number_format($exp['amount'], 2, '.', ',')."</td>";
				$output .= "<td align='center'>".$att."</td>";
				$output .= "<td align='center'>".$payment_received."</td>";
				if ($readonly_status == false) {
					$output .= "<td align='left'>".$invoice_stat."</td>";
				} else {
					$output .= "<td align='left'>".$invoice_stat."</td>";
				}
				$output .= "</tr>";
				$pt_select_box .= '<option value="'. $exp['expectid'] .'">' . $exp['project_milestone_name'] ." \${$payment_amount} by {$expected_date}" . '</option>';
				$expi ++;
			}
		}
		$output .= "<tr>";
		$output .= "<td></td><td></td>";
		$output .= "<td><b>Total Milestone Payment : </b></td><td><b>".$exp['expect_worth_name'].' '.number_format($total_amount_recieved, 2, '.', ',') ."</b></td>";
		$output .= "</tr>";
		$output .= "</table>";
		$output .= '</div>';
		echo $output;
	}
	
	/*
	 *edit the payment term
	 */
	function payment_term_edit($eid, $jid)
	{
		$exp = array();
		$payment_details = $this->project_model->get_payment_term_det($eid, $jid);
		$attached_files  = $this->project_model->get_attached_files($eid);
		
		$exp['payment_remark']		   = $payment_details['payment_remark'];
		$exp['expect_id']			   = $eid;
		$exp['job_id']			   	   = $jid;
		$exp['expected_date']          = date('d-m-Y', strtotime($payment_details['expected_date']));
		$exp['month_year']             = ($payment_details['month_year']!='0000-00-00 00:00:00') ? date('F Y', strtotime($payment_details['month_year'])) : '';
		$exp['project_milestone_name'] = $payment_details['project_milestone_name'];
		$exp['project_milestone_amt']  = $payment_details['amount'];
		$exp['invoice_status']  	   = $payment_details['invoice_status'];
		if(!empty($attached_files)) {
			$exp['attached_file']      = $attached_files;
		}
		$get_parent_folder_id = $this->request_model->getParentFfolderId($jid,$parent=0);
		$exp['ff_id'] = $get_parent_folder_id['folder_id'];
		$this->load->view("projects/update_payment_term", $exp);
	}
	
	/**
	 * sets the payment terms
	 * for the project
	 */
	function set_payment_terms($update = false)
	{
		$errors = array();
		$res_file = array();
		$today = time();
		
		$data = real_escape_array($this->input->post());
		
		$fname = $_FILES['newfile_upload'];
	
		if($fname!="") {
			$f_dir = UPLOAD_PATH.'files/';
			if (!is_dir($f_dir)) {
				mkdir($f_dir);
				chmod($f_dir, 0777);
			}
			
			//creating lead_id folder name
			$f_dir = $f_dir.$data['sp_form_jobid'];
			if (!is_dir($f_dir)) {
				mkdir($f_dir);
				chmod($f_dir, 0777);
			}
			
			$this->upload->initialize(array(
			   "upload_path" => $f_dir,
			   "overwrite" => FALSE,
			   "remove_spaces" => TRUE,
			   "max_size" => 51000000,
			   "allowed_types" => "*"
			)); 
			$returnUpload = array();
			if(!empty($_FILES['newfile_upload']['name'][0])) {
				if ($this->upload->do_multi_upload("newfile_upload")) {
					$returnUpload  = $this->upload->get_multi_upload_data();
					$i = 1;
					if(!empty($returnUpload)) {
						foreach($returnUpload as $file_up) {
							$lead_files['lead_files_name']		 = $file_up['file_name'];
							$lead_files['lead_files_created_by'] = $this->userdata['userid'];
							$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
							$lead_files['lead_id'] 				 = $data['sp_form_jobid'];
							$lead_files['folder_id'] 			 = $data['filefolder_id']; //get here folder id from file_management table.
							$insert_file						 = $this->request_model->return_insert_id('lead_files', $lead_files);
							$res_file[] = $insert_file;
							
							$logs['jobid_fk']	   = $data['sp_form_jobid'];
							$logs['userid_fk']	   = $this->userdata['userid'];
							$logs['date_created']  = date('Y-m-d H:i:s');
							$logs['log_content']   = $file_up['file_name'].' is added.';
							$logs['attached_docs'] = $file_up['file_name'];
							$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
							
							$i++;
						}
					}
				}
			}
		}
		
		// echo "<pre>"; print_r($res_file); exit;
		
		$pdate1 = $data['sp_date_1'];
		$pdate2 = strtotime($data['sp_date_2']);
		$pdate3 = $data['sp_date_3'];
		$payment_remark = $data['payment_remark'];
		
		
		// $new_file_res = $this->payment_file_upload($data['sp_form_jobid'], $data['filefolder_id'])
		
		if (count($errors))
		{
			echo "<p style='color:#FF4400;'>" . join('\n', $errors) . "</p>";
		}
		else
		{
			$job_updated   = FALSE;
			$expected_date = date('Y-m-d', $pdate2);
			$month_year    = date('Y-m-d', strtotime($data['month_year']));
			$data3         = array('jobid_fk' => $data['sp_form_jobid'], 'percentage' => '0', 'amount' => $pdate3, 'expected_date' => $expected_date, 'month_year' => $month_year,'project_milestone_name' => $pdate1, 'payment_remark' => $payment_remark);
			
			$payment_details = $this->project_model->get_expect_payment_terms($data['sp_form_jobid']);

			if ($update == "") 
			{
				$ins_exp_pay = $this->project_model->return_insert_id('expected_payments', $data3);
				
				$attach		= array();
				$new_attach = array();
				if($ins_exp_pay) {
					$attach['expectid'] = $ins_exp_pay;
					if(!empty($data['file_id'])) {
						foreach($data['file_id'] as $files) {
							$attach['file_id'] = $files;
							$this->project_model->insert_row('expected_payments_attach_file', $attach);
						}
					}
					$new_attach['expectid'] = $ins_exp_pay;
					if(!empty($res_file)) {
						foreach($res_file as $files) {
							$new_attach['file_id'] = $files;
							$this->project_model->insert_row('expected_payments_attach_file', $new_attach);
						}
					}
				}

				$pay_det = 'Project Milestone Name: '.$data3['project_milestone_name'].'  Amount: '.$payment_details[0]['expect_worth_name'].' '.$data3['amount'].'  Expected Date: '.$expected_date;
				
				$ins['jobid_fk']      = $data['sp_form_jobid'];
				$ins['userid_fk']     = $this->userdata['userid'];
				$ins['date_created']  = date('Y-m-d H:i:s');
				$ins['log_content']   = $pay_det;
				$insert_logs = $this->project_model->insert_row('logs', $ins);
			}
			else 
			{				
				$pay_status = $this->project_model->get_payment_term_det($update, $data['sp_form_jobid']);
				
				if ($pay_status['received'] != 1) 
				{
					$pay_det = 'Project Milestone Name: '.$data3['project_milestone_name'].'  Amount: '.$payment_details[0]['expect_worth_name'].' '.$data3['amount'].'  Expected Date: '.$data3['expected_date'];
					
					$ins['jobid_fk']      = $data['sp_form_jobid'];
					$ins['userid_fk']     = $this->userdata['userid'];
					$ins['date_created']  = date('Y-m-d H:i:s');
					$ins['log_content']   = $pay_det;
					$insert_logs = $this->project_model->insert_row('logs', $ins);
					
					$this->project_model->delete_row('expected_payments_attach_file', array("expectid"=>$update));
					
					if(!empty($data['file_id'])){
						$attach_updt['expectid'] = $update;
						foreach($data['file_id'] as $files) {
							$attach_updt['file_id'] = $files;
							$this->project_model->insert_row('expected_payments_attach_file', $attach_updt);
						}
					}
					if(!empty($res_file)) {
						$attach_new_updt['expectid'] = $update;
						foreach($res_file as $files) {
							$attach_new_updt['file_id'] = $files;
							$this->project_model->insert_row('expected_payments_attach_file', $attach_new_updt);
						}
					}
					
					$updatepayment = array('amount' => $pdate3, 'expected_date' => $expected_date, 'month_year' => $month_year, 'project_milestone_name' => $pdate1, 'payment_remark' => $payment_remark );
					$wh_condn = array('expectid' => $update, 'jobid_fk' => $data['sp_form_jobid']);
					$updt_pay = $this->project_model->update_row('expected_payments', $updatepayment, $wh_condn);
					
				}
				else
				{
					echo "<span id=paymentfadeout><h6>Received Payment cannot be Edited!</h6></span>";
				}	
			}	
			$job_updated = TRUE;

			if ($job_updated)
			{
				$up = array('payment_terms'=>1);
				$wh_condn = array('lead_id' => $data['sp_form_jobid']);
				$this->project_model->update_row('leads', $up, $wh_condn);
				
				$output = '';
				// $payment_det = $this->project_model->get_expect_payment_terms($data['sp_form_jobid']); //after update
				$output .= $this->retrieve_payment_terms($data['sp_form_jobid']);
				echo $output;
			}
			else
			{
				echo "{error:true, errormsg:'Payment update failed'}";
			}
		}
	}
	
	/**
	 * Uploads a file by payment milestone posted to a specified job
	 * works with the Ajax file uploader
	 *
	/
	/*
	 *Delete the expected payment
	 *@params expect_id, lead_id
	 */
	function agreedPaymentDelete($eid, $jid)
	{
		$stat = $this->project_model->get_payment_term_det($eid, $jid);
		
		if ($stat['received'] == 0)
		{
			//log details
			$ins['jobid_fk'] = $jid;
			$ins['userid_fk'] = $this->userdata['userid'];
			$ins['date_created'] = date('Y-m-d H:i:s');
			$ins['log_content'] = 'Project Milestone Name: '.$stat['project_milestone_name'].'  Amount: '.$stat['expect_worth_name'].' '.$stat['amount'].'  is deleted on '.date('Y-m-d');
			
			//delete the record
			$wh_condn = array('expectid' => $eid, 'jobid_fk' => $jid, 'received' => 0);
			$del      = $this->project_model->delete_row('expected_payments', $wh_condn);
			if ($del)
			{
				//insert the log
				$wh_condn = array('expectid' => $eid);
				$this->project_model->delete_row('expected_payments_attach_file', $wh_condn);
				$insert_logs = $this->project_model->insert_row('logs', $ins);
				echo "<span id=paymentfadeout><h6>Payment Deleted!</h6></span>";
			}
			else
			{
				echo "<span id=paymentfadeout><h6>Error In Deletion!</h6></span>";
			}
		}
		else
		{
			echo "<span id=paymentfadeout><h6>Received Payments cannot be Deleted!</h6></span>";
		}
		$this->retrieve_payment_terms($jid);
	}
	
	//list the expected payments
	function agreedPaymentView($jobId)
	{
		$get_parent_folder_id = $this->request_model->getParentFfolderId($jobId,$parent=0);
		$data['ff_id'] = $get_parent_folder_id['folder_id'];
		$data['jobid'] = $jobId;
		$this->load->view("projects/add_payment_term", $data);
	}
	
	/*
	 *retrieve the payment terms in payment received form(Map to a payment term - Dropdown)
	 *@params - lead_id
	 */
	function retrieve_record($lead_id)
	{
		$retrieve_rec = $this->project_model->get_expect_payment_terms($lead_id);
		$pt_select_box = '';
		$pt_select_box .= '<option value="0"> &nbsp; </option>';
		echo sizeof($retrieve_rec); 
		if(count($retrieve_rec)>0)
		{
			foreach ($retrieve_rec as $rec)
			{
				if($rec['invoice_status'] == 1) {
					$pt_select_box .= '<option value="'.$rec['expectid'].'">' . $rec['project_milestone_name']. ' - '.$rec['expect_worth_name']." ".number_format($rec['amount'], 2, '.', ',')." by ".date('d-m-Y', strtotime($rec['expected_date']))." " . '</option>';
				}
			}
		}
		echo $pt_select_box;
	}
	
	/**
	 * add & edit the received payments for the project
	 * @params received payment id, expected payment id
	 */
	function add_project_received_payments($update = false, $eid = false)
	{
		$errors = array();
		$updt_data = real_escape_array($this->input->post());
		
		if (isset($updt_data['pr_date_2']) && !preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $updt_data['pr_date_2']))
		{
			$errors[] = 'Invalid deposit amount';
		}
		
		if (!isset($updt_data['pr_form_jobid']) || (int) $updt_data['pr_form_jobid'] == 0)
		{
			$errors[] = 'Invalid job ID supplied';
		}
		
		if (!isset($updt_data['pr_date_3']) || !preg_match('/^[0-9]{2}\-[0-9]{2}\-[0-9]{4}$/', $updt_data['pr_date_3']) || strtotime($updt_data['pr_date_3']) == FALSE)
		{
			$errors[] = 'Invalid deposit date supplied';
		}
		
		$expect_payment = $this->project_model->get_payment_term_det($updt_data['deposit_map_field'], $updt_data['pr_form_jobid']);
		
		$det = $this->project_model->get_quote_data($updt_data['pr_form_jobid']);
		
		if (!isset($update))
		{
			$wh_condn = array('map_term'=>$updt_data['deposit_map_field'], 'jobid_fk'=>$updt_data['pr_form_jobid']);
			$received_payment = $this->project_model->get_deposits_amt($wh_condn);
				
			$temp_tot_amt = $updt_data['pr_date_2'] + $received_payment['tot_amt'];
			$remaining_amt = $expect_payment['amount'] - $received_payment['tot_amt'];
		} 
		else 
		{
			$wh_condn = array('map_term'=>$updt_data['deposit_map_field'], 'jobid_fk'=>$updt_data['pr_form_jobid'], 'depositid'=>$update);
			$received_payment = $this->project_model->get_deposits_amt($wh_condn);
			
			$temp_tot_amt = $updt_data['pr_date_2'] + $received_payment['tot_amt'];
			$remaining_amt = $expect_payment['amount'] - $received_payment['tot_amt'];
		}

		if ($temp_tot_amt > $expect_payment['amount']) 
		{
			$errors[] = 'Error: As per payment milestone value of '.$expect_payment['amount'].', pending amount to be received is only '.$remaining_amt.'. Amount entered is higher than this value.';
		}	
		
		if (count($errors))
		{	
			$json['error'] = true;
			$json['errormsg'] = join($errors);
			echo json_encode($json);
		}
		else
		{	
			$ins_data = array('jobid_fk' => $updt_data['pr_form_jobid'], 'invoice_no' => $updt_data['pr_date_1'], 'amount' => $updt_data['pr_date_2'],
						  'deposit_date' => date('Y-m-d H:i:s', strtotime($updt_data['pr_date_3'])), 'comments' => $updt_data['pr_date_4'], 
						  'userid_fk' => $this->userdata['userid'], 'payment_received' => 1, 'map_term' => $updt_data['deposit_map_field']);
			
			if ($update == "")
			{
				$inst_data = $this->project_model->insert_row('deposits', $ins_data);
				
				$dd = strtotime($updt_data['pr_date_3']);
				$deposit_date = date('Y-m-d', $dd);				

				$log_data['jobid_fk'] = $updt_data['pr_form_jobid'];
				$log_data['userid_fk'] = $this->userdata['userid'];
				$log_data['date_created'] = date('Y-m-d H:i:s');
				$log_data['log_content'] = 'Invoice No: '.$updt_data['pr_date_1'].'  Amount: '.$det[0]['expect_worth_name'].' '.$updt_data['pr_date_2'].' Deposit Date: '.$deposit_date.' is Created.';
				
				$inst_logs = $this->project_model->insert_row('logs', $log_data);
			}
			else 
			{
				$wh_condn = array('expectid'=>$eid, 'jobid_fk'=>$updt_data['pr_form_jobid']);
				$updt = array('received'=>0);
				$updt_row = $this->project_model->update_row("expected_payments", $updt, $wh_condn);
				
				$updatepayment = array('jobid_fk' => $updt_data['pr_form_jobid'], 'invoice_no' => $updt_data['pr_date_1'], 'amount' => $updt_data['pr_date_2'], 'deposit_date' => date('Y-m-d H:i:s', strtotime($updt_data['pr_date_3'])), 'comments' => $updt_data['pr_date_4'], 'userid_fk' => $this->userdata['userid'], 'payment_received' => 1, 'map_term' => $updt_data['deposit_map_field']);
				
				$wh_condn1 = array('depositid'=>$update, 'jobid_fk'=>$updt_data['pr_form_jobid']);
				$updt_exp_pay = $this->project_model->update_row("deposits", $updatepayment, $wh_condn1);
				
				$dd = strtotime($updatepayment['deposit_date']);
				$deposit_date = date('Y-m-d', $dd);
				
				$log_data['jobid_fk'] = $updt_data['pr_form_jobid'];
				$log_data['userid_fk'] = $this->userdata['userid'];
				$log_data['date_created'] = date('Y-m-d H:i:s');
				$log_data['log_content'] = 'Invoice No: '.$updatepayment['invoice_no'].'  Amount: '.$det[0]['expect_worth_name'].' '.$updatepayment['amount'].' Deposit Date: '.$deposit_date;
				
				$inst_logs = $this->project_model->insert_row('logs', $log_data);
			}
			if (isset($updt_data['deposit_map_field']) && $updt_data['deposit_map_field'] > 0 && preg_match('/^[0-9]+$/', $updt_data['deposit_map_field']))
			{				
				$wh_condn = array('map_term'=>$updt_data['deposit_map_field'], 'jobid_fk'=>$updt_data['pr_form_jobid']);
				$payment_status = $this->project_model->get_deposits_amt($wh_condn);
				
				$payment_status_expect = $this->project_model->get_payment_term_det($updt_data['deposit_map_field'], $updt_data['pr_form_jobid']);
				
				if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) 
				{
					$this->db->where('expectid', $updt_data['deposit_map_field']);
					$this->db->update($this->cfg['dbpref'] . 'expected_payments', array('received' => 1));
				}
				else 
				{
					$this->db->where('expectid', $updt_data['deposit_map_field']);
					$this->db->update($this->cfg['dbpref'] . 'expected_payments', array('received' => 2));
				}
			}

			$deposit_data = $this->project_model->get_deposits_data($updt_data['pr_form_jobid']);
			
			$output = '';
			$amount_recieved = '';
			$output .= '<div class="payment-received-mini-view2" style="margin-top:5px;">';
			$pdi = 1;
			$output .= '<option value="0"> &nbsp; </option>';
			$output .= "<p><h6>Payment History</h6></p>";
			$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
			$output .= "<thead>";
			$output .= "<tr align='left'>";
			$output .= "<th class='header'>Invoice No</th>";
			$output .= "<th class='header'>Date Received</th>";
			$output .= "<th class='header'>Amt Received</th>";
			$output .= "<th class='header'>Payment Term</th>";
			$output .= "<th class='header'>Action</th>";
			$output .= "</tr>";
			$output .= "</thead>";
			foreach ($deposit_data as $dd)
			{
				$expected_date = date('d-m-Y', strtotime($dd['deposit_date']));
				$payment_amount = number_format($dd['amount'], 2, '.', ',');
				$amount_recieved += $dd['amount'];
				$payment_received = '';
				if ($dd['payment_received'] == 1)
				{
					$payment_received = '<img src="assets/img/crm-payment-received.gif" alt="received" />';
				}
				$output .= "<tr align='left'>";
				$output .= "<td>".$dd['invoice_no']."</td>";
				$output .= "<td>".date('d-m-Y', strtotime($dd['deposit_date']))."</td>";
				$output .= "<td> ".$det[0]['expect_worth_name'].' '.number_format($dd['amount'], 2, '.', ',')."</td>";
				$output .= "<td>".$dd['payment_term']."</td>";
				$output .= "<td align='left'><a class='edit' title='Edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' ><img src='assets/img/edit.png' alt='edit'></a>";
				$output .= "<a class='edit' title='Delete' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' ><img src='assets/img/trash.png' alt='delete'></a></td>";
				$output .= "</tr>";
			}
			$output .= "<tr>";
			$output .= "<td></td>";
			$output .= "<td><b>Total Payment: </b> </td><td colspan='2'><b>".$det[0]['expect_worth_name'].' '.number_format($amount_recieved, 2, '.', ',')."</b></td>";
			$output .= "</tr>";
			$output .= "</table>";
			$output .= "</div>";

			$json['error'] = false;
			$json['msg'] = $output;
			echo json_encode($json);
		}
	}
	
	//List the received payment view.
	function PaymentView()
	{
		echo '<script type="text/javascript">
		$(function(){
			$("#pr_date_3").datepicker({
				dateFormat: "dd-mm-yy", 
				maxDate: "0",
				beforeShow : function(input, inst) {
					$("#ui-datepicker-div")[ $(input).is("[data-calendar=false]") ? "addClass" : "removeClass" ]("hide-calendar");
				}
			});
		});
		function isNumberKey(evt)
		{
		  var charCode = (evt.which) ? evt.which : event.keyCode;
		  if (charCode != 46 && charCode > 31 
			&& (charCode < 48 || charCode > 57))
			 return false;

		  return true;
		}
	   </script>
		<br />
		<form id="payment-recieved-terms">
			<p>Invoice No *<input type="text" name="pr_date_1" id="pr_date_1" class="textfield width200px" /> </p>
			<p>Amount Received *<input onkeypress="return isNumberKey(event)" type="text" name="pr_date_2" id="pr_date_2" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
			<p>Date Received *<input type="text" data-calendar="true" name="pr_date_3" id="pr_date_3" class="textfield width200px pick-date" readonly /> </p>
			
			<p>Map to a payment term *<select name="deposit_map_field" id="deposit_map_field" class="deposit_map_field" style="width:210px;"> "'.$updt.'" </select></p>

			<p>Comments <textarea name="pr_date_4" id="pr_date_4" class="textfield width200px" ></textarea> </p>
			<div class="buttons">
				<button type="submit" class="positive" onclick="setPaymentRecievedTerms(); return false;">Add Payment</button>
			</div>
			<input type="hidden" name="pr_form_jobid" id="pr_form_jobid" value="0" />
			<input type="hidden" name="pr_form_invoice_total" id="pr_form_invoice_total" value="0" />
		</form>';
	}
	
	//Payment Received Edit function
	function paymentEdit($pdid,$jid) 
	{
		$received_payment_details = $this->project_model->get_receivedpaymentDet($pdid, $jid);
		$eid = $received_payment_details['map_term'];
		$received_deposit_date = date('d-m-Y', strtotime($received_payment_details['deposit_date']));
		$updt = $this->retrieveRecordEdit($jid, $eid);
		echo '<br />
			<script>
				$("#pr_date_3").datepicker({
					dateFormat: "dd-mm-yy", 
					maxDate: "0",
					beforeShow : function(input, inst) {
						$("#ui-datepicker-div")[ $(input).is("[data-calendar=false]") ? "addClass" : "removeClass" ]("hide-calendar");
					}
				});
				
				function isNumberKey(evt)
				{
				  var charCode = (evt.which) ? evt.which : event.keyCode;
				  if (charCode != 46 && charCode > 31 
					&& (charCode < 48 || charCode > 57))
					 return false;

				  return true;
				}
			</script>
			<form id="update-payment-recieved-terms">
			<p>Invoice No *<input type="text" name="pr_date_1" id="pr_date_1" value="'.$received_payment_details['invoice_no'].'" class="textfield width200px" /> </p>
			<p>Amount Received *<input type="text" onkeypress="return isNumberKey(event)" name="pr_date_2" id="pr_date_2" value="'.$received_payment_details['amount'].'" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
			<p>Date Received *<input type="text" data-calendar="true" name="pr_date_3" id="pr_date_3" value="'.$received_deposit_date.'" class="textfield width200px" readonly /> </p>
			
			<p>Map to a payment term *<select name="deposit_map_field" id="deposit_map_field" class="deposit_map_field" style="width:210px;"> "'.$updt.'" </select></p>

			<p>Comments <textarea name="pr_date_4" id="pr_date_4" class="textfield width200px" >'.$received_payment_details['comments'].'</textarea> </p>
			<div class="buttons">
				<button type="submit" class="positive" onclick="updatePaymentRecievedTerms('.$pdid.','.$eid.'); return false;" >Update Payment</button>
			</div>
			<input type="hidden" name="pr_form_jobid" id="pr_form_jobid" value="0" />
			<input type="hidden" name="pr_form_invoice_total" id="pr_form_invoice_total" value="0" />
		</form>';
	}
	
	//For Edit Functionality - Edit Received Payments.
	function retrieveRecordEdit($lead_id, $eid) 
	{		
		$expect_payment_terms = $this->project_model->get_expect_payment_terms($lead_id);
		
		$pt_select_box = '';
		$pt_select_box .= '<option value="0"> &nbsp; </option>';
		foreach ($expect_payment_terms as $ext)
		{
			if($eid ==  $ext['expectid'])
			{	
				$pt_select_box .= '<option selected ="selected" value="'.$ext['expectid'].'">' . $ext['project_milestone_name'] .' '.$ext['expect_worth_name']." ".number_format($ext['amount'], 2, '.', ',')." by ".$ext['expected_date']." " . '</option>';
			}
			else 
			{
				$pt_select_box .= '<option value="'.$ext['expectid'].'">' . $ext['project_milestone_name'].' '.$ext['expect_worth_name']." ".number_format($ext['amount'], 2, '.', ',')." by  ".$ext['expected_date']." " . '</option>';
			}
		}
		return $pt_select_box;
	}
	
	function receivedPaymentDelete($pdid, $jid, $map) 
	{
		$rec_det = $this->project_model->get_receivedpaymentDet($pdid, $jid);//get the details for inserting logs
		
		$wh_condn = array('depositid'=>$pdid, 'jobid_fk'=>$jid, 'payment_received'=>1);
		$stat = $this->project_model->delete_row('deposits', $wh_condn);		
		
		if ($stat) 
		{
			$det = $this->project_model->get_quote_data($jid);			

			$inst_log['jobid_fk'] = $jid;
			$inst_log['userid_fk'] = $this->userdata['userid'];
			$inst_log['date_created'] = date('Y-m-d H:i:s');
			$inst_log['log_content'] = 'Invoice No: '.$rec_det['invoice_no'].'  Amount: '.$det[0]['expect_worth_name'].' '.$rec_det['amount'].'  Deposit Date: '.date('Y-m-d',strtotime($rec_det['deposit_date']));
			
			$inse = $this->project_model->insert_row('logs', $inst_log);
			
			$wh_condn = array('map_term'=>$map, 'jobid_fk'=>$jid);
			$payment_status = $this->project_model->get_deposits_amt($wh_condn);

			$payment_status_expect = $this->project_model->get_payment_term_det($map, $jid);
			
			$rec = $this->project_model->get_payment_term_det($map, $jid);

			
			if ($rec['received'] == 2) 
			{ //echo "rec 2 " . $payment_status['tot_amt'];
				if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) {
					$updt = array('received' => 1);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);
				} else if ($payment_status['tot_amt'] == 'null' || $payment_status['tot_amt'] == '') {
					$updt = array('received' => 0);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);				
				} else {
					$updt = array('received' => 2);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);	
				}
			} 
			else if ($rec['received'] == 1) 
			{ //echo "rec 1 " . $payment_status['tot_amt'];
				if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) {
					$updt = array('received' => 1);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);
				} else if ($payment_status['tot_amt'] == 'null' || $payment_status['tot_amt'] == '') {
					$updt = array('received' => 0);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);
				} else {
					$updt = array('received' => 2);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);
				}
			} 
			else 
			{ //echo "rec else " . $payment_status['tot_amt'];
				if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) {
					$updt = array('received' => 1);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);
				} else if ($payment_status['tot_amt'] == 'null' || $payment_status['tot_amt'] == '') {
					$updt = array('received' => 0);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);					
				} else {
					$updt = array('received' => 2);
					$wh_condn = array('expectid' => $map, 'jobid_fk' => $jid);
					$updt_id = $this->project_model->update_row('expected_payments', $updt, $wh_condn);					
				}
			}
			echo "<span id=paymentfadeout><h6>Received Payment Deleted!</h6></span>";
		} 
		else 
		{
			echo "<span id=paymentfadeout><h6>Error Occured!</h6></span>";
		}
		$this->received_payment_terms_delete($jid);
	}
	
	//list the received payments
	function received_payment_terms_delete($jid)
	{
		//mychanges
			$jsql = $this->db->query("select expect_worth_id from ".$this->cfg['dbpref']."leads where lead_id='$jid'");
			$jres = $jsql->result();
			$worthid = $jres[0]->expect_worth_id;
			$expect_worth = $this->db->query("select expect_worth_name from ".$this->cfg['dbpref']."expect_worth where expect_worth_id='$worthid'");
			$eres = $expect_worth->result();
			$symbol = $eres[0]->expect_worth_name;		
		
		$userdata = $this->session->userdata('logged_in_user'); 
		$userid=$userdata['userid'];
		$query = $this->db->get_where($this->cfg['dbpref'].'deposits', array('depositid' => $pdid, 'jobid_fk' => $jid ));
		$get = $query->row_array();		
		$milename = $get['invoice_no'];
		$amount = $get['amount'];
		$map_term = $get['map_term'];
		$expectdate = date('Y-m-d',strtotime($get['deposit_date']));	
		$filename = 'Invoice No: '.$milename.'  Amount: '.$symbol.' '.$amount.'  Deposit Date: '.$expectdate.' Map Term: '.$map_term; 
		
	
		$output = '';
		$recieve_query = $this->db->query("SELECT `".$this->cfg['dbpref']."deposits` . * , `".$this->cfg['dbpref']."expected_payments`.`project_milestone_name` AS payment_term FROM (`".$this->cfg['dbpref']."deposits`) LEFT JOIN `".$this->cfg['dbpref']."expected_payments` ON `".$this->cfg['dbpref']."deposits`.`map_term` = `".$this->cfg['dbpref']."expected_payments`.`expectid` WHERE `".$this->cfg['dbpref']."deposits`.`jobid_fk` = ".$jid." ORDER BY `depositid` ASC");

		$output .= '<div class="payment-received-mini-view2" style="margin-top:5px;">';

		$pdi = 1;
		$output .= '<option value="0"> &nbsp; </option>';
		$output .= "<p><h6>Payment History</h6></p>";
		$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
		$output .= "<thead>";
		$output .= "<tr align='left'>";
		$output .= "<th class='header'>Invoice No</th>";
		$output .= "<th class='header'>Date Received</th>";
		$output .= "<th class='header'>Amt Received</th>";
		$output .= "<th class='header'>Payment Term</th>";
		$output .= "<th class='header'>Action</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		foreach ($recieve_query->result_array() as $dd)
		{
			$expected_date = date('d-m-Y', strtotime($dd['deposit_date']));
			$payment_amount = number_format($dd['amount'], 2, '.', ',');
			$amount_recieved += $dd['amount'];
			$payment_received = '';
			if ($dd['payment_received'] == 1)
			{
				$payment_received = '<img src="assets/img/vcs-payment-received.gif" alt="received" />';
			}
			$output .= "<tr align='left'>";
			$output .= "<td>".$dd['invoice_no']."</td>";
			$output .= "<td>".date('d-m-Y', strtotime($dd['deposit_date']))."</td>";
			$output .= "<td> ".$symbol.' '.number_format($dd['amount'], 2, '.', ',')."</td>";
			$output .= "<td>".$dd['payment_term']."</td>";
			$output .= "<td align='left'><a class='edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' >Edit</a> | ";
			$output .= "<a class='edit' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' >Delete</a></td>";
			$output .= "</tr>";
		}
		$output .= "<tr>";
		$output .= "<td></td>";
		$output .= "<td><b>Total Payment: </b> </td><td colspan='2'><b>".$symbol.' '.number_format($amount_recieved, 2, '.', ',')."</b></td>";
		$output .= "</tr>";
		$output .= "</table>";
		$output .= "</div>";
		echo $output;
	}
	
	//adding log
	function pjt_add_log()
	{ 
		$data_log = real_escape_array($this->input->post());
		
		$data_log['log_content'] = str_replace('\n', "", $data_log['log_content']);
		$data_log['log_content'] = str_replace('\\', "", $data_log['log_content']);
		$ins['log_content'] 	 = str_replace('\n', "", $data_log['log_content']);
		
		$break = 120;
		//$data_log['log_content'] =  implode(PHP_EOL, str_split($data_log['log_content'], $break));
		
		$data_log['sign_content'] = str_replace('\n', "", $data_log['sign_content']);
		$data_log['sign_content'] = str_replace('\\', "", $data_log['sign_content']);
		
		
        if (isset($data_log['lead_id']) && isset($data_log['userid']) && isset($data_log['log_content'])) {
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			$job_details = $this->project_model->get_lead_det($data_log['lead_id']);
            
            if (count($job_details) > 0)
            {
				$wh_condn = array('userid'=>$data_log['userid']);
				$user_data = $this->project_model->get_user_data_by_id('users', $wh_condn);
				
				$wh_condn_cust = array('custid'=>$job_details['custid_fk']);
				$client = $this->project_model->get_user_data_by_id('customers', $wh_condn_cust);
				
                $this->load->helper('url');
				
				$emails = trim($data_log['emailto'], ':');
				
				$successful = $received_by = '';
				
				if ($emails != '' || isset($data_log['email_to_customer']))
				{
					$emails = explode(':', $emails);
					$mail_id = array();
					foreach ($emails as $mail)
					{
						$mail_id[] = str_replace('email-log-', '', $mail);
					}

					$data['user_accounts'] = array();
					$this->db->where_in('userid', $mail_id);
					$users = $this->db->get($this->cfg['dbpref'] . 'users');
					
					if ($users->num_rows() > 0)
					{
						$data['user_accounts'] = $users->result_array();
					}
					foreach ($data['user_accounts'] as $ua)
					{
						# default email
						$to_user_email = $ua['email'];

						$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name'],'');
						$received_by .= $ua['first_name'] . ' ' . $ua['last_name'] . ', ';
					}
					$successful = 'This log has been emailed to:<br />';
					
					$log_subject = "eSmart Notification - {$job_details['lead_title']} [ref#{$job_details['lead_id']}] {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}";
					
					//email sent by email template
					$param = array();

					$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'first_name'=>$client[0]['first_name'], 'last_name'=>$client[0]['last_name'], 'log_content'=>$data_log['log_content'], 'received_by'=>$received_by, 'signature'=>$data_log['sign_content']);
                     
					$param['to_mail'] = $senders;
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $user_data[0]['email'];
					$param['from_email_name'] = $user_data[0]['first_name'];
					$param['template_name'] = "Project Notification Message";
					$param['subject'] = $log_subject;
					
					$json['debug_info'] = '';
					
					if (isset($data_log['email_to_customer']) && isset($data_log['client_email_address']) && isset($data_log['client_full_name']))
					{
						// we're emailing the client, so remove the eNoah log prefix
						$log_subject = preg_replace('/^eNoah Notification \- /', '', $log_subject);
						
						for ($cei = 1; $cei < 5; $cei ++)
						{
							if (isset($data_log['client_emails_' . $cei]))
							{
								$send_to[] = array($data_log['client_emails_' . $cei], '');
								$received_by .= $data_log['client_emails_' . $cei] . ', ';
							}
						}
						
						if (isset($data_log['additional_client_emails']) && trim($data_log['additional_client_emails']) != '')
						{
							$additional_client_emails = explode(',', trim($data_log['additional_client_emails'], ' ,'));
							if (is_array($additional_client_emails)) foreach ($additional_client_emails as $aces)
							{
								$aces = trim($aces);
								if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $aces))
								{
									$send_to[] = array($aces, '');
									$received_by .= $aces . ', ';
								}
							}
						}					
					}
					else
					{
						$dis['date_created'] = date('Y-m-d H:i:s');
						$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					}
					foreach($send_to as $recps) 
					{
						$arrRecs[]=$recps[0];
					}
					$senders=implode(',',$arrRecs);

					//email sent by email template
					$param = array();

					$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'first_name'=>$client[0]['first_name'], 'last_name'=>$client[0]['last_name'], 'log_content'=>$data_log['log_content'], 'received_by'=>$received_by, 'signature'=>$data_log['sign_content']);

					$param['to_mail'] = $senders;
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $user_data[0]['email'];
					$param['from_email_name'] = $user_data[0]['first_name'];
					$param['template_name'] = "Project Notification Message";
					$param['subject'] = $log_subject;
                   
					if($data_log['client_emails'] =='true')
					{ 
						$sent_mail = $this->project_model->sent_email_client($param);
					}
					else
					{
						$sent_mail = $this->email_template_model->sent_email($param);
					}
					if($sent_mail)
					{
						$successful .= trim($received_by, ', ');
					}
					else
					{
						echo 'failure';
					}
					
					if (isset($full_file_path) && is_file($full_file_path)) unlink ($full_file_path);
					
					if ($successful == 'This log has been emailed to:<br />')
					{
						$successful = '';
					}
					else
					{
						$successful = '<br /><br />' . $successful;
					}
				}
			
				$ins['jobid_fk'] = $data_log['lead_id'];
				
				// use this to update the view status
				$ins['userid_fk'] = $upd['log_view_status'] = $data_log['userid'];
				
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = $ins['log_content'] . $successful;
				
				$stick_class = '';
				if (isset($data_log['log_stickie']))
				{
					$ins['stickie'] = 1;
					$stick_class = ' stickie';
				}
				
				if (isset($data_log['time_spent']))
				{
					$ins['time_spent'] = (int) $data_log['time_spent'];
				}
				
				// inset the new log
				$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
				
				// update the leads table
				$this->db->where('lead_id', $ins['jobid_fk']);
				$this->db->update($this->cfg['dbpref'] . 'leads', $upd);
                
                $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $data_log['log_content'])))), 'url', TRUE)) . $successful;
                
				$fancy_date = date('l, jS F y h:iA', strtotime($ins['date_created']));
				
$table = <<<HDOC
<tr id="log" class="log{$stick_class}">
<td id="log" class="log">
<p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</td>
</tr>
HDOC;
				
                $json['error'] = FALSE;
                $json['html'] = $table;
				
                echo json_encode($json);
				exit;
            }
            else
            {

				$json['error'] = true;
				$json['errormsg'] = 'Post insert failed';
				echo json_encode($json);
				exit;
            }
        }
        else
        {
			$json['error'] = true;
			$json['errormsg'] = 'Invalid data supplied';
			echo json_encode($json);
			exit;
        }
    }
	
	/**
	 *uploading files - creating log
	 */
	public function lead_fileupload_details($lead_id, $filename, $userid) {
	   
		$lead_files['lead_files_name'] = $filename;
		$lead_files['lead_files_created_by'] = $userid;
		$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
		$lead_files['lead_id'] = $lead_id;
		$insert_logs = $this->project_model->insert_row('lead_files', $lead_files);
		
		$logs['jobid_fk'] = $lead_id;
		$logs['userid_fk'] = $this->userdata['userid'];
		$logs['date_created'] = date('Y-m-d H:i:s');
		$logs['log_content'] = $filename.' is added.';
		$logs['attached_docs'] = $filename;
		$insert_logs = $this->project_model->insert_row('logs', $logs);
	}
	
	/**
	 * Deletes Project from the list
	 */
	function delete_quote($id) 
	{
		if ($this->session->userdata('delete')==1) {
			if ($id > 0) {
				$pjt_det = $this->project_model->get_lead_det($id);
				$wh_condn = array('userid'=>$pjt_det['lead_assign']);
				$lead_assign_mail = $this->project_model->get_user_data_by_id('users', $wh_condn);

				$condn = array('userid'=>$pjt_det['belong_to']);
				$lead_owner = $this->project_model->get_user_data_by_id('users', $condn);

				$delete_job = $this->project_model->delete_project('leads', $id);
				if ($delete_job) 
				{
					$del_condn = array('jobid_fk'=>$id);
					$delete_item = $this->project_model->delete_row('items', $del_condn);
					$delete_log = $this->project_model->delete_row('logs', $del_condn); 
					$delete_task = $this->project_model->delete_row('tasks', $del_condn);
					$delete_deposits = $this->project_model->delete_row('deposits', $del_condn);
					$delete_exp_pay = $this->project_model->delete_row('expected_payments', $del_condn);
					
					$del_condn1 = array('lead_id'=>$id);					
					$delete_file = $this->project_model->delete_row('lead_files', $del_condn1);
					
					$del_condn2 = array('lead_id'=>$id);
					$delete_query = $this->project_model->delete_row('lead_query', $del_condn2);
					
					# Project Delete Mail Notification
					$ins['log_content'] = 'Project Deleted Sucessfully - Project ' .word_limiter($pjt_det['lead_title'], 4). ' ';

					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$from=$this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail  = implode(',',$mangement_email);
					$admin_mail = implode(',',$arrSetEmails);
					
					//email sent by email template
					$param = array();
					
					$param['email_data'] = array('user_name'=>$user_name, 'print_fancydate'=>$print_fancydate, 'log_content'=>$ins['log_content'], 'signature'=>$this->userdata['signature']);

					$param['to_mail'] 			= $mgmt_mail.','.$lead_owner[0]['email'];
					$param['bcc_mail'] 			= $admin_mail;
					$param['from_email'] 		= $this->userdata['email'];
					$param['from_email_name'] 	= $user_name;
					$param['template_name'] 	= "Lead - Delete Notification Message";
					$param['subject'] 			= "Project Delete Notification";

					$this->email_template_model->sent_email($param);
					
					$this->session->set_flashdata('confirm', array("Item deleted from the system"));

					redirect('project');
				}
				else 
				{
					$this->session->set_flashdata('login_errors', array("Error in Deletion."));
					redirect('project');
				}
			}
			else 
			{
				$this->session->set_flashdata('login_errors', array("Project does not exist or you may not be authorised to delete Project."));
				redirect('project');
			}
		} 
		else 
		{
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('project');
		}
	}
	
	public function set_project_estimate_hour()
	{
		$updt_data = real_escape_array($this->input->post());
		
		$result = $this->project_model->get_quote_data($updt_data['lead_id']);
		
		$data['error'] = FALSE;
		
		$estimateHr = $updt_data['esthr'];
		
		if (!is_numeric($updt_data['esthr'])) 
		{
			$data['error'] = 'Invalid estimated hour!';
		} 
		else 
		{
			$wh_condn = array('lead_id'=>$updt_data['lead_id']);
			$updt = array('estimate_hour'=>$estimateHr);
			$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
		}
		echo json_encode($data);
		exit;
	}
	
	public function set_project_type()
	{
		$updt_data = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		$project_type=$updt_data['project_type'];
		if ($updt_data['project_type'] == '')
		{
			$data['error'] = 'Please select project type';
		}
		else
		{
			$wh_condn  = array('lead_id'=>$updt_data['lead_id']);
			$updt	   = array('project_type'=>$project_type);
			$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
		}
		echo json_encode($data);
	}
	
	public function set_rag_status()
	{
		$updt_data = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		$rag_status=$updt_data['rag_status'];
		if ($updt_data['rag_status'] == '')
		{
			$data['error'] = 'Please check RAG status';
		}
		else
		{
			$wh_condn = array('lead_id'=>$updt_data['lead_id']);
			$updt = array('rag_status'=>$rag_status);
			$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
		}
		echo json_encode($data);
	}
	
	/*
	 *@method set_bill_type
	 *@set the bill type for the Project
	 */
	public function set_bill_type()
	{
		$updt_data = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		$billing_type = $updt_data['billing_type'];
		if ($updt_data['billing_type'] == '') {
			$data['error'] = 'Please Check Billing Type';
		} else {
			$wh_condn  = array('lead_id'=>$updt_data['lead_id']);
			$data 	   = array('billing_type'=>$billing_type);
			$updt_date = $this->project_model->update_row('leads', $data, $wh_condn);
			
			if($updt_date) {
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt_data['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}

		}
		echo json_encode($data);
	}
	
	/*
	 *@method set_customer_type
	 *@set the customer type for the Project
	 */
	public function set_customer_type()
	{
		$updt_data = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		$customer_type = $updt_data['customer_type'];
		if ($updt_data['customer_type'] == '') {
			$data['error'] = 'Please Check';
		} else {
			$wh_condn  = array('lead_id'=>$updt_data['lead_id']);
			$data 	   = array('customer_type'=>$customer_type);
			$updt_date = $this->project_model->update_row('leads', $data, $wh_condn);
			
			if($updt_date) {
				// $project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt_data['lead_id'], 'pjt_id');
				// $this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	

	/*
	*@For Add & Update addMilestones
	*@Method addMilestones
	*/
	function addMilestones($update = false)
	{
		$errors = array();
		$today = time();
		
		$milestone_data = real_escape_array($this->input->post());
		
		if(!empty($milestone_data['ms_plan_st_date'])) {
			$milestone_data['ms_plan_st_date'] = date('Y-m-d', strtotime($milestone_data['ms_plan_st_date']));
		}
		if(!empty($milestone_data['ms_plan_end_date'])) {
			$milestone_data['ms_plan_end_date'] = date('Y-m-d', strtotime($milestone_data['ms_plan_end_date']));
		}
		if(!empty($milestone_data['ms_act_st_date']) ) {
			$milestone_data['ms_act_st_date'] = date('Y-m-d', strtotime($milestone_data['ms_act_st_date']));
		}
		if(!empty($milestone_data['ms_act_end_date'])) {
			$milestone_data['ms_act_end_date'] = date('Y-m-d', strtotime($milestone_data['ms_act_end_date']));
		}
		
		$milestone_data['actual_effort'] = ($milestone_data['ms_effort'] * $milestone_data['ms_percent'])/100;
		
		if (count($errors)) {
		
			echo "<p style='color:#FF4400;'>" . join('\n', $errors) . "</p>";
			
		} else {
			$job_updated = FALSE;
			
			if ($update == "") {
				$ins_milestone = $this->project_model->insert_row('milestones', $milestone_data);
				$job_updated = TRUE;
			} else {
				$wh_condn = array('milestoneid'=>$update, 'jobid_fk'=>$milestone_data['jobid_fk']);
				$updt_row = $this->project_model->update_row("milestones", $milestone_data, $wh_condn);
				$job_updated = TRUE;
			}	
			
			$this->retrieveMilestoneTerms($milestone_data['jobid_fk']);
			
			if ($job_updated==FALSE) {
				echo "<h3>Add/Update Failed</h3>";
			}
		}
	}
	
	/*
	 *edit the milestone term
	 */
	function milestone_edit_term($ms_id, $pjtid)
	{
		$milestone_details = $this->project_model->get_milestone_term_det($ms_id, $pjtid);
		
		$msPlStDate  = date('d-m-Y', strtotime($milestone_details['ms_plan_st_date']));
		$msPlEndDate = date('d-m-Y', strtotime($milestone_details['ms_plan_end_date']));
		if($milestone_details['ms_act_st_date'] == '0000-00-00 00:00:00') {
			$msActStDate = '';
		} else {
			$msActStDate  = date('d-m-Y', strtotime($milestone_details['ms_act_st_date']));
		}
		if($milestone_details['ms_act_end_date'] == '0000-00-00 00:00:00') {
			$msActEndDate = '';
		} else {
			$msActEndDate = date('d-m-Y', strtotime($milestone_details['ms_act_end_date']));
		}
		
		$percentSelectBox = "<select name='ms_percent' id='ms_percent' class='textfield width60px'>";
		foreach($this->cfg['milestones_complete_status'] as $statusKey => $statusValue) {
			if($milestone_details['ms_percent']==$statusKey){
				$selectedPercent = 'selected="selected"';
			} else {
				$selectedPercent = '';
			}
			$percentSelectBox .= "<option value=".$statusKey." ".$selectedPercent.">".$statusValue."</option>";
		}
		$percentSelectBox .= "</select>";
		
		$statusSelectBox = '<select name="milestone_status" class="textfield width100px">';
		foreach($this->cfg['milestones_status'] as $msStatusKey => $msStatusValue) {
			if($milestone_details['milestone_status']==$msStatusKey){
				$selectedStatus = 'selected="selected"';
			} else {
				$selectedStatus = '';
			}
			$statusSelectBox .= "<option value=".$msStatusKey." ".$selectedStatus.">".$msStatusValue."</option>";
		}
		$statusSelectBox .= '</select>';
		
		echo '
		<script>			
		function isNumberKey(evt)
		{
          var charCode = (evt.which) ? evt.which : event.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
		}
		
		$(function() {
			$("#ms_plan_st_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, onSelect: function(date) {
				if($("#ms_plan_end_date").val!="") {
					$("#ms_plan_end_date").val("");
				}
			   var return_date = $("#ms_plan_st_date").val();
			   $("#ms_plan_end_date").datepicker("option", "minDate", return_date);
			
			}});
			$("#ms_plan_end_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true });
			
			$("#ms_act_st_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, onSelect: function(date) {
				if($("#ms_act_end_date").val!="")
				{
					$("#ms_act_end_date").val("");
				}
				var return_date = $("#ms_act_st_date").val();
				$("#ms_act_end_date").datepicker("option", "minDate", return_date);
			}});
			$("#ms_act_end_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true });
		});
		
		</script>
		
		<form id="milestone-management" onsubmit="return false;">
		<table class="milestone-table ms-toggler">
			<tr>
				<td>
				<p>Milestone name *<input type="text" name="milestone_name" id="milestone_name" value= "'.$milestone_details['milestone_name'].'" class="textfield" style="width:235px;" /> </p>
				</td>
			</tr>
			<tr>
				<td>
				<p style="float: left;">Planned Start Date *<input type="text" name="ms_plan_st_date" id="ms_plan_st_date" autocomplete="off" value= "'.$msPlStDate.'" class="textfield width60px pick-date" readonly /> </p>
				<p style="float: left; margin: 0px 15px;">Planned End Date *<input type="text" name="ms_plan_end_date" id="ms_plan_end_date" autocomplete="off" value= "'.$msPlEndDate.'" class="textfield width60px pick-date" readonly /> </p>
				</td>
			</tr>
			<tr>
				<td>
				<p style="float: left;">Actual Start Date<input type="text" name="ms_act_st_date" id="ms_act_st_date" autocomplete="off" value= "'.$msActStDate.'" class="textfield width60px pick-date" readonly /> </p>
				<p style="float: left; margin: 0px 15px;">Actual End Date<input type="text" name="ms_act_end_date" id="ms_act_end_date" autocomplete="off" value= "'.$msActEndDate.'" class="textfield width60px pick-date" readonly /> </p>
				</td>
			</tr>
			<tr>
			<td colspan=2>
			<p>
			Efforts *(Numbers)<input onkeypress="return isNumberKey(event)" type="text" name="ms_effort" value= "'.$milestone_details['ms_effort'].'" id="ms_effort" class="textfield width60px" maxlength="5" /></p>
			</td>
			</tr>
			
			<tr>
			<td>
				<p style="float: left;">Percentage of Completion '.$percentSelectBox.'</p>
				<p style="float: left; margin: 0px 15px;">Status '.$statusSelectBox.'</p>
					</td>
				</tr>
			<tr>
					<td colspan=2>
						<p>
						<div class="buttons">
							<button type="submit" class="positive" onclick="updateMilestoneTerms('.$ms_id.'); return false;">Update</button>
						</div>
						</p>
					</td>
				</tr>
			<input type="hidden" name="jobid_fk" id="jobid_fk" value="0" />
		</table>
		</form>';
	}
	
	//**Ajax Reload the Milestone Add view**//
	function addMilestoneFormView()
	{
		$percentSelectBox = "<select name='ms_percent' id='ms_percent' class='textfield width60px'>";
		foreach($this->cfg['milestones_complete_status'] as $statusKey => $statusValue) {
			$percentSelectBox .= "<option value=".$statusKey." ".$selectedPercent.">".$statusValue."</option>";
		}
		$percentSelectBox .= "</select>";
		
		$statusSelectBox = '<select name="milestone_status" class="textfield width100px">';
		foreach($this->cfg['milestones_status'] as $msStatusKey => $msStatusValue) {
			$statusSelectBox .= "<option value=".$msStatusKey." ".$selectedStatus.">".$msStatusValue."</option>";
		}
		$statusSelectBox .= '</select>';
		
		$clk = "onclick=$('.ms-toggler').slideToggle();";
	
		echo '<script>			
		function isNumberKey(evt)
		{
          var charCode = (evt.which) ? evt.which : event.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
		}
		$(function() {
			$("#ms_plan_st_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, onSelect: function(date) {
				if($("#ms_plan_end_date").val!="") {
					$("#ms_plan_end_date").val("");
				}
			   var return_date=$("#ms_plan_st_date").val();
			   $("#ms_plan_end_date").datepicker("option", "minDate", return_date);
			
			}});
			$("#ms_plan_end_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true });
			
			$("#ms_act_st_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, onSelect: function(date) {
				if($("#ms_act_end_date").val!="")
				{
					$("#ms_act_end_date").val("");
				}
				var return_date=$("#ms_act_st_date").val();
				$("#ms_act_end_date").datepicker("option", "minDate", return_date);
			}});
			$("#ms_act_end_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true });
		});
		</script>
		<form id="milestone-management" onsubmit="return false;">
		<table class="milestone-table ms-toggler">
			<tr>
				<td>
				<p>Milestone name *<input type="text" name="milestone_name" id="milestone_name" class="textfield" style="width:235px;" /> </p>
				</td>
			</tr>
			<tr>
				<td>
				<p style="float: left;">Planned Start Date *<input type="text" name="ms_plan_st_date" id="ms_plan_st_date" autocomplete="off" class="textfield width60px pick-date" readonly /> </p>
				<p style="float: left; margin: 0px 10px;">Planned End Date *<input type="text" name="ms_plan_end_date" id="ms_plan_end_date" autocomplete="off" class="textfield width60px pick-date" readonly /> </p>
				</td>
			</tr>
			<tr>
				<td>
				<p style="float: left;">Actual Start Date<input type="text" name="ms_act_st_date" id="ms_act_st_date" autocomplete="off" class="textfield width60px pick-date" readonly /> </p>
				<p style="float: left; margin: 0px 10px;">Actual End Date<input type="text" name="ms_act_end_date" id="ms_act_end_date" autocomplete="off" class="textfield width60px pick-date" readonly /> </p>
				</td>
			</tr>
			<tr>
			<td colspan=2><p>
			Efforts * (Numbers)<input onkeypress="return isNumberKey(event)" type="text" name="ms_effort" value= "'.$milestone_details['ms_effort'].'" id="ms_effort" class="textfield width60px" maxlength="5" /></p>
			</td>
			</tr>
			
			<tr>
			<td>
				<p style="float: left;">Percentage of Completion '.$percentSelectBox.'</p>
				<p style="float: left; margin: 0px 15px;">Status '.$statusSelectBox.'</p>
					</td>
				</tr>
			<tr>
					<td colspan=2>
						<p>
						<div class="buttons">
							<button type="submit" class="positive" onclick="addMilestoneTerms(); return false;">Add</button>
						</div>
						<div class="buttons">
							<button type="submit" '.$clk.'>Cancel</button>
						</div>
						</p>
					</td>
				</tr>
			<input type="hidden" name="jobid_fk" id="jobid_fk" value="0" />
		</table>
		</form>';
	}
	
	/*
	 *Delete the expected payment
	 *@params expect_id, lead_id
	 */
	function deleteMilestoneTerm($msid, $pjtid)
	{
		//delete the record
		$wh_condn = array('milestoneid' => $msid, 'jobid_fk' => $pjtid);
		$deleteTerm = $this->project_model->delete_row('milestones', $wh_condn);
		if ($deleteTerm)
		{
			echo "<span id=paymentfadeout><h6>Milestone Deleted!</h6></span>";
		}
		else
		{
			echo "<span id=paymentfadeout><h6>Error In Deletion!</h6></span>";
		}
		$this->retrieveMilestoneTerms($pjtid);
	}
	
	/*
	*@method retrieveMilestoneTerms()
	*@param Jobid
	*for lists all the milestone terms
	*/
	function retrieveMilestoneTerms($pjt_id)
	{
		$milestone_det = $this->project_model->get_milestone_terms($pjt_id); //after update		
		$output = '';
		$output .= "<table width='100%' class='payment_tbl'>
		<tr><td colspan='3'><h6>Milestone Terms</h6></td></tr>
		</table>";
		$output .= "<table class='data-table' id='milestone-data' cellspacing = '0' cellpadding = '0' border = '0'>";
		$output .= "<thead>";
		$output .= "<tr align='left'>";
		$output .= "<th class='header'>Milestone Name</th>";
		$output .= "<th class='header'>Planned Start Date</th>";
		$output .= "<th class='header'>Planned End Date</th>";
		$output .= "<th class='header'>Actual Start Date</th>";
		$output .= "<th class='header'>Actual End Date</th>";
		$output .= "<th class='header'>Efforts</th>";
		$output .= "<th class='header'>Completion(%)</th>";
		$output .= "<th class='header'>Status</th>";
		$output .= "<th class='header'>Action</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		if (count($milestone_det>0))
		{
			foreach ($milestone_det as $ms_det)
			{
				switch($ms_det['milestone_status']) {
					case 0:
					$ms_stat = 'Scheduled';
					break;
					case 1:
					$ms_stat = 'In Progress';
					break;
					case 2:
					$ms_stat = 'Completed';
					break;
				}
				$ms_act_st = ($ms_det['ms_act_st_date'] != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($ms_det['ms_act_st_date'])) : '';
				$ms_act_end = ($ms_det['ms_act_end_date'] != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($ms_det['ms_act_end_date'])) : '';
				$output .= "<tr>";
				$output .= "<td align='left'>".$ms_det['milestone_name']."</td>";
				$output .= "<td align='left'>".date('d-m-Y', strtotime($ms_det['ms_plan_st_date']))."</td>";
				$output .= "<td align='left'>".date('d-m-Y', strtotime($ms_det['ms_plan_end_date']))."</td>";
				$output .= "<td align='left'>".$ms_act_st."</td>";
				$output .= "<td align='left'>".$ms_act_end."</td>";
				$output .= "<td align='left'>".$ms_det['ms_effort']."</td>";
				$output .= "<td align='left'>".$ms_det['ms_percent']."</td>";
				$output .= "<td align='left'>".$ms_stat."</td>";
				$output .= "<td align='left'><a class='edit' title='Edit' onclick='milestoneEditTerm(".$ms_det['milestoneid']."); return false;' ><img src='assets/img/edit.png' alt='edit'></a>";
				$output .= "<a class='edit' title='Delete' onclick='milestoneDeleteTerm(".$ms_det['milestoneid'].");' ><img src='assets/img/trash.png' alt='delete'></a></td>";
				$output .= "</tr>";
			}
		}
		$output .= "</table>";
		$output .= "#";
		$output .= $this->calculateProjectMeter($pjt_id);
		echo $output;
	}
	
	/*
	*@method calculateProjectMeter()
	*@param leadid
	*/
	function calculateProjectMeter($jobid)
	{
		$projectMeterStatus = $this->project_model->get_project_meter_status($jobid);
		$meterStatus		= ($projectMeterStatus['actual_effort']/$projectMeterStatus['ms_effort'])*100;
		return round($meterStatus);
	}
	
	function exportMilestoneTerms()
	{
		$inputData = $this->input->post();
		$leadId	   = $inputData['lead_id'];
		
		$milestone_det = $this->project_model->get_milestone_terms($leadId);

		if(!empty($milestone_det)) {
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Milestone');
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			
			$this->excel->getActiveSheet()->setCellValue('A1', 'Milestone Name');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Planned Start Date');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Planned End Date');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Actual Start Date');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Actual End Date');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Efforts');
			$this->excel->getActiveSheet()->setCellValue('G1', 'Completion(%)');
			$this->excel->getActiveSheet()->setCellValue('H1', 'Status');
			$this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(10);
			
			$i=2;
			foreach ($milestone_det as $milestone) {
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $milestone['milestone_name']);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, date('d-m-Y', strtotime($milestone['ms_plan_st_date'])));
				$this->excel->getActiveSheet()->setCellValue('C'.$i, date('d-m-Y', strtotime($milestone['ms_plan_end_date'])));
				if($milestone['ms_act_st_date']==0){
					$actStartDate='NIL';
				}else{
					$actStartDate=date('d-m-Y', strtotime($milestone['ms_act_st_date']));
				}
				if($milestone['ms_act_end_date']==0){
					$actEndDate='NIL';
				}else{
					$actEndDate=date('d-m-Y', strtotime($milestone['ms_act_end_date']));
				}
				
				$this->excel->getActiveSheet()->setCellValue('D'.$i, $actStartDate);
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $actEndDate);
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $milestone['ms_effort']);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $milestone['ms_percent']);
				if($milestone['milestone_status'] == '0'){
					$mStatus='Scheduled';
				}elseif($milestone['milestone_status'] == '1'){
					$mStatus='In Progress';
				}if($milestone['milestone_status'] == '2'){
					$mStatus='Completed';
				}
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $mStatus);
				$i++;
			}
			$this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$filename='milestone_'.time().'.xls';
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
			//$error = true;
			exit();
		} else {
			$error = true;
			exit($error);
		}
	}
	
	/*
	*method : get_currency_rates
	*/
	public function get_currency_rates() {
		$currency_rates = $this->report_lead_region_model->get_currency_rate();
    	$rates 			= array();
    	if(!empty($currency_rates)) {
    		foreach ($currency_rates as $currency) {
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}
	
	public function conver_currency($amount, $val) {
			return round($amount*$val, 2);
	}
	
	/* Change the actual worth amount to Default currency */
	public function getProjectsDataByDefaultCurrency_original($records,$project_billing_type=false,$metrics_date=false)
	{ //echo date('Y-m-01'); exit;
		$rates = $this->get_currency_rates();
		 
		$data['project_record'] = array();
		$i = 0;
		echo"<pre>";print_r($records);echo"</pre>";exit;
		if (isset($records) && count($records)) :
			foreach($records as $rec) {
				
				$amt_converted = $this->conver_currency($rec['actual_worth_amount'], $rates[$rec['expect_worth_id']][$this->default_cur_id]);
				
				$data['timesheet_data'] = array();
				$timesheet				= array();
				$total_cost				= 0;
				$total_hours			= 0;
				$total_billable_hrs		= 0;
				$total_internal_hrs		= 0;
				$total_non_billable_hrs = 0;
				$project_type			= '';
				
				if(!empty($project_billing_type) && $project_billing_type==3) {
					$bill_type = 3;
				} else {
					$bill_type = $rec['billing_type'];
				}
				echo"<BR>get_timesheet_data_updated";
				if(!empty($rec['pjt_id'])){
					$timesheet = $this->project_model->get_timesheet_data_updated($rec['pjt_id'], $rec['lead_id'], $bill_type, $metrics_date, $groupby_type=2);
				}
				
				$total_amount_inv_raised = 0;
				$invoice_amount = $this->project_model->get_invoice_total($rec['lead_id']);
				if(count($invoice_amount)>0 && !empty($invoice_amount)){
					$total_amount_inv_raised = $invoice_amount->invoice_amount+$invoice_amount->tax_price;
				}
				
				$total_billable_hrs = 0;
				$total_internal_hrs = 0;
				$total_non_billable_hrs = 0;
				$total_cost  = 0;
				$total_hours = 0;
				$total_dc_hours = 0;
				
				/* calculation for UC based on the max hours starts */
				$timesheet_data = array();
				if(count($timesheet)>0) {
					foreach($timesheet as $ts) {
					
						$financialYear 		= get_current_financial_year($ts['yr'],$ts['month_name']);
						$max_hours_resource = get_practice_max_hour_by_financial_year($ts['practice_id'],$financialYear);
						
						$timesheet_data[$ts['username']]['practice_id'] = $ts['practice_id'];
						$timesheet_data[$ts['username']]['max_hours'] = $max_hours_resource->practice_max_hours;
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
						$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
						$directrateCostPerHr = $this->conver_currency($ts['direct_cost'], $rates[1][$this->default_cur_id]);
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['direct_rateperhr'] = $directrateCostPerHr;
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['direct_cost'] = $ts['duration_direct_cost'];
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']]['total_hours'] = $ts['resource_total_hours'];
						/* get_timesheet_hours_by_user($ts['username'],$ts['yr'],$ts['month_name'],array('Leave','Hol'));*/
					}
				}
				$total_billable_hrs		= 0;
				$total_non_billable_hrs = 0;
				$total_internal_hrs		= 0;
				$total_cost				= 0;
				$total_hours			= 0;
				$total_dc_hours			= 0;
				
				if(count($timesheet_data)>0 && !empty($timesheet_data)){
					foreach($timesheet_data as $key1=>$value1) {
						$resource_name = $key1;
						$max_hours = $value1['max_hours'];
						
						foreach($value1 as $key2=>$value2) {
							$year = $key2;
							foreach($value2 as $key3=>$value3) {
								$individual_billable_hrs		= 0;
								$month		 	  = $key3;
								$billable_hrs	  = 0;
								$non_billable_hrs = 0;
								$internal_hrs	  = 0;
								foreach($value3 as $key4=>$value4) {
									
									switch($key4) {
										case 'Billable':
											$rate				 = $value4['rateperhr'];
											$direct_rateperhr	 = $value4['direct_rateperhr'];
											$billable_hrs		 = $value4['duration'];
											$direct_billable_hrs = $value4['duration_direct_cost'];
											$total_billable_hrs += $billable_hrs;
										break;
										case 'Non-Billable':
											$rate				 	 = $value4['rateperhr'];
											$direct_rateperhr	 	 = $value4['direct_rateperhr'];
											$non_billable_hrs		 = $value4['duration'];
											$direct_non_billable_hrs = $value4['duration_direct_cost'];
											$total_non_billable_hrs += $non_billable_hrs;
										break;
										case 'Internal':
											$rate				 = $value4['rateperhr'];
											$direct_rateperhr	 = $value4['direct_rateperhr'];
											$internal_hrs 		 = $value4['duration'];
											$direct_internal_hrs = $value4['duration_direct_cost'];
											$total_internal_hrs += $internal_hrs;
										break;
									}
								}
							
								$individual_billable_hrs = $value3['total_hours'];
								 
								// calculation for the utilization cost based on the master hours entered.

								$rate1 = $rate;
								$direct_rateperhr1 = $direct_rateperhr;
								if($individual_billable_hrs>$max_hours){
									//echo 'max'.$max_hours.'<br>';
									$percentage = ($max_hours/$individual_billable_hrs);
									//echo 'percentage'.$percentage.'<br>';
									$rate1 = number_format(($percentage*$rate),2);
									$direct_rateperhr1 = number_format(($percentage*$direct_rateperhr),2);
								}
							 
								$total_hours += $billable_hrs+$internal_hrs+$non_billable_hrs;
								//$total_dc_hours += $direct_billable_hrs+$direct_non_billable_hrs+$direct_internal_hrs;
								$total_dc_hours += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);
								$total_cost += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);				
							}
						}
					}	 
				}	 
				/* calculation for UC based on the max hours ends */
				
				$total_amount_inv_raised = $this->conver_currency($total_amount_inv_raised, $rates[$rec['expect_worth_id']][$this->default_cur_id]);
				
				//get the other cost details for the project.
				$other_cost_values = $this->getOtherCostValues($rec['lead_id']);
				
				// for company name
				$company = $rec['company'];
				if($rec['cfname']!=''){
					$company .= ' - '.$rec['cfname'];
				}
				/* if($rec['clname']!=''){
					$company .= ' '.$rec['clname'];
				} */
				//Build the Array
				$data['project_record'][$i]['lead_id'] 			= $rec['lead_id'];
				$data['project_record'][$i]['invoice_no'] 		= $rec['invoice_no'];
				$data['project_record'][$i]['division'] 		= $rec['division'];
				$data['project_record'][$i]['lead_title']		= $rec['lead_title'];
				$data['project_record'][$i]['customer_name']	= $company;
				$data['project_record'][$i]['actual_worth_amt'] = number_format($amt_converted, 2, '.', '');
				$data['project_record'][$i]['lead_stage']		= $rec['lead_stage'];
				$data['project_record'][$i]['pjt_id']			= $rec['pjt_id'];
				$data['project_record'][$i]['assigned_to'] 		= $rec['assigned_to'];
				$data['project_record'][$i]['date_start'] 		= $rec['date_start'];
				$data['project_record'][$i]['date_due'] 		= $rec['date_due'];
				$data['project_record'][$i]['complete_status'] 	= $rec['complete_status'];
				$data['project_record'][$i]['pjt_status'] 		= $rec['pjt_status'];
				$data['project_record'][$i]['estimate_hour'] 	= $rec['estimate_hour'];
				$data['project_record'][$i]['project_type']	 	= $rec['project_billing_type'];
				$data['project_record'][$i]['rag_status'] 		= $rec['rag_status'];
				$data['project_record'][$i]['cfname'] 			= $rec['cfname'];
				$data['project_record'][$i]['clname'] 			= '';
				$data['project_record'][$i]['company'] 			= $rec['company'];
				$data['project_record'][$i]['fnm'] 				= $rec['fnm'];
				$data['project_record'][$i]['lnm'] 				= $rec['lnm'];
				$data['project_record'][$i]['billing_type'] 	= $rec['billing_type'];
				$data['project_record'][$i]['bill_hr'] 			= $total_billable_hrs;
				$data['project_record'][$i]['int_hr'] 			= $total_internal_hrs;
				$data['project_record'][$i]['nbil_hr'] 			= $total_non_billable_hrs;
				$data['project_record'][$i]['other_cost'] 		= $other_cost_values;
				$data['project_record'][$i]['total_hours'] 		= $total_hours;
				$data['project_record'][$i]['total_dc_hours'] 	= $total_dc_hours;
				$data['project_record'][$i]['total_amount_inv_raised'] = $total_amount_inv_raised;
				$data['project_record'][$i]['total_cost'] 		= number_format($total_cost, 2, '.', '');
				$i++;
			}
		endif;
		//print_r($data['project_record']); exit;
		return $data['project_record'];
	}
	
	public function getProjectsDataByDefaultCurrency($records,$project_billing_type=false,$metrics_date=false)
	{   
		$rates 					= $this->get_currency_rates();
		$practice_id_year_array = $this->project_model->get_practice_id_year();
		$practice_id_array  	= $this->project_model->get_practice_id();
		$book_keeping_rates 	= get_book_keeping_rates();
		$data['project_record'] 			= array();
		$arr_billing_type_projects 			= array();
		$arr_billing_type_project_codes 	= array();
		$arr_billing_type_project_lead_ids 	= array();
		/** Making billing type vice project details and project codes **/
		foreach($records as $rec) {
			$arr_billing_type_projects[$rec['billing_type']][]						= $rec;
			$arr_billing_type_project_codes[$rec['billing_type']]['project_code'][]	= $rec['pjt_id'];
			$arr_billing_type_project_lead_ids[$rec['billing_type']]['lead_id'][]	= $rec['lead_id'];
		}
		/** Loop through all projects according to billing type  **/
		foreach($arr_billing_type_project_codes as $key_billing_type=>$res) 
		{
			$data['timesheet_data'] = array();
		    $timesheet				= array();
			$lead_id_array 			= $arr_billing_type_project_lead_ids[$key_billing_type]['lead_id'];
			$invoice_amount_array 	= $this->project_model->get_invoice_total_by_lead($lead_id_array);
			$other_cost_array 		= $this->project_model->get_other_cost_by_all_lead($lead_id_array);
			/** For monthly billing billing type is 3 **/
			if(!empty($project_billing_type) && $project_billing_type==3) {
				$bill_type = 3;
			} else {
				$bill_type = $key_billing_type;
			}
			$exp_proj_codes = $res['project_code'];
			/** Getting timesheet details against projects **/
			$timesheet = $this->project_model->get_timesheet_data_updated($exp_proj_codes, '', $bill_type, $metrics_date, $groupby_type=2);
			/* calculation for UC based on the max hours starts */
			$timesheet_data = array();
			if(count($timesheet)>0) {
				foreach($timesheet as $ts) {
					$financialYear 		= get_current_financial_year($ts['yr'],$ts['month_name']);
					$max_hrs 			= 0;
					if(isset($practice_id_year_array[$ts['practice_id']][$financialYear]))
					{
						$max_hrs = $practice_id_year_array[$ts['practice_id']][$financialYear];
					}
					else if(isset($practice_id_array[$ts['practice_id']]))
					{
						$max_hrs = $practice_id_array[$ts['practice_id']];
					}
					$timesheet_data[$ts['project_code']][$ts['username']]['practice_id'] = $ts['practice_id'];
					$timesheet_data[$ts['project_code']][$ts['username']]['max_hours'] = $max_hrs;
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
					$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
					$directrateCostPerHr = $this->conver_currency($ts['direct_cost'], $rates[1][$this->default_cur_id]);
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['direct_rateperhr'] = $directrateCostPerHr;
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration_direct_cost'] = $ts['duration_direct_cost'];
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']]['total_hours'] = $ts['resource_total_hours'];
					
				}
			}
			
				$i		= 0; 
				$keys	= array();
				foreach($exp_proj_codes as $proj_key) {
					$total_billable_hrs		= 0;
					$total_non_billable_hrs = 0;
					$total_internal_hrs		= 0;
					$total_cost				= 0;
					$total_hours			= 0;
					$total_dc_hours			= 0;
					$total_amount_inv_raised= 0;
					$other_cost_values		= 0;
					if(array_key_exists($proj_key, $timesheet_data))
					{
						$projects_arr = $timesheet_data[$proj_key];
						foreach($projects_arr as $key1=>$value1) {
							$resource_name = $key1;
							$max_hours = $value1['max_hours'];
							foreach($value1 as $key2=>$value2) {
								$year = $key2;										
								if(!empty($value2) && is_array($value2)){
									foreach($value2 as $key3=>$value3) {
										$individual_billable_hrs		= 0;
										$month		 	  = $key3;
										$billable_hrs	  = 0;
										$non_billable_hrs = 0;
										$internal_hrs	  = 0;
										foreach($value3 as $key4=>$value4) {
											
											switch($key4) {
												case 'Billable':
													$rate				 = $value4['rateperhr'];
													$direct_rateperhr	 = $value4['direct_rateperhr'];
													$billable_hrs		 = $value4['duration'];
													$direct_billable_hrs = $value4['duration_direct_cost'];
													$total_billable_hrs += $billable_hrs;
												break;
												case 'Non-Billable':
													$rate				 	 = $value4['rateperhr'];
													$direct_rateperhr	 	 = $value4['direct_rateperhr'];
													$non_billable_hrs		 = $value4['duration'];
													$direct_non_billable_hrs = $value4['duration_direct_cost'];
													$total_non_billable_hrs += $non_billable_hrs;
												break;
												case 'Internal':
													$rate				 = $value4['rateperhr'];
													$direct_rateperhr	 = $value4['direct_rateperhr'];
													$internal_hrs 		 = $value4['duration'];
													$direct_internal_hrs = $value4['duration_direct_cost'];
													$total_internal_hrs += $internal_hrs;
												break;
											}
										}
									
										$individual_billable_hrs = $value3['total_hours'];											 
										/* calculation for the utilization cost based on the master hours entered. */
										$rate1 = $rate;
										$direct_rateperhr1 = $direct_rateperhr;
										if($individual_billable_hrs>$max_hours){												
											$percentage 		= ($max_hours/$individual_billable_hrs);												
											$rate1 				= number_format(($percentage*$rate),2);
											$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
										}
									 
										$total_hours += $billable_hrs+$internal_hrs+$non_billable_hrs;											
										$total_dc_hours += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);
										$total_cost += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);				
									}
								}
							}
						}
					}
				$proj = $arr_billing_type_projects[$key_billing_type][$i];
				$amt_converted = $this->conver_currency($proj['actual_worth_amount'], $rates[$proj['expect_worth_id']][$this->default_cur_id]);
				$total_amount_inv_raised = 0;
				$invoice_amount=0;
				if(!empty($invoice_amount_array))
				{
					if(array_key_exists($lead_id_array[$i],$invoice_amount_array))
					{
						$invoice_amount = $invoice_amount_array[$lead_id_array[$i]];
						if(count($invoice_amount)>0 && !empty($invoice_amount)){
							$total_amount_inv_raised = $invoice_amount['invoice_amount']+$invoice_amount['tax_amount'];
						}
					}
				}
				/* calculation for UC based on the max hours ends */
				$total_amount_inv_raised = $this->conver_currency($total_amount_inv_raised, $rates[$proj['expect_worth_id']][$this->default_cur_id]);
				/* get the other cost details for the project. */
				$other_cost_values = 0;
				if(!empty($other_cost_array))
				{
					if(array_key_exists($lead_id_array[$i], $other_cost_array))
					{
						$other_cost_values = $this->getOtherCostValuesForBookRates($other_cost_array[$lead_id_array[$i]],$book_keeping_rates);
					}
				}
				
			   /* for company name */
				$company = $proj['company'];
				if($rec['cfname']!=''){
					$company .= ' - '.$proj['cfname'];
				} 	
			
				/** Building resultant array **/
				$data['project_record'][$proj_key]['lead_id'] 			= $proj['lead_id'];
				$data['project_record'][$proj_key]['invoice_no'] 		= $proj['invoice_no'];
				$data['project_record'][$proj_key]['division'] 			= $proj['division'];
				$data['project_record'][$proj_key]['lead_title']		= $proj['lead_title'];
				$data['project_record'][$proj_key]['customer_name']		= $company;
				$data['project_record'][$proj_key]['actual_worth_amt']  = number_format($amt_converted, 2, '.', '');
				$data['project_record'][$proj_key]['lead_stage']		= $proj['lead_stage'];
				$data['project_record'][$proj_key]['pjt_id']			= $proj['pjt_id'];
				$data['project_record'][$proj_key]['assigned_to'] 		= $proj['assigned_to'];
				$data['project_record'][$proj_key]['date_start'] 		= $proj['date_start'];
				$data['project_record'][$proj_key]['date_due'] 			= $proj['date_due'];
				$data['project_record'][$proj_key]['complete_status'] 	= $proj['complete_status'];
				$data['project_record'][$proj_key]['pjt_status'] 		= $proj['pjt_status'];
				$data['project_record'][$proj_key]['estimate_hour'] 	= $proj['estimate_hour'];
				$data['project_record'][$proj_key]['project_type']	 	= $proj['project_billing_type'];
				$data['project_record'][$proj_key]['rag_status'] 		= $proj['rag_status'];
				$data['project_record'][$proj_key]['cfname'] 			= $proj['cfname'];
				$data['project_record'][$proj_key]['clname'] 			= '';
				$data['project_record'][$proj_key]['company'] 			= $proj['company'];
				$data['project_record'][$proj_key]['fnm'] 				= $proj['fnm'];
				$data['project_record'][$proj_key]['lnm'] 				= $proj['lnm'];
				$data['project_record'][$proj_key]['billing_type'] 		= $proj['billing_type'];
				$data['project_record'][$proj_key]['bill_hr'] 			= $total_billable_hrs;
				$data['project_record'][$proj_key]['int_hr'] 			= $total_internal_hrs;
				$data['project_record'][$proj_key]['nbil_hr'] 			= $total_non_billable_hrs;
				$data['project_record'][$proj_key]['other_cost'] 		= $other_cost_values;
				$data['project_record'][$proj_key]['total_hours'] 		= $total_hours;
				$data['project_record'][$proj_key]['total_dc_hours'] 	= $total_dc_hours;
				$data['project_record'][$proj_key]['total_amount_inv_raised'] = $total_amount_inv_raised;
				$data['project_record'][$proj_key]['total_cost'] 		= number_format($total_cost, 2, '.', '');
				$i++;
			}
		}
		return $data['project_record'];
	}
	
	/*
	*get all other cost values from the db & sum it
	*base currency
	*/
	function getOtherCostValues($project_id)
	{
		$value = 0;
		$bk_rates = get_book_keeping_rates(); //get all the book keeping rates
		$other_cost_data = $this->project_model->getOtherCost($project_id);
		if(!empty($other_cost_data) && count($other_cost_data)>0) {
			foreach($other_cost_data as $rec) {
				$conver_value  = 0;
				$curFiscalYear = date('Y'); //set as default current year as fiscal year
				$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
				$convert_value = $this->conver_currency($rec['value'], $bk_rates[$curFiscalYear][$rec['currency_type']][$this->default_cur_id]);
				$value += $convert_value;
			}
		}		
		return $value;
	}
	function getOtherCostValuesForBookRates($other_cost_data,$bk_rates)
	{
		$value = 0;
		if(!empty($other_cost_data) && count($other_cost_data)>0) {
			foreach($other_cost_data as $rec) {
				$conver_value  = 0;
				$curFiscalYear = date('Y'); //set as default current year as fiscal year
				$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
				$convert_value = $this->conver_currency($rec['value'], $bk_rates[$curFiscalYear][$rec['currency_type']][$this->default_cur_id]);
				$value += $convert_value;
			}
		}	
		return $value;
	}
	
	/*
	*@Get Current Financial year
	*@Method  calculateFiscalYearForDate
	*@param date, fy start date & fy end date
	*@return fy
	*/
	function calculateFiscalYearForDate($inputDate, $fyStart, $fyEnd) 
	{
		$date = strtotime($inputDate);
		$inputyear = strftime('%Y',$date);
	 
		$fystartdate = strtotime($fyStart.'/'.$inputyear);
		$fyenddate = strtotime($fyEnd.'/'.$inputyear);
	 
		if($date <= $fyenddate) {
			$fy = intval($inputyear);
		} else {
			$fy = intval(intval($inputyear) + 1);
		}
		return $fy;
	}
	
	/* Export to Excel */
	public function excelExport($searchId = false)
	{
		$keyword = null;
		if($searchId != '' & is_numeric($searchId)) {
			$wh_condn = array('search_id'=>$searchId, 'search_for'=>2, 'user_id'=>$this->userdata['userid']);
			$get_rec  = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
			if(!empty($get_rec))
			$filter	= real_escape_array($get_rec);
			$filter['export_type'] = $this->input->post('export_type');
			
			if((!empty($filter['pjtstage'])) && $filter['pjtstage']!='null')
			$pjtstage = explode(",",$filter['pjtstage']);
			else 
			$pjtstage = '';
			
			if((!empty($filter['customer'])) && $filter['customer']!='null')
			$cust = explode(",",$filter['customer']);
			else
			$cust = '';
			
			if((!empty($filter['service'])) && $filter['service']!='null')
			$service = explode(",",$filter['service']);
			else
			$service = '';
			
			if((!empty($filter['practice'])) && $filter['practice']!='null')
			$practice = explode(",",$filter['practice']);
			else
			$practice = '';
			
		} else {
			$filter = real_escape_array($this->input->post());
			
			if((!empty($filter['stages'])) && $filter['stages']!='null')
			$pjtstage = explode(",",$filter['stages']);
			else 
			$pjtstage = '';
			
			if((!empty($filter['customers'])) && $filter['customers']!='null')
			$cust = explode(",",$filter['customers']);
			else
			$cust = '';
			
			if((!empty($filter['services'])) && $filter['services']!='null')
			$service = explode(",",$filter['services']);
			else
			$service = '';
			
			if((!empty($filter['practices'])) && $filter['practices']!='null')
			$practice = explode(",",$filter['practices']);
			else
			$practice = '';
		}
		
		if((!empty($filter['divisions'])) && $filter['divisions']!='null')
		$divisions = explode(",",$filter['divisions']);
		else
		$divisions = '';
		
		if(!empty($filter['from_date']))
		$from_date = $filter['from_date'];
		else
		$from_date = '';
		
		if(!empty($filter['to_date']))
		$to_date = $filter['to_date'];
		else
		$to_date = '';
		
		if(!empty($filter['datefilter']))
		$datefilter = $filter['datefilter'];
		else
		$datefilter = '';
		
		$keyword = $this->input->post('keyword');
		// echo "asdf<pre>"; print_r($filter); exit;
		
		$export_type = $filter['export_type'];
		
		if($export_type == 'milestone') {
			$billing_type = 1;
		}
		
		if($export_type == 'monthly') {
			$billing_type = 2;
			$metrics_month = $this->input->post('metrics_month');
			$metrics_year = $this->input->post('metrics_year');
			$metrics_date = date('Y-m-01', strtotime($metrics_year.'-'.$metrics_month));
			$project_type = 3;
		}
		
    	$getProjectData = $this->project_model->get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,$billing_type,$divisions);
		// echo $this->db->last_query(); exit;
		$pjts_data	    = $this->getProjectsDataByDefaultCurrency($getProjectData,$project_type,$metrics_date);
		// echo "<pre>"; print_r($pjts_data); exit;
    	if(count($pjts_data)>0) {
    		//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle($export_type);

			//set cell A1 content with some text			
			$this->excel->getActiveSheet()->setCellValue('A1', 'Project Title');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Project Completion (%)');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Project Type');
			$this->excel->getActiveSheet()->setCellValue('D1', 'RAG Status');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Planned Hours');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Billable Hours');
			$this->excel->getActiveSheet()->setCellValue('G1', 'Internal Hours');
			$this->excel->getActiveSheet()->setCellValue('H1', 'Non-Billable Hours');
			$this->excel->getActiveSheet()->setCellValue('I1', 'Total Utilized Hours (Actuals)');
			$this->excel->getActiveSheet()->setCellValue('J1', 'Effort Variance');
			$this->excel->getActiveSheet()->setCellValue('K1', 'Project Value ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('L1', 'Utilization Cost ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('M1', 'Direct Cost ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('N1', 'Invoice Raised ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('O1', 'Contribution %');
			$this->excel->getActiveSheet()->setCellValue('P1', 'P&L');
			$this->excel->getActiveSheet()->setCellValue('Q1', 'P&L %');

			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);

			$i=2;
			
    		foreach($pjts_data as $rec) {
			
				$estimate_hr =  (isset($rec['estimate_hour'])) ? $rec['estimate_hour'] : "-";
				switch ($rec['rag_status']) {
					case 1:
						$rag = 'Red';
					break;
					case 2:
						$rag = 'Amber';
					break;
					case 3:
						$rag = 'Green';
					break;
					default:
						$rag = '-';
				}
				$bill_hr = (isset($rec['bill_hr'])) ? round($rec['bill_hr']) : "-";
				$inter_hr = (isset($rec['int_hr'])) ? round($rec['int_hr']) : "-";
				$nbill_hr = (isset($rec['nbil_hr'])) ? round($rec['nbil_hr']) : "-";
				$total_hr = ($rec['bill_hr']+$rec['int_hr']+$rec['nbil_hr']);
				$pjt_val = (isset($rec['actual_worth_amt'])) ? $rec['actual_worth_amt'] : "-";
				$util_cost = (isset($rec['total_cost'])) ? round($rec['total_cost']) : "-";
				$total_amount_inv_raised = (isset($rec['total_amount_inv_raised'])) ? round($rec['total_amount_inv_raised']) : "-";
				$total_dc_hours = (isset($rec['total_dc_hours'])) ? (round($rec['total_dc_hours'])) : '0';
				$contributePercent = round((($total_amount_inv_raised-$total_dc_hours)/$total_amount_inv_raised)*100);

				$profitloss    = round($total_amount_inv_raised-$util_cost);
				//$plPercent = ($rec['actual_worth_amt']-$rec['total_cost'])/$rec['actual_worth_amt'];
				$plPercent = round(($profitloss/$util_cost)*100);
				
				//$percent = ($plPercent == FALSE)?'-':round($plPercent)*100;
				
				$bill_type = $rec['billing_type'];
				
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $rec['lead_title']);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $rec['complete_status']);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $rec['project_type']);
				$this->excel->getActiveSheet()->setCellValue('D'.$i, $rag);
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $estimate_hr);
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $bill_hr);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $inter_hr);
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $nbill_hr);
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $total_hr);
				$this->excel->getActiveSheet()->setCellValue('J'.$i, $total_hr-$rec['estimate_hour']);
				$this->excel->getActiveSheet()->setCellValue('K'.$i, $pjt_val);
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $util_cost);
				$this->excel->getActiveSheet()->setCellValue('M'.$i, $total_dc_hours);
				$this->excel->getActiveSheet()->setCellValue('N'.$i, $total_amount_inv_raised);
				$this->excel->getActiveSheet()->setCellValue('O'.$i, $contributePercent);
				$this->excel->getActiveSheet()->setCellValue('P'.$i, $profitloss);
				$this->excel->getActiveSheet()->setCellValue('Q'.$i, $plPercent);
				$i++;
    		}
			
			
			//for first sheet
			$this->excel->setActiveSheetIndex(0);
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
			$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
			//Column Alignment
			$this->excel->getActiveSheet()->getStyle('D2:D'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('E2:E'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('F2:F'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('H2:H'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('I2:I'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('J2:J'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('K2:K'.$i)->getNumberFormat()->setFormatCode('0.00');
			
			// $filename='Project_report.xls'   ; //save our workbook as this file name
			$filename='Project_report_'.time().'.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
    	}	
    	redirect('/project/');
    }
	
	public function filterTimesheetMetricsData()
	{
		$postdata   = real_escape_array($this->input->post());
		$start_date = date('Y-m-01', strtotime($postdata['start_date']));

		//for differentiate sending the past date in search.
		$bill_type  = 3;
		$timesheet  = $this->project_model->get_timesheet_data($postdata['project_code'], $postdata['project_id'], $bill_type, $start_date, $groupby_type=2);
		
		$rates = $this->get_currency_rates();
		$data['timesheet_data'] = array();
		/* if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				$costdata = array();
				if(isset($ts['cost'])) {
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
					$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$postdata['expect_worth_id']]);
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
				} else {
					$costdata = $this->project_model->get_latest_cost($ts['username']);
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $costdata['cost'];
					$rateCostPerHr = $this->conver_currency($costdata['cost'], $rates[1][$postdata['expect_worth_id']]);
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
				}
				$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['Duration'];
				$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['first_name'] . ' ' .$ts['last_name'];
			}
		} */
		
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				if(isset($ts['cost'])) {
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
					$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
					$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
				}
			}
		}

		$data['project_costs'] = array();
		if(!empty($data['timesheet_data'])) {
			// echo "<pre>"; print_r($data['timesheet_data']); exit;
			$res = $this->calcActualProjectCost($data['timesheet_data']);
			if($res['total_cost']>0) {
				$data['project_costs'] = $res['total_cost'];
			}
			if($res['total_hours']>0) {
				$data['actual_hour_data'] = $res['total_hours'];
			}
		}
		
		//Display the Date in view
		$htmldata = '';
		if(count($data['timesheet_data'])>0){
			
			$total_billable_hrs		= 0;
			$total_non_billable_hrs = 0;
			$total_internal_hrs		= 0;
			$total_cost				= 0;
			
			$htmldata = '<table class="head_timesheet data-table">
							<tr>
								<th>Resource</th>
								<th>Month & Year</th>
								<th>Billable Hours</th>
								<th>Internal Hours</th>
								<th>Non-Billable Hours</th>
								<th>Cost Per Hour('.$postdata["cur_name"].')</th>
								<th>Cost('.$postdata["cur_name"].')</th>
							</tr>
						</table>';
			
			$htmldata .= '<table class="data-table">';
			
			foreach($data['timesheet_data'] as $key1=>$value1) {
				$resource_name = $key1;
				foreach($value1 as $key2=>$value2) {
					$year = $key2;
					foreach($value2 as $key3=>$value3) {
						$month		 	  = $key3;
						$billable_hrs	  = 0;
						$non_billable_hrs = 0;
						$internal_hrs	  = 0;
						foreach($value3 as $key4=>$value4) {
							switch($key4) {
								case 'Billable':
									$rs_name			 = $value4['rs_name'];
									$rate				 = $value4['rateperhr'];
									$billable_hrs 		 = $value4['duration'];
									$total_billable_hrs += $billable_hrs;
								break;
								case 'Non-Billable':
									$rs_name			 	 = $value4['rs_name'];
									$rate					 = $value4['rateperhr'];
									$non_billable_hrs		 = $value4['duration'];
									$total_non_billable_hrs += $non_billable_hrs;
								break;
								case 'Internal':
									$rs_name			 = $value4['rs_name'];
									$rate				 = $value4['rateperhr'];
									$internal_hrs		 = $value4['duration'];
									$total_internal_hrs += $internal_hrs;
								break;
							}
						}
						$htmldata .= "<tr>
							<td>".$rs_name."</td>
							<td>".substr($month, 0, 3). " " . $year."</td>
							<td align=right>".sprintf('%0.2f', $billable_hrs)."</td>
							<td align=right>".sprintf('%0.2f', $internal_hrs)."</td>
							<td align=right>".sprintf('%0.2f', $non_billable_hrs)."</td>
							<td align=right>".$rate."</td>
							<td align=right>".sprintf('%0.2f', $rate*($billable_hrs+$internal_hrs+$non_billable_hrs))."</td>
						</tr>";
						$total_cost += $rate*($billable_hrs+$internal_hrs+$non_billable_hrs);
					}
				}
			}
			$htmldata .= "<tr>
							<td align=right><b>Total</b></td>
							<td></td>
							<td align=right><b>".sprintf('%0.2f', $total_billable_hrs)."</b></td>
							<td align=right><b>".sprintf('%0.2f', $total_internal_hrs)."</b></td>
							<td align=right><b>".sprintf('%0.2f', $total_non_billable_hrs)."</b></td>
							<td></td>
							<td align=right><b>".sprintf('%0.2f', $total_cost)."</b></td>
						</tr>";
			$htmldata .= '</table>';
		} else {
			$htmldata .= '<div align="center" style="margin: 20px 0 0;"><b> No Data Available for "'.$postdata['start_date'].'"</b></div>';
		}
		echo $htmldata;
		exit;
	}
	
	public function generateInvoice($eid, $pjtid) 
	{
		$inv_gen_time	 = date('Y-m-d H:i:s');
		$wh_condn		 = array('expectid' => $eid,'jobid_fk'=>$pjtid);
		$updt			 = array('invoice_status'=>1, 'invoice_generate_notify_date'=>$inv_gen_time);
		
		$output['error'] = FALSE;
		
		$updt_payment_ms = $this->project_model->update_row('expected_payments', $updt, $wh_condn);
		// $updt_payment_ms = 1;
		
		if($updt_payment_ms) {
			$project_details = $this->project_model->get_quote_data($pjtid);
			$pm_inv_cc_mail  = '';
			if( $this->userdata['userid'] != $project_details[0]['assigned_to'] ) {
				//get user details
				$cc_wh_condn 	= array('userid'=>$project_details[0]['assigned_to']);
				$cc_user_data 	= $this->project_model->get_user_data_by_id('users', $cc_wh_condn);
				$pm_inv_cc_mail = isset($cc_user_data[0]['email']) ? $cc_user_data[0]['email'] : '';
			}
			
			$payment_details 	 = $this->project_model->get_payment_term_det($eid, $pjtid);
			$attached_files  	 = $this->project_model->get_attached_files($eid);
			
			$user_name 			 = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
			$dis['date_created'] = date('Y-m-d H:i:s');
			$print_fancydate 	 = date('l, jS F y h:iA', strtotime($dis['date_created']));

			$from		  	 = $this->userdata['email'];
			$arrayEmails   	 = $this->config->item('crm');
			$to				 = implode(',',$arrayEmails['account_emails']);
			
			//checking attaching attachment not exceed to 5 mb - (5mb == 5242880)
			$file_size = 0;
			$attachment_exists = false;
			$attachment_array = array();
			if(is_array($attached_files) && !empty($attached_files) && count($attached_files)>0){
				$attach_file_path = UPLOAD_PATH.'files/'.$pjtid.'/';
				foreach($attached_files as $att_file) {
					$file_size += filesize($attach_file_path.$att_file['lead_files_name']);
					$attachment_array[] = $attach_file_path.$att_file['lead_files_name'];
				}
			}
			//1-BPO 2-ITS 
			switch($project_details[0]['project_center']) {
				case 1:
					$cc_email = implode(',', $arrayEmails['bpo_invoice_emails_cc']);
				break;
				case 2:
					$cc_email = implode(',', $arrayEmails['its_invoice_emails_cc']);
				break;
				default:
					$cc_email = implode(',', $arrayEmails['bpo_invoice_emails_cc']);   
			}

			/* switch($project_details[0]['practice']) {
				case 1:
				case 3:
				case 5:
				case 7:
				case 10:
				case 12:
				case 13:
					$cc_email = implode(',', $arrayEmails['eads_account_emails_cc']);
				break;
				case 6:
					$cc_email = implode(',', $arrayEmails['bpo_account_emails_cc']);
				break;
				default:
					$cc_email = implode(',', $arrayEmails['account_emails_cc']);
				break;
			} */
			
			$subject		 = 'Generate Invoice Notification';
			$customer_name   = $project_details[0]['company'].' - '.$project_details[0]['customer_name'];
			$project_name	 = word_limiter($project_details[0]['lead_title'], 4);
			$project_id	 	 = $project_details[0]['invoice_no'];
			$project_code	 = $project_details[0]['pjt_id'];
			$milestone_name  = $payment_details['project_milestone_name'];
			$month_year  	 = date('F Y', strtotime($payment_details['month_year']));
			$payment_type	 = '';
			$inv_amt['sign'] = '';
			$inv_amt 	  	 = array('value' => abs($payment_details['amount']));
			if ($payment_details['amount'] < 0)
			{
				$inv_amt['sign'] = '-';
				$payment_type = ' (Negative Invoice)';
			}			
			$milestone_value = $inv_amt['sign'].' '.$payment_details['expect_worth_name'] . ' ' . $inv_amt['value'] . $payment_type;
			$payment_remark  = isset($payment_details['payment_remark']) ? $payment_details['payment_remark'] : '-';
			if(is_array($attachment_array) && !empty($attachment_array) && count($attachment_array)>0) {
				$attachment_exists = true;
				if($file_size != 0 && $attachment_exists == true) {
					if(5242880 < $file_size) {
						$payment_remark .= "<br /><br /> <span style='color:red';>Attachments Size exceeds to 5 Mb. So attachments are not included in this mail.</span>";
						$attached_files = array();
					}
				}
			}

			//email sent by email template
			$param = array();
			
			$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'month_year'=>$month_year,'signature'=>$this->userdata['signature'],'customer_name'=>$customer_name,'project_name'=>$project_name,'project_id'=>$project_id,'project_code'=>$project_code,'milestone_name'=>$milestone_name,'milestone_value'=>$milestone_value,'payment_remark'=>$payment_remark);

			$param['to_mail'] 		  = $to;
			$param['cc_mail'] 		  = $this->userdata['email'].','.$cc_email.','.$pm_inv_cc_mail;
			// $param['bcc_mail'] 	  = $bcc_email;
			$param['from_email']	  = 'webmaster@enoahprojects.com';
			$param['from_email_name'] = 'Webmaster';
			$param['template_name']	  = "Generate Invoice Notification";
			$param['subject'] 		  = $subject;
			$param['attach'] 		  = $attached_files;
			$param['job_id'] 		  = $pjtid;
			
			//insert log
			$ins_log = array();
			$ins_log['log_content'] 	= 'Invoice has been raised for the milestone "'.$payment_details['project_milestone_name'].'" & for the Month & Year "'.$month_year.'".<br /> Invoice Amount - '.trim($milestone_value).'.';
			$ins_log['jobid_fk']    	= $pjtid;
			$ins_log['date_created'] 	= $inv_gen_time;
			$ins_log['userid_fk']   	= $this->userdata['userid'];
			$insert_log = $this->welcome_model->insert_row('logs', $ins_log);

			$this->email_template_model->sent_email($param);
		} else {
			$output['error'] = true;
			$output['errormsg'] = 'An error occured. Milestone cannot be updated.';
		}
		echo json_encode($output);
	}
	
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function payment_file_upload($lead_id, $filefolder_id)
	{
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['payment_ajax_file_uploader']['name']);
		
		$user_data = $this->session->userdata('logged_in_user');

		$project_members = $this->request_model->get_project_members($lead_id); // This array to get a project normal members(Developers) details.
		$project_leaders = $this->request_model->get_project_leads($lead_id); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
		$arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
		$arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
		$arrLeadInfo = $this->request_model->get_lead_info($lead_id); // This function to get a current lead informations.		
		
		// CHANGES BY MANI START HERE
		if($filefolder_id == 'Files') {
			$arrFolderId   = $this->request_model->getParentFfolderId($lead_id, 0); 
			$filefolder_id = $arrFolderId['folder_id'];
		}
		// CHANGES BY MANI END HERE

		//creating files folder name
		$f_dir = UPLOAD_PATH.'files/';
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		//creating lead_id folder name
		$f_dir = $f_dir.$lead_id;
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$this->upload->initialize(array(
		   "upload_path" => $f_dir,
		   "overwrite" => FALSE,
		   "remove_spaces" => TRUE,
		   "max_size" => 51000000,
		   "allowed_types" => "*"
		)); 
		// $config['allowed_types'] = '*';
		// "allowed_types" => "gif|png|jpeg|jpg|bmp|tiff|tif|txt|text|doc|docs|docx|oda|class|xls|xlsx|pdf|mpp|ppt|pptx|hqx|cpt|csv|psd|pdf|mif|gtar|gz|zip|tar|html|htm|css|shtml|rtx|rtf|xml|xsl|smi|smil|tgz|xhtml|xht"
		
		$returnUpload = array();
		$json  		  = array();
		$res_file     = array();
		if(!empty($_FILES['payment_ajax_file_uploader']['name'][0])) {
			if ($this->upload->do_multi_upload("payment_ajax_file_uploader")) { 
			   $returnUpload  = $this->upload->get_multi_upload_data();
			   $json['error'] = FALSE;
			   $json['msg']   = "File successfully uploaded!";
			   $i = 1;
			   if(!empty($returnUpload)) {
				  foreach($returnUpload as $file_up) {
					$lead_files['lead_files_name']		 = $file_up['file_name'];
					$lead_files['lead_files_created_by'] = $this->userdata['userid'];
					$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
					$lead_files['lead_id'] 				 = $lead_id;
					$lead_files['folder_id'] 			 = $filefolder_id; //get here folder id from file_management table.
					$insert_file						 = $this->request_model->return_insert_id('lead_files', $lead_files);
					$json['res_file'][]					 = $insert_file.'~'.$file_up['file_name'];
					
					$logs['jobid_fk']	   = $lead_id;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $file_up['file_name'].' is added.';
					$logs['attached_docs'] = $file_up['file_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);					
					$i++;
				  }
			   }
			} else {
				$this->upload_error = strip_tags($this->upload->display_errors());
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
			}
		}
		echo json_encode($json); exit;
	}
	
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function othercost_file_upload($lead_id, $filefolder_id)
	{		
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['othercost_ajax_file_uploader']['name']);
		
		$user_data = $this->session->userdata('logged_in_user');


		// CHANGES BY MANI START HERE
		if($filefolder_id == 'Files') {
			$arrFolderId = $this->request_model->getParentFfolderId($lead_id, 0); 
			$filefolder_id = $arrFolderId['folder_id'];
		}
		// CHANGES BY MANI END HERE

		//creating files folder name
		$f_dir = UPLOAD_PATH.'files/';
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		//creating lead_id folder name
		$f_dir = $f_dir.$lead_id;
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$this->upload->initialize(array(
		   "upload_path" => $f_dir,
		   "overwrite" => FALSE,
		   "remove_spaces" => TRUE,
		   "max_size" => 51000000,
		   "allowed_types" => "*"
		)); 
	
		$returnUpload = array();
		$json  		  = array();
		$res_file     = array();
		if(!empty($_FILES['othercost_ajax_file_uploader']['name'][0])) {
			if ($this->upload->do_multi_upload("othercost_ajax_file_uploader")) { 
			   $returnUpload  = $this->upload->get_multi_upload_data();
			   $json['error'] = FALSE;
			   $json['msg']   = "File successfully uploaded!";
			   $i = 1;
			   if(!empty($returnUpload)) {
				  foreach($returnUpload as $file_up) {
					$lead_files['lead_files_name']		 = $file_up['file_name'];
					$lead_files['lead_files_created_by'] = $this->userdata['userid'];
					$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
					$lead_files['lead_id'] 				 = $lead_id;
					$lead_files['folder_id'] 			 = $filefolder_id; //get here folder id from file_management table.
					$insert_file						 = $this->request_model->return_insert_id('lead_files', $lead_files);
					$json['res_file'][]					 = $insert_file.'~'.$file_up['file_name'];
					
					$logs['jobid_fk']	   = $lead_id;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $file_up['file_name'].' is added.';
					$logs['attached_docs'] = $file_up['file_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);					
					$i++;
				  }
			   }
			} else {
				$this->upload_error = strip_tags($this->upload->display_errors());
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
			}
		}
		echo json_encode($json); exit;
	}
	
	function download_file($job_id,$file_name)
	{
		$this->load->helper('download');
		$file_dir = UPLOAD_PATH.'files/'.$job_id.'/'.$file_name;
		$data = file_get_contents($file_dir); // Read the file's contents
		$name = $file_name;
		force_download($name, $data); 
	}
	
	
	/*
	*
	*@ Author Mani.S
	*@ Method Name getCurentLeadsDetails
	*@ Parameter current job id
	*@ Usage Display Client and project details with model window
	*
	*/
	public function getCurentLeadsDetails()
	{
	
		$inputData = real_escape_array($this->input->post());		
		$id        = $inputData['job_id'];
		
		$getLeadDet = $this->welcome_model->get_lead_detail($id);
		// echo $this->db->last_query(); exit;
		$arrLeadInfo = $this->request_model->get_lead_info($id);
		
		if(!empty($getLeadDet)) {
		
		    $data['quote_data'] = $getLeadDet[0];

			// $customer = $this->customer_model->get_customer($data['quote_data']['custid_fk']);	
			$customer    = $this->customer_model->get_lead_customer($data['quote_data']['custid_fk']);
			$data['customer_data'] = $customer[0];

			if($customer[0]['sales_contact_userid_fk']!='0') {
				$data['sales_person_detail'] = $this->customer_model->get_records_by_id('users', array('userid'=>$customer[0]['sales_contact_userid_fk']));
			}
			
		 	$data['regions']    = $this->regionsettings_model->region_list();
		 	$data['project_id'] = $inputData['job_id'];
			
            $data['view_quotation'] = true;
			$data['user_accounts']  = $this->welcome_model->get_users();
			
			$data['user_roles']		     = $this->userdata['role_id']; 
			$data['login_userid']		 = $this->userdata['userid']; 
			$data['project_belong_to']	 = $arrLeadInfo['belong_to']; 
			$data['project_assigned_to'] = $arrLeadInfo['assigned_to']; 
			$data['project_lead_assign'] = $arrLeadInfo['lead_assign']; 

			$data['query_files1_html'] = $this->welcome_model->get_query_files_list($id);
			/**
			 * Get URLs associated with this job
			 */
			$data['job_urls_html'] = $this->welcome_model->get_job_urls($id);
			
			$data['lead_stat_history'] = $this->welcome_model->get_lead_stat_history($id);
			
			$data['job_cate'] = $this->welcome_model->get_lead_services();
			
			/**
			*@Initiate to get all departments data
			**/
			$data['departments'] = $this->department_model->get_departments_list(array('active'=>1));
			
			/**
			*@Initiate to get all project types data
			**/
			$data['project_types'] = $this->project_types_model->get_project_types_list(array('status'=>1));
			
			/**
			*@Initiate to get all cost center data
			**/
			$data['arr_cost_center'] = $this->cost_center_model->get_cost_center_list(array('status'=>1));
			
			/**
			*@Initiate to get all billing category data from timesheet database
			**/
			$data['billing_categories'] = $this->project_model->get_billing_types();
			
			/**
			*@Initiate to get all project types data from timesheet database
			**/
			$data['timesheet_project_types'] = $this->project_model->get_timesheet_project_types();
			
			/**
			*@Initiate to get all billing category data from timesheet database
			**/
			$data['arr_profit_center'] = $this->profit_center_model->get_profit_center_list();
			
			/**
			*@Initiate to get all Practice data database
			**/
			$data['practices'] 		= $this->project_model->get_practices();

			$data['contract_users'] = $this->project_model->get_contract_users($id);
			$data['stake_holders'] 	= $this->project_model->get_stake_holders($id);

			$this->load->view('leads/leads_confirmations_view', $data);
        }
	
	}
	
	/*
	*@method set_service_req
	*@Use update lead service to project
	*@Author eNoah - Sriram.S
	*/
	public function set_service_req()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['lead_service'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Value Missing';
		} else {
			$wh_condn 	= array('lead_id' => $updt['lead_id']);
			$data 		= array('lead_service' => $updt['lead_service']);
			
			if($this->db->update($this->cfg['dbpref'] . 'leads', $data, $wh_condn)) {
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	*@method set_departments
	*@Use update department to leads
	*@Author eNoah - Sriram.S
	*/
	public function set_departments()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['department_id_fk'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data = array('department_id_fk' => $updt['department_id_fk']);
			$updt_id = $this->project_model->update_practice('leads', $data, $wh_condn);
			if($updt_id) {
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
	/*
	*@method set_project_types
	*@Use update project_types to leads
	*@Author eNoah - Mani.S
	*/
	public function set_project_types()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['project_types'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data = array('project_type' => $updt['project_types']);
			$updt_id = $this->project_model->update_practice('leads', $data, $wh_condn);

			if($updt_id) {
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
	/*
	*@method set_econ_project_types
	*@Use update project_types to leads
	*@Author eNoah
	*/
	public function set_econ_project_types()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['project_types'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data = array('project_types' => $updt['project_types']);
			$updt_id = $this->project_model->update_practice('leads', $data, $wh_condn);

			if($updt_id) {
				// $project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				// $this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
	
	/*
	 *@method set_bill_type
	 *@set the bill type for the Project
	 */
	public function set_sow_status()
	{
		$updt_data = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		$sow_status = $updt_data['sow_status'];
		if ($updt_data['sow_status'] == '') {
			$data['error'] = 'Please Check SOW Status';
		} else {
			$wh_condn  = array('lead_id'=>$updt_data['lead_id']);
			$data 	   = array('sow_status'=>$sow_status);
			$updt_date = $this->project_model->update_row('leads', $data, $wh_condn);
			
			if($updt_date) {
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt_data['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
	/*
	*@method set_resource_type
	*@Use update resource_types to leads table
	*@Table leads
	*@Author eNoah - Mani.S
	*/
	public function set_resource_type()
	{
		$updt = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;

		if (($updt['resource_type'] == "") or ($updt['lead_id'] == "")) {
			$data['error'] = 'Error in Updation';
		} else {
			$wh_condn = array('lead_id' => $updt['lead_id']);
			$data	  = array('resource_type' => $updt['resource_type']);
			$updt_id  = $this->project_model->update_practice('leads', $data, $wh_condn);
			
			if($updt_id) {
				$project_code = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $updt['lead_id'], 'pjt_id');
				$this->customer_model->update_project_details($project_code); //Update project title to timesheet and e-connect
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Error in Updation';
			}
		}
		echo json_encode($data);
	}
	
	/**
	 * Uploads a file posted to a specified project
	 * works with the Ajax SOW file uploader
	 */
	public function sow_file_upload($lead_id)
	{
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['sow_ajax_file_uploader']['name']);
		
		// $user_data = $this->session->userdata('logged_in_user');

		$project_members = $this->request_model->get_project_members($lead_id); // This array to get a project normal members(Developers) details.
		$project_leaders = $this->request_model->get_project_leads($lead_id); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
		$arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
		$arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
		$arrLeadInfo = $this->request_model->get_lead_info($lead_id); // This function to get a current lead informations.		

				
		
		// echo "<pre>"; print_r($_FILES); exit;
		
		$arrFolderId = $this->request_model->getParentFfolderId($lead_id, 0); 
		$filefolder_id = $arrFolderId['folder_id'];
		

		//creating files folder name
		$f_dir = UPLOAD_PATH.'files/';
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		//creating lead_id folder name
		$f_dir = $f_dir.$lead_id;
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$this->upload->initialize(array(
		   "upload_path" => $f_dir,
		   "overwrite" => FALSE,
		   "remove_spaces" => TRUE,
		   "max_size" => 51000000,
		   "allowed_types" => "*"
		)); 
		// $config['allowed_types'] = '*';
		// "allowed_types" => "gif|png|jpeg|jpg|bmp|tiff|tif|txt|text|doc|docs|docx|oda|class|xls|xlsx|pdf|mpp|ppt|pptx|hqx|cpt|csv|psd|pdf|mif|gtar|gz|zip|tar|html|htm|css|shtml|rtx|rtf|xml|xsl|smi|smil|tgz|xhtml|xht"
		
		$returnUpload = array();
		$json  		  = array();
		$res_file     = array();
		if(!empty($_FILES['sow_ajax_file_uploader']['name'][0])) {
			if ($this->upload->do_multi_upload("sow_ajax_file_uploader")) { 
			   $returnUpload  = $this->upload->get_multi_upload_data();
			   $json['error'] = FALSE;
			   $json['msg']   = "File successfully uploaded!";
			   $i = 1;			  
			   if(!empty($returnUpload)) {
				  foreach($returnUpload as $file_up) {
					$lead_files['lead_files_name']		 = $file_up['file_name'];
					$lead_files['lead_files_created_by'] = $this->userdata['userid'];
					$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
					$lead_files['lead_id'] 				 = $lead_id;
					$lead_files['folder_id'] 			 = $filefolder_id; //get here folder id from file_management table.
					$insert_file						 = $this->request_model->return_insert_id('lead_files', $lead_files);
					$json['res_file'][]					 = $insert_file.'~'.$file_up['file_name'];
					
					$logs['jobid_fk']	   = $lead_id;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $file_up['file_name'].' is added.';
					$logs['attached_docs'] = $file_up['file_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
				
					$i++;
				  }
			   }
			} else {
				$this->upload_error = strip_tags($this->upload->display_errors());
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
				// return $this->upload_error;						
				// exit;
			}
		}
		echo json_encode($json); exit;
	}
	
	
	//For Saving the search criteria
	public function save_search($type)
	{
		$post_data = real_escape_array($this->input->post());
		$ins = array();
		
		$ins['search_for']   = $type;
		$ins['search_name']  = $post_data['search_name'];
		$ins['user_id']	  	 = $this->userdata['userid'];
		$ins['is_default']	 = $post_data['is_default'];
		$ins['pjtstage'] 	 = $post_data['pjtstage'];
		$ins['customer']	 = $post_data['customer'];
		$ins['service']		 = $post_data['service'];
		$ins['divisions']	 = $post_data['divisions'];
		$ins['customer_type']= $post_data['customer_type'];
		$ins['practice']     = $post_data['practice'];
		$ins['datefilter']   = $post_data['datefilter'];
		$ins['from_date'] 	 = $post_data['from_date'];
		$ins['to_date']	 	 = $post_data['to_date'];
		$ins['created_on'] 	 = date('Y-m-d H:i:s');
		// echo "<pre>"; print_r($ins); exit;
		
		$last_ins_id = $this->welcome_model->insert_row_return_id('saved_search_critriea', $ins);
		if($last_ins_id) {
			if($post_data['is_default'] == 1) {
				$updt['is_default'] = 0;
				$this->db->where('search_id != ', $last_ins_id);
				$this->db->where('user_id', $this->userdata['userid']);
				$this->db->where('search_for', $type);
				$this->db->update($this->cfg['dbpref'] . 'saved_search_critriea', $updt);
				// echo $this->db->last_query();
			}
			
			$saved_search = $this->welcome_model->get_saved_search($this->userdata['userid'], $search_for=$type);
			
			$result['res'] = true;
			$result['msg'] = 'Search Criteria Saved.';
			
			$result['search_div'] = '';
			
			$result['search_div'] .= '<li id="item_'.$last_ins_id.'" class="saved-search-res"><span><a href="javascript:void(0)" onclick="show_search_results('.$last_ins_id.')">'.$post_data['search_name'].'</a></span>';
			$result['search_div'] .= '<span class="rd-set-default">';
			$result['search_div'] .= '<input type="radio" name="set_default_search" class="set_default_search" value="'.$last_ins_id.'" ';
			if($post_data['is_default']==1) {
				$result['search_div'] .= 'checked="checked"';
			}
			$result['search_div'] .= '/>';
			$result['search_div'] .= '</span>';
			$result['search_div'] .= '<span><a title="Set Default" href="javascript:void(0)" onclick="delete_save_search('.$last_ins_id.')" ><img alt="delete" src="assets/img/trash.png"></a></span></li>';

		} else {
			$result['res'] = false;
			$result['msg'] = 'Search Criteria cant be Saved.';
		}
		echo json_encode($result);
		exit;
	}
	
	public function get_search_name_form() {
		$html = '<table><tr>';
		$html .= '<td><label>Search Name:</label></td>';
		$html .= '<td><input type="text"  class="textfield width160px" name="search_name" id="search_name" value="" /></td></tr><tr>';
		$html .= '<td><label>Is Default:</label></td>';
		$html .= '<td><input type="checkbox" name="is_default" id="is_default" value="1" /></td></tr><tr><td colspan=2>';
		$html .= '<div class="buttons"><button onclick="save_search(); return false;" class="positive" type="submit">Save</button>
		<button onclick="save_cancel(); return false;" class="negative" type="submit">Cancel</button></div></td></tr></table>';
		echo json_encode($html);
		exit;
	}
	
	public function set_default_search($search_id, $type) {
		
		$result = array();
		
		$tbl = 'saved_search_critriea';
		$wh_condn = array('search_for'=>$type, 'user_id'=>$this->userdata['userid']);
		
		$updt = $this->welcome_model->update_records($tbl,$wh_condn,'',$up_arg=array('is_default'=>0));
		$updt_condn = $this->welcome_model->update_records($tbl,$wh_condn=array('search_id'=>$search_id),'',$up_arg=array('is_default'=>1));

		if($updt_condn) {
			$result['resu'] = 'updated';
		}
		
		echo json_encode($result);
		exit;
	}

	public function delete_save_search($search_id, $type) {
		
		$result = array();
		
		$tbl = 'saved_search_critriea';
		$wh_condn = array('search_for'=>$type, 'search_id'=>$search_id);

		if($this->welcome_model->delete_records($tbl, $wh_condn)) {
			$result['resu'] = 'deleted';
		}
		
		echo json_encode($result);
		exit;
	}
	
	public function get_folder_permissions_ui_for_a_project()
	{
		$lead_id = $this->input->get('lead_id');
		$data    = array('lead_id'=>$lead_id);
		
		// $lead_folders = $this->welcome_model->getLeadFolders($lead_id);
		$team_members   = $this->welcome_model->getLeadTeamMembers($lead_id);
		$lead_folders   = $this->welcome_model->get_tree_file_list_except_root($lead_id);
		$data['folders_access'] = $this->welcome_model->get_folders_access($lead_id);
		// echo "<pre>"; print_r($folders_access); exit;
		
		$data['team_members'] = $team_members;
		$data['lead_folders'] = $lead_folders;

		echo $this->load->view('projects/lead_folder_permissions', $data);
	}
	
	public function save_folder_permissions()
	{
		$error = true;
		$lead_id = $this->input->post('lead_id');
		
		$team_members = $this->welcome_model->getLeadTeamMembers($lead_id);
		$lead_folders = $this->welcome_model->get_tree_file_list_except_root($lead_id);
		
		foreach($lead_folders as $folder_id => $folder_name) 
		{
			if($folder_name!=$lead_id)
			{
				foreach($team_members as $member)
				{
					$record_array = array();
					$user_id    = $member['userid_fk'];
					$record_array['user_id'] = $user_id;
					$input_name = 'permission_for_'.$folder_id.'_'.$user_id;
					
					$permission = isset($_POST[$input_name]) ? $this->input->post($input_name) : 0;
										
					$record_array['updated_by']  = $this->userdata['userid'];
					$record_array['updated_on']  = date('Y-m-d H:i:s');
					$record_array['access_type'] = $permission;
					
					$exist_record_id = $this->welcome_model->checkIsFolderAccessRecordExist($lead_id, $folder_id, $user_id);
					// echo "<pre>"; print_r($exist_record_id); exit;
					if(!empty($exist_record_id)) {
						$exist_id = $exist_record_id['lead_folder_access_id'];
						$stat = $this->welcome_model->updateFolderAccessRecord((int)$exist_id, $record_array);
					} else {
						$record_array['user_id']    = $user_id;
						$record_array['lead_id'] 	= $lead_id;
						$record_array['folder_id']  = $folder_id;
						$record_array['created_by'] = $this->userdata['userid'];
						$record_array['created_on'] = date('Y-m-d H:i:s');
						
						$ins_stat = $this->welcome_model->createFolderAccessRecord($record_array);
						if($ins_stat == true){
							$error = false;
						}
					}
				}
			}
		}
		if($error == false){
			echo "Error in saving";
		} else {
			echo "true";
		}
		exit;
	}
	
	function set_dashboard_fields()
	{
		$fields = array();
		$fields['CN'] = 'Customer Name';
		$fields['CP'] = 'Completion Percentage';
		$fields['PT'] = 'Project Type';
		$fields['RAG'] = 'RAG';
		$fields['PH'] = 'Planned Hours';
		$fields['BH'] = 'Billable Hours';
		$fields['IH'] = 'Internal Hours';
		$fields['NBH'] = 'Non Billable Hours';
		$fields['TUH'] = 'Total Utilized Hours';
		$fields['EV'] = 'Effort Variance';
		$fields['PV'] = 'Project Value';
		$fields['UC'] = 'Utilization Cost';
		$fields['RC'] = 'Resource Cost';
		$fields['OC'] = 'Other Cost';
		$fields['IR'] = 'Invoice Raised';
		$fields['Contribution %'] = 'Contribution %';
		$fields['P&L'] = 'Profit & Loss';
		$fields['P&L %'] = 'Profit & Loss %';
		
		$data['fields'] = $fields;
		
		$oldfields  = $this->project_model->get_dashboard_field($this->userdata['userid']);
		$remove_select = array();
		// echo "<pre>"; print_r($oldfields); die;
		$old_select = $base_select = '';
		if(!empty($oldfields) && count($oldfields)>0){
			$cl_checked1 = ' selected="selected"';
			foreach($oldfields as $record) {
				$old_select .= '<option value="'.$record['column_name'].'"' .$cl_checked1.'>' . $fields[$record['column_name']].'</option>';
				$remove_select[] = $record['column_name'];
			}
		}

		foreach($fields as $key=>$val) {
			if(!in_array($key, $remove_select)){
				$base_select .= '<option value="'.$key.'">' . $val.'</option>';
			}
		}
		$data['base_select'] = $base_select;
		$data['old_select']  = $old_select;
		// echo "<pre>"; print_r($old_select); echo"***"; print_r($base_select); die;
		
		$this->load->view('projects/set_dashboard_fields', $data);
	}
	
	function save_dashboard_fields()
	{
		$existfields  = $this->project_model->get_records('project_dashboard_fields', $arr=array('user_id'=>$this->userdata['userid']), $ord=array('column_order'=>'ASC'));
		
		$i=0;
		$res = array();
		$wh_condn = array('user_id'=>$this->userdata['userid']);
		$del = $this->db->delete($this->cfg['dbpref'].'project_dashboard_fields', $wh_condn);
		$newselect = $this->input->post('new_select');
		
		if(!empty($newselect) && count($newselect)>0){
			foreach($newselect as $rec){
				$ins_arr = array('user_id'=>$this->userdata['userid']);
				$ins_arr['column_name'] = $rec;
				$ins_arr['column_order'] = $i;
				$i++;
				$insert = $this->project_model->insert_row('project_dashboard_fields', $ins_arr);
			}
		}
		if($insert){
			$res['result'] = 'success';
		} else {
			$res['result'] = 'error';
		}
		echo json_encode($res); 
		exit;
	}
	
	public function getCustomers($id)
	{
		$result = $this->project_model->get_quote_data($id);

		$data['quote_data']		= $result[0];
		// $this->userdata
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$data['chge_access'] = 1;
		} else {
			$data['chge_access'] = $this->project_model->get_access($id, $this->userdata['userid']);
		}
		//get customers & company
		$data['company_det'] = $this->welcome_model->get_company_det($data['quote_data']['companyid']);
		$data['contact_det'] = $this->welcome_model->get_contact_det($data['quote_data']['companyid']);
		
		$this->load->view('projects/load_customer_det', $data);
	}
	
	public function sendTestEmail()
	{
		//email sent by email template
		$param = array();

		$param['to_mail'] 		  = 'ssriram@enoahisolution.com';
		$param['from_email']	  = 'raamsri14@gmail.com';
		$param['from_email_name'] = 'Webmaster';
		$param['template_name']	  = "test email";
		$param['subject'] 		  = "test email";

		if($this->email_template_model->sent_email($param)){
			echo "Email Sent";
		} else {
			echo "Email Not Sent";
		}
		echo $this->email->print_debugger();
	}
	
	//check gantt chart data exists for given project id
	public function check_gantt_chart_data($id)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('project_id', $id);
		$this->db->where('status', 0);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	//check milestones entries exists for given project id
	public function check_milestones_data($id)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'milestones');
		$this->db->where('jobid_fk', $id);
		$query = $this->db->get();
		return $query->num_rows();
	}
   public function get_template_content()
	{
		$temp_id = $this->input->post('temp_id');
		$temp_content =$this->project_model->get_template_content($temp_id);
		echo json_encode($temp_content);
	}
	public function get_signature_content()
	{
		$sign_id = $this->input->post('sign_id');
		$sign_content =$this->project_model->get_signature_content($sign_id);
		 echo json_encode($sign_content);
	}

}
?>
