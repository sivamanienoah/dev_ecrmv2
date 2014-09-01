<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Invoice Model
 *
 * @class 		invoice_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Invoice_model extends crm_model {
    
	/*
	*@construct
	*@Invoice_model
	*/
    function Invoice_model() {
       parent::__construct();
    }

	/*
	*@Get invoices
	*@Invoice_model
	*/
	public function get_invoices($filter = false) {
		$this->db->select('expm.expectid,expm.amount,expm.project_milestone_name,expm.invoice_generate_notify_date,expm.expected_date, l.lead_title,l.custid_fk,l.pjt_id,l.expect_worth_id,ew.expect_worth_name,c.first_name,c.last_name,c.company');
		$this->db->from($this->cfg['dbpref'].'expected_payments as expm');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = expm.jobid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
		$this->db->where('expm.invoice_status',1);
		$this->db->where('expm.received !=',1);
		
		if (!empty($filter['project']) && $filter['project']!='null') {
			$filter['project'] = explode(',',$filter['project']);
			$this->db->where_in('l.lead_id', $filter['project']);
		}
		if (!empty($filter['customer']) && $filter['customer']!='null') {
			$filter['customer'] = explode(',',$filter['customer']);
			$this->db->where_in('l.custid_fk', $filter['customer']);
		}
		if (!empty($filter['practice']) && $filter['practice']!='null') {
			$filter['practice'] = explode(',',$filter['practice']);
			$this->db->where_in('l.practice', $filter['practice']);
		}
		if(!empty($filter['from_date']) && empty($filter['to_date'])) {
			$this->db->where('DATE(expm.invoice_generate_notify_date) >=', date('Y-m-d', strtotime($filter['from_date'])));
		} else if(!empty($filter['from_date']) && !empty($filter['to_date'])) {
			$this->db->where('DATE(expm.invoice_generate_notify_date) >=', date('Y-m-d', strtotime($filter['from_date'])));
			$this->db->where('DATE(expm.invoice_generate_notify_date) <=', date('Y-m-d', strtotime($filter['to_date'])));
		} else {
			$from = date('Y-m-01');
			$end  = date('Y-m-t');
			$this->db->where('DATE(expm.invoice_generate_notify_date) >=', $from);
			$this->db->where('DATE(expm.invoice_generate_notify_date) <=', $end);
		}
		$query  = $this->db->get();
		// echo $this->db->last_query();
		$res 	= $query->result_array();
		return $res;
    }

	/*
	*@Get row record for dynamic table
	*@Method get_row
	*/
	public function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }
	
	/*
	*@Get currency rates
	*@Method get_currency_rate
	*/
	public function get_currency_rate() {		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
	}
	
	/*
	*@Get the projects details
	*@Method get_projects
	*/
	function get_projects() {
		$this->db->select('l.lead_id,l.lead_title,l.invoice_no,l.custid_fk');
		$this->db->from($this->cfg['dbpref'].'leads as l');
		$this->db->where("l.lead_id != 'null' AND l.lead_status IN ('4') AND l.pjt_status IN ('1','2','3','4') ");
		$this->db->order_by("l.lead_title");
		$query  = $this->db->get();
		$res 	= $query->result_array();
		return $res;
	}
	
	/*
	*@Get the customer details
	*@Method get_customers
	*/
	function get_customers() {
	    $this->db->select('custid, first_name, last_name, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers');
		$this->db->order_by("first_name");
	    $customers = $this->db->get();
	    $customers =  $customers->result_array();
	    return $customers;
	}
	
	/*
	*@Get the practices
	*@Method get_practices
	*/
	function get_practices() {
	    $this->db->select('id, practices');
	    $this->db->from($this->cfg['dbpref'] . 'practices');
		$this->db->where("status", 1);
		$this->db->order_by("practices");
	    $customers = $this->db->get();
	    $customers =  $customers->result_array();
	    return $customers;
	}
}

?>
