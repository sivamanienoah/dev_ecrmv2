<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dms_search extends crm_controller {

	function Dms_search()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->load->model("dms_search_model");
		$this->load->model("project_model");
		$this->load->model("customer_model");
        $this->load->helper('custom');
        $this->load->library('validation');
	}
	
	function index($limit = 0, $search = false) {
		$data  = array();
		$user_details = $this->session->userdata("logged_in_user");
		$user_id = $user_details['userid'];
		$user_role = $user_details['role_id'];
		$data['keyword'] = $keyword;
		
		$data['page_heading'] = "DMS Search";
		$data['customers']   = $this->customer_model->customer_list();
		 
		$data['projects']   = $this->dms_search_model->get_projects_results();
		//$data['projects']   = $this->dms_search_model->get_projects();
		$data['extension']   = $this->dms_search_model->get_extensions();
		

		$customers = $this->input->post('customers');
		$projects = $this->input->post('projects');
		$extension = $this->input->post('extension');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		
		$data['files'] = $this->dms_search_model->search_files($keyword,$customers,$projects,$extension,$from_date,$to_date);		

		$this->load->view('dms_view', $data);
	}
	
	function search(){
		$data  = array();
		$user_details = $this->session->userdata("logged_in_user");
		$user_id = $user_details['userid'];
		$user_role = $user_details['role_id'];
		$data['keyword'] = $keyword;
		
		$data['page_heading'] = "DMS Search";
		$data['customers']   = $this->customer_model->customer_list();
		$data['projects']   = $this->dms_search_model->get_projects_results();
		$data['extension']   = $this->dms_search_model->get_extensions();		
		
		$customers = $this->input->post('customers');
		$projects = $this->input->post('projects');
		$extension = $this->input->post('extension');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		
		$data['files'] = $this->dms_search_model->search_files($keyword,$customers,$projects,$extension,$from_date,$to_date);
		if(count($data['files']>0))		$this->load->view('dms_view_search', $data);		
	}
	
/* 	function search($keyword=null)
	{
		$data = array();
		$keyword  = $this->input->post("keyword");
		//$data['folders'] = $this->dms_search_model->search_folder('','',$keyword);
		//if($keyword)
		//{			
			$data['keyword'] = $keyword;
			$data['files'] = $this->dms_search_model->search_files($keyword);
			//echo '<pre>';		print_r($data);		exit;
	//	}
		$this->load->view('dms_view', $data); 
	} */
}

/* End of dms search file */