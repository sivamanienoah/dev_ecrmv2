<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends crm_controller {
    
	public $userdata;
	public $cfg;
	
    public function __construct() {
        parent::__construct();
		$this->load->helper('form');
		$this->load->helper('task');
		$this->load->helper('lead');
		$this->load->model('task_model');
		$this->load->model('project_model');
		$this->login_model->check_login();
		$this->load->helper('text');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
	
	
	public function index($extend = FALSE, $task_end_notify = FALSE) {	
/*	$data['created_by'] = $this->task_model->get_task_created_by();
	
 	
		$res = $this->get_daily_tasks($extend, $task_end_notify = FALSE);

		foreach($res[0] as $r) 
		{
			$data['userid_fk'] = $this->task_model->get_created_by_for_task($r['taskid']);
		}
		$data['results'] = $res[0];
		$data['start_date_stamp'] = $res[1];
		$data['page_title'] = 'Task List for '. date('l, jS F y', $res[1]);
 */
		

		$this->load->model('task_model');
		$data['category_listing_ls'] = $this->project_model->getTaskCategoryList();
		$newarray=array();
		$uidd = $this->session->userdata['logged_in_user']; 
		$uid  = $uidd['userid'];
		$search=array(
					'taskcomplete'=>0,
					'taskowner'=>$uid,
					'taskallocateduser'=>$uid,
					'taskstartdate'=>'',
					'taskenddate'=>''
					);
		foreach($data['category_listing_ls'] as $row) 
		{
			$newarray[]=$this->task_model->taskCategoryQuery($row['id'],$row['task_category'],$search,'OR');
		}
		$data['newarray']=$newarray;
		$this->load->view('tasks/full_view', $data);
	}
	
	/**
	 * Tasks for the main menu
	 */
	public function all() {
		$data = array();
		
		$data['user_accounts'] = array();

		if(isset($_POST) && isset($_POST['type']) && $_POST['type'] == 'task_end_notify'){
			$data['task_end_notify'] = 'task_end_notify';
		} else {
			$data['task_end_notify'] = '';
		}		// $users = $this->db->get($this->cfg['dbpref'] . 'users');
		$users = $this->task_model->getActiveUsers();

		if ($users['num'] > 0)
		{
			$data['user_accounts'] = $users['user'];
		}
		$data['category_listing_ls'] = $this->project_model->getTaskCategoryList();
		$data['project_listing_ls'] = $this->project_model->ListActiveprojects();
		$data['created_by'] = $this->task_model->get_task_created_by();
		$this->load->view('tasks/main_view', $data);			
	}
	
	
	/**
	 * Get all tasks
	 * For today
	 */
	private function get_daily_tasks($extend = FALSE,$task_end_notify = FALSE) {
		$uidd = $this->session->userdata['logged_in_user']; 
		$uid = $uidd['userid'];

		$now = time();
		if (isset($_GET['day'])) {
			$from_date = explode('-', $_GET['day']);
			if ($from_date_stamp = mktime(0, 0, 0, $from_date[1], $from_date[0], $from_date[2]))
			{
				$now = $from_date_stamp;
			}
		}
		
		$today = date('Y-m-d', $now);

		$data = $this->task_model->get_task_daily($uid, $today, $task_end_notify);
		
		return array($data, $now);
	}
	
	//Search functionality
	function search() {
		$uidd = $this->session->userdata['logged_in_user']; 
		$uid = $uidd['userid'];
	
		$task_owner =element_value_check('task_owner_user');
		$task_allocated =element_value_check('task_allocated_user');
		echo "notificate".$notification_val =element_value_check('a');
		
		if($task_owner=="")
		{
			$task_owner = $uid;
		}
		
		$search=array(
					'taskcomplete'=>element_value_check('task_search'),
					'taskowner'=> $task_owner,
					'taskallocateduser'=>$task_allocated,
					'taskstartdate'=>element_value_check('task_search_start_date'),
					'taskenddate'=>element_value_check('task_search_end_date'),
					'taskproject'=>element_value_check('task_project')
					);
		if(!empty($task_owner) &&!empty($task_allocated))
		{
			$operation= 'AND';
		}
		else
		{
			$operation ='OR';
		}
		
		
/* 		$data['created_by'] = $this->task_model->get_task_created_by();
		
		$res = $this->search_user_tasks($_POST['task_search_start_date'], $_POST['task_search_end_date'], $_POST['task_search']);

		foreach($res[0] as $r) 
		{
			$data['userid_fk'] = $this->task_model->get_created_by_for_task($r['taskid']);
		}

		$data['results'] = $res[0]; */
	
		$this->load->model('task_model');
		$data['category_listing_ls'] = $this->project_model->getTaskCategoryList(element_value_check('task_category'));
		
		$newarray=array();
		
		foreach($data['category_listing_ls'] as $row) 
		{
			$newarray[]=$this->task_model->taskCategoryQuery($row['id'],$row['task_category'],$search,$operation);
		}

		$data['newarray']=$newarray;
		 
		$this->load->view('tasks/search_results_view', $data);
	}
	
	/**
	 * Search the tasks
	 */
	private function search_user_tasks($search_from_date = '', $search_to_date = '', $task_search = '') {		
		//New Search Functionality - 04/02/2013
		//echo $search_from_date;

		if ((!empty($search_from_date)) && (!empty($search_to_date))) {
			$varStart_date = "AND `".$this->cfg['dbpref']."tasks`.`start_date` BETWEEN '".date("Y-m-d",strtotime($search_from_date))."' AND '".date("Y-m-d",strtotime($search_to_date))."' ";
		}
		else {
			$varStart_date = "";
		}

		$uidd = $this->session->userdata['logged_in_user']; 
		$uid  = $uidd['userid'];

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
		
		switch($task_search) {
			case 0:
				//Work In Progress
				$data = $this->task_model->get_task_search_wip($today, $uid, $varStart_date);
			break;
			case 1:
				//Completed
				$data = $this->task_model->get_task_search_comp($today, $uid, $varStart_date);
			break;
			case -1:
				//All
				$data = $this->task_model->get_task_search_all($today, $uid, $varStart_date);
			break;
		}
		return array($data, $now);
	}
	
}

?>
