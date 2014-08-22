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
	public function get_projects_results($pjtstage,$cust,$service,$practice,$keyword,$datefilter,$from_date,$to_date) {
		
		$userdata   = $this->session->userdata('logged_in_user');
		$stage 		= $pjtstage;
		$customer 	= $cust;
		$services	= $service;
		$practices	= $practice;
		$datefilter = $datefilter;
		$from_date 	= $from_date;
		$to_date	= $to_date;
	
		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1')) {
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.expect_worth_id, j.expect_worth_amount, j.actual_worth_amount, ew.expect_worth_name, j.lead_stage, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, j.estimate_hour, j.project_type, j.rag_status, j.billing_type, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm, j.actual_date_start, j.actual_date_due');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
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
			if($keyword != 'Lead No, Job Title, Name or Company' && !empty($keyword)){	
				
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}
			$this->db->order_by("j.lead_id", "desc");
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
			$curusid= $this->session->userdata['logged_in_user']['userid'];
			
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.expect_worth_id, j.expect_worth_amount, j.actual_worth_amount, ew.expect_worth_name, j.lead_stage, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, j.estimate_hour, j.project_type, j.rag_status, j.billing_type, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm');
			$this->db->from($this->cfg['dbpref'] . 'customers as c');
			$this->db->join($this->cfg['dbpref'] . 'leads as j', 'j.custid_fk = c.custid AND j.lead_id != "null"');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->where_in('j.lead_id', $result_ids);

			if(!empty($stage)) {
				$this->db->where("j.lead_status", 4);
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.lead_id != 'null' AND j.lead_status IN ('4') AND j.pjt_status = 1 ");
			}
			if(!empty($customer)) {		
				$this->db->where_in('j.custid_fk',$customer);		
			}
			if(!empty($services)){		
				$this->db->where_in('j.lead_service',$services); 
			}
			if(!empty($practices)){		
				$this->db->where_in('j.practice',$practices);
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
			if($keyword != 'Project Title, Name or Company' && !empty($keyword)){		
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}
			$this->db->order_by("j.lead_id", "desc");
			
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
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
	/* function get_contract_jobs($id) 
	{
    	$this->db->where('jobid_fk', $id);
		$cq = $this->db->get($this->cfg['dbpref'].'contract_jobs');
		return $cq->result_array();
    } */
	
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
	
	public function get_practices()
	{
    	$this->db->select('id, practices');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$query = $this->db->get($this->cfg['dbpref'] . 'practices');
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
	
	function update_practice($tbl, $updt, $condn)
	{
		return $this->db->update($this->cfg['dbpref'] . $tbl, $updt, $condn);
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
	
	public function get_timesheet_data($pjt_code, $lead_id, $bill_type, $st_date=false)
	{
		//$bill_type == 3 for view the particular month metrics data
		$start_date = $end_date = '';
		switch($bill_type){
			case 1:
				if(!empty($lead_id)) {
					$getActDate = $this->get_quote_data($lead_id);
				}
				if(!empty($getActDate[0]['date_created'])) {
					$start_date = date('Y-m-d', strtotime($getActDate[0]['date_created']));
				} else {
					$start_date = '0000-00-00';
				}
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

		$timesheet_db = $this->load->database('timesheet', TRUE);
		
		$sql = "SELECT ROUND((ct.direct_cost + ct.overheads_cost), 2) as cost, Monthname(t.start_time) as month_name, YEAR(t.start_time) as yr, u.emp_id, u.first_name, u.last_name, u.username, t.start_time AS start_time_str, t.end_time AS end_time_str, SUM((t.duration/60)) as Duration, t.resoursetype, WEEK(t.start_time) AS Week, p.project_type_id, pt.project_type_name
		FROM ".$timesheet_db->dbprefix('user')." AS u
		LEFT JOIN ".$timesheet_db->dbprefix('times')." AS t ON t.uid = u.username
		LEFT JOIN ".$timesheet_db->dbprefix('user_cost')." as ct ON ct.employee_id = u.emp_id AND ct.month=MONTH(t.start_time) AND ct.year=YEAR(t.start_time) 
		LEFT JOIN ".$timesheet_db->dbprefix('project')." as p ON p.proj_id = t.proj_id
		LEFT JOIN ".$timesheet_db->dbprefix('project_types')." as pt ON pt.project_type_id = p.project_type_id
		WHERE ((t.start_time >= '".$start_date."') AND (t.end_time <= '".$end_date."')) AND u.status='ACTIVE' AND p.project_code = '".$pjt_code."'
		GROUP BY cost, u.first_name, u.last_name, u.username, month_name, t.resoursetype
		ORDER BY yr, month_name, Week, u.first_name, u.last_name, u.username, t.resoursetype, WEEKDAY(t.start_time)";
		
		// echo $sql; #EXIT;
		$query = $timesheet_db->query($sql);
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
		
		// echo $sql; exit;
		$query = $timesheet_db->query($sql);
		$res = $query->result_array();
		
		if(count($res) > 0) {
			foreach($res as $row){
				$users[] = $row['first_name'] . ' ' .$row['last_name'];
			}
		}
		return $users;
	}
	
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
	
}
?>