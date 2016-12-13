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

class Custom_email_template extends CRM_Controller {
	
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
		$this->load->model('custom_email_template_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Service categories List
	*@Method index
	*/
	
    public function index() 
	{ 
        $data['page_heading'] = 'E-Mail Template';
		$data['email_template'] = $this->custom_email_template_model->get_email_templates();
        $this->load->view('custom_email_template/email_template_view', $data); 
    }
	
	
	/*
	*@For Add & Update E-Mail Template
	*@Method add_email_template
	*/
	public function add_email_template($update = false, $id = false) {

		$this->load->library('validation');
        $data = array();        
		$rules['temp_name'] = "trim|required";
		$rules['temp_content'] = "trim|required";
		$this->validation->set_rules($rules);
		$fields['temp_name'] = 'Email Template Name';
		$fields['temp_content'] = 'Email Content';	
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
       
	   $update_item = $this->input->post('update_item');	

		if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {	
            $src = $this->custom_email_template_model->get_row('custom_email_template', array('temp_id' => $id));
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
				
			if ($this->custom_email_template_model->update_row('custom_email_template', array('temp_id' => $id), $updt_item)) {
                    $this->session->set_flashdata('confirm', array('Email Template Updated!'));
                    redirect('custom_email_template');
                }
            } else {
				$update_data['is_default']=1;
				$ses_data = $this->session->userdata;
		        $user_id =$ses_data['logged_in_user']['userid'];
				$update_data['user_id']=$user_id;
				$ins_item = $update_data;
	            $this->custom_email_template_model->insert_row('custom_email_template', $ins_item);
                $this->session->set_flashdata('confirm', array('Email Template Added!'));
                redirect('custom_email_template');
            }
		}
		$data['def_templates']=$this->custom_email_template_model->get_default_templates();
		$this->load->view('custom_email_template/email_template_add', $data);
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
	public function get_template()
	{
		$temp_id = $this->input->post('temp_id');
		$temp_content =$this->custom_email_template_model->get_template_content($temp_id);
		 echo json_encode($temp_content);
	}
	
}