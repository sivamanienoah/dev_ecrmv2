<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_lead_region_model extends crm_model {
    
    function Report_lead_region_model() {
        parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
    }
    
    public function getLeadReportByRegion($options = array()) {
    	$order_by = 'reg.region_name';
    	
    	if(!empty($options['cust_id'])){    		
    		$this->db->where_in('cc.companyid',$options['cust_id']);
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
			$owner = @explode(',', $options['owner']);
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
			$order_by = 'reg.region_name';
		}
		
    	if(!empty($options['countryname']) && $options['countryname'] != 'null'){
			$countryname = explode(',',$options['countryname']);
			$this->db->where_in('cc.add1_country', $countryname);
			$order_by = 'country.country_name';
		}
		
    	if(!empty($options['statename']) && $options['statename'] != 'null'){
			$statename = @explode(',',$options['statename']);
			$this->db->where_in('cc.add1_state', $statename);
			$order_by = 'state.state_name';
		}
		
    	if(!empty($options['locname']) && $options['locname'] != 'null'){
			$locname = @explode(',',$options['locname']);
			$this->db->where_in('cc.add1_location', $locname);
			$order_by = 'location.location_name';
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
    	
    	
    	$this->db->select('jb.*,cust.customer_name as cust_first_name,cc.company,cc.add1_region,reg.region_name,u.first_name as owner_first_name,u.last_name as owner_last_name,u.email as owner_mail,mu.first_name as modified_first_name,mu.last_name as modified_last_name,ls.lead_stage_name,ew.expect_worth_name,country.country_name,state.state_name,location.location_name');
    	$this->db->join($this->cfg['dbpref'].'customers cust','jb.custid_fk = cust.custid','INNER');
		$this->db->join($this->cfg['dbpref'].'customers_company cc', 'cc.companyid = cust.company_id','INNER');
    	$this->db->join($this->cfg['dbpref'].'region reg','cc.add1_region = reg.regionid','INNER');
    	$this->db->join($this->cfg['dbpref'].'country country','cc.add1_country = country.countryid','INNER');
    	$this->db->join($this->cfg['dbpref'].'state state','cc.add1_state = state.stateid','INNER');
    	$this->db->join($this->cfg['dbpref'].'location location','cc.add1_location = location.locationid','INNER');
    	$this->db->join($this->cfg['dbpref'].'users u','u.userid = jb.created_by','INNER');
    	$this->db->join($this->cfg['dbpref'].'lead_stage ls','lead_stage_id = jb.lead_stage','INNER');    	
    	$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id = jb.expect_worth_id','INNER');
    	$this->db->join($this->cfg['dbpref'].'users mu','mu.userid = jb.modified_by','LEFT');
    	$this->db->where_in('jb.lead_stage', $this->stg);
    	$this->db->group_by('jb.lead_id');
    	$this->db->order_by($order_by,'ASC');
    	$this->db->where('lead_status',1);
		
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$reseller_condn = '(jb.belong_to = '.$this->userdata['userid'].' OR jb.assigned_to ='.$this->userdata['userid'].' OR FIND_IN_SET('.$this->userdata['userid'].', jb.lead_assign)) ';
			$this->db->where($reseller_condn);
		}

    	$query = $this->db->get($this->cfg['dbpref'].'leads jb');
    	$result['res'] = $query->result();
    	$result['num'] = $query->num_rows();
		// echo $this->db->last_query();
    	return $result;    	
    }
    
	
	public function getCustomerByLocation()
	{
		$user_data = $this->session->userdata('logged_in_user');		
		$str = array();
		
		$this->db->select('c.companyid');
		
		if($user_data['level']==5){			
			$this->db->where('loc.user_id', $user_data['userid']);	
			$this->db->join($this->cfg['dbpref'].'levels_location loc','c.add1_location = loc.location_id','INNER');	
		}
		
		if($user_data['level']==4){
			$this->db->where('st.user_id',$user_data['userid']);
			$this->db->join($this->cfg['dbpref'].'levels_state st','c.add1_state = st.state_id','INNER');	
		}
		
		if($user_data['level']==3){
			$this->db->where('ct.user_id',$user_data['userid']);
			$this->db->join($this->cfg['dbpref'].'levels_country ct','c.add1_country = ct.country_id','INNER');	
		}
		
		if($user_data['level']==2){
			$this->db->where('reg.user_id',$user_data['userid']);
			$this->db->join($this->cfg['dbpref'].'levels_region reg','c.add1_region = reg.region_id','INNER');	
		}		
			
		$query = $this->db->get($this->cfg['dbpref'].'customers_company c');
		$res = $query->result();
		
		if($query->num_rows()>0){			
			foreach ($res as $rows){
				$str[]=$rows->companyid;				
			}			
		}else{
			$str[]=0;
		}		
		return $str;			
	}
	
	public function get_currency_rate()
	{		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
	}
	
	public function get_currency_rates_new() {
		$currency_rates = $this->get_currency_rate();
    	$rates 			= array();
    	if(!empty($currency_rates)) {
    		foreach ($currency_rates as $currency) {
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}	
}

?>
