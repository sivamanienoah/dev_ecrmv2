<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getLeadStage'))
{
	function getLeadStage()
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm');
		
		$CI = get_instance();
		// $CI->db->select('lead_stage_id');
		// $CI->db->from($cfg['dbpref'].'lead_stage');
		// $CI->db->where('status', 1);
		// $CI->db->order_by('sequence');
		$leadStageQuery = $CI->db->query("SELECT `lead_stage_id` FROM ".$cfg['dbpref']."lead_stage WHERE `status` = 1 ORDER BY `sequence`");
		//$leadStageQuery = $CI->db->get();
		$res1 = $leadStageQuery ->result_array();
		
		foreach ($res1 as $stage) {
			$stg[] = $stage['lead_stage_id'];
		}
		return $stg;
		
	}
	
	function getLeadStageName()
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm');
		$CI->db->select('lead_stage_id, lead_stage_name');
		$CI->db->from($cfg['dbpref'].'lead_stage');
		$CI->db->where('status', 1);
		$CI->db->order_by('sequence');
		$sql = $CI->db->get();
		// echo $CI->db->last_query(); exit;
		$res = $sql->result_array();
		// echo "<pre>"; print_r($res); exit;
		return $res;
		
	}
}

/* End of file lead_stage_helper.php */
/* Location: ./system/helpers/lead_stage_helper.php */