<?php

/********************************************************************************
File Name       : update_master.php
Created Date    : 27/06/2017
Modified Date   : 27/06/2017
Created By      : Sriram.S
Modified By     : Sriram.S
Reviewed By     : Subbiah.S
*********************************************************************************/

/**
 * update_master
 *
 * @class 		update_master
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Cron
 * @Menu        Cron
 * @author 		eNoah
 * @Controller
 */

class Update_masters extends crm_controller 
{
	
    public function __construct()
	{
        parent::__construct();
    }
	
	public function index() 
	{
		echo "MASTER UPDATE";
		echo "<br>";
		echo "----------------------------------------------<br>";
		
		$this->db->from($this->cfg['dbpref'].'view_econnect_business_unit');
		$bu_sql 	   = $this->db->get();
		
		if($bu_sql->num_rows > 0) {
			$this->db->truncate($this->cfg['dbpref'].'business_unit');
			$bu_query = "REPLACE INTO ".$this->cfg['dbpref']."business_unit(business_id,business_unit_name,created_on,modified_on,status) SELECT business_id,business_unit_name,created_on,modified_on,status FROM ".$this->cfg['dbpref']."view_econnect_business_unit";
			
			$business_update = $this->db->query($bu_query);

			if($business_update) {
				echo "Business unit master updated.<br>";
			} else {
				echo "Something wrong in updating Business unit master. Please try again.<br>";
			}
		} else {
			echo "Error in selecting the econnect business unit master.<br>";
		}
		echo "----------------------------------------------<br>";
		
		///***///
		echo "----------------------------------------------<br>";
		
		$this->db->from($this->cfg['dbpref'].'view_econnect_department_master');
		$dept_sql 	   = $this->db->get();
		
		if($dept_sql->num_rows > 0) {
			$this->db->truncate($this->cfg['dbpref'].'department');
		
			$dept_query = "REPLACE INTO ".$this->cfg['dbpref']."department(department_id, department_name, department_description, active, created_on, modified_on) SELECT department_id, department_name, department_description, active, created_on, modified_on FROM ".$this->cfg['dbpref']."view_econnect_department_master";
			
			$department_update = $this->db->query($dept_query);

			if($department_update) {
				echo "Department master updated. <br>";
			} else {
				echo "Something wrong in updating Department master. Please try again.<br>";
			}
		} else {
			echo "Error in selecting the econnect department master.<br>";
		}
		
		echo "----------------------------------------------<br>";
		
		///***///
		echo "----------------------------------------------<br>";
		
		$this->db->from($this->cfg['dbpref'].'view_econnect_practice');
		$prac_sql 	   = $this->db->get();

		if($prac_sql->num_rows > 0) {
			$practice_records = $prac_sql->result();
			if(!empty($practice_records) && count($practice_records)>0) {
				foreach($practice_records as $p_row) {
					// echo $p_row->id . " - " . $p_row->practice_practice_name . "<br>";
					
					$updt_data 	= array();
					$p_count 	= 0;
					
					$p_query = $this->db->query("SELECT * FROM ".$this->cfg['dbpref']."practices WHERE id = ".$p_row->id);
					$p_count = $p_query->num_rows();
					
					$updt_data = array('practices'=>$p_row->practice_practice_name, 'status'=>1);
					if($p_count > 0) {
						//update
						$this->db->where('id', $p_row->id);
						$this->db->update($this->cfg['dbpref'].'practices', $updt_data);
					} else {
						//insert
						$updt_data['id'] = $p_row->id;
						$this->db->insert($this->cfg['dbpref'].'practices', $updt_data);
					}
					// echo $this->db->last_query() . '<br>';
				}
				echo "Practice master updated.<br>";
			}
		} else {
			echo "Error in selecting the econnect practice master.<br>";
		}
		
		echo "----------------------------------------------<br>";
	}
}
?>