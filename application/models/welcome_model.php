<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome_model extends crm_model {
    
    public function __construct()
    {
		parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode('","', $this->stg);
    }
	
	/*
	*Get the Lead Detail
	*/
	public function get_lead_detail($leadid) {
	
		$this->db->select('j.jobid, j.invoice_no, j.job_title, j.job_category, j.lead_source, j.job_status, j.date_created, j.date_modified, j.belong_to,
		j.created_by, j.expect_worth_amount, j.actual_worth_amount, j.expect_worth_id, j.division, j.lead_indicator, j.lead_status, j.lead_assign, 
		j.proposal_expected_date, j.log_view_status, j.lead_hold_reason, 
		c.*, c.first_name AS cfn, c.last_name AS cln, c.add1_region, c.add1_country, c.add1_state, c.add1_location,  rg.region_name, coun.country_name, 
		st.state_name, loc.location_name, ass.first_name as assfname, ass.last_name as asslname, us.first_name as usfname, us.last_name as usslname, 
		own.first_name as ownfname, own.last_name as ownlname, ls.lead_stage_name,ew.expect_worth_name, lsrc.lead_source_name, jbcat.category as job_category, sadiv.division_name');
		$this->db->from($this->cfg['dbpref'] . 'leads as j');
		$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');		
		$this->db->join($this->cfg['dbpref'] . 'users as ass', 'ass.userid = j.lead_assign');
		$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
		$this->db->join($this->cfg['dbpref'] . 'users as own', 'own.userid = j.belong_to');
		$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = c.add1_region');
		$this->db->join($this->cfg['dbpref'] . 'country as coun', 'coun.countryid = c.add1_country');
		$this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = c.add1_state');
		$this->db->join($this->cfg['dbpref'] . 'location as loc', 'loc.locationid = c.add1_location');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.job_status');
		$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
		$this->db->join($this->cfg['dbpref'] . 'lead_source as lsrc', 'lsrc.lead_source_id = j.lead_source');
		$this->db->join($this->cfg['dbpref'] . 'job_categories as jbcat', 'jbcat.cid = j.job_category');
		$this->db->join($this->cfg['dbpref'] . 'sales_divisions as sadiv', 'sadiv.div_id = j.division');
		$this->db->where('j.jobid = "'.$leadid.'" AND j.job_status IN ("'.$this->stages.'")');
		$this->db->where('j.pjt_status', 0);
		
		$sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    $res =  $sql->result_array();
	    return $res;
	}
	
	function get_lead_all_detail($id) 
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'leads as j');
		$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.job_status');
		$this->db->where('jobid', $id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() > 0)
		{
			$data = $query->result_array();
			return $data[0];
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_users() 
	{
    	$this->db->select('userid, first_name, last_name, level, role_id, inactive');
		$this->db->where('inactive', 0);
    	$this->db->order_by('first_name', "asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }	
	
	function get_job_categories() 
	{
    	$this->db->select('cid, category');
		$this->db->where('status', 1);
    	$this->db->order_by('cid');
		$q = $this->db->get($this->cfg['dbpref'] . 'job_categories');
		return $q->result_array();
    }
	
	function get_sales_divisions() 
	{
    	$this->db->select('div_id, division_name');
		$this->db->where('status', 1);
    	$this->db->order_by('div_id');
		$q = $this->db->get($this->cfg['dbpref'] . 'sales_divisions');
		return $q->result_array();
    }
	
	function get_userlist($userList) {
    	$this->db->select('userid,first_name,last_name,level,role_id,inactive');
		$this->db->where('inactive', 0);
		if(!empty($userList))
		$this->db->where_in('userid', $userList);
    	$this->db->order_by('first_name', "asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }
	
	function get_user_data_by_id($ld) {
		$this->db->where('userid', $ld);
		$user = $this->db->get($this->cfg['dbpref'] . 'users');
		return $user->result_array();
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
	
	function get_customer_det($cid) {
	    $this->db->select('first_name, last_name, company, email_1');
	    $this->db->from($this->cfg['dbpref'] . 'customers');
	    $this->db->where('custid', $cid);
	    $cust_det = $this->db->get();
	    return $cust_det->row_array();
	}
	
	function get_client_data_by_id($cid) {
		$this->db->where('custid', $cid);
		$client = $this->db->get($this->cfg['dbpref'] . 'customers');
		return $client->result_array();
	}
	
	function updt_log_view_status($id, $log) {
		$this->db->where('jobid', $id);
		return $this->db->update($this->cfg['dbpref'] . 'leads', $log);
	}
	
	function get_logs($id) {
		$this->db->order_by('date_created', 'desc');
		$query = $this->db->get_where($this->cfg['dbpref'] . 'logs', array('jobid_fk' => $id));
		return $query->result_array();
	}
    
	function get_quote_items($jobid) {
		$this->db->where('jobid_fk', $jobid);
        $this->db->order_by('item_position', 'asc');
        $sql = $this->db->get($this->cfg['dbpref'] . 'items');
		return $sql->result_array();
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
	
    public function get_query_files_list($jobid)
    {
		
        $data['query_files1_html'] = '';       
		$query_tab = "SELECT lq.job_id, us.first_name,us.last_name, lq.query_msg, lq.query_id, lq.query_file_name, lq.query_sent_date, lq.replay_query 
		FROM ".$this->cfg['dbpref']."lead_query as lq
		LEFT JOIN ".$this->cfg['dbpref']."users as us ON us.userid= lq.user_id WHERE lq.job_id=".$jobid." ORDER BY lq.query_sent_date DESC";
		
		$results = $this->db->query($query_tab);
		$results = $results->result_array();
		$path = 'crm_data/query/' . $jobid. '/';
		
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
								'.urldecode($result['query_msg']).' 
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
	
	public function get_lead_det($jid) 
	{
	    $this->db->select('*');
	    $this->db->from($this->cfg['dbpref'] . 'leads');
	    $this->db->where('jobid', $jid);
	    $lead_history = $this->db->get();
	    return $leads =  $lead_history->row_array();
	}
	
	function updt_lead_stg_status($id, $updt) 
	{
		$this->db->where('jobid', $id);
		return $this->db->update($this->cfg['dbpref'] . 'leads', $updt);
	}
	
	function get_lead_stg_name($id) 
	{
		$query = $this->db->get_where($this->cfg['dbpref'].'lead_stage', array('lead_stage_id' => $id));
		return $query->row_array();
	}
	
	function insert_row($tbl, $ins) {
		return $this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
    }
	
	function insert_row_return_id($tbl, $ins) {
		$this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
		return $this->db->insert_id();
    }
	
	function update_row($tbl, $updt, $jid) {
		$this->db->where('jobid', $jid);
		$this->db->update($this->cfg['dbpref'] . $tbl, $updt);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }
	
	function update_row_item($tbl, $ins, $jid) {
		$this->db->where('itemid', $jid);
		$this->db->update($this->cfg['dbpref'] . $tbl, $ins);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }
	
	function get_expect_worths() {
    	$this->db->select('expect_worth_id, expect_worth_name');
		$this->db->where('status', 1);
    	$q = $this->db->get($this->cfg['dbpref'] . 'expect_worth');
    	return $q->result_array();
    }
		
	public function get_lead_stage() {
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
	
	//$rcsl(region, country, state, location)
	function get_lvl_users($tbl, $rcsl, $rcsl_id, $lvl_id) 
	{
		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref'] . $tbl);
		$this->db->where($rcsl, $rcsl_id);
		$this->db->where_not_in('level_id', $lvl_id);
		$sql = $this->db->get();
		return $res = $sql->result_array();
    }
	
	function get_lvlOne_users() 
	{
		$this->db->select('userid as user_id');
		$this->db->from($this->cfg['dbpref'] . 'users');
		$this->db->where('level', 1);
		$sql = $this->db->get();
		return $res = $sql->result_array();
    }	
	
	function get_item_position($jid) 
	{
		$this->db->select_max('item_position');
		$query = $this->db->get_where($this->cfg['dbpref'].'items', array('jobid_fk' => $jid));
		return $query->result_array();
	}
	
	function delete_lead($tbl, $lead_id) 
	{
		$this->db->where('jobid', $lead_id);
		$this->db->delete($this->cfg['dbpref'] . $tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	function delete_row($tbl, $condn, $lead_id) 
	{
		$this->db->where($condn, $lead_id);
		$this->db->delete($this->cfg['dbpref'] . $tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
	
	function get_categories() 
	{
    	$this->db->order_by('cat_id');
		$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
		return $q->result_array();
    }
    
	function get_cat_records($id) 
	{
		$this->db->where('item_type', $id);
		$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
		return $q->result_array();
    }
  	   
    function get_lead_sources() 
	{
    	$this->db->where('status', 1);
		$q = $this->db->get($this->cfg['dbpref'] . 'lead_source');
		return $q->result_array();
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
	
	//unwanted
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
			c.first_name, c.last_name, c.company, rg.region_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
			us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
			$this->db->from($this->cfg['dbpref']. 'leads as j');
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
			c.first_name, c.last_name, c.company, rg.region_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
			us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
			$this->db->from($this->cfg['dbpref'] . 'leads as j');
			
			$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = j.custid_fk');		
			$this->db->join($this->cfg['dbpref'].'users as u', 'u.userid = j.lead_assign');
			$this->db->join($this->cfg['dbpref'].'users as us', 'us.userid = j.modified_by');
			$this->db->join($this->cfg['dbpref'].'users as ub', 'ub.userid = j.belong_to');
			$this->db->join($this->cfg['dbpref'].'region as rg', 'rg.regionid = c.add1_region');
			$this->db->join($this->cfg['dbpref'].'lead_stage as ls', 'ls.lead_stage_id = j.job_status');
			$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			$this->db->where('j.pjt_status', 0); 
			// $this->db->where('j.jobid != "null" AND j.job_status IN (1,2,3,4,5,6,7,8,9,10,11,12)');
			$this->db->where('j.jobid != "null" AND j.job_status IN ("'.$this->stages.'")');
			
			
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
				// $this->db->or_where('(j.belong_to = '.$curusid.' AND j.job_status IN ("'.$this->stages.'") AND j.pjt_status = 0)');
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
		// echo $this->db->last_query();
		
		$res =  $query->result_array();       
		return $res;
	}
	
	
	//project
	public function assign_lists($stage, $customer, $worth, $owner, $keyword)
	{
		$userdata = $this->session->userdata('logged_in_user');
		 //print_r($userdata['userid']);
		 $this->db->select('j.jobid, j.invoice_no, j.job_title, j.lead_source, j.job_status, j.date_created, j.date_modified, j.belong_to,
		j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.proposal_expected_date, 
		c.first_name, c.last_name, c.company, rg.region_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
		us.last_name as usslname, ls.lead_stage_name,ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'] . 'customers as c');		
		$this->db->join($this->cfg['dbpref'] . 'leads as j', 'j.custid_fk = c.custid AND j.jobid != "null"');		
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
    	$this->db->insert($this->cfg['dbpref'] . 'leads', $ins);
    	return $this->db->insert_id();
    }
	
    function update_job($insert_id, $up_args) {
    	$this->db->where('jobid', $insert_id);
		$this->db->update($this->cfg['dbpref'] . 'leads', $up_args);
    }
    
    function get_lead_assign($level) {
    	$this->db->select('userid', 'first_name');
    	if(!empty($level))
    	$this->db->where('level', $level);
    	$q = $this->db->get($this->cfg['dbpref'] . 'users');
    	return $q->result_array();
    }
	
}

?>
