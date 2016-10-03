<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Reseller Model
 *
 * @class 		reseller_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Reseller_model extends crm_model {
    
	/*
	*@construct
	*@Reseller_model
	*/
    function __construct() {
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
	public function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }
	
	/*
	*@Get the projects details
	*@Method get_projects
	*/
	function get_projects() {
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
	

	/*
	*@Get the Currency details
	*@Method get_currencies
	*/
	function get_currencies() 
	{
	    $this->db->select('expect_worth_id, expect_worth_name');
	    $this->db->from($this->cfg['dbpref'] . 'expect_worth');
	    $query = $this->db->get();
	    return $query->result_array();
	}
}

?>
