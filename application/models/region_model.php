<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Region_model extends Common_model {
    
    function Region_model() {
        
        parent::__construct();
        
    }
    
    function region_list($offset, $search) {
        
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('region', 'asc');
       
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('region', $search);
        }
        $customers = $this->db->get($this->cfg['dbpref'] . 'region', 35, $offset);
        return $customers->result_array();
        
    }
 
 
    function update_region($id, $data) {
        
        $this->db->where('regionid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'region', $data);
        
    }
    
    function insert_region($data) {
   
        if ( $this->db->insert($this->cfg['dbpref'] . 'region', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
        
    }
    
    function delete_region($id) {
        
        $this->db->where('regionid', $id);
        return $this->db->delete($this->cfg['dbpref'] . 'region');
        
    }
 
}

?>
