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
		$this->db->where('custid', $custid);
		$q = $this->db->get($this->cfg['dbpref'].'customers');
		$data = $q->result_array();
		$data['contacts']=$data[0];
		if($this->input->post())
		{
			$update_data['customer_name']=$this->input->post('customer_name');
			$update_data['email_1']=$this->input->post('email');
			$update_data['position_title']=$this->input->post('position_title');
			$update_data['phone_1']=$this->input->post('phone');
			$update_data['skype_name']=$this->input->post('skype_name');
			$update_data['sales_contact_userid_fk']=$this->userdata['userid'];
			
			$this->db->where('custid',$this->input->post('custid'));
			$this->db->update($this->cfg['dbpref'].'customers',$update_data);
			echo "1";exit;
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
				$this->customer_model->delete_customer_contact($custid);
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