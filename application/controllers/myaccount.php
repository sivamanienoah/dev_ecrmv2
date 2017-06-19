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
		$this->load->model('email_template_model');
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
		$rules['signature']  = "trim|required";
		$this->validation->set_rules($rules);
		
		$fields['first_name']  = "First Name";
		$fields['last_name']   = "Last Name";
		$fields['email']       = "Email Address";
        $fields['phone']       = "Telephone";
        $fields['mobile'] 	   = "Mobile";
		$fields['oldpassword'] = "oldPassword";
		$fields['password']    = "Password";
		$fields['pass_conf']   = "Password Confirmation";
		$fields['signature']   = "Email Signature";
		
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
			
			if(($this->input->post('password')) != "") {
				if (strlen($this->input->post('password'))>=6) {
					if (($this->input->post('password')) == ($this->input->post('pass_conf'))) {
						if ($userdata[0]['password'] == sha1($this->input->post('oldpassword'))) {
							unset($update_data['pass_conf'], $update_data['oldpassword']);
							$update_data['password'] = sha1($this->input->post('password'));
							// update query
							if ($this->user_model->update_user($customer['userid'], $update_data)) {
								// echo $this->db->last_query(); die;
								/* $new = $this->user_model->get_user($customer['userid']);
								$this->session->set_userdata('logged_in_user', $new[0]); */
								$this->session->set_flashdata('confirm', array('User details updated!'));
								redirect('userlogin/logout/true');
							}
							redirect('myaccount/'); 
						} else {
							$this->session->set_flashdata('login_errors', array('Your OldPassword is wrong!'));
							redirect('myaccount');
						}
					} else {
						$this->session->set_flashdata('login_errors', array('Password & Password confirmation mismatch!'));
						redirect('myaccount');
					}
				} else {
					$this->session->set_flashdata('login_errors', array('Your Password must be atleast more than six characters!'));
					redirect('myaccount');
				}
			} else {
				 unset($update_data['password'], $update_data['pass_conf'], $update_data['oldpassword']);
				 //update query
			
				if ($this->user_model->update_user($customer['userid'], $update_data))
				{
					$new = $this->user_model->get_user_det($customer['userid']);
					$this->session->set_userdata('logged_in_user', $new);
					
					$user_name 			 = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate	 = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$from    	  = $this->userdata['email'];
					$arrEmails 	  = $this->config->item('crm');
					$arrSetEmails = $arrEmails['director_emails'];
					
					$admin_mail = implode(',',$arrSetEmails);
					$subject    = 'User Profile Changes Notification';
					
					//email sent by email template
					$param = array();
					$param['email_data']	  = array('print_fancydate'=>$print_fancydate,'first_name'=>$update_data['first_name'],'last_name'=>$update_data['last_name'],'user_name'=>$user_name,'signature'=>$this->userdata['signature']);
					$param['to_mail']		  = $admin_mail;
					$param['from_email']	  = $from;
					$param['from_email_name'] = $user_name;
					$param['template_name']	  = "User Profile Changes Notification";
					$param['subject']		  = $subject;

					$this->email_template_model->sent_email($param);

					$this->session->set_flashdata('confirm', array('User details updated!'));
				}
				redirect('myaccount');
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
