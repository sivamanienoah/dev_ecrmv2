<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage service
 *
 * @class 		Email_template
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      User Module
 * @Menu        Service Catalogue
 * @author 		eNoah
 * @Controller
 */

class Email_template extends CRM_Controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@Email_template
	*/
	
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('email_template_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Service categories List
	*@Method index
	*/
	
    public function index() 
	{
        $data['page_heading'] = 'E-Mail Template';
		$data['email_template'] = $this->email_template_model->get_email_templates();
        $this->load->view('email_template/email_template_view', $data); 
    }
	
	
	/*
	*@For Add & Update E-Mail Template
	*@Method add_email_template
	*/
	public function add_email_template($update = false, $id = false) {
		$this->load->library('validation');
        $data = array();        
		$rules['email_templatename'] = "trim|required";
		$rules['email_templatesubject'] = "trim|required";
		$rules['email_templatefrom'] = "trim|required";
		$rules['email_templatecontent'] = "trim|required";
		$this->validation->set_rules($rules);
		$fields['email_templatename'] = 'Email Template Name';
		$fields['email_templatesubject'] = 'Email Subject';
		$fields['email_templatefrom'] = 'Email From';
		$fields['email_templatecontent'] = 'Email Content';	
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
       
	   $update_item = $this->input->post('update_item');	

		if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {	
            $src = $this->email_template_model->get_row('email_template', array('email_tempid' => $id));
            if (isset($src) && is_array($src) && count($src) > 0) foreach ($src[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }		
		if ($this->validation->run() != false) {
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
				$updt_item = $update_data;
				
				$updt_item['email_templatesubject'] = '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">'.$updt_item['email_templatesubject'].'</h3></td></tr>';
				
                if ($this->email_template_model->update_row('email_template', array('email_tempid' => $id), $updt_item)) {
                    $this->session->set_flashdata('confirm', array('Email Template Updated!'));
                    redirect('email_template');
                }
            } else {
				$ins_item = $update_data;
				
				$ins_item['email_templatesubject'] = '<tr><td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">'.$ins_item['email_templatesubject'].'</h3></td></tr>';

                $this->email_template_model->insert_row('email_template', $ins_item);
                $this->session->set_flashdata('confirm', array('Email Template Added!'));
                redirect('email_template');
            }
		}
		$this->load->view('email_template/email_template_add', $data);
	}
	
	/*
	*@For Delete Email template
	*@Method delete_email_template
	*/
	public function delete_email_template($update, $id) {
		if ($this->session->userdata('delete')==1) {
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
				$this->email_template_model->delete_row('email_template',array('email_tempid' => $id));
				$this->session->set_flashdata('confirm', array('Email Template Deleted!'));
				redirect('email_template');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('email_template');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('email_template');
		}
	}
	
	
	/*
	*@For Add & Update E-Mail Template Header & Footer
	*@Method add_template_header
	*/
	public function add_template_header() { 		
		$this->load->library('validation');
        $data = array();        
		$rules['email_template_header'] = "trim|required";
		$rules['email_template_footer'] = "trim|required";
		$this->validation->set_rules($rules);
		$fields['email_template_header'] = 'Template Header';
		$fields['email_template_footer'] = 'Template Footer';
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
       
		$update_item = $this->input->post('update_item');	

		$src = $this->email_template_model->get_row('email_template_hf', array('id' => 1));
		if (isset($src) && is_array($src) && count($src) > 0) foreach ($src[0] as $k => $v) {
			if (isset($this->validation->$k)) $this->validation->$k = $v;
		}
	
		if ($this->validation->run() != false) {
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
            if (count($src) > 0) {
				$updt_item = $update_data;
                if ($this->email_template_model->update_row('email_template_hf', array('id' => 1), $updt_item)) {
                    $this->session->set_flashdata('confirm', array('Template Header & Footer Updated!'));
                    redirect('email_template');
                }
            } else {
				$ins_item = $update_data;
                $this->email_template_model->insert_row('email_template_hf', $ins_item);
                $this->session->set_flashdata('confirm', array('Template Header & Footer Added!'));
                redirect('email_template');
            }
		}
		$this->load->view('email_template/template_header_footer', $data);
	}


	
}