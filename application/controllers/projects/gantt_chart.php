<?php
class Gantt_chart extends crm_controller {
	
      public function __construct() { 
         parent::__construct(); 
         $this->load->helper(array('form', 'url')); 
      }
	
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
		{
			$row = $query->result_array();
			foreach($row as $list)
			{
				$id=$list['id'];
				$task_name=$list['task_name'];
				$start_date=date("d-m-Y",strtotime($list['start_date']));
				$end_date=date("d-m-Y",strtotime($list['end_date']));
				$predecessors=$this->get_task_id($list['predecessors']);
				$duration=$list['duration'];
				$resource_name=$list['resource_name'];
				$complete_percentage=$list['complete_percentage']/100;
				$result['data'][]=array('id'=>$id,'text'=>$task_name,'start_date'=>$start_date,'end_date'=>$end_date,'predecessor'=>$predecessors,'duration'=>$duration,'resource_name'=>$resource_name,'progress'=>$complete_percentage,'parent'=>$list['parent_id'],'open'=>"true");
			}
		}
		echo json_encode($result);
	  }
	  
	  public function get_task_id($uid,$project_id)
	  {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$this->db->where('project_id', $project_id);
		$this->db->where('uid', $uid);
		$query = $this->db->get();
		$result=array();
		if($query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row['id'];
		}
	  }
	  
	  public function getProjectInfo()
	  {
		$project_id=$_GET['project_id'];
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'leads');
		$this->db->where('lead_id', $project_id);
		$query = $this->db->get();
		$result=array();
		if($query->num_rows() > 0 )
		{
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
	  }
	  
		public function getProgress_status()
		{
			$progress=0;$result=array();
			$project_id=$_GET['project_id'];
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'project_plan');
			$this->db->where('project_id', $project_id);
			$query = $this->db->get();
			$result=array();
			$row_count=$query->num_rows();
			if($query->num_rows() > 0 )
			{
				$row = $query->result_array();
				foreach($row as $list)
				{
					$total_percentage+=$list['complete_percentage'];
					$progress=$total_percentage/$row_count;
				}
			}
			$result['response']=round($progress);
			echo json_encode($result);
		}
		
		public function updateTask()
		{
			if($this->input->post())
			{
				$id=$this->input->post('id');
				$task_name=$this->input->post('task_name');
				$start_date=$this->input->post('start_date');
				$end_date=$this->input->post('end_date');
				$duration=$this->input->post('duration');
				$progress=($this->input->post('progress')*100);
				
				$sql="update ".$this->cfg['dbpref']."project_plan set task_name='$task_name',duration='$duration',start_date='$start_date',end_date='$end_date',complete_percentage='$progress' WHERE id='$id'";
				$exe=$this->db->query($sql);
			}
		}
		
		
		public function deleteTask()
		{
			if($this->input->post())
			{
				$id=$this->input->post('id');
				
				//DELETE CHILD NODES
				$sql_exe="update ".$this->cfg['dbpref']."project_plan set status='1' WHERE parent_id='$id'";
				$execute=$this->db->query($sql_exe);
				
				//DELETE PARENT NODE
				$sql="update ".$this->cfg['dbpref']."project_plan set status='1' WHERE id='$id'";
				$exe=$this->db->query($sql);
			}
		}
		
		
		public function addTask()
		{
			if($this->input->post())
			{
				$id=$this->input->post('id');
				$project_id=$this->input->post('project_id');
				$parent_id=$this->input->post('parent_id');
				$task_name=$this->input->post('task_name');
				$start_date=$this->input->post('start_date');
				$end_date=$this->input->post('end_date');
				$duration=$this->input->post('duration');
				$progress=($this->input->post('progress')*100);
				$uid=$this->get_last_uid();
				$task_id=$this->get_taskid($parent_id,$project_id);
				
				$sql="INSERT INTO ".$this->cfg['dbpref']."project_plan( 	uid,project_id,task_id,parent_id,task_name,duration,start_date,end_date,predecessors,resource_name,estimated_start,estimated_end,complete_percentage) VALUES ('$uid','$project_id','$task_id','$parent_id','$task_name','$duration','$start_date','$end_date','','','','','$progress')";
				$exe=$this->db->query($sql);
				
				$id = $this->db->insert_id();
			
				echo $id;exit;
			}
		}
		
		function get_last_uid()
		{
			$id="";
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'project_plan');
			$this->db->order_by("id", "desc");
			$sql = $this->db->get();
			if($sql->num_rows() > 0 )
			{
				$row = $sql->row_array();
				$id=$row['id'];
			}
			return $id;
		}
		
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
			{
				$row=$sql_query->row_array();
				$task_id=$row['task_id'];
				$last_id = substr(strrchr($task_id, "."), 1);
				$last_id=$last_id+1;
				$first_id=substr($task_id, 0, strripos($task_id, '.'));
			}
			else
			{
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
   } 
 ?>