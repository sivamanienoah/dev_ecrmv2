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
			return false;
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
		$CI->db->where('jb.lead_assign', $userdata['userid']);
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
		$CI->db->where('t.actualend_date', 0);
		$CI->db->where('t.userid_fk', $userdata['userid']);
		$sql1 = $CI->db->get($cfg['dbpref'].'tasks as t');
		
		// echo $CI->db->last_query(); exit;
		
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
	$CI = get_instance();
	$cfg = $CI->config->item('crm'); // load config
	
	$CI->db->select('lead_assign, assigned_to, belong_to');
	$CI->db->where('lead_id', $id);
	$CI->db->where("(lead_assign = '".$uid."' || assigned_to = '".$uid."' || belong_to = '".$uid."')");
	$sql = $CI->db->get($cfg['dbpref'].'leads');
	$res = $sql->result_array();
	if (empty($res1)) {
		$chge_access = 0;
	} else {
		$chge_access = 1;
	}
	return $chge_access;
}


/* End of file number_helper.php */
/* Location: ./system/helpers/number_helper.php */