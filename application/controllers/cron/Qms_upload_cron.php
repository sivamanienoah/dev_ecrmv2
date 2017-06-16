<?php
class Qms_upload_cron extends crm_controller 
{
    
	public $userdata;
	
    function __construct()
	{
        parent::__construct();
		//$this->login_model->check_login();
		//$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->library('email');
    }
	
	function clean_string($string) 
	{
	   $temp_string = preg_replace('/[^A-Za-z0-9\-<>]/', '_', $string); // Removes special chars.
	   $temp_string = str_replace("___", "_", $temp_string);
	   return str_replace("__", "_", $temp_string);
	}
    
    function index()
	{
		$this->db->select('l.lead_id, l.lead_title');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_status != ", 4);
		$this->db->where('l.pjt_status', 0);
		$this->db->order_by('l.lead_status', 'asc');
		$query 		= $this->db->get();
		$resdata 	= $query->result();
		
		$qms_added		= array();
		// $qms_not_added	= array();
		
		echo count($resdata) . "<br>";
		$qms_file 		= UPLOAD_PATH.'template_file/QMS_Template.xls';
		if(!empty($resdata) && count($resdata)>0) {
			foreach($resdata as $key=>$val) {
				// echo $val->lead_title . " - ".$this->clean_string(trim($val->lead_title)) . '<br>';
				$parent_id = $this->getParentId($val->lead_id);
				
				$f_dir = UPLOAD_PATH.'files/';
				$f_dir = $f_dir.$val->lead_id;
				if (!is_dir($f_dir)) {
					mkdir($f_dir);
					chmod($f_dir, 0777);
				}
				
				// if(0 != $parent_id) {
					// inserting QMS file
					$title 				= $this->clean_string(trim($val->lead_title));
					echo $new_qms_file 	= UPLOAD_PATH.'files/'.$val->lead_id.'/'.$title.'_QMS_Procedure_Documents_and_Approvals.xls';
					echo "<br>";

					if (copy($qms_file, $new_qms_file)) {
						$lead_files 						 = array();
						$lead_files['lead_files_name'] 		 = $title.'_QMS_Procedure_Documents_and_Approvals.xls';
						$lead_files['lead_files_created_by'] = 59;
						$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
						$lead_files['lead_id'] 				 = $val->lead_id;
						$lead_files['folder_id'] 			 = $parent_id;
						$insert_files 						 = $this->db->insert($this->cfg['dbpref'].'lead_files', $lead_files);
						
						$qms_added[$val->lead_id] 			 = $val->lead_title;
						$new_qms_file = '';
					} else {
						echo $val->lead_id; die;
						$qms_not_added[$val->lead_id] = $val->lead_title;
					}
				// }
				
			}
		}
		
		echo "<pre>"; print_r($qms_added);
		echo "<br>**************************<br/>aa";
		print_r($qms_not_added);
		echo "</pre>";
		//$this->load->view('hosting_cron_view', $data);
    }
	
	function getParentId($lead_id)
	{
		$parent_id 	= $fold_parent_id = 0;
		$this->db->select('folder_id, folder_name');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('folder_name', 'Quality Control Documents');
		$this->db->where('lead_id', $lead_id);
		$query  	= $this->db->get();
		$result 	= $query->row_array();
		$parent_id 	= $result['folder_id'];
		
		if(0 == $parent_id) {

			$this->db->select('folder_id, folder_name');
			$this->db->from($this->cfg['dbpref'] . 'file_management');
			$this->db->where('folder_name', $lead_id);
			$this->db->where('lead_id', $lead_id);
			$query1  		= $this->db->get();
			$result1 		= $query1->row_array();
			$fold_parent_id = $result1['folder_id'];
			if(0 == $fold_parent_id) {
				$root_data 					= array();
				$root_data['lead_id']		= $lead_id;
				$root_data['folder_name']	= $lead_id;
				$root_data['parent']		= 0;
				$root_data['created_by']	= 59;
				$this->db->insert($this->cfg['dbpref'].'file_management', $root_data);
				$parent_id = $this->db->insert_id();
			} else {
				$parent_id = $fold_parent_id;
			}			
		}
		return $parent_id;
	}
   
}