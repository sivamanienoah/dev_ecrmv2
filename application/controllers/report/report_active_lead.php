<?php
class report_active_lead extends crm_controller {
    
public $userdata;
	
    function __construct() {
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');	
		$this->load->model('report/report_lead_region_model');
        $this->load->model('report/report_active_lead_model');        
        $this->load->model('welcome_model');
        $this->load->model('regionsettings_model');        
        $this->load->library('validation');
		$this->load->library('email');
		$this->load->helper('custom_helper');
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id = '1';
			$this->default_cur_name = 'USD';
		}
    }
    
    public function index() {  
    	$data = array();
    	$data['lead_stage'] = $this->welcome_model->get_lead_stage();
    	$data['customers'] = $this->welcome_model->get_customers();	
		$data['regions'] = $this->regionsettings_model->region_list();		
    	$data['report'] = $this->get_lead_report();
		$data['user'] = $this->report_active_lead_model->get_users_list('users', 'userid, first_name, emp_id', 'first_name');
		$this->load->vars($data);
    	$this->load->view('report/report_active_lead');		   	    	   	
    }

    
    public function get_lead_report() {  	
    	
    	$data =array();
    	$options = array();
    	$options['customer'] = $this->input->post('customer');
    	//$options['range'] = $this->input->post('range');
		$options['end_date'] = $this->input->post('end_date');
		$options['leadassignee'] = $this->input->post('leadassignee');
		$options['owner'] = $this->input->post('owner');
		$options['stage'] = $this->input->post('stage');
		$options['start_date'] = $this->input->post('start_date');
		$options['worth'] = $this->input->post('worth');		
		$options['regionname'] = $this->input->post('regionname');		
		$options['countryname'] = $this->input->post('countryname');		
		$options['statename'] = $this->input->post('statename');		
		$options['locname'] = $this->input->post('locname');
		
   		if($this->userdata['level'] >1){
			$options['cust_id'] =  $this->report_lead_region_model->getCustomerByLocation();						
		}		
    	//$res = $this->report_lead_assignee_model->getLeadReportByAssignee($options);
    	$res = $this->report_active_lead_model->getActiveLead($options);   	
    	
    	$data['res'] = $res['res'];
    	$data['num'] = $res['num'];
    	if($data['num']>0) {
	    	// currency_convert();
	    	$data['rates'] = $this->get_currency_rates();
    	}
    	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
   			$this->load->view('report/active_lead_report_view',$data);
		} else {
    		return $this->load->view('report/active_lead_report_view',$data,true);
		}
    }
   
    public function excelExport() {
    	$options = array();
    	$options['customer'] = $this->input->post('customer');
    	$options['range'] = $this->input->post('range');
		//$options['end_date'] = $this->input->post('end_date');
		$options['leadassignee'] = $this->input->post('leadassignee');
		$options['owner'] = $this->input->post('owner');
		$options['stage'] = $this->input->post('stage');
		//$options['start_date'] = $this->input->post('start_date');
		$options['worth'] = $this->input->post('worth');		
		$options['regionname'] = $this->input->post('regionname');
		$options['countryname'] = $this->input->post('countryname');
		$options['statename'] = $this->input->post('statename');
		$options['locname'] = $this->input->post('locname');

    	if($this->userdata['level'] >1){
			$options['cust_id'] =  $this->report_lead_region_model->getCustomerByLocation();						
		}
		
    	$res = $this->report_active_lead_model->getActiveLead($options);  	
    	if($res['num']>0) {
    		//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Leads');											
			//set cell A1 content with some text			
			$this->excel->getActiveSheet()->setCellValue('A1', 'Lead No.');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Lead Title');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Customer');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Region');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Lead Owner');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Lead Assignee');
			$this->excel->getActiveSheet()->setCellValue('G1', 'Lead Indicator');
			$this->excel->getActiveSheet()->setCellValue('H1', 'Lead Stage');
			$this->excel->getActiveSheet()->setCellValue('I1', 'Status');
			$this->excel->getActiveSheet()->setCellValue('J1', 'Expected Worth ('.$this->default_cur_name.')');
			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			$i=2;		
    		/*To build columns*/
			$leads = $res['res'];
			//currency_convert();
    		$rates = $this->get_currency_rates();
    		$lead_reg = array();
    		
    		$region_name = array();
    		$cnt = 0;$st=2;$end = 3;
    		$gross = 0;
    		$amt = 0;
    		foreach($leads as $lead) {
    			$this->excel->getActiveSheet()->setCellValue('A'.$i, $lead->invoice_no);
    			$this->excel->getActiveSheet()->setCellValue('B'.$i, $lead->lead_title);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $lead->company.' - '.$lead->cust_first_name);
    			$this->excel->getActiveSheet()->setCellValue('D'.$i, $lead->region_name);
    			$this->excel->getActiveSheet()->setCellValue('E'.$i, $lead->ownrfname.' '.$lead->ownrlname);
    			$this->excel->getActiveSheet()->setCellValue('F'.$i, $lead->usrfname.' '.$lead->usrlname);
    			$this->excel->getActiveSheet()->setCellValue('G'.$i, $lead->lead_indicator);
    			$this->excel->getActiveSheet()->setCellValue('H'.$i, $lead->lead_stage_name);
    			if($lead->lead_status == 1)
					$status = 'Active';
				else if ($lead->lead_status == 2)
					$status = 'On Hold';
				else 
					$status = 'Dropped';
    			$this->excel->getActiveSheet()->setCellValue('I'.$i, $status);
    			$amt_converted = $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
    			$this->excel->getActiveSheet()->setCellValue('J'.$i, $amt_converted);
    			$amt += $amt_converted;
    			$i++;
    			$cnt++;
    		}
    		/*To build columns ends*/
    		$this->excel->getActiveSheet()->getStyle('J2:J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    		/*Gross total starts*/
    		$this->excel->getActiveSheet()->setCellValue('I'.$i, 'Total ('.$this->default_cur_name.')');
    		$this->excel->getActiveSheet()->setCellValue('J'.$i, $amt);
    		$this->excel->getActiveSheet()->getStyle('I'.$i.':'.'J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    		$this->excel->getActiveSheet()->getStyle('I'.$i.':'.'J'.$i)->getFont()->setBold(true);
    		/*Gross total ends*/
    		//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);
			//merge cell A1 until D1			
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
			//Column Alignment
			$this->excel->getActiveSheet()->getStyle('A2:A'.$i)->getNumberFormat()->setFormatCode('00000');
			$filename='Active_lead_report.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
    	}    	
    	redirect('/report/report_active_lead/');
    }
    
	public function get_currency_rates() {
		$currency_rates = $this->report_active_lead_model->get_currency_rate();
    	$rates = array();
    	if(!empty($currency_rates)){
    		foreach ($currency_rates as $currency) {
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}
	
	public function conver_currency($amount,$val) {
		return round($amount*$val);
	}
}
