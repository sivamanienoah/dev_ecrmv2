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
		$this->load->model('welcome_model');
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
    public function index()
	{
        $data['page_heading'] = 'Invoices';
		
		$data['projects']  = $this->invoice_model->get_projects();
		$data['customers'] = $this->invoice_model->get_customers();
		$data['practices'] = $this->invoice_model->get_practices();
		$data['sales_divisions'] = $this->welcome_model->get_sales_divisions();
		
		$filter   = real_escape_array($this->input->post());
		$invoices = $this->invoice_model->get_invoices($filter);
		
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
		if($this->input->post("filter")!="")
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
}