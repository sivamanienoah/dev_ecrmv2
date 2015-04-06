<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dms_search extends crm_controller {

	function Dms_search()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->load->model("dms_search_model");
        $this->load->helper('custom');
        $this->load->library('validation');
	}
	
	function index($limit = 0, $search = false) {
		$data  = array();
		$this->load->view('dms_view', $data);
	}
	
	function search($keyword=null)
	{
		$data = array();
		$keyword  = $this->input->post("keyword");
		//$data['folders'] = $this->dms_search_model->search_folder('','',$keyword);
		if($keyword){
			$user_details = $this->session->userdata("logged_in_user");
			$user_id = $user_details['userid'];
			$user_role = $user_details['role_id'];
			//echo "<pre>"; echo $user_details['userid'];print_r($user_details);	exit;			
			$data['keyword'] = $keyword;
			$data['files'] = $this->dms_search_model->search_files($keyword,$user_id,$user_role);
			//echo '<pre>';		print_r($data);		exit;
		}
		$this->load->view('dms_view', $data); 
	}
}

/* End of dms search file */