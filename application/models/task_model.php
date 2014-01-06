<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends crm_model 
{
    
    function Task_model() {
		parent::__construct();
    }
	
	public function get_task_created_by() {
		$sql = $this->db->query("SELECT `".$this->cfg['dbpref']."tasks`.`created_by`,`".$this->cfg['dbpref']."tasks`.`jobid_fk`,`".$this->cfg['dbpref']."tasks`.`userid_fk` FROM `".$this->cfg['dbpref']."tasks`,`".$this->cfg['dbpref']."users` WHERE `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`");
		
		return $sql->result_array();
	}
	
	public function get_task_daily($uid, $today) {
		$sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
		DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
		DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
		IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
		`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
		`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
		FROM `".$this->cfg['dbpref']."tasks`
		JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
		JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 0 
		LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
		LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
		WHERE `".$this->cfg['dbpref']."tasks`.`created_by` = '".$uid."' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."' ";
		
		$query = $this->db->query($sql);
		
		return $query->result_array();
	}
	
	public function get_created_by_for_task($id) {
		$query = $this->db->query("SELECT `created_by` FROM `".$this->cfg['dbpref']."tasks` WHERE `taskid`=".$id);
		return $query->result_array();
	}
	
	public function get_task_search_wip($today, $uid, $varStart_date) {
		$sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
		DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
		DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
		IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
		`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
		`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
		FROM `".$this->cfg['dbpref']."tasks`
		JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
		JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 0 
		LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
		LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
		WHERE (`".$this->cfg['dbpref']."tasks`.`created_by` = '".$uid."' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."') ".$varStart_date." ";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	
	}
	
	public function get_task_search_comp($today, $uid, $varStart_date) {
		$sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
		DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
		DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
		IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
		`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
		`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
		FROM `".$this->cfg['dbpref']."tasks`
		JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
		JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 1
		LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
		LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
		WHERE (`".$this->cfg['dbpref']."tasks`.`created_by` = '".$uid."' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."') ".$varStart_date." ";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	
	}
	
	public function get_task_search_all($today, $uid, $varStart_date) {
		 $sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
		DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
		DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
		IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
		`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
		`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
		FROM `".$this->cfg['dbpref']."tasks`
		JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
		JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid`
		LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
		LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
		WHERE (`".$this->cfg['dbpref']."tasks`.`created_by` = '".$uid."' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."') ".$varStart_date." ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function getActiveUsers() {
		$this->db->select("userid, first_name, last_name, email");
		$this->db->from($this->cfg['dbpref']."users");
		$this->db->where("inactive", 0);
		$this->db->order_by("first_name");	
		$query = $this->db->get();
		$res['num']  = $query->num_rows();
		$res['user'] = $query->result_array();
		return $res;
	}
    
}
?>