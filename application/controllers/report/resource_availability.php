<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
class Resource_availability extends crm_controller {
	function Resource_availability()
	{
		parent::__construct();
		$this->login_model->check_login();
        $this->load->helper('custom');
        $this->load->library('validation');
	}
	
	function index(){

		$data = array();
		$dept = array();
		$master = array();
		$data['page_heading'] = "Resource Availability";		
		// get departments from master table
		//echo '<pre>';print_r($_REQUEST);exit;
		$master = array();
		$timesheet_db = $this->load->database("timesheet",true);
		if($this->input->post("month_year_from_date")){
			$date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($date));
			$end_date = date("Y-m-t",strtotime($date));
		}else{
			$start_date = date("Y-m-01");
			$end_date = date("Y-m-t");
		}
		$where='';
		$department_ids = $this->input->post("department_ids");
		if(count($department_ids)>0 && !empty($department_ids)){
			$dids = implode(",",$department_ids);
			$data['department_ids'] = $department_ids;
			$where .= " and v.department_id in ($dids)";
		}
		
		$skill_ids = $this->input->post("skill_ids");
		if(count($department_ids)>0 && !empty($department_ids)){
			$sids = implode(",",$skill_ids);
			if(count($skill_ids)>0 && !empty($skill_ids)){
				$data['skill_ids'] = $skill_ids;
				$where .= " and v.skill_id in ($sids)";
			}
			$qry = $timesheet_db->query("SELECT v.skill_id,v.name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids)  and t.start_time between '$start_date' and '$end_date' group by
			v.skill_id order by v.name asc");			
			$data['skill_ids_selected'] = $qry->result();			
		}

		$member_ids = $this->input->post("member_ids");
		if(count($skill_ids)>0 && !empty($skill_ids) && count($department_ids)>0 && !empty($department_ids)){
			$mids = "'".implode("','",$member_ids)."'";
			if(count($member_ids)>0 && !empty($member_ids)){
				$data['member_ids'] = $member_ids;
				$where .= " and v.username in ($mids)";
			}
			$qry1 = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and v.skill_id in ($sids) and t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc");			
			$data['member_ids_selected'] = $qry1->result();			
		}		
		
		$data['date_filter'] = $start_date;
		$json = '';
		
		$callback = $_REQUEST['callback'];
		$timestamp = $_REQUEST['_'];		
		//fetch departments master from timesheet db
		$timesheet_db->order_by("department_name","asc");
		$qry = $timesheet_db->get($timesheet_db->dbprefix('department'));
		$sql_data_qry = "SELECT v.department_name, v.name, v.username,
		ah.available_hours_month,ah.available_hours_day, 
		v.status as emp_active_status , v.join_date,v.exit_date,  t.start_time, t.end_time, t.duration, if(t.resoursetype='Internal', 'Non-Billable',t.resoursetype) as resoursetype , t.proj_id,ep.title

		FROM enoah_times t 
		left join v_emp_details v on v.username=t.uid  
		left join enoah_available_hours ah on ah.dept_id=v.department_id
		left join enoah_project ep on ep.proj_id=t.proj_id
		WHERE t.start_time between '$start_date ' and '$end_date' $where
		order by v.department_name, v.name,v.username";
		//echo"<br>".$sql_data_qry; exit;
		$qry_d = $timesheet_db->query($sql_data_qry);
		$res_d = $qry_d->result();	
		$arr_depts = array();
		$arr_user_avail_set= array();
		
		// get all departments  from timesheet
		$timesheet_db->order_by("department_name","asc");
		$timesheet_db->group_by("department_name");
		$qry = $timesheet_db->get("enoah_department");
		if($qry->num_rows()>0){
			$depts_res = $qry->result();
		}
		$data['departments'] = $depts_res;
		$timesheet_db->close();
		
		foreach($res_d as $k => $v){
			if($v->name == NULL) {$v->name="NA";}
			
			if(!isset($v->available_hours_month)){
				//get from the variable executed from the very earlier query 
				$v->available_hours_month = 160;
				$v->available_hours_day=9;
			}
			
			if(!isset($arr_user_avail_set["user_avail_hours"][$v->username]["set"])){
				//$users_available_hours = $v->available_hours_month;
				$no_of_working_days = $this->getWorkingDays($start_date,$end_date);
				$users_available_hours = $no_of_working_days*$v->available_hours_day;
				
				if($v->join_date >= $start_date && $v->join_date <= $end_date ){
					// count the no. of working hours and multiply by available_hours for that department
					// this gives the summation of available hours
					$month_join_date = $v->join_date;
					$month_last_date = date("Y-m-t",strtotime($v->join_date));					
					$no_of_working_days = $this->getWorkingDays($month_join_date,$month_last_date);
					$users_available_hours = ($no_of_working_days*$v->available_hours_day);
					//echo $v->username.$no_of_working_days;exit;
				}

				if( ($v->exit_date >= $start_date && $v->exit_date <= $end_date) && $v->emp_active_status=="INACTIVE"){
					// count the no. of working hours and multiply by available_hours for that department
					// this gives the summation of available hours
					$month_start_date = date("Y-m-01",strtotime($v->exit_date));
					$month_last_working_date = $v->exit_date;					
					$no_of_working_days = $this->getWorkingDays($month_start_date,$month_last_working_date);
					$users_available_hours = ($no_of_working_days*$v->available_hours_day);
				}

				$arr_user_avail_set["user_avail_hours"][$v->username]["set"]=1;
				$arr_user_avail_set["user_avail_hours"][$v->username]["users_available_hours"]=$users_available_hours;
			}else{
				 $users_available_hours=$arr_user_avail_set["user_avail_hours"][$v->username]["users_available_hours"];
			}
			
			$arr_depts[$v->department_name]["departmentwise"][$v->resoursetype]  += ($v->duration/60);
			$arr_depts[$v->department_name]["skillwise"][$v->name][$v->resoursetype] += ($v->duration/60);
			$arr_depts[$v->department_name]["userwise"][$v->name][$v->username][$v->resoursetype] += ($v->duration/60);
			$arr_depts[$v->department_name]["skill_based_available_hours"][$v->name][$v->username] = $users_available_hours;
			$arr_depts[$v->department_name]["department_based_available_hours"][$v->username] = $users_available_hours;
			
			$arr_depts[$v->department_name]["projectwise"][$v->username][$v->title] = $v->title;
			$arr_depts[$v->department_name]["projuser"][$v->username][$v->title][$v->resoursetype] += ($v->duration/60);
		}

		foreach($arr_depts as $dep_name=>$dept_arr){
			foreach($dept_arr["department_based_available_hours"] as $dep_user_based){
				$arr_depts[$dep_name]["summation_department_based_available_hours"]+= $dep_user_based;
			}

			foreach($dept_arr["skill_based_available_hours"] as $skill_name =>$skill_user_based){
				foreach($skill_user_based as  $skill_user_based_arr2){
					$arr_depts[$dep_name][$skill_name]["summation_skill_based_available_hours"]+= $skill_user_based_arr2;
				}
			}

		//unset($arr_depts[$dep_name]["department_based_available_hours"]);
		//unset($arr_depts[$dep_name]["skill_based_available_hours"]);

		}
		//echo"<pre>";print_r($arr_depts);echo"</pre>";
		//exit;  
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['results']= $arr_depts;
		$this->load->view("resource_availability",$data);
	}
	
	function getWorkingDays($startDate, $endDate)
	{
		$begin = strtotime($startDate);
		$end   = strtotime($endDate);
		if ($begin > $end) {
			echo "startdate is in the future! <br />";

			return 0;
		} else {
			$no_days  = 0;
			$weekends = 0;
			while ($begin <= $end) {
				$no_days++; // no of days in the given interval
				$what_day = date("N", $begin);
				if ($what_day > 5) { // 6 and 7 are weekend days
					$weekends++;
				};
				$begin += 86400; // +1 day
			};
			$working_days = $no_days - $weekends;

			return $working_days;
		}
	}
	
	function get_skills(){
		if($this->input->post("dept_ids")){
			$ids = $this->input->post("dept_ids");
			$start_date = $this->input->post("start_date");
			$end_date = $this->input->post("end_date");
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
			
			$timesheet_db = $this->load->database("timesheet",true);
			$qry = $timesheet_db->query("SELECT v.username,concat(v.first_name,' ',v.last_name) as emp_name FROM `v_emp_details` v join enoah_times t on v.username=t.uid where v.department_id in ($dids) and v.skill_id in ($sids) and t.start_time between '$start_date' and '$end_date' group by v.username order by v.username asc");
			if($qry->num_rows()>0){
				$res = $qry->result();
				echo json_encode($res); exit;
			}else{
				echo 0;exit;
			}
		}
	}
	
	public function excelExport(){
		$timesheet_db = $this->load->database("timesheet",true);
		if($this->input->post("month_year_from_date")){
			$date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($date));
			$end_date = date("Y-m-t",strtotime($date));
		}else{
			$start_date = date("Y-m-01");
			$end_date = date("Y-m-t");
		}
		
		
		//fetch departments master from timesheet db
		$timesheet_db->order_by("department_name","asc");
		$qry = $timesheet_db->get($timesheet_db->dbprefix('department'));
		$sql_data_qry = "SELECT v.department_name, v.name, v.username,
		ah.available_hours_month,ah.available_hours_day, 
		v.status as emp_active_status , v.join_date,v.exit_date,  t.start_time, t.end_time, t.duration, if(t.resoursetype='Internal', 'Non-Billable',t.resoursetype) as resoursetype , t.proj_id,ep.title

		FROM enoah_times t 
		left join v_emp_details v on v.username=t.uid  
		left join enoah_available_hours ah on ah.dept_id=v.department_id
		left join enoah_project ep on ep.proj_id=t.proj_id
		WHERE t.start_time between '$start_date ' and '$end_date' $where
		order by v.department_name, v.name,v.username";
		echo"<br>".$sql_data_qry; exit;
		$qry_d = $timesheet_db->query($sql_data_qry);
		$res_d = $qry_d->result();	
		$arr_depts = array();
		$arr_user_avail_set= array();
		$timesheet_db->close();		
		
		
	}
}
/* End of dms resource_availability file */