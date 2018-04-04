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
		// $reseller_condn = '(`jb`.`belong_to` = '.$userid.' OR `jb`.`lead_assign` = '.$userid.')';
		$reseller_condn = '(jb.belong_to = '.$userid.' OR FIND_IN_SET('.$userid.', jb.lead_assign)) ';
		$CI->db->where($reseller_condn);
		$CI->db->where('jb.lead_id != "null" AND jb.lead_stage IN ("'.$CI->stages.'")');
		$query  = $CI->db->get($CI->cfg['dbpref'].'leads jb');
		// echo $CI->db->last_query(); exit;
		$num 	= $query->num_rows();
		if ($num<1) {
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
		// $reseller_condn = '(`jb`.`belong_to` = '.$userid.' OR `jb`.`lead_assign` = '.$userid.' OR `jb`.`assigned_to` = '.$userid.')';
		$reseller_condn = '(jb.belong_to = '.$userid.' OR jb.assigned_to ='.$userid.' OR FIND_IN_SET('.$userid.', jb.lead_assign)) ';
		$CI->db->where($reseller_condn);
		$query  = $CI->db->get($CI->cfg['dbpref'].'leads jb');
		// echo $CI->db->last_query(); exit;
		$num 	= $query->num_rows();
		if ($num<1) {
			return false;
		} else {
			return $num;
		}
	}
}

if ( ! function_exists('getResellerAgreementDate'))
{
	function getResellerAgreementDate($userid)
	{	
		$res = array();
		$CI  = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$CI->db->select('contract_start_date, contract_end_date, renewal_reminder_date');
		$CI->db->where('contracter_id', $userid);
		$CI->db->where('contract_status', 1);
		$CI->db->order_by('contract_end_date', 'desc');
		$CI->db->limit(1);
		$query  = $CI->db->get($CI->cfg['dbpref'].'contracts');
		// echo $CI->db->last_query(); exit;
		$num 	= $query->num_rows();
		if ($num<1){
			return false;
		} else {
			$res = $query->row_array();
			return $res;
		}
	}
}

if ( ! function_exists('getContractsUploadsFile'))
{
    function getContractsUploadsFile($id = false)
	{
		if($id) {
			$CI  = get_instance();
			$cfg = $CI->config->item('crm'); /// load config
			
			$CI->db->select('cu.id, cu.file_name');
			$CI->db->from($CI->cfg['dbpref']."contracts_uploads_mapping as cum");
			$CI->db->where('cum.contract_id', $id);
			$CI->db->join($CI->cfg['dbpref']."contracts_uploads as cu", 'cu.id = cum.contract_file_upload_id', 'left');
			$CI->db->order_by("cu.id", "asc");
			$query = $CI->db->get();
			// echo $CI->db->last_query(); exit;
			$reseller = $query->result_array();	
			return $reseller;
		} else {
			return false;
		}
    }
}

if ( ! function_exists('getCommissionUploadsFile'))
{
    function getCommissionUploadsFile($id = false)
	{
		if($id) {
			$CI  = get_instance();
			$cfg = $CI->config->item('crm'); /// load config
			
			$CI->db->select('cu.id, cu.file_name');
			$CI->db->from($CI->cfg['dbpref']."commission_uploads_mapping as cmsn");
			$CI->db->where('cmsn.commission_id', $id);
			$CI->db->join($CI->cfg['dbpref']."commission_uploads as cu", 'cu.id = cmsn.commission_file_upload_id', 'left');
			$CI->db->order_by("cu.id", "asc");
			$query = $CI->db->get();
			// echo $CI->db->last_query(); exit;
			$reseller = $query->result_array();	
			return $reseller;
		} else {
			return false;
		}
    }
}