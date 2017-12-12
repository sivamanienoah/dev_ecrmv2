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
		$this->load->helper('custom_helper');
		$this->load->model('report/report_lead_region_model');
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
    }
	
	public function index() 
	{
		@set_time_limit(-1); //disable the mysql query maximum execution time
		
		$timesheet_db = $this->load->database('timesheet', TRUE);
		
		$totalMonths  = 4;
		
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

		$monthYearIn = implode("','",$monthYear);
		
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
		
		if(!empty($times_result)) {
		
			$del_status = $this->db->delete($this->cfg['dbpref'].'timesheet_data', array('DATE(start_time) >=' => $start_date, 'DATE(end_time) <= '=> $end_date));
			// echo $this->db->last_query();
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
		// getting dept,skill,practice details
		// $del_status = 1;
		if($del_status) {
		
			foreach($times_result as $key=>$val) {
				// echo "<pre>"; print_r($val); exit;
				$costPerHour = 0;
				$directCostPerHour = 0;
				
				if( !empty($val['emp_id']) && !empty($val['entry_year']) && !empty($val['entry_month']) ) {
				
					$ts_month = date('m', strtotime($val['start_time']));
					$ts_month = ltrim($ts_month, '0');
					$cost  = $userCostArr[$val['emp_id']][$val['entry_year']][$ts_month];
					$dcost = $userDirectCostArr[$val['emp_id']][$val['entry_year']][$ts_month];
					
					if(!empty($cost)) {
						$costPerHour = $cost;
						$directCostPerHour = $dcost;
					} else {
						if(is_null($userCostArr['final_cost'][$val['emp_id']])){
							ksort($userCostArr[$val['emp_id']]);
							ksort($userDirectCostArr[$val['emp_id']]);
							$arr = end($userCostArr[$val['emp_id']]);
							$darr = end($userDirectCostArr[$val['emp_id']]);
							// sort($arr);
							// sort($darr);
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
				$costPerHr 										= (!is_null($costPerHour)) ? $costPerHour : 0;
				$diretCostPerHr 								= (!is_null($directCostPerHour)) ? $directCostPerHour : 0;
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
			$param['email_data'] 	  = array('print_date'=>date('d-m-Y'), 'started_at'=>$started_at, 'ended_at'=>$ended_at, 'upload_status'=>$upload_status);
			// $param['to_mail']    	  = 'ssriram@enoahisolution.com';
			$param['to_mail']    	  = 'ssriram@enoahisolution.com, ssubbiah@enoahisolution.com';
			$param['template_name']   = "Timesheet data uploaded status";
			$param['subject'] 		  = "Timesheet data uploaded status On ".date('d-m-Y');
			$this->email_template_model->sent_email($param);
		} else {
			echo "<br>Insertion Failed";
			$upload_status = "Failed to Insert the timesheet data";
			$this->load->model('email_template_model');
			$param 					= array();
			$param['email_data'] 	= array('print_date'=>date('d-m-Y'), 'started_at'=>'-', 'ended_at'=>'-', 'upload_status'=>$upload_status);
			$param['to_mail']    	= 'ssriram@enoahisolution.com';
			// $param['from_email'] 	  = 'webmaster@enoahisolution.com';
			// $param['from_email_name'] = 'Webmaster';
			$param['template_name'] = "Timesheet data uploaded status";
			$param['subject'] 	    = "Timesheet data uploaded status On ".date('d-m-Y');
			$this->email_template_model->sent_email($param);
		}
	}
	
	public function month_wise() 
	{
		echo "<br>Start :".date("Y-m-d H:i;s");
		@set_time_limit(-1);

		$this->db->select('l.pjt_id, l.division, l.move_to_project_status, sd.division_name');
		$this->db->from($this->cfg['dbpref'].'leads l');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as sd', 'sd.div_id = l.division');
		$this->db->where('l.pjt_id != ', '');
		$this->db->where('l.move_to_project_status', 1);
		$pjt_query 	 = $this->db->get();
		$crm_res 	 = $pjt_query->result();
		
		$projEntityArr = array();
		if(!empty($crm_res) && count($crm_res)>0) {
			foreach($crm_res as $enty) {
				$projEntityArr[$enty->pjt_id]['entity_id'] = $enty->division;
				$projEntityArr[$enty->pjt_id]['entity_name'] = $enty->division_name;
			}
		}
		
		// echo "<pre>"; print_r($projEntityArr); die;
	    $times_sql = "SELECT td.client_id,td.client_code,td.project_code,td.username,td.empname,td.resoursetype,td.entry_year,td.entry_month,td.start_time,td.end_time,td.duration,sum(td.duration_hours) as duration_hours,td.cost_per_hour,sum(td.resource_duration_cost) as resource_duration_cost,td.direct_cost_per_hour,sum(td.resource_duration_direct_cost) as resource_duration_direct_cost,td.added_by,td.added_date,td.modified_by,td.modified_date,td.dept_id,td.dept_name,td.practice_id,td.practice_name,td.skill_id,td.skill_name FROM ".$this->cfg['dbpref']."timesheet_data td where DATE(td.start_time) >='2004-06-01' AND DATE(td.end_time) <= CURDATE() GROUP by project_code,username,resoursetype,entry_year,entry_month";
		
		$times_query  = $this->db->query($times_sql);
		$times_result = $times_query->result_array();
		if(!empty($times_result))
		{ 
			$this->db->truncate($this->cfg['dbpref'].'timesheet_month_data');
			$hours_details = array();
			foreach($times_result as $key=>$ts)
			{ 
				/** To calculate total hours used by a resource **/
				if(isset($hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['total_hours']))
				{
					$hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['total_hours'] = $hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['total_hours'];
				}
				else
				{
					$hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['total_hours'] = get_timesheet_hours_by_user_modified($ts['username'],$ts['entry_year'],$ts['entry_month'],array('Leave','Hol'));
				}
				/** To calculate total leave hours of a resource **/
				if(isset($hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['leave_hours']))
				{
					$hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['leave_hours'] = $hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['leave_hours'];
				}
				else
				{
					$hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['leave_hours'] = get_leave_hours_by_user($ts['username'],$ts['entry_year'],$ts['entry_month']);
				}
				/** To calculate total holiday hours of a resource **/
				if(isset($hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['holiday_hours']))
				{
					$hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['holiday_hours'] = $hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['holiday_hours'];
				}
				else
				{
					$hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['holiday_hours'] = get_hoilday_hours_by_user($ts['username'],$ts['entry_year'],$ts['entry_month']);
				}
				
				$ts['resource_total_hours'] 	= $hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['total_hours'];
				$ts['resource_leave_hours'] 	= $hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['leave_hours'];
				$ts['resource_holiday_hours'] 	= $hours_details[$ts['username']][$ts['entry_year']][$ts['entry_month']]['holiday_hours'];
				$ts['entity_id']	= isset($projEntityArr[$ts['project_code']]['entity_id']) ? $projEntityArr[$ts['project_code']]['entity_id'] : '';
				$ts['entity_name']	= isset($projEntityArr[$ts['project_code']]['entity_name']) ? $projEntityArr[$ts['project_code']]['entity_name'] : '';
				
				$this->db->insert($this->cfg['dbpref'].'timesheet_month_data', $ts);
			}
		}
		echo "<br>End :".date("Y-m-d H:i;s");
	}
}
?>