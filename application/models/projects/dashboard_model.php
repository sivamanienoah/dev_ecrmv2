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
	    $this->db->select('custid, customer_name, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers');
		$this->db->order_by("company", "asc");
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
		return $query->result_array();
    }
	
	//update project thermometer
	public function update_project_thermometer($project_id)
	{
		$progress=0;$result=array();
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('project_id', $project_id);
		$this->db->where('parent_id =',0);
		$query = $this->db->get();
		$result=array();
		$row_count=$query->num_rows();
		if($query->num_rows() > 0 )
		{//if array is not empty
			$row = $query->row_array();
			$progress=$row['complete_percentage'];
		}
		
		$sql="update ".$this->cfg['dbpref']."leads set complete_status=".$progress." where lead_id=".$project_id;
		$result=$this->db->query($sql);
		
		//update complete percentage in leads
	}
}
?>