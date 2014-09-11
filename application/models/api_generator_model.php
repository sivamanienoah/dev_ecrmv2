<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Model
 *
 * @class 		User_model
 * @extends		crm_model (application/core/CRM_Model.php)
 * @author 		eNoah
 * @Model
 */
class Api_generator_model extends crm_model {
    
	/*
	*@Constructor
	*@User Model
	*/
    public function Api_generator_model() {
       parent::__construct();
    }
	
	public function insert_api_details($data)
	{
		 $this->db->where('id', "1");
         $this->db->update('crm_keys', $data);
		 return true;
	}
  
}

?>
