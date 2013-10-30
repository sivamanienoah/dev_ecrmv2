<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Item_mgmt_model extends crm_model {
	//public $cfg;
	
	function Item_mgmt_model() {
        parent::__construct();        
    }
    
	function get_category_list() {
		$this->db->order_by('cat_id');
		$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
		return $q->result_array();
	}
	
	function get_row_bycond($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
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