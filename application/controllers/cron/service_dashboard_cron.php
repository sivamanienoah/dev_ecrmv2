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
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
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
		
		/* $this->db->select('div_id, division_name, base_currency');
		$this->db->from($this->cfg['dbpref']. 'sales_divisions');
		$equery = $this->db->get();
		$eres = $equery->result();
		$data['entity_data'] = $equery->result(); 
		
		if(!empty($eres) && count($eres)>0){
			foreach($eres as $erow) {
				$base_cur_arr[$erow->div_id] = $erow->base_currency;
			}
		}*/
		
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
				$practice_array[] = $prow->practices;
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
				$base_conver_amt = $this->conver_currency($ir['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
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
		
		// echo "<pre>"; print_r($projects); echo "</pre>";
		
		$ins_array = array();
		$tot = array();
		$totCM_Irval = $tot_Irval = $tot_billhour = $tot_tothours = $tot_billval = $tot_totbillval = $tot_actual_hr = $tot_estimated_hrs = $tot_cm_irvals = $tot_cm_dc_tot = $tot_dc_vals = $tot_dc_tots = 0;
		
		if(!empty($practice_array)){
			foreach($practice_array as $parr){					
					$ins_array['billing_month'] = ($projects['cm_irval'][$parr] != '') ? round($projects['cm_irval'][$parr]) : '-';
					$ins_array['ytd_billing']   = ($projects['irval'][$parr] != '') ? round($projects['irval'][$parr]) : '-';
					$ins_array['ytd_utilization_cost'] = ($projects['direct_cost'][$parr]['total_direct_cost'] != '') ? round($projects['direct_cost'][$parr]['total_direct_cost']) : '-';
					$cm_billval = $billval = $eff_var = $cm_dc_val = $dc_val = 0;
					$cm_billval = (($projects['billable_month'][$parr]['Billable']['hour'])/$projects['billable_month'][$parr]['totalhour'])*100;
					$ins_array['billable_month'] = ($cm_billval != 0) ? round($cm_billval) : '-';
					
					$billval = (($projects['billable_ytd'][$parr]['Billable']['hour'])/$projects['billable_ytd'][$parr]['totalhour'])*100;
					$ins_array['ytd_billable']   = ($billval != 0) ? round($billval) : '-';
					
					$eff_var = (($projects['eff_var'][$parr]['total_actual_hrs'] - $projects['eff_var'][$parr]['tot_estimate_hrs'])/$projects['eff_var'][$parr]['tot_estimate_hrs'])*100;
					$ins_array['effort_variance'] = ($eff_var != 0) ? round($eff_var) : '-';
					
					$cm_dc_val = (($projects['cm_irval'][$parr] - $projects['cm_direct_cost'][$parr]['total_cm_direct_cost'])/$projects['cm_irval'][$parr]) * 100;
					$ins_array['contribution_month'] = ($cm_dc_val != 0) ? round($cm_dc_val) : '-';
					$dc_val = (($projects['irval'][$parr] - $projects['direct_cost'][$parr]['total_direct_cost'])/$projects['irval'][$parr]) * 100;
					$ins_array['ytd_contribution'] = ($dc_val != 0) ? round($dc_val) : '-';
					
					// $totCM_Irval += $projects['cm_irval'][$parr];
					// $tot_Irval   += $projects['irval'][$parr];
					
					// $tot_dc_values += $projects['irval'][$parr];
					// $tot_dc_totals += $projects['direct_cost'][$parr]['total_direct_cost'];
					
					/* $tot_billhour += $projects['billable_month'][$parr]['Billable']['hour'];
					$tot_tothours += $projects['billable_month'][$parr]['totalhour'];
					
					$tot_billval += $projects['billable_ytd'][$parr]['Billable']['hour'];
					$tot_totbillval += $projects['billable_ytd'][$parr]['totalhour'];
				
					$tot_actual_hr += $projects['eff_var'][$parr]['total_actual_hrs'];
					$tot_estimated_hrs += $projects['eff_var'][$parr]['tot_estimate_hrs'];
					
					$tot_cm_irvals += $projects['cm_irval'][$parr];
					$tot_cm_dc_tot += $projects['cm_direct_cost'][$parr]['total_cm_direct_cost'];
					
					$tot_dc_vals += $projects['irval'][$parr];
					$tot_dc_tots += $projects['direct_cost'][$parr]['total_direct_cost']; */
					
					$this->db->where('practice_name', $parr);
					$this->db->update($this->cfg['dbpref'] . 'services_dashboard', $ins_array);
					$ins_array = array();
			}
			
			/* $tot['billing_month'] = $totCM_Irval;
			$tot['ytd_billing']   = $tot_Irval;
			$tot['ytd_utilization_cost'] = $tot_dc_tots;
			$tot['billable_month'] = round(($tot_billhour/$tot_tothours)*100);
			$tot['ytd_billable'] = round(($tot_billval/$tot_totbillval)*100);
			$tot['effort_variance'] = round((($tot_actual_hr-$tot_estimated_hrs)/$tot_estimated_hrs)*100);
			$tot['contribution_month'] = round((($tot_cm_irvals-$tot_cm_dc_tot)/$tot_cm_irvals)*100);
			$tot['ytd_contribution'] = round((($tot_dc_vals-$tot_dc_tots)/$tot_dc_vals)*100);
			
			//updating the total values
			$this->db->where('practice_name', 'Total');
			$this->db->update($this->cfg['dbpref'] . 'services_dashboard', $tot);
			echo $this-db->last_query(); */
			
		}
		
	}
	
	/*
	*@Get Current Financial year
	*@Method  calculateFiscalYearForDate
	*/
	function calculateFiscalYearForDate($inputDate, $fyStart, $fyEnd) 
	{
		$date = strtotime($inputDate);
		$inputyear = strftime('%Y',$date);
	 
		$fystartdate = strtotime($fyStart.'/'.$inputyear);
		$fyenddate = strtotime($fyEnd.'/'.$inputyear);
	 
		if($date <= $fyenddate){
			$fy = intval($inputyear);
		}else{
			$fy = intval(intval($inputyear) + 1);
		}
	
		return $fy;
	}
	
	public function conver_currency($amount, $val) 
	{
		return round($amount*$val, 2);
	}
	
	public function get_timesheet_data($practice_arr, $start_date=false, $end_date=false, $month=false)
	{
		// echo "<pre>"; print_r($practice_arr);
		$prs = array();
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$pract = $pquery->result();
		if(!empty($pract) && count($pract)>0){
			foreach($pract as $pr){
				$prs[] = $pr->id;
			}
		}
		
		$this->db->select('dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code');
		$this->db->from($this->cfg['dbpref'].'timesheet_data');
		$tswhere = "resoursetype is NOT NULL";
		$this->db->where($tswhere);
		$excludewhere = "project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$this->db->where('practice_id !=', 0);
		$this->db->where_not_in("practice_id", 6);
		//for eads & eqad only
		$deptwhere = "dept_id in ('10','11')";
		$this->db->where($deptwhere);
		if(!empty($start_date)) {
			$this->db->where("DATE(start_time) >= ", date('Y-m-d', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("DATE(start_time) <= ", date('Y-m-d', strtotime($end_date)));
		}
		if(!empty($month)) {
			$this->db->where("DATE(start_time) >= ", date('Y-m-d', strtotime($month)));
			$this->db->where("DATE(end_time) <= ", date('Y-m-t', strtotime($month)));
		}
		$query2 = $this->db->get();
		// echo $this->db->last_query(); die;
		$timesheet_data = $query2->result();
		
		// echo "<pre>"; print_r($timesheet_data); die;
		
		$resarr = array();

		if(count($timesheet_data)>0) {
			foreach($timesheet_data as $row) {
				// echo $row->practice_id . " " . $row->resoursetype; exit;
				if (isset($resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'])) {
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'] = $row->duration_hours + $resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'];
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['cost'] = $row->resource_duration_cost + $resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['cost'];
				} else {
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'] = $row->duration_hours;
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['cost'] = $row->resource_duration_cost;
				}
				$resarr[$practice_arr[$row->practice_id]]['totalhour'] = $resarr[$practice_arr[$row->practice_id]]['totalhour'] + $row->duration_hours;
				$resarr[$practice_arr[$row->practice_id]]['totalcost'] = $resarr[$practice_arr[$row->practice_id]]['totalcost'] + $row->resource_duration_cost;
				if(!empty($start_date) && !empty($end_date)) {
					if(!in_array($row->project_code, $resarr['project_code'])){
						$resarr['project_code'][] = $row->project_code;
					}
				}
			}
		}
		/* if(!empty($start_date) && !empty($end_date)) {
			echo "<pre>"; print_r($resarr); die;
		} */
		return $resarr;
	}
	
	public function get_timesheet_actual_hours($pjt_code, $start_date=false, $end_date=false, $month=false)
	{
		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, SUM(ts.duration_hours) as duration_hours, ts.resoursetype, ts.username, ts.empname, ts.direct_cost_per_hour as direct_cost, sum( ts.`resource_duration_direct_cost`) as duration_direct_cost, sum( ts.`resource_duration_cost`) as duration_cost');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_data as ts');
		$this->db->where("ts.project_code", $pjt_code);
		if( (!empty($start_date)) && (!empty($end_date)) ){
			$this->db->where("DATE(ts.start_time) >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("DATE(ts.start_time) <= ", date('Y-m-d', strtotime($end_date)));
		}
		if(!empty($month)) {
			$this->db->where("DATE(ts.start_time) >= ", date('Y-m-d', strtotime($month)));
			$this->db->where("DATE(ts.end_time) <= ", date('Y-m-t', strtotime($month)));
		}
		$this->db->group_by(array("ts.username", "yr", "month_name", "ts.resoursetype"));
		
		$query = $this->db->get();
		$timesheet = $query->result_array();
		$res = array();
		// echo "<pre>"; print_r($timesheet); exit;
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				$res['total_cost']     += $ts['duration_cost'];
				$res['total_hours']    += $ts['duration_hours'];
				$res['total_dc'] 	   += $ts['duration_direct_cost'];
			}
		}
		// echo "<pre>"; print_r($res); exit;
		return $res;
	}

}
?>