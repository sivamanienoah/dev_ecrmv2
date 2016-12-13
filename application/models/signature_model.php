<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * email_template_model
 *
 * @class 		signature_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class signature_model extends crm_model {
    
	/*
	*@construct
	*@Email_template_model
	*/
    function Signature_model() {
       parent::__construct();
    }

	/*
	*@Get email template
	*@Email_template_model
	*/
	
	public function get_signatures() {
		$ses_data = $this->userdata;
		$user_id =$ses_data['userid'];
		$this->db->select('*');
		$this->db->where('user_id',$user_id);
		$this->db->from($this->cfg['dbpref'].'signatures');
		$query = $this->db->get();
		$templt =  $query->result_array();
		return $templt;
    }


	/*
	*@Insert Row for dynamic table
	*@Method  insert_row
	*/
	public function insert_row($table, $param) {
    	$this->db->insert($this->cfg['dbpref'].$table, $param);
    }
	

	/*
	*@Get row record for dynamic table
	*@Method  get_row
	*/
	public function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }
	
	
	/*
	*@Update Row for dynamic table
	*@Method update_row
	*/
    public function update_row($table, $cond, $data) {
    	$this->db->where($cond);
		return $this->db->update($this->cfg['dbpref'].$table, $data);
    }

	
	/*
	*@Delete Row for dynamic table
	*@Method insert_row
	*/
    public function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }

	public function get_default_templates()
	{
		$this->db->select('temp_id,temp_name');
		$this->db->where('is_default',1);
		$this->db->from($this->cfg['dbpref'].'custom_email_template');
		$query = $this->db->get();
		$templt =  $query->result_array();
		return $templt;
	}
	public function get_template_content($temp_id)
	{
		$this->db->select('temp_content');
		$this->db->where('temp_id',$temp_id);
		$this->db->from($this->cfg['dbpref'].'custom_email_template');
		$query = $this->db->get();
		$templt =  $query->row_array();
		return $templt;
	}
	public function clear_is_default($user_id)
	{
		$this->db->where('user_id',$user_id);
		return $this->db->update($this->cfg['dbpref'].'signatures', array('is_default'=>0));
	}
}

?>
