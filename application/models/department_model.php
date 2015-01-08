<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Manage Departments Model
 *
 * @class 		Department_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah - Mani.S
 */

class Department_model extends crm_model {
    
	/*
	*@construct
	*@Manage Project Type Model
	*/
    function Department_model() {
       parent::__construct();
	   $this->load->helper('custom_helper');
    }

	/*
	*@Get departments for Search
	*@Department_model
	*/
	public function get_departments($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'department');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('department_name', $search); 
		}
		$query = $this->db->get();
		return $query->result_array();
    }
	
	/*
	*@Method get_departments_list
	*@Model Department_model
	*@Author Mani.S
	*/
	public function get_departments_list($conditions=array()) {
	
	
		if(isset($conditions) && !empty($conditions)) {
			$this->db->where($conditions);
		}
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'department');	
		$query = $this->db->get();
		return $query->result_array();
    }
	
	/*
	*@Get get department datas from econnect and update
	*@Model Department_model
	*@Method update_departments
	*@Parameter --
	*@Author eNoah - Mani.S
	*/
	public function updateDepartments()
	{
	
		$econnect_db = $this->load->database('econnect',TRUE);		
		if(isset($econnect_db) && !empty($econnect_db))
		{
				$econnect_db->select('*');
				$econnect_db->from($econnect_db->dbprefix('department_master'));	
				$query = $econnect_db->get();
				$arrResult = $query->result_array();
				
				
				if(isset($arrResult) && !empty($arrResult)) {
				
					foreach($arrResult as $listResults) {					
						$this->db->query( ' REPLACE INTO  `'.$this->cfg['dbpref'].'department`  SET 
																						`department_id` = '.$listResults['department_id'].',
																						`department_name` = "'.$listResults['department_name'].'",
																						`department_description` = "'.$listResults['department_description'].'",
																						`active` = '.$listResults['active'].',
																						`created_by` = '.$listResults['created_by'].',
																						`created_on` = "'.$listResults['created_on'].'",
																						`modified_by` = '.$listResults['modified_by'].',
																						`modified_on` = "'.$listResults['modified_on'].'"');
				}				
			}
		}
	}
}
?>