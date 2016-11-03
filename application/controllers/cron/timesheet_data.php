<?php

/********************************************************************************
File Name       : timesheet_data.php
Created Date    : 18/02/2015
Modified Date   : 04/03/2015
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Subbiah.S
*********************************************************************************/

/**
 * timesheet_data
 *
 * @class 		Timesheet_data
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */

class Timesheet_data extends crm_controller 
{
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('text');
    }
	
	public function index() 
	{
		@set_time_limit(-1); //disable the mysql query maximum execution time
		
		$timesheet_db = $this->load->database('timesheet', TRUE);
		
		$totalMonths  = 9;
		
		$monthYearArr = date('01-n-Y'); //For uploading last 4 months data
		// $monthYearArr = date('d-n-Y', strtotime('2015-01-01')); //For uploading old data
		// $startMonthYearArr = date('d-m-Y', strtotime('2015-06-01')); //For uploading old data
		
		$monthYearIn  = 0;
		
		$userCostArr  = array();
		$userDirectCostArr  = array();
		
		for($i=1;$i<=$totalMonths;$i++) {
			$monthYear[] 	= $monthYearArr;
			$monthYearArr   = date('01-n-Y', strtotime('-'.$i.' months')); //For uploading last 4 months data
			// $monthYearArr   = date('01-n-Y', strtotime('-'.$i.' months', strtotime ( $startMonthYearArr ))); //For uploading old data
		}
		
		echo "<br> Start Date ".$start_date = date('Y-m-01',strtotime(end($monthYear)));
		$end_date   = date('Y-m-d'); //For uploading last 4 months data
		// echo "<br> End Date ".$end_date = date('Y-m-d', strtotime('2015-06-01')); //For uploading old data
		
		// echo "<pre>"; print_r($monthYear);
		
		$monthYearIn = implode("','",$monthYear);
		
		// echo "<pre>"; print_r($monthYearIn); exit;
		
		$sql = "SELECT `uc`.`employee_id`, `uc`.`month`, `uc`.`year`, `uc`.`direct_cost`, `uc`.`overheads_cost`, CONCAT_WS('-','01',uc.month,uc.year) FROM (".$timesheet_db->dbprefix('user_cost')." as uc) WHERE CONCAT_WS('-','01',uc.month,uc.year) IN ('".$monthYearIn."') ";
		
		// echo $sql; #exit;
		// echo "<br>";
		$query  = $timesheet_db->query($sql);
		$result = $query->result_array();
		
		// echo "<pre>"; print_r($result); exit;
		
		if(!empty($result)) {
			foreach($result as $row) {
				$userCostArr[$row['employee_id']][$row['year']][$row['month']] = $row['direct_cost'] + $row['overheads_cost'];
				$userDirectCostArr[$row['employee_id']][$row['year']][$row['month']] = $row['direct_cost'];
			}
		}
		
		ksort($userCostArr);
		ksort($userDirectCostArr);
		
		echo "<pre>"; print_r($userCostArr); echo "</pre>";

		echo "<br>Started = ".date("Y-m-d H:i:s");
		$started_at  = date("Y-m-d H:i:s");
		
		$times_sql  = 	"SELECT  
						c.client_id,c.client_code, 
						p.project_code,
						u.username,u.emp_id,concat(u.first_name,' ',u.last_name) as empname,u.department_id as dept_id,u.skill_id,

						t.resoursetype, 
						YEAR(t.start_time) entry_year,Monthname(t.start_time) entry_month,
						t.start_time,t.end_time, t.duration,(t.duration/60) as duration_hours, 

						t.added_by, t.added_date, t.modified_by, t.modified_date
						FROM enoah_times t
						left join enoah_user u on u.username=t.uid
						left join enoah_project p on p.proj_id=t.proj_id
						left join enoah_client c on c.client_id=p.client_id
						left join enoah_billrate_type brt on brt.billrate_type_id=t.billrate_type_id
						WHERE
						( (DATE(t.start_time) >= '".$start_date."') AND (DATE(t.end_time) <= '".$end_date."') ) AND
						p.title is not null AND c.client_id is not null AND p.client_id is not null AND t.duration is not null AND 
						p.project_code is not null
						order by p.client_id,t.proj_id,t.uid,t.start_time";
		
		// echo $times_sql; exit;

		$times_query  = $timesheet_db->query($times_sql);
		$times_result = $times_query->result_array();
		
		// echo "<pre>"; print_r($times_result);
		
		if(!empty($times_result)) {
		
			$del_status = $this->db->delete($this->cfg['dbpref'].'timesheet_data', array('DATE(start_time) >=' => $start_date, 'DATE(end_time) <= '=> $end_date));
			echo $this->db->last_query();
			
		}
		
		//getting dept,skill,practice details
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
		//getting dept,skill,practice details
		// $del_status = 1;
		if($del_status) {
		
			foreach($times_result as $key=>$val) {
				echo $ts_month = date('m', strtotime($val['start_time'])); exit;
				$costPerHour = 0;
				$directCostPerHour = 0;
				
				if( !empty($val['emp_id']) && !empty($val['entry_year']) && !empty($val['entry_month']) ) {
				
					$cost  = $userCostArr[$val['emp_id']][$val['entry_year']][$val['entry_month']];
					$dcost = $userDirectCostArr[$val['emp_id']][$val['entry_year']][$val['entry_month']];
					
					if(!empty($cost)) {
						$costPerHour = $cost;
						$directCostPerHour = $dcost;
					} else {
						if(is_null($userCostArr['final_cost'][$val['emp_id']])){
							ksort($userCostArr[$val['emp_id']]);
							ksort($userDirectCostArr[$val['emp_id']]);
							$arr = end($userCostArr[$val['emp_id']]);
							$darr = end($userDirectCostArr[$val['emp_id']]);
							sort($arr);
							sort($darr);
							$costPerHour = end($arr);
							$directCostPerHour = end($darr);
							if(!is_null($costPerHour)){
								$userCostArr['final_cost'][$val['emp_id']]=$costPerHour;
								$userDirectCostArr['final_cost'][$val['emp_id']]=$directCostPerHour;
							}
						}else{
							$costPerHour =  $userCostArr['final_cost'][$val['emp_id']];
							$directCostPerHour =  $userDirectCostArr['final_cost'][$val['emp_id']];
						}

						if( is_null($costPerHour) ) {
							if(!is_null($userCostArr['final_cost'][$val['emp_id']])) {
								$costPerHour = $userCostArr['final_cost'][$val['emp_id']];
								$directCostPerHour = $userDirectCostArr['final_cost'][$val['emp_id']];
							} else {
								$sql_finalcost = "SELECT round((direct_cost+overheads_cost),2) as cost, direct_cost as dcost  FROM (".$timesheet_db->dbprefix('user_cost')." as uc) WHERE uc.employee_id= '".$val['emp_id']."' order by year desc, month desc limit 0,1 ";

								 // echo "<br>sql_finalcost = ".$sql_finalcost;

								$query_finalcost  = $timesheet_db->query($sql_finalcost);
								$result_finalcost = $query_finalcost->row_array();
								if($result_finalcost["cost"]) {
									$userCostArr['final_cost'][$val['emp_id']]       = $result_finalcost["cost"];
									$userDirectCostArr['final_cost'][$val['emp_id']] = $result_finalcost["dcost"];
									$costPerHour 	   = $result_finalcost["cost"];
									$directCostPerHour = $result_finalcost["dcost"];
								}
							}
						}
					}
				}
			
				$ins_row[$key] = $val;
				$ins_row[$key]['cost_per_hour']				    = (!is_null($costPerHour)) ? $costPerHour : '';
				$ins_row[$key]['direct_cost_per_hour'] 			= (!is_null($directCostPerHour)) ? $directCostPerHour : '';
				$costPerHr = (!is_null($costPerHour)) ? $costPerHour : 0;
				$diretCostPerHr = (!is_null($directCostPerHour)) ? $directCostPerHour : 0;
				$ins_row[$key]['resource_duration_cost'] 		= ($costPerHr * $val['duration_hours']);
				$ins_row[$key]['resource_duration_direct_cost'] = ($diretCostPerHr * $val['duration_hours']);
				// $ins_row[$key]['dept_id']       = $val['department_id'];
				$ins_row[$key]['dept_name']		= isset($dept_arr[$val['dept_id']]) ? $dept_arr[$val['dept_id']] : '';;
				$ins_row[$key]['practice_id']	= isset($practice_arr[$val['skill_id']]['pid']) ? $practice_arr[$val['skill_id']]['pid'] : 0;
				$ins_row[$key]['practice_name'] = isset($practice_arr[$val['skill_id']]['pname']) ? $practice_arr[$val['skill_id']]['pname'] : 0;
				// $ins_row[$key]['skill_id']		= $val['skill_id'];
				$ins_row[$key]['skill_name'] 	= isset($skill_arr[$val['skill_id']]) ? $skill_arr[$val['skill_id']] : 0;
				
				// echo "<pre>"; print_r($ins_row[$key]); exit;
				// if(!empty($ins_row['client_id'])) {
					$ins_res = $this->db->insert($this->cfg['dbpref'].'timesheet_data', $ins_row[$key]);
				// }
				// echo $this->db->last_query() . "<br />";
				$ins_result = true;
			}
		}
		
		echo "<br>End Time = ".date("Y-m-d H:i:s");
		$ended_at = date("Y-m-d H:i:s");
		
		if($ins_result) {
		
			$upload_status = "Insert successfully";
			echo "<br>Insert successfully";
			
			$this->load->model('email_template_model');
			
			$param = array();

			$param['email_data'] = array('print_date'=>date('d-m-Y'), 'started_at'=>$started_at, 'ended_at'=>$ended_at, 'upload_status'=>$upload_status);

			$param['to_mail']    	  = 'ssriram@enoahisolution.com';
			// $param['from_email'] 	 = 'webmaster@enoahisolution.com';
			// $param['from_email_name'] = 'Webmaster';
			$param['template_name']   = "Timesheet data uploaded status";
			$param['subject'] 		  = "Timesheet data uploaded status On ".date('d-m-Y');
			
			$this->email_template_model->sent_email($param);

			
		} else {
			
			echo "<br>Insertion Failed";
			
			$upload_status = "Failed to Insert the timesheet data";

			$this->load->model('email_template_model');
			
			$param = array();

			$param['email_data'] = array('print_date'=>date('d-m-Y'), 'started_at'=>'-', 'ended_at'=>'-', 'upload_status'=>$upload_status);

			$param['to_mail']    	  = 'ssriram@enoahisolution.com';
			// $param['from_email'] 	  = 'webmaster@enoahisolution.com';
			// $param['from_email_name'] = 'Webmaster';
			$param['template_name']   = "Timesheet data uploaded status";
			$param['subject'] 		  = "Timesheet data uploaded status On ".date('d-m-Y');
			
			$this->email_template_model->sent_email($param);
			
		}
	}
	
	public function	updt_direct_cost()
	{
		@set_time_limit(-1); //disable the mysql query maximum execution time
		$timesheet_db = $this->load->database('timesheet', TRUE);
		
		$sql = "SELECT `uc`.`employee_id`, `uc`.`month`, `uc`.`year`, `uc`.`direct_cost`, `uc`.`overheads_cost`, CONCAT_WS('-','01',uc.month,uc.year) FROM (".$timesheet_db->dbprefix('user_cost')." as uc)";
		
		$query  = $timesheet_db->query($sql);
		$result = $query->result_array();
		
		$userDirectCostArrr = array();
		
		if(!empty($result)) {
			foreach($result as $row) {
				// $userCostArr[$row['employee_id']][$row['year']][$row['month']] = $row['direct_cost'] + $row['overheads_cost'];
				$userDirectCostArrr[$row['employee_id']][$row['year']][$row['month']] = $row['direct_cost'];
			}
		}
		ksort($userDirectCostArrr);
		
		// echo "<pre>"; print_r($userDirectCostArrr); exit;
		
	}

}
?>