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
		$this->load->model('projects/dashboard_model');
	}
	
	function index()
	{
		$data  				  = array();
		$dept   			  = array();
		$master 			  = array();
		$data['page_heading'] = "Project Dashboard";
		
		// $practices = $this->dashboard_model->get_practices();
		// echo "<pre>"; print_r($this->input->post()); exit;
		
		$master = array();
		$timesheet_db = $this->load->database("timesheet",true);
		if($this->input->post("month_year_from_date")) {
			$date = $this->input->post("month_year_from_date");
			
			$cur_month    = date("m");
			$post_month   = date('m',strtotime($date));
			
			$start_date   = date("Y-m-01",strtotime($date));
			if($cur_month == $post_month) {
				$end_date = date("Y-m-d");	
			} else {
				$end_date = date("Y-m-t",strtotime($date));	
			}
		} else {
			$start_date = date("Y-m-1");
			$end_date   = date("Y-m-d");
			// $start_date = date("2015-10-1");
			// $end_date   = date("2015-10-31");
		}
		$where = '';
		$department_ids = $this->input->post("department_ids");
		if(count($department_ids)>0 && !empty($department_ids)){
			$dids = implode(",",$department_ids);
			$data['department_ids'] = $department_ids;
			$where .= " and dept_id in ($dids)";
		} else {
			$where .= " and dept_id in ('10','11')";
		}
		
		$practice_ids = $this->input->post("practice_ids");
		if(count($practice_ids)>0 && !empty($practice_ids)){
			$pids = implode(",",$practice_ids);
			$data['practice_ids'] = $practice_ids;

			$where .= " and practice_id in ($pids)";
			
			$this->db->select('t.practice_id, t.practice_name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
			if(count($department_ids)>0 && !empty($department_ids)){
				$dids = implode(",",$department_ids);
				if(!empty($dids)) {
					$this->db->where_in("t.dept_id", $department_ids);
				}
			}
			$this->db->group_by('t.practice_id');
			$this->db->order_by('t.practice_name');
			$query = $this->db->get();
			// echo $this->db->last_query(); exit;
			if($query->num_rows()>0){
				// $res = $query->result();
				$data['practice_ids_selected'] = $query->result();
			}
		}
		
		$skill_ids = $this->input->post("skill_ids");
		if(count($department_ids)>0 && !empty($department_ids)) {
			$sids = implode(",",$skill_ids);
			if(count($skill_ids)>0 && !empty($skill_ids)){
				$data['skill_ids'] = $skill_ids;
				$where .= " and skill_id in ($sids)";
			}
			$qry = $timesheet_db->query("SELECT v.skill_id,v.name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and t.start_time between '$start_date' and '$end_date' group by
			v.skill_id order by v.name asc");
			$data['skill_ids_selected'] = $qry->result();
		}

		$member_ids = $this->input->post("member_ids");
		if(count($skill_ids)>0 && !empty($skill_ids) && count($department_ids)>0 && !empty($department_ids)){
			$mids = "'".implode("','",$member_ids)."'";
			if(count($member_ids)>0 && !empty($member_ids)){
				$data['member_ids'] = $member_ids;
				$where .= " and username in ($mids)";
			}
			$qry1 = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and v.skill_id in ($sids) and t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc");			
			$data['member_ids_selected'] = $qry1->result();			
		}		
		
		$data['date_filter'] = $start_date;
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
		$dept = $timesheet_db->query("SELECT department_id,department_name FROM ".$timesheet_db->dbprefix('department')." where department_id IN ('10','11') ");
		if($dept->num_rows()>0){
			$depts_res = $dept->result();
		}
		$data['departments'] = $depts_res;
		$timesheet_db->close();

		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;
		$data['results']    = $arr_depts;
		$this->load->view("projects/project_dashboard", $data);
	}
	
	/*
	@method - get_data()
	@for drill down data
	*/
	public function get_data()
	{
		// echo "<pre>"; print_r($this->input->post()); exit;
		if($this->input->post("month_year_from_date")) {
			$date = $this->input->post("month_year_from_date");
			
			$cur_month    = date("m");
			$post_month   = date('m',strtotime($date));
			
			$start_date   = date("Y-m-01",strtotime($date));
			if($cur_month == $post_month) {
				$end_date = date("Y-m-d");	
			} else {
				$end_date = date("Y-m-t",strtotime($date));
			}
		} else {
			$start_date = date("Y-m-1");
			$end_date   = date("Y-m-d");
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
		if(!empty($skill_ids))
		$this->db->where_in("t.skill_id", $skill_ids);
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
		if(!empty($practice_ids) && !empty($department_ids)) {
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
		$data['filter_sort_by'] = 'asc';
	
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
	
	function get_practices(){
		// echo "projects"; exit;
		if($this->input->post("dept_ids")){
			$ids 		= $this->input->post("dept_ids");
			$start_date = $this->input->post("start_date");
			$end_date   = $this->input->post("end_date");
			
			$this->db->select('t.practice_id, t.practice_name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
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
	
	function get_skills(){
		// echo "projects"; exit;
		if($this->input->post("dept_ids")){
			$ids = $this->input->post("dept_ids");
			$start_date = $this->input->post("start_date");
			$end_date = $this->input->post("end_date");
			$dids = implode(',',$ids);
			/* $dids 		= 10;
			$start_date = date("Y-m-01");
			$end_date   = date("Y-m-d"); */
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
	
	function get_skills_by_practice(){
		// echo "<pre>"; print_R($this->input->post()); exit;
		if($this->input->post("dept_ids")){
			$ids = $this->input->post("dept_ids");
			$dids = implode(',',$ids);
			$p_ids = $this->input->post("prac_id");
			$pids = implode(',',$p_ids);
			$start_date = $this->input->post("start_date");
			$end_date = $this->input->post("end_date");

			$this->db->select('t.skill_id, t.skill_name as name');
			$this->db->from($this->cfg['dbpref']. 'timesheet_data as t');
			$this->db->where("t.practice_id !=", 0);
			$this->db->where("(t.start_time >='".date('Y-m-d', strtotime($start_date))."' )", NULL, FALSE);
			$this->db->where("(t.start_time <='".date('Y-m-d', strtotime($end_date))."' )", NULL, FALSE);
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
	
	function get_members(){
		if($this->input->post("dept_ids")){
			$ids = $this->input->post("dept_ids");
			$start_date = $this->input->post("start_date");
			$end_date = $this->input->post("end_date");
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
	
	function get_skill_members(){
		if($this->input->post("dept_ids")){
			$ids = $this->input->post("dept_ids");
			$skill_ids = $this->input->post("skill_ids");
			$start_date = $this->input->post("start_date");
			$end_date = $this->input->post("end_date");
			
			$dids = implode(',',$ids);
			$sids = implode(',',$skill_ids);
			
			if(!empty($dids) && !empty($sids)){
				$timesheet_db = $this->load->database("timesheet",true);
				$qry = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and v.skill_id in ($sids) and t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc");
				if($qry->num_rows()>0){
					$res = $qry->result();
					echo json_encode($res); exit;
				}else{
					echo 0;exit;
				}
			}else{
				echo 0;exit;
			}
		}
	}
	

}
/* End of dms resource_availability file */