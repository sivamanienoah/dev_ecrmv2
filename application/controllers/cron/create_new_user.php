<?php

/********************************************************************************
File Name       : create_new_user.php
Created Date    : 05/09/2014
Modified Date   : 09/09/2014
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Karthikeyan.S
*********************************************************************************/

/**
 * create_new_user
 *
 * @class 		Create_new_user
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */

class Create_new_user extends crm_controller 
{
    
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('text');
    }
	
	public function index() {
	
		$crm_email = array();
		$user_failed  = array();
		$user_success = array();
		
		//get crm users emails
		$this->db->select('u.email');
		$this->db->from($this->cfg['dbpref'].'users as u');
		$query = $this->db->get();
		$email_res = $query->result_array();
		
		if(!empty($email_res)) {
			foreach($email_res as $email){
				$crm_email[] = $email['email'];
			}
		}
		
		$this->db->select('v.id,v.username,v.empid,v.email,v.active,v.first_name,v.last_name');
		$this->db->from($this->cfg['dbpref'].'view_econnect_mas as v');
		$this->db->where('v.active',1);
		$this->db->where('v.username !=','');
		$sql = $this->db->get();
		//echo $this->db->last_query();
		$econnect_users = $sql->result_array();
		foreach($econnect_users as $eusers){
			//1.check whether the username exists or not in CRM DB.
			$this->db->select('u.username,u.email');
			$this->db->from($this->cfg['dbpref'].'users as u');
			$this->db->where('u.username',$eusers['username']);
			$query = $this->db->get();
			$res = $query->row_array();
			
			if($query->num_rows() == 0) {
				//check email
				if(!in_array($eusers['email'],$crm_email)) {
					//insert into crm db
					$data = array(
					   'role_id' => '1',
					   'first_name' => $eusers['first_name'],
					   'last_name' => $eusers['last_name'],
					   'username' => $eusers['username'],
					   'password' => sha1('admin123'),
					   'email' => $eusers['email'],
					   'phone' => '',
					   'mobile' => '',
					   'level' => 1,
					   'auth_type' => 1,
					   'signature' => '',
					   'inactive' => 0
					);
					if($this->db->insert($this->cfg['dbpref'].'users', $data)) {
						// $user_success[] = $eusers['empid'].' - '.$eusers['username']." - New user created.";
						$user_success[] = $eusers['empid'].' => '.$eusers['username'];
						$crm_email[] = $eusers['email'];
					}
				} else {
					//econnect user cannot be created. Email already exist.
					$user_failed[] = $eusers['empid'].' => '.$eusers['username']." => Email ID already exists. This user cannot be created.";
				}
				
			} else {
				if($eusers['email'] != $res['email']) {
					//econnect user cannot be created. username already exists.
					$user_failed[] = $eusers['empid'].' => '.$eusers['username']." => User Name already exists. This user cannot be created.";
				}
			}
		}
		
		//Sending email to CRM Admin
		if(!empty($user_success)){
			$from		  	 = 'webmaster@enoahisolution.com';
			$arrayEmails   	 = $this->config->item('crm');
			$to				 = implode(',',$arrayEmails['director_emails']);
			$print_fancydate = date('l, jS F y h:iA');
			
			$subject		 = 'New User List from eConnect';
			
			$user_list  = '<table border=1>';
			$user_list .= '<tr><th style="font-family: Arial,Helvetica,sans-serif; font-size:12px">Emp ID</th><th style="font-family: Arial,Helvetica,sans-serif; font-size:12px">UserName</th></tr>';
			foreach($user_success as $users) {
				list($empid, $username) = explode("=>", $users);
				$user_list .= '<tr><td style="font-family: Arial,Helvetica,sans-serif; font-size:12px">'.$empid.'</td><td style="font-family: Arial,Helvetica,sans-serif; font-size:12px">'.$username.'</td></tr>';
			}
			$user_list .= '</table>';
			//email sent by email template
			$param = array();
			
			$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_list'=>$user_list);

			$param['to_mail'] 		  = $to;
			// $param['cc_mail'] 		  = 'rshankar@enoahisolution.com';
			$param['from_email']	  = 'webmaster@enoahisolultion.com';
			$param['from_email_name'] = 'Webmaster';
			$param['template_name']	  = 'New User List from eConnect';
			$param['subject'] 		  = $subject;

			$this->load->model('email_template_model');
			$this->email_template_model->sent_email($param);
		}
		if(!empty($user_failed)){
			$from		  	 = 'webmaster@enoahisolution.com';
			$arrayEmails   	 = $this->config->item('crm');
			$to				 = implode(',',$arrayEmails['director_emails']);
			$print_fancydate = date('l, jS F y h:iA');
			
			$subject		 = 'Failed User List from eConnect';
			$user_lists  = '<table border=1>';
			$user_lists .= '<tr><th style="font-family: Arial,Helvetica,sans-serif; font-size:12px">Emp ID</th><th style="font-family: Arial,Helvetica,sans-serif; font-size:12px">UserName</th><th style="font-family: Arial,Helvetica,sans-serif; font-size:12px">Reason</th></tr>';
			foreach($user_failed as $faileduser) {
				list($empid, $username, $reason) = explode("=>", $faileduser);
				$user_lists .= '<tr><td style="font-family: Arial,Helvetica,sans-serif; font-size:12px">'.$empid.'</td><td style="font-family: Arial,Helvetica,sans-serif; font-size:12px">'.$username.'</td><td style="font-family: Arial,Helvetica,sans-serif; font-size:12px">'.$reason.'</td></tr>';
			}
			$user_lists .= '</table>';
			//email sent by email template
			$param = array();
			
			$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_list'=>$user_lists);

			$param['to_mail'] 		  = $to;
			// $param['cc_mail'] 		  = 'rshankar@enoahisolution.com';
			$param['from_email']	  = 'webmaster@enoahisolultion.com';
			$param['from_email_name'] = 'Webmaster';
			$param['template_name']	  = 'Failed User List from eConnect';
			$param['subject'] 		  = $subject;

			$this->load->model('email_template_model');
			$this->email_template_model->sent_email($param);
		}	
	}

}
?>