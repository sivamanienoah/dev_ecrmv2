<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage_task_category_model Model
 *
 * @class 		Manage_task_category_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Manage_task_category_model extends crm_model {
    
	CONST TABLE_NAME = 'task_category';
	
	/*
	*@construct
	*@Manage Service Model
	*/
    function Manage_task_category_model() {
       parent::__construct();
	   $this->load->helper('custom_helper');
    }

	/*
	*@Get task_type for Search
	*@Manage_task_category_model
	*/
	public function get_task_category($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].SELF::TABLE_NAME);
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('task_category', $search); 
		}
		$query = $this->db->get();
		echo $this->db->last_query(); die;
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
    
}

?>
