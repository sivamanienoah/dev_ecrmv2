<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends crm_controller {
    
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->login_model->check_login();
        $this->load->model('user_model');
        $this->load->model('role_model');
		$this->load->model('email_template_model');
        $this->load->library('validation');
		$this->userdata = $this->session->userdata('logged_in_user');
    }

	/*
	*@User List
	*
	*/
    public function index($limit = 0, $search = false)
	{
        $data['customers'] = $this->user_model->user_list($limit, $search);
        
        if ($search == false) {
            $config['base_url'] = $this->config->item('base_url') . 'user/index/';
            $config['total_rows'] = (string) $this->user_model->user_count();
        }
		$data['max_allow_user'] = $this->cfg['max_allowed_users'][0];
        $this->load->view('user/list_view', $data);
    }

	/*
	*@Add User
	*
	*/
    public function add_user($update = false, $id = false, $ajax = false)
	{	
		$post_data = real_escape_array($this->input->post());
	
        $rules['first_name'] = "trim|required";
		$rules['last_name']  = "trim|required";
		$rules['username']   = "trim|required";
        if ($this->input->post('new_user') || $this->input->post('update_password')) {
			$rules['password'] = "trim|required|min_length[6]";
		}
        $rules['level']   = "required|callback_level_check";
		$rules['role_id'] = "required|callback_level_check";
		$rules['email']   = "trim|required|valid_email";
		
		$this->validation->set_rules($rules); // validation rules
		
		$fields['first_name']  = "First Name";
		$fields['last_name']   = "Last Name";
		$fields['username']    = "Username";
        $fields['phone']       = "Telephone";
        $fields['mobile']      = "Mobile";
		$fields['email']       = "Email Address";
		$fields['role_id']     = "Role";
		$fields['contract_manager'] = "Contract Manager";
		$fields['password']    = "Password";
		$fields['level']       = "User Level";
		$fields['skill_id']    = "Skill";
		$fields['department_id'] = "Department";
		$fields['inactive']    = 'Inactive';
		$fields['auth_type']   = 'Login Authentication';
		
		// insert new level settings concepts
		$fields1['region']     = 'region';
		$fields1['country']    = 'country';
		$fields1['state']      = 'state';
		$fields1['location']   = 'location';
		
		// validation rules
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        
        $data = '';	
		
		//for Inactive Role
		if($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
			// $where = "(belong_to=".$id." or lead_assign=".$id." or assigned_to =".$id.")";
			$where = '(belong_to = '.$id.' OR assigned_to ='.$id.' OR FIND_IN_SET('.$id.', lead_assign)) ';
			$this->db->where($where);
			$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		}
		
        $data['roles']		= $this->role_model->active_role_list();
		$data['levels'] 	= $this->user_model->get_levels();
		$data['users'] 		= $this->user_model->getUserLists($type='active');
		$data['skill_arr'] 	= $this->user_model->getSkill();
		$data['dept_arr'] 	= $this->user_model->getDept();
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_user'])) {
            $customer = $this->user_model->get_user($id); /*get the user details*/
			$data['users'] 			 = $this->user_model->getUserLists($type='all');
            $data['this_user']		 = $customer[0]['userid'];
			$data['this_user_level'] = $customer[0]['level'];

            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() == false) 
		{
            if ($ajax == false) 
			{
                $this->load->view('user/add_view', $data);
            } 
			else 
			{
                $json['error']			 = true;
                $json['ajax_error_str']  = $this->validation->error_string;
                echo json_encode($json);
            }
		} 
		else 
		{
			// all good
            foreach($fields as $key => $val) 
			{
                $update_data[$key] = $this->input->post($key);
            }
			//for inactive role
			if ($update_data['inactive'] == "") {
				$update_data['inactive'] = 0;
			} else if ($update_data['inactive'] == 1) {
				$update_data['inactive'] = 1;
			} else {
				if ($data['cb_status']==0) {
					$update_data['inactive'] = 0;
				} else {
					$update_data['inactive'] = 1;
				}
			}
			
			//for new level settings concepts
			foreach($fields1 as $key => $val) {
                $update_data1[$key] = $this->input->post($key);
            }
			if($update_data['role_id'] == 14) {
				$update_data['contract_manager'] = $this->input->post('contract_manager');
			}
            if ($this->input->post('new_user') || $this->input->post('update_password')) 
			{
                $update_data['password'] = sha1($update_data['password']);
                if (isset($update_data['update_password'])) unset($update_data['update_password']);
                if (isset($update_data['new_user'])) unset($update_data['new_user']);
            } 
			else 
			{
                unset($update_data['password']);
            }
            
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$user_ids = $this->uri->segment(4);
				$level_id = $update_data['level'];
				
				$this->user_model->update_level($update_data1,$user_ids,$level_id);
				//update
				if ($this->user_model->update_user($id, $update_data)) 
				{
					if($post_data['role_id'] == $post_data['role_change_mail']) 
					{
						//insert log
						$usr_data = $this->user_model->get_user($id);
						$ins_data					= array();
						$ins_data['emp_id'] 		= $usr_data[0]['emp_id'];
						$ins_data['user_id'] 		= $id;
						$ins_data['username'] 		= $usr_data[0]['username'];
						$ins_data['role_id'] 		= $update_data['role_id'];
						$ins_data['skill_id'] 		= $usr_data[0]['skill_id'];
						$ins_data['department_id'] 	= $usr_data[0]['department_id'];
						$ins_data['active'] 		= $usr_data[0]['inactive'];
						$ins_data['created_on'] 	= date('Y-m-d H:i:s');
						$this->db->insert($this->cfg['dbpref'] . 'users_logs', $ins_data);
						
						$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
						$dis['date_created'] = date('Y-m-d H:i:s');
						$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
							
						$from		  = $this->userdata['email'];
						$arrEmails 	  = $this->config->item('crm');
						$arrSetEmails = $arrEmails['director_emails'];		
						$admin_mail	  = implode(',',$arrSetEmails);
						$subject      = 'User Role Change Notification';
						
						//email sent by email template
						$param = array();

						$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'user_name'=>$user_name, 'first_name'=>$update_data['first_name'], 'last_name'=>$update_data['last_name'], 'signature'=>$this->userdata['signature']);

						$param['to_mail'] = $update_data['email'];
						$param['bcc_mail'] = $admin_mail;
						$param['from_email'] = $from;
						$param['from_email_name'] = $user_name;
						$param['template_name'] = "User Role Change Notification";
						$param['subject'] = $subject;

						$this->email_template_model->sent_email($param);
					}   
					
					if($post_data['level'] == $post_data['level_change_mail']) 
					{
						$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
						$dis['date_created'] = date('Y-m-d H:i:s');
						$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
							
						$from=$this->userdata['email'];
						$arrEmails = $this->config->item('crm');
						$arrSetEmails=$arrEmails['director_emails'];
								
						$admin_mail=implode(',',$arrSetEmails);
						$subject='User Level Change Notification';

						//email sent by email template
						$param = array();

						$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'user_name'=>$user_name, 'first_name'=>$update_data['first_name'], 'last_name'=>$update_data['last_name'], 'signature'=>$this->userdata['signature']);

						$param['to_mail'] = $update_data['email'];
						$param['bcc_mail'] = $admin_mail;
						$param['from_email'] = $from;
						$param['from_email_name'] = $user_name;
						$param['template_name'] = "User Level Change Notification";
						$param['subject'] = $subject;

						$this->email_template_model->sent_email($param);
					}
					$this->session->set_flashdata('confirm', array('User Details Updated!'));
					// redirect('user/add_user/update/' . $id);
					if($id==$this->userdata['userid']) {
						unset($this->session->userdata);
						redirect('userlogin');
					}
					redirect('user');
				}
			} 
			else 
			{
				//insert
				$newid = $this->user_model->insert_user($update_data);
				if ($newid == 'maxusers')
				{
					$this->session->set_flashdata('login_errors', array('You can create maximum '.$this->cfg['max_allowed_users'][0].' users only.!'));
					redirect('user');
				}
				else if ($newid == 'emailexist')
				{
					redirect('user');
				}
				else
				{
					$user_ids = $this->db->insert_id();
					$level_id = $update_data['level'];
					$this->user_model->insert_level_settings($update_data1, $user_ids, $level_id);
					
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
						
					$from=$this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
							
					$admin_mail=implode(',',$arrSetEmails);
					$subject='New User Creation Notification';
					
					//email sent by email template
					$param = array();

					$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'user_name'=>$user_name, 'first_name'=>$update_data['first_name'], 'last_name'=>$update_data['last_name'],'login_username'=>$update_data['username'], 'base_url'=>$this->config->item('base_url'), 'email'=>$update_data['email'], 'password'=>$post_data['password'], 'signature'=>$this->userdata['signature']);

					$param['to_mail'] 			= $update_data['email'];
					$param['bcc_mail'] 			= $admin_mail;
					$param['from_email'] 		= $from;
					$param['from_email_name'] 	= $user_name;
					$param['template_name'] 	= "New User Creation Notification";
					$param['subject'] 			= $subject;

					$this->email_template_model->sent_email($param);
		
					if ($ajax == false) {
						$this->session->set_flashdata('confirm', array('New User Added!'));
						redirect('user');
					} 
					else 
					{
						$json['error'] 		= false;
						$json['custid'] 	= $newid;
						$json['cust_name'] 	= $this->input->post('first_name') . ' ' . $this->input->post('last_name');
						$json['cust_email'] = $this->input->post('email');
						echo json_encode($json);
					}
				}
			}
		}
    }
	
	/*
	*@ Find exist email address by ajax
	*
	*/
	function getUserResult() {
		$data =	real_escape_array($this->input->post());
		$this->user_model->find_exist_email($data);
	}

	/*
	*@Get User for Lead Assigned To
	*
	*/
	function getUserDetFromDb() {
		$data = real_escape_array($this->input->post());
		$res = $this->user_model->getUserLeadAssigned($data['user']);
		return $res;
	}
	
	function getRestrictedUsers() {
		$data = real_escape_array($this->input->post());
		
		$this->db->select("userid, first_name, last_name, emp_id");
		$this->db->from($this->cfg['dbpref']."users");
		$this->db->where("userid in (".$data['user'].")");
		$this->db->where("inactive", 0);
		$this->db->order_by("first_name");
		$query = $this->db->get();
		$user_res = $query->result_array();
		$res = '';
		foreach($user_res as $user) {
			$res .= "<option value=".$user['userid'].">".$user['first_name']." ".$user['last_name']." - ".$user['emp_id']."</option>";
		}
		echo $res;
	}
	
	
	/*
	*@Check User Status
	*
	*/
	function ajax_check_status_user() 
	{
		$data =	real_escape_array($this->input->post()); // escape special characters
		$this->user_model->check_user_status($data);
	}
	
	/*
	*@Delete User
	*
	*/
	function delete_user($id = false)
	{
		if ($this->session->userdata('delete')==1) {
				$this->login_model->check_login();
				if ($this->user_model->delete_user($id)) {
					$this->session->set_flashdata('confirm', array('User Account Deleted!'));
					redirect('user');
				}
		} else {
				$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
				redirect('user');
		}
	}
    
	/*
	*@Search User
	*
	*/
    public function search() {
        $this->login_model->check_login();
		$data =	real_escape_array($this->input->post()); // escape special characters
        if (isset($data['cancel_submit'])) {
            redirect('user');
        } else if ($name = $data['cust_search']) {

            redirect('user/index/0/' . remove_special_chars($name));
        } else {
            redirect('user');
        }
    }


	/*
	*@Method : Check Level
	*@module : User
	*/
    public function level_check($str) {
        if (!preg_match('/^[0-9]+$/', $str)) {
			$this->validation->set_message('level_check', 'Level must be selected.');
			return false;
		} else {
            return true;
        }
    }
	
	/*
	*@ Method :log History
	*@ module : User
	*/
	public function log_history($user = 0) {
		$log_user = $this->user_model->get_user($user);
		if (count($log_user) > 0) {
			if ( ! in_array($this->userdata['level'], array(0, 1)) && $log_user[0]['userid'] != $this->userdata['userid']) {
				$this->session->set_flashdata('login_errors', array('Your access level does not allow access to this area!'));
                redirect('notallowed/');
                exit();
			}
			$log_user = $log_user[0];
		}
		else {
			$log_user = $this->userdata;
		}
		
		$log_date = $this->check_date($this->input->post('log_date'));
		if ( ! $log_date) {
			$log_date = date('Y-m-d');
		}
		
		$data['current_log_date'] = date('l, jS F y', strtotime($log_date));
		$rs                       = $this->user_model->log_history($log_date,$log_user); // Get Log History	
		$data['log_user_name']    = $log_user['first_name'] . ' ' . $log_user['last_name'];
		$data['log_set']          = '';
		$time_total               = 0;
		
		foreach ($rs as $row) { 
			$log_content = nl2br($row['log_content']);
			$numerc_time = (int) $row['time_spent'];
			$time_total += $numerc_time;
			if ( ! isset($row['lead_title'])) {
				$row['lead_title'] = 'General Task';
			}
			$row_time_spent = '';
			if ($numerc_time > 0) {
				$the_hours = floor($numerc_time / 60);
				$the_mins = $numerc_time % 60;
				if ($the_hours > 0) {
					$row_time_spent = " - Time Spent: {$the_hours} Hours";
					if ($the_mins > 0) $row_time_spent .= " {$the_mins} Mins";
				}
				else {
					$row_time_spent = " - Time Spent: {$the_mins} Mins";
				}
			}
			
			$data['log_set'] .= '
			<div class="log">
				<p class="data">
					<span>'.$row["fancy_date"].'</span>
					'.$data["log_user_name"].' - '.$row["lead_title"].'
				</p>
				<p class="desc">
					'.$log_content.'
				</p>
			</div>';
		}
		
		$hours_spent    = floor( $time_total / 60);
		$remainder_mins = $time_total - ($hours_spent * 60);
		$mins_spent     = '';
		if ($remainder_mins > 0) {
			$mins_spent  = "{$remainder_mins} Mins";
		}
		
		if ($hours_spent > 0) {
			$data['total_time_spent'] = "Total Time: {$hours_spent} Hours {$mins_spent}";
		}
		else {
			$data['total_time_spent'] = ($mins_spent != '') ? "Total Time: {$mins_spent}" : '';
		}
		if ($data['log_set'] == '') {
			$data['log_set'] = '<h4>No logs available for this date!</h4>';
		}
		$this->load->view('user/log_list_view', $data);
	}


	/*
	*@ Method : Check Date
	*@ module : User
	*/

	public function check_date($date)
	{
		if ($date)
		{
			$date_parts = @explode('-', $date);
			if (count($date_parts) == 3)
			{
				$time = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]);
				if ($time)
				{
					return $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
				}
			}
		}
		
		return FALSE;
	}

	/*
	*@ Method : Use Check Exist User
	*@ User Controller 
	*/
	
	function getUserfromdb($username, $update)
	{
		 $this->user_model->getUserfromdb($username, $update);
	}
	
	/*
	*@ Method : Load Regions
	*@ User Controller 
	*/
	
	public function loadRegions()
	{
		$this->user_model->get_regions();
	}
	
	/*
	*@Method : Edit Load Regions
	*@User Controller 
	*/
	public function editloadRegions($uid)
	{
	   $this->user_model->get_loadregionsByuserId($uid);
	}
	
	/* 
	 *Adding User Page 
	 *loading country
	 *@User Controller 
	 */
	public function loadCountrys()
	{
		$data 	   = real_escape_array($this->input->post());
		$region_id = join(",", $data['region_id']); 
		$this->user_model->get_loadCountrysByRegionid($region_id);
	}
	
	/*
	*@Method : Edit Load Regions
	*@User Controller 
	*/
	public function editloadCountrys()
	{
		$data 	   = real_escape_array($this->input->post());
		$region_id = join(",", $data['regionid']);
		$this->user_model->edit_loadCountrys($region_id, $data['uid']);
	}
	/* 
	 * Adding User Page 
	 * loading state
	 */
	public function loadStates()
	{
		$data = real_escape_array($this->input->post());
		$country_id = join(",", $data['country_id']);
		$this->user_model->get_load_state($country_id);
	}

	/* 
	 * Adding User Page 
	 * edit loading state
	 */
	public function editloadStates()
	{
		$data = real_escape_array($this->input->post()); 
	    $this->user_model->edit_loadstate($data['country_id'],$data['uid']);
	}
	/* 
	 * Adding User Page 
	 * loading location
	 */
	public function loadLocations()
	{
		 $data = real_escape_array($this->input->post());
		 $state_id = join(",", $data['state_id']);
		 $this->user_model->get_loadLocations($state_id);
	}

	/*
	*@Method : load Locations
	*@User Controller 
	*/
	public function editloadLocations()
	{
		$data      = real_escape_array($this->input->post()); // escape special characters
		$state_id  = $data['state_id']; 
		$uid       = $data['uid'];
		$this->user_model->editloadLocations($state_id,$uid);
	}

	/*
	*@Method : Check Country
	*@User Controller 
	*/
	public function checkcountry() 
	{
		$data             =	real_escape_array($this->input->post()); // escape special characters
		$explode_region   = @explode(',',$data['region_load']);
		$explode_country  = @explode(',',$data['country_load']);

		for($i=0;$i<count($explode_region);$i++) {
			$check_country_query = $this->user_model->checkcountrylevel3($explode_region[$i], $explode_country);
			if($check_country_query == 0) {
				$json['msg'] = 'noans'; 
				break;
			} else {
				$json['msg'] = 'success';
			}
		}
		echo json_encode($json); exit;
	}

	/*
	*@Method : Check State
	*@User Controller 
	*/
	public function checkstate() 
	{
	
		$data             =	real_escape_array($this->input->post()); // escape special characters
		$explode_region   = @explode(',',$data['region_load']);
		$explode_country  = @explode(',',$data['country_load']);
		$explode_state    = @explode(',',$data['state_load']);
		
		for($i=0;$i<count($explode_region);$i++) {
			$check_country_query = $this->user_model->checkcountrylevel3($explode_region[$i], $explode_country);
			if($check_country_query == 0) {
				$json['countrymsg'] = 'noans'; 
				break;
			} else {
				$json['countrymsg'] = 'success';
			}
		}
		
		for($j=0;$j<count($explode_country);$j++) {
			$check_state_query = $this->user_model->checkstatelevel4($explode_country[$j], $explode_state);
			if($check_state_query == 0) {
				$json['statemsg'] = 'nostate'; 
				break;
			} else {
				$json['statemsg'] = 'success';
			}
		}
		
		echo json_encode($json); exit;
		
	}
	
	
	/*
	*@Method : Check Location
	*@User Controller 
	*/
	public function checklocation() 
	{
		$data             =	real_escape_array($this->input->post()); // escape special characters
		$explode_region   = @explode(',',$data['region_load']);
		$explode_country  = @explode(',',$data['country_load']);
		$explode_state    = @explode(',',$data['state_load']);
		$explode_location = @explode(',',$data['location_load']);
		
		for($i=0;$i<count($explode_region);$i++) {
			$check_country_query = $this->user_model->checkcountrylevel3($explode_region[$i],$explode_country);
			if($check_country_query == 0) {
				$json['countrymsg'] = 'noans'; 
				break;
			} else {
				$json['countrymsg'] = 'success';
			}
		}
		
		for($j=0;$j<count($explode_country);$j++) 
		{
			$check_state_query = $this->user_model->checkstatelevel4($explode_country[$j],$explode_state);
			if($check_state_query == 0) {
				$json['statemsg'] = 'nostate'; 
				break;
			} else {
				$json['statemsg'] = 'success';
			}
		}
		
		for($k=0;$k<count($explode_state);$k++) 
		{
			$check_location_query = $this->user_model->checklocationlevel5($explode_state[$k],$explode_location);
			if($check_location_query == 0) {
				$json['locationmsg'] = 'noloc'; 
				break;
			} else {
				$json['locationmsg'] = 'success';
			}
		}
		echo json_encode($json); exit;
	}
	
	/*
	*@method checkUniqueUsername
	*POST var username
	*/
	function checkUniqueUsername() {
		$data =	real_escape_array($this->input->post());
		$this->user_model->check_username($data['username'],$data['updatedata']);
	}
	
	/*
	*@For Get the logs for User
	*@Method get_user_logs
	*/
	public function get_user_logs($id) 
	{	
		$error = false;

		if (preg_match('/^[0-9]+$/', $id)) {
			$data['log_data'] = $this->user_model->get_logs($id);
		} else {
			$error = true;
		}
		
		if($error==true) {
			return false;
		} else {
			$this->load->view('user/user_log_view', $data);
		}
	}

}

?>
