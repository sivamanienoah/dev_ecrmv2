<?php

/********************************************************************************
File Name       : service_graphical_dashboard_cron.php
Created Date    : 18/10/2016
Modified Date   : 18/10/2016
Created By      : Sriram.S
Modified By     : Sriram.S
*********************************************************************************/

/**
 * service_graphical_dashboard_cron
 *
 * @class 		Service_graphical_dashboard_cron
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */
// error_reporting(E_ALL);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
class Service_graphical_dashboard_cron extends crm_controller 
{	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('text');
		$this->load->helper('custom');
		$this->load->helper('url');
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
			
		$data  				  = array();
			
		$bk_rates = get_book_keeping_rates();
		
		$curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month 		= date('Y-m-01 00:00:00');
		$start_date = date("Y-m-01",strtotime($start_date));
		$end_date 	= date("Y-m-d", strtotime($end_date));

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

		$query = $this->db->get();
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
		
		//for utiliztion cost calculation -start
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
		// $this->db->where("l.pjt_id", 'ITS-LET-01-0516'); // temporary use
	 
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-d', strtotime($end_date)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$resrc = 't.resoursetype IS NOT NULL';
		$this->db->where($resrc);
		$this->db->where("l.practice is not null");
		$query = $this->db->get();		
		$resdata = $query->result();
		// echo $this->db->last_query(); exit;
		// echo '<pre>';print_r($resdata);
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
				
				$timesheet_data[$rec->username]['practice_id'] = $rec->practice_id;
				$timesheet_data[$rec->username]['max_hours'] = $max_hours_resource->practice_max_hours;
				$timesheet_data[$rec->username]['dept_name'] = $rec->dept_name;
				
				$rateCostPerHr = round($rec->cost_per_hour * $rates[1][$this->default_cur_id], 2);
				$directrateCostPerHr = round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
				if(isset($timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'])) {
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
				} else {
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] = $rec->duration_hours;
				}
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username,$rec->yr,$rec->month_name,array('Leave','Hol'));
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr'] 		= $rateCostPerHr;
				if($rec->resoursetype == 'Billable') {
					if(isset($timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['billable_hours'])) {
						$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['billable_hours'] += $rec->duration_hours;
					} else {
						$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['billable_hours'] = $rec->duration_hours;
					}
				}
			}

			// echo '<pre>'; print_r($timesheet_data); echo "</pre>";
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
												if($individual_billable_hrs > $max_hours){
													//echo 'max'.$max_hours.'<br>';
													$percentage 		= ($max_hours/$individual_billable_hrs);
													$rate1 				= number_format(($percentage*$direct_rateperhr),2);
													$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
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
													$resource_cost[$resource_name][$year][$month][$key4]['billable_cost'] = ($billable_hours*$direct_rateperhr1);
												}
												$resource_cost[$resource_name][$year][$month][$key4]['practice_id'] 	 = ($duration_hours*$rate1);
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
		// echo '<pre>';print_r($directcost1);exit;
		$this->db->select("pjt_id,practice,lead_title");
		$res = $this->db->get_where($this->cfg['dbpref']."leads",array("pjt_id !=" => '',"practice !=" => ''));
		$project_res = $res->result();
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec) {
				if(isset($directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'])) {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'] += $directcost1[$prec->pjt_id]['project_total_direct_cost'];
				} else {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'] = $directcost1[$prec->pjt_id]['project_total_direct_cost'];
				}
				if(isset($directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_billable_cost'])) {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_billable_cost'] += $directcost1[$prec->pjt_id]['project_total_billable_cost'];
				} else {
					$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_billable_cost'] = $directcost1[$prec->pjt_id]['project_total_billable_cost'];
				}
			}
		}
		// echo '<pre>';print_r($directcost2);exit;
		foreach($directcost2 as $practiceId => $val1) {
			foreach($val1 as $pjtCode => $val){
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
		// echo '<pre>';print_r($directcost);exit;
		$projects['direct_cost']    = $directcost;
		//for utiliztion cost calculation -end
		
		// echo "<pre>"; print_r($projects['direct_cost']); exit;
		
		$ins_array = array();
		$tot = array();
		$tot_bill_eff = $tot_tot_bill_eff = $tot_temp_ytd_uc = $tot_temp_billable_ytd_uc = 0;

		if(!empty($practice_array)){
			
			//truncate the table & inserting the practices name from table.
			$this->db->truncate($this->cfg['dbpref'].'services_graphical_dashboard');
			foreach($practice_array as $parr){
				$ins_data['practice_name'] = $parr;
				$this->db->insert($this->cfg['dbpref'] . 'services_graphical_dashboard', $ins_data);
			}
			//exit;
			$ins_data['practice_name'] = 'Total';
			$this->db->insert($this->cfg['dbpref'] . 'services_graphical_dashboard', $ins_data);
			//echo '<pre>';print_r($practice_array); 
			
			foreach($practice_array as $parr){
				
				/**other cost data*/
				$other_cost_val 	= 0;
				if(isset($projects['othercost_projects']) && !empty($projects['othercost_projects'][$parr]) && count($projects['othercost_projects'][$parr])>0) {
					foreach($projects['othercost_projects'][$parr] as $pro_id) {
						$val 			 = getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $start_date, $end_date);
						$other_cost_val += $val;
					}
					$projects['other_cost'][$parr] = $other_cost_val;
				}
				/**other cost data*/
				//for billable efforts
				$bill_eff = 0;
				if(isset($projects['billable_ytd'][$parr]) && !empty($projects['billable_ytd'][$parr])) {
					$bill_eff = (($projects['billable_ytd'][$parr]['Billable']['hour'])/$projects['billable_ytd'][$parr]['totalhour'])*100;		
				}
				$ins_array['ytd_billable']   = ($bill_eff != 0) ? round($bill_eff) : '-';
				//for billable utilization cost
				$temp_ytd_utilization_cost = $projects['direct_cost'][$parr]['total_direct_cost'] + $projects['other_cost'][$parr];
				if(isset($temp_ytd_utilization_cost) && !empty($temp_ytd_utilization_cost)) {
					$bill_ytd_uc = (($projects['direct_cost'][$parr]['total_billable_cost'])/$temp_ytd_utilization_cost)*100;
				}
				$ins_array['ytd_billable_utilization_cost'] = ($bill_ytd_uc != '') ? round($bill_ytd_uc) : '-';
				if(isset($projects['direct_cost'][$parr]) && !empty($projects['direct_cost'][$parr])) {
					$tot_temp_billable_ytd_uc  	+= $temp_ytd_utilization_cost;
					$tot_temp_ytd_uc 			+= $projects['direct_cost'][$parr]['total_billable_cost'];
				}
				
				if(isset($projects['billable_ytd'][$parr]) && !empty($projects['billable_ytd'][$parr])) {
					$tot_bill_eff     += $projects['billable_ytd'][$parr]['Billable']['hour'];
					$tot_tot_bill_eff += $projects['billable_ytd'][$parr]['totalhour'];
				}
				
				$this->db->where(array('practice_name' => $parr));
				$this->db->update($this->cfg['dbpref'] . 'services_graphical_dashboard', $ins_array);
				echo $this->db->last_query() . "<br />";
				$ins_array = array();
			}
			
			$tot['ytd_billable'] 		 			= round(($tot_bill_eff/$tot_tot_bill_eff)*100);
			$tot[' ytd_billable_utilization_cost '] = round(($tot_temp_billable_ytd_uc/$tot_temp_ytd_uc)*100);

			//updating the total values
			$this->db->where(array('practice_name' => 'Total'));
			$this->db->update($this->cfg['dbpref'] . 'services_graphical_dashboard', $tot);
			echo $this->db->last_query() . "<br />";
		}
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
		$resarr['project_code'] = array();

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
		return $resarr;
	}
}
?>