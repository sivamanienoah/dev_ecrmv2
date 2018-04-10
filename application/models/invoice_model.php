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
	   $this->userdata = $this->session->userdata('logged_in_user');
    }

	/*
	*@Get invoices
	*@Invoice_model
	*/
	public function get_invoices($filter = false,$invoice) 
	{
		$job_ids = array();
		if($filter['from_date']=='0000-00-00 00:00:00'){
			$filter['from_date'] = '';
		}
		if($filter['to_date']=='0000-00-00 00:00:00'){
			$filter['to_date'] = '';
		}
		if($filter['month_year_from_date']=='0000-00-00 00:00:00'){
			$filter['month_year_from_date'] = '';
		}
		if($filter['month_year_to_date']=='0000-00-00 00:00:00'){
			$filter['month_year_to_date'] = '';
		}
		
		//Access control RESTIRCTION
		$role_not_in_arr = array(ROLE_ADMIN, ROLE_MGMT, ROLE_FINANCE);
		$restrict_job 	 = array();
		if(!in_array($this->userdata['role_id'], $role_not_in_arr)) {
			$job_ids = $this->get_projects($ret=true);
		}
		//Access control RESTIRCTION
		
		$this->db->select('expm.received, expm.expectid, expm.invoice_status, expm.amount, expm.project_milestone_name, expm.invoice_generate_notify_date, expm.expected_date, expm.month_year, l.lead_title, l.lead_id, l.custid_fk, l.pjt_id, l.expect_worth_id, ew.expect_worth_name, c.customer_name, cc.company, sd.base_currency, sd.division_name, p.practices');

		$this->db->from($this->cfg['dbpref'].'expected_payments as expm');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = expm.jobid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as sd', 'sd.div_id = l.division');
		$this->db->join($this->cfg['dbpref'].'practices as p', 'p.id = l.practice');
		
		if(!empty($job_ids) && count($job_ids)>0) {
			$this->db->where_in('expm.jobid_fk', $job_ids);
		}
		
		if($this->userdata['role_id'] == 14){
			$reseller_condn = '(l.belong_to = '.$this->userdata['userid'].' OR l.lead_assign = '.$this->userdata['userid'].' OR l.assigned_to ='.$this->userdata['userid'].')';
			$this->db->where($reseller_condn);
		}
		
		if($invoice){
			$this->db->where('expm.invoice_status',0);
		}else{
			$this->db->where('expm.invoice_status',1);
		}
		
		//$this->db->where('expm.received !=',1);
		
		if (!empty($filter['project']) && $filter['project']!='null') {
			$filter['project'] = explode(',',$filter['project']);
			$this->db->where_in('l.lead_id', $filter['project']);
		}
		if (!empty($filter['customer']) && $filter['customer']!='null') {
			$filter['customer'] = explode(',',$filter['customer']);
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		
		if (!empty($filter['divisions']) && $filter['divisions']!='null') {
			$filter['divisions'] = explode(',',$filter['divisions']);
			$this->db->where_in('l.division', $filter['divisions']);
		}
		if (!empty($filter['practice']) && $filter['practice']!='null') {
			$filter['practice'] = @explode(',',$filter['practice']);
			$this->db->where_in('l.practice', $filter['practice']);
		}
		
		if(!$invoice){
			if(!empty($filter['from_date']) && empty($filter['to_date'])) {
				$this->db->where('DATE(expm.invoice_generate_notify_date) >=', date('Y-m-d', strtotime($filter['from_date'])));
			} else if(!empty($filter['from_date']) && !empty($filter['to_date'])) {
				$this->db->where('DATE(expm.invoice_generate_notify_date) >=', date('Y-m-d', strtotime($filter['from_date'])));
				$this->db->where('DATE(expm.invoice_generate_notify_date) <=', date('Y-m-d', strtotime($filter['to_date'])));
			} else {
				if(!empty($filter['month_year_from_date']) && empty($filter['month_year_to_date'])) {
					$this->db->where('DATE(expm.month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
				} else if(!empty($filter['month_year_from_date']) && !empty($filter['month_year_to_date'])) {
					$this->db->where('DATE(expm.month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
					$this->db->where('DATE(expm.month_year) <=', date('Y-m-d', strtotime($filter['month_year_to_date'])));
				} else {
					$from = date('Y-m-01');
					$end  = date('Y-m-t');
					$this->db->where('DATE(expm.invoice_generate_notify_date) >=', $from);
					$this->db->where('DATE(expm.invoice_generate_notify_date) <=', $end);
				}
			}
		}else{
			if(!$filter['month_year_to_date']){
				$from = date('Y-m-01');
				$end  = date('Y-m-t');
			}else{
				$from = $filter['month_year_from_date'];
				$end = $filter['month_year_to_date'];
			}
			
			$this->db->where('DATE(expm.month_year) >=', date('Y-m-d', strtotime($from)));
			$this->db->where('DATE(expm.month_year) <=', date('Y-m-d', strtotime($end)));
		}
	
		
		if(!empty($filter['month_year_from_date']) && empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(expm.month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
		} else if(!empty($filter['month_year_from_date']) && !empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(expm.month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
			$this->db->where('DATE(expm.month_year) <=', date('Y-m-d', strtotime($filter['month_year_to_date'])));
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
	function get_projects($ret_id=false) 
	{
		// echo $this->userdata['userid'] . " - ".LVL_GLOBAL_ACCESS; die;
		$result_ids = array();
		$role_not_in_arr = array(ROLE_ADMIN, ROLE_MGMT, ROLE_FINANCE);
		if(!in_array($this->userdata['role_id'], $role_not_in_arr))
		{
			//Fetching Project Team Members.
			/* $this->db->select('jobid_fk as lead_id');
			$this->db->where('userid_fk', $this->userdata['userid']);
			$rowscj = $this->db->get($this->cfg['dbpref'] . 'contract_jobs');
			$data['jobids'] = $rowscj->result_array(); */
			
			//Fetching Project Manager, Lead Assigned to & Lead owner jobids.
			$this->db->select('lead_id');
			$this->db->where("(assigned_to = '".$this->userdata['userid']."' OR lead_assign = '".$this->userdata['userid']."' OR belong_to = '".$this->userdata['userid']."')");
			$this->db->where("lead_status", 4);
			if (empty($stage) && $stage=='null') {
				$this->db->where("pjt_status", 1);
			}
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'leads');
			$data['jobids1'] = $rowsJobs->result_array();

			//Fetching Stake Holders.
			$data['jobids2'] = array();
			$this->db->select('lead_id');
			$this->db->where("user_id", $this->userdata['userid']);
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'stake_holders');
			if($rowsJobs->num_rows()>0)	$data['jobids2'] = $rowsJobs->result_array();			
			
			// $data = array_merge_recursive($data['jobids'], $data['jobids1'], $data['jobids2']);
			$data = array_merge_recursive($data['jobids1'], $data['jobids2']);
 
			$res[] = 0;
			if (is_array($data) && count($data) > 0) { 
				foreach ($data as $data) {
					$res[] = $data['lead_id'];
				}
			}
			$result_ids = array_unique($res);
		}
		
		if($ret_id==true) {
			return $result_ids;
		}
		
		$this->db->select('l.lead_id,l.lead_title,l.invoice_no,l.custid_fk');
		$this->db->from($this->cfg['dbpref'].'leads as l');
		$this->db->where("l.lead_id != 'null' AND l.lead_status IN ('4') AND l.pjt_status IN ('1','2','3','4') ");
		if($this->userdata['role_id'] == ROLE_RESELLER) { /*Condition for Reseller user*/
			$reseller_condn = '(l.belong_to = '.$this->userdata['userid'].' OR l.lead_assign = '.$this->userdata['userid'].' OR l.assigned_to ='.$this->userdata['userid'].')';
			$this->db->where($reseller_condn);
		}
		if(is_array($result_ids) && !empty($result_ids) && count($result_ids)>IS_ZERO) {
			$this->db->where_in('l.lead_id', $result_ids);
		}
		$this->db->order_by("l.lead_title");
		$query  = $this->db->get();
		// echo $this->db->last_query();
		$res 	= $query->result_array();
		return $res;
	}
	
	/*
	*@Get the customer details
	*@Method get_customers
	*/
	function get_customers() {
	    $this->db->select('companyid, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers_company');
		if ($this->userdata['role_id']==14) {
			$this->db->where('created_by', $this->userdata['userid']);
		}
		$this->db->order_by("company");
	    $customers = $this->db->get();
	    $customers = $customers->result_array();
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
	
	/*
	*@method get_sales_divisions
	*/
	function get_sales_divisions() 
	{
    	$this->db->select('div_id, division_name');
		$this->db->where('status', 1);
    	$this->db->order_by('div_id');
		$q = $this->db->get($this->cfg['dbpref'] . 'sales_divisions');
		return $q->result_array();
    }
	
	/*
	*@method get_saved_search
	*/
	function get_saved_search($user_id, $search_for) 
	{
    	$this->db->select('search_id,search_name,is_default');
		$this->db->where('user_id', $user_id);
		$this->db->where('search_for', $search_for);
    	$this->db->order_by('search_id');
		$sql = $this->db->get($this->cfg['dbpref'] . 'saved_search_critriea');
		return $sql->result_array();
    }
	
	/*
	*@method insert_row_return_id
	*/
	function insert_row_return_id($tbl, $ins) {
		$this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
		return $this->db->insert_id();
    }

	/*
	*@method get_data_by_id
	*/
	function get_data_by_id($table, $wh_condn) {
		$this->db->where($wh_condn);
		$user = $this->db->get($this->cfg['dbpref'] . $table);
		// echo $this->db->last_query(); exit;
		return $user->row_array();
	}

	/*
	*@method update_records
	*/
	function update_records($tbl, $wh_condn, $not_wh_condn, $up_arg) {
    	$this->db->where($wh_condn);
		if(!empty($not_wh_condn) && $not_wh_condn != '') {
			foreach($not_wh_condn as $key=>$value) {
				$this->db->where($key.'!=',$value);
			}
		}
		return $this->db->update($this->cfg['dbpref'] . $tbl, $up_arg);
    }

	/*
	*@method delete_records
	*/
	function delete_records($tbl, $condn) 
	{
		$this->db->where($condn);
        $this->db->delete($this->cfg['dbpref'].$tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
	
	function get_invoice_customer(){
		$qry = "select cus.company,cus.custid,cus.first_name,cus.last_name from ".$this->cfg['dbpref']."customers as cus join ".$this->cfg['dbpref']."leads as le on cus.custid=le.custid_fk join ".$this->cfg['dbpref']."expected_payments as exp on exp.jobid_fk = le.lead_id where exp.invoice_status = 1 group by cus.custid order by cus.first_name ";
		$res = $this->db->query($qry);
		return $res->result();
	}
	
	function get_invoice_customer_usentity(){
		$qry = "select cus.company,cus.custid,cus.first_name,cus.last_name from ".$this->cfg['dbpref']."customers as cus join ".$this->cfg['dbpref']."leads as le on cus.custid=le.custid_fk join ".$this->cfg['dbpref']."expected_payments as exp on exp.jobid_fk = le.lead_id where exp.invoice_status = 1 and le.division=3 group by cus.custid order by cus.first_name ";
		$res = $this->db->query($qry);
		return $res->result();
	}	
	
	function get_customer_invoices($custid){
		$qry = "select exp.total_amount,expatt.file_name,cus.first_name,cus.last_name,cus.email_1,cus.email_2,cus.email_3,cus.email_4,cus.custid,le.lead_title,DATE_FORMAT(exp.month_year,'%d-%m-%Y') as month_year,DATE_FORMAT(exp.expected_date,'%d-%m-%Y') as milestone_date,exp.expectid,exp.amount,exp.project_milestone_name,exw.expect_worth_name from ".$this->cfg['dbpref']."customers as cus join ".$this->cfg['dbpref']."leads as le on cus.custid=le.custid_fk join ".$this->cfg['dbpref']."expected_payments as exp on exp.jobid_fk = le.lead_id join ".$this->cfg['dbpref']."expect_worth as exw on exw.expect_worth_id=le.expect_worth_id join ".$this->cfg['dbpref']."expected_payments_attachments as expatt on exp.expectid = expatt.expectid where exp.invoice_status = 1 and exp.received != '1' and cus.custid=$custid group by exp.expectid order by exp.month_year desc,le.lead_title ";
		
		$res = $this->db->query($qry);
		//echo $this->db->last_query();exit;
		return $res->result();		
	}
	
	function get_payment_invoice_list()
	{
		$this->db->select("cus.customer_name, cc.company, inv.inv_id as invoice_id, inv.*,payhis.*");
		$this->db->from($this->cfg['dbpref']."invoices as inv");
		$this->db->join($this->cfg['dbpref']."customers as cus","cus.custid=inv.cust_id");
		$this->db->join($this->cfg['dbpref']."customers_company as cc","cc.customerid=cus.company_id");
		$this->db->join($this->cfg['dbpref']."payment_history as payhis","payhis.inv_id=inv.inv_id","left");
		$qry = $this->db->get();
		return $qry->result();
		
	}
	
	function get_payment_options(){
		$qry = $this->db->get_where($this->cfg['dbpref'].'payment_options',array("ptype_status" => 1));
		return $qry->result();
	}

	function get_currencies() {
	    $this->db->select('expect_worth_id, expect_worth_name');
	    $this->db->from($this->cfg['dbpref'] . 'expect_worth');
	    $query = $this->db->get();
	    return $query->result_array();
	}
}

?>
