<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dms_search_model extends crm_model {

    function __construct() {
        parent::__construct();
		$user_details = $this->session->userdata("logged_in_user");		
		$this->user_id = $user_details['userid'];
		$this->user_role = $user_details['role_id'];		
		$this->level = $user_details['level'];			
    }
		
	public function search_files($search_name = null,$customers = null,$projects = null,$extension = null,$from_date = null,$to_date = null){
	
		$user_id = $this->user_id;
		$this->db->select('f.folder_name,cus.company,cus.first_name as cust_firstname ,cus.last_name as cust_lastname,le.lead_title,lf.file_id,lf.lead_id,lf.lead_files_name,lf.folder_id,us.first_name,us.last_name,lf.lead_files_created_on');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS lf');
		$this->db->join($this->cfg['dbpref'].'users AS us', 'us.userid = lf.lead_files_created_by', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'leads AS le', 'le.lead_id = lf.lead_id', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'customers AS cus', 'cus.custid = le.custid_fk', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'file_management as f', 'f.folder_id = lf.folder_id', 'LEFT');
	 
		if($this->user_role !=1 && $this->level != 1){
			$this->db->where("(le.lead_assign = $user_id or le.assigned_to = $user_id or le.belong_to = $user_id)");
		}
		
		if(!empty($customers) && count($customers)>0){
			$this->db->where_in("cus.custid",$customers);			
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
		if($search_name)	$this->db->like("lf.lead_files_name", $search_name);
	//	$this->db->order_by("lf.lead_files_created_on",'DESC');
		$this->db->order_by("cus.company",'ASC');
	    $sql = $this->db->get();
		//echo $this->db->last_query();exit;
	    return $sql->result_array();
	}
	
	public function get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date,$billing_type=false,$divisions=false) {
		 
		$stage 		= $pjtstage;
		$customer 	= $cust;
		$services	= $service;
		$practices	= $practice;
		$datefilter = $datefilter;
		$from_date 	= $from_date;
		$to_date	= $to_date;
		$divisions	= $divisions;
		
		if (($this->user_role == '1' && $this->level == '1') || ($this->user_role == '2' && $this->level == '1')) {
		
			$this->db->select('j.lead_id,j.lead_title');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->join($this->cfg['dbpref'] . 'project_billing_type as pbt', 'pbt.id = j.project_type' , "LEFT");
			
		/*	if(!empty($stage)){	
				$this->db->where("j.lead_status", '4');
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status = 1 ");
			}
		*/	
			
			if(!empty($customer)){		
				$this->db->where_in('j.custid_fk',$customer); 
			}
			/* if(!empty($pm)){		
				$this->db->where_in('j.assigned_to',$pm); 
			} */
			if(!empty($services)){		
				$this->db->where_in('j.lead_service',$services);
			}
			if(!empty($practices)){		
				$this->db->where_in('j.practice',$practices);
			}
			if(!empty($divisions)){		
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
			$varSessionId = $this->user_id; //Current Session Id.

			//Fetching Project Team Members.
			$this->db->select('jobid_fk as lead_id');
			$this->db->where('userid_fk', $varSessionId);
			$rowscj = $this->db->get($this->cfg['dbpref'] . 'contract_jobs');
			$data['jobids'] = $rowscj->result_array();
			
			//Fetching Project Manager, Lead Assigned to & Lead owner jobids.
			$this->db->select('lead_id');
			$this->db->where("(assigned_to = '".$varSessionId."' OR lead_assign = '".$varSessionId."' OR belong_to = '".$varSessionId."')");
			//$this->db->where("lead_status", 4);
			//$this->db->where("pjt_status", 1);
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'leads');
			$data['jobids1'] = $rowsJobs->result_array();

			$data = array_merge_recursive($data['jobids'], $data['jobids1']);

			$res[] = 0;
			if (is_array($data) && count($data) > 0) { 
				foreach ($data as $data) {
					$res[] = $data['lead_id'];
				}
			}
			$result_ids = array_unique($res);
			$curusid = $this->user_id;
			
			
			$this->db->select('j.lead_id,j.lead_title');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->join($this->cfg['dbpref'] . 'project_billing_type as pbt', 'pbt.id = j.project_type' , "LEFT");
			//For Regionwise filtering
			$this->db->where_in('j.lead_id', $result_ids);
		
			/* if(!empty($stage)){	
				$this->db->where("j.lead_status", '4');
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status = 1 ");
			} */
			if(!empty($customer)){		
				$this->db->where_in('j.custid_fk',$customer); 
			}
			/* if(!empty($pm)){		
				$this->db->where_in('j.assigned_to',$pm); 
			} */
			if(!empty($services)){		
				$this->db->where_in('j.lead_service',$services);
			}
			if(!empty($practices)){		
				$this->db->where_in('j.practice',$practices);
			}
			if(!empty($divisions)){		
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
			$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
			$this->db->where($invwhere);
		}
		
		$this->db->order_by("j.lead_title", "asc");
		$query = $this->db->get();
	//	echo $this->db->last_query();exit;
		$pjts =  $query->result();
		return $pjts;
	}	
	
	function customer_list($offset, $search, $order_field = 'last_name', $order_type = 'asc', $limit = false) {
        $restrict = '';
        $restrict_search = '';
		//customer restriction on level based.
		 
		if ($this->level == 2 || $this->level == 3 || $this->level == 4 || $this->level == 5) {
			$cond = array('level_id' => $this->level, 'user_id' => $this->user_id);
			
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
       
        $offset = mysql_real_escape_string($offset);		
		$this->db->select('lds.lead_id,CUST.custid,CUST.first_name,CUST.last_name,CUST.company, REG.regionid, REG.region_name, COUN.countryid, COUN.country_name');
		$this->db->from($this->cfg['dbpref'].'customers as CUST');
		$this->db->join($this->cfg['dbpref'].'leads as lds', 'CUST.custid = lds.custid_fk', 'join');
		$this->db->join($this->cfg['dbpref'].'lead_files as lf', 'lds.lead_id = lf.lead_id', 'join');
		$this->db->join($this->cfg['dbpref'].'region as REG', 'CUST.add1_region = REG.regionid', 'left');
		$this->db->join($this->cfg['dbpref'].'country as COUN', 'CUST.add1_country = COUN.countryid', 'left');
        if ($this->level == 2) {
			$this->db->where_in('CUST.add1_region', $regions_ids);				
		} else if ($this->level == 3) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
		} else if ($this->level == 4) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
		} else if ($this->level == 5) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
			$this->db->where_in('CUST.add1_location', $locations_ids);
		}
		//$this->db->where("lds.lead_id != 'null' AND lds.lead_status IN ('4') AND lds.pjt_status = 1 "); 
		
		$this->db->group_by("CUST.custid");
		$this->db->order_by("CUST.company","ASC");
		
		if(!empty($limit))	
		$this->db->limit($limit);
		
		$customers = $this->db->get();        
		//echo $this->db->last_query();
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