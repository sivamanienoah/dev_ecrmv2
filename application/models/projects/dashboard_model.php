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
		return $query->result();
    }

	public function get_entities()
	{
    	$this->db->select('div_id, division_name');
		$this->db->where("status", 1);
		$entity_query = $this->db->get($this->cfg['dbpref'].'sales_divisions');
		return $entity_query->result();
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
	
	//for IT cost report
	public function getOtherCosts($start_date, $end_date, $entity_ids=array(), $practice_ids=array())
	{
		$us_currenty_type = 1;
		$bk_rates = get_book_keeping_rates();
		$this->db->select("oc.id, oc.cost_incurred_date, oc.currency_type, oc.value, l.pjt_id");
		$this->db->from($this->cfg['dbpref'].'project_other_cost as oc');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = oc.project_id');
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("(oc.cost_incurred_date >='".date('Y-m-d', strtotime($start_date))."')", NULL, FALSE);
			$this->db->where("(oc.cost_incurred_date <='".date('Y-m-d', strtotime($end_date))."')", NULL, FALSE);
		}
		if(!empty($entity_ids) && count($entity_ids)>0) {
			$this->db->where_in('l.division', $entity_ids);
		}
		if(!empty($practice_ids) && count($practice_ids)>0) {
			$this->db->where_in('l.practice', $practice_ids);
		}
		$query 	= $this->db->get();
		$data	= $query->result_array();
		if(!empty($data)) {
			$other_cost_array = array();
			foreach($data as $row) {
				if(isset$other_cost_array[$row['pjt_id']][date('Y', strtotime($row['cost_incurred_date']))][date('F', strtotime($row['cost_incurred_date']))]['oc_val']) {
					$other_cost_array[$row['pjt_id']][date('Y', strtotime($row['cost_incurred_date']))][date('F', strtotime($row['cost_incurred_date']))]['oc_val'] += $this->conver_currency($row['value'], $bk_rates[date('Y', strtotime($row['cost_incurred_date']))][$row['currency_type']][$us_currenty_type]);
				} else {
					$other_cost_array[$row['pjt_id']][date('Y', strtotime($row['cost_incurred_date']))][date('F', strtotime($row['cost_incurred_date']))]['oc_val'] = $this->conver_currency($row['value'], $bk_rates[date('Y', strtotime($row['cost_incurred_date']))][$row['currency_type']][$us_currenty_type]);
				}
			}
			// echo "<pre>"; print_r($bk_rates); echo "<br>****<br>";
			// echo "<pre>"; print_r($other_cost_array); die;
		}
		return $other_cost_array;
	}
	
	public function conver_currency($amount, $val) {
		return round($amount*$val, 2);
	}
}
?>