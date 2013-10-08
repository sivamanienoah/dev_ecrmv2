<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manage_lead_stage_model extends crm_model {
    
    function Manage_lead_stage_model() {
        parent::__construct();
    }
	
	function get_leadStage($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'lead_stage');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('lead_stage_name', $search); 
		}
		$query = $this->db->get();
		$leadstg =  $query->result_array();
		return $leadstg;
    }
   
}

?>
