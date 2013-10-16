<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_lead_assignee_model extends crm_model {
    
    public function __construct() {
        parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();		
    }
    
    public function getLeadReportByAssignee($options = array())
    {
    	// $job_status = array(1,2,3,4,5,6,7,8,9,10,11,12);
    	
    	if(!empty($options['cust_id'])){    		
    		$this->db->where_in('cust.custid',$options['cust_id']);
    	}
    	if(!empty($options['start_date']))
		{
			$start_date = @date('Y-m-d',strtotime($options['start_date']));
			$this->db->where('date(jb.date_created) >=',$start_date);
		}
		if(!empty($options['end_date']))
		{
			$end_date = @date('Y-m-d',strtotime($options['end_date']));
			$this->db->where('date(jb.date_created) <=',$end_date);
		}
		
		if(!empty($options['customer']) && $options['customer'] != 'null')
		{
			$customer = @explode(',', $options['customer']);
			$this->db->where_in('jb.custid_fk',$customer);
		}   	
		
    	if(!empty($options['leadassignee']) && $options['leadassignee'] != 'null')
		{
			$leadassignee = @explode(',', $options['leadassignee']);
			$this->db->where_in('jb.lead_assign',$leadassignee);
		}
		
    	if(!empty($options['owner']) && $options['owner'] != 'null')
		{
			$owner = @explode(',', $options['owner']);			
			$this->db->where_in('jb.created_by',$owner);
		}
		
   		if(!empty($options['stage']) && $options['stage'] != 'null')
		{
			$stage = @explode(',', $options['stage']);
			$this->db->where_in('jb.job_status',$stage);
		}   	
		
		
		if(!empty($options['regionname']) && $options['regionname'] != 'null'){
			$regionname = @explode(',',$options['regionname']);
			$this->db->where_in('cust.add1_region', $regionname);
		}
		
    	if(!empty($options['countryname']) && $options['countryname'] != 'null'){
			$countryname = @explode(',',$options['countryname']);
			$this->db->where_in('cust.add1_country', $countryname);
		}
		
    	if(!empty($options['statename']) && $options['statename'] != 'null'){
			$statename = @explode(',',$options['statename']);
			$this->db->where_in('cust.add1_state', $statename);
		}
		
    	if(!empty($options['locname']) && $options['locname'] != 'null'){
			$locname = @explode(',',$options['locname']);
			$this->db->where_in('cust.add1_location', $locname);
		}
		
    	if(!empty($options['worth']) && $options['worth'] != 'null')
		{
			$worth = @explode(',', $options['worth']);
			$where = '(';
			foreach ($worth as $amt)
			{
				$amt = @explode('-', $amt);
				if($amt[1] == 'above')
				{
					$where.= "jb.expect_worth_amount > $amt[0] OR ";
				}else{
					$where.= "jb.expect_worth_amount BETWEEN $amt[0] AND $amt[1] OR ";
				}
			}
			$where= rtrim($where, "OR ");
			$where.= ')';
			$this->db->where($where);
		}   	
    	
    	
    	$this->db->select('jb.*,cust.first_name as cust_first_name,cust.last_name as cust_last_name,cust.company,cust.add1_region,reg.region_name,u.first_name as owner_first_name,u.last_name as owner_last_name,u.email as owner_mail,au.userid as assigned_id,au.first_name as assigned_first_name,au.last_name as assigned_last_name,au.email as assigned_mail,mu.first_name as modified_first_name,mu.last_name as modified_last_name,ls.lead_stage_name,ew.expect_worth_name');
    	$this->db->join($this->cfg['dbpref'].'customers cust','jb.custid_fk = cust.custid','INNER');
    	$this->db->join($this->cfg['dbpref'].'region reg','cust.add1_region = reg.regionid','INNER');
    	$this->db->join($this->cfg['dbpref'].'users u','u.userid = jb.created_by','INNER');
    	$this->db->join($this->cfg['dbpref'].'users au','au.userid = jb.lead_assign','INNER');    	
    	$this->db->join($this->cfg['dbpref'].'lead_stage ls','lead_stage_id = jb.job_status','INNER');    	
    	$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id = jb.expect_worth_id','INNER');
    	$this->db->join($this->cfg['dbpref'].'users mu','mu.userid = jb.modified_by','LEFT');
    	$this->db->where_in('jb.job_status', $this->stg);
    	$this->db->order_by('au.userid','ASC');
    	$this->db->where('lead_status',1);
		
    	$query = $this->db->get($this->cfg['dbpref'].'jobs jb');
    	$result['res'] = $query->result();
    	$result['num'] = $query->num_rows();    	
    	return $result;    	
    }
	
	public function get_currency_rate()
	{		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
	}
}

?>
