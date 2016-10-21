<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Service_graphical_dashboard_model extends crm_model {
    
    public function __construct()
    {
        parent::__construct();
    }
	
	public function get_practices()
	{
    	$res = array();		
		
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		$practice_not_in = array(6);
		$this->db->where_not_in('p.id', $practice_not_in);
		//$this->db->where_in('p.id', array(1));
		$pquery = $this->db->get();
		$pres = $pquery->result();
		
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$res['practice_arr'][$prow->id] = $prow->practices;
				$res['practice_array'][] = $prow->practices;
			}
		}
		return $res;
    }
	
	/*
	*
	*/
	public function getUcRecords($uc_filter_by)
	{
    	if($uc_filter_by == 'hour') {
			$this->db->select('practice_name, ytd_billable');
			$this->db->from($this->cfg['dbpref']. 'services_graphical_dashboard');
		} else if ($uc_filter_by == 'cost') {
			$this->db->select('practice_name, ytd_billable_utilization_cost as ytd_billable');
			$this->db->from($this->cfg['dbpref']. 'services_graphical_dashboard');
		}
		$sql = $this->db->get();
		$uc_graph_res = $sql->result_array();
		
		$uc_graph_val = array();
		if(!empty($uc_graph_res)){
			foreach($uc_graph_res as $key=>$val) {
				if($val['practice_name'] == 'Infra Services'){
					continue;
				}
				$graph_id = strtolower($val['practice_name']);
				$graph_id = str_replace(' ', '_', $graph_id);
				$uc_graph_val[$graph_id] = $val;
			}
		}
		return $uc_graph_val;
    }
	
	/*
	*@Get invoices Records
	*@method get_variance_records_for_dashboard
	*@table crm_view_sales_forecast_variance
	*/
	public function getInvoiceRecords($start_date, $end_date) 
	{
		$job_ids = array();
	
		//LEVEL BASED RESTIRCTION
		/* if( $this->userdata['level'] != 1 ) {
			if (isset($this->session->userdata['region_id']))
			$region = explode(',',$this->session->userdata['region_id']);
			if (isset($this->session->userdata['countryid']))
			$countryid = explode(',',$this->session->userdata['countryid']);
			if (isset($this->session->userdata['stateid']))
			$stateid = explode(',',$this->session->userdata['stateid']);
			if (isset($this->session->userdata['locationid']))
			$locationid = explode(',',$this->session->userdata['locationid']);
			
			$this->db->select('ls.lead_id');
			$this->db->from($this->cfg['dbpref'].'leads as ls');
			$this->db->join($this->cfg['dbpref'].'customers as cs', 'cs.custid  = ls.custid_fk');
			$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = cs.company_id');
			
			switch($this->userdata['level']) {
				case 2:
					$this->db->where_in('cc.add1_region',$region);
				break;
				case 3:
					$this->db->where_in('cc.add1_region',$region);
					$this->db->where_in('cc.add1_country',$countryid);
				break;
				case 4:
					$this->db->where_in('cc.add1_region',$region);
					$this->db->where_in('cc.add1_country',$countryid);
					$this->db->where_in('cc.add1_state',$stateid);
				break;
				case 5:
					$this->db->where_in('cc.add1_region',$region);
					$this->db->where_in('cc.add1_country',$countryid);
					$this->db->where_in('cc.add1_state',$stateid);
					$this->db->where_in('cc.add1_location',$locationid);
				break;
			}
			
			$query = $this->db->get();
			// echo $this->db->last_query();
			$rowscust1 = $query->result_array();
			
			$this->db->select('ld.lead_id');
			$this->db->from($this->cfg['dbpref'].'leads as ld');
			$this->db->where("(ld.assigned_to = '".$this->userdata['userid']."' OR ld.lead_assign = '".$this->userdata['userid']."' OR ld.belong_to = '".$this->userdata['userid']."')");
			$this->db->where("ld.lead_status", 4);
			$this->db->where("ld.pjt_status", 1);
			$query1 = $this->db->get();
			// echo $this->db->last_query();
			$rowscust2 = $query1->result_array();
			
			$customers = array_merge_recursive($rowscust1, $rowscust2);
			
			$res[] = 0;
			if (is_array($customers) && count($customers) > 0) { 
				foreach ($customers as $cus) {
					$res[] = $cus['lead_id'];
				}
			}
			$job_ids = array_unique($res);
			
		} */
		// LEVEL BASED RESTIRCTION
		
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, l.expect_worth_id, pr.practices, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'practices as pr', 'pr.id = l.practice');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		
		/* if(!empty($job_ids) && count($job_ids)>0) {
			$this->db->where_in('sfv.job_id', $job_ids);
		} */
		
		$this->db->where('sfv.type', 'A');
		//BPO practice are not shown in IT Services Dashboard
		$practice_not_in = array(6);
		$this->db->where_not_in('pr.id', $practice_not_in);
		// $this->db->where_in('pr.id', 13);
		$this->db->where('DATE(sfv.for_month_year) >=', date('Y-m-d', strtotime($start_date)));
		$this->db->where('DATE(sfv.for_month_year) <=', date('Y-m-t', strtotime($end_date)));
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
    }

}

?>
