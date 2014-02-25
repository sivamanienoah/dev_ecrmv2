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
		$filt = real_escape_array($this->input->post());
		if (count($filt)>0) {
			$stage 		  = $filt['stage'];
			$customer 	  = $filt['customer'];
			$worth   	  = $filt['worth'];
			$owner 		  = $filt['owner'];
			$leadassignee = $filt['leadassignee'];
			$regionname   = $filt['regionname'];
			$countryname  = $filt['countryname'];
			$statename 	  = $filt['statename'];
			$locname 	  = $filt['locname'];
			$lead_status  = $filt['lead_status'];
			$lead_indi 	  = $filt['lead_indi'];
			$keyword 	  = $filt['keyword'];
			$excel_arr 	  = array();
			foreach ($filt as $key => $val) {
				$excel_arr[$key] = $val;
			}
			// print_r($excel_arr); exit;
			$this->session->set_userdata(array("excel_download"=>$excel_arr));
		} else {
			$this->session->unset_userdata(array("excel_download"=>''));
		}
		// echo "<pre>"; print_r($this->session->userdata);
		$filter_results = $this->enquiries_model->get_filter_results();	
		// echo $this->db->last_query();
		$data['filter_results'] = $filter_results;

		$data['stage'] 		  = $stage;
		$data['customer']	  = $customer;
		$data['worth'] 		  = $worth;
		$data['owner'] 		  = $owner;
		$data['leadassignee'] = $leadassignee;
		$data['regionname']   = $regionname;
		$data['countryname']  = $countryname;
		$data['statename'] 	  = $statename;
		$data['locname'] 	  = $locname;
		$data['lead_status']  = $lead_status;
		$data['lead_indi']    = $lead_indi;
		$data['keyword'] 	  = $keyword;
		$this->load->view('enquiries/enquiries_list_view', $data);
	}
	
	
	function view_enquiries($id)
	{
	  $data = array();
	  $get_enquiry_detail = $this->enquiries_model->get_enquiry_detail($id);
	  $data['get_enquiry_detail'] = $get_enquiry_detail;
	  $this->load->view('enquiries/view_enquiry', $data);
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