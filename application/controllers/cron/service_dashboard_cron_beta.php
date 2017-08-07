<?php

/********************************************************************************
File Name       : service_dashboard_cron_beta.php
Created Date    : 16/06/2016
Modified Date   : 16/06/2016
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Subbiah.S
*********************************************************************************/

/**
 * Service_dashboard_cron_beta
 *
 * @class 		Service_dashboard_cron_beta
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */
//error_reporting(E_ALL);
class Service_dashboard_cron_beta extends crm_controller 
{
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('text');
		$this->load->helper('url');
		$this->load->helper('custom');
		$this->load->model('report/report_lead_region_model');
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
		
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $end_date   = $curFiscalYear."-".date('m-d'); //eg.2014-03-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month 		= date('Y-m-01 00:00:00');
		$start_date = date("Y-m-01",strtotime($start_date));
		$end_date 	= date("Y-m-t", strtotime($end_date));

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
		$practice_not_in = array(6);
		$this->db->where_not_in('p.id', $practice_not_in);
		//$this->db->where_in('p.id', array(1));
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();		
				
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
				$practice_array[] = $prow->practices;
			}
		}
		
		//for othercost projects
		$this->db->select("pjt_id, lead_id, practice, lead_title");
		$ocres  = $this->db->get_where($this->cfg['dbpref']."leads", array("pjt_id !=" => '',"practice !=" => '', "practice !=" => 6)); //for temporary use
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

		$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		$this->db->where("l.customer_type", '1');
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		//BPO practice are not shown in IT Services Dashboard
		// $this->db->where_not_in("l.practice", 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('l.practice', $practice_not_in);

		if($project_status){
			if($project_status !=2)
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}

		$query = $this->db->get();
		$res = $query->result_array();
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
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
		// echo $this->db->last_query(); exit;
		$invoices_data = $query1->result_array();
		
		if(!empty($invoices_data) && count($invoices_data)>0) {
			foreach($invoices_data as $ir) {
				if($practice_arr[$ir['practice']] == 'Testing' || $practice_arr[$ir['practice']] == 'Infra Services'){
					$practice_arr[$ir['practice']] = 'Others';
				}
				$base_conver_amt = $this->conver_currency($ir['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				$projects['irval'][$practice_arr[$ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
			}
		}
		
		//for current month ir
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, cc.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		//BPO & Testing practice are not shown in IT Services Dashboard
		// $this->db->where_not_in("l.practice", 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('l.practice', $practice_not_in);
		if(!empty($month)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($month)));
			$this->db->where("sfv.for_month_year <= ", date('Y-m-t H:i:s', strtotime($month)));
		}
		
		$query5 = $this->db->get();
		$cm_invoices_data = $query5->result_array();

		if(!empty($cm_invoices_data) && count($cm_invoices_data)>0) {
			foreach($cm_invoices_data as $cm_ir) {
				if($practice_arr[$cm_ir['practice']] == 'Testing' || $practice_arr[$cm_ir['practice']] == 'Infra Services') {
					$practice_arr[$cm_ir['practice']] = 'Others';
				}
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
				// $this->db->where_not_in("l.practice", 6);
				$practice_not_in = array(6);
				$this->db->where_not_in('l.practice', $practice_not_in);
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

		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
	 
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-d', strtotime($end_date)));
		}
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
		
		if(count($resdata)>0) {
			$rates = $this->get_currency_rates();
			foreach($resdata as $rec) {		
				$financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
				$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id,$financialYear);
				
				$timesheet_data[$rec->username]['practice_id'] 	= $rec->practice_id;
				$timesheet_data[$rec->username]['max_hours'] 	= $max_hours_resource->practice_max_hours;
				$timesheet_data[$rec->username]['dept_name'] 	= $rec->dept_name;
				
				$rateCostPerHr 		 = round($rec->cost_per_hour*$rates[1][$this->default_cur_id], 2);
				$directrateCostPerHr = round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr']        = $rateCostPerHr;
			}

			if(count($timesheet_data)>0 && !empty($timesheet_data)) {
				foreach($timesheet_data as $dept_arr=>$resource_type_arr) {
					if(!empty($resource_type_arr) && count($resource_type_arr)>0) {
						foreach($resource_type_arr as $key1=>$value1) {
							$resource_name 	= $key1;
							$max_hours 		= $value1['max_hours'];
							$dept_name 		= $value1['dept_name'];
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
		
		## code month contribution starts here##
		
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
	 
		if(!empty($month)) {
			$this->db->where("t.start_time >= ", date('Y-m-01 H:i:s', strtotime($month)));
			$this->db->where("t.start_time <= ", date('Y-m-t H:i:s', strtotime($month)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$resrc = 't.resoursetype IS NOT NULL';
		$this->db->where($resrc);
		$deptwhere = "t.dept_id IN ('10','11')";
		$this->db->where($deptwhere);
		$this->db->where("l.practice is not null");
		$query = $this->db->get();
		$resdata = $query->result();

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
		
		if(count($resdata)>0) {
			$rates = $this->get_currency_rates();
			foreach($resdata as $rec) {		
				$financialYear 		= get_current_financial_year($rec->yr,$rec->month_name);
				$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id,$financialYear);
				
				$timesheet_data[$rec->username]['practice_id'] = $rec->practice_id;
				$timesheet_data[$rec->username]['max_hours'] = $max_hours_resource->practice_max_hours;
				$timesheet_data[$rec->username]['dept_name'] = $rec->dept_name;
				
				$rateCostPerHr = round($rec->cost_per_hour*$rates[1][$this->default_cur_id], 2);
				$directrateCostPerHr = round($rec->direct_cost_per_hour*$rates[1][$this->default_cur_id], 2);
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] =get_timesheet_hours_by_user($rec->username,$rec->yr,$rec->month_name,array('Leave','Hol'));
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
				
			}

		if(count($timesheet_data)>0 && !empty($timesheet_data)){
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
								$individual_billable_hrs		= 0;
								$tsh_month		 	  = $key3;
								if(count($value3)>0 && !empty($value3)){
									foreach($value3 as $key4=>$value4) {
										if($key4 != 'total_hours'){ 
											$individual_billable_hrs = $value3['total_hours'];
											$duration_hours			 = $value4['duration_hours'];
											$direct_rateperhr	     = $value4['direct_rateperhr'];
											$direct_rateperhr1 = $direct_rateperhr;
											if($individual_billable_hrs>$max_hours){
												$percentage 		= ($max_hours/$individual_billable_hrs);
												$rate1 				= number_format(($percentage*$direct_rateperhr),2);
												$direct_rateperhr1 	= number_format(($percentage*$direct_rateperhr),2);
											}
											$resource_cost[$resource_name][$year][$tsh_month][$key4]['total_dc_cost'] += ($duration_hours*$direct_rateperhr1);
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
								foreach($array2 as $rsc_month => $array3){
									$total_dc_cost = 0;
									foreach($array3 as $project_code => $array4){
										$total_dc_cost = $array4['total_dc_cost'];
										$cm_directcost1[$project_code]['project_total_cm_direct_cost'] += $total_dc_cost;
									}
								}
							}
						}
					}
				}
			}
		}
		$this->db->select("pjt_id,practice,lead_title");
		$res = $this->db->get_where($this->cfg['dbpref']."leads",array("pjt_id !=" => '',"practice !=" => ''));
		$project_res = $res->result();
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec) {
				$cm_directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_cm_direct_cost'] += $cm_directcost1[$prec->pjt_id]['project_total_cm_direct_cost'];			
			}
		}
 
		foreach($cm_directcost2 as $practiceId => $cval1){
			if(!empty($practiceId)) {
				if($practiceId == 'Testing' || $practiceId == 'Infra Services') {
					$practiceId = 'Others';
				}
				foreach($cval1 as $pjtCode => $cval){ 
					$cm_directcost[$practiceId]['total_cm_direct_cost'] += $cval['total_cm_direct_cost'];
				}
			}
		}
		
		## code month contribution ends here##
		
		$projects['direct_cost']    = $directcost;
		$projects['cm_direct_cost'] = $cm_directcost;
		$data['projects']           = $projects; 
		$ins_array = array();
		$tot = array();
		$totCM_Irval = $tot_Irval = $tot_billhour = $tot_tothours = $tot_billval = $tot_totbillval = $tot_actual_hr = $tot_estimated_hrs = $tot_cm_irvals = $tot_cm_dc_tot = $tot_dc_vals = $tot_dc_tots = 0;

		if(!empty($practice_array)){
			
			//delete the old records & inserting the practices name from table.	
			$this->db->where('month_status', 1);
			$this->db->delete($this->cfg['dbpref'].'services_dashboard_beta');			
			foreach($practice_array as $parr){
				$ins_data['practice_name'] = $parr;
				$ins_data['month_status'] = 1;
				$this->db->insert($this->cfg['dbpref'] . 'services_dashboard_beta', $ins_data);
			}
			//exit;
			$ins_data['practice_name'] = 'Total';
			$ins_data['month_status'] = 1;
			$this->db->insert($this->cfg['dbpref'] . 'services_dashboard_beta', $ins_data);
			
			foreach($practice_array as $parr){
				/**other cost data*/
				$other_cost_val 	= 0;
				$cm_other_cost_val  = 0;
				if(isset($projects['othercost_projects']) && !empty($projects['othercost_projects'][$parr]) && count($projects['othercost_projects'][$parr])>0) {
					foreach($projects['othercost_projects'][$parr] as $pro_id) {
						$val 	= getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $start_date, $end_date);
						$cm_val = getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $month, $month);
						$other_cost_val    += $val;
						$cm_other_cost_val += $cm_val;
					}
					$projects['other_cost'][$parr] = $other_cost_val;
					$projects['cm_other_cost'][$parr] = $cm_other_cost_val;
				}
				if($parr != 'Infra Services' || $parr != 'Testing') {
					$totCM_Irval += $projects['cm_irval'][$parr];
					$tot_Irval   += $projects['irval'][$parr];
				}
				/**other cost data*/
				if($parr == 'Infra Services' || $parr == 'Testing') {
					// $parr = 'Others';
					continue;
				}
				
				$ins_array['billing_month'] = ($projects['cm_irval'][$parr] != '') ? round($projects['cm_irval'][$parr]) : '-';
				$ins_array['ytd_billing']   = ($projects['irval'][$parr] != '') ? round($projects['irval'][$parr]) : '-';
				
				$temp_ytd_utilization_cost = $projects['direct_cost'][$parr]['total_direct_cost'] + $projects['other_cost'][$parr];
				$ins_array['ytd_utilization_cost'] = ($temp_ytd_utilization_cost != '') ? round($temp_ytd_utilization_cost) : '-';
				
				$cm_billval = $billval = $eff_var = $cm_dc_val = $dc_val = 0;
				$cm_billval = (($projects['billable_month'][$parr]['Billable']['hour'])/$projects['billable_month'][$parr]['totalhour'])*100;
				$ins_array['billable_month'] = ($cm_billval != 0) ? round($cm_billval) : '-';
				
				$billval = (($projects['billable_ytd'][$parr]['Billable']['hour'])/$projects['billable_ytd'][$parr]['totalhour'])*100;
				$ins_array['ytd_billable']   = ($billval != 0) ? round($billval) : '-';
				
				$eff_var = (($projects['eff_var'][$parr]['total_actual_hrs'] - $projects['eff_var'][$parr]['tot_estimate_hrs'])/$projects['eff_var'][$parr]['tot_estimate_hrs'])*100;
				$ins_array['effort_variance'] = ($eff_var != 0) ? round($eff_var) : '-';
				$temp_cm_utd_cost = $projects['cm_direct_cost'][$parr]['total_cm_direct_cost'] + $projects['cm_other_cost'][$parr];
				if($temp_cm_utd_cost){
					$cm_dc_val = (($projects['cm_irval'][$parr] - $temp_cm_utd_cost)/$projects['cm_irval'][$parr]) * 100;
				}
				$ins_array['contribution_month'] = ($cm_dc_val != 0) ? round($cm_dc_val) : '-';
				$dc_val = (($projects['irval'][$parr] - $temp_ytd_utilization_cost)/$projects['irval'][$parr]) * 100;
				$ins_array['ytd_contribution'] = ($dc_val != 0) ? round($dc_val) : '-';
				$ins_array['month_status'] 	   = 1;
				
				$tot_billhour += $projects['billable_month'][$parr]['Billable']['hour'];
				$tot_tothours += $projects['billable_month'][$parr]['totalhour'];
				
				$tot_billval += $projects['billable_ytd'][$parr]['Billable']['hour'];
				$tot_totbillval += $projects['billable_ytd'][$parr]['totalhour'];
			
				$tot_actual_hr += $projects['eff_var'][$parr]['total_actual_hrs'];
				$tot_estimated_hrs += $projects['eff_var'][$parr]['tot_estimate_hrs'];
				
				$tot_cm_irvals += $projects['cm_irval'][$parr];
				$tot_cm_dc_tot += $temp_cm_utd_cost;
				
				$tot_dc_vals += $projects['irval'][$parr];
				$tot_dc_tots += $temp_ytd_utilization_cost;
				
				$this->db->where(array('practice_name' => $parr,'month_status' => 1));
				$this->db->update($this->cfg['dbpref'] . 'services_dashboard_beta', $ins_array);
				$ins_array = array();
			}
			
			$tot['billing_month'] 		 = $totCM_Irval;
			$tot['ytd_billing']   		 = $tot_Irval;
			$tot['ytd_utilization_cost'] = $tot_dc_tots;
			$tot['billable_month'] 		 = round(($tot_billhour/$tot_tothours)*100);
			$tot['ytd_billable'] 		 = round(($tot_billval/$tot_totbillval)*100);
			$tot['effort_variance'] 	 = round((($tot_actual_hr-$tot_estimated_hrs)/$tot_estimated_hrs)*100);
			$cmonth						 = '-';
			//if($tot_cm_dc_tot){
			$cmonth 					 = round((($tot_cm_irvals-$tot_cm_dc_tot)/$tot_cm_irvals)*100);	
			//}
			$tot['contribution_month'] 	 = $cmonth;
			$tot['ytd_contribution'] 	 = round((($tot_dc_vals-$tot_dc_tots)/$tot_dc_vals)*100);
			$tot['month_status'] 		 = 1;

			//updating the total values
			$this->db->where(array('practice_name' => 'Total','month_status' => 1));
			$this->db->update($this->cfg['dbpref'] . 'services_dashboard_beta', $tot);
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
		// $this->db->where_not_in("practice_id", 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('practice_id', $practice_not_in);
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
				if (isset($resarr[$practice_arr[$row->practice_id]]['totalhour'])) {
					$resarr[$practice_arr[$row->practice_id]]['totalhour'] = $resarr[$practice_arr[$row->practice_id]]['totalhour'] + $row->duration_hours;
					$resarr[$practice_arr[$row->practice_id]]['totalcost'] = $resarr[$practice_arr[$row->practice_id]]['totalcost'] + $row->resource_duration_cost;
				} else {
					$resarr[$practice_arr[$row->practice_id]]['totalhour'] = $row->duration_hours;
					$resarr[$practice_arr[$row->practice_id]]['totalcost'] = $row->resource_duration_cost;
				}
				
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