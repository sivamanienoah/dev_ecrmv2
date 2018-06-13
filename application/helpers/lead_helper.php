<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


	
if ( ! function_exists('getAccess'))
{
	function getAccess($mid, $rid)
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$CI->db->select('view');
		$CI->db->where('masterid', $mid);
		$CI->db->where('role_id', $rid);
		$sql = $CI->db->get($cfg['dbpref'].'master_roles');
		$res = $sql->row_array();
		// echo $CI->db->last_query();
		return $res;
	}	
}

if ( ! function_exists('getClientLogo') )
{
	function getClientLogo()
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$query = $CI->db->get($cfg['dbpref'].'client_logo');
		$num = $query->num_rows();
		// echo $CI->db->last_query(); exit;
		if ($num<1)
			return false;
		else 
			return $query->row_array();
	}
}

if ( ! function_exists('get_notify_status') )
{
	function get_notify_status($cid)
	{	
		$default_days = 7; // show notification default for leads & task
		
		$CI=get_instance();
		$userdata = $CI->session->userdata('logged_in_user');
		$cfg = $CI->config->item('crm'); // load config
		
		$CI->db->select('cn.onscreen_notify_status, cn.email_notify_status, cn.no_of_days');
		$CI->db->where('cn.cron_id', $cid);
		$CI->db->where('cn.userid', $userdata['userid']);
		$sql = $CI->db->get($cfg['dbpref'].'crons_notificatons as cn');
		$num = $sql->num_rows();
		// echo $CI->db->last_query();
		if ($num<1) {
			//return false;
			return $default_days;
		} else { 
			$res = $sql->row_array();
			if ($res['onscreen_notify_status'] == 1)
				return $res['no_of_days'];
			else 
				return false;
		}
	}
}

if ( ! function_exists('proposal_expect_end_msg') )
{
	function proposal_expect_end_msg($day)
	{
		$CI = get_instance();
		$userdata = $CI->session->userdata('logged_in_user');
		$cfg = $CI->config->item('crm'); // load config

		$CI->db->select('jb.lead_id, jb.lead_title, jb.proposal_expected_date as dt, DATEDIFF(jb.proposal_expected_date, CURDATE()) as datediff');
		$CI->db->where('jb.proposal_expected_date BETWEEN CURDATE() AND DATE(DATE_ADD(CURDATE(), INTERVAL '.$day.' DAY)) ');
		$CI->db->where('jb.lead_status', 1);
		// $CI->db->where('jb.lead_assign', $userdata['userid']);
		$CI->db->where("FIND_IN_SET('".$userdata['userid']."', jb.lead_assign)");
		$sql = $CI->db->get($cfg['dbpref'].'leads as jb');

		// echo $CI->db->last_query(); exit;
		
		$nums = $sql->num_rows();

		if ($nums<1)
			return false;
		else 
			return $sql->result_array();
	}
}

if (  ! function_exists('task_end_msg') )
{
	function task_end_msg($day)
	{
		$CI = get_instance();
		$userdata = $CI->session->userdata('logged_in_user');
		$cfg = $CI->config->item('crm'); // load config
		$today = date('Y-m-d'); 
		
		$CI->db->select('t.taskid, t.end_date, t.task');
		$CI->db->where('t.end_date BETWEEN CURDATE() AND DATE(DATE_ADD(CURDATE(), INTERVAL "'.$day.'" DAY)) ');
		$CI->db->where('t.actualend_date', '0000-00-00 00:00:00');
		$CI->db->where('t.userid_fk', $userdata['userid']);
		$sql1 = $CI->db->get($cfg['dbpref'].'tasks as t');
		
		// echo $CI->db->last_query(); 
		
		$res = $sql1->num_rows();

		if ($res<1)
			return false;
		else 
			return $sql1->result_array();
	}
}

if ( ! function_exists('check_max_users') )
{
	function check_max_users()
	{
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$CI->db->select('count(userid) as avail_users');
		$sql = $CI->db->get($cfg['dbpref'].'users');
		$num = $sql->num_rows();
		// echo $CI->db->last_query(); exit;
		if ($num<1)
			return false;
		else 
			return $sql->row_array();
	}
}

function get_del_access($id, $uid)
{
	$wh_condn = '(belong_to = '.$uid.' OR assigned_to ='.$uid.' OR FIND_IN_SET('.$uid.', lead_assign)) ';
	
	$CI = get_instance();
	$cfg = $CI->config->item('crm'); // load config
	
	$CI->db->select('lead_assign, assigned_to, belong_to');
	$CI->db->where('lead_id', $id);
	// $CI->db->where("(lead_assign = '".$uid."' || assigned_to = '".$uid."' || belong_to = '".$uid."')");
	$CI->db->where($wh_condn);
	$sql = $CI->db->get($cfg['dbpref'].'leads');
	$res = $sql->result_array();
	if (empty($res)) {
		$chge_access = 0;
	} else {
		$chge_access = 1;
	}
	return $chge_access;
}

function get_lead_assigne_names($user_id)
{
	$CI = get_instance();
	$cfg = $CI->config->item('crm'); // load config
	
	$CI->db->select('GROUP_CONCAT(CONCAT(u.first_name, " " , u.last_name) SEPARATOR ",") as assignees', FALSE);
	$CI->db->where_in('u.userid', @explode(',', $user_id));
	$sql = $CI->db->get($cfg['dbpref'].'users u');
	$res = $sql->row_array();
	echo $CI->db->last_query() . '<br>'; exit;
	return $res['assignees'];
}

function get_lead_assigne_email($user_id)
{
	$CI = get_instance();
	$cfg = $CI->config->item('crm'); // load config
	
	$CI->db->select('GROUP_CONCAT(u.email SEPARATOR ",") as emails', FALSE);
	$CI->db->where_in('u.userid', @explode(',', $user_id));
	$sql = $CI->db->get($cfg['dbpref'].'users u');
	$res = $sql->row_array();
	// echo $CI->db->last_query() . '<br>'; 
	return $res['emails'];
}

if ( ! function_exists('get_file_access'))
{
	function get_file_access($id, $uid)
	{
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$CI->db->select('*');
		$CI->db->where('jobid_fk', $id);
		$CI->db->where('userid_fk', $uid);
		$sql = $CI->db->get($cfg['dbpref'].'contract_jobs');
		$res = $sql->result_array();
		$res_num = $sql->num_rows();
		if ($res_num>0) {
			$file_access = 1;
		} else {
			$file_access = 0;
		}
		return $file_access;
	}
}

if ( ! function_exists('get_folder_access'))
{
	function get_folder_access($lead_id, $folder_id, $user_id)
	{
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); // load config		
		$CI->db->select('access_type');
		$CI->db->where(array('lead_id' => $lead_id,'folder_id' => $folder_id,'user_id' => $user_id));
		$sql = $CI->db->get($cfg['dbpref'].'lead_folder_access');
		$res = $sql->row_array();
		// echo $CI->db->last_query(); exit;
		return $res['access_type'];
	}
}

if ( ! function_exists('check_is_root'))
{
	function check_is_root($lead_id, $folder_id)
	{
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); // load config		
		$CI->db->select('*');
		$CI->db->where(array('lead_id' => $lead_id,'folder_id' => $folder_id));
		$sql = $CI->db->get($cfg['dbpref'].'file_management');
		$res = $sql->row_array();
		// echo $CI->db->last_query(); exit;
		if($res['parent']==0){
			return 'root';
		} else {
			return $res['parent'];
		}
	}
}
// changes the date format to readable without time
if ( ! function_exists('date_format_readable'))
{
	function date_format_readable($date)
	{
		// If date value exist it enters the condition
		if(!empty($date))
		{
			if($date=='0000-00-00 00:00:00')
			{
				$date="Not Assigned";				
			}
			else
			{
				$date = date('d-m-Y', strtotime($date));
			}
			
		}
		else
		{
			$date="";
		}

		return $date;
	}
}

// This Function is used to define the datatable with the table details(table heads,table datas, category id)
if ( ! function_exists('datatable_structure'))
{
function datatable_structure($task_category,$permission,$category_title,$category_id,$table_head,$additionalcolumn)
{
	
	$CI  = get_instance();
	$cfg = $CI->config->item('crm'); // load config		
	$CI->db->select('*');
	$CI->db->where(array('status' => 1));
	$sql = $CI->db->get($cfg['dbpref'].'task_stages');
	$taskres = $sql->result_array();
	$taskArr = array();
	if(!empty($taskres) && count($taskres)>0) {
		foreach($taskres as $ta) {
			$taskArr[$ta['task_stage_id']] = $ta['task_stage_name'];
		}
	}
	
	$userid = $permission['logged_in_user']['userid'];
	$userroleid = $permission['logged_in_user']['role_id'];
	$project_td ="";
	$CI  = get_instance();
	$CI->load->model('user_model');
	$CI->load->model('customer_model');
	
	$tableid= strtolower(str_replace(' ', '', $category_title));
	echo '<div style="padding:10px;">
	<h4 style="margin-bottom:0px;">'.$category_title.' </h4>
	<table border="0" id="'.$tableid.'"  rel="'.$category_id.'" cellpadding="0" cellspacing="0"  class="data-tbls dashboard-heads dataTable  ">
		<thead>
			<tr>';
			foreach ($table_head as $key => $value) 
			{
 				echo'<th style="PADDING:11PX;" width="'.$value.'%">'.$key.'</th>';
			}	
		echo'</tr>
		</thead>
		<tbody>';
		// If task_category is array and array count of task category greater than 0 it enters the condition
		if (is_array($task_category) && CONST_ZERO < count($task_category)) 
		{ 
			foreach($task_category as $row) 
			{ 
				$createdUser=$CI->user_model->get_user($row['taskcreated_by']);
				$allocatedUser=$CI->user_model->get_user($row['userid_fk']);
				$company_name= $CI->customer_model->get_company($row['custid_fk']);
				// If lead_title value exist it enters the condition
				if(!empty($row['lead_title']))
				{
					$company_title = $row['lead_title'].'-'.$company_name[CONST_ZERO]['company'];
				}
				else
				{
					$company_title ="";
				}
				$taskid="'".$row['taskid']."'";
				// if additionalcolumn value equals 1 it enters the condition
				if(CONST_ONE == $additionalcolumn)
				{
					$lead_access = getAccessFromLead($userid, $row['lead_id']);	
					$team_access = getAccessFromTeam($userid, $row['lead_id']);
					$stake_access = getAccessFromStakeHolder($userid, $row['lead_id']);
					$link_access = CONST_ZERO;
					// If Lead access or team access or stake access or user role access equals 1 it enters the condition
					if(CONST_ONE == $lead_access  || CONST_ONE == $team_access  || CONST_ONE == $stake_access  || CONST_ONE == $userroleid ) 
					{
						$link_access = CONST_ONE;
					}
					// If link access equals 1 it enters the condition
					if(CONST_ONE == $link_access)
					{
						// If lead_or_project value equals 1 it enters the condition
						if(CONST_ONE == $row['lead_or_project'])
						{
							$lead_title = "<a target=\"blank\" href=\"project/view_project/{$row['lead_id']}\">{$company_title}</a>";
						} 
						else
						{
							$lead_title = "<a target=\"blank\" href=\"welcome/view_quote/{$row['lead_id']}\">{$company_title}</a>";
						}
					}
					else
					{
						$lead_title = $company_title;
					}
		
		
					$project_td ='<td style="padding:10px;"><span class="hide">'.$row['taskid'].'</span>'.$lead_title.'</td>';
		
				}
				// if userid equals the task created user or task assigned user it enters the condition
				if($userid== $row['taskcreated_by'] || $userid==$row['userid_fk'])
				{
					$status_return =CONST_ONE;
				}
				else
				{
					$status_return =CONST_ZERO;
				}
	
				echo '<tr>
					'.$project_td.'
					<td style="padding:10px;">'.$row['task'].'</td>
					<td style="padding:10px;" id="'.$row['task_priority'].'">'. priority_name_define($row['task_priority']).'</td>
					<td style="padding:10px;" id="'.$row['taskcreated_by'].'" >'. $createdUser[0]['first_name'].'</td>
					<td style="padding:10px;" id="'.$row['userid_fk'].'" >'. $allocatedUser[0]['first_name'].'</td>
					<td style="padding:10px;">'. date_format_readable($row['start_date']).'</td>
					<td style="padding:10px;">'. date_format_readable($row['end_date']).'</td>
					<td style="padding:10px;">'. date_format_readable($row['actualstart_date']).'</td>
					<td style="padding:10px;">'. date_format_readable($row['actualend_date']).'</td>
					<td class="est-hr" style="padding:10px;">'.$row['estimated_hours'].'</td>
					<td style="padding:10px;">'.taskStatusForm($row['taskid'],$status_return,$row['status']).'</td>
					<td style="padding:10px;">'.$taskArr[$row['task_stage']].'</td>
					<td style="padding:10px;" class="actions">';
					
					// if is_complete value equals 1 it enters the condition
					if (CONST_ONE == $row['is_complete'])
					{
						echo '<span class="label-success">&nbsp;Approved&nbsp;</span>';
					}
					else
					{
						// if userid equals the task created user it enters the condition
						if($userid ==$row['taskcreated_by'])
						{
							// if status value equals the 100  it enters the condition 
							if(CONST_HUNDRED ==$row['status'])
							{
								$s="setTaskStatus($taskid,'complete');return false";
								echo'<a  onclick="'.$s.'"href="javascript:void(0);" title="Approve"><img src="assets/img/tick.png" alt="edit"> </a>';
							}
						
							echo'<a id='.$taskid.' onclick="openEditTask('.$taskid.'); return false;"href="javascript:void(0);" title="Edit"><img src="assets/img/edit.png" alt="edit"> </a>';
							$s="deleteItem('delete','ajax','set_task_status',$taskid)";
							echo'<a class="delete" href="javascript:void(0)" onclick="'.$s.'" title="Delete"> <img src="assets/img/trash.png" alt="delete"> </a>';
						}
						else if($userid ==$row['userid_fk'])
						{
							echo'<a id='.$taskid.' onclick="openEditTask('.$taskid.'); return false;"href="javascript:void(0);" title="Edit"><img src="assets/img/edit.png" alt="edit"> </a>';
						}
					}
					echo '<div class="dialog-err pull-right" id="dialog-message-$row[id]" style="display:none"></div>
					</td>
				</tr>';
			}
		} 
		echo'</tbody>
	</table>'.'<br/></div>';	
}
}

// It is used to define the priority name using the id
if ( ! function_exists('priority_name_define'))
{
	function priority_name_define($id)
	{
		// Switch case base on the Priority Id
		switch ($id)
		{
			case "1":
				$val="<span class='label-hot'>&nbsp;Critical&nbsp;</span>";
			break;
			case "2":
				$val="<span class='label-hot'>&nbsp;High&nbsp;</span>";
			break;
			case "3":
				$val="<span class='label-warm'>Medium</span>";
			break;
			case "4":
				$val="<span class='label-cold'>&nbsp;Low&nbsp;</span>";
			break;		
			
			default:
				$val="null";
		}

		return $val;
	}
}
//Checks if value exist or not.
if ( ! function_exists('element_value_check'))
{
	function element_value_check($element)
	{
		// If get element of value exist it enters the condition
		if(isset($_GET[$element]))
		{
			$element=$_GET[$element];
		}
		else
		{
			$element="";
		}
		return $element;
	}
}
// This function is for status 100% listing to update the status
if ( ! function_exists('taskStatusForm'))
{
	function taskStatusForm($tk,$val,$status)
	{
		// If val equals 1 it enters the condition
		if($val==CONST_ONE)
		{
			$options = array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100);
			$opts = '';
			foreach ($options as $o)
			{
				$sel = ($status == $o) ? ' selected="selected"' : '';
				$opts .= "<option value=\"{$o}\"{$sel}>{$o}%</option>";
			}
			$own_task_form =  <<< EOD
			<form onsubmit="return false" style="margin-bottom:0;">
				<select name="set_task_status_{$tk}" id="set_task_status_{$tk}" class="set-task-status" style="margin-bottom:0;">
					{$opts}
				</select>
				<div class="buttons">
					<button type="submit"style="margin-left:0px;" onclick="setTaskStatus('{$tk}'); return false;">Set</button>
				</div>
			</form>
		
EOD;
		}
		else
		{
			$own_task_form = $status.'%';
		}
		return $own_task_form;
	}
}


/* End of file lead_helper.php */
/* Location: ./system/helpers/lead_helper.php */