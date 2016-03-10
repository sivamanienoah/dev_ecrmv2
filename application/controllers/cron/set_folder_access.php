<?php

/********************************************************************************
File Name       : set_folder_access.php
Created Date    : 09/03/2016
Modified Date   : 10/03/2016
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Subbiah.S
*********************************************************************************/

/**
 * timesheet_data
 *
 * @class 		Set_folder_access
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */

class Set_folder_access extends crm_controller 
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
		
		//get the projects id
		$sql = $this->db->query("SELECT `lead_id` FROM ".$this->cfg['dbpref']."leads WHERE `lead_status`= 4 AND `pjt_status` = 1");
		$projects = $sql->result();
		
		foreach($projects as $rec){
			//get the project folders based on project id
			$fo_sql  = $this->db->query("SELECT `folder_name`,`folder_id` FROM ".$this->cfg['dbpref']."file_management WHERE parent !=0 AND `lead_id`= ".$rec->lead_id);
			$folders = $fo_sql->result_array();
			
			//get the project members based on project id
			if(!empty($folders)){
				$tm_sql  = $this->db->query("SELECT `userid_fk` FROM ".$this->cfg['dbpref']."contract_jobs WHERE `jobid_fk`= ".$rec->lead_id);
				$members = $tm_sql->result_array();
				
				if(!empty($members)){
					for($i=0; $i<count($folders); $i++) {
						for($j=0;$j<count($members);$j++) {
							if((trim($folders[$i]['folder_name'])!='SOW') && (trim($folders[$i]['folder_name'])!='Statement Of Work')) {
								$ins_arr = array('lead_id'=>$rec->lead_id, 'folder_id'=>$folders[$i]['folder_id'], 'user_id'=>$members[$j]['userid_fk'], 'access_type'=>1, 'updated_by'=>59, 'updated_on'=>date('Y-m-d H:i:s'), 'created_by'=>59, 'created_on'=>date('Y-m-d H:i:s'));
							} else {
								$ins_arr = array('lead_id'=>$rec->lead_id, 'folder_id'=>$folders[$i]['folder_id'], 'user_id'=>$members[$j]['userid_fk'], 'access_type'=>0, 'updated_by'=>59, 'updated_on'=>date('Y-m-d H:i:s'), 'created_by'=>59, 'created_on'=>date('Y-m-d H:i:s'));
							}
							// echo "<pre>"; print_r($ins_arr); echo "<br>";
							$ins = $this->db->insert($this->cfg['dbpref']."lead_folder_access", $ins_arr);
							echo $this->db->last_query() ."<br>";
						}
					}
				}
			}
		}
	}
}
?>