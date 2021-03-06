<?php
class Customers_contact extends crm_controller {
    
    function __construct() {
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('customer_model');	
        $this->load->model('regionsettings_model');
		$this->load->model('email_template_model');
        $this->load->library('validation');
    }
    
	function index() 
	{
        $data['customers'] = $this->customer_model->customer_list();
		$data['contact']   = $this->customer_model->customer_contact_list($data['customers']);
        $this->load->view('customer_contact_view', $data);
    }
	
	function update_contacts($custid)
	{
		$res = array();
		$this->db->where('custid', $custid);
		$q 		= $this->db->get($this->cfg['dbpref'].'customers');
		$data 	= $q->result_array();
		$data['contacts']	= $data[0];
		if($this->input->post())
		{
			$update_data['customer_name']			= $this->input->post('customer_name');
			$update_data['email_1']					= $this->input->post('email');
			$update_data['position_title']			= $this->input->post('position_title');
			$update_data['phone_1']					= $this->input->post('phone');
			$update_data['skype_name']				= $this->input->post('skype_name');
			$update_data['sales_contact_userid_fk'] = $this->userdata['userid'];
			
			$this->db->where('custid',$this->input->post('custid'));
			$this->db->update($this->cfg['dbpref'].'customers',$update_data);
			/* if($res) {
				$this->db->where('companyid', ['company_id']);
				$cust_qey = $this->db->get($this->cfg['dbpref'].'customers_company');
				$cmp_data = $cust_qey->row_array();
				echo $this->db->last_query(); die;
				$ins_log 					= array();
				$ins_log['jobid_fk']    	= 0;
				$ins_log['userid_fk']   	= $this->userdata['userid'];
				$ins_log['date_created'] 	= date('Y-m-d H:i:s');
				$ins_log['log_content'] 	= $this->input->post('customer_name')." Contact Updated for the company ".$cmp_data['company']." On :" . " " . date('M j, Y g:i A');
				$log_res = $this->customer_model->insert_row("logs", $ins_log);
			} */
			// $res['result'] = 'success';
			// echo "1";exit;
			$this->session->set_flashdata('confirm', array('Contact Details updated Successfully!'));
			redirect('customers_contact');
			exit();
		}
		$this->load->view('customers_contact_update', $data);
	}
	
	function delete_customer($custid = false) 
	{
		if ($this->session->userdata('delete')==1) 
		{	
			if (preg_match('/^[0-9]+$/', $custid)) 
			{
				// check to see if this customer has a job on the system before deleting
				$leads = $this->db->get_where($this->login_model->cfg['dbpref'] . 'leads', array('custid_fk' => $custid));
				if ($leads->num_rows() > 0) 
				{
					$this->session->set_flashdata('login_errors', array('Cannot delete customer with exiting lead records!'));
					redirect('customers');
					exit();
				}
				$res = $this->customer_model->delete_customer_contact($custid);
				$this->session->set_flashdata('confirm', array('Customer Contact  Deleted!'));
				redirect('customers');
			}
		}
		else 
		{
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('customers');
		}
	}
}
?>