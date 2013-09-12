<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_alert_model extends Common_model {
	
	function Taskdue_model() {

        parent::__construct();
        
    }
	
    function getConfig()
    {
    	$query = $this->db->get($this->cfg['dbpref'].'task_alert');
    	$res = $query->row();

    	return $res;	
    }
    
    function addConfig($options = array()){
    	$query = $this->db->insert($this->cfg['dbpref'].'task_alert',$options);
    	return $query;
    }
    
    function updateConfig($options = array())
    {
    	$this->db->where('id',$options['id']);
    	$this->db->set('task_alert_days',$options['task_alert_days']);
    	$query = $this->db->update($this->cfg['dbpref'].'task_alert');
    	return $query;
    }
    
    function getDailyTask()
    {
    	$this->db->select('t.*,u.first_name as owner_first_name,u.last_name as owner_last_name,au.first_name as assigned_first_name,au.last_name as assigned_last_name');
    	$this->db->join($this->cfg['dbpref'].'users u','u.userid = t.created_by','INNER');
    	$this->db->join($this->cfg['dbpref'].'users au','au.userid = t.userid_fk','INNER');
    	$this->db->where('t.actualend_date',0);
    	$this->db->where('t.end_date','date(now())',false);
    	$query = $this->db->get($this->cfg['dbpref'].'tasks t');
    	$res['result'] = $query->result();   
		$res['num'] = $query->num_rows();
    	return $res;
    }
}