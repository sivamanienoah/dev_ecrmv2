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
		
		$data['projects']  = $this->invoice_model->get_projects();
		$data['customers'] = $this->invoice_model->get_customers();
		$data['practices'] = $this->invoice_model->get_practices();
		$data['sales_divisions'] = $this->invoice_model->get_sales_divisions();
		$data['saved_search'] = $this->invoice_model->get_saved_search($this->userdata['userid'], $search_for=3);
		
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
		if (count($filter)>0) {
			
			$project   = $filter['project'];
			$customer  = $filter['customer'];
			$divisions = $filter['divisions'];
			$practice  = $filter['practice'];
			$from_date = $filter['from_date'];
			$to_date   = $filter['to_date'];
			$month_year_from_date = $filter['month_year_from_date'];
			$month_year_to_date   = $filter['month_year_to_date'];
			
			$inv_excel_arr 	  = array();
			foreach ($filter as $key => $val) {
				$inv_excel_arr[$key] = $val;
			}
			// print_r($excel_arr); exit;
			$this->session->set_userdata(array("inv_excel_export"=>$inv_excel_arr));
		} else {
			$this->session->unset_userdata(array("inv_excel_export"=>''));
		}
		// echo "<pre>"; print_r($this->session->userdata('inv_excel_export')); exit;
		$invoices = $this->invoice_model->get_invoices($filter);
		// echo $this->db->last_query();
		$rates 	  = $this->get_currency_rates();
		$data['default_currency'] = $this->default_cur_name;
		$data['invoices'] = array();
		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoices)>0) {
			foreach ($invoices as $inv) {
				$data['invoices'][$i]['lead_title']			    = $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 				= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 				= $inv['lead_id'];
				$data['invoices'][$i]['customer'] 			    = $inv['first_name'].' '.$inv['last_name'].' - '.$inv['company'];
				$data['invoices'][$i]['project_milestone_name'] = $inv['project_milestone_name'];
				$data['invoices'][$i]['actual_amt'] 			= $inv['expect_worth_name']." ".$inv['amount'];
				$data['invoices'][$i]['coverted_amt']		    = $this->conver_currency($inv['amount'], $rates[$inv['expect_worth_id']][$this->default_cur_id]);
				$data['invoices'][$i]['invoice_generate_notify_date'] = $inv['invoice_generate_notify_date'];
				$data['invoices'][$i]['month_year'] 			= $inv['month_year'];
				$data['total_amt'] 	                           += $data['invoices'][$i]['coverted_amt'];
				$i++;
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
		return round($amount*$val);
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
			if($ins['is_default'] == 1) {
				$updt['is_default'] = 0;
				$this->db->where('search_id != ', $last_ins_id);
				$this->db->where('user_id', $this->userdata['userid']);
				$this->db->where('search_for', $type);
				$this->db->update($this->cfg['dbpref'] . 'saved_search_critriea', $updt);
			}
			
			$saved_search = $this->invoice_model->get_saved_search($this->userdata['userid'], $search_for=$type);
			
			$result['res'] = true;
			$result['msg'] = 'Search Criteria Saved.';
			
			$result['search_div'] .= '<li id="item_'.$last_ins_id.'" class="saved-search-res"><span><a href="javascript:void(0)" onclick="show_search_results('.$last_ins_id.')">'.$post_data['search_name'].'</a></span>';
			$result['search_div'] .= '<span class="rd-set-default">';
			$result['search_div'] .= '<input type="radio" name="set_default_search" class="set_default_search" value="'.$last_ins_id.'" ';
			if($searc['is_default']==1) { 
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
	
		if((!empty($filter['project'])) && $filter['project']!='null')
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
		$filter['divisions'] = '';
		
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

		$invoices_res = $this->invoice_model->get_invoices($filter);
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
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $this->conver_currency($excelarr['amount'], $rates[$excelarr['expect_worth_id']][$this->default_cur_id]));
				
				$amt 	   = $this->conver_currency($excelarr['amount'], $rates[$excelarr['expect_worth_id']][$this->default_cur_id]);
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
	
}