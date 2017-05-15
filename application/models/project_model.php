<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project_model extends crm_model 
{   
    public function __construct()
    {
		parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode('","', $this->stg);
    }
    
	function get_user_byrole($role_id)
	{
    	$users = $this->db->get_where($this->cfg['dbpref'] . 'users', array('role_id'=>$role_id))->result_array();
    	return $users;
    }
	
	function get_customers() 
	{
		$this->db->select('companyid, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers_company');
		$this->db->order_by("company", "asc");
	    $customers = $this->db->get();
	    $customers =  $customers->result_array();
	    return $customers;
	}

	//advance search functionality for projects in home page.
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
		
		// echo "<pre>"; print_r($customer); die;
		
		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '4')) {
		 
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
		//echo $this->db->last_query(); exit;
		$pjts =  $query->result_array();
		//echo '<pre>';print_r($pjts);exit;
		return $pjts;
	}
	
	//get the access for changing the project id, project manager, assigning project members
	public function get_access($id, $uid)
	{
		$this->db->select('lead_assign, assigned_to, belong_to');
		$this->db->where('lead_id', $id);
		$this->db->where("(lead_assign = '".$uid."' || assigned_to = '".$uid."' || belong_to = '".$uid."')");
		$sql = $this->db->get($this->cfg['dbpref'] . 'leads');
		$res1 = $sql->result_array();
		if (empty($res1)) {
			$chge_access = 0;			
			$this->db->select('user_id');
			$this->db->where('lead_id', $id);
			$this->db->where('user_id', $uid);
			$stake_holders = $this->db->get($this->cfg['dbpref'] . 'stake_holders');
			$stake_acces = $stake_holders->row_array();
			if(is_array($stake_acces) && count($stake_acces)>0 && !empty($stake_acces)) {
				$chge_access = 1;
			}		
		} else {
			$chge_access = 1;
		}
		return $chge_access;
	}
	
	//get overall details for lead or project
	function get_quote_data($id)
	{
    	$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'customers as cus');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = cus.company_id');
		$this->db->join($this->cfg['dbpref'].'leads as jb', 'jb.custid_fk = cus.custid', 'left');
    	$this->db->join($this->cfg['dbpref'].'region as reg', 'reg.regionid = cc.add1_region', 'left');
    	$this->db->join($this->cfg['dbpref'].'country as cnty', 'cnty.countryid = cc.add1_country', 'left');
    	$this->db->join($this->cfg['dbpref'].'state as ste', 'ste.stateid = cc.add1_state', 'left');
    	$this->db->join($this->cfg['dbpref'].'location as locn ', 'locn.locationid = cc.add1_location', 'left');
    	$this->db->join($this->cfg['dbpref'].'expect_worth as exw', 'exw.expect_worth_id = jb.expect_worth_id', 'left');
    	$this->db->join($this->cfg['dbpref'].'lead_stage as ls', 'ls.lead_stage_id = jb.lead_stage', 'left');
    	$this->db->join($this->cfg['dbpref'].'lead_services as jbcat', 'jbcat.sid = jb.lead_service', 'left');
    	$this->db->join($this->cfg['dbpref'].'sales_divisions as sd', 'sd.div_id = jb.division', 'left');
    	$this->db->where('jb.lead_id', $id);
    	$this->db->where('jb.pjt_status !=', 0);
		$this->db->limit(1);
		$results = $this->db->get();
        return $results->result_array();
    }
	
	//get the job url
	public function get_job_urls($lead_id)
    {
        $sql = "SELECT *
                FROM `".$this->cfg['dbpref']."job_urls`
                WHERE `jobid_fk` = ?
                ORDER BY `urlid`";
        $rs = $this->db->query($sql, array($lead_id));
        
        $html = '';
        
        if ($rs->num_rows() > 0)
        {
            foreach ($rs->result() as $row)
            {
                $html .= '<li>';
                
                    $html .= '<a href="#" onclick="ajaxDeleteJobURL(' . $row->urlid . ', this); return false;" class="file-delete">delete URL</a>';
               
                
                $html .= '<span>' . auto_link($row->url) . '</span><p>' . htmlentities($row->content, ENT_QUOTES) . '</p></li>';
            }
        }
        return $html;
    }
	
	public function get_list_users($id)
	{
		$this->db->select('lead_assign, assigned_to, belong_to');
		$this->db->where('lead_id', $id);
		$list_users = $this->db->get($this->cfg['dbpref'] . 'leads');
		return $list_users->row_array();
	}
	
	public function get_contract_users($id)
	{
		$this->db->select('userid_fk');
		$this->db->where('jobid_fk', $id);
		$contract_users = $this->db->get($this->cfg['dbpref'] . 'contract_jobs');
		return $contract_users->result_array();
	}
	
	public function get_stake_holders($id)
	{
		$this->db->select('user_id');
		$this->db->where('lead_id', $id);
		$stake_holders = $this->db->get($this->cfg['dbpref'] . 'stake_holders');
		return $stake_holders->result_array();
	}	
	
	function updt_log_view_status($id, $log) 
	{
		$this->db->where('lead_id', $id);
		return $this->db->update($this->cfg['dbpref'] . 'leads', $log);
	}
	
	function get_logs($id) 
	{
		$this->db->order_by('date_created', 'desc');
		$query = $this->db->get_where($this->cfg['dbpref'] . 'logs', array('jobid_fk' => $id));
		return $query->result_array();
	}
	
	function get_user_data_by_id($tbl, $condn) 
	{
		$this->db->where($condn);
		$user = $this->db->get($this->cfg['dbpref'] . $tbl);
		return $user->result_array();
	}
	
	function get_users() 
	{
    	$this->db->select('userid,first_name,last_name,username,level,role_id,inactive,emp_id');
		$this->db->where('inactive',0);
		$this->db->where('username != ',"admin.enoah");
		
    	$this->db->order_by('first_name',"asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }	
	
	function get_all_users() 
	{
    	$this->db->select('userid,first_name,last_name,username,level,role_id,inactive,emp_id');
		//$this->db->where('inactive',0);
		$this->db->where('username != ',"admin.enoah");
    	$this->db->order_by('first_name',"asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }
	
	public function get_practices()
	{
    	$this->db->select('id, practices');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$query = $this->db->get($this->cfg['dbpref'] . 'practices');
		return $query->result_array();
    }
	
	public function get_lead_services()
	{
    	$this->db->select('sid, services');
    	$this->db->order_by('services');
		$query = $this->db->get($this->cfg['dbpref'] . 'lead_services');
		return $query->result_array();
    }
	
	function get_payment_terms($id) 
	{
		$this->db->where('jobid_fk', $id);
		$this->db->order_by('expectid', 'asc');
		$payment_terms = $this->db->get($this->cfg['dbpref'] . 'expected_payments');
		return $payment_terms->result_array();
	}
	
	function get_deposits_data($id) 
	{
		$this->db->select('de.*, exp.project_milestone_name AS payment_term, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'] . 'deposits as de');
		$this->db->join($this->cfg['dbpref'].'expected_payments as exp', 'exp.expectid = de.map_term', 'left');
		$this->db->join($this->cfg['dbpref'].'leads as jb', 'jb.lead_id = de.jobid_fk', 'left');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = jb.expect_worth_id', 'left');
		$this->db->where('de.jobid_fk', $id);
		$this->db->order_by('depositid', 'asc');
		$deposits = $this->db->get();
		return $deposits->result_array();
	}
	
	function insert_row($tbl, $ins) 
	{
		return $this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
    }
	
	function return_insert_id($tbl, $ins) 
	{
		$this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
		return $this->db->insert_id();
    }
	
	
	function get_userlist($userList) 
	{
    	$this->db->select('userid, first_name, last_name, email, level, role_id, inactive');
		if(!empty($userList))
		$this->db->where_in('userid', $userList);
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }
	
	public function get_lead_det($id) 
	{
		$res = $this->db->get_where($this->cfg['dbpref'] . 'leads', array('lead_id'=>$id))->row_array();
    	return $res;	
	}
	
	function delete_row($tbl, $condn) 
	{
		$this->db->where($condn);
		$this->db->delete($this->cfg['dbpref'] . $tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
	
	function delete_contract_job($tbl, $condn, $uid)
	{	
		$this->db->where($condn);
		$this->db->where_in('userid_fk', $uid);
		$this->db->delete($this->cfg['dbpref'] . $tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
	
	public function chk_status($tbl, $condn)
	{		
		$this->db->where($condn);
		$sql = $this->db->get($this->cfg['dbpref'] . $tbl);
        return ($sql->num_rows() > 0) ? TRUE : FALSE;
    }
	
	function update_row($tbl, $updt, $condn)
	{
		$this->db->update($this->cfg['dbpref'] . $tbl, $updt, $condn);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }
	
	function update_practice($tbl, $updt, $condn)
	{
		return $this->db->update($this->cfg['dbpref'] . $tbl, $updt, $condn);
    }
	
	//get expected payment details for the project.
	public function get_expect_payment_terms($id) 
	{
    	$this->db->select('expm.expectid, expm.expected_date, expm.month_year, expm.amount, expm.project_milestone_name, expm.received, expm.invoice_status, jb.expect_worth_id, exnm.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'expected_payments as expm');
		$this->db->join($this->cfg['dbpref'].'leads as jb', 'jb.lead_id = expm.jobid_fk', 'left');
		$this->db->join($this->cfg['dbpref'].'expect_worth as exnm', 'exnm.expect_worth_id = jb.expect_worth_id', 'left');
    	$this->db->where('expm.jobid_fk', $id);
    	$this->db->order_by('expm.expectid');
		$results = $this->db->get();
        return $results->result_array();
    }
	
	//get the payment term details.
	function get_payment_term_det($eid, $jid)
	{
		$wh_condn = array('expectid' => $eid, 'jobid_fk' => $jid);
		$this->db->select('expm.expectid,expm.amount,expm.expected_date,expm.month_year,expm.received,expm.project_milestone_name,expm.payment_remark, expm.invoice_status,j.expect_worth_id,exnm.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'expected_payments as expm');
		$this->db->join($this->cfg['dbpref'].'leads as j', 'j.lead_id = expm.jobid_fk', 'left');
		$this->db->join($this->cfg['dbpref'].'expect_worth as exnm', 'exnm.expect_worth_id = j.expect_worth_id', 'left');
		$this->db->where($wh_condn);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	function get_receivedpaymentDet($pdid, $jid) 
	{
		$query = $this->db->get_where($this->cfg['dbpref'].'deposits', array('depositid' => $pdid, 'jobid_fk' => $jid ));
		return $query->row_array();
	}
	
	function get_deposits_amt($condn) 
	{	
		$this->db->select('sum(amount) as tot_amt');
		$this->db->where('jobid_fk', $condn['jobid_fk']);
		$this->db->where('map_term', $condn['map_term']);
		if(!empty($condn['depositid']))
		$this->db->where('depositid !=', $condn['depositid']);
		$query = $this->db->get($this->cfg['dbpref'].'deposits');
		return $query->row_array();
	}
	
	function delete_project($tbl, $lead_id) 
	{
		$this->db->where('lead_id', $lead_id);
		$this->db->delete($this->cfg['dbpref'] . $tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	/* public function get_timesheet_data($pjt_code, $lead_id)
	{	
		if(!empty($lead_id))
		$getActDate = $this->get_quote_data($lead_id);
		
		if(!empty($getActDate[0]['date_created'])) {
			$start_date = date('Y-m-d',strtotime($getActDate[0]['date_created']));
		} else {
			$start_date = '0000-00-00';
		}
	
		$timesheet_db = $this->load->database('timesheet',TRUE);
		//$id='315';
		$sql = "SELECT t.uid AS Resources, sum(t.duration)/60 as total_hour, ";
		$sql .= " SUM(CASE WHEN t.resoursetype='Billable' AND ((t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/60 as 'Billable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Non-Billable' AND ((t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/60 as 'Non-Billable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Internal' AND ((t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/60 as 'Internal'";
		$sql .= " FROM ".$timesheet_db->dbprefix('times')." AS t";
		$sql .= " JOIN ".$timesheet_db->dbprefix('project')." AS p";
		$sql .= " JOIN ".$timesheet_db->dbprefix('task')." AS tsk";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid = a.username";
		$sql .= " WHERE ( p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id AND tsk.task_id = t.task_id ) GROUP BY t.uid";
		// echo $sql; #EXIT;
		$query=$timesheet_db->query($sql);
		return $query->result_array();
	} */
	
	public function get_timesheet_data($pjt_code, $lead_id, $bill_type, $st_date=false, $groupby_type)
	{
		//$bill_type == 3 for view the particular month metrics data
		$start_date = $end_date = '';
		switch($bill_type){
			case 1:
				/* if(!empty($lead_id)) {
					$getActDate = $this->get_quote_data($lead_id);
				}
				if(!empty($getActDate[0]['date_created'])) {
					$start_date = date('Y-m-d', strtotime($getActDate[0]['date_created']));
				} else {
					$start_date = '0000-00-00';
				} */		
				$start_date = '2006-01-01';
				$end_date   = date('Y-m-d');
			break;
			case 2:
				$start_date = date('Y-m-01');
				$end_date   = date('Y-m-d');
			break;
			case 3:
				$start_date = $st_date;
				$end_date   = date(('Y-m-t'), strtotime($st_date));
			break;
		}

		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, duration_hours, ts.resoursetype, ts.username, ts.empname,ts.practice_id, ts.`resource_duration_cost` as duration_cost, ts.direct_cost_per_hour as direct_cost, ts.`resource_duration_direct_cost` as duration_direct_cost,resource_total_hours');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_month_data as ts');
		$this->db->where("ts.project_code",$pjt_code);
		$this->db->where("DATE(ts.start_time) >= ",$start_date);
		$this->db->where("DATE(ts.end_time) <= ",$end_date);
		if($groupby_type == 1) {
			$this->db->group_by(array("ts.resoursetype"));
		} else if($groupby_type == 2) {
			$this->db->group_by(array("ts.username", "yr", "month_name", "ts.resoursetype"));
		}
		
		$query = $this->db->get();
		
		// echo $this->db->last_query() . "<br />"; exit;
		
		return $query->result_array();
	}
	
	public function get_timesheet_data_updated_original($pjt_code, $lead_id, $bill_type, $st_date=false, $groupby_type)
	{
		//$bill_type == 3 for view the particular month metrics data
		$start_date = $end_date = '';
		switch($bill_type){
			case 1:	
				$start_date = '2006-01-01';
				$end_date   = date('Y-m-d');
			break;
			case 2:
				$start_date = date('Y-m-01');
				$end_date   = date('Y-m-d');
			break;
			case 3:
				$start_date = $st_date;
				$end_date   = date(('Y-m-t'), strtotime($st_date));
			break;
		}

		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, ts.duration_hours, ts.resoursetype, ts.username, ts.empname,ts.practice_id, ts.`resource_duration_cost` as duration_cost, ts.direct_cost_per_hour as direct_cost, ts.`resource_duration_direct_cost` as duration_direct_cost,resource_total_hours');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_month_data as ts');//timesheet_month_data
		$this->db->where("ts.project_code",$pjt_code);
		$this->db->where("DATE(ts.start_time) >= ",$start_date);
		$this->db->where("DATE(ts.end_time) <= ",$end_date);
		if($groupby_type == 1) {
			$this->db->group_by(array("ts.resoursetype"));
		} else if($groupby_type == 2) {
			$this->db->group_by(array("ts.username", "yr", "month_name", "ts.resoursetype"));
		}
		
		$query = $this->db->get();
		/* if($pjt_code =='ITS-WIN-19-1216')
		{
			echo $this->db->last_query() . "<br />"; exit;
		}*/
		
		return $query->result_array();
	}	
	
	public function get_timesheet_data_updated($pjt_code, $lead_id, $bill_type, $st_date=false, $groupby_type)
	{
		//$bill_type == 3 for view the particular month metrics data
		$start_date = $end_date = '';
		switch($bill_type){
			case 1:	
				$start_date = '2006-01-01';
				$end_date   = date('Y-m-d');
			break;
			case 2:
				$start_date = date('Y-m-01');
				$end_date   = date('Y-m-d');
			break;
			case 3:
				$start_date = $st_date;
				$end_date   = date(('Y-m-t'), strtotime($st_date));
			break;
		}

		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, ts.duration_hours, ts.resoursetype, ts.username, ts.empname,ts.practice_id, ts.`resource_duration_cost` as duration_cost, ts.direct_cost_per_hour as direct_cost, ts.`resource_duration_direct_cost` as duration_direct_cost,resource_total_hours,ts.project_code');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_month_data as ts');//timesheet_month_data
		$this->db->where_in("ts.project_code",$pjt_code);
		$this->db->where("DATE(ts.start_time) >= ",$start_date);
		$this->db->where("DATE(ts.end_time) <= ",$end_date);
		if($groupby_type == 1) {
			$this->db->group_by(array("ts.resoursetype"));
		} else if($groupby_type == 2) {
			$this->db->group_by(array("ts.project_code","ts.username", "yr", "month_name", "ts.resoursetype"));
		}
		
		$query = $this->db->get();
		//echo $this->db->last_query() . "<br />"; exit;
		return $query->result_array();
	}	
	
	/*
	 *@method get_timesheet_project_type
	 *@param project_code
	 */
	public function get_timesheet_project_type($pjt_code)
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);
	
		$sql = "SELECT pj.project_type_id, pjtype.project_type_name
		FROM ".$timesheet_db->dbprefix('project')." as pj 
		JOIN ".$timesheet_db->dbprefix('project_types')." as pjtype ON pjtype.project_type_id = pj.project_type_id 
		WHERE pj.project_code = '".$pjt_code."' ";
		// echo $sql; exit;
		$query = $timesheet_db->query($sql);
		return $query->row_array();
	}
	
	/*
	 *@method get_timesheet_project_lead
	 *@param project_code
	 */
	public function get_timesheet_project_lead($pjt_code)
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);
	
		$sql = "SELECT CONCAT(us.first_name,' ',us.last_name) as project_lead, pj.proj_leader 
		FROM ".$timesheet_db->dbprefix('project')." as pj 
		JOIN ".$timesheet_db->dbprefix('user')." as us ON us.username = pj.proj_leader 
		WHERE pj.project_code = '".$pjt_code."' ";
		
		// echo $sql; exit;
		$query = $timesheet_db->query($sql);
		return $query->row_array();
	}
	
	/*
	 *@method get_timesheet_users
	 *@param project_code
	 */
	public function get_timesheet_users($pjt_code)
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);
		
		$users = array();
		
		$sql = "SELECT us.first_name, us.last_name, assgn.username 
		FROM ".$timesheet_db->dbprefix('assignments')." as assgn 
		JOIN ".$timesheet_db->dbprefix('project')." as pj ON pj.proj_id = assgn.proj_id 
		JOIN ".$timesheet_db->dbprefix('user')." as us ON us.username = assgn.username 
		WHERE pj.project_code = '".$pjt_code."' AND assgn.status = 0
		ORDER BY assgn.username";
		
		$query = $timesheet_db->query($sql);
		$res = $query->result_array();
		
		if(count($res) > 0) {
			foreach($res as $row){
				$users['name'][] = $row['first_name'] . ' ' .$row['last_name'];
				$users['username'][] = strtolower($row['username']);
			}
		}
		return $users;
	}
	/*
	 *@method task_timesheet_entry
	 *@param project_id,username
	 * Add all the project task to new assigning members 10/7/2015	
	 */
	public function task_timesheet_entry($pjt_id,$username)
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);
		$sql = "SELECT task_id FROM ".$timesheet_db->dbprefix('task')."  WHERE proj_id = '".$pjt_id."'";	
		$query = $timesheet_db->query($sql);
		$res = $query->result_array();		
		if(count($res) > 0) {
			foreach($res as $row) {				
				$taskid = $row['task_id'];
				$timesheet_db->insert($timesheet_db->dbprefix("task_assignments"), array("task_id"=>$taskid,"proj_id"=>$pjt_id,"username"=>$username));
			}
		}		
	}
	/*
	* @method task_timesheet_entry Ends here
	*/
	
	/* public function get_actual_project_hour($pjt_code, $lead_id)
	{
		if(!empty($lead_id))
		$getActDate = $this->get_quote_data($lead_id);
		
		if(!empty($getActDate[0]['date_created'])) {
			$start_date = date('Y-m-d', strtotime($getActDate[0]['date_created']));
		} else {
			$start_date = '0000-00-00';
		}
		
		$timesheet_db = $this->load->database('timesheet', TRUE); 
		
		$sql = "SELECT a.proj_id, sum(t.duration)/60 as total_Hour "; 
		$sql .=" FROM ".$timesheet_db->dbprefix('times')." AS t ";
		$sql .=" JOIN ".$timesheet_db->dbprefix('project')." AS p";
		$sql .=" JOIN ".$timesheet_db->dbprefix('task')." AS tsk";
		$sql .=" LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid = a.username ";
		$sql .=" LEFT JOIN ".$timesheet_db->dbprefix('billrate')." as brt ON a.rate_id=brt.rate_id ";
		$sql .=" WHERE (p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id AND tsk.task_id = t.task_id) ";
		$sql .=" AND (t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW()) GROUP BY a.proj_id ";
		// echo $sql; #exit;
		$query=$timesheet_db->query($sql);
		return $query->row_array();
	} */
	
	/* public function get_project_cost($pjt_code, $lead_id)
	{
		if(!empty($lead_id))
		$getActDate = $this->get_quote_data($lead_id);
		
		if(!empty($getActDate[0]['date_created'])) {
			$start_date = date('Y-m-d',strtotime($getActDate[0]['date_created']));
		} else {
			$start_date = '0000-00-00';
		}
		
		$timesheet_db = $this->load->database('timesheet', TRUE);
		
		$sql = "SELECT t.uid AS Resources, sum(t.duration)/60 as total_hour ";  
		$sql .= " FROM ".$timesheet_db->dbprefix('times')." AS t ";
		$sql .= " JOIN ".$timesheet_db->dbprefix('project')." AS p ";
		$sql .= " JOIN ".$timesheet_db->dbprefix('task')." AS tsk" ;
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid=a.username " ;
		$sql .= " WHERE (p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id AND tsk.task_id = t.task_id) ";
		$sql .= " AND (t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW()) GROUP BY t.uid";
		//echo $sql;
		$query=$timesheet_db->query($sql);
		return $query->result_array();
	} */
	
	//Displaying in Project Dashboard
	/* public function get_timesheet_hours($pjt_code, $lead_id)
	{
		if(!empty($lead_id))
		$getActDate = $this->get_quote_data($lead_id);
		
		if(!empty($getActDate[0]['date_created'])) {
			$start_date = date('Y-m-d',strtotime($getActDate[0]['date_created']));
		} else {
			$start_date = '0000-00-00';
		}
	
		$timesheet_db = $this->load->database('timesheet', TRUE);
		//$id='315';
		$sql = "SELECT t.proj_id, sum(t.duration)/60 as total_hour, a.username, ";
		$sql .= " SUM(CASE WHEN t.resoursetype='Billable' AND ((t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/60 as 'billable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Non-Billable' AND ((t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/60 as 'nonbillable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Internal' AND ((t.start_time > '".$start_date."') AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/60 as 'internal'";
		$sql .= " FROM ".$timesheet_db->dbprefix('times')." AS t";
		$sql .= " JOIN ".$timesheet_db->dbprefix('project')." AS p";
		$sql .= " JOIN ".$timesheet_db->dbprefix('task')." AS tsk";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid = a.username";
		$sql .= " WHERE (p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id AND tsk.task_id = t.task_id) GROUP BY t.proj_id";
		// echo $sql; exit;
		$query = $timesheet_db->query($sql);
		return $query->row();
	} */
	
	//Get the latest cost from the timesheet db.
	public function get_latest_cost($username) {
		$timesheet_db = $this->load->database('timesheet', TRUE);
		$sql = "SELECT ROUND((uc.direct_cost+uc.overheads_cost), 2) as cost FROM ".$timesheet_db->dbprefix('user_cost')." AS uc WHERE uc.employee_id = (SELECT u.emp_id FROM ".$timesheet_db->dbprefix('user')." AS u WHERE u.username='".$username."') ORDER BY uc.year DESC, uc.month DESC LIMIT 0,1";
		$query = $timesheet_db->query($sql);
		return $query->row_array();
	}
	
	public function get_services() {
		$this->db->select('sid, services');
	    $this->db->from($this->cfg['dbpref'] . 'lead_services');
	    $this->db->where("status", '1');
		$this->db->order_by("sid", "asc");
	    $services = $this->db->get();
	    $services =  $services->result_array();
	    return $services;
	}
	
	//get milestone details for the project.
	public function get_milestone_terms($id)
	{
    	$this->db->select('ms.milestoneid ,ms.milestone_name, ms.ms_plan_st_date, ms.ms_plan_end_date, ms.ms_act_st_date,  ms.ms_act_end_date, ms.ms_effort, ms.ms_percent, ms.milestone_status');
		$this->db->from($this->cfg['dbpref'].'milestones as ms');
    	$this->db->where('ms.jobid_fk', $id);
    	$this->db->order_by('ms.milestoneid');
		$results = $this->db->get();
        return $results->result_array();
    }
	
	//get the milestone term details.
	function get_milestone_term_det($msid, $jobid)
	{
		$wh_condn = array('milestoneid' => $msid, 'jobid_fk' => $jobid);
		$this->db->select('ms.milestone_name, ms.ms_plan_st_date, ms.ms_plan_end_date, ms.ms_act_st_date, ms.ms_act_end_date, ms.ms_effort, ms.ms_percent, ms.milestone_status');
		$this->db->from($this->cfg['dbpref'].'milestones as ms');
		$this->db->where($wh_condn);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	/*
	 *@Method get_project_meter_status
	 *@Param lead id
	 */
	function get_project_meter_status($jobid)
	{
		$wh_condn = array('jobid_fk' => $jobid);
		$this->db->select_avg('ms_effort');
		$this->db->select_avg('actual_effort');
		$this->db->from($this->cfg['dbpref'].'milestones');
		$this->db->where($wh_condn);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	/*
	 *@Method get_attached_files
	 *@Param expect id
	 */
	function get_attached_files($eid)
	{
		$wh_condn = array('expectid' => $eid);
		$this->db->select('lf.lead_files_name, lf.file_id');
		$this->db->from($this->cfg['dbpref'].'expected_payments_attach_file as expa');
		$this->db->join($this->cfg['dbpref'].'lead_files as lf', 'lf.file_id = expa.file_id');
		$this->db->where($wh_condn);
		$query = $this->db->get();
		return $query->result_array();
	}

	/*
	 *@Method get_attached_files
	 *@Param expect id
	 */
	function get_oc_attached_files($oc_id)
	{
		$wh_condn = array('other_cost_id' => $oc_id);
		$this->db->select('lf.lead_files_name, lf.file_id');
		$this->db->from($this->cfg['dbpref'].'other_cost_attach_file as ocf');
		$this->db->join($this->cfg['dbpref'].'lead_files as lf', 'lf.file_id = ocf.file_id');
		$this->db->where($wh_condn);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/*
	 *@Database timesheet
	 *@method get_billing_types
	 *@Use Get billing categories
	 *@Author eNoah - Mani.S
	 */
	public function get_billing_types()
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);		
		$timesheet_db->select('*');
		$timesheet_db->from($timesheet_db->dbprefix('bill_categories'));		
		$query = $timesheet_db->get();
		return $query->result_array();		
	}
	
	/*
	 *@Database timesheet
	 *@method get_timesheet_project_types
	 *@Use Get Timesheet project types
	 *@Author eNoah - Mani.S
	 */
	public function get_timesheet_project_types()
	{
		$this->db->select('id as project_type_id,project_billing_type as project_type_name');
		$this->db->where('status',1);
    	$this->db->order_by('id',"asc");
		$query = $this->db->get($this->cfg['dbpref'] . 'project_billing_type');
		return $query->result_array();
	}
	
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
	
	public function get_invoice_total($lead_id)
	{
		$this->db->select("SUM(amount) as invoice_amount,SUM(tax_price) as tax_amount");
		$this->db->group_by("jobid_fk");
		$qry = $this->db->get_where($this->cfg['dbpref']."expected_payments", array("jobid_fk" => $lead_id,"invoice_status" => 1));
		if($qry->num_rows()>0){
			$res = $qry->row();
			return $res;
		}
		return false;
	}
	public function get_invoice_total_by_lead($lead_id_array)
	{
		$this->db->select("SUM(amount) as invoice_amount,SUM(tax_price) as tax_amount,jobid_fk");
		$this->db->group_by("jobid_fk");
		$this->db->where("invoice_status",1);
		$this->db->where_in("jobid_fk", $lead_id_array);
		$qry = $this->db->get($this->cfg['dbpref']."expected_payments");
		if($qry->num_rows()>0){
			$res = $qry->result_array();
			$amt_array=array();
			foreach($res as $result)
			{
				$amt_array[$result['jobid_fk']]=$result;
			}
			//print_r($amt_array); exit;
			return $amt_array;
		}
		return false;
	}
	
	public function get_dashboard_field($id)
	{
		$this->db->select("column_name");
		$this->db->from($this->cfg['dbpref'].'project_dashboard_fields');
		$this->db->where('user_id', $id);
		$this->db->order_by('column_order', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getOtherCost($id)
	{
		$this->db->select("id, description, cost_incurred_date, currency_type, value");
		$this->db->from($this->cfg['dbpref'].'project_other_cost');
		$this->db->where('project_id', $id);
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_other_cost_by_all_lead($lead_id_array)
	{
		$this->db->select("id, description, cost_incurred_date, currency_type, value,project_id");
		$this->db->from($this->cfg['dbpref'].'project_other_cost');
		$this->db->where_in('project_id', $lead_id_array);
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		$data=$query->result_array();
		if(!empty($data))
		{
			$other_cost_array=array();
			foreach($data as $row)
			{
				$other_cost_array[$row['project_id']][] =$row;
			}
			
			//echo "<pre>";print_r($other_cost_array); exit;
			return $other_cost_array;
		}
		
	}
	
	function get_data_by_id($table, $wh_condn) {
		$this->db->where($wh_condn);
		$user = $this->db->get($this->cfg['dbpref'] . $table);
		return $user->row_array();
	}
	public function getTaskCategoryList($id="")
	{
		$this->db->select('id, task_category, status');
		$this->db->where('status', 1);
		if(!empty($id))
		{
			$this->db->where('id', $id);
		}
		$list_users = $this->db->get($this->cfg['dbpref'] . 'task_category');
		return $list_users->result_array();
	}
	
	public function ListActiveprojects()
	{
		$this->db->select('lead_id,lead_title');
		$this->db->where('lead_status', 4);
		$this->db->where('pjt_status', 1);
		$list_projects = $this->db->get($this->cfg['dbpref'] . 'leads');
		return $list_projects->result_array();

	}
	function get_user_email_templates($user_id)
	{
		$this->db->select('*');
		$this->db->where('user_id',$user_id);
		$this->db->where('is_default',0);
		$this->db->from($this->cfg['dbpref'].'custom_email_template');
		$query = $this->db->get();
		$templt =  $query->result_array();
		return $templt;
	}
	function get_user_email_signatures($user_id)
	{
		$this->db->select('*');
		$this->db->where('user_id',$user_id);
		$this->db->from($this->cfg['dbpref'].'signatures');
		$query = $this->db->get();
		$templt =  $query->result_array();
		return $templt;
	}
   function get_template_content($temp_id)
	{
		$this->db->select('temp_content');
		$this->db->where('temp_id',$temp_id);
		$this->db->from($this->cfg['dbpref'].'custom_email_template');
		$query = $this->db->get();
		$templt =  $query->row_array();
		return $templt;
	}
  function get_signature_content($sign_id)
	{
		$this->db->select('sign_content');
		$this->db->where('sign_id',$sign_id);
		$this->db->from($this->cfg['dbpref'].'signatures');
		$query = $this->db->get();
		$templt =  $query->row_array();
		return $templt;
	}
	function get_user_default_signature($user_id)
	{
		$whr=array('is_default'=>1,'user_id'=>$user_id);
		$this->db->select('sign_content');
		$this->db->where($whr);
		$this->db->from($this->cfg['dbpref'].'signatures');
		$query = $this->db->get();
		$templt =  $query->row_array();
		return $templt;
	}
	public function sent_email_client($data=array()) {
		
		$email_template = $data['email_data']['log_content'].'<br />'.$data['email_data']['signature'];
		$email_subject = $data['subject'];
		$this->email->from($data['from_email'],$data['from_email_name']);
	    if (!empty($data['to_mail'])) {
		$this->email->to($data['to_mail']);
		}
		if (!empty($data['cc_mail'])) {
			$this->email->cc($data['cc_mail']);
		}
		if (!empty($data['bcc_mail'])) {
			$this->email->bcc($data['bcc_mail']);
		} 
		$this->email->subject($email_subject);
		$this->email->message($email_template);
		
		if(!empty($data['attach'])) {
			$file_path = UPLOAD_PATH.'files/'.$data['job_id'].'/';
			foreach ($data['attach'] as $attach){
				$this->email->attach($file_path.$attach['lead_files_name']);
			}
		}
		if(!empty($data['external_attach'])) {
			$file_path = FCPATH.'crm_data/invoices/';
			foreach ($data['external_attach'] as $attach){
				$this->email->attach($file_path.$attach['file_name']);
			}
		}
		if(!empty($data['attachments'])) {
			foreach ($data['attachments'] as $att_row){
				$this->email->attach($att_row);
			}
		}		
	
		if($this->email->send()) { 
			return true;
		} else {
			return false;
		}
	}
	public function get_project_calculation($proj_id)
	{
		$result = $this->db->query('select * from crm_project_calculation where pjt_id=?',array($proj_id));
		return $result->row_array();
	}
	public function get_practice_id_year()
	{
		
		$this->db->select('practice_id,practice_max_hours,financial_year');
		$this->db->from($this->cfg['dbpref'].'practice_max_hours_history');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		$data =  $query->result_array();
		if(!empty($data))
		{
			$id_year_arr =array();
			foreach($data as $row)
			{
				$id_year_arr[$row['practice_id']][$row['financial_year']] = $row['practice_max_hours'];
			}
			return $id_year_arr;
		}
		
	}
	public function get_practice_id()
	{
		$this->db->select('practice_id,practice_max_hours');
		$this->db->from($this->cfg['dbpref'].'practice_max_hours_history');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		$data =  $query->result_array();
		if(!empty($data))
		{
			$id_arr =array();
			foreach($data as $row)
			{
				$id_arr[$row['practice_id']] = $row['practice_max_hours'];
			}
			
			return $id_arr;
		}
	}
}
?>