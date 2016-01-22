<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends crm_model 
{   
    public function __construct()
    {
		parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode('","', $this->stg);
    }
    
	function get_user_byrole($role_id)
	{
    	$users = $this->db->get_where($this->cfg['dbpref'] . 'users', array('role_id'=>$role_id))->result_array();
    	return $users;
    }
	
	function get_customers() 
	{
	    $this->db->select('custid, first_name, last_name, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers');
		$this->db->order_by("first_name", "asc");
	    $customers = $this->db->get();
	    $customers =  $customers->result_array();
	    return $customers;
	}

	public function get_practices()
	{
    	$this->db->select('id, practices');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$query = $this->db->get($this->cfg['dbpref'] . 'practices');
		$res = $query->result_array();
		$practices = array();
		if(!empty($res)){
			foreach($res as $row){
				$practices[$row['id']] = $row['practices'];
			}
		}
		return $practices;
    }
	
/* 	public function get_practices()
	{
    	$this->db->select('id, practices');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$query = $this->db->get($this->cfg['dbpref'] . 'practices');
		$res = $query->result_array();
		$practices = array();
		if(!empty($res)){
			foreach($res as $row){
				$practices[$row['id']] = $row['practices'];
			}
		}
		return $practices;
    } */
}
?>