<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Enquiries_model extends crm_model {
    
    public function __construct()
    {
		parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode('","', $this->stg);
    }
	
	/*
	*Get the Lead Detail
	*/
	public function get_enquiry_detail($enquiryid) {
	
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'oppurtunities');
		$this->db->where('oppurtunity_id', $enquiryid);
		$sql = $this->db->get();
	    $res =  $sql->row_array();
	    return $res;
	}
	
	public function get_filter_results()
	{
		$sql = $this->db->select('*');
	    $sql = $this->db->from($this->cfg['dbpref'].'oppurtunities');
		$sql = $this->db->get();
	    $res =  $sql->result();
		return $res;
	}
	
	function get_lead_all_detail($id) 
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'leads as j');
		$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage');
		$this->db->where('lead_id', $id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() > 0)
		{
			$data = $query->result_array();
			return $data[0];
		}
		else
		{
			return FALSE;
		}
	}
		
	function delete_enquiry($id) 
	{
		$this->db->where('oppurtunity_id', $id);
		$this->db->delete($this->cfg['dbpref'] . "oppurtunities");
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

}

?>
