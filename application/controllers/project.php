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
		
 		// $inputData = real_escape_array($this->input->post());
		if(!empty($inputData['metrics_year'])) {
			$metrics_year = $inputData['metrics_year'];
			$metrics_date = date('Y-m-01', strtotime($inputData['metrics_year'].'-'.$inputData['metrics_month']));
		} else {
			$metrics_date = '';
		}
		
		// echo "asdf<pre>"; print_r($inputData); exit;
		
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
        $this->load->helper('text');
		$this->load->helper('fix_text');
		$usernme = $this->session->userdata('logged_in_user');

		if ($usernme['role_id'] == 1 || $usernme['role_id'] == 2) {
			$data['chge_access'] = 1;
		} else {
			$data['chge_access'] = $this->project_model->get_access($id, $usernme['userid']);
		}
		
		$result = $this->project_model->get_quote_data($id);
		
		// echo '<pre>';print_r($result[0]);exit;
		
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
			
			$data['practices'] 	 = $this->project_model->get_practices();
			
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
				// echo '<pre>'; print_r($timesheet); exit;
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
				//Set the Project Manager in our CRM DB.
				/* if(!empty($timesheet_users['username']) && count($timesheet_users['username'])>0) {
					 
					$proj_team_members = $user_details[$data['timesheetProjectLead']['proj_leader']]['userid'];
					if($proj_leader != $data['quote_data']['assigned_to']){
						$condn = array('lead_id' => $data['quote_data']['lead_id']);
						$updt  = array('assigned_to' => $proj_leader);
						$setPM = $this->project_model->update_row('leads', $updt, $condn);
					}
				} */
				
				//Set the Project Team Members in our CRM DB.
				$result = $this->identical_values($team_mem,$ts_team_members);
				if(!$result) {
					$wh_condn = array('jobid_fk'=>$data['quote_data']['lead_id']);
					$this->db->delete($this->cfg['dbpref'].'contract_jobs',$wh_condn);
					
					$inse['jobid_fk']  =  $data['quote_data']['lead_id'];
					foreach($ts_team_members as $ts){
						$inse['userid_fk'] =  $ts;
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
						$data['timesheet_data'][$ts['username']]['max_hours'] = $max_hours_resource->practice_max_hours;
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
						//$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
						$rateCostPerHr = $ts['cost'];
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
						
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']]['total_hours'] =get_timesheet_hours_by_user($ts['username'],$ts['yr'],$ts['month_name'],array('Leave','Hol'));
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

			/**
			get the project variance report from timesheet
			**/
			$data['timesheet_variance'] = '';
			$timesheet_db = $this->load->database('timesheet', TRUE);		
			$project_code_ts = $data['quote_data']['pjt_id'];
		
			$qry_pv = $timesheet_db->query("SELECT tt.task_id,tt.name as taskName, sum(pte.prj_task_hours) As EstimatedHours, pe.prj_est_id,pe.proj_est_name,pte.prj_est_id,pte.proj_id,pte.task_id,(select sum(tim.duration)/60 from ".$timesheet_db->dbprefix('times')." As tim where tim.proj_id = tt.proj_id and tim.task_id = tt.task_id) As actualHours from ".$timesheet_db->dbprefix('task')." AS tt LEFT JOIN ".$timesheet_db->dbprefix('project_task_estimation')." AS pte ON pte.task_id = tt.task_id LEFT JOIN ".$timesheet_db->dbprefix('project_estimation')." AS pe ON pe.prj_est_id = pte.prj_est_id  left join ".$timesheet_db->dbprefix('project')." as prj on prj.proj_id = tt.proj_id WHERE prj.project_code='".$project_code_ts."' group by tt.task_id");
			//echo $timesheet_db->last_query();exit;
			if($qry_pv->num_rows()>0){
				$res_pv = $qry_pv->result();
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
					$res = $qry->result();
					$pjtIds = array();					
					$pjtNames = array();					
					$pnames_arr = array();					
					foreach($res as $r){
						$pjtIds[] = $r->id;
					}
					$pjtIds = array_unique($pjtIds);
					//echo '<pre>';print_r($pjtIds);exit;
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
			
			$gantt_chart_data=$this->check_gantt_chart_data($id);
			
			$milestones_data=$this->check_milestones_data($id);
			
			if($gantt_chart_data==0) $data['show_gantt_chart']=0;
			else $data['show_gantt_chart']=1;
			
			if($milestones_data==0) $data['show_milestones']=0;
			else $data['show_milestones']=1;
			
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
		/* $ins_val = array();
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
			
			//map uploaded file, if exists
			if(!empty($this->input->post('file_id')) && count($this->input->post('file_id'))>0) {
				$oc_file 					= array();
				$oc_file['other_cost_id'] 	= $insert_cost;
				foreach($this->input->post('file_id') as $file_id) {
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
		} */
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
		/* $updt_val = array();
		$updt_val['description'] 		= $this->input->post('description');
		$updt_val['cost_incurred_date'] = ($this->input->post('cost_incurred_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('cost_incurred_date'))) : '';
		$updt_val['currency_type'] 		= $this->input->post('currency_type');
		$updt_val['value'] 				= $this->input->post('value');
		$updt_val['modified_by'] 		= $this->userdata['userid'];
		$updt_val['modified_on'] 		= date('Y-m-d H:i:s');
		$condn = array('id'=>$this->input->post('cost_id'), 'project_id'=>$this->input->post('project_id'));
		$this->project_model->delete_row('other_cost_attach_file', array("other_cost_id"=>$this->input->post('cost_id')));
		$update_cost = $this->project_model->update_row('project_other_cost', $updt_val, $condn);
		if(!empty($this->input->post('file_id'))){
			$attach_updt['other_cost_id'] 	= $this->input->post('cost_id');
			foreach($this->input->post('file_id') as $ocfile) {
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
		exit; */
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

	
}
?>