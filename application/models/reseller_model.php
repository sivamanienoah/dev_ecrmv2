<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Reseller Model
 *
 * @class 		reseller_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Reseller_model extends crm_model 
{
    
	/*
	*@construct
	*@Reseller_model
	*/
    function __construct() 
	{
       parent::__construct();
	   $this->userdata = $this->session->userdata('logged_in_user');
    }

	/*
	*@Get Reseller User list
	*@Method  user_list
	*/
    public function get_reseller($id = false) 
	{
		$this->db->select('a.userid, a.role_id, a.contract_manager, a.first_name, a.last_name, a.username, a.email, a.phone, a.mobile, c.id, c.name');
		$this->db->from($this->cfg['dbpref']."users as a");
		$this->db->where('a.role_id', $this->reseller_role_id);
		if($id) {
			$this->db->where('a.userid', $id);
		}
		if($this->userdata['role_id'] == $this->reseller_role_id) {
			$this->db->where('a.userid', $this->userdata['userid']);
		}
		$this->db->join($this->cfg['dbpref'].'roles as c', 'c.id = a.role_id', 'left');
		$this->db->order_by("a.first_name", "asc");
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$reseller = $query->result_array();	
        return $reseller;
    }

	/*
	*@Get row record for dynamic table
	*@Method get_row
	*/
	public function get_row($table, $cond) 
	{
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }
	
	/*
	*@Get the projects details
	*@Method get_projects
	*/
	function get_projects() 
	{
		$this->db->select('l.lead_id,l.lead_title,l.invoice_no,l.custid_fk');
		$this->db->from($this->cfg['dbpref'].'leads as l');
		$this->db->where("l.lead_id != 'null' AND l.lead_status IN ('4') AND l.pjt_status IN ('1','2','3','4') ");
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(l.belong_to = '.$this->userdata['userid'].' OR l.lead_assign = '.$this->userdata['userid'].' OR l.assigned_to ='.$this->userdata['userid'].')';
			$this->db->where($reseller_condn);
		}
		$this->db->order_by("l.lead_title");
		$query  = $this->db->get();
		$res 	= $query->result_array();
		return $res;
	}
	
	/*
	*@Get the customer details
	*@Method get_customers
	*/
	function get_customers() 
	{
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
	*@method insert_row_return_id
	*/
	function insert_row_return_id($tbl, $ins) 
	{
		$this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
		return $this->db->insert_id();
    }

	/*
	*@method get_data_by_id
	*/
	function get_data_by_id($table, $wh_condn) 
	{
		$this->db->where($wh_condn);
		$user = $this->db->get($this->cfg['dbpref'] . $table);
		// echo $this->db->last_query(); exit;
		return $user->row_array();
	}

	/*
	*@method update_records
	*/
	function update_records($tbl, $wh_condn, $not_wh_condn, $up_arg) 
	{
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
	

	/*
	*@Get the Currency details
	*@Method get_currencies
	*/
	public function get_records($tbl, $wh_condn='', $order='') 
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		if(!empty($wh_condn))
		$this->db->where($wh_condn);
		if(!empty($order)) {
			foreach($order as $key=>$value) {
				$this->db->order_by($key,$value);
			}
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->result_array();
    }

	/*
	*@Get the Currency details
	*@Method get_currencies
	*/
	public function getLeads($userid) 
	{
		$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_service, j.lead_source, j.lead_stage, j.date_created, j.date_modified, j.belong_to, j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.pjt_status, j.lead_assign, j.proposal_expected_date, j.division, j.industry,
		c.customer_name, cc.company, c.email_1, c.phone_1, c.position_title, c.skype_name, rg.region_name, co.country_name, st.state_name, locn.location_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
		us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref']. 'leads as j');
		$this->db->where('j.lead_id != "null" AND j.lead_stage IN ("'.$this->stages.'")');
		$this->db->where('j.pjt_status', 0);
		$this->db->where('j.lead_status', 1);
		$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
		$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
		$this->db->join($this->cfg['dbpref'] . 'users as ub', 'ub.userid = j.belong_to');
		$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
		$this->db->join($this->cfg['dbpref'] . 'country as co', 'co.countryid = cc.add1_country');
		$this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
		$this->db->join($this->cfg['dbpref'] . 'location as locn', 'locn.locationid = cc.add1_location');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage', 'LEFT');
		$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
		$reseller_condn = '(j.belong_to = '.$userid.' OR j.lead_assign = '.$userid.' OR j.assigned_to ='.$userid.')';
		$this->db->where($reseller_condn);
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
    }
	
	/*
	*@Get the Currency details
	*@Method getAllLeads
	*/
	public function getResellerLeads($userid, $filter_type) 
	{
		$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_service, j.lead_source, j.lead_stage, j.date_created, j.date_modified, j.belong_to, j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.pjt_status, j.lead_assign, j.proposal_expected_date, j.division, j.industry,
		c.customer_name, cc.company, c.email_1, c.phone_1, c.position_title, c.skype_name, rg.region_name, co.country_name, st.state_name, locn.location_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
		us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref']. 'leads as j');
		$this->db->where('j.lead_id != "null" AND j.lead_stage IN ("'.$this->stages.'")');
		$this->db->where('j.pjt_status', 0);
		if($filter_type=='all'){
			$this->db->where_in('j.lead_status', array(1,2,3,4));
		} else if ($filter_type=='active') {
			$this->db->where_in('j.lead_status', array(1));
		} else {
			$this->db->where_in('j.lead_status', array(1));
		}
		$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
		$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
		$this->db->join($this->cfg['dbpref'] . 'users as ub', 'ub.userid = j.belong_to');
		$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
		$this->db->join($this->cfg['dbpref'] . 'country as co', 'co.countryid = cc.add1_country');
		$this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
		$this->db->join($this->cfg['dbpref'] . 'location as locn', 'locn.locationid = cc.add1_location');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage', 'LEFT');
		$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
		$reseller_condn = '(j.belong_to = '.$userid.' OR j.lead_assign = '.$userid.' OR j.assigned_to ='.$userid.')';
		$this->db->where($reseller_condn);
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
    }
	
	/*
	*@Get Reseller User list
	*@Method  user_list
	*/
    public function get_contracts_details($id = false)
	{
		$this->db->select('c.id, c.contract_title, c.contracter_id, c.contract_manager, c.contract_start_date, c.contract_end_date, c.renewal_reminder_date, c.description, c.contract_signed_date, c.contract_status, c.currency, c.tax, u.first_name, u.last_name');
		$this->db->from($this->cfg['dbpref']."contracts as c");
		if($id) {
			$this->db->where('c.contracter_id', $id);
		}
		$this->db->join($this->cfg['dbpref']."users as u", 'u.userid = c.contract_manager', 'left');
		$this->db->order_by("c.contract_start_date", "desc");
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$reseller = $query->result_array();	
        return $reseller;
    }

	/*
	*@Get Commission Details
	*@Method get_commission_details
	*@return array
	*/
    public function get_commission_details($id = false) 
	{
		$this->db->select('c.id, c.contracter_id, c.commission_title, c.job_id, c.payment_advice_date, c.commission_milestone_name, c.for_the_month_year, c.commission_currency, c.commission_value, c.remarks, j.lead_title');
		$this->db->from($this->cfg['dbpref']."commission_history as c");
		if($id) {
			$this->db->where('c.contracter_id', $id);
		}
		$this->db->join($this->cfg['dbpref']."leads as j", 'j.lead_id = c.job_id', 'left');
		$this->db->order_by("c.id", "desc");
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$reseller = $query->result_array();	
        return $reseller;
    }

	/*
	*@Get Reseller User list
	*@Method  user_list
	*/
    public function getUploadsFile($id = false)
	{
		if($id) {
			$this->db->select('cu.id, cu.file_name');
			$this->db->from($this->cfg['dbpref']."contracts_uploads_mapping as cum");
			$this->db->where('cum.contract_id', $id);
			$this->db->join($this->cfg['dbpref']."contracts_uploads as cu", 'cu.id = cum.contract_file_upload_id', 'left');
			$this->db->order_by("cu.id", "asc");
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			$reseller = $query->result_array();	
			return $reseller;
		} else {
			return false;
		}
    }

	/*
	*@Get map Uploaded Files
	*@Method mapUploadedFiles
	*/
    public function mapUploadedFiles($uploaded_files, $contract_id) 
	{
		$ins_map_val    			= array();
		$ins_map_val['contract_id'] = $contract_id;
		if(is_array($uploaded_files) && !empty($uploaded_files) && count($uploaded_files)>0) {
			/**insert into contract upload mapping table**/
			foreach($uploaded_files as $row_file_id) {
				$ins_map_val['contract_file_upload_id'] = base64_decode($row_file_id);
				$this->db->insert($this->cfg['dbpref'] . 'contracts_uploads_mapping', $ins_map_val);
			}
		}
		return true;
    }
	
	/*
	*@Get logs
	*@Method  getLogs
	*@Params reseller user id
	*@return array
	*/
	function getLogs($userid)
	{
		$this->db->select('lg.*, j.lead_title, CONCAT(u.first_name," ",u.last_name) as log_user', false);
		$this->db->from($this->cfg['dbpref']."logs as lg");
		$this->db->where('lg.userid_fk', $userid);
		$this->db->join($this->cfg['dbpref']."leads as j", 'j.lead_id = lg.jobid_fk', 'left');
		$this->db->join($this->cfg['dbpref']."users as u", 'u.userid = lg.userid_fk', 'left');
		$this->db->order_by("lg.date_created", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/*
	*@Get customer contact list
	*@Method customer_contact_list
	*@Params reseller user id
	*@return array
	*/
	function customer_contact_list($userid)
	{		
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref']."customers AS c");
		$this->db->join($this->cfg['dbpref'].'customers_company AS cc', 'cc.companyid = c.company_id');
		if(!empty($userid)) {
			$this->db->where('c.created_by', $userid);
		}
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	public function get_active_contract($tbl, $wh_condn='', $order='', $limit='') 
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		if(!empty($wh_condn))
		$this->db->where($wh_condn);
		if(!empty($order)) {
			foreach($order as $key=>$value) {
				$this->db->order_by($key,$value);
			}
		}
		if(!empty($limit)) {
			$this->db->limit($limit);
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->row_array();
    }
	
	/*
	*@Get the reseller closed jobs
	*@Method get_closed_jobs
	*/
	function get_closed_jobs($reseller_id) 
	{
		$this->db->select('l.lead_id,l.lead_title,l.invoice_no,l.custid_fk');
		$this->db->from($this->cfg['dbpref'].'leads as l');
		$this->db->where("l.lead_id != 'null' AND l.lead_status IN ('4') AND l.pjt_status IN ('0','1','2','3','4') ");
		$reseller_condn = '(l.belong_to = '.$reseller_id.' OR l.lead_assign = '.$reseller_id.' OR l.assigned_to ='.$reseller_id.')';
		$this->db->where($reseller_condn);
		$this->db->order_by("l.lead_title");
		$query  = $this->db->get();
		$res 	= $query->result_array();
		return $res;
	}
	
	/*
	*@Get mapping Commission Uploaded Files
	*@Method mapCommissionUploadedFiles
	*/
    public function mapCommissionUploadedFiles($uploaded_files, $commission_id) 
	{
		$ins_map_val    				= array();
		$ins_map_val['commission_id'] 	= $commission_id;
		if(is_array($uploaded_files) && !empty($uploaded_files) && count($uploaded_files)>0) {
			/**insert into contract upload mapping table**/
			foreach($uploaded_files as $row_file_id) {
				$ins_map_val['commission_file_upload_id'] = base64_decode($row_file_id);
				$this->db->insert($this->cfg['dbpref'] . 'commission_uploads_mapping', $ins_map_val);
			}
		}
		return true;
    }
	
	/*
	*@Get get Commission Uploads File
	*@Method getCommissionUploadsFile
	*/
    public function getCommissionUploadsFile($id = false) 
	{
		if($id) {
			$this->db->select('cu.id, cu.file_name');
			$this->db->from($this->cfg['dbpref']."commission_uploads_mapping as cmsn");
			$this->db->where('cmsn.commission_id', $id);
			$this->db->join($this->cfg['dbpref']."commission_uploads as cu", 'cu.id = cmsn.commission_file_upload_id', 'left');
			$this->db->order_by("cu.id", "asc");
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			$reseller = $query->result_array();	
			return $reseller;
		} else {
			return false;
		}
    }
	
	
	//for closed opportunities getClosedJobids
	public function getClosedJobids($reseller_id)
	{
		$pjt_stat = array(0,1,2,3);
		
		// my fiscal year starts on July,1 and ends on June 30, so... $curYear = date("Y"); eg. calculateFiscalYearForDate("5/15/08","7/1","6/30"); m/d/y
		// $curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		// $frm_dt = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $to_dt = $curFiscalYear."-03-31"; //eg.2014-03-01
		$this->db->select('j.lead_id, j.lead_title, j.expect_worth_id, j.actual_worth_amount, j.lead_status, j.pjt_status, c.customer_name AS customer_contact_name, cc.company AS company_name');
		$this->db->from($this->cfg['dbpref'].'leads as j');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->where('lead_status', 4);
		$reseller_condn = '(j.belong_to = '.$reseller_id.' OR j.lead_assign = '.$reseller_id.' OR j.assigned_to ='.$reseller_id.')';
		$this->db->where($reseller_condn);
		
   		$this->db->where_in('j.pjt_status', $pjt_stat);
   		// $this->db->where('j.date_modified BETWEEN "'.$frm_dt.'" AND "'.$to_dt.'" ');
		$this->db->order_by('j.lead_id', 'DESC');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
	}
	
	function getLeadClosedDate($id, $curFiscalYear)
	{
		// my fiscal year starts on July,1 and ends on June 30, so... $curYear = date("Y");
		// eg. calculateFiscalYearForDate("5/15/08","7/1","6/30"); m/d/y
		// $curFiscalYear 	= getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$frm_dt 		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$to_dt  		= $curFiscalYear."-03-31"; //eg.2014-03-01
		
	    $this->db->select('lead_id, dateofchange, modified_by, CONCAT(u.first_name," ",u.last_name) as sale_by', false);
	    $this->db->from($this->cfg['dbpref'].'lead_status_history');
		$this->db->join($this->cfg['dbpref'].'users as u', 'u.userid = modified_by');
		$this->db->where("lead_id", $id);
		$this->db->where("changed_status", 4);
		$this->db->where('dateofchange BETWEEN "'.$frm_dt.'" AND "'.$to_dt.'" ');
		$this->db->order_by('dateofchange', 'desc');
		$this->db->limit(1);
	    $sql = $this->db->get();
	    return $sql->row_array();
	}
	
	function getLeadClosedDateYear($id)
	{	
	    $this->db->select('lead_id, dateofchange, modified_by, CONCAT(u.first_name," ",u.last_name) as sale_by', false);
	    $this->db->from($this->cfg['dbpref'].'lead_status_history');
		$this->db->join($this->cfg['dbpref'].'users as u', 'u.userid = modified_by');
		$this->db->where("lead_id", $id);
		$this->db->where("changed_status", 4);
		$this->db->order_by('dateofchange', 'desc');
		$this->db->limit(1);
	    $sql = $this->db->get();
	    return $sql->row_array();
	}
}