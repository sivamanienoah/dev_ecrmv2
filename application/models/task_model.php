<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends crm_model 
{
    
    function Task_model() 
	{
		parent::__construct();
    }
	
	public function get_task_created_by()
	{
		$sql = $this->db->query("SELECT `".$this->cfg['dbpref']."tasks`.`created_by`,`".$this->cfg['dbpref']."tasks`.`jobid_fk`,`".$this->cfg['dbpref']."tasks`.`userid_fk` FROM `".$this->cfg['dbpref']."tasks`,`".$this->cfg['dbpref']."users` WHERE `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`");
		//echo $this->db->last_query();
		return $sql->result_array();
	}
	// This Function list out the tasks with the category from task table
	public function taskCategoryQuery($category_id,$category_name,$task_search,$both)
	{
		// if $task_search['endnotify']  value exist it enters the condition
		if($task_search['task_end_notify']!="" )
		{
			$task_notify_status = get_notify_status(2);
			$query_end_notify="AND `".$this->cfg['dbpref']."tasks`.end_date BETWEEN CURDATE() AND DATE(DATE_ADD(CURDATE(), INTERVAL ".$task_notify_status." DAY))
								AND `".$this->cfg['dbpref']."tasks`.actualend_date='0000-00-00 00:00:00'";
		
			$notify_query="";
		}
		else
		{
			$notify_query = $both."`".$this->cfg['dbpref']."tasks`.`created_by` = '".$task_search['taskowner']."'";
			$query_end_notify="";
		}
		// if taskproject key exist in task_search array it enters the condition	
		if (array_key_exists("taskproject",$task_search))
		{
			//if taskproject value exist it enters the condition
			if($task_search['taskproject']!="" )
			{
				$query="AND `".$this->cfg['dbpref']."tasks`.`jobid_fk` = '".$task_search['taskproject']."'";
			}
			
		}
		else
		{
			$query="";
		}
		
		// if taskstartdate & taskenddate key exist in task_search array it enters the condition	
		if (array_key_exists("taskstartdate",$task_search) && array_key_exists("taskenddate",$task_search))
		{
			//if taskstartdate & taskenddate value exist it enters the condition
			if($task_search['taskstartdate']!=""  &&  $task_search['taskenddate']!="")
			{
				$ts_startdate = $this->dateFormat($task_search['taskstartdate']);
				$ts_enddate = $this->dateFormat($task_search['taskenddate']);
				$query_date= "AND (`".$this->cfg['dbpref']."tasks`.`start_date` >= '". $ts_startdate." 00:00:00"."' AND `".$this->cfg['dbpref']."tasks`.`end_date` <='".$ts_enddate." 23:59:59"."')";
			}	
		}
		else
		{
			$query_date="";
		}
		$stgquery = '';
		if (array_key_exists("task_stage", $task_search)) {
			if( $task_search['task_stage']!="" )
			{
				$stgquery =" AND `".$this->cfg['dbpref']."tasks`.`task_stage` = '".$task_search['task_stage']."'";
			}
		}	
		
		$sql = "SELECT *, `".$this->cfg['dbpref']."tasks`.`start_date` AS `start_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`,`".$this->cfg['dbpref']."leads`.`lead_title` ,`".$this->cfg['dbpref']."tasks`.`created_by` as `taskcreated_by`,`".$this->cfg['dbpref']."leads`.`move_to_project_status` as `lead_or_project`".
			"FROM `".$this->cfg['dbpref']."tasks`
			LEFT JOIN  `".$this->cfg['dbpref']."users`ON`".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
			LEFT JOIN  `".$this->cfg['dbpref']."leads`ON`".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`
			WHERE `".$this->cfg['dbpref']."tasks`.`task_category` = '".$category_id."'
			AND `".$this->cfg['dbpref']."tasks`.`is_complete` = '".$task_search['taskcomplete']."'".$query.$query_date.$query_end_notify.$stgquery."
			AND (`".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$task_search['taskallocateduser']."'
			 ".$notify_query.")
			ORDER BY `".$this->cfg['dbpref']."tasks`.`is_complete` asc, `".$this->cfg['dbpref']."tasks`.`status`, `".$this->cfg['dbpref']."tasks`.`start_date`";
 		$q = $this->db->query($sql);
		$data['records'] = $q->result_array();
		$data['values'] = $category_name;
		$data['categoryid'] = $category_id;
		$data['rows'] = $q->num_rows();
		// echo $this->db->last_query(); exit;
		return $data;	
	}
	
	public function get_task_daily($uid, $today, $task_end_notify) 
	{ 
		if(isset($task_end_notify) && ($task_end_notify == 'task_end_notify')) {
			$task_notify_status = get_notify_status(2);
			
			// $CI->db->select('t.taskid, t.end_date, t.task');
			// $CI->db->where('t.end_date BETWEEN CURDATE() AND DATE(DATE_ADD(CURDATE(), INTERVAL "'.$task_notify_status.'" DAY)) ');
			// $CI->db->where('t.actualend_date', '0000-00-00 00:00:00');
			// $CI->db->where('t.userid_fk', $userdata['userid']);
			// $sql1 = $CI->db->get($cfg['dbpref'].'tasks as t');
			
			$sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, ".$this->cfg['dbpref']."leads.lead_title, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, CONCAT(us.`first_name`, ' ',us.`last_name`) AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
			DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
			DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
			IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
			`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
			`".$this->cfg['dbpref']."customers_company`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`, ".$this->cfg['dbpref']."leads.move_to_project_status
			FROM `".$this->cfg['dbpref']."tasks`
			JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
			JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 0 
			LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
			LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
			LEFT JOIN `".$this->cfg['dbpref']."customers_company` ON `".$this->cfg['dbpref']."customers_company`.`companyid` = `".$this->cfg['dbpref']."customers`.`company_id`
			WHERE 
			`".$this->cfg['dbpref']."tasks`.end_date BETWEEN CURDATE() AND DATE(DATE_ADD(CURDATE(), INTERVAL ".$task_notify_status." DAY)) AND
			`".$this->cfg['dbpref']."tasks`.`actualend_date` = '0000-00-00 00:00:00' AND
			`".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."' ";			
		} else {
			$sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, ".$this->cfg['dbpref']."leads.lead_title, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, CONCAT(us.`first_name`, ' ',us.`last_name`) AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
			DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
			DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
			IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
			`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
			`".$this->cfg['dbpref']."customers_company`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`, ".$this->cfg['dbpref']."leads.move_to_project_status
			FROM `".$this->cfg['dbpref']."tasks`
			JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
			JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 0 
			LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
			LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
			LEFT JOIN `".$this->cfg['dbpref']."customers_company` ON `".$this->cfg['dbpref']."customers_company`.`companyid` = `".$this->cfg['dbpref']."customers`.`company_id`
			WHERE `".$this->cfg['dbpref']."tasks`.`created_by` = '".$uid."' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."' ";
		}
		$query = $this->db->query($sql);
		// echo $this->db->last_query(); die;
		
		return $query->result_array();
	}
	
	public function get_created_by_for_task($id) {
		$query = $this->db->query("SELECT `created_by` FROM `".$this->cfg['dbpref']."tasks` WHERE `taskid`=".$id);
		return $query->result_array();
	}
	
	public function get_task_search_wip($today, $uid, $varStart_date) {
		$sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, ".$this->cfg['dbpref']."leads.lead_title, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, CONCAT(us.`first_name`, ' ',us.`last_name`) AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
		DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
		DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
		IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
		`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
		`".$this->cfg['dbpref']."customers_company`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`, ".$this->cfg['dbpref']."leads.move_to_project_status
		FROM `".$this->cfg['dbpref']."tasks`
		JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
		JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 0 
		LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
		LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
		LEFT JOIN `".$this->cfg['dbpref']."customers_company` ON `".$this->cfg['dbpref']."customers_company`.`companyid` = `".$this->cfg['dbpref']."customers`.`company_id`
		WHERE (`".$this->cfg['dbpref']."tasks`.`created_by` = '".$uid."' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."') ".$varStart_date." ";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	
	}
	
	public function get_task_search_comp($today, $uid, $varStart_date) {
		$sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, ".$this->cfg['dbpref']."leads.lead_title, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, CONCAT(us.`first_name`, ' ',us.`last_name`) AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
		DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
		DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
		IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
		`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
		`".$this->cfg['dbpref']."customers_company`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`, ".$this->cfg['dbpref']."leads.move_to_project_status
		FROM `".$this->cfg['dbpref']."tasks`
		JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
		JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 1
		LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
		LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
		LEFT JOIN `".$this->cfg['dbpref']."customers_company` ON `".$this->cfg['dbpref']."customers_company`.`companyid` = `".$this->cfg['dbpref']."customers`.`company_id`
		WHERE (`".$this->cfg['dbpref']."tasks`.`created_by` = '".$uid."' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '".$uid."') ".$varStart_date." ";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	
	}
	
	public function get_task_search_all($today, $uid, $varStart_date) {
		 $sql = "SELECT ".$this->cfg['dbpref']."leads.lead_id as lead_id, ".$this->cfg['dbpref']."leads.lead_title, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, CONCAT(us.`first_name`, ' ',us.`last_name`) AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
		DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
		DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('".$today."') ) AS `delayed`,
		IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('".$today."'), '1', '0') AS `due_today`,
		`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
		`".$this->cfg['dbpref']."customers_company`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `lead_id`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`, ".$this->cfg['dbpref']."leads.move_to_project_status
		FROM `".$this->cfg['dbpref']."tasks`
		JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
		JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid`
		LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`lead_id`					
		LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
		LEFT JOIN `".$this->cfg['dbpref']."customers_company` ON `".$this->cfg['dbpref']."customers_company`.`companyid` = `".$this->cfg['dbpref']."customers`.`company_id`
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
	
	public function getAccessByUserId()
	{
		
	}

	function dateFormat($value)
	{
		$date=date_create($value);
		return date_format($date,"Y-m-d");
	}	
    
}
?>