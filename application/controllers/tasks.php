<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends CI_Controller {
    
	public $userdata;
	public $cfg;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->helper('form');
		//$this->login_model->check_login(array(0, 1, 2, 3, 5, 4, 6));
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->cfg = $this->config->item('crm');
    }
	
	public function index($extend = FALSE)
	{	
		//mychanges
		$qqql = $this->db->query("SELECT `crm_tasks`.`created_by` FROM `crm_tasks`,`crm_users` WHERE `crm_tasks`.`userid_fk` = `crm_users`.`userid`");
		//echo $this->db->last_query(); exit; 	
		$data['created_by'] = $qqql->result_array();
		
			
		$res = $this->get_daily_tasks($extend);
		//echo "<pre>"; print_r($res); exit;
		foreach($res[0] as $r) {
			$jjj = $this->db->query("SELECT `created_by` FROM `crm_tasks` WHERE `taskid`=".$r['taskid']);
			$data['userid_fk'] = $jjj->result_array();						
		}
		$data['results'] = $res[0];
		$data['start_date_stamp'] = $res[1];
		$data['page_title'] = 'Task List for '. date('l, jS F y', $res[1]);
		$data['hosting']=$res[2];
		//echo "<pre>"; print_r($data['results']); exit;
		$this->load->view('tasks/full_view', $data);
	}
	
	/* weekly task caller */
    public function weekly()
	{
		$res = $this->get_weekly_tasks();
		$data['start_date_stamp'] = $res[3];
		$data['results'] = $res[2];
		$data['page_title'] = 'Task List for the week <span>' . $res[0] . '</span> TO <span>' . $res[1] . '</span>';
		
		$this->load->view('tasks/weekly_view', $data);
	}
	
	/**
	 * Tasks for the main menu
	 */
	public function all()
	{
		$data = array();
		
		$data['user_accounts'] = array();
		//echo "<pre>"; print_r($data); exit;
		$users = $this->db->get($this->cfg['dbpref'] . 'users');
		if ($users->num_rows() > 0)
		{
			$data['user_accounts'] = $users->result_array();
		}
		
		$cq = $this->db->get('crm_contract_jobs');
		
		$temp_cont = $cq->result_array();
		
		$data['assigned_contractors'] = array();
		
		foreach ($temp_cont as $tc)
		{
			$data['assigned_contractors'][] = $tc['userid_fk'];
		}
		$this->db->select(array('job_title','jobid'));
		$this->db->where_not_in('job_title','');
		$project = $this->db->get($this->cfg['dbpref'] . 'jobs'	);
		$data['project'] = $project->result_array();

		//mychanges
			$qqql = $this->db->query("SELECT `crm_tasks`.`created_by`,`crm_tasks`.`userid_fk` FROM `crm_tasks`,`crm_users` WHERE `crm_tasks`.`userid_fk` = `crm_users`.`userid`");
			//echo $this->db->last_query(); exit; 	
			$data['created_by'] = $qqql->result_array();
		$this->load->view('tasks/main_view', $data);
	}
	
    /**
	 * Get all tasks
	 * For today
	 */
	private function get_daily_tasks($extend = FALSE)
	{
	
	$uidd = $this->session->userdata['logged_in_user']; 
	$uid = $uidd['userid'];
	$urole = $uidd['role_id'];

		$now = time();
		if (isset($_GET['day']))
		{
			$from_date = explode('-', $_GET['day']);
			if ($from_date_stamp = mktime(0, 0, 0, $from_date[1], $from_date[0], $from_date[2]))
			{
				$now = $from_date_stamp;
			}
		}
		
		$today = date('Y-m-d', $now);

		  $sql = "SELECT crm_jobs.jobid as jobid, `crm_tasks`.`taskid` AS `taskid`, `crm_tasks`.`task` AS `task`, `crm_tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`crm_tasks`.`start_date`) AS `start_date`, DATE(`crm_tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`crm_tasks`.`actualend_date`) AS `actualend_date`, `crm_tasks`.`approved` AS `approved`, `crm_tasks`.`require_qc` AS `require_qc`,
						DATE(`crm_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`, `crm_tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`crm_tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`crm_tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`crm_tasks`.`hours` AS `hours`, `crm_tasks`.`mins` AS `mins`, `crm_tasks`.`status` AS `status`, `crm_tasks`.`is_complete` AS `is_complete`, `crm_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, `crm_tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `crm_tasks`.priority AS `priority`
				FROM `crm_tasks`
					JOIN `crm_users` ON `crm_tasks`.`userid_fk` = `crm_users`.`userid`
					JOIN `crm_users` AS us ON `crm_tasks`.`created_by` = us.`userid` AND `crm_tasks`.`is_complete` = 0 
					LEFT JOIN `crm_jobs` ON `crm_tasks`.`jobid_fk` = `crm_jobs`.`jobid`					
					LEFT JOIN `crm_customers` ON `crm_jobs`.`custid_fk` = `crm_customers`.`custid`
				WHERE
					`crm_tasks`.`created_by` = '{$uid}' OR `crm_tasks`.`userid_fk` = '{$uid}'
					
					";
				
		$q = $this->db->query($sql);
		$data = $q->result_array();
		//echo $this->db->last_query();
		
		return array($data, $now);
	}
	
	/**
	 * Get all tasks
	 * For the week
	 */
	private function get_weekly_tasks($search_from_date = '', $search_to_date = '', $search_user = '')
	{
		$now = time();
		
		if (isset($_GET['from']))
		{
			$from_date = explode('-', $_GET['from']);
			if ($from_date_stamp = mktime(0, 0, 0, $from_date[1], $from_date[0], $from_date[2]))
			{
				$now = $from_date_stamp;
			}
		}
		
		$monday = date('Y-m-d', $now);
		$friday = date('Y-m-d', strtotime('next ' . date('l', $now)) - 86400);
		
		if (preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $search_from_date))
		{
			$search_from_date = explode('-', $search_from_date);
			if ($search_from_date_stamp = mktime(0, 0, 0, $search_from_date[1], $search_from_date[0], $search_from_date[2]))
			{
				$monday = date('Y-m-d', $search_from_date_stamp);
			}
		}
		
		if (preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $search_to_date))
		{
			$search_to_date = explode('-', $search_to_date);
			if ($search_to_date_stamp = mktime(0, 0, 0, $search_to_date[1], $search_to_date[0], $search_to_date[2]))
			{
				$friday = date('Y-m-d', $search_to_date_stamp);
			}
		}
		
		$user_search = '';
		
		if (is_numeric($search_user))
		{
			$user_search = " AND `crm_users`.`userid` = '{$search_user}'";
		}
		
		$sql = "SELECT `crm_tasks`.`taskid` AS `taskid`, `crm_tasks`.`task` AS `task`, DATE(`crm_tasks`.`start_date`) AS `start_date`, `crm_tasks`.`approved` AS `approved`,
						DATE(`crm_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`,
						DATEDIFF( DATE(`crm_tasks`.`end_date`), DATE('{$monday}') ) AS `delayed`,
						IF ( DATE(`crm_tasks`.`end_date`) = DATE('{$monday}'), '1', '0') AS `due_today`,
						DATEDIFF(`crm_tasks`.`end_date`, `crm_tasks`.`start_date`) + 1 AS `duration`,
						DATEDIFF('{$monday}', `crm_tasks`.`start_date`) AS `start_offset`,
						`crm_tasks`.`hours` AS `hours`, `crm_tasks`.`mins` AS `mins`, `crm_tasks`.`status` AS `status`, `crm_tasks`.`is_complete` AS `is_complete`, `crm_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, `crm_tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`
				FROM `crm_tasks`
				LEFT JOIN `crm_jobs` ON `crm_tasks`.`jobid_fk` = `crm_jobs`.`jobid`
				JOIN `crm_users` ON `crm_tasks`.`userid_fk` = `crm_users`.`userid` 
				LEFT JOIN `crm_customers` ON `crm_jobs`.`custid_fk` = `crm_customers`.`custid`
				WHERE
				(
					( DATE(`crm_tasks`.`end_date`) BETWEEN '{$monday}' AND '{$friday}' )
						OR
					( DATE(`crm_tasks`.`start_date`) BETWEEN '{$monday}' AND '{$friday}' )
				)
				{$user_search}
				
				UNION
				
				SELECT `crm_lead_tasks`.`taskid` AS `taskid`, `crm_lead_tasks`.`task` AS `task`, DATE(`crm_lead_tasks`.`start_date`) AS `start_date`, `crm_lead_tasks`.`approved` AS `approved`,
						DATE(`crm_lead_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`,
						DATEDIFF( DATE(`crm_lead_tasks`.`end_date`), DATE('{$monday}') ) AS `delayed`,
						IF ( DATE(`crm_lead_tasks`.`end_date`) = DATE('{$monday}'), '1', '0') AS `due_today`,
						DATEDIFF(`crm_lead_tasks`.`end_date`, `crm_lead_tasks`.`start_date`) + 1 AS `duration`,
						DATEDIFF('{$monday}', `crm_lead_tasks`.`start_date`) AS `start_offset`,
						`crm_lead_tasks`.`hours` AS `hours`, `crm_lead_tasks`.`mins` AS `mins`, `crm_lead_tasks`.`status` AS `status`, `crm_lead_tasks`.`is_complete` AS `is_complete`, `crm_lead_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, `crm_lead_tasks`.`leadid_fk` AS `jobid`, 'YES' AS `leadid`
				FROM `crm_lead_tasks`
				LEFT JOIN `crm_leads` ON `crm_lead_tasks`.`leadid_fk` = `crm_leads`.`leadid`
				JOIN `crm_users` ON `crm_lead_tasks`.`userid_fk` = `crm_users`.`userid` 
				LEFT JOIN `crm_customers` ON `crm_leads`.`custid_fk` = `crm_customers`.`custid`
				WHERE
				(
					( DATE(`crm_lead_tasks`.`end_date`) BETWEEN '{$monday}' AND '{$friday}' )
						OR
					( DATE(`crm_lead_tasks`.`start_date`) BETWEEN '{$monday}' AND '{$friday}' )
				)
				{$user_search}
				
				ORDER BY `company`, `end_date`";
		
		$q = $this->db->query($sql);
		$data = $q->result_array();
		#echo $sql;
		#print_r($data);
		
		return array($monday, $friday, $data, $now);
	}
	
	/**
	 * Search the tasks
	 */
	private function search_user_tasks($search_from_date = '', $search_to_date = '', $task_search = '')
	{
		/*$now = time();
		
		if (isset($_GET['from']))
		{
			$from_date = explode('-', $_GET['from']);
			if ($from_date_stamp = mktime(0, 0, 0, $from_date[1], $from_date[0], $from_date[2]))
			{
				$now = $from_date_stamp;
			}
		}
		
		$today=$monday = date('Y-m-d', $now);
		$friday = date('Y-m-d', strtotime('next ' . date('l', $now)) - 86400);
		$search="( DATE(`crm_tasks`.`end_date` AND `is_complete` = 0) >= '{$today}') OR  ( DATE(`crm_tasks`.`end_date`) <= '{$today}'  AND `is_complete` = 0) OR (DATE(`crm_tasks`.`start_date`) < '{$today}' AND DATE(`crm_tasks`.`end_date`) > '{$today}' AND `is_complete` = 0)";
		$search1="( DATE(`crm_lead_tasks`.`end_date`) >= '{$today}' AND `is_complete` = 0) OR ( DATE(`crm_lead_tasks`.`end_date`) <= '{$today}'   AND `is_complete` = 0) OR (DATE(`crm_lead_tasks`.`start_date`) < '{$today}' AND DATE(`crm_lead_tasks`.`end_date`) > '{$today}' AND `is_complete` = 0)";
	
		if (preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $search_from_date))
		{
			$search_from_date = explode('-', $search_from_date);
			if ($search_from_date_stamp = mktime(0, 0, 0, $search_from_date[1], $search_from_date[0], $search_from_date[2]))
			{
				$monday = date('Y-m-d', $search_from_date_stamp);
			}
			$search="( DATE(`crm_tasks`.`end_date`) <= '{$friday}' )	AND	( DATE(`crm_tasks`.`end_date`) >= '{$monday}' )";
			$search1="( DATE(`crm_lead_tasks`.`end_date`) <= '{$friday}' )	AND	( DATE(`crm_lead_tasks`.`end_date`) >= '{$monday}' )";
		}
		
		if (preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $search_to_date))
		{
			$search_to_date = explode('-', $search_to_date);
			if ($search_to_date_stamp = mktime(0, 0, 0, $search_to_date[1], $search_to_date[0], $search_to_date[2]))
			{
				$friday = date('Y-m-d', $search_to_date_stamp);
			}
			$search="( DATE(`crm_tasks`.`end_date`) <= '{$friday}' )	AND	( DATE(`crm_tasks`.`end_date`) >= '{$monday}' )";
			$search1="( DATE(`crm_lead_tasks`.`end_date`) <= '{$friday}' )	AND	( DATE(`crm_lead_tasks`.`end_date`) >= '{$monday}' )";
		}
		
		$user_search = '';
		
		if (is_numeric($search_user))
		{
			$user_search = " AND `crm_users`.`userid` = '{$search_user}'";
		}
		$sql = "SELECT `crm_tasks`.`taskid` AS `taskid`, `crm_tasks`.`task` AS `task`, `crm_users`.`first_name` AS `created`, `crm_tasks`.`remarks` AS `remark`, DATE(`crm_tasks`.`start_date`) AS `start_date`, DATE(`crm_tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`crm_tasks`.`actualend_date`) AS `actualend_date`,  `crm_tasks`.`approved` AS `approved`,
						DATE(`crm_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`,`crm_tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`crm_tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`crm_tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						DATEDIFF(`crm_tasks`.`end_date`, `crm_tasks`.`start_date`) + 1 AS `duration`,
						DATEDIFF('{$today}', `crm_tasks`.`start_date`) AS `start_offset`,
						`crm_tasks`.`hours` AS `hours`, `crm_tasks`.`mins` AS `mins`, `crm_tasks`.`status` AS `status`, `crm_tasks`.`is_complete` AS `is_complete`, `crm_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, `crm_tasks`.`jobid_fk` AS `jobid`, 0 AS lead
				FROM `crm_tasks`
				LEFT JOIN `crm_jobs` ON `crm_tasks`.`jobid_fk` = `crm_jobs`.`jobid`
				JOIN `crm_users` ON `crm_tasks`.`userid_fk` = `crm_users`.`userid` OR `crm_tasks`.`created_by` = `crm_users`.`userid`
				LEFT JOIN `crm_customers` ON `crm_jobs`.`custid_fk` = `crm_customers`.`custid`
				WHERE
				(
					{$search}	
				)
				{$user_search}
				";
				/*
				UNION
				
				SELECT `crm_lead_tasks`.`taskid` AS `taskid`, `crm_lead_tasks`.`task` AS `task`, DATE(`crm_lead_tasks`.`start_date`) AS `start_date`, `crm_lead_tasks`.`approved` AS `approved`,
						DATE(`crm_lead_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`,
						DATEDIFF( DATE(`crm_lead_tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`crm_lead_tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						DATEDIFF(`crm_lead_tasks`.`end_date`, `crm_lead_tasks`.`start_date`) + 1 AS `duration`,
						DATEDIFF('{$today}', `crm_lead_tasks`.`start_date`) AS `start_offset`,
						`crm_lead_tasks`.`hours` AS `hours`, `crm_lead_tasks`.`mins` AS `mins`, `crm_lead_tasks`.`status` AS `status`, `crm_lead_tasks`.`is_complete` AS `is_complete`, `crm_lead_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, 0 AS `jobid`, `crm_lead_tasks`.`leadid_fk` AS lead
				FROM `crm_lead_tasks`
				LEFT JOIN `crm_leads` ON `crm_lead_tasks`.`leadid_fk` = `crm_leads`.`leadid`
				JOIN `crm_users` ON `crm_lead_tasks`.`userid_fk` = `crm_users`.`userid`
				LEFT JOIN `crm_customers` ON `crm_leads`.`custid_fk` = `crm_customers`.`custid`
				WHERE
				(
					{$search1}
				)
				{$user_search}
				ORDER BY `end_date` ASC				
				";*/
				/*
		$q = $this->db->query($sql);
		$data = $q->result_array();
		
		return array($monday, $friday, $data, $now);
		*/
		
		//echo "$task_search";
		
		//New Search Functionality - 04/02/2013
		//echo $search_from_date;

		if ((!empty($search_from_date)) && (!empty($search_to_date))) {
			$varStart_date = "AND `crm_tasks`.`start_date` BETWEEN '".date("Y-m-d",strtotime($search_from_date))."' AND '".date("Y-m-d",strtotime($search_to_date))."' ";
		}
		else {
			$varStart_date = "";
		}

		$uidd = $this->session->userdata['logged_in_user']; 
		$uid = $uidd['userid'];
		$urole = $uidd['role_id'];

		$now = time();
		if (isset($_GET['day']))
		{
			$from_date = explode('-', $_GET['day']);
			if ($from_date_stamp = mktime(0, 0, 0, $from_date[1], $from_date[0], $from_date[2]))
			{
				$now = $from_date_stamp;
			}
		}
		
		$today = date('Y-m-d', $now);
		//echo $task_search;
		if ($task_search == 0) {
			$sql = "SELECT crm_jobs.jobid as jobid, `crm_tasks`.`taskid` AS `taskid`, `crm_tasks`.`task` AS `task`, `crm_tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`crm_tasks`.`start_date`) AS `start_date`, DATE(`crm_tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`crm_tasks`.`actualend_date`) AS `actualend_date`, `crm_tasks`.`approved` AS `approved`, `crm_tasks`.`require_qc` AS `require_qc`,
						DATE(`crm_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`, `crm_tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`crm_tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`crm_tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`crm_tasks`.`hours` AS `hours`, `crm_tasks`.`mins` AS `mins`, `crm_tasks`.`status` AS `status`, `crm_tasks`.`is_complete` AS `is_complete`, `crm_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, `crm_tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `crm_tasks`.priority AS `priority`
				FROM `crm_tasks`
					JOIN `crm_users` ON `crm_tasks`.`userid_fk` = `crm_users`.`userid`
					JOIN `crm_users` AS us ON `crm_tasks`.`created_by` = us.`userid` AND `crm_tasks`.`is_complete` = 0 
					LEFT JOIN `crm_jobs` ON `crm_tasks`.`jobid_fk` = `crm_jobs`.`jobid`					
					LEFT JOIN `crm_customers` ON `crm_jobs`.`custid_fk` = `crm_customers`.`custid`
				WHERE
					(`crm_tasks`.`created_by` = '{$uid}' OR `crm_tasks`.`userid_fk` = '{$uid}') {$varStart_date}
					";
		}
		else if ($task_search == 1) {
			$sql = "SELECT crm_jobs.jobid as jobid, `crm_tasks`.`taskid` AS `taskid`, `crm_tasks`.`task` AS `task`, `crm_tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`crm_tasks`.`start_date`) AS `start_date`, DATE(`crm_tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`crm_tasks`.`actualend_date`) AS `actualend_date`, `crm_tasks`.`approved` AS `approved`, `crm_tasks`.`require_qc` AS `require_qc`,
						DATE(`crm_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`, `crm_tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`crm_tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`crm_tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`crm_tasks`.`hours` AS `hours`, `crm_tasks`.`mins` AS `mins`, `crm_tasks`.`status` AS `status`, `crm_tasks`.`is_complete` AS `is_complete`, `crm_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, `crm_tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `crm_tasks`.priority AS `priority`
				FROM `crm_tasks`
					JOIN `crm_users` ON `crm_tasks`.`userid_fk` = `crm_users`.`userid`
					JOIN `crm_users` AS us ON `crm_tasks`.`created_by` = us.`userid` AND `crm_tasks`.`is_complete` = 1
					LEFT JOIN `crm_jobs` ON `crm_tasks`.`jobid_fk` = `crm_jobs`.`jobid`					
					LEFT JOIN `crm_customers` ON `crm_jobs`.`custid_fk` = `crm_customers`.`custid`
				WHERE
					(`crm_tasks`.`created_by` = '{$uid}' OR `crm_tasks`.`userid_fk` = '{$uid}') {$varStart_date}
					";
		}
		else {
			 $sql = "SELECT crm_jobs.jobid as jobid, `crm_tasks`.`taskid` AS `taskid`, `crm_tasks`.`task` AS `task`, `crm_tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`crm_tasks`.`start_date`) AS `start_date`, DATE(`crm_tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`crm_tasks`.`actualend_date`) AS `actualend_date`, `crm_tasks`.`approved` AS `approved`, `crm_tasks`.`require_qc` AS `require_qc`,
						DATE(`crm_tasks`.`end_date`) AS `end_date`, CONCAT(`crm_users`.`first_name`, ' ', `crm_users`.`last_name`) AS `user_label`, `crm_tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`crm_tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`crm_tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`crm_tasks`.`hours` AS `hours`, `crm_tasks`.`mins` AS `mins`, `crm_tasks`.`status` AS `status`, `crm_tasks`.`is_complete` AS `is_complete`, `crm_tasks`.`userid_fk` AS `userid_fk`,
						`crm_customers`.`company` AS `company`, `crm_tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `crm_tasks`.priority AS `priority`
				FROM `crm_tasks`
					JOIN `crm_users` ON `crm_tasks`.`userid_fk` = `crm_users`.`userid`
					JOIN `crm_users` AS us ON `crm_tasks`.`created_by` = us.`userid`
					LEFT JOIN `crm_jobs` ON `crm_tasks`.`jobid_fk` = `crm_jobs`.`jobid`					
					LEFT JOIN `crm_customers` ON `crm_jobs`.`custid_fk` = `crm_customers`.`custid`
				WHERE
					(`crm_tasks`.`created_by` = '{$uid}' OR `crm_tasks`.`userid_fk` = '{$uid}') {$varStart_date}
					";
		
		}
		
		$q = $this->db->query($sql);
		$data = $q->result_array();
		//echo "<pre>"; print_r($data); exit;
		return array($data, $now);
	
	}
	
	function search()
	{
		
		//mychanges
		$qqql = $this->db->query("SELECT `crm_tasks`.`created_by`FROM `crm_tasks`,`crm_users` WHERE `crm_tasks`.`userid_fk` = `crm_users`.`userid`");
		//echo $this->db->last_query(); exit; 	
		$data['created_by'] = $qqql->result_array();
		//print_r($data['created_by']);		
		
		//echo "<pre>"; print_r($_POST); exit;
		
		$res = $this->search_user_tasks($_POST['task_search_start_date'], $_POST['task_search_end_date'], $_POST['task_search']);
		//echo "<pre>"; print_r($res[0]); echo "<br>";
		
		foreach($res[0] as $r) {
		//print_r($r);
			$jjj = $this->db->query("SELECT `created_by` FROM `crm_tasks` WHERE `taskid`=".$r['taskid']);
			$data['userid_fk'] = $jjj->result_array();
		}
		//$data['start_date_stamp'] = $res[3];
		//$data['results'] = $res[2];
		
		$data['results'] = $res[0];

		//$data['page_title'] = 'Task List for the week <span>' . $res[0] . '</span> TO <span>' . $res[1] . '</span>';
		$this->load->view('tasks/search_results_view', $data);
	}
}

?>
