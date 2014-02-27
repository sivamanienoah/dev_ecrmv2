<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Enquiries extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() {
		parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('welcome_model');
		$this->load->model('customer_model');
		$this->load->model('enquiries_model');
		$this->load->model('regionsettings_model');
		$this->load->model('email_template_model');
		$this->load->helper('text');
		$this->email->set_newline("\r\n");
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stg_name = getLeadStageName();
		$this->stages = @implode('","', $this->stg);
	}
	
    /*
	 * Redirect user to quotation list
	 */
	public function index() {
		redirect('enquiries/enquirieslist');
    }
	
	/*
	 * List all the Leads based on levels
	 * @access public
	 */
	public function enquirieslist() {

		$page_label = 'Leads List' ;
		
		$data['lead_stage'] = $this->stg_name;
		$data['customers'] = $this->welcome_model->get_customers();
		$data['lead_owner'] = $this->welcome_model->get_users();
		$data['regions'] = $this->regionsettings_model->region_list();
		
		$this->load->view('enquiries/enquiries_view', $data);
	}
	
	public function advance_filter_search() 
	{
		$filter_results = $this->enquiries_model->get_filter_results();	
		$data['filter_results'] = $filter_results;
		$this->load->view('enquiries/enquiries_list_view', $data);
	}
	
	
	function view_enquiries($id)
	{
	  $data = array();
	  $get_enquiry_detail = $this->enquiries_model->get_enquiry_detail($id);
	  $data['get_enquiry_detail'] = $get_enquiry_detail;
	  $this->load->view('enquiries/view_enquiry', $data);
	}
	
	function edit_enquiry($id)
	{
	    $data['categories'] = $this->welcome_model->get_categories();
		$c = count($data['categories']);
		for ($i = 0; $i < $c; $i++) {
			$data['categories'][$i]['records'] = $this->welcome_model->get_cat_records($data['categories'][$i]['cat_id']);
		}
		$data['lead_source'] = $this->welcome_model->get_lead_sources();
		$data['expect_worth'] = $this->welcome_model->get_expect_worths();
		$data['job_cate'] = $this->welcome_model->get_lead_services();
		$data['sales_divisions'] = $this->welcome_model->get_sales_divisions();
		$get_enquiry_detail = $this->enquiries_model->get_enquiry_detail($id);
	    $data['get_enquiry_detail'] = $get_enquiry_detail;
		$this->load->view('enquiries/enquiry_to_lead_view', $data);
	}
	
	function delete_enquiry($id)
	{
	if ($this->session->userdata('delete')==1) {
	   $delete_query = $this->enquiries_model->delete_enquiry($id);
	   $this->session->set_flashdata('confirm', array("Enquiry deleted from the system"));
	   redirect(base_url()."enquiries/enquirieslist");
	   }
	}
	
}
?>