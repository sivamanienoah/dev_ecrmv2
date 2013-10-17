<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Production extends crm_controller {

	public $cfg;
	
	public function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('text');
		$this->load->library('email');
		$this->email->initialize($cfg);
		$this->email->set_newline("\r\n");
	}
    
    public function index()
    {
		
    }
	
	public function set_project_lead($jobid, $userid, $previous_manager_id)
	{
		$data['error'] = FALSE;
		$user_det = array();
		$pm_det = array();
		$previous_manager = $this->db->query('SELECT email,first_name,last_name FROM '.$this->cfg['dbpref'].'users WHERE userid ='.$previous_manager_id);
		//$val = $_GET['previous_manager']; echo $val;
		//$pm_info = $previous_manager->result();
		foreach($previous_manager->result() as $pm_info)
		{
			$pm_det['email'] = $pm_info->email;
			$pm_det['first_name'] = $pm_info->first_name;
			$pm_det['last_name'] = $pm_info->last_name;
		}
		$pemail = $pm_det['email']; 
		$pfirst_name = $pm_det['first_name']; 
		$plast_name = $pm_det['last_name']; 
		$pmp_name = $pfirst_name . " " . $plast_name;
		$job_query = $this->db->query('SELECT job_title FROM '.$this->cfg['dbpref'].'jobs WHERE jobid ='.$jobid);
		$job_title = $job_query->result(); 
		$project = $job_title[0]->job_title; 
		if(!empty($pm_det))
		{
			$this->sent_to_manager($pemail, $pfirst_name, $project, $mail_type = "old_manager");
		}
		$user_query = $this->db->query('SELECT email,first_name,last_name FROM '.$this->cfg['dbpref'].'users WHERE userid ='.$userid);
		foreach($user_query->result() as $user_detail)
		{
			$user_det['email'] = $user_detail->email;
			$user_det['first_name'] = $user_detail->first_name;
			$user_det['last_name'] = $user_detail->last_name;
			
		}
		//print_r($user_det); exit;
		 $email = $user_det['email']; 
		 $first_name = $user_det['first_name']; 
		 $last_name = $user_det['last_name']; 
		 $pm_name = $first_name ." ". $last_name;
		//echo $email;
		
		$this->sent_to_manager($email, $first_name, $project, $mail_type = "new_manager");
		
		
		/* if (!empty($pm_det)) {
			$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs) 
		VALUES('".$jobid."','".$this->userdata['userid']."','".date('Y-m-d H:i:s')."','".$pm_name." is assigned as Project Manager.' ,'')"; 
		} else {
			$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs) 
		VALUES('".$jobid."','".$this->userdata['userid']."','".date('Y-m-d H:i:s')."','".$pm_name." is re-assigned as Project Manager.' ,'')"; 
		} 
		$qlogs = $this->db->query($logs); */
		
		if ($userid == 0)
		{	
			$data['error'] = 'User must be selected!';
		}
		else
		{
			$this->db->update($this->cfg['dbpref'].'jobs', array('assigned_to' => $userid), array('jobid' => $jobid));
		}
		
		echo json_encode($data);
	 
	}
	
	public function sent_to_manager($email, $first_name, $project_name, $mail_type) 
	{	  
		$log_email_content = '';
		$log_email_content .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Template</title>
<style type="text/css">
body { margin: 0px; }
</style>
</head>

<body>
<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
<tr><td bgcolor="#FFFFFF">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td style="padding:15px; border-bottom:2px #5a595e solid;">
		<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
	</td>
  </tr>
  <tr>
    <td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td>
  </tr>

  <tr>
    <td>
	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
    <p style="background: none repeat scroll 0 0 #4B6FB9;
    border-bottom: 1px solid #CCCCCC;
    color: #FFFFFF;
    margin: 0;
    padding: 4px;">
        <span>Hi </span>&nbsp;'.$first_name.',</p>
    <p style="padding: 4px;">';
	if($mail_type == "new_manager")
	{
		$log_email_content .= '<p style="font-family:Arial, Helvetica, sans-serif; margin-left:18px;">You have been assigned as the Project Manager for the project - '.$project_name.'</p><br />';
	}
	else 
	{
		$log_email_content .= '<p style="font-family:Arial, Helvetica, sans-serif; margin-left:18px;">You are moved from this project - '.$project_name.'</p><br />';
	}
	$log_email_content .='
		&nbsp;&nbsp;&nbsp;Regards<br />
		&nbsp;&nbsp;&nbsp;Webmaster
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
		
		if($mail_type == "new_manager")
		{
			$log_subject = 'New Project Assignment Notification';
		}
		else
		{
			$log_subject = 'Project Removal Notification';
		}
		
		//$set = $this->get_user_mail(); 
		$send_to = $email;
		$this->email->from('webmaster@enoahisolution.com','Webmaster');
		$this->email->to($send_to);
		$this->email->subject($log_subject);
		$this->email->message($log_email_content);
		//$this->email->attach($pdf_file_attach);
		if($this->email->send()){
			 echo $successful .= 'This log has been emailed to:<br />'.$send_to;
		}
	}
	
	public function set_project_id($jobid, $pjtId)
	{
		$data['error'] = FALSE;

		if ($pjtId == "")
		{
			$data['error'] = 'Id must not be Null value!';
		}
		else
		{
			$this->db->update($this->cfg['dbpref'].'jobs', array('pjt_id' => $pjtId), array('jobid' => $jobid));
		}

		echo json_encode($data);
	}
	
	public function set_project_value($jobid, $pjtVal)
	{
		$data['error'] = FALSE;

		if ($pjtVal == "")
		{
			$data['error'] = 'Value must not be Null value!';
		}
		else
		{
			$this->db->update($this->cfg['dbpref'].'jobs', array('actual_worth_amount' => $pjtVal), array('jobid' => $jobid));
		}

		echo json_encode($data);
	}
	
	public function set_project_status_date($jobid, $date_status, $date)
	{	//echo $jobid . ' ' . $date_status . ' ' . $date; exit;
		$data['error'] = FALSE;

		$timestamp = strtotime($date);
		
		if ($date_status != 'start' && $date_status != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($date_status == 'start')
			{	
				$this->db->where('jobid',$jobid);
				$this->db->where('date_due <', date('Y-m-d H:i:s', $timestamp));
				$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
				if($query == 1) { 
						$data['error'] = 'Planned Project Start Date Must be Equal or Earlier than the Planned Project End Date!';
				} else {
				$update['date_start'] = date('Y-m-d H:i:s', $timestamp);
				$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
				}
			}
			else
			{	
				if ($date_status == 'end') {
					$dt = date('Y-m-d H:i:s', $timestamp);
					$chk_dt = $this->db->query(" SELECT * FROM (`".$this->cfg['dbpref']."jobs`) WHERE `jobid` = '".$jobid."' ");
					$check_dt = $chk_dt->row_array();
					//echo $check_dt['date_start']; exit;
					if (isset($check_dt['date_start'])) {
						if($check_dt['date_start'] > $dt) {
						$data['error'] = 'Planned Project End Date Must be Equal or Later than the Planned Project Start Date!';
						} else {
						$update['date_due'] = $dt;
						$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
						}
					} else {
						$data['error'] = 'Planned Project Start Date Must be Filled!';
					}
				}
			}
			//$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
		}
		echo json_encode($data);
	}
	
	public function actual_set_project_status_date($jobid, $date_status, $date)
	{
		//echo $jobid . ' ' . $date_status . ' ' . $date; exit;
		
		$data['error'] = FALSE;
		
		$timestamp = strtotime($date);
		
		if ($date_status != 'start' && $date_status != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($date_status == 'start')
			{	
				/* $this->db->where('jobid',$jobid);
				$this->db->where('date_start >', date('Y-m-d H:i:s', $timestamp));
				$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
				if($query == 1) { 
					$data['error'] = 'Actual Project Start Date Must be Equal or Later than the Planned Project Start Date!';
				} else {
					$update['actual_date_start'] = date('Y-m-d H:i:s', $timestamp);
					$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
				} */
				
				$dt = date('Y-m-d H:i:s', $timestamp);
				$chk_act_dt = $this->db->query(" SELECT * FROM (`".$this->cfg['dbpref']."jobs`) WHERE `jobid` = '".$jobid."' ");
				$check_act_dt = $chk_act_dt->row_array();
				// echo $check_act_dt['actual_date_due']; 
				// echo"<br />"; echo $dt; exit;
				if (isset($check_act_dt['date_start'])) {
					if($check_act_dt['date_start'] > $dt) {
						$data['error'] = 'Actual Project Start Date Must be Equal or Later than the Planned Project Start Date!';
					} else {
						if (isset($check_act_dt['actual_date_due'])) {
							if ($check_act_dt['actual_date_due'] < $dt) {
								$data['error'] = 'Actual Project Start Date Must be Equal or Earlier than the Actual Project End Date!';
							}
						} else {
						//echo "update";
							$update['actual_date_start'] = $dt;
							$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
						}
					}
				} else {
					$data['error'] = 'Planned Project Start Date Must be Filled!';
				}
				
			}
			else
			{	
				if ($date_status == 'end') {
					/* $this->db->where('jobid',$jobid);
					$this->db->where('actual_date_start >', date('Y-m-d H:i:s', $timestamp));
					$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
					if($query == 1) { 
						$data['error'] = 'Actual Project End Date Must be Equal or Later than the Actual Project Start Date!';
					} else {
						$update['actual_date_due'] = date('Y-m-d H:i:s', $timestamp);
						$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
					} */
					
					$dt = date('Y-m-d H:i:s', $timestamp);
					$chk_act_end_dt = $this->db->query(" SELECT * FROM (`".$this->cfg['dbpref']."jobs`) WHERE `jobid` = '".$jobid."' ");
					$check_act_end_dt = $chk_act_end_dt->row_array();
					if (isset($check_act_end_dt['actual_date_start'])) {
						if($check_act_end_dt['actual_date_start'] > $dt) {
							$data['error'] = 'Actual Project End Date Must be Equal or Later than the Actual Project Start Date!';
						} else {
							$update['actual_date_due'] = $dt;
							$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
						}
					} else {
						$data['error'] = 'Actual Project Start Date Must be Filled!';
					}
				}		
			}
			//$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
		}
		echo json_encode($data);
	}
	
	public function set_proposal_date($jobid, $date_status, $date)
	{
		$data['error'] = FALSE;
		
		$timestamp = strtotime($date);
		
		if ($date_status != 'start' && $date_status != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($date_status == 'start')
			{
				$update['proposal_expected_date'] = date('Y-m-d H:i:s', $timestamp);
			}
			else
			{
				$update['proposal_sent_date'] = date('Y-m-d H:i:s', $timestamp);
			}
			
			$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
			
		}
		
		echo json_encode($data);
	}
	
	//unwanted function
	public function get_pm_profile($profile_id = 0)
	{
		$json['path'] = '';
		if (is_file(dirname(FCPATH) . '/assets/img/profiles/' . $profile_id . '-s.jpg'))
		{
			$json['path'] = base_url() . '/assets/img/profiles/' . $profile_id . '-s.jpg';
		}
		
		echo json_encode($json);
	}
	
	public function set_project_status() {
		$data['error'] = FALSE;
		
		if ($_POST['pjt_stat'] == "") {
			$data['error'] = 'Value must not be Null value!';
		} else {
			switch ($_POST['pjt_stat'])
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
			if ($this->db->update($this->cfg['dbpref'].'jobs', array('pjt_status' => $_POST['pjt_stat']), array('jobid' => $_POST['job_id'])))
				$ins['userid_fk'] = $this->userdata['userid'];
				$ins['jobid_fk'] = $_POST['job_id'];
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = "Status Change:\n" . urldecode($log_status);
				// inset the new log
				$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
		}
		echo json_encode($data);
	}
    
}
