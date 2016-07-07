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
   } 
 ?>