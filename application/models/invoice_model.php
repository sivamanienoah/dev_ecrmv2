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
	public function get_invoices() {
		$this->db->select('expm.expectid,expm.amount,expm.project_milestone_name,expm.invoice_generate_notify_date,expm.expected_date, l.lead_title,l.custid_fk,l.pjt_id,l.expect_worth_id,ew.expect_worth_name,c.first_name,c.last_name,c.company');
		$this->db->from($this->cfg['dbpref'].'expected_payments as expm');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = expm.jobid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
		$this->db->where('expm.invoice_status',1);
		$this->db->where('expm.received !=',1);
		$query  = $this->db->get();
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
	*@Get row record for dynamic table
	*@Method get_row
	*/
	public function get_currency_rate() {		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
	}
}

?>
