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
	public function get_invoices($filter = false) {

		$job_ids = array();
	
		//LEVEL BASED RESTIRCTION
		if( $this->userdata['level'] != 1 ) {
			if (isset($this->session->userdata['region_id']))
			$region = explode(',',$this->session->userdata['region_id']);
			if (isset($this->session->userdata['countryid']))
			$countryid = explode(',',$this->session->userdata['countryid']);
			if (isset($this->session->userdata['stateid']))
			$stateid = explode(',',$this->session->userdata['stateid']);
			if (isset($this->session->userdata['locationid']))
			$locationid = explode(',',$this->session->userdata['locationid']);
			
			$this->db->select('ls.lead_id');
			$this->db->from($this->cfg['dbpref'].'leads as ls');
			$this->db->join($this->cfg['dbpref'].'customers as cs', 'cs.custid  = ls.custid_fk');
			
			switch($this->userdata['level']) {
				case 2:
					$this->db->where_in('cs.add1_region',$region);
				break;
				case 3:
					$this->db->where_in('cs.add1_region',$region);
					$this->db->where_in('cs.add1_country',$countryid);
				break;
				case 4:
					$this->db->where_in('cs.add1_region',$region);
					$this->db->where_in('cs.add1_country',$countryid);
					$this->db->where_in('cs.add1_state',$stateid);
				break;
				case 5:
					$this->db->where_in('cs.add1_region',$region);
					$this->db->where_in('cs.add1_country',$countryid);
					$this->db->where_in('cs.add1_state',$stateid);
					$this->db->where_in('cs.add1_location',$locationid);
				break;
			}
			// $this->db->where("ls.lead_status", 4); //for active projects only
			// $this->db->where("ls.pjt_status", 1); //for active projects only
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			$rowscust1 = $query->result_array();
			
			$this->db->select('ld.lead_id');
			$this->db->from($this->cfg['dbpref'].'leads as ld');
			$this->db->where("(ld.assigned_to = '".$this->userdata['userid']."' OR ld.lead_assign = '".$this->userdata['userid']."' OR ld.belong_to = '".$this->userdata['userid']."')");
			$this->db->where("ld.lead_status", 4);
			$this->db->where("ld.pjt_status", 1);
			$query1 = $this->db->get();
			// echo $this->db->last_query(); exit;
			$rowscust2 = $query1->result_array();
			
			$customers = array_merge_recursive($rowscust1, $rowscust2);
			
			$res[] = 0;
			if (is_array($customers) && count($customers) > 0) { 
				foreach ($customers as $cus) {
					$res[] = $cus['lead_id'];
				}
			}
			$job_ids = array_unique($res);
			
		}
		//LEVEL BASED RESTIRCTION
		
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
	
		$this->db->select('expm.expectid,expm.amount,expm.project_milestone_name,expm.invoice_generate_notify_date,expm.expected_date,expm.month_year, l.lead_title,l.lead_id,l.custid_fk,l.pjt_id,l.expect_worth_id,ew.expect_worth_name,c.first_name,c.last_name,c.company');
		$this->db->from($this->cfg['dbpref'].'expected_payments as expm');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = expm.jobid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
		$this->db->where('expm.invoice_status',1);
		$this->db->where('expm.received !=',1);
		
		if(!empty($job_ids) && count($job_ids)>0) {
			$this->db->where_in('expm.jobid_fk', $job_ids);
		}
		
		if (!empty($filter['project']) && $filter['project']!='null') {
			$filter['project'] = explode(',',$filter['project']);
			$this->db->where_in('l.lead_id', $filter['project']);
		}
		if (!empty($filter['customer']) && $filter['customer']!='null') {
			$filter['customer'] = explode(',',$filter['customer']);
			$this->db->where_in('l.custid_fk', $filter['customer']);
		}
		
		if (!empty($filter['divisions']) && $filter['divisions']!='null') {
			$filter['divisions'] = explode(',',$filter['divisions']);
			$this->db->where_in('l.division', $filter['divisions']);
		}
		if (!empty($filter['practice']) && $filter['practice']!='null') {
			$filter['practice'] = @explode(',',$filter['practice']);
			$this->db->where_in('l.practice', $filter['practice']);
		}
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
}

?>
