<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
class Dashboard extends crm_controller 
{
	function Dashboard()
	{
		parent::__construct();
		$this->login_model->check_login();
        $this->load->helper('custom');
        $this->load->library('validation');
		$this->load->helper('custom_helper');
		$this->load->model('projects/dashboard_model');
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
	
	function index()
	{
		$data  				  = array();
		$dept   			  = array();
		$data['page_heading'] = "Project Dashboard";
		
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
			if(count($skill_ids)>0 && !empty($skill_ids)){
				$data['skill_ids'] = $skill_ids;
				$where .= " and skill_id in ($sids)";
			}
			
			if($this->input->post("department_ids")) {
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
		if(count($skill_ids)>0 && !empty($skill_ids) && count($department_ids)>0 && !empty($department_ids)) {
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
		$json = '';
		
		$getITDataQry = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code
		FROM crm_timesheet_data 
		WHERE start_time between '$start_date' and '$end_date' $where";
		
		// echo $getITDataQry; exit;
		$sql = $this->db->query($getITDataQry);
		$data['resdata'] = $sql->result();
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
		$this->load->view("projects/project_dashboard", $data);
	}
	
	function trend_analysis()
	{
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
				$where .= " and skill_id in ($sids)";
			}
		}

		$member_ids = $this->input->post("member_ids");
		if(count($member_ids)>0 && !empty($member_ids)) {
			$mids = "'".implode("','",$member_ids)."'";
			if(count($member_ids)>0 && !empty($member_ids)) {
				$data['member_ids'] = $member_ids;
				$where .= " and username in ($mids)";
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
		
		$getITDataQry = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost, project_code, start_time, end_time
		FROM crm_timesheet_data 
		WHERE start_time between '$start_date' and '$end_date' $where";
		
		// echo $getITDataQry; exit;
		$sql = $this->db->query($getITDataQry);
		$data['resdata'] = $sql->result();
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
		
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname');
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
		
		$this->db->select('t.dept_id, t.dept_name, t.practice_id, t.practice_name, t.skill_id, t.skill_name, t.resoursetype, t.username, t.duration_hours, t.resource_duration_cost, t.cost_per_hour, t.project_code, t.empname');
		$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
		$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
		$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
		if(!empty($resource_type))
		$this->db->where('t.resoursetype', $resource_type);
		if(!empty($department_ids)) {
			$dids = @explode(",",$department_ids);
			$this->db->where_in("t.dept_id", $dids);
		}
		if(!empty($practice_ids)) {
			$pids = explode(',', $practice_ids);
			$this->db->where_in("t.practice_id", $pids);
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
				echo $this->db->last_query();
				echo "<br>";
			}
		}
	}
	
	function get_practices()
	{
		// echo "projects"; exit;
		if($this->input->post("dept_ids")){
			$ids 		= $this->input->post("dept_ids");
			$start_date = $this->input->post("start_date");
			$end_date   = $this->input->post("end_date");
			
			$this->db->select('t.practice_id, t.practice_name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			// $this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			// $this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(!empty($ids))
			$this->db->where_in("t.dept_id", $ids);
			$this->db->group_by('t.practice_id');
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
			if(!empty($dids)) {
				$where .= 'and v.department_id in ('.$dids.')';
			}
			if(!empty($sids)) {
				$where .= 'and v.skill_id in ('.$sids.')';
			}
			
			if(!empty($sids)) {
				$timesheet_db = $this->load->database("timesheet",true);
				$qry = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where t.start_time between '$start_date' and '$end_date' ".$where." group by v.username order by v.username asc");
				// echo $qry; exit;
				$timesheet_db->close();
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
	
	public function service_dashboard()
	{
		$data  				  = array();
		$data['page_heading'] = "IT Services Dashboard";
		
		// echo "<pre>"; print_R($this->input->post());
		
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date   = $curFiscalYear."-03-31"; //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			/* if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			} */
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
		// echo $month;  die;
		$project_status = 1;
		if($this->input->post("project_status") && ($this->input->post("project_status")!='null')) {
			$project_status = @explode(',', $this->input->post("project_status"));
			/* if(count($project_status) == 2){
				$project_status = '';
			} else {
				$project_status = $this->input->post("project_status");
			} */
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
		
		if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
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
		}
		
		$this->db->select('l.lead_id, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.division');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
		if($project_status){
			$this->db->where_in("l.pjt_status", $project_status);
		}
		if($division){
			$this->db->where_in("l.division", $division);
		}
		
		if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
			$this->db->where_in('l.lead_id', $result_ids);
		}
		
		// $this->db->limit('10');
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$res = $query->result_array();
		
		// echo "<pre>"; print_r($res); die;
		
		if(!empty($res) && count($res)>0) {
			foreach($res as $row) {
				// $projects['project'][$practice_arr[$row['practice']]][] = $row['pjt_id'];
				$base_currency = $base_cur_arr[$row['division']];
				$timesheet = array();
				if(!empty($row['pjt_id']))
				$timesheet = $this->get_timesheet_data($row['pjt_id'], $start_date, $end_date);
				
				if (isset($projects['practicewise'][$practice_arr[$row['practice']]])) {
					$projects['practicewise'][$practice_arr[$row['practice']]] += 1;
					//need to calculate for the total IR
					$projects['irval'][$practice_arr[$row['practice']]] += $this->get_ir_val($row['lead_id'], $row['expect_worth_id'], '', $start_date, $end_date, $base_currency);
					//need to calculate for the current month IR
					$curmonth_ir = $this->get_ir_val($row['lead_id'], $row['expect_worth_id'], $month,'','',$base_currency);
					$projects['cm_irval'][$practice_arr[$row['practice']]] += $curmonth_ir;
					//effort variance
					if(!empty($timesheet) && count($timesheet)>0){
						$projects['eff_var'][$practice_arr[$row['practice']]] += round($timesheet['total_hours'] - $row['estimate_hour']);
						$projects['dc'][$practice_arr[$row['practice']]] += round($timesheet['total_dc']);
						$projects['cm_dc'][$practice_arr[$row['practice']]] += round($curmonth_ir - $timesheet['total_dc']);
					}
				} else {
					$projects['practicewise'][$practice_arr[$row['practice']]]  = 1;  ///Initializing count
					$projects['irval'][$practice_arr[$row['practice']]] = $this->get_ir_val($row['lead_id'], $row['expect_worth_id'], '',$start_date, $end_date, $base_currency);
					$curmonth_ir = $this->get_ir_val($row['lead_id'], $row['expect_worth_id'], $month, '', '', $base_currency);
					$projects['cm_irval'][$practice_arr[$row['practice']]] = $curmonth_ir;
					if(!empty($timesheet) && count($timesheet)>0){
						$projects['eff_var'][$practice_arr[$row['practice']]] = round($timesheet['total_hours'] - $row['estimate_hour']);
						$projects['dc'][$practice_arr[$row['practice']]] = round($timesheet['total_dc']);
						$projects['cm_dc'][$practice_arr[$row['practice']]] = round($curmonth_ir - $timesheet['total_dc']);
					}
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
		// echo "<pre>"; print_r($projects); exit;
		
		if($this->input->post("filter")!="")
		$this->load->view('projects/service_dashboard_grid', $data);
		else
		$this->load->view('projects/service_dashboard', $data);
	}
	
	public function get_timesheet_data($pjt_code, $start_date, $end_date)
	{
		
		// $start_date = '2006-01-01';
		// $end_date   = date('Y-m-d');
		
		$this->db->select('ts.cost_per_hour as cost, ts.entry_month as month_name, ts.entry_year as yr, ts.emp_id, 
		ts.empname, ts.username, SUM(ts.duration_hours) as duration_hours, ts.resoursetype, ts.username, ts.empname, ts.direct_cost_per_hour as direct_cost, sum( ts.`resource_duration_direct_cost`) as duration_direct_cost, sum( ts.`resource_duration_cost`) as duration_cost');
		$this->db->from($this->cfg['dbpref'] . 'timesheet_data as ts');
		$this->db->where("ts.project_code", $pjt_code);
		$this->db->where("DATE(ts.start_time) >= ", $start_date);
		$this->db->where("DATE(ts.end_time) <= ", $end_date);
		$this->db->group_by(array("ts.username", "yr", "month_name", "ts.resoursetype"));
		
		$query = $this->db->get();
		// echo $this->db->last_query() . "<br />"; exit;
		$timesheet = $query->result_array();
		$res = array();
		if(count($timesheet)>0) {
			foreach($timesheet as $ts) {
				$res['total_cost']     += $ts['duration_cost'];
				$res['total_hours']    += $ts['duration_hours'];
				$res['total_dc'] 	   += $ts['duration_direct_cost'];
				switch($ts['resoursetype']) {
					case 'Billable':
						$res['total_billable_hrs'] = $ts['duration_hours'];
					break;
					case 'Non-Billable':
						$res['total_non_billable_hrs'] = $ts['duration_hours'];
					break;
					case 'Internal':
						$res['total_internal_hrs'] = $ts['duration_hours'];
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
		// echo "<pre>"; print_R($this->input->post()); exit;
		$curFiscalYear = $this->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date   = $curFiscalYear."-03-31"; //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');
		
		if($this->input->post("month_year_from_date")) {
			$start_date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($start_date));
			/* if($this->input->post("month_year_to_date")== "") {
				$end_date   = date("Y-m-t",strtotime($start_date));
			} */
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
		if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
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
		}
		//role based filtering
		
		$this->db->select('l.lead_id, l.lead_title, l.complete_status, l.estimate_hour, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.project_type, l.division');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", '4');
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
		if (($this->userdata['role_id'] != '1' && $this->userdata['level'] != '1') || ($this->userdata['role_id'] != '2' && $this->userdata['level'] != '1')) {
			$this->db->where_in('l.lead_id', $result_ids);
		}
		// $this->db->limit('10');
		$query = $this->db->get();
		// echo $this->db->last_query();
		$res = $query->result_array();
		
		// if(($clicktype == 'noprojects') || ($clicktype == 'rag'))
		// $data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
		// if($clicktype == 'irval')
		// $data['invoices_data'] = $this->getIRData($res, $start_date, $end_date);
		

		switch($clicktype){
			case 'noprojects':
			case 'rag':
			
				$data['projects_data'] = $this->getProjectsDataByDefaultCurrency($res, $start_date, $end_date);
			
				/* $this->load->model('project_model');
				//for field restriction
				$db_fields = $this->project_model->get_dashboard_field($this->userdata['userid']);
				if(!empty($db_fields) && count($db_fields)>0) {
					foreach($db_fields as $record) {
						$data['db_fields'][] = $record['column_name'];
					}
				} */
				$this->db->select('project_billing_type, id');
				$this->db->from($this->cfg['dbpref']. 'project_billing_type');
				$ptquery = $this->db->get();
				$data['project_type'] = $ptquery->result();	
				$this->load->view('projects/service_dashboard_projects_drill_data', $data);
			break;
			case 'irval':
				$data['invoices_data'] = $this->getIRData($res, $start_date, $end_date);
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
			case 'cmirval':
				$data['invoices_data'] = $this->getCMIRData($res, $month);
				$this->load->view('projects/service_dashboard_invoice_drill_data', $data);
			break;
		}
	}
	
	/* Change the actual worth amount to Default currency */
	public function getProjectsDataByDefaultCurrency($records, $start_date, $end_date)
	{
		// echo "<pre>"; print_r($records); exit;
		$this->load->model('project_model');
		$rates = $this->get_currency_rates();
		
		// echo "<pre>"; print_r($rates); exit;
		 
		$data['project_record'] = array();
		$i = 0;
		if (isset($records) && count($records)) :
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
				if(!empty($rec['pjt_id'])){
					// $timesheet = $this->project_model->get_timesheet_data($rec['pjt_id'], $rec['lead_id'], $bill_type=1, $metrics_date, $groupby_type=2);
					$timesheet = $this->get_timesheet_data($rec['pjt_id'], $start_date, $end_date);
				}
				
				$total_amount_inv_raised = 0;
				$invoice_amount = $this->project_model->get_invoice_total($rec['lead_id']);
				if(count($invoice_amount)>0 && !empty($invoice_amount)){
					$total_amount_inv_raised = $invoice_amount->invoice_amount+$invoice_amount->tax_price;
				}
								
				// $total_cost = $this->conver_currency($total_cost, $rates[1][$this->default_cur_id]);
				$total_amount_inv_raised = $this->conver_currency($total_amount_inv_raised, $rates[$rec['expect_worth_id']][$this->default_cur_id]);

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
				$data['project_record'][$i]['bill_hr'] 			= isset($timesheet['total_billable_hrs'])?$timesheet['total_billable_hrs']:'0';
				$data['project_record'][$i]['int_hr'] 			= isset($timesheet['total_internal_hrs'])?$timesheet['total_internal_hrs']:'0';
				$data['project_record'][$i]['nbil_hr'] 			= isset($timesheet['total_non_billable_hrs'])?$timesheet['total_non_billable_hrs']:'0';
				$data['project_record'][$i]['total_hours'] 		= isset($timesheet['total_hours'])?$timesheet['total_hours']:'0';
				$data['project_record'][$i]['total_dc_hours'] 	= isset($timesheet['total_dc_hours'])?$timesheet['total_dc_hours']:'0';
				$data['project_record'][$i]['total_amount_inv_raised'] = $total_amount_inv_raised;
				$data['project_record'][$i]['total_cost'] 		= number_format($total_cost, 2, '.', '');
				$i++;
				
			}
			// echo "<pre>"; print_r($data['project_record']); exit;
		endif;
		return $data['project_record'];
	}
	
	public function getIRData($records, $start_date, $end_date)
	{
		$bk_rates = get_book_keeping_rates();
		
		$$data = array();
		$lead_id = array();
		if(!empty($records) && count($records)>0){
			foreach($records as $lrow){
				$lead_id[] = $lrow['lead_id'];
				// $inv_val += $this->get_ir_val($lrow['lead_id'], $lrow['expect_worth_id'], '', $start_date, $end_date, $base_cur_arr[$lrow['division']]);
			}
		}
		$this->db->select('sfv.milestone_value, sfv.for_month_year, sfv.milestone_name, l.lead_title, l.lead_id, l.custid_fk, l.pjt_id, l.expect_worth_id, c.first_name, c.last_name, c.company, sd.base_currency');

		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as sd', 'sd.div_id = l.division');
		$this->db->where('sfv.type', 'A');
		if(!empty($lead_id) && count($lead_id)>0) {
			$this->db->where_in('sfv.job_id', $lead_id);
		}
		$this->db->where('DATE(sfv.for_month_year) >=', date('Y-m-d', strtotime($start_date)));
		$this->db->where('DATE(sfv.for_month_year) <=', date('Y-m-d', strtotime($end_date)));
		$query  = $this->db->get();
		// echo $this->db->last_query(); exit;
		$invoice_rec = $query->result_array();
		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoice_rec)>0) {
			foreach ($invoice_rec as $inv) {
				$data['invoices'][$i]['lead_title']		= $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 		= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 		= $inv['lead_id'];
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
		// echo "<pre>"; print_r($data); die;
		return $data;
	}
	
	public function getCMIRData($records, $month)
	{
		$bk_rates = get_book_keeping_rates();
		
		$$data = array();
		$lead_id = array();
		if(!empty($records) && count($records)>0){
			foreach($records as $lrow){
				$lead_id[] = $lrow['lead_id'];
			}
		}
		$this->db->select('sfv.milestone_value, sfv.for_month_year, sfv.milestone_name, l.lead_title, l.lead_id, l.custid_fk, l.pjt_id, l.expect_worth_id, c.first_name, c.last_name, c.company, sd.base_currency');

		$this->db->from($this->cfg['dbpref'].'view_sales_forecast_variance as sfv');
		$this->db->join($this->cfg['dbpref'].'leads as l', 'l.lead_id = sfv.job_id');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as sd', 'sd.div_id = l.division');
		$this->db->where('sfv.type', 'A');
		if(!empty($lead_id) && count($lead_id)>0) {
			$this->db->where_in('sfv.job_id', $lead_id);
		}
		$this->db->where('DATE(sfv.for_month_year) =', date('Y-m-d', strtotime($month)));
		$query  = $this->db->get();
		// echo $this->db->last_query(); exit;
		$invoice_rec = $query->result_array();
		$i = 0;
		$data['total_amt'] = 0;
		if(count($invoice_rec)>0) {
			foreach ($invoice_rec as $inv) {
				$data['invoices'][$i]['lead_title']		= $inv['lead_title'];
				$data['invoices'][$i]['pjt_id'] 		= $inv['pjt_id'];
				$data['invoices'][$i]['lead_id'] 		= $inv['lead_id'];
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
		// echo "<pre>"; print_r($data); die;
		return $data;
	}
	

}
/* End of dms resource_availability file */