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



/* End of file number_helper.php */
/* Location: ./system/helpers/number_helper.php */