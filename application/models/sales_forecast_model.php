<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales Forecast Model
 *
 * @class 		sales_forecast_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Sales_forecast_model extends crm_model {
    
	/*
	*@construct
	*@Sales Forecast Model
	*/
    public function __construct() {
       parent::__construct();
	   $this->load->helper('custom_helper');
	   $this->userdata = $this->session->userdata('logged_in_user');
    }

	/*
	*@Get Sale Records
	*@Sales Forecast Model
	*/
	public function get_sale_records($filter = false) {
		
		$this->db->select('sfc.forecast_id, c.company, l.lead_title, l.expect_worth_amount, enti.division_name');
		$this->db->from($this->cfg['dbpref'].'sales_forecast as sfc');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfc.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		
		if (!empty($filter['entity']) && $filter['entity']!='null') {
			$filter['entity'] = explode(',',$filter['entity']);
			$this->db->where_in('sfc.entity', $filter['entity']);
		}
		if (!empty($filter['customer']) && $filter['customer']!='null') {
			$filter['customer'] = explode(',',$filter['customer']);
			$this->db->where_in('sfc.customer_name', $filter['customer']);
		}
		if (!empty($filter['lead_names']) && $filter['lead_names']!='null') {
			$filter['lead_names'] = explode(',',$filter['lead_names']);
			$this->db->where_in('sfc.lead_name', $filter['lead_names']);
		}
		if(!empty($filter['month_year_from_date']) && empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(sfc.for_month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
		} else if(!empty($filter['month_year_from_date']) && !empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(sfc.for_month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
			$this->db->where('DATE(sfc.for_month_year) <=', date('Y-m-d', strtotime($filter['month_year_to_date'])));
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
    }
	
	####### get single row ########
	function get_record($select,$table,$where='')
	{
		$this->db->select($select);
		if($where){
			$this->db->where($where);
		}
		$query = $this->db->get($this->cfg['dbpref'].$table,1);
 		return $query->row_array();
	}
	
	/*
	*@Get records for Search
	*@Sales Forecast Model
	*/
	public function get_records($tbl, $wh_condn='', $order='') {
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
	*@Update Exist Currency
	*@Method  insert_new_currency
	*@table   expect_worth
	*/
	public function updt_exist_currency($updt, $id) {
		$res = $this->db->update($this->cfg['dbpref'].'expect_worth', $updt, "expect_worth_id = ".$id." ");
		if (array_key_exists('is_default', $updt)) {
			$data[is_default] = 0;
			$cur_conver['to'] = $id;
			$this->db->update($this->cfg['dbpref'].'expect_worth', $data, "expect_worth_id != ".$id." ");
			$this->db->truncate($this->cfg['dbpref'].'currency_rate');
		}
		currency_convert();
		return $res;
    }

	/*
	*@Get row record for dynamic table
	*@Method  get_row
	*/
	public function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }

	/*
	*@Get row count for dynamic table
	*@Method  get_num_row
	*/
    public function get_num_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->num_rows();
    }

	/*
	*@Update Row for dynamic table
	*@Method  update_row
	*/
    public function update_row($table, $cond, $data) {
    	$this->db->where($cond);
		return $this->db->update($this->cfg['dbpref'].$table, $data);
    }

	/*
	*@Insert Row for dynamic table
	*@Method  insert_row
	*/
	public function insert_row($table, $param) {
    	return $this->db->insert($this->cfg['dbpref'].$table, $param);
    }
	
	/*
	*@Insert Row for dynamic table
	*@Method  insert_row_return_id
	*/
	function insert_row_return_id($table, $param) {
	
	    if ( $this->db->insert($this->cfg['dbpref'].$table, $param) ) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return false;
        }
    }

	/*
	*@Delete Row for dynamic table
	*@Method  delete_row
	*/
	public function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }
	
	/*
	*Checking duplicates
	*/
	function check_duplicate($tbl_cont, $condn, $tbl_name) {
		$this->db->select($tbl_cont['name']);
		$this->db->where($tbl_cont['name'], $condn['name']);
		if(!empty($condn['id'])) {
			$this->db->where($tbl_cont['id'].' !=', $condn['id']);
		}
		$res = $this->db->get($this->cfg['dbpref'].$tbl_name);
        return $res->num_rows();
	}
	
	/*
	*@Get Row for Customers
	*@Method  get_customers
	*/
	function get_customers($wh_condn, $order='') 
	{
		//customer restriction on level based.
		if ($this->userdata['level'] != 1) {
			$cond = array('level_id' => $this->userdata['level'], 'user_id' => $this->userdata['userid']);
			
			$this->db->select('region_id');
		 	$reg_res = $this->db->get_where($this->cfg['dbpref']."levels_region", $cond);
			$reg_details = $reg_res->result_array();
			foreach($reg_details as $reg) {
				$regions[] = $reg['region_id'];
			}
			$regions_ids = array_unique($regions);
			$regions_ids = (array_values($regions)); //reset the keys in the array
			//$regions_ids = implode(",", $regions_ids);
		
			//restriction for country
			$this->db->select('country_id');
			$coun_res = $this->db->get_where($this->cfg['dbpref']."levels_country", $cond);
			$coun_details = $coun_res->result_array();
			foreach($coun_details as $coun) {
				$countries[] = $coun['country_id'];
			}
			if (!empty($countries)) {
				$countries_ids = array_unique($countries);
				$countries_ids = (array_values($countries)); //reset the keys in the array
				//$countries_ids = @implode(",",$countries_ids);
			}
		
			//restriction for state
			$this->db->select('state_id');
			$state_res = $this->db->get_where($this->cfg['dbpref']."levels_state", $cond);
			$ste_details = $state_res->result_array();
			foreach($ste_details as $ste) {
				$states[] = $ste['state_id'];
			}
			if (!empty($states)) {
				$states_ids = array_unique($states);
				$states_ids = (array_values($states)); //reset the keys in the array				
			}
			//$states_ids = implode(",",$states_ids);
		
			//restriction for location
			$this->db->select('location_id');
			$loc_res = $this->db->get_where($this->cfg['dbpref']."levels_location", $cond);
			$loc_details = $loc_res->result_array();
			foreach($loc_details as $loc) {
				$locations[] = $loc['location_id'];
			}
			if (!empty($locations)) {
				$locations_ids = array_unique($locations);
				$locations_ids = (array_values($locations)); //reset the keys in the array
			}
			//$locations_ids = implode(",",$locations_ids);
		}
		
		$this->db->select('distinct(c.custid),c.first_name,c.last_name,c.company,l.lead_status,l.pjt_status');
		$this->db->from($this->cfg['dbpref'].'customers as c');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.custid_fk = c.custid');
		if(!empty($wh_condn))
		$this->db->where($wh_condn);

        if ($this->userdata['level'] == 2) {
			$this->db->where_in('c.add1_region', $regions_ids);				
		} else if ($this->userdata['level'] == 3) {
			$this->db->where_in('c.add1_region', $regions_ids);
			$this->db->where_in('c.add1_country', $countries_ids);
		} else if ($this->userdata['level'] == 4) {
			$this->db->where_in('c.add1_region', $regions_ids);
			$this->db->where_in('c.add1_country', $countries_ids);
			$this->db->where_in('c.add1_state', $states_ids);
		} else if ($this->userdata['level'] == 5) {
			$this->db->where_in('c.add1_region', $regions_ids);
			$this->db->where_in('c.add1_country', $countries_ids);
			$this->db->where_in('c.add1_state', $states_ids);
			$this->db->where_in('c.add1_location', $locations_ids);
		}

		if(!empty($order)) {
			foreach($order as $key=>$value) {
				$this->db->order_by($key,$value);
			}
		}
		
		$customers = $this->db->get();        
        // echo $this->db->last_query(); exit;
        return $customers->result_array(); 
    }
	
	/*
	*@Get Lead details
	*@Method  get_lead_detail
	*/
	function get_lead_detail($id)
	{
		$this->db->select('l.lead_id,l.lead_title,l.expect_worth_amount,l.invoice_no,l.pjt_id,exp.expect_worth_name,sd.division_name,pbt.project_billing_type');
		$this->db->from($this->cfg['dbpref'].'leads as l');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as sd', 'sd.div_id = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as exp', 'exp.expect_worth_id = l.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'project_billing_type as pbt', 'pbt.id = l.project_type', 'LEFT');
		$this->db->where('l.lead_id',$id);
		$query = $this->db->get();
		// echo $this->db->last_query();
 		return $query->row_array();
	}

	/*
	*@Get payment Milestones Row for Leads
	*@Method  get_ms_records
	*/
	function get_ms_records($id)
	{
		$this->db->select('ep.expectid, ep.amount, ep.month_year, ep.project_milestone_name, exp.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'expected_payments as ep');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = ep.jobid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth as exp', 'exp.expect_worth_id = l.expect_worth_id');
		$this->db->where('ep.invoice_status', 0);
		$this->db->where('ep.jobid_fk', $id);
		$query = $this->db->get();
		// echo $this->db->last_query();
 		return $query->result_array();
	}
	
	/*
	*@Get_milestone_records
	*@Sales Forecast Model
	*/
	public function get_milestone_records($wh_condn) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'expected_payments');
		if(!empty($wh_condn))
		$this->db->where_in('expectid', $wh_condn);
		
		$this->db->order_by('expectid');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
    }

	/*
	*@Get_milestone_records
	*@Sales Forecast Model
	*/
	public function get_sf_category($wh_condn) {
		$this->db->select('forecast_category');
		$this->db->from($this->cfg['dbpref'].'sales_forecast_milestone');
		if(!empty($wh_condn))
		$this->db->where('forecast_id_fk', $wh_condn);
		
		$this->db->order_by('forecast_category', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->row_array();
    }
	
	/*
	*@get_exists_ms_records
	*@Sales Forecast Model
	*/
	public function get_exists_ms_records($id) {
		$this->db->select('milestone_ref_no');
		$this->db->from($this->cfg['dbpref'].'sales_forecast_milestone');
		if(!empty($wh_condn))
		$this->db->where('forecast_id_fk', $id);
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
	}
    
}

?>
