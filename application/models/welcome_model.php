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
	
		$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_service, j.custid_fk, j.lead_source, j.lead_stage, j.date_created, j.date_modified, j.belong_to, j.created_by, j.expect_worth_amount, j.actual_worth_amount, j.expect_worth_id, j.division,
		j.lead_indicator, j.lead_status, j.lead_assign, j.proposal_expected_date, j.log_view_status, j.lead_hold_reason, j.assigned_to, 
		j.department_id_fk, j.resource_type, j.project_type, j.project_category, j.cost_center, j.project_center, j.sow_status, 
		j.date_start, j.date_due, j.practice, j.project_types, j.customer_type, 
		c.*, c.customer_name AS cfn, cc.add1_region, cc.add1_country, cc.add1_state, cc.add1_location, cc.companyid, rg.region_name, coun.country_name, 
		st.state_name, loc.location_name, us.first_name as usfname, us.last_name as usslname, 
		own.first_name as ownfname, own.last_name as ownlname, ls.lead_stage_name,ew.expect_worth_name, lsrc.lead_source_name, jbcat.services as lead_service, sadiv.division_name, i.industry');
		$this->db->select('GROUP_CONCAT(CONCAT(ass.first_name, " " , ass.last_name) SEPARATOR ",") as assfname', FALSE);
		// $this->db->select(' CONCAT(ass.first_name, " " , ass.last_name) as assfname', FALSE); 
		$this->db->from($this->cfg['dbpref'] . 'leads as j');
		$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');		
		// $this->db->join($this->cfg['dbpref'] . 'users as ass', 'ass.userid = j.lead_assign');
		$this->db->join($this->cfg['dbpref'] . 'users as ass',' FIND_IN_SET (ass.userid , j.lead_assign) ');
		$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
		$this->db->join($this->cfg['dbpref'] . 'users as own', 'own.userid = j.belong_to');
		$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
		$this->db->join($this->cfg['dbpref'] . 'country as coun', 'coun.countryid = cc.add1_country');
		$this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
		$this->db->join($this->cfg['dbpref'] . 'location as loc', 'loc.locationid = cc.add1_location');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage');
		$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
		$this->db->join($this->cfg['dbpref'] . 'lead_source as lsrc', 'lsrc.lead_source_id = j.lead_source');
		$this->db->join($this->cfg['dbpref'] . 'lead_services as jbcat', 'jbcat.sid = j.lead_service');
		$this->db->join($this->cfg['dbpref'] . 'sales_divisions as sadiv', 'sadiv.div_id = j.division');
		$this->db->join($this->cfg['dbpref'] . 'industry as i', 'i.id = j.industry', 'LEFT');
		$this->db->where('j.lead_id = "'.$leadid.'" AND j.lead_stage IN ("'.$this->stages.'")');
		// $this->db->where('j.pjt_status', 0);
		
		$sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    $res =  $sql->result_array();
	    return $res;
	}
	
	function get_company_det($id){
		$this->db->select('cc.*, rg.region_name, coun.country_name, st.state_name, loc.location_name');
		$this->db->from($this->cfg['dbpref'] . 'customers_company as cc');
		$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
		$this->db->join($this->cfg['dbpref'] . 'country as coun', 'coun.countryid = cc.add1_country');
		$this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
		$this->db->join($this->cfg['dbpref'] . 'location as loc', 'loc.locationid = cc.add1_location');
		$this->db->where('companyid', $id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	function get_contact_det($cid){
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'customers');
		$this->db->where('company_id', $cid);
		$this->db->order_by('custid');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function get_lead_all_detail($id) 
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'leads as j');
		$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage');
		$this->db->where('lead_id', $id);
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
    	$this->db->select('userid, first_name, last_name, level, role_id, inactive, emp_id');
		$this->db->where('inactive', 0);
		$this->db->where('username !=', 'admin.enoah');
    	$this->db->order_by('first_name', "asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }	
	
	function get_lead_services() 
	{
    	$this->db->select('sid, services');
		$this->db->where('status', 1);
    	$this->db->order_by('sid');
		$q = $this->db->get($this->cfg['dbpref'] . 'lead_services');
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

	function get_lead_stages() 
	{
    	$this->db->select('lead_stage_id, lead_stage_name');
		$this->db->where('status', 1);
    	$this->db->order_by('sequence');
		$q = $this->db->get($this->cfg['dbpref'] . 'lead_stage');
		return $q->result_array();
    }

	function get_industry() 
	{
    	$this->db->select('id, industry');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$q = $this->db->get($this->cfg['dbpref'] . 'industry');
		return $q->result_array();
    }
	
	function get_userlist($userList) {
    	$this->db->select('userid,first_name,last_name,level,role_id,inactive,emp_id');
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
	
	function get_data_by_id($table, $wh_condn) {
		$this->db->where($wh_condn);
		$user = $this->db->get($this->cfg['dbpref'] . $table);
		return $user->row_array();
	}
	
	function get_user_byrole($role_id) {
    	$users = $this->db->get_where($this->cfg['dbpref'] . 'users', array('role_id'=>$role_id))->result_array();
    	return $users;
    }
	
	function get_customers() {
		$cusId = $this->level_restriction();
		
		$userdata = $this->session->userdata('logged_in_user');
		
	    $this->db->select('companyid, company');
	    $this->db->from($this->cfg['dbpref'] . 'customers_company');
		if ($this->userdata['level']!=1) {
			$this->db->where_in('companyid', $cusId);
		}
		if ($this->userdata['role_id']==14) {
			$this->db->where('created_by', $this->userdata['userid']);
		}
		$this->db->order_by("company", "asc");
	    $customers = $this->db->get();
	    $customers=  $customers->result_array();
	    return $customers;
	}
	
	function get_customer_det($cid) {
	    $this->db->select('c.customer_name, cc.company');
	    $this->db->from($this->cfg['dbpref'] . 'customers as c');
	    $this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
	    $this->db->where('c.custid', $cid);
	    $cust_det = $this->db->get();
	    return $cust_det->row_array();
	}
	
	function get_customer_name_by_lead($id) {
	    $this->db->select('c.customer_name, cc.company');
	    $this->db->from($this->cfg['dbpref'] . 'customers as c');
	    $this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
	    $this->db->join($this->cfg['dbpref'] . 'leads as l', 'l.custid_fk = c.custid');
	    $this->db->where('l.lead_id', $id);
	    $cust_det = $this->db->get();
	    return $cust_det->row_array();
	}
	
	function get_client_data_by_id($cid) {
		$this->db->where('custid', $cid);
		$client = $this->db->get($this->cfg['dbpref'] . 'customers');
		return $client->result_array();
	}
	
	function updt_log_view_status($id, $log) {
		$this->db->where('lead_id', $id);
		return $this->db->update($this->cfg['dbpref'] . 'leads', $log);
	}
	
	function get_logs($id) {
		$this->db->order_by('date_created', 'desc');
		$query = $this->db->get_where($this->cfg['dbpref'] . 'logs', array('jobid_fk' => $id));
		return $query->result_array();
	}
	
	function get_last_logs($id) {
		$this->db->order_by('date_created', 'desc');
		$query = $this->db->get_where($this->cfg['dbpref'] . 'logs', array('jobid_fk' => $id));
		return $query->row_array();
	}
    
	function get_quote_items($lead_id) {
		$this->db->where('jobid_fk', $lead_id);
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
	
    public function get_query_files_list($lead_id)
    {
		$userdata = $this->session->userdata('logged_in_user');
		
		$this->db->select('lead_assign,belong_to');
		$this->db->where('lead_id', $lead_id);
        $sql = $this->db->get($this->cfg['dbpref'] . 'leads');
		$lead_det = $sql->row_array();
		
        $data['query_files1_html'] = '';       
		$query_tab = "SELECT lq.lead_id, us.first_name,us.last_name, lq.query_msg, lq.query_id, lq.query_file_name, lq.query_sent_date, lq.replay_query 
		FROM ".$this->cfg['dbpref']."lead_query as lq
		LEFT JOIN ".$this->cfg['dbpref']."users as us ON us.userid= lq.user_id WHERE lq.lead_id=".$lead_id." ORDER BY lq.query_sent_date DESC";
		
		$results = $this->db->query($query_tab);
		$results = $results->result_array();
		$path = 'crm_data/query/' . $lead_id. '/';
		
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
				</tr>';
				if ($lead_det['belong_to'] == $userdata['userid'] || $lead_det['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) {
					$data['query_files1_html'] .='<tr><td colspan="4" valign="top">
						<input type="button" class="positive" style="float:right;cursor:pointer;" id="replay" onclick="getReplyForm('.$result['query_id'].')" value="Reply" />
					</td></tr>';
				}
				$data['query_files1_html'] .='</tbody></table>
			</td></tr>';
	   }
        return $data['query_files1_html'];
    }
	
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
	
	public function get_lead_det($jid) 
	{
	    $this->db->select('*');
	    $this->db->from($this->cfg['dbpref'] . 'leads');
	    $this->db->where('lead_id', $jid);
	    $lead_history = $this->db->get();
	    return $leads =  $lead_history->row_array();
	}
	
	function updt_lead_stg_status($id, $updt) 
	{
		$this->db->where('lead_id', $id);
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
		$this->db->where('lead_id', $jid);
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
		$this->db->where('lead_id', $lead_id);
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
		$this->db->where("lead_id", $id);
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
	
	public function get_filter_results($from_date,$to_date,$stage, $customer, $service, $lead_src, $industry, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status,$lead_indi, $keyword, $proposal_expect_end)
	{
		$userdata 		= $this->session->userdata('logged_in_user');
		
		$stage 			= (count($stage)>0)?explode(',',$stage):'';
		$owner 			= (count($owner)>0)?explode(',',$owner):'';
		$customer 		= (count($customer)>0)?explode(',',$customer):'';
		$service 		= (count($service)>0)?explode(',',$service):'';
		$lead_src 		= (count($lead_src)>0)?explode(',',$lead_src):'';
		$industry 		= (count($industry)>0)?explode(',',$industry):'';
		$worth 			= (count($worth)>0)?explode(',',$worth):'';//print_r($worth);exit;
		$leadassignee 	= (count($leadassignee)>0)?explode(',',$leadassignee):'';
		$regionname 	= (count($regionname)>0)?explode(',',$regionname):'';
		$countryname 	= (count($countryname)>0)?explode(',',$countryname):'';
		$statename 		= (count($statename)>0)?explode(',',$statename):'';
		$locname 		= (count($locname)>0)?explode(',',$locname):'';
		$lead_status 	= (count($lead_status)>0)?explode(',',$lead_status):'';
		$lead_indi 		= (count($lead_indi)>0)?explode(',',$lead_indi):'';
		
		
		if(isset($proposal_expect_end) && ($proposal_expect_end == 'load_proposal_expect_end')) {
			$proposal_notify_day = get_notify_status(1);
		}
		// echo $this->userdata['role_id'];exit;
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_service, j.lead_source, j.lead_stage, j.date_created, j.date_modified, j.belong_to, j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.pjt_status, j.lead_assign, j.proposal_expected_date, j.division, j.industry,
			c.customer_name, cc.company, c.email_1, c.phone_1, c.position_title, c.skype_name, rg.region_name, co.country_name, st.state_name, locn.location_name, u.first_name as ufname, u.last_name as ulname, us.first_name as usfname,
			us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
			$this->db->select('GROUP_CONCAT(CONCAT(u.first_name, " " , u.last_name)) as ufname', FALSE);
			$this->db->from($this->cfg['dbpref']. 'leads as j');
			$this->db->where('j.lead_id != "null" AND j.lead_stage IN ("'.$this->stages.'")');
			// $this->db->where('j.pjt_status', 0);
			$this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
			// $this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
			$this->db->join($this->cfg['dbpref'] . 'users as u',' FIND_IN_SET (u.userid , j.lead_assign) ');
			$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
			$this->db->join($this->cfg['dbpref'] . 'users as ub', 'ub.userid = j.belong_to');
			$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
			$this->db->join($this->cfg['dbpref'] . 'country as co', 'co.countryid = cc.add1_country');
			$this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
			$this->db->join($this->cfg['dbpref'] . 'location as locn', 'locn.locationid = cc.add1_location');
			$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage', 'LEFT');
			$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			// date_created
			if(isset($from_date) && !empty($from_date) && empty($to_date)) {
				$dt_query =  'DATE(j.date_created) >= "'.date('Y-m-d', strtotime($from_date)).'"';
				$dt_mod_query =  'DATE(j.date_modified) >= "'.date('Y-m-d', strtotime($from_date)).'"';
				// echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			} else if(isset($to_date) && !empty($to_date) && empty($from_date)) {	
				$dt_query = 'DATE(j.date_created) <= "'.date('Y-m-d', strtotime($to_date)).'"';
				$dt_mod_query = 'DATE(j.date_modified) <= "'.date('Y-m-d', strtotime($to_date)).'"';
				// echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			} else if(isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)) {
				$dt_query = '((DATE(j.date_created) >= "'.date('Y-m-d', strtotime($from_date)).'" AND DATE(j.date_created) <= "'.date('Y-m-d', strtotime($to_date)).'")';
				$dt_mod_query = '(DATE(j.date_modified) >= "'.date('Y-m-d', strtotime($from_date)).'" AND DATE(j.date_modified) <= "'.date('Y-m-d', strtotime($to_date)).'"))';
				 // echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			}
			
			if(!empty($stage) && count($stage)>0){
				if($stage[0] != 'null' && $stage[0] != 'all') {		
					$this->db->where_in('j.lead_stage',$stage); 
				}	
			}
			if(!empty($customer) && count($customer)>0){
				if($customer[0] != 'null' && $customer[0] != 'all'){		
					$this->db->where_in('cc.companyid',$customer); 
				}
			}
			if(!empty($service) && count($service)>0){
				if($service[0] != 'null' && $service[0] != 'all'){		
					$this->db->where_in('j.lead_service',$service); 
				}
			}
			if(!empty($lead_src) && count($lead_src)>0){
				if($lead_src[0] != 'null' && $lead_src[0] != 'all' && $lead_src[0] != ''){		
					$this->db->where_in('j.lead_source',$lead_src); 
				}
			}
			if(!empty($industry) && count($industry)>0){
				if($industry[0] != 'null' && $industry[0] != 'all' && $industry[0] != ''){		
					$this->db->where_in('j.industry',$industry); 
				}
			}
			/* Expected Worth amount filter search starts */
			if(!empty($worth) && count($worth)>0 && $worth[0] !='null'){//print_r($worth);exit;
				$where_query='(';
				foreach($worth as $key=>$worth_val)
				{
					$exploded_worth_val=explode('-',$worth_val);
					$left_side=$exploded_worth_val[0];
					$right_side=$exploded_worth_val[1];
					if($right_side =='above')
					{
						$where_query .='j.expect_worth_amount >= '.$left_side;
					}
					else
					{
						$where_query .='j.expect_worth_amount BETWEEN '.$left_side.' AND '.$right_side;
					}
					if($key < count($worth)-1)
					{
					$where_query .=' OR ';
					}
					//$this->db->or_where('j.expect_worth_amount BETWEEN '.$left_side.' AND '.$right_side);
				}
				$where_query .=')';
				$this->db->where($where_query);
				/* 
				if($worth[0] != 'null' && $worth[0] != 'all'){	
					if($worth[1] == 'above')
					$this->db->where('j.expect_worth_amount >= '.$worth['0']);	
					else
					$this->db->where('j.expect_worth_amount BETWEEN '.$worth['0'].' AND '.$worth['1']);	
				} */
			}
			/* Expected Worth amount filter search ends */
			if(!empty($owner) ){
				if($owner[0] != 'null' && $owner[0] != 'all'){
					$this->db->where_in('j.belong_to',$owner); 
				}
			}
			if(!empty($leadassignee) && count($leadassignee)>0){
				if($leadassignee[0] != 'null' && $leadassignee[0] != 'all'){		
					// $this->db->where_in('j.lead_assign', $leadassignee);
					$cnt = count($leadassignee);
					if(count($leadassignee)>1) {
						$find_wh_id = '(';
						for($i=0; $i<count($leadassignee); $i++) {
							$find_wh_id .= $leadassignee[$i];
							if($cnt != ($i+1)) {
								$find_wh_id .= "|";
							}
						}
						$find_wh_id .= ')';
						$find_wh 	= 'CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
					} else {
						$find_wh 	= "FIND_IN_SET('".$leadassignee[0]."', j.lead_assign)";
					}
					$this->db->where($find_wh);
				}
			}
			if(!empty($regionname) && count($regionname)>0){
				if($regionname[0] != 'null' && $regionname[0] != 'all'){
					$this->db->where_in('cc.add1_region', $regionname);
				}
			}
			if(!empty($countryname) && count($countryname)>0){
				if(!empty($countryname) && $countryname[0] != 'null' && $countryname[0] != 'all'){
					$this->db->where_in('cc.add1_country', $countryname);
				}
			}
			if(!empty($statename) && count($statename)>0){
				if(!empty($statename) && $statename[0] != 'null' && $statename[0] != 'all'){	
					$this->db->where_in('cc.add1_state', $statename);
				}
			}
			if(!empty($locname) && count($locname)>0){
				if(!empty($locname) && $locname[0] != 'null' && $locname[0] != 'all'){	
					$this->db->where_in('cc.add1_location', $locname);
				}
			}
			if(!empty($lead_indi) && count($lead_indi)>0){
				if(!empty($lead_indi) && $lead_indi[0] != 'null' && $lead_indi[0] !='') {	
					$this->db->where_in('j.lead_indicator', $lead_indi);
				}
			}
			if(!empty($keyword) && count($keyword)>0){
				if(!empty($keyword) && $keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null'){		
					$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR cc.company LIKE '%$keyword%' OR c.customer_name LIKE '%$keyword%' ))";
					$this->db->where($invwhere);
				}
			} 
			//echo $this->db->last_query();exit;
		} else if($this->userdata['role_id'] == 14) { //for reseller role
			$curusid = $this->session->userdata['logged_in_user']['userid'];
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_service, j.lead_source, j.lead_stage, j.date_created, j.date_modified, j.belong_to, j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.pjt_status, j.lead_assign, j.proposal_expected_date, j.division, j.industry,
			c.customer_name, cc.company, c.email_1, c.phone_1, c.phone_2, rg.region_name, co.country_name, st.state_name, locn.location_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
			us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
			$this->db->select('GROUP_CONCAT(CONCAT(u.first_name, " " , u.last_name)) as ufname', FALSE);
			$this->db->from($this->cfg['dbpref']. 'leads as j');
			$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');			
			$this->db->join($this->cfg['dbpref'].'users as u',' FIND_IN_SET (u.userid , j.lead_assign) ');
			$this->db->join($this->cfg['dbpref'].'users as us', 'us.userid = j.modified_by');
			$this->db->join($this->cfg['dbpref'].'users as ub', 'ub.userid = j.belong_to');
			$this->db->join($this->cfg['dbpref'].'region as rg', 'rg.regionid = cc.add1_region');
			$this->db->join($this->cfg['dbpref'].'country as co', 'co.countryid = cc.add1_country');
			$this->db->join($this->cfg['dbpref'].'state as st', 'st.stateid = cc.add1_state');
			$this->db->join($this->cfg['dbpref'].'location as locn', 'locn.locationid = cc.add1_location');
			$this->db->join($this->cfg['dbpref'].'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage');
			$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			
			$reseller_condn = '(j.belong_to = '.$curusid.' OR j.assigned_to ='.$curusid.' OR FIND_IN_SET('.$curusid.', j.lead_assign)) ';
			$this->db->where($reseller_condn);
			
			$this->db->where('j.lead_id != "null" AND j.lead_stage IN ("'.$this->stages.'")');
			
			if(isset($from_date) && !empty($from_date) && empty($to_date)) {
				$dt_query =  'DATE(j.date_created) >= "'.date('Y-m-d', strtotime($from_date)).'"';
				$dt_mod_query =  'DATE(j.date_modified) >= "'.date('Y-m-d', strtotime($from_date)).'"';
				// echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			} else if(isset($to_date) && !empty($to_date) && empty($from_date)) {	
				$dt_query = 'DATE(j.date_created) <= "'.date('Y-m-d', strtotime($to_date)).'"';
				$dt_mod_query = 'DATE(j.date_modified) <= "'.date('Y-m-d', strtotime($to_date)).'"';
				// echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			} else if(isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)) {
				$dt_query = '((DATE(j.date_created) >= "'.date('Y-m-d', strtotime($from_date)).'" AND DATE(j.date_created) <= "'.date('Y-m-d', strtotime($to_date)).'")';
				$dt_mod_query = '(DATE(j.date_modified) >= "'.date('Y-m-d', strtotime($from_date)).'" AND DATE(j.date_modified) <= "'.date('Y-m-d', strtotime($to_date)).'"))';
				 // echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			}
			
			if(!empty($stage) && count($stage)>0){
				if($stage[0] != 'null' && $stage[0] != 'all') {
					$this->db->where_in('j.lead_stage',$stage);
				}
			}
			if(!empty($customer) && count($customer)>0){
				if($customer[0] != 'null' && $customer[0] != 'all') {		
					$this->db->where_in('cc.companyid',$customer);				
				}
			}
			if(!empty($service) && count($service)>0){
				if($service[0] != 'null' && $service[0] != 'all'){		
					$this->db->where_in('j.lead_service',$service); 
				}
			}
			if(!empty($lead_src) && count($lead_src)>0){
				if($lead_src[0] != 'null' && $lead_src[0] != 'all' && $lead_src[0] != ''){		
					$this->db->where_in('j.lead_source',$lead_src);
				}
			}
			if(!empty($industry) && count($industry)>0){
				if($industry[0] != 'null' && $industry[0] != 'all' && $industry[0] != ''){
					$this->db->where_in('j.industry',$industry);
				}
			}
			/* Expected Worth amount filter search starts */
			if(!empty($worth) && count($worth)>0 && $worth[0] !='null'){//print_r($worth);exit;
				$where_query='(';
				foreach($worth as $key=>$worth_val)
				{
					$exploded_worth_val=explode('-',$worth_val);
					$left_side=$exploded_worth_val[0];
					$right_side=$exploded_worth_val[1];
					if($right_side =='above')
					{
						$where_query .='j.expect_worth_amount >= '.$left_side;
					}
					else
					{
						$where_query .='j.expect_worth_amount BETWEEN '.$left_side.' AND '.$right_side;
					}
					if($key < count($worth)-1)
					{
					$where_query .=' OR ';
					}
					//$this->db->or_where('j.expect_worth_amount BETWEEN '.$left_side.' AND '.$right_side);
				}
				$where_query .=')';
				$this->db->where($where_query);
				/* 
				if($worth[0] != 'null' && $worth[0] != 'all'){	
					if($worth[1] == 'above')
					$this->db->where('j.expect_worth_amount >= '.$worth['0']);	
					else
					$this->db->where('j.expect_worth_amount BETWEEN '.$worth['0'].' AND '.$worth['1']);	
				} */
			}
			/* Expected Worth amount filter search ends */
			if(!empty($owner) ){
				if($owner[0] != 'null' && $owner[0] != 'all') {		
					$this->db->where_in('j.belong_to',$owner); 
				}
			}
			if(!empty($leadassignee) && count($leadassignee)>0){
				if($leadassignee[0] != 'null' && $leadassignee[0] != 'all'){		
					// $this->db->where_in('j.lead_assign', $leadassignee);
					$cnt = count($leadassignee);
					if(count($leadassignee)>1) {
						$find_wh_id = '(';
						for($i=0; $i<count($leadassignee); $i++) {
							$find_wh_id .= $leadassignee[$i];
							if($cnt != ($i+1)) {
								$find_wh_id .= "|";
							}
						}
						$find_wh_id .= ')';
						$find_wh 	= 'CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
					} else {
						$find_wh 	= "FIND_IN_SET('".$leadassignee[0]."', j.lead_assign)";
					}
					$this->db->where($find_wh);
				}
			}
			if(!empty($keyword) && count($keyword)>0){
				if($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null'){		
					$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.customer_name LIKE '%$keyword%' ))";
					$this->db->where($invwhere);
				}
			}
			
			if (isset($this->session->userdata['region_id']))
			$region = explode(',',$this->session->userdata['region_id']);
			if (isset($this->session->userdata['countryid']))
			$countryid = explode(',',$this->session->userdata['countryid']);
			if (isset($this->session->userdata['stateid']))
			$stateid = explode(',',$this->session->userdata['stateid']);
			if (isset($this->session->userdata['locationid']))
			$locationid = explode(',',$this->session->userdata['locationid']);

			if ( ($stage[0] == 'null' || $stage[0] == 'all') && ($customer[0] == 'null' || $customer[0] == 'all') && ($worth[0] == 'null' || $worth[0] == 'all') && ($owner[0] == 'null' || $owner[0] == 'all') && ($leadassignee[0] == 'null' || $leadassignee[0] == 'all') && ($regionname[0] == 'null' || $regionname[0] == 'all') && ($countryname[0] == 'null' || $countryname[0] == 'all') && ($statename[0] == 'null' || $statename[0] == 'all') && ($locname[0] == 'null' || $locname[0] == 'all') && $keyword == 'null' ) {
				
				if (isset($this->session->userdata['region_id']))
				$region = explode(',',$this->session->userdata['region_id']);
				if (isset($this->session->userdata['countryid']))
				$countryid = explode(',',$this->session->userdata['countryid']);
				if (isset($this->session->userdata['stateid']))
				$stateid = explode(',',$this->session->userdata['stateid']);
				if (isset($this->session->userdata['locationid']))
				$locationid = explode(',',$this->session->userdata['locationid']);

				$this->db->where_in('cc.add1_region',$region);
				
				if (isset($this->session->userdata['countryid'])) {
					$this->db->where_in('cc.add1_country',$countryid); 
				}
				if (isset($this->session->userdata['stateid'])) {
					$this->db->where_in('cc.add1_state',$stateid);
				}
				if (isset($this->session->userdata['locationid'])) {
					$this->db->where_in('cc.add1_location',$locationid); 
				}
			}
			
			/*Advanced filter*/
			if(!empty($regionname) && $regionname[0] != 'null'){
				$this->db->where_in('cc.add1_region', $regionname);
			} else {
				$this->db->where_in('cc.add1_region', $region);
			}
			if(!empty($countryname) && $countryname[0] != 'null') {
				$this->db->where_in('cc.add1_country', $countryname);
			} else if ((($this->userdata['level'])==3) || (($this->userdata['level'])==4) || (($this->userdata['level'])==5)) {
				$this->db->where_in('cc.add1_country', $countryid);
			}
			if(!empty($statename) && $statename[0] != 'null') {	
				$this->db->where_in('cc.add1_state', $statename);
			} else if ((($this->userdata['level'])==4) || (($this->userdata['level'])==5)) {
				$this->db->where_in('cc.add1_state', $stateid);
			}
			if(!empty($locname) && $locname[0] != 'null') {	
				$this->db->where_in('cc.add1_location', $locname);
			} else if (($this->userdata['level'])==5) {
				$this->db->where_in('cc.add1_location', $locationid);
			}
			
			if(!empty($lead_indi) && $lead_indi[0] != 'null' && $lead_indi[0] !='') {	
				$this->db->where_in('j.lead_indicator', $lead_indi);
			}
			/*Advanced filter*/
		} else {
			$curusid = $this->session->userdata['logged_in_user']['userid'];
			$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_service, j.lead_source, j.lead_stage, j.date_created, j.date_modified, j.belong_to, j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.pjt_status, j.lead_assign, j.proposal_expected_date, j.division, j.industry, c.customer_name, cc.company, c.email_1, c.phone_1, c.phone_2, rg.region_name, co.country_name, st.state_name, locn.location_name, us.first_name as usfname,	us.last_name as usslname, ub.first_name as ubfn, ub.last_name as ubln, ls.lead_stage_name,ew.expect_worth_name');
			$this->db->select('GROUP_CONCAT(CONCAT(u.first_name, " " , u.last_name)) as ufname', FALSE);
			$this->db->from($this->cfg['dbpref']. 'leads as j');
			
			$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = j.custid_fk');
			$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');			
			// $this->db->join($this->cfg['dbpref'].'users as u', 'u.userid = j.lead_assign');
			$this->db->join($this->cfg['dbpref'].'users as u',' FIND_IN_SET (u.userid , j.lead_assign) ');
			$this->db->join($this->cfg['dbpref'].'users as us', 'us.userid = j.modified_by');
			$this->db->join($this->cfg['dbpref'].'users as ub', 'ub.userid = j.belong_to');
			$this->db->join($this->cfg['dbpref'].'region as rg', 'rg.regionid = cc.add1_region');
			$this->db->join($this->cfg['dbpref'].'country as co', 'co.countryid = cc.add1_country');
			$this->db->join($this->cfg['dbpref'].'state as st', 'st.stateid = cc.add1_state');
			$this->db->join($this->cfg['dbpref'].'location as locn', 'locn.locationid = cc.add1_location');
			$this->db->join($this->cfg['dbpref'].'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage');
			$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
			
			$this->db->where('j.lead_id != "null" AND j.lead_stage IN ("'.$this->stages.'")');
			
			if(isset($from_date) && !empty($from_date) && empty($to_date)) {
				$dt_query =  'DATE(j.date_created) >= "'.date('Y-m-d', strtotime($from_date)).'"';
				$dt_mod_query =  'DATE(j.date_modified) >= "'.date('Y-m-d', strtotime($from_date)).'"';
				// echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			} else if(isset($to_date) && !empty($to_date) && empty($from_date)) {	
				$dt_query = 'DATE(j.date_created) <= "'.date('Y-m-d', strtotime($to_date)).'"';
				$dt_mod_query = 'DATE(j.date_modified) <= "'.date('Y-m-d', strtotime($to_date)).'"';
				// echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			} else if(isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)) {
				$dt_query = '((DATE(j.date_created) >= "'.date('Y-m-d', strtotime($from_date)).'" AND DATE(j.date_created) <= "'.date('Y-m-d', strtotime($to_date)).'")';
				$dt_mod_query = '(DATE(j.date_modified) >= "'.date('Y-m-d', strtotime($from_date)).'" AND DATE(j.date_modified) <= "'.date('Y-m-d', strtotime($to_date)).'"))';
				 // echo'<pre>';print_r($dt_query);exit;
				$this->db->where($dt_query);
				$this->db->or_where($dt_mod_query);
			}
			
			if(!empty($stage) && count($stage)>0){
				if($stage[0] != 'null' && $stage[0] != 'all') {
					$this->db->where_in('j.lead_stage',$stage); 
					// $this->db->where('j.belong_to', $curusid);
				}
			}
			if(!empty($customer) && count($customer)>0){
				if($customer[0] != 'null' && $customer[0] != 'all') {		
					$this->db->where_in('cc.companyid',$customer);				
				}
			}
			if(!empty($service) && count($service)>0){
				if($service[0] != 'null' && $service[0] != 'all'){		
					$this->db->where_in('j.lead_service',$service); 
				}
			}
			if(!empty($lead_src) && count($lead_src)>0){
				if($lead_src[0] != 'null' && $lead_src[0] != 'all' && $lead_src[0] != ''){		
					$this->db->where_in('j.lead_source',$lead_src);
				}
			}
			if(!empty($industry) && count($industry)>0){
				if($industry[0] != 'null' && $industry[0] != 'all' && $industry[0] != ''){
					$this->db->where_in('j.industry',$industry);
				}
			}
			/* Expected Worth amount filter search starts */
			if(!empty($worth) && count($worth)>0 && $worth[0] !='null'){//print_r($worth);exit;
				$where_query='(';
				foreach($worth as $key=>$worth_val)
				{
					$exploded_worth_val=explode('-',$worth_val);
					$left_side=$exploded_worth_val[0];
					$right_side=$exploded_worth_val[1];
					if($right_side =='above')
					{
						$where_query .='j.expect_worth_amount >= '.$left_side;
					}
					else
					{
						$where_query .='j.expect_worth_amount BETWEEN '.$left_side.' AND '.$right_side;
					}
					if($key < count($worth)-1)
					{
					$where_query .=' OR ';
					}
					//$this->db->or_where('j.expect_worth_amount BETWEEN '.$left_side.' AND '.$right_side);
				}
				$where_query .=')';
				$this->db->where($where_query);
				/* 
				if($worth[0] != 'null' && $worth[0] != 'all'){	
					if($worth[1] == 'above')
					$this->db->where('j.expect_worth_amount >= '.$worth['0']);	
					else
					$this->db->where('j.expect_worth_amount BETWEEN '.$worth['0'].' AND '.$worth['1']);	
				} */
			}
			/* Expected Worth amount filter search ends */
			if(!empty($owner) ){
				if($owner[0] != 'null' && $owner[0] != 'all') {		
					$this->db->where_in('j.belong_to',$owner); 
				}
			}
			if(!empty($leadassignee) && count($leadassignee)>0){
				if($leadassignee[0] != 'null' && $leadassignee[0] != 'all'){		
					// $this->db->where_in('j.lead_assign', $leadassignee);
					$cnt = count($leadassignee);
					if(count($leadassignee)>1) {
						$find_wh_id = '(';
						for($i=0; $i<count($leadassignee); $i++) {
							$find_wh_id .= $leadassignee[$i];
							if($cnt != ($i+1)) {
								$find_wh_id .= "|";
							}
						}
						$find_wh_id .= ')';
						$find_wh 	= 'CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
					} else {
						$find_wh 	= "FIND_IN_SET('".$leadassignee[0]."', j.lead_assign)";
					}
					$this->db->where($find_wh);
				}
			}
			if(!empty($keyword) && count($keyword)>0){
				if($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null'){		
					$invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.customer_name LIKE '%$keyword%' ))";
					$this->db->where($invwhere);
				}
			}
			
			if (isset($this->session->userdata['region_id']))
			$region = explode(',',$this->session->userdata['region_id']);
			if (isset($this->session->userdata['countryid']))
			$countryid = explode(',',$this->session->userdata['countryid']);
			if (isset($this->session->userdata['stateid']))
			$stateid = explode(',',$this->session->userdata['stateid']);
			if (isset($this->session->userdata['locationid']))
			$locationid = explode(',',$this->session->userdata['locationid']);

			if ( ($stage[0] == 'null' || $stage[0] == 'all') && ($customer[0] == 'null' || $customer[0] == 'all') && ($worth[0] == 'null' || $worth[0] == 'all') && ($owner[0] == 'null' || $owner[0] == 'all') && ($leadassignee[0] == 'null' || $leadassignee[0] == 'all') && ($regionname[0] == 'null' || $regionname[0] == 'all') && ($countryname[0] == 'null' || $countryname[0] == 'all') && ($statename[0] == 'null' || $statename[0] == 'all') && ($locname[0] == 'null' || $locname[0] == 'all') && $keyword == 'null' ) {
				
				if (isset($this->session->userdata['region_id']))
				$region = explode(',',$this->session->userdata['region_id']);
				if (isset($this->session->userdata['countryid']))
				$countryid = explode(',',$this->session->userdata['countryid']);
				if (isset($this->session->userdata['stateid']))
				$stateid = explode(',',$this->session->userdata['stateid']);
				if (isset($this->session->userdata['locationid']))
				$locationid = explode(',',$this->session->userdata['locationid']);

				$this->db->where_in('cc.add1_region',$region);
				
				if (isset($this->session->userdata['countryid'])) {
					$this->db->where_in('cc.add1_country',$countryid); 
				}
				if (isset($this->session->userdata['stateid'])) {
					$this->db->where_in('cc.add1_state',$stateid);
				}
				if (isset($this->session->userdata['locationid'])) {
					$this->db->where_in('cc.add1_location',$locationid); 
				}
			}
			
			/*Advanced filter*/
			if(!empty($regionname) && $regionname[0] != 'null'){
				$this->db->where_in('cc.add1_region', $regionname);
			} else {
				$this->db->where_in('cc.add1_region', $region);
			}
			if(!empty($countryname) && $countryname[0] != 'null') {
				$this->db->where_in('cc.add1_country', $countryname);
			} else if ((($this->userdata['level'])==3) || (($this->userdata['level'])==4) || (($this->userdata['level'])==5)) {
				$this->db->where_in('cc.add1_country', $countryid);
			}
			if(!empty($statename) && $statename[0] != 'null') {	
				$this->db->where_in('cc.add1_state', $statename);
			} else if ((($this->userdata['level'])==4) || (($this->userdata['level'])==5)) {
				$this->db->where_in('cc.add1_state', $stateid);
			}
			if(!empty($locname) && $locname[0] != 'null') {	
				$this->db->where_in('cc.add1_location', $locname);
			} else if (($this->userdata['level'])==5) {
				$this->db->where_in('cc.add1_location', $locationid);
			}
			
			if(!empty($lead_indi) && $lead_indi[0] != 'null' && $lead_indi[0] !='') {	
				$this->db->where_in('j.lead_indicator', $lead_indi);
			}
			/*Advanced filter*/
		}
		
		/*Lead & move to project Status filter*/
		if(!empty($lead_status) && count($lead_status)>0){
			if(!empty($lead_status) & count($lead_status)==1){
				if(in_array('5', $lead_status)){
					$lead_status = array_diff($lead_status, array('5'));
					$this->db->where('j.move_to_project_status', 1);
				}
				if(in_array('4', $lead_status)){
					$this->db->where('j.lead_status', 4);
					$this->db->where('j.pjt_status', 0);
				}
			}
			if(!empty($lead_status) && $lead_status[0] != 'null' && $lead_status[0] !='' && !in_array('5', $lead_status)) {
				$this->db->where_in('j.lead_status', $lead_status);
			}
			if(!empty($lead_status) && $lead_status[0] != 'null' && $lead_status[0] !='' && in_array('5', $lead_status) && (in_array('4', $lead_status) || in_array('3', $lead_status) || in_array('2', $lead_status) || in_array('1', $lead_status))) {
				$lead_status = array_diff($lead_status, array('5'));
				$this->db->where('j.move_to_project_status', 1);
				$this->db->or_where_in('j.lead_status', $lead_status);
			}
		}
		/*Lead & move to project Status filter*/
		
		/*Load data from notification bell icon*/
		if(isset($proposal_expect_end) && ($proposal_expect_end == 'load_proposal_expect_end')) {
			$this->db->where('j.proposal_expected_date BETWEEN CURDATE() AND DATE(DATE_ADD(CURDATE(), INTERVAL '.$proposal_notify_day.' DAY)) ');
			$this->db->where('j.lead_status', 1);
			// $lead_assign_condn = ' FIND_IN_SET('.$this->userdata['userid'].', j.lead_assign)';
			$lead_assign_condn 	= "FIND_IN_SET('".$this->userdata['userid']."', j.lead_assign)";
			$this->db->where($lead_assign_condn);
		}
		
		$this->db->group_by("j.lead_id");
		$this->db->order_by("j.lead_id", "desc");
		$query = $this->db->get();
		  //echo $this->db->last_query(); exit;
		
		$res =  $query->result_array();
		return $res;
	}
	
	
	//project
	public function assign_lists($stage, $customer, $worth, $owner, $keyword)
	{
		$userdata = $this->session->userdata('logged_in_user');
		 //print_r($userdata['userid']);
		 $this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.lead_source, j.lead_stage, j.date_created, j.date_modified, j.belong_to,
		j.created_by, j.expect_worth_amount, j.expect_worth_id, j.lead_indicator, j.lead_status, j.proposal_expected_date, 
		c.customer_name, cc.company, rg.region_name, u.first_name as ufname, u.last_name as ulname,us.first_name as usfname,
		us.last_name as usslname, ls.lead_stage_name,ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'] . 'customers as c');
		$this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');		
		$this->db->join($this->cfg['dbpref'] . 'leads as j', 'j.custid_fk = c.custid AND j.lead_id != "null"');		
		$this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
		$this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
		$this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
		$this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage');
		$this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id ');
		
		$this->db->where('j.lead_assign', $userdata['userid']);
		
		$region     = @explode(',',$this->session->userdata['region_id']);	
		$countryid  = @explode(',',$this->session->userdata['countryid']);
		$stateid    = @explode(',',$this->session->userdata['stateid']);
		$locationid = @explode(',',$this->session->userdata['locationid']);
		
		
		$this->db->where_in('cc.add1_region',$region); 
		if($this->session->userdata['countryid'] != '') {
			$this->db->where_in('cc.add1_country',$countryid); 
		}
		if($this->session->userdata['stateid'] != '') {
			$this->db->where_in('cc.add1_state',$stateid); 
		}
		if($this->session->userdata['locationid'] != '') {
			$this->db->where_in('cc.add1_location',$locationid); 
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
		foreach($coun_details as $coun) {
			$countries[] = $coun['country_id'];
		}
		if (!empty($countries)) {
			$countries_ids = array_unique($countries);
			$countries_ids = (array_values($countries)); //reset the keys in the array
		}
		
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
    	$this->db->where('lead_id', $insert_id);
		$this->db->update($this->cfg['dbpref'] . 'leads', $up_args);
    }
    
    function get_lead_assign($level) {
    	$this->db->select('userid', 'first_name');
    	if(!empty($level))
    	$this->db->where('level', $level);
    	$q = $this->db->get($this->cfg['dbpref'] . 'users');
    	return $q->result_array();
    }
	
	//level restriction
	public function level_restriction() 
	{
		$userdata = $this->session->userdata('logged_in_user');
		if (($userdata['role_id'] == 1 && $userdata['level'] == 1) || ($userdata['role_id'] == 2 && $userdata['level'] == 1)) 
		{
			$cusId = '';
		}
		else
		{
			$cusIds = array();
			$cusIds[] = 0;
			$reg = array();
			$cou = array();
			$ste = array();
			$loc = array();
			switch($userdata['level']){
				case 2:
					$regions = $this->getRegions($userdata['userid'], $userdata['level']); //Get the Regions based on Level
						foreach ($regions as $rgid) {
							$reg[] = $rgid['region_id'];
						}
					$CustomersId = $this->getCustomersIds($reg); //Get the Customer id based on Regions
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['companyid'];
						}
					$cusId = $cusIds;
				break;
				case 3:
					$countries = $this->getCountries($userdata['userid'], $userdata['level']); //Get the Countries based on Level
						foreach ($countries as $couid) {
							$cou[] = $couid['country_id'];
						}
					$CustomersId = $this->getCustomersIds($reg,$cou); //Get the Customer id based on Regions & Countries
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['companyid'];
						}
					$cusId = $cusIds;
				break;
				case 4:
					$states = $this->getStates($userdata['userid'], $userdata['level']); //Get the States based on Level
						foreach ($states as $steid) {
							$ste[] = $steid['state_id'];
						}
					$CustomersId = $this->getCustomersIds($reg,$cou,$ste); //Get the Customer id based on Regions & Countries
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['companyid'];
						}
					$cusId = $cusIds;
				break;
				case 5:
					$locations = $this->getLocations($userdata['userid'], $userdata['level']); //Get the Locations based on Level
						foreach ($locations as $locid) {
							$loc[] = $locid['location_id'];
						}	
					$CustomersId = $this->getCustomersIds($reg,$cou,$ste,$loc); //Get the Customer id based on Regions & Countries
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['companyid'];
						}
					$cusId = $cusIds;
				break;
			}
		}
		return $cusId;
	}
	
	/*Level Restrictions*/
	//For Regions
	public function getRegions($uid, $lvlid) 
	{
		$this->db->select('region_id');
		$this->db->from($this->cfg['dbpref'].'levels_region');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$rg_query = $query->result_array();
		return $rg_query;
	}
	
	//For Countries
	public function getCountries($uid, $lvlid) 
	{
		$this->db->select('country_id');
		$this->db->from($this->cfg['dbpref'].'levels_country');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$cs_query = $query->result_array();
		return $cs_query;
	}
	
	//For States
	public function getStates($uid, $lvlid) 
	{
		$this->db->select('state_id');
		$this->db->from($this->cfg['dbpref'].'levels_state');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$ste_query = $query->result_array();
		return $ste_query;
	}
	
	//For Locations
	public function getLocations($uid, $lvlid) 
	{
		$this->db->select('location_id');
		$this->db->from($this->cfg['dbpref'].'levels_location');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$loc_query = $query->result_array();
		return $loc_query;
	}
	
	//For Customers
	public function getCustomersIds($regId = FALSE, $couId = FALSE, $steId = FALSE, $locId = FALSE) 
	{
		$this->db->select('companyid');
		$this->db->from($this->cfg['dbpref'].'customers_company');
		if (!empty($regId)) {
			$this->db->where_in('add1_region', $regId);
		}
		if (!empty($couId)) {
			$this->db->where_in('add1_country', $couId);
		}
		if (!empty($steId)) {
			$this->db->where_in('add1_state', $steId);
		}
		if (!empty($locId)) {
			$this->db->where_in('add1_location', $locId);
		}
		$query = $this->db->get();
		$res_query =  $query->result_array();
		return $res_query;
	}
	/*Level Restrictions*/
	
	/*
	*@method check_isclient_stat()
	*@param customer_id
	*@return row count
	*/
	public function check_isclient_stat($cus_id)
	{
		$this->db->select('lead_status');
		$this->db->from($this->cfg['dbpref'].'leads');
		$this->db->where_in('custid_fk', $cus_id);
		$this->db->where('lead_status', 4);
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->num_rows();
	}	
	
	/*
	*@method updtCustomerIsClient()
	*@param customer_id
	*
	*/
	public function updtCustomerIsClient($cus_id, $updt)
	{
    	$this->db->where('custid', $cus_id);
		return $this->db->update($this->cfg['dbpref'] . 'customers', $updt);
	}
	
	/*
	*@method updtCustomerIsClient()
	*@param customer_id
	*
	*/
	public function get_field_values($table, $where_filed, $where_field_value, $filed_name)
	{	
		$this->db->select($filed_name);
		$this->db->from($this->cfg['dbpref'].$table);
		$this->db->where($where_filed, $where_field_value);
		$query = $this->db->get();
		$result =  $query->row_array();
		return $result[$filed_name];
	}
	
	/*
	*@method get_saved_search
	*/
	function get_saved_search($user_id, $search_for) 
	{
    	$this->db->select('search_id,search_name,is_default');
		$this->db->where('user_id', $user_id);
		$this->db->where('search_for', $search_for);
    	$this->db->order_by('search_id');
		$sql = $this->db->get($this->cfg['dbpref'] . 'saved_search_critriea');
		return $sql->result_array();
    }
	
	function update_records($tbl, $wh_condn, $not_wh_condn, $up_arg) {
    	$this->db->where($wh_condn);
		if(!empty($not_wh_condn) && $not_wh_condn != '') {
			foreach($not_wh_condn as $key=>$value) {
				$this->db->where($key.'!=',$value);
			}
		}
		return $this->db->update($this->cfg['dbpref'] . $tbl, $up_arg);
    }
	
	function delete_records($tbl, $condn) 
	{
		$this->db->where($condn);
        $this->db->delete($this->cfg['dbpref'].$tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	//Folder creation code start
	function insert_default_folder($project_id, $title) {
		
		$this->db->where('parent',0);
		$this->db->where('folder_name',$project_id);
		$check=$this->db->get($this->cfg['dbpref'].'file_management')->row();
		if(empty($check)) {
			//root entry
			$root_data				= new stdClass();
			$root_data->lead_id		= $project_id;
			$root_data->folder_name	= $project_id;
			$root_data->parent		= 0;
			$root_data->created_by	= $this->userdata['userid'];
			$this->db->insert($this->cfg['dbpref'].'file_management', $root_data);
			$root_id				= $this->db->insert_id();
		} else {
			$root_id                = $check->folder_id;
		}
		//parent entry
		$this->db->where('parent_id',0);
		$result=$this->db->get($this->cfg['dbpref'].'default_folder')->result_array();
		$i=0;
		foreach($result as $value)
		{
			$data			   = new stdClass();
			$data->lead_id	   = $project_id;
			$data->folder_name = $value['folder_name'];
			$data->parent	   = $root_id;
			$data->created_by  = $this->userdata['userid'];
			$this->db->insert($this->cfg['dbpref'].'file_management', $data);
			$parent_id = $this->db->insert_id();
			/* if('Quality Control Documents'==$value['folder_name']) {
				//inserting QMS file
				$title = str_replace(' ', '_', $title);
				$qms_file 		= UPLOAD_PATH.'template_file/QMS_Template.xls';
				$new_qms_file 	= UPLOAD_PATH.'files/'.$project_id.'/'.$title.'_QMS_Procedure_Documents_and_Approvals.xls';

				if (copy($qms_file, $new_qms_file)) {
					$lead_files 						 = array();
					$lead_files['lead_files_name'] 		 = $title.'_QMS_Procedure_Documents_and_Approvals.xls';
					$lead_files['lead_files_created_by'] = $this->userdata['userid'];
					$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
					$lead_files['lead_id'] 				 = $project_id;
					$lead_files['folder_id'] 			 = $parent_id;
					$insert_logs 						 = $this->db->insert($this->cfg['dbpref'].'lead_files', $lead_files);
				}
				
			} */
			
			$this->db->where('parent_id', $value['id']);
			$sub_result = $this->db->get($this->cfg['dbpref'].'default_folder')->result_array();
			
			if(count($sub_result))
			{
				foreach($sub_result as $sub_value)
				{
					$sub_data			   = new stdClass();
					$sub_data->lead_id	   = $project_id;
					$sub_data->folder_name = $sub_value['folder_name'];
					$sub_data->parent	   = $parent_id;
					$sub_data->created_by  = $this->userdata['userid'];
					$this->db->insert($this->cfg['dbpref'].'file_management', $sub_data);
				}
			}
		}
		//inserting QMS file
		$title = str_replace(' ', '_', $title);
		$qms_file 		= UPLOAD_PATH.'template_file/QMS_Template.xls';
		$new_qms_file 	= UPLOAD_PATH.'files/'.$project_id.'/'.'QMS_Procedure_Documents_and_Approvals_Checklist.xls';
		if (copy($qms_file, $new_qms_file)) {
			$lead_files 						 = array();
			$lead_files['lead_files_name'] 		 = 'QMS_Procedure_Documents_and_Approvals_Checklist.xls';
			$lead_files['lead_files_created_by'] = $this->userdata['userid'];
			$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
			$lead_files['lead_id'] 				 = $project_id;
			$lead_files['folder_id'] 			 = $root_id;
			$insert_logs 						 = $this->db->insert($this->cfg['dbpref'].'lead_files', $lead_files);
		}

		$asset_file 		= UPLOAD_PATH.'template_file/Asset_Template.xls';
		$new_asset_file 	= UPLOAD_PATH.'files/'.$project_id.'/'.'Asset_Classification_Register.xls';
		if (copy($asset_file, $new_asset_file)) {
			$lead_files 						 = array();
			$lead_files['lead_files_name'] 		 = 'Asset_Classification_Register.xls';
			$lead_files['lead_files_created_by'] = $this->userdata['userid'];
			$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
			$lead_files['lead_id'] 				 = $project_id;
			$lead_files['folder_id'] 			 = $root_id;
			$insert_logs 						 = $this->db->insert($this->cfg['dbpref'].'lead_files', $lead_files);
		}
		 
		$project_file 		= UPLOAD_PATH.'template_file/Project_Template.xls';
		$new_project_file 	= UPLOAD_PATH.'files/'.$project_id.'/'.'Project_Metrics.xls';
		if (copy($project_file, $new_project_file)) {
			$lead_files 						 = array();
			$lead_files['lead_files_name'] 		 = 'Project_Metrics.xls';
			$lead_files['lead_files_created_by'] = $this->userdata['userid'];
			$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
			$lead_files['lead_id'] 				 = $project_id;
			$lead_files['folder_id'] 			 = $root_id;
			$insert_logs 						 = $this->db->insert($this->cfg['dbpref'].'lead_files', $lead_files);
		}
    }
	
	public function getLeadFolders($lead_id)
	{
		$this->db->select('folder_id, folder_name, parent');
		$this->db->where(array('lead_id'=>$lead_id));
		$this->db->from($this->cfg['dbpref'].'file_management');
		$result = $this->db->get()->result_array();
		return $result;
	}
	
	public function get_tree_file_list_except_root($lead_id, $parentId=0 , $counter=-1) {
		$arrayVal = array();
		
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('parent = '. (int) $parentId);
		$results = $this->db->get()->result();
		
		foreach($results as $result) {
			if($result->folder_name!=$lead_id){
				$isparent = $this->checkisparent($result->folder_id, $lead_id);
				if($isparent=='parent')
				$folder_options = '<i class="fa fa-folder-open"></i>'.$result->folder_name;
				else
				$folder_options = '<i class="fa fa-folder"></i>'.$result->folder_name;
				$arrayVal[$result->folder_id] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $counter)."{$folder_options}";
			}
			$arrayVal = $arrayVal + $this->get_tree_file_list_except_root($lead_id, $result->folder_id, $counter+1);
		}
        return $arrayVal;
	}
	
	public function checkisparent($folderid, $leadid){
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $leadid);
		$this->db->where('parent = '. (int) $folderid);
		$results = $this->db->get()->num_rows();
		if($results>0){
			return 'parent';
		}else{
			return 'noparent';
		}
	}
	
	public function getLeadTeamMembers($lead_id)
	{
		$stake_holders = array();
		$team_members  = array();
		$this->db->select('cj.userid_fk, u.first_name, u.last_name');
		$this->db->where('cj.jobid_fk',$lead_id);
		$this->db->from($this->cfg['dbpref'] . 'contract_jobs cj');
		$this->db->join($this->cfg['dbpref'] . 'users u', 'u.userid=cj.userid_fk');
		$this->db->order_by('first_name','asc');
		$team_members = $this->db->get()->result_array();
		
		$this->db->select('sh.user_id as userid_fk, u.first_name, u.last_name', false);
		$this->db->where('sh.lead_id',$lead_id);
		$this->db->from($this->cfg['dbpref'] . 'stake_holders sh');
		$this->db->join($this->cfg['dbpref'] . 'users u', 'u.userid=sh.user_id');
		$this->db->order_by('first_name','asc');
		$stake_holders = $this->db->get()->result_array();
		
		$team = array_merge_recursive($team_members, $stake_holders);
		// echo "<pre>"; print_r($team); exit;
		
		return $team;
	}
	
	public function checkIsFolderAccessRecordExist($lead_id, $folder_id, $user_id)
	{
		$this->db->where(array(
            'lead_id' => $lead_id,
			'folder_id' => $folder_id,
			'user_id' => $user_id
        ));
		$this->db->from($this->cfg['dbpref'].'lead_folder_access');
        $result = $this->db->get()->row_array();
		
		if(!empty($result))	{
			return $result;
		} else {
			return FALSE;
		}
	}

	public function get_folders_access($lead_id)
	{
		$this->db->where(array('lead_id' => $lead_id));
		$this->db->from($this->cfg['dbpref'].'lead_folder_access');
        return $this->db->get()->result_array();
	}
	
	public function updateFolderAccessRecord($exist_record_id, $record_array)
	{
		$this->db->where('lead_folder_access_id', $exist_record_id);
		return $this->db->update($this->cfg['dbpref'].'lead_folder_access', $record_array); 

	}
	
	public function createFolderAccessRecord($record_array)
	{
		// echo "<pre>"; print_r($record_array); exit;
		$this->db->insert($this->cfg['dbpref'].'lead_folder_access', $record_array);
		// echo $this->db->last_query() . "<br/>";
	}

	function get_rscl($id, $cond, $table_name, $ch_name)
	{
		$res = 'no_id';
		if( empty($id) && empty($cond) ) {
			$whr_cond = array('lower('.$table_name.'_name'.')'=>$ch_name);
		} else {
			$whr_cond = array('lower('.$table_name.'_name'.')'=>$ch_name, $cond=>$id);
		}
		$this->db->select($table_name.'id');
		$results = $this->db->get_where($this->cfg['dbpref'].$table_name, $whr_cond)->row_array();
		if(!empty($results)) {
			$res = $results[$table_name.'id'];
		}
		return $res;				
	}
	
	function get_rscl_return_id($id, $cond, $table_name, $ch_name){		
		if( empty($id) && empty($cond) ) {
			$whr_cond = array('lower('.$table_name.'_name'.')'=>$ch_name);
		} else {
			$whr_cond = array('lower('.$table_name.'_name'.')'=>$ch_name, $cond=>$id);
		}
		$this->db->select($table_name.'id');
		$results = $this->db->get_where($this->cfg['dbpref'].$table_name, $whr_cond)->row_array();
		if(!empty($results)) {
			$strreg = $results[$table_name.'id'];
		} else {
			$user_Detail = $this->session->userdata('logged_in_user');
			if( empty($id) && empty($cond) ) {
				$args = array(
					$table_name.'_name' => $ch_name,
					'created_by' => $user_Detail['userid'],
					'modified_by' => $user_Detail['userid'],
					'created' => date('Y-m-d H:i:s'),
					'modified' => date('Y-m-d H:i:s')
				);
			} else {						
				$args = array(
					$cond => $id,
					$table_name.'_name' => $ch_name,
					'created_by' => $user_Detail['userid'],
					'modified_by' => $user_Detail['userid'],
					'created' => date('Y-m-d H:i:s'),
					'modified' => date('Y-m-d H:i:s')
				);	
			}
			$this->db->insert($this->cfg['dbpref'].$table_name, $args); 
			$strreg = $this->db->insert_id();
		}
		return $strreg;				
	}
	
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
	
	public function getClosedJobids($cusId = FALSE, $filter = FALSE) 
	{
		$date_range = false;
		$leadid_arr = array();
		
		if(isset($filter) && $filter['from_date'] == '0000-00-00 00:00:00'){
			$lead_created_from_date = '';
		}
		if(isset($filter) && $filter['to_date'] == '0000-00-00 00:00:00'){
			$lead_created_to_date = '';
		}
		
		if(isset($filter) && $filter['month_year_from_date']) {
			$from_date  = date('Y-m-d', strtotime($filter['month_year_from_date']));
			$date_range = true;
		}
		if(isset($filter) && $filter['month_year_to_date']) {
			$to_date    = date('Y-m-t', strtotime($filter['month_year_to_date']));
			$date_range = true;
		}
		
		if($date_range == true)
		{
			//get the ids from the lead stage history table			
			$this->db->select('lead_id, dateofchange');
			$this->db->from($this->cfg['dbpref'].'lead_status_history');
			$this->db->where("changed_status", 4);
			// echo'<pre>from_date=>';print_r($from_date);
			// echo'<pre>to_date=>';print_r($to_date);exit;
			if(!empty($from_date) && empty($to_date)) {
				$this->db->where('DATE(dateofchange) >=', $from_date);
			} else if(!empty($from_date) && !empty($to_date)) {
				$this->db->where('DATE(dateofchange) >=', $from_date);
				$this->db->where('DATE(dateofchange) <=', $to_date);
			} else if(empty($from_date) && !empty($to_date)) {
				$this->db->where('DATE(dateofchange) <=', $to_date);
			}
			// if(empty($from_date) && empty($to_date)){
				// $this->db->where('DATE(dateofchange) >=', date('Y-m'));
			// }
			$this->db->order_by('dateofchange', 'desc');
			$sql = $this->db->get();

			$res_arr = $sql->result_array();
			// echo "<pre>"; print_r($res_arr); die;
			if(!empty($res_arr) && count($res_arr)>0){
				foreach($res_arr as $row)
				$leadid_arr[] = $row['lead_id'];
			}
			// echo $this->db->last_query(); exit;
			// echo "<pre>"; print_r($leadid_arr); die;
		}
		
		
		// $pjt_stat = array(0,1,2,3);
		$pjt_stat = array(1,2,3);
		
		// my fiscal year starts on July,1 and ends on June 30, so... $curYear = date("Y");
		//eg. calculateFiscalYearForDate("5/15/08","7/1","6/30"); m/d/y
		// $curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");

		// $frm_dt = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $to_dt = $curFiscalYear."-03-31"; //eg.2014-03-01
		$this->db->select('j.lead_id, j.invoice_no, j.lead_title, j.expect_worth_id, j.actual_worth_amount, j.lead_status, j.date_created, j.belong_to, j.lead_assign, j.pjt_status, cc.company, c.customer_name, rg.region_name, co.country_name, u.first_name as ufname, u.last_name as ulname, ub.first_name as ubfn, ub.last_name as ubln, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads as j');
		$this->db->join($this->cfg['dbpref'].'users as u', 'u.userid = j.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users as ub', 'ub.userid = j.belong_to');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->join($this->cfg['dbpref'].'region as rg', 'rg.regionid = cc.add1_region');
		$this->db->join($this->cfg['dbpref'].'country as co', 'co.countryid = cc.add1_country');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
		$this->db->where('j.lead_status', 4);
		$this->db->where('j.customer_type', 1);
		if ($this->userdata['level']!= 1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(j.belong_to = '.$this->userdata['userid'].' OR j.lead_assign = '.$this->userdata['userid'].' OR j.assigned_to ='.$this->userdata['userid'].')';
			$this->db->where($reseller_condn);
		}
		if(!empty($leadid_arr) && count($leadid_arr)) {
			$this->db->where_in('j.lead_id',$leadid_arr);
		}
		
		if(isset($filter['from_date']) && !empty($filter['from_date']) && empty($filter['to_date'])) {
			$dt_query =  'DATE(j.date_created) >= "'.date('Y-m-d', strtotime($filter['from_date'])).'"';
			// echo'<pre>';print_r($dt_query);exit;
			$this->db->where($dt_query);
		} else if(isset($filter['to_date']) && !empty($filter['to_date']) && empty($filter['from_date'])) {	
			$dt_query = 'DATE(j.date_created) <= "'.date('Y-m-d', strtotime($filter['to_date'])).'"';
			// echo'<pre>';print_r($dt_query);exit;
			$this->db->where($dt_query);
		} else if(isset($filter['from_date']) && !empty($filter['from_date']) && isset($filter['to_date']) && !empty($filter['to_date'])) {
			$dt_query = '(DATE(j.date_created) >= "'.date('Y-m-d', strtotime($filter['from_date'])).'" AND DATE(j.date_created) <= "'.date('Y-m-d', strtotime($filter['to_date'])).'")';
			 // echo'<pre>';print_r($dt_query);exit;
			$this->db->where($dt_query);
		}
		
		if (!empty($filter['customer']) && ($filter['customer']!='null')) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['owner']) && ($filter['owner']!='null')) {
			$this->db->where_in('j.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee']) && ($filter['leadassignee']!='null')) {
			$this->db->where_in('j.lead_assign', $filter['leadassignee']);
		}
		if (!empty($filter['regionname']) && ($filter['regionname']!='null')) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname']) && ($filter['countryname']!='null')) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename']) && ($filter['statename']!='null')) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname']) && ($filter['locname']!='null')) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['service']) && ($filter['service']!='null')) {
			$this->db->where_in('j.lead_service', $filter['service']);
		}
		if (!empty($filter['lead_src']) && ($filter['lead_src']!='null')) {
			$this->db->where_in('j.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry']) && ($filter['industry']!='null')) {
			$this->db->where_in('j.industry', $filter['industry']);
		}		
   		$this->db->where_in('j.pjt_status', $pjt_stat);
		
		// closed_opportunities
		
		$this->db->order_by('j.lead_id', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$cls_query =  $query->result_array();
		return $cls_query;
	}
	
	function get_record($tbl, $wh_condn)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		$this->db->where($wh_condn);
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->row_array();
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
	
	public function get_lead_dashboard_field($id)
	{
		$this->db->select("column_name");
		$this->db->from($this->cfg['dbpref'].'lead_dashboard_fields');
		$this->db->where('user_id', $id);
		$this->db->order_by('column_order', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}
}

?>
