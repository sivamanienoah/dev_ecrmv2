<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Taskdue_model extends crm_model {
	
	function Taskdue_model() {

        parent::__construct();
        
    }

    function getDailyTask($daysConfig)
    {
    	
    	$this->db->select('t.*,u.first_name as owner_first_name,u.last_name as owner_last_name,u.email as owner_mail,au.first_name as assigned_first_name,au.last_name as assigned_last_name,au.email as assigned_mail');
    	$this->db->join($this->cfg['dbpref'].'users u','u.userid = t.created_by','INNER');
    	$this->db->join($this->cfg['dbpref'].'users au','au.userid = t.userid_fk','INNER');
    	$this->db->where('t.actualend_date',0);
    	if($daysConfig <=1){
    		$this->db->where('t.end_date','date(now())',false);
    	}else{
    		$daysConfig--;
    		$this->db->where('t.end_date BETWEEN CURDATE() AND DATE(DATE_ADD(CURDATE(),INTERVAL '.$daysConfig.' DAY))');
    	}

    	$query = $this->db->get($this->cfg['dbpref'].'tasks t');
    	$res['result'] = $query->result();
    	//echo $this->db->last_query();
		$res['num'] = $query->num_rows();
    	return $res;
    }
    
 	function getConfig()
    {
    	$query = $this->db->get($this->cfg['dbpref'].'task_alert');
    	$res = $query->row();

    	return $res;	
    }
}