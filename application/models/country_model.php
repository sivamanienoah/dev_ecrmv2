<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Country_model extends crm_model {
    
    function Country_model() {
        parent::__construct();
    }
    
    function country_list($offset, $search) {
        
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('country_name', 'asc');
       
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('country_name', $search);
        }
        $customers = $this->db->get($this->cfg['dbpref']. '_country', 35, $offset);
        return $customers->result_array();
        
    }
 
 
    function update_country($id, $data) {
        
        $this->db->where('countryid', $id);
        return $this->db->update($this->cfg['dbpref']. '_country', $data);
        
    }
    
    function insert_country($data) {
        
        if ( $this->db->insert($this->cfg['dbpref']. '_country', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
        
    }
    
    function delete_country($id) {
        
        $this->db->where('countryid', $id);
        return $this->db->delete($this->cfg['dbpref']. '_country');
        
    }
 
}

?>
