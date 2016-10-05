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
		$this->db->select('a.userid, a.role_id, a.contract_manager, a.first_name, a.last_name, a.username, a.email, c.id, c.name');
		$this->db->from($this->cfg['dbpref']."users as a");
		$this->db->where('a.role_id', $this->reseller_role_id);
		if($id){
			$this->db->where('a.userid', $id);
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
}