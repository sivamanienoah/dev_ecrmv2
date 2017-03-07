<?php

/********************************************************************************
File Name       : create_timesheet_user.php
Created Date    : 06/03/2017
Modified Date   : 06/03/2017
Created By      : Dhanapal.P
Modified By     : Dhanapal.P
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

class Create_timesheet_user extends crm_controller 
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
		
		
		$timesheet_db = $this->load->database('timesheet', TRUE);
		
		$timesheet_db->select('*');
		$timesheet_db->from($timesheet_db->dbprefix('user'));	
		$timesheet_db->where('authentication','ldb');		
		$timesheet_db->where('status','ACTIVE');	
		$timesheet_db->where('username != ','admin');	
		$timesheet_db->where('email_address != ','');	
		$time_sheet_query = $timesheet_db->get();
		$timesheet_users  = $time_sheet_query->result_array();
		
		foreach($timesheet_users as $eusers){
			//1.check whether the username exists or not in CRM DB.
			$this->db->select('u.username,u.email');
			$this->db->from($this->cfg['dbpref'].'users as u');
			$this->db->where('u.username',$eusers['username']);
			$query = $this->db->get();
			$res = $query->row_array();
			
			if($query->num_rows() == 0) { 
			//check email
				if(!in_array($eusers['email'], $crm_email)) {
					//insert into crm db
					$data = array(
					   'role_id' => 8,
					   'first_name' => $eusers['first_name'],
					   'last_name' => $eusers['last_name'],
					   'username' => $eusers['username'],
					   'emp_id' => $eusers['uid'],
					   'password' => $eusers['password'],
					   'email' => $eusers['email_address'],
					   'phone' => '',
					   'mobile' => '',
					   'level' => 1,
					   'auth_type' => 0,
					   'signature' => '',
					   'inactive' => 0
					);
					if($this->db->insert($this->cfg['dbpref'].'users', $data)) {
						$user_success[] = $eusers['uid'].' => '.$eusers['username'];
						$crm_email[] = $eusers['email_address'];
					}
				} else {
					//econnect user cannot be created. Email already exist.
					$user_failed[] = $eusers['uid'].' => '.$eusers['username']." => Email ID already exists. This user cannot be created.";
				}
			} else {
				if(strtolower($eusers['email_address']) != $res['email_address']) {
					//econnect user cannot be created. username already exists.
					$user_failed[] = $eusers['uid'].' => '.$eusers['username']." => User Name already exists. This user cannot be created.";
				}
			}
		}
		
		//Sending email to CRM Admin
		if(!empty($user_success)){
			$from		  	 = 'webmaster@enoahprojects.com';
			$arrayEmails   	 = $this->config->item('crm');
			$to				 = implode(',', $arrayEmails['crm_admin']);
			$print_fancydate = date('l, jS F y h:iA');
			
			$subject		 = 'New User List from Timesheet';
			
			$user_list  = '<table border=1>';
			$user_list .= '<tr><th style="font-family: Arial,Helvetica,sans-serif; font-size:12px">Emp ID</th><th style="font-family: Arial,Helvetica,sans-serif; font-size:12px">UserName</th></tr>';
			foreach($user_success as $users) {
				list($empid, $username) = explode("=>", $users);
				$user_list .= '<tr><td style="font-family: Arial,Helvetica,sans-serif; font-size:12px">'.$empid.'</td><td style="font-family: Arial,Helvetica,sans-serif; font-size:12px">'.$username.'</td></tr>';
			}
			$user_list .= '</table>';
			
			echo $user_list;
			
			//email sent by email template
			$param = array();
			
			$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_list'=>$user_list);

			$param['to_mail'] 		  = $to;
			//$param['bcc_mail'] 		  = 'ssriram@enoahisolution.com';
			$param['template_name']	  = 'New User List from Timesheet';
			$param['subject'] 		  = $subject;

			$this->load->model('email_template_model');
			$this->email_template_model->sent_email($param);
		}
		if(!empty($user_failed)){
			$from		  	 = 'webmaster@enoahprojects.com';
			$arrayEmails   	 = $this->config->item('crm');
			$to				 = implode(',',$arrayEmails['crm_admin']);
			$print_fancydate = date('l, jS F y h:iA');
			
			$subject		 = 'Failed User List from Timesheet';
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
			//$param['bcc_mail'] 		  = 'ssriram@enoahisolution.com';
			$param['template_name']	  = 'Failed User List from Timesheet';
			$param['subject'] 		  = $subject;

			$this->load->model('email_template_model');
			$this->email_template_model->sent_email($param);
		}	
	}

}
?>