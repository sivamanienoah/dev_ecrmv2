<?php
if ( ! function_exists('getContractManagerName'))
{
	function getContractManagerName($cm_userid)
	{	
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$CI->db->select('first_name, last_name, username, email');
		$CI->db->where('userid', $cm_userid);
		$query  = $CI->db->get($CI->cfg['dbpref'].'users');
		$num 	= $query->num_rows();
		if ($num<1){
			return false;
		} else {
			$res = $query->row_array();
			$username = $res['first_name'];
			if(!empty($res['last_name'])){
				$username .= " " . $res['last_name'];
			}
			return $username;
		}
	}
}

if ( ! function_exists('getResellerActiveLeads'))
{
	function getResellerActiveLeads($userid)
	{	
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$CI->db->select('jb.lead_id');
		$CI->db->where('jb.lead_status', 1);
		$reseller_condn = '(`jb`.`belong_to` = '.$userid.' OR `jb`.`lead_assign` = '.$userid.')';
		$CI->db->where($reseller_condn);
		$CI->db->where('jb.lead_id != "null" AND jb.lead_stage IN ("'.$CI->stages.'")');
		$query  = $CI->db->get($CI->cfg['dbpref'].'leads jb');
		// echo $CI->db->last_query(); exit;
		$num 	= $query->num_rows();
		if ($num<1){
			return false;
		} else {
			return $num;
		}
	}
}

if ( ! function_exists('getResellerActiveProjects'))
{
	function getResellerActiveProjects($userid)
	{	
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$CI->db->select('jb.lead_id');
		$CI->db->where('jb.lead_status', 4);
		$CI->db->where('jb.pjt_status', 1);
		$reseller_condn = '(`jb`.`belong_to` = '.$userid.' OR `jb`.`lead_assign` = '.$userid.' OR `jb`.`assigned_to` = '.$userid.')';
		$CI->db->where($reseller_condn);
		$query  = $CI->db->get($CI->cfg['dbpref'].'leads jb');
		$num 	= $query->num_rows();
		if ($num<1){
			return false;
		} else {
			return $num;
		}
	}
}
