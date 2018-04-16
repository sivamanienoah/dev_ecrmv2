<?php

/********************************************************************************
File Name       : project_pl_report_cron.php
Created Date    : 10/04/2018
Modified Date   : 10/04/2018
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Subbiah.S
*********************************************************************************/

/**
 * project_pl_report_cron
 *
 * @class 		project_pl_report_cron
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */
//error_reporting(E_ALL);
class Project_pl_report_cron extends crm_controller 
{
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('custom');
        $this->load->library('validation');
		$this->load->helper('lead_stage');
		$this->load->helper('url'); 
		$this->load->model('projects/dashboard_model');
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
    }
	
	/*
	*method : get_currency_rates
	*/
	public function get_currency_rates() {
		$this->load->model('report/report_lead_region_model');
		$currency_rates = $this->report_lead_region_model->get_currency_rate();
    	$rates 			= array();
    	if(!empty($currency_rates)) {
    		foreach ($currency_rates as $currency) {
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}	
	
	public function index() 
	{
		@set_time_limit(-1); //disable the mysql query maximum execution time
		$data  				  = array();
		$bk_rates = get_book_keeping_rates();
		
		$post_data 		  = $this->input->post();
		// echo '<pre>'; print_r($post_data); die;
		
		$curFiscalYear = calculateFiscalYearForDateHelper(date("m/d/y"),"4/1","3/31");
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
		} else {
			$default_fy_start_month = '04';
			$start_date = calc_fy_dates($curFiscalYear, $default_fy_start_month, 'start');
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		} else {
			$default_fy_end_month = date('m');
			$end_date 			  = calc_fy_dates($curFiscalYear, $default_fy_end_month, 'end');
		}
		
		$data['start_date']  = $start_date;
		$data['end_date']    = $end_date;
		

		//for othercost projects
		$this->db->select("pjt_id, lead_id, practice, lead_title");
		$ocres  = $this->db->get_where($this->cfg['dbpref']."leads", array("pjt_id !=" => '',"practice !=" => '', "practice !=" => 6));
		$oc_res = $ocres->result_array();
		
		if(!empty($oc_res)) {
			foreach($oc_res as $ocrow) {
				if (isset($projects['othercost_projects'][$practice_arr[$ocrow['practice']]])) {
					$projects['othercost_projects'][$practice_arr[$ocrow['practice']]][] = $ocrow['lead_id'];
				} else {
					$projects['othercost_projects'][$practice_arr[$ocrow['practice']]][] = $ocrow['lead_id'];
				}
			}
		}
			
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, cc.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		//BPO practice are not shown in IT Services Dashboard
		// $this->db->where_not_in("l.practice", 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('l.practice', $practice_not_in);
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
				if($practice_arr[$ir['practice']] == 'Testing' || $practice_arr[$ir['practice']] == 'Infra Services'){
					$practice_arr[$ir['practice']] = 'Others';
				}
				$base_conver_amt = $this->conver_currency($ir['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				if(isset($projects['irval'][$practice_arr[$ir['practice']]])) {
					$projects['irval'][$practice_arr[$ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				} else {
					$projects['irval'][$practice_arr[$ir['practice']]] = $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				}
			}
		}
			
		//Contribution
		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.resource_total_hours, t.practice_id, t.practice_name');		
		$this->db->from($this->cfg['dbpref'].'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
		// $this->db->join($this->cfg['dbpref'].'practices as p', 'p.id = l.practice');
	 
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-d', strtotime($end_date)));
		}
		//exclude internal projects
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("t.client_code", $client_not_in_arr);
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$resrc = 't.resoursetype IS NOT NULL';
		$this->db->where($resrc);
		$deptwhere = "t.dept_id IN ('10','11')";
		$this->db->where($deptwhere);
		$this->db->where("l.practice is not null");
		$query 	 = $this->db->get();
		$resdata = $query->result();
		## code starts here##
		$tbl_data = array();
		$sub_tot  = array();
		$cost_arr = array();
		$directcost_arr = array();
		$usercnt  = array();
		$prjt_hr  = array();
		$prjt_cst = array();
		$prjt_directcst = array();
		$prac = array();
		$dept = array();
		$skil = array();
		$proj = array();
		$tot_hour = 0;
		$tot_cost = 0;
		$tot_directcost = 0;		
		$timesheet_data = array();
		$resource_cost = array();
		
		$rates = $this->get_currency_rates();
			
		//get all the hours for practice by financial year wise
		$practice_id_year_array = $this->dashboard_model->get_practice_max_hrs_by_fiscal_year();
		$practice_id_array  	= $this->dashboard_model->get_practice_max_hr();
			
		if(count($resdata)>0) {				
			foreach($resdata as $rec) {		
				$financialYear 			= get_current_financial_year($rec->yr, $rec->month_name);
				$max_hrs 				= 0;
				if(isset($practice_id_year_array[$rec->practice_id][$financialYear])) {
					$max_hrs = $practice_id_year_array[$rec->practice_id][$financialYear];
				} else if(isset($practice_id_array[$rec->practice_id])) {
					$max_hrs = $practice_id_array[$rec->practice_id];
				}
				//get all the hours for practice by financial year wise
				
				$timesheet_data[$rec->username]['practice_id'] 	= $rec->practice_id;
				// $timesheet_data[$rec->username]['max_hours'] = $max_hours_resource->practice_max_hours;
				$timesheet_data[$rec->username]['max_hours'] 	= $max_hrs;
				$timesheet_data[$rec->username]['dept_name'] 	= $rec->dept_name;
				
				$rateCostPerHr 		 = round($rec->cost_per_hour * $rates[1][$this->default_cur_id], 2);
				$directrateCostPerHr = round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
				if(isset($timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'])) {
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
				} else {
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours']   = $rec->duration_hours;
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr']        = $rateCostPerHr;
				}
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = $rec->resource_total_hours;
			}
			
			if(count($timesheet_data)>0 && !empty($timesheet_data)) {
				foreach($timesheet_data as $key1=>$value1) {
					$resource_name = $key1;
					
					$max_hours = $value1['max_hours'];
					$dept_name = $value1['dept_name'];
					$resource_cost[$resource_name]['dept_name'] = $dept_name;
					if(count($value1)>0 && !empty($value1)){
						foreach($value1 as $key2=>$value2) {
							$year = $key2;
							if(count($value2)>0 && !empty($value2)){
								foreach($value2 as $key3=>$value3) {
									$individual_billable_hrs = 0;
									$ts_month		 	  	 = $key3;
									if(count($value3)>0 && !empty($value3)){
										foreach($value3 as $key4=>$value4) {
											if($key4 != 'total_hours'){ 
												$individual_billable_hrs = $value3['total_hours'];
												$duration_hours			 = $value4['duration_hours'];
												$rate				 	 = $value4['rateperhr'];
												$direct_rateperhr	 	 = $value4['direct_rateperhr'];
												$rate1 					 = $rate;
												$direct_rateperhr1 		 = $direct_rateperhr;
												if($individual_billable_hrs>$max_hours) {
													$percentage 		= ($max_hours/$individual_billable_hrs);
													$rate1 				= number_format(($percentage*$direct_rateperhr),2);
													$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
												}
												if($value1['practice_id'] == 0) {
													$direct_rateperhr1  = $direct_rateperhr;
												}
												$resource_cost[$resource_name][$year][$ts_month][$key4]['duration_hours'] += $duration_hours;
												$resource_cost[$resource_name][$year][$ts_month][$key4]['total_cost'] 	  += ($duration_hours*$direct_rateperhr1);
												$resource_cost[$resource_name][$year][$ts_month][$key4]['practice_id'] 	   = ($duration_hours*$rate1);
												$resource_cost[$resource_name][$year][$ts_month][$key4]['total_dc_cost']  += ($duration_hours*$direct_rateperhr1);
											}
										}
									}
								}
							}
						}
					}
				}	 
			}
		}
		if(count($resource_cost)>0 && !empty($resource_cost)){
			foreach($resource_cost as $resourceName => $array1){
				$dept_name = $resource_cost[$resourceName]['dept_name'];
				if(count($array1)>0 && !empty($array1)){
					foreach($array1 as $year => $array2){
						if($year !='dept_name'){
							if(count($array2)>0 && !empty($array2)){
								foreach($array2 as $rs_month => $array3){
									$duration_hours = 0;
									$total_cost = 0;
									$total_dc_cost = 0;
									foreach($array3 as $project_code => $array4){
										$duration_hours = $array4['duration_hours'];
										$total_cost 	= $array4['total_cost'];
										$total_dc_cost 	= $array4['total_dc_cost'];
										$directcost1[$project_code]['project_total_direct_cost'] += $total_cost;
									}
								}
							}
						}
					}
				}
			}
		}
		// echo "After resource cost calc" . date('d-m-Y H:i:s') . "<br>"; die;
		// echo '<pre><br><br><br><br><br>'; print_r($directcost1); die;
		$this->db->select("pjt_id,practice,lead_title");
		$res = $this->db->get_where($this->cfg['dbpref']."leads",array("pjt_id !=" => '',"practice !=" => ''));
		$project_res = $res->result();
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec){
				$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'] += $directcost1[$prec->pjt_id]['project_total_direct_cost'];
			}
		}
			
		foreach($directcost2 as $practiceId => $val1){
			if(!empty($practiceId)) {
				if($practiceId == 'Testing' || $practiceId == 'Infra Services') {
					$practiceId = 'Others';
				}
				foreach($val1 as $pjtCode => $val){
					$directcost[$practiceId]['total_direct_cost'] += $val['total_direct_cost'];
				}
			}
		}
		## code ends here##
			
		$projects['direct_cost']    = $directcost;
		$data['projects'] 			= $projects;
		// echo '<pre>'; print_R($projects); die;
		$ins_array = array();
		$show_arr  = array();
		$tot = array();
		$totCM_Irval = $tot_Irval = $tot_billhour = $tot_tothours = $tot_billval = $tot_totbillval = $tot_actual_hr = $tot_estimated_hrs = $tot_cm_irvals = $tot_cm_dc_tot = $tot_dc_vals = $tot_dc_tots = 0;
		
		foreach($practice_array as $prarr){
			/**other cost data*/
			$other_cost_val 	= 0;
			$cm_other_cost_val  = 0;
			if(isset($projects['othercost_projects']) && !empty($projects['othercost_projects'][$prarr]) && count($projects['othercost_projects'][$prarr])>0) {
				foreach($projects['othercost_projects'][$prarr] as $pro_id) {
					$val 	= getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $start_date, $end_date);
					$cm_st_mon = date('Y-m-01 H:i:s', strtotime($month));
					$cm_ed_mon = date('Y-m-t H:i:s', strtotime($month));
					$cm_val = getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $cm_st_mon, $cm_ed_mon);
					$other_cost_val    += $val;
					$cm_other_cost_val += $cm_val;
				}
				$projects['other_cost'][$prarr] = $other_cost_val;
				$projects['cm_other_cost'][$prarr] = $cm_other_cost_val;
			}
			if($prarr != 'Infra Services' || $prarr != 'Testing') {
				// $totCM_Irval += $projects['cm_irval'][$prarr];
				$tot_Irval   += $projects['irval'][$prarr];
				// echo "$prarr " . $tot_Irval . "<br>";
			}
			/**other cost data*/
			if($prarr == 'Infra Services' || $prarr == 'Testing') {
				// $prarr = 'Others';
				continue;
			}
			
			$ins_array['billing_month'] = ($projects['cm_irval'][$prarr] != '') ? round($projects['cm_irval'][$prarr]) : '-';
			$ins_array['ytd_billing']   = ($projects['irval'][$prarr] != '') ? round($projects['irval'][$prarr]) : '-';
			$temp_ytd_utilization_cost = $projects['direct_cost'][$prarr]['total_direct_cost'] + $projects['other_cost'][$prarr];
			$ins_array['ytd_utilization_cost'] = ($temp_ytd_utilization_cost != '') ? round($temp_ytd_utilization_cost) : '-';
			
			$cm_billval = $billval = $eff_var = $cm_dc_val = $dc_val = 0;
			
			$temp_cm_utd_cost = $projects['cm_direct_cost'][$prarr]['total_cm_direct_cost'] + $projects['cm_other_cost'][$prarr];
			if($temp_cm_utd_cost){
				$cm_dc_val = (($projects['cm_irval'][$prarr] - $temp_cm_utd_cost)/$projects['cm_irval'][$prarr]) * 100;
			}
			$ins_array['contribution_month'] = ($cm_dc_val != 0) ? round($cm_dc_val) : '-';
			$dc_val = (($projects['irval'][$prarr] - $temp_ytd_utilization_cost)/$projects['irval'][$prarr]) * 100;
			$ins_array['ytd_contribution'] = ($dc_val != 0) ? round($dc_val) : '-';
			$ins_array['month_status'] 	   = 1;
			
			$tot_billval += $projects['billable_ytd'][$prarr]['Billable']['hour'];
			$tot_totbillval += $projects['billable_ytd'][$prarr]['totalhour'];
			
			// echo '<br>'.$prarr.'<br>tot_cm_irvals';
			$tot_cm_irvals += $projects['cm_irval'][$prarr];
			// echo '<br>tot_cm_dc_tot';
			$tot_cm_dc_tot += $temp_cm_utd_cost;
			// echo '<br>tot_dc_vals';
			$tot_dc_vals += $projects['irval'][$prarr];
			// echo '<br>tot_dc_tots';
			$tot_dc_tots += $temp_ytd_utilization_cost;
			
			$show_arr[$prarr] = $ins_array;
		}
			
		//for total
		$show_arr['Total']['ytd_billing']   	   	= $tot_Irval;
		$show_arr['Total']['ytd_utilization_cost'] 	= $tot_dc_tots;
		$show_arr['Total']['ytd_contribution'] 	 	= round((($tot_dc_vals-$tot_dc_tots)/$tot_dc_vals)*100);
		
		$data['dashboard_det'] = $show_arr;
		echo '<pre>'; print_r($show_arr); die;
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
	
	
	
	

}
?>