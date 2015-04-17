<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() {
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
		// $data['records']     = $this->project_model->get_projects_results($pjtstage = '', $pm_acc = '', $cust = '', $service='', $keyword = '', $datefilter = '', $fromdate = '', $todate = '');
		// $data['project_record'] = $this->getProjectsDataByDefaultCurrency($data['records']);
		// echo "<pre>"; print_r($data['project_record']); exit;
		// unset($data['records']);
		// $data['records']     = array();
		$this->load->view('projects/projects_view', $data);
    }
	
	/*
	 *Advanced Search For Projects
	 */
	public function advance_filter_search_pjt()
	{
 		$inputData = real_escape_array($this->input->post());
		
		if(!empty($inputData)) {
			$pjtstage 	= $inputData['pjtstage'];
			// $pm_acc   	= $inputData['pm_acc'];
			$cust     	= $inputData['cust'];
			$service 	= $inputData['service'];
			$practice 	= $inputData['practice'];
			$keyword  	= $inputData['keyword'];
			$datefilter = $inputData['datefilter'];
			$from_date	= $inputData['from_date'];
			$to_date  	= $inputData['to_date'];
			$divisions  = $inputData['divisions'];
		} else {
			$pjtstage 	= '';
			$cust     	= '';
			$service 	= '';
			$practice 	= '';
			$keyword  	= '';
			$datefilter = '';
			$from_date	= '';
			$to_date  	= '';
			$divisions  	= '';
		}
		
	    /*
		 *$pjtstage - lead_stage. $pm_acc - Project Manager Id. $cust - Customers Id.(custid_fk)
		 */
		if ($keyword == 'false' || $keyword == 'undefined') {
			$keyword = 'null';
		}
		$getProjects	   = $this->project_model->get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,false,$divisions);
		
		//echo '<pre>'; print_r($getProjects);

		$data['pjts_data'] = $this->getProjectsDataByDefaultCurrency($getProjects);
		$this->load->view('projects/projects_view_inprogress', $data);
	}
	
	/*
	 *Advanced Search For Projects
	 */
	public function advanceFilterMetrics()
	{
 		$inputData = real_escape_array($this->input->post());
		
		if(!empty($inputData)) {
			$pjtstage 	  = $inputData['pjtstage'];
			$cust     	  = $inputData['customer1'];
			$service 	  = $inputData['services'];
			$practice 	  = $inputData['practices'];
			$divisions 	  = $inputData['divisions'];
			$keyword  	  = $inputData['keyword'];
			$datefilter	  = $inputData['datefilter'];
			$from_date	  = $inputData['from_date'];
			$to_date  	  = $inputData['to_date'];
			$metrics_month = $inputData['metrics_month'];
			$metrics_year = $inputData['metrics_year'];
			$metrics_date = date('Y-m-01', strtotime($inputData['metrics_year'].'-'.$inputData['metrics_month']));
		} else {
			$pjtstage 	  = '';
			$cust     	  = '';
			$service 	  = '';
			$practice  	  = '';
			$divisions    = '';
			$keyword  	  = '';
			$datefilter   = '';
			$from_date	  = '';
			$to_date  	  = '';
			$metrics_date = '';
		}
		
		if ($keyword == 'false' || $keyword == 'undefined') {
			$keyword = 'null';
		}
		$getProjects	   = $this->project_model->get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,$billing_type=2, $divisions);
		$data['pjts_data'] = $this->getProjectsDataByDefaultCurrency($getProjects,$project_type=3,$metrics_date);
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
		
		//echo '<pre>'; print_r($usernme);

		if ($usernme['role_id'] == 1 || $usernme['role_id'] == 2) {
			$data['chge_access'] = 1;
		} else {
			$data['chge_access'] = $this->project_model->get_access($id, $usernme['userid']);
		}
		
		$result = $this->project_model->get_quote_data($id);
		//echo '<pre>';print_r($result[0]);exit;
		// $arrLeadInfo = $this->request_model->get_lead_info($id);
		
		if(!empty($result)) {
			
			$data['quote_data']		= $result[0];
			$data['view_quotation'] = true;
			
			// Get User Role
			// $data['user_roles']		= $usernme['role_id']; 
			// $data['login_userid']		= $usernme['userid']; 
			// $data['project_belong_to']		= $arrLeadInfo['belong_to']; 
			// $data['project_assigned_to']		= $arrLeadInfo['assigned_to']; 
			// $data['project_lead_assign']		= $arrLeadInfo['lead_assign']; 
			// $temp_cont = $this->project_model->get_contract_jobs($result[0]['lead_id']);

			$data['timesheetProjectType']   = array();
			$data['timesheetProjectLead']   = array();
			$data['timesheetAssignedUsers'] = array();

			/* foreach ($temp_cont as $tc) {
				$data['assigned_contractors'][] = $tc['userid_fk'];
			} */
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
				// $data['payment_data'] = $this->project_model->get_payment_terms($data['quote_data']['lead_id']);
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
			// $data['job_files_html'] = $this->project_model->get_job_files($f_dir, $fcpath, $data['quote_data']);
			$get_parent_folder_id = $this->request_model->getParentFfolderId($id,$parent=0);
			
			// $project_members = array();
			// $project_leaders = array();
		
			//$arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR);
			
			//echo '<pre>'; print_r($get_parent_folder_id);exit;
			//echo '<pre>'; print_r($project_leaders);
			//echo '<pre>'; print_r($project_members);exit;
			
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
				
				
				// $project_members = $this->request_model->get_project_members($id); // This array to get a project normal members(Developers) details.
				// $project_leaders = $this->request_model->get_project_leads($id); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
				// $arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
				// $arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
				// $arrLeadInfo = $this->request_model->get_lead_info($id); // This function to get a current lead informations.		
				
				
				
					/* if(isset($arrProjectMembers) && !empty($arrProjectMembers)) { 
	
						foreach($arrProjectMembers as $members){
							
							$arrLeadExistFolderAccess= $this->request_model->check_lead_file_access_by_id($id, 'folder_id', $data['parent_ffolder_id'], $members['userid']);						
								if(empty($arrLeadExistFolderAccess)) {	
								
									$read_access = 0;
									$write_access = 0;
									$delete_access = 0;									
									// Check this user is "Lead Owner", "Lead Assigned to", ""Project Manager"
									if($arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || $arrLeadInfo['lead_assign'] == $members['userid']) {
									$read_access = 1;
									$write_access = 1;
									$delete_access = 1;								
									}
								$folder_permissions_contents  = array('userid'=>$members['userid'],'lead_id'=>$id,'folder_id'=>$data['parent_ffolder_id'],'lead_file_access_read'=>$read_access,'lead_file_access_delete'=>$delete_access,'lead_file_access_write'=>$write_access,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>$members['userid']);
								$insert_folder_permissions   = $this->request_model->insert_new_row('lead_file_access', $folder_permissions_contents); //Mani
								
							}							
						}
					} */
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
				$data['timesheetProjectType']   = $this->project_model->get_timesheet_project_type($data['quote_data']['pjt_id']);
				$data['timesheetProjectLead']   = $this->project_model->get_timesheet_project_lead($data['quote_data']['pjt_id']);
				$timesheet_users = $this->project_model->get_timesheet_users($data['quote_data']['pjt_id']);
				if(count($timesheet_users['name'])>0) {
					$data['timesheetAssignedUsers'] = $timesheet_users['name'];
				}

				/* //Set the Project Manager in our CRM DB.
				if(!empty($data['timesheetProjectLead']) && count($data['timesheetProjectLead'])>0) {
					$proj_leader = $user_details[$data['timesheetProjectLead']['proj_leader']]['userid'];
					if($proj_leader != $data['quote_data']['assigned_to']){
						$condn = array('lead_id' => $data['quote_data']['lead_id']);
						$updt  = array('assigned_to' => $proj_leader);
						$setPM = $this->project_model->update_row('leads', $updt, $condn);
					}
				} */
				
				$contract_users = $this->project_model->get_contract_users($id);
				if(!empty($contract_users) && count($contract_users)>0) {
					foreach($contract_users as $teamMem) {
						$team_mem[] = $teamMem['userid_fk'];
					}
				}
				if(!empty($timesheet_users['username']) && count($timesheet_users['username'])>0) {
					foreach($timesheet_users['username'] as $u_name) {
						if(!empty($user_details[$u_name]['userid'])) {
							$ts_team_members[] = $user_details[$u_name]['userid'];
						}
					}
				}
				
			/* 	//Set the Project Manager in our CRM DB.
				if(!empty($timesheet_users['username']) && count($timesheet_users['username'])>0) {
					$proj_team_members = $user_details[$data['timesheetProjectLead']['proj_leader']]['userid'];
					if($proj_leader != $data['quote_data']['assigned_to']){
						$condn = array('lead_id' => $data['quote_data']['lead_id']);
						$updt  = array('assigned_to' => $proj_leader);
						$setPM = $this->project_model->update_row('leads', $updt, $condn);
					}
				} */
				
				//Set the Project Team Members in our CRM DB.
				/*$result = $this->identical_values($team_mem,$ts_team_members);
				if(!$result) {
					$wh_condn = array('jobid_fk'=>$data['quote_data']['lead_id']);
					$this->db->delete($this->cfg['dbpref'].'contract_jobs',$wh_condn);
					
					$inse['jobid_fk']  =  $data['quote_data']['lead_id'];
					foreach($ts_team_members as $ts){
						$inse['userid_fk'] =  $ts;
						$this->db->insert($this->cfg['dbpref'].'contract_jobs',$inse);
					}
				} */
			}
			
			//For list the particular lead owner, project manager & lead assigned_to in the welcome_view_project page.
			$data['list_users'] = $this->project_model->get_list_users($id);
			
			//For list the particular project team member in the welcome_view_project page.
			$data['contract_users'] = $this->project_model->get_contract_users($id);
			$data['stake_holders'] = $this->project_model->get_stake_holders($id);
			//echo '<pre>';print_r($project_members); 
			//echo '<pre>';print_r($data['contract_users']);exit;
			$rates = $this->get_currency_rates();

			$data['timesheet_data'] = array();
			/* if(count($timesheet)>0) {
				foreach($timesheet as $ts) {
					$costdata = array();
					if(isset($ts['cost'])) {
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
						$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$data['quote_data']['expect_worth_id']]);
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
					} else {
						$costdata = $this->project_model->get_latest_cost($ts['username']);
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $costdata['cost'];
						$rateCostPerHr = $this->conver_currency($costdata['cost'], $rates[1][$data['quote_data']['expect_worth_id']]);
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
				$res = $this->calcActualProjectCost($data['timesheet_data']);
				if($res['total_cost']>0) {
					$data['project_costs'] = $res['total_cost'];
				}
				if($res['total_hours']>0) {
					$data['actual_hour_data'] = $res['total_hours'];
				}
			}
		
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
			
			$data['all_users'] = $this->project_model->get_users();
			
            $this->load->view('projects/welcome_view_project', $data);
        }
        else
        {
			$this->session->set_flashdata('login_errors', array("Project does not exist."));
			redirect('project');
            // echo "Project does not exist or if you are an account manager you may not be authorised to view this";
        }
    }
	
	/*
	*Check the two arrays
	*/
	function identical_values( $arrayA , $arrayB ) {
		sort( $arrayA );
		sort( $arrayB );
		return $arrayA == $arrayB;
	}
	
	/*
	* calculate the project actual value based on actual hour
	*/
	function calcActualProjectCost($timesheet_data)
	{
		$total_billable_hrs		= 0;
		$total_non_billable_hrs = 0;
		$total_internal_hrs		= 0;
		$data['total_cost']		= 0;
		
		// echo "<pre>"; print_r($timesheet_data); exit;
		
		foreach($timesheet_data as $key1=>$value1) {
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
								$billable_hrs		 = $value4['duration'];
								$total_billable_hrs += $billable_hrs;
							break;
							case 'Non-Billable':
								$rs_name				 = $value4['rs_name'];
								$rate				 	 = $value4['rateperhr'];
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
					$data['total_cost'] += $rate*($billable_hrs+$internal_hrs+$non_billable_hrs);
				}
			}
		}
		$data['total_billable_hrs']		= $total_billable_hrs;
		$data['total_internal_hrs']	    = $total_internal_hrs;
		$data['total_non_billable_hrs'] = $total_non_billable_hrs;
		$data['total_hours']			= $total_billable_hrs+$total_internal_hrs+$total_non_billable_hrs;
		return $data;
	}
	
	/*
	*Get the logs
	*/
	function getLogs($id){
	
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
						 <p class="desc log'.$stick_class.'">'.$log_content.'</p></td></tr>';
				$data['log_html'] .= $table;
				unset($table, $user_data, $user, $log_content);
			}
		}
		$data['log_html'] .= '</tbody></table>';
		echo $data['log_html'];
	}
	
	/*
	*@method set_practices
	*
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
				$timesheet_db->update($timesheet_db->dbprefix('project'),array("proj_leader" => $userrow->username),array("project_code" => $project_code));
				$timesheet_db->close();
			}else{
				$data['error'] = 'Project Manager mismatch in Timesheet.';
			}
			
			if($updt_id==0)
			$data['error'] = 'Project Manager Not Updated.';
		}
		echo json_encode($data);
	}
	
	public function set_project_members(){
		$updt = real_escape_array($this->input->post());
		$data['error'] = FALSE;
		$ins = array();
		
		$project_team_members = $this->input->post('project_team_members');
		$project_code = $this->input->post('project_code');
		$lead_id = $this->input->post('lead_id');
		
		if ($project_team_members == "")
		{
			$data['error'] = 'Project Members must not be Null value!';
		}
		else
		{
			//update in crm			
			if($project_team_members)
			{
				$ins['jobid_fk'] = $lead_id;
				$ptms = explode(",",$project_team_members);
				if(count($ptms)>0){
					// query to get the username from the selected users in crm users table.
					$this->db->select("username");
					$this->db->where_in("userid",$ptms);
					$res_cm_users = $this->db->get($this->cfg['dbpref']."users");
					$rs_cm_users = $res_cm_users->result();
					
					// delete the existing assigned users from the contract jobs table before inserting the new things.
					$this->db->delete($this->cfg['dbpref']."contract_jobs",array("jobid_fk" => $lead_id));
					
					//inserting the assigned users in the contract jobs table.
					foreach($ptms as $pmembers){
						$ins['userid_fk'] = $pmembers;
						$insert = $this->project_model->insert_row('contract_jobs', $ins);
					}
				}
			}
			
			//update in timesheet starts
			// get the project id from project table of timesheet 
			$timesheet_db = $this->load->database('timesheet', TRUE); 
			$qry = $timesheet_db->get_where($timesheet_db->dbprefix('project'),array("project_code" => $project_code));
			if($qry->num_rows())
			{
				$res_t = $qry->row();
				$timesheet_proj_id =  $res_t->proj_id;
				$time_ins = array();
				
				//delete the existing assigned users in the timesheet assignment table, before inserting.
				$timesheet_db->delete($timesheet_db->dbprefix("assignments"),array("proj_id" => $timesheet_proj_id));
				$time_ins['proj_id'] = $timesheet_proj_id;
				if(count($rs_cm_users) > 0){
					foreach($rs_cm_users as $muser){
						$time_ins['username'] = $muser->username;
						$time_ins['rate_id'] = 1;
						$timesheet_db->insert($timesheet_db->dbprefix("assignments"), $time_ins);
					}
				}
			}else{
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
		$data['error'] = FALSE;
		$stake_members = $this->input->post("stake_members");
		$lead_id = $this->input->post("lead_id");
		
		if ($stake_members == "")
		{
			$data['error'] = 'State Holders must not be Null value!';
		}
		
		$stms = explode(",",$stake_members);
		$ins = array();
		if(count($stms) > 0){
			$this->db->delete($this->cfg['dbpref']."stake_holders",array("lead_id" => $lead_id));
			$ins['lead_id'] = $lead_id;
			foreach($stms as $sm){
				$ins['user_id'] = $sm;
				$this->project_model->insert_row("stake_holders", $ins);				 
			}
		}
		
		echo json_encode($data);	
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
		
		/* if($updt['pjt_stat'] == 2) {
			$checkStat = $this->project_model->get_lead_det($updt['lead_id']);
			if(isset($checkStat['actual_date_due'])) {
				$data['error'] = FALSE;
			} else {
				$data['error'] = 'Actual End Date must be filled';
			}
		} */

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
						$invoice_stat = "<a title='Generate Invoice' href='javascript:void(0)' onclick='generate_inv(".$exp['expectid']."); return false;'><img src='assets/img/generate_invoice.png' alt='Generate Invoice' ></a>";
					} else if ($exp['invoice_status'] == 1) {
						$invoice_stat = "<a title='Generate Invoice' href='javascript:void(0)' class='readonly-status img-opacity'><img src='assets/img/generate_invoice.png' alt='Generate Invoice'></a>";
					}
				} else {
					$invoice_stat = "<a title='Generate Invoice' href='javascript:void(0)' class='readonly-status img-opacity'><img src='assets/img/generate_invoice.png' alt='Generate Invoice'></a>";
				}
				$att = "";
				if($attachments>0){
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
					$output .= "<td align='left'>
						<a title='Edit' onclick='paymentProfileEdit(".$exp['expectid']."); return false;' ><img src='assets/img/edit.png' alt='edit'> </a>
						<a title='Delete' onclick='paymentProfileDelete(".$exp['expectid']."); return false;'><img src='assets/img/trash.png' alt='delete' ></a>
						".$invoice_stat."
					</td>";
				} else {
					$output .= "<td align='left'>
						<a title='Edit' class='readonly-status img-opacity' href='javascript:void(0)'><img src='assets/img/edit.png' alt='edit'></a>
						<a title='Delete' class='readonly-status img-opacity' href='javascript:void(0)'><img src='assets/img/trash.png' alt='delete'></a>
						".$invoice_stat."
					</td>";
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
		
		$data_log['log_content'] = str_replace('\n', "<br />", $data_log['log_content']);
		$ins['log_content'] = str_replace('\n', "", $data_log['log_content']);
		
		$break = 120;
		$data_log['log_content'] =  implode(PHP_EOL, str_split($data_log['log_content'], $break));
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

					$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'first_name'=>$client[0]['first_name'], 'last_name'=>$client[0]['last_name'], 'log_content'=>$data_log['log_content'], 'received_by'=>$received_by, 'signature'=>$this->userdata['signature']);

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

					$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'first_name'=>$client[0]['first_name'], 'last_name'=>$client[0]['last_name'], 'log_content'=>$data_log['log_content'], 'received_by'=>$received_by, 'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $senders;
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $user_data[0]['email'];
					$param['from_email_name'] = $user_data[0]['first_name'];
					$param['template_name'] = "Project Notification Message";
					$param['subject'] = $log_subject;

					if($this->email_template_model->sent_email($param))
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
					$mgmt_mail = implode(',',$mangement_email);
					$admin_mail=implode(',',$arrSetEmails);
					
					//email sent by email template
					$param = array();
					
					$param['email_data'] = array('user_name'=>$user_name, 'print_fancydate'=>$print_fancydate, 'log_content'=>$ins['log_content'], 'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $mgmt_mail.','.$lead_assign_mail[0]['email'].','.$lead_owner[0]['email'];
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $this->userdata['email'];
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "Lead - Delete Notification Message";
					$param['subject'] = "Project Delete Notification";

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
		return round($meterStatus, 2);
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
	public function getProjectsDataByDefaultCurrency($records,$project_billing_type=false,$metrics_date=false)
	{
		$rates = $this->get_currency_rates();
		
		$data['project_record'] = array();
		$i = 0;
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
				
				if(!empty($rec['pjt_id']))
				$timesheet = $this->project_model->get_timesheet_data($rec['pjt_id'], $rec['lead_id'], $bill_type, $metrics_date, $groupby_type=1);
				
				/* if(count($timesheet)>0) {
					foreach($timesheet as $ts) {
						$costdata = array();
						if(isset($ts['cost'])) {
							$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
							$rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
							$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
						} else {
							$costdata = $this->project_model->get_latest_cost($ts['username']);
							$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $costdata['cost'];
							$rateCostPerHr = $this->conver_currency($costdata['cost'], $rates[1][$this->default_cur_id]);
							$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
						}
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['Duration'];
						$data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['first_name'] . ' ' .$ts['last_name'];
					}
				} */
				
				// if(count($timesheet)>0) {
					// foreach($timesheet as $ts) {
						// if(isset($ts['cost'])) {
							// $data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
							// $rateCostPerHr = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
							// $data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
							// $data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
							// $data['timesheet_data'][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
						// }
					// }
				// }
				$total_billable_hrs = 0;
				$total_internal_hrs = 0;
				$total_non_billable_hrs = 0;
				$total_cost  = 0;
				$total_hours = 0;
				
				if(count($timesheet)>0) {
					foreach($timesheet as $ts) {
						$total_cost  += $ts['duration_cost'];
						$total_hours += $ts['duration_hours'];
						switch($ts['resoursetype']) {
							case 'Billable':
								$total_billable_hrs = $ts['duration_hours'];
							break;
							case 'Non-Billable':
								$total_non_billable_hrs = $ts['duration_hours'];
							break;
							case 'Internal':
								$total_internal_hrs = $ts['duration_hours'];
							break;
						}
					}
				}
				
				$total_cost = $this->conver_currency($total_cost, $rates[1][$this->default_cur_id]);

				// if(!empty($data['timesheet_data'])) {
				
					// $res = $this->calcActualProjectCost($data['timesheet_data']);
					// echo "<pre>"; print_r($res); exit;
					// if($res['total_cost']>0) {
						// $total_cost = $res['total_cost'];
					// }
					// if($res['total_hours']>0) {
						// $total_hours = $res['total_hours'];
					// }
					// if($res['total_billable_hrs']>0) {
						// $total_billable_hrs = $res['total_billable_hrs'];
					// }
					// if($res['total_internal_hrs']>0) {
						// $total_internal_hrs = $res['total_internal_hrs'];
					// }
					// if($res['total_non_billable_hrs']>0) {
						// $total_non_billable_hrs = $res['total_non_billable_hrs'];
					// }
				// }

				/* if(!empty($rec['pjt_id'])) {
					$timesheet_project_type = $this->project_model->get_timesheet_project_type($rec['pjt_id']);
					if(!empty($timesheet_project_type))
					$project_type = $timesheet_project_type['project_type_name'];
				} */
				
				//Build the Array
				$data['project_record'][$i]['lead_id'] 			= $rec['lead_id'];
				$data['project_record'][$i]['invoice_no'] 		= $rec['invoice_no'];
				$data['project_record'][$i]['division'] 		= $rec['division'];
				$data['project_record'][$i]['lead_title']		= $rec['lead_title'];
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
				$data['project_record'][$i]['clname'] 			= $rec['clname'];
				$data['project_record'][$i]['company'] 			= $rec['company'];
				$data['project_record'][$i]['fnm'] 				= $rec['fnm'];
				$data['project_record'][$i]['lnm'] 				= $rec['lnm'];
				$data['project_record'][$i]['billing_type'] 	= $rec['billing_type'];
				$data['project_record'][$i]['bill_hr'] 			= $total_billable_hrs;
				$data['project_record'][$i]['int_hr'] 			= $total_internal_hrs;
				$data['project_record'][$i]['nbil_hr'] 			= $total_non_billable_hrs;
				$data['project_record'][$i]['total_hours'] 		= $total_hours;
				$data['project_record'][$i]['total_cost'] 		= number_format($total_cost, 2, '.', '');
				$i++;
			}
		endif;
		return $data['project_record'];
	}
	
	/* Export to Excel */
	public function excelExport() {
		$pjtstage = $this->input->post('stages');
		// $pm_acc = $this->input->post('pm');
		$cust = $this->input->post('customers');
		$service = $this->input->post('services');
		$practice = $this->input->post('practices');
		$divisions = $this->input->post('divisions');
		$datefilter = $this->input->post('datefilter');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$export_type = $this->input->post('export_type');
		$keyword = null;
		
		if((!empty($pjtstage)) && $pjtstage!='null')
		$pjtstage = explode(",",$pjtstage);
		else 
		$pjtstage = '';
		// if((!empty($pm_acc)) && $pm_acc!='null')
		// $pm_acc = explode(",",$pm_acc);
		// else
		// $pm_acc = '';
		if((!empty($cust)) && $cust!='null')
		$cust = explode(",",$cust);
		else
		$cust = '';
		if((!empty($service)) && $service!='null')
		$service = explode(",",$service);
		else
		$service = '';
		if((!empty($practice)) && $practice!='null')
		$practice = explode(",",$practice);
		else
		$practice = '';
		if((!empty($divisions)) && $divisions!='null')
		$divisions = explode(",",$divisions);
		else
		$divisions = '';
		
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
		$pjts_data	    = $this->getProjectsDataByDefaultCurrency($getProjectData,$project_type,$metrics_date);
		
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
			$this->excel->getActiveSheet()->setCellValue('M1', 'P&L');
			$this->excel->getActiveSheet()->setCellValue('N1', 'P&L %');

			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:N1')->getFont()->setSize(10);
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);

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
				$plPercent = ($rec['actual_worth_amt']-$rec['total_cost'])/$rec['actual_worth_amt'];
				$percent = ($plPercent == FALSE)?'-':round($plPercent)*100;
				
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
				$this->excel->getActiveSheet()->setCellValue('M'.$i, round($rec['actual_worth_amt']-$rec['total_cost']));
				$this->excel->getActiveSheet()->setCellValue('N'.$i, $percent);
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
			$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
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
	
	public function generateInvoice($eid, $pjtid) {

		$wh_condn		 = array('expectid' => $eid,'jobid_fk'=>$pjtid);
		$updt			 = array('invoice_status'=>1,'invoice_generate_notify_date'=>date('Y-m-d H:i:s'));
		
		$output['error'] = FALSE;
		
		$updt_payment_ms = $this->project_model->update_row('expected_payments', $updt, $wh_condn);
		// $updt_payment_ms = 1;
		
		if($updt_payment_ms) {
			$project_details = $this->project_model->get_quote_data($pjtid);
			$payment_details = $this->project_model->get_payment_term_det($eid, $pjtid);
			$attached_files  = $this->project_model->get_attached_files($eid);
			
			$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
			$dis['date_created'] = date('Y-m-d H:i:s');
			$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

			$from		  	 = $this->userdata['email'];
			$arrayEmails   	 = $this->config->item('crm');
			$to				 = implode(',',$arrayEmails['account_emails']);
			$cc_email		 = implode(',',$arrayEmails['account_emails_cc']);
			$subject		 = 'Generate Invoice Notification';
			$customer_name   = $project_details[0]['company'].' - '.$project_details[0]['first_name'].' '.$project_details[0]['last_name'];
			$project_name	 = word_limiter($project_details[0]['lead_title'], 4);
			$project_id	 	 = $project_details[0]['invoice_no'];
			$project_code	 = $project_details[0]['pjt_id'];
			$milestone_name  = $payment_details['project_milestone_name'];
			$month_year  	 = date('F Y', strtotime($payment_details['month_year']));
			$milestone_value = $payment_details['expect_worth_name'] . ' - ' . $payment_details['amount'];
			$payment_remark  = isset($payment_details['payment_remark']) ? $payment_details['payment_remark'] : '-';
			//email sent by email template
			$param = array();
			
			$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'month_year'=>$month_year,'signature'=>$this->userdata['signature'],'customer_name'=>$customer_name,'project_name'=>$project_name,'project_id'=>$project_id,'project_code'=>$project_code,'milestone_name'=>$milestone_name,'milestone_value'=>$milestone_value,'payment_remark'=>$payment_remark);

			$param['to_mail'] 		  = $to;
			$param['cc_mail'] 		  = $this->userdata['email'].','.$cc_email;
			// $param['bcc_mail'] 		  = $bcc_email;
			$param['from_email']	  = 'webmaster@enoahisolultion.com';
			$param['from_email_name'] = 'Webmaster';
			$param['template_name']	  = "Generate Invoice Notification";
			$param['subject'] 		  = $subject;
			$param['attach'] 		  = $attached_files;
			$param['job_id'] 		  = $pjtid;

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

				
		
		// echo "<pre>"; print_r($_FILES); exit;
		
		/*$filefolder_id - first we check whether filefolder_id is a Parent or Child*/
		
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
					
					
					/* #################  Permission add new file owner start here  ################## */
				/* if($user_data['role_id'] != 1) {
				$permissions_contents  = array('userid'=>$user_data['userid'],'lead_id'=>$lead_id,'file_id'=>$insert_file,'lead_file_access_read'=>1,'lead_file_access_delete'=>1,'lead_file_access_write'=>1,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>$user_data['userid']);
				$insert_permissions   = $this->request_model->insert_new_row('lead_file_access', $permissions_contents); //Mani
				} */
				/* #################  Permission add new file owner end here  ################## */
				
				
				/* #################  Assing permission to all users by lead id start here  ################## */
					/* if(isset($arrProjectMembers) && !empty($arrProjectMembers)) { 
		
							foreach($arrProjectMembers as $members){
								if($user_data['userid'] != $members['userid']) {
								
								$arrLeadExistFolderAccess= $this->request_model->check_lead_file_access_by_id($af_data['aflead_id'], 'folder_id', $res_insert, $members['userid']);						
								if(empty($arrLeadExistFolderAccess)) {	
								
									$read_access = 0;
									$write_access = 0;
									$delete_access = 0;	 	
					
									// Check this user is "Lead Owner", "Lead Assigned to", ""Project Manager"
									if($arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || $arrLeadInfo['lead_assign'] == $members['userid']) {
									$read_access = 1;
									$write_access = 1;
									$delete_access = 1;								
									}
	
									$other_permissions_contents  = array('userid'=>$members['userid'],'lead_id'=>$lead_id,'file_id'=>$insert_file,'lead_file_access_read'=>$read_access,'lead_file_access_delete'=>$delete_access,'lead_file_access_write'=>$write_access,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>$user_data['userid']);
									$insert_other_users_permissions   = $this->request_model->insert_new_row('lead_file_access', $other_permissions_contents); //Mani
										
								}
							}
						}
					} */
				/* #################  Assing permission to all users by lead id end here  ################## */
				
				
					
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

			$customer = $this->customer_model->get_customer($data['quote_data']['custid_fk']);				
			$data['customer_data'] = $customer[0];

			if($customer[0]['sales_contact_userid_fk']!='0') {
				$data['sales_person_detail'] = $this->customer_model->get_records_by_id('users', array('userid'=>$customer[0]['sales_contact_userid_fk']));
			}
			
		 	$data['regions']    = $this->regionsettings_model->region_list();
		 	$data['project_id'] = $inputData['job_id'];
			
            $data['view_quotation'] = true;
			$data['user_accounts'] = $this->welcome_model->get_users();
			
			$data['user_roles']		     = $usid['role_id']; 
			$data['login_userid']		 = $usid['userid']; 
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
			$data['practices'] = $this->project_model->get_practices();
			
			$this->load->view('leads/leads_confirmations_view', $data);
        }
	
	}
	
	/*
	*@method set_departments
	*@Use update department to leads
	*@Author eNoah - Mani.S
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
					
					
					/* #################  Permission add new file owner start here  ################## */
				/* if($this->userdata['role_id'] != 1) {
					$permissions_contents  = array('userid'=>$this->userdata['userid'],'lead_id'=>$lead_id,'file_id'=>$insert_file,'lead_file_access_read'=>1,'lead_file_access_delete'=>1,'lead_file_access_write'=>1,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>$this->userdata['userid']);
					
					$insert_permissions   = $this->request_model->insert_new_row('lead_file_access', $permissions_contents); //Mani
				} */
				/* #################  Permission add new file owner end here  ################## */
				
				/* #################  Assing permission to all users by lead id start here  ################## */
					/* if(isset($arrProjectMembers) && !empty($arrProjectMembers)) {
						foreach($arrProjectMembers as $members){
							if(!empty($members)) {
								if($this->userdata['userid'] != $members['userid']) {
									$arrLeadExistFolderAccess= $this->request_model->check_lead_file_access_by_id($af_data['aflead_id'], 'folder_id', $res_insert, $members['userid']);
									if(empty($arrLeadExistFolderAccess)) {
										// $read_access = 0;
										// $write_access = 0;
										// $delete_access = 0;
											$read_access = 1;
											$write_access = 1;
											$delete_access = 1;										
										// Check this user is "Lead Owner", "Lead Assigned to", ""Project Manager"
										if($arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || $arrLeadInfo['lead_assign'] == $members['userid']) {
											$read_access = 1;
											$write_access = 1;
											$delete_access = 1;
										}
										$other_permissions_contents  = array('userid'=>$members['userid'],'lead_id'=>$lead_id,'file_id'=>$insert_file,'lead_file_access_read'=>$read_access,'lead_file_access_delete'=>$delete_access,'lead_file_access_write'=>$write_access,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>$this->userdata['userid']);
										$insert_other_users_permissions   = $this->request_model->insert_new_row('lead_file_access', $other_permissions_contents); //Mani
									}
								}
							}
						}
					} */
				/* #################  Assing permission to all users by lead id end here  ################## */
				
				
					
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
	
	
}
?>