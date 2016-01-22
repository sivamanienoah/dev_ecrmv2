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
		
		$practices = $this->dashboard_model->get_practices();
		// echo "<pre>"; print_r($practices); exit;
		
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
			// $start_date = date("Y-m-1");
			// $end_date   = date("Y-m-10");
			$start_date = date("2015-10-1");
			$end_date   = date("2015-10-31");
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
		
		// from timesheet table
		/* $getITDataQry = "SELECT v.department_name, v.name, v.username,concat(v.first_name,' ',v.last_name) as emp_name, 
		ah.available_hours_month, ah.available_hours_day, v.status as emp_active_status, v.join_date, v.exit_date, t.start_time, t.resoursetype, t.end_time, t.duration, t.proj_id, ep.title
		FROM enoah_times t 
		left join v_emp_details v on v.username=t.uid  
		left join enoah_available_hours ah on ah.dept_id=v.department_id
		left join enoah_project ep on ep.proj_id=t.proj_id
		WHERE t.start_time between '$start_date ' and '$end_date' $where"; */
		
		$getITDataQry = "SELECT dept_id, dept_name, practice_id, practice_name, skill_id, skill_name, resoursetype, username, duration_hours, resource_duration_cost
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
		
		// get all departments  from timesheet
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
	
	public function excelExport(){
		$timesheet_db = $this->load->database("timesheet",true);
		if($this->input->post("month_year_from_date")){
			$date = $this->input->post("month_year_from_date");
			
			$cur_month = date("m");
			$post_month = date('m',strtotime($date));
			
			$start_date = date("Y-m-01",strtotime($date));
			if($cur_month==$post_month){
				$end_date = date("Y-m-d");	
			}else{
				$end_date = date("Y-m-t",strtotime($date));	
			}			
			
		}else{
			$start_date = date("Y-m-01");
			$end_date = date("Y-m-d");
		}
		$where='';
		$department_ids = $this->input->post("department_ids");
		if(count($department_ids)>0 && !empty($department_ids) && array_filter($department_ids)){
			$dids = implode(",",$department_ids);
			$where .= " and v.department_id in ($dids)";
		}
		
		$skill_ids = $this->input->post("skill_ids");
	 
		//print_r($skill_ids);
		if(count($department_ids)>0 && !empty($department_ids) && array_filter($department_ids)){
			$sids = implode(",",$skill_ids);
			if(count($skill_ids)>0 && !empty($skill_ids) && $sids!=''){
				$where .= " and v.skill_id in ($sids)";
			}
		}

		$member_ids = $this->input->post("member_ids");
		//echo '<pre>';print_r($_REQUEST);exit;
		if(count($skill_ids)>0 && !empty($skill_ids) && array_filter($skill_ids) && array_filter($department_ids) && count($department_ids)>0 && !empty($department_ids)){
			$ex = explode(",",$member_ids[0]);
			$mids = "'".implode("','",$ex)."'";
			if(count($member_ids)>0 && !empty($member_ids) && array_filter($member_ids)){
				$where .= " and v.username in ($mids)";
			}
		}
		
		$project_wise_breakup = $this->input->post("project_wise_breakup");
		$resource_type_selection = $this->input->post("resource_type_selection");
		$check_condition = $this->input->post("check_condition");
		$percentage = $this->input->post("percentage");		
		
		//fetch departments master from timesheet db
		$timesheet_db->order_by("department_name","asc");
		$qry = $timesheet_db->get($timesheet_db->dbprefix('department'));
		$sql_data_qry = "SELECT v.department_name, v.name, v.username, concat(v.first_name,' ',v.last_name) as emp_name,
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
		$timesheet_db->close();		

/* 		foreach($res_d as $k => $v){
			if($v->name == NULL) {$v->name="NA";}
			$check_array[$v->department_name][$v->name][$v->username][$v->resoursetype] += ($v->duration/60);
		}	 */	
		
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
					$month_last_date = date("Y-m-d");					
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
			$arr_depts[$v->department_name]["userwise"][$v->name][$v->username][$v->username] = $v->emp_name;
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
		}
		
		//load our new PHPExcel library
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Resource_Availability');											
		//set cell A1 content with some text			
		$this->excel->getActiveSheet()->setCellValue('A1', 'Members/Projects');
		$this->excel->getActiveSheet()->setCellValue('B1', 'Department Name');
		$this->excel->getActiveSheet()->setCellValue('C1', 'Skill Name');
		$this->excel->getActiveSheet()->setCellValue('D1', 'Available Hours');
		$this->excel->getActiveSheet()->setCellValue('E1', 'Billable Hours');
		$this->excel->getActiveSheet()->setCellValue('F1', 'Non Billable Hours');
		$this->excel->getActiveSheet()->setCellValue('G1', 'Billable Hours (%)');
		$this->excel->getActiveSheet()->setCellValue('H1', 'Non Billable Hours (%)');
		
		if($project_wise_breakup) $this->excel->getActiveSheet()->setCellValue('I1', 'Is Member/Project');
		
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
		$i=2;		
 
		$cnt = 0;$st=2;$end = 3;
		$gross = 0;
		$amt = 0;
		foreach($arr_depts as $department_name => $depts)
		{
/* 			$total_availability = $depts['summation_department_based_available_hours'];
			$total_billable_hrs = $depts['departmentwise']['Billable'];
			$total_non_billable_hrs = $depts['departmentwise']['Non-Billable'];
			$dept_skill_count = count($depts['skillwise']);
			$dept_member_count = count($depts['department_based_available_hours']);
			 
			$billable_percentage = (($total_billable_hrs/$total_availability)*100);
			$non_billable_percentage = (($total_non_billable_hrs/$total_availability)*100);
 */
			foreach($depts['skillwise'] as $skill_name => $skill){
/* 				$skill_slug = str_replace(" ","",$skill_name);
				
				$total_availability = $depts[$skill_name]['summation_skill_based_available_hours'];
				$total_billable_hrs = $skill['Billable'];
				$total_non_billable_hrs = $skill['Non-Billable'];
				
				$dept_member_count = count($depts['skill_based_available_hours'][$skill_name]);
				
				$billable_percentage = (($total_billable_hrs/$total_availability)*100);
				$non_billable_percentage = (($total_non_billable_hrs/$total_availability)*100);
 */
			
				foreach($depts['userwise'][$skill_name] as $username => $user)
				{
					$total_availability = $depts['department_based_available_hours'][$username];
					 
					$total_billable_hrs = $user['Billable'];
					$total_non_billable_hrs = $user['Non-Billable'];
					
					$billable_percentage = (($total_billable_hrs/$total_availability)*100);
					$non_billable_percentage = (($total_non_billable_hrs/$total_availability)*100);
					
					if(!empty($resource_type_selection) && !empty($check_condition))
					{
						if($resource_type_selection == 'billable_percentage'){$value = number_format($billable_percentage,2);}
						else if($resource_type_selection == 'non_billable_percentage'){$value = number_format($non_billable_percentage,2);} 
						else if($resource_type_selection == 'all'){$value = ($billable_percentage || $non_billable_percentage);} 
						
						if($check_condition=='greater_than_equal'  && !empty($percentage))
						{
							$condition = $value >= $percentage;
						}else if($check_condition=='greater_than'  && !empty($percentage)){
							$condition = $value > $percentage;
						}else if($check_condition=='less_than_equal'  && !empty($percentage)){
							$condition = $value <= $percentage;
						}else if($check_condition=='less_than'  && !empty($percentage)){
							$condition = $value < $percentage;
						}else if($check_condition=='equal'  && !empty($percentage)){
							$condition = $value == $percentage;
						}else if($check_condition=='all'){
							$condition = $value > 0;
						}
						
						if($resource_type_selection == 'all' && $check_condition != 'all' && !empty($percentage)){
							if($check_condition=='greater_than_equal' && !empty($percentage))
							{
								$condition = (($billable_percentage >= $percentage) && ($non_billable_percentage >= $percentage));
							}else if($check_condition=='greater_than' && !empty($percentage)){
								$condition = (($billable_percentage > $percentage) && ($non_billable_percentage > $percentage));
							}else if($check_condition=='less_than_equal' && !empty($percentage)){
								$condition = (($billable_percentage <= $percentage) && ($non_billable_percentage <= $percentage));
							}else if($check_condition=='less_than' && !empty($percentage)){
								$condition = (($billable_percentage < $percentage) && ($non_billable_percentage < $percentage));
							}else if($check_condition=='equal' && !empty($percentage)){
								$condition = (($billable_percentage == $percentage) && ($non_billable_percentage == $percentage));
							}
						}						
			
						if($condition)
						{
							$this->excel->getActiveSheet()->setCellValue('A'.$i, $user[$username]);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $department_name);
							$this->excel->getActiveSheet()->setCellValue('C'.$i, $skill_name);
							$this->excel->getActiveSheet()->setCellValue('D'.$i, number_format($total_availability,2));
							$this->excel->getActiveSheet()->setCellValue('E'.$i, number_format($total_billable_hrs,2));
							$this->excel->getActiveSheet()->setCellValue('F'.$i, number_format($total_non_billable_hrs,2));
							$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($billable_percentage,2).'%');
							$this->excel->getActiveSheet()->setCellValue('H'.$i, number_format($non_billable_percentage,2).'%');
							
							if($project_wise_breakup){
								$this->excel->getActiveSheet()->setCellValue('I'.$i, 'Member');	
								++$i;
								foreach($depts['projectwise'][$username] as $project)
								{
									$billable = $depts['projuser'][$username][$project]['Billable'];
									$nonbillable = $depts['projuser'][$username][$project]['Non-Billable'];
									
									$billable = ($billable!='')?number_format($billable,2):'0.00';
									$nonbillable = ($nonbillable!='')?number_format($nonbillable,2):'0.00';

									$this->excel->getActiveSheet()->setCellValue('A'.$i,  $project);
									$this->excel->getActiveSheet()->setCellValue('B'.$i,  $department_name);
									$this->excel->getActiveSheet()->setCellValue('C'.$i,  $skill_name);
									$this->excel->getActiveSheet()->setCellValue('D'.$i, '0');
									$this->excel->getActiveSheet()->setCellValue('E'.$i, $billable);
									$this->excel->getActiveSheet()->setCellValue('F'.$i, $nonbillable);
									$this->excel->getActiveSheet()->setCellValue('G'.$i, 'N/A');
									$this->excel->getActiveSheet()->setCellValue('H'.$i, 'N/A');	
									$this->excel->getActiveSheet()->setCellValue('I'.$i, 'Project');	
									$i++;
								}
							}else{
								$i++;
							}
						}
					}else{
						$this->excel->getActiveSheet()->setCellValue('A'.$i, $user[$username]);
						$this->excel->getActiveSheet()->setCellValue('B'.$i, $department_name);
						$this->excel->getActiveSheet()->setCellValue('C'.$i, $skill_name);
						$this->excel->getActiveSheet()->setCellValue('D'.$i, number_format($total_availability,2));
						$this->excel->getActiveSheet()->setCellValue('E'.$i, number_format($total_billable_hrs,2));
						$this->excel->getActiveSheet()->setCellValue('F'.$i, number_format($total_non_billable_hrs,2));
						$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($billable_percentage,2).'%');
						$this->excel->getActiveSheet()->setCellValue('H'.$i, number_format($non_billable_percentage,2).'%');
						
						if(!empty($project_wise_breakup)){
							$this->excel->getActiveSheet()->setCellValue('I'.$i, 'Member');	
							++$i;
							foreach($depts['projectwise'][$username] as $project)
							{
								$billable = $depts['projuser'][$username][$project]['Billable'];
								$nonbillable = $depts['projuser'][$username][$project]['Non-Billable'];
								
								$billable = ($billable!='')?number_format($billable,2):'0.00';
								$nonbillable = ($nonbillable!='')?number_format($nonbillable,2):'0.00';

								$this->excel->getActiveSheet()->setCellValue('A'.$i,$project);
								$this->excel->getActiveSheet()->setCellValue('B'.$i,$department_name);
								$this->excel->getActiveSheet()->setCellValue('C'.$i,$skill_name);
								$this->excel->getActiveSheet()->setCellValue('D'.$i,'0');
								$this->excel->getActiveSheet()->setCellValue('E'.$i,$billable);
								$this->excel->getActiveSheet()->setCellValue('F'.$i,$nonbillable);
								$this->excel->getActiveSheet()->setCellValue('G'.$i,'N/A');
								$this->excel->getActiveSheet()->setCellValue('H'.$i,'N/A');	
								$this->excel->getActiveSheet()->setCellValue('I'.$i,'Project');	
								$i++;
							}
						}else{
							$i++;
						}
					}
				}
			}
			$cnt++;	
		}
		/*To build columns ends*/
		$this->excel->getActiveSheet()->getStyle('J2:J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);
		//merge cell A1 until D1			

		//Column Alignment
		$this->excel->getActiveSheet()->getStyle('A2:A'.$i)->getNumberFormat()->setFormatCode('00000');
		$filename='resource_availability.xls'   ; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');		
		redirect('report/resource_availability/');				
		
	}
}
/* End of dms resource_availability file */