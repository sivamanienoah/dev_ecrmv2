<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class manage_service_model extends Common_model {
    
    function Manage_service_model() {
       parent::__construct();
    }
	
	function get_jobscategory($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'job_categories');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('category', $search); 
		}
		$query = $this->db->get();
		$cate =  $query->result_array();
		return $cate;
    }
	
	function get_salesDivisions($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'sales_divisions');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('division_name', $search); 
		}
		$query = $this->db->get();
		$divs =  $query->result_array();
		return $divs;
    }
	
	function get_list_active($tbl_name) {
		$query = $this->db->get_where($tbl_name, array('status' => 1));
		$activeLists = $query->result_array();
		return $activeLists;
	}
	
	function get_lead_source($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'lead_source');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('lead_source_name', $search); 
		}
		$query = $this->db->get();
		//echo $this->db->last_query();
		$leads =  $query->result_array();
		return $leads;
    }
   
}

?>
