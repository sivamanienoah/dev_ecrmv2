<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage Service Model
 *
 * @class 		manage_service_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class email_template_model extends crm_model {
    
	/*
	*@construct
	*@Manage Service Model
	*/
	
    function Email_template_model() {
       parent::__construct();
    }

	/*
	*@Get Job Category for Search
	*@Manage Service Model
	*/
	
	public function get_email_templates() {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'email_template');
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
    
	/*
	*@Send the email by using email template
	*@Method sent_email
	*/
	public function sent_email($data=array()) {
		
		$email_hf = $this->get_row('email_template_hf', array('id'=>1));
		$email_header = $email_hf[0]['email_template_header'];
		$email_footer =  $email_hf[0]['email_template_footer'];
		
		$body_content = $this->get_row('email_template', array('email_templatename'=>$data['template_name']));
		$email_title = $body_content[0]['email_templatesubject'];
		$email_content = $body_content[0]['email_templatecontent'];
		
		$email_subject = $data['subject'];
		
		if(count($data['email_data'])>0) {
			foreach($data['email_data'] as $key=>$value) {
				$key = "{{".$key."}}";
				$email_content = str_replace($key,$value,$email_content);
			}
		}
		
		$email_template = $email_header . $email_title . $email_content . $email_footer;

		$this->email->from($data['from_email'],$data['from_email_name']);
		$this->email->to($data['to_mail']);
		if (!empty($data['bcc_mail'])) {
			$this->email->bcc($data['bcc_mail']);
		}
		$this->email->subject($email_subject);
		$this->email->message($email_template);
		if($this->email->send())
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	
	
}

?>
