<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends crm_controller {
    
	public $userdata;
	public $cfg;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->helper('form');
		//$this->login_model->check_login(array(0, 1, 2, 3, 5, 4, 6));
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
    }
	
	public function index($extend = FALSE)
	{	
		//mychanges
		$qqql = $this->db->query("SELECT `".$this->cfg['dbpref']."tasks`.`created_by` FROM `".$this->cfg['dbpref']."tasks`,`".$this->cfg['dbpref']."users` WHERE `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`");
		$data['created_by'] = $qqql->result_array();
			
		$res = $this->get_daily_tasks($extend);
		//echo "<pre>"; print_r($res); exit;
		foreach($res[0] as $r) {
			$jjj = $this->db->query("SELECT `created_by` FROM `".$this->cfg['dbpref']."tasks` WHERE `taskid`=".$r['taskid']);
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
		
		$cq = $this->db->get($this->cfg['dbpref'].'contract_jobs');
		
		$temp_cont = $cq->result_array();
		
		$data['assigned_contractors'] = array();
		
		foreach ($temp_cont as $tc)
		{
			$data['assigned_contractors'][] = $tc['userid_fk'];
		}
		$this->db->select(array('job_title','jobid'));
		$this->db->where_not_in('job_title','');
		$project = $this->db->get($this->cfg['dbpref'] . 'leads');
		$data['project'] = $project->result_array();

		//mychanges
			$qqql = $this->db->query("SELECT `".$this->cfg['dbpref']."tasks`.`created_by`,`".$this->cfg['dbpref']."tasks`.`userid_fk` FROM `".$this->cfg['dbpref']."tasks`,`".$this->cfg['dbpref']."users` WHERE `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`");
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

		  $sql = "SELECT ".$this->cfg['dbpref']."leads.jobid as jobid, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
						DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
						`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
				FROM `".$this->cfg['dbpref']."tasks`
					JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
					JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 0 
					LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`jobid`					
					LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
				WHERE
					`".$this->cfg['dbpref']."tasks`.`created_by` = '{$uid}' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '{$uid}'
					
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
			$user_search = " AND `".$this->cfg['dbpref']."users`.`userid` = '{$search_user}'";
		}
		
		$sql = "SELECT `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`,
						DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`,
						DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('{$monday}') ) AS `delayed`,
						IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('{$monday}'), '1', '0') AS `due_today`,
						DATEDIFF(`".$this->cfg['dbpref']."tasks`.`end_date`, `".$this->cfg['dbpref']."tasks`.`start_date`) + 1 AS `duration`,
						DATEDIFF('{$monday}', `".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_offset`,
						`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
						`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`
				FROM `".$this->cfg['dbpref']."tasks`
				LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`jobid`
				JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid` 
				LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
				WHERE
				(
					( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) BETWEEN '{$monday}' AND '{$friday}' )
						OR
					( DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) BETWEEN '{$monday}' AND '{$friday}' )
				)
				{$user_search}
				
				UNION
				
				SELECT `".$this->cfg['dbpref']."lead_tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."lead_tasks`.`task` AS `task`, DATE(`".$this->cfg['dbpref']."lead_tasks`.`start_date`) AS `start_date`, `".$this->cfg['dbpref']."lead_tasks`.`approved` AS `approved`,
						DATE(`".$this->cfg['dbpref']."lead_tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`,
						DATEDIFF( DATE(`".$this->cfg['dbpref']."lead_tasks`.`end_date`), DATE('{$monday}') ) AS `delayed`,
						IF ( DATE(`".$this->cfg['dbpref']."lead_tasks`.`end_date`) = DATE('{$monday}'), '1', '0') AS `due_today`,
						DATEDIFF(`".$this->cfg['dbpref']."lead_tasks`.`end_date`, `".$this->cfg['dbpref']."lead_tasks`.`start_date`) + 1 AS `duration`,
						DATEDIFF('{$monday}', `".$this->cfg['dbpref']."lead_tasks`.`start_date`) AS `start_offset`,
						`".$this->cfg['dbpref']."lead_tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."lead_tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."lead_tasks`.`status` AS `status`, `".$this->cfg['dbpref']."lead_tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."lead_tasks`.`userid_fk` AS `userid_fk`,
						`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."lead_tasks`.`leadid_fk` AS `jobid`, 'YES' AS `leadid`
				FROM `".$this->cfg['dbpref']."lead_tasks`
				LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."lead_tasks`.`leadid_fk` = `".$this->cfg['dbpref']."leads`.`leadid`
				JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."lead_tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid` 
				LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
				WHERE
				(
					( DATE(`".$this->cfg['dbpref']."lead_tasks`.`end_date`) BETWEEN '{$monday}' AND '{$friday}' )
						OR
					( DATE(`".$this->cfg['dbpref']."lead_tasks`.`start_date`) BETWEEN '{$monday}' AND '{$friday}' )
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
		
		//echo "$task_search";
		
		//New Search Functionality - 04/02/2013
		//echo $search_from_date;

		if ((!empty($search_from_date)) && (!empty($search_to_date))) {
			$varStart_date = "AND `".$this->cfg['dbpref']."tasks`.`start_date` BETWEEN '".date("Y-m-d",strtotime($search_from_date))."' AND '".date("Y-m-d",strtotime($search_to_date))."' ";
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
			$sql = "SELECT ".$this->cfg['dbpref']."leads.jobid as jobid, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
						DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
						`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
				FROM `".$this->cfg['dbpref']."tasks`
					JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
					JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 0 
					LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`jobid`					
					LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
				WHERE
					(`".$this->cfg['dbpref']."tasks`.`created_by` = '{$uid}' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '{$uid}') {$varStart_date}
					";
		}
		else if ($task_search == 1) {
			$sql = "SELECT ".$this->cfg['dbpref']."leads.jobid as jobid, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
						DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
						`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
				FROM `".$this->cfg['dbpref']."tasks`
					JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
					JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid` AND `".$this->cfg['dbpref']."tasks`.`is_complete` = 1
					LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`jobid`					
					LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
				WHERE
					(`".$this->cfg['dbpref']."tasks`.`created_by` = '{$uid}' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '{$uid}') {$varStart_date}
					";
		}
		else {
			 $sql = "SELECT ".$this->cfg['dbpref']."leads.jobid as jobid, `".$this->cfg['dbpref']."tasks`.`taskid` AS `taskid`, `".$this->cfg['dbpref']."tasks`.`task` AS `task`, `".$this->cfg['dbpref']."tasks`.`remarks` AS `remark`, us.`first_name` AS `created`, DATE(`".$this->cfg['dbpref']."tasks`.`start_date`) AS `start_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualstart_date`) AS `actualstart_date`, DATE(`".$this->cfg['dbpref']."tasks`.`actualend_date`) AS `actualend_date`, `".$this->cfg['dbpref']."tasks`.`approved` AS `approved`, `".$this->cfg['dbpref']."tasks`.`require_qc` AS `require_qc`,
						DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) AS `end_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`, `".$this->cfg['dbpref']."tasks`.`created_by` AS `created_byid`,
						DATEDIFF( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`), DATE('{$today}') ) AS `delayed`,
						IF ( DATE(`".$this->cfg['dbpref']."tasks`.`end_date`) = DATE('{$today}'), '1', '0') AS `due_today`,
						`".$this->cfg['dbpref']."tasks`.`hours` AS `hours`, `".$this->cfg['dbpref']."tasks`.`mins` AS `mins`, `".$this->cfg['dbpref']."tasks`.`status` AS `status`, `".$this->cfg['dbpref']."tasks`.`is_complete` AS `is_complete`, `".$this->cfg['dbpref']."tasks`.`userid_fk` AS `userid_fk`,
						`".$this->cfg['dbpref']."customers`.`company` AS `company`, `".$this->cfg['dbpref']."tasks`.`jobid_fk` AS `jobid`, 'NO' AS `leadid`, `".$this->cfg['dbpref']."tasks`.priority AS `priority`
				FROM `".$this->cfg['dbpref']."tasks`
					JOIN `".$this->cfg['dbpref']."users` ON `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
					JOIN `".$this->cfg['dbpref']."users` AS us ON `".$this->cfg['dbpref']."tasks`.`created_by` = us.`userid`
					LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."tasks`.`jobid_fk` = `".$this->cfg['dbpref']."leads`.`jobid`					
					LEFT JOIN `".$this->cfg['dbpref']."customers` ON `".$this->cfg['dbpref']."leads`.`custid_fk` = `".$this->cfg['dbpref']."customers`.`custid`
				WHERE
					(`".$this->cfg['dbpref']."tasks`.`created_by` = '{$uid}' OR `".$this->cfg['dbpref']."tasks`.`userid_fk` = '{$uid}') {$varStart_date}
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
		$qqql = $this->db->query("SELECT `".$this->cfg['dbpref']."tasks`.`created_by`FROM `".$this->cfg['dbpref']."tasks`,`".$this->cfg['dbpref']."users` WHERE `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`");
		//echo $this->db->last_query(); exit; 	
		$data['created_by'] = $qqql->result_array();
		//print_r($data['created_by']);		
		
		//echo "<pre>"; print_r($_POST); exit;
		
		$res = $this->search_user_tasks($_POST['task_search_start_date'], $_POST['task_search_end_date'], $_POST['task_search']);
		//echo "<pre>"; print_r($res[0]); echo "<br>";
		
		foreach($res[0] as $r) {
		//print_r($r);
			$jjj = $this->db->query("SELECT `created_by` FROM `".$this->cfg['dbpref']."tasks` WHERE `taskid`=".$r['taskid']);
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
