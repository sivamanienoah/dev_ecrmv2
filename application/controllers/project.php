<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() {
		parent::__construct();
		
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('project_model');
		$this->load->model('customer_model');
		$this->load->model('regionsettings_model');
		$this->load->model('email_template_model');
		$this->load->helper('text');
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stg_name = getLeadStageName();
		$this->stages = @implode('","', $this->stg);
	}
	
	/*
	 * List all the Leads based on levels
	 * @access public
	 */
	public function index() 
	{
		$data['page_heading'] = "Projects - Lists";		
		$data['pm_accounts'] = array();
		$pjt_managers = $this->project_model->get_user_byrole(3);
		if(!empty($pjt_managers))
		$data['pm_accounts'] = $pjt_managers;
		$data['customers'] = $this->project_model->get_customers();
		$data['services'] = $this->project_model->get_services();
		$data['records'] = $this->project_model->get_projects_results($pjtstage = '', $pm_acc = '', $cust = '', $service='', $keyword = '');
		$this->load->view('projects/projects_view', $data);
    }
	
	/*
	 *Advanced Search For Projects
	 */
	public function advance_filter_search_pjt()
	{
 		$inputData=real_escape_array($this->input->post());
 		
		$pjtstage=$inputData['pjtstage'];
		$pm_acc=$inputData['pm_acc'];
		$cust=$inputData['cust'];
		$service=$inputData['service'];
		$keyword=$inputData['keyword'];
	    /*
		 *$pjtstage - lead_stage. $pm_acc - Project Manager Id. $cust - Customers Id.(custid_fk)
		 */
		if ($keyword == 'false' || $keyword == 'undefined') {
			$keyword = 'null';
		}
		
		$getProjects = $this->project_model->get_projects_results($pjtstage, $pm_acc, $cust, $service, $keyword);	
		
		$data['pjts_data'] = $getProjects;
		$data['records'] = $getProjects;
		
		$this->load->view('projects/projects_view_inprogress', $data);
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
		
		if(!empty($result)) {
			$data['quote_data'] = $result[0];
			$data['view_quotation'] = true;
			$temp_cont = $this->project_model->get_contract_jobs($result[0]['lead_id']);
			
			$data['assigned_contractors'] = array();
			foreach ($temp_cont as $tc) {
				$data['assigned_contractors'][] = $tc['userid_fk'];
			}            
			
			if (!strstr($data['quote_data']['log_view_status'], $this->userdata['userid']))
			{
				$log_view_status['log_view_status'] = $data['quote_data']['log_view_status'] . ':' . $this->userdata['userid'];
				$logViewStatus = $this->project_model->updt_log_view_status($id, $log_view_status);
			}
			$data['log_html'] = '';
            $getLogs = $this->project_model->get_logs($id);
			
			if (!empty($getLogs)) {
                $log_data = $getLogs;
                $this->load->helper('url');
                
                foreach ($log_data as $ld)
                {
					$wh_condn = array('userid'=>$ld['userid_fk']);
					$user_data = $this->project_model->get_user_data_by_id('users', $wh_condn);
					
					if (count($user_data) < 1)
					{
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
			
			$data['user_accounts'] = $this->project_model->get_users();
			
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
			$data['job_files_html'] = $this->project_model->get_job_files($f_dir, $fcpath, $data['quote_data']);

			/**
			 * Get the URLs associated with this job
			 */
			$data['job_urls_html'] = $this->project_model->get_job_urls($id);
			
			//For list the particular lead owner, project manager & lead assigned_to in the welcome_view_project page.
			$data['list_users'] = $this->project_model->get_list_users($id);
			
			//For list the particular project team member in the welcome_view_project page.
			$data['contract_users'] = $this->project_model->get_contract_users($id);
			
			if(!empty($data['quote_data']['pjt_id']))
			$timesheet = $this->project_model->get_timesheet_data($data['quote_data']['pjt_id']);
			
			$data['timesheet_data']=array();
			$total_billable	   = 0;
			$total_nonbillable = 0;
			$total_internal	   = 0;
			$total_cost        = 0;
			if(count($timesheet)>0) {
				$i=0;
				foreach($timesheet as $timesheetData) {
					$total_billable 	     += $timesheetData['Billable'];
					$total_nonbillable		 += $timesheetData['Non-Billable'];
					$total_internal  		 += $timesheetData['Internal'];
					$total_cost       		 += $timesheetData['cost'];
					$data['timesheet_data'][] = $timesheetData;
					$i++;
				}
				// To Get total of values
				$data['timesheet_data'][$i]['Resources']	= 'Total';
				$data['timesheet_data'][$i]['Billable'] 	= $total_billable;
				$data['timesheet_data'][$i]['Internal']		= $total_internal;
				$data['timesheet_data'][$i]['Non-Billable'] = $total_nonbillable;
				$data['timesheet_data'][$i]['bill_rate']	= '';
				$data['timesheet_data'][$i]['cost']			= $total_cost;
			}
			$data['actual_hour_data']						= 'NIL';
			
			if(!empty($data['quote_data']['pjt_id']))
			$actual_hour = $this->project_model->get_actual_project_hour($data['quote_data']['pjt_id']);
			
			if(count($actual_hour)>0){
				$data['actual_hour_data'] = $actual_hour['total_Hour'];
			}

			if(!empty($data['quote_data']['pjt_id']))
			$project_cost = $this->project_model->get_project_cost($data['quote_data']['pjt_id']);

			$data['project_costs'] = array();
			if(count($project_cost)>0){
				foreach($project_cost as $project_cost_data){
					$data['project_total_cost'] += $project_cost_data['cost'];
				}
				$data['project_costs'] = $project_cost;
			}
			
			//Intially Get all the Milestone data
			$data['milestone_data'] = $this->project_model->get_milestone_terms($id);
			
            $this->load->view('projects/welcome_view_project', $data);
        }
        else
        {
            echo "Project does not exist or if you are an account manager you may not be authorised to view this";
        }
        
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

		$wh_condn = array('actual_worth_amount'=>$data['pjt_val']);
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
		
		if ($updt['pjt_stat'] == "") 
		{
			$data['error'] = 'Value must not be Null value!';
		} 
		else 
		{
			switch ($updt['pjt_stat'])
			{
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

			$wh_condn = array('lead_id' => $updt['lead_id']);
			$updt = array('pjt_status' => $updt['pjt_stat']);
			$updt_pjt = $this->project_model->update_row('leads', $updt, $wh_condn);
			
			if($updt_pjt==0)
			{
				$data['error'] = 'Project Status Not Updated.';
			}
			else 
			{
				$ins['userid_fk'] = $this->userdata['userid'];
				$ins['jobid_fk'] = $this->input->post('lead_id');
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = "Status Change:\n" . urldecode($log_status);
				$insert_logs = $this->project_model->insert_row('logs', $ins);
			}
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
			$data['error'] = 'User must be selected!';
		}
		else
		{
			$wh_condn = array('lead_id' => $data_pm['lead_id']);
			$updt = array('assigned_to' => $data_pm['new_pm']);
			$updt_stat = $this->project_model->update_row('leads', $updt, $wh_condn);
		}
		echo json_encode($data);	 
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
		
		if($this->email_template_model->sent_email($param))
		{
			echo $successful .= 'This log has been emailed to:<br />'.$send_to;
		}
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
		if ($usernme['role_id'] == 1 || $usernme['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = $this->project_model->get_access($jid, $usernme['userid']);
		}
		
		$output = '';
		$total_amount_recieved = '';
		$output .= '<div class="payment-terms-mini-view2" style="float:left; margin-top: 5px;">';
		$expi = 1;
		$pt_select_box = '';
		$pt_select_box .= '<option value="0"> &nbsp; </option>';
		$output .= "<table width='100%' class='payment_tbl'>
					<tr><td colspan='3'><h6>Agreed Payment Terms</h6></td></tr>
					<tr>
					<td><img src=assets/img/payment-received.jpg height='10' width='10' > Payment Received</td>
					<td><img src=assets/img/payment-pending.jpg height='10' width='10' > Partial Payment</td>
					<td><img src=assets/img/payment-due.jpg height='10' width='10' > Payment Due</td>
					</tr>
					</table>";
		$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
		$output .= "<thead>";
		$output .= "<tr align='left'>";
		$output .= "<th class='header'>Payment Milestone</th>";
		$output .= "<th class='header'>Milestone Date</th>";
		$output .= "<th class='header'>Amount</th>";
		$output .= "<th class='header'>Status</th>";
		$output .= "<th class='header'>Action</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		if (count($expect_payment_terms>0))
		{
			foreach ($expect_payment_terms as $exp)
			{
				$expected_date = date('d-m-Y', strtotime($exp['expected_date']));
				$payment_amount = number_format($exp['amount'], 2, '.', ',');
				$total_amount_recieved += $exp['amount'];
				$payment_received = '';
				if ($exp['received'] == 0)
				{
					$payment_received = '<img src="assets/img/payment-due.jpg" alt="Due" height="10" width="10" />';
				}
				else if ($exp['received'] == 1)
				{
					$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" height="10" width="10" />';
				}
				else
				{
					$payment_received = '<img src="assets/img/payment-pending.jpg" alt="pending" height="10" width="10" />';
				}							
				$output .= "<tr>";
				$output .= "<td align='left'>".$exp['project_milestone_name']."</td>";
				$output .= "<td align='left'>".date('d-m-Y', strtotime($exp['expected_date']))."</td>";
				$output .= "<td align='left'> ".$exp['expect_worth_name'].' '.number_format($exp['amount'], 2, '.', ',')."</td>";
				$output .= "<td align='center'>".$payment_received."</td>";
				if( $chge_access == 1 ) {
				$output .= "<td align='left'><a class='edit' onclick='paymentProfileEdit(".$exp['expectid']."); return false;' >Edit</a> | ";
				$output .= "<a class='edit' onclick='paymentProfileDelete(".$exp['expectid']."); return false;' >Delete</a></td>";
				} else {
				$output .= "<td align='left'><a class='edit' >Edit</a> | ";
				$output .= "<a class='edit'>Delete</a></td>";
				}
				$output .= "</tr>";
				$pt_select_box .= '<option value="'. $exp['expectid'] .'">' . $exp['project_milestone_name'] ." \${$payment_amount} by {$expected_date}" . '</option>';
				$expi ++;
			}
		}
		$output .= "<tr>";
		$output .= "<td></td>";
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
		$payment_details = $this->project_model->get_payment_term_det($eid, $jid);
		
		$expected_date = date('d-m-Y', strtotime($payment_details['expected_date']));
		$project_milestone_name = $payment_details['project_milestone_name'];
		$project_milestone_amt  = $payment_details['amount'];
		
		echo '
		<script>
			$(function(){
				$("#sp_date_2").datepicker({dateFormat: "dd-mm-yy"});
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
		<form id="update-payment-terms">
		<table class="payment-table">
			<tr>
				<td>
				<br />
				<p>Payment Milestone *<input type="text" name="sp_date_1" id="sp_date_1" value= "'.$project_milestone_name.'" class="textfield width200px" /> </p>
				<p>Milestone date *<input type="text" name="sp_date_2" id="sp_date_2" value= "'.$expected_date.'" class="textfield width200px pick-date" readonly /> </p>
				<p>Value *<input type="text" onkeypress="return isNumberKey(event)" name="sp_date_3" id="sp_date_3" value= "'.$project_milestone_amt.'" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
				<div class="buttons">
					<button type="submit" class="positive" onclick="updateProjectPaymentTerms('.$eid.'); return false;">Update Payment Terms</button>
				</div>
				<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
				<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
				</td>
			</tr>
		</table>
		</form>';
	}
	
	/**
	 * sets the payment terms
	 * for the project
	 */
	function set_payment_terms($update = false)
	{
		$errors = array();
		$today = time();
		
		$data = real_escape_array($this->input->post());
		
		$pdate1 = $data['sp_date_1'];
		$pdate2 = strtotime($data['sp_date_2']);
		$pdate3 = $data['sp_date_3'];

		if (count($errors))
		{
			echo "<p style='color:#FF4400;'>" . join('\n', $errors) . "</p>";
		}
		else
		{
			$job_updated = FALSE;
			$expected_date = date('Y-m-d', $pdate2);
			$data3 = array('jobid_fk' => $data['sp_form_jobid'], 'percentage' => '0', 'amount' => $pdate3, 'expected_date' => $expected_date, 		'project_milestone_name' => $pdate1);
			
			$payment_details = $this->project_model->get_expect_payment_terms($data['sp_form_jobid']);

			if ($update == "") 
			{
				$ins_exp_pay = $this->project_model->insert_row('expected_payments', $data3);
				
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
					
					$updatepayment = array('amount' => $pdate3, 'expected_date' => $expected_date, 'project_milestone_name' => $pdate1);
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
				
				$payment_det = $this->project_model->get_expect_payment_terms($data['sp_form_jobid']); //after update

				$output = '';
				$total_amount_recieved = '';
				$output .= '<div class="payment-terms-mini-view2" style="float:left; margin-top: 5px;">';
				$pdi = 1;
				$pt_select_box = '';
				$pt_select_box .= '<option value="0"> &nbsp; </option>';
				$output .= "<table width='100%' class='payment_tbl'>
				<tr><td colspan='3'><h6>Agreed Payment Terms</h6></td></tr>
				<tr>
				<td><img src=assets/img/payment-received.jpg height='10' width='10' > Payment Received</td>
				<td><img src=assets/img/payment-pending.jpg height='10' width='10' > Partial Payment</td>
				<td><img src=assets/img/payment-due.jpg height='10' width='10' > Payment Due</td>
				</tr>
				</table>";
				$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
				$output .= "<thead>";
				$output .= "<tr align='left'>";
				$output .= "<th class='header'>Payment Milestone</th>";
				$output .= "<th class='header'>Milestone Date</th>";
				$output .= "<th class='header'>Amount</th>";
				$output .= "<th class='header'>Status</th>";
				$output .= "<th class='header'>Action</th>";
				$output .= "</tr>";
				$output .= "</thead>";
				if (count($payment_det>0))
				{
					foreach ($payment_det as $pd)
					{
						$total_amount_recieved += $pd['amount'];
						$payment_received = '';
						if ($pd['received'] == 0)
						{
							$payment_received = '<img src="assets/img/payment-due.jpg" alt="due" height="10" width="10" />';
						}
						else if ($pd['received'] == 1)
						{
							$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" height="10" width="10" />';
						}
						else
						{
							$payment_received = '<img src="assets/img/payment-pending.jpg" alt="pending" height="10" width="10" />';
						}							
						$output .= "<tr>";
						$output .= "<td align='left'>".$pd['project_milestone_name']."</td>";
						$output .= "<td align='left'>".date('d-m-Y', strtotime($pd['expected_date']))."</td>";
						$output .= "<td align='left'> ".$pd['expect_worth_name'].' '.number_format($pd['amount'], 2, '.', ',')."</td>";
						$output .= "<td align='center'>".$payment_received."</td>";
						$output .= "<td align='left'><a class='edit' onclick='paymentProfileEdit(".$pd['expectid']."); return false;' >Edit</a> | ";
						$output .= "<a class='edit' onclick='paymentProfileDelete(".$pd['expectid']."); return false;' >Delete</a></td>";
						$output .= "</tr>";
						$pt_select_box .= '<option value="'. $pd['expectid'] .'">' . $pd['project_milestone_name'] ." \$ ".number_format($pd['amount'], 2, '.', ',')." by ".date('d-m-Y', strtotime($pd['expected_date']))." " . '</option>';
						$pdi ++;
					}
				}
				$output .= "<tr>";
				$output .= "<td></td>";
				$output .= "<td><b>Total Milestone Payment : </b></td><td><b>".$pd['expect_worth_name'].' '.number_format($total_amount_recieved, 2, '.', ',') ."</b></td>";
				$output .= "</tr>";
				$output .= "</table>";
				$output .= '</div>';
				echo $output;
			}
			else
			{
				echo "{error:true, errormsg:'Percentage update failed'}";
			}
		}
	}
	
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
			$del = $this->project_model->delete_row('expected_payments', $wh_condn);
			if ($del)
			{
				//insert the log
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
	function agreedPaymentView() 
	{
		echo '<script type="text/javascript">
		$(function(){
				$("#sp_date_2").datepicker({dateFormat: "dd-mm-yy"});
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
		<br /><form id="set-payment-terms">
		<table class="payment-table">
		<tr>
			<td>
				<p>Payment Milestone *<input type="text" name="sp_date_1" id="sp_date_1" class="textfield width200px" /> </p>
				<p>Milestone date *<input type="text" name="sp_date_2" id="sp_date_2" class="textfield width200px pick-date" readonly /> </p>
				<p>Value *<input type="text" onkeypress="return isNumberKey(event)" name="sp_date_3" id="sp_date_3" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
				<div class="buttons">
					<button type="submit" class="positive" onclick="setProjectPaymentTerms(); return false;">Add Payment Terms</button>
				</div>
				<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
				<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
			</td>
		</tr>
		</table>
		</form>';
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
				$pt_select_box .= '<option value="'.$rec['expectid'].'">' . $rec['project_milestone_name']. ' - '.$rec['expect_worth_name']." ".number_format($rec['amount'], 2, '.', ',')." by ".date('d-m-Y', strtotime($rec['expected_date']))." " . '</option>';
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
				$output .= "<td align='left'><a class='edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' >Edit</a> | ";
				$output .= "<a class='edit' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' >Delete</a></td>";
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
			$("#pr_date_3").datepicker({dateFormat: "dd-mm-yy", maxDate: "0"});
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
			<p>Date Received *<input type="text" name="pr_date_3" id="pr_date_3" class="textfield width200px pick-date" readonly /> </p>
			
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
				$(function(){
					$("#pr_date_3").datepicker({dateFormat: "dd-mm-yy", maxDate: "0"});
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
			<p>Date Received *<input type="text" name="pr_date_3" id="pr_date_3" value="'.$received_deposit_date.'" class="textfield width200px pick-date" readonly /> </p>
			
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
		
		// echo "<pre>"; print_r($data_log); exit;
		
		$data_log['log_content'] = str_replace('\n', "<br />", $data_log['log_content']);
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
						// we're emailing the client, so remove the VCS log  prefix
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
				$ins['log_content'] = $data_log['log_content'] . $successful;
				
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
		
		$data['error'] = FALSE;
		
		$estimateHr = $updt_data['esthr'];
		
		if (!is_numeric($updt_data['esthr'])) 
		{
			$data['error'] = 'Invalid estimated hour!';
		} 
		else 
		{
			$wh_condn = $updt_data['lead_id'];
			$chk_stat = $this->project_model->get_actual_project_hour($wh_condn);
			if(count($chk_stat)>0) 
			{
				$actual_hr=$chk_stat['total_Hour'];
			} 
			else 
			{
				$actual_hr='0';
			}
			
			if($estimateHr < $actual_hr) 
			{
				$data['error'] = 'Estimated project hour must be equal or greater than the actual project hour!';
			}
			else 
			{
				$wh_condn = array('lead_id'=>$updt_data['lead_id']);
				$updt = array('estimate_hour'=>$estimateHr);
				$updt_date = $this->project_model->update_row('leads', $updt, $wh_condn);
			}
		}
		echo json_encode($data);
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
		$msPlEndDate  = date('d-m-Y', strtotime($milestone_details['ms_plan_end_date']));
		if($milestone_details['ms_act_st_date'] == '0000-00-00 00:00:00') {
			$msActStDate = '';
		} else {
			$msActStDate  = date('d-m-Y', strtotime($milestone_details['ms_act_st_date']));
		}
		if($milestone_details['ms_act_end_date'] == '0000-00-00 00:00:00') {
			$msActEndDate = '';
		} else {
			$msActEndDate  = date('d-m-Y', strtotime($milestone_details['ms_act_end_date']));
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
				$output .= "<td align='left'><a class='edit' onclick='milestoneEditTerm(".$ms_det['milestoneid']."); return false;' >Edit</a> | ";
				$output .= "<a class='edit' onclick='milestoneDeleteTerm(".$ms_det['milestoneid'].");' >Delete</a></td>";
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


}
?>