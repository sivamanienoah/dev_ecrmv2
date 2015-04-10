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
	public function get_sf_milestone_records($filter = false) {
	
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
			
			$query = $this->db->get();
			// echo $this->db->last_query();
			$rowscust1 = $query->result_array();
			
			$this->db->select('ld.lead_id');
			$this->db->from($this->cfg['dbpref'].'leads as ld');
			$this->db->where("(ld.assigned_to = '".$this->userdata['userid']."' OR ld.lead_assign = '".$this->userdata['userid']."' OR ld.belong_to = '".$this->userdata['userid']."')");
			$this->db->where("ld.lead_status", 4);
			$this->db->where("ld.pjt_status", 1);
			$query1 = $this->db->get();
			// echo $this->db->last_query();
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
		

		$this->db->select('sfm.milestone_id, sfm.forecast_category, sfm.milestone_name, sfm.milestone_value, sfm.for_month_year, sfc.forecast_id, c.company, c.first_name, c.last_name, l.lead_title, l.expect_worth_id, l.expect_worth_amount, enti.division_name, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'sales_forecast_milestone as sfm');
		$this->db->join($this->cfg['dbpref'].'sales_forecast as sfc', 'sfc.forecast_id = sfm.forecast_id_fk');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfc.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		
		if(!empty($job_ids) && count($job_ids)>0) {
			$this->db->where_in('sfc.job_id', $job_ids);
		}
		
		if (!empty($filter['entity']) && $filter['entity']!='null') {
			$filter['entity'] = explode(',',$filter['entity']);
			$this->db->where_in('l.division', $filter['entity']);
		}
		if (!empty($filter['customer']) && $filter['customer']!='null') {
			$filter['customer'] = explode(',',$filter['customer']);
			$this->db->where_in('sfc.customer_id', $filter['customer']);
		}
		if (!empty($filter['lead_ids']) && $filter['lead_ids']!='null') {
			$filter['lead_ids'] = explode(',',$filter['lead_ids']);
			$this->db->where_in('l.lead_id', $filter['lead_ids']);
		}
		if (!empty($filter['project_ids']) && $filter['project_ids']!='null') {
			$filter['project_ids'] = explode(',',$filter['project_ids']);
			$this->db->where_in('l.lead_id', $filter['project_ids']);
		}
		if(!empty($filter['month_year_from_date']) && empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(sfm.for_month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
		} else if(!empty($filter['month_year_from_date']) && !empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(sfm.for_month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
			$this->db->where('DATE(sfm.for_month_year) <=', date('Y-m-t', strtotime($filter['month_year_to_date'])));
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
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
	*@Update Row for dynamic table
	*@Method  update_row_return_affected_rows
	*/
    public function update_row_return_affected_rows($table, $cond, $data) {
    	$sql =  '
				UPDATE `'.$this->cfg['dbpref'].'sales_forecast_milestone` SET 
				milestone_name = "'.$data['milestone_name'].'",
				milestone_value = '.$data['milestone_value'].',
				for_month_year = "'.date("Y-m-d", strtotime($data['for_month_year'])).'",
				modified_by = '.$this->userdata['userid'].'
				WHERE milestone_id = '.$cond.'
				';
		mysql_query($sql);
		return mysql_affected_rows();
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
	*@Get Row for Customers
	*@Method  get_customers
	*/
	function get_customers($wh_condn='', $order='') 
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
	*@Get Logs for Milestones
	*@Method  get_ms_logs
	*/
	function get_ms_logs($id)
	{
		$this->db->select('msl.milestone_name, msl.milestone_value, msl.for_month_year, msl.modified_by, msl.modified_on, us.first_name, us.last_name');
		$this->db->from($this->cfg['dbpref'].'sales_forecast_milestone_audit_log as msl');
		$this->db->join($this->cfg['dbpref'].'users as us', 'us.userid = msl.modified_by');
		$this->db->where('msl.milestone_id', $id);
		$this->db->order_by('modified_on', 'desc');
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
		$this->db->select('l.lead_status,l.pjt_status,exp.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads as l');
		$this->db->join($this->cfg['dbpref'].'sales_forecast as sf','sf.job_id = l.lead_id');
		$this->db->join($this->cfg['dbpref'].'expect_worth as exp', 'exp.expect_worth_id = l.expect_worth_id');
		$this->db->where('sf.forecast_id', $wh_condn);
		$query = $this->db->get();
		// echo $this->db->last_query(); #exit;
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
	
	/*
	*@get_sf_customers
	*@Sales Forecast Model
	*/
	public function get_sf_records($type) {
	
		if($type=='customers')
		$this->db->select('c.custid, c.company, c.first_name, c.last_name');
		else if($type=='jobs')
		$this->db->select('c.custid, c.company, c.first_name, c.last_name, l.lead_id, l.lead_title');
		
		$this->db->from($this->cfg['dbpref'].'customers as c');
		$this->db->join($this->cfg['dbpref'].'sales_forecast as sf', 'sf.customer_id  = c.custid');
		if($type=='jobs') {
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id  = sf.job_id');
		}
	
		//LEVEL BASED RESTIRCTION
		if( $this->userdata['level'] != 1 ) {
			if (isset($this->session->userdata['region_id']))
			$regionid = explode(',',$this->session->userdata['region_id']);
			if (isset($this->session->userdata['countryid']))
			$countryid = explode(',',$this->session->userdata['countryid']);
			if (isset($this->session->userdata['stateid']))
			$stateid = explode(',',$this->session->userdata['stateid']);
			if (isset($this->session->userdata['locationid']))
			$locationid = explode(',',$this->session->userdata['locationid']);
			
			switch($this->userdata['level']) {
				case 2:
					$this->db->where_in('c.add1_region',$regionid);
				break;
				case 3:
					$this->db->where_in('c.add1_region',$regionid);
					$this->db->where_in('c.add1_country',$countryid);
				break;
				case 4:
					$this->db->where_in('c.add1_region',$regionid);
					$this->db->where_in('c.add1_country',$countryid);
					$this->db->where_in('c.add1_state',$stateid);
				break;
				case 5:
					$this->db->where_in('c.add1_region',$regionid);
					$this->db->where_in('c.add1_country',$countryid);
					$this->db->where_in('c.add1_state',$stateid);
					$this->db->where_in('c.add1_location',$locationid);
				break;
			}
		}
		if($type=='customers') {
			$this->db->group_by('c.custid');
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->result_array();
	}
	
	/*
	*@Get Current Financial year
	*@Method  calculateFiscalYearForDate
	*/
	function calculateFiscalYearForDate($inputDate, $fyStart, $fyEnd) {
		$date = strtotime($inputDate);
		$inputyear = strftime('%Y',$date);
	 
		$fystartdate = strtotime($fyStart.'/'.$inputyear);
		$fyenddate = strtotime($fyEnd.'/'.$inputyear);
	 
		if($date <= $fyenddate){
			$fy = intval($inputyear);
		}else{
			$fy = intval(intval($inputyear) + 1);
		}
	 
		return $fy;
	}
    
}

?>
