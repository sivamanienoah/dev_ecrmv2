<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends crm_controller {
    
	public $userdata;
	public $cfg;
	
    public function __construct() {
        parent::__construct();
		$this->load->helper('form');
		$this->load->model('task_model');
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
    }
	
	
	public function index($extend = FALSE) {	
		$data['created_by'] = $this->task_model->get_task_created_by();

		$res = $this->get_daily_tasks($extend);

		foreach($res[0] as $r) 
		{
			$data['userid_fk'] = $this->task_model->get_created_by_for_task($r['taskid']);
		}
		$data['results'] = $res[0];
		$data['start_date_stamp'] = $res[1];
		$data['page_title'] = 'Task List for '. date('l, jS F y', $res[1]);

		$this->load->view('tasks/full_view', $data);
	}
	
	/**
	 * Tasks for the main menu
	 */
	public function all() {
		$data = array();
		
		$data['user_accounts'] = array();

		// $users = $this->db->get($this->cfg['dbpref'] . 'users');
		$users = $this->task_model->getActiveUsers();

		if ($users['num'] > 0)
		{
			$data['user_accounts'] = $users['user'];
		}
		
		$data['created_by'] = $this->task_model->get_task_created_by();

		$this->load->view('tasks/main_view', $data);
	}
	
	
	/**
	 * Get all tasks
	 * For today
	 */
	private function get_daily_tasks($extend = FALSE) {
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

		$data = $this->task_model->get_task_daily($uid, $today);
		
		return array($data, $now);
	}
	
	//Search functionality
	function search() {
		$data['created_by'] = $this->task_model->get_task_created_by();
		
		$res = $this->search_user_tasks($_POST['task_search_start_date'], $_POST['task_search_end_date'], $_POST['task_search']);

		foreach($res[0] as $r) 
		{
			$data['userid_fk'] = $this->task_model->get_created_by_for_task($r['taskid']);
		}

		$data['results'] = $res[0];

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
