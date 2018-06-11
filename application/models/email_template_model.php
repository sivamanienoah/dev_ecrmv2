<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * email_template_model
 *
 * @class 		email_template_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class email_template_model extends crm_model {
    
	/*
	*@construct
	*@Email_template_model
	*/
    function Email_template_model() {
       parent::__construct();
    }

	/*
	*@Get email template
	*@Email_template_model
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
	
	public function email_outer_content(){
		return $email_outer_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>eSmart</title>
			<style type="text/css">
				<!----body {
					margin-left: 0px;
					margin-top: 0px;
					margin-right: 0px;
					margin-bottom: 0px;
				}
				@media only screen and (max-width: 480px) {
					/* Force table to not be like tables anymore */
					#no-more-tables table, 
					#no-more-tables thead, 
					#no-more-tables tbody, 
					#no-more-tables th, 
					#no-more-tables td, 
					#no-more-tables tr { 
						display: block; 
					}
					/* Hide table headers (but not display: none;, for accessibility) */
					#no-more-tables thead tr { 
						position: absolute;
						top: -9999px;
						left: -9999px;
					}
					#no-more-tables tr { border: 1px solid #ccc; }
					#no-more-tables td { 
						/* Behave like a "row" */
						border: none;
						border-bottom: 1px solid #eee; 
						position: relative;
						padding-left: 50%; 
						white-space: normal;
						text-align:left;
					}
					#no-more-tables td:before { 
						/* Now like a table header */
						position: absolute;
						/* Top/left values mimic padding */
						top: 6px;
						left: 6px;
						width: 45%; 
						padding-right: 10px; 
						white-space: nowrap;
						text-align:left;
						font-weight: bold;
					}
					/*
					Label the data
					*/
					#no-more-tables td:before { content: attr(data-title); }
					/*td[data-title]{ color:red;}
					td{color:green;}*/
				}
				#no-more-tables tr:nth-of-type(even) {
					background: #eee;
				}----->

				</style>
			</head>

			<body>
				<div style="width:70%; margin: 0 auto; padding:5px; background:#f5f5f5">
					<div style="font-family:Arial, Helvetica, sans-serif; font-size:12px;background:#fff; padding:5px;">
						{{main_body_content}}
					</div>
				</div>
			</body>
		</html>';
	}
    
	/*
	*@Send the email by using email template
	*@Method sent_email
	*/
	public function sent_email($data=array()) {
		print_r($data);exit;
		$email_hf = $this->get_row('email_template_hf', array('id'=>1));
		$email_header = $email_hf[0]['email_template_header'];
		$email_footer =  $email_hf[0]['email_template_footer'];
		
		$body_content = $this->get_row('email_template', array('email_templatename'=>$data['template_name']));

		$email_title 	 = $body_content[0]['email_templatesubject'];
		$email_content	 = $body_content[0]['email_templatecontent'];
		$email_from 	 = "webmaster@enoahprojects.com";
		$email_from_name = 'Webmaster';
		
		$email_subject = $data['subject'] . " - Mail from DEV Server";
		//print_r($data['email_data']);
		
		if(count($data['email_data'])>0) {
			foreach($data['email_data'] as $key=>$value) {
				$key = "{{".$key."}}";
				$email_content = str_replace($key, $value, $email_content);
			}
		}
		
		//Changed Email title for timesheet
		if($data['subject']=="New User List from Timesheet" || $data['subject']=="Failed User List from Timesheet"){
			$email_title = str_replace('eConnect', 'Timesheet', $email_title);
		}
		 
		$email_template = $email_header . $email_title .'<div style="padding:10px;" id="body">'.$email_content.'</div>'. $email_footer;
		
		$email_outer_content = $this->email_outer_content();
		$email_template = str_replace('{{main_body_content}}', $email_template, $email_outer_content);

		// $this->email->from($data['from_email'],$data['from_email_name']);
		$this->email->from($email_from, $email_from_name);
		//$data['to_mail'] = array('ssriram@enoahisolution.com');
		$data['to_mail'] = array('kbalaji@enoahisolution.com');
		$data['cc_mail'] = array();
		$this->email->to($data['to_mail']);
		$this->email->cc($data['cc_mail']);
		/* if (!empty($data['cc_mail'])) {
			$this->email->cc($data['cc_mail']);
		}
		if (!empty($data['bcc_mail'])) {
			$this->email->bcc($data['bcc_mail']);
		} */
		$this->email->subject($email_subject);
		$this->email->message($email_template);

		if(!empty($data['attach'])) {
			$file_path = UPLOAD_PATH.'files/'.$data['job_id'].'/';
			foreach ($data['attach'] as $attach){
				$this->email->attach($file_path.$attach['lead_files_name']);
			}
		}
		if(!empty($data['external_attach'])) {
			$file_path = FCPATH.'crm_data/invoices/';
			foreach ($data['external_attach'] as $attach){
				$this->email->attach($file_path.$attach['file_name']);
			}
		}
		if(!empty($data['attachments'])) {
			foreach ($data['attachments'] as $att_row){
				$this->email->attach($att_row);
			}
		}		
		// return true;
		// $this->email->send();
		// echo $this->email->print_debugger();exit;
		//return true;
		//echo $email_template;
		if($this->email->send()) { 
			return true;
		} else {
			return false;
		}
	}
}

?>
