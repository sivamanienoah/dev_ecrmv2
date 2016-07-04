<?php
class Gantt_chart extends crm_controller {
	
      public function __construct() { 
         parent::__construct(); 
         $this->load->helper(array('form', 'url')); 
      }
	
	  public function getTask()
	  {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'project_plan');
		$query = $this->db->get();
		$result=array();
		if($query->num_rows() > 0 )
		{
			$row = $query->result_array();
			foreach($row as $list)
			{
				$id=$list['id'];
				$task_name=$list['task_name'];
				$start_date=$list['start_date'];
				$end_date=$list['end_date'];
				$predecessors=$list['predecessors'];
				$duration=$list['duration'];
				$resource_name=$list['resource_name'];
				$complete_percentage=$list['complete_percentage'];
				$result[]=array('id'=>$id,'task_name'=>$task_name,'start'=>$start_date,'end'=>$end_date,'predecessor'=>$predecessors,'duration'=>$duration,'resource_name'=>$resource_name,'complete_percentage'=>$complete_percentage);
			}
		}
		
		echo json_encode($result);
	  }
     
   } 
 ?>