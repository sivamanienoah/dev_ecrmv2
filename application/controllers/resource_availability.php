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
		
		if($this->input->post("month_year_from_date")){
			$date = $this->input->post("month_year_from_date");
			$start_date = date("Y-m-01",strtotime($date));
			$end_date = date("Y-m-t",strtotime($date));
		}else{
			$start_date = date("Y-m-01");
			$end_date = date("Y-m-t");
		}
		$data['date_filter'] = $start_date;
		$json = '';
		$timesheet_db = $this->load->database("timesheet",true);
		$callback = $_REQUEST['callback'];
		$timestamp = $_REQUEST['_'];		
		//fetch departments master from timesheet db
		$timesheet_db->order_by("department_name","asc");
		$qry = $timesheet_db->get($timesheet_db->dbprefix('department'));
	 
	 
		$qry_d = $timesheet_db->query("SELECT v.department_id, v.department_name,v.skill_id, v.name, v.username,t.uid,
		ah.available_hours_month,ah.available_hours_day, 
		concat(v.first_name,' ', v.last_name) as emp_name, v.status as emp_active_status , v.join_date,v.exit_date,  t.start_time, t.end_time, t.duration, if(t.resoursetype='Internal', 'Non-Billable',t.resoursetype) as resoursetype , t.proj_id

		FROM enoah_times t 
		left join v_emp_details v on v.username=t.uid
		left join enoah_available_hours ah on ah.dept_id=v.department_id
		WHERE t.start_time between '$start_date ' and '$end_date ' 
		order by v.department_name, v.name,v.username ");
		$res_d = $qry_d->result();	
		$arr_depts = array();
		$arr_user_avail_set= array();
		
		foreach($res_d as $k => $v){
			if($v->name == NULL) {$v->name="NA";}
			
			if(!isset($v->available_hours_month)){
				//get from the variable executed from the very earlier query 
				$v->available_hours_month = 160;
				$v->available_hours_day=9;
			}
			
			if(!isset($arr_user_avail_set["user_avail_hours"][$v->username]["set"])){
				$users_available_hours = $v->available_hours_month;
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
		/* echo"<pre>";print_r($arr_depts);echo"</pre>";
		exit; */
		$data['departments']= $arr_depts;
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
}
/* End of dms resource_availability file */