<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
// error_reporting(E_ALL);
class Service_graphical_dashboard extends crm_controller 
{
	function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('form');
        $this->load->helper('custom');
		$this->load->helper('lead_stage');
		$this->load->helper('url');
		$this->load->model('projects/service_graphical_dashboard_model');
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
		if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
			$end_date = date('Y-m-t');
		} else {
			$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
			$end_date = date('Y-m-t', strtotime('-1 month', $base_mon)); // changed upto last month only
		}
		
		$this->upto_month = date('M', strtotime($end_date));
	}

	public function index()
	{
 // echo 'hi'; exit;
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		
		$data  				  = array();
		
		$curFiscalYearTemp = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		
		$curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
//               / print_r($curFiscalYear);exit;
                $lastFiscalYear =  getLastFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$data['current_year_val'] = $curFiscalYearTemp;
		$lastMonthArrCalcNoForEndmonth = array('04', '05');
		if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
			$end_date = date('Y-m-t');
		} else {
			$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
			$end_date = date('Y-m-t', strtotime('-1 month', $base_mon)); // changed upto last month only
		}
		$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year'));
		// echo $last_yr_end_date   = date('Y-m-t', strtotime($end_date.' +11 months')); upto Last date of last financial year
		if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
			$last_yr_end_date = date('Y-m-t');
		} else {
			$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year')); //upto current month of last financial year
		}
		
		$data['fiscal_year_status'] = $curFiscalYear;
                $data['last_year'] = $lastFiscalYear;
               //print_r($data['last_year']);exit;
		$data['fy_year']  = $this->service_graphical_dashboard_model->get_records($tbl='financial_year', $wh_condn=array(), $order=array('id'=>'desc'));

		$data['page_heading'] = "IT Service Graphical Dashboard";
		if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
			$post_data = real_escape_array($this->input->post());
			// echo '<pre>'; print_r($post_data); die;
			$data['fiscal_year_status'] = $post_data['fy_name'];
			$start_date    = ($data['fiscal_year_status'] -1)."-04-01";  //eg.2013-04-01
			$end_date 	   = $data['fiscal_year_status'].'-03-31';
			//print_r($start_date);exit;
			$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year'));
                      //  echo $last_yr_start_date;exit;
			$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year'));
			$this->upto_month   = date('M', strtotime($end_date));
			if($curFiscalYearTemp == $data['fiscal_year_status']) {
				$cur_date = date('Y-m-t');
				$this->upto_month = date('M', strtotime($cur_date));
			}
		}
		$res 				  = array();
		$res['result']		  = false;
		
		/* if($data['fiscal_year_status']=='current') {
			$curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
			$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
			
			$lastMonthArrCalcNoForEndmonth = array('04', '05');
			if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
				$end_date = date('Y-m-t');
			} else {
				$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
				$end_date = date('Y-m-t', strtotime('-1 month', $base_mon)); // changed upto last month only
			}
			
			$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year'));
			// echo $last_yr_end_date   = date('Y-m-t', strtotime($end_date.' +11 months')); upto Last date of last financial year
			if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
				$last_yr_end_date = date('Y-m-t');
			} else {
				$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year')); //upto current month of last financial year
			}
		} else if($data['fiscal_year_status']=='last') {
			$curFiscalYear = getLastFiscalYear();
			$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
			$end_date 	   = $curFiscalYear.'-03-31';
			
			$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year')); 
			$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year'));
			$this->upto_month   = date('M', strtotime($end_date));
		} */

		$uc_filter_by   = 'cost'; //default_value
		$inv_filter_by  = 'inv_month'; //default_value

		$data['start_date'] 	= $start_date;
		$data['end_date']   	= $end_date;
		$data['uc_filter_by'] 	= $uc_filter_by;
		$data['inv_filter_by'] 	= $inv_filter_by;

		//practice
		$data['practice_arr'] = $this->service_graphical_dashboard_model->get_practices();
               
		// $data['practice_arr']['practice_array']; //normal practice array
		// $data['practice_arr']['practice_arr']; //key value practice array
		
		//get utilization cost values from service graphical dashboard table
		$data['uc_graph_val'] = $this->service_graphical_dashboard_model->getUcRecords($uc_filter_by = 'cost', $data['fiscal_year_status']);
                
                $data['uc_cost_graph_val'] = $this->service_graphical_dashboard_model->getUcCostRecords($uc_filter_by = 'cost',$data['last_year']);
		
                foreach($data['uc_cost_graph_val'] as $key => $value){
                   $ytd_utilization_cost = $value['ytd_utilization_cost'];
                   if($value['ytd_utilization_cost'] != '-'){
                       $data['array_lastyr'] = array_column($data['uc_cost_graph_val'], $ytd_utilization_cost);
                   }else{
                       $data['array_lastyr'] = '-';
                   }
                    //
                }

                
                 //Get an array of just the app_subject_id colu mn
          // $data['uc_lastyr_cost_graph_val'] = implode(',', $array_lastyr);
               
                $data['uc_curcost_graph_val'] = $this->service_graphical_dashboard_model->getUcCurYrRecords($uc_filter_by = 'cost', $data['fiscal_year_status']);
              echo "<pre>"; print_r($data['uc_curcost_graph_val']); exit;
                foreach($data['uc_curcost_graph_val'] as $key => $value){
                          $ytd_utilization_cost = $value['ytd_utilization_cost'];
                       //   $data['array_cur'] = array_column($data['uc_curcost_graph_val'],$ytd_utilization_cost); //Get an array of just the app_subject_id colu mn
                  
                 }
               print_r($ytd_utilization_cost);
                
//   $data['uc_curyr_graph_val'] = implode(',', $array_cur);
                
  

                foreach($data['uc_cost_graph_val'] as $key => $value){
                  
                    $data['uc_cost_graph_val']['practic_cost_val'][$key]['y'] = isset($value[ytd_billable]) ? (int)$value[ytd_billable] : 0;
                    $data['uc_cost_graph_val']['practic_cost_val'][$key]['label'] = $value[practice_name];
                    
                }
             //  echo '<pre>';  print_r( $data['uc_cost_graph_val']['practic_cost_val']);exit;
//                $data['uc_graph_val']['practic_val'][$i]['y'] = isset($pract_last_yr_val[$prac_name]) ? $pract_last_yr_val[$prac_name] : 0;
//                $data['prat_rev_inv_compare']['practic_val'][$i]['label'] = $prac_name;
		
		//get current fiscal ytd invoice records
		$invoice_data = $this->service_graphical_dashboard_model->getInvoiceRecords($start_date, $end_date);
		$data['invoice_val'] = $this->calcInvoiceDataByPractice($invoice_data);
               // echo '<pre>'; print_r($invoice_data);exit;
		
		//current_year revenue - entity wise
		$data['invoice_val_by_entity'] = $this->calcInvoiceDataByEntity($invoice_data);
		
		//get last fiscal year invoice records
		$curr_yr_inv_value = $this->calcInvoiceDataByMonthWise($invoice_data);
		$data['inv_compare']['curr_yr']['mon_inv_value'] = $curr_yr_inv_value['allValuesArr'];
		$data['inv_compare']['curr_yr']['tot_inv_value'] = $curr_yr_inv_value['total_value'];
		$last_yr_invoice_data = $this->service_graphical_dashboard_model->getInvoiceRecords($last_yr_start_date, $last_yr_end_date);
		 
                $last_yr_inv_value = $this->calcInvoiceDataByMonthWise($last_yr_invoice_data);
		$data['inv_compare']['last_yr']['mon_inv_value'] = $last_yr_inv_value['allValuesArr'];
		$data['inv_compare']['last_yr']['tot_inv_value'] = $last_yr_inv_value['total_value'];
		//for x values
		foreach($this->fiscal_month_arr as $fis_mon){
			$data['inv_compare']['fis_mon_upto_current'][] = $fis_mon;
			if($this->upto_month == $fis_mon) { break; } //from current month only
		}
                
		//for last year 
		$pract_curr_yr_val = $data['invoice_val'];
                $pract_last_yr_val = $this->calcInvoiceDataByPractice($last_yr_invoice_data);
               // print_r($pract_curr_yr_val);exit;
               
		if(!empty($data['practice_arr']['practice_array']) && count($data['practice_arr']['practice_array'])>0) {
                        $i=0;
			foreach($data['practice_arr']['practice_array']  as $prac_name) {
				if($prac_name == 'Infra Services' || $prac_name == 'Testing') {
					continue;
				}
                                //practice name
                                $data['prat_rev_inv_compare']['practic_val'][$i]['y'] = isset($pract_last_yr_val[$prac_name]) ? $pract_last_yr_val[$prac_name] : 0;
				$data['prat_rev_inv_compare']['practic_val'][$i]['label'] = $prac_name;
                               
                                $data['prat_inv_compare']['practic_val'][] = $prac_name;
                                $data['prat_inv_compare']['curr_yr_val'][] = isset($pract_curr_yr_val[$prac_name]) ? $pract_curr_yr_val[$prac_name] : 0;
				$data['prat_inv_compare']['last_yr_val'][] = isset($pract_last_yr_val[$prac_name]) ? $pract_last_yr_val[$prac_name] : 0;
			
                                $i++; 
                         }
		}
//           /     echo '<pre>';print_r($data['prat_inv_compare']['last_yr_val'])  ;exit;        
		//get the trend values from invoice & create an array based on practice wise & month wise
		$trend_value 	 = calcInvoiceDataByPracticeWiseMonthWise($invoice_data, $this->default_cur_id);
		$trend_pract_arr = array();
		//allign trend array by practicewise then monthwise
		if(!empty($data['practice_arr']['practice_array']) && count($data['practice_arr']['practice_array'])>0 && count($trend_value)>0) {
			foreach($data['practice_arr']['practice_array'] as $prac_name) {
				if($prac_name == 'Infra Services' || $prac_name == 'Testing') {
					continue;
				}
				$trend_pract_arr['practic_arr'][] = $prac_name;
				foreach($this->fiscal_month_arr as $fis_mon) {
					$trend_pract_arr['trend_pract_val_arr'][$prac_name][] = isset($trend_value[$prac_name][$fis_mon]) ? $trend_value[$prac_name][$fis_mon] : 0;
					if($this->upto_month == $fis_mon) { break; }
				}
			}
		}
		foreach($this->fiscal_month_arr as $fis_mon) {
			$trend_pract_arr['trend_mont_arr'][] = $fis_mon;
			if($this->upto_month == $fis_mon) { break; }
		}
		$data['trend_pract_month_val'] = $trend_pract_arr;
		// echo "<pre>"; print_r($trend_pract_arr); exit;
		// echo "<pre>"; print_r($data['prat_inv_compare']); echo"</pre>"; exit;
		//contribution values from services_graphical_dashboard table
		//select field names
		
		$sel_values = 'practice_name,';
		foreach($this->fiscal_month_arr as $fis_mons) {
			$sel_values .= 'contri_'.$fis_mons.' AS "'.$fis_mons.'"';
			if($this->upto_month == $fis_mons) { break; }
			else { $sel_values .= ','; }
		}
		// echo $sel_values; exit;
		$data['contri_graph_val'] = $this->service_graphical_dashboard_model->getContributionRecords($sel_values, $data['fiscal_year_status']);
		// echo "<pre>"; print_r($data['contri_graph_val']); exit;
		
		$data['contri_tot_val'] = $this->service_graphical_dashboard_model->getTotalContributionRecord($data['fiscal_year_status']);
		//echo json_encode($data);exit;
		$this->load->view('projects/service_graphical_dashboard_view', $data);
	}
        
        public function revenueVsCost()
	{
         // echo 'hi';exit;
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		
		$data = array();
		
		$curFiscalYearTemp = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		
		$curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$data['current_year_val'] = $curFiscalYearTemp;
		$lastMonthArrCalcNoForEndmonth = array('04', '05');
		if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
			$end_date = date('Y-m-t');
		} else {
			$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
			$end_date = date('Y-m-t', strtotime('-1 month', $base_mon)); // changed upto last month only
		}
		$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year'));
		// echo $last_yr_end_date   = date('Y-m-t', strtotime($end_date.' +11 months')); upto Last date of last financial year
		if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
			$last_yr_end_date = date('Y-m-t');
		} else {
			$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year')); //upto current month of last financial year
		}
		
		$data['fiscal_year_status'] = $curFiscalYear;
		$data['fy_year']  = $this->service_graphical_dashboard_model->get_records($tbl='financial_year', $wh_condn=array(), $order=array('id'=>'desc'));

		$data['page_heading'] = "IT Service Graphical Dashboard";
		if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
			$post_data = real_escape_array($this->input->post());
			// echo '<pre>'; print_r($post_data); die;
			$data['fiscal_year_status'] = $post_data['fy_name'];
			$start_date    = ($data['fiscal_year_status']-1)."-04-01";  //eg.2013-04-01
			$end_date 	   = $data['fiscal_year_status'].'-03-31';
			
			$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year'));
			$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year'));
			$this->upto_month   = date('M', strtotime($end_date));
			if($curFiscalYearTemp == $data['fiscal_year_status']) {
				$cur_date = date('Y-m-t');
				$this->upto_month = date('M', strtotime($cur_date));
			}
		}
		$res 				  = array();
		$res['result']		  = false;
		
		/* if($data['fiscal_year_status']=='current') {
			$curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
			$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
			
			$lastMonthArrCalcNoForEndmonth = array('04', '05');
			if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
				$end_date = date('Y-m-t');
			} else {
				$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
				$end_date = date('Y-m-t', strtotime('-1 month', $base_mon)); // changed upto last month only
			}
			
			$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year'));
			// echo $last_yr_end_date   = date('Y-m-t', strtotime($end_date.' +11 months')); upto Last date of last financial year
			if(in_array(date('m'), $lastMonthArrCalcNoForEndmonth)) {
				$last_yr_end_date = date('Y-m-t');
			} else {
				$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year')); //upto current month of last financial year
			}
		} else if($data['fiscal_year_status']=='last') {
			$curFiscalYear = getLastFiscalYear();
			$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
			$end_date 	   = $curFiscalYear.'-03-31';
			
			$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year')); 
			$last_yr_end_date   = date('Y-m-t', strtotime($end_date.' -1 year'));
			$this->upto_month   = date('M', strtotime($end_date));
		} */

		$uc_filter_by   = 'cost'; //default_value
		$inv_filter_by  = 'inv_month'; //default_value

		$data['start_date'] 	= $start_date;
		$data['end_date']   	= $end_date;
		$data['uc_filter_by'] 	= $uc_filter_by;
		$data['inv_filter_by'] 	= $inv_filter_by;

		//practice
		$data['practice_arr'] = $this->service_graphical_dashboard_model->get_practices();
		// $data['practice_arr']['practice_array']; //normal practice array
		// $data['practice_arr']['practice_arr']; //key value practice array
		
		//get utilization cost values from service graphical dashboard table
		$data['uc_graph_val'] = $this->service_graphical_dashboard_model->getUcRecords($uc_filter_by = 'cost', $data['fiscal_year_status']);
		//echo "<pre>"; print_r($data['uc_graph_val']); exit;
		
		//get current fiscal ytd invoice records
		$invoice_data = $this->service_graphical_dashboard_model->getInvoiceRecords($start_date, $end_date);
		$data['invoice_val'] = $this->calcInvoiceDataByPractice($invoice_data);
              
		
		//current_year revenue - entity wise
		$data['invoice_val_by_entity'] = $this->calcInvoiceDataByEntity($invoice_data);
		
		//get last fiscal year invoice records
		$curr_yr_inv_value = $this->calcInvoiceDataByMonthWise($invoice_data);
		$data['inv_compare']['curr_yr']['mon_inv_value'] = $curr_yr_inv_value['allValuesArr'];
		$data['inv_compare']['curr_yr']['tot_inv_value'] = $curr_yr_inv_value['total_value'];
		$last_yr_invoice_data = $this->service_graphical_dashboard_model->getInvoiceRecords($last_yr_start_date, $last_yr_end_date);
		 
                $last_yr_inv_value = $this->calcInvoiceDataByMonthWise($last_yr_invoice_data);
		$data['inv_compare']['last_yr']['mon_inv_value'] = $last_yr_inv_value['allValuesArr'];
		$data['inv_compare']['last_yr']['tot_inv_value'] = $last_yr_inv_value['total_value'];
		//for x values
		foreach($this->fiscal_month_arr as $fis_mon){
			$data['inv_compare']['fis_mon_upto_current'][] = $fis_mon;
			if($this->upto_month == $fis_mon) { break; } //from current month only
		}
                
		//for last year 
		$pract_curr_yr_val = $data['invoice_val'];
                $pract_last_yr_val = $this->calcInvoiceDataByPractice($last_yr_invoice_data);
            
               
		if(!empty($data['practice_arr']['practice_array']) && count($data['practice_arr']['practice_array'])>0) {
			foreach($data['practice_arr']['practice_array'] as $prac_name) {
				if($prac_name == 'Infra Services' || $prac_name == 'Testing') {
					continue;
				}
                                //practice name
				$data['prat_inv_compare']['practic_val'][] = $prac_name;
                                $data['prat_inv_compare']['curr_yr_val'][] = isset($pract_curr_yr_val[$prac_name]) ? $pract_curr_yr_val[$prac_name] : 0;
				$data['prat_inv_compare']['last_yr_val'][] = isset($pract_last_yr_val[$prac_name]) ? $pract_last_yr_val[$prac_name] : 0;
			}
		}
		
		//get the trend values from invoice & create an array based on practice wise & month wise
		$trend_value 	 = calcInvoiceDataByPracticeWiseMonthWise($invoice_data, $this->default_cur_id);
		$trend_pract_arr = array();
		//allign trend array by practicewise then monthwise
		if(!empty($data['practice_arr']['practice_array']) && count($data['practice_arr']['practice_array'])>0 && count($trend_value)>0) {
			foreach($data['practice_arr']['practice_array'] as $prac_name) {
				if($prac_name == 'Infra Services' || $prac_name == 'Testing') {
					continue;
				}
				$trend_pract_arr['practic_arr'][] = $prac_name;
				foreach($this->fiscal_month_arr as $fis_mon) {
					$trend_pract_arr['trend_pract_val_arr'][$prac_name][] = isset($trend_value[$prac_name][$fis_mon]) ? $trend_value[$prac_name][$fis_mon] : 0;
					if($this->upto_month == $fis_mon) { break; }
				}
			}
		}
		foreach($this->fiscal_month_arr as $fis_mon) {
			$trend_pract_arr['trend_mont_arr'][] = $fis_mon;
			if($this->upto_month == $fis_mon) { break; }
		}
		$data['trend_pract_month_val'] = $trend_pract_arr;
		// echo "<pre>"; print_r($trend_pract_arr); exit;
		// echo "<pre>"; print_r($data['prat_inv_compare']); echo"</pre>"; exit;
		//contribution values from services_graphical_dashboard table
		//select field names
		
		$sel_values = 'practice_name,';
		foreach($this->fiscal_month_arr as $fis_mons) {
			$sel_values .= 'contri_'.$fis_mons.' AS "'.$fis_mons.'"';
			if($this->upto_month == $fis_mons) { break; }
			else { $sel_values .= ','; }
		}
		// echo $sel_values; exit;
		$data['contri_graph_val'] = $this->service_graphical_dashboard_model->getContributionRecords($sel_values, $data['fiscal_year_status']);
		// echo "<pre>"; print_r($data['contri_graph_val']); exit;
		
		$data['contri_tot_val'] = $this->service_graphical_dashboard_model->getTotalContributionRecord($data['fiscal_year_status']);
		// echo "<pre>"; print_r($data); exit;
		$this->load->view('projects/service_graphical_dashboard_view', $data);
	}

	
	/*
	*@method calcInvoiceDataByPractice()
	*@param array
	*/
	public function calcInvoiceDataByPractice($invoice_data)
	{
		$inv_array = array();
		$bk_rates = get_book_keeping_rates();
		if(is_array($invoice_data) && !empty($invoice_data) && count($invoice_data)>0) {
                    
			foreach($invoice_data as $ir) {
                            
				if($ir['practices'] == 'Infra Services' || $ir['practices'] == 'Testing') { //infra services & Testing practice values are merged with other practices
					$ir['practices'] = 'Others';
				}
				$base_conversion_camt = converCurrency($ir['milestone_value'],$bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				if(isset($inv_array[$ir['practices']])){
					$inv_array[$ir['practices']] += converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				} else {
					$inv_array[$ir['practices']] = converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				}
			}
		}
		
		return $inv_array;
	}
	
	/*
	*@method calcInvoiceDataByEntity()
	*@param array
	*/
	public function calcInvoiceDataByEntity($invoice_data)
	{
		$inv_array = array();
		$inv_array['entity_name'] = array();
		$bk_rates = get_book_keeping_rates();
		if(is_array($invoice_data) && !empty($invoice_data) && count($invoice_data)>0) {
			foreach($invoice_data as $ir) {
				if(!in_array($ir['division_name'], $inv_array['entity_name'])){
					$inv_array['entity_name'][] = $ir['division_name'];
				}
				$base_conversion_camt = converCurrency($ir['milestone_value'],$bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				if(isset($inv_array['entity_val'][$ir['division_name']])){
					$inv_array['entity_val'][$ir['division_name']] += converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				} else {
					$inv_array['entity_val'][$ir['division_name']] = converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				}
			}
		}
		return $inv_array;
	}
		
	/*
	*@method calcInvoiceDataByPractice()
	*@param array
	*/
	public function calcInvoiceDataByMonthWise($invoice_data)
	{
		$data 				= array();
		$inv_array 			= array();
		$valuesArr 			= array();
		$allValuesArr 		= array();
		$monthArr 			= array();
		$totalSum 			= 0;
		$bk_rates = get_book_keeping_rates();
		if(is_array($invoice_data) && !empty($invoice_data) && count($invoice_data)>0) {
			foreach($invoice_data as $ir) {

				$mon = date("M", strtotime($ir['for_month_year']));
				
				$base_conversion_camt = converCurrency($ir['milestone_value'],$bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
								
				if (in_array($mon, $monthArr)) {
					$inv_array = $valuesArr[$mon] + converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				} else {
					$inv_array = converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
				}
				$valuesArr[$mon] = $inv_array;
				$monthArr[] 	 = $mon;
			}
		}
		foreach($this->fiscal_month_arr as $fis_mon){
			$allValuesArr[] = isset($valuesArr[$fis_mon]) ? $valuesArr[$fis_mon] : 0;
			$totalSum 	   += isset($valuesArr[$fis_mon]) ? $valuesArr[$fis_mon] : 0;
			if($this->upto_month == $fis_mon) { break; } //from current month only
		}
		$data['total_value'] = $totalSum;
		$data['allValuesArr'] = $allValuesArr;
		return $data;
	}
	
	/*
	*@method getUcVal()
	*@return json
	*/
	public function getUcVal()
	{
		$res = array('result'=>false);
		
		$postdata = $this->input->post();

		$uc_filter_by = 'cost';
		if(isset($postdata['uc_filter_by'])){
			$uc_filter_by = $postdata['uc_filter_by'];
		}
		
		$data['uc_graph_val'] = $this->service_graphical_dashboard_model->getUcRecords($uc_filter_by, $postdata['fiscal_year_status']);
		// echo "<pre>"; print_r($data); exit;
		
		$res['result']  = true;
		$res['html'] 	= $this->load->view('projects/service_graphical_box_uc', $data, true);
		echo json_encode($res); exit;
	}

	/*
	*@method getInvoiceFilter()
	*@return json
	*/
	public function getInvoiceFilter()
	{
		$res = array('result'=>false);
		
		$postdata = $this->input->post();

		$inv_filter_by = 'inv_month'; //default_value
		if(isset($postdata['inv_filter_by'])){
			$inv_filter_by = $postdata['inv_filter_by'];
		}
		$data['inv_filter_by'] = $inv_filter_by;
		$res['result']  = true;
		$res['html'] 	= $this->load->view('projects/service_graphical_box_inv_compare', $data, true);
		echo json_encode($res); exit;
	}
}
/* End of file */