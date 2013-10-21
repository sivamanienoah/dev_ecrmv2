<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class manage_service_model extends crm_model {
    
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
		$leads =  $query->result_array();
		return $leads;
    }
	
	function get_expect_worth_cur($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'expect_worth');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('expect_worth_name', $search);
		}
		$query = $this->db->get();
		$expect =  $query->result_array();
		return $expect;
    }
	
	function get_all_currency() {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'currency_all');
		$query = $this->db->get();
		$cur =  $query->result_array();
		return $cur;
    }
	
	function insert_new_currency($ins) {
		$this->db->insert("{$this->cfg['dbpref']}" . 'expect_worth', $ins);
		$last_ins_id = $this->db->insert_id();
		if (array_key_exists('is_default', $ins)) {
			$data['is_default'] = 0;
			$cur_conver['to'] = $last_ins_id;
			$this->db->update($this->cfg['dbpref'].'expect_worth', $data, "expect_worth_id != ".$last_ins_id." ");
			$this->db->truncate($this->cfg['dbpref'].'currency_rate');
		}
    }
	
	function updt_exist_currency($updt, $id) {
		$res = $this->db->update($this->cfg['dbpref'].'expect_worth', $updt, "expect_worth_id = ".$id." ");
		if (array_key_exists('is_default', $updt)) {
			$data[is_default] = 0;
			$cur_conver['to'] = $id;
			$this->db->update($this->cfg['dbpref'].'expect_worth', $data, "expect_worth_id != ".$id." ");
			$this->db->truncate($this->cfg['dbpref'].'currency_rate');
		}
		return $res;
    }
	
	function getCurName($id) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'currency_all');
		$this->db->where('cur_id', $id);
		$query = $this->db->get();
		$res =  $query->row_array();
		return $res;
    }
   
	function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }
	
    function get_num_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->num_rows();
    }
    
    function update_row($table, $cond, $data) {
    	$this->db->where($cond);
		return $this->db->update($this->cfg['dbpref'].$table, $data);
    }
    
	function insert_row($table, $param) {
    	$this->db->insert($this->cfg['dbpref'].$table, $param);
    }
    
	function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }
    
}

?>
