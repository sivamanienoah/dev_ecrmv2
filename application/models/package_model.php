<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class Package_model extends crm_model {
    function Package_model() {
        parent::__construct();
    }
	function result_list($offset, $search) {
        $this->db->order_by('package_id', 'asc');
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like($this->cfg['dbpref'].'package.package_name', $search);
        }
		$this->db->select($this->cfg['dbpref'].'package.*, '.$this->cfg['dbpref'].'package_type.package_name AS PACK_NAME,'.$this->cfg['dbpref'].'package_type.type_id');
		$this->db->from($this->cfg['dbpref'].'package');
		$this->db->join($this->cfg['dbpref'].'package_type', $this->cfg['dbpref'].'package.typeid_fk = '.$this->cfg['dbpref'].'package_type.type_id', 'inner');
		$this->db->limit(20, $offset);
		$accounts=$this->db->get();
        $list = $accounts->result_array();
		return $list;
    }
	function acc_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'package');
    }
	function get_pack($id=false) {
       $account = $this->db->get_where($this->cfg['dbpref'] . 'package', array('package_id' => $id), 1);
	   return $account->result_array();
    }
	function update_pack($id,$data) {
		$this->db->where('package_id', $id);
        return $this->db->update($this->cfg['dbpref'] . 'package', $data);   
    }
    function insert_pack($data) {
		if ( $this->db->insert($this->cfg['dbpref'] . 'package', $data) ) {
			return true;
        } else {
            return false;
        }
    }
	
	function get_package($id=false) {
       $account = $this->db->get_where($this->cfg['dbpref'] . 'package_type', array('type_id' => $id), 1);
	   return $account->result_array();
    }
	function update($id,$data) {
		$this->db->where('type_id', $id);
        return $this->db->update($this->cfg['dbpref'].'package_type', $data);   
    }
	function delete($id) {
		$this->db->where('type_id', $id);
		return $this->db->delete($this->cfg['dbpref'].'package_type');
    }
	function delete_packagename($id) {
		$this->db->where('package_id', $id);
		return $this->db->delete($this->cfg['dbpref'].'package');
    }
    function insert($data) {
		if ( $this->db->insert($this->cfg['dbpref'].'package_type', $data) ) {
			return true;
        } else {
            return false;
        }
    }
	
	function list_result($offset, $search) {
        
        $this->db->order_by('type_id', 'asc');
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('package_name', $search);
        }
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'package_type');
		$this->db->limit(20,$offset);
		$accounts=$this->db->get();
        $list = $accounts->result_array();
		return $list;
    }
	function account_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'package_type');
    }
	function active(){
		$account = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}package_type WHERE package_flag='active'");
	    return $account->result_array();
	}
}
?>