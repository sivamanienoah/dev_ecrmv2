<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage Project Type Model
 *
 * @class 		Project_types_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah - Mani.S
 */

class Project_types_model extends crm_model {
    
	/*
	*@construct
	*@Manage Project Type Model
	*/
    function Project_types_model() {
       parent::__construct();
	   $this->load->helper('custom_helper');
    }

	/*
	*@Get practices for Search
	*@Project_types_model
	*/
	public function get_project_types($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_types');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('project_types', $search); 
		}
		$query = $this->db->get();
		return $query->result_array();
    }
	
	/*
	*@Method get_departments_list
	*@Model Project_types_model
	*@Author Mani.S
	*/
	public function get_project_types_list($conditions=array()) {
	
	
		if(isset($conditions) && !empty($conditions)) {
			$this->db->where($conditions);
		}
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_types');	
		$query = $this->db->get();
		return $query->result_array();
    }
	
	/*
	*Checking duplicates
	*/
	function check_duplicate($tbl_cont, $condn, $tbl_name) {
		$this->db->select($tbl_cont['name']);
		$this->db->where($tbl_cont['name'], $condn['name']);
		if(!empty($condn['id'])) {
			$this->db->where($tbl_cont['id'].' !=', $condn['id']);
		}
		$res = $this->db->get($this->cfg['dbpref'].$tbl_name);
        return $res->num_rows();
	}    
}
?>