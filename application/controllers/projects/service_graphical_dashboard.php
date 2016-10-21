<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
error_reporting(E_ALL);
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
	}

	public function index()
	{	
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$data['page_heading'] = "YTD Utilization Cost Dashboard";
		$res 				  = array();
		$res['result']		  = false;

		$curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		$last_yr_start_date = date('Y-m-d', strtotime($start_date.' -1 year'));
		$last_yr_end_date   = date('Y-m-t', strtotime($last_yr_start_date.' +11 months'));
		
		$uc_filter_by   = 'hour'; //default_value
		$inv_filter_by  = 'inv_month'; //default_value

		$data['start_date'] 	= $start_date;
		$data['end_date']   	= $end_date;
		$data['uc_filter_by'] 	= $uc_filter_by;
		$data['inv_filter_by'] 	= $inv_filter_by;

		//get utilization cost values from service graphical dashboard table
		$data['uc_graph_val'] = $this->service_graphical_dashboard_model->getUcRecords($uc_filter_by = 'hour');
		//get current fiscal ytd invoice records
		$invoice_data = $this->service_graphical_dashboard_model->getInvoiceRecords($start_date, $end_date);
		$data['invoice_val'] = $this->calcInvoiceDataByPractice($invoice_data);
		//get last fiscal year invoice records
		$curr_yr_inv_value = $this->calcInvoiceDataByMonthWise($invoice_data);
		$data['inv_compare']['curr_yr']['mon_inv_value'] = $curr_yr_inv_value['allValuesArr'];
		$data['inv_compare']['curr_yr']['tot_inv_value'] = $curr_yr_inv_value['total_value'];
		$last_yr_invoice_data = $this->service_graphical_dashboard_model->getInvoiceRecords($last_yr_start_date, $last_yr_end_date);
		$last_yr_inv_value = $this->calcInvoiceDataByMonthWise($last_yr_invoice_data);
		$data['inv_compare']['last_yr']['mon_inv_value'] = $last_yr_inv_value['allValuesArr'];
		$data['inv_compare']['last_yr']['tot_inv_value'] = $last_yr_inv_value['total_value'];
		// echo "<pre>"; print_r($data['inv_compare']); exit;
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
				if($ir['practices'] == 'Infra Services') { //infra services practices are merged with other practices
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
		// echo "<pre>"; print_r($inv_array); exit;
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

		$uc_filter_by = 'hour';
		if(isset($postdata['uc_filter_by'])){
			$uc_filter_by = $postdata['uc_filter_by'];
		}
		
		$data['uc_graph_val'] = $this->service_graphical_dashboard_model->getUcRecords($uc_filter_by);
		// echo "<pre>"; print_r($data); exit;
		
		$res['result']  = true;
		$res['html'] 	= $this->load->view('projects/graphical_box_uc', $data, true);
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
		$res['html'] 	= $this->load->view('projects/graphical_box_inv_compare', $data, true);
		echo json_encode($res); exit;
	}
}
/* End of file */