<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_moved_project_model extends crm_model {
    
    public function __construct() {
        
        parent::__construct();
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		
    }
    
    public function getMovedproject($options = array()) {
    	
    	if(!empty($options['start_date'])) {
			$start_date = date('Y-m-d',strtotime($options['start_date']));			
			$this->db->where('date(jb.date_start) >=',$start_date);
		}
		if(!empty($options['end_date'])) {
			$end_date = date('Y-m-d',strtotime($options['end_date']));
			$this->db->where('date(jb.date_start) <=',$end_date);
		}
    	
   		if(!empty($options['practices']) && $options['practices'] != 'null') {
			$practices = explode(',', $options['practices']);
			$this->db->where_in('jb.practice',$practices);
		}   	
		
    	if(!empty($options['divisions']) && $options['divisions'] != 'null') {
			$divisions = explode(',',$options['divisions']);
			$this->db->where_in('jb.division', $divisions);
		}
		
    	$this->db->select('jb.*,prac.practices,division.division_name,cust.customer_name,cust.last_name');
		$this->db->from($this->cfg['dbpref'].'leads jb');
		$this->db->join($this->cfg['dbpref'].'customers cust','jb.custid_fk = cust.custid','INNER');
		$this->db->join($this->cfg['dbpref'].'practices prac','jb.practice = prac.id','INNER');
		$this->db->join($this->cfg['dbpref'].'sales_divisions division','jb.division = division.div_id','INNER');
		$this->db->where('jb.pjt_status > 0');   	
		$query = $this->db->get();
		// echo $str = $this->db->last_query();exit;
		$res['res'] =  $query->result();
		$res['num'] =  $query->num_rows();       
		return $res;
    }
    
	public function get_currency_rate() {		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
	}
	
	function get_users_list($table, $select, $order) {
		$this->db->select($select);
		$this->db->order_by($order, "asc"); 
    	$res = $this->db->get($this->cfg['dbpref'].$table);
        return $res->result_array();
    }
}