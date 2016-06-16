<?php

/********************************************************************************
File Name       : service_dashboard_cron.php
Created Date    : 16/06/2016
Modified Date   : 16/06/2016
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Subbiah.S
*********************************************************************************/

/**
 * Service_dashboard_cron
 *
 * @class 		Service_dashboard_cron
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */
ini_set('display_errors', 1);
class Service_dashboard_cron extends crm_controller 
{
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('text');
		$this->load->helper('custom_helper');
		$this->load->helper('url');
		$this->load->helper('custom');
    }
	
	public function index() 
	{
		@set_time_limit(-1); //disable the mysql query maximum execution time
		
		$data  				  = array();
		
		$bk_rates = get_book_keeping_rates();
		
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $end_date  	   = $curFiscalYear."-".date('m-d'); //eg.2014-03-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		$start_date = date("Y-m-01",strtotime($start_date));
		$end_date = date("Y-m-t", strtotime($end_date));
		
		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;

		$project_status = 1;
		
		$project_code = array();
		$projects 	  = array();
		$practice_arr = array();

		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in('p.id', 6);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();		
		
		$this->db->select('div_id, division_name, base_currency');
		$this->db->from($this->cfg['dbpref']. 'sales_divisions');
		$equery = $this->db->get();
		$eres = $equery->result();
		$data['entity_data'] = $equery->result();
		
		if(!empty($eres) && count($eres)>0){
			foreach($eres as $erow) {
				$base_cur_arr[$erow->div_id] = $erow->base_currency;
			}
		}
		
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
			}
		}
		
		$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in("l.practice", 6);

		if($project_status){
			if($project_status !=2)
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}

		$query = $this->db->get();
		$res = $query->result_array();
		
		// echo "<pre>"; print_r($res); die;
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
				// $projects['project'][$practice_arr[$row['practice']]][] = $row['pjt_id'];
				$timesheet = array();
				
				if (isset($projects['practicewise'][$practice_arr[$row['practice']]])) {
					$projects['practicewise'][$practice_arr[$row['practice']]] += 1;
				} else {
					$projects['practicewise'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
				}
				if($row['rag_status'] == 1){
					if (isset($projects['rag_status'][$practice_arr[$row['practice']]])) {
						$projects['rag_status'][$practice_arr[$row['practice']]] += 1;
					} else {
						$projects['rag_status'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
					}
				}
			}
		}
		
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, c.company, c.first_name, c.last_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in("l.practice", 6);
		if(!empty($start_date)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("sfv.for_month_year <= ", date('Y-m-d H:i:s', strtotime($end_date)));
		}
		
		$query1 = $this->db->get();
		$invoices_data = $query1->result_array();

		if(!empty($invoices_data) && count($invoices_data)>0) {
			foreach($invoices_data as $ir) {
				$base_conver_amt = $this->conver_currency($ir['milestone_value'],$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				$projects['irval'][$practice_arr[$ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
			}
		}
		
		//for current month ir
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, c.company, c.first_name, c.last_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in("l.practice", 6);
		if(!empty($month)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($month)));
			$this->db->where("sfv.for_month_year <= ", date('Y-m-t H:i:s', strtotime($month)));
		}
		
		$query5 = $this->db->get();
		$cm_invoices_data = $query5->result_array();

		if(!empty($cm_invoices_data) && count($cm_invoices_data)>0) {
			foreach($cm_invoices_data as $cm_ir) {
				$base_conver_amt = $this->conver_currency($cm_ir['milestone_value'],$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($cm_ir['for_month_year'])),"4/1","3/31")][$cm_ir['expect_worth_id']][$cm_ir['base_currency']]);
				$projects['cm_irval'][$practice_arr[$cm_ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($cm_ir['for_month_year'])),"4/1","3/31")][$cm_ir['base_currency']][$this->default_cur_id]);
			}
		}
		
		//for current month EFFORTS
		$projects['billable_month'] = $this->get_timesheet_data($practice_arr, "", "", $month);
		$projects['billable_ytd']   = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
		
		//for effort variance
		$pcodes = $projects['billable_ytd']['project_code'];
		
		if(!empty($pcodes) && count($pcodes)>0){
			foreach($pcodes as $rec){
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_arra = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_arra = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_arra);
				$this->db->where("l.pjt_id", $rec);
				//BPO practice are not shown in IT Services Dashboard
				$this->db->where_not_in("l.practice", 6);
				// $this->db->where("l.billing_type", 1);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				if(!empty($pro_data) && count($pro_data)>0){
					foreach($pro_data as $recrd){
						if(isset($effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'])){
							$effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'] += $recrd['estimate_hour'];
							$actuals = $this->get_timesheet_actual_hours($recrd['pjt_id'], "", "");
							$effvar[$practice_arr[$recrd['practice']]]['total_actual_hrs'] += $actuals['total_hours'];
						} else {
							$effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'] = $recrd['estimate_hour'];
							$actuals = $this->get_timesheet_actual_hours($recrd['pjt_id'], "", "");
							$effvar[$practice_arr[$recrd['practice']]]['total_actual_hrs'] = $actuals['total_hours'];
						}
						$fixed_bid[$practice_arr[$recrd['practice']]][$recrd['pjt_id']] = $recrd['lead_title'];
					}
				}
			}
		}
		$projects['eff_var']   = $effvar;

		$contribution_query = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code, direct_cost_per_hour, resource_duration_direct_cost
		FROM crm_timesheet_data 
		WHERE start_time between '".$start_date."' and '".$end_date."' AND resoursetype != '' AND project_code NOT IN ('HOL','Leave')";
		
		$sql1 = $this->db->query($contribution_query);
		$contribution_data = $sql1->result();
		if(!empty($contribution_data)) {
			foreach($contribution_data as $cdrow){
				$directcost[$practice_arr[$cdrow->practice_id]]['total_direct_cost'] = $directcost[$practice_arr[$cdrow->practice_id]]['total_direct_cost'] + $cdrow->resource_duration_direct_cost;
			}
		}
		$projects['direct_cost']   = $directcost;
		
		$month_contribution_query = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code, direct_cost_per_hour, resource_duration_direct_cost
		FROM crm_timesheet_data 
		WHERE start_time between '".date('Y-m-d', strtotime($month))."' and '".date('Y-m-t', strtotime($month))."' AND resoursetype != '' AND project_code NOT IN ('HOL','Leave')";
		
		$sql2 = $this->db->query($month_contribution_query);
		$month_contribution_data = $sql2->result();
		if(!empty($month_contribution_data)) {
			foreach($month_contribution_data as $mcdrow) {
				$cm_directcost[$practice_arr[$mcdrow->practice_id]]['total_cm_direct_cost'] = $cm_directcost[$practice_arr[$mcdrow->practice_id]]['total_cm_direct_cost'] + $mcdrow->resource_duration_direct_cost;
			}
		}
		// echo "<pre>"; print_r($cm_directcost); die;
		$projects['cm_direct_cost'] = $cm_directcost;
		$data['projects'] = $projects;
		
		echo "<pre>"; print_r($projects); exit;
		
		
	}

}
?>