<?php
class report_moved_project extends crm_controller {
    
public $userdata;
	
    function __construct() {
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');	
		$this->load->model('report/report_moved_project_model');
        $this->load->model('welcome_model');
        $this->load->model('project_model');
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
    	$data['report'] = $this->get_moved_project_report();
		$data['practices']   = $this->project_model->get_practices();
		$data['sales_divisions'] = $this->welcome_model->get_sales_divisions();
		
		$this->load->vars($data);
    	$this->load->view('report/report_moved_project');		   	    	   	
    }

    
    public function get_moved_project_report() {  	
    	
    	$data =array();
    	$options = array();
		
		if($this->input->post('start_date')=='' && $this->input->post('end_date')=='')
		{
			$_POST['start_date']=date('01-m-Y');
			$_POST['end_date']=date('31-m-Y');
		}
		$options['end_date'] = $this->input->post('end_date');
		$options['start_date'] = $this->input->post('start_date');	
		$options['practices'] = $this->input->post('practices');
		$options['divisions'] = $this->input->post('divisions');
			
    	$res = $this->report_moved_project_model->getMovedproject($options);   	
    	
    	$data['res'] = $res['res'];
    	$data['num'] = $res['num'];
    	if($data['num']>0) {
	    	// currency_convert();
	    	$data['rates'] = $this->get_currency_rates();
    	}
    	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
   			$this->load->view('report/moved_project_report_view',$data);
		} else {
    		return $this->load->view('report/moved_project_report_view',$data,true);
		}
    }
   
    public function excelExport() {
    	$options = array();
		
		if($this->input->post('start_date')=='' && $this->input->post('end_date')=='')
		{
			$_POST['start_date']=date('01-m-Y');
			$_POST['end_date']=date('31-m-Y');
		}
		
    	$options['end_date'] = $this->input->post('end_date');
		$options['start_date'] = $this->input->post('start_date');	
		$options['practices'] = $this->input->post('practices');
		$options['divisions'] = $this->input->post('divisions');

    	$res = $this->report_moved_project_model->getMovedproject($options);  	
    	if($res['num']>0) {
    		//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Project Created');											
			//set cell A1 content with some text			
			$this->excel->getActiveSheet()->setCellValue('A1', 'Project No.');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Project Title');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Customer');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Practice');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Entity');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Project Start Date');
			$this->excel->getActiveSheet()->setCellValue('G1', 'Project End Date');
			$this->excel->getActiveSheet()->setCellValue('H1', 'Project Status');
			$this->excel->getActiveSheet()->setCellValue('I1', 'Actual Worth ('.$this->default_cur_name.')');
			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			$i=2;		
    		/*To build columns*/
			$leads = $res['res'];
			//currency_convert();
    		$rates = $this->get_currency_rates();
    		
    		$cnt = 0;$st=2;$end = 3;
    		$gross = 0;
    		$amt = 0;
    		foreach($leads as $lead) {
				if($lead->date_start!='') {
					$start_date=date("d-m-Y",strtotime($lead->date_start));
				}else{
					$start_date='';
				}
				
				if($lead->date_due!='') {
					$end_date=date("d-m-Y",strtotime($lead->date_due));
				}else{
					$end_date='';
				}
								
    			$this->excel->getActiveSheet()->setCellValue('A'.$i, $lead->invoice_no);
    			$this->excel->getActiveSheet()->setCellValue('B'.$i, $lead->lead_title);
    			$this->excel->getActiveSheet()->setCellValue('C'.$i, $lead->first_name.' '.$lead->last_name);
    			$this->excel->getActiveSheet()->setCellValue('D'.$i, $lead->practices);
    			$this->excel->getActiveSheet()->setCellValue('E'.$i, $lead->division_name);
    			$this->excel->getActiveSheet()->setCellValue('F'.$i, $start_date);
    			$this->excel->getActiveSheet()->setCellValue('G'.$i, $end_date);
    			
    			if($lead->pjt_status == 1)
					$status = 'In Progress';
				else if ($lead->pjt_status == 2)
					$status = 'Completed';
				else if ($lead->pjt_status == 3)
					$status = 'Onhold';
				else if ($lead->pjt_status == 4)
					$status = 'Inactive';
    			$this->excel->getActiveSheet()->setCellValue('H'.$i, $status);
    			$amt_converted = $this->conver_currency($lead->actual_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
    			$this->excel->getActiveSheet()->setCellValue('I'.$i, $amt_converted);
    			$amt += $amt_converted;
    			$i++;
    			$cnt++;
    		}
    		/*To build columns ends*/
    		$this->excel->getActiveSheet()->getStyle('J2:J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    		/*Gross total starts*/
    		$this->excel->getActiveSheet()->setCellValue('H'.$i, 'Total ('.$this->default_cur_name.')');
    		$this->excel->getActiveSheet()->setCellValue('I'.$i, $amt);
    		$this->excel->getActiveSheet()->getStyle('H'.$i.':'.'I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    		$this->excel->getActiveSheet()->getStyle('H'.$i.':'.'I'.$i)->getFont()->setBold(true);
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
			$filename='Project_created.xls'   ; //save our workbook as this file name
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
		$currency_rates = $this->report_moved_project_model->get_currency_rate();
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
