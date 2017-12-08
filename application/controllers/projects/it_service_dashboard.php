<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

set_time_limit(0);
ini_set('display_errors', 1);

class It_service_dashboard extends crm_controller 
{
	function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
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
		$this->userdata   = $this->session->userdata('logged_in_user');
	}
	
	/*
	* 
	*/
	function index()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$projects  			  = array();
		$data['page_heading'] = "IT Services Dashboard";
		
		$post_data = $this->input->post();
		
		$data['fy_year']  = $this->dashboard_model->get_records($tbl='financial_year', $wh_condn=array(), $order=array('id'=>'desc'));
		$config_data 	  = $this->config->item('crm');
		$data['fy_month'] =	$config_data['fy_months'];
		
		// $start_date    	= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $end_date  	   	= date('Y-m-d'); //eg.2014-03-01
		
		// dates - start
		$curFiscalYear 	  = getFiscalYearForDate(date("m/d/y"), "4/1", "3/31");
		if(!empty($post_data['fy_name'])) {
			$curFiscalYear = $post_data['fy_name'];
		}
		
		// assign month & dates based on start month
		
		$default_fy_start_month = '04';
		$data['start_month'] 	= $default_fy_start_month;
		if(!empty($post_data['start_month'])) {
			$data['start_month'] = $post_data['start_month'];
		}
		$start_date 	= calc_fy_dates($curFiscalYear, $data['start_month'], 'start');
		
		$default_fy_end_month 	= date('m');
		$data['end_month']  	= $default_fy_end_month;
		if(!empty($post_data['end_month'])) {
			$data['end_month']  = $post_data['end_month'];
		}
		$end_date 			 = calc_fy_dates($curFiscalYear, $data['end_month'], 'end');
		
		$month = $data['bill_month'] = ($end_date != "") ? date('Y-m-01 00:00:00', strtotime($end_date)) : date('Y-m-01 00:00:00'); //set the default billing & billable month.
		$data['start_date']  = $start_date;
		$data['end_date']    = $end_date;
		$data['fy_name']     = $curFiscalYear;
		$data['start_month'] = date('m', strtotime($start_date));
		$data['end_month']   = date('m', strtotime($end_date));
		// dates - end
		
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		
		// BPO practice are not shown in IT Services Dashboard
		$practice_not_in_arr = array(6);
		$this->db->where_not_in('p.id', $practice_not_in_arr);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();
		$practice_arr 	= array();
		$practice_array = array();
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
				$practice_array[] 		 = $prow->practices;
			}
		}
		
		// in progress projects only
		$project_status = array(1);
		
		//display projects
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
		$this->db->where_in("l.pjt_status", $project_status);
		$query = $this->db->get();
		$res = $query->result_array();
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
				if (isset($projects['practicewise'][$practice_arr[$row['practice']]])) {
					$projects['practicewise'][$practice_arr[$row['practice']]] += 1;
				} else {
					$projects['practicewise'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
				}
				if($row['rag_status'] == 1) {
					if (isset($projects['rag_status'][$practice_arr[$row['practice']]])) {
						$projects['rag_status'][$practice_arr[$row['practice']]] += 1;
					} else {
						$projects['rag_status'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
					}
				}
			}
		}
		$data['projects'] = $projects;
		
		if($this->input->post("filter")=="") {
			$month_status = 1;
			//get values from services dashboard table
			$this->db->select('practice_name, billing_month, ytd_billing, ytd_utilization_cost, billable_month, ytd_billable, effort_variance, contribution_month, ytd_contribution');
			$this->db->from($this->cfg['dbpref']. 'services_dashboard_beta');
			$this->db->where("month_status",$month_status);
			$sql = $this->db->get();
			$dashboard_details = $sql->result_array();
			$dashboard_det = array();
			if(!empty($dashboard_details)){
				foreach($dashboard_details as $key=>$val) {
					$dashboard_det[$val['practice_name']] = $val;
				}
			}
			$data['dashboard_det'] = $dashboard_det;

		} else {
			
			$bk_rates = get_book_keeping_rates();

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
			$practice_not_in = array(6);
			$this->db->where_not_in('l.practice', $practice_not_in);
			if(!empty($month)) {
				$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($month)));
				$this->db->where("sfv.for_month_year <= ", date('Y-m-t H:i:s', strtotime($month)));
			}
			$query5 = $this->db->get();
			// echo '<pre>'; print_r($this->db->last_query()); die;
			$cm_invoices_data = $query5->result_array();

			if(!empty($cm_invoices_data) && count($cm_invoices_data)>0) {
				foreach($cm_invoices_data as $cm_ir) {
					if($practice_arr[$cm_ir['practice']] == 'Testing' || $practice_arr[$cm_ir['practice']] == 'Infra Services') {
						$practice_arr[$cm_ir['practice']] = 'Others';
					}
					$base_conver_amt = $this->conver_currency($cm_ir['milestone_value'],$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($cm_ir['for_month_year'])),"4/1","3/31")][$cm_ir['expect_worth_id']][$cm_ir['base_currency']]);
					if(isset($projects['cm_irval'][$practice_arr[$cm_ir['practice']]])) {
						$projects['cm_irval'][$practice_arr[$cm_ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($cm_ir['for_month_year'])),"4/1","3/31")][$cm_ir['base_currency']][$this->default_cur_id]);
					} else {
						$projects['cm_irval'][$practice_arr[$cm_ir['practice']]] = $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($cm_ir['for_month_year'])),"4/1","3/31")][$cm_ir['base_currency']][$this->default_cur_id]);
					}
				}
			}
			
			// echo "After Invoices " . date('d-m-Y H:i:s') . "<br>";
			
			//for current month EFFORTS
			$projects['billable_month'] = $this->get_timesheet_data($practice_arr, "", "", $month);
			$projects['billable_ytd']   = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");

			//for effort variance
			$pcodes = array();
			$pcodes = $projects['billable_ytd']['project_code'];
			
			//the effort variance calculation
			$projects['eff_var'] = $this->do_eff_variance_calculation($pcodes, $practice_arr);
			
			//Contribution
			$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.resource_total_hours, l.practice as practice_id, p.practices as practice_name');
			
			$this->db->from($this->cfg['dbpref'].'timesheet_month_data as t');
			$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
			$this->db->join($this->cfg['dbpref'].'practices as p', 'p.id = l.practice');
		 
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
			// echo "123<pre><br><br><br><br><br><br>"; print_r($resdata); echo "</pre>";
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
					/*
					$financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
					$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id, $financialYear);
					*/					
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
					// $timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user_frm_month_data($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = $rec->resource_total_hours;
				}
				
				// echo "get timesheet hours " . date('d-m-Y H:i:s') . "<br>";
				
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
			$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.resource_total_hours');
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
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
					// $financialYear 		= get_current_financial_year($rec->yr, $rec->month_name);
					// $max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id, $financialYear);
					
					$financialYear 			= get_current_financial_year($rec->yr, $rec->month_name);
					$max_hrs 				= 0;
					if(isset($practice_id_year_array[$rec->practice_id][$financialYear])) {
						$max_hrs = $practice_id_year_array[$rec->practice_id][$financialYear];
					} else if(isset($practice_id_array[$rec->practice_id])) {
						$max_hrs = $practice_id_array[$rec->practice_id];
					}
					
					$timesheet_data[$rec->username]['practice_id'] = $rec->practice_id;
					// $timesheet_data[$rec->username]['max_hours'] = $max_hours_resource->practice_max_hours;
					$timesheet_data[$rec->username]['max_hours'] = $max_hrs;
					$timesheet_data[$rec->username]['dept_name'] = $rec->dept_name;
					
					$rateCostPerHr 		 = round($rec->cost_per_hour*$rates[1][$this->default_cur_id], 2);
					$directrateCostPerHr = round($rec->direct_cost_per_hour*$rates[1][$this->default_cur_id], 2);
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours']  += $rec->duration_hours;
					$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
					// $timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user_frm_month_data($rec->username,$rec->yr,$rec->month_name,array('Leave','Hol'));
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
			if(count($resource_cost)>0 && !empty($resource_cost)) {
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
			if(!empty($project_res)) {
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
						$cm_val = getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $month, $month);
						$other_cost_val    += $val;
						$cm_other_cost_val += $cm_val;
					}
					$projects['other_cost'][$prarr] = $other_cost_val;
					$projects['cm_other_cost'][$prarr] = $cm_other_cost_val;
				}
				if($prarr != 'Infra Services' || $prarr != 'Testing') {
					$totCM_Irval += $projects['cm_irval'][$prarr];
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
				$cm_billval = (($projects['billable_month'][$prarr]['Billable']['hour'])/$projects['billable_month'][$prarr]['totalhour'])*100;
				$ins_array['billable_month'] = ($cm_billval != 0) ? round($cm_billval) : '-';
				
				$billval = (($projects['billable_ytd'][$prarr]['Billable']['hour'])/$projects['billable_ytd'][$prarr]['totalhour'])*100;
				$ins_array['ytd_billable']   = ($billval != 0) ? round($billval) : '-';
				
				$eff_var = (($projects['eff_var'][$prarr]['total_actual_hrs'] - $projects['eff_var'][$prarr]['tot_estimate_hrs'])/$projects['eff_var'][$prarr]['tot_estimate_hrs'])*100;
				$ins_array['effort_variance'] = ($eff_var != 0) ? round($eff_var) : '-';
				$temp_cm_utd_cost = $projects['cm_direct_cost'][$prarr]['total_cm_direct_cost'] + $projects['cm_other_cost'][$prarr];
				if($temp_cm_utd_cost){
					$cm_dc_val = (($projects['cm_irval'][$prarr] - $temp_cm_utd_cost)/$projects['cm_irval'][$prarr]) * 100;
				}
				$ins_array['contribution_month'] = ($cm_dc_val != 0) ? round($cm_dc_val) : '-';
				$dc_val = (($projects['irval'][$prarr] - $temp_ytd_utilization_cost)/$projects['irval'][$prarr]) * 100;
				$ins_array['ytd_contribution'] = ($dc_val != 0) ? round($dc_val) : '-';
				$ins_array['month_status'] 	   = 1;
				
				$tot_billhour += $projects['billable_month'][$prarr]['Billable']['hour'];
				$tot_tothours += $projects['billable_month'][$prarr]['totalhour'];
				
				$tot_billval += $projects['billable_ytd'][$prarr]['Billable']['hour'];
				$tot_totbillval += $projects['billable_ytd'][$prarr]['totalhour'];
			
				$tot_actual_hr += $projects['eff_var'][$prarr]['total_actual_hrs'];
				$tot_estimated_hrs += $projects['eff_var'][$prarr]['tot_estimate_hrs'];
				
				$tot_cm_irvals += $projects['cm_irval'][$prarr];
				$tot_cm_dc_tot += $temp_cm_utd_cost;
				
				$tot_dc_vals += $projects['irval'][$prarr];
				$tot_dc_tots += $temp_ytd_utilization_cost;
				
				$show_arr[$prarr] = $ins_array;
			}
			
			//for total
			// $tot['practice_name']		 = ;
			$show_arr['Total']['billing_month'] 	   	= $totCM_Irval;
			$show_arr['Total']['ytd_billing']   	   	= $tot_Irval;
			$show_arr['Total']['ytd_utilization_cost'] 	= $tot_dc_tots;
			$show_arr['Total']['billable_month'] 	   	= round(($tot_billhour/$tot_tothours)*100);
			$show_arr['Total']['ytd_billable'] 		 	= round(($tot_billval/$tot_totbillval)*100);
			$show_arr['Total']['effort_variance'] 	 	= round((($tot_actual_hr-$tot_estimated_hrs)/$tot_estimated_hrs)*100);
			$cmonth						 			 	= '-';
			//if($tot_cm_dc_tot){
			$cmonth 					 			 	= round((($tot_cm_irvals-$tot_cm_dc_tot)/$tot_cm_irvals)*100);	
			//}
			$show_arr['Total']['contribution_month'] 	= $cmonth;
			$show_arr['Total']['ytd_contribution'] 	 	= round((($tot_dc_vals-$tot_dc_tots)/$tot_dc_vals)*100);
			
			$data['dashboard_det'] = $show_arr;
			// echo '<pre>'; print_r($data); die;
		}

		if($this->input->post("filter")!="") {
			$this->load->view('projects/it_service_dashboard_grid', $data);
		} else {
			$this->load->view('projects/it_service_dashboard', $data);
		}
	}
	
	public function do_eff_variance_calculation($pcodes, $practice_arr)
	{
		if(!empty($pcodes) && count($pcodes)>0) {
			
			$effvar = array();
			
			//get all the estimate hours
			$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title');
			$this->db->from($this->cfg['dbpref']. 'leads as l');
			//Project Billing Type - Fixed bid projects only
			$this->db->where("l.project_type", 1);
			$client_not_in_arr = array('ENO','NOA');
			$this->db->where_not_in("l.client_code", $client_not_in_arr);
			$this->db->where_in("l.pjt_id", $pcodes);
			//BPO practice are not shown in IT Services Dashboard
			$practice_not_in = array(6);
			$this->db->where_not_in('l.practice', $practice_not_in);
			$query3   = $this->db->get();
			$pro_data = $query3->result_array();
			
			//get all the actual hours
			$act_hr_calc = $this->get_timesheet_actual_hours_by_pjt_code_arr($pcodes, "", "");

			if(!empty($pro_data) && count($pro_data)>0) {
				foreach($pro_data as $recrd){
					if(isset($effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'])){
						$effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'] += $recrd['estimate_hour'];
						$effvar[$practice_arr[$recrd['practice']]]['total_actual_hrs'] += $act_hr_calc[$recrd['pjt_id']]['total_hours'];
					} else {
						$effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'] = $recrd['estimate_hour'];
						$effvar[$practice_arr[$recrd['practice']]]['total_actual_hrs'] = $act_hr_calc[$recrd['pjt_id']]['total_hours'];
					}
					// $fixed_bid[$practice_arr[$recrd['practice']]][$recrd['pjt_id']] = $recrd['lead_title'];
				}
			}	
			return $effvar;
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
		
	public function get_timesheet_actual_hours($pjt_code, $start_date=false, $end_date=false, $month=false)
	{
		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, SUM(ts.duration_hours) as duration_hours, ts.resoursetype, ts.username, ts.empname, ts.direct_cost_per_hour as direct_cost, sum( ts.`resource_duration_direct_cost`) as duration_direct_cost, sum( ts.`resource_duration_cost`) as duration_cost');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_month_data as ts');
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
		// echo $this->db->last_query(); exit;
		// echo "<pre>"; print_r($timesheet); exit;
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				if(isset($res['total_cost'])) {
					$res['total_cost']     += $ts['duration_cost'];
					$res['total_hours']    += $ts['duration_hours'];
					$res['total_dc'] 	   += $ts['duration_direct_cost'];
				} else {
					$res['total_cost']     = $ts['duration_cost'];
					$res['total_hours']    = $ts['duration_hours'];
					$res['total_dc'] 	   = $ts['duration_direct_cost'];
				}
			}
		}
		/* if($pjt_code == 'ITS-REA- 01-0112') {
			echo '<pre>'; print_r($res); die;
		} */
		// echo "<pre>"; print_r($res); exit;
		return $res;
	}
	
	//for optimization
	public function get_timesheet_actual_hours_by_pjt_code_arr($pjt_code, $start_date=false, $end_date=false, $month=false)
	{
		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, ts.project_code, ts.empname, ts.username, SUM(ts.duration_hours) as duration_hours, ts.resoursetype, ts.username, ts.empname, ts.direct_cost_per_hour as direct_cost, sum( ts.`resource_duration_direct_cost`) as duration_direct_cost, sum( ts.`resource_duration_cost`) as duration_cost');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_month_data as ts');
		$this->db->where_in("ts.project_code", $pjt_code);
		if( (!empty($start_date)) && (!empty($end_date)) ){
			$this->db->where("DATE(ts.start_time) >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("DATE(ts.start_time) <= ", date('Y-m-d', strtotime($end_date)));
		}
		if(!empty($month)) {
			$this->db->where("DATE(ts.start_time) >= ", date('Y-m-d', strtotime($month)));
			$this->db->where("DATE(ts.end_time) <= ", date('Y-m-t', strtotime($month)));
		}
		// $this->db->group_by(array("ts.username", "yr", "month_name", "ts.resoursetype"));
		$this->db->group_by(array("ts.project_code", "ts.username", "yr", "month_name", "ts.resoursetype"));
		
		$query = $this->db->get();
		$timesheet = $query->result_array();
		$res = array();
		// echo $this->db->last_query(); exit;
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				if(isset($res[$ts['project_code']]['total_cost'])) {
					$res[$ts['project_code']]['total_cost']  += $ts['duration_cost'];
					$res[$ts['project_code']]['total_hours'] += $ts['duration_hours'];
					$res[$ts['project_code']]['total_dc'] 	 += $ts['duration_direct_cost'];
				} else {
					$res[$ts['project_code']]['total_cost']  = $ts['duration_cost'];
					$res[$ts['project_code']]['total_hours'] = $ts['duration_hours'];
					$res[$ts['project_code']]['total_dc'] 	 = $ts['duration_direct_cost'];
				}
			}
		}
		// echo "<pre>"; print_r($res); exit;
		return $res;
	}
	
	public function get_timesheet_data($practice_arr, $start_date=false, $end_date=false, $month=false)
	{		
		$this->db->select('dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code');
		$this->db->from($this->cfg['dbpref'].'timesheet_month_data');
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
				} else {
					$resarr[$practice_arr[$row->practice_id]]['totalhour'] = $row->duration_hours;
				}
				if (isset($resarr[$practice_arr[$row->practice_id]]['totalcost'])) {
					$resarr[$practice_arr[$row->practice_id]]['totalcost'] = $resarr[$practice_arr[$row->practice_id]]['totalcost'] + $row->resource_duration_cost;
				} else {
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
	
	public function conver_currency($amount, $val) {
		return round($amount*$val, 2);
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
	
	public function service_dashboard_data()
	{
		/* $curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		//default billable_month
		$month 			= date('Y-m-01 00:00:00');
		if($this->input->post("billable_month")) {
			$bill_month = $this->input->post("billable_month");
			$month      = date("Y-m-01 00:00:00", strtotime($bill_month));
		}
		$month_status = $this->input->post("month_status");
		$current_month 		= date('m');
		$fiscalStartMonth 	= '04';
		$month_status = $this->input->post("month_status");
		
		if(!empty($month_status)) {
			if($month_status == 2) {
				$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
				if($fiscalStartMonth == $current_month) {
					$curFiscalYear 	= getLastFiscalYear();
					$start_date		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
					$end_date   	= ($curFiscalYear)."-03-31";  //eg.2013-04-01
				} else {
					$end_date = date('Y-m-t', strtotime('-1 month', $base_mon));
				}
				$month 	  = date('Y-m-01 00:00:00', strtotime('-1 month', $base_mon));
			} else {
				$end_date  	= date('Y-m-t');
				$month    	= date("Y-m-01 00:00:00");
			}
		}
		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date; */
		
		$post_data 	= $this->input->post();
		$data 		= array();
		
		// dates - start
		$curFiscalYear 	  = getFiscalYearForDate(date("m/d/y"), "4/1", "3/31");
		if(!empty($post_data['fy_name'])) {
			$curFiscalYear = $post_data['fy_name'];
		}
		
		$default_fy_start_month = '04';
		$data['start_month'] 	= $default_fy_start_month;
		if(!empty($post_data['start_month'])) {
			$data['start_month'] = $post_data['start_month'];
		}
		$start_date 	= calc_fy_dates($curFiscalYear, $data['start_month'], 'start');
		
		$default_fy_end_month 	= date('m');
		$data['end_month']  	= $default_fy_end_month;
		if(!empty($post_data['end_month'])) {
			$data['end_month']  = $post_data['end_month'];
		}
		$end_date 		= calc_fy_dates($curFiscalYear, $data['end_month'], 'end');
		
		$month = $data['bill_month'] = ($end_date != "") ? date('Y-m-01 00:00:00', strtotime($end_date)) : date('Y-m-01 00:00:00'); //set the default billing & billable month.
		$data['start_date']  = $start_date;
		$data['end_date']    = $end_date;
		$data['fy_name']     = $curFiscalYear;
		$data['start_month'] = date('m', strtotime($start_date));
		$data['end_month']   = date('m', strtotime($end_date));
		// dates - end
		
		if($this->input->post("practice")) {
			$practice = $this->input->post("practice");
		}
		if($this->input->post("clicktype")) {
			$clicktype = $this->input->post("clicktype");
		}
		
		/* echo '<pre>'; print_r($post_data); echo '<br>';
		print_r($data); echo '</pre>';
		die; */
		
		$project_status = 1; // default - always in progress project only
		
		$this->db->select('l.lead_id, l.lead_title, l.complete_status, l.estimate_hour, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.project_type, l.division');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		
		$this->db->where("l.lead_status", '4');
		$this->db->where("l.customer_type", '1');
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		if($practice) {
			if($practice == 10) { //practice - others
				$pr_arr = array(7, 10, 13);
				$this->db->where_in("l.practice", $pr_arr);
			} else {
				$this->db->where("l.practice", $practice);
			}
		}
		if($project_status){
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if(isset($clicktype) && ($clicktype == 'rag')) {
			$this->db->where_in("l.rag_status", 1);
		}
		if(isset($clicktype) && ($clicktype == 'rag_project_export')) {
			$this->db->where_in("l.rag_status", 1);
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$res = $query->result_array();
		
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		$pquery = $this->db->get();
		$pres1 = $pquery->result();					
		if(!empty($pres1) && count($pres1)>0){
			foreach($pres1 as $prow1) {
				$practice_arr[$prow1->id] = $prow1->practices;
			}
		}

		switch($clicktype){
			case 'noprojects':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "inprogress_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'rag':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "rag_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'inprogress_project_export':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$res = $this->excelexport($data['projects_data']);
			break;
			case 'rag_project_export':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$res = $this->excelexport($data['projects_data']);
			break;
			case 'cm_billing':
				$data['invoices_data'] = $this->getCMIRData($practice, $month);
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "cm_billing_export";
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
			case 'cm_billing_export':
				$data['invoices_data'] = $this->getCMIRData($practice, $month);
				$result = $this->excelexportinvoice($data['invoices_data']);
			break;
			case 'irval':
				$data['invoices_data'] = $this->getIRData($res, $start_date, $end_date, $practice);
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "inv_project_export";
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
			case 'inv_project_export':
				$data['invoices_data'] = $this->getIRData($res, $start_date, $end_date, $practice);
				$result = $this->excelexportinvoice($data['invoices_data']);
			break;
			case 'cm_eff':
				$data = $this->get_billable_efforts_beta($practice, $month); 
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id'] = $practice;
				$this->load->view('projects/service_dashboard_billable_drill_data_beta', $data);
			break;
			case 'ytd_eff':
				$data = $this->get_billable_efforts_beta($practice, "", $start_date, $end_date);
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id'] = $practice;
				$this->load->view('projects/service_dashboard_billable_drill_data_beta', $data);
			break;
			case 'dc_value':
				$data = $this->get_direct_cost_val($practice, "", $start_date, $end_date);
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id']   = $practice;
				$data['start_date']   	= $start_date;
				$data['end_date']   	= $end_date;
				//*for other cost value projects only*//
				$data['othercost_projects'] = array();
				
				$this->db->select("pjt_id, lead_id, practice, lead_title");
				$this->db->where_in('department_id_fk', array(10,11)); //only eads & eqad projects only
				$ocres  = $this->db->get_where($this->cfg['dbpref']."leads", array("practice" => $practice));
				$oc_res = $ocres->result_array();
				
				if(!empty($oc_res)) {
					foreach($oc_res as $ocrow) {
						if (isset($data['othercost_projects'][$practice_arr[$practice]])) {						
							$data['othercost_projects'][$practice_arr[$practice]][] = $ocrow['pjt_id'];
						} else {
							$data['othercost_projects'][$practice_arr[$practice]][] = $ocrow['pjt_id'];
						}
					}
				}
				$this->load->view('projects/service_dashboard_billable_drill_data_beta', $data);
			break;
			case 'fixedbid':
				$billable_ytd = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
				$pcodes = $billable_ytd['project_code'];
				$project_codes = array();
				if(!empty($pcodes) && count($pcodes)>0){
					foreach($pcodes as $rec){
						if(!in_array($rec, $project_codes)){
							$project_codes[] = $rec;
						}
					}
				}
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title, l.complete_status, l.project_type');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_array = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_array = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_array);
				// $this->db->where("l.pjt_id", $rec);
				// $this->db->where("l.billing_type", 1);
				$this->db->where("l.practice", $practice);
				$this->db->where_in("l.pjt_id", $project_codes);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($pro_data, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "fixedbid_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'fixedbid_project_export':
				$billable_ytd = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
				$pcodes = $billable_ytd['project_code'];
				$project_codes = array();
				if(!empty($pcodes) && count($pcodes)>0){
					foreach($pcodes as $rec){
						if(!in_array($rec, $project_codes)){
							$project_codes[] = $rec;
						}
					}
				}
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title, l.complete_status, l.project_type');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_array = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_array = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_array);
				// $this->db->where("l.pjt_id", $rec);
				// $this->db->where("l.billing_type", 1);
				$this->db->where("l.practice", $practice);
				$this->db->where_in("l.pjt_id", $project_codes);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($pro_data, $start_date, $end_date);		
				$res = $this->excelexport($data['projects_data']);
			break;
		}
	}	
	
	public function getProjectsDataByDefaultCurrency($records, $start_date, $end_date)
	{  
		$this->load->model('project_model');
		$rates 					= $this->get_currency_rates();
		$practice_id_year_array = $this->project_model->get_practice_id_year();
		$practice_id_array  	= $this->project_model->get_practice_id();
		$book_keeping_rates 	= get_book_keeping_rates();
		$data['project_record'] 			= array();
		$arr_billing_type_projects 			= array();
		$arr_billing_type_project_codes 	= array();
		$arr_billing_type_project_lead_ids 	= array();
		/** Making billing type vice project details and project codes **/
		foreach($records as $rec) {
			$arr_billing_type_projects[$rec['billing_type']][]						= $rec;
			$arr_billing_type_project_codes[$rec['billing_type']]['project_code'][]	= $rec['pjt_id'];
			$arr_billing_type_project_lead_ids[$rec['billing_type']]['lead_id'][]	= $rec['lead_id'];
		}
		/** Loop through all projects according to billing type  **/
		foreach($arr_billing_type_project_codes as $key_billing_type=>$res) 
		{
			$data['timesheet_data'] = array();
		    $timesheet				= array();
			$lead_id_array 			= $arr_billing_type_project_lead_ids[$key_billing_type]['lead_id'];
			$invoice_amount_array 	= $this->project_model->get_invoice_total_by_lead($lead_id_array);
			$other_cost_array 		= $this->project_model->get_other_cost_by_all_lead($lead_id_array);
			
			$bill_type = 1;
			
			$exp_proj_codes = $res['project_code'];
			/** Getting timesheet details against projects **/
			$timesheet = $this->project_model->get_timesheet_data_updated($exp_proj_codes, '', $bill_type, '', $groupby_type=2);
			/* calculation for UC based on the max hours starts */
			$timesheet_data = array();
			if(count($timesheet)>0) {
				foreach($timesheet as $ts) {
					$financialYear 		= get_current_financial_year($ts['yr'],$ts['month_name']);
					$max_hrs 			= 0;
					if(isset($practice_id_year_array[$ts['practice_id']][$financialYear]))
					{
						$max_hrs = $practice_id_year_array[$ts['practice_id']][$financialYear];
					}
					else if(isset($practice_id_array[$ts['practice_id']]))
					{
						$max_hrs = $practice_id_array[$ts['practice_id']];
					}
					$timesheet_data[$ts['project_code']][$ts['username']]['practice_id'] = $ts['practice_id'];
					$timesheet_data[$ts['project_code']][$ts['username']]['max_hours'] = $max_hrs;
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
					$rateCostPerHr 		 = $this->conver_currency($ts['cost'], $rates[1][$this->default_cur_id]);
					$directrateCostPerHr = $this->conver_currency($ts['direct_cost'], $rates[1][$this->default_cur_id]);
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['direct_rateperhr'] = $directrateCostPerHr;
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration_direct_cost'] = $ts['duration_direct_cost'];
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
					$timesheet_data[$ts['project_code']][$ts['username']][$ts['yr']][$ts['month_name']]['total_hours'] = $ts['resource_total_hours'];
					
				}
			}
			
			$i		= 0; 
			$keys	= array();
			foreach($exp_proj_codes as $proj_key) {
				$total_billable_hrs		= 0;
				$total_non_billable_hrs = 0;
				$total_internal_hrs		= 0;
				$total_cost				= 0;
				$total_hours			= 0;
				$total_dc_hours			= 0;
				$total_amount_inv_raised= 0;
				$other_cost_values		= 0;
				if(array_key_exists($proj_key, $timesheet_data))
				{
					$projects_arr = $timesheet_data[$proj_key];
					foreach($projects_arr as $key1=>$value1) {
						$resource_name = $key1;
						$max_hours = $value1['max_hours'];
						foreach($value1 as $key2=>$value2) {
							$year = $key2;										
							if(!empty($value2) && is_array($value2)){
								foreach($value2 as $key3=>$value3) {
									$individual_billable_hrs		= 0;
									$month		 	  = $key3;
									$billable_hrs	  = 0;
									$non_billable_hrs = 0;
									$internal_hrs	  = 0;
									foreach($value3 as $key4=>$value4) {
										
										switch($key4) {
											case 'Billable':
												$rate				 = $value4['rateperhr'];
												$direct_rateperhr	 = $value4['direct_rateperhr'];
												$billable_hrs		 = $value4['duration'];
												$direct_billable_hrs = $value4['duration_direct_cost'];
												$total_billable_hrs += $billable_hrs;
											break;
											case 'Non-Billable':
												$rate				 	 = $value4['rateperhr'];
												$direct_rateperhr	 	 = $value4['direct_rateperhr'];
												$non_billable_hrs		 = $value4['duration'];
												$direct_non_billable_hrs = $value4['duration_direct_cost'];
												$total_non_billable_hrs += $non_billable_hrs;
											break;
											case 'Internal':
												$rate				 = $value4['rateperhr'];
												$direct_rateperhr	 = $value4['direct_rateperhr'];
												$internal_hrs 		 = $value4['duration'];
												$direct_internal_hrs = $value4['duration_direct_cost'];
												$total_internal_hrs += $internal_hrs;
											break;
										}
									}
								
									$individual_billable_hrs = $value3['total_hours'];											 
									/* calculation for the utilization cost based on the master hours entered. */
									$rate1 = $rate;
									$direct_rateperhr1 = $direct_rateperhr;
									if($individual_billable_hrs>$max_hours){												
										$percentage 		= ($max_hours/$individual_billable_hrs);												
										$rate1 				= number_format(($percentage*$rate),2);
										$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
									}
								 
									$total_hours += $billable_hrs+$internal_hrs+$non_billable_hrs;											
									$total_dc_hours += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);
									$total_cost += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);				
								}
							}
						}
					}
				}
				$proj = $arr_billing_type_projects[$key_billing_type][$i];
				$amt_converted = $this->conver_currency($proj['actual_worth_amount'], $rates[$proj['expect_worth_id']][$this->default_cur_id]);
				$total_amount_inv_raised = 0;
				$invoice_amount=0;
				if(!empty($invoice_amount_array))
				{
					if(array_key_exists($lead_id_array[$i],$invoice_amount_array))
					{
						$invoice_amount = $invoice_amount_array[$lead_id_array[$i]];
						if(count($invoice_amount)>0 && !empty($invoice_amount)){
							$total_amount_inv_raised = $invoice_amount['invoice_amount']+$invoice_amount['tax_amount'];
						}
					}
				}
				/* calculation for UC based on the max hours ends */
				$total_amount_inv_raised = $this->conver_currency($total_amount_inv_raised, $rates[$proj['expect_worth_id']][$this->default_cur_id]);
				/* get the other cost details for the project. */
				$other_cost_values = 0;
				if(!empty($other_cost_array))
				{
					if(array_key_exists($lead_id_array[$i], $other_cost_array))
					{
						$other_cost_values = $this->getOtherCostValuesForBookRates($other_cost_array[$lead_id_array[$i]], $book_keeping_rates);
					}
				}	
			
				/** Building resultant array **/
				$data['project_record'][$proj_key]['lead_id'] 			= $proj['lead_id'];
				$data['project_record'][$proj_key]['lead_title']		= $proj['lead_title'];
				$data['project_record'][$proj_key]['practice']			= $proj['practice'];
				$data['project_record'][$proj_key]['complete_status'] 	= $proj['complete_status'];
				$data['project_record'][$proj_key]['project_type']	 	= $proj['project_billing_type'];
				$data['project_record'][$proj_key]['estimate_hour'] 	= $proj['estimate_hour'];
				$data['project_record'][$proj_key]['actual_worth_amt']  = number_format($amt_converted, 2, '.', '');
				$data['project_record'][$proj_key]['pjt_id']			= $proj['pjt_id'];
				$data['project_record'][$proj_key]['rag_status'] 		= $proj['rag_status'];
				$data['project_record'][$proj_key]['expect_worth_id'] 	= $proj['expect_worth_id'];
				$data['project_record'][$proj_key]['bill_hr'] 			= $total_billable_hrs;
				$data['project_record'][$proj_key]['int_hr'] 			= $total_internal_hrs;
				$data['project_record'][$proj_key]['nbil_hr'] 			= $total_non_billable_hrs;
				$data['project_record'][$proj_key]['other_cost'] 		= $other_cost_values;
				$data['project_record'][$proj_key]['total_hours'] 		= $total_hours;
				$data['project_record'][$proj_key]['total_dc_hours'] 	= $total_dc_hours;
				$data['project_record'][$proj_key]['total_amount_inv_raised'] = $total_amount_inv_raised;
				$data['project_record'][$proj_key]['total_cost'] 		= number_format($total_cost, 2, '.', '');
				
				$i++;
			}
		}
		return $data['project_record'];
	}
	
	/*
	*get all other cost values from the db & sum it
	*base currency
	*/
	function getOtherCostValues($project_id)
	{
		$this->load->model('project_model');
		$value = 0;
		$bk_rates = get_book_keeping_rates(); //get all the book keeping rates
		$other_cost_data = $this->project_model->getOtherCost($project_id);
		if(!empty($other_cost_data) && count($other_cost_data)>0) {
			foreach($other_cost_data as $rec) {
				$conver_value  = 0;
				$curFiscalYear = date('Y'); //set as default current year as fiscal year
				$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
				$convert_value = $this->conver_currency($rec['value'], $bk_rates[$curFiscalYear][$rec['currency_type']][$this->default_cur_id]);
				$value += $convert_value;
			}
		}		
		return $value;
	}
	function getOtherCostValuesForBookRates($other_cost_data,$bk_rates)
	{
		$value = 0;
		if(!empty($other_cost_data) && count($other_cost_data)>0) {
			foreach($other_cost_data as $rec) {
				$conver_value  = 0;
				$curFiscalYear = date('Y'); //set as default current year as fiscal year
				$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
				$convert_value = $this->conver_currency($rec['value'], $bk_rates[$curFiscalYear][$rec['currency_type']][$this->default_cur_id]);
				$value += $convert_value;
			}
		}	
		return $value;
	}
	
	public function getIRData($records, $start_date, $end_date, $practice)
	{
		$bk_rates = get_book_keeping_rates();
		
		$data = array();
		
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, cc.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		if(!empty($practice)) {
			if($practice == 10){ //INFRA SERVICES & TESTING practice values are merged with OTHERS practice
				$pra_arr = array(7, 10, 13);
				$this->db->where_in("l.practice", $pra_arr);
			} else {
				$this->db->where("l.practice", $practice);
			}
			
		}
		if(!empty($start_date)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("sfv.for_month_year <= ", date('Y-m-d H:i:s', strtotime($end_date)));
		}
		
		$query = $this->db->get();
		$invoice_rec = $query->result_array();

		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoice_rec)>0) {
			foreach ($invoice_rec as $inv) {
				$data['invoices'][$i]['lead_title']		= $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 		= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 		= $inv['job_id'];
				$data['invoices'][$i]['customer'] 		= $inv['first_name'].' '.$inv['last_name'].' - '.$inv['company'];
				$data['invoices'][$i]['milestone_name'] = $inv['milestone_name'];

				$base_conversion_amt					= $this->conver_currency($inv['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['expect_worth_id']][$inv['base_currency']]);
				$data['invoices'][$i]['coverted_amt']   = $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['base_currency']][$this->default_cur_id]);
				// converting based on base currency
				$data['invoices'][$i]['month_year']    = $inv['for_month_year'];
				$data['total_amt'] 	                  += $data['invoices'][$i]['coverted_amt'];
				$i++;
			}
		}
		return $data;
	}
	
	public function getCMIRData($practice, $month)
	{
		$bk_rates = get_book_keeping_rates();
		
		$data = array();
		
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, cc.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		if(!empty($practice)) {
			if($practice == 10) { //INFRA SERVICE & TESTING practice values are merged with OTHERS practice
				$pr_arra = array(7, 10, 13);
				$this->db->where_in("l.practice", $pr_arra);
			} else {
				$this->db->where("l.practice", $practice);
			}
		}
		if(!empty($month)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($month)));
			$this->db->where("sfv.for_month_year <= ", date('Y-m-t H:i:s', strtotime($month)));
		}
		$query = $this->db->get();
		$invoice_rec = $query->result_array();
		
		$resarr = array();
		//****//
		
		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoice_rec)>0) {
			foreach ($invoice_rec as $inv) {
				$data['invoices'][$i]['lead_title']		= $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 		= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 		= $inv['job_id'];
				$data['invoices'][$i]['customer'] 		= $inv['first_name'].' '.$inv['last_name'].' - '.$inv['company'];
				$data['invoices'][$i]['milestone_name'] = $inv['milestone_name'];

				$base_conversion_amt					= $this->conver_currency($inv['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['expect_worth_id']][$inv['base_currency']]);
				$data['invoices'][$i]['coverted_amt']   = $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['base_currency']][$this->default_cur_id]);
				// converting based on base currency
				$data['invoices'][$i]['month_year']    = $inv['for_month_year'];
				$data['total_amt'] 	                  += $data['invoices'][$i]['coverted_amt'];
				$i++;
			}
		}
		return $data;
	}
	
	/*
	@method - get_billable_efforts_beta()
	@for drill down data
	*/
	public function get_billable_efforts_beta($practice, $month=false, $start_date=false, $end_date=false)
	{		
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr');
		// $this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->where('t.resoursetype', 'Billable');
		if(!empty($month)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($month))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-t', strtotime($month))."' )", NULL, FALSE);
		}
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-d', strtotime($end_date)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$this->db->where_in("t.practice_id", $practice);

		$query = $this->db->get();
		$data['resdata'] 	   = $query->result(); 
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		
		$data['heading'] 	     = $practice;
		$data['resource_type']   = "Billable";
		$data['filter_sort_by']  = 'desc';
		$data['filter_sort_val'] = 'hour';
		$timesheet_db->close();
		
		return $data;
	}	
	
	/*
	@method - get_direct_cost_val()
	@for drill down data
	*/
	public function get_direct_cost_val($practice, $month=false, $start_date=false, $end_date=false)
	{
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, l.lead_id');
		// $this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
		if(!empty($month)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($month))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-t', strtotime($month))."' )", NULL, FALSE);
		}
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-t', strtotime($end_date)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$resrc = 't.resoursetype IS NOT NULL';
		$deptwhere = "t.dept_id IN ('10','11')";
		$this->db->where($deptwhere);
		$this->db->where($resrc);
		if($practice == 10) { //Infra services & tecting practice values are merged with others practices
			$p_arr = array(7, 10, 13);
			$this->db->where_in("l.practice", $p_arr);
		} else {
			$this->db->where_in("l.practice", $practice);
		}
		// $this->db->where_in('l.lead_id', array(710, 276)); // for temporary - load some data only
		// $this->db->where('l.lead_id', 710); // for temporary - load some data only
		// $this->db->limit(3); // for temporary - load some data only
		$query 					= $this->db->get();
		// echo $this->db->last_query(); exit;
		$data['resdata'] 	   	= $query->result();
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$timesheet_db->close();
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		
		$data['heading'] 	     = $practice;
		$data['resource_type']   = "Billable";
		$data['filter_sort_by']  = 'desc';
		$data['filter_sort_val'] = 'cost';
		
		return $data;
	}
	
	public function excelexport($pjts_data) {
		
		$this->db->select('project_billing_type, id');
		$this->db->from($this->cfg['dbpref']. 'project_billing_type');
		$ptquery = $this->db->get();
		$project_type = $ptquery->result();
		
		$pt_arr = array();
		if(!empty($project_type) && count($project_type)>0){
			foreach($project_type as $prec){
				$pt_arr[$prec->id] = $prec->project_billing_type;
			}
		}
		
		// echo "<pre>"; print_r($pjts_data); die;
		if(count($pjts_data)>0) {
    		//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Projects');

			//set cell A1 content with some text			
			$this->excel->getActiveSheet()->setCellValue('A1', 'Project Title');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Project Completion (%)');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Project Type');
			$this->excel->getActiveSheet()->setCellValue('D1', 'RAG Status');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Planned Hours');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Billable Hours');
			$this->excel->getActiveSheet()->setCellValue('G1', 'Internal Hours');
			$this->excel->getActiveSheet()->setCellValue('H1', 'Non-Billable Hours');
			$this->excel->getActiveSheet()->setCellValue('I1', 'Total Utilized Hours (Actuals)');
			$this->excel->getActiveSheet()->setCellValue('J1', 'Effort Variance');
			$this->excel->getActiveSheet()->setCellValue('K1', 'Project Value ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('L1', 'Utilization Cost ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('M1', 'Direct Cost ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('N1', 'Invoice Raised ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('O1', 'Contribution %');
			$this->excel->getActiveSheet()->setCellValue('P1', 'P&L');
			$this->excel->getActiveSheet()->setCellValue('Q1', 'P&L %');

			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);

			$i=2;
			
    		foreach($pjts_data as $rec) {
			
				$estimate_hr =  (isset($rec['estimate_hour'])) ? $rec['estimate_hour'] : "-";
				switch ($rec['rag_status']) {
					case 1:
						$rag = 'Red';
					break;
					case 2:
						$rag = 'Amber';
					break;
					case 3:
						$rag = 'Green';
					break;
					default:
						$rag = '-';
				}
				$bill_hr = (isset($rec['bill_hr'])) ? round($rec['bill_hr']) : "-";
				$inter_hr = (isset($rec['int_hr'])) ? round($rec['int_hr']) : "-";
				$nbill_hr = (isset($rec['nbil_hr'])) ? round($rec['nbil_hr']) : "-";
				$total_hr = ($rec['bill_hr']+$rec['int_hr']+$rec['nbil_hr']);
				$pjt_val = (isset($rec['actual_worth_amt'])) ? $rec['actual_worth_amt'] : "-";
				$util_cost = (isset($rec['total_cost'])) ? round($rec['total_cost']) : "-";
				$total_amount_inv_raised = (isset($rec['total_amount_inv_raised'])) ? round($rec['total_amount_inv_raised']) : "-";
				
				$profitloss    = round($total_amount_inv_raised-$util_cost);
				$plPercent = round(($profitloss/$util_cost)*100);
				
				$bill_type = $rec['billing_type'];
				
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $rec['lead_title']);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $rec['complete_status']);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $pt_arr[$rec['project_type']]);
				$this->excel->getActiveSheet()->setCellValue('D'.$i, $rag);
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $estimate_hr);
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $bill_hr);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $inter_hr);
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $nbill_hr);
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $total_hr);
				$this->excel->getActiveSheet()->setCellValue('J'.$i, $total_hr-$rec['estimate_hour']);
				$this->excel->getActiveSheet()->setCellValue('K'.$i, $pjt_val);
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $util_cost);
				$total_dc_hours = (isset($rec['total_dc_hours'])) ? (round($rec['total_dc_hours'])) : '0';
				$contributePercent = round((($total_amount_inv_raised-$total_dc_hours)/$total_amount_inv_raised)*100);
				$this->excel->getActiveSheet()->setCellValue('M'.$i, $total_dc_hours);
				$this->excel->getActiveSheet()->setCellValue('N'.$i, $total_amount_inv_raised);
				$this->excel->getActiveSheet()->setCellValue('O'.$i, $contributePercent);
				$this->excel->getActiveSheet()->setCellValue('P'.$i, $profitloss);
				$this->excel->getActiveSheet()->setCellValue('Q'.$i, $plPercent);
				$i++;
    		}
			
			//for first sheet
			$this->excel->setActiveSheetIndex(0);
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
			$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
			//Column Alignment
			$this->excel->getActiveSheet()->getStyle('D2:D'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('E2:E'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('F2:F'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('H2:H'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('I2:I'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('J2:J'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('K2:K'.$i)->getNumberFormat()->setFormatCode('0.00');
			
			// $filename='Project_report.xls'   ; //save our workbook as this file name
			$filename='Project_report_'.time().'.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
    	}
		redirect('/projects/dashboard');
	}
	
	public function excelexportinvoice($invoices_res)
	{
		if((count($invoices_res['invoices'])>0) && !empty($invoices_res['invoices'])) {
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Invoices');
			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', 'Month & Year');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Project Title');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Project Code');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Milestone Name');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Value('.$this->default_cur_name.')');
			
			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			$i=2;		
			$total_amt = '';
			if(count($invoices_res['invoices'])>0) {
				foreach($invoices_res['invoices'] as $excelarr) {
					//display only date
					$this->excel->getActiveSheet()->setCellValue('A'.$i, date('M Y', strtotime($excelarr['month_year'])));
					$this->excel->getActiveSheet()->setCellValue('B'.$i, $excelarr['lead_title']);
					$this->excel->getActiveSheet()->setCellValue('C'.$i, $excelarr['pjt_id']);
					$this->excel->getActiveSheet()->setCellValue('D'.$i, $excelarr['milestone_name']);
					$this->excel->getActiveSheet()->setCellValue('E'.$i, $excelarr['coverted_amt']);
					$i++;
				}
			}
			$this->excel->getActiveSheet()->setCellValue('E'.$i, $invoices_res['total_amt']);
			
			// $this->excel->getActiveSheet()->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('E2:E'.$i)->getNumberFormat()->setFormatCode('0.00');
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
			//merge cell A1 until D1
			//$this->excel->getActiveSheet()->mergeCells('A1:D1');
			//set aligment to center for that merged cell (A1 to D1)
			
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			
			//cell format
			// $this->excel->getActiveSheet()->getStyle('A2:A'.$i)->getNumberFormat()->setFormatCode('00000');
			
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$filename='Invoice.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
						 
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
		}
	}
	
	public function validate_adv_filter_fy_month()
	{
		$post_data 		= $this->input->post();
		$curFiscalYear	= $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31"); //get fiscal year
		$config_data 	= $this->config->item('crm');
		$fy_month 		= $config_data['fy_months'];
		$fy_start_month = '04';
		$data = array();
		$fy_st_mon_opt 	= '<option value="">--Select--</option>';
		$fy_end_mon_opt = '<option value="">--Select--</option>';
		if($post_data['fy_name'] < $curFiscalYear) {
			$fy_end_month 	= '03';
			if(!empty($fy_month) && count($fy_month)>IS_ZERO) {
				foreach($fy_month as $fy_mon_key=>$fy_mon_val) {
					$sel_st_mon  	 = ($fy_start_month == $fy_mon_key) ? 'selected="selected"' : '';
					$sel_end_mon 	 = ($fy_end_month == $fy_mon_key) ? 'selected="selected"' : '';
					$fy_st_mon_opt  .= '<option value="'.$fy_mon_key.'" '.$sel_st_mon.'>'.$fy_mon_val.'</option>';
					$fy_end_mon_opt .= '<option value="'.$fy_mon_key.'" '.$sel_end_mon.'>'.$fy_mon_val.'</option>';
				}
			}
		} else {
			$fy_end_month 	= date('m');
			if(!empty($fy_month) && count($fy_month)>IS_ZERO) {
				foreach($fy_month as $fy_mon_key=>$fy_mon_val) {
					$sel_st_mon  	 = ($fy_start_month == $fy_mon_key) ? 'selected="selected"' : '';
					$sel_end_mon 	 = ($fy_end_month == $fy_mon_key) ? 'selected="selected"' : '';
					$fy_st_mon_opt  .= '<option value="'.$fy_mon_key.'" '.$sel_st_mon.'>'.$fy_mon_val.'</option>';
					$fy_end_mon_opt .= '<option value="'.$fy_mon_key.'" '.$sel_end_mon.'>'.$fy_mon_val.'</option>';
					if ($fy_mon_key == $fy_end_month) { break; }
				}
			}
		}
		$data['fy_st']  = $fy_st_mon_opt;
		$data['fy_end'] = $fy_end_mon_opt;
		echo json_encode($data);
		exit;
	}
	
	public function set_fy_month()
	{
		$post_data 		= $this->input->post();
		$config_data 	= $this->config->item('crm');
		$fy_month 		= $config_data['fy_months'];
		$default_fy_start_month = '04';
		$i = 0;
		
		$curFiscalYear	= $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31"); //get fiscal year
		
		$start_month 	= (isset($post_data['start_month']) && !empty($post_data['start_month'])) ? $post_data['start_month'] : $default_fy_start_month;
		
		$fy_end_mon_opt = '<option value="">--Select--</option>';
		
		if(!empty($fy_month) && count($fy_month)>IS_ZERO) {
			foreach($fy_month as $fy_mon_key=>$fy_mon_val) {
				if(($fy_mon_key != $start_month) && ($i == 0)) {
					continue;
				} else {
					$sel 			 = ($i == 0) ? 'selected="selected"' : '';
					$fy_end_mon_opt .= '<option value="'.$fy_mon_key.'" '.$sel.'>'.$fy_mon_val.'</option>';
					$i++;
					if(($post_data['fy_name'] == $curFiscalYear) && ($fy_mon_key == date('m'))) {
						break;
					}
				}
			}
		}
		$data['fy_end'] = $fy_end_mon_opt;
		echo json_encode($data);
		exit;
	}

}
/* End of dms resource_availability file */
