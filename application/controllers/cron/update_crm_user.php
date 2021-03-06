<?php

/********************************************************************************
File Name       : update_crm_user.php
Created Date    : 03/01/2017
Modified Date   : 03/01/2017
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Subbiah.S
*********************************************************************************/

/**
 * update_crm_user
 *
 * @class 		Update_crm_user
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */

class Update_crm_user extends crm_controller 
{
    
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->helper('text');
    }
	
	public function index() 
	{
		//update the skill set master
		$this->db->select('id,name,status');
		$this->db->from($this->cfg['dbpref'].'view_econnect_skills_set');
		$skill_sql 		= $this->db->get();
		$econnect_skill = $skill_sql->result_array();
		if(!empty($econnect_skill) && count($econnect_skill>0)) {
			$this->db->truncate($this->cfg['dbpref'].'skills_set');
			$skill_updt_sql		= "REPLACE INTO ".$this->cfg['dbpref']."skills_set(id,name,status,created_on,modified_on) SELECT id,name,status,created_on,modified_on FROM ".$this->cfg['dbpref']."view_econnect_skills_set";
			$skill_updt_query	= $this->db->query($skill_updt_sql);
		}
		
		//update the department master
		$this->db->select('department_id,department_name,active');
		$this->db->from($this->cfg['dbpref'].'view_econnect_department_master');
		$dept_sql 	   = $this->db->get();
		$econnect_dept = $dept_sql->result_array();
		if(!empty($econnect_dept) && count($econnect_dept>0)) {
			$this->db->truncate($this->cfg['dbpref'].'department');
			$dept_updt_sql		= "REPLACE INTO ".$this->cfg['dbpref']."department(department_id, department_name, department_description, active, created_on, modified_on) SELECT department_id, department_name, department_description, active, created_on, modified_on FROM ".$this->cfg['dbpref']."view_econnect_department_master";
			$dept_updt_query	= $this->db->query($dept_updt_sql);
		}

		//update the crm user skill set & department
		$this->db->select('v.autoid,v.username,v.EmpID,v.email,v.active,v.first_name,v.last_name,v.skill_id,v.department');
		$this->db->from($this->cfg['dbpref'].'view_econnect_mas as v');
		$this->db->where('v.username !=', '');
		$this->db->where('v.email !=', '');
		$this->db->where('v.skill_id is NOT NULL', NULL, FALSE);
		$this->db->where('v.department is NOT NULL', NULL, FALSE);
		$sql = $this->db->get();
		$econnect_users = $sql->result_array();
		
		foreach($econnect_users as $eusers) {	
			$updt_array 				 = array();
			$updt_array['skill_id'] 	 = $eusers['skill_id'];
			$updt_array['department_id'] = $eusers['department'];
			$updt_array['inactive'] 	 = ($eusers['active']==1) ? 0 : 1;
			$afftectedRows = 0;
			$this->db->where(array('username' => $eusers['username'], 'email' => $eusers['email']));
			$this->db->update($this->cfg['dbpref'] . 'users', $updt_array);
			$afftectedRows = $this->db->affected_rows();
			
			if($afftectedRows) {
				$this->db->select('u.emp_id, u.userid, u.username, u.role_id');
				$this->db->from($this->cfg['dbpref'].'users as u');
				$this->db->where(array('u.username' => $eusers['username'], 'u.email' => $eusers['email']));
				$query = $this->db->get();
				$user_data = $query->row_array();
				
				if(!empty($user_data) && !empty($user_data['emp_id']) && !empty($user_data['username'])) {
					$ins_data					= array();
					$ins_data['emp_id'] 		= $user_data['emp_id'];
					$ins_data['user_id'] 		= $user_data['userid'];
					$ins_data['username'] 		= $user_data['username'];
					$ins_data['role_id'] 		= $user_data['role_id'];
					$ins_data['skill_id'] 		= $updt_array['skill_id'];
					$ins_data['department_id'] 	= $updt_array['department_id'];
					$ins_data['active'] 		= $updt_array['inactive'];
					$ins_data['created_on'] 	= date('Y-m-d H:i:s');
					$this->db->insert($this->cfg['dbpref'] . 'users_logs', $ins_data);
				}
			}
		}	
	}

}
?>