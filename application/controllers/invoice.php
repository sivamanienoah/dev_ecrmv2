<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/********************************************************************************
File Name       : invoice.php
Created Date    : 31/08/2014
Modified Date   : 14/01/2015
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Karthikeyan.S
*********************************************************************************/
/**
 * Invoice
 *
 * @class 		Invoice
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      User Module
 * @Menu        Invoices
 * @author 		eNoah
 * @Controller
 */

class Invoice extends CRM_Controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@Invoice
	*/
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('invoice_model');
		$this->load->model('email_template_model');
		$this->load->helper('custom_helper');
		$this->load->helper('text_helper');
		$this->userdata = $this->session->userdata('logged_in_user');
		
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
	*@Get Invoices List
	*@Method index
	*/
    public function index($search_type = false, $search_id = false)
	{
        $data['page_heading'] = 'Invoices';
		
		$data['projects']  		 = $this->invoice_model->get_projects();
		$data['customers'] 		 = $this->invoice_model->get_customers();
		$data['practices'] 		 = $this->invoice_model->get_practices();
		$data['sales_divisions'] = $this->invoice_model->get_sales_divisions();
		$data['saved_search']    = $this->invoice_model->get_saved_search($this->userdata['userid'], $search_for=3);
		
		$project   = 'null';
		$customer  = 'null';
		$divisions = 'null';
		$practice  = 'null';
		$from_date = 'null';
		$to_date   = 'null';
		$month_year_from_date = 'null';
		$month_year_to_date   = 'null';
		
		$filter =  array();
		$data['val_export'] = 'no_search';
		if($search_type == 'search' && $search_id == false) {
			$filter = real_escape_array($this->input->post());
			// echo "<pre>"; print_r($filter); exit;
			$data['val_export'] = 'search';
		} else if ($search_type == 'search' && is_numeric($search_id)) {
			
			$wh_condn = array('search_id'=>$search_id, 'search_for'=>3, 'user_id'=>$this->userdata['userid']);
			$get_rec  = $this->invoice_model->get_data_by_id('saved_search_critriea', $wh_condn);
			if(!empty($get_rec)) {
				$data['val_export'] = $search_id;
				unset($get_rec['search_id']);
				unset($get_rec['search_for']);
				unset($get_rec['search_name']);
				unset($get_rec['user_id']);
				unset($get_rec['is_default']);
				unset($get_rec['stage']);
				unset($get_rec['pjtstage']);
				unset($get_rec['leadassignee']);
				unset($get_rec['owner']);
				unset($get_rec['worth']);
				unset($get_rec['regionname']);
				unset($get_rec['countryname']);
				unset($get_rec['statename']);
				unset($get_rec['locname']);
				unset($get_rec['lead_status']);
				unset($get_rec['lead_indi']);
				unset($get_rec['service']);
				$filter	  = real_escape_array($get_rec);
				$filter['filter'] = 'filter';
			}
		} else {
			$wh_condn = array('search_for'=>3, 'user_id'=>$this->userdata['userid'], 'is_default'=>1);
			$get_rec  = $this->invoice_model->get_data_by_id('saved_search_critriea', $wh_condn);
			// echo $this->db->last_query(); # exit;
			if(!empty($get_rec)) {
				$data['val_export'] = $get_rec['search_id'];
				unset($get_rec['search_id']);
				unset($get_rec['search_for']);
				unset($get_rec['search_name']);
				unset($get_rec['user_id']);
				unset($get_rec['is_default']);
				unset($get_rec['stage']);
				unset($get_rec['pjtstage']);
				unset($get_rec['leadassignee']);
				unset($get_rec['owner']);
				unset($get_rec['worth']);
				unset($get_rec['regionname']);
				unset($get_rec['countryname']);
				unset($get_rec['statename']);
				unset($get_rec['locname']);
				unset($get_rec['lead_status']);
				unset($get_rec['lead_indi']);
				unset($get_rec['service']);
				$filter	  = real_escape_array($get_rec);
			}
		}
		// echo 'val_export '.$data['val_export']; exit;
		$bk_rates = get_book_keeping_rates();

		$invoices = $this->invoice_model->get_invoices($filter,0);
		// echo $this->db->last_query(); die;
		$rates 	  = $this->get_currency_rates();
		$data['default_currency'] = $this->default_cur_name;
		$data['invoices'] = array();
		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoices)>0) {
			foreach ($invoices as $inv) {
				$data['invoices'][$i]['received']			    = $inv['received'];
				$data['invoices'][$i]['expectid']			    = $inv['expectid'];
				$data['invoices'][$i]['lead_title']			    = $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 				= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 				= $inv['lead_id'];
				$data['invoices'][$i]['customer'] 			    = $inv['company'].' - '.$inv['customer_name'];
				$data['invoices'][$i]['project_milestone_name'] = $inv['project_milestone_name'];
				$data['invoices'][$i]['actual_amt'] 			= $inv['expect_worth_name']." ".$inv['amount'];
				
				$base_conversion_amt = $this->conver_currency($inv['amount'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['month_year'])),"4/1","3/31")][$inv['expect_worth_id']][$inv['base_currency']]);
				$data['invoices'][$i]['entity_conversion_name']  = $inv['base_currency'];
				$data['invoices'][$i]['entity_conversion_value'] = $base_conversion_amt;
				$data['invoices'][$i]['coverted_amt'] = $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['month_year'])),"4/1","3/31")][$inv['base_currency']][$this->default_cur_id]);
				// converting based on base currency
				$data['invoices'][$i]['invoice_generate_notify_date'] = $inv['invoice_generate_notify_date'];
				$data['invoices'][$i]['month_year'] 				  = $inv['month_year'];
				$data['total_amt'] 	                           += $data['invoices'][$i]['coverted_amt'];
				$i++;
			}
		}
		
		// echo "<pre>"; print_r($data['invoices']); exit;
		$currencies = $this->invoice_model->get_currencies();
		if(!empty($currencies)){
			foreach($currencies as $cure){
				$data['currency_names'][$cure['expect_worth_id']] = $cure['expect_worth_name'];
			}
		}
		
		if($filter['filter']!="")
			$this->load->view('invoices/invoice_view_grid', $data);
		else
			$this->load->view('invoices/invoice_view', $data);
    }
	
	/*
	*method : get_currency_rates
	*/
	public function get_currency_rates() {
		$currency_rates = $this->invoice_model->get_currency_rate();
    	$rates 			= array();
    	if(!empty($currency_rates)) {
    		foreach ($currency_rates as $currency) {
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}
	
	public function conver_currency($amount,$val) {
		return ($amount*$val);
		// return round($amount*$val);
	}
	
	/*
	*method : get_search_name_form
	*/
	public function get_search_name_form() {
		$html = '<table><tr>';
		$html .= '<td><label>Search Name:</label></td>';
		$html .= '<td><input type="text" class="textfield width160px" name="search_name" id="search_name" maxlength="30" value="" /></td></tr><tr>';
		$html .= '<td><label>Is Default:</label></td>';
		$html .= '<td><input type="checkbox" name="is_default" id="is_default" value="1" /></td></tr><tr><td colspan=2>';
		$html .= '<div class="buttons"><button onclick="save_search(); return false;" class="positive" type="submit">Save</button>
		<button onclick="save_cancel(); return false;" class="negative" type="submit">Cancel</button></div></td></tr></table>';
		echo json_encode($html);
		exit;
	}
	
	//For Saving the search criteria
	public function save_search($type)
	{
		$post_data = real_escape_array($this->input->post());
		// echo "<pre>"; print_r($post_data); exit;
		$ins = array();
		
		$ins['search_for']  		 = $type;
		$ins['search_name'] 		 = $post_data['search_name'];
		$ins['user_id']	  			 = $this->userdata['userid'];
		$ins['is_default']			 = $post_data['is_default'];
		$ins['customer']			 = $post_data['customer'];
		$ins['project']	  			 = $post_data['project'];
		$ins['divisions'] 			 = $post_data['divisions'];
		$ins['practice'] 			 = $post_data['practice'];
		if($post_data['from_date']!='')
		$ins['from_date']			 = date('Y-m-d H:i:s', strtotime($post_data['from_date']));
		else
		$ins['from_date']  	    	 = '0000-00-00 00:00:00';
		if($post_data['to_date']!='')
		$ins['to_date']	 			 = date('Y-m-d H:i:s', strtotime($post_data['to_date']));
		else
		$ins['to_date']   			 = '0000-00-00 00:00:00';
		if($post_data['month_year_from_date']!='')
		$ins['month_year_from_date'] = date('Y-m-d H:i:s', strtotime($post_data['month_year_from_date']));
		else
		$ins['month_year_from_date']   = '0000-00-00 00:00:00';
		if($post_data['month_year_to_date']!='')
		$ins['month_year_to_date']   = date('Y-m-d H:i:s', strtotime($post_data['month_year_to_date']));
		else
		$ins['month_year_to_date']   = '0000-00-00 00:00:00';
		$ins['created_on'] 			 = date('Y-m-d H:i:s');
		// echo "<pre>"; print_r($ins); exit;
		$last_ins_id = $this->invoice_model->insert_row_return_id('saved_search_critriea', $ins);
		if($last_ins_id) {
			if($post_data['is_default'] == 1) {
				$updt['is_default'] = 0;
				$this->db->where('search_id != ', $last_ins_id);
				$this->db->where('user_id', $this->userdata['userid']);
				$this->db->where('search_for', $type);
				$this->db->update($this->cfg['dbpref'] . 'saved_search_critriea', $updt);
			}
			
			$saved_search = $this->invoice_model->get_saved_search($this->userdata['userid'], $search_for=$type);
			
			$result['res'] = true;
			$result['msg'] = 'Search Criteria Saved.';
			
			$result['search_div'] = '';
			$result['search_div'] .= '<li id="item_'.$last_ins_id.'" class="saved-search-res"><span><a href="javascript:void(0)" onclick="show_search_results('.$last_ins_id.')">'.$post_data['search_name'].'</a></span>';
			$result['search_div'] .= '<span class="rd-set-default">';
			$result['search_div'] .= '<input type="radio" name="set_default_search" class="set_default_search" value="'.$last_ins_id.'" ';
			if($post_data['is_default']==1) { 
				$result['search_div'] .= 'checked="checked"';
			}
			$result['search_div'] .= '/>';
			$result['search_div'] .= '</span>';
			$result['search_div'] .= '<span><a title="Set Default" href="javascript:void(0)" onclick="delete_save_search('.$last_ins_id.')" ><img alt="delete" src="assets/img/trash.png"></a></span></li>';

		} else {
			$result['res'] = false;
			$result['msg'] = 'Search Criteria cant be Saved.';
		}
		echo json_encode($result);
		exit;
	}
	
	/*
	*set_default_search
	*/
	public function set_default_search($search_id, $type) {
		
		$result = array();
		
		$tbl = 'saved_search_critriea';
		$wh_condn = array('search_for'=>$type, 'user_id'=>$this->userdata['userid']);
		
		$updt = $this->invoice_model->update_records($tbl,$wh_condn,'',$up_arg=array('is_default'=>0));
		$updt_condn = $this->invoice_model->update_records($tbl,$wh_condn=array('search_id'=>$search_id),'',$up_arg=array('is_default'=>1));

		if($updt_condn) {
			$result['resu'] = 'updated';
		}

		echo json_encode($result);
		exit;
	}
	
	/*
	* delete_save_search
	*/
	public function delete_save_search($search_id, $type) {
		
		$result = array();
		
		$tbl = 'saved_search_critriea';
		$wh_condn = array('search_for'=>$type, 'search_id'=>$search_id);

		if($this->invoice_model->delete_records($tbl, $wh_condn)) {
			$result['resu'] = 'deleted';
		}
		
		echo json_encode($result);
		exit;
	}
	
	
	/*
	 *Exporting data(leads) to the excel
	 */
	public function invExcelExport($searchId=false) 
	{
		$filter = array();
	
		if($searchId != '' & is_numeric($searchId)) {
			$wh_condn = array('search_id'=>$searchId, 'search_for'=>3, 'user_id'=>$this->userdata['userid']);
			$get_rec  = $this->invoice_model->get_data_by_id('saved_search_critriea', $wh_condn);
			if(!empty($get_rec))
			$filter	= real_escape_array($get_rec);
		} else {
			$filter = real_escape_array($this->input->post());
		}
	
		$rates 	  = $this->get_currency_rates();
		$default_currency = $this->default_cur_name;
		
		$bk_rates = get_book_keeping_rates();
	
		/* if((!empty($filter['project'])) && $filter['project']!='null')
		$filter['project'] = explode(",",$filter['project']);
		else 
		$filter['project'] = '';
		
		if((!empty($filter['customer'])) && $filter['customer']!='null')
		$filter['customer'] = explode(",",$filter['customer']);
		else
		$filter['customer'] = '';
		
		if((!empty($filter['divisions'])) && $filter['divisions']!='null')
		$filter['divisions'] = explode(",",$filter['divisions']);
		else
		$filter['divisions'] = '';
		
		if((!empty($filter['practice'])) && $filter['practice']!='null')
		$filter['practice'] = explode(",",$filter['practice']);
		else
		$filter['practice'] = '';
		
		if((!empty($filter['divisions'])) && $filter['divisions']!='null')
		$filter['divisions'] = explode(",",$filter['divisions']);
		else
		$filter['divisions'] = ''; */
		
		if(!empty($filter['from_date']))
		$filter['from_date'] = $filter['from_date'];
		else
		$filter['from_date'] = '';
		
		if(!empty($filter['to_date']))
		$filter['to_date'] = $filter['to_date'];
		else
		$filter['to_date'] = '';
		
		if(!empty($filter['month_year_from_date']))
		$filter['month_year_from_date'] = $filter['month_year_from_date'];
		else
		$filter['month_year_from_date'] = '';
		
		if(!empty($filter['month_year_to_date']))
		$filter['month_year_to_date'] = $filter['month_year_to_date'];
		else
		$filter['month_year_to_date'] = '';
		
		$invoices_res = $this->invoice_model->get_invoices($filter,0);
		
		// echo $this->db->last_query(); exit;
		
		//load our new PHPExcel library
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Invoices');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Invoice Date');
		$this->excel->getActiveSheet()->setCellValue('B1', 'Month & Year');
		$this->excel->getActiveSheet()->setCellValue('C1', 'Customer Name');
		$this->excel->getActiveSheet()->setCellValue('D1', 'Project Title');
		$this->excel->getActiveSheet()->setCellValue('E1', 'Project Code');
		$this->excel->getActiveSheet()->setCellValue('F1', 'Milestone Name');
		$this->excel->getActiveSheet()->setCellValue('G1', 'Actual Value');
		$this->excel->getActiveSheet()->setCellValue('H1', 'Value('.$default_currency.')');
		
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
		$i=2;		
		$total_amt = '';
		if(count($invoices_res)>0) {
			foreach($invoices_res as $excelarr) {
				//display only date
				$this->excel->getActiveSheet()->setCellValue('A'.$i, date('d-m-Y', strtotime($excelarr['invoice_generate_notify_date'])));
				$this->excel->getActiveSheet()->setCellValue('B'.$i, date('M Y', strtotime($excelarr['month_year'])));
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $excelarr['first_name'].' '.$excelarr['last_name'].' - '.$excelarr['company']);
				$this->excel->getActiveSheet()->setCellValue('D'.$i, $excelarr['lead_title']);
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $excelarr['pjt_id']);
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $excelarr['project_milestone_name']);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $excelarr['expect_worth_name'].' '.$excelarr['amount']);
				// $this->excel->getActiveSheet()->setCellValue('H'.$i, $this->conver_currency($excelarr['amount'], $rates[$excelarr['expect_worth_id']][$this->default_cur_id]));
				// converting based on base currency
				$base_conversion_amt = $this->conver_currency($excelarr['amount'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($excelarr['month_year'])),"4/1","3/31")][$excelarr['expect_worth_id']][$excelarr['base_currency']]);
				// $amt = $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($excelarr['month_year'])),"4/1","3/31")][$excelarr['base_currency']][$this->default_cur_id]);
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($excelarr['month_year'])),"4/1","3/31")][$excelarr['base_currency']][$this->default_cur_id]));
				
				// $amt 	   = $this->conver_currency($excelarr['amount'], $rates[$excelarr['expect_worth_id']][$this->default_cur_id]);
				
				// $base_conversion_amt = $this->conver_currency($excelarr['amount'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($excelarr['month_year'])),"4/1","3/31")][$excelarr['expect_worth_id']][$excelarr['base_currency']]);
				$amt = $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($excelarr['month_year'])),"4/1","3/31")][$excelarr['base_currency']][$this->default_cur_id]);
				// converting based on base currency
				
				$total_amt += $amt;
				$i++;
			}
		}
		$this->excel->getActiveSheet()->setCellValue('H'.$i, $total_amt);
		
		$this->excel->getActiveSheet()->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode('0.00');
		$this->excel->getActiveSheet()->getStyle('H2:H'.$i)->getNumberFormat()->setFormatCode('0.00');
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
		//merge cell A1 until D1
		//$this->excel->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		
		//Set width for cells
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);			
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		
		//cell format
		$this->excel->getActiveSheet()->getStyle('A2:A'.$i)->getNumberFormat()->setFormatCode('00000');
		
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
	
	/*
	method to get the list of payment invoice has been sent.
	*/
	
	public function payment($search_type = false, $search_id = false)
	{
		$data = array();
		$data['page_heading'] = "Payment(s) List";
		$data['results'] = $this->invoice_model->get_payment_invoice_list();
		$this->load->view('invoices/payment_milestones_view', $data);			
    }
	
	/*
	method to show the invoices for the payment list history
	*/

	public function show_invoice_history(){
		$invoice_id = $this->input->get("invoice_id");
		if($invoice_id){
			$this->db->select("invc.exp_id,exp.*,expw.expect_worth_name,le.lead_title");
			$this->db->from($this->cfg['dbpref']."invoices_child as invc");
			$this->db->join($this->cfg['dbpref']."expected_payments as exp","exp.expectid = invc.exp_id");
			//$this->db->join($this->cfg['dbpref']."expected_payments as exp","exp.expectid = invc.exp_id");
			$this->db->join($this->cfg['dbpref']."leads as le","le.lead_id = exp.jobid_fk");
			$this->db->join($this->cfg['dbpref']."expect_worth as expw","expw.expect_worth_id = le.expect_worth_id");
			$this->db->where("invc.inv_id",$invoice_id);
			$qry = $this->db->get();
			if($qry->num_rows()>0){
				$res = $qry->result();
				$data['invoices'] = $res;
			}
			$this->load->view("invoices/show_invoice_history",$data);
		}
	}
	
	public function show_payment_history(){
		$expect_id = $this->input->get("expect_id");
		if($expect_id){
			$this->db->select("inv.*,paym.*");
			$this->db->from($this->cfg['dbpref']."invoices_child as invc");
			$this->db->join($this->cfg['dbpref']."invoices as inv","inv.inv_id = invc.inv_id");
			$this->db->join($this->cfg['dbpref']."payment_history as paym","paym.inv_id = inv.inv_id","Right");
			//$this->db->join($this->cfg['dbpref']."expected_payments as exp","exp.expectid = invc.exp_id");
			//$this->db->join($this->cfg['dbpref']."leads as le","le.lead_id = exp.jobid_fk");
			//$this->db->join($this->cfg['dbpref']."expect_worth as expw","expw.expect_worth_id = le.expect_worth_id");
			$this->db->where("invc.exp_id",$expect_id);
			$qry = $this->db->get();
			if($qry->num_rows()>0){
				$res = $qry->result();
				$data['payments'] = $res;
			}
			//echo '<pre>';print_r($data['payments']);exit;
			$this->load->view("invoices/show_payment_history",$data);
		}		
	}
	
	public function send_invoice($expid){
		$data = array();
		$data['expresults'] = '';
		$data['customers'] = $this->invoice_model->get_invoice_customer_usentity();
		$data['payment_options'] = $this->invoice_model->get_payment_options();
		$data['page_heading'] = "Send Invoice(s)";
		$this->load->view("invoices/send_invoice",$data);
	}
	
	public function edit_invoice($expid){
		$data = array();
		$data['expresults'] = '';
		$data['attachments'] = '';
		$this->db->select('expm.tax,expm.tax_price,expm.total_amount,expm.expectid,expm.invoice_status,expm.amount,expm.project_milestone_name,expm.invoice_generate_notify_date,expm.expected_date,expm.month_year, l.lead_title,l.lead_id,l.custid_fk,l.pjt_id,l.expect_worth_id,ew.expect_worth_name,c.custid,c.customer_name,c.company,c.email_1,c.email_2,c.email_3,c.email_4');
		$this->db->from($this->cfg['dbpref'].'expected_payments as expm');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = expm.jobid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');		
		$this->db->where("expm.expectid", $expid);		
		$qry = $this->db->get();
		if($qry->num_rows()>0){
			$res = $qry->row();
			$attcs = $this->db->get_where($this->cfg['dbpref']."expected_payments_attachments",array("expectid" => $expid));
			if($attcs->num_rows > 0){
				$data['attachments'] = $attcs->result();
			}
			$data['expresults'] = $res;
		}
		//echo '<pre>';print_r($res);exit;
		$data['customers'] = $this->invoice_model->get_invoice_customer();
		$data['payment_options'] = $this->invoice_model->get_payment_options();
		$data['page_heading'] = "Edit Invoice";
		$this->load->view("invoices/edit_invoice",$data);
	}
	
	function save_invoice()
	{
		$errors = false;
		$invoice_id = $this->input->post("invoice_id");		
		$qry = $this->db->get_where($this->cfg['dbpref']."expected_payments",array("expectid" => $invoice_id));
		if($qry->num_rows()>0){
			/* echo "<pre>";
			print_r($_REQUEST);
			exit; */
			$project_milestone_name = $this->input->post("project_milestone_name");
			$tax = $this->input->post("tax");
			$tax_price = $this->input->post("tax_price");
			$total = $this->input->post("total");
			
			$this->load->library('upload');

			$files = $_FILES;
			$cpt = count($_FILES['attachment']['name']);
			
			if($cpt>0 && !empty($cpt) && $_FILES['attachment']['size'][0]>0){
				for($i=0; $i<$cpt; $i++)
				{ 
					$_FILES['attachment']['name']= $files['attachment']['name'][$i];
					$_FILES['attachment']['type']= $files['attachment']['type'][$i];
					$_FILES['attachment']['tmp_name']= $files['attachment']['tmp_name'][$i];
					$_FILES['attachment']['error']= $files['attachment']['error'][$i];
					$_FILES['attachment']['size']= $files['attachment']['size'][$i];    
					
					$config = array();
					$config['upload_path'] = FCPATH.'crm_data/invoices/';
					$config['allowed_types'] = 'pdf|doc|docx|jpg|png';				
					
					$this->upload->initialize($config);
					if( ! $this->upload->do_upload("attachment"))
					{                                           
						$data['upload_message'] = $this->upload->display_errors(); // ERR_OPEN and ERR_CLOSE are error delimiters defined in a config file
						$this->load->vars($data);
						$errors = TRUE;
					}else{
						$upload_data = $this->upload->data();
						$arr = array("expectid" => $invoice_id,"file_name" => $upload_data['file_name'],"created_on" => date("Y-m-d"),"created_by" => $this->userdata['userid']);
						$this->db->insert($this->cfg['dbpref']."expected_payments_attachments",$arr);
					}
				}
				if($errors){
					$this->session->set_userdata("error_message",$data['upload_message']);
					redirect("invoice");				
				}				
			}

			$this->db->update($this->cfg['dbpref']."expected_payments",array("project_milestone_name" => $project_milestone_name,"tax" => $tax,"tax_price" => $tax_price,"total_amount" => $total),array("expectid" => $invoice_id));
			//echo $this->db->last_query();exit;
			
			$this->session->set_userdata("success_message","Invoice has been updated successfully");
			redirect("invoice");
		}else{
			$this->session->set_userdata("error_message","Invalid process!");
			redirect("invoice");
		}
	}
	
	function delete_attachment(){
		$id = $this->input->get("id");
		if($id){
			$qry = $this->db->get_where($this->cfg['dbpref']."expected_payments_attachments",array("id" => $id));
			if($qry->num_rows()>0){
				$res = $qry->row();
				unlink(FCPATH.'crm_data/invoices/'.$res->file_name);
				$this->db->delete($this->cfg['dbpref']."expected_payments_attachments",array("id" => $id));	
				echo 1;
			}
		}
		exit;
	}
	
	function get_customer_invoices()
	{
		$data = array();
		$custid = $this->input->get("custid");
		$data['invoices'] = $this->invoice_model->get_customer_invoices($custid);
		 
		if(count($data['invoices'])>0 && !empty($data['invoices'])){
			//echo json_encode($invoices);
			$this->load->view("invoices/invoices_to_send",$data);
		}else{
			echo 'no_results';
			exit;
		}
	}
	
	function submit_invoice(){

		if($this->input->post("customer")){
			$customer_id = $this->input->post("customer");			
			$payment_options = implode(",",$this->input->post("payment_options"));
			$invoice_ids = $this->input->post("invoice_id");			
			$expiry_date = $this->input->post("expiry_date");
			$unique_link = uniqid();
			
			$currency_type = $this->input->post("currency_type");
			$created_date = date("Y-m-d H:i:s");
			
			$this->db->select("first_name,last_name");
			$cust = $this->db->get_where($this->cfg['dbpref']."customers",array("custid" => $customer_id));
			if($cust->num_rows()>0){
				
				$customer = $cust->row();
				$customer_name = $customer->first_name.' '.$customer->last_name;
			}
			
			$cust_email = $this->input->post("email_address");
			
			$ins_arr = array("cust_id" => $customer_id,
							"cust_email" => $cust_email,
							// "exp_id" => $invoice_ids, removed to allow multiple entries
							"unique_link" => $unique_link,
							"payment_options" => 2,
							"expiry_date" => date("Y-m-d",strtotime($expiry_date)),
							"status" => 0,
							"created_date" => $created_date);
			 
			
			$this->db->insert($this->cfg['dbpref'].'invoices',$ins_arr);
			$insert_id = $this->db->insert_id();
			
			 if(count($invoice_ids)>0){
				foreach($invoice_ids as $invoice_id){
					$sub_ins_arr = array("inv_id" => $insert_id,"exp_id" => $invoice_id,"created_on" => $created_date,"status"=> 0);
					$this->db->insert($this->cfg['dbpref'].'invoices_child',$sub_ins_arr);
				}
			} 
			
			if($insert_id){
				//email sent by email template
				$print_fancydate = date('l, jS F y h:iA', strtotime($created_date));
				$from		  	 = $this->userdata['email'];
				$arrayEmails   	 = $this->config->item('crm');
				$to				 = implode(',',$arrayEmails['account_emails']);
				$cc_email		 = implode(',',$arrayEmails['account_emails_cc']);
				$subject		 = 'Invoice Notification from enoahisolution';
				$param = array();
				
				$cont = '';
				$project_name = $this->input->post("project_name");
				$project_milestone_name = $this->input->post("project_milestone_name");
				$month_year = $this->input->post("month_year");
				$amount = $this->input->post("amount");
			 
				$link = base_url().'payment/dopay/'.$unique_link;
				//if(count($project_name)>0 && !empty($project_name)){
				//	for($i=0;$i<count($project_name);$i++){
				//		$cont .= '<tr><td>'.$project_name.'</td><td>'.$project_milestone_name.'</td><td>'.date("F Y",strtotime($month_year)).'</td><td>'.$sub_total.' '.$currency_type.'</td></tr>';
				//	}
				//}
				
				$param['email_data'] = array('print_fancydate'=>$print_fancydate,'customer_name'=>$customer_name,'content' => $cont,"link" => $link,"expiry_date" => $expiry_date);
				
				$attached_files = array();
			 
				
				$this->db->where_in("expectid",$invoice_ids);
				$f = $this->db->get($this->cfg['dbpref']."expected_payments_attachments");
				$files = $f->result();
				if(count($files)>0 && !empty($files)){
					foreach($files as $key => $f){
						$attached_files[$key]['file_name'] = $f->file_name;	
					}
				}
				
				$param['to_mail'] 		  = "ssubbiah@enoahisolution.com,mthiyagarajan@enoahisolution.com"; //$cust_email
				//$param['to_mail'] 		  = "paulwills2015@gmail.com";
				//$param['cc_mail'] 		  = $this->userdata['email'].','.$cc_email.','.$to;
				//$param['cc_mail'] 		  = $this->userdata['email'].','.$cc_email.','.$to;
				$param['from_email']	  = 'webmaster@enoahprojects.com';
				$param['from_email_name'] = 'Webmaster';
				$param['template_name']	  = "Send Customer Invoice";
				$param['subject'] 		  = $subject;
				$param['external_attach'] 		  = $attached_files;
				$param['job_id'] 		  = $pjtid;
				//echo '<pre>';print_r($param);exit;
				$this->email_template_model->sent_email($param);
				$this->session->set_userdata("success_message","Invoice sent successfully!");
				redirect("invoice/payment");
			}
		}
	}
	
	/*
	*@Get Current Financial year
	*@Method  calculateFiscalYearForDate
	*@eg-1 for current-date calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
	*@eg-2 for custom date calculateFiscalYearForDate("12/1/08","7/1","6/30");
	*/
	function calculateFiscalYearForDate($inputDate, $fyStart, $fyEnd) {
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
	
	function download_file($file_name)
	{
		$this->load->helper('download');
		$file_dir = FCPATH.'crm_data/invoices/'.$file_name;
		$data = file_get_contents($file_dir); // Read the file's contents
		$name = $file_name;
		force_download($name, $data); 
	}	
}