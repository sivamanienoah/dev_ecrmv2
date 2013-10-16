<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome_model extends crm_model {
    
    public function __construct()
    {
		parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode('","', $this->stg);
    }
    
    /**
     * get the lead details
     */
    public function get_lead($leadid, $belong_to = FALSE)
    {
		$owner_only = ($belong_to == FALSE) ? '' : " AND `belong_to` = '" . mysql_real_escape_string($belong_to) . "' ";
        $sql = "SELECT *
					FROM `{$this->cfg['dbpref']}leads`, `{$this->cfg['dbpref']}customers`
					WHERE `custid` = `custid_fk` AND `leadid` = ? {$owner_only} LIMIT 1";
			
		$q = $this->db->query($sql, array($leadid));
        
        if ($q->num_rows() > 0)
        {
            $result = $q->result_array();
            return $result[0];
        }
        else
        {
            return FALSE;
        }
    }
    
    public function get_job($filter = array())
    {
        if (count($filter))
        {
            $where  = '';
            $bind = array();
            
            foreach ($filter as $k => $v)
            {
                $where .= "AND `{$k}` = ? ";
                $bind[] = $v;
            }
            
            $sql = "SELECT *
                    FROM `".$this->cfg['dbpref']."jobs`, `".$this->cfg['dbpref']."customers`
                    WHERE `custid` = `custid_fk`
                    {$where}
                    LIMIT 1";
                    
            $q = $this->db->query($sql, $bind);
            
            if ($q->num_rows() > 0)
            {
                $data = $q->result_array();
                return $data[0];
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
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
                    
                    $data['job_files_html'] .= '<a href="' . str_replace($fcpath, '', $jf) . '" onclick="window.open(this.href); return false;">' . str_replace($f_dir, '', $jf) . '</a> <span>' . $out . '</span>';
		    $data['job_files_html'] .='</li>';
                }
            }
        }
        
        return $data['job_files_html'];
    }
    public function get_query_files_list($jobid)
    {
		
        $data['query_files1_html'] = '';       
		$query_tab = "SELECT lq.job_id, us.first_name,us.last_name, lq.query_msg, lq.query_id, lq.query_file_name, lq.query_sent_date, lq.replay_query 
		FROM ".$this->cfg['dbpref']."lead_query as lq
		LEFT JOIN ".$this->cfg['dbpref']."users as us ON us.userid= lq.user_id WHERE lq.job_id=".$jobid." ORDER BY lq.query_sent_date DESC";
		
		$results = $this->db->query($query_tab);
		$results = $results->result_array();
		$path = 'vps_data/query/' . $jobid. '/';
		
		foreach($results as $result) {	
			if($result['replay_query'] == 0) {
			$class = "Raised";
			} else  {
			$class = "Replied";
			}
			
			if($result['query_file_name']== 'File Not Attached') {
				$fname = "File Not Attached";
			} else {
				$fname = '<a href="'.$path.$result['query_file_name'].'" onclick="window.open(this.href); return false;">'.$result['query_file_name'].'</a>';
			}
			
			$data['query_files1_html'] .='<tr><td>
			<table border="0" cellpadding="5" cellspacing="5" class="task-list-item" id="task-table-15">
						<tbody><tr>
							<td valign="top" width="80">
								Query '.$class.'
							</td>
							<td colspan="3" class="task">
								'.$result['query_msg'].'
							</td>
						</tr>
						<tr>
							<td>
								Date & Time
							</td>
							<td class="item user-name" rel="59" width="100">
								'.$result['query_sent_date'].'
							</td>
							<td width="80">
								'.$class.' By 
							</td>
							<td class="item hours-mins" rel="4:0">
								'.$result['first_name'].' '.$result['last_name'].'
							</td>
						</tr>	
						<tr>
							<td colspan="1" valign="top">
									File Name		
							</td>
							<td colspan="3">
							'.$fname.'
							</td>
						</tr>
						<tr>
						<td	colspan="4" valign="top">
							<input type="button" class="positive" style="float:right;cursor:pointer;" id="replay" onclick="getReplyForm('.$result['query_id'].')" value="Reply" />
						</td>	
						</tr>
					</tbody></table>
			</td></tr>
			';
	   }
		  
        
        return $data['query_files1_html'];
    }
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
	
	/**
	 * $type 1 = web dev, 2 = web design, 3 = graphic design
	 */
	// unwanted function
	public function get_qc_list($type = 1)
	{
		$q = $this->db->get_where($this->cfg['dbpref'].'quality_checklist', array('qc_type' => $type));
		return $q->result_array();
	}
    // unwanted function
	public function get_qc_complete_status($jobid, $type = 1)
	{
		$this->db->limit(1);
		$this->db->order_by('date', 'desc');
		$q = $this->db->get_where($this->cfg['dbpref'].'quality_control', array('jobid_fk' => $jobid, 'qc_type' => $type));
		
		if ($q->num_rows() > 0)
		{
			$data = $q->row();
			if ($data->is_complete == 1)
			{
				return $data->id;
			}
		}
		
		return FALSE;
	}
	// unwanted function
	public function unset_qc_complete_status($qcid)
	{
		$this->db->where('id', $qcid);
		return $this->db->update($this->cfg['dbpref'].'quality_control', array('is_complete' => 0));
	}
	// unwanted function
	public function get_qc_history($jobid, $type = 1)
	{
		$this->db->order_by('date', 'desc');
		$q = $this->db->get_where($this->cfg['dbpref'].'quality_control', array('jobid_fk' => $jobid, 'qc_type' => $type));
		return $q->result_array();
	}
	
	public function get_lead_stage()
	{
	    $this->db->select('*');
	    $this->db->from($this->cfg['dbpref'].'lead_stage');
	    // $this->db->limit(13);
		$this->db->where("status",1);
		$this->db->order_by("sequence", "asc");
	    $lead_stage = $this->db->get();
	    $leads = $lead_stage->result_array();
	    // echo $this->db->last_query(); exit;
	    return $leads;
	}
	//unwanted function
	public function get_lead_stage_projects()
	{
		$this->db->select('sequence, lead_stage_id');
		$this->db->from($this->cfg['dbpref'] . 'lead_stage');
		$this->db->where("status",1);
		// $this->db->where("is_sale", 1);
		$ls = $this->db->get();
		$res = $ls->row_array();
		
		$this->db->select('*');
	    $this->db->from($this->cfg['dbpref'] . 'lead_stage');
	    // $this->db->limit(13);
		$this->db->where("status",1);
		$this->db->where('lead_stage_id >=', $res['lead_stage_id']);
		$this->db->order_by("sequence", "asc");
	    $pjt_stg = $this->db->get();
	    $pjts = $pjt_stg->result_array();
	    return $pjts;
	}
	
	// Get the Lead Status History
	public function get_lead_stat_history($id)
	{
	    $this->db->select('lsh.dateofchange, us.first_name, us.last_name, ls.lead_stage_name');
	    $this->db->from($this->cfg['dbpref'].'lead_stage_history lsh');
	    $this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = lsh.changed_status', 'LEFT');
	    $this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = lsh.modified_by', 'LEFT');
		$this->db->where("jobid", $id);
		$this->db->order_by('dateofchange', 'desc');
	    $lead_stg_his = $this->db->get();
	    $lead_sh=  $lead_stg_his->result_array();
	    // echo $this->db->last_query(); exit;
	    return $lead_sh;
	}
	
	//unwanted function
	public function get_lead_stage_pjt()
	{
	    $this->db->select('*');
	    $this->db->from($this->cfg['dbpref'] . 'lead_stage');
	    // $jobStatus = array(13,14,15,16);
		$jobStatus = $this->pjt_stg;
	    $this->db->where_in('lead_stage_id', $jobStatus);
	    $lead_stage_pjt = $this->db->get();
	    $leadsjob =  $lead_stage_pjt->result_array();
	    return $leadsjob;
	}
	
	
	public function get_customers()
	{
	    $this->db->select('custid, first_name, last_name, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers');
		$this->db->order_by("first_name", "asc");
	    $customers = $this->db->get();
	    $customers=  $customers->result_array();
	    return $customers;
	}
	
	public function get_jobid($jid, $uid)
	{
	    $res = $this->db->query("SELECT lead_assign, assigned_to, belong_to FROM `".$this->cfg['dbpref']."jobs` WHERE (jobid = '".$jid."' && (lead_assign = '".$uid."' || assigned_to = '".$uid."' || belong_to = '".$uid."'))");
	    $res1 = $res->result_array();
		if (empty($res1)) {
			$chge_access = 0;
		}
		else {
			$chge_access = 1;
		}
		return $chge_access;
	}
	
	public function get_list_users($jid)
	{
	    $query = $this->db->query("SELECT lead_assign, assigned_to, belong_to FROM `".$this->cfg['dbpref']."jobs` WHERE jobid = '".$jid."' ");
	    $list_users = $query->row_array();
		return $list_users;
	}
	
	public function get_contract_users($jid)
	{
	    $query = $this->db->query("SELECT userid_fk FROM `".$this->cfg['dbpref']."contract_jobs` WHERE jobid_fk = '".$jid."' ");
	    $contract_users = $query->result_array();
		return $contract_users;
	}
	public function get_customers_id()
	{
		$this->db->select('custid');
		$this->db->from($this->cfg['dbpref']. 'customers');
        $customers = $this->db->get();
        $customers =  $customers->result_array();
		return $customers;
	}
	
	public function get_filter_results($stage, $customer, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $keyword)
	{
		$userdata = $this->session->userdata('logged_in_user');
		$stage = explode(',',$stage);
		$customer = explode(',',$customer);
		$worth = explode('-',$worth);		
		$owner = explode(',',$owner);
		$leadassignee = explode(',',$leadassignee);
		$regionname = explode(',',$regionname);
		$countryname = explode(',',$countryname);
		$statename = explode(',',$statename);
		$locname = explode(',',$locname);
		
		if ($this->userdata['role_id'] == 1 || $this->userdata['level'] == 1 || $this->userdata['role_id'] == 2) {
			$this->db->select('j.jobid, j.invoice_no, j.job_title, j.lead_source, j.job_status, j.date_created, j.date_modified, j.belong_to,
			j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.lead_assign, j.proposal_expected_date,
			j.proposal_sent_date, c.first_name, c.last_name, c.company, rg.region_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
			us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
			$this->db->from($this->cfg['dbpref']. 'jobs as j');
			$this->db->where('j.jobid != "null" AND j.job_status IN ("'.$this->stages.'")');
			$this->db->where('j.pjt_status', 0);
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');		
			
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
			$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
			$this->db->join($this->cfg['dbpref'] . 'users as ub', 'ub.userid = j.belong_to');
			$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = c.add1_region');
			$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.job_status', 'LEFT');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			
			
			if($stage[0] != 'null' && $stage[0] != 'all') {		
				$this->db->where_in('j.job_status',$stage); 
			}
			
			if($customer[0] != 'null' && $customer[0] != 'all'){		
				$this->db->where_in('j.custid_fk',$customer); 
			}
			if($worth[0] != 'null' && $worth[0] != 'all'){	
				if($worth[1] == 'above')
				$this->db->where('j.expect_worth_amount >= '.$worth['0']);	
				else
				$this->db->where('j.expect_worth_amount BETWEEN '.$worth['0'].' AND '.$worth['1']);	
			}
			if($owner[0] != 'null' && $owner[0] != 'all'){
				$this->db->where_in('j.belong_to',$owner); 
			}
			if($leadassignee[0] != 'null' && $leadassignee[0] != 'all'){		
				$this->db->where_in('j.lead_assign', $leadassignee);
			}
			if($regionname[0] != 'null' && $regionname[0] != 'all'){
				$this->db->where_in('c.add1_region', $regionname);
			}
			if($countryname[0] != 'null' && $countryname[0] != 'all'){
				$this->db->where_in('c.add1_country', $countryname);
			}
			if($statename[0] != 'null' && $statename[0] != 'all'){	
				$this->db->where_in('c.add1_state', $statename);
			}
			if($locname[0] != 'null' && $locname[0] != 'all'){	
				$this->db->where_in('c.add1_location', $locname);
			}
			
			if($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null'){		
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.job_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}
			$this->db->order_by("j.jobid", "desc");
			
		}
		else { 
			$curusid = $this->session->userdata['logged_in_user']['userid'];
			
			$this->db->select('j.jobid, j.invoice_no, j.job_title, j.lead_source, j.job_status, j.date_created, j.date_modified, j.belong_to,
			j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.lead_assign, j.proposal_expected_date,
			j.proposal_sent_date, c.first_name, c.last_name, c.company, rg.region_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
			us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
			$this->db->from($this->cfg['dbpref'] . 'jobs as j');
			
			$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = j.custid_fk');		
			$this->db->join($this->cfg['dbpref'].'users as u', 'u.userid = j.lead_assign');
			$this->db->join($this->cfg['dbpref'].'users as us', 'us.userid = j.modified_by');
			$this->db->join($this->cfg['dbpref'].'users as ub', 'ub.userid = j.belong_to');
			$this->db->join($this->cfg['dbpref'].'region as rg', 'rg.regionid = c.add1_region');
			$this->db->join($this->cfg['dbpref'].'lead_stage as ls', 'ls.lead_stage_id = j.job_status');
			$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			
			// $this->db->where('j.jobid != "null" AND j.job_status IN (1,2,3,4,5,6,7,8,9,10,11,12)');
			$this->db->where('j.jobid != "null" AND j.job_status IN ("'.$this->stages.'") AND j.pjt_status = "0" ');
			
			if($stage[0] != 'null' && $stage[0] != 'all') {
				$this->db->where_in('j.job_status',$stage); 
				$this->db->where('j.belong_to', $curusid);
			}
			
			if($customer[0] != 'null' && $customer[0] != 'all') {		
				$this->db->where_in('j.custid_fk',$customer);				
			}
			
			if($worth[0] != 'null' && $worth[0] != 'all') {	
				if($worth[1] == 'above') {
				$this->db->where('j.expect_worth_amount >= '.$worth['0']);	
				} else {
				$this->db->where('j.expect_worth_amount BETWEEN '.$worth['0'].' AND '.$worth['1']);			
				}
			}
			if($owner[0] != 'null' && $owner[0] != 'all') {		
				$this->db->where_in('j.belong_to',$owner); 
			}
			if($leadassignee[0] != 'null' && $leadassignee[0] != 'all'){		
				$this->db->where_in('j.lead_assign', $leadassignee);
			}
			if($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null'){		
				$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.job_title LIKE '%$keyword%' OR c.company LIKE '%$keyword%' OR c.first_name LIKE '%$keyword%' OR c.last_name LIKE '%$keyword%'))";
				$this->db->where($invwhere);
			}
			$region = explode(',',$this->session->userdata['region_id']);
			$countryid = explode(',',$this->session->userdata['countryid']);
			$stateid = explode(',',$this->session->userdata['stateid']);
			$locationid = explode(',',$this->session->userdata['locationid']);

			if ( ($stage[0] == 'null' || $stage[0] == 'all') && ($customer[0] == 'null' || $customer[0] == 'all') && ($worth[0] == 'null' || $worth[0] == 'all') && ($owner[0] == 'null' || $owner[0] == 'all') && ($leadassignee[0] == 'null' || $leadassignee[0] == 'all') && ($regionname[0] == 'null' || $regionname[0] == 'all') && ($countryname[0] == 'null' || $countryname[0] == 'all') && ($statename[0] == 'null' || $statename[0] == 'all') && ($locname[0] == 'null' || $locname[0] == 'all') && $keyword == 'null' ) {
				
			$region = explode(',',$this->session->userdata['region_id']);
			$countryid = explode(',',$this->session->userdata['countryid']);
			$stateid = explode(',',$this->session->userdata['stateid']);
			$locationid = explode(',',$this->session->userdata['locationid']);

				$this->db->where_in('c.add1_region',$region);
				
				if($this->session->userdata['countryid'] != '') {
					$this->db->where_in('c.add1_country',$countryid); 
				}
				if($this->session->userdata['stateid'] != '') {
					$this->db->where_in('c.add1_state',$stateid); 
				}
				if($this->session->userdata['locationid'] != '') {
					$this->db->where_in('c.add1_location',$locationid); 
				}
				
				//or_where condition is used for to bring the lead owner leads when he creating the leads for different region.
				// $this->db->or_where('(j.belong_to = '.$curusid.' AND j.job_status IN (1,2,3,4,5,6,7,8,9,10,11,12))');
				$this->db->or_where('(j.belong_to = '.$curusid.' AND j.job_status IN ("'.$this->stages.'"))');
			}
			
			//Advanced filter
				if($regionname[0] != 'null' && $regionname[0] != 'all'){		
					$this->db->where_in('c.add1_region',$regionname);
				} else {
					$this->db->where_in('c.add1_region',$region);
				}
				if($countryname[0] != 'null' && $countryname[0] != 'all') {
					$this->db->where_in('c.add1_country', $countryname);
				} else if ((($this->userdata['level'])==3) || (($this->userdata['level'])==4) || (($this->userdata['level'])==5)) {
					$this->db->where_in('c.add1_country',$countryid);
				}
				if($statename[0] != 'null' && $statename[0] != 'all') {	
					$this->db->where_in('c.add1_state', $statename);
				} else if ((($this->userdata['level'])==4) || (($this->userdata['level'])==5)) {
					$this->db->where_in('c.add1_state',$stateid);
				}
				if($locname[0] != 'null' && $locname[0] != 'all') {	
					$this->db->where_in('c.add1_location', $locname);
				} else if (($this->userdata['level'])==5) {
					$this->db->where_in('c.add1_location',$locationid);
				}
			//Advanced filter

			$this->db->order_by("j.jobid", "desc");
			
		}
		$query = $this->db->get();
		//echo $this->db->last_query();
		
		$customers =  $query->result_array();       
		return $customers;
	}
	
	//advance search functionality for projects in home page.
	public function get_projects_results($pjtstage, $pm_acc, $cust, $keyword)
	{
		$userdata = $this->session->userdata('logged_in_user');
		//echo "<pre>"; print_r($userdata);
		$stage = explode(',',$pjtstage);
		$customer = explode(',',$cust);
		$pm = explode(',',$pm_acc);
		//print_r($customer); exit;
		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1')) {
			$this->db->select('j.jobid, j.invoice_no, j.job_title, j.job_status, j.pjt_id, j.assigned_to, j.date_start, j.date_due, j.complete_status, j.pjt_status, c.first_name as cfname, c.last_name as clname, c.company, u.first_name as fnm, u.last_name as lnm');
			$this->db->from($this->cfg['dbpref'] . 'jobs as j');
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');		
			$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.assigned_to' , "LEFT");
			
			if($stage[0] != 'null'){		
				// $this->db->where_in('j.job_status',$stage);
				$this->db->where("j.lead_status", 4);
				$this->db->where_in("j.pjt_status", $stage);
			} else {
				// $this->db->where("j.jobid != 'null' AND j.job_status IN ('".$this->pjt_stages."')");
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
			$sqlcj = "SELECT jobid_fk as jobid FROM `".$this->cfg['dbpref']."contract_jobs` WHERE `userid_fk` = '".$varSessionId."'";
			$rowscj = $this->db->query($sqlcj);
			$data['jobids'] = $rowscj->result_array();

			//Fetching Project Manager, Lead Assigned to & Lead owner jobids.
			$sqlJobs = "SELECT jobid FROM `".$this->cfg['dbpref']."jobs` WHERE (`assigned_to` = '".$varSessionId."' OR `lead_assign` = '".$varSessionId."' OR `belong_to` = '".$varSessionId."') AND lead_status IN ('4') AND pjt_status !='0' ";
			$rowsJobs = $this->db->query($sqlJobs);
			//echo $this->db->last_query();
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
		// echo $this->db->last_query();
		
		$pjts =  $query->result_array();		
		return $pjts;
	}
	
	
	
	public function assign_lists($stage, $customer, $worth, $owner, $keyword)
	{
		$userdata = $this->session->userdata('logged_in_user');
		 //print_r($userdata['userid']);
		 $this->db->select('j.jobid, j.invoice_no, j.job_title, j.lead_source, j.job_status, j.date_created, j.date_modified, j.belong_to,
		j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.proposal_expected_date,
		j.proposal_sent_date, c.first_name, c.last_name, c.company, rg.region_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
		us.last_name as usslname, ls.lead_stage_name,ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'] . 'customers as c');		
		$this->db->join($this->cfg['dbpref'] . 'jobs as j', 'j.custid_fk = c.custid AND j.jobid != "null"');		
		$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
		$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
		$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = c.add1_region');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.job_status');
		$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id ');
		
		$this->db->where('j.lead_assign', $userdata['userid']);
		
		$region     = @explode(',',$this->session->userdata['region_id']);	
		$countryid  = @explode(',',$this->session->userdata['countryid']);
		$stateid    = @explode(',',$this->session->userdata['stateid']);
		$locationid = @explode(',',$this->session->userdata['locationid']);
		
		
		$this->db->where_in('c.add1_region',$region); 
		if($this->session->userdata['countryid'] != '') {
			$this->db->where_in('c.add1_country',$countryid); 
		}
		if($this->session->userdata['stateid'] != '') {
			$this->db->where_in('c.add1_state',$stateid); 
		}
		if($this->session->userdata['locationid'] != '') {
			$this->db->where_in('c.add1_location',$locationid); 
		}
		 
		 $query = $this->db->get();
		 
		 $customers =  $query->result_array();       
		 return $customers;
	}
	
	//Payment Milestone edit functionality.
	function get_paymentDet($eid, $jid) {
		$query = $this->db->get_where($this->cfg['dbpref'].'expected_payments', array('expectid' => $eid, 'jobid_fk' => $jid ));
		return $query->row_array();
	}
	
	function Del_paymentDet($eid, $jid) {
		$query = $this->db->query("select received from ".$this->cfg['dbpref']."expected_payments where expectid = '".$eid."' and jobid_fk = '".$jid."'");
		$res_status = $query->row_array();
		if ($res_status['received'] == 0){
			//mychanges
			$jsql = $this->db->query("select expect_worth_id from ".$this->cfg['dbpref']."jobs where jobid='$jid'");
			$jres = $jsql->result();
			$worthid = $jres[0]->expect_worth_id;
			$expect_worth = $this->db->query("select expect_worth_name from ".$this->cfg['dbpref']."expect_worth where expect_worth_id='$worthid'");
			$eres = $expect_worth->result();
			$symbol = $eres[0]->expect_worth_name;		
			
			$userdata = $this->session->userdata('logged_in_user'); 
			$userid=$userdata['userid'];
			$query = $this->db->get_where($this->cfg['dbpref'].'expected_payments', array('expectid' => $eid, 'jobid_fk' => $jid ));
			$get = $query->row_array();		
			$milename = $get['project_milestone_name'];
			$amount = $get['amount'];
			$expectdate = date('Y-m-d',strtotime($get['expected_date']));	
			$filename = 'Project Milestone Name: '.$milename.'  Amount: '.$symbol.' '.$amount.'  Expected Date: '.$expectdate; 
			
			$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs)
			VALUES('".$jid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is deleted.' ,'".$filename."')";                 
			$qlogs = $this->db->query($logs);
		}
		$this->db->delete($this->cfg['dbpref'].'expected_payments', array('expectid' => $eid, 'jobid_fk' => $jid, 'received' => 0));
		return $this->db->affected_rows();
	}
	
	function get_receivedpaymentDet($pdid, $jid) {
		$query = $this->db->get_where($this->cfg['dbpref'].'deposits', array('depositid' => $pdid, 'jobid_fk' => $jid ));
		return $query->row_array();
	}
	
	function Del_receivedPaymentDet($pdid, $jid, $map) {
		//mychanges
		$jsql = $this->db->query("select expect_worth_id from ".$this->cfg['dbpref']."jobs where jobid='$jid'");
		$jres = $jsql->result();
		$worthid = $jres[0]->expect_worth_id;
		$expect_worth = $this->db->query("select expect_worth_name from ".$this->cfg['dbpref']."expect_worth where expect_worth_id='$worthid'");
		$eres = $expect_worth->result();
		$symbol = $eres[0]->expect_worth_name;		
		
		$userdata = $this->session->userdata('logged_in_user'); 
		$userid=$userdata['userid'];
		$query = $this->db->get_where($this->cfg['dbpref'].'deposits', array('depositid' => $pdid, 'jobid_fk' => $jid ));
		$get = $query->row_array();		
		$milename = $get['invoice_no'];
		$amount = $get['amount'];
		$map_term = $get['map_term'];
		$expectdate = date('Y-m-d',strtotime($get['deposit_date']));	
		$filename = 'Invoice No: '.$milename.'  Amount: '.$symbol.' '.$amount.'  Deposit Date: '.$expectdate.' Map Term: '.$map_term; 
		//die();

		$delete_logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs)
		VALUES('".$jid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is deleted.' ,'".$filename."')";  

        $qlogs = $this->db->query($delete_logs);

		//delete query for deleting received payments.
		$this->db->delete($this->cfg['dbpref'].'deposits', array('depositid' => $pdid, 'jobid_fk' => $jid, 'payment_received' => 1)); 
		$stat = $this->db->affected_rows();
		
		//for status setting.
		$statusQuery = $this->db->query("select sum(amount) as tot_amt from ".$this->cfg['dbpref']."deposits where jobid_fk = '".$jid."' AND map_term = '".$map."' ");
		$payment_status = $statusQuery->row_array();

		$statusQueryExpect = $this->db->query("select amount from ".$this->cfg['dbpref']."expected_payments where jobid_fk = '".$jid."' AND expectid = '".$map."' ");
		$payment_status_expect = $statusQueryExpect->row_array();

		$query = $this->db->query("select received from ".$this->cfg['dbpref']."expected_payments where expectid = '".$map."' and jobid_fk = '".$jid."'");
		$rec = $query->row_array();
		
		if ($rec['received'] == 2) { //echo "rec 2 " . $payment_status['tot_amt'];
			if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 1));
			} else if ($payment_status['tot_amt'] == 'null' || $payment_status['tot_amt'] == '') {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 0));
			} else {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 2));
			}
		} else if ($rec['received'] == 1) { //echo "rec 1 " . $payment_status['tot_amt'];
			if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 1));
			} else if ($payment_status['tot_amt'] == 'null' || $payment_status['tot_amt'] == '') {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 0));
			} else {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 2));
			}
		} else { //echo "rec else " . $payment_status['tot_amt'];
			if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 1));
			} else if ($payment_status['tot_amt'] == 'null' || $payment_status['tot_amt'] == '') {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 0));
			} else {
				$this->db->where('expectid' ,$map);
				$this->db->where('jobid_fk' ,$jid);
				$this->db->update($this->cfg['dbpref'].'expected_payments', array('received' => 2));
			}
		}	
		
		return $stat;
	}
	//Payment Milestone edit functionality.- Ends here
	
	//advanced search functionality- new requirement
	function getcountry_list($val) {
		$val = explode(",", $val); //using as an array
		$userdata = $this->session->userdata('logged_in_user');	
		
		//restriction for country
		$coun_query = $this->db->query("SELECT country_id FROM ".$this->cfg['dbpref']."levels_country WHERE level_id = '".$userdata['level']."' AND user_id = '".$userdata['userid']."' ");
		$coun_details = $coun_query->result_array();
		foreach($coun_details as $coun)
		{
			$countries[] = $coun['country_id'];
		}
		$countries_ids = array_unique($countries);
		$countries_ids = (array_values($countries)); //reset the keys in the array
		
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('country_name', 'asc');
		
		$this->db->where_in('regionid', $val);
		if ($userdata['level'] == 3 || $userdata['level'] == 4 || $userdata['level'] == 5) {
			$this->db->where_in('countryid', $countries_ids);
		}
		$customers = $this->db->get($this->cfg['dbpref']. 'country');
		return $customers->result_array();	
    }
	
	//For States
	function getstate_list($val) {       
		$userdata = $this->session->userdata('logged_in_user');
		$val = explode(",", $val); //using as an array
		//restriction for state
		$ste_query = $this->db->query("SELECT state_id FROM ".$this->cfg['dbpref']."levels_state WHERE level_id = '".$userdata['level']."' AND user_id = '".$userdata['userid']."' ");
		$ste_details = $ste_query->result_array();
		foreach($ste_details as $ste)
		{
			$states[] = $ste['state_id'];
		}
		$states_ids = array_unique($states);
		$states_ids = (array_values($states)); //reset the keys in the array
		
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('state_name', 'asc');
		
		$this->db->where_in('countryid', $val);
		if ($userdata['level'] == 4 || $userdata['level'] == 5) {
			$this->db->where_in('stateid', $states_ids);
		}
		$stat = $this->db->get($this->cfg['dbpref'] . 'state');
		
		return $stat->result_array();	
    }
	
	//for locations
	function getlocation_list($val) {
		$userdata = $this->session->userdata('logged_in_user');
		$val = @explode(",", $val); //using as an array
		
		//restriction for location
		$loc_query = $this->db->query("SELECT location_id FROM ".$this->cfg['dbpref']."levels_location WHERE level_id = '".$userdata['level']."' AND user_id = '".$userdata['userid']."' ");
		$loc_details = $loc_query->result_array();
		foreach($loc_details as $loc)
		{
			$locations[] = $loc['location_id'];
		}
		$locations_ids = array_unique($locations);
		$locations_ids = (array_values($locations)); //reset the keys in the array
		
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('location_name', 'asc');
		
		$this->db->where_in('stateid', $val);
		if ($userdata['level'] == 5) {
			$this->db->where_in('locationid', $locations_ids);
		}
		$customers = $this->db->get($this->cfg['dbpref'] . 'location');
		//echo $this->db->last_query();
		return $customers->result_array();
    }
    
    //Insert new Job - Below functions are created by MAR
    function insert_job($ins) {
    	$this->db->insert($this->cfg['dbpref'] . 'jobs', $ins);
    	return $this->db->insert_id();
    }
    function update_job($insert_id, $up_args) {
    	$this->db->where('jobid', $insert_id);
		$this->db->update($this->cfg['dbpref'] . 'jobs', $up_args);
    }
    
	function get_categories() {
    	$this->db->order_by('cat_id');
		$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
		return $q->result_array();
    }
    
	function get_cat_records($id) {
		$this->db->where('item_type', $id);
		$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
		return $q->result_array();
    }
  	
    function get_package() {
    	$this->db->where('status', 'active');
		$q = $this->db->get($this->cfg['dbpref'] . 'package');
    	return $q->result_array();
    }
    
    function get_lead_sources() {
    	$this->db->where('status', 1);
		$q = $this->db->get($this->cfg['dbpref'] . 'lead_source');
		return $q->result_array();
    }
    
    function get_lead_assign($level) {
    	$this->db->select('userid', 'first_name');
    	if(!empty($level))
    	$this->db->where('level', $level);
    	$q = $this->db->get($this->cfg['dbpref'] . 'users');
    	return $q->result_array();
    }
    
    function get_expect_worths() {
    	$this->db->select('expect_worth_id', 'expect_worth_name');
    	$q = $this->db->get($this->cfg['dbpref'] . 'expect_worth');
    	return $q->result_array();
    }
    
    function get_lead_results($args, $job_status, $cnt_join, $search, $restrict) {
    	$sql = "SELECT *, LS.lead_stage_name, SUM(`".$this->cfg['dbpref']."items`.`item_price`) AS `project_cost`,
		(SELECT SUM(`amount`) FROM `".$this->cfg['dbpref']."deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
		FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J, `{$this->cfg['dbpref']}lead_stage` as LS, {$args} `{$this->cfg['dbpref']}customers` AS C
		LEFT JOIN `{$this->cfg['dbpref']}hosting` as H ON C.custid=H.custid_fk WHERE C.`custid` = J.`custid_fk` AND C.`add1_region` IN(".$this->session->userdata['region_id'].")";
		if($this->session->userdata['countryid'] != '') {
		$sql .= " AND C.`add1_country` IN(".$this->session->userdata['countryid'].")";
		}
		if($this->session->userdata['stateid'] != '') {
		$sql .= " AND C.`add1_state`  IN(".$this->session->userdata['stateid'].") ";
		}
		if($this->session->userdata['locationid'] != '') {
		$sql .= " AND C.`add1_location` IN(".$this->session->userdata['locationid'].") ";
		}
		//$sql .= "OR (J.belong_to = '".$curusid."' AND  J.job_status IN (1,2,3,4,5,6,7,8,9,10,11,12))";
		$sql .= " AND LS.lead_stage_id = J.job_status AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$cnt_join} {$search} {$restrict} 
        GROUP BY `jobid` ORDER BY `belong_to`, `date_created`";
		$rows = $this->db->query($sql);
		return $rows->result_array();
    }
    function get_leadowner_results($usid, $cnt_join1, $job_status, $cnt_join, $search, $restrict) {
    	$lead_cond = "AND J.belong_to = '".$usid."'";
    	$leadownerquery = "SELECT *, LS.lead_stage_name, SUM(`".$this->cfg['dbpref']."items`.`item_price`) AS `project_cost`,
		(SELECT SUM(`amount`) FROM `".$this->cfg['dbpref']."deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
		FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J, `{$this->cfg['dbpref']}lead_stage` as LS, {$cnt_join1} `{$this->cfg['dbpref']}customers` AS C
		LEFT JOIN `{$this->cfg['dbpref']}hosting` as H ON C.custid=H.custid_fk
		WHERE C.`custid` = J.`custid_fk` AND LS.lead_stage_id = J.job_status AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$cnt_join} {$search} {$restrict} {$lead_cond}
		GROUP BY `jobid` ORDER BY `belong_to`, `date_created`";
		$leadowner_rows = $this->db->query($leadownerquery);
		return $leadowner_rows->result_array(); 
    }
    function get_another_lead_results($cnt_join1, $job_status, $cnt_join, $search, $restrict) {
    	$sql = "SELECT *, LS.lead_stage_name, SUM(`".$this->cfg['dbpref']."items`.`item_price`) AS `project_cost`,
		(SELECT SUM(`amount`) FROM `".$this->cfg['dbpref']."deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
        FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J, `{$this->cfg['dbpref']}lead_stage` as LS, {$cnt_join1} `{$this->cfg['dbpref']}customers` AS C
		LEFT JOIN `{$this->cfg['dbpref']}hosting` as H ON C.custid=H.custid_fk WHERE C.`custid` = J.`custid_fk` AND LS.lead_stage_id = J.job_status AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$cnt_join} {$search} {$restrict} 
        GROUP BY `jobid` ORDER BY `belong_to`, `date_created`";
		$rows = $this->db->query($sql);
		echo $this->db->last_query();
		return $rows->result_array();	
    }
    
    
	function get_lead_owner($order) {
    	$this->db->select('userid,first_name');
    	if(!empty($order))
    	$this->db->order_by($order, "asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		//echo $this->db->last_query();exit; 
		return $q->result_array();
    }
    
    function get_user_byrole($role_id) {
    	$users = $this->db->get_where($this->cfg['dbpref'] . 'users',array('role_id'=>$role_id))->result_array();
    	return $users;
    }
    
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
    
    function get_contract_jobs($id) {
    	$this->db->where('jobid_fk', $id);
		$cq = $this->db->get($this->cfg['dbpref'].'contract_jobs');
		return $cq->result_array();
    }
}

?>
