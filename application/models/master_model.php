<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Master_model extends crm_model {
    
    function Master_model() {
        
        parent::__construct();
        
    }
    
    function master_list($search=false) {
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('master.master_name', $search);
        }		
		$this->db->select('creuser.first_name as cfnam,creuser.last_name as clnam,moduser.first_name as mfnam,parentmaster.master_name as parentname,moduser.last_name as mlnam,master.*');
		$this->db->from($this->cfg['dbpref'] . 'masters as master');
		$this->db->join($this->cfg['dbpref'] . 'masters as parentmaster','parentmaster.masterid='.'master.master_parent_id','left');
		$this->db->join($this->cfg['dbpref'] . 'users as creuser','creuser.userid='.'master.created_by ');
		$this->db->join($this->cfg['dbpref'] . 'users as moduser','moduser.userid='. 'master.modified_by ');
		$this->db->order_by('master.inactive', 'asc');
	
		//$this->db->where('master.created_by!=', 0);
		$customers = $this->db->get();
        $master_lists=  $customers->result_array();
		return $master_lists;
    }

    function update_master($id, $data) {
        $this->db->where('masterid', $id);		 
		return $this->db->update($this->cfg['dbpref'] . 'masters', $data);
    }
    
    function insert_master($data) {
       // print_r($data);
        if ( $this->db->insert($this->cfg['dbpref'] . 'masters', $data) ) {
		//echo $this->db->last_query();
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    
    function delete_master($id) {
        $this->db->where('masterid', $id);
		return $this->db->delete($this->cfg['dbpref'] . 'masters');
    }
	
	function master_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'masters');
    }
	
	function get_master($id) {
		if( ! $id ) {
            return false;
        } else {
			$customer = $this->db->get_where($this->cfg['dbpref'] . 'masters', array('masterid' => $id), 1);
			return $customer->result_array();
		}
    }
}

?>
