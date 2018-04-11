<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
class Dashboard extends crm_controller 
{
	function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
        $this->load->helper('custom');
        $this->load->library('validation');
		$this->load->helper('lead_stage');
		$this->load->helper('url'); 
		$this->load->model('projects/dashboard_model');
		$this->load->model('report/report_lead_region_model');
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$this->userdata   = $this->session->userdata('logged_in_user');
	}
	
	/*
	* Utilization Metrics
	*/
	function index()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$dept   			  = array();
		$data['page_heading'] = "Utiliztion Metrics Dashboard";
		
		// $practices = $this->dashboard_model->get_practices();
		// echo "<pre>"; print_r($this->input->post()); exit;
		
		$timesheet_db = $this->load->database("timesheet",true);
				
		$start_date = date("Y-m-1");
		$end_date   = date("Y-m-d");
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		
		$where = '';
		// echo $start_date.' '.$end_date; exit;
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			$where .= " and project_code NOT IN ('Leave')";
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			$where .= " and project_code NOT IN ('HOL')";
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			$where .= " and project_code NOT IN ('HOL','Leave')";
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		
		$department_ids = $this->input->post("department_ids");
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			$data['department_ids'] = $department_ids;
			$where .= " and dept_id in ($dids)";
		} else {
			$where .= " and dept_id in ('10','11')";
		}
		
		$practice_ids = $this->input->post("practice_ids");
		
		//for practices
		$this->db->select('t.practice_id, t.practice_name');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->where("t.practice_id !=", 0);
		$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
		$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			if(!empty($dids)) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
		}
		if(count($practice_ids)>0 && !empty($practice_ids)) {
			$pids = implode(",",$practice_ids);
			$data['practice_ids'] = $practice_ids;
			$where .= " and practice_id in ($pids)";
		}
		
		$this->db->group_by('t.practice_id');
		$this->db->order_by('t.practice_name');
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		if($query->num_rows()>0) {
			$data['practice_ids_selected'] = $query->result();
		}
		
		$skill_ids = $this->input->post("skill_ids");
		if(count($department_ids)>0 && !empty($department_ids)) {
			$sids = implode(",",$skill_ids);
			if(count($skill_ids)>0 && !empty($skill_ids))
			{
				$data['skill_ids'] = $skill_ids;
				$where .= " and skill_id in ($sids)";
			}
			
			if($this->input->post("department_ids")) 
			{
				$ids = $this->input->post("department_ids");
				$dids = implode(',',$ids);
				
				$p_ids = $practice_ids;
				$pids = implode(',',$p_ids);
				// $start_date = $this->input->post("start_date");
				// $end_date   = $this->input->post("end_date");
				
				// echo "<pre>"; print_R($_POST); exit;

				$this->db->select('t.skill_id, t.skill_name as name');
				$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
				$this->db->where("t.practice_id !=", 0);
				$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
				$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
				if(!empty($ids))
				$this->db->where_in("t.dept_id", $ids);
				if(!empty($p_ids))
				$this->db->where_in("t.practice_id", $practice_ids);
				$this->db->group_by('t.skill_id');
				$this->db->order_by('t.skill_name');
				$query = $this->db->get();
				// echo $this->db->last_query(); exit;
				$data['skill_ids_selected'] = $query->result();
			}
			// $data['skill_ids_selected'] = $qry->result();
		}

		$member_ids = $this->input->post("member_ids");
		if(count($skill_ids)>0 && !empty($skill_ids) && count($department_ids)>0 && !empty($department_ids)) 
		{
			$mids = "'".implode("','",$member_ids)."'";
			if(count($member_ids)>0 && !empty($member_ids)) {
				$data['member_ids'] = $member_ids;
				$where .= " and username in ($mids)";
			}
			$qry1 = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and v.skill_id in ($sids) and t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc");	
			
			$data['member_ids_selected'] = $qry1->result();			
		}
		
		
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;
		$json 				= '';
		
		$getITDataQry = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours,cost_per_hour,resource_duration_cost, project_code, direct_cost_per_hour, resource_duration_direct_cost,entry_year,entry_month
		FROM crm_timesheet_data 
		WHERE start_time between '$start_date' and '$end_date' AND resoursetype != '' $where";
		
		//echo $getITDataQry;	
		$sql = $this->db->query($getITDataQry);
		
		
		$this->db->select('dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, cost_per_hour, resource_duration_cost, project_code, direct_cost_per_hour, resource_duration_direct_cost, entry_year, entry_month');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data');
		$this->db->where("resoursetype !=", 0);
		$this->db->where("(start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
		$this->db->where("(start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			// $this->db->where("project_code !=", 'Leave');
			$this->db->where_not_in("project_code", array('Leave'));
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			// $this->db->where("project_code !=", 'HOL');
			$this->db->where_not_in("project_code", array('HOL'));
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			// $where .= " and project_code NOT IN ('HOL','Leave')";
			$this->db->where_not_in("project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			if(!empty($dids)) {
				$this->db->where_in("dept_id", $department_ids);
			}
		}
		$query = $this->db->get();
		
		// echo "<br>****<br>" . $this->db->last_query();
		
		$data['resdata'] = $sql->result();
		$arr_depts          = array();
		$check_array 	    = array();
		$check_user_array   = array();
		$arr_depts1		    = array();
		$arr_user_avail_set = array();
		/* echo "<pre>"; print_r($data['resdata']); echo "</pre>"; */
		
		// get all departments from timesheet
		$dept = $timesheet_db->query("SELECT department_id, department_name FROM ".$timesheet_db->dbprefix('department')." where department_id IN ('10','11') ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		$timesheet_db->close();

		$data['start_date'] 	  = $start_date;
		$data['end_date']   	  = $end_date;
		$data['results']    	  = $arr_depts;
		$data['filter_area_status'] = $this->input->post("filter_area_status");
		// echo "<pre>"; print_r($data); die;
		$this->load->view("projects/project_dashboard", $data);
	}
	
	/*
	* Utilization Metrics
	*/
	function utilization_metrics_beta()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$dept   			  = array();
		$data['page_heading'] = "Utiliztion Metrics Dashboard";
		
		$timesheet_db = $this->load->database("timesheet", true);
				
		$start_date = date("Y-m-1");
		$end_date   = date("Y-m-d");
		// $start_date = date("Y-m-d", strtotime('01-04-2017'));
		// $end_date   = date("Y-m-d", strtotime('30-04-2017'));
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		
		$where = '';
		// echo $start_date.' '.$end_date; exit;
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			$where .= " and project_code NOT IN ('Leave')";
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			$where .= " and project_code NOT IN ('HOL')";
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			$where .= " and project_code NOT IN ('HOL','Leave')";
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		
		$entity_ids = $this->input->post("entity_ids");
		if(!empty($entity_ids) && count($entity_ids)>0) {
			$entis = implode(",",$entity_ids);
			$data['entity_ids'] = $entity_ids;
			$where .= " and entity_id in ($entis)";
		}
		
		$department_ids = $this->input->post("department_ids");
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			$data['department_ids'] = $department_ids;
			$where .= " and dept_id in ($dids)";
		} else {
			$where .= " and dept_id in ('10','11')";
		}
		
		$practice_ids = $this->input->post("practice_ids");
		
		//for practices
		/* $this->db->select('t.practice_id, t.practice_name');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->where("t.practice_id !=", 0);
		$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
		$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			if(!empty($dids)) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
		}
		if(count($practice_ids)>0 && !empty($practice_ids)) {
			$pids = implode(",",$practice_ids);
			$data['practice_ids'] = $practice_ids;
			$where .= " and practice_id in ($pids)";
		}
		$this->db->group_by('t.practice_id');
		$this->db->order_by('t.practice_name');
		$query = $this->db->get();
		if($query->num_rows()>0) {
			$data['practice_ids_selected'] = $query->result();
		} */
		
		$practice_not_in_array = array('6','7','8');
		$this->db->select('id as practice_id, practices as practice_name');
		$this->db->from($this->cfg['dbpref']. 'practices');
		$this->db->where("status !=", 0);
		if(count($practice_ids)>0 && !empty($practice_ids)) {
			$pids = implode(",",$practice_ids);
			$data['practice_ids'] = $practice_ids;
			$where .= " and l.practice in ($pids)";
		}
		$this->db->where_not_in("id", $practice_not_in_array);
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$data['practice_ids_selected'] = $query->result();
		
		$skill_ids = $this->input->post("skill_ids");
		if(count($department_ids)>0 && !empty($department_ids)) {
			$sids = implode(",",$skill_ids);
			if(count($skill_ids)>0 && !empty($skill_ids))
			{
				$data['skill_ids'] = $skill_ids;
				$where .= " and skill_id in ($sids)";
			}
			
			if($this->input->post("department_ids")) 
			{
				$ids = $this->input->post("department_ids");
				$dids = implode(',',$ids);
				
				$p_ids = $practice_ids;
				$pids = implode(',',$p_ids);

				$this->db->select('t.skill_id, t.skill_name as name');
				// $this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
				$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
				$this->db->where("t.practice_id !=", 0);
				$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
				$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
				if(!empty($ids))
				$this->db->where_in("t.dept_id", $ids);
				if(!empty($p_ids))
				$this->db->where_in("t.practice_id", $practice_ids);
				$this->db->group_by('t.skill_id');
				$this->db->order_by('t.skill_name');
				$query = $this->db->get();
				// echo $this->db->last_query(); exit;
				$data['skill_ids_selected'] = $query->result();
			}
			// $data['skill_ids_selected'] = $qry->result();
		}

		$member_ids = $this->input->post("member_ids");
		if(count($skill_ids)>0 && !empty($skill_ids) && count($department_ids)>0 && !empty($department_ids)) 
		{
			$mids = "'".implode("','",$member_ids)."'";
			if(count($member_ids)>0 && !empty($member_ids)) {
				$data['member_ids'] = $member_ids;
				$where .= " and username in ($mids)";
			}
			$qry1 = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and v.skill_id in ($sids) and t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc");	
			
			$data['member_ids_selected'] = $qry1->result();			
		}

		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.entity_id, t.entity_name, t.practice_id, t.practice_name');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref']. 'leads as l', 'l.pjt_id = t.project_code', 'LEFT');
		// $this->db->join($this->cfg['dbpref']. 'practices as p', 'p.id = l.practice');
		$this->db->where("t.resoursetype !=", '');
		// $this->db->where("t.project_code", 'ITS-IIT- 01-0414'); //for testing load some data only
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			$this->db->where_not_in("t.project_code", array('Leave'));
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			$this->db->where_not_in("t.project_code", array('HOL'));
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			$this->db->where_not_in("t.project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		if(!empty($entity_ids) && count($entity_ids)>0) {
			$data['entity_ids'] = $entity_ids;
			$this->db->where_in('t.entity_id', $entity_ids);
		}
		if(!empty($practice_ids) && count($practice_ids)>0) {
			$data['practice_ids'] = $practice_ids;
			$this->db->where_in('l.practice', $practice_ids);
		}
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			if(!empty($dids)) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
		} else {
			$deptwhere = "t.dept_id IN ('10','11')";
			$this->db->where($deptwhere);
		}
		if(count($skill_ids)>0 && !empty($skill_ids)) {
			$this->db->where_in('t.skill_id', $skill_ids);
		}
		if(count($member_ids)>0 && !empty($member_ids)) {
			$this->db->where_in('t.username', $member_ids);
		}
		$this->db->where('l.practice is not null');
		$query 			 = $this->db->get();		
		$data['resdata'] = $query->result();
		
		// echo $this->db->last_query(); die;
		
		// echo "<pre>"; print_r($data['resdata']); die;

		$arr_depts          = array();
		$check_array 	    = array();
		$check_user_array   = array();
		$arr_depts1		    = array();
		$arr_user_avail_set = array();
		$data['conversion_rates'] = $this->get_currency_rates();
		/* echo "<pre>"; print_r($data['resdata']); echo "</pre>"; */
		
		// get all departments from timesheet
		$dept = $timesheet_db->query("SELECT department_id, department_name FROM ".$timesheet_db->dbprefix('department')." where department_id IN ('10','11') ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		$timesheet_db->close();
		
		$this->db->select('div_id, division_name');
		$this->db->where("status", 1);
		$entity_query 		= $this->db->get($this->cfg['dbpref'].'sales_divisions');
		$data['entitys'] 	= $entity_query->result();

		$data['start_date'] 	  = $start_date;
		$data['end_date']   	  = $end_date;
		$data['results']    	  = $arr_depts;
		$data['filter_area_status'] = $this->input->post("filter_area_status");

		// echo "<pre>"; print_r($data); die;
		$this->load->view("projects/project_dashboard_beta", $data);
	}
	
	function trend_analysis()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		// echo "<pre>"; print_r($_POST); exit;
		$data  				  = array();
		$dept   			  = array();
		$data['page_heading'] = "Trend Analysis";
		
		/* $curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date   = $curFiscalYear."-03-31"; //eg.2014-03-01 */
		
		$end_date   = date('Y-m-t');
		$temp_date 	= strtotime($end_date.' -1 year');
		$start_date = date('Y-m-01', $temp_date);
		
		$timesheet_db = $this->load->database("timesheet",true);

		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		if($this->input->post("graph_based")==''){
			$data['graph_based'] = 'hour';
		} else {
			$data['graph_based'] = $this->input->post("graph_based");
		}
		if($this->input->post("value_based")==''){
			$data['value_based'] = 'value';
		} else {
			$data['value_based'] = $this->input->post("value_based");
		}
		$where = '';
		// echo $this->input->post("exclude_leave"); exit;
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			$where .= " and t.project_code NOT IN ('Leave')";
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			$where .= " and t.project_code NOT IN ('HOL')";
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			$where .= " and t.project_code NOT IN ('HOL','Leave')";
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		
		$department_ids = $this->input->post("department_ids");
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			$data['department_ids'] = $department_ids;
			$where .= " and t.dept_id in ($dids)";
		} else {
			$where .= " and t.dept_id in ('10','11')";
		}
		
		$practice_ids = $this->input->post("practice_ids");
		
		//for practices
		/* $this->db->select('t.practice_id, t.practice_name');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->where("t.practice_id !=", 0);
		$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
		$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		if(count($department_ids)>0 && !empty($department_ids)) {
			$dids = implode(",",$department_ids);
			if(!empty($dids)) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
		}
		if(count($practice_ids)>0 && !empty($practice_ids)) {
			$pids = implode(",",$practice_ids);
			$data['practice_ids'] = $practice_ids;
			$where .= " and l.practice in ($pids)";
		}
		
		$this->db->group_by('t.practice_id');
		$this->db->order_by('t.practice_name');
		$query = $this->db->get();
		if($query->num_rows()>0) {
			$data['practice_ids_selected'] = $query->result();
		} */
		
		$practice_not_in_array = array('6','7','8');
		$this->db->select('id as practice_id, practices as practice_name');
		$this->db->from($this->cfg['dbpref']. 'practices');
		$this->db->where("status !=", 0);
		if(count($practice_ids)>0 && !empty($practice_ids)) {
			$pids = implode(",",$practice_ids);
			$data['practice_ids'] = $practice_ids;
			$where .= " and l.practice in ($pids)";
		}
		$this->db->where_not_in("id", $practice_not_in_array);
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$data['practice_ids_selected'] = $query->result();
		
		if($this->input->post("practice_ids")) {
			$ids = $this->input->post("department_ids");
			$dids = implode(',',$ids);
			$p_ids = $practice_ids;
			$pids = implode(',',$p_ids);

			$this->db->select('t.skill_id, t.skill_name as name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($ids))
			$this->db->where_in("t.dept_id", $ids);
			if(!empty($p_ids))
			$this->db->where_in("t.practice_id", $practice_ids);
			$this->db->group_by('t.skill_id');
			$this->db->order_by('t.skill_name');
			$query = $this->db->get();
	
			$data['skill_ids_selected'] = $query->result();
		}
		$sids = '';
		$skill_ids = $this->input->post("skill_ids");
		if(count($skill_ids)>0 && !empty($skill_ids)) {
			$sids = implode(",",$skill_ids);
			if(count($skill_ids)>0 && !empty($skill_ids)){
				$data['skill_ids'] = $skill_ids;
				$where .= " and t.skill_id in ($sids)";
			}
		}

		$member_ids = $this->input->post("member_ids");
		if(count($member_ids)>0 && !empty($member_ids)) {
			$mids = "'".implode("','",$member_ids)."'";
			if(count($member_ids)>0 && !empty($member_ids)) {
				$data['member_ids'] = $member_ids;
				$where .= " and t.username in ($mids)";
			}
			
			$ids = $this->input->post("department_ids");
			$dids = implode(',',$ids);
			$p_ids = $practice_ids;
			$pids = implode(',',$p_ids);
			
			/* $qry1 = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc"); */
			
			$this->db->select('t.username, t.empname as emp_name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($ids))
			$this->db->where_in("t.dept_id", $ids);
			if(!empty($p_ids))
			$this->db->where_in("t.practice_id", $practice_ids);
			if(!empty($skill_ids))
			$this->db->where_in("t.skill_id", $skill_ids);
			$this->db->group_by('t.username');
			$this->db->order_by('t.username');
			$qry1 = $this->db->get();
			
			$data['member_ids_selected'] = $qry1->result();
		}		
		
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;
		$json = '';
		// $where .= ' AND project_code = "ITS-IIT- 01-0414"'; //for testing load some data only
		
		$getITDataQry = "SELECT t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.project_code, t.start_time, t.end_time, t.direct_cost_per_hour, t.resource_duration_direct_cost
		FROM crm_timesheet_month_data as t
		LEFT JOIN `crm_leads` as l ON l.pjt_id = t.project_code
		WHERE t.start_time >= '$start_date' and t.start_time <= '$end_date' AND t.resoursetype != '' $where";
		
		/* $getITDataQry = "SELECT t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost, t.entry_month as month_name, t.entry_year as yr, t.entity_id, t.entity_name, t.practice_id, t.practice_name 
		FROM (crm_timesheet_month_data as t) LEFT JOIN crm_leads as l ON l.pjt_id = t.project_code 
		WHERE t.resoursetype != '' AND (t.start_time >='$start_date') AND (t.start_time <='$end_date') $where"; */
		
		// echo $getITDataQry; exit;
		$sql = $this->db->query($getITDataQry);
		$data['resdata'] = $sql->result();
		// echo '<pre>'; print_r($data['resdata']); die;
		$arr_depts          = array();
		$check_array 	    = array();
		$check_user_array   = array();
		$arr_depts1		    = array();
		$arr_user_avail_set = array();
		
		// get all departments from timesheet
		$dept = $timesheet_db->query("SELECT department_id, department_name FROM ".$timesheet_db->dbprefix('department')." where department_id IN ('10','11') ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		$timesheet_db->close();

		$data['start_date'] 	  = $start_date;
		$data['end_date']   	  = $end_date;
		$data['results']    	  = $arr_depts;
		$data['filter_area_status'] = $this->input->post("filter_area_status");
		// echo "<pre>"; print_r($data); die;
		$this->load->view("projects/trend_analysis_view", $data);
	}
	
	/*
	@method - get_data()
	@for drill down data
	*/
	public function get_data()
	{
		// echo "<pre>"; print_r($this->input->post()); exit;
		$start_date = date("Y-m-1");
		$end_date   = date("Y-m-d");
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		
		$resource_type  = $this->input->post("resource_type");
		$department_ids = $this->input->post("department_ids");
		$practice_ids   = $this->input->post("practice_ids");
		$dept_type      = $this->input->post("dept_type");
		$skill_ids 		= $this->input->post("skill_ids");
		$member_ids		= $this->input->post("member_ids");
		
		
		/* $qry = "SELECT t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.project_code, t.empname
		FROM crm_timesheet_data t
		WHERE start_time between '$start_date' and '$end_date' $where"; */
		
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
		$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		if(!empty($resource_type))
		$this->db->where('t.resoursetype', $resource_type);
		if(!empty($department_ids))
		$this->db->where_in("t.dept_id", $department_ids);
		if(!empty($skill_ids)){
			$skill = @explode(',',$skill_ids);
			$this->db->where_in("t.skill_id", $skill);
		}
		if(empty($department_ids) && !empty($dept_type)) {
			switch($dept_type) {
				case 1:
				$type = array(10, 11);
				$this->db->where_in("t.dept_id", $type);
				break;
				case 2:
				$type = array(10);
				$this->db->where_in("t.dept_id", $type);
				break;
				case 3:
				$type = array(11);
				$this->db->where_in("t.dept_id", $type);
				break;
			}
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			// $where .= " and project_code NOT IN ('Leave')";
			$this->db->where_not_in("t.project_code", 'Leave');
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			// $where .= " and project_code NOT IN ('HOL')";
			$this->db->where_not_in("t.project_code", 'HOL');
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			// $where .= " and project_code NOT IN ('HOL','Leave')";
			$this->db->where_not_in("t.project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		// if(!empty($practice_ids) && !empty($department_ids)) {
		if(!empty($practice_ids)) {
			$pids = explode(',', $practice_ids);
			$this->db->where_in("t.practice_id", $pids);
		}
		switch($dept_type) {
			case 1:
			$heading = 'IT - '.$resource_type;
			break;
			case 2:
			$heading = 'eADS - '.$resource_type;
			break;
			case 3:
			$heading = 'eQAD - '.$resource_type;
			break;
		}
		if(!empty($skill_ids) && !empty($department_ids) && !empty($member_ids)) {
			/* $pre_mids = implode(",",$member_ids);
			if(!empty($pre_mids)) {
				$mids = "'".implode("','",$member_ids)."'";
				if(!empty($mids)) {
					$where .= " and t.username in ($mids)";
				}
			} */
			$mids = explode(',', $member_ids);
			$this->db->where_in("t.username", $mids);
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		
		$data['resdata'] 	   = $query->result();
		$data['heading'] 	   = $heading;
		$data['dept_type']     = $dept_type;
		$data['resource_type'] = $resource_type;
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		$timesheet_db->close();
		
		$filter_group_by = $this->input->post("filter_group_by");
		$filter_sort_by  = $this->input->post("filter_sort_by");
		$filter_sort_val = $this->input->post("filter_sort_val");
		
		$data['filter_group_by'] = $this->input->post("filter_group_by");
		if(isset($filter_sort_by) && !empty($filter_sort_by))
		$data['filter_sort_by'] = $this->input->post("filter_sort_by");
		else
		$data['filter_sort_by'] = 'desc';
	
		if(isset($filter_sort_val) && !empty($filter_sort_val))
		$data['filter_sort_val'] = $this->input->post("filter_sort_val");
		else
		$data['filter_sort_val'] = 'hour';
	
		switch($this->input->post("filter_group_by")){
			case 0:
				$this->load->view('projects/practice_drilldata', $data);
			break;
			case 1:
				$this->load->view('projects/skill_drilldata', $data);
			break;
			case 2:
				$this->load->view('projects/prjt_drilldata', $data);
			break;
			case 3:
				$this->load->view('projects/resource_drilldata', $data);
			break;
		}
	}
	
	/*
	@method - get_data_beta()
	@for drill down data
	*/
	public function get_data_beta()
	{
		// echo "<pre>"; print_r($this->input->post()); exit;
		$start_date = date("Y-m-1");
		$end_date   = date("Y-m-d");
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		
		$resource_type  = $this->input->post("resource_type");
		$department_ids = $this->input->post("department_ids");
		$practice_ids   = $this->input->post("practice_ids");
		$dept_type      = $this->input->post("dept_type");
		$skill_ids 		= $this->input->post("skill_ids");
		$member_ids		= $this->input->post("member_ids");
		$entity_ids 	= $this->input->post("entity_ids");
		
		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost, t.entry_month as month_name, t.entry_year as yr, t.entity_id, t.entity_name, t.practice_id, t.practice_name, p.practices');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref']. 'leads as l', 'l.pjt_id = t.project_code', 'LEFT');
		$this->db->join($this->cfg['dbpref']. 'practices as p', 'p.id = l.practice');
		// $this->db->where("t.project_code", 'ITS-IIT- 01-0414'); //for testing load some data only
		$this->db->where("t.resoursetype !=", '');
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		}
		if(!empty($resource_type))
		$this->db->where('t.resoursetype', $resource_type);
		if(!empty($department_ids))
		$this->db->where_in("t.dept_id", $department_ids);
		if(!empty($skill_ids)){
			$skill = @explode(',',$skill_ids);
			$this->db->where_in("t.skill_id", $skill);
		}
		if(empty($department_ids) && !empty($dept_type)) {
			switch($dept_type) {
				case 1:
				$type = array(10, 11);
				$this->db->where_in("t.dept_id", $type);
				break;
				case 2:
				$type = array(10);
				$this->db->where_in("t.dept_id", $type);
				break;
				case 3:
				$type = array(11);
				$this->db->where_in("t.dept_id", $type);
				break;
			}
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			// $where .= " and project_code NOT IN ('Leave')";
			$this->db->where_not_in("t.project_code", 'Leave');
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			// $where .= " and project_code NOT IN ('HOL')";
			$this->db->where_not_in("t.project_code", 'HOL');
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			// $where .= " and project_code NOT IN ('HOL','Leave')";
			$this->db->where_not_in("t.project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		// if(!empty($practice_ids) && !empty($department_ids)) {
		if(!empty($practice_ids)) {
			$pids = explode(',', $practice_ids);
			// $this->db->where_in("t.practice_id", $pids);
			$this->db->where_in("l.practice", $pids);
		}
		if(!empty($entity_ids)) {
			$entys = explode(',', $entity_ids);
			$this->db->where_in('t.entity_id', $entys);
		}		
		switch($dept_type) {
			case 1:
			$heading = 'IT - '.$resource_type;
			break;
			case 2:
			$heading = 'eADS - '.$resource_type;
			break;
			case 3:
			$heading = 'eQAD - '.$resource_type;
			break;
		}
		if(!empty($skill_ids) && !empty($department_ids) && !empty($member_ids)) {
			$mids = explode(',', $member_ids);
			$this->db->where_in("t.username", $mids);
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		
		$data['resdata'] 	   		= $query->result();
		$data['heading'] 	   		= $heading;
		$data['dept_type']     		= $dept_type;
		$data['resource_type'] 		= $resource_type;
		$data['conversion_rates'] 	= $this->get_currency_rates();
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		$timesheet_db->close();
		
		$filter_group_by = $this->input->post("filter_group_by");
		$filter_sort_by  = $this->input->post("filter_sort_by");
		$filter_sort_val = $this->input->post("filter_sort_val");
		
		$data['filter_group_by'] = $this->input->post("filter_group_by");
		if(isset($filter_sort_by) && !empty($filter_sort_by))
		$data['filter_sort_by'] = $this->input->post("filter_sort_by");
		else
		$data['filter_sort_by'] = 'desc';
	
		if(isset($filter_sort_val) && !empty($filter_sort_val)) {
			$data['filter_sort_val'] = $this->input->post("filter_sort_val");
		} else {
			$data['filter_sort_val'] = 'hour';
		}
	
		switch($this->input->post("filter_group_by")) {
			case 0:
				$this->load->view('projects/practice_drilldata_beta', $data);
			break;
			case 1:
				$this->load->view('projects/skill_drilldata_beta', $data);
			break;
			case 2:
				$this->load->view('projects/prjt_drilldata_beta', $data);
			break;
			case 3:
				$this->load->view('projects/resource_drilldata_beta', $data);
			break;
			case 4:
				$this->load->view('projects/entity_drilldata_beta', $data);
			break;
		}
	}
	
	/*
	@method - get_data()
	@for drill down data
	*/
	public function get_trend_drill_data()
	{
		// echo "<pre>"; print_r($this->input->post()); exit;
		if($this->input->post("start_date")) {
			$date = $this->input->post("start_date");
			$start_date = date("Y-m-01",strtotime($date));
			$end_date   = date("Y-m-t",strtotime($date));
		} else {
			$start_date = date("Y-m-1");
			$end_date   = date("Y-m-d");
		}
		// echo $start_date .'='. $end_date; exit;
		$resource_type  = $this->input->post("resource_type");
		$department_ids = $this->input->post("department_ids");
		$practice_ids   = $this->input->post("practice_ids");
		$dept_type      = $this->input->post("dept_type");
		$skill_ids 		= $this->input->post("skill_ids");
		$member_ids		= $this->input->post("member_ids");
		$data['hmonth_year'] = $this->input->post("month_year_from_date");
		
		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost, t.entry_month as month_name, t.entry_year as yr, t.entity_id, t.entity_name, t.practice_id, t.practice_name, p.practices');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref']. 'leads as l', 'l.pjt_id = t.project_code', 'LEFT');
		$this->db->join($this->cfg['dbpref']. 'practices as p', 'p.id = l.practice');
		$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
		$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		// $this->db->where("(t.project_code ='ITS-IIT- 01-0414')", NULL, FALSE); //for testing load some data only
		if(!empty($resource_type))
		$this->db->where('t.resoursetype', $resource_type);
		if(!empty($department_ids)) {
			$dids = @explode(",",$department_ids);
			$this->db->where_in("t.dept_id", $dids);
		}
		if(!empty($practice_ids)) {
			$pids = explode(',', $practice_ids);
			$this->db->where_in("l.practice", $pids);
		}
		if(!empty($skill_ids)) {
			$sids = @explode(",",$skill_ids);
			$this->db->where_in("t.skill_id", $sids);
		}
		if(empty($department_ids) && !empty($dept_type)) {
			switch($dept_type) {
				case 1:
				$type = array(10, 11);
				$this->db->where_in("t.dept_id", $type);
				break;
				case 2:
				$type = array(10);
				$this->db->where_in("t.dept_id", $type);
				break;
				case 3:
				$type = array(11);
				$this->db->where_in("t.dept_id", $type);
				break;
			}
		}
		if(!empty($member_ids)) {
			$mids = @explode(',', $member_ids);
			$this->db->where_in("t.username", $mids);
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			// $where .= " and project_code NOT IN ('Leave')";
			$this->db->where_not_in("t.project_code", 'Leave');
			$data['exclude_leave'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			// $where .= " and project_code NOT IN ('HOL')";
			$this->db->where_not_in("t.project_code", 'HOL');
			$data['exclude_holiday'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			// $where .= " and project_code NOT IN ('HOL','Leave')";
			$this->db->where_not_in("t.project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		
		switch($dept_type) {
			case 1:
			$heading = 'IT - '.$resource_type;
			break;
			case 2:
			$heading = 'eADS - '.$resource_type;
			break;
			case 3:
			$heading = 'eQAD - '.$resource_type;
			break;
		}
		
		$data['resdata'] 	   		= $query->result();
		// echo '<pre>'; print_R($data['resdata']); die;
		$data['heading'] 	   		= $heading;
		$data['dept_type']     		= $dept_type;
		$data['resource_type'] 		= $resource_type;
		$data['conversion_rates'] 	= $this->get_currency_rates();
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		$timesheet_db->close();
		
		$filter_group_by = $this->input->post("filter_group_by");
		$filter_sort_by  = $this->input->post("filter_sort_by");
		$filter_sort_val = $this->input->post("filter_sort_val");
		
		$data['filter_group_by'] = $this->input->post("filter_group_by");
		
		if(isset($filter_sort_by) && !empty($filter_sort_by))
		$data['filter_sort_by'] = $this->input->post("filter_sort_by");
		else
		$data['filter_sort_by'] = 'desc';
	
		if(isset($filter_sort_val) && !empty($filter_sort_val))
		$data['filter_sort_val'] = $this->input->post("filter_sort_val");
		else
		$data['filter_sort_val'] = 'hour';
	
		$data['start_date'] = $this->input->post("start_date");
	
		switch($this->input->post("filter_group_by")){
			case 0:
				$this->load->view('projects/practice_trend_drilldata', $data);
			break;
			case 1:
				$this->load->view('projects/skill_trend_drilldata', $data);
			break;
			case 2:
				$this->load->view('projects/prjt_trend_drilldata', $data);
			break;
			case 3:
				$this->load->view('projects/resource_trend_drilldata', $data);
			break;
		}
	}
	
	public function updt_crm_timesheet_data()
	{
		$timesheet_db = $this->load->database("timesheet",true);
		$dept      = $timesheet_db->query("SELECT department_id,department_name FROM ".$timesheet_db->dbprefix('department')." ");
		$depts_res = $dept->result();
		$dept_arr  = array();
		foreach($depts_res as $row_arr){
			$dept_arr[$row_arr->department_id] = $row_arr->department_name;
		}
		
		$skills     = $timesheet_db->query("SELECT skill_id, name FROM ".$timesheet_db->dbprefix('skills')." ");
		$skills_res = $skills->result();
		$skill_arr  = array();
		foreach($skills_res as $rows_arr){
			$skill_arr[$rows_arr->skill_id] = $rows_arr->name;
		}
		
		$practices     = $timesheet_db->query("SELECT practice_id,practice_name,department_id,skill_id FROM ".$timesheet_db->dbprefix('practice')." ");
		$practices_res = $practices->result();
		$practice_arr  = array();
		
		foreach($practices_res as $rec_arr){
			if($rec_arr->skill_id){
				$skk = explode(",",$rec_arr->skill_id);
				foreach($skk as $pr){
					$practice_arr[$pr]['pid']   = $rec_arr->practice_id;
					$practice_arr[$pr]['pname'] = $rec_arr->practice_name;
				}
			}
		}
		// echo "<pre>"; print_r($practice_arr); exit;
		$users     = $timesheet_db->query("SELECT username,department_id,skill_id FROM ".$timesheet_db->dbprefix('user')." where 1");
		$user_data = $users->result();
		
		// echo "<pre>"; print_r($user_data); exit;
		if(!empty($user_data)){
			foreach($user_data as $user_row){
				$updata_data = array();
				$updata_data['dept_id']       = $user_row->department_id;
				$updata_data['dept_name']     = isset($dept_arr[$user_row->department_id]) ? $dept_arr[$user_row->department_id] : '';
				$updata_data['practice_id']   = isset($practice_arr[$user_row->skill_id]['pid']) ? $practice_arr[$user_row->skill_id]['pid'] : 0;
				$updata_data['practice_name'] = isset($practice_arr[$user_row->skill_id]['pname']) ? $practice_arr[$user_row->skill_id]['pname'] : 0;
				$updata_data['skill_id']      = $user_row->skill_id;
				$updata_data['skill_name']    = isset($skill_arr[$user_row->skill_id]) ? $skill_arr[$user_row->skill_id] : 0;
				// echo "<pre>"; print_r($updata_data); exit;
				$wh_condn = array('username'=>$user_row->username);
				$this->db->update($this->cfg['dbpref'].'timesheet_data', $updata_data, $wh_condn);
				//echo $this->db->last_query();
				//echo "<br>";
			}
		}
	}
	
	function get_practices()
	{
		// echo "<pre>"; print_R($this->input->post()); exit;
		$practice_not_in_array = array('6','7','8');
		
		$eads_arr = array(1,3,10,12,13,14,15);
		$eqad_arr = array(3,5);
		// echo "projects"; exit;
		if($this->input->post("dept_ids")) {
			$dept_arr = $this->input->post("dept_ids");
			
			$this->db->select('id as practice_id, practices as practice_name');
			$this->db->from($this->cfg['dbpref']. 'practices');
			$this->db->where("status !=", 0);
			$this->db->where_not_in("id", $practice_not_in_array);
			if(!empty($dept_arr) && count($dept_arr)!=2) {
				if(in_array(10, $dept_arr)) {
					$this->db->where_in("id", $eads_arr);
				}
				if(in_array(11, $dept_arr)) {
					$this->db->where_in("id", $eqad_arr);
				}
			}
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			// $data['resdata'] =  $query->result();
			if($query->num_rows()>0){
				$res = $query->result();
				echo json_encode($res); exit;
			}else{
				echo 0;
				exit;
			}
		}
	}
	
	function get_skills()
	{
		// echo "projects"; exit;
		if($this->input->post("dept_ids")){
			$ids 		= $this->input->post("dept_ids");
			$start_date = $this->input->post("start_date");
			$end_date 	= $this->input->post("end_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			$end_date   = date("Y-m-t",strtotime($end_date));
			$dids = implode(',',$ids);

			$timesheet_db = $this->load->database("timesheet",true);
			$qry = $timesheet_db->query("SELECT v.skill_id,v.name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and t.start_time between '$start_date' and '$end_date' group by
			v.skill_id order by v.name asc");
			
			if($qry->num_rows()>0){
				$res = $qry->result();
				echo json_encode($res); exit;
			}else{
				echo 0;exit;
			}
		}
	}
	
	function get_skills_by_practice()
	{
		// echo "<pre>"; print_R($this->input->post()); exit;
		if($this->input->post("prac_id")){
			$ids = '';
			if($this->input->post("dept_ids")) {
				$ids = $this->input->post("dept_ids");
				$dids = implode(',',$ids);
			}
			$p_ids = $this->input->post("prac_id");
			$pids = implode(',',$p_ids);
			$start_date = $this->input->post("start_date");
			$end_date = $this->input->post("end_date");

			$this->db->select('t.skill_id, t.skill_name as name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			// $this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			// $this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($ids))
			$this->db->where_in("t.dept_id", $ids);
			if(!empty($p_ids))
			$this->db->where_in("t.practice_id", $p_ids);
			$this->db->group_by('t.skill_id');
			$this->db->order_by('t.skill_name');
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			if($query->num_rows()>0){
				$res = $query->result();
				echo json_encode($res); exit;
			}else{
				echo 0;
				exit;
			}
		}
	}
	
	
	function get_projects_by_condition()
	{
		$varSessionId = $this->userdata['userid'];
		$ids = $this->input->post("dept_ids");
		$p_ids = $this->input->post("prac_id");
		$entity_ids=$this->input->post("entity_ids");

		$this->db->select('t.project_code, p.lead_title as project_name');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as p', 'p.pjt_id = t.project_code');
		$this->db->where("t.practice_id !=", 0);			
		if(!empty($ids))
		$this->db->where_in("t.dept_id", $ids);
		if(!empty($p_ids))
		$this->db->where_in("t.practice_id", $p_ids);
		if(!empty($entity_ids))
		$this->db->where_in("p.division", $entity_ids);
		//Checking Admin,Management
		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '4')) 
		{
		   //No restriction
		}
		else
		{
			// $this->db->where("(p.assigned_to = '".$varSessionId."' OR p.lead_assign = '".$varSessionId."' OR p.belong_to = '".$varSessionId."')");
			$wh_condn = ' (p.belong_to = '.$varSessionId.' OR p.assigned_to ='.$varSessionId.' OR FIND_IN_SET('.$varSessionId.', p.lead_assign)) ';
			$this->db->where($wh_condn);
		}
		$this->db->where("p.lead_status", 4);
	
		$this->db->group_by('t.project_code');
		$this->db->order_by('project_name');
		$query = $this->db->get();
		 //echo $this->db->last_query(); exit;
		if($query->num_rows()>0){
			$res = $query->result();
			echo json_encode($res); exit;
		}else{
			echo 0;
			exit;
		}
		
	}
	
	function get_members()
	{
		if($this->input->post("dept_ids")){
			$ids = $this->input->post("dept_ids");
			$start_date = $this->input->post("start_date");
			$end_date   = $this->input->post("end_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			$end_date   = date("Y-m-t",strtotime($end_date));
			
			$dids = implode(',',$ids);
			$timesheet_db = $this->load->database("timesheet",true);
			$qry = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc");
			if($qry->num_rows()>0){
				$res = $qry->result();
				echo json_encode($res); exit;
			}else{
				echo 0;exit;
			}
		}		
	}
	
	function get_practice_members() 
	{
		if($this->input->post("dept_ids")){
			$ids = $this->input->post("dept_ids");
			if($this->input->post("prac_id")) {
				$p_ids = $this->input->post("prac_id");
				$pids  = implode(',',$p_ids);
			}
			$start_date = $this->input->post("start_date");
			$end_date   = $this->input->post("end_date");

			$this->db->select('t.username, t.empname as emp_name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($ids))
			$this->db->where_in("t.dept_id", $ids);
			if(!empty($p_ids))
			$this->db->where_in("t.practice_id", $p_ids);
			$this->db->group_by('t.username');
			$this->db->order_by('t.username');
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			if($query->num_rows()>0) {
				$res = $query->result();
				echo json_encode($res); 
				exit;
			} else {
				echo 0;
				exit;
			}
		}		
	}
	
	function get_skill_members()
	{
		if($this->input->post("skill_ids")){
			$where = '';
			$ids 		= $this->input->post("dept_ids");
			$skill_ids  = $this->input->post("skill_ids");
			$start_date = $this->input->post("start_date");
			$end_date   = $this->input->post("end_date");
			
			$start_date = date("Y-m-01",strtotime($start_date));
			$end_date   = date("Y-m-t",strtotime($end_date));
			
			$dids = implode(',',$ids);
			$sids = implode(',',$skill_ids);
			$where .= 'and status = "ACTIVE" ';
			if(!empty($dids)) {
				$where .= 'and v.department_id in ('.$dids.')';
			}
			if(!empty($sids)) {
				$where .= 'and v.skill_id in ('.$sids.')';
			}
			
			if(!empty($sids)) {
				$this->db->select("t.empname as emp_name, t.username");
				$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
				$this->db->where("t.practice_id !=", 0);
				$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
				$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
				if(!empty($ids)) {
					$this->db->where_in("t.dept_id", $ids);
				}				
				if(!empty($p_ids)){
					$this->db->where_in("t.practice_id", $p_ids);
				}
				if(!empty($sids)) {
					$this->db->where_in("t.skill_id", $sids);
				}
				$this->db->group_by('t.empname');
				$this->db->order_by('t.empname');
				$qry = $this->db->get();
				if($qry->num_rows()>0){
					$res = $qry->result();
					echo json_encode($res); exit;
				}else{
					echo 0;exit;
				}
			} else {
				echo 0;exit;
			}
		}
	}
	
	/*
	*@Get Current Financial year
	*@Method  calculateFiscalYearForDate
	*/
	function calculateFiscalYearForDate($inputDate, $fyStart, $fyEnd) 
	{
		$date = strtotime($inputDate);
		$inputyear = strftime('%Y',$date);
	 
		$fystartdate = strtotime($fyStart.'/'.$inputyear);
		$fyenddate = strtotime($fyEnd.'/'.$inputyear);
	 
		if($date <= $fyenddate){
			$fy = intval($inputyear);
		}else{
			$fy = intval(intval($inputyear) + 1);
		}
	
		return $fy;
	}
	
	public function service_dashboard_old()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$data['page_heading'] = "IT Services Dashboard";
		
		$bk_rates = get_book_keeping_rates();
		
		// echo "<pre>"; print_R($this->input->post());
		
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $end_date  	   = $curFiscalYear."-".date('m-d'); //eg.2014-03-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t", strtotime($end_date));
			$month    = date("Y-m-01 00:00:00", strtotime($end_date));
		}
		if($this->input->post("billable_month")) {
			$bill_month = $this->input->post("billable_month");
			$month      = date("Y-m-01 00:00:00", strtotime($bill_month));
		}
		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;

		$project_status = 1;
		if($this->input->post("project_status") && ($this->input->post("project_status")!='null')) {
			$project_status = @explode(',', $this->input->post("project_status"));
			if(count($project_status) == 2){
				$project_status = '';
			} else {
				$project_status = $this->input->post("project_status");
			}
		}
		$division = '';
		if($this->input->post("entity") && ($this->input->post("entity")!='null')) {
			$division = @explode(',', $this->input->post("entity"));
		}
		
		$project_code = array();
		$projects 	  = array();
		$practice_arr = array();

		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in('p.id', 6);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();		
		
		$this->db->select('div_id, division_name, base_currency');
		$this->db->from($this->cfg['dbpref']. 'sales_divisions');
		$equery = $this->db->get();
		$eres = $equery->result();
		$data['entity_data'] = $equery->result();
		
		if(!empty($eres) && count($eres)>0){
			foreach($eres as $erow) {
				$base_cur_arr[$erow->div_id] = $erow->base_currency;
			}
		}
		
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
			}
		}
		
		$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		$this->db->where("l.customer_type", '1');
		// $pt_not_in_arr = array('4','8');
		// $this->db->where("l.project_type", 1);
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in("l.practice", 6);
		// $this->db->where("DATE(l.date_start) >= ", $start_date);
		// $this->db->where("DATE(l.date_due) <= ", $end_date);
		if($project_status){
			if($project_status !=2)
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}
		
		/* if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
			$this->db->where_in('l.lead_id', $result_ids);
		} */
		$query = $this->db->get();
		$res = $query->result_array();
		
		// echo "<pre>"; print_r($res); die;
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
				// $projects['project'][$practice_arr[$row['practice']]][] = $row['pjt_id'];
				$timesheet = array();
				
				if (isset($projects['practicewise'][$practice_arr[$row['practice']]])) {
					$projects['practicewise'][$practice_arr[$row['practice']]] += 1;
				} else {
					$projects['practicewise'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
				}
				if($row['rag_status'] == 1){
					if (isset($projects['rag_status'][$practice_arr[$row['practice']]])) {
						$projects['rag_status'][$practice_arr[$row['practice']]] += 1;
					} else {
						$projects['rag_status'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
					}
				}
			}
		}
		
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, c.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in("l.practice", 6);
		if(!empty($start_date)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("sfv.for_month_year <= ", date('Y-m-d H:i:s', strtotime($end_date)));
		}
		
		$query1 = $this->db->get();
		$invoices_data = $query1->result_array();

		if(!empty($invoices_data) && count($invoices_data)>0) {
			foreach($invoices_data as $ir) {
				$base_conver_amt = $this->conver_currency($ir['milestone_value'],$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				$projects['irval'][$practice_arr[$ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
			}
		}
		
		//for current month ir
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, c.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in("l.practice", 6);
		if(!empty($month)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($month)));
			$this->db->where("sfv.for_month_year <= ", date('Y-m-t H:i:s', strtotime($month)));
		}
		
		$query5 = $this->db->get();
		$cm_invoices_data = $query5->result_array();

		if(!empty($cm_invoices_data) && count($cm_invoices_data)>0) {
			foreach($cm_invoices_data as $cm_ir) {
				$base_conver_amt = $this->conver_currency($cm_ir['milestone_value'],$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($cm_ir['for_month_year'])),"4/1","3/31")][$cm_ir['expect_worth_id']][$cm_ir['base_currency']]);
				$projects['cm_irval'][$practice_arr[$cm_ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($cm_ir['for_month_year'])),"4/1","3/31")][$cm_ir['base_currency']][$this->default_cur_id]);
			}
		}
		
		//for current month EFFORTS
		$projects['billable_month'] = $this->get_timesheet_data($practice_arr, "", "", $month);
		$projects['billable_ytd']   = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
		
		//for effort variance
		$pcodes = $projects['billable_ytd']['project_code'];
		
		if(!empty($pcodes) && count($pcodes)>0){
			foreach($pcodes as $rec){
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_arra = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_arra = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_arra);
				$this->db->where("l.pjt_id", $rec);
				//BPO practice are not shown in IT Services Dashboard
				$this->db->where_not_in("l.practice", 6);
				// $this->db->where("l.billing_type", 1);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				if(!empty($pro_data) && count($pro_data)>0){
					foreach($pro_data as $recrd){
						if(isset($effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'])){
							$effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'] += $recrd['estimate_hour'];
							$actuals = $this->get_timesheet_actual_hours($recrd['pjt_id'], "", "");
							$effvar[$practice_arr[$recrd['practice']]]['total_actual_hrs'] += $actuals['total_hours'];
						} else {
							$effvar[$practice_arr[$recrd['practice']]]['tot_estimate_hrs'] = $recrd['estimate_hour'];
							$actuals = $this->get_timesheet_actual_hours($recrd['pjt_id'], "", "");
							$effvar[$practice_arr[$recrd['practice']]]['total_actual_hrs'] = $actuals['total_hours'];
						}
						$fixed_bid[$practice_arr[$recrd['practice']]][$recrd['pjt_id']] = $recrd['lead_title'];
					}
				}
			}
		}
		$projects['eff_var']   = $effvar;

		$contribution_query = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code, direct_cost_per_hour, resource_duration_direct_cost
		FROM crm_timesheet_data 
		WHERE start_time between '".$start_date."' and '".$end_date."' AND resoursetype != '' AND project_code NOT IN ('HOL','Leave')";
		
		$sql1 = $this->db->query($contribution_query);
		$contribution_data = $sql1->result();
		if(!empty($contribution_data)) {
			foreach($contribution_data as $cdrow){
				$directcost[$practice_arr[$cdrow->practice_id]]['total_direct_cost'] = $directcost[$practice_arr[$cdrow->practice_id]]['total_direct_cost'] + $cdrow->resource_duration_direct_cost;
			}
		}
		// echo "<pre>"; print_r($directcost); die;
		$projects['direct_cost']   = $directcost;
		
		$month_contribution_query = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code, direct_cost_per_hour, resource_duration_direct_cost
		FROM crm_timesheet_data 
		WHERE start_time between '".date('Y-m-d', strtotime($month))."' and '".date('Y-m-t', strtotime($month))."' AND resoursetype != '' AND project_code NOT IN ('HOL','Leave')";
		
		$sql2 = $this->db->query($month_contribution_query);
		$month_contribution_data = $sql2->result();
		if(!empty($month_contribution_data)) {
			foreach($month_contribution_data as $mcdrow) {
				$cm_directcost[$practice_arr[$mcdrow->practice_id]]['total_cm_direct_cost'] = $cm_directcost[$practice_arr[$mcdrow->practice_id]]['total_cm_direct_cost'] + $mcdrow->resource_duration_direct_cost;
			}
		}
		// echo "<pre>"; print_r($cm_directcost); die;
		$projects['cm_direct_cost'] = $cm_directcost;
		$data['projects'] = $projects;
		// echo "<pre>"; print_r($projects); exit;
		
		if($this->input->post("filter")!="")
		$this->load->view('projects/service_dashboard_grid', $data);
		else
		$this->load->view('projects/service_dashboard', $data);
	}
	
	
	public function service_dashboard()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$data['page_heading'] = "IT Services Dashboard";
		
		$bk_rates = get_book_keeping_rates();
		
		// echo "<pre>"; print_R($this->input->post());
		
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $end_date  	   = $curFiscalYear."-".date('m-d'); //eg.2014-03-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t", strtotime($end_date));
			$month    = date("Y-m-01 00:00:00", strtotime($end_date));
		}
		if($this->input->post("billable_month")) {
			$bill_month = $this->input->post("billable_month");
			$month      = date("Y-m-01 00:00:00", strtotime($bill_month));
		}
		 
		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;

		$project_status = 1;
		if($this->input->post("project_status") && ($this->input->post("project_status")!='null')) {
			$project_status = @explode(',', $this->input->post("project_status"));
			if(count($project_status) == 2){
				$project_status = '';
			} else {
				$project_status = $this->input->post("project_status");
			}
		}
		$division = '';
		if($this->input->post("entity") && ($this->input->post("entity")!='null')) {
			$division = @explode(',', $this->input->post("entity"));
		}
		
		$project_code = array();
		$projects 	  = array();
		$practice_arr = array();

		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		// $this->db->where_not_in('p.id', 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('p.id', $practice_not_in);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();		
		
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
			}
		}
		
		$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		$this->db->where("l.customer_type", '1');
		// $pt_not_in_arr = array('4','8');
		// $this->db->where("l.project_type", 1);
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		//BPO practice are not shown in IT Services Dashboard
		// $this->db->where_not_in("l.practice", 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('l.practice', $practice_not_in);
		// $this->db->where("DATE(l.date_start) >= ", $start_date);
		// $this->db->where("DATE(l.date_due) <= ", $end_date);
		if($project_status){
			if($project_status !=2)
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}

		$query = $this->db->get();
		$res = $query->result_array();
		
		//get values from services dashboard table
		$this->db->select('practice_name, billing_month, ytd_billing, ytd_utilization_cost, billable_month, ytd_billable, effort_variance, contribution_month, ytd_contribution');
		$this->db->from($this->cfg['dbpref']. 'services_dashboard');
		$sql = $this->db->get();
		$dashboard_details = $sql->result_array();
		
		$dashboard_det = array();
		if(!empty($dashboard_details)){
			foreach($dashboard_details as $key=>$val) {
				$dashboard_det[$val['practice_name']] = $val;
			}
		}
		$data['dashboard_det'] = $dashboard_det;
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
				if (isset($projects['practicewise'][$practice_arr[$row['practice']]])) {
					$projects['practicewise'][$practice_arr[$row['practice']]] += 1;
				} else {
					$projects['practicewise'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
				}
				if($row['rag_status'] == 1){
					if (isset($projects['rag_status'][$practice_arr[$row['practice']]])) {
						$projects['rag_status'][$practice_arr[$row['practice']]] += 1;
					} else {
						$projects['rag_status'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
					}
				}				
			}
		}
		
		$data['projects'] = $projects;
		
		if($this->input->post("filter")!="")
		$this->load->view('projects/service_dashboard_grid', $data);
		else
		$this->load->view('projects/service_dashboard', $data);
	}

	public function service_dashboard_beta()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$data['page_heading'] = "IT Services Dashboard";
		
		$bk_rates = get_book_keeping_rates();
		
		// echo "<pre>"; print_R($this->input->post()); exit;
		
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		
		$current_month 		= date('m');
		$fiscalStartMonth 	= '04';
		
		$month_status = $this->input->post("month_status");
		
		if(!empty($month_status)) {
			if($month_status == 2) {
				$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
				if($fiscalStartMonth == $current_month) {
					$curFiscalYearTemp 	= calculateFiscalYearForDateHelper(date("m/d/y"),"4/1","3/31"); 
					$last_fiscal_year 	= ($curFiscalYearTemp-1);
					$curFiscalYear 		= $last_fiscal_year;
					$start_date    		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
					$end_date    		= ($curFiscalYear)."-03-31";  //eg.2013-04-01
					
					/* $curFiscalYear 	= getLastFiscalYear();
					$start_date		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
					$end_date   	= ($curFiscalYear)."-03-31";  //eg.2013-04-01 */
				} else {
					$end_date = date('Y-m-d', strtotime('-1 month', $base_mon));
				}
				$month 	  = date('Y-m-01 00:00:00', strtotime('-1 month', $base_mon));
			} else {
				$end_date  	= date('Y-m-d');
				$month    	= date("Y-m-01 00:00:00");
			}
		} else {
			$month_status = 1;
		}

		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;
		
		$project_status = 1;
		if($this->input->post("project_status") && ($this->input->post("project_status")!='null')) {
			$project_status = @explode(',', $this->input->post("project_status"));
			if(count($project_status) == 2){
				$project_status = '';
			} else {
				$project_status = $this->input->post("project_status");
			}
		}
		$division = '';
		if($this->input->post("entity") && ($this->input->post("entity")!='null')) {
			$division = @explode(',', $this->input->post("entity"));
		}
		
		$project_code = array();
		$projects 	  = array();
		$practice_arr = array();

		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		// $this->db->where_not_in('p.id', 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('p.id', $practice_not_in);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();		
		
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
			}
		}
		
		$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		$this->db->where("l.customer_type", '1');
		// $pt_not_in_arr = array('4','8');
		// $this->db->where("l.project_type", 1);
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		//BPO practice are not shown in IT Services Dashboard
		// $this->db->where_not_in("l.practice", 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('l.practice', $practice_not_in);
		// $this->db->where("DATE(l.date_start) >= ", $start_date);
		// $this->db->where("DATE(l.date_due) <= ", $end_date);
		if($project_status){
			if($project_status !=2)
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}

		$query = $this->db->get();
		$res = $query->result_array();

		//get values from services dashboard table
		$this->db->select('practice_name, billing_month, ytd_billing, ytd_utilization_cost, billable_month, ytd_billable, effort_variance, contribution_month, ytd_contribution');
		$this->db->from($this->cfg['dbpref']. 'services_dashboard_beta');
		$this->db->where("month_status",$month_status);
		$sql = $this->db->get();
		$dashboard_details = $sql->result_array();
		//	echo '<pre>';print_r($dashboard_details);exit;
		$dashboard_det = array();
		if(!empty($dashboard_details)){
			foreach($dashboard_details as $key=>$val) {
				$dashboard_det[$val['practice_name']] = $val;
			}
		}
		$data['dashboard_det'] = $dashboard_det;
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
				if (isset($projects['practicewise'][$practice_arr[$row['practice']]])) {
					$projects['practicewise'][$practice_arr[$row['practice']]] += 1;
				} else {
					$projects['practicewise'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
				}
				if($row['rag_status'] == 1) {
					if (isset($projects['rag_status'][$practice_arr[$row['practice']]])) {
						$projects['rag_status'][$practice_arr[$row['practice']]] += 1;
					} else {
						$projects['rag_status'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
					}
				}				
			}
		}
		
		$data['projects'] = $projects;
		$data['month_status'] = $month_status;
		
		if($this->input->post("filter")!="")
		$this->load->view('projects/service_dashboard_grid_beta', $data);
		else
		$this->load->view('projects/service_dashboard_beta', $data);
	}
	
	public function get_timesheet_actual_hours($pjt_code, $start_date=false, $end_date=false, $month=false)
	{
		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, SUM(ts.duration_hours) as duration_hours, ts.resoursetype, ts.username, ts.empname, ts.direct_cost_per_hour as direct_cost, sum( ts.`resource_duration_direct_cost`) as duration_direct_cost, sum( ts.`resource_duration_cost`) as duration_cost');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_data as ts');
		$this->db->where("ts.project_code", $pjt_code);
		if( (!empty($start_date)) && (!empty($end_date)) ){
			$this->db->where("DATE(ts.start_time) >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("DATE(ts.start_time) <= ", date('Y-m-d', strtotime($end_date)));
		}
		if(!empty($month)) {
			$this->db->where("DATE(ts.start_time) >= ", date('Y-m-d', strtotime($month)));
			$this->db->where("DATE(ts.end_time) <= ", date('Y-m-t', strtotime($month)));
		}
		$this->db->group_by(array("ts.username", "yr", "month_name", "ts.resoursetype"));
		
		$query = $this->db->get();
		$timesheet = $query->result_array();
		$res = array();
		// echo "<pre>"; print_r($timesheet); exit;
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				$res['total_cost']     += $ts['duration_cost'];
				$res['total_hours']    += $ts['duration_hours'];
				$res['total_dc'] 	   += $ts['duration_direct_cost'];
			}
		}
		// echo "<pre>"; print_r($res); exit;
		return $res;
	}
	
	public function get_timesheet_data($practice_arr, $start_date=false, $end_date=false, $month=false)
	{
		// echo "<pre>"; print_r($practice_arr);
		$prs = array();
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$pract = $pquery->result();
		if(!empty($pract) && count($pract)>0){
			foreach($pract as $pr){
				$prs[] = $pr->id;
			}
		}
		
		$this->db->select('dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code');
		$this->db->from($this->cfg['dbpref'].'timesheet_data');
		$tswhere = "resoursetype is NOT NULL";
		$this->db->where($tswhere);
		$excludewhere = "project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$this->db->where('practice_id !=', 0);
		$this->db->where_not_in("practice_id", 6);
		//for eads & eqad only
		$deptwhere = "dept_id in ('10','11')";
		$this->db->where($deptwhere);
		if(!empty($start_date)) {
			$this->db->where("DATE(start_time) >= ", date('Y-m-d', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("DATE(start_time) <= ", date('Y-m-d', strtotime($end_date)));
		}
		if(!empty($month)) {
			$this->db->where("DATE(start_time) >= ", date('Y-m-d', strtotime($month)));
			$this->db->where("DATE(end_time) <= ", date('Y-m-t', strtotime($month)));
		}
		$query2 = $this->db->get();
		// echo $this->db->last_query(); die;
		$timesheet_data = $query2->result();
		
		// echo "<pre>"; print_r($timesheet_data); die;
		
		$resarr = array();

		if(count($timesheet_data)>0) {
			foreach($timesheet_data as $row) {
				// echo $row->practice_id . " " . $row->resoursetype; exit;
				if (isset($resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'])) {
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'] = $row->duration_hours + $resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'];
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['cost'] = $row->resource_duration_cost + $resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['cost'];
				} else {
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['hour'] = $row->duration_hours;
					$resarr[$practice_arr[$row->practice_id]][$row->resoursetype]['cost'] = $row->resource_duration_cost;
				}
				$resarr[$practice_arr[$row->practice_id]]['totalhour'] = $resarr[$practice_arr[$row->practice_id]]['totalhour'] + $row->duration_hours;
				$resarr[$practice_arr[$row->practice_id]]['totalcost'] = $resarr[$practice_arr[$row->practice_id]]['totalcost'] + $row->resource_duration_cost;
				if(!empty($start_date) && !empty($end_date)) {
					if(!in_array($row->project_code, $resarr['project_code'])){
						$resarr['project_code'][] = $row->project_code;
					}
				}
			}
		}
		/* if(!empty($start_date) && !empty($end_date)) {
			echo "<pre>"; print_r($resarr); die;
		} */
		return $resarr;
	}
	
	public function get_timesheet_data_hours($pjt_code, $start_date=false, $end_date=false, $month=false)
	{
		// $start_date = '2006-01-01';
		// $end_date   = date('Y-m-d');
		
		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, SUM(ts.duration_hours) as duration_hours, ts.resoursetype, ts.username, ts.empname, ts.direct_cost_per_hour as direct_cost, sum( ts.`resource_duration_direct_cost`) as duration_direct_cost, sum( ts.`resource_duration_cost`) as duration_cost');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_data as ts');
		$this->db->where("ts.project_code", $pjt_code);
		if( (!empty($start_date)) && (!empty($end_date)) ){
			$this->db->where("DATE(ts.start_time) >= ", $start_date);
			$this->db->where("DATE(ts.end_time) <= ", $end_date);
		}
		if(!empty($month)) {
			$this->db->where("DATE(ts.start_time) >= ", date('Y-m-d', strtotime($month)));
			$this->db->where("DATE(ts.end_time) <= ", date('Y-m-t', strtotime($month)));
		}
		
		$this->db->group_by(array("ts.username", "yr", "month_name", "ts.resoursetype"));
		
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		$timesheet = $query->result_array();
		$res = array();
		// echo "<pre>"; print_r($timesheet); exit;
		$res['total_internal_hrs'] = $res['total_non_billable_hrs'] = $res['total_billable_hrs'] = 0;
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				$res['total_cost']     += $ts['duration_cost'];
				$res['total_hours']    += $ts['duration_hours'];
				$res['total_dc'] 	   += $ts['duration_direct_cost'];
				switch($ts['resoursetype']) {
					case 'Billable':
						$res['total_billable_hrs'] += $ts['duration_hours'];
					break;
					case 'Non-Billable':
						$res['total_non_billable_hrs'] += $ts['duration_hours'];
					break;
					case 'Internal':
						$res['total_internal_hrs'] += $ts['duration_hours'];
					break;
				}
			}
		}
		// echo "<pre>"; print_r($res); exit;
		return $res;
	}

	
	public function conver_currency($amount, $val) {
		return round($amount*$val, 2);
	}
	
	public function get_ir_val($lead_id, $ewid, $month=false, $start_date=false, $end_date=false, $base_currency) {
		
		// $rates = $this->get_currency_rates();
		$bk_rates = get_book_keeping_rates();
		
		$this->db->select('milestone_value, for_month_year');
		$this->db->from($this->cfg['dbpref']. 'view_sales_forecast_variance');
		$this->db->where("type = 'A' AND job_id = '".$lead_id."' ");
		if(!empty($month)) {
			// $this->db->where("(for_month_year ='".date('Y-m-d 00:00:00', strtotime($month))."' )", NULL, FALSE);
			$this->db->where("for_month_year", date('Y-m-d H:i:s', strtotime($month)));
		}
		if(!empty($start_date)) {
			$this->db->where("for_month_year >= ", date('Y-m-d H:i:s', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("for_month_year <= ", date('Y-m-d H:i:s', strtotime($end_date)));
		}
		$query  = $this->db->get();
		$result = $query->result();
		// echo $this->db->last_query() ."<br>"; exit;
		/* if(!empty($month)) {
			echo $this->db->last_query() ."<br>";
		} */
		$val = 0;
		if(!empty($result)) {
			foreach($result as $row) {
				$base_conversion_amt = $this->conver_currency($row->milestone_value, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($row->for_month_year)),"4/1","3/31")][$ewid][$base_currency]);
				$val += $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($row->for_month_year)),"4/1","3/31")][$base_currency][$this->default_cur_id]);
				// $val += $this->conver_currency($row->milestone_value, $rates[$ewid][$this->default_cur_id]);
			}
		}
		return $val;
	}
	
	/*
	*method : get_currency_rates
	*/
	public function get_currency_rates() {
		$this->load->model('report/report_lead_region_model');
		$currency_rates = $this->report_lead_region_model->get_currency_rate();
    	$rates 			= array();
    	if(!empty($currency_rates)) {
    		foreach ($currency_rates as $currency) {
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}
	
	/*
	@method - service_dashboard_data()
	@for drill down data
	*/
	public function service_dashboard_data()
	{
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		// $end_date  	   = $curFiscalYear."-".date('m-d'); //eg.2014-03-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t", strtotime($end_date));
			$month    = date("Y-m-01 00:00:00", strtotime($end_date));
		}
		if($this->input->post("billable_month")) {
			$bill_month = $this->input->post("billable_month");
			$month      = date("Y-m-01 00:00:00", strtotime($bill_month));
		}
		if($this->input->post("practice")) {
			$practice = $this->input->post("practice");
		}
		if($this->input->post("clicktype")) {
			$clicktype = $this->input->post("clicktype");
		}
		
		$month_status = $this->input->post("month_status");
		if(!empty($month_status)) {
			if($month_status==2) {
				$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
				$end_date = date('Y-m-t', strtotime('-1 month', $base_mon));
				$month 	  = date('Y-m-01 00:00:00', strtotime('-1 month', $base_mon));
				// $end_date  	   = date('Y-m-t', strtotime("-1 month"));
				// $month    = date("Y-m-01 00:00:00", strtotime("-1 month"));
			} else {
				$end_date = date('Y-m-d');
				$month    = date("Y-m-01 00:00:00");
			}
		}
 
		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;
		// echo $month;  die;
		$project_status = 1;
		if($this->input->post("project_status") && ($this->input->post("project_status")!='null')) {
			$project_status = @explode(',', $this->input->post("project_status"));
		}
		$division = '';
		if($this->input->post("entity") && ($this->input->post("entity")!='null')) {
			$division = @explode(',', $this->input->post("entity"));
		}
		
		//role based filtering
		/* if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
			$varSessionId = $this->userdata['userid']; //Current Session Id.

			//Fetching Project Team Members.
			$this->db->select('jobid_fk as lead_id');
			$this->db->where('userid_fk', $varSessionId);
			$rowscj = $this->db->get($this->cfg['dbpref'] . 'contract_jobs');
			$dat['jobids'] = $rowscj->result_array();
			
			//Fetching Project Manager, Lead Assigned to & Lead owner jobids.
			$this->db->select('lead_id');
			$this->db->where("(assigned_to = '".$varSessionId."' OR lead_assign = '".$varSessionId."' OR belong_to = '".$varSessionId."')");
			$this->db->where("lead_status", 4);
			$this->db->where("pjt_status", 1);
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'leads');
			$dat['jobids1'] = $rowsJobs->result_array();

			//Fetching Stake Holders.
			$data['jobids2'] = array();
			$this->db->select('lead_id');
			$this->db->where("user_id",$varSessionId);
			$rowsJobs = $this->db->get($this->cfg['dbpref'] . 'stake_holders');
			if($rowsJobs->num_rows()>0)	$dat['jobids2'] = $rowsJobs->result_array();			
			
			$data = array_merge_recursive($dat['jobids'], $dat['jobids1'],$dat['jobids2']);
 
			$res[] = 0;
			if (is_array($data) && count($data) > 0) { 
				foreach ($data as $data) {
					$res[] = $data['lead_id'];
				}
			}
			$result_ids = array_unique($res);
		} */
		//role based filtering
		
		$this->db->select('l.lead_id, l.lead_title, l.complete_status, l.estimate_hour, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.project_type, l.division');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		// $pt_not_in_arr = array('4','8');
		// $this->db->where("l.project_type", 1);
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		if($practice){
			$this->db->where("l.practice", $practice);
		}
		if($project_status){
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}
		if(isset($clicktype) && ($clicktype == 'rag')) {
			$this->db->where_in("l.rag_status", 1);
		}
		if(isset($clicktype) && ($clicktype == 'rag_project_export')) {
			$this->db->where_in("l.rag_status", 1);
		}
		/* if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
			$this->db->where_in('l.lead_id', $result_ids);
		} */
		$query = $this->db->get();
		$res = $query->result_array();
		
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		$pquery = $this->db->get();
		$pres1 = $pquery->result();					
		if(!empty($pres1) && count($pres1)>0){
			foreach($pres1 as $prow1) {
				$practice_arr[$prow1->id] = $prow1->practices;
			}
		}
		
		switch($clicktype) {
			case 'noprojects':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "inprogress_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'rag':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "rag_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'inprogress_project_export':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$res = $this->excelexport($data['projects_data']);
			break;
			case 'rag_project_export':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$res = $this->excelexport($data['projects_data']);
			break;
			case 'cm_billing':
				$data['invoices_data'] = $this->getCMIRData($practice, $month);
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "cm_billing_export";
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
			case 'cm_billing_export':
				$data['invoices_data'] = $this->getCMIRData($practice, $month);
				$result = $this->excelexportinvoice($data['invoices_data']);
			break;
			case 'irval':
				$data['invoices_data'] = $this->getIRData($res, $start_date, $end_date, $practice);
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "inv_project_export";
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
			case 'inv_project_export':
				$data['invoices_data'] = $this->getIRData($res, $start_date, $end_date, $practice);
				$result = $this->excelexportinvoice($data['invoices_data']);
			break;
			case 'cm_eff':
				$data = $this->get_billable_efforts($practice, $month); 
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id'] = $practice;
				$this->load->view('projects/service_dashboard_billable_drill_data', $data);
			break;
			case 'ytd_eff':
				$data = $this->get_billable_efforts($practice, "", $start_date, $end_date);
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id'] = $practice;
				$this->load->view('projects/service_dashboard_billable_drill_data', $data);
			break;
			case 'dc_value':
				$data = array();
				$data = $this->get_direct_cost_val($practice, "", $start_date, $end_date);
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id']   = $practice;
				$this->load->view('projects/service_dashboard_billable_drill_data', $data);
			break;
			case 'fixedbid':
				$billable_ytd = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
				$pcodes = $billable_ytd['project_code'];
				$project_codes = array();
				if(!empty($pcodes) && count($pcodes)>0){
					foreach($pcodes as $rec){
						if(!in_array($rec, $project_codes)){
							$project_codes[] = $rec;
						}
					}
				}
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title, l.complete_status, l.project_type');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_array = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_array = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_array);
				// $this->db->where("l.pjt_id", $rec);
				// $this->db->where("l.billing_type", 1);
				$this->db->where("l.practice", $practice);
				$this->db->where_in("l.pjt_id", $project_codes);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($pro_data, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "fixedbid_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'fixedbid_project_export':
				$billable_ytd = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
				$pcodes = $billable_ytd['project_code'];
				$project_codes = array();
				if(!empty($pcodes) && count($pcodes)>0){
					foreach($pcodes as $rec){
						if(!in_array($rec, $project_codes)){
							$project_codes[] = $rec;
						}
					}
				}
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title, l.complete_status, l.project_type');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_array = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_array = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_array);
				// $this->db->where("l.pjt_id", $rec);
				// $this->db->where("l.billing_type", 1);
				$this->db->where("l.practice", $practice);
				$this->db->where_in("l.pjt_id", $project_codes);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($pro_data, $start_date, $end_date);		
				$res = $this->excelexport($data['projects_data']);
			break;
		}
	}
	
	public function service_dashboard_data_beta()
	{
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		
		if($this->input->post("billable_month")) {
			$bill_month = $this->input->post("billable_month");
			$month      = date("Y-m-01 00:00:00", strtotime($bill_month));
		}
		if($this->input->post("practice")) {
			$practice = $this->input->post("practice");
		}
		if($this->input->post("clicktype")) {
			$clicktype = $this->input->post("clicktype");
		}
		
		$month_status = $this->input->post("month_status");
		
		$current_month 		= date('m');
		$fiscalStartMonth 	= '04';
		
		$month_status = $this->input->post("month_status");
		
		if(!empty($month_status)) {
			if($month_status == 2) {
				$base_mon = strtotime(date('Y-m',time()) . '-01 00:00:01');
				if($fiscalStartMonth == $current_month) {
					$curFiscalYearTemp 	= calculateFiscalYearForDateHelper(date("m/d/y"),"4/1","3/31"); 
					$last_fiscal_year 	= ($curFiscalYearTemp-1);
					$curFiscalYear 		= $last_fiscal_year;
					$start_date    		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
					$end_date    		= ($curFiscalYear)."-03-31";  //eg.2013-04-01
					
					/* $curFiscalYear 	= getLastFiscalYear();
					$start_date		= ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
					$end_date   	= ($curFiscalYear)."-03-31";  //eg.2013-04-01 */
				} else {
					$end_date = date('Y-m-t', strtotime('-1 month', $base_mon));
				}
				$month 	  = date('Y-m-01 00:00:00', strtotime('-1 month', $base_mon));
			} else {
				$end_date  	= date('Y-m-t');
				$month    	= date("Y-m-01 00:00:00");
			}
		}

		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;
		// echo $month;  die;
		$project_status = 1;
		if($this->input->post("project_status") && ($this->input->post("project_status")!='null')) {
			$project_status = @explode(',', $this->input->post("project_status"));
		}
		$division = '';
		if($this->input->post("entity") && ($this->input->post("entity")!='null')) {
			$division = @explode(',', $this->input->post("entity"));
		}
		
		$this->db->select('l.lead_id, l.lead_title, l.complete_status, l.estimate_hour, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.project_type, l.division');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		// $pt_not_in_arr = array('4','8');
		// $this->db->where("l.project_type", 1);
		$client_not_in_arr = array('ENO','NOA');
		$this->db->where_not_in("l.client_code", $client_not_in_arr);
		if($practice) {
			if($practice == 10) { //practice - others
				$pr_arr = array(7, 10, 13);
				$this->db->where_in("l.practice", $pr_arr);
			} else {
				$this->db->where("l.practice", $practice);
			}
		}
		if($project_status){
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}
		if(isset($clicktype) && ($clicktype == 'rag')) {
			$this->db->where_in("l.rag_status", 1);
		}
		if(isset($clicktype) && ($clicktype == 'rag_project_export')) {
			$this->db->where_in("l.rag_status", 1);
		}
		/* if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
			$this->db->where_in('l.lead_id', $result_ids);
		} */
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$res = $query->result_array();
		
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		$pquery = $this->db->get();
		$pres1 = $pquery->result();					
		if(!empty($pres1) && count($pres1)>0){
			foreach($pres1 as $prow1) {
				$practice_arr[$prow1->id] = $prow1->practices;
			}
		}

		switch($clicktype){
			case 'noprojects':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "inprogress_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'rag':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "rag_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'inprogress_project_export':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$res = $this->excelexport($data['projects_data']);
			break;
			case 'rag_project_export':
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
				$res = $this->excelexport($data['projects_data']);
			break;
			case 'cm_billing':
				$data['invoices_data'] = $this->getCMIRData($practice, $month);
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "cm_billing_export";
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
			case 'cm_billing_export':
				$data['invoices_data'] = $this->getCMIRData($practice, $month);
				$result = $this->excelexportinvoice($data['invoices_data']);
			break;
			case 'irval':
				$data['invoices_data'] = $this->getIRData($res, $start_date, $end_date, $practice);
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "inv_project_export";
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
			case 'inv_project_export':
				$data['invoices_data'] = $this->getIRData($res, $start_date, $end_date, $practice);
				$result = $this->excelexportinvoice($data['invoices_data']);
			break;
			case 'cm_eff':
				$data = $this->get_billable_efforts_beta($practice, $month); 
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id'] = $practice;
				$this->load->view('projects/service_dashboard_billable_drill_data_beta', $data);
			break;
			case 'ytd_eff':
				$data = $this->get_billable_efforts_beta($practice, "", $start_date, $end_date);
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id'] = $practice;
				$this->load->view('projects/service_dashboard_billable_drill_data_beta', $data);
			break;
			case 'dc_value':
				$data = $this->get_direct_cost_val($practice, "", $start_date, $end_date);
				$data['practices_name'] = $practice_arr[$practice];
				$data['practices_id']   = $practice;
				$data['start_date']   	= $start_date;
				$data['end_date']   	= $end_date;
				//*for other cost value projects only*//
				$data['othercost_projects'] = array();
				
				$this->db->select("pjt_id, lead_id, practice, lead_title");
				$this->db->where_in('department_id_fk', array(10,11)); //only eads & eqad projects only
				$ocres  = $this->db->get_where($this->cfg['dbpref']."leads", array("practice" => $practice)); //for temporary use
				$oc_res = $ocres->result_array();
				
				if(!empty($oc_res)) {
					foreach($oc_res as $ocrow) {
						if (isset($data['othercost_projects'][$practice_arr[$practice]])) {						
							$data['othercost_projects'][$practice_arr[$practice]][] = $ocrow['pjt_id'];
						} else {
							$data['othercost_projects'][$practice_arr[$practice]][] = $ocrow['pjt_id'];
						}
					}
				}
				$this->load->view('projects/service_dashboard_billable_drill_data_beta', $data);
			break;
			case 'fixedbid':
				$billable_ytd = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
				$pcodes = $billable_ytd['project_code'];
				$project_codes = array();
				if(!empty($pcodes) && count($pcodes)>0){
					foreach($pcodes as $rec){
						if(!in_array($rec, $project_codes)){
							$project_codes[] = $rec;
						}
					}
				}
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title, l.complete_status, l.project_type');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_array = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_array = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_array);
				// $this->db->where("l.pjt_id", $rec);
				// $this->db->where("l.billing_type", 1);
				$this->db->where("l.practice", $practice);
				$this->db->where_in("l.pjt_id", $project_codes);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($pro_data, $start_date, $end_date);
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				$data['practices_id'] = $practice;
				$data['excelexporttype'] = "fixedbid_project_export";
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'fixedbid_project_export':
				$billable_ytd = $this->get_timesheet_data($practice_arr, $start_date, $end_date, "");
				$pcodes = $billable_ytd['project_code'];
				$project_codes = array();
				if(!empty($pcodes) && count($pcodes)>0){
					foreach($pcodes as $rec){
						if(!in_array($rec, $project_codes)){
							$project_codes[] = $rec;
						}
					}
				}
				$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division, l.billing_type, l.lead_title, l.complete_status, l.project_type');
				$this->db->from($this->cfg['dbpref']. 'leads as l');
				// $pt_not_in_array = array('4','8');
				$this->db->where("l.project_type", 1);
				$client_not_in_array = array('ENO','NOA');
				$this->db->where_not_in("l.client_code", $client_not_in_array);
				// $this->db->where("l.pjt_id", $rec);
				// $this->db->where("l.billing_type", 1);
				$this->db->where("l.practice", $practice);
				$this->db->where_in("l.pjt_id", $project_codes);
				$query3 = $this->db->get();
				$pro_data = $query3->result_array();
				
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();
				
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($pro_data, $start_date, $end_date);		
				$res = $this->excelexport($data['projects_data']);
			break;
		}
	}	
	
	/* Change the actual worth amount to Default currency */
	public function getProjectsDataByDefaultCurrency($records, $start_date, $end_date)
	{
		$this->load->model('project_model');
		$rates = $this->get_currency_rates();
		 
		$data['project_record'] = array();
		$i = 0;
		if (isset($records) && count($records)) {
			foreach($records as $rec) {
				
				$amt_converted = $this->conver_currency($rec['actual_worth_amount'], $rates[$rec['expect_worth_id']][$this->default_cur_id]);
				
				$data['timesheet_data'] = array();
				$timesheet				= array();
				$total_cost				= 0;
				$total_hours			= 0;
				$total_billable_hrs		= 0;
				$total_internal_hrs		= 0;
				$total_non_billable_hrs = 0;
				$project_type			= '';
				$timesheet 			    = array();
				/* if(!empty($rec['pjt_id'])){
					$timesheet = $this->get_timesheet_data_hours($rec['pjt_id'], "", "");
				} */
				
				$bill_type = 1;
				
				$total_amount_inv_raised = 0;
				$invoice_amount = $this->project_model->get_invoice_total($rec['lead_id']);
				if(count($invoice_amount)>0 && !empty($invoice_amount)){
					$total_amount_inv_raised = $invoice_amount->invoice_amount+$invoice_amount->tax_price;
				}

				$total_amount_inv_raised = $this->conver_currency($total_amount_inv_raised, $rates[$rec['expect_worth_id']][$this->default_cur_id]);
				
				if(!empty($rec['pjt_id'])){
					$timesheet = $this->project_model->get_timesheet_data_updated($rec['pjt_id'], $rec['lead_id'], $bill_type, $metrics_date, $groupby_type=2);
				}
				
				$total_billable_hrs 	= 0;
				$total_internal_hrs 	= 0;
				$total_non_billable_hrs = 0;
				$total_cost  			= 0;
				$total_hours 			= 0;
				$total_dc_hours 		= 0;
				
				/* calculation for UC based on the max hours starts */
				$timesheet_data = array();
				if(count($timesheet)>0) {
					foreach($timesheet as $ts) { 
						$financialYear 		= get_current_financial_year($ts['yr'],$ts['month_name']);
						$max_hours_resource = get_practice_max_hour_by_financial_year($ts['practice_id'],$financialYear);
						
						$timesheet_data[$ts['username']]['practice_id'] = $ts['practice_id'];
						$timesheet_data[$ts['username']]['max_hours'] = $max_hours_resource->practice_max_hours;
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['cost'] = $ts['cost'];
						$rateCostPerHr		 = $ts['cost'];
						$directrateCostPerHr = $ts['direct_cost'];
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rateperhr'] = $rateCostPerHr;
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['direct_rateperhr'] = $directrateCostPerHr;
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['duration'] = $ts['duration_hours'];
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['direct_cost'] = $ts['duration_direct_cost'];
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']][$ts['resoursetype']]['rs_name'] = $ts['empname'];
						
						$timesheet_data[$ts['username']][$ts['yr']][$ts['month_name']]['total_hours'] =get_timesheet_hours_by_user($ts['username'],$ts['yr'],$ts['month_name'],array('Leave','Hol'));
					
					}
				}
				// echo "<pre>"; print_r($timesheet_data); die;
				$total_billable_hrs		= 0;
				$total_non_billable_hrs = 0;
				$total_internal_hrs		= 0;
				$total_cost				= 0;
				$total_hours			= 0;
				$total_dc_hours			= 0;
				if(count($timesheet_data)>0 && !empty($timesheet_data)){
					foreach($timesheet_data as $key1=>$value1) {
						$resource_name = $key1;
						$max_hours = $value1['max_hours'];
						foreach($value1 as $key2=>$value2) {
							$year = $key2;
							foreach($value2 as $key3=>$value3) {
								$individual_billable_hrs		= 0;
								$month		 	  = $key3;
								$billable_hrs	  = 0;
								$non_billable_hrs = 0;
								$internal_hrs	  = 0;
								foreach($value3 as $key4=>$value4) {
									
									switch($key4) {
										case 'Billable':
											$rate				 = $value4['rateperhr'];
											$direct_rateperhr	 = $value4['direct_rateperhr'];
											$billable_hrs		 = $value4['duration'];
											$direct_billable_hrs = $value4['duration_direct_cost'];
											$total_billable_hrs += $billable_hrs;
										break;
										case 'Non-Billable':
											$rate				 	 = $value4['rateperhr'];
											$direct_rateperhr	 	 = $value4['direct_rateperhr'];
											$non_billable_hrs		 = $value4['duration'];
											$direct_non_billable_hrs = $value4['duration_direct_cost'];
											$total_non_billable_hrs += $non_billable_hrs;
										break;
										case 'Internal':
											$rate				 = $value4['rateperhr'];
											$direct_rateperhr	 = $value4['direct_rateperhr'];
											$internal_hrs 		 = $value4['duration'];
											$direct_internal_hrs = $value4['duration_direct_cost'];
											$total_internal_hrs += $internal_hrs;
										break;
									}
								}
							
								$individual_billable_hrs = $value3['total_hours'];
								 
								// calculation for the utilization cost based on the master hours entered.

								$rate1 = $rate;
								$direct_rateperhr1 = $direct_rateperhr;
								if($individual_billable_hrs>$max_hours){
									//echo 'max'.$max_hours.'<br>';
									$percentage = ($max_hours/$individual_billable_hrs);
									//echo 'percentage'.$percentage.'<br>';
									$rate1 = number_format(($percentage*$rate),2);
									$direct_rateperhr1 = number_format(($percentage*$direct_rateperhr),2);
								}
							 
								$total_hours += $billable_hrs+$internal_hrs+$non_billable_hrs;
								//$total_dc_hours += $direct_billable_hrs+$direct_non_billable_hrs+$direct_internal_hrs;
								$total_dc_hours += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);
								$total_cost += $rate1*($billable_hrs+$internal_hrs+$non_billable_hrs);				
							}
						}
					}	 
				}	 
				/* calculation for UC based on the max hours ends */
				
				//get the other cost details for the project.
				$other_cost_values = $this->getOtherCostValues($rec['lead_id']);
				
				// for company name

				//Build the Array
				$data['project_record'][$i]['lead_id'] 			= $rec['lead_id'];
				$data['project_record'][$i]['lead_title'] 		= $rec['lead_title'];
				$data['project_record'][$i]['practice']			= $rec['practice'];
				$data['project_record'][$i]['complete_status']	= $rec['complete_status'];
				$data['project_record'][$i]['project_type']	 	= $rec['project_type'];
				$data['project_record'][$i]['estimate_hour']	= $rec['estimate_hour'];
				$data['project_record'][$i]['actual_worth_amt'] = number_format($amt_converted, 2, '.', '');
				$data['project_record'][$i]['pjt_id']			= $rec['pjt_id'];
				$data['project_record'][$i]['rag_status'] 		= $rec['rag_status'];
				$data['project_record'][$i]['expect_worth_id'] 	= $rec['expect_worth_id'];
				$data['project_record'][$i]['bill_hr'] 			= isset($total_billable_hrs)?$total_billable_hrs:'0';
				$data['project_record'][$i]['int_hr'] 			= isset($total_internal_hrs)?$total_internal_hrs:'0';
				$data['project_record'][$i]['nbil_hr'] 			= isset($total_non_billable_hrs)?$total_non_billable_hrs:'0';
				$data['project_record'][$i]['other_cost'] 		= $other_cost_values;
				$data['project_record'][$i]['total_hours'] 		= $total_hours;
				$data['project_record'][$i]['total_dc_hours'] 	= isset($total_dc_hours)?$total_dc_hours:'0';
				$data['project_record'][$i]['total_amount_inv_raised'] = $total_amount_inv_raised;
				$data['project_record'][$i]['total_cost'] 		= isset($total_cost)?number_format($total_cost, 2, '.', ''):'0';
				$i++;
				
			}
		}
		return $data['project_record'];
	}
	
	/*
	*get all other cost values from the db & sum it
	*base currency
	*/
	function getOtherCostValues($project_id)
	{
		$this->load->model('project_model');
		$value = 0;
		$bk_rates = get_book_keeping_rates(); //get all the book keeping rates
		$other_cost_data = $this->project_model->getOtherCost($project_id);
		if(!empty($other_cost_data) && count($other_cost_data)>0) {
			foreach($other_cost_data as $rec) {
				$conver_value  = 0;
				$curFiscalYear = date('Y'); //set as default current year as fiscal year
				$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
				$convert_value = $this->conver_currency($rec['value'], $bk_rates[$curFiscalYear][$rec['currency_type']][$this->default_cur_id]);
				$value += $convert_value;
			}
		}		
		return $value;
	}
	
	public function getIRData($records, $start_date, $end_date, $practice)
	{
		
		$bk_rates = get_book_keeping_rates();
		
		$data = array();
		
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, cc.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		if(!empty($practice)) {
			if($practice == 10){ //INFRA SERVICES & TESTING practice values are merged with OTHERS practice
				$pra_arr = array(7, 10, 13);
				$this->db->where_in("l.practice", $pra_arr);
			} else {
				$this->db->where("l.practice", $practice);
			}
			
		}
		if(!empty($start_date)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("sfv.for_month_year <= ", date('Y-m-d H:i:s', strtotime($end_date)));
		}
		
		$query = $this->db->get();
		$invoice_rec = $query->result_array();

		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoice_rec)>0) {
			foreach ($invoice_rec as $inv) {
				$data['invoices'][$i]['lead_title']		= $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 		= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 		= $inv['job_id'];
				$data['invoices'][$i]['customer'] 		= $inv['first_name'].' '.$inv['last_name'].' - '.$inv['company'];
				$data['invoices'][$i]['milestone_name'] = $inv['milestone_name'];

				$base_conversion_amt					= $this->conver_currency($inv['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['expect_worth_id']][$inv['base_currency']]);
				$data['invoices'][$i]['coverted_amt']   = $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['base_currency']][$this->default_cur_id]);
				// converting based on base currency
				$data['invoices'][$i]['month_year']    = $inv['for_month_year'];
				$data['total_amt'] 	                  += $data['invoices'][$i]['coverted_amt'];
				$i++;
			}
		}
		return $data;
	}
	
	public function getCMIRData($practice, $month)
	{
		$bk_rates = get_book_keeping_rates();
		
		$data = array();
		
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, cc.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		if(!empty($practice)) {
			if($practice == 10) { //INFRA SERVICE & TESTING practice values are merged with OTHERS practice
				$pr_arra = array(7, 10, 13);
				$this->db->where_in("l.practice", $pr_arra);
			} else {
				$this->db->where("l.practice", $practice);
			}
		}
		if(!empty($month)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($month)));
			$this->db->where("sfv.for_month_year <= ", date('Y-m-t H:i:s', strtotime($month)));
		}
		$query = $this->db->get();
		$invoice_rec = $query->result_array();
		
		$resarr = array();
		//****//
		
		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoice_rec)>0) {
			foreach ($invoice_rec as $inv) {
				$data['invoices'][$i]['lead_title']		= $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 		= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 		= $inv['job_id'];
				$data['invoices'][$i]['customer'] 		= $inv['first_name'].' '.$inv['last_name'].' - '.$inv['company'];
				$data['invoices'][$i]['milestone_name'] = $inv['milestone_name'];

				$base_conversion_amt					= $this->conver_currency($inv['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['expect_worth_id']][$inv['base_currency']]);
				$data['invoices'][$i]['coverted_amt']   = $this->conver_currency($base_conversion_amt, $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($inv['for_month_year'])),"4/1","3/31")][$inv['base_currency']][$this->default_cur_id]);
				// converting based on base currency
				$data['invoices'][$i]['month_year']    = $inv['for_month_year'];
				$data['total_amt'] 	                  += $data['invoices'][$i]['coverted_amt'];
				$i++;
			}
		}
		return $data;
	}
	
	
	public function get_billable_efforts($practice, $month=false, $start_date=false, $end_date=false)
	{		
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->where('t.resoursetype', 'Billable');
		if(!empty($month)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($month))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-t', strtotime($month))."' )", NULL, FALSE);
		}
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-d', strtotime($end_date)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$this->db->where_in("t.practice_id", $practice);

		$query = $this->db->get();
		$data['resdata'] 	   = $query->result(); 
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		
		$data['heading'] 	     = $practice;
		$data['resource_type']   = "Billable";
		$data['filter_sort_by']  = 'desc';
		$data['filter_sort_val'] = 'hour';
		$timesheet_db->close();
		
		return $data;
	}	
	/*
	@method - get_billable_efforts()
	@for drill down data
	*/
	public function get_billable_efforts_beta($practice, $month=false, $start_date=false, $end_date=false)
	{		
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->where('t.resoursetype', 'Billable');
		if(!empty($month)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($month))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-t', strtotime($month))."' )", NULL, FALSE);
		}
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-d', strtotime($end_date)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$this->db->where_in("t.practice_id", $practice);

		$query = $this->db->get();
		$data['resdata'] 	   = $query->result(); 
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		
		$data['heading'] 	     = $practice;
		$data['resource_type']   = "Billable";
		$data['filter_sort_by']  = 'desc';
		$data['filter_sort_val'] = 'hour';
		$timesheet_db->close();
		
		return $data;
	}	
	
	/*
	@method - get_billable_efforts()
	@for drill down data
	*/
	public function get_direct_cost_val($practice, $month=false, $start_date=false, $end_date=false)
	{
		/* $contribution_query = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code, direct_cost_per_hour, resource_duration_direct_cost
		FROM crm_timesheet_data 
		WHERE start_time between '".$start_date."' and '".$end_date."' AND resoursetype != '' AND project_code NOT IN ('HOL','Leave')"; */
	
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, l.lead_id');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
		if(!empty($month)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($month))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-t', strtotime($month))."' )", NULL, FALSE);
		}
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-t', strtotime($end_date)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$resrc = 't.resoursetype IS NOT NULL';
		$deptwhere = "t.dept_id IN ('10','11')";
		$this->db->where($deptwhere);
		$this->db->where($resrc);
		if($practice == 10) { //Infra services & tecting practice values are merged with others practices
			$p_arr = array(7, 10, 13);
			$this->db->where_in("l.practice", $p_arr);
		} else {
			$this->db->where_in("l.practice", $practice);
		}
		// $this->db->where_in('l.lead_id', array(710, 276)); // for temporary - load some data only
		// $this->db->where('l.lead_id', 710); // for temporary - load some data only
		// $this->db->limit(3); // for temporary - load some data only
		$query 					= $this->db->get();
		// echo $this->db->last_query(); exit;
		$data['resdata'] 	   	= $query->result();
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$timesheet_db->close();
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		
		$data['heading'] 	     = $practice;
		$data['resource_type']   = "Billable";
		$data['filter_sort_by']  = 'desc';
		$data['filter_sort_val'] = 'cost';
		
		return $data;
	}
	
	public function excelexport($pjts_data) {
		
		$this->db->select('project_billing_type, id');
		$this->db->from($this->cfg['dbpref']. 'project_billing_type');
		$ptquery = $this->db->get();
		$project_type = $ptquery->result();
		
		$pt_arr = array();
		if(!empty($project_type) && count($project_type)>0){
			foreach($project_type as $prec){
				$pt_arr[$prec->id] = $prec->project_billing_type;
			}
		}
		
		// echo "<pre>"; print_r($pjts_data); die;
		if(count($pjts_data)>0) {
    		//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Projects');

			//set cell A1 content with some text			
			$this->excel->getActiveSheet()->setCellValue('A1', 'Project Title');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Project Completion (%)');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Project Type');
			$this->excel->getActiveSheet()->setCellValue('D1', 'RAG Status');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Planned Hours');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Billable Hours');
			$this->excel->getActiveSheet()->setCellValue('G1', 'Internal Hours');
			$this->excel->getActiveSheet()->setCellValue('H1', 'Non-Billable Hours');
			$this->excel->getActiveSheet()->setCellValue('I1', 'Total Utilized Hours (Actuals)');
			$this->excel->getActiveSheet()->setCellValue('J1', 'Effort Variance');
			$this->excel->getActiveSheet()->setCellValue('K1', 'Project Value ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('L1', 'Utilization Cost ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('M1', 'Direct Cost ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('N1', 'Invoice Raised ('.$this->default_cur_name.')');
			$this->excel->getActiveSheet()->setCellValue('O1', 'Contribution %');
			$this->excel->getActiveSheet()->setCellValue('P1', 'P&L');
			$this->excel->getActiveSheet()->setCellValue('Q1', 'P&L %');

			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);

			$i=2;
			
    		foreach($pjts_data as $rec) {
			
				$estimate_hr =  (isset($rec['estimate_hour'])) ? $rec['estimate_hour'] : "-";
				switch ($rec['rag_status']) {
					case 1:
						$rag = 'Red';
					break;
					case 2:
						$rag = 'Amber';
					break;
					case 3:
						$rag = 'Green';
					break;
					default:
						$rag = '-';
				}
				$bill_hr = (isset($rec['bill_hr'])) ? round($rec['bill_hr']) : "-";
				$inter_hr = (isset($rec['int_hr'])) ? round($rec['int_hr']) : "-";
				$nbill_hr = (isset($rec['nbil_hr'])) ? round($rec['nbil_hr']) : "-";
				$total_hr = ($rec['bill_hr']+$rec['int_hr']+$rec['nbil_hr']);
				$pjt_val = (isset($rec['actual_worth_amt'])) ? $rec['actual_worth_amt'] : "-";
				$util_cost = (isset($rec['total_cost'])) ? round($rec['total_cost']) : "-";
				$total_amount_inv_raised = (isset($rec['total_amount_inv_raised'])) ? round($rec['total_amount_inv_raised']) : "-";
				
				$profitloss    = round($total_amount_inv_raised-$util_cost);
				$plPercent = round(($profitloss/$util_cost)*100);
				
				$bill_type = $rec['billing_type'];
				
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $rec['lead_title']);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $rec['complete_status']);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $pt_arr[$rec['project_type']]);
				$this->excel->getActiveSheet()->setCellValue('D'.$i, $rag);
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $estimate_hr);
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $bill_hr);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $inter_hr);
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $nbill_hr);
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $total_hr);
				$this->excel->getActiveSheet()->setCellValue('J'.$i, $total_hr-$rec['estimate_hour']);
				$this->excel->getActiveSheet()->setCellValue('K'.$i, $pjt_val);
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $util_cost);
				$total_dc_hours = (isset($rec['total_dc_hours'])) ? (round($rec['total_dc_hours'])) : '0';
				$contributePercent = round((($total_amount_inv_raised-$total_dc_hours)/$total_amount_inv_raised)*100);
				$this->excel->getActiveSheet()->setCellValue('M'.$i, $total_dc_hours);
				$this->excel->getActiveSheet()->setCellValue('N'.$i, $total_amount_inv_raised);
				$this->excel->getActiveSheet()->setCellValue('O'.$i, $contributePercent);
				$this->excel->getActiveSheet()->setCellValue('P'.$i, $profitloss);
				$this->excel->getActiveSheet()->setCellValue('Q'.$i, $plPercent);
				$i++;
    		}
			
			//for first sheet
			$this->excel->setActiveSheetIndex(0);
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
			$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
			//Column Alignment
			$this->excel->getActiveSheet()->getStyle('D2:D'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('E2:E'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('F2:F'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('H2:H'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('I2:I'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('J2:J'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('K2:K'.$i)->getNumberFormat()->setFormatCode('0.00');
			
			// $filename='Project_report.xls'   ; //save our workbook as this file name
			$filename='Project_report_'.time().'.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
    	}
		redirect('/projects/dashboard');
	}
	
	public function excelexportinvoice($invoices_res)
	{
		if((count($invoices_res['invoices'])>0) && !empty($invoices_res['invoices'])) {
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Invoices');
			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', 'Month & Year');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Project Title');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Project Code');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Milestone Name');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Value('.$this->default_cur_name.')');
			
			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			$i=2;		
			$total_amt = '';
			if(count($invoices_res['invoices'])>0) {
				foreach($invoices_res['invoices'] as $excelarr) {
					//display only date
					$this->excel->getActiveSheet()->setCellValue('A'.$i, date('M Y', strtotime($excelarr['month_year'])));
					$this->excel->getActiveSheet()->setCellValue('B'.$i, $excelarr['lead_title']);
					$this->excel->getActiveSheet()->setCellValue('C'.$i, $excelarr['pjt_id']);
					$this->excel->getActiveSheet()->setCellValue('D'.$i, $excelarr['milestone_name']);
					$this->excel->getActiveSheet()->setCellValue('E'.$i, $excelarr['coverted_amt']);
					$i++;
				}
			}
			$this->excel->getActiveSheet()->setCellValue('E'.$i, $invoices_res['total_amt']);
			
			// $this->excel->getActiveSheet()->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode('0.00');
			$this->excel->getActiveSheet()->getStyle('E2:E'.$i)->getNumberFormat()->setFormatCode('0.00');
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
			//merge cell A1 until D1
			//$this->excel->getActiveSheet()->mergeCells('A1:D1');
			//set aligment to center for that merged cell (A1 to D1)
			
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			
			//cell format
			// $this->excel->getActiveSheet()->getStyle('A2:A'.$i)->getNumberFormat()->setFormatCode('00000');
			
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$filename='Invoice.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
						 
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
		}
	}
	
	public function cost_report()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$dept   			  = array();
		$data['page_heading'] = "IT Cost Report";
		$data['filter_area_status'] = $this->input->post("filter_area_status");
		
		$start_date = date("Y-m-1");
		$end_date   = date("Y-m-d");
		// $start_date = date("Y-m-d", strtotime('01-04-2017'));
		// $end_date   = date("Y-m-d", strtotime('30-04-2017'));
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		
		$resource_type  = $this->input->post("resource_type");
		$department_ids = $this->input->post("department_ids");
		$practice_ids   = $this->input->post("practice_ids");
		$dept_type      = $this->input->post("dept_type");
		$skill_ids 		= $this->input->post("skill_ids");
		$member_ids		= $this->input->post("member_ids");
		$entity_ids 	= $this->input->post("entity_ids");
		
		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.entity_id, t.entity_name, p.id as practice_id, p.practices as practice_name');
		// t.practice_id, t.practice_name
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref']. 'leads as l', 'l.pjt_id = t.project_code', 'LEFT');
		$this->db->join($this->cfg['dbpref']. 'practices as p', 'p.id = l.practice', 'LEFT');
		$this->db->where("t.resoursetype !=", '');
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."')", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."')", NULL, FALSE);
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			$this->db->where_not_in("t.project_code", array('Leave'));
			$data['exclude_leave'] = 1;
			$data['filter_area_status'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			$this->db->where_not_in("t.project_code", array('HOL'));
			$data['exclude_holiday'] = 1;
			$data['filter_area_status'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			$this->db->where_not_in("t.project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		if(!empty($entity_ids) && count($entity_ids)>0) {
			$data['entity_ids'] = $entity_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('t.entity_id', $entity_ids);
		}
		if(!empty($practice_ids) && count($practice_ids)>0) {
			$data['sel_practice_ids'] = $practice_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('l.practice', $practice_ids);
		}
		if(count($department_ids)>0 && !empty($department_ids)) {
			$data['department_ids'] = $department_ids;
			$data['filter_area_status'] = 1;
			$dids = implode(",",$department_ids);
			if(!empty($dids)) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
		} else {
			$deptwhere = "t.dept_id IN ('10','11')";
			$this->db->where($deptwhere);
		}
		if(count($skill_ids)>0 && !empty($skill_ids)) {
			$data['skill_ids'] = $skill_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('t.skill_id', $skill_ids);
		}
		if(count($member_ids)>0 && !empty($member_ids)) {
			$data['member_ids'] = $member_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('t.username', $member_ids);
		}
		$this->db->where('l.practice is not null');
		$query 						= $this->db->get();		
		// echo $this->db->last_query(); exit;
		$data['resdata'] 	   		= $query->result();
		$data['heading'] 	   		= $heading;
		$data['dept_type']     		= $dept_type;
		$data['resource_type'] 		= $resource_type;
		$data['conversion_rates'] 	= $this->get_currency_rates();
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		
		/* $this->db->select('department_id, department_name');
		$this->db->where_in('department_id', array('10','11'));
		$dept = $this->db->get($timesheet_db->dbprefix . 'department');
		$data['departments'] = $dept->result(); */
		
		$depts_res = array();
		$dept = $timesheet_db->query("SELECT department_id, department_name FROM ".$timesheet_db->dbprefix('department')." where department_id IN ('10','11') ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		
		$timesheet_db->close();
		
		if(!empty($data['departments']) && count($data['departments'])>0) {
			$this->db->select('t.skill_id, t.skill_name as name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($department_ids) && count($department_ids)>0) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
			if(!empty($practice_ids) && count($practice_ids)>0) {
				$this->db->where_in('t.practice_id', $practice_ids);
			}
			$this->db->group_by('t.skill_id');
			$this->db->order_by('t.skill_name');
			$skquery = $this->db->get();
			$data['skill_ids_selected'] = $skquery->result();
		}
		
		if(!empty($data['member_ids']) && count($data['member_ids'])>0) {
			$this->db->select("t.empname as emp_name, t.username");
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($department_ids) && count($department_ids)>0) {
				$this->db->where_in("t.dept_id", $department_ids);
			}				
			if(!empty($practice_ids) && count($practice_ids)>0) {
				$this->db->where_in('t.practice_id', $practice_ids);
			}
			/* if(!empty($data['member_ids'])) {
				$this->db->where_in("t.skill_id", $data['member_ids']);
			} */
			$this->db->group_by('t.empname');
			$mem_qry = $this->db->get();
			$data['member_ids_selected'] = $mem_qry->result();
		}

		//get other costs
		$data['other_cost_arr']   = $this->dashboard_model->getOtherCosts($start_date, $end_date, $entity_ids, $practice_ids);

		//for practices		
		$data['practice_ids'] 	  = $this->get_default_practices($start_date, $end_date);
		$data['entitys'] 	  	  = $this->dashboard_model->get_entities();

		$data['start_date'] 	  = $start_date;
		$data['end_date']   	  = $end_date;
		$data['results']    	  = $arr_depts;

		// echo "<pre>"; print_r($data); die;
		$this->load->view("projects/cost_report_view", $data);
	}
	
	public function cost_report_export()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$dept   			  = array();
		$data['page_heading'] = "IT Cost Report";
		$data['filter_area_status'] = $this->input->post("filter_area_status");
		
		$start_date = date("Y-m-1");
		$end_date   = date("Y-m-d");
		// $start_date = date("Y-m-d", strtotime('01-04-2017'));
		// $end_date   = date("Y-m-d", strtotime('30-04-2017'));
		
		// echo '<pre>'; print_r($this->input->post()); die;
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		
		$resource_type  = $this->input->post("resource_type");
		$department_ids = $this->input->post("department_ids");
		$practice_ids   = $this->input->post("practice_ids");
		$dept_type      = $this->input->post("dept_type");
		$skill_ids 		= $this->input->post("skill_ids");
		$member_ids		= $this->input->post("member_ids");
		$entity_ids 	= $this->input->post("entity_ids");
		
		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.entity_id, t.entity_name, p.id as practice_id, p.practices as practice_name');
		// t.practice_id, t.practice_name
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref']. 'leads as l', 'l.pjt_id = t.project_code', 'LEFT');
		$this->db->join($this->cfg['dbpref']. 'practices as p', 'p.id = l.practice', 'LEFT');
		$this->db->where("t.resoursetype !=", '');
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."')", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."')", NULL, FALSE);
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			$this->db->where_not_in("t.project_code", array('Leave'));
			$data['exclude_leave'] = 1;
			$data['filter_area_status'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			$this->db->where_not_in("t.project_code", array('HOL'));
			$data['exclude_holiday'] = 1;
			$data['filter_area_status'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			$this->db->where_not_in("t.project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		if(!empty($entity_ids) && count($entity_ids)>0) {
			if($entity_ids != 'null') {
				$this->db->where_in('t.entity_id', $entity_ids);
			}
		}
		if(!empty($practice_ids) && count($practice_ids)>0) {
			if($practice_ids != 'null') {
				$this->db->where_in('l.practice', $practice_ids);
			}
		}
		if(count($department_ids)>0 && !empty($department_ids)) {
			if($department_ids != 'null') {
				$data['department_ids'] = $department_ids;
				$data['filter_area_status'] = 1;
				$dids = implode(",",$department_ids);
				if(!empty($dids)) {
					$this->db->where_in("t.dept_id", $department_ids);
				}
			} else {
				$deptwhere = "t.dept_id IN ('10','11')";
				$this->db->where($deptwhere);
			}
		} else {
			$deptwhere = "t.dept_id IN ('10','11')";
			$this->db->where($deptwhere);
		}
		if(count($skill_ids)>0 && !empty($skill_ids)) {
			if($skill_ids != 'null') {
				$data['skill_ids'] = $skill_ids;
				$data['filter_area_status'] = 1;
				$this->db->where_in('t.skill_id', $skill_ids);
			}
		}
		if(count($member_ids)>0 && !empty($member_ids)) {
			if($member_ids != 'null') {
				$data['member_ids'] = $member_ids;
				$data['filter_area_status'] = 1;
				$this->db->where_in('t.username', $member_ids);
			}
		}
		$this->db->where('l.practice is not null');
		$query 						= $this->db->get();		
		// echo $this->db->last_query(); exit;
		$resdata  = $query->result();
		$data['heading'] 	   		= $heading;
		$data['dept_type']     		= $dept_type;
		$data['resource_type'] 		= $resource_type;
		$conversion_rates 	= $this->get_currency_rates();
		
		// echo '<pre>'; print_r($resdata); die;
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		
		$depts_res = array();
		$dept = $timesheet_db->query("SELECT department_id, department_name FROM ".$timesheet_db->dbprefix('department')." where department_id IN ('10','11') ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		
		$timesheet_db->close();
		
		if(!empty($data['departments']) && count($data['departments'])>0) {
			$this->db->select('t.skill_id, t.skill_name as name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($department_ids) && count($department_ids)>0) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
			if(!empty($practice_ids) && count($practice_ids)>0) {
				$this->db->where_in('t.practice_id', $practice_ids);
			}
			$this->db->group_by('t.skill_id');
			$this->db->order_by('t.skill_name');
			$skquery = $this->db->get();
			$data['skill_ids_selected'] = $skquery->result();
		}
		
		if(!empty($data['member_ids']) && count($data['member_ids'])>0) {
			$this->db->select("t.empname as emp_name, t.username");
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($department_ids) && count($department_ids)>0) {
				$this->db->where_in("t.dept_id", $department_ids);
			}				
			if(!empty($practice_ids) && count($practice_ids)>0) {
				$this->db->where_in('t.practice_id', $practice_ids);
			}
			/* if(!empty($data['member_ids'])) {
				$this->db->where_in("t.skill_id", $data['member_ids']);
			} */
			$this->db->group_by('t.empname');
			$mem_qry = $this->db->get();
			$data['member_ids_selected'] = $mem_qry->result();
		}
		
		//get other costs
		if($entity_ids == 'null') {
			$entity_ids = array();
		}
		$other_cost_arr   = $this->dashboard_model->getOtherCosts($start_date, $end_date, $entity_ids, $practice_ids);
		// echo '<pre>123'; print_r($other_cost_arr); die;

		//for practices		
		$data['practice_ids'] 	  = $this->get_default_practices($start_date, $end_date);
		$data['entitys'] 	  	  = $this->dashboard_model->get_entities();

		$data['start_date'] 	  = $start_date;
		$data['end_date']   	  = $end_date;
		$data['results']    	  = $arr_depts;
		
		$tbl_data = array();
		$sub_tot  = array();
		$sub_tot_hr    = array();
		$sub_tot_cst   = array();
		$pr_usercnt    = array();
		$sk_usercnt    = array();
		$skil_sub_tot  = array();
		$skil_sort_hr  = array();
		$skil_sort_cst = array();
		$user_hr 	   = array();
		$user_cst 	   = array();
		$cost_arr 	   = array();
		$prac = array();
		$dept = array();
		$skil = array();
		$proj = array();
		$user_data 		= array();
		$timesheet_data = array();
		$sub_tot_entity_hr 		= array();
		$sub_tot_entity_cst 	= array();
		$sub_tot_entity_dircst 	= array();
		$tot_hour = 0;
		$tot_cost = 0;
		// echo '<pre>'; print_r($resdata); die;
		if(!empty($resdata)) {
			foreach($resdata as $rec) {
				$rates 				= $conversion_rates;
				$financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
				$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id,$financialYear);
				
				$user_data[$rec->username]['emp_name'] 		= $rec->empname;
				$user_data[$rec->username]['max_hours'] 	= $max_hours_resource->practice_max_hours;
				$user_data[$rec->username]['dept_name'] 	= $rec->dept_name;
				$user_data[$rec->username]['prac_id'] 		= $rec->practice_id; 
				
				$rateCostPerHr 			= round($rec->cost_per_hour * $rates[1][$this->default_cur_id], 2);
				$directrateCostPerHr 	= round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
				
				if(isset($timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'])) {
					$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
				} else {
					$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] = $rec->duration_hours;
				}
				
				$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
				$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr']        = $rateCostPerHr;
				
				$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
			}
			
			// echo "<pre>"; print_r($timesheet_data); echo "</pre>"; die;
			
			if(!empty($timesheet_data) && count($timesheet_data)>0) {
				foreach($timesheet_data as $entity_key=>$entity_arr) {
					if(!empty($entity_arr) && count($entity_arr)>0) {
						foreach($entity_arr as $dept_key=>$prac_arr) {
							if(!empty($prac_arr) && count($prac_arr)>0) {
								foreach($prac_arr as $prac_key=>$skill_arr) { 
								#echo "dept key " .$dept_key . " practice ".$prac_key; print_r($skill_arr); echo "</pre>"; die;
									if(!empty($skill_arr) && count($skill_arr)>0) {
										foreach($skill_arr as $skill_key=>$resrc_type_arr) {
											if(!empty($resrc_type_arr) && count($resrc_type_arr)>0) {
												foreach($resrc_type_arr as $resrc_type_key=>$resrc_data) {
													if(!empty($resrc_data) && count($resrc_data)>0) {
														foreach($resrc_data as $resrc_name=>$recval_data) {
															$resource_name 	= $resrc_name;
															$emp_name 		= $user_data[$resrc_name]['emp_name'];
															$max_hours 		= $user_data[$resrc_name]['max_hours'];
															$dept_name 		= $user_data[$resrc_name]['dept_name'];
															$prac_id 		= $user_data[$resrc_name]['prac_id']; 
															if(count($recval_data)>0 && !empty($recval_data)) { 
																foreach($recval_data as $key2=>$value2) {
																	$year = $key2;
																	if(count($value2)>0 && !empty($value2)) {
																		// echo '<pre>'; print_r($value2); die;
																		foreach($value2 as $key3=>$value3) {
																			$individual_billable_hrs = 0;
																			$ts_month		 	  	 = $key3;
																			$individual_billable_hrs = $resrc_type_arr[$resrc_name][$year][$ts_month]['total_hours'];
																			if(is_array($value3) && count($value3)>0 && !empty($value3)) {
																				foreach($value3 as $pjt_code=>$value4) {
																					$duration_hours			 = $value4['duration_hours'];
																					$rate				 	 = $value4['rateperhr'];
																					$direct_rateperhr	 	 = $value4['direct_rateperhr'];
																					$rate1 					 = $rate;
																					$direct_rateperhr1 		 = $direct_rateperhr;
																					if($individual_billable_hrs>$max_hours) {
																						$percentage 		= ($max_hours/$individual_billable_hrs);
																						$rate1 				= number_format(($percentage*$direct_rateperhr),2);
																						$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
																					}
																					if($prac_id == 0) {
																						$direct_rateperhr1  = $direct_rateperhr;
																					}
																					/*calc*/
																					$rateHour = $duration_hours * $direct_rateperhr1;
																					
																					//hour;
																					if(isset($tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['hour'])) {
																						$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['hour'] += $duration_hours;
																					} else {
																						$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['hour']  = $duration_hours;
																					}
																					//cost
																					if(isset($tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['cost'])) {
																						$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][$pjt_code][$emp_name]['cost'] += $rateHour;
																					} else {
																						$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['cost'] = $rateHour;
																					}
																					//direct_cost
																					if(isset($tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['directcost'])) {
																						$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['directcost'] += $rateHour;
																					} else {
																						$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['directcost'] = $rateHour;
																					}
																					
																					//total
																					$tot_hour 		= $tot_hour + $duration_hours;
																					$tot_cost 		= $tot_cost + $rateHour;
																					$tot_directcost = $tot_directcost + $rateHour;
																					
																					//cost
																					$cost_arr[$emp_name] 		= $rateHour;
																					$directcost_arr[$emp_name] 	= $rateHour;
																				}
																				
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		// echo '<pre>'; print_r($other_cost_arr); die;
		//other cost
		if(!empty($other_cost_arr)) {
			foreach($other_cost_arr as $oc_pjt_code=>$oc_pjtArr) {
				if(!empty($oc_pjtArr) && count($oc_pjtArr)>0) {
					foreach($oc_pjtArr as $ocYrKey=>$ocYrArr) {
						if(!empty($ocYrArr) && count($ocYrArr)>O) {
							foreach($ocYrArr as $ocMonthKey=>$ocArrDet) {
								if(!empty($ocArrDet) && count($ocArrDet)>O) {
									foreach($ocArrDet as $no=>$ocArrRow) {
										$oc_entity_key 	= $ocArrRow['oc_entity'];
										$oc_dept_key 	= $ocArrRow['oc_dept'];
										$oc_prac_key 	= $ocArrRow['oc_practice'];
										$oc_mon_yr 		= substr($ocMonthKey,0,3).' '.$ocYrKey;
										$oc_other_cost_resrc_type = 'Other Cost';
										$tbl_data[$oc_entity_key][$oc_dept_key][$oc_prac_key]['oc_skill'][$oc_other_cost_resrc_type][$oc_mon_yr][$oc_pjt_code][$ocArrRow['oc_descrptn']]['cost'] = $ocArrRow['oc_val'];
									}
								}
							}
						}
					}
				}
			}
		}
		$res = $this->exportCRXls($tbl_data, $project_master);
		// echo "<pre>"; print_r(); die;
		// $this->load->view("projects/cost_report_view", $data);
	}
	
	public function exportCRXls($tbl_data, $project_master)
	{
		if(!empty($tbl_data) && count($tbl_data)>0) {
			
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Cost Report');
			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', 'ENTITY');
			$this->excel->getActiveSheet()->setCellValue('B1', 'DEPARTMENT');
			$this->excel->getActiveSheet()->setCellValue('C1', 'PRACTICE');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SKILL');
			$this->excel->getActiveSheet()->setCellValue('E1', 'RESOURCE TYPE');
			$this->excel->getActiveSheet()->setCellValue('F1', 'MONTH YEAR');
			$this->excel->getActiveSheet()->setCellValue('G1', 'PROJECT');
			$this->excel->getActiveSheet()->setCellValue('H1', 'RESOURCE');
			$this->excel->getActiveSheet()->setCellValue('I1', 'HOUR');
			$this->excel->getActiveSheet()->setCellValue('J1', 'COST');
			
			$this->excel->getActiveSheet()->getStyle('A1:J1')->getFont()->setSize(10);
			$i=2;
			
			foreach($tbl_data as $entiyKey=>$entiyArr) {
				if(!empty($entiyArr) && count($entiyArr)>0) {
					foreach($entiyArr as $deptKey=>$deptArr) {
						if(!empty($deptArr) && count($deptArr)>0) {
							foreach($deptArr as $pracKey=>$pracArr) {
								if(!empty($pracArr) && count($pracArr)>0) {
									foreach($pracArr as $skilKey=>$skilArr) {
										if(!empty($skilArr) && count($skilArr)>0) {
											foreach($skilArr as $resrcTypeKey=>$resrcTypeArr) {
												if(!empty($resrcTypeArr) && count($resrcTypeArr)>0) {
													foreach($resrcTypeArr as $yrMonKey=>$yrMonArr) {
														if(!empty($yrMonArr) && count($yrMonArr)>0) {
															foreach($yrMonArr as $pjtCdeKey=>$pjtCdeArr) {
																if(!empty($pjtCdeArr) && count($pjtCdeArr)>0) {
																	foreach($pjtCdeArr as $resrcNmeKey=>$resrcNmeArr) {
																		$tempSkilKey 	 = $skilKey;
																		$tempresrcNmeKey = $resrcNmeKey;
																		$tempResrcHour 	 = round($resrcNmeArr['hour'], 1);
																		$tempCls		 = '';
																		if('Other Cost'==$resrcTypeKey) {
																			$tempSkilKey 	 = $tempResrcHour = '-';
																			$tempCls	 	 = 'tr_othercost';
																			$tot_cost	    += $resrcNmeArr['cost'];
																		}
																		
																		$pjt_nme = isset($project_master[$pjtCdeKey]) ? $project_master[$pjtCdeKey] : $pjtCdeKey;
																		$this->excel->getActiveSheet()->setCellValue('A'.$i, $entiyKey);
																		$this->excel->getActiveSheet()->setCellValue('B'.$i, $deptKey);
																		$this->excel->getActiveSheet()->setCellValue('C'.$i, $pracKey);
																		$this->excel->getActiveSheet()->setCellValue('D'.$i, $tempSkilKey);
																		$this->excel->getActiveSheet()->setCellValue('E'.$i, $resrcTypeKey);
																		$this->excel->getActiveSheet()->setCellValue('F'.$i, $yrMonKey);
																		$this->excel->getActiveSheet()->setCellValue('G'.$i, $pjt_nme);
																		$this->excel->getActiveSheet()->setCellValue('H'.$i, $tempresrcNmeKey);
																		$this->excel->getActiveSheet()->setCellValue('I'.$i, $tempResrcHour);
																		$this->excel->getActiveSheet()->setCellValue('J'.$i, round($resrcNmeArr['cost'], 2));
																		$overall_hour	+= $tempResrcHour;
																		$overall_cost	+= round($resrcNmeArr['cost'], 2);
																		$i++;
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$this->excel->getActiveSheet()->getStyle('I2:I'.$i)->getNumberFormat()->setFormatCode('0.00');
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
		
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		
		$this->excel->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
		$filename='cost_report_'.time().'.xls'   ; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		$objWriter->save('php://output');
	}
	
	
	/*
	* Cost Report
	* Based on timesheet data(Like timesheet utilization report)
	*/
	public function cost_report_new()
	{
		$varSessionId = $this->userdata['userid'];
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$dept   			  = array();
		$data['page_heading'] = "IT Cost Report";
		$data['filter_area_status'] = $this->input->post("filter_area_status");
		
		$start_date = date("Y-m-1");
		$end_date   = date("Y-m-d");
		// $start_date = date("Y-m-d", strtotime('01-04-2017'));
		// $end_date   = date("Y-m-d", strtotime('30-04-2017'));
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			}
		}
		if($this->input->post("month_year_to_date")) {
			$end_date = $this->input->post("month_year_to_date");
			$end_date = date("Y-m-t",strtotime($end_date));	
		}
		
		$resource_type  = $this->input->post("resource_type");
		$department_ids = $this->input->post("department_ids");
		$practice_ids   = $this->input->post("practice_ids");
		$dept_type      = $this->input->post("dept_type");
		$skill_ids 		= $this->input->post("skill_ids");
		$member_ids		= $this->input->post("member_ids");
		$entity_ids 	= $this->input->post("entity_ids");
		$project_ids   = $this->input->post("project_ids");
		
		$this->db->select('t.dept_id, t.dept_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr, t.entity_id, t.entity_name, l.practice as practice_id, p.practices as practice_name');
		$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
		$this->db->join($this->cfg['dbpref']. 'leads as l', 'l.pjt_id = t.project_code', 'LEFT');
		$this->db->join($this->cfg['dbpref']. 'practices as p', 'p.id = l.practice');
		$this->db->where("t.resoursetype !=", '');
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."')", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."')", NULL, FALSE);
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")!=1) {
			$this->db->where_not_in("t.project_code", array('Leave'));
			$data['exclude_leave'] = 1;
			$data['filter_area_status'] = 1;
		}
		if(($this->input->post("exclude_holiday")==1) && $this->input->post("exclude_leave")!=1) {
			$this->db->where_not_in("t.project_code", array('HOL'));
			$data['exclude_holiday'] = 1;
			$data['filter_area_status'] = 1;
		}
		if(($this->input->post("exclude_leave")==1) && $this->input->post("exclude_holiday")==1) {
			$this->db->where_not_in("t.project_code", array('HOL','Leave'));
			$data['exclude_leave']   = 1;
			$data['exclude_holiday'] = 1;
		}
		if(!empty($entity_ids) && count($entity_ids)>0) {
			$data['entity_ids'] = $entity_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('t.entity_id', $entity_ids);
		}
		if(!empty($practice_ids) && count($practice_ids)>0) {
			$data['sel_practice_ids'] = $practice_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('l.practice', $practice_ids);
		}
		if(!empty($project_ids) && count($project_ids)>0) {
			$data['sel_project_ids'] = $project_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('t.project_code', $project_ids);
		}
		if(count($department_ids)>0 && !empty($department_ids)) {
			$data['department_ids'] = $department_ids;
			$data['filter_area_status'] = 1;
			$dids = implode(",",$department_ids);
			if(!empty($dids)) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
		} else {
			$deptwhere = "t.dept_id IN ('10','11')";
			$this->db->where($deptwhere);
		}
		if(count($skill_ids)>0 && !empty($skill_ids)) {
			$data['skill_ids'] = $skill_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('t.skill_id', $skill_ids);
		}
		if(count($member_ids)>0 && !empty($member_ids)) {
			$data['member_ids'] = $member_ids;
			$data['filter_area_status'] = 1;
			$this->db->where_in('t.username', $member_ids);
		}
		$this->db->where('l.practice is not null');
		/* Checking Admin,Management */
		if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '4')) 
		{
		   //No restriction
		}
		else
		{
			// $this->db->where("(l.assigned_to = '".$varSessionId."' OR l.lead_assign = '".$varSessionId."' OR l.belong_to = '".$varSessionId."')");
			$wh_condn = ' (l.belong_to = '.$varSessionId.' OR l.assigned_to ='.$varSessionId.' OR FIND_IN_SET('.$varSessionId.', l.lead_assign)) ';
			$this->db->where($wh_condn);
		}
		$this->db->where("l.lead_status", 4);
		$query 						= $this->db->get();		
		// echo $this->db->last_query(); exit;
		$data['resdata'] 	   		= $query->result();
		
		$data['heading'] 	   		= $heading;
		$data['dept_type']     		= $dept_type;
		$data['resource_type'] 		= $resource_type;
		$data['conversion_rates'] 	= $this->get_currency_rates();
		
		// get all projects from timesheet
		$timesheet_db = $this->load->database("timesheet", true);
		$proj_mas_qry = $timesheet_db->query("SELECT DISTINCT(project_code), title FROM ".$timesheet_db->dbprefix('project')." ");
		if($proj_mas_qry->num_rows()>0){
			$project_res = $proj_mas_qry->result();
		}
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec)
			$project_master[$prec->project_code] = $prec->title;
		}
		$data['project_master']  = $project_master;
		
		/* $this->db->select('department_id, department_name');
		$this->db->where_in('department_id', array('10','11'));
		$dept = $this->db->get($timesheet_db->dbprefix . 'department');
		$data['departments'] = $dept->result(); */
		
		$depts_res = array();
		$dept = $timesheet_db->query("SELECT department_id, department_name FROM ".$timesheet_db->dbprefix('department')." where department_id IN ('10','11') ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		
		$timesheet_db->close();
		
		if(!empty($data['departments']) && count($data['departments'])>0) {
			$this->db->select('t.skill_id, t.skill_name as name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($department_ids) && count($department_ids)>0) {
				$this->db->where_in("t.dept_id", $department_ids);
			}
			if(!empty($practice_ids) && count($practice_ids)>0) {
				$this->db->where_in('t.practice_id', $practice_ids);
			}
			$this->db->group_by('t.skill_id');
			$this->db->order_by('t.skill_name');
			$skquery = $this->db->get();
			$data['skill_ids_selected'] = $skquery->result();
		}
		
		//if(!empty($department_ids) || !empty($practice_ids) || !empty($entity_ids)) {
		if($this->input->post())
		{
			
			$this->db->select('t.project_code, p.lead_title as project_name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
			$this->db->join($this->cfg['dbpref'].'leads as p', 'p.pjt_id = t.project_code');
			$this->db->where("t.practice_id !=", 0);
			if(!empty($department_ids))
			$this->db->where_in("t.dept_id", $department_ids);
			if(!empty($practice_ids))
			$this->db->where_in("t.practice_id", $practice_ids);
			if(!empty($entity_ids))
			$this->db->where_in("p.division", $entity_ids);
			//Checking Admin,Management
			if (($this->userdata['role_id'] == '1' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '2' && $this->userdata['level'] == '1') || ($this->userdata['role_id'] == '4')) 
			{
			   //No restriction
			}
			else
			{
				// $this->db->where("(p.assigned_to = '".$varSessionId."' OR p.lead_assign = '".$varSessionId."' OR p.belong_to = '".$varSessionId."')");
				$wh_condn = ' (p.belong_to = '.$varSessionId.' OR p.assigned_to ='.$varSessionId.' OR FIND_IN_SET('.$varSessionId.', p.lead_assign)) ';
				$this->db->where($wh_condn);
			}
			$this->db->where("p.lead_status", 4);
			$this->db->group_by('t.project_code');
			$this->db->order_by('project_name');
			$proj_query = $this->db->get();
			$data['project_ids'] = $proj_query->result();
			
		}	
		//}
		
		if(!empty($data['member_ids']) && count($data['member_ids'])>0) {
			$this->db->select("t.empname as emp_name, t.username");
			$this->db->from($this->cfg['dbpref']. 'timesheet_month_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($department_ids) && count($department_ids)>0) {
				$this->db->where_in("t.dept_id", $department_ids);
			}				
			if(!empty($practice_ids) && count($practice_ids)>0) {
				$this->db->where_in('t.practice_id', $practice_ids);
			}
			/* if(!empty($data['member_ids'])) {
				$this->db->where_in("t.skill_id", $data['member_ids']);
			} */
			$this->db->group_by('t.empname');
			$mem_qry = $this->db->get();
			$data['member_ids_selected'] = $mem_qry->result();
		}
		
		//get other costs
		$data['other_cost_arr']   = $this->dashboard_model->getOtherCosts($start_date, $end_date, $entity_ids, $practice_ids);

		//for practices		
		$data['practice_ids'] 	  = $this->get_default_practices($start_date, $end_date);
		$data['entitys'] 	  	  = $this->dashboard_model->get_entities();

		$data['start_date'] 	  = $start_date;
		$data['end_date']   	  = $end_date;
		$data['results']    	  = $arr_depts;

		// echo "<pre>"; print_r($data); die;
		$this->load->view("projects/cost_report_view_new", $data);
	}
	
	private function get_default_practices($start_date, $end_date)
	{
		$practice_not_in_array = array('6','7','8');
		$this->db->select('id, practices');
		$this->db->from($this->cfg['dbpref']. 'practices');
		$this->db->where("status !=", 0);
		$this->db->where_not_in("id", $practice_not_in_array);
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		return $query->result();
	}
	
	public function revenue_cost()
	{
		$data['previous_year'] = date("Y",strtotime("-1 year"))."-".date("Y");  //last year "2013"
		$data['current_year'] = date("Y")."-".date("Y",strtotime("+1 year"));  //last year "2013"
		
		$timesheet_db = $this->load->database("timesheet", true);
		$depts_res = array();
		$dept = $timesheet_db->query("SELECT department_id, department_name FROM ".$timesheet_db->dbprefix('department')."  ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		
		$months = array('1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
		/* $results = array('revenue'=>'1000','offshore_revenue'=>'2000','total_cost'=>'3000','offshore_cost'=>'4000','contribution'=>'5000','revnue_prev'=>'6000','offshore_revenue_prev'=>'7000','total_cost_prev'=>'8000','offshore_cost_prev'=>'9000','contribution_prev'=>'10000','saving'=>'11000'); */
		
		$start_month=4;
		$end_month=6;
		$practice_ids = '';
		
		if($this->input->post("start_month")) {
			$start_month = $this->input->post("start_month");
		}
		if($this->input->post("end_month")) {
			$end_month = $this->input->post("end_month");
		}
		if($this->input->post("practice_ids")) {
			$practice_ids = $this->input->post("practice_ids");
		}
		for($i=$start_month;$i<=$end_month;$i++)
		{
			if($i<9){$month_key="0".$i;}
			else{$month_key=$i;}
			$start_date_prev = date("Y",strtotime("-1 year"))."-".$month_key."-01";
			$end_date_prev = date("Y",strtotime("-1 year"))."-".$month_key."-".date('t');
			
			$revenue_results_prev = $this->findRevenue($start_date_prev,$end_date_prev,$practice_ids);
			// echo "<pre>";print_r($revenue);exit;
			$result_set[$months[$i]]['revenue_prev'] = $revenue_results_prev['revenue'];
			$result_set[$months[$i]]['total_cost_prev'] = $revenue_results_prev['total_cost'];
			
			$start_date = date('Y')."-".$month_key."-01";
			$end_date = date('Y')."-".$month_key."-".date('t');
			
			$revenue_results = $this->findRevenue($start_date,$end_date,$practice_ids);
			
			$result_set[$months[$i]]['revenue'] = $revenue_results_prev['revenue'];
			$result_set[$months[$i]]['total_cost'] = $revenue_results_prev['total_cost'];
		}
		// echo "<pre>";print_r($result_set);exit;
		$data['results'] = $result_set;
		$data['start_month'] = $start_month;
		$data['end_month'] = $end_month;
		$data['end_month'] = $end_month;
		$data['department_ids'] = $this->input->post("department_ids");
		$data['sel_practice_ids']   = $this->input->post("practice_ids");
		$data['filter_area_status']   = $this->input->post("filter_area_status");
		$data['practice_ids'] 	  = $this->get_default_practices($start_date, $end_date);
		
		$this->load->view("projects/revenue_cost_view", $data);
	}
	
	public function findRevenue($start_date,$end_date,$practice_ids)
	{
		@set_time_limit(-1); //disable the mysql query maximum execution time
			
		$data  				  = array();
			
		$bk_rates = get_book_keeping_rates();

		$project_status = 1;
		
		$project_code = array();
		$projects 	  = array();
		$practice_arr = array();

		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		$practice_not_in = array(6);
		$this->db->where_not_in('p.id', $practice_not_in);
		if($practice_ids!='')
		{
			$this->db->where_in('p.id', $practice_ids);
		}
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();		
				
		if(!empty($pres) && count($pres)>0){
			foreach($pres as $prow) {
				$practice_arr[$prow->id] = $prow->practices;
				$practice_array[] = $prow->practices;
			}
		}
		
		//for othercost projects
		$this->db->select("pjt_id, lead_id, practice, lead_title");
		$this->db->from($this->cfg['dbpref']. 'leads');
		$this->db->where('pjt_id !=', '');
		$this->db->where('practice !=', '');
		$this->db->where('practice !=', 6);
		if($practice_ids!='')
		{
			$this->db->where_in('practice', $practice_ids);
		}
		/* $ocres  = $this->db->get_where($this->cfg['dbpref']."leads", array("pjt_id !=" => '',"practice !=" => '', "practice !=" => 6)); //for temporary use */
		$ocres = $this->db->get();
		$oc_res = $ocres->result_array();
		
		if(!empty($oc_res)) {
			foreach($oc_res as $ocrow) {
				if (isset($projects['othercost_projects'][$practice_arr[$ocrow['practice']]])) {
					$projects['othercost_projects'][$practice_arr[$ocrow['practice']]][] = $ocrow['lead_id'];
				} else {
					$projects['othercost_projects'][$practice_arr[$ocrow['practice']]][] = $ocrow['lead_id'];
				}
			}
		}
		
		//need to calculate for the total IR
		$this->db->select('sfv.job_id, sfv.type, sfv.milestone_name, sfv.for_month_year, sfv.milestone_value, cc.company, c.customer_name, l.lead_title, l.expect_worth_id, l.practice, l.pjt_id, enti.division_name, enti.base_currency, ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid  = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid  = c.company_id');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id  = l.division');
		$this->db->join($this->cfg['dbpref'].'expect_worth as ew', 'ew.expect_worth_id = l.expect_worth_id');
		$this->db->where("sfv.type", 'A');
		//BPO practice are not shown in IT Services Dashboard
		// $this->db->where_not_in("l.practice", 6);
		$practice_not_in = array(6);
		$this->db->where_not_in('l.practice', $practice_not_in);
		if($practice_ids!='')
		{
			$this->db->where_in('l.practice', $practice_ids);
		}
		if(!empty($start_date)) {
			$this->db->where("sfv.for_month_year >= ", date('Y-m-d H:i:s', strtotime($start_date)));
		}
		if(!empty($end_date)) {
			$this->db->where("sfv.for_month_year <= ", date('Y-m-d H:i:s', strtotime($end_date)));
		}
		
		$query1 = $this->db->get();
		// echo $this->db->last_query(); exit;
		$invoices_data = $query1->result_array();
		
		if(!empty($invoices_data) && count($invoices_data)>0) {
			foreach($invoices_data as $ir) {
				if($practice_arr[$ir['practice']] == 'Testing' || $practice_arr[$ir['practice']] == 'Infra Services'){
					$practice_arr[$ir['practice']] = 'Others';
				}
				$base_conver_amt = $this->conver_currency($ir['milestone_value'], $bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				$projects['irval'][$practice_arr[$ir['practice']]] += $this->conver_currency($base_conver_amt,$bk_rates[$this->calculateFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$this->default_cur_id]);
			}
		}
	
		
		

		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname, t.direct_cost_per_hour, t.resource_duration_direct_cost,t.entry_month as month_name, t.entry_year as yr');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.pjt_id = t.project_code', 'left');
	 
		if(!empty($start_date) && !empty($end_date)) {
			$this->db->where("t.start_time >= ", date('Y-m-d', strtotime($start_date)));
			$this->db->where("t.start_time <= ", date('Y-m-d', strtotime($end_date)));
		}
		$excludewhere = "t.project_code NOT IN ('HOL','Leave')";
		$this->db->where($excludewhere);
		$resrc = 't.resoursetype IS NOT NULL';
		$this->db->where($resrc);
		$deptwhere = "t.dept_id IN ('10','11')";
		$this->db->where($deptwhere);
		$this->db->where("l.practice is not null");
		$query 	 = $this->db->get();		
		$resdata = $query->result();
		
		## code starts here##
		$tbl_data = array();
		$sub_tot  = array();
		$cost_arr = array();
		$directcost_arr = array();
		$usercnt  = array();
		$prjt_hr  = array();
		$prjt_cst = array();
		$prjt_directcst = array();
		$prac = array();
		$dept = array();
		$skil = array();
		$proj = array();
		$tot_hour = 0;
		$tot_cost = 0;
		$tot_directcost = 0;		
		$timesheet_data = array();
		$resource_cost = array();	
		
		if(count($resdata)>0) {
			$rates = $this->get_currency_rates();
			foreach($resdata as $rec) {		
				$financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
				$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id,$financialYear);
				
				$timesheet_data[$rec->username]['practice_id'] 	= $rec->practice_id;
				$timesheet_data[$rec->username]['max_hours'] 	= $max_hours_resource->practice_max_hours;
				$timesheet_data[$rec->username]['dept_name'] 	= $rec->dept_name;
				
				$rateCostPerHr 		 = round($rec->cost_per_hour*$rates[1][$this->default_cur_id], 2);
				$directrateCostPerHr = round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
				$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr']        = $rateCostPerHr;
			}

			if(count($timesheet_data)>0 && !empty($timesheet_data)) {
				foreach($timesheet_data as $key1=>$value1) {
					$resource_name = $key1;
					$max_hours = $value1['max_hours'];
					$dept_name = $value1['dept_name'];
					$resource_cost[$resource_name]['dept_name'] = $dept_name;
					if(count($value1)>0 && !empty($value1)){
						foreach($value1 as $key2=>$value2) {
							$year = $key2;
							if(count($value2)>0 && !empty($value2)){
								foreach($value2 as $key3=>$value3) {
									$individual_billable_hrs = 0;
									$ts_month		 	  	 = $key3;
									if(count($value3)>0 && !empty($value3)){
										foreach($value3 as $key4=>$value4) {
											if($key4 != 'total_hours'){ 
												$individual_billable_hrs = $value3['total_hours'];
												$duration_hours			 = $value4['duration_hours'];
												$rate				 	 = $value4['rateperhr'];
												$direct_rateperhr	 	 = $value4['direct_rateperhr'];
												$rate1 					 = $rate;
												$direct_rateperhr1 		 = $direct_rateperhr;
												if($individual_billable_hrs>$max_hours) {
													$percentage 		= ($max_hours/$individual_billable_hrs);
													$rate1 				= number_format(($percentage*$direct_rateperhr),2);
													$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
												}
												$resource_cost[$resource_name][$year][$ts_month][$key4]['duration_hours'] += $duration_hours;
												$resource_cost[$resource_name][$year][$ts_month][$key4]['total_cost'] 	  += ($duration_hours*$direct_rateperhr1);
												$resource_cost[$resource_name][$year][$ts_month][$key4]['practice_id'] 	   = ($duration_hours*$rate1);
												$resource_cost[$resource_name][$year][$ts_month][$key4]['total_dc_cost']  += ($duration_hours*$direct_rateperhr1);
											}
										}
									}
								}
							}
						}
					}
				}	 
			}
		}
		if(count($resource_cost)>0 && !empty($resource_cost)){
			foreach($resource_cost as $resourceName => $array1){
				$dept_name = $resource_cost[$resourceName]['dept_name'];
				if(count($array1)>0 && !empty($array1)){
					foreach($array1 as $year => $array2){
						if($year !='dept_name'){
							if(count($array2)>0 && !empty($array2)){
								foreach($array2 as $rs_month => $array3){
									$duration_hours = 0;
									$total_cost = 0;
									$total_dc_cost = 0;
									foreach($array3 as $project_code => $array4){
										$duration_hours = $array4['duration_hours'];
										$total_cost 	= $array4['total_cost'];
										$total_dc_cost 	= $array4['total_dc_cost'];
										$directcost1[$project_code]['project_total_direct_cost'] += $total_cost;
									}
								}
							}
						}
					}
				}
			}
		}
		
		$this->db->select("pjt_id,practice,lead_title");
		$res = $this->db->get_where($this->cfg['dbpref']."leads",array("pjt_id !=" => '',"practice !=" => ''));
		$project_res = $res->result();
		$project_master = array();
		if(!empty($project_res)){
			foreach($project_res as $prec){
				$directcost2[$practice_arr[$prec->practice]][$prec->pjt_id]['total_direct_cost'] += $directcost1[$prec->pjt_id]['project_total_direct_cost'];
			}
		}
		
		foreach($directcost2 as $practiceId => $val1){
			if(!empty($practiceId)) {
				if($practiceId == 'Testing' || $practiceId == 'Infra Services') {
					$practiceId = 'Others';
				}
				foreach($val1 as $pjtCode => $val){				
					$directcost[$practiceId]['total_direct_cost'] += $val['total_direct_cost'];
				}
			}
		}
		
		$projects['direct_cost']    = $directcost;
		$ins_array = array();
		
		if(!empty($practice_array)){
			foreach($practice_array as $parr){
				/**other cost data*/
				$other_cost_val 	= 0;
				if(isset($projects['othercost_projects']) && !empty($projects['othercost_projects'][$parr]) && count($projects['othercost_projects'][$parr])>0) {
					foreach($projects['othercost_projects'][$parr] as $pro_id) {
						$val 	= getOtherCostByLeadIdByDateRange($pro_id, $this->default_cur_id, $start_date, $end_date);
						$other_cost_val    += $val;
					}
					$projects['other_cost'][$parr] = $other_cost_val;
				}
				
				$ytd_billing   += ($projects['irval'][$parr] != '') ? round($projects['irval'][$parr]) : '-';
				
				$temp_ytd_utilization_cost = $projects['direct_cost'][$parr]['total_direct_cost'] + $projects['other_cost'][$parr];
				$ytd_utilization_cost += ($temp_ytd_utilization_cost != '') ? round($temp_ytd_utilization_cost) : '-';
				
				$results['revenue']=$ytd_billing;
				$results['total_cost']=$ytd_utilization_cost;
			}
		
		}
		return $results;
	}
}
/* End of dms resource_availability file */
