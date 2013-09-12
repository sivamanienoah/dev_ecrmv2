<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request extends CI_Controller {

	public $cfg;
	public $userdata;
	
	function __construct()
	{
		parent::__construct();
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
	}
    
    function index()
    {

    }
    
    function set_flash_data($str, $type = 'header_messages')
    {
        $this->session->set_flashdata($type, array($str));
    }
	
	/**
	 * Delete a file, or a folder and its contents (recursive algorithm)
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.3
	 * @link        http://aidanlister.com/repos/v/function.rmdirr.php
	 * @param       string   $dirname    Directory to delete
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	function rmdirr($dirname)
	{
		// Sanity check
		if (!file_exists($dirname)) {
			return false;
		}
		
		// Simple delete for a file
		if (is_file($dirname) || is_link($dirname)) {
			return unlink($dirname);
		}
		
		// Loop through the folder
		$dir = dir($dirname);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			// Recurse
			$this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		}
		
		// Clean up
		$dir->close();
		return rmdir($dirname);
	}
	
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function file_upload($jobid, $date, $upby, $type = 'job')
	{	
	
		/**
		 * we need to know errors
		 * not the stupid ilisys restricted open_base_dir errors
		 */
		//error_reporting(E_ERROR);
		
		$json['error'] = '';
		$json['msg'] = '';
		
		$dir_type = ($type == 'lead') ? '/vps_lead_data/' : '/vps_data/';
		
		$f_dir = dirname(FCPATH) . $dir_type . $jobid;
		
		if (!is_dir($f_dir))
		{
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		if (isset($_FILES['ajax_file_uploader']) && is_uploaded_file($_FILES['ajax_file_uploader']['tmp_name']))
		{
			$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['ajax_file_uploader']['name']);
			
			if (preg_match('/\.(php|js|exe)+$/', $f_name, $matches)) // basic sanity
			{
				$json['error'] = TRUE;
				$json['msg'] = "You uploaded a file type that is not allowed!\nYour file extension : {$matches[1]}";
			}
			else // good to go
			{
				// full path
				$full_path = $f_dir . '/' . $f_name;
				if (is_file($full_path))
				{
					$f_name = time() . $f_name;
					$full_path = $f_dir . '/' . $f_name;
				}
				
				move_uploaded_file($_FILES['ajax_file_uploader']['tmp_name'], $full_path);
				
				$fz = filesize($full_path);
				$kb = 1024;
				$mb = 1024 * $kb;
				if ($fz > $mb)
				{
				  $out = round($fz/$mb, 2);
				  $out .= 'Mb';
				}
				else if ($fz > $kb) {
				  $out = round($fz/$kb, 2);
				  $out .= 'Kb';
				} else {
				  $out = $fz . ' Bytes';
				}
				
				$json['error'] = FALSE;
			
				
				$json['msg'] = "File successfully uploaded!";
				$json['file_name'] = $f_name;
				$json['file_size'] = $out;
				$json['date'] = $date;
				
			
			}
		}
		else
		{
			$json['error'] = serialize($_FILES);
		}
		
		echo json_encode($json);
		
	}
	
	/**
	 * Deletes a file based on a Ajax post request
	 */
	public function file_delete()
	{
		$userdata = $this->session->userdata('logged_in_user');
	$userid = $userdata['userid']; 
	$ex = explode('/',urldecode($_POST['file_path'])); 
	$jobid = $ex[2]; 
	$filename = $ex[3]; //filename
		if (isset($_POST['file_path']))
		{
			$path = dirname(FCPATH) . urldecode($_POST['file_path']);
			if (is_file($path))
			{
				if (@unlink($path))
				{
				    
					$json['error'] = FALSE;
					$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs) 
					VALUES('".$jobid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is deleted.' ,'".$filename."')"; 		
					$qlogs = $this->db->query($logs);
				}
				else
				{
					$json['error'] = TRUE;
				}
			}
			else
			{
				$json['error'] = TRUE;
			}
		}
		else
		{
			$json['error'] = TRUE;
		}
		echo json_encode($json);
	}
	
	/**
	 * Get all logs for a particular job;
	 */
    public function logs($jobid)
	{
		$this->db->where('jobid_fk', $jobid);
		$this->db->order_by('date_created', 'desc');
		$logs = $this->db->get($this->cfg['dbpref'] . 'logs');
		
		if ($logs->num_rows() > 0)
		{
			$log_data = $logs->result_array();
			$this->load->helper('url');
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			$data['log_html'] = '';
			
			foreach ($log_data as $ld)
			{
				
				$this->db->where('userid', $ld['userid_fk']);
				$user = $this->db->get($this->cfg['dbpref'] . 'users');
				$user_data = $user->result_array();
				
				$log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
				
				$fancy_date = date('d-m-Y H:i:s', strtotime($ld['date_created']));
				
				$table = <<< HDOC
<div class="log">
<p class="data">
	<span>{$fancy_date}</span>
{$user_data[0]['first_name']} {$user_data[0]['last_name']}
</p>
<p class="desc">
	{$log_content}
</p>
</div>
HDOC;
				$data['log_html'] .= $table;
				unset($table, $user_data, $user, $log_content);
			}
			
			echo '<div class="log-container">', $data['log_html'], '</div>';
		}
		else
		{
			echo 'No logs available';
		}
	}
	
	public function get_new_logs($jobid, $datetime)
	{
		$this->db->where('jobid_fk', $jobid);
		$this->db->order_by('date_created', 'desc');
		$this->db->limit(1);
		$logs = $this->db->get($this->cfg['dbpref'] . 'logs');
		
		if ($logs->num_rows() > 0)
		{
			$log_data = $logs->result_array();
			$ld = $log_data[0];
			
			if (strtotime($ld['date_created']) > strtotime($datetime))
			{
				$this->load->helper('url');
				
				$data['log_html'] = '';
				
				$this->db->where('userid', $ld['userid_fk']);
				$user = $this->db->get($this->cfg['dbpref'] . 'users');
				$user_data = $user->result_array();
				
				$log_content = nl2br(auto_link($ld['log_content'], 'url', TRUE));
				
				$fancy_date = date('d-m-Y H:i:s', strtotime($ld['date_created']));
				
				$table = <<< HDOC
<div class="log" style="display:none;">
<p class="data">
	<span>{$fancy_date}</span>
{$user_data[0]['first_name']} {$user_data[0]['last_name']}
</p>
<p class="desc">
	{$log_content}
</p>
</div>
HDOC;
				$data['log_html'] .= $table;
				
				$data['error'] = FALSE;
				
				echo json_encode($data);
			}
			
		}
	}
	
	public function add_url_tojob()
	{
		if (isset($_POST['jobid']) && isset($_POST['url']))
		{
			$jobid = $_POST['jobid'];
			$url = $_POST['url'];
			
			$ins['content'] = (isset($_POST['content'])) ? $_POST['content'] : '';
			$ins['url'] = urldecode($url);
			
			$data['error'] = FALSE;
			
			if ( $userdata = $this->session->userdata('logged_in_user') )
			{
				if (!filter_var($ins['url'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
				{
					$data['error'] = "Please enter a valid URL!";
					echo json_encode($data);
					return FALSE;
				}
				else
				{
					$this->load->helper('url');
					
					$ins['jobid_fk'] = $jobid;
					$ins['userid_fk'] = $userdata['userid'];
					$ins['date'] = date('Y-m-d H:i:s');
					$this->db->insert('crm_job_urls', $ins);
					
					$insid = $this->db->insert_id();
					
					$html = '<li>';
				
						$html .= '<a href="#" onclick="ajaxDeleteJobURL(' . $insid . ', this); return false;" class="file-delete">delete URL</a>';
					
					$html .= '<span>' . auto_link(htmlentities($ins['url'])) . '</span><p>' . htmlentities($ins['content'], ENT_QUOTES) . '</p></li>';
					
					$data['html'] = $html;
					
					echo json_encode($data);
				}
			}
			else
			{
				$data['error'] = "You required to be logged in!";
				echo json_encode($data);
				return FALSE;
			}
		}
		else
		{
			$data['error'] = "Invalid request!";
			echo json_encode($data);
			return FALSE;
		}
	}
	
	public function delete_url($id)
	{
		if ($this->db->delete('crm_job_urls', array('urlid' => $id)))
		{
			$data['error'] = FALSE;
		}
		else
		{
			$data['error'] = TRUE;
		}
		echo json_encode($data);
	}
	
	/**
	 * Update the job status
	 * based on the request
	 */
	function update_job_status()
	{
		$json['error'] = FALSE;
		$jobid = $_GET['jobid'];
		$job_status = $_GET['job_status'] * 10;
		
		if (!is_numeric($jobid) || $job_status % 10 != 0 || $job_status > 100)
		{
			$json['error'] = 'Invalid details supplied!';
		}
		else
		{
			$this->db->where('jobid', $jobid);
			$this->db->update('crm_jobs', array('complete_status' => $job_status)); 
		}
		echo json_encode($json);
	}
	
	/**
	 * Add job task for a user
	 * Edits a task
	 * Adds a random task for a user
	 */
function add_job_task($update = 'NO', $random = 'NO')
	{	
		$this->load->model('user_model');
		$this->load->library('email');
		$this->email->initialize($config);
		$errors = array();
		
		if ($random != 'NO')
		{
			//$_POST['jobid'] = 0;
		}
		
		$json['error'] = FALSE;
		if($update == 'NO') {
		$ins['jobid_fk'] = (int) $_POST['jobid'];
		}
		$ins['task'] = $_POST['job_task'];
		$ins['userid_fk'] = $_POST['task_user'];
		$ins['hours'] = (int) $_POST['task_hours'];
		$ins['mins'] = (int) $_POST['task_mins'];
		$ins['remarks'] = $_POST['remarks'];
		if($update == 'NO') {
		//$ins['approved'] = ($this->userdata['is_pm'] == 1 ) ? 1 : 0;
		$ins['approved'] = 1;
		}
		if($update == 'NO') {
			$ins['created_by'] = $this->userdata['userid'];
		}			
		$ins['created_on'] = date('Y-m-d H:i:s');
		
		$task_start_date = explode('-', trim($_POST['task_start_date']));
		$task_end_date = explode('-', trim($_POST['task_end_date']));
		
		
		if (count($task_start_date) != 3 || ! $start_date = mktime(0, 0, 0, $task_start_date[1], $task_start_date[0], $task_start_date[2]))
		{
			$errors[] = 'Invalid Start Date!';
		}
		
		if (count($task_end_date) != 3 || ! $end_date = mktime(0, 0, 0, $task_end_date[1], $task_end_date[0], $task_end_date[2]))
		{
			$errors[] = 'Invalid End Date!';
		}
				
		$time_range = array(
							'10:00:00'	=> '10:00AM',
							'11:00:00'	=> '11:00AM',
							'12:00:00'	=> '12:00PM',
							'13:00:00'	=> '1:00PM',
							'14:00:00'	=> '2:00PM',
							'15:00:00'	=> '3:00PM',
							'16:00:00'	=> '4:00PM',
							'17:00:00'	=> '5:00PM',
							'18:00:00'	=> '6:00PM',
							'19:00:00'	=> '7:00PM'
						 );
		
		/*if (isset($_POST['task_end_hour']) && ! array_key_exists($_POST['task_end_hour'], $time_range))
		{
			$errors[] = 'Invalid task end time!';
		}*/
		
		if ($this->userdata['is_pm'] != 1)
		{
			if ($start_date < strtotime(date('Y-m-d')) && $update == 'NO')
			{
				$errors[] = 'Start date cannot be earlier than today!';
			}
			
			/*if ($end_date < strtotime(date('Y-m-d')))
			{
				$errors[] = 'End date cannot be earlier than today!';
			}	*/		
			
		}
		
		if ($end_date < $start_date)
		{
			$errors[] = 'End date cannot be earlier than start date';
		}
		
		/*if ($ins['jobid_fk'] == 0 && $random == 'NO')
		{
			$errors[] = $ins['jobid_fk'];
			$errors[] = 'Valid jobid is required!';
		}*/
		
		/*if ($update != 'NO')
		{
			$errors[] = 'Only the production manager can edit the tasks!';
		}*/
		
		if (count($errors) > 0)
		{
			$json['error'] = TRUE;
			$json['errormsg'] = implode("\n", $errors);
		}
		else
		{
			$ins['start_date'] = date('Y-m-d H:i:s', $start_date);
			$ins['end_date'] = date('Y-m-d H:i:s', $end_date);
			
			
			$dtask_start_date=date('d-m-Y H:i:s', $start_date);
			$dtask_end_date=date('d-m-Y H:i:s' , $end_date);
			if (isset($_POST['task_end_hour']))
			{
				$ins['end_date'] = date('Y-m-d H:i:s', $end_date);
			}
			
			$ins['require_qc'] = (isset($_POST['require_qc']) && $_POST['require_qc'] == 'YES') ? '1' : '0';
			$ins['priority'] = (isset($_POST['priority']) && $_POST['priority'] == 'YES') ? '1' : '0';
			
			if ($update != 'NO' && $old_task = $this->get_task($update))
			{
			//$vard = $_POST['actualstart_date'];
			//echo $vard;
			//exit;
				//mychanges
				$updatedby = $this->user_model->updatedby($old_task->taskid);
				$ins['created_by'] = $updatedby[0]['created_by'];				
					
					$task_actualstart_date = explode('-', trim($_POST['actualstart_date']));
					if (count($task_actualstart_date) != 3 || ! $actualtask_date = mktime(0, 0, 0, $task_actualstart_date[1], $task_actualstart_date[0], $task_actualstart_date[2]))
						{
							$errors[] = 'Invalid Actual Start Date!';
						}
				if($_POST['actualstart_date'] =='0000-00-00'){
				$ins['actualstart_date']='0000-00-00 00:00:00';
				} else {
				$ins['actualstart_date'] = date('Y-m-d H:i:s', $actualtask_date);
				}
						//$ins['actualstart_date'] = date('Y-m-d H:i:s', $actualtask_date); //- sriram
	
				//ends
				//update
				//echo "<pre>"; print_r($ins); exit;
				$this->db->where('taskid', $update);
				$this->db->update('crm_tasks', $ins);
				
				//echo $this->db->last_query();exit;
				$ins['user_label'] = $_POST['user_label'];
				$ins['status'] = $ins['is_complete'] = 0;
				$ins['taskid'] = $update;
				$ins['userid'] = $ins['userid_fk'];
				$taskowner = $this->user_model->get_user($ins['userid']);
				$taskAssignedTo=$taskowner[0]['first_name'].'&nbsp;'.$taskowner[0]['last_name'];
				$taskAssignedToEmail=$taskowner[0]['email'];
				$hm="&nbsp;".$ins['hours']."&nbsp;Hours&nbsp;".$ins['mins']."&nbsp;Mins";

				$json['html'] = $this->format_task($ins);
				
				# add a record
				$record['taskid_fk'] = $old_task->taskid;
				$record['event'] = 'Task Update';
				$record['date'] = date('Y-m-d H:i:s');
				$record['event_data'] = json_encode($old_task);
				$this->db->insert('crm_tasks_track', $record);
				$this->email->initialize($config);
				$from_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				
				$admin_mail=implode(',',$arrSetEmails);
				$subject = 'New Task Update Notification';
				$from=$this->userdata['email'];
				
				$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				
				$task_owner_name = $this->db->query("SELECT u.first_name,u.last_name,t.remarks
													FROM `crm_tasks` AS t, `crm_users` AS u
													WHERE u.userid = t.created_by
													AND t.taskid ={$update}");
					$task_owners = $task_owner_name->result_array();

				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				$email_body_task_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
					.task-list-item, .task-list-item td {
					border: 1px solid #666666;
					border-collapse: collapse;
					}
					.task-list-item {
						margin-bottom: 10px;
						width: 500px;
					}
					#set-job-task .task-list-item td {
						padding: 5px;
					}
					#set-job-task td {
						padding: 0 5px 0 0;
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
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Update Notification</h3></td>
					  </tr>

					  <tr>
						<td>
						<table cellspacing="0" align="center" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666;
					border-collapse: collapse;">
						<tbody><tr>
						<td width="80" valign="top" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Task
						</td>
						<td class="task" colspan="3" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FEF08D;
    color: #333333; padding: 5px;"><a href='.$this->config->item('base_url').'/tasks/all>'.$_POST['job_task'].'</a>
						</td>
						</tr>
						<tr>
						<td  style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated to
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$taskAssignedTo.'</td>
						<td width="80" style="border: 1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;" >
						Remarks
						</td>
						<td rel="2:0" class="item hours-mins " style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$task_owners[0]['remarks'].'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Planned Start Date
						</td>
						<td class="item start-date" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$dtask_start_date.'
						</td>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Planned End Date
						</td>
						<td class="item end-date" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$dtask_end_date.'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated&nbsp;by:
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$task_owners[0]['first_name'].' '.$task_owners[0]['last_name'].'</td>
						<td width="80" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5;">
						Status
						</td>
						<td rel="2:0"  style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px; padding: 5px;">
						'.$ins['status'].'%'.'
						</td>
						</tr>
						</tbody></table>
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
				
				
				
				$this->email->from($from,$from_name);
				$this->email->to($taskAssignedToEmail.','.$admin_mail);
				$this->email->subject($subject);
				$this->email->message($email_body_task_content);
				$this->email->send();
				
				
				
				
			}
			else if ($update == 'NO')
			{
				if ( ! $this->db->insert('crm_tasks', $ins))
				{
					$json['error'] = TRUE;
					$json['errormsg'] = 'Task insert error';
				}
				else
				{
					
					$ins['user_label'] = $_POST['user_label'];
					$ins['status'] = $ins['is_complete'] = 0;
					$ins['taskid'] = $this->db->insert_id();
					$ins['userid'] = $ins['userid_fk'];
					$json['html'] = $this->format_task($ins);
					
					
					$creator = $this->user_model->get_user($this->userdata['userid']);
					$creator = $creator[0];
					$task_owner = $this->user_model->get_user($ins['userid_fk']);
					//$task_owner = $task_owner[0];
					$taskSetTo=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
					$taskSetToEmail=$task_owner[0]['email'];
					$hm="&nbsp;".$ins['hours']."&nbsp;Hours&nbsp;".$ins['mins']."&nbsp;Mins";
					$job_url = ($ins['jobid_fk'] != 0) ? $this->config->item('base_url')."welcome/view_quote/{$ins['jobid_fk']}" : '';
					$task_owner_name = $this->db->query("SELECT u.first_name,u.last_name,t.remarks
													FROM `crm_tasks` AS t, `crm_users` AS u
													WHERE u.userid = t.created_by
													AND t.taskid ={$ins['taskid']}");
					$task_owners = $task_owner_name->result_array();
		
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					$email_body_task_content ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
					.task-list-item, .task-list-item td {
					border: 1px solid #666666;
					border-collapse: collapse;
					}
					.task-list-item {
						margin-bottom: 10px;
						width: 500px;
					}
					#set-job-task .task-list-item td {
						padding: 5px;
					}
					#set-job-task td {
						padding: 0 5px 0 0;
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
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Task Notification</h3></td>
					  </tr>

					  <tr>
						<td>
						<table cellspacing="0" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666;
					border-collapse: collapse;">
						<tbody><tr>
						<td width="80" valign="top" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Task Desc
						</td>
						<td class="task" colspan="3" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FEF08D;
    color: #333333; padding: 5px;"><a href='.$this->config->item('base_url').'tasks/all>'.$_POST['job_task'].'</a>
						</td>
						</tr>
						<tr>
						<td  style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated to
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$taskSetTo.'</td>
						<td width="80" style="border: 1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;" >
						Remarks
						</td>
						<td rel="2:0" class="item hours-mins " style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$task_owners[0]['remarks'].'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Planned Start Date
						</td>
						<td class="item start-date" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$dtask_start_date.'
						</td>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Planned End Date
						</td>
						<td class="item end-date" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$dtask_end_date.'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated&nbsp;by:
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$task_owners[0]['first_name'].''.$task_owners[0]['last_name'].'</td>
						<td width="80" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5;">
						Status
						</td>
						<td rel="2:0"  style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px; padding: 5px;">
						'.$ins['status'].'%'.'
						</td>
						</tr>
						</tbody></table>
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
				
					$this->email->initialize($config);

					$subject = 'New Task Notification';
					$from = $this->userdata['email'];;

					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$admin_mail=implode(',',$arrSetEmails);
					$this->email->from($from,$user_name);
					$this->email->to($taskSetToEmail.','.$admin_mail );
					$this->email->subject($subject);
					$this->email->message($email_body_task_content);
					$this->email->send();	

				}
			}
			else
			{
				$json['error'] = TRUE;
				$json['errormsg'] = 'Task insert or edit error';
			}
		}
		
		echo json_encode($json);
	}
	
	/* getting a single task */
	function get_task($taskid)
	{
		$this->db->where('taskid', $taskid);
		$q = $this->db->get('crm_tasks');		
		if ($q->num_rows() > 0)
		{
			return $q->row();
		}
		else
		{
			return FALSE;
		}
	}
	
	/* getting a single task */
	function get_lead_task($taskid)
	{
		$this->db->where('taskid', $taskid);
		$q = $this->db->get('crm_lead_tasks');
		
		if ($q->num_rows() > 0)
		{
			return $q->row();
		}
		else
		{
			return FALSE;
		}
	}
	
	/* get tasks without jobs */
	function get_random_tasks()
	{
		$html = '';
		
		if ( ! isset($_POST['id_set']) || ! preg_match('/[0-9,]+/', $_POST['id_set']))
		{
			$html = '';
		}
		else
		{
			$sql = "SELECT *, `crm_tasks`.`start_date` AS `start_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`
					FROM `crm_tasks`, `crm_users`
					WHERE `crm_tasks`.`taskid` IN ({$_POST['id_set']})
					AND `crm_tasks`.`userid_fk` = `crm_users`.`userid` 
					
					ORDER BY `crm_tasks`.`is_complete`, `crm_tasks`.`status`, `crm_tasks`.`start_date`";
					
			$q = $this->db->query($sql);
			$data = $q->result_array();
			
			foreach ($data as $row)
			{
				$html .= $this->format_task($row);
			}
		}
		if ($html == '')
		{
			$html = '<p class="task-notice">Sorry, there are no tasks set for this project!</p>';
		}
		echo $html;
	}
	
	/**
	 * Get tasks for a given job
	 */
	 //SELECT *, `crm_tasks`.`start_date` AS `start_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label` FROM `crm_tasks`, `crm_users`	WHERE `crm_tasks`.`userid_fk` = {$uid} OR `crm_tasks`.`created_by` = {$uid}	
	 //AND `crm_tasks`.`userid_fk` = `crm_users`.`userid` ORDER BY `crm_tasks`.`is_complete`, `crm_tasks`.`status`, `crm_tasks`.`start_date`
	function get_job_tasks($jobid)
	{	
		$uidd = $this->session->userdata['logged_in_user'];
		$uid = $uidd['userid'];
		$sql = "SELECT *, `crm_tasks`.`start_date` AS `start_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`
				FROM `crm_tasks`, `crm_users`
				WHERE `crm_tasks`.`jobid_fk` = ?
				AND `crm_tasks`.`userid_fk` = `crm_users`.`userid`
				ORDER BY `crm_tasks`.`is_complete`, `crm_tasks`.`status`, `crm_tasks`.`start_date`";				
		$q = $this->db->query($sql, array('jobid_fk' => $jobid));
		//echo $this->db->last_query();exit;
		$data = $q->result_array();	

		$html = '';
		foreach ($data as $row)
		{		
			$html .= $this->format_task($row);
		}
		
		if ($html == '')
		{
			$html = '<p class="task-notice">Sorry, there are no tasks set for this project!</p>';
		}
		
		echo $html;
	}
	
	/**
	 * format the output HTML for a given task
	 * Changes made for
	 * Only the task owner has got the rights to re-assign the task to another user.
	 * Any other user who has got the same level of access, will still not be able to re-assign the tasks
	 * Task assigned To person will not be able to change the task description, planned start date, planned end date, actual end date.
	 */
	private function format_task($array, $type = 'job')
	{
		$uidd = $this->session->userdata['logged_in_user'];
		$uid = $uidd['userid'];
		$lead_assigns = $this->db->query("SELECT userid,first_name FROM {$this->cfg['dbpref']}users ");
		$data['lead_assign'] = $lead_assigns->result_array();
		
		$res = array();
		
		$taskid = nl2br($array['taskid']);
		//$task_desk = nl2br($array['task']);
		$sqltask="select userid_fk,created_by,status from crm_tasks where taskid='$taskid'";
		$rssqltask=mysql_query($sqltask);
		$rows=mysql_fetch_array($rssqltask);
		$taskuid=$rows['userid_fk'];
		$taskcid=$rows['created_by'];
		$taskstatus = $rows['status'];
		if($uid==$taskcid){
			$task_desk = nl2br($array['task']);
			$taskread="";
		} else {
			$task_desk = nl2br($array['task']);
			$taskread ="readonly";
		}
		$select1 = "SELECT crm_users.first_name,crm_users.userid FROM crm_users WHERE crm_users.userid=".$taskuid;	
		$dd1 = $this->db->query($select1);
		$res1 = $dd1->result();
		
		$task_remarks = nl2br($array['remarks']);
		$select = "SELECT crm_users.first_name,crm_users.userid FROM crm_users WHERE crm_users.userid=".$array['created_by'];	
		$dd = $this->db->query($select);
		$res = $dd->result();
		#$$html = $this->session->set_userdata('taskownerid', $res[0]->userid);		
		if (!isset($array['user_label']))
		{
			$array['user_label'] = '';
		}
		
		$own_task = $task_edit = $task_approve = '';
		
		//echo $this->userdata['role_id'];
			$options = array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100);
			$opts = '';
			foreach ($options as $o)
			{
				$sel = ($array['status'] == $o) ? ' selected="selected"' : '';
				$opts .= "<option value=\"{$o}\"{$sel}>{$o}%</option>";
			}
			
			
				$task_edit = "<button type=\"submit\" onclick=\"openEditTask('{$array['taskid']}'); return false;\">Edit Task</button>";
				$task_approve = ($array['approved'] == 0) ? "<button type=\"submit\" onclick=\"approveTask('{$array['taskid']}'); return false;\">Approve Task</button>" : '';
			
			if($array['userid'] == $this->userdata['userid']) {			
			$own_task =  <<< EOD
			<select name="set_task_status_{$array['taskid']}" id="set_task_status_{$array['taskid']}" class="set-task-status">
				{$opts}
			</select>
			<div class="buttons">
				<button type="submit" onclick="setTaskStatus('{$array['taskid']}'); return false;">Set Status</button>
				{$task_edit}
			</div>		
EOD;
}

		if ($array['created_by'] == $array['userid'] &&  $array['userid'] == $this->userdata['userid']) {
		$own_task =  <<< EOD
			<select name="set_task_status_{$array['taskid']}" id="set_task_status_{$array['taskid']}" class="set-task-status">
				{$opts}
			</select>
			<div class="buttons">
				<button type="submit" onclick="setTaskStatus('{$array['taskid']}'); return false;">Set Status</button>
				
			</div>		
EOD;
		
		}

		
		$is_admin = '';
		if ($array['created_by'] == $this->userdata['userid'])
		{
			$is_admin = ($array['is_complete'] == 1) ? 'Task Complete!' : <<< EOD
			<a href="#" class="delete-task"onclick="setTaskStatus('{$array['taskid']}', 'delete'); return false;">Delete?</a>
			<div class="buttons">
				<button type="submit" class="positive" onclick="setTaskStatus('{$array['taskid']}', 'complete'); return false;">Approve</button>
				{$task_edit}
			</div>
EOD;
		}
		$isprior='';$priority=0;
		if(!empty($array['priority'])) 
		if($array['priority']==1 && $array['status']!= 100) $isprior=' style="background-color:purple;color:white;"';
		$is_complete = ($array['is_complete'] == 1) ? ' completed' : '';
		$marked_100pct = ($array['status'] == 100) ? ' marked_100pct' : '';
		if(!empty($array['priority'])) $priority=$array['priority'];
		
		if($uid==$taskcid){
			$start_date = date('d-m-Y', strtotime($array['start_date']));
			$starttaskread="";
		} else {
			$start_date = date('d-m-Y', strtotime($array['start_date']));
			$starttaskread ="read";
		}
		//$end_date = date('d-m-Y', strtotime($array['end_date']));
		if($uid==$taskcid){
			$end_date = date('d-m-Y', strtotime($array['end_date']));
			$endtaskread="";
		} else {
			$end_date = date('d-m-Y', strtotime($array['end_date']));
			$endtaskread ="read";
		}
		$end_time = date('gA', strtotime($array['end_date']));
		
		/*mychanges*/
		$actualstart_date=$array['actualstart_date'];
        if($actualstart_date == '0000-00-00 00:00:00') {
			$actualstart_date = '0000-00-00';
		} else {
			if($actualstart_date =='') {
				$actualstart_date='0000-00-00';
			} else {
				$actualstart_date=date('d-m-Y', strtotime($array['actualstart_date']));
			}
		}
		
		if($uid==$taskcid) {
			$actualend_date=$array['actualend_date'];
			if($actualend_date == '0000-00-00 00:00:00') {
				$actualend_date = '0000-00-00';
				$actualend_dateread="";
			} else {
				if($actualend_date == '') {
				$actualend_date='0000-00-00';
				$actualend_dateread="";
				} else {
					$actualend_date=date('d-m-Y', strtotime($array['actualend_date']));
					$actualend_dateread="";
				}
			}
		} else {
			if($actualend_date == '0000-00-00 00:00:00') {
				$actualend_date = '0000-00-00';
				$actualend_dateread ="read";
			} else {
				if($actualend_date == '') {
					$actualend_date='0000-00-00';
					$actualend_dateread="read";
				} else {
					$actualend_date=date('d-m-Y', strtotime($array['actualend_date']));
					$actualend_dateread ="read";
					}
				}
		}
		
		if($uid==$taskcid){
			//$pl_sel = '';			
			//$taskuserid= '<option value="1">' . $array['user_label'] . '</option>';
			
			$taskuserid=$array['user_label'];
			$taskuserid_read="";
		} else {
			$taskuserid=$array['user_label'];
			$taskuserid_read ="read";
		}
				
		/*ends*/ 
		
		
		$qc_required = (isset($array['require_qc'])) ? $array['require_qc'] : '0';
		foreach($data['lead_assign'] as $val) {
			$val['userid'];
			$val['first_name'];
		}
			
		$html = <<< EOD
					<table border="0" cellpadding="0" cellspacing="0" class="task-list-item{$is_complete}{$marked_100pct}" id="task-table-{$array['taskid']}">						
						<tr>
							<td valign="top" width="80">
								Task Desc
							</td>
							<td colspan="3" class="task"{$isprior}>
								{$task_desk} 
							</td>
						</tr>
						
						<tr>
							<td valign="top" width="80">
								Task Owner
							</td>
							<td class="item task-owner">
								{$res[0]->first_name}
							</td>	
													
						</tr>
						
						<tr style="display:none;">
							<td valign="top" width="80" >
								User ID
							</td>
							<td class="task-uid" >
								{$uid}
							</td>	
													
						</tr>
						<tr style="display:none;">
							<td valign="top" width="80" >
								Assigned ID
							</td>
							<td class="task-cid" >
								{$taskcid}
							</td>	
													
						</tr>
						<tr>
							<td>
								Allocated to
							</td>
							<td class="item user-name" rel="{$array['userid']}" width="100">
								{$array['user_label']}
							</td>
							<td style="display:none" width="80">
								Hours
							</td>
							
						</tr>
						
						
						<tr>
							<td>
								Planned Start Date
							</td>
							<td class="item start-date">
								{$start_date}
							</td>
							<td>
								Planned End Date
							</td>
							<td class="item end-date">
								<span class="date_part">{$end_date}</span> 
							</td>
						</tr>
						
						<tr>
							<td>
								Actual Start Date
							</td>
							<td class="item actualstart-date">
								{$actualstart_date}
							</td>
							<td>
								Actual End Date
							</td>
							<td class="item actualend-date">
								{$actualend_date}
							</td>
						</tr>					
						<tr>
							<td>Status</td>
							<td class="item status-of-project">{$taskstatus}%</td>
						</tr>
						<tr>
							<td>Remarks</td>
							<td class="edit-task-remarks"><textarea class="taskremarks" readonly>{$task_remarks}</textarea></td>
						</tr>						
						
						<tr>
							<td colspan="2" valign="top">
								{$own_task}
								<span class="display-none task-require-qc">{$qc_required}</span>
								<span class="display-none priority">{$priority}</span>
							</td>
							<td colspan="2" valign="top">
								{$is_admin}
							</td>
						</tr>
							
					</table>
EOD;
		
		return ($html);
	}
	// Ends here
	
	function set_task_status($type = 'job')
	{
		$this->load->model('user_model');
		$task_table = 'crm_tasks';
		$fk = 'jobid_fk';
		if ($type == 'lead')
		{
			$task_table = 'crm_lead_tasks';
			$fk = 'leadid_fk';
		}
		
		$json['error'] = TRUE;
		$taskid = (isset($_POST['taskid'])) ? $_POST['taskid'] : 0;
		//mychanges
			$taskstat = $_POST['task_status'];	
			/*if($taskstat == 100) {
				$task_table = 'crm_tasks';
				$ud = array();
				$ud['status'] = 100;
				$ud['actualend_date'] = date('Y-m-d H:i:s');
				$this->db->where('taskid', $taskid);
				$this->db->update($task_table, $ud);			
			} */
		//mychanges ends
		$q = $this->db->get_where($task_table, array('taskid' => $taskid));
		
		if ($q->num_rows() > 0)
		{
			$data = $q->row();
			if (isset($_POST['set_as_complete']))
			{
				if ($data->status < 100)
				{
					$json['errormsg'] = 'Task status is not 100%';
				}
				else
				{
					$upd = array();
					$upd['is_complete'] = 1;
					$upd['marked_complete'] = date('Y-m-d H:i:s');
					$this->db->where('taskid', $taskid);
					$this->db->update($task_table, $upd);
					$uid=$data->userid_fk;
				$task_name=$data->task;
				if($upd['is_complete']==1){
						$task_status="Completed";
				}					
								$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];

				$task_owner = $this->user_model->get_user($uid);
				$taskSetTo=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
				$taskStatusToEmail=$task_owner[0]['email'];
				$start_date=$data->start_date;
				$end_date=$data->end_date;
				$hours=$data->hours;
				$mins=$data->mins;
				$hm=$hours.'&nbsp;Hours&nbsp;and&nbsp;'.$mins.'&nbsp;mins';
				$start_date=date('d-m-Y', strtotime($start_date));
				$end_date=date('d-m-Y', strtotime($end_date));
				$completed_date=date('l, jS F y h:iA', strtotime($upd['marked_complete']));
				$task_owner_name = $this->db->query("SELECT u.first_name,u.last_name,t.remarks
													FROM `crm_tasks` AS t, `crm_users` AS u
													WHERE u.userid = t.created_by
													AND t.taskid ={$taskid}");
				$task_owners = $task_owner_name->result_array();
				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				$task_email_content='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
					.task-list-item, .task-list-item td {
					border: 1px solid #666666;
					border-collapse: collapse;
					}
					.task-list-item {
						margin-bottom: 10px;
						width: 500px;
					}
					#set-job-task .task-list-item td {
						padding: 5px;
					}
					#set-job-task td {
						padding: 0 5px 0 0;
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
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Completed Notification</h3></td>
					  </tr>

					  <tr>
						<td>
						<table cellspacing="0" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666;
					border-collapse: collapse;">
						<tbody><tr>
						<td width="80" valign="top" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Task
						</td>
						<td class="task" colspan="3" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FEF08D;
    color: #333333; padding: 5px;">'.$task_name.'
						</td>
						</tr>
						<tr>
						<td  style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated to
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$taskSetTo.'</td>
						<td width="80" style="border: 1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;" >
						Remarks
						</td>
						<td rel="2:0" class="item hours-mins " style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$task_owners[0]['remarks'].'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Planned Start Date
						</td>
						<td class="item start-date" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$start_date.'
						</td>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Planned End Date
						</td>
						<td class="item end-date" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$end_date.'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated&nbsp;by:
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$task_owners[0]['first_name'].' '.$task_owners[0]['last_name'].'</td>
						<td width="80" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5;">
						Status
						</td>
						<td rel="2:0"  style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px; padding: 5px;">
						'.$task_status.'
						</td>
						</tr>
						</tbody></table>
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
				
					$subject='Task Completion Notification';
					$from = $this->userdata['email'];

					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$admin_mail=implode(',',$arrSetEmails);
					$this->email->from($from,$user_name);
					$this->email->to($taskStatusToEmail);
					$this->email->bcc($admin_mail);
					$this->email->subject($subject);
					$this->email->message($task_email_content);
					$this->email->send();	
					
					
					
					
					$json['set_complete'] = TRUE;
					$json['error'] = FALSE;
					
				}
			}
			else if (isset($_POST['delete_task']))
			{
				$data = $q->row();
				
			
					//print_r($data);exit;			
					$this->db->where('taskid', $taskid);
					$this->db->delete($task_table);
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$task_name=$data->task;
					$task_createdby=$data->created_by;
					$uid=$data->userid_fk;
					$task_owner = $this->user_model->get_user($task_createdby);
					$taskCreatedBy=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
					$taskCreatedByEmail=$task_owner[0]['email'];
					$task_allocated = $this->user_model->get_user($uid);
					$taskSetTo=$task_allocated[0]['first_name'].'&nbsp;'.$task_allocated[0]['last_name'];
					$taskSetEmail=$task_allocated[0]['email'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					$taskdelete_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Delete Notification Message
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
								<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;</p>
							<p style="padding: 4px;">'.
								$task_name.'&nbsp; has&nbsp;been&nbsp;declined&nbsp;by&nbsp;'.$user_name.'<br /><br />'.$this->userdata['signature'].'<br />
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
				$subject='Task Delete Notification';
				$this->email->from($from,$user_name);
				$this->email->to($taskSetEmail);
				$this->email->bcc($admin_mail);
				$this->email->subject($subject);
				$this->email->message($taskdelete_email_content);
				$this->email->send();	
				$json['error'] = FALSE;	
				$json['task_delete'] = TRUE;
	
			}
			else
			{
				$upd = array();
				$upd['status'] = (int) $_POST['task_status'];
				
				if ($upd['status'] == 100)
				{
				    $upd['actualend_date'] = date('Y-m-d H:i:s');
					$upd['marked_100pct'] = date('Y-m-d H:i:s');
					$json['marked_100pct'] = TRUE;
				}else { // Added For task set completion
					if($taskstat!='0'){
					$task_table = 'crm_tasks';
					$ud = array();
					$ud['status'] = $taskstat;
					$ud['actualstart_date'] = date('Y-m-d H:i:s');
					$this->db->where('taskid', $taskid);
					$this->db->update($task_table, $ud);
					}
				 }
				$this->db->where('taskid', $taskid);
				$this->db->update($task_table, $upd);
				$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$uid=$data->userid_fk;
				$task_createdby=$data->created_by;
				$start_date=$data->start_date;
				$end_date=$data->end_date;
				$hours=$data->hours;
				$mins=$data->mins;
				$hm=$hours.'&nbsp;Hours&nbsp;and&nbsp;'.$mins.'&nbsp;mins';
				$start_date=date('d-m-Y', strtotime($start_date));
				$end_date=date('d-m-Y', strtotime($end_date));
				$task_name=$data->task;
				//$task_status=$data->status;
				$task_status=$_POST['task_status'];
				$task_owner = $this->user_model->get_user($uid);
				$taskSetTo=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
				$taskStatusToEmail=$task_owner[0]['email'];
				$task_owner_mail = $this->db->query("SELECT u.email,u.first_name,u.last_name,t.remarks
													FROM `crm_tasks` AS t, `crm_users` AS u
													WHERE u.userid = t.created_by
													AND t.created_by ={$task_createdby}
													AND t.taskid ={$taskid}");
				$task_owners = $task_owner_mail->result_array();
				//echo $this->db->last_query(); exit;
				 //echo '********' . print_r($taskStatusToEmail); exit;
				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				$task_email_content='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
					.task-list-item, .task-list-item td {
					border: 1px solid #666666;
					border-collapse: collapse;
					}
					.task-list-item {
						margin-bottom: 10px;
						width: 500px;
					}
					#set-job-task .task-list-item td {
						padding: 5px;
					}
					#set-job-task td {
						padding: 0 5px 0 0;
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
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Status Notification</h3></td>
					  </tr>

					  <tr>
						<td>
						<table cellspacing="0" align="center" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666; border-collapse: collapse;">
						<tbody><tr>
						<td width="80" valign="top" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Task Desc
						</td>
						<td class="task" colspan="3" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FEF08D;
    color: #333333; padding: 5px;"><a href='.$this->config->item('base_url').'tasks/all>'.$task_name.'</a>
						</td>
						</tr>
						<tr>
						<td  style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated to
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$taskSetTo.'</td>
						<td width="80" style="border: 1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;" >
						Remarks
						</td>
						<td rel="2:0" class="item hours-mins " style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$task_owners[0]['remarks'].'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px; ">
						Planned Start Date
						</td>
						<td class="item start-date" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$start_date.'
						</td>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Planned End Date
						</td>
						<td class="item end-date" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$end_date.'
						</td>
						</tr>

						<tr>
						<td style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Allocated by:
						</td>
						<td width="100" rel="107" class="item user-name" style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">'.$task_owners[0]['first_name'].' '.$task_owners[0]['last_name'].'</td>
						<td width="80" style="border:1px solid #666666;
					border-collapse: collapse; background: none repeat scroll 0 0 #5F5F5; padding: 5px;">
						Status
						</td>
						<td rel="2:0"  style="border:1px solid #666666;
					border-collapse: collapse;background: none repeat scroll 0 0 #FDF7BB;
    color: #333333; padding: 5px;">
						'.$task_status.'%'.'
						</td>
						</tr>
						</tbody></table>
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
				$subject='Task Status Notification';
				$this->email->from($from,$user_name);
				$this->email->to($taskStatusToEmail.','.$task_owners[0]['email']);
				$this->email->bcc($admin_mail);
				$this->email->subject($subject);
				$this->email->message($task_email_content);
				$this->email->send();	
				$json['error'] = FALSE;
			}
		}
		else
		{
			$json['errormsg'] = 'Task does not exist';
		}
		
		echo json_encode($json);
	}
	
	/**
	 * Add job task for a user
	 * Edits a task
	 * Adds a random task for a user
	 */
	function add_lead_task($update = 'NO', $random = 'NO')
	{
		$this->load->model('user_model');
		
		$errors = array();
		
		if ($random != 'NO')
		{
			$_POST['leadid'] = 0;
		}
		
		$json['error'] = FALSE;
		$ins['leadid_fk'] = (int) $_POST['leadid'];
		$ins['task'] = $_POST['job_task'];
		$ins['userid_fk'] = $_POST['task_user'];
		$ins['hours'] = (int) $_POST['task_hours'];
		$ins['mins'] = (int) $_POST['task_mins'];
		
		$ins['approved'] = 1;
		
		$ins['created_by'] = $this->userdata['userid'];
		$ins['created_on'] = date('Y-m-d H:i:s');
		
		$task_start_date = explode('-', trim($_POST['task_start_date']));
		$task_end_date = explode('-', trim($_POST['task_end_date']));
		
		if (count($task_start_date) != 3 || ! $start_date = mktime(0, 0, 0, $task_start_date[1], $task_start_date[0], $task_start_date[2]))
		{
			$errors[] = 'Invalid Start Date!';
		}
		
		if (count($task_end_date) != 3 || ! $end_date = mktime(0, 0, 0, $task_end_date[1], $task_end_date[0], $task_end_date[2]))
		{
			$errors[] = 'Invalid End Date!';
		}
		
		if ($start_date < strtotime(date('Y-m-d')) && $update == 'NO')
		{
			$errors[] = 'Start date cannot be earlier than today!';
		}
		
		/*if ($end_date < strtotime(date('Y-m-d')))
		{
			$errors[] = 'End date cannot be earlier than today!';
		}*/
		
		if ($end_date < $start_date)
		{
			$errors[] = 'End date cannot be earlier than start date';
		}
		
		if ($ins['leadid_fk'] == 0 && $random == 'NO')
		{
			$errors[] = 'Valid leadid is required!';
		}
				
		/*if ($update != 'NO')
		{
			$errors[] = 'Only the production manager can edit the tasks!';
		}*/
		
		if (count($errors) > 0)
		{
			$json['error'] = TRUE;
			$json['errormsg'] = implode("\n", $errors);
		}
		else
		{
			$ins['start_date'] = date('Y-m-d H:i:s', $start_date);
			$ins['end_date'] = date('Y-m-d H:i:s', $end_date);
			$ins['priority'] = (isset($_POST['priority']) && $_POST['priority'] == 'YES') ? '1' : '0';
			if ($update != 'NO' && $old_task = $this->get_lead_task($update))
			{
				// update
				$this->db->where('taskid', $update);
				$this->db->update('crm_lead_tasks', $ins);
				
				$ins['user_label'] = $_POST['user_label'];
				$ins['status'] = $ins['is_complete'] = 0;
				$ins['taskid'] = $update;
				$ins['userid'] = $ins['userid_fk'];
				$json['html'] = $this->format_task($ins, 'lead');
			}
			else if ($update == 'NO')
			{
				if ( ! $this->db->insert('crm_lead_tasks', $ins))
				{
					$json['error'] = TRUE;
					$json['errormsg'] = 'Task insert error';
				}
				
			}
			else
			{
				$json['error'] = TRUE;
				$json['errormsg'] = 'Task insert or edit error';
			}
		}
		
		echo json_encode($json);
	}
    
    
	/**
	 * Get tasks for a given lead
	 */
	function get_lead_tasks($jobid)
	{
		$sql = "SELECT *, `crm_lead_tasks`.`start_date` AS `start_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`
				FROM `crm_lead_tasks`, `crm_users`
				WHERE `crm_lead_tasks`.`leadid_fk` = ?
				AND `crm_lead_tasks`.`userid_fk` = `crm_users`.`userid`
				ORDER BY `crm_lead_tasks`.`is_complete`, `crm_lead_tasks`.`status`, `crm_lead_tasks`.`start_date`";
				
		$q = $this->db->query($sql, array('jobid_fk' => $jobid));
		$data = $q->result_array();
		$html = '';
		foreach ($data as $row)
		{
			$html .= $this->format_task($row);
		}
		
		if ($html == '')
		{
			$html = '<p class="task-notice">Sorry, there are no tasks set for this lead!</p>';
		}
		
		echo $html;
	}
	
	function get_job_overview($jobid, $return = FALSE)
	{
		$this->db->order_by('due_date', 'asc');
		$this->db->order_by('position', 'asc');
		$q = $this->db->get_where('crm_milestones', array('jobid_fk' => $jobid));
		
		$rows = $q->result();
		
		$data = $this->job_overview_html($rows);
		
		if ($return)
		{
			return $data;
		}
		
		echo $data;
	}
	
	function save_job_overview($jobid)
	{
		$this->db->where('jobid_fk', $jobid);
		$this->db->delete('crm_milestones');
		
		$mc = count($_POST['milestone']);
		for ($i = 0; $i < $mc; $i++)
		{
			$date_parts = explode('-', $_POST['milestone_date'][$i]);
			if (trim($_POST['milestone'][$i]) == '' || count($date_parts) != 3 || ! $date = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]))
			{
				continue;
			}
			
			$ins['jobid_fk'] = $jobid;
			$ins['milestone'] = $_POST['milestone'][$i];
			$ins['due_date'] = date('Y-m-d', $date);
			$ins['status'] = $_POST['milestone_status'][$i];
			$ins['position'] = $i;
			
			$this->db->insert('crm_milestones', $ins);
		}
		
		echo $this->get_job_overview($jobid, TRUE);
	}
	
	function job_overview_html($object)
	{
		$html = '';
		foreach ($object as $row)
		{
			$status_select_1 = ($row->status == 1) ? ' selected="selected"' : '';
			$status_select_2 = ($row->status == 2) ? ' selected="selected"' : '';
			$qa = $this->db->query("select lead_assign, belong_to from crm_jobs where jobid = '".$row->jobid_fk."' ");
			$lead_details = $qa->row_array();
			//echo "<pre>"; print_r($lead_details);
			if ($this->userdata['role_id'] == 1 || $lead_details['belong_to'] == $this->userdata['userid'] || $lead_details['lead_assign'] == $this->userdata['userid']) {
			$html .= '
			<tr>
				<td class="milestone">
					<input type="text" name="milestone[]" class="textfield width250px" value="' . htmlentities($row->milestone, ENT_QUOTES) . '" />
				</td>
				<td class="milestone-date">
					<input type="text" name="milestone_date[]" class="textfield width80px pick-date" value="' . date('d-m-Y', strtotime($row->due_date)) . '"/>
				</td>
				<td class="milestone-status">
					<select name="milestone_status[]" class="textfield width80px">
						<option value="0">Scheduled</option>
						<option value="1"' . $status_select_1 . '>In Progress</option>
						<option value="2"' . $status_select_2 . '>Completed</option>
					</select>
				</td>
				<td class="milestone-action" valign="middle">
					&nbsp; <a href="#" onclick="removeMilestoneRow(this); return false;">Remove</a>
				</td>
			</tr>
			';
			} else {
				$html .= '
				<tr>
					<td class="milestone">
						<input type="text" name="milestone[]" class="textfield width250px" value="' . htmlentities($row->milestone, ENT_QUOTES) . '" />
					</td>
					<td class="milestone-date">
						<input type="text" name="milestone_date[]" class="textfield width80px pick-date" value="' . date('d-m-Y', strtotime($row->due_date)) . '"/>
					</td>
					<td class="milestone-status">
						<select name="milestone_status[]" class="textfield width80px">
							<option value="0">Scheduled</option>
							<option value="1"' . $status_select_1 . '>In Progress</option>
							<option value="2"' . $status_select_2 . '>Completed</option>
						</select>
					</td>
				</tr>
				';
			}
		}
		
		return $html;
	}
	
	function confirm_qc_check()
	{
		$data['jobid_fk'] = $_POST['jobid'];
		$data['is_complete'] = ($_POST['complete'] == 'yes') ? 1 : 0;
		$data['userid_fk'] = $this->userdata['userid'];
		$data['date'] = date('Y-m-d H:i:s');
		$data['qc_type'] = $_POST['qc_type'];
		$data['event_data'] = (isset($_POST['event_data'])) ? $_POST['event_data'] : NULL;
		
		$json['error'] = 'Record Insert Failed!';
		
		if ($this->db->insert('crm_quality_control', $data))
		{
			$json['error'] = FALSE;
		}
		
		echo json_encode($json);
	}
	
	function undo_qc_check()
	{
		$this->load->model('welcome_model');
		$is_complete = $this->welcome_model->get_qc_complete_status((int) $_POST['jobid'], (int) $_POST['qc_type']);
		
		if ($this->userdata['is_pm'] != 1 )
		{
			$json['error'] = 'Only the production manager OR the managing director can cancel the QC list!';
			echo json_encode($json);
			exit;
		}
		
		if ($is_complete !== FALSE && is_numeric($is_complete))
		{
			if ($this->welcome_model->unset_qc_complete_status($is_complete))
			{
				$json['error'] = FALSE;
			}
			else
			{
				$json['error'] = 'Record Update Failed!';
			}
		}
		else
		{
			$json['error'] = 'This job QC is not marked complete!';
		}
		
		echo json_encode($json);
	}
	function get_packages($hostingid=0){
		if($hostingid==0) return false;
		$q=$this->db->query("SELECT * FROM crm_hosting_package HP, crm_package P WHERE P.package_id=HP.packageid_fk && HP.hostingid_fk={$hostingid} && P.status='active'");
		$r=$q->result_array();
		if(sizeof($r)>0) echo json_encode($r[0]);
		else return false;
	}
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function query_file_upload($jobid, $lead_query, $status, $type = 'job')
	{	
		/**
		 * we need to know errors
		 * not the stupid ilisys restricted open_base_dir errors
		 */
		error_reporting(E_ERROR);
		
		$json['error'] = '';
		$json['msg'] = '';
		
		$dir_type = ($type == 'lead') ? '/vps_lead_data/' : '/vps_data/query/';
		
		$f_dir = dirname(FCPATH) . $dir_type . $jobid;
		
		if (!is_dir($f_dir))
		{
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		if (isset($_FILES['query_file']) && is_uploaded_file($_FILES['query_file']['tmp_name']))
		{
			$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['query_file']['name']);
			
			if (preg_match('/\.(php|js|exe)+$/', $f_name, $matches)) // basic sanity
			{
				$json['error'] = TRUE;
				$json['msg'] = "You uploaded a file type that is not allowed!\nYour file extension : {$matches[1]}";
			}
			else // good to go
			{
				// full path
				$full_path = $f_dir . '/' . $f_name;
				if (is_file($full_path))
				{
					$f_name = time() . $f_name;
					$full_path = $f_dir . '/' . $f_name;
				}
				
				if(move_uploaded_file($_FILES['query_file']['tmp_name'], $full_path)) {
					$qry = "SELECT first_name, last_name, email FROM ".$this->cfg['dbpref']."users WHERE userid=".$this->session->userdata['logged_in_user']['userid'];
					$users = $this->db->query($qry);
					$user = $users->result_array();

					$qry1 = "SELECT email_1 FROM ".$this->cfg['dbpref']."customers WHERE custid = (SELECT custid_fk FROM ".$this->cfg['dbpref']."jobs WHERE jobid=".$jobid.")";
					$customers = $this->db->query($qry1);
					$customer = $customers->result_array();
					if($status == 'query') {
						$st = $status;
						$rep_to = 0;
					} else {
						$status = explode('-',$status);
						$st = $status[0];
						$rep_to = $status[1];
					}
						$userdata = $this->session->userdata('logged_in_user');
					$lead_query = addslashes($lead_query);
					$query = "INSERT INTO ".$this->cfg['dbpref']."_lead_query (job_id,user_id,query_msg,query_file_name,query_sent_date,query_sent_to,query_from,status,replay_query) 
					VALUES(".$jobid.",'".$userdata['userid']."','".$lead_query."','".$f_name."','".date('Y-m-d H:i:s')."','".$customer[0]['email_1']."','".$user[0]['email']."','".$st."',".$rep_to.")";		
					$q = $this->db->query($query);
					
							$json['up_date'] = date('d-m-Y');
							$json['lead_query'] = $lead_query;
							$json['firstname'] = $user[0]['first_name'];
							$json['lastname'] = $user[0]['last_name'];
					//echo $this->db->last_query();
					/*if($q) {						
						$url = base_url();
						$attachment_url = $url.'vps_data/query/'.$jobid.'/'.$filename;
						$to = $customer[0]['email_1'];
						$subject = 'Query Lead Converstion';
						$from = $user[0]['email'];
						$from_name = $user[0]['first_name'];

						$this->load->plugin('phpmailer');
						$this->load->library('email');
						$this->email->initialize($config);
						$this->email->set_newline("\r\n");
						$this->email->from($from, $from_name);
						$this->email->to($to);
						$this->email->subject($subject);
						$this->email->message($msg);	
						//$this->email->AddAttachment($attachment_url);			
						$ok = $this->email->send();
						if($ok) {
							$json['up_date'] = date('d-m-Y');
							$json['lead_query'] = $lead_query;
							$json['firstname'] = $user[0]['first_name'];
							$json['lastname'] = $user[0]['last_name'];
							$json['mail_msg'] = "Successfully send the mail";	
						}
						else 
						$json['mail_msg'] = "Mail Sending Problem";
					}	*/
				
				}
				$fz = filesize($full_path);
				$kb = 1024;
				$mb = 1024 * $kb;
				if ($fz > $mb)
				{
				  $out = round($fz/$mb, 2);
				  $out .= 'Mb';
				}
				else if ($fz > $kb) {
				  $out = round($fz/$kb, 2);
				  $out .= 'Kb';
				} else {
				  $out = $fz . ' Bytes';
				}
				
				$json['error'] = FALSE;
				$json['msg'] = "File successfully uploaded!";
				$json['file_name'] = $f_name;			
				$json['file_size'] = $out;
				
			}
			
		}
		else 
		{
			$qry = "SELECT first_name, last_name, email FROM ".$this->cfg['dbpref']."users WHERE userid=".$this->session->userdata['logged_in_user']['userid'];
					$users = $this->db->query($qry);
					$user = $users->result_array();

					$qry1 = "SELECT email_1 FROM ".$this->cfg['dbpref']."customers WHERE custid = (SELECT custid_fk FROM ".$this->cfg['dbpref']."jobs WHERE jobid=".$jobid.")";
					$customers = $this->db->query($qry1);
					$customer = $customers->result_array();
					if($status == 'query') {
						$st = $status;
						$rep_to = 0;
					} else {
						$status = explode('-',$status);
						$st = $status[0];
						$rep_to = $status[1];
					}
						$userdata = $this->session->userdata('logged_in_user');
						$lead_query = addslashes($lead_query);
					$query = "INSERT INTO ".$this->cfg['dbpref']."_lead_query (job_id,user_id,query_msg,query_file_name,query_sent_date,query_sent_to,query_from,status,replay_query) 
					VALUES(".$jobid.",'".$userdata['userid']."','".$lead_query."','File Not Attached','".date('Y-m-d H:i:s')."','".$customer[0]['email_1']."','".$user[0]['email']."','".$st."',".$rep_to.")";		
					$q = $this->db->query($query);	
			
			//$query = "INSERT INTO ".$this->cfg['dbpref']."_lead_query (job_id,user_id,query_msg,query_file_name,query_sent_date,query_sent_to,query_from,status,replay_query) 
					//VALUES(".$jobid.",'59','".$lead_query."','File Not Attached','".date('Y-m-d H:i:s')."','abc@mail.com','cba@mail.com','query','0')";		
					//$q = $this->db->query($query);
					
			$json['up_date'] = date('d-m-Y');
			$json['lead_query'] = str_replace('\\', '', $lead_query);
			$json['firstname'] = $user[0]['first_name'];
			$json['lastname'] = $user[0]['last_name'];
			
			
		}
		
		
		echo json_encode($json);
	}
}
