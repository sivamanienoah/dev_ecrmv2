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
	
	
	/**
	 * Initiates and create the quote based on an ajax request
	 */
	function ajax_enquiry_to_lead() {
	
		if (trim($this->input->post('lead_title')) == '' || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_service'))) || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_source'))) || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_assign'))))
        {
			echo "{error:true, errormsg:'Title and Lead Service are required fields!'}";
		}
        else if ( !preg_match('/^[0-9]+$/', trim($this->input->post('custid_fk'))) )
        {
			echo "{error:true, errormsg:'Customer ID must be numeric!'}";
		}
        else
        {   
			$data = real_escape_array($this->input->post());
			
			$proposal_expected_date = strtotime($data['proposal_expected_date']);
		    $ewa = '';
			$ins['lead_title'] = $data['lead_title'];
			$ins['custid_fk'] = $data['custid_fk'];
			$ins['lead_service'] = $data['lead_service'];
			$ins['lead_source'] = $data['lead_source'];
			$ins['lead_assign'] = $data['lead_assign'];
			$ins['expect_worth_id'] = $data['expect_worth'];
			if($data['expect_worth_amount'] == '') {
				$ewa = '0.00';
			}
			else {
			$ewa = $data['expect_worth_amount'];
			}  
			$ins['expect_worth_amount'] = $ewa; 
			$ins['belong_to'] = $data['job_belong_to'];
			$ins['division'] = $data['job_division'];
			$ins['date_created'] = date('Y-m-d H:i:s');
			$ins['date_modified'] = date('Y-m-d H:i:s');
			$ins['lead_stage'] = 1;
			$ins['lead_indicator'] = $data['lead_indicator'];
			$ins['proposal_expected_date'] = date('Y-m-d H:i:s', $proposal_expected_date);
			$ins['created_by'] = $this->userdata['userid'];
			$ins['modified_by'] = $this->userdata['userid'];
			$ins['lead_status'] = 1;
			
			if ($this->db->insert($this->cfg['dbpref'] . 'leads', $ins))
            {
				$insert_id = $this->db->insert_id();
				$invoice_no = (int) $insert_id;
				$invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);
				
				//history - lead_stage_history
				$lead_hist['lead_id'] = $insert_id;
				$lead_hist['dateofchange'] = date('Y-m-d H:i:s');
				$lead_hist['previous_status'] = 1;
				$lead_hist['changed_status'] = 1;
				$lead_hist['lead_status'] = 1;
				$lead_hist['modified_by'] = $this->userdata['userid'];
				$insert_lead_stg_his = $this->welcome_model->insert_row('lead_stage_history', $lead_hist);
				
				//history - lead_status_history
				$lead_stat_hist['lead_id'] = $insert_id;
				$lead_stat_hist['dateofchange'] = date('Y-m-d H:i:s');
				$lead_stat_hist['changed_status'] = 1;
				$lead_stat_hist['modified_by'] = $this->userdata['userid'];
				// $this->db->insert('lead_status_history', $lead_stat_hist);
				$insert_lead_stat_his = $this->welcome_model->insert_row('lead_status_history', $lead_stat_hist);
				
				$inv_no['invoice_no'] = $invoice_no;
				$updt_job = $this->welcome_model->update_row('leads', $inv_no, $insert_id);
				
				// $this->quote_add_item($insert_id, "\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:", 0, '', FALSE);

				$json['error'] = false;
                $json['fancy_insert_id'] = $invoice_no;
                $json['insert_id'] = $insert_id;
                $json['lead_title'] = htmlentities($data['lead_title'], ENT_QUOTES);
                $json['lead_service'] = $data['lead_service'];
                $json['lead_source'] = $data['lead_source'];
                $json['lead_assign'] = $data['lead_assign'];
				
				$json['expect_worth_id'] = $data['expect_worth_id'];
                $json['expect_worth_amount'] = $data['expect_worth_amount'];
				echo json_encode($json);
			}
            else
            {
				echo "{error:true, errormsg:'Data insert failed!'}";
			}
			
			$get_det = $this->welcome_model->get_lead_det($insert_id);
			$customer = $this->welcome_model->get_customer_det($get_det['custid_fk']);
			
			$lead_assign_mail = $this->welcome_model->get_user_data_by_id($get_det['lead_assign']);

			$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
		
			$from=$this->userdata['email'];
			$arrEmails = $this->config->item('crm');
			$arrSetEmails=$arrEmails['director_emails'];
			$mangement_email = $arrEmails['management_emails'];
			$mgmt_mail = implode(',',$mangement_email);
			$admin_mail=implode(',',$arrSetEmails);
			
			$param['email_data'] = array('first_name'=>$customer['first_name'],'last_name'=>$customer['last_name'],'company'=>$customer['company'],'base_url'=>$this->config->item('base_url'),'insert_id'=>$insert_id);

			$param['to_mail'] = $mgmt_mail.','. $lead_assign_mail[0]['email'];
			$param['bcc_mail'] = $admin_mail;
			$param['from_email'] = $from;
			$param['from_email_name'] = $user_name;
			$param['template_name'] = "New Lead Creation Notification";
			$param['subject'] = 'New Lead Creation Notification';
			
			$this->email_template_model->sent_email($param);
		}
	}
	
}
?>