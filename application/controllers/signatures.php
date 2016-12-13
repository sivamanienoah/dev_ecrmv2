<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage service
 *
 * @class 		Signatures
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      User Module
 * @Menu        Service Catalogue
 * @author 		eNoah
 * @Controller
 */

class Signatures extends CRM_Controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@signature
	*/
	
	public function __construct() 
	{
        parent::__construct();
		
		//error_reporting(E_ALL);
        //ini_set('display_errors', 1);
        $this->login_model->check_login();
		$this->load->model('signature_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Service categories List
	*@Method index
	*/
	
    public function index() 
	{ 
        $data['page_heading'] = 'Signatures';
		$data['signatures'] = $this->signature_model->get_signatures();
		//print_r($data['signature']); exit;
		$this->load->view('signatures/signature_view', $data); 
    }
	
	
	/*
	*@For Add & Update E-Mail Template
	*@Method add_signature
	*/
	public function add_signature($update = false, $id = false) {

		$this->load->library('validation');
        $data = array();        
		$rules['sign_name'] = "trim|required";
		$rules['sign_content'] = "trim|required";
		$this->validation->set_rules($rules);
		$fields['sign_name'] = 'Signature Name';
		$fields['sign_content'] = 'Signature Content';	
		$fields['is_default'] = 'is default';	
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
       
	   $update_item = $this->input->post('update_item');	

		if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {	
            $src = $this->signature_model->get_row('signatures', array('sign_id' => $id));
            if (isset($src) && is_array($src) && count($src) > 0) foreach ($src[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }		
		if ($this->validation->run() != false) {
			$ses_data = $this->session->userdata;
		    $user_id =$ses_data['logged_in_user']['userid'];
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
			if(array_key_exists('is_default',$this->input->post()))
			{
				
				$this->signature_model->clear_is_default($user_id);
				$update_data['is_default']=1;
			}
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
				$updt_item = $update_data;
				
			if ($this->signature_model->update_row('signatures', array('sign_id' => $id), $updt_item)) {
                    $this->session->set_flashdata('confirm', array('Signature Updated!'));
                    redirect('signatures');
                }
            } else {
				
				$update_data['user_id']=$user_id;
				$ins_item = $update_data;
	            $this->signature_model->insert_row('signatures', $ins_item);
                $this->session->set_flashdata('confirm', array('Signature Added!'));
                redirect('signatures');
            }
		}
		$this->load->view('signatures/signature_add', $data);
	}
	
	/*
	*@For Delete Email template
	*@Method delete_signature
	*/
	public function delete_signature($update, $id) {
		
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
				$this->signature_model->delete_row('signatures',array('sign_id' => $id));
				$this->session->set_flashdata('confirm', array('Signature Deleted!'));
				redirect('signatures');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('signatures');
			}
		
	}
	public function get_template()
	{
		$temp_id = $this->input->post('temp_id');
		$temp_content =$this->signature_model->get_template_content($temp_id);
		 echo json_encode($temp_content);
	}
	
}