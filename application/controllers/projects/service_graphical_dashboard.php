<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
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
		
		$uc_filter_by  = 'hour';

		$data['start_date'] 	= $start_date;
		$data['end_date']   	= $end_date;
		$data['uc_filter_by'] 	= $uc_filter_by;

		//get utilization cost values from service graphical dashboard table
		$data['uc_graph_val'] = $this->service_graphical_dashboard_model->getUcRecords($uc_filter_by = 'hour');
		//get invoice records
		$invoice_data = $this->service_graphical_dashboard_model->getInvoiceRecords($start_date, $end_date);
		$data['invoice_val'] = $this->calcInvoiceDataByPractice($invoice_data);
		
		$this->load->view('projects/service_graphical_dashboard_view', $data);
	}
	
	/*
	*@method calcInvoiceData()
	*@param array
	*/
	public function calcInvoiceDataByPractice($invoice_data)
	{
		$inv_array = array();
		$bk_rates = get_book_keeping_rates();
		foreach($invoice_data as $ir) {
			$base_conversion_camt = converCurrency($ir['milestone_value'],$bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
			
			if(isset($inv_array[$ir['practices']])){
				$inv_array[$ir['practices']] += converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
			} else {
				$inv_array[$ir['practices']] = converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
			}
		}
		// echo "<pre>"; print_r($inv_array); exit;
		return $inv_array;
	}
	
	/*
	*@method getUcVal()
	*@return json
	*/
	public function getUcVal()
	{		
		$postdata = $this->input->post();
		$uc_filter_by = 'hour';
		if(isset($postdata['uc_filter_by'])){
			$uc_filter_by = $postdata['uc_filter_by'];
		}
		
		$data['uc_graph_val'] = $this->service_graphical_dashboard_model->get_uc_details($uc_filter_by);
		
		$res['result']  = true;
		$res['html'] 	= $this->load->view('projects/graphical_box_uc', $data, true);
		echo json_encode($res); exit;
	}
	
	
}
/* End of file */