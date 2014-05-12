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
	    $this->db->select('custid, first_name, last_name, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers');
		$this->db->order_by("first_name", "asc");
	    $customers = $this->db->get();
	    $customers =  $customers->result_array();
	    return $customers;
	}

	//advance search functionality for projects in home page.
	public function get_projects_results($pjtstage, $pm_acc, $cust, $service,$keyword) {	
		
		$userdata = $this->session->userdata('logged_in_user');
		$stage = $pjtstage;
		$customer = $cust;
		$pm = $pm_acc;
		$services = $service;
	
		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1')) {
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title,j.expect_worth_amount, j.lead_stage, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status,j.estimate_hour,j.project_type,j.rag_status, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');		
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			
			if(!empty($stage)){	
				$this->db->where("j.lead_status", '4');
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status !='0' ");
			}
			
			if(!empty($customer)){		
				$this->db->where_in('j.custid_fk',$customer); 
			}
			
			if(!empty($pm)){		
				$this->db->where_in('j.assigned_to',$pm); 
			}
			
			if(!empty($services)){		
				$this->db->where_in('j.lead_service',$services); 
			}
			
			if($keyword != 'Lead No, Job Title, Name or Company' && !empty($keyword)){	
				
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}
			$this->db->order_by("j.lead_id", "desc");
		}
		else 
		{
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
			$this->db->where("pjt_status !=", 0);
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
			$curusid= $this->session->userdata['logged_in_user']['userid'];
			
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_stage, j.pjt_id, j.assigned_to, j.complete_status,j.date_start, j.date_due, j.pjt_status, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm');
			$this->db->from($this->cfg['dbpref'] . 'customers as c');		
			$this->db->join($this->cfg['dbpref'] . 'leads as j', 'j.custid_fk = c.custid AND j.lead_id != "null"');		
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->where_in('j.lead_id', $result_ids);
			
			
			if(!empty($stage)) {
				$this->db->where("j.lead_status", 4);
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status !='0' ");
			}
			
			if(!empty($customer)) {		
				$this->db->where_in('j.custid_fk',$customer);		
			}
			
			if(!empty($pm)) {		
				$this->db->where_in('j.assigned_to',$pm); 
			}
			
			if(!empty($services)){		
				$this->db->where_in('j.lead_service',$services); 
			}
			
			if($keyword != 'Project No, Project Title, Name or Company' && !empty($keyword)){		
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}

			$this->db->order_by("j.lead_id", "desc");
			
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
		$pjts =  $query->result_array();		
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
		}
		else {
			$chge_access = 1;
		}
		return $chge_access;
	}
	
	//get the access for add, edit & delete Milestones in Project Module
	public function get_ms_access($id, $uid)
	{
		$this->db->select('assigned_to');
		$this->db->where('lead_id', $id);
		$this->db->where('assigned_to', $uid);
		$sql = $this->db->get($this->cfg['dbpref'] . 'leads');
		$res1 = $sql->result_array();
		if (empty($res1)) {
			$ms_chge_access = 0;
		}
		else {
			$ms_chge_access = 1;
		}
		return $ms_chge_access;
	}
	
	//get overall details for lead or project 
	function get_quote_data($id) 
	{
    	$this->db->select('*,jbcat.services as lead_service');
		$this->db->from($this->cfg['dbpref'].'customers as cus');
		$this->db->join($this->cfg['dbpref'].'leads as jb', 'jb.custid_fk = cus.custid', 'left');
    	$this->db->join($this->cfg['dbpref'].'region as reg', 'reg.regionid = cus.add1_region', 'left');
    	$this->db->join($this->cfg['dbpref'].'country as cnty', 'cnty.countryid = cus.add1_country', 'left');
    	$this->db->join($this->cfg['dbpref'].'state as ste', 'ste.stateid = cus.add1_state', 'left');
    	$this->db->join($this->cfg['dbpref'].'location as locn ', 'locn.locationid = cus.add1_location', 'left');
    	$this->db->join($this->cfg['dbpref'].'expect_worth as exw', 'exw.expect_worth_id = jb.expect_worth_id', 'left');
    	$this->db->join($this->cfg['dbpref'].'lead_stage as ls', 'ls.lead_stage_id = jb.lead_stage', 'left');
    	$this->db->join($this->cfg['dbpref'].'lead_services as jbcat', 'jbcat.sid = jb.lead_service', 'left');
    	$this->db->where('jb.lead_id', $id);
		$this->db->limit(1);
		$results = $this->db->get();
        return $results->result_array();
    }
	
	//get the project assigned members
	function get_contract_jobs($id) 
	{
    	$this->db->where('jobid_fk', $id);
		$cq = $this->db->get($this->cfg['dbpref'].'contract_jobs');
		return $cq->result_array();
    }
	
	public function get_job_files($f_dir, $fcpath, $lead_details)
    {
		$userdata = $this->session->userdata('logged_in_user'); 
        $data['job_files_html'] = '';
        if (is_dir($f_dir))
        {
            $job_files = glob($f_dir . '*.*');
			
            if (is_array($job_files) && count($job_files))
            {
                foreach ($job_files as $jf)
                {
                    $data['job_files_html'] .= '<li>';
                     if ( $userdata['role_id'] == 1 || $lead_details['belong_to'] == $userdata['userid'] || $lead_details['lead_assign'] == $userdata['userid'] || $lead_details['assigned_to'] == $userdata['userid'] )  {  
                        $data['job_files_html'] .= '<a href="#" onclick="ajaxDeleteFile(\'/' . str_replace($fcpath, '', $jf) . '\', this); return false;" class="file-delete">delete file</a>';
						}
						
                    $fz = filesize($jf);
                    $kb = 1024;
                    $mb = 1024 * $kb;
                    if ($fz > $mb)
                    {
                      $out = round($fz/$mb, 2);
                      $out .= 'Mb';
                    }
                    else if ($fz > $kb) {
                      $out = round($fz/$kb, 2);
                      $out .= 'Kb';
                    } else {
                      $out = $fz . ' Bytes';
                    }
					
                    $data['job_files_html'] .= '<a href="crm_data/' . str_replace($fcpath, '', $jf) . '" onclick="window.open(this.href); return false;">' . str_replace($f_dir, '', $jf) . '</a> <span>' . $out . '</span>';
					$data['job_files_html'] .='</li>';
                }
            }
        }
        return $data['job_files_html'];
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
    	$this->db->select('userid, first_name, last_name, level, role_id, inactive');
		$this->db->where('inactive', 0);
    	$this->db->order_by('first_name', "asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
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
	    $this->db->select('*');
	    $this->db->from($this->cfg['dbpref'] . 'leads');
	    $this->db->where('lead_id', $id);
	    $lead_det = $this->db->get();
	    return $leads =  $lead_det->row_array();
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
	
	//get expected payment details for the project.
	public function get_expect_payment_terms($id) 
	{
    	$this->db->select('expm.expectid, expm.expected_date, expm.amount, expm.project_milestone_name, expm.received, jb.expect_worth_id, exnm.expect_worth_name');
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
		$this->db->select('expm.expectid, expm.amount, expm.expected_date, expm.received, expm.project_milestone_name, j.expect_worth_id, exnm.expect_worth_name');
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

	public function get_timesheet_data($pjt_code){
		$timesheet_db = $this->load->database('timesheet',TRUE); 
		//$id='315';
		$sql = "SELECT t.uid AS Resources, sum(t.duration)/8 as total_cost,brt.bill_rate,(sum(t.duration)/8)*brt.bill_rate as cost, ";
		$sql .= " SUM(CASE WHEN t.resoursetype='Billable' AND ((t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/8 as 'Billable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Non-Billable' AND ((t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/8 as 'Non-Billable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Internal' AND ((t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/8 as 'Internal'";
		$sql .= " FROM ".$timesheet_db->dbprefix('times')." AS t";
		$sql .= " JOIN ".$timesheet_db->dbprefix('project')." AS p";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid=a.username";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('billrate')." as brt ON a.rate_id=brt.rate_id";
		$sql .= " WHERE (p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id) GROUP BY t.uid";
		// echo $sql;
		$query=$timesheet_db->query($sql);
		return $query->result_array();
	}
	
	public function get_actual_project_hour($pjt_code){
		$timesheet_db = $this->load->database('timesheet', TRUE); 
		
		$sql = "SELECT a.proj_id, sum(t.duration)/8 as total_Hour "; 
		$sql .=" FROM ".$timesheet_db->dbprefix('times')." AS t ";
		$sql .=" JOIN ".$timesheet_db->dbprefix('project')." AS p";
		$sql .=" LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid=a.username ";
		$sql .=" LEFT JOIN ".$timesheet_db->dbprefix('billrate')." as brt ON a.rate_id=brt.rate_id ";
		$sql .=" WHERE (p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id) ";
		$sql .=" AND (t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW()) GROUP BY a.proj_id ";
		//echo $sql;
		$query=$timesheet_db->query($sql);
		return $query->row_array();
	}
	
	public function get_project_cost($pjt_code){
		$timesheet_db = $this->load->database('timesheet', TRUE);
		
		$sql = "SELECT t.uid AS Resources, sum(t.duration)/8 as total_hour, brt.bill_rate, (sum(t.duration)/8)*brt.bill_rate as cost ";  
		$sql .= " FROM ".$timesheet_db->dbprefix('times')." AS t ";
		$sql .= " JOIN ".$timesheet_db->dbprefix('project')." AS p ";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid=a.username ";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('billrate')." as brt ON a.rate_id=brt.rate_id ";
		$sql .= " WHERE (p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id) ";
		$sql .= " AND (t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW()) GROUP BY t.uid";
		//echo $sql;
		$query=$timesheet_db->query($sql);
		return $query->result_array();
	}
	
	public function get_timesheet_hours($pjt_code){
		$timesheet_db = $this->load->database('timesheet', TRUE); 
		//$id='315';
		$sql = "SELECT t.proj_id, sum(t.duration)/8 as total_hour,brt.bill_rate,(sum(t.duration)/8)*brt.bill_rate as cost, ";
		$sql .= " SUM(CASE WHEN t.resoursetype='Billable' AND ((t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/8 as 'billable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Non-Billable' AND ((t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/8 as 'nonbillable',";
		$sql .= " SUM(CASE WHEN t.resoursetype='Internal' AND ((t.start_time = '0000-00-00' OR t.start_time < NOW()) AND (t.end_time = '0000-00-00' OR t.end_time <= NOW())) THEN duration ELSE 0 END)/8 as 'internal'";
		$sql .= " FROM ".$timesheet_db->dbprefix('times')." AS t";
		$sql .= " JOIN ".$timesheet_db->dbprefix('project')." AS p";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('assignments')." as a ON t.uid=a.username";
		$sql .= " LEFT JOIN ".$timesheet_db->dbprefix('billrate')." as brt ON a.rate_id=brt.rate_id";
		$sql .= " WHERE (p.project_code = '".$pjt_code."' AND t.proj_id = p.proj_id AND a.proj_id = p.proj_id) GROUP BY t.proj_id";
		// echo $sql; exit;
		$query = $timesheet_db->query($sql);
		return $query->row();
	}
	
	public function get_services(){
		$this->db->select('sid, services');
	    $this->db->from($this->cfg['dbpref'] . 'lead_services');
	    $this->db->where("status",'1');
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
	
	//Calculate the project meter status
	function get_project_meter_status($jobid)
	{
		$wh_condn = array('jobid_fk' => $jobid);
		$this->db->select_avg('ms_percent');
		$this->db->from($this->cfg['dbpref'].'milestones');
		$this->db->where($wh_condn);
		$query = $this->db->get();
		return $query->row_array();
	}
}

?>
