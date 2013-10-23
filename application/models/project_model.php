<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project_model extends crm_model {
    
    public function __construct()
    {
		parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode('","', $this->stg);
    }
    
	
	function get_user_byrole($role_id) {
    	$users = $this->db->get_where($this->cfg['dbpref'] . 'users', array('role_id'=>$role_id))->result_array();
    	return $users;
    }
	
	function get_customers() {
	    $this->db->select('custid, first_name, last_name, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers');
		$this->db->order_by("first_name", "asc");
	    $customers = $this->db->get();
	    $customers=  $customers->result_array();
	    return $customers;
	}
	
	//advance search functionality for projects in home page.
	public function get_projects_results($pjtstage, $pm_acc, $cust, $keyword) {	
		$userdata = $this->session->userdata('logged_in_user');
		$stage = explode(',',$pjtstage);
		$customer = explode(',',$cust);
		$pm = explode(',',$pm_acc);

		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1')) {
			$this->db->select('j.jobid, j.invoice_no, j.job_title, j.job_status, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm');
			$this->db->from($this->cfg['dbpref'] . 'jobs as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');		
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			
			if($stage[0] != 'null'){		
				$this->db->where("j.lead_status", 4);
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.jobid != 'null' AND j.lead_status IN ('4') AND j.pjt_status !='0' ");
			}
			
			if($customer[0] != 'null' && $customer[0] != '0'){		
				$this->db->where_in('j.custid_fk',$customer); 
			}
			
			if($pm[0] != 'null' && $pm[0] != '0'){		
				$this->db->where_in('j.assigned_to',$pm); 
			}
			if($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null'){	
				
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.job_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}
			$this->db->order_by("j.jobid", "desc");
		}
		else {
			$varSessionId = $this->userdata['userid']; //Current Session Id.

			//Fetching Project Team Members.
			$this->db->select('jobid_fk as jobid');
			$this->db->where('userid_fk', $varSessionId);
			$rowscj = $this->db->get($this->cfg['dbpref'] . 'contract_jobs');
			$data['jobids'] = $rowscj->result_array();
			
			//Fetching Project Manager, Lead Assigned to & Lead owner jobids.
			$this->db->select('jobid');
			$this->db->where("(assigned_to = '".$varSessionId."' OR lead_assign = '".$varSessionId."' OR belong_to = '".$varSessionId."')");
			$this->db->where("lead_status", 4);
			$this->db->where("pjt_status !=", 0);
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'jobs');
			$data['jobids1'] = $rowsJobs->result_array();

			$data = array_merge_recursive($data['jobids'], $data['jobids1']);

			$res[] = 0;
			if (is_array($data) && count($data) > 0) { 
				foreach ($data as $data) {
					$res[] = $data['jobid'];
				}
			}
			$result_ids = array_unique($res);
			$curusid= $this->session->userdata['logged_in_user']['userid'];
			
			$this->db->select('j.jobid, j.invoice_no, j.job_title, j.job_status, j.pjt_id, j.assigned_to, j.complete_status,j.date_start, j.date_due, j.pjt_status, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm');
			$this->db->from($this->cfg['dbpref'] . 'customers as c');		
			$this->db->join($this->cfg['dbpref'] . 'jobs as j', 'j.custid_fk = c.custid AND j.jobid != "null"');		
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			$this->db->where_in('j.jobid', $result_ids);
			
			
			if($stage[0] != 'null') {
				$this->db->where("j.lead_status", 4);
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				$this->db->where("j.jobid != 'null' AND j.lead_status IN ('4') AND j.pjt_status !='0' ");
			}
			
			if($customer[0] != 'null' && $customer[0] != '0') {		
				$this->db->where_in('j.custid_fk',$customer);		
			}
			
			if($pm[0] != 'null' && $pm[0] != '0') {		
				$this->db->where_in('j.assigned_to',$pm); 
			}
			if($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null'){		
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.job_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}

			$this->db->order_by("j.jobid", "desc");
			
		}
		$query = $this->db->get();
		
		$pjts =  $query->result_array();		
		return $pjts;
	}
	
	//get the access for changing the project id, project manager, assigning project members
	public function get_jobid($id, $uid)
	{
		$this->db->select('lead_assign, assigned_to, belong_to');
		$this->db->where('jobid', $id);
		$this->db->where("(lead_assign = '".$uid."' || assigned_to = '".$uid."' || belong_to = '".$uid."')");
		$sql = $this->db->get($this->cfg['dbpref'] . 'jobs');
		$res1 = $sql->result_array();
		if (empty($res1)) {
			$chge_access = 0;
		}
		else {
			$chge_access = 1;
		}
		return $chge_access;
	}
	
	//get the lead or project overall details
	function get_quote_data($id) {
    	$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'customers as cus');
		$this->db->join($this->cfg['dbpref'].'jobs as jb', 'jb.custid_fk = cus.custid', 'left');
    	$this->db->join($this->cfg['dbpref'].'region as reg', 'reg.regionid = cus.add1_region', 'left');
    	$this->db->join($this->cfg['dbpref'].'country as cnty', 'cnty.countryid = cus.add1_country', 'left');
    	$this->db->join($this->cfg['dbpref'].'state as ste', 'ste.stateid = cus.add1_state', 'left');
    	$this->db->join($this->cfg['dbpref'].'location as locn ', 'locn.locationid = cus.add1_location', 'left');
    	$this->db->join($this->cfg['dbpref'].'expect_worth as exw', 'exw.expect_worth_id = jb.expect_worth_id', 'left');
    	$this->db->join($this->cfg['dbpref'].'lead_stage as ls', 'ls.lead_stage_id = jb.job_status', 'left');
    	$this->db->where('jb.jobid', $id);
		$this->db->limit(1);
		$results = $this->db->get();
        return $results->result_array();
    }
	
	//get the project assigned members
	function get_contract_jobs($id) {
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
	public function get_job_urls($jobid)
    {
        $sql = "SELECT *
                FROM `".$this->cfg['dbpref']."job_urls`
                WHERE `jobid_fk` = ?
                ORDER BY `urlid`";
        $rs = $this->db->query($sql, array($jobid));
        
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
		$this->db->where('jobid', $id);
		$list_users = $this->db->get($this->cfg['dbpref'] . 'jobs');
		return $list_users->row_array();
	}
	
	public function get_contract_users($id)
	{
		$this->db->select('userid_fk');
		$this->db->where('jobid_fk', $id);
		$contract_users = $this->db->get($this->cfg['dbpref'] . 'contract_jobs');
		return $contract_users->result_array();
	}
	
	function updt_log_view_status($id, $log) {
		$this->db->where('jobid', $id);
		return $this->db->update($this->cfg['dbpref'] . 'jobs', $log);
	}
	
	function get_logs($id) {
		$this->db->order_by('date_created', 'desc');
		$query = $this->db->get_where($this->cfg['dbpref'] . 'logs', array('jobid_fk' => $id));
		return $query->result_array();
	}
	
	function get_user_data_by_id($ld) {
		$this->db->where('userid', $ld);
		$user = $this->db->get($this->cfg['dbpref'] . 'users');
		return $user->result_array();
	}
	
	function get_users() {
    	$this->db->select('userid,first_name,level,role_id,inactive');
		$this->db->where('inactive', 0);
    	$this->db->order_by('first_name', "asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }
	
	function get_payment_terms($id) {
		$this->db->where('jobid_fk', $id);
		$this->db->order_by('expectid', 'asc');
		$payment_terms = $this->db->get($this->cfg['dbpref'] . 'expected_payments');
		return $payment_terms->result_array();
	}
	
	function get_deposits_data($id) {
		$this->db->select($this->cfg['dbpref'].'deposits.*');
		$this->db->select($this->cfg['dbpref'].'expected_payments.project_milestone_name AS payment_term');
		$this->db->from($this->cfg['dbpref'] . 'deposits');
		$this->db->where($this->cfg['dbpref'].'deposits.jobid_fk', $id);
		$this->db->join($this->cfg['dbpref'].'expected_payments', $this->cfg['dbpref'].'deposits.map_term = '.$this->cfg['dbpref'].'expected_payments.expectid', 'left');
		$this->db->order_by('depositid', 'asc');
		$deposits = $this->db->get();
		return $deposits->result_array();
	}
}

?>
