<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dms_search_model extends crm_model {

    function __construct() {
        parent::__construct();
		$user_details = $this->session->userdata("logged_in_user");		
		$this->user_id = $user_details['userid'];
		$this->user_role = $user_details['role_id'];		
		$this->level = $user_details['level'];			
    }
		
	public function search_files($search_name = null,$tag_keyword = null,$customers = null,$projects = null,$extension = null,$from_date = null,$to_date = null){
	
		$user_id = $this->user_id;
		
		$qry = $this->db->get_where($this->cfg['dbpref']."stake_holders",array("user_id" => $user_id));
		if($qry->num_rows()>0){
			$res = $qry->result();
			$stake_arr = array();
			foreach($res as $r){
				$stake_arr[] = $r->lead_id;
			}
			$st = implode(",",$stake_arr);
		}		
		 
		$this->db->select('f.folder_name, cc.company, cus.customer_name as cust_firstname, le.lead_title, lf.file_id, lf.lead_id, lf.lead_files_name, lf.folder_id,us.first_name,us.last_name,lf.lead_files_created_on,lf.tag_names');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS lf');
		$this->db->join($this->cfg['dbpref'].'users AS us', 'us.userid = lf.lead_files_created_by', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'leads AS le', 'le.lead_id = lf.lead_id', 'join');
		$this->db->join($this->cfg['dbpref'].'customers AS cus', 'cus.custid = le.custid_fk', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = cus.company_id', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'file_management as f', 'f.folder_id = lf.folder_id', 'LEFT');
		
		 
		if (($this->user_role != 1 && $this->user_role != 2 && $this->user_role != 4)) {
			if($st){
				$this->db->where("(le.lead_assign = $user_id or le.assigned_to = $user_id or le.belong_to = $user_id or le.lead_id in ($st))");
			}else{
				$this->db->where("(le.lead_assign = $user_id or le.assigned_to = $user_id or le.belong_to = $user_id)");	
			}
		}
		
		if(!empty($customers) && count($customers)>0){
			$this->db->where_in("cc.companyid",$customers);	
		}
		
		if(!empty($projects) && count($projects)>0){
			$this->db->where_in("le.lead_id",$projects);
		} 
		
		if(!empty($extension) && count($extension)>0){
			$this->db->where_in('SUBSTRING_INDEX(lf.lead_files_name,".","-1")',$extension);
		}
		
		if(!empty($from_date)){
			$fd = date("Y-m-d H:i:s",strtotime($from_date));
			$this->db->where("lead_files_created_on >=",$fd);
		}
		if(!empty($to_date)){
			$td= date("Y-m-d H:i:s",strtotime($to_date));
			$this->db->where("lead_files_created_on <=",$td);
		}
		if($search_name)	$this->db->like("lf.lead_files_name", $search_name);$this->db->or_like('lf.tag_names', $search_name);
		if($tag_keyword) {
			$srch_val = @explode(',',$tag_keyword);
			$find_wh = '(';
			if(count($srch_val)>0) {
				$i = 0;
				foreach($srch_val as $srch) {
					if($i==0) {
						// $find_wh .= "FIND_IN_SET('".$srch."', lf.tag_names)";
						$find_wh .= "(lf.tag_names LIKE '%".$srch."%')";
					} else {
						// $find_wh .= " OR FIND_IN_SET('".$srch."', lf.tag_names)";
						$find_wh .= " OR (lf.tag_names LIKE '%".$srch."%' )";
					}
					$i++;
				}
			}
			$find_wh .= ')';
			$this->db->where($find_wh);
		}
		//	$this->db->order_by("lf.lead_files_created_on",'DESC');
		$this->db->order_by("cc.company",'ASC');
	    $sql = $this->db->get();
		//echo $this->db->last_query();exit;
	    return $sql->result_array();
	}
	
		public function get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,$billing_type=false,$divisions=false) {
		
		$userdata   = $this->session->userdata('logged_in_user');
		
		if($from_date=='0000-00-00 00:00:00'){
			$from_date = '';
		}
		if($to_date=='0000-00-00 00:00:00'){
			$to_date = '';
		}
		if($datefilter=='0'){
			$datefilter = '';
		}
		
		$stage 		= $pjtstage;
		$customer 	= $cust;
		$services	= $service;
		$practices	= $practice;
		$datefilter = $datefilter;
		$from_date 	= $from_date;
		$to_date	= $to_date;
		$divisions	= $divisions;
		
		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1')) {
		 
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.division, j.expect_worth_id, j.expect_worth_amount, j.actual_worth_amount, ew.expect_worth_name, j.lead_stage, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, j.estimate_hour, j.project_type, j.rag_status, j.billing_type, pbt.project_billing_type, c.customer_name as cfname, cc.company, u.first_name as fnm, u.last_name as lnm, j.actual_date_start, j.actual_date_due');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->join($this->cfg['dbpref'] . 'project_billing_type as pbt', 'pbt.id = j.project_type' , "LEFT");
			
			if (!empty($stage) && $stage!='null') {
				$this->db->where("j.lead_status", '4');
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status = 1 ");
			}
			if (!empty($customer) && $customer!='null') {
				$this->db->where_in('cc.companyid',$customer); 
			}
			/* if(!empty($pm)){		
				$this->db->where_in('j.assigned_to',$pm); 
			} */
			if(!empty($services) && $services!='null'){		
				$this->db->where_in('j.lead_service',$services);
			}
			if(!empty($practices) && $practices!='null'){	
				$this->db->where_in('j.practice',$practices);
			}
			if(!empty($divisions) && $divisions!='null'){		
				$this->db->where_in('j.division',$divisions);
			}
			if(!empty($billing_type)) {
				$this->db->where("j.billing_type", $billing_type);
			}
			
			if(!empty($from_date)) {
				switch($datefilter) {
					case 1:
						if(!empty($to_date)) {
							$this->db->where("(j.actual_date_start >='".date('Y-m-d', strtotime($from_date))."' OR j.actual_date_due >='".date('Y-m-d', strtotime($from_date))."')", NULL, FALSE);
							$this->db->where("(j.actual_date_start <='".date('Y-m-d', strtotime($to_date))."' OR j.actual_date_due <='".date('Y-m-d', strtotime($to_date))."')", NULL, FALSE);
						} else {
							$this->db->where("(j.actual_date_start >='".date('Y-m-d', strtotime($from_date))."' OR j.actual_date_due >='".date('Y-m-d', strtotime($from_date))."')", NULL, FALSE);
						}
					break;
					case 2:
						if(!empty($to_date)) {
							$this->db->where('j.actual_date_start >=', date('Y-m-d', strtotime($from_date)));
							$this->db->where('j.actual_date_start <=', date('Y-m-d', strtotime($to_date)));
						} else {
							$this->db->where('j.actual_date_start >=', date('Y-m-d', strtotime($from_date)));
						}
					break;
					case 3:
						if(!empty($to_date)) {
							$this->db->where('j.actual_date_due >=', date('Y-m-d', strtotime($from_date)));
							$this->db->where('j.actual_date_due <=', date('Y-m-d', strtotime($to_date)));
						} else {
							$this->db->where('j.actual_date_due >=', date('Y-m-d', strtotime($from_date)));
						}
					break;
				}
			}
			
		} else {
			$varSessionId = $this->userdata['userid']; //Current Session Id.

			//Fetching Project Team Members.
			$this->db->select('jobid_fk as lead_id');
			$this->db->where('userid_fk', $varSessionId);
			$rowscj = $this->db->get($this->cfg['dbpref'] . 'contract_jobs');
			$data['jobids'] = $rowscj->result_array();
			
			//Fetching Project Manager, Lead Assigned to & Lead owner jobids.
			$this->db->select('lead_id');
			$this->db->where("(assigned_to = '".$varSessionId."' OR lead_assign = '".$varSessionId."' OR belong_to = '".$varSessionId."')");
			$this->db->where("lead_status", 4);
			if (empty($stage) && $stage=='null') {
				$this->db->where("pjt_status", 1);
			}
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'leads');
			$data['jobids1'] = $rowsJobs->result_array();

			//Fetching Stake Holders.
			$data['jobids2'] = array();
			$this->db->select('lead_id');
			$this->db->where("user_id",$varSessionId);
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'stake_holders');
			if($rowsJobs->num_rows()>0)	$data['jobids2'] = $rowsJobs->result_array();			
			
			$data = array_merge_recursive($data['jobids'], $data['jobids1'],$data['jobids2']);
 
			$res[] = 0;
			if (is_array($data) && count($data) > 0) { 
				foreach ($data as $data) {
					$res[] = $data['lead_id'];
				}
			}
			$result_ids = array_unique($res);
			$curusid = $this->session->userdata['logged_in_user']['userid'];
			
			
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.division, j.expect_worth_id, j.expect_worth_amount, j.actual_worth_amount, ew.expect_worth_name, j.lead_stage, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, j.estimate_hour, j.project_type, j.rag_status, j.billing_type, pbt.project_billing_type, c.customer_name as cfname, cc.company, u.first_name as fnm, u.last_name as lnm, j.actual_date_start, j.actual_date_due');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->join($this->cfg['dbpref'] . 'project_billing_type as pbt', 'pbt.id = j.project_type' , "LEFT");
			//For Regionwise filtering
			$this->db->where_in('j.lead_id', $result_ids);
		
			if (!empty($stage) && $stage!='null') {
				$this->db->where("j.lead_status", '4');
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status = 1 ");
			}
			if (!empty($customer) && $customer!='null') {
				$this->db->where_in('cc.companyid',$customer); 
			}
			/* if(!empty($pm)){		
				$this->db->where_in('j.assigned_to',$pm); 
			} */
			if(!empty($services) && $services!='null'){
				$this->db->where_in('j.lead_service',$services);
			}
			if(!empty($practices) && $practices!='null'){	
				$this->db->where_in('j.practice',$practices);
			}
			if(!empty($divisions) && $divisions!='null'){		
				$this->db->where_in('j.division',$divisions);
			}
			if(!empty($billing_type)) {
				$this->db->where("j.billing_type", $billing_type);
			}
			
			if(!empty($from_date)) {
				switch($datefilter) {
					case 1:
						if(!empty($to_date)) {
							$this->db->where("(j.actual_date_start >='".date('Y-m-d', strtotime($from_date))."' OR j.actual_date_due >='".date('Y-m-d', strtotime($from_date))."')", NULL, FALSE);
							$this->db->where("(j.actual_date_start <='".date('Y-m-d', strtotime($to_date))."' OR j.actual_date_due <='".date('Y-m-d', strtotime($to_date))."')", NULL, FALSE);
						} else {
							$this->db->where("(j.actual_date_start >='".date('Y-m-d', strtotime($from_date))."' OR j.actual_date_due >='".date('Y-m-d', strtotime($from_date))."')", NULL, FALSE);
						}
					break;
					case 2:
						if(!empty($to_date)) {
							$this->db->where('j.actual_date_start >=', date('Y-m-d', strtotime($from_date)));
							$this->db->where('j.actual_date_start <=', date('Y-m-d', strtotime($to_date)));
						} else {
							$this->db->where('j.actual_date_start >=', date('Y-m-d', strtotime($from_date)));
						}
					break;
					case 3:
						if(!empty($to_date)) {
							$this->db->where('j.actual_date_due >=', date('Y-m-d', strtotime($from_date)));
							$this->db->where('j.actual_date_due <=', date('Y-m-d', strtotime($to_date)));
						} else {
							$this->db->where('j.actual_date_due >=', date('Y-m-d', strtotime($from_date)));
						}
					break;
				}
			}
		}
		
		if($keyword != 'Project Title, Name or Company' && !empty($keyword)) {
			$keyword = urldecode($keyword);
			$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR cc.company LIKE '%$keyword%' OR c.customer_name LIKE '%$keyword%' ))";
			$this->db->where($invwhere);
		}
		
		$this->db->order_by("j.lead_id", "desc");
		// $this->db->limit(5);
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$pjts =  $query->result_array();
		//echo '<pre>';print_r($pjts);exit;
		return $pjts;
	}	
	
	function customer_list($offset, $search, $order_field='last_name', $order_type='asc', $limit = false)
	{
        $restrict = '';
        $restrict_search = '';
		//customer restriction on level based.
		if ($this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4 || $this->userdata['level'] == 5) {
			$cond = array('level_id' => $this->userdata['level'], 'user_id' => $this->userdata['userid']);
			
			$this->db->select('region_id');
		 	$reg_res = $this->db->get_where($this->cfg['dbpref']."levels_region", $cond);
			$reg_details = $reg_res->result_array();
			foreach($reg_details as $reg) {
				$regions[] = $reg['region_id'];
			}
			$regions_ids = array_unique($regions);
			$regions_ids = (array_values($regions)); //reset the keys in the array
		
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
		}
       
        $offset = $this->db->escape_str($offset);	
		$this->db->select('CUST.*, REG.regionid, REG.region_name, COUN.countryid, COUN.country_name');
		$this->db->from($this->cfg['dbpref'].'customers_company as CUST');
		$this->db->join($this->cfg['dbpref'].'region as REG', 'CUST.add1_region = REG.regionid', 'left');
		$this->db->join($this->cfg['dbpref'].'country as COUN', 'CUST.add1_country = COUN.countryid', 'left');
        if ($this->userdata['level'] == 2) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
		} else if ($this->userdata['level'] == 3) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
		} else if ($this->userdata['level'] == 4) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
		} else if ($this->userdata['level'] == 5) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
			$this->db->where_in('CUST.add1_location', $locations_ids);
		}
		if($search != false) {
			$search = $this->db->escape_str(urldecode($search));
			$this->db->where("(company LIKE '%$search%')");
		}
		if(!empty($limit))
		$this->db->limit($limit);
		$customers = $this->db->get();
        // echo $this->db->last_query(); exit;
        return $customers->result_array();
    }	
	
	
	function get_extensions(){
		$qry = $this->db->query("SELECT distinct SUBSTRING_INDEX(lead_files_name,'.',-1) as extension FROM `crm_lead_files` order by extension asc");
		$res = $qry->num_rows();
		if($res){
			return $qry->result_array();
		}
		return false;
	}
}

/* End of dms search model file */