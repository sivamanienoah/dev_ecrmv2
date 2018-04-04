<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends crm_model {
    
    public function __construct()
    {
        parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg 		= getLeadStage();
		$this->stages 	= @implode("','", $this->stg);
		$this->userdata = $this->session->userdata('logged_in_user');
    }
	
	//Dashboard functionality
	public function getTotLeads($cusId = FALSE, $filter = FALSE) {
		// echo "<pre>"; print_r($filter); exit;
		$this->db->select('lstg.lead_stage_name, COUNT( * )');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_stage lstg', 'lstg.lead_stage_id = jb.lead_stage');
		$this->db->join($this->cfg['dbpref']. 'customers as c', 'c.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref']. 'customers_company as cc', 'cc.companyid = c.company_id');
   		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			// $reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.lead_assign = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].')';			
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('jb.lead_stage', $filter['stage']);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('jb.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('jb.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('jb.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('jb.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('jb.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('jb.lead_indicator', $filter['lead_indi']);
		}
		$this->db->group_by('jb.lead_stage');
		// $this->db->group_by('jb.lead_id');
		$this->db->order_by('jb.lead_stage', 'asc');
		$query = $this->db->get();
		$tot_query =  $query->result_array();
		// echo $this->db->last_query(); EXIT;
		return $tot_query;
	}
	
	//Dashboard functionality
	function getTotLead($cusId = FALSE, $filter) {
		$this->db->select('lstg.lead_stage_name, COUNT( * )');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_stage lstg', 'lstg.lead_stage_id = jb.lead_stage');
   		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if ($filter['customer'] !='null') {
			$this->db->where('jb.custid_fk in ('.$filter['customer'].')');
		}
		$this->db->group_by('jb.lead_stage');
		$this->db->order_by('jb.lead_stage', 'asc');
		$query = $this->db->get();
		$tot_query =  $query->result_array();
		// echo $this->db->last_query(); exit;
		return $tot_query;
	}
	
	public function getLeadsByReg($cusId = FALSE, $filter = FALSE) {
		// $lead_stage = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$this->db->select('rg.region_name, coun.country_name, ste.state_name, loc.location_name, j.expect_worth_amount, ew.expect_worth_name, ew.expect_worth_id');	
		$this->db->join($this->cfg['dbpref'].'customers c','c.custid = j.custid_fk','inner');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id','inner');
		$this->db->join($this->cfg['dbpref'].'region rg','rg.regionid=cc.add1_region','inner');
		$this->db->join($this->cfg['dbpref'].'country coun','coun.countryid=cc.add1_country','inner');
		$this->db->join($this->cfg['dbpref'].'state ste','ste.stateid=cc.add1_state','inner');
		$this->db->join($this->cfg['dbpref'].'location loc','loc.locationid=cc.add1_location','inner');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id = j.expect_worth_id','inner');
		$this->db->where('j.lead_status',1);
		// $this->db->where_in('j.lead_stage', $lead_stage);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			// $this->db->where('c.created_by', $this->userdata['userid']);
			$reseller_condn = '(j.belong_to = '.$this->userdata['userid'].' OR j.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', j.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		$this->db->where_in('j.lead_stage', $this->stg);
		
		if ($this->userdata['level']==1) {
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
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('j.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('j.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('j.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', j.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('j.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('j.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('j.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('j.lead_indicator', $filter['lead_indi']);
		}
		$query = $this->db->get($this->cfg['dbpref'].'leads j');
		// echo $this->db->last_query();
		$result['res'] = $query->result();
		$result['num'] = $query->num_rows();
		
		return $result;
	}
	
	public function getLeadsByOwner($cusId = FALSE, $filter = FALSE) {
		$this->db->select('us.userid, COUNT( * ), SUM(jb.expect_worth_amount) as amt, CONCAT(us.first_name," ",us.last_name) as user_name', FALSE);
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'users us', 'us.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'customers c','c.custid = jb.custid_fk','inner');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id','inner');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('jb.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('jb.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('jb.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('jb.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('jb.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('jb.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('jb.lead_indicator', $filter['lead_indi']);
		}
		$this->db->group_by('jb.belong_to');
		$this->db->order_by('us.first_name', 'asc');
		$query = $this->db->get();
		$own_query = $query->result_array();
		return $own_query;
	}
	
	public function getLeadsByAssignee($cusId = FALSE, $filter = FALSE) {
		$this->db->select('us.userid, COUNT( * ), CONCAT(us.first_name," ",us.last_name) as user_name', FALSE);
		$this->db->from($this->cfg['dbpref'].'leads jb');
		// $this->db->join($this->cfg['dbpref'].'users us', 'us.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users as us',' FIND_IN_SET (us.userid , jb.lead_assign) ');
		$this->db->join($this->cfg['dbpref'].'customers c','c.custid = jb.custid_fk','inner');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id','inner');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('jb.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('jb.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('jb.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('jb.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('jb.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('jb.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('jb.lead_indicator', $filter['lead_indi']);
		}
		$this->db->group_by('us.userid');
		$this->db->order_by('us.first_name', 'asc');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$assg_query = $query->result_array();
		return $assg_query;
	}
	
	public function getLeadsIndicator($cusId = FALSE, $filter = FALSE) {
		/*
		$this->db->select('COUNT(lead_indicator), jb.lead_indicator');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->where('jb.lead_stage BETWEEN 1 AND 12');
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
				$where_level = " AND cc.companyid IN ('".$cusId."')";
			}
		}
		$condn = "";
		if (!empty($filter['customer'])) {
			$custom = implode("','", $filter['customer']);
			$condn  = " AND cc.companyid IN ('".$custom."')";
		}
		if (!empty($filter['stage'])) {
			$stg    = implode("','", $filter['stage']);
			$condn .= " AND j.lead_stage IN ('".$stg."')";
		}
		if (!empty($filter['owner'])) {
			$ownr   = implode("','", $filter['owner']);
			$condn .= " AND j.belong_to IN ('".$ownr."')";
		}
		if (!empty($filter['leadassignee'])) {
			/* $assinee = implode("','", $filter['leadassignee']);
			$condn  .= " AND j.lead_assign IN ('".$assinee."')"; */
			
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$condn 	.= 'AND CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$condn 	.= "AND FIND_IN_SET('".$filter['leadassignee'][0]."', j.lead_assign)";
			}
			// $this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$reg	 = implode("','", $filter['regionname']);
			$condn  .= " AND cc.add1_region IN ('".$reg."')";
		}
		if (!empty($filter['countryname'])) {
			$county	 = implode("','", $filter['countryname']);
			$condn  .= " AND cc.add1_country IN ('".$county."')";
		}
		if (!empty($filter['statename'])) {
			$ste	 = implode("','", $filter['statename']);
			$condn  .= " AND cc.add1_state IN ('".$ste."')";
		}
		if (!empty($filter['locname'])) {
			$locname = implode("','", $filter['locname']);
			$condn  .= " AND cc.add1_location IN ('".$locname."')";
		}
		if (!empty($filter['ser_requ'])) {
			$ser = implode("','", $filter['ser_requ']);
			$condn  .= " AND j.lead_service IN ('".$ser."')";
		}
		if (!empty($filter['lead_src'])) {
			$sorc = implode("','", $filter['lead_src']);
			$condn  .= " AND j.lead_source IN ('".$sorc."')";
		}
		if (!empty($filter['industry'])) {
			$inds = implode("','", $filter['industry']);
			$condn  .= " AND j.industry IN ('".$inds."')";
		}
		if (!empty($filter['lead_indi'])) {
			$indic = implode("','", $filter['lead_indi']);
			$condn  .= " AND j.lead_indicator IN ('".$indic."')";
		}
		$reseller_condn = '';
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = 'AND (j.belong_to = '.$this->userdata['userid'].' OR j.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', j.lead_assign)) ';
		}
		$indicator_query = $this->db->query("SELECT COUNT(
			CASE WHEN j.lead_indicator = 'HOT'
			THEN j.lead_indicator
			END ) AS 'HOT', COUNT(
			CASE WHEN j.lead_indicator = 'WARM'
			THEN j.lead_indicator
			END ) AS 'WARM', COUNT(
			CASE WHEN j.lead_indicator = 'COLD'
			THEN j.lead_indicator
			END ) AS 'COLD'
			FROM ".$this->cfg['dbpref']."leads j
			JOIN ".$this->cfg['dbpref']."customers c ON c.custid = j.custid_fk
			JOIN ".$this->cfg['dbpref']."customers_company cc ON cc.companyid = c.company_id
			WHERE j.lead_stage IN ('".$this->stages."')
			AND j.lead_status =1".$where_level." ".$condn." ".$reseller_condn);
			// echo $this->db->last_query(); exit;
			return $indicator_query->row_array();
	}
	
	public function getIndiLeads($cusId = FALSE, $indi, $filters = FALSE) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title,ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.expect_worth_amount, jb.lead_indicator, jb.lead_assign, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		//for advance filters
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->where('jb.lead_indicator', $indi);
		$this->db->order_by('jb.lead_id', 'desc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		$ind_query = $query->result_array();
		return $ind_query;
	}
	
	public function getLeastLeadsCount($cusId = FALSE, $filter = FALSE) {
		/*
		$cnt_query = $this->db->query("SELECT `lead_indicator`,count(`lead_indicator`) FROM `".$this->cfg['dbpref']."leads` WHERE `lead_stage` between 1 and 12 and `lead_status` = 1 and `lead_indicator` !='HOT' GROUP BY `lead_indicator` ORDER BY `lead_indicator`");
		*/
		$this->db->select('j.lead_indicator, count(j.lead_indicator)');
		$this->db->from($this->cfg['dbpref'].'leads as j');
		$this->db->join($this->cfg['dbpref'].'customers as c','c.custid = j.custid_fk','inner');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id','inner');
		$this->db->where_in('j.lead_stage', $this->stg);
		$this->db->where('j.lead_status',1);
		$this->db->where('j.lead_indicator != ', 'HOT');
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			// $reseller_condn = '(j.belong_to = '.$this->userdata['userid'].' OR j.lead_assign = '.$this->userdata['userid'].' OR j.assigned_to ='.$this->userdata['userid'].')';
			$reseller_condn = '(j.belong_to = '.$this->userdata['userid'].' OR j.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', j.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('j.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('j.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('j.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', j.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('j.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('j.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('j.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('j.lead_indicator', $filter['lead_indi']);
		}
		$this->db->GROUP_BY('j.lead_indicator');
		$this->db->order_by('j.lead_indicator', 'asc');
		$this->db->order_by('j.lead_id', 'asc');
		$query	   = $this->db->get();
		// echo $this->db->last_query(); die;
		$cnt_query =  $query->result_array();
		return $cnt_query;
	}
	
	public function getCurrentActivityLeads($isSelect = 7, $cusId = FALSE, $filter = FALSE) {
		$this->db->select('jb.lead_title,jb.invoice_no,ew.expect_worth_id, ew.expect_worth_name, ownr.userid as ownr_userid, jb.lead_id, jb.lead_assign, jb.expect_worth_amount, jb.belong_to, ownr.first_name as ownrfname, ownr.last_name as ownrlname');
		$this->db->select('GROUP_CONCAT(CONCAT(usr.first_name, " " , usr.last_name) SEPARATOR ",") as usr', FALSE);
		$this->db->from($this->cfg['dbpref'].'leads jb');
		// $this->db->join($this->cfg['dbpref'].'users usr', 'usr.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users as usr',' FIND_IN_SET (usr.userid , jb.lead_assign) ');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'customers c','c.custid = jb.custid_fk','inner');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id','inner');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		//advanced filter
		if (!empty($filter['stage'])) {
			$this->db->where_in('jb.lead_stage', $filter['stage']);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('jb.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('jb.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('jb.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('jb.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('jb.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('jb.lead_indicator', $filter['lead_indi']);
		}
		$this->db->where('jb.date_modified BETWEEN DATE_SUB(NOW(), INTERVAL '.$isSelect.' DAY) AND NOW()');
		$query 		= $this->db->get();
		$act_query  =  $query->result_array();
		// echo $this->db->last_query(); exit;		
		return $act_query;
	}
	
	function getCurrentActivityLeadsAjax($isSelect = 7, $cusId = FALSE, $filters = FALSE) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		$this->db->select('jb.lead_title,jb.invoice_no,ew.expect_worth_id, ew.expect_worth_name, ownr.userid as ownr_userid, jb.lead_id, jb.lead_assign, jb.expect_worth_amount, jb.belong_to, ownr.first_name as ownrfname, ownr.last_name as ownrlname');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->join($this->cfg['dbpref'].'customers c','c.custid = jb.custid_fk','inner');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id','inner');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		//for advanced filters
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('jb.custid_fk', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			$this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->where('jb.date_modified BETWEEN DATE_SUB(NOW(), INTERVAL '.$isSelect.' DAY) AND NOW()');
		$query 		= $this->db->get();
		$act_query  =  $query->result_array();       
		return $act_query;
	}
	
	public function getLeadsAging($cusId = FALSE, $filter = FALSE) {
		$todayDate = date('Y-m-d h:m:s');
		// $todayDate = date('Y-m-d');
		$thirtyDays 	= date('Y-m-d h:m:s', strtotime("now -30 days"));
		$sixtyDays 		= date('Y-m-d h:m:s', strtotime("now -60 days"));
		$ninetyDays 	= date('Y-m-d h:m:s', strtotime("now -90 days"));
		$oneTwentyDays 	= date('Y-m-d h:m:s', strtotime("now -120 days"));
		$oneFiftyDays 	= date('Y-m-d h:m:s', strtotime("now -150 days"));
		$oneEightyDays 	= date('Y-m-d h:m:s', strtotime("now -180 days"));
		
		
		$thirtyOneDaysTemp 	= strtotime ( '+1 day' , strtotime ( $thirtyDays ));
		$thirtyOneDays 		= date ( 'Y-m-d h:m:s' , $thirtyOneDaysTemp);
		
		$sixtyOneDaysTemp 	= strtotime ( '+1 day' , strtotime ( $sixtyDays ));
		$sixtyOneDays 		= date ( 'Y-m-d h:m:s' , $sixtyOneDaysTemp);
		
		$ninetyOneDaysTemp 	= strtotime ( '+1 day' , strtotime ( $ninetyDays ));
		$ninetyOneDays 		= date ( 'Y-m-d h:m:s' , $ninetyOneDaysTemp);
		
		$oneTwentyOneDaysTemp 	= strtotime ( '+1 day' , strtotime ( $oneTwentyDays ));
		$oneTwentyOneDays 		= date ( 'Y-m-d h:m:s' , $oneTwentyOneDaysTemp);
		
		$oneFiftyOneDaysTemp 	= strtotime ( '+1 day' , strtotime ( $oneFiftyDays ));
		$oneFiftyOneDays 		= date ( 'Y-m-d h:m:s' , $oneFiftyOneDaysTemp);
		
		$oneEightyOneDaysTemp 	= strtotime ( '+1 day' , strtotime ( $oneEightyDays ));
		$oneEightyOneDays 		= date ( 'Y-m-d h:m:s' , $oneEightyOneDaysTemp);

		$where_level = "";
		if (!empty($cusId)) {
			$cusId = implode("','", $cusId);
			if ($this->userdata['level']!=1) {
				$where_level = " AND cc.companyid IN ('".$cusId."')";
			}
		}
		$condn = "";
		if (!empty($filter['customer'])) {
			$custom = implode("','", $filter['customer']);
			$condn  = " AND cc.companyid IN ('".$custom."')";
		}
		if (!empty($filter['stage'])) {
			$stag   = implode("','", $filter['stage']);
			$condn .= " AND j.lead_stage IN ('".$stag."')";
		}
		if (!empty($filter['owner'])) {
			$ownr   = implode("','", $filter['owner']);
			$condn .= " AND j.belong_to IN ('".$ownr."')";
		}
		if (!empty($filter['leadassignee'])) {
			/* $assinee = implode("','", $filter['leadassignee']);
			$condn  .= " AND j.lead_assign IN ('".$assinee."')"; */
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$condn 	.= 'AND CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$condn 	.= "AND FIND_IN_SET('".$filter['leadassignee'][0]."', j.lead_assign)";
			}
			// $this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$reg	 = implode("','", $filter['regionname']);
			$condn  .= " AND cc.add1_region IN ('".$reg."')";
		}
		if (!empty($filter['countryname'])) {
			$county	 = implode("','", $filter['countryname']);
			$condn  .= " AND cc.add1_country IN ('".$county."')";
		}
		if (!empty($filter['statename'])) {
			$ste	 = implode("','", $filter['statename']);
			$condn  .= " AND cc.add1_state IN ('".$ste."')";
		}
		if (!empty($filter['locname'])) {
			$locname = implode("','", $filter['locname']);
			$condn  .= " AND cc.add1_location IN ('".$locname."')";
		}
		if (!empty($filter['ser_requ'])) {
			$ser = implode("','", $filter['ser_requ']);
			$condn  .= " AND j.lead_service IN ('".$ser."')";
		}
		if (!empty($filter['lead_src'])) {
			$sorc = implode("','", $filter['lead_src']);
			$condn  .= " AND j.lead_source IN ('".$sorc."')";
		}
		if (!empty($filter['industry'])) {
			$inds = implode("','", $filter['industry']);
			$condn  .= " AND j.industry IN ('".$inds."')";
		}
		if (!empty($filter['lead_indi'])) {
			$indic = implode("','", $filter['lead_indi']);
			$condn  .= " AND j.lead_indicator IN ('".$indic."')";
		}
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = 'AND (j.belong_to = '.$this->userdata['userid'].' OR j.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', j.lead_assign)) ';
		}
		$stg = $this->stages;
		$age_query = $this->db->query("SELECT 
		COUNT(CASE WHEN DATE(j.date_created) BETWEEN '".$thirtyDays."' AND '".$todayDate."' THEN DATE(j.date_created) END) as '0-30 Days',
		COUNT(CASE WHEN DATE(j.date_created) BETWEEN '".$sixtyDays."' AND '".$thirtyOneDays."' THEN DATE(j.date_created) END) as '31-60 Days',
		COUNT(CASE WHEN DATE(j.date_created) BETWEEN '".$ninetyDays."' AND '".$sixtyOneDays."' THEN DATE(j.date_created) END) as '61-90 Days',
		COUNT(CASE WHEN DATE(j.date_created) BETWEEN '".$oneTwentyDays."' AND '".$ninetyOneDays."' THEN DATE(j.date_created) END) as '91-120 Days',
		COUNT(CASE WHEN DATE(j.date_created) BETWEEN '".$oneFiftyDays."' AND '".$oneTwentyOneDays."' THEN DATE(j.date_created) END) as '121-150 Days',
		COUNT(CASE WHEN DATE(j.date_created) BETWEEN '".$oneEightyDays."' AND '".$oneFiftyOneDays."' THEN DATE(j.date_created) END) as '151-180 Days',
		COUNT(CASE WHEN DATE(j.date_created) < '".$oneEightyOneDays."' THEN DATE(j.date_created) END) as 'Above181 Days'		
		FROM ".$this->cfg['dbpref']."leads j
		JOIN ".$this->cfg['dbpref']."customers c ON c.custid = j.custid_fk
		JOIN ".$this->cfg['dbpref']."customers_company cc ON cc.companyid = c.company_id		
		WHERE j.lead_stage IN ('".$stg."')
		AND j.lead_status = 1".$where_level." ".$condn." ".$reseller_condn);
		// echo $this->db->last_query(); exit;
		return $age_query->row_array();
	}
	
	public function leadAgingLeads($cusId = FALSE, $dt, $filters) {	
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		
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


		switch($dt) 
		{
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
		
		$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title, jb.lead_assign, ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.expect_worth_amount, jb.lead_indicator, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = cs.company_id', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id', 'LEFT');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if ($dt == 6) {
			$this->db->where('date_format( jb.date_created, "%Y-%m-%d" ) < "'.$todt.'" ');
		} else {
			$this->db->where('date_format( jb.date_created, "%Y-%m-%d" ) BETWEEN "'.$todt.'" AND "'.$frmdt.'" ');
		}
		//for advanced filters
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		
		$this->db->order_by('jb.lead_id', 'desc');
		$query = $this->db->get();
		$ag_query = $query->result_array();
		return $ag_query;
	}
	
	/* Get lead_id, lead title, region, lead owner, lead assigned to, customer, */
	public function getLeadOwnerDependencies($userid, $cusId = FALSE, $filters = FALSE) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
	    $lead_dependencies = $this->db->select('jb.lead_id, jb.lead_title, ew.expect_worth_id, cs.customer_name as cfname, cc.company, jb.invoice_no, jb.lead_assign, jb.lead_indicator, jb.expect_worth_amount, jb.belong_to, ownr.first_name as ownrfname, ownr.last_name as ownrlname, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'users as ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		$this->db->where('ownr.userid', $userid);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->order_by('jb.lead_id', 'desc');
		$depend_query = $this->db->get();
		return $depend_query;
	}

	public function getLeadAssigneeDependencies($userid, $cusId = FALSE, $filters = FALSE) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
	    $lead_dependencies = $this->db->select('jb.lead_id, jb.invoice_no, ew.expect_worth_id, jb.lead_title, cs.customer_name as cfname, cc.company, jb.lead_assign, jb.lead_indicator, jb.expect_worth_amount, jb.belong_to, ownr.first_name as ownrfname, ownr.last_name as ownrlname, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'users as usr',' FIND_IN_SET (usr.userid , jb.lead_assign) ');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		$this->db->where('usr.userid', $userid);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			// $reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.lead_assign = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].')';
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->order_by('jb.lead_id', 'desc');
		$depend_query = $this->db->get();
		return $depend_query;
	}

	public function getLeadsDetails($leadStage, $cusId = FALSE, $filters = FALSE) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title,ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.expect_worth_amount, jb.lead_indicator, jb.lead_assign, ls.lead_stage_name, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_stage ls', 'ls.lead_stage_id = jb.lead_stage');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		// $this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
   		$this->db->like('ls.lead_stage_name', $leadStage, 'after');
		$this->db->where('jb.lead_status', 1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->group_by('jb.lead_id');
		$this->db->order_by('jb.lead_id', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query();
		$ld_query =  $query->result_array();
		return $ld_query;
	}
	
	public function getRegionLeadsDetails($leadsRegion, $cusId = FALSE, $filters = FALSE) {
		//echo $leadsRegion; exit;
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		
		$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title, ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.lead_assign, jb.expect_worth_amount, jb.lead_indicator, reg.region_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'region reg', 'reg.regionid = cc.add1_region');
		$this->db->join($this->cfg['dbpref'].'country cou', 'cou.countryid = cc.add1_country');
		$this->db->join($this->cfg['dbpref'].'state ste', 'ste.stateid = cc.add1_state');
		$this->db->join($this->cfg['dbpref'].'location loc', 'loc.locationid = cc.add1_location');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		// $this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'] . 'users assi',' FIND_IN_SET (assi.userid , jb.lead_assign) ');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}

		if ($this->userdata['level']==1) {
			if ((!empty($fresult['freg_id'])) && (empty($fresult['fcntry_id'])) && (empty($fresult['fstet_id'])) && (empty($fresult['flocn_id'])))
				$this->db->where('cou.country_name', $leadsRegion);
			else if ((!empty($fresult['fcntry_id'])) && (empty($fresult['fstet_id'])) && (empty($fresult['flocn_id'])))
				$this->db->where('ste.state_name', $leadsRegion);
			else if (!empty($fresult['fstet_id']))
				$this->db->where('loc.location_name', $leadsRegion);
			else 
				$this->db->where('reg.region_name', $leadsRegion);
		}
		if (($this->userdata['level'])==2) {
			if ((!empty($fresult['fcntry_id'])) && (empty($fresult['fstet_id'])) && (empty($fresult['flocn_id'])))
				$this->db->where('ste.state_name', $leadsRegion);
			else if (!empty($fresult['fstet_id']))
				$this->db->where('loc.location_name', $leadsRegion);
			else 
				$this->db->where('cou.country_name', $leadsRegion);
		}
		if (($this->userdata['level'])==3) {
			if (!empty($fresult['fstet_id']))
				$this->db->where('loc.location_name', $leadsRegion);
			else
				$this->db->where('ste.state_name', $leadsRegion);
		}
		if (($this->userdata['level']==4) || ($this->userdata['level']==5)) {
			$this->db->where('loc.location_name', $leadsRegion);
		}
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status', 1);
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		//for advanced filter
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->group_by('jb.lead_id');
		$this->db->order_by('jb.lead_id', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$ld_query =  $query->result_array();
		return $ld_query;
	}
	
	public function getCurrentLeadActivity($lead_id) {
	    $lead_dependencies = $this->db->select('jb.lead_id, ew.expect_worth_id, jb.lead_title, cs.customer_name as cfname, cc.company, jb.invoice_no, jb.lead_assign,jb.lead_indicator, jb.expect_worth_amount, jb.belong_to, ownr.first_name as ownrfname, ownr.last_name as ownrlname, ew.expect_worth_name');
		$this->db->select('GROUP_CONCAT(CONCAT(usr.first_name, " " , usr.last_name) SEPARATOR ",") as usrfname', FALSE);
		$this->db->from($this->cfg['dbpref'].'leads jb');
		// $this->db->join($this->cfg['dbpref'].'users usr', 'usr.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users as usr',' FIND_IN_SET (usr.userid , jb.lead_assign) ');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		$this->db->where('jb.lead_id', $lead_id);
		$this->db->order_by('jb.lead_id', 'desc');
		$depend_query = $this->db->get();
		return $depend_query;
	}
	
	public function LeadDetails($jid) {
		$this->db->select('jb.invoice_no, jb.lead_title,ew.expect_worth_id, cs.customer_name, owr.first_name as owrfname, owr.last_name as owrlname, jb.expect_worth_amount, jb.lead_indicator, ls.lead_stage_name, ew.expect_worth_name');
		$this->db->select('GROUP_CONCAT(CONCAT(assi.first_name, " " , assi.last_name) SEPARATOR ",") as assifname', FALSE);
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_stage ls', 'ls.lead_stage_id = jb.lead_stage');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		// $this->db->join($this->cfg['dbpref'].'users assi', 'assi.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users as assi',' FIND_IN_SET (assi.userid , jb.lead_assign) ');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
   		$this->db->where('jb.lead_id', $jid);
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
		$rg_query = $query->result_array();
		return $rg_query;
	}
	
	//For Countries
	public function getCountries($uid, $lvlid) {
		$this->db->select('country_id');
		$this->db->from($this->cfg['dbpref'].'levels_country');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$cs_query = $query->result_array();
		// echo $this->db->last_query(); die;
		return $cs_query;
	}
	
	//For States
	public function getStates($uid, $lvlid) {
		$this->db->select('state_id');
		$this->db->from($this->cfg['dbpref'].'levels_state');
		$this->db->where('user_id', $uid);
   		$this->db->where('level_id', $lvlid);
		$query = $this->db->get();
		$ste_query = $query->result_array();
		return $ste_query;
	}
	
	//For Locations
	public function getLocations($uid, $lvlid) {
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
		$this->db->select('c.custid as companyid');		
		$this->db->join($this->cfg['dbpref']. 'customers as c', 'cc.companyid = c.company_id');
		$this->db->from($this->cfg['dbpref'].'customers_company as cc');
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
	public function getClosedJobids($cusId = FALSE, $filter = FALSE) {
		$pjt_stat = array(0,1,2,3);
		
		// my fiscal year starts on July,1 and ends on June 30, so... $curYear = date("Y");
		//eg. calculateFiscalYearForDate("5/15/08","7/1","6/30"); m/d/y
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");

		$frm_dt = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$to_dt = $curFiscalYear."-03-31"; //eg.2014-03-01
		$this->db->select('j.lead_id , j.expect_worth_id, j.actual_worth_amount as expect_worth_amount');
		$this->db->from($this->cfg['dbpref'].'leads as j');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = j.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->where('lead_status', 4);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(j.belong_to = '.$this->userdata['userid'].' OR j.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', j.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		
		if ($this->userdata['level']!= 1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('j.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('j.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('j.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", j.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', j.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('j.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('j.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('j.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('j.lead_indicator', $filter['lead_indi']);
		}
   		$this->db->where_in('j.pjt_status', $pjt_stat);
   		$this->db->where('j.date_modified BETWEEN "'.$frm_dt.'" AND "'.$to_dt.'" ');
		$this->db->order_by('j.lead_id', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$cls_query =  $query->result_array();
		return $cls_query;
	}
	
	//For Closed Opportunities Leads only - actual_worth_amount
	public function closedLeadDet($jbid, $filters=false) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		if (!empty($jbid)) {
			$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title, jb.pjt_status, jb.lead_assign, ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.actual_worth_amount as expect_worth_amount, jb.lead_indicator, ls.lead_stage_name, ew.expect_worth_name');
			$this->db->from($this->cfg['dbpref'].'leads jb');
			$this->db->join($this->cfg['dbpref'].'lead_stage ls', 'ls.lead_stage_id = jb.lead_stage', "LEFT");
			$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk', "LEFT");
			$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id', "LEFT");
			$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to', "LEFT");
			$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id', "LEFT");
			$this->db->where_in('jb.lead_id', $jbid);
			$this->db->where('jb.lead_status', 4);
			if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
				$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
				$this->db->where($reseller_condn);
			}
			if (!empty($fresult['fstge'])) {
				$this->db->where_in('jb.lead_stage', $fresult['fstge']);
			}
			if (!empty($fresult['fcust_id'])) {
				$this->db->where_in('cc.companyid', $fresult['fcust_id']);
			}
			if (!empty($fresult['fownr_id'])) {
				$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
			}
			if (!empty($fresult['fassg_id'])) {
				// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
				$cnt = count($fresult['fassg_id']);
				if(count($fresult['fassg_id'])>1) {
					$find_wh_id = '(';
					for($i=0; $i<count($fresult['fassg_id']); $i++) {
						$find_wh_id .= $fresult['fassg_id'][$i];
						if($cnt != ($i+1)) {
							$find_wh_id .= "|";
						}
					}
					$find_wh_id .= ')';
					$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
				} else {
					$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
				}
				$this->db->where($find_wh);
			}
			if (!empty($fresult['freg_id'])) {
				$this->db->where_in('cc.add1_region', $fresult['freg_id']);
			}
			if (!empty($fresult['fcntry_id'])) {
				$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
			}
			if (!empty($fresult['fstet_id'])) {
				$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
			}
			if (!empty($fresult['flocn_id'])) {
				$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
			}
			if (!empty($fresult['fser_req_id'])) {
				$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
			}
			if (!empty($fresult['flead_src_id'])) {
				$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
			}
			if (!empty($fresult['findustry'])) {
				$this->db->where_in('jb.industry', $fresult['findustry']);
			}
			if (!empty($fresult['flead_indic_id'])) {
				$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
			}
			$this->db->order_by('jb.lead_id', 'desc');
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			$rest_query =  $query->result_array();
			return $rest_query;
		} else {
			return "no records";
		}
	}
	
	public function getLeadSource($cusId = FALSE, $filter = FALSE) {
		$this->db->select('ldsrc.lead_source_name, count(jb.lead_source) as src');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_source ldsrc', 'ldsrc.lead_source_id = jb.lead_source');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('jb.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('jb.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('jb.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('jb.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('jb.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('jb.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('jb.lead_indicator', $filter['lead_indi']);
		}
		$this->db->GROUP_BY('jb.lead_source');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$ls_query =  $query->result_array();
		return $ls_query;
	}

	public function getIndustryLead($cusId = FALSE, $filter = FALSE) {
		$this->db->select('i.industry, count(jb.industry) as src');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'industry i', 'i.id = jb.industry');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('jb.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('jb.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('jb.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('jb.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('jb.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('jb.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('jb.lead_indicator', $filter['lead_indi']);
		}
		$this->db->GROUP_BY('jb.industry');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$ls_query =  $query->result_array();
		return $ls_query;
	}
	
	public function getServiceReq($cusId = FALSE, $filter = FALSE) {
		$this->db->select('jc.services, count(jb.lead_service) as job_cat');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_services jc', 'jc.sid = jb.lead_service');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
		$this->db->where('jb.lead_status',1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if (!empty($filter['customer'])) {
			$this->db->where_in('cc.companyid', $filter['customer']);
		}
		if (!empty($filter['stage'])) {
			$this->db->where_in('jb.lead_stage', $filter['stage']);
		}
		if (!empty($filter['owner'])) {
			$this->db->where_in('jb.belong_to', $filter['owner']);
		}
		if (!empty($filter['leadassignee'])) {
			// $this->db->where_in('jb.lead_assign', $filter['leadassignee']);
			$cnt = count($filter['leadassignee']);
			if(count($filter['leadassignee'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($filter['leadassignee']); $i++) {
					$find_wh_id .= $filter['leadassignee'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$filter['leadassignee'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($filter['regionname'])) {
			$this->db->where_in('cc.add1_region', $filter['regionname']);
		}
		if (!empty($filter['countryname'])) {
			$this->db->where_in('cc.add1_country', $filter['countryname']);
		}
		if (!empty($filter['statename'])) {
			$this->db->where_in('cc.add1_state', $filter['statename']);
		}
		if (!empty($filter['locname'])) {
			$this->db->where_in('cc.add1_location', $filter['locname']);
		}
		if (!empty($filter['ser_requ'])) {
			$this->db->where_in('jb.lead_service', $filter['ser_requ']);
		}
		if (!empty($filter['lead_src'])) {
			$this->db->where_in('jb.lead_source', $filter['lead_src']);
		}
		if (!empty($filter['industry'])) {
			$this->db->where_in('jb.industry', $filter['industry']);
		}
		if (!empty($filter['lead_indi'])) {
			$this->db->where_in('jb.lead_indicator', $filter['lead_indi']);
		}
		$this->db->GROUP_BY('jb.lead_service');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$sq_query =  $query->result_array();
		return $sq_query;
	}
	
	public function getLeadsDetails_pie2($leadStage, $cusId = FALSE, $filters = FALSE) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title, jb.lead_assign, ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.expect_worth_amount, jb.lead_indicator, ldsrc.lead_source_name, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_source ldsrc', 'ldsrc.lead_source_id = jb.lead_source');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
   		$this->db->like('ldsrc.lead_source_name', $leadStage);
		$this->db->where('jb.lead_status', 1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->order_by('jb.lead_id', 'desc');
		$query = $this->db->get();
		$ldsr_query =  $query->result_array();
		return $ldsr_query;
	}
	
	public function getLeadsDetails_pie4($leadStage, $cusId = FALSE, $filters = FALSE) {
		// echo $leadStage; exit;
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title,  jb.lead_assign, ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.expect_worth_amount, jb.lead_indicator, ldsrc.industry as lead_source_name, ew.expect_worth_name', false);
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'industry ldsrc', 'ldsrc.id = jb.industry');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'users as assi',' FIND_IN_SET (assi.userid , jb.lead_assign)');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
   		$this->db->like('ldsrc.industry', $leadStage);
		$this->db->where('jb.lead_status', 1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid', $cusId);
		}
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->group_by('jb.lead_id');
		$this->db->order_by('jb.lead_id', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$ldsr_query =  $query->result_array();
		return $ldsr_query;
	}
	
	public function getLeadsDetails_pie3($leadStage, $cusId = FALSE, $filters = FALSE) {
		if (!empty($filters)) {
			$fresult = $this->explod_arr($filters);
		}
		$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title, jb.lead_assign, ew.expect_worth_id, cs.customer_name, cc.company, owr.first_name as owrfname, owr.last_name as owrlname, jb.expect_worth_amount, jb.lead_indicator, jbc.services, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'lead_services jbc', 'jbc.sid = jb.lead_service');
		$this->db->join($this->cfg['dbpref'].'customers cs', 'cs.custid = jb.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cs.company_id');
		$this->db->join($this->cfg['dbpref'].'users owr', 'owr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');
		$this->db->where_in('jb.lead_stage', $this->stg);
   		$this->db->like('jbc.services', $leadStage);
		$this->db->where('jb.lead_status', 1);
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}
		if ($this->userdata['level']!=1) {
			$this->db->where_in('cc.companyid',$cusId);
		}
		if (!empty($fresult['fstge'])) {
			$this->db->where_in('jb.lead_stage', $fresult['fstge']);
		}
		if (!empty($fresult['fcust_id'])) {
			$this->db->where_in('cc.companyid', $fresult['fcust_id']);
		}
		if (!empty($fresult['fownr_id'])) {
			$this->db->where_in('jb.belong_to', $fresult['fownr_id']);
		}
		if (!empty($fresult['fassg_id'])) {
			// $this->db->where_in('jb.lead_assign', $fresult['fassg_id']);
			$cnt = count($fresult['fassg_id']);
			if(count($fresult['fassg_id'])>1) {
				$find_wh_id = '(';
				for($i=0; $i<count($fresult['fassg_id']); $i++) {
					$find_wh_id .= $fresult['fassg_id'][$i];
					if($cnt != ($i+1)) {
						$find_wh_id .= "|";
					}
				}
				$find_wh_id .= ')';
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$fresult['fassg_id'][0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}
		if (!empty($fresult['freg_id'])) {
			$this->db->where_in('cc.add1_region', $fresult['freg_id']);
		}
		if (!empty($fresult['fcntry_id'])) {
			$this->db->where_in('cc.add1_country', $fresult['fcntry_id']);
		}
		if (!empty($fresult['fstet_id'])) {
			$this->db->where_in('cc.add1_state', $fresult['fstet_id']);
		}
		if (!empty($fresult['flocn_id'])) {
			$this->db->where_in('cc.add1_location', $fresult['flocn_id']);
		}
		if (!empty($fresult['fser_req_id'])) {
			$this->db->where_in('jb.lead_service', $fresult['fser_req_id']);
		}
		if (!empty($fresult['flead_src_id'])) {
			$this->db->where_in('jb.lead_source', $fresult['flead_src_id']);
		}
		if (!empty($fresult['findustry'])) {
			$this->db->where_in('jb.industry', $fresult['findustry']);
		}
		if (!empty($fresult['flead_indic_id'])) {
			$this->db->where_in('jb.lead_indicator', $fresult['flead_indic_id']);
		}
		$this->db->order_by('jb.lead_id', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$serreq_query =  $query->result_array();
		return $serreq_query;
	}
	
	function explod_arr($filters) {
		// echo "<pre>"; print_r($filters); exit;
		$fres = array();

		if (isset($filters['stge']) && $filters['stge']!='') {
			$fres['fstge'] 		= explode(',',$filters['stge']);
		}
		if (isset($filters['cust_id']) && $filters['cust_id']!='') {
			$fres['fcust_id'] 	= explode(',',$filters['cust_id']);
		}
		if (isset($filters['ownr_id']) && $filters['ownr_id']!='') {
			$fres['fownr_id'] 	= explode(',',$filters['ownr_id']);
		}
		if (isset($filters['assg_id']) && $filters['assg_id']!='') {
			$fres['fassg_id'] 	= explode(',',$filters['assg_id']);
		}
		if (isset($filters['reg_id']) && $filters['reg_id']!='') {
			$fres['freg_id']	= explode(',',$filters['reg_id']);
		}
		if (isset($filters['cntry_id']) && $filters['cntry_id']!='') {
			$fres['fcntry_id']	= explode(',',$filters['cntry_id']);
		}
		if (isset($filters['stet_id']) && $filters['stet_id']!='') {
			$fres['fstet_id'] 	= explode(',',$filters['stet_id']);
		}
		if (isset($filters['locn_id']) && $filters['locn_id']!='') {
			$fres['flocn_id'] 	= explode(',',$filters['locn_id']);
		}
		if (isset($filters['servic_req']) && $filters['servic_req']!='') {
			$fres['fser_req_id'] 	= explode(',',$filters['servic_req']);
		}
		if (isset($filters['lead_sour']) && $filters['lead_sour']!='') {
			$fres['flead_src_id'] 	= explode(',',$filters['lead_sour']);
		}
		if (isset($filters['industry']) && $filters['industry']!='') {
			$fres['findustry'] 	= explode(',',$filters['industry']);
		}
		if (isset($filters['lead_indic']) && $filters['lead_indic']!='') {
			$fres['flead_indic_id'] 	= explode(',',$filters['lead_indic']);
		}
		return $fres;
	}
	
	/*
	*Get the Lead Service
	*/
	function get_serv_req() {		
		$query = $this->db->get_where($this->cfg['dbpref'].'lead_services', array('status'=>1));
		return $query->result_array();
	}
	
	public function get_practices()
	{
    	$this->db->select('id, practices');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$query = $this->db->get($this->cfg['dbpref'] . 'practices');
		return $query->result_array();
    }
	
	/*
	*Get the Lead Sources
	*/
	function get_lead_sources() {		
		$query = $this->db->get_where($this->cfg['dbpref'].'lead_source', array('status'=>1));
		return $query->result_array();
	}
	
	/*
	*Get the Lead Status History
	*/ 
	function getLeadClosedDate($id) {
		// my fiscal year starts on July,1 and ends on June 30, so... $curYear = date("Y");
		//eg. calculateFiscalYearForDate("5/15/08","7/1","6/30"); m/d/y
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");

		$frm_dt = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$to_dt = $curFiscalYear."-03-31"; //eg.2014-03-01
		
	    $this->db->select('lead_id, dateofchange');
	    $this->db->from($this->cfg['dbpref'].'lead_status_history');
		$this->db->where("lead_id", $id);
		$this->db->where("changed_status", 4);
		$this->db->where('dateofchange BETWEEN "'.$frm_dt.'" AND "'.$to_dt.'" ');
		$this->db->order_by('dateofchange', 'desc');
		$this->db->limit(1);
	    $sql = $this->db->get();
	    return $sql->row_array();
	}
	
	/*
	*@Get Current Financial year
	*@Method  calculateFiscalYearForDate
	*/
	function calculateFiscalYearForDate($inputDate, $fyStart, $fyEnd) {
		$date = strtotime($inputDate);
		$inputyear = strftime('%Y',$date);
	 
		// $fystartdate = strtotime($fyStart.$inputyear);
		// $fyenddate = strtotime($fyEnd.$inputyear);
		
		$fystartdate = strtotime($fyStart.'/'.$inputyear);
		$fyenddate = strtotime($fyEnd.'/'.$inputyear);
	 
		// if($date < $fyenddate){
		if($date <= $fyenddate){
			$fy = intval($inputyear);
		}else{
			$fy = intval(intval($inputyear) + 1);
		}
	 
		return $fy;
	}
	
	function get_industry() 
	{
    	$this->db->select('id, industry');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$q = $this->db->get($this->cfg['dbpref'] . 'industry');
		return $q->result_array();
    }

}

?>
