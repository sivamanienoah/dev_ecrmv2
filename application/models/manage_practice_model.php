<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage Service Model
 *
 * @class 		manage_practice_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Manage_practice_model extends crm_model {
    
	/*
	*@construct
	*@Manage Service Model
	*/
    function Manage_practice_model() {
       parent::__construct();
	   $this->load->helper('custom_helper');
    }

	/*
	*@Get practices for Search
	*@Manage_practice_model
	*/
	public function get_practices($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'practices');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('practices', $search); 
		}
		$query = $this->db->get();
		return $query->result_array();
    }

	/*
	*@Update Exist Currency
	*@Method  insert_new_currency
	*@table   expect_worth
	*/
	public function updt_exist_currency($updt, $id) {
		$res = $this->db->update($this->cfg['dbpref'].'expect_worth', $updt, "expect_worth_id = ".$id." ");
		if (array_key_exists('is_default', $updt)) {
			$data[is_default] = 0;
			$cur_conver['to'] = $id;
			$this->db->update($this->cfg['dbpref'].'expect_worth', $data, "expect_worth_id != ".$id." ");
			$this->db->truncate($this->cfg['dbpref'].'currency_rate');
		}
		currency_convert();
		return $res;
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
