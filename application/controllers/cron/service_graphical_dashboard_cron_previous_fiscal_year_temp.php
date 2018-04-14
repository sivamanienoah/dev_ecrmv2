<?php

/********************************************************************************
File Name       : Service_graphical_dashboard_cron_previous_fiscal_year.php
Created Date    : 18/10/2016
Modified Date   : 18/10/2016
Created By      : Sriram.S
Modified By     : Sriram.S
*********************************************************************************/

/**
 * Service_graphical_dashboard_cron_previous_fiscal_year
 *
 * @class 		Service_graphical_dashboard_cron_previous_fiscal_year
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */
// error_reporting(E_ALL);
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
// ini_set('display_errors',1);
class Service_graphical_dashboard_cron_previous_fiscal_year extends crm_controller
{	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('text');
		$this->load->helper('custom');
		$this->load->helper('url');
		$this->load->model('projects/service_graphical_dashboard_model');
		$this->load->model('projects/dashboard_model');
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$this->fiscal_month_arr 	= array('Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar');
		$lastMonthArrCalcNoForEndmonth = array('04', '05');
		
		/* $curFiscalYear 	= getLastFiscalYear();
		$start_date    	= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date    	= ($curFiscalYear)."-03-31";  //eg.2013-04-01 */
		
		$curFiscalYear 		= 2018;
		$start_date    		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date    		= ($curFiscalYear)."-03-31";  //eg.2013-04-01
		
		/* if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
			$end_date = date('Y-m-t');
		} else {
			$bas_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
			$end_date = date('Y-m-t', strtotime('-1 month', $bas_mon));
		} */
		
		$this->upto_month = date('M', strtotime($end_date));
    }
	
	/*
	*method : get_currency_rates
	*/
	public function get_currency_rates() 
	{
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
			
		$data  		  	= array();
			
		$bk_rates 	   	= get_book_keeping_rates();
		$ins_result	   	= 0;
		
		$curFiscalYear 		= 2018;
		$start_date    		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date    		= ($curFiscalYear)."-03-31";  //eg.2013-04-01

		$start_date 	= date("Y-m-01",strtotime($start_date));
		$end_date 		= date("Y-m-t", strtotime($end_date));

		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;

		$project_status = 1;
		
		$project_code 	= array();
		$projects 	  	= array();
		$practice_arr 	= array();
		$practice_array = array();
		
		$started_at  = date("Y-m-d H:i:s");

		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		$practice_not_in = array(6);
		$this->db->where_not_in('p.id', $practice_not_in);
		// $this->db->where_in('p.id', array(1));
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();		
				
		if(!empty($pres) && count($pres)>0) {
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
				$practice_array[] = $prow->practices;
			}
		}

		//get current fiscal ytd invoice records
		$invoice_data 	 = $this->service_graphical_dashboard_model->getInvoiceRecords($start_date, $end_date);
		$trend_value 	 = calcInvoiceDataByPracticeWiseMonthWise($invoice_data, $this->default_cur_id);
		$trend_pract_arr = array();
		//allign trend array by practicewise then monthwise
		if(!empty($practice_array) && count($practice_array)>0 && count($trend_value)>0) {
			foreach($practice_array as $prac_name) {
				if($prac_name == 'Infra Services') {
					continue;
				}
				$trend_pract_arr['practic_arr'][] = $prac_name;
				foreach($this->fiscal_month_arr as $fis_mon) {
					$trend_pract_arr['trend_pract_val_arr'][$prac_name][$fis_mon] = isset($trend_value[$prac_name][$fis_mon]) ? $trend_value[$prac_name][$fis_mon] : 0;
					if($fis_mon == $this->upto_month) { break; }
				}
			}
		}

		// echo "<pre>"; print_r($trend_pract_arr); exit;
		$projects['trend_pract_arr'] = $trend_pract_arr;
		
		$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		$this->db->where("l.customer_type", '1');
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		//BPO practice are not shown in IT Services Dashboard
		$practice_not_in = array(6);
		$this->db->where_not_in('l.practice', $practice_not_in);
		// $this->db->where_in('l.practice', array(12)); //for temporary use
		if($project_status){
			if($project_status !=2)
			$this->db->where_in("l.pjt_status", $project_status);
		}

		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$res = $query->result_array();
		
		// echo "<pre>"; print_r($res); die;
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
				if (isset($projects['practicewise'][$practice_arr[$row['practice']]])) {
					$projects['othercost_projects'][$practice_arr[$row['practice']]][] = $row['lead_id'];
				} else {
					$projects['othercost_projects'][$practice_arr[$row['practice']]][] = $row['lead_id'];
				}
			}
		}
		
		//billable efforts
		$projects['billable_ytd']   = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
		// echo "<pre>"; print_r($projects['billable_ytd']); exit;
		$data['projects']           = $projects; //billable efforts
		
		//for utiliztion cost calculation - start
		/* $this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost, t.entry_month as month_name, t.entry_year as yr, t.start_time');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left'); */
		
		
		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.resource_total_hours, t.practice_id, t.practice_name, t.start_time');		
		$this->db->from($this->cfg['dbpref'].'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
		
		// $this->db->where_in("l.pjt_id", array('ITS-ULT-04-0916')); // for temporary use
		// $this->db->where_in("l.pjt_id", array("ITS-ERS-07-1016","ITS-SVM-03-0916","ITS-ULT-04-0916","ITS-SVM-02-0716","ITS-ULT-03-0716","COS-ENO-49-0616","COS-ENO-46-0616","COS-ENO-44-0616","COS-ENO-34-0616","ITS-PAW-01-0716","ITS-ULT-02-0416","ITS-SVM-01-0416","ITS-ULT-01-0416","ITS-MAG-01-0416","ITS-TON-01-0316","COS-NOA-05-0216","ITS-ENO-19-1015","ITS-ENO-09-0415","ITS-RPT-01-1115")); // for temporary use
		
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
		// $this->db->limit(20); // for temporary use
		$query = $this->db->get();
		$resdata = $query->result();
		// echo $this->db->last_query(); exit;
		// echo '<pre>';print_r($resdata); exit;
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
		
		$contribution_trend_project_arr = array();
		$contribution_trend_arr = array();
		
		//get all the hours for practice by financial year wise
		$practice_id_year_array = $this->dashboard_model->get_practice_max_hrs_by_fiscal_year();
		$practice_id_array  	= $this->dashboard_model->get_practice_max_hr();
		
		if(count($resdata)>0) {
			$rates = $this->get_currency_rates();
			foreach($resdata as $rec) {
				/* $financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
				$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id, $financialYear); */
				
				$financialYear 			= get_current_financial_year($rec->yr, $rec->month_name);
				$max_hrs 				= 0;
				if(isset($practice_id_year_array[$rec->practice_id][$financialYear])) {
					$max_hrs = $practice_id_year_array[$rec->practice_id][$financialYear];
				} else if(isset($practice_id_array[$rec->practice_id])) {
					$max_hrs = $practice_id_array[$rec->practice_id];
				}
				
				$timesheet_data[$rec->username]['practice_id'] 	= $rec->practice_id;
				// $timesheet_data[$rec->username]['max_hours']   = isset($max_hours_resource->practice_max_hours) ? $max_hours_resource->practice_max_hours : 0;
				$timesheet_data[$rec->username]['max_hours'] 	= $max_hrs;
				$timesheet_data[$rec->username]['dept_name']   	= $rec->dept_name;
				
				$rateCostPerHr = round($rec->cost_per_hour * $rates[1][$this->default_cur_id], 2);
				$directrateCostPerHr = round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
				
				if(isset($timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'])) {
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours']  += $rec->duration_hours;
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr'] 		= $rateCostPerHr;
				} else {
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours']   = $rec->duration_hours;
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr'] 		= $rateCostPerHr;
				}
				/* $get_total_hours = get_timesheet_hours_by_user($rec->username,$rec->yr,$rec->month_name,array('Leave','Hol'));
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = $get_total_hours; */
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = $rec->resource_total_hours;
				
				if($rec->resoursetype == 'Billable') {
					if(isset($timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['billable_hours'])) {
						$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['billable_hours'] += $rec->duration_hours;
					} else {
						$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['billable_hours'] = $rec->duration_hours;
					}
				}
			}

			if(count($timesheet_data)>0 && !empty($timesheet_data)) {
				foreach($timesheet_data as $key1=>$value1) {
					$resource_name = $key1;
					$max_hours 									= $value1['max_hours'];
					$dept_name 									= $value1['dept_name'];
					$resource_cost[$resource_name]['dept_name'] = $dept_name;
					if(is_array($value1) && count($value1)>0 && !empty($value1)) {
						foreach($value1 as $key2=>$value2) {
							$year = $key2;
							if(is_array($value2) && count($value2)>0 && !empty($value2)){
								foreach($value2 as $key3=>$value3) {
									$individual_billable_hrs		= 0;
									$month		 	  				= $key3;
									if(count($value3)>0 && !empty($value3)){
										foreach($value3 as $key4=>$value4) {
											if($key4 != 'total_hours'){ 
												$individual_billable_hrs = $value3['total_hours'];
												$duration_hours			 = $value4['duration_hours'];
												$billable_hours			 = isset($value4['billable_hours']) ? $value4['billable_hours'] : 0;
												$rate				 	 = $value4['rateperhr'];
												$direct_rateperhr	 	 = $value4['direct_rateperhr'];
												$rate1 					 = $rate;
												$direct_rateperhr1 		 = $direct_rateperhr;
												if($individual_billable_hrs > $max_hours) {
													$percentage 		= ($max_hours/$individual_billable_hrs);
													$rate1 				= number_format(($percentage*$direct_rateperhr),2);
													$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
												}
												if($value1['practice_id'] == 0) {
													$direct_rateperhr1  = $direct_rateperhr;
												}
												if(isset($resource_cost[$resource_name][$year][$month][$key4]['duration_hours'])){
													$resource_cost[$resource_name][$year][$month][$key4]['duration_hours'] += $duration_hours;
													$resource_cost[$resource_name][$year][$month][$key4]['total_cost'] 	   += ($duration_hours*$direct_rateperhr1);
													$resource_cost[$resource_name][$year][$month][$key4]['total_dc_cost']  += ($duration_hours*$direct_rateperhr1);
													$resource_cost[$resource_name][$year][$month][$key4]['billable_cost']  += ($billable_hours*$direct_rateperhr1);
												} else {
													$resource_cost[$resource_name][$year][$month][$key4]['duration_hours'] = $duration_hours;
													$resource_cost[$resource_name][$year][$month][$key4]['total_cost'] 	   = ($duration_hours*$direct_rateperhr1);
													$resource_cost[$resource_name][$year][$month][$key4]['total_dc_cost']  = ($duration_hours*$direct_rateperhr1);
													$resource_cost[$resource_name][$year][$month][$key4]['billable_cost']  = ($billable_hours*$direct_rateperhr1);
												}
												$resource_cost[$resource_name][$year][$month][$key4]['practice_id'] 	   = ($duration_hours*$rate1);
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
		//for contribution trend
		if(is_array($resource_cost) && count($resource_cost)>0 && !empty($resource_cost)) {
			foreach($resource_cost as $resourceKey => $resourceVal){
				if(is_array($resourceVal) && count($resourceVal)>0 && !empty($resourceVal)) {
					foreach($resourceVal as $yrKey => $yrVal) {
						if(is_array($yrVal) && count($yrVal)>0 && !empty($yrVal)) {
							foreach($yrVal as $monKey => $monVal) {
								$shortMonKey 	 = substr($monKey,0,3);
								$duration_hours1 = 0;
								$total_cost1 	 = 0;
								$total_dc_cost1  = 0;
								foreach($monVal as $project_code => $project_val) {
									$duration_hours1 = $project_val['duration_hours'];
									$billable_cost1  = $project_val['billable_cost'];
									$total_cost1	 = $project_val['total_cost'];
									$total_dc_cost1  = $project_val['total_dc_cost'];
									if(isset($contribution_trend_project_arr[$project_code][$shortMonKey]['project_total_direct_cost'])) {
										$contribution_trend_project_arr[$project_code][$shortMonKey]['project_total_direct_cost'] += $total_cost1;
									} else {
										$contribution_trend_project_arr[$project_code][$shortMonKey]['project_total_direct_cost'] = $total_cost1;
									}
								}
							}
						}
					}
				}
			}
		}
		
		if(is_array($resource_cost) && count($resource_cost)>0 && !empty($resource_cost)){
			foreach($resource_cost as $resourceName => $array1){
				$dept_name = $resource_cost[$resourceName]['dept_name'];
				if(is_array($array1) && count($array1)>0 && !empty($array1)){
					foreach($array1 as $year => $array2){
						if($year !='dept_name'){
							if(is_array($array2) && count($array2)>0 && !empty($array2)){
								foreach($array2 as $month => $array3){
									$duration_hours = 0;
									$total_cost 	= 0;
									$total_dc_cost 	= 0;
									foreach($array3 as $project_code => $array4){
										$duration_hours = $array4['duration_hours'];
										$billable_cost  = $array4['billable_cost'];
										$total_cost 	= $array4['total_cost'];
										$total_dc_cost 	= $array4['total_dc_cost'];
										if(isset($directcost1[$project_code]['project_total_direct_cost'])) {
											$directcost1[$project_code]['project_total_direct_cost'] += $total_cost;
										} else {
											$directcost1[$project_code]['project_total_direct_cost'] = $total_cost;
										}
										if(isset($directcost1[$project_code]['project_total_billable_cost'])) {
											$directcost1[$project_code]['project_total_billable_cost'] += $billable_cost;
										} else {
											$directcost1[$project_code]['project_total_billable_cost'] = $billable_cost;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$this->db->select("pjt_id,practice,lead_title");
		$res = $this->db->get_where($this->cfg['dbpref']."leads",array("pjt_id !=" => '',"practice !=" => '', "practice !=" => 6)); //for temporary use
		$project_res = $res->result();
		
		/*
		// for temporary use		
		$this->db->select("pjt_id,practice,lead_title");
		$this->db->from($this->cfg['dbpref']. 'leads');
		$this->db->where_in("pjt_id", array('ITS-FRE-01-0916', 'ITS-LET-01-0516')); 
		$this->db->where("practice !=", "");
		$query = $this->db->get();		
		$project_res = $query->result(); 
		*/
		
		$project_master = array();
		if(!empty($project_res)) {
			foreach($project_res as $prec) {
				if($practice_arr[$prec->practice] == 'Infra Services' || $practice_arr[$prec->practice] == 'Testing') {
					$practice_arr[$prec->practice] = 'Others';
				}
				if(isset($directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'])) {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'] += isset($directcost1[$prec->pjt_id]['project_total_direct_cost']) ? $directcost1[$prec->pjt_id]['project_total_direct_cost'] : 0;
				} else {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'] = isset($directcost1[$prec->pjt_id]['project_total_direct_cost']) ? $directcost1[$prec->pjt_id]['project_total_direct_cost'] : 0;
				}
				if(isset($directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_billable_cost'])) {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_billable_cost'] += isset($directcost1[$prec->pjt_id]['project_total_billable_cost']) ? $directcost1[$prec->pjt_id]['project_total_billable_cost'] : 0;
				} else {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_billable_cost'] = isset($directcost1[$prec->pjt_id]['project_total_billable_cost']) ? $directcost1[$prec->pjt_id]['project_total_billable_cost'] : 0;
				}
				//for contribution trend
				$other_cos_arr = array();
				$other_cos_arr = getOtherCostByProjectCodeByDateRangeByMonthWise($prec->pjt_id, $this->default_cur_id, $start_date, $end_date);
				$go_merge_proj_arr = isset($contribution_trend_project_arr[$prec->pjt_id]) ? $contribution_trend_project_arr[$prec->pjt_id] : array();
				$contribution_trend_arr = $this->combine_contribution_project_arr($practice_arr[$prec->practice], $contribution_trend_arr, $go_merge_proj_arr, $other_cos_arr);
			}
		}
		
		
		
		$projects['contribution_trend_arr'] = $contribution_trend_arr;
		foreach($directcost2 as $practiceId => $val1) {
			foreach($val1 as $pjtCode => $val) {
				if(isset($directcost[$practiceId]['total_direct_cost'])) {
					$directcost[$practiceId]['total_direct_cost'] += $val['total_direct_cost'];
				} else {
					$directcost[$practiceId]['total_direct_cost'] = $val['total_direct_cost'];
				}
				if(isset($directcost[$practiceId]['total_billable_cost'])) {
					$directcost[$practiceId]['total_billable_cost'] += $val['total_billable_cost'];
				} else {
					$directcost[$practiceId]['total_billable_cost'] = $val['total_billable_cost'];
				}
			}
		}
		$projects['direct_cost'] = $directcost;
		//for utiliztion cost calculation -end
		
		$ins_array    = array();
		$tot 		  = array();
		$tot_bill_eff = $tot_tot_bill_eff = $tot_temp_ytd_uc = $tot_temp_billable_ytd_uc = 0;

		if(!empty($practice_array)){
			//truncate the table & inserting the practices name from table.
			$this->db->truncate($this->cfg['dbpref'].'services_graphical_dashboard_last_fiscal_year_temp');
			foreach($practice_array as $parr) {
				$ins_data['practice_name'] = $parr;
				$this->db->insert($this->cfg['dbpref'] . 'services_graphical_dashboard_last_fiscal_year_temp', $ins_data);
			}
			$ins_data['practice_name'] = 'Total';
			$this->db->insert($this->cfg['dbpref'] . 'services_graphical_dashboard_last_fiscal_year_temp', $ins_data);
			
			// for total contribution & total revenue
			$overall_revenue = $overall_contrib = 0;
			foreach($practice_array as $parr){
				$mon_revenue = $mon_contrib = 0;
				foreach($this->fiscal_month_arr as $fis_mon) {
					$con_month = 'contri_'.$fis_mon;
					
					$inse_array[$con_month] = 0;
					$mon_revenue += isset($projects['trend_pract_arr']['trend_pract_val_arr'][$parr][$fis_mon]) ? round($projects['trend_pract_arr']['trend_pract_val_arr'][$parr][$fis_mon], 2) : 0;
					$overall_revenue += isset($projects['trend_pract_arr']['trend_pract_val_arr'][$parr][$fis_mon]) ? round($projects['trend_pract_arr']['trend_pract_val_arr'][$parr][$fis_mon], 2) : 0;
					// echo $parr ." - ".$fis_mon." - ".$overall_revenue. "<br>";
					$mon_contrib += isset($projects['contribution_trend_arr'][$parr][$fis_mon]) ? round($projects['contribution_trend_arr'][$parr][$fis_mon], 2) : 0;
					// echo $parr . " - ". $overall_contrib . "<br>";
					$overall_contrib += isset($projects['contribution_trend_arr'][$parr][$fis_mon]) ? round($projects['contribution_trend_arr'][$parr][$fis_mon], 2) : 0;
					if(isset($mon_revenue) && $mon_revenue != 0) {
						$inse_array[$con_month] = round((($mon_revenue - $mon_contrib)/$mon_revenue)*100);
					}
					// echo $parr.'-  Mon - '. $fis_mon . ' Revenue - ' .$mon_revenue . ' Contribu - ' .$mon_contrib; echo '<br />';
					// echo '<pre>'; print_r($inse_array); echo '</pre>';
					$this->db->where(array('practice_name' => $parr));
					$this->db->update($this->cfg['dbpref'] . 'services_graphical_dashboard_last_fiscal_year_temp', $inse_array);
					// echo $this->db->last_query() . "<br />";
					$inse_array = array();
					if($fis_mon == $this->upto_month) { break; }
				}
			}
			// echo '<br>**************************************************';
			
			foreach($practice_array as $parr){
				/**other cost data*/
				$other_cost_val 	= 0;
				if(isset($projects['othercost_projects']) && !empty($projects['othercost_projects'][$parr]) && count($projects['othercost_projects'][$parr])>0) {
					foreach($projects['othercost_projects'][$parr] as $pro_id) {
						$val 			 = getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $start_date, $end_date);
						$other_cost_val += $val;
					}
					if($parr == 'Infra Services' || $parr == 'Testing') {
						$parr = 'Others';
					}
					$projects['other_cost'][$parr] = $other_cost_val;
				}
				/**other cost data*/
				if($parr == 'Infra Services' || $parr == 'Testing') {
					// $parr = 'Others';
					continue;
				}
				//for billable efforts
				$bill_eff = 0;
				if(isset($projects['billable_ytd'][$parr]) && !empty($projects['billable_ytd'][$parr])) {
					$bill_eff = (($projects['billable_ytd'][$parr]['Billable']['hour'])/$projects['billable_ytd'][$parr]['totalhour'])*100;		
				}
				
				$ins_array['ytd_billable']   = ($bill_eff != 0) ? round($bill_eff) : '-';
				//for billable utilization cost
				$temp_ytd_utilization_cost = '';
				if(isset($projects['direct_cost'][$parr]['total_direct_cost']) && (isset($projects['other_cost'][$parr]))) {
					$temp_ytd_utilization_cost = $projects['direct_cost'][$parr]['total_direct_cost'] + $projects['other_cost'][$parr];
				}
				
				if(isset($temp_ytd_utilization_cost) && !empty($temp_ytd_utilization_cost)) {
					$bill_ytd_uc = (($projects['direct_cost'][$parr]['total_billable_cost'])/$temp_ytd_utilization_cost)*100;
				}
				$ins_array['ytd_billable_utilization_cost'] = ($bill_ytd_uc != '') ? round($bill_ytd_uc) : '-';
				if(isset($projects['direct_cost'][$parr]) && !empty($projects['direct_cost'][$parr])) {
					$tot_temp_ytd_uc  			+= $temp_ytd_utilization_cost;
					$tot_temp_billable_ytd_uc 	+= $projects['direct_cost'][$parr]['total_billable_cost'];
				}
				
				if(isset($projects['billable_ytd'][$parr]) && !empty($projects['billable_ytd'][$parr])) {
					$tot_bill_eff     += $projects['billable_ytd'][$parr]['Billable']['hour'];
					$tot_tot_bill_eff += $projects['billable_ytd'][$parr]['totalhour'];
				}			
				
				$this->db->where(array('practice_name' => $parr));
				$this->db->update($this->cfg['dbpref'] . 'services_graphical_dashboard_last_fiscal_year_temp', $ins_array);
				// echo $this->db->last_query() . "<br />";
				echo '<pre>'; print_r($ins_array); echo '</pre>';
				$ins_array = array();
				$ins_result = 1;
			}
			
			$tot['ytd_billable'] 		 		  = round(($tot_bill_eff/$tot_tot_bill_eff)*100);
			$tot['ytd_billable_utilization_cost'] = round(($tot_temp_billable_ytd_uc/$tot_temp_ytd_uc)*100);
			$tot['tot_contri'] 			  		  = round((($overall_revenue-$overall_contrib)/$overall_revenue)*100);

			//updating the total values
			$this->db->where(array('practice_name' => 'Total'));
			$this->db->update($this->cfg['dbpref'] . 'services_graphical_dashboard_last_fiscal_year_temp', $tot);
			// echo $this->db->last_query() . "<br />";
			
			// echo '<pre>'; print_r($tot); die;
			$ended_at = date("Y-m-d H:i:s");
			
			/* insert or update data in services_graphical_dashboard_last_fiscal_year table from services_graphical_dashboard_last_fiscal_year_temp table */
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref']. 'services_graphical_dashboard_last_fiscal_year_temp');
			$sgdlast_temp_res = $this->db->get();
			$last_res_data = $sgdlast_temp_res->result();	
			
			if(!empty($last_res_data) && count($last_res_data)>0) {
				foreach($last_res_data as $each_res){
					$this->db->select('sgdlast.*');
					$this->db->from($this->cfg['dbpref']. 'services_graphical_dashboard_last_fiscal_year as sgdlast');
					$this->db->where('sgdlast.practice_name', $each_res->practice_name);
					$this->db->where('sgdlast.fiscal_year', $curFiscalYear);
					$sgdlast_res = $this->db->get();
					$sgdlast_data = $sgdlast_res->result();
					
					$inser_data = array();
					
					$inser_data['ytd_billable'] = $each_res->ytd_billable;
					$inser_data['ytd_billable_utilization_cost'] = $each_res->ytd_billable_utilization_cost;
					$inser_data['contri_Apr'] = $each_res->contri_Apr;
					$inser_data['contri_May'] = $each_res->contri_May;
					$inser_data['contri_Jun'] = $each_res->contri_Jun;
					$inser_data['contri_Jul'] = $each_res->contri_Jul;
					$inser_data['contri_Aug'] = $each_res->contri_Aug;
					$inser_data['contri_Sep'] = $each_res->contri_Sep;
					$inser_data['contri_Oct'] = $each_res->contri_Oct;
					$inser_data['contri_Nov'] = $each_res->contri_Nov;
					$inser_data['contri_Dec'] = $each_res->contri_Dec;
					$inser_data['contri_Jan'] = $each_res->contri_Jan;
					$inser_data['contri_Feb'] = $each_res->contri_Feb;
					$inser_data['contri_Mar'] = $each_res->contri_Mar;
					$inser_data['tot_contri'] = $each_res->tot_contri;
					
					if(empty($sgdlast_data)){
						
						$inser_data['practice_name'] = $each_res->practice_name;
						$inser_data['fiscal_year'] = $curFiscalYear;
						
						$ins_res = $this->db->insert($this->cfg['dbpref'] . 'services_graphical_dashboard_last_fiscal_year', $inser_data);
						if($ins_res == true){
							echo'inserted' . "<br />";
						}
					} else if(!empty($sgdlast_data) && count($sgdlast_data)>0){
						
						$this->db->where('practice_name',$sgdlast_data->practice_name);
						$this->db->where('fiscal_year',$curFiscalYear);
						$upd_res = $this->db->update($this->cfg['dbpref'] . 'services_graphical_dashboard_last_fiscal_year', $inser_data);
						if($upd_res == true){
							echo'updated' . "<br />";
						}
					}
				}
			}
			
			if($ins_result) {
				$upload_status = "Updated Successfully";
				$this->load->model('email_template_model');
				$param = array();
				$param['email_data'] = array('print_date'=>date('d-m-Y'), 'started_at'=>$started_at, 'ended_at'=>$ended_at, 'upload_status'=>$upload_status);
				$param['to_mail']    	  = 'ssriram@enoahisolution.com';
				$param['template_name']   = "IT service graph dashboard data upload status";
				$param['subject'] 		  = "IT service graph dashboard Cron On - ".date('d-m-Y'). " Status - ".$upload_status;
				$this->email_template_model->sent_email($param);
			} else {
				$upload_status = "Updation Failed";
				$this->load->model('email_template_model');
				$param = array();
				$param['email_data'] = array('print_date'=>date('d-m-Y'), 'started_at'=>$started_at, 'ended_at'=>'', 'upload_status'=>$upload_status);
				$param['to_mail']    	  = 'ssriram@enoahisolution.com';
				$param['template_name']   = "IT service graph dashboard data upload status";
				$param['subject'] 		  = "IT service graph dashboard Cron On - ".date('d-m-Y'). " Status - ".$upload_status;		
				$this->email_template_model->sent_email($param);
			}
		}
	}
	
	public function combine_contribution_project_arr($practice_name, $contr_trend_arr, $project_mon_arr, $other_cos_arr_val)
	{
		// echo "asdf".$practice_name; exit;
		$res_arr = array();
		$res_arr = $contr_trend_arr;
		// if(!empty($project_mon_arr) && count($project_mon_arr)>0) {
			foreach($this->fiscal_month_arr as $fis_mon) {
				$contr_value = $other_value = 0;
				if(isset($res_arr[$practice_name][$fis_mon])) {
					//contribution value
					$contr_value = isset($project_mon_arr[$fis_mon]) ? $project_mon_arr[$fis_mon]['project_total_direct_cost'] : 0;
					$other_value = isset($other_cos_arr_val[$fis_mon]) ? $other_cos_arr_val[$fis_mon] : 0;
					$res_arr[$practice_name][$fis_mon] += $contr_value + $other_value;
				} else {
					$contr_value = isset($project_mon_arr[$fis_mon]) ? $project_mon_arr[$fis_mon]['project_total_direct_cost'] : 0;
					$other_value = isset($other_cos_arr_val[$fis_mon]) ? $other_cos_arr_val[$fis_mon] : 0;
					$res_arr[$practice_name][$fis_mon] = $contr_value + $other_value;
				}
			}
		// }
		// echo "<pre>"; print_r($res_arr); exit;
		return $res_arr;
	}
	
	public function get_timesheet_data($practice_arr, $start_date=false, $end_date=false, $month=false)
	{
		$prs = array();
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$pract = $pquery->result();
		if(!empty($pract) && count($pract)>0) {
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
		
		$resarr = array();
		$resarr['project_code'] = array();

		if(count($timesheet_data)>0) {
			foreach($timesheet_data as $row) {
				if($row->practice_id == 7 || $row->practice_id == 13) {
					$row->practice_id = 10;
				}
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
					if(!in_array($row->project_code, $resarr['project_code'])) {
						$resarr['project_code'][] = $row->project_code;
					}
				}
			}
		}
		return $resarr;
	}
}
?>