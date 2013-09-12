<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends Common_model {
    
    public function __construct()
    {
        parent::__construct();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode("','", $this->stg);
    }
	
	//Dashboard functionality
	public function getTotLeads($cusId = FALSE) {
	
		$this->db->select('lstg.lead_stage_name, COUNT( * )');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'lead_stage lstg', 'lstg.lead_stage_id = jb.job_status');
   		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status',1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->group_by('jb.job_status');
		$this->db->order_by('jb.job_status', 'asc');
		$query = $this->db->get();
		$tot_query =  $query->result_array();
		// echo $this->db->last_query(); exit;
		return $tot_query;
	}
	
	public function getLeadsByReg($cusId = FALSE) {
		//print_r($custId); exit;
		// $job_status = array(1,2,3,4,5,6,7,8,9,10,11,12);
		
		$this->db->select('rg.region_name, coun.country_name, ste.state_name, loc.location_name, j.expect_worth_amount, ew.expect_worth_name, ew.expect_worth_id');	
		$this->db->join($this->cfg['dbpref'].'customers c','custid = j.custid_fk','inner');
		$this->db->join($this->cfg['dbpref'].'region rg','rg.regionid=c.add1_region','inner');
		$this->db->join($this->cfg['dbpref'].'country coun','coun.countryid=c.add1_country','inner');
		$this->db->join($this->cfg['dbpref'].'state ste','ste.stateid=c.add1_state','inner');
		$this->db->join($this->cfg['dbpref'].'location loc','loc.locationid=c.add1_location','inner');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id = j.expect_worth_id','inner');
		$this->db->where('j.lead_status',1);
		// $this->db->where_in('j.job_status', $job_status);
		$this->db->where_in('j.job_status', $this->stg);
		
		if ($this->userdata['level']==1) {
			//$this->db->where_in('j.custid_fk', $cusId);
			$this->db->order_by('rg.region_name', 'ASC');
		}
		if ($this->userdata['level']==2) {
			$this->db->where_in('j.custid_fk', $cusId);
			$this->db->order_by('coun.country_name', 'ASC');
		}
		if ($this->userdata['level']==3) {
			$this->db->where_in('j.custid_fk', $cusId);
			$this->db->order_by('ste.state_name', 'ASC');
		}
		if (($this->userdata['level']==4) || ($this->userdata['level']==5)) {
			$this->db->where_in('j.custid_fk', $cusId);
			$this->db->order_by('loc.location_name', 'ASC');
		}
		$query = $this->db->get($this->cfg['dbpref'].'jobs j');
		//echo $this->db->last_query();
		$result['res'] = $query->result();
		$result['num'] = $query->num_rows();
		
		return $result;
	}
	
	public function getLeadsByOwner($cusId = FALSE) {
		$this->db->select('us.userid, COUNT( * ), SUM(jb.expect_worth_amount) as amt, CONCAT(us.first_name," ",us.last_name) as user_name', FALSE);
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'users us', 'us.userid = jb.belong_to');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status',1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->group_by('jb.belong_to');
		$this->db->order_by('us.first_name', 'asc');
		$query = $this->db->get();
		$own_query = $query->result_array();
		return $own_query;
	}
	
	public function getLeadsByAssignee($cusId = FALSE) {
		$this->db->select('us.userid, COUNT( * ), CONCAT(us.first_name," ",us.last_name) as user_name', FALSE);
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'users us', 'us.userid = jb.lead_assign');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status',1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->group_by('jb.lead_assign');
		$this->db->order_by('us.first_name', 'asc');
		$query = $this->db->get();
		$assg_query = $query->result_array();
		return $assg_query;
	}
	
	public function getLeadsIndicator($cusId = FALSE) {
		/*
		$this->db->select('COUNT(lead_indicator), jb.lead_indicator');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->where('jb.job_status BETWEEN 1 AND 12');
		$this->db->where('jb.lead_status',1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk', $cusId);
		}
		$this->db->group_by('jb.lead_indicator');
		$this->db->order_by('jb.lead_indicator', 'asc');
		
		$query = $this->db->get();
		//echo $this->db->last_query(); exit;
		$least_query = $query->result_array();
		return $least_query;
		*/
		//echo $cusId; exit;
		
		// $stages = @implode("','", $this->stg);
		
		$where_level = "";
		if (!empty($cusId)) {
			$cusId = implode("','", $cusId);
			if ($this->userdata['level']!=1) {
				$where_level = " AND custid_fk IN ('".$cusId."')";
			}
		}
		$indicator_query = $this->db->query("SELECT COUNT(
			CASE WHEN lead_indicator = 'HOT'
			THEN lead_indicator
			END ) AS 'HOT', COUNT(
			CASE WHEN lead_indicator = 'WARM'
			THEN lead_indicator
			END ) AS 'WARM', COUNT(
			CASE WHEN lead_indicator = 'COLD'
			THEN lead_indicator
			END ) AS 'COLD'
			FROM ".$this->cfg['dbpref']."jobs
			WHERE job_status IN ('".$this->stages."')
			AND lead_status =1".$where_level);
		
			return $indicator_query->row_array();
	}
	
	public function getIndiLeads($cusId = FALSE, $indi) {
	
		$this->db->select('jb.jobid, jb.invoice_no, jb.job_title,ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.expect_worth_amount, jb.lead_indicator, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('custid_fk',$cusId);
		}
		$this->db->where('jb.lead_indicator', $indi);
		$this->db->order_by('jb.jobid', 'desc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		$ind_query = $query->result_array();
		return $ind_query;
		
	}
	
	public function getLeastLeadsCount($cusId = FALSE) {
		/*
		$cnt_query = $this->db->query("SELECT `lead_indicator`,count(`lead_indicator`) FROM `".$this->cfg['dbpref']."jobs` WHERE `job_status` between 1 and 12 and `lead_status` = 1 and `lead_indicator` !='HOT' GROUP BY `lead_indicator` ORDER BY `lead_indicator`");
		*/
		$this->db->select('lead_indicator, count(`lead_indicator`)');
		$this->db->from($this->cfg['dbpref'].'jobs');
		$this->db->where_in('job_status', $this->stg);
		$this->db->where('lead_status',1);
		$this->db->where('lead_indicator != ', 'HOT');
		if ($this->userdata['level']!=1) {
			$this->db->where_in('custid_fk',$cusId);
		}
		$this->db->GROUP_BY('lead_indicator');
		$this->db->order_by('lead_indicator', 'asc');
		$this->db->order_by('jobid', 'asc');
		$query = $this->db->get();
		$cnt_query =  $query->result_array();
		return $cnt_query;
	}
	
	public function getCurrentActivityLeads($isSelect = 7, $cusId = FALSE) {
		$this->db->select('jb.job_title,jb.invoice_no,ew.expect_worth_id, ew.expect_worth_name, ownr.userid as ownr_userid, jb.jobid, jb.lead_assign, jb.expect_worth_amount, jb.belong_to, usr.first_name as usrfname, usr.last_name as usrlname, ownr.first_name as ownrfname, ownr.last_name as ownrlname');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'users usr', 'usr.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('custid_fk', $cusId);
		}
		$this->db->where('jb.date_modified BETWEEN DATE_SUB(NOW(), INTERVAL '.$isSelect.' DAY) AND NOW()');
		$query = $this->db->get();
		$act_query =  $query->result_array();       
		return $act_query;
	}
	
	public function getLeadsAging($cusId = FALSE)
	{
		
		$todayDate = date('Y-m-d h:m:s');
		$thirtyDays = date('Y-m-d h:m:s', strtotime("now -30 days"));
		$sixtyDays = date('Y-m-d h:m:s', strtotime("now -60 days"));
		$ninetyDays = date('Y-m-d h:m:s', strtotime("now -90 days"));
		$oneTwentyDays = date('Y-m-d h:m:s', strtotime("now -120 days"));
		$oneFiftyDays = date('Y-m-d h:m:s', strtotime("now -150 days"));
		$oneEightyDays = date('Y-m-d h:m:s', strtotime("now -180 days"));
		
		
		$thirtyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $thirtyDays ));
		$thirtyOneDays = date ( 'Y-m-d h:m:s' , $thirtyOneDaysTemp);
		
		$sixtyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $sixtyDays ));
		$sixtyOneDays = date ( 'Y-m-d h:m:s' , $sixtyOneDaysTemp);
		
		$ninetyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $ninetyDays ));
		$ninetyOneDays = date ( 'Y-m-d h:m:s' , $ninetyOneDaysTemp);
		
		$oneTwentyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $oneTwentyDays ));
		$oneTwentyOneDays = date ( 'Y-m-d h:m:s' , $oneTwentyOneDaysTemp);
		
		$oneFiftyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $oneFiftyDays ));
		$oneFiftyOneDays = date ( 'Y-m-d h:m:s' , $oneFiftyOneDaysTemp);
		
		$oneEightyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $oneEightyDays ));
		$oneEightyOneDays = date ( 'Y-m-d h:m:s' , $oneEightyOneDaysTemp);

		$where_level = "";
		if (!empty($cusId)) {
			$cusId = implode("','", $cusId);
			//echo $cusId; exit;
			if ($this->userdata['level']!=1) {
				$where_level = " AND custid_fk IN ('".$cusId."')";
			}
		}
		$age_query = $this->db->query("SELECT 
		COUNT(CASE WHEN DATE(date_created) BETWEEN '".$thirtyDays."' AND '".$todayDate."' THEN DATE(date_created) END) as '0-30 Days',
		COUNT(CASE WHEN DATE(date_created) BETWEEN '".$sixtyDays."' AND '".$thirtyOneDays."' THEN DATE(date_created) END) as '31-60 Days',
		COUNT(CASE WHEN DATE(date_created) BETWEEN '".$ninetyDays."' AND '".$sixtyOneDays."' THEN DATE(date_created) END) as '61-90 Days',
		COUNT(CASE WHEN DATE(date_created) BETWEEN '".$oneTwentyDays."' AND '".$ninetyOneDays."' THEN DATE(date_created) END) as '91-120 Days',
		COUNT(CASE WHEN DATE(date_created) BETWEEN '".$oneFiftyDays."' AND '".$oneTwentyOneDays."' THEN DATE(date_created) END) as '121-150 Days',
		COUNT(CASE WHEN DATE(date_created) BETWEEN '".$oneEightyDays."' AND '".$oneFiftyOneDays."' THEN DATE(date_created) END) as '151-180 Days',
		COUNT(CASE WHEN DATE(date_created) < '".$oneEightyOneDays."' THEN DATE(date_created) END) as 'Above181 Days'
		FROM ".$this->cfg['dbpref']."jobs
		WHERE job_status BETWEEN 1 AND 12
		AND lead_status=1".$where_level);
		return $age_query->row_array();
	}
	
	public function leadAgingLeads($cusId = FALSE, $dt)
	{	
		$todayDate = date('Y-m-d h:m:s');
		$thirtyDays = date('Y-m-d h:m:s', strtotime("now -30 days"));
		$sixtyDays = date('Y-m-d h:m:s', strtotime("now -60 days"));
		$ninetyDays = date('Y-m-d h:m:s', strtotime("now -90 days"));
		$oneTwentyDays = date('Y-m-d h:m:s', strtotime("now -120 days"));
		$oneFiftyDays = date('Y-m-d h:m:s', strtotime("now -150 days"));
		$oneEightyDays = date('Y-m-d h:m:s', strtotime("now -180 days"));
		
		
		$thirtyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $thirtyDays ));
		$thirtyOneDays = date ( 'Y-m-d h:m:s' , $thirtyOneDaysTemp);
		
		$sixtyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $sixtyDays ));
		$sixtyOneDays = date ( 'Y-m-d h:m:s' , $sixtyOneDaysTemp);
		
		$ninetyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $ninetyDays ));
		$ninetyOneDays = date ( 'Y-m-d h:m:s' , $ninetyOneDaysTemp);
		
		$oneTwentyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $oneTwentyDays ));
		$oneTwentyOneDays = date ( 'Y-m-d h:m:s' , $oneTwentyOneDaysTemp);
		
		$oneFiftyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $oneFiftyDays ));
		$oneFiftyOneDays = date ( 'Y-m-d h:m:s' , $oneFiftyOneDaysTemp);
		
		$oneEightyOneDaysTemp = strtotime ( '+1 day' , strtotime ( $oneEightyDays ));
		$oneEightyOneDays = date ( 'Y-m-d h:m:s' , $oneEightyOneDaysTemp);


		switch($dt) {
			case 0:
				$todt = $thirtyDays;
				$frmdt = $todayDate;
			break;	
			case 1:
				$todt = $sixtyDays;
				$frmdt = $thirtyOneDays;
			break;	
			case 2:
				$todt = $ninetyDays;
				$frmdt = $sixtyOneDays;
			break;	
			case 3:
				$todt = $oneTwentyDays;
				$frmdt = $ninetyOneDays;
			break;	
			case 4:
				$todt = $oneFiftyDays;
				$frmdt = $oneTwentyOneDays;
			break;	
			case 5:
				$todt = $oneEightyDays;
				$frmdt = $oneFiftyOneDays;
			break;	
			case 6:
				$todt = $oneEightyOneDays;
			break;	
		}
		
		$this->db->select('jb.jobid, jb.invoice_no, jb.job_title,ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.expect_worth_amount, jb.lead_indicator, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('custid_fk', $cusId);
		}
		if ($dt == 6) {
			$this->db->where('jb.date_created < "'.$todt.'" ');
		} else {
			$this->db->where('jb.date_created BETWEEN "'.$todt.'" AND "'.$frmdt.'" ');
		}
		$this->db->order_by('jb.jobid', 'desc');
		$query = $this->db->get();
		$ag_query = $query->result_array();
		return $ag_query;
	}
	
	/* Get jobid, lead title, region, lead owner, lead assigned to, customer, */
	public function getLeadOwnerDependencies($userid, $cusId = FALSE) {
	    $lead_dependencies = $this->db->select('jb.jobid,jb.job_title,ew.expect_worth_id,cs.first_name as cfname, jb.invoice_no, cs.last_name as clname, jb.lead_assign,jb.lead_indicator, jb.expect_worth_amount, jb.belong_to, usr.first_name as usrfname, usr.last_name as usrlname, ownr.first_name as ownrfname, ownr.last_name as ownrlname, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'users usr', 'usr.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status',1);
		$this->db->where('ownr.userid', $userid);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->order_by('jb.jobid', 'desc');
		$depend_query = $this->db->get();
		//echo $this->db->last_query(); exit;
		return $depend_query;
		
	}

	public function getLeadAssigneeDependencies($userid, $cusId = FALSE) {
	    $lead_dependencies = $this->db->select('jb.jobid, jb.invoice_no, ew.expect_worth_id, jb.job_title,cs.first_name as cfname ,cs.last_name as clname, jb.lead_assign,jb.lead_indicator, jb.expect_worth_amount, jb.belong_to, usr.first_name as usrfname, usr.last_name as usrlname, ownr.first_name as ownrfname, ownr.last_name as ownrlname, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'users usr', 'usr.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status',1);
		$this->db->where('usr.userid', $userid);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->order_by('jb.jobid', 'desc');
		$depend_query = $this->db->get();
		return $depend_query;
		
	}

	public function getLeadsDetails($leadStage, $cusId = FALSE) {
		$this->db->select('jb.jobid, jb.invoice_no, jb.job_title,ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.expect_worth_amount, jb.lead_indicator, ls.lead_stage_name, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'lead_stage ls', 'ls.lead_stage_id = jb.job_status');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
   		$this->db->like('ls.lead_stage_name', $leadStage, 'after');
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('custid_fk',$cusId);
		}
		$this->db->order_by('jb.jobid', 'desc');
		$query = $this->db->get();
		//echo $this->db->last_query(); exit;
		$ld_query =  $query->result_array();
		return $ld_query;
	}
	
	public function getRegionLeadsDetails($leadsRegion, $cusId = FALSE) {
		//echo $leadsRegion; exit;
		$this->db->select('jb.jobid, jb.invoice_no, jb.job_title, ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.expect_worth_amount, jb.lead_indicator, reg.region_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'region reg', 'reg.regionid = cs.add1_region');
		$this->db->join($this->cfg['dbpref'].'country cou', 'cou.countryid = cs.add1_country');
		$this->db->join($this->cfg['dbpref'].'state ste', 'ste.stateid = cs.add1_state');
		$this->db->join($this->cfg['dbpref'].'location loc', 'loc.locationid = cs.add1_location');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');

		if ($this->userdata['level']==1) {
			$this->db->where('reg.region_name', $leadsRegion);
		}
		if (($this->userdata['level'])==2){
			$this->db->where('cou.country_name', $leadsRegion);
		}
		if (($this->userdata['level'])==3){
			$this->db->where('ste.state_name', $leadsRegion);
		}
		if (($this->userdata['level']==4) || ($this->userdata['level']==5)) {
			$this->db->where('loc.location_name', $leadsRegion);
		}
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->order_by('jb.jobid', 'desc');
		$query = $this->db->get();
		$ld_query =  $query->result_array();
		return $ld_query;
	}
	
	public function getCurrentLeadActivity($jobid) {
	    $lead_dependencies = $this->db->select('jb.jobid, ew.expect_worth_id, jb.job_title,cs.first_name as cfname, jb.invoice_no, cs.last_name as clname, jb.lead_assign,jb.lead_indicator, jb.expect_worth_amount, jb.belong_to, usr.first_name as usrfname, usr.last_name as usrlname, ownr.first_name as ownrfname, ownr.last_name as ownrlname, ew.expect_worth_name');
							 $this->db->from($this->cfg['dbpref'].'jobs jb');
							 $this->db->join($this->cfg['dbpref'].'users usr', 'usr.userid = jb.lead_assign');
							 $this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
							 $this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
							 $this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
							 $this->db->where_in('jb.job_status', $this->stg);
							 $this->db->where('jb.lead_status',1);
							 $this->db->where('jb.jobid', $jobid);
							 $this->db->order_by('jb.jobid', 'desc');
		$depend_query = $this->db->get();
		return $depend_query;
	}
	
	public function LeadDetails($jid) {
		$this->db->select('jb.invoice_no, jb.job_title,ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.expect_worth_amount, jb.lead_indicator, ls.lead_stage_name, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'lead_stage ls', 'ls.lead_stage_id = jb.job_status');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
   		$this->db->where('jb.jobid', $jid);
		$this->db->where('jb.lead_status', 1);
		$query = $this->db->get();
		$lst_query =  $query->row_array();
		return $lst_query;
	}
	
	/*Level Restrictions*/
	//For Regions
	public function getRegions($uid, $lvlid) {
		$this->db->select('region_id');
		$this->db->from($this->cfg['dbpref'].'levels_region');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$rg_query =  $query->result_array();
		return $rg_query;
	}
	
	//For Countries
	public function getCountries($uid, $lvlid) {
		$this->db->select('country_id');
		$this->db->from($this->cfg['dbpref'].'levels_country');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$cs_query =  $query->result_array();
		return $cs_query;
	}
	
	//For States
	public function getStates($uid, $lvlid) {
		$this->db->select('state_id');
		$this->db->from($this->cfg['dbpref'].'levels_state');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$ste_query =  $query->result_array();
		return $ste_query;
	}
	
	//For States
	public function getLocations($uid, $lvlid) {
		$this->db->select('location_id');
		$this->db->from($this->cfg['dbpref'].'levels_location');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$loc_query =  $query->result_array();
		return $loc_query;
	}
	
	//For Customers
	public function getCustomersIds($regId = FALSE, $couId = FALSE, $steId = FALSE, $locId = FALSE) {
		$this->db->select('custid');
		$this->db->from($this->cfg['dbpref'].'customers');
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
	
	//for closed opportunities getClosedJobids
	public function getClosedJobids($cusId = FALSE) {
		$jb_stat = array('13','14','16');
		$curYear = date("Y");
		$frm_dt = $curYear."-04-01";
		$to_dt = ($curYear+1)."-03-31";
		$this->db->select('`jobid` , `expect_worth_id` , actual_worth_amount as expect_worth_amount');
		$this->db->from('`'.$this->cfg['dbpref'].'jobs`');
		$this->db->where('`lead_status`', 1);
		if ($this->userdata['level']!= 1) {
			$this->db->where_in('custid_fk',$cusId);
		}
   		$this->db->where_in('job_status', $jb_stat);
   		$this->db->where('date_modified BETWEEN "'.$frm_dt.'" AND "'.$to_dt.'" ');
		$this->db->order_by('jobid', 'desc');
		$query = $this->db->get();
		//echo $this->db->last_query(); exit;
		$cls_query =  $query->result_array();
		return $cls_query;
	}
	
	//For Closed Opportunities Leads only - actual_worth_amount
	public function closedLeadDet($jbid) {
		//echo "<pre>"; print_r($jbid); exit;
		if (!empty($jbid)) {
			$this->db->select('jb.jobid, jb.invoice_no, jb.job_title,ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.actual_worth_amount as expect_worth_amount, jb.lead_indicator, ls.lead_stage_name, ew.expect_worth_name');
			$this->db->from($this->cfg['dbpref'].'jobs jb');
			$this->db->join($this->cfg['dbpref'].'lead_stage ls', 'ls.lead_stage_id = jb.job_status');
			$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
			$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
			$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
			$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
			$this->db->where_in('jb.jobid', $jbid);
			$this->db->where('jb.lead_status', 1);
			$this->db->order_by('jb.jobid', 'desc');
			$query = $this->db->get();
			$rest_query =  $query->result_array();
			return $rest_query;
		} else {
			return "no records";
		}
	}
	
	public function getLeadSource($cusId = FALSE) {
		$this->db->select('ldsrc.lead_source_name, count(`lead_source`) as src');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'lead_source ldsrc', 'ldsrc.lead_source_id = jb.lead_source');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status',1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->GROUP_BY('jb.lead_source');
		$query = $this->db->get();
		//echo $this->db->last_query(); exit;
		$ls_query =  $query->result_array();
		return $ls_query;
	}
	
	public function getServiceReq($cusId = FALSE) {
		$this->db->select('jc.category, count(`job_category`) as job_cat');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'job_categories jc', 'jc.cid = jb.job_category');
		$this->db->where_in('jb.job_status', $this->stg);
		$this->db->where('jb.lead_status',1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('jb.custid_fk',$cusId);
		}
		$this->db->GROUP_BY('jb.job_category');
		$query = $this->db->get();
		//echo $this->db->last_query(); exit;
		$sq_query =  $query->result_array();
		return $sq_query;
	}
	
	public function getLeadsDetails_pie2($leadStage, $cusId = FALSE) {
		$this->db->select('jb.jobid, jb.invoice_no, jb.job_title,ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.expect_worth_amount, jb.lead_indicator, ldsrc.lead_source_name, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'lead_source ldsrc', 'ldsrc.lead_source_id = jb.lead_source');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
   		$this->db->like('ldsrc.lead_source_name', $leadStage);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('custid_fk',$cusId);
		}
		$this->db->order_by('jb.jobid', 'desc');
		$query = $this->db->get();
		$ldsr_query =  $query->result_array();
		return $ldsr_query;
	}
	
	public function getLeadsDetails_pie3($leadStage, $cusId = FALSE) {
		$this->db->select('jb.jobid, jb.invoice_no, jb.job_title, ew.expect_worth_id, cs.first_name, cs.last_name, owr.first_name as owrfname, owr.last_name as owrlname, assi.first_name as assifname, assi.last_name as assilname, jb.expect_worth_amount, jb.lead_indicator, jbc.category, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'job_categories jbc', 'jbc.cid = jb.job_category');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.job_status', $this->stg);
   		$this->db->like('jbc.category', $leadStage);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('custid_fk',$cusId);
		}
		$this->db->order_by('jb.jobid', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$serreq_query =  $query->result_array();
		return $serreq_query;
	}

}

?>
