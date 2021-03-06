<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends crm_model 
{   
    public function __construct()
    {
		parent::__construct();
		$this->load->helper('lead_stage');
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

	public function get_departments()
	{
    	$this->db->select('department_id, department_name');
		$query = $this->db->get($this->cfg['dbpref'] . 'department');
		return $query->result();
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
	public function getOtherCosts($start_date, $end_date, $entity_ids=array(), $practice_ids=array(), $project_reslt)
	{
		$us_currenty_type = 1;
		$bk_rates = get_book_keeping_rates();
		$this->db->select("oc.id, oc.cost_incurred_date, oc.currency_type, oc.value, oc.description, l.pjt_id, l.department_id_fk, l.division, l.practice, l.lead_title");
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
		if(!empty($project_reslt) && count($project_reslt)>0) {
			$this->db->where_in('l.pjt_id', $project_reslt);
		}
		$query 	= $this->db->get();
		// echo $this->db->last_query(); die;
		$data	= $query->result_array();
		$departments 	= $this->get_departments();
		$deptArr		= array();
		if(!empty($departments) && count($departments)>0) {
			foreach($departments as $dept_row) {
				$deptArr[$dept_row->department_id] = $dept_row->department_name;
			}
		}

		$entities 	= $this->get_entities();
		$entiArr	= array();
		if(!empty($entities) && count($entities)>0) {
			foreach($entities as $enti_row) {
				$entiArr[$enti_row->div_id] = $enti_row->division_name;
			}
		}
		
		$practices 	= $this->get_practices();
		$practArr	= array();
		if(!empty($practices) && count($practices)>0) {
			foreach($practices as $pract_row) {
				$practArr[$pract_row->id] = $pract_row->practices;
			}
		}
		
		if(!empty($data)) {
			$other_cost_array = array();
			$i = 0;
			foreach($data as $row) {
				$year_no 	= trim(date('Y', strtotime($row['cost_incurred_date'])));
				$month_name = trim(date('F', strtotime($row['cost_incurred_date'])));
				$other_cost_array[$row['pjt_id']][$year_no][$month_name][$i]['oc_val']		= $this->conver_currency($row['value'], $bk_rates[$year_no][$row['currency_type']][$us_currenty_type]);
				$other_cost_array[$row['pjt_id']][$year_no][$month_name][$i]['oc_entity'] 	= $entiArr[$row['division']];
				$other_cost_array[$row['pjt_id']][$year_no][$month_name][$i]['oc_dept'] 	= $deptArr[$row['department_id_fk']];
				$other_cost_array[$row['pjt_id']][$year_no][$month_name][$i]['oc_practice'] = $practArr[$row['practice']];
				$other_cost_array[$row['pjt_id']][$year_no][$month_name][$i]['oc_descrptn'] = $row['description'];
				$i++;
			}
			// echo "<pre>"; print_r($other_cost_array); die;
		}
		
		return $other_cost_array;
	}
	
	public function conver_currency($amount, $val) {
		return round($amount*$val, 2);
	}	
	
	public function get_records($tbl, $wh_condn='', $order='') {
		$cur_Fiscal_Year = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		$this->db->where('financial_yr <=', $cur_Fiscal_Year);
		if(!empty($wh_condn))
		$this->db->where($wh_condn);
		if(!empty($order)) {
			foreach($order as $key=>$value) {
				$this->db->order_by($key,$value);
			}
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->result_array();
    }
	
	public function get_practice_max_hrs_by_fiscal_year()
	{
		$this->db->select('practice_id,practice_max_hours,financial_year');
		$this->db->from($this->cfg['dbpref'].'practice_max_hours_history');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		$data =  $query->result_array();
		if(!empty($data)) {
			$id_year_arr = array();
			foreach($data as $row) {
				$id_year_arr[$row['practice_id']][$row['financial_year']] = $row['practice_max_hours'];
			}
			return $id_year_arr;
		}
	}
	
	public function get_practice_max_hr()
	{
		$this->db->select('practice_id,practice_max_hours');
		$this->db->from($this->cfg['dbpref'].'practice_max_hours_history');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		$data =  $query->result_array();
		if(!empty($data)) {
			$id_arr =array();
			foreach($data as $row) {
				$id_arr[$row['practice_id']] = $row['practice_max_hours'];
			}
			return $id_arr;
		}
	}
}
?>