<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customer_contact_model extends crm_model {
    
    public $userdata;
    
    function __construct()
    {
        parent::__construct();
        $this->userdata = $this->session->userdata('logged_in_user');
    }
    
	 function insert_customer_contacts($data) {
		
	    if ( $this->db->insert_batch($this->cfg['dbpref'] . 'customer_contacts', $data) ) {
            //$insert_id = $this->db->insert_id();
            return true;
        } else {
            return false;
        }
    }
	function get_customer_contacts($id) {
		$this->db->order_by('custid','ASC');
        $customer = $this->db->get_where($this->cfg['dbpref'].'customer_contacts', array('company_id_fk' => $id));
        if ($customer->num_rows() > 0) {
            return $customer->result_array();
        } else {
            return FALSE;
        }
    }
	function update_customer_contacts($data,$contact_id) {
		$this->db->where(['custid'=>$contact_id]);
        $customer = $this->db->update($this->cfg['dbpref'].'customer_contacts',$data);
    }
    
}
/* end of file */