<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * My Profile
 *
 * @class 		Myaccount
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @Menu        My Profile
 * @author 		eNoah
 * @Controller
 */

class Myaccount extends crm_controller {
    
	public $userdata;
	
	/*
	*@Constructor
	*@My Account
	*/
	
    public function Myaccount() {
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('user_model');
        $this->load->library('validation');
    }
    
	
	/*
	*@Login user Details
	*@Method   index
	*/
	
   public function index() {
	
        $rules['first_name'] = "trim|required";
		$rules['last_name']  = "trim|required";
		$rules['email']      = "trim|required|valid_email";
		$rules['add_email']  = "trim|valid_email";
		$rules['signature']  = "trim|required";
		$this->validation->set_rules($rules);
		
		$fields['first_name']      = "First Name";
		$fields['last_name']       = "Last Name";
		$fields['email']           = "Email Address";
		$fields['add_email']       = "Additional Email Address";
		$fields['use_both_emails'] = "Using Both Emails";
        $fields['phone']           = "Telephone";
        $fields['mobile']          = "Mobile";
		$fields['oldpassword']     = "oldPassword";
		$fields['password']        = "Password";
		$fields['pass_conf']       = "Password Confirmation";
		$fields['signature']       = "Email Signature";
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        $customer = $this->session->userdata('logged_in_user');
        $userdata = $this->user_model->get_user($customer['userid']);
		
        if (is_array($userdata) && count($userdata) > 0) 
		foreach ($userdata[0] as $k => $v)
		{
            if (isset($this->validation->$k)) $this->validation->$k = $v;
        }
        
        if ($this->validation->run() == false)
		{
            $this->load->view('user/account_view');
        }
		else
		{
            foreach($fields as $key => $val)
			{
                $update_data[$key] = $this->input->post($key);
            }
			
			if(($this->input->post('password')) != "")
			{	if (strlen($this->input->post('password'))>=6) {
					
				
				if (($this->input->post('password')) == ($this->input->post('pass_conf'))) {
					if ($userdata[0]['password'] == sha1($this->input->post('oldpassword'))) {
						unset($update_data['pass_conf'], $update_data['oldpassword']);
            
						$update_data['password'] = sha1($this->input->post('password'));
					// update query
					if ($this->user_model->update_user($customer['userid'], $update_data))
					{//echo $this->db->last_query();
						$new = $this->user_model->get_user($customer['userid']);
						$this->session->set_userdata('logged_in_user', $new[0]);
						
						$this->session->set_flashdata('confirm', array('User details updated!'));
						
					}
					redirect('myaccount/'); 
					}
					else {
						$this->session->set_flashdata('login_errors', array('Your OldPassword is wrong!'));
						redirect('myaccount');
					}
				}
				else {
					$this->session->set_flashdata('login_errors', array('Password & Password confirmation mismatch!'));
					redirect('myaccount');
				}
				}
				else {
					$this->session->set_flashdata('login_errors', array('Your Password must be atleast more than six characters!'));
					redirect('myaccount');
				}
				
			}
			else {
				 unset($update_data['password'], $update_data['pass_conf'], $update_data['oldpassword']);
             //update query
			
            if ($this->user_model->update_user($customer['userid'], $update_data))
			{
                $new = $this->user_model->get_user($customer['userid']);
                $this->session->set_userdata('logged_in_user', $new[0]);
				
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
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Profile Changes Notification 
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
							<p style="padding: 4px;"> User Profile Modified - '.$update_data['first_name']. '  '.$update_data['last_name']. '<br /><br />
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
		$subject='User Profile Changes Notification';
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		$this->email->from($from,$user_name);
		$this->email->to($admin_mail);
		$this->email->subject($subject);
		$this->email->message($log_email_content);

		$this->email->send();				
				
                
                $this->session->set_flashdata('confirm', array('User details updated!'));
                /*if ($this->input->post('update_password'))
				{
                    $this->session->set_flashdata('confirm', array('Your password updated!'));
                }*/
            }
            redirect('myaccount/');
			}
            
        }
        
    }
	
	/*
	*@Insert log record
	*@Method   add_log
	*/
	
	public function add_log()
	{
		$post_data           = real_escape_array($this->input->post());
		$ins['jobid_fk']     = 0;
		$ins['userid_fk']    = $this->userdata['userid'];
		$ins['log_content']  = $post_data['log_content'];
		$ins['time_spent']   = $post_data['time_spent'];
		$ins['date_created'] = date('Y-m-d H:i:s');
		$this->user_model->add_log($ins);
	}
    
}

?>
