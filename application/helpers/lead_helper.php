<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getleadaccess'))
{
	function getleadaccess($id)
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$CI->db->select('view');
		$CI->db->where('masterid', 51);
		$CI->db->where('role_id', $id);
		$sql = $CI->db->get($cfg['dbpref'].'master_roles');
		$res = $sql->row_array();
		return $res;
	}	
}

/* End of file number_helper.php */
/* Location: ./system/helpers/number_helper.php */