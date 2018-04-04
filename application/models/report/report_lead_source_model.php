<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_lead_source_model extends crm_model {
    
    function Report_lead_source_model() {
        parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
    }
    
    public function getLeadReportBySourc($options = array())
    {
    	if(!empty($options['cust_id'])){    		
    		$this->db->where_in('cc.companyid',$options['cust_id']);
    	}
    	
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
		
		if(!empty($options['customer']) && $options['customer'] != 'null')
		{
			$customer = explode(',', $options['customer']);
			$this->db->where_in('cc.companyid',$customer);
		}
    	if(!empty($options['leadassignee']) && $options['leadassignee'] != 'null')
		{
			$leadassignee = @explode(',', $options['leadassignee']);
			// $this->db->where_in('jb.lead_assign',$leadassignee);
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
				$find_wh 	= 'CONCAT(",", jb.lead_assign, ",") REGEXP "'.$find_wh_id.'" ';
			} else {
				$find_wh 	= "FIND_IN_SET('".$leadassignee[0]."', jb.lead_assign)";
			}
			$this->db->where($find_wh);
		}		
    	if(!empty($options['owner']) && $options['owner'] != 'null')
		{
			$owner = explode(',', $options['owner']);
			$this->db->where_in('jb.created_by',$owner);
		}
   		if(!empty($options['stage']) && $options['stage'] != 'null')
		{
			$stage = explode(',', $options['stage']);
			$this->db->where_in('jb.lead_stage',$stage);
		}
		if(!empty($options['regionname']) && $options['regionname'] != 'null'){
			$regionname = explode(',',$options['regionname']);
			$this->db->where_in('cc.add1_region', $regionname);
		}
    	if(!empty($options['countryname']) && $options['countryname'] != 'null'){
			$countryname = explode(',',$options['countryname']);
			$this->db->where_in('cc.add1_country', $countryname);
		}
    	if(!empty($options['statename']) && $options['statename'] != 'null'){
			$statename = explode(',',$options['statename']);
			$this->db->where_in('cc.add1_state', $statename);
		}
    	if(!empty($options['locname']) && $options['locname'] != 'null'){
			$locname = explode(',',$options['locname']);
			$this->db->where_in('cc.add1_location', $locname);
		}
		if(!empty($options['lead_src']) && $options['lead_src'] != 'null'){
			$lead_src = explode(',',$options['lead_src']);
			$this->db->where_in('jb.lead_source', $lead_src);
		}
    	if(!empty($options['worth']) && $options['worth'] != 'null')
		{
			$worth = explode(',', $options['worth']);
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
    	
    	$this->db->select('jb.lead_id, jb.invoice_no, jb.lead_title, jb.expect_worth_amount, jb.expect_worth_id, jb.actual_worth_amount, jb.lead_indicator, jb.lead_status, jb.lead_source, src.lead_source_name, cust.customer_name as cust_first_name, cc.company, cc.add1_region, reg.region_name, u.userid as owner_id, u.first_name as owner_first_name, u.last_name as owner_last_name, u.email as owner_mail, au.first_name as assigned_first_name, au.last_name as assigned_last_name, au.email as assigned_mail, mu.first_name as modified_first_name, mu.last_name as modified_last_name, ls.lead_stage_name, ew.expect_worth_name');
    	$this->db->join($this->cfg['dbpref'].'customers cust','jb.custid_fk = cust.custid', 'INNER');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cust.company_id','INNER');
    	$this->db->join($this->cfg['dbpref'].'region reg','cc.add1_region = reg.regionid', 'INNER');
    	$this->db->join($this->cfg['dbpref'].'users u','u.userid = jb.created_by', 'INNER');
    	$this->db->join($this->cfg['dbpref'].'users au','au.userid = jb.lead_assign', 'INNER');    	
    	$this->db->join($this->cfg['dbpref'].'lead_stage ls','lead_stage_id = jb.lead_stage', 'INNER');    	
    	$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id = jb.expect_worth_id', 'INNER');
    	$this->db->join($this->cfg['dbpref'].'lead_source src','src.lead_source_id = jb.lead_source', 'INNER');
    	$this->db->join($this->cfg['dbpref'].'users mu','mu.userid = jb.modified_by', 'LEFT');
    	$this->db->where_in('jb.lead_stage', $this->stg);
    	$this->db->order_by('jb.lead_source', 'ASC');
    	$this->db->where('lead_status', 1);
		
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.lead_assign = '.$this->userdata['userid'].' OR jb.assigned_to = '.$this->userdata['userid'].')';
			$this->db->where($reseller_condn);
		}
		
    	$query = $this->db->get($this->cfg['dbpref'].'leads jb');
    	// echo $this->db->last_query(); exit;
    	$result['res'] = $query->result();
    	$result['num'] = $query->num_rows();    	
    	return $result;    	
    }
	
	public function get_currency_rate()
	{		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
	}
	
	public function get_lead_sources()
	{		
		$query = $this->db->get_where($this->cfg['dbpref'].'lead_source', array('status'=>1));
		return $query->result_array();
	}
}

?>
