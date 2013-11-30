<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Leadsdue_model extends crm_model {
	
	function Leadsdue_model() {

        parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
        
    }

    function getLeads()
    {
    	// $lead_stage = array(1,2,3,4,5,6,7,8,9,10,11,12);
    	
    	$this->db->select('jb.*,cust.first_name as cust_first_name,cust.last_name as cust_last_name,cust.company,cust.add1_region,reg.region_name,u.first_name as owner_first_name,u.last_name as owner_last_name,u.email as owner_mail,au.first_name as assigned_first_name,au.last_name as assigned_last_name,au.email as assigned_mail,mu.first_name as modified_first_name,mu.last_name as modified_last_name,ls.lead_stage_name,ew.expect_worth_name');
    	$this->db->join($this->cfg['dbpref'].'customers cust','jb.custid_fk = cust.custid','INNER');
    	$this->db->join($this->cfg['dbpref'].'region reg','cust.add1_region = reg.regionid','INNER');
    	$this->db->join($this->cfg['dbpref'].'users u','u.userid = jb.created_by','INNER');
    	$this->db->join($this->cfg['dbpref'].'users au','au.userid = jb.lead_assign','INNER');    	
    	$this->db->join($this->cfg['dbpref'].'lead_stage ls','lead_stage_id = jb.lead_stage','INNER');    	
    	$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id = jb.expect_worth_id','INNER');
    	$this->db->join($this->cfg['dbpref'].'users mu','mu.userid = jb.modified_by','LEFT');
    	$this->db->where_in('jb.lead_stage', $this->stg);
    	$this->db->where('lead_status',1);
    	$this->db->where('jb.proposal_expected_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)');
    	$query = $this->db->get($this->cfg['dbpref'].'leads jb');

    	//echo $this->db->last_query();

    	$res['result'] = $query->result();    	
		$res['num'] = $query->num_rows();
    	return $res;
    }
    
    public function getManagementMail(){
    	$this->db->select('email');
    	$this->db->where('role_id','2');
    	$query = $this->db->get($this->cfg['dbpref'].'users');
    	$res = $query->result(); 
    	return $res;
    }
}