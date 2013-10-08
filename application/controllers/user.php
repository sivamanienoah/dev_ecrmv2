<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends crm_controller {
    
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->login_model->check_login();
        $this->load->model('user_model');
        $this->load->model('role_model');
        $this->load->library('validation');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    public function index($limit = 0, $search = false)
	{
        $default = array('last_name', 'asc');
		if (!$this->session->userdata('user_sort'))
		{
			$this->session->set_userdata('user_sort', $default);
		}
		$current = $this->session->userdata('user_sort');
		$data['user_sort'] = $current;
		
		
		$this->login_model->check_login();
		
        $data['customers'] = $this->user_model->user_list($limit, $search, $current[0], $current[1]);
        
       //$data['pagination'] = '';
        if ($search == false) {
            //$this->load->library('pagination');
            
            $config['base_url'] = $this->config->item('base_url') . 'user/index/';
            $config['total_rows'] = (string) $this->user_model->user_count();
            //$config['per_page'] = '35';
            
            //$this->pagination->initialize($config);
            
            //$data['pagination'] = $this->pagination->create_links();
        }
		$data['max_allow_user'] = $this->cfg['max_allowed_users'][0];
        $this->load->view('user/list_view', $data);
        
    }
    
    public function add_user($update = false, $id = false, $ajax = false)
	{
        /*if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($_POST['delete_user'])) {
            
            // check to see if this customer has a job on the system before deleting
            // to do
            
            $this->user_model->delete_user($id);
            $this->session->set_flashdata('confirm', array('User Account Deleted!'));
            redirect('user/'); 
        }*/
        
        $rules['first_name'] = "trim|required";
		$rules['last_name'] = "trim|required";
        if ($this->input->post('new_user') || $this->input->post('update_password')) {
            $rules['password'] = "trim|required|min_length[6]";
        }
        $rules['level'] = "required|callback_level_check";
		$rules['role_id'] = "required|callback_level_check";
		$rules['email'] = "trim|required|valid_email";
		
		$this->validation->set_rules($rules);
		
		$fields['first_name'] = "First Name";
		$fields['last_name'] = "Last Name";
        $fields['phone'] = "Telephone";
        $fields['mobile'] = "Mobile";
		$fields['email'] = "Email Address";
		$fields['role_id'] = "Role";
		$fields['sales_code'] = 'Sales Code';
		//$fields['key'] = "Office Key Status";
		//$fields['bldg_key'] = "Building Key Status";
		$fields['password'] = "Password";
		$fields['level'] = "User Level";
		$fields['inactive'] = 'Inactive';
		// insert new level settings concepts
		$fields1['region'] = 'region';
		$fields1['country'] = 'country';
		$fields1['state'] = 'state';
		$fields1['location'] = 'location';
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        
        $data = '';
        $data['roles']=$this->role_model->role_list();
		$data['levels'] = $this->user_model->get_levels();
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_user'])) {

            $customer = $this->user_model->get_user($id);
			//echo "hi" . "<pre>"; print_r($customer); exit;
			
            $data['this_user'] = $customer[0]['userid'];
			$data['this_user_level'] = $customer[0]['level'];

            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
			
        }
		
		if ($this->validation->run() == false) {
            if ($ajax == false) {
			//echo "<pre>"; print_r($data);
                $this->load->view('user/add_view', $data);
                //$this->load->view('user/add_view', $customer);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }
			
		} else {
			
			// all good
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
			//for new level settings concepts
			foreach($fields1 as $key => $val) {
                $update_data1[$key] = $this->input->post($key);
            }
		
			
            if ($this->input->post('new_user') || $this->input->post('update_password')) {
                $update_data['password'] = sha1($update_data['password']);
                if (isset($update_data['update_password'])) unset($update_data['update_password']);
                if (isset($update_data['new_user'])) unset($update_data['new_user']);
            } else {
                unset($update_data['password']);
            }
            
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
                //echo "<pre>"; print_r($update_data); exit;
				$user_ids = $this->uri->segment(4);
				$level_id = $update_data['level'];
				
				$this->user_model->update_level($update_data1,$user_ids,$level_id);
                //update
                if ($this->user_model->update_user($id, $update_data)) {
				
				
		 if($_POST['role_id'] == $_POST['role_change_mail']) 
		{
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
						<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title>Email Template</title>
						<style type="text/css">
						body {
							margin-left: 0px;
							margin-top: 0px;
							margin-right: 0px;
							margin-bottom: 0px;
						}
						</style>
						</head>

						<body>
						<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
						<tr><td bgcolor="#FFFFFF">
						<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
						  <tr>
							<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" /></td>
						  </tr>
						  <tr>
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Role Change Notification
</h3></td>
						  </tr>

						  <tr>
							<td>
							<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
							<p style="background: none repeat scroll 0 0 #4B6FB9;
							border-bottom: 1px solid #CCCCCC;
							color: #FFFFFF;
							margin: 0;
							padding: 4px;">
								<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
							<p style="padding: 4px;"> User Role has been Changed for &nbsp;'.$update_data['first_name']. '  '.$update_data['last_name']. '<br /><br />
							'.$this->userdata['signature'].'<br />
							</p>
						</div>
						</td>
						  </tr>

						   <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
						  </tr>
						</table>
						</td>
						</tr>
						</table>
						</body>
						</html>';	
						
						$from=$this->userdata['email'];
						$arrEmails = $this->config->item('crm');
						$arrSetEmails=$arrEmails['director_emails'];
								
						$admin_mail=implode(',',$arrSetEmails);
						$subject='User Role Change Notification';
						$this->load->library('email');
						$this->email->set_newline("\r\n");
						$this->email->from($from,$user_name);
						$this->email->to($update_data['email']);
						$this->email->bcc($admin_mail);
						$this->email->subject($subject);
						$this->email->message($log_email_content);

						$this->email->send(); 
		}   
		
		if($_POST['level'] == $_POST['level_change_mail']) 
		{
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
						<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title>Email Template</title>
						<style type="text/css">
						body {
							margin-left: 0px;
							margin-top: 0px;
							margin-right: 0px;
							margin-bottom: 0px;
						}
						</style>
						</head>

						<body>
						<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
						<tr><td bgcolor="#FFFFFF">
						<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
						  <tr>
							<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" /></td>
						  </tr>
						  <tr>
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;"> User Level Change Notification 
</h3></td>
						  </tr>

						  <tr>
							<td>
							<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
							<p style="background: none repeat scroll 0 0 #4B6FB9;
							border-bottom: 1px solid #CCCCCC;
							color: #FFFFFF;
							margin: 0;
							padding: 4px;">
								<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
							<p style="padding: 4px;"> User Level has been Changed for &nbsp;'.$update_data['first_name']. '  '.$update_data['last_name']. '<br /><br />
							'.$this->userdata['signature'].'<br />
							</p>
						</div>
						</td>
						  </tr>

						   <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
						  </tr>
						</table>
						</td>
						</tr>
						</table>
						</body>
						</html>';	
						
						$from=$this->userdata['email'];
						$arrEmails = $this->config->item('crm');
						$arrSetEmails=$arrEmails['director_emails'];
								
						$admin_mail=implode(',',$arrSetEmails);
						$subject='User Level Change Notification';
						$this->load->library('email');
						$this->email->set_newline("\r\n");
						$this->email->from($from,$user_name);
						$this->email->to($update_data['email']);
						$this->email->bcc($admin_mail);
						$this->email->subject($subject);
						$this->email->message($log_email_content);

						$this->email->send(); 
		}
                    $this->session->set_flashdata('confirm', array('User Details Updated!'));
                    redirect('user/add_user/update/' . $id);
                    
                }
                
                
            } else {
                //insert
				$newid = $this->user_model->insert_user($update_data);
                if ($newid != 'max_users') {
                   // echo "<pre>"; print_r($update_data); exit;
				    $user_ids = $this->db->insert_id();
					$level_id = $update_data['level'];
					$this->user_model->insert_level_settings($update_data1, $user_ids, $level_id);
					
				    $user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
						<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title>Email Template</title>
						<style type="text/css">
						body {
							margin-left: 0px;
							margin-top: 0px;
							margin-right: 0px;
							margin-bottom: 0px;
						}
						</style>
						</head>

						<body>
						<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
						<tr><td bgcolor="#FFFFFF">
						<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
						  <tr>
							<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" /></td>
						  </tr>
						  <tr>
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New User Creation Notification 
</h3></td>
						  </tr>

						  <tr>
							<td>
							<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
							<p style="background: none repeat scroll 0 0 #4B6FB9;
							border-bottom: 1px solid #CCCCCC;
							color: #FFFFFF;
							margin: 0;
							padding: 4px;">
								<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
							<p style="padding: 4px;">New user Created -'.$update_data['first_name']. '  '.$update_data['last_name']. '<br /> User Login Details<br /><br />
							Login URL : '.$this->config->item('base_url').' <br />User Login email id : '.$update_data['email'].'<br />
							Password : '.$_POST['password'].' <br />
							'.$this->userdata['signature'].'<br />
							</p>
						</div>
						</td>
						  </tr>

						   <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
						  </tr>
						</table>
						</td>
						</tr>
						</table>
						</body>
						</html>';	
						
		$from=$this->userdata['email'];
		$arrEmails = $this->config->item('crm');
		$arrSetEmails=$arrEmails['director_emails'];
				
		$admin_mail=implode(',',$arrSetEmails);
		$subject='New User Creation Notification';
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		$this->email->from($from,$user_name);
		$this->email->to($update_data['email']);
		$this->email->bcc($admin_mail);
		$this->email->subject($subject);
		$this->email->message($log_email_content);

		$this->email->send(); 
				  
                    if ($ajax == false) {
						

                        $this->session->set_flashdata('confirm', array('New User Added!'));
                        redirect('user/');
						 
						
						
                    } else {
                        $json['error'] = false;
                        $json['custid'] = $newid;
                        $json['cust_name'] = $this->input->post('first_name') . ' ' . $this->input->post('last_name');
                        $json['cust_email'] = $this->input->post('email');
                        echo json_encode($json);
                    }
                    
                } else if ($newid == 'max_users') {
					$this->session->set_flashdata('login_errors', array('You can create maximum '.$this->cfg['max_allowed_users'][0].' users only.!'));
					redirect('user/');
				}
                
            }
			
		}
        
    }
	
	// function getUserResult($email,$update)
	function getUserResult()
	{
	echo "<pre>"; print_r($_POST); exit;
		if ($update != 'undefined') 
		{
			$emailid = $this->db->query("select email from ".$this->cfg['dbpref']."users where email = '".$email."' and userid != '".$update."' ");
			if ($emailid == 1) 
			{
				echo 'userOk';
			}
			else 
			{
				echo 'userNo';
			}	
		}
		else {
			$this->db->where('email',$email);
			$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
			if ($query == 0) echo 'userOk';
			else echo 'userNo';
		}	
	}
	
	function getUserDetFromDb($users)
	{
		//echo $users;
		$query = $this->db->query("select userid, first_name, last_name from ".$this->cfg['dbpref']."users where userid in ($users) ORDER BY first_name");
		$user_res = $query->result_array();
		//print_r($user_res);
		//$res = '<select>';
		$res = '';
		$res .= "<option value='not_select'>Please Select</option>";
		foreach($user_res as $user) {
			$res .= "<option value=".$user['userid'].">".$user['first_name']." ".$user['last_name']."</option>";
		}
		//$res .= "</select>";
		echo $res;
	}
	
	function ajax_check_status_user() 
	{
		$id = $_POST['data'];
		$where = "(belong_to=".$id." or lead_assign=".$id." or assigned_to =".$id.")"; 
		$this->db->where($where);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		// echo $this->db->last_query(); exit;
		$res = array();
		if($query == 0) {
			$res['html'] .= "YES";
		} else {
			$res['html'] .= "NO";
		}
		echo json_encode($res);
		exit;
	}
	
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
    
    public function search()
	{
        $this->login_model->check_login();
		
        if (isset($_POST['cancel_submit'])) {
            
            redirect('user/');
            
        } else if ($name = $this->input->post('cust_search')) {
            
            redirect('user/index/0/' . $name);
            
        } else {
		
            redirect('user/');
            
        }
        
    }
    
    public function level_check($str)
	{
        if (!preg_match('/^[0-9]+$/', $str)) {
			$this->validation->set_message('level_check', 'Level must be selected.');
			return false;
		} else {
            return true;
        }
    }
	
	public function log_history($user = 0)
	{
		#$this->output->enable_profiler(TRUE);
		$log_user = $this->user_model->get_user($user);
		
		if (count($log_user) > 0)
		{
			if ( ! in_array($this->userdata['level'], array(0, 1)) && $log_user[0]['userid'] != $this->userdata['userid'])
			{
				$this->session->set_flashdata('login_errors', array('Your access level does not allow access to this area!'));
                redirect('notallowed/');
                exit();
			}
			$log_user = $log_user[0];
		}
		else
		{
			$log_user = $this->userdata;
		}
		
		$log_date = $this->check_date($this->input->post('log_date'));
		
		if ( ! $log_date)
		{
			$log_date = date('Y-m-d');
		}
		
		$data['current_log_date'] = date('l, jS F y', strtotime($log_date));
		
		# now get the logs for the user on that day
		$sql = "SELECT *, DATE_FORMAT(`".$this->cfg['dbpref']."logs`.`date_created`, '%W, %D %M %y %h:%i%p') AS `fancy_date`
				FROM ".$this->cfg['dbpref']."logs
				LEFT JOIN `".$this->cfg['dbpref']."jobs` ON `".$this->cfg['dbpref']."jobs`.`jobid` = `".$this->cfg['dbpref']."logs`.`jobid_fk`
				WHERE DATE(`".$this->cfg['dbpref']."logs`.`date_created`) = ?
				AND `userid_fk` = ?
				ORDER BY `".$this->cfg['dbpref']."logs`.`date_created`";
			
		$q = $this->db->query($sql, array($log_date, $log_user['userid']));
		$rs = $q->result_array();
		
		$data['log_user_name'] = $log_user['first_name'] . ' ' . $log_user['last_name'];
		
		$data['log_set'] = '';
		
		$time_total = 0;
		
		foreach ($rs as $row)
		{
			$log_content = nl2br($row['log_content']);
			
			$numerc_time = (int) $row['time_spent'];
			
			$time_total += $numerc_time;
			
			if ( ! isset($row['job_title']))
			{
				$row['job_title'] = 'General Task';
			}
			
			$row_time_spent = '';
			if ($numerc_time > 0)
			{
				$the_hours = floor($numerc_time / 60);
				$the_mins = $numerc_time % 60;
				if ($the_hours > 0)
				{
					$row_time_spent = " - Time Spent: {$the_hours} Hours";
					if ($the_mins > 0) $row_time_spent .= " {$the_mins} Mins";
				}
				else
				{
					$row_time_spent = " - Time Spent: {$the_mins} Mins";
				}
			}
			
			$data['log_set'] .= <<< EOD
	<div class="log">
		<p class="data">
		    <span>{$row['fancy_date']} <strong>{$row_time_spent}</strong></span>
		{$data['log_user_name']} - {$row['job_title']}
		</p>
		<p class="desc">
		{$log_content}
		</p>
	</div>
EOD;
		}
		
		$hours_spent = floor( $time_total / 60);
		$remainder_mins = $time_total - ($hours_spent * 60);
		
		$mins_spent = '';
		if ($remainder_mins > 0)
		{
			$mins_spent = "{$remainder_mins} Mins";
		}
		
		if ($hours_spent > 0)
		{
			$data['total_time_spent'] = "Total Time: {$hours_spent} Hours {$mins_spent}";
		}
		else
		{
			$data['total_time_spent'] = ($mins_spent != '') ? "Total Time: {$mins_spent}" : '';
		}
		
		if ($data['log_set'] == '')
		{
			$data['log_set'] = '<h4>No logs available for this date!</h4>';
		}
		
		$this->load->view('user/log_list_view', $data);
	}
	
	public function check_date($date)
	{
		if ($date)
		{
			$date_parts = explode('-', $date);
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
	
	function getUserfromdb($username, $update)
	{
	//echo $update;
		if ($update != 'undefined') {
		
			$where = "email = '".$username."' AND `userid` != '".$update."' ";
			$this->db->where($where);
			$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
			//echo $this->db->last_query();
			if ($query == 0) {
				echo 'userOk';
			}
			else {
				echo 'userNo';
			}
		}
		else {	
			$this->db->where('email',$username);
			$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
			if( $query == 0 ) echo 'userOk';
			else echo 'userNo';
		}	
	}
	
	public function loadRegions()
	{
	    $output = '';
		$region_query = $this->db->query("SELECT regionid,region_name FROM ".$this->cfg['dbpref']."region");
		foreach ($region_query->result() as $regions)
		{
			if($id == $regions->regionid)
				$output .= '<option value="'.$regions->regionid.'" selected = "selected" >'.$regions->region_name.'</option>';
			else
				$output .= '<option value="'.$regions->regionid.'">'.$regions->region_name.'</option>';
		}
		echo $output;
	}
	
	public function editloadRegions($uid)
	{
	    $output = '';
		
		$rs = $this->db->query("select region_id from ".$this->cfg['dbpref']."levels_region where user_id='{$uid}'");
		$r_ids = $rs->result();	 
		$user_reg = array();
		foreach($r_ids as $reg_id) {
			$user_reg[] = $reg_id->region_id;
		}
		//print_r($user_reg);		die();
		$region_query = $this->db->query("SELECT regionid,region_name FROM ".$this->cfg['dbpref']."region");
		
		foreach ($region_query->result() as $regions)
		{		
			
			if(in_array($regions->regionid,$user_reg)){
				$output .= '<option value="'.$regions->regionid.'" selected = "selected" >'.$regions->region_name.'</option>';
			}else{
				$output .= '<option value="'.$regions->regionid.'">'.$regions->region_name.'</option>';
				}
		}
		echo $output;
	}
	
	/* 
	 * Adding User Page 
	 * loading country
	 */
	public function loadCountrys($region_id)
	{
	    $output = '';
		
		$sql = 'SELECT countryid,country_name FROM '.$this->cfg['dbpref'].'region r INNER JOIN '.$this->cfg['dbpref'].'country c ON r.regionid = c.regionid WHERE c.regionid IN('.$region_id.')';
		$country_query = $this->db->query($sql);
		foreach ($country_query->result() as $countrys)
		{
		    if($cid == $countrys->countryid)
				$output .= '<option value="'.$countrys->countryid.'" selected = "selected">'.$countrys->country_name.'</option>';
			else 
				$output .= '<option value="'.$countrys->countryid.'">'.$countrys->country_name.'</option>';
			
		}
		echo $output;
	}
	
	public function editloadCountrys($regionid,$uid)
	{
	    $output = '';
		$cs = $this->db->query("select country_id from ".$this->cfg['dbpref']."levels_country where user_id='{$uid}'");
		$c_ids = $cs->result();	
		$user_con = array();
		foreach($c_ids as $con_id) {
			$user_con[] = $con_id->country_id;
		}		
		$country_query = $this->db->query('SELECT countryid,country_name FROM '.$this->cfg['dbpref'].'country WHERE regionid IN('.$regionid.')');
		foreach ($country_query->result() as $countries)
		{	
			
			if(in_array($countries->countryid,$user_con)){
				$output .= '<option value="'.$countries->countryid.'" selected = "selected" >'.$countries->country_name.'</option>';
			}else{
				$output .= '<option value="'.$countries->countryid.'">'.$countries->country_name.'</option>';
				}
		}
		echo $output;
		
	}
	/* 
	 * Adding User Page 
	 * loading state
	 */
	public function loadStates($country_id)
	{
	    $output = '';
		$sql = 'SELECT stateid,state_name FROM '.$this->cfg['dbpref'].'state r INNER JOIN '.$this->cfg['dbpref'].'country c ON r.countryid = c.countryid WHERE c.countryid IN('.$country_id.')';
		$state_query = $this->db->query($sql);
		foreach ($state_query->result() as $states)
		{
			if($sid == $states->stateid)
				$output .= '<option value="'.$states->stateid.'" selected = "selected">'.$states->state_name.'</option>';
			else
			    $output .= '<option value="'.$states->stateid.'">'.$states->state_name.'</option>';
		}
		echo $output;
	}
	
	public function editloadStates($countryid,$uid)
	{
	    $output = '';
		$ss = $this->db->query("select state_id from ".$this->cfg['dbpref']."levels_state where user_id='{$uid}'");
		$s_ids = $ss->result();	
		$user_stat = array();
		foreach($s_ids as $sta_id) {
			$user_stat[] = $sta_id->state_id;
		}			
		$state_query = $this->db->query('SELECT stateid,state_name FROM '.$this->cfg['dbpref'].'state WHERE countryid IN('.$countryid.')');
		foreach ($state_query->result() as $states)
		{	
			
			if(in_array($states->stateid,$user_stat)){
				$output .= '<option value="'.$states->stateid.'" selected = "selected" >'.$states->state_name.'</option>';
			}else{
				$output .= '<option value="'.$states->stateid.'">'.$states->state_name.'</option>';
				}
		}
		echo $output;
		
	}
	/* 
	 * Adding User Page 
	 * loading location
	 */
	public function loadLocations($state_id)
	{
	    $output = '';
		$sql = 'SELECT locationid,location_name FROM '.$this->cfg['dbpref'].'location r INNER JOIN '.$this->cfg['dbpref'].'state c ON r.stateid = c.stateid WHERE c.stateid IN('.$state_id.')';
		$location_query = $this->db->query($sql);
		foreach ($location_query->result() as $location)
		{
		    if($loc_id == $location->locationid)	
				$output .= '<option value="'.$location->locationid.'" selected = "selected">'.$location->location_name.'</option>';
			else 
				$output .= '<option value="'.$location->locationid.'">'.$location->location_name.'</option>';
		}
		echo $output;
	}
	
	public function editloadLocations($state_id,$uid)
	{
	    $output = '';
		$ls = $this->db->query("select location_id from ".$this->cfg['dbpref']."levels_location where user_id='{$uid}'");
		$l_ids = $ls->result();	
		$user_loc = array();
		foreach($l_ids as $l_id) {
			$user_loc[] = $l_id->location_id;
		}		
		$location_query = $this->db->query('SELECT locationid,location_name FROM '.$this->cfg['dbpref'].'location WHERE stateid IN('.$state_id.')');
		foreach ($location_query->result() as $locations)
		{	
			
			if(in_array($locations->locationid,$user_loc)){
				$output .= '<option value="'.$locations->locationid.'" selected = "selected" >'.$locations->location_name.'</option>';
			}else{
				$output .= '<option value="'.$locations->locationid.'">'.$locations->location_name.'</option>';
				}
		}
		echo $output;
		
	}
	
	public function checkcountry() 
	{
		$region_load = $_POST['region_load']; 
		$explode_region = explode(',',$region_load);
		
		$country_load = $_POST['country_load'];
		$explode_country = explode(',',$country_load);
		
		for($i=0;$i<count($explode_region);$i++) {
			$check_country_query = $this->user_model->checkcountrylevel3($explode_region[$i],$country_load);
			if($check_country_query == 0) {
				$json['msg'] = 'noans'; 
				break;
			} else {
				$json['msg'] = 'success';
			}
		}
		//$array_regionload[] = explode(',',$region_load); 
		//print_r($array_regionload);
		echo json_encode($json); exit;
	}
	
	public function checkstate() 
	{
		$region_load = $_POST['region_load'];
		$explode_region = explode(',',$region_load);

		$country_load = $_POST['country_load'];
		$explode_country = explode(',',$country_load);
		
		$state_load = $_POST['state_load'];
		$explode_state = explode(',',$state_load);
		
		for($i=0;$i<count($explode_region);$i++) {
			$check_country_query = $this->user_model->checkcountrylevel3($explode_region[$i],$country_load);
			if($check_country_query == 0) {
				$json['countrymsg'] = 'noans'; 
				break;
			} else {
				$json['countrymsg'] = 'success';
			}
		}
		for($j=0;$j<count($explode_country);$j++) {
			$check_state_query = $this->user_model->checkstatelevel4($explode_country[$j],$state_load);
			if($check_state_query == 0) {
				$json['statemsg'] = 'nostate'; 
				break;
			} else {
				$json['statemsg'] = 'success';
			}
		}
		echo json_encode($json); exit;
	}
	
	public function checklocation() 
	{
		$region_load = $_POST['region_load'];
		$explode_region = explode(',',$region_load);

		$country_load = $_POST['country_load'];
		$explode_country = explode(',',$country_load);
		
		$state_load = $_POST['state_load'];
		$explode_state = explode(',',$state_load);
		
		$location_load = $_POST['location_load'];
		$explode_location = explode(',',$location_load);
		
		for($i=0;$i<count($explode_region);$i++) {
			$check_country_query = $this->user_model->checkcountrylevel3($explode_region[$i],$country_load);
			if($check_country_query == 0) {
				$json['countrymsg'] = 'noans'; 
				break;
			} else {
				$json['countrymsg'] = 'success';
			}
		}
		for($j=0;$j<count($explode_country);$j++) {
			$check_state_query = $this->user_model->checkstatelevel4($explode_country[$j],$state_load);
			if($check_state_query == 0) {
				$json['statemsg'] = 'nostate'; 
				break;
			} else {
				$json['statemsg'] = 'success';
			}
		}
		for($k=0;$k<count($explode_state);$k++) {
			$check_location_query = $this->user_model->checklocationlevel5($explode_state[$k],$location_load);
			if($check_location_query == 0) {
				$json['locationmsg'] = 'noloc'; 
				break;
			} else {
				$json['locationmsg'] = 'success';
			}
		}
		echo json_encode($json); exit;
	}

}

?>
