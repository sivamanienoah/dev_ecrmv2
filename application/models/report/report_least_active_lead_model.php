<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_least_active_lead_model extends Common_model {
    
    function Country_model() {
        parent::__construct();
    }
    
    public function getLeastActiveLead($options = array())
    {
    	
    	$isSelect=7;
    	$job_status = array(1,2,3,4,5,6,7,8,9,10,11,12);
    	
    	if(!empty($options['start_date']))
		{
			$start_date = date('Y-m-d',strtotime($options['start_date']));
			$this->db->where('date(jb.date_created) >=',$start_date);
		}
		if(!empty($options['end_date']))
		{
			$end_date = date('Y-m-d',strtotime($options['end_date']));
			$this->db->where('date(jb.date_created) <=',$end_date);
		}
		
		
    	if(!empty($options['cust_id'])){   		
    		$this->db->where_in('cust.custid',$options['cust_id']);
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
				$amt = explode('-', $amt);
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
		
		
    	$this->db->select('jb.*,ew.expect_worth_id, ew.expect_worth_name, ownr.userid as ownr_userid, usr.first_name as usrfname, usr.last_name as usrlname, ownr.first_name as ownrfname, ownr.last_name as ownrlname,cust.first_name as cust_first_name,cust.last_name as cust_last_name,cust.company,cust.add1_region,reg.region_name,ls.lead_stage_name');
		$this->db->from($this->cfg['dbpref'].'jobs jb');
		$this->db->join($this->cfg['dbpref'].'customers cust','jb.custid_fk = cust.custid','INNER');
    	$this->db->join($this->cfg['dbpref'].'region reg','cust.add1_region = reg.regionid','INNER');
		$this->db->join($this->cfg['dbpref'].'users usr', 'usr.userid = jb.lead_assign');
		$this->db->join($this->cfg['dbpref'].'users ownr', 'ownr.userid = jb.belong_to');
		$this->db->join($this->cfg['dbpref'].'lead_stage ls','lead_stage_id = jb.job_status','INNER');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew', 'ew.expect_worth_id = jb.expect_worth_id');   		
   		$this->db->where_in('jb.job_status',$job_status);
		//$this->db->where('jb.lead_status',1);
		//$this->db->where('jb.date_modified BETWEEN DATE_SUB(NOW(), INTERVAL '.$isSelect.' DAY) AND NOW()');
		$this->db->where('jb.lead_status',1);   	
    	$this->db->where('jb.lead_indicator !=','HOT');
    	$this->db->order_by('jb.lead_indicator','ASC');
		$query = $this->db->get();
		//echo $this->db->last_query(); exit;
		$res['res'] =  $query->result();
		$res['num'] =  $query->num_rows();       
		return $res;
    }
    
	public function get_currency_rate()
	{		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
	}
}