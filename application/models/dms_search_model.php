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
		
		if(!empty($projects) && count($projects)>0 && empty($customers)){
			$this->db->where_in("le.lead_id",$projects);
		}else if(!empty($projects) && count($projects)>0){
			$this->db->or_where_in("le.lead_id",$projects);
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
		$this->db->order_by("lf.lead_files_created_on");
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
		
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.division, j.expect_worth_id, j.expect_worth_amount, j.actual_worth_amount, ew.expect_worth_name, j.lead_stage, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, j.estimate_hour, j.project_type, j.rag_status, j.billing_type, pbt.project_billing_type, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm, j.actual_date_start, j.actual_date_due');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->join($this->cfg['dbpref'] . 'project_billing_type as pbt', 'pbt.id = j.project_type' , "LEFT");
			
			if(!empty($stage)){	
				$this->db->where("j.lead_status", '4');
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status = 1 ");
			}
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
			$this->db->where("lead_status", 4);
			$this->db->where("pjt_status", 1);
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
			
			
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.division, j.expect_worth_id, j.expect_worth_amount, j.actual_worth_amount, ew.expect_worth_name, j.lead_stage, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, j.estimate_hour, j.project_type, j.rag_status, j.billing_type, pbt.project_billing_type, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm, j.actual_date_start, j.actual_date_due');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->join($this->cfg['dbpref'] . 'project_billing_type as pbt', 'pbt.id = j.project_type' , "LEFT");
			//For Regionwise filtering
			$this->db->where_in('j.lead_id', $result_ids);
		
			if(!empty($stage)){	
				$this->db->where("j.lead_status", '4');
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status = 1 ");
			}
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
		
		$this->db->order_by("j.lead_id", "asc");
		$query = $this->db->get();
		$pjts =  $query->result_array();
		return $pjts;
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