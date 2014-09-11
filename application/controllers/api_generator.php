<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * My Profile
 *
 * @class 		api_generator
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @Admin       My Profile
 * @author 		eNoah
 * @Controller
 */

class Api_generator extends crm_controller {
    
	public $userdata;
	
	/*
	*@Constructor
	*@Api_generator
	*/
    public function Api_generator() {
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('api_generator_model');
		$this->load->model('email_template_model');
        $this->load->library('validation');
    }
	
	/*
	*@Method   index
	*/
	public function index() {
		$this->load->view('api_generator/api_generator_view');
    }
	
	/*
	*@Insert log record
	*@Method   add_log
	*/
	

	function generatesecretkey()
	{
	    echo md5(time() . $this->session->userdata('logged_in_user') . 'mysite');
	}
	
	function updateapi()
	{
	    $apikey       = $this->input->post("api_key");
		$api_password = $this->input->post("api_password");
		$api_username = $this->input->post("api_username");
		
		$user_name			 = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
	    $dis['date_created'] = date('Y-m-d H:i:s');
	    $print_fancydate     = date('l, jS F y h:iA', strtotime($dis['date_created']));
							
		$from         = $this->userdata['email'];
		$arrEmails    = $this->config->item('crm');
		$arrSetEmails = $arrEmails['director_emails'];
		$admin_mail   = implode(',',$arrSetEmails);
		$subject      = 'API Changes Notification';
		
		//email sent by email template
		$param = array();
		$param['email_data']      = array('print_fancydate'=>$print_fancydate,'first_name'=>$update_data['first_name'],'last_name'=>$update_data['last_name'],'user_name'=>$user_name,'signature'=>$this->userdata['signature'],'SECRETKEY'=>$apikey,'APIUSERNAME'=>$api_username,'APIPASSWORD'=>$api_password);
		$param['to_mail'] 		  = $admin_mail;
		$param['from_email'] 	  = $from;
		$param['from_email_name'] = $user_name;
		$param['template_name']   = "API Changes";
		$param['subject']		  = $subject;
		$data_insert			  = array("key"=>$apikey,"username"=>$api_username,"password"=>md5($api_password));
		$ins					  = $this->api_generator_model->insert_api_details($data_insert);
		if($ins) {
			$this->email_template_model->sent_email($param);
		}
	}
    
}

?>
