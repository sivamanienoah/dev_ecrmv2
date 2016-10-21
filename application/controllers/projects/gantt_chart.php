<?php
	/*
	* @author priya
	* @created 04.07.2016
	* @modified on 17.10.2016 by @author priya
	*/

	/**
	* File Description
	* This class is mainly for uploading xml into database.
	* Related to table "crm_project_plan".
	* It performs the following methods
	__construct,getTask,get_parent_id,get_task_id,getProjectInfo,getProgress_status,updateTask,deleteTask,
	addTask,get_last_uid,get_taskid,updateParentHours,updateParentProgress
	**/
	
class Gantt_chart extends crm_controller 
{
	//initial declaration for the class
	public function __construct() { 
		parent::__construct(); 
		$this->load->helper(array('form', 'url')); 
		$this->load->model('projects/dashboard_model');
	}
	
	//get task as input for the gantt chart
	public function getTask()
	{
		$project_id=$_GET['project_id'];
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('project_id', $project_id);
		$this->db->where('status', 0);
		// $this->db->limit(5);
		$query = $this->db->get();
		$result=array();
		if($query->num_rows() > 0 )
		{ //if array is not empty
			$row = $query->result_array();
			
			foreach($row as $list)
			{
				$id=$list['id'];
				$task_name=$list['task_name'];
				$start_date=date("d-m-Y",strtotime($list['start_date']));
				$end_date=date("d-m-Y",strtotime($list['end_date']));
				
				$startdate=date("Y-m-d",strtotime($list['start_date']));
				$enddate=date("Y-m-d",strtotime($list['end_date']));
				
				$predecessors=$this->get_task_id($list['predecessors']);
				$duration=$list['duration'];
				$resource_name=$list['resource_name'];
				$complete_percentage=$list['complete_percentage']/100;
				//to calculate percentage 
				
				$datetime1 = date_create($startdate);
				$datetime2 = date_create($enddate);
				$interval = date_diff($datetime1, $datetime2);
				$days=$interval->format('%a')+1;
				//to find the difference between days
				
				$parent_id=$this->get_parent_id($project_id);
				//get parent_id for the given project_id
				
				if($list['parent_id']==$parent_id){$open=false;}
				else{$open=true;}
				//tree structure nodes open based on parent task and child task
				
				$result['data'][]=array('text'=>$task_name,'start_date'=>$start_date,'hours'=>$duration,'duration'=>$days,'progress'=>$complete_percentage,'parent'=>$list['parent_id'],'id'=>$id,'owner'=>$resource_name,'resource'=>$resource_name,'enddate'=>$end_date,"open"=>true);
				//returns result in terms of array
			}
		}
		echo json_encode($result);
		//convert result to json format
	}
	
	//get parent_id for the project
	public function get_parent_id($project_id)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('project_id', $project_id);
		$this->db->where('parent_id =', 0);
		$query = $this->db->get();
		$result=array();
		if($query->num_rows() > 0 )
		{//if array is not empty
			$row = $query->row_array();
			return $row['id'];
		}
	}
	
	//get task id for the project
	public function get_task_id($uid,$project_id)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('project_id', $project_id);
		$this->db->where('uid', $uid);
		$query = $this->db->get();
		$result=array();
		if($query->num_rows() > 0 )
		{//if array is not empty
			$row = $query->row_array();
			return $row['id'];
		}
	}

	//get project related data
	public function getProjectInfo()
	{
		$project_id=$_GET['project_id'];
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'leads');
		$this->db->where('lead_id', $project_id);
		$query = $this->db->get();
		$result=array();
		if($query->num_rows() > 0 )
		{//if array is not empty
			$row = $query->result_array();
			foreach($row as $list)
			{
				$start=$list['date_start'];
				$end=$list['date_due'];
				$date1 = new DateTime($start);
				$date2 = new DateTime($end);
				$diff = $date2->diff($date1)->format("%a");
				$result['start']=$start;
				$result['days']=$diff+1;
			}
		}
		echo json_encode($result);
		//convert result to json format
	}
	
	
	//update task
	public function updateTask()
	{
		if($this->input->post())
		{ //if values posted
			$id=$this->input->post('id');
			$task_name=$this->input->post('task_name');
			$start_date=$this->input->post('start_date');
			$end_date=$this->input->post('end_date');
			$hours=$this->input->post('hours');
			$progress=($this->input->post('progress')*100);
			$project_id=$this->input->post('project_id');
			$resource=$this->input->post('resource');
		
			$sql="update ".$this->cfg['dbpref']."project_plan set task_name='$task_name',duration='$hours',start_date='$start_date',end_date='$end_date',complete_percentage='$progress',resource_name='$resource' WHERE id='$id'";
			$exe=$this->db->query($sql);
			//update task 
			
			$this->updateParentHours($id);
			//update parent node in terms of hours
			
			$this->updateParentProgress($id);
			//update parent node in terms of progress
			
			$this->dashboard_model->update_project_thermometer($project_id);
			//update thermometer status
		}
	}
	
	//delete task(soft delete)
	public function deleteTask()
	{
		if($this->input->post())
		{
			//if values posted
			$id=$this->input->post('id');
			$project_id=$this->input->post('project_id');

			//DELETE CHILD NODES
			$sql_exe="update ".$this->cfg['dbpref']."project_plan set status='1' WHERE parent_id='$id' and project_id='$project_id'";
			$execute=$this->db->query($sql_exe);

			//DELETE PARENT NODE
			$sql="update ".$this->cfg['dbpref']."project_plan set status='1' WHERE id='$id' and project_id='$project_id'";
			$exe=$this->db->query($sql);
			
			$this->updateParentHours($id);
			//update parent node in terms of hours
			
			$this->updateParentProgress($id);
			//update parent node in terms of progress
			
			$this->dashboard_model->update_project_thermometer($project_id);
			//update thermometer status
		}
	}
	
	//add task under parent nodes
	public function addTask()
	{
		if($this->input->post())
		{
			//if values posted
			$id=$this->input->post('id');
			$project_id=$this->input->post('project_id');
			$parent_id=$this->input->post('parent_id');
			$task_name=$this->input->post('task_name');
			$start_date=$this->input->post('start_date');
			$end_date=$this->input->post('end_date');
			$hours=$this->input->post('hours');
			$progress=($this->input->post('progress')*100);
			$uid=$this->get_last_uid($project_id);
			$task_id=$this->get_taskid($parent_id,$project_id);

			$sql="INSERT INTO ".$this->cfg['dbpref']."project_plan( 	uid,project_id,task_id,parent_id,task_name,duration,start_date,end_date,predecessors,resource_name,estimated_start,estimated_end,complete_percentage) VALUES ('$uid','$project_id','$task_id','$parent_id','$task_name','$hours','$start_date','$end_date','','','','','$progress')";
			$exe=$this->db->query($sql);
			$id = $this->db->insert_id();
			//insert into database
			
			$this->updateParentHours($id);
			//update parent node in terms of hours
			
			$this->updateParentProgress($id);
			//update parent node in terms of progress
			
			$this->dashboard_model->update_project_thermometer($project_id);
			//update thermometer status
			
			echo $id;exit;
		}
	}

	//get last uid(task id) for the project
	function get_last_uid($project_id)
	{
		$id="";
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('project_id', $project_id);
		$this->db->order_by("id", "desc");
		$sql = $this->db->get();
		if($sql->num_rows() > 0 )
		{//if array is not empty
			$row = $sql->row_array();
			$id=$row['id'];
		}
		return $id;
	}
	
	//get task id for the parent and project
	function get_taskid($parent_id,$project_id)
	{
		$task_id="";$first_id="";$last_id="";
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('parent_id', $parent_id);
		$this->db->where('project_id', $project_id);
		$this->db->order_by("id", "desc");
		$this->db->limit(1);
		$sql_query = $this->db->get();
		if($sql_query->num_rows() > 0 )
		{//if array is not empty
			$row=$sql_query->row_array();
			$task_id=$row['task_id'];
			$last_id = substr(strrchr($task_id, "."), 1);
			$last_id=$last_id+1;
			$first_id=substr($task_id, 0, strripos($task_id, '.'));
		}
		else
		{ //if array is empty
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'project_plan');
			$this->db->where('id', $parent_id);
			$this->db->where('project_id', $project_id);
			$sql = $this->db->get();
			if($sql->num_rows() > 0 )
			{
				$rows=$sql->row_array();
				$parent_task_id=$rows['task_id'];
				$last_id=1;
				$first_id=$parent_task_id;
			}
		}
		return $first_id.'.'.$last_id; 
	} 
	
	//update parent hours for the related sub nodes
	public function updateParentHours($id)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('id', $id);
		$this->db->where('parent_id != ', 0);
		$this->db->order_by("id", "desc");
		$sql = $this->db->get();
		if($sql->num_rows() > 0 )
		{//if array is not empty
			$row = $sql->row_array();
			$parent_id=$row['parent_id'];
			
			$this->db->select_sum('duration');
			$this->db->from($this->cfg['dbpref'].'project_plan');
			$this->db->where('parent_id', $parent_id);
			$this->db->where('status = ', 0);
			$sql = $this->db->get();
			$row = $sql->row_array();
			$total_hours=$row['duration'];
						
			$sql_query="update ".$this->cfg['dbpref']."project_plan set duration='$total_hours' WHERE id='$parent_id'";
			$exe=$this->db->query($sql_query);
			//update parent nodes
			
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'project_plan');
			$this->db->where('id', $parent_id);
			$this->db->where('parent_id != ', 0);
			$this->db->where('status = ', 0);
			$this->db->order_by("id", "desc");
			$sql = $this->db->get();
			if($sql->num_rows() > 0 )
			{
				//call function repeatedly if there is a parent for the existing node
				$this->updateParentHours($parent_id);
			}
		}
	}
	
	//update parent progress for the related sub nodes
	public function updateParentProgress($id)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('id', $id);
		$this->db->where('parent_id != ', 0);
		$this->db->order_by("id", "desc");
		$sql = $this->db->get();
		if($sql->num_rows() > 0 )
		{//if array is not empty
			$row = $sql->row_array();
			$parent_id=$row['parent_id'];
			
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'project_plan');
			$this->db->where('parent_id', $parent_id);
			$this->db->where('status = ', 0);
			$sql = $this->db->get();
			
			if($sql->num_rows() > 0 )
			{//if array is not empty
				$total_percentage=0;$total_work_hours=0;
				$row = $sql->result_array();
				foreach($row as $each_row)
				{
					$work_hours=$each_row['duration'];
					$progress=$each_row['complete_percentage'];
					$total_percentage+=$work_hours*$progress;
					$total_work_hours+=$work_hours;
					//calculate percentage of completion based on work hours and progress status
				}
				
				$total_progress=$total_percentage/$total_work_hours;
				//calculate total progress status 
				
				$total_progress=round($total_progress);
				
				$sql_query="update ".$this->cfg['dbpref']."project_plan set complete_percentage='$total_progress' WHERE id='$parent_id'";
				$exe=$this->db->query($sql_query);
				//update parent nodes
				
				$this->db->select('*');
				$this->db->from($this->cfg['dbpref'].'project_plan');
				$this->db->where('id', $parent_id);
				$this->db->where('parent_id != ', 0);
				$this->db->where('status = ', 0);
				$this->db->order_by("id", "desc");
				$sql = $this->db->get();
				if($sql->num_rows() > 0 )
				{
					//call function repeatedly if there is a parent for the existing node
					$this->updateParentProgress($parent_id);
				}
			}
		}
	}
	
	//get project complete percentage
	public function getProgress_status()
	{
		$result=array();
		$project_id=$_GET['project_id'];
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'leads');
		$this->db->where('lead_id', $project_id);
		$query = $this->db->get();
		$result=array();
		$row_count=$query->num_rows();
		if($query->num_rows() > 0 )
		{//if array is not empty
			$row = $query->row_array();
			$result['response']=$row['complete_status'];
		}
		echo json_encode($result);
		//convert result to json format
	}
} 
?>