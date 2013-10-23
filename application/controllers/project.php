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
		$this->load->helper('text');
		$this->load->library('email');
		$this->email->initialize($config);
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
		$data['records'] = $this->project_model->get_projects_results($pjtstage = 'null', $pm_acc = 'null', $cust = 'null', $keyword = 'null');
		$this->load->view('projects_view', $data);
    }
	
	/*
	 *Advanced Search For Projects
	 */
	public function advance_filter_search_pjt($pjtstage='false', $pm_acc='false', $cust='false', $keyword='false')
	{
	    /*
		 *$pjtstage - job_status. $pm_acc - Project Manager Id. $cust - Customers Id.(custid_fk)
		 */
		if ($keyword == 'false' || $keyword == 'undefined') {
			$keyword = 'null';
		}
		$getProjects = $this->project_model->get_projects_results($pjtstage, $pm_acc, $cust, $keyword);	

		$data['pjts_data'] = $getProjects;
		$data['records'] = $getProjects;
		
		$this->load->view('projects_view_inprogress', $data);
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
			$data['chge_access'] = $this->project_model->get_jobid($id, $usernme['userid']);
		}
		
		$result = $this->project_model->get_quote_data($id);
		if(!empty($result)) {
			$data['quote_data'] = $result[0];
			$data['view_quotation'] = true;
			$temp_cont = $this->project_model->get_contract_jobs($result[0]['jobid']);
			
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

					$user_data = $this->project_model->get_user_data_by_id($ld['userid_fk']);
					
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
				$data['payment_data'] = $this->project_model->get_payment_terms($data['quote_data']['jobid']);
			}
			
			$deposits = $this->project_model->get_deposits_data($data['quote_data']['jobid']);
			if (!empty($deposits))
			{
				$data['deposits_data'] = $deposits;
			}
			
			/**
			 * Get files associated with this job
			 */
			$fcpath = UPLOAD_PATH; 
		    $f_dir = $fcpath . 'files/' . $id . '/'; 
			$data['job_files_html'] = $this->project_model->get_job_files($f_dir, $fcpath, $data['quote_data']);

			/**
			 * Get URLs associated with this job
			 */
			$data['job_urls_html'] = $this->project_model->get_job_urls($id);
			
			//For list the particular lead owner, project manager & lead assigned_to in the welcome_view_project page.
			$data['list_users'] = $this->project_model->get_list_users($id);
			
			//For list the particular project team member in the welcome_view_project page.
			$data['contract_users'] = $this->project_model->get_contract_users($id);	

            $this->load->view('welcome_view_project', $data);
        }
        else
        {
            echo "Project does not exist or if you are an account manager you may not be authorised to view this";
        }
        
    }
	
	function getPjtIdFromdb($pjtid) 
	{
		$this->db->where('pjt_id',$pjtid);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		if($query == 0 ) echo 'userOk';
		else echo 'userNo';
	}
	
	function getPjtValFromdb($pjtval) 
	{
		$this->db->where('actual_worth_amount', $pjtval);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		if($query == 0 ) echo 'userOk';
		else echo 'userNo';
	}
	
	public function ajax_set_contractor_for_job()
	{
		$data = real_escape_array($this->input->post());
		if (isset($data['jobid']) && !empty($data['contractors']) && $this->welcome_model->get_job(array('jobid' => $data['jobid'])))
		{
			$contractors = explode(',', $data['contractors']);	
			$project_member = array();
			$result = array();
			$project_member = $this->db->query("SELECT userid_fk FROM ".$this->cfg['dbpref']."contract_jobs WHERE jobid_fk = " .$data['jobid']); 
			foreach ($project_member->result() as $project_mem)
			{
				$result[] = $project_mem->userid_fk;
			}
			$new_project_member_insert = array_diff($contractors, $result);
			$user_id_for_mail = implode ("," , $new_project_member_insert);
			$new_project_member_delete = array_diff($result, $contractors);
			$new_project_member_delete = array_values($new_project_member_delete);	
			if(!empty($new_project_member_insert))
			{
				foreach ($new_project_member_insert as $con) 
				{
					if (preg_match('/^[0-9]+$/', $con))
					{
						$this->db->insert($this->cfg['dbpref'].'contract_jobs', array('jobid_fk' => $data['jobid'], 'userid_fk' => $con));
					}
				}
				$user_id_for_mail = implode ("," , $new_project_member_insert);
				$query_for_mail = $this->db->query("SELECT email,first_name FROM ".$this->cfg['dbpref']."users u WHERE u.userid IN(".$user_id_for_mail.")");
				//echo $this->db->last_query();
				foreach ($query_for_mail->result() as $mail_id)
				{			
					$mail = $mail_id->email;
					$first_name = $mail_id->first_name;
					$log_email_content1 = $this->get_user_mail($mail , $first_name, $type = "insert");
						
				}
			}
			if(!empty($new_project_member_delete))
			{
				$user_id_for_mail = implode("," , $new_project_member_delete);	
				$query_for_mail = $this->db->query("SELECT email, first_name FROM ".$this->cfg['dbpref']."users u WHERE u.userid IN(".$user_id_for_mail.")");
				foreach ($query_for_mail->result() as $mail_id)
				{
					 $mail = $mail_id->email;
					 $first_name = $mail_id->first_name;
					 $log_email_content1 = $this->get_user_mail($mail , $first_name, $type = "remove" );
				}
				$this->db->where('jobid_fk', $data['jobid']);
				$this->db->where_in('userid_fk', $new_project_member_delete);
			    $this->db->delete($this->cfg['dbpref'].'contract_jobs');
				//echo $this->db->last_query();
			}
			echo '{status: "OK"}';
		}
		else if(empty($data['contractors']))
		{
		    $members_id = $data['project-mem'];
			$members = explode(',', $data['project-mem']);				
			$query_for_mail = $this->db->query("SELECT email, first_name FROM ".$this->cfg['dbpref']."users u WHERE u.userid IN(".$members_id.")");
			//echo $this->db->last_query();
			foreach ($query_for_mail->result() as $mail_id)
			{
				 $mail = $mail_id->email;
				 $first_name = $mail_id->first_name;
				 $log_email_content1 = $this->get_user_mail($mail , $first_name, $type = "remove");
			}
			$this->db->where_in('jobid_fk', $data['jobid']);
			$this->db->delete($this->cfg['dbpref'].'contract_jobs');
			//echo $this->db->last_query();
		}
		else
		{
			echo '{error: "Invalid job or userid supplied!"}';
		}
	}
	

}
?>