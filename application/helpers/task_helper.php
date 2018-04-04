<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('getAccessFromLead'))
{
	function getAccessFromLead($uid, $id)
	{
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$wh_condn = '(belong_to = '.$uid.' OR assigned_to ='.$uid.' OR FIND_IN_SET('.$uid.', lead_assign)) ';

		$CI->db->select('lead_assign, assigned_to, belong_to');
		$CI->db->where('lead_id', $id);
		// $CI->db->where("(lead_assign = '".$uid."' || assigned_to = '".$uid."' || belong_to = '".$uid."')");
		$CI->db->where($wh_condn);
		$sql = $CI->db->get($cfg['dbpref'].'leads');
		
		//echo $CI->db->last_query();
		$res = $sql->result_array();
		if (empty($res)) {
			$chge_access = 0;
		} else {
			$chge_access = 1;
		}
		return $chge_access;
	}
}

if ( ! function_exists('getAccessFromTeam'))
{
	function getAccessFromTeam($uid, $id)
	{
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$CI->db->select('*');
		$CI->db->where('jobid_fk', $id);
		$CI->db->where('userid_fk', $uid);
		$sql = $CI->db->get($cfg['dbpref'].'contract_jobs');
		//echo $CI->db->last_query();
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

if ( ! function_exists('getAccessFromStakeHolder'))
{
	function getAccessFromStakeHolder($uid, $id)
	{
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$CI->db->select('*');
		$CI->db->where('lead_id', $id);
		$CI->db->where('user_id', $uid);
		$sql = $CI->db->get($cfg['dbpref'].'stake_holders');
		//echo $CI->db->last_query();
		$res = $sql->result_array();
		$res_num = $sql->num_rows();
		if ($res_num>0) {
			$stake_access = 1;
		} else {
			$stake_access = 0;
		}
		return $stake_access;
	}
}




/* End of file number_helper.php */
/* Location: ./system/helpers/number_helper.php */