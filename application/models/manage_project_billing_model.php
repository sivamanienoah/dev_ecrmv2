<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage_project_billing_model
 *
 * @class 		manage_project_billing_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Manage_project_billing_model extends crm_model {
    
	/*
	*@construct
	*@Manage_project_billing_model
	*/
    function Manage_project_billing_model() {
       parent::__construct();
	   $this->load->helper('custom_helper');
    }

	/*
	*@Get practices for Search
	*@Manage_project_billing_model
	*/
	public function get_project_billing_type($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_billing_type');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('project_billing_type', $search); 
		}
		$query = $this->db->get();
		return $query->result_array();
    }

	/*
	*@Get row record for dynamic table
	*@Method  get_row
	*/
	public function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }

	/*
	*@Get row count for dynamic table
	*@Method  get_num_row
	*/
    public function get_num_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->num_rows();
    }

	/*
	*@Update Row for dynamic table
	*@Method  update_row
	*/
    public function update_row($table, $cond, $data) {
    	$this->db->where($cond);
		return $this->db->update($this->cfg['dbpref'].$table, $data);
    }

	/*
	*@Insert Row for dynamic table
	*@Method  insert_row
	*/
	public function insert_row($table, $param) {
    	$this->db->insert($this->cfg['dbpref'].$table, $param);
		return $this->db->insert_id();
    }

	/*
	*@Delete Row for dynamic table
	*@Method  insert_row
	*/
	public function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
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
	
	/*
	 *Insert to timesheet db
	 */
	function insert_timesheet_db($ins_data) {
		$timesheet_db = $this->load->database('timesheet',TRUE);
		$sql = 'INSERT INTO '.$timesheet_db->dbprefix('project_types'). ' values ("'.$ins_data['id'].'","'.$ins_data['project_billing_type'].'")';
		return $timesheet_db->query($sql);
	}

	/*
	 *Update to timesheet db
	 */
	function updt_timesheet_db($updt_data) {
		$timesheet_db = $this->load->database('timesheet',TRUE);
		$sql = 'UPDATE '.$timesheet_db->dbprefix('project_types'). ' SET project_type_name = "'.$updt_data['project_billing_type'].'" WHERE project_type_id = "'.$updt_data['id'].'" ';
		return $timesheet_db->query($sql);
	}
    
}

?>
