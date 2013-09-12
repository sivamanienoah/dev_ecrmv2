<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getLeadStage'))
{
	function getLeadStage()
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm');
		
		$CI->db->select('sequence');
		$CI->db->from($cfg['dbpref'].'lead_stage');
		$CI->db->where('is_sale', 1);
		$sql = $CI->db->get();
		$res = $sql->row_array();

		
		$CI = get_instance();
		$CI->db->select('lead_stage_id');
		$CI->db->from($cfg['dbpref'].'lead_stage');
		$CI->db->where('sequence >=', 0);
		$CI->db->where('sequence <', $res['sequence']);
		$sql1 = $CI->db->get();
		$res1 = $sql1->result_array();
		
		foreach ($res1 as $stage) {
			$stg[] = $stage['lead_stage_id'];
		}
		return $stg;
		
	}	
}

/* End of file lead_stage_helper.php */
/* Location: ./system/helpers/lead_stage_helper.php */