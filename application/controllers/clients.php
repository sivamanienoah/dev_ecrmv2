<?php
class Clients extends crm_controller {
    
	public $userdata;
	
    function __construct() 
	{
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('client_model');
        $this->load->model('regionsettings_model');
		$this->load->model('email_template_model');
        $this->load->library('validation');
    }
    
    function index($limit = 0, $search = false)
	{
        $data['clients'] = $this->client_model->client_list();

        $this->load->view('clients/client_view', $data);
    }
	
    function add_client($update = false, $id = false)
	{
		$data['regions'] = $this->regionsettings_model->region_list();

        $rules['client_name'] = "trim|required";
		$rules['region_id']   = "selected[region_id]";
		$rules['country_id']  = "selected[country_id]";
		$rules['state_id']    = "selected[state_id]";
		$rules['location_id'] = "selected[location_id]";
		$rules['post_code']   = "trim";

		$this->validation->set_rules($rules);
		
		$fields['client_name'] = "Client Name";
		$fields['address_1']   = "Address 1";
		$fields['address_2']   = "Address 2";
		$fields['suburb']	   = "";		
		$fields['post_code']   = "Post Code";
		$fields['region_id']   = "Region";
		$fields['country_id']  = "Country";
		$fields['state_id']	   = "State";
		$fields['location_id'] = "Location";
		$fields['website']	   = "Website";
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		// print_r($this->input->post()); exit;
		$pst_data = real_escape_array($this->input->post());
		
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($pst_data['update_client']))
		{
            $client = $this->client_model->get_client($id);
			
            if (is_array($client) && count($client) > 0) foreach ($client[0] as $k => $v) 
			{
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() == false)
		{
            if ($ajax == false) {
                $this->load->view('clients/client_add_view', $data);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }
		}
		else 
		{
			// all good
            foreach($fields as $key => $val)
			{
				if (isset($pst_data[$key]))
				{
					$update_data['created_by']  = $this->userdata['userid'];
					$update_data['modified_by'] = $this->userdata['userid'];
					$update_data['created_on']  = date('Y-m-d H:i:s');
					$update_data[$key] 			= $pst_data[$key];
				}
            }

			if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) 
			{
				//update
				if ($this->client_model->update_customer($id, $update_data))
				{
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

					$from = $this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail = implode(',',$mangement_email);
					$admin_mail=implode(',',$arrSetEmails);
					$subject='Customer Details Modification Notification';

					//email sent by email template
					$param = array();
					
					// $param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'first_name'=>$update_data['first_name'],'last_name'=>$update_data['last_name'],'company'=>$update_data['company'],'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $admin_mail.','.$mgmt_mail;
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $from;
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "Customer Details Modification Notification";
					$param['subject'] = $subject;

					// $this->email_template_model->sent_email($param);
				
					$this->session->set_flashdata('confirm', array('Customer Details Updated!'));
					redirect('clients');
				}
			} 
			else 
			{
				//insert
				if ($newid = $this->client_model->insert_client($update_data))
				{	
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

					$from = $this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails = $arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail = implode(',',$mangement_email);
					$admin_mail=implode(',',$arrSetEmails);		
					$varEmailRecipients=implode(',',$arrSetEmails);
					$subject='New Customer Creation Notification';

					//email sent by email template
					$param = array();
					
					// $param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'first_name'=>$update_data['first_name'],'last_name'=>$update_data['last_name'],'company'=>$update_data['company'],'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $mgmt_mail;
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $from;
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "New Customer Creation";
					$param['subject'] = $subject;

					// $this->email_template_model->sent_email($param);

					if ($ajax == false)
					{
						$this->session->set_flashdata('confirm', array('New Client Added!'));
						redirect('clients');
					} 
					else 
					{
						$json['error'] = false;
						$json['custid'] = $newid;
						$json['cust_name1'] = $pst_data['first_name'] . ' ' . $pst_data['last_name'] . ' - ' . $pst_data['company'];
						$json['cust_name'] = $pst_data['first_name'] . ' ' . $pst_data['last_name'];
						$json['cust_email'] = $pst_data['email_1'];
						$json['cust_company'] = $pst_data['company'];
						$json['cust_reg'] = $pst_data['add1_region'];
						$json['cust_cntry'] = $pst_data['add1_country'];
						$json['cust_ste'] = $pst_data['add1_state'];
						$json['cust_locn'] = $pst_data['add1_location'];
						echo json_encode($json);
					}
				}
			}
		}
    }
	
	function delete_customer($id = false)
	{
		if ($this->session->userdata('delete')==1) 
		{	
			if (preg_match('/^[0-9]+$/', $id)) 
			{
				// check to see if this customer has a job on the system before deleting
				$leads = $this->db->get_where($this->login_model->cfg['dbpref'] . 'leads', array('custid_fk' => $id));
				if ($leads->num_rows() > 0) 
				{
					$this->session->set_flashdata('login_errors', array('Cannot delete customer with exiting lead records!'));
					redirect('customers');
					exit();
				}
				$this->client_model->delete_customer($id);
				$this->session->set_flashdata('confirm', array('Customer Record Deleted!'));
				redirect('customers');
			}
		}
		else 
		{
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('customers');
		}
	}
	
	/*
	*@Check User Status
	*/
	function ajax_chk_status_customer()
	{
		$data =	real_escape_array($this->input->post()); // escape special characters
		$this->client_model->check_customer_status($data);
	}
	
	/*
	*@Get Country Record for adding New Client Page
	*@get_countries method
	*@copy of getCountry
	*/
	function get_countries($value,$id,$updt)
	{
		$data = array();
		$data = $this->regionsettings_model->getcountry_list($value);
		$opt  = '';
		$opt .= '<select name="country_id" id="country_id" class="textfield width200px" onchange="getState(this.value)">';
		$opt .= '<option value="0">Select Country</option>';
		if(sizeof($data)>0){
			foreach($data as $country){
				if($id == $country['countryid']) 
				$opt .= '<option value="'.$country['countryid'].'" selected = "selected" >'.$country['country_name'].'</option>';			
				else 
				$opt .= '<option value="'.$country['countryid'].'">'.$country['country_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		//Code for Adding New Country in Client Page.
		if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2) {
			if ($updt != "update") {
				$opt .= "<a class='addNew' id='addButton' onclick='ajxCty()'></a>";
			}
		}	
		$opt .= "<div id='addcountry' class='addCus'>";
		$opt .= "Add Country: <input type='text' class='textfield width200px required' name='addcountry' id='newcountry'>";
		$opt .= "<a class='addSave' id='savecountry' onclick='ajxSaveCty()'></a>";
		$opt .= "</div>";
		echo $opt;
	}
	
	/*
	*@Get State List Record for adding New Client Page
	*@get_countries method
	*@copy of getState
	*/
	function get_states($value,$id,$updt)
	{
		$data=array();
		$data = $this->regionsettings_model->getstate_list($value);
		$opt = '';
		$opt .= '<select name="state_id" id="state_id" onchange="getLocation(this.value)" class="textfield width200px">';
		$opt .= '<option value="0">Select State</option>';

		foreach($data as $state){
			if($id == $state['stateid']) 
			$opt .= '<option value="'.$state['stateid'].'" selected = "selected" >'.$state['state_name'].'</option>';			
			else 
			$opt .= '<option value="'.$state['stateid'].'">'.$state['state_name'].'</option>';			
		}
		$opt .= '</select>';
		//Code for Adding New State in Client Page.
		if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3) {
			if ($updt != "update") {
				$opt .= "<a class='addNew' id='addStButton' onclick='ajxSt()'></a>";
			}
		}	
		$opt .= "<div id='addstate' class='addCus'>";
		$opt .= "Add State : <input type='text' class='textfield width200px required' name='addstate' id='newstate' />";
		$opt .= "<a class='addSave' id='savestate' onclick='ajxSaveSt()'></a>";
		$opt .= "</div>";
		echo $opt;
	}
	
	/*
	*@Get location List Record for adding New Client Page
	*@get_locations method
	*@copy of getLocation
	*/
	public function get_locations($value, $id, $updt)
	{
		$data = array();
		$data = $this->regionsettings_model->getlocation_list($value);
		$opt  = '';
		$opt .= '<select name="location_id" id="location_id" class="textfield width200px">';
		$opt .= '<option value="0">Select Location</option>';
		if(sizeof($data)>0){
			foreach($data as $location){
				if($id == $location['locationid']) 
				$opt .= '<option value="'.$location['locationid'].'" selected = "selected" >'.$location['location_name'].'</option>';			
				else 
				$opt .= '<option value="'.$location['locationid'].'">'.$location['location_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		
		//Code for Adding New Location in Customer Page.
		if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4) {
			if ($updt != "update") {
				$opt .= "<a class='addNew' id='addLocButton' onclick='ajxLoc()'></a>";
			}
		}	
		$opt .= "<div id='addLocation' class='addCus'>";
		$opt .= "Add Location: <input type='text' class='textfield width200px required' name='addlocation' id='newlocation' />";
		$opt .= "<a class='addSave' id='savelocation' onclick='ajxSaveLoc()'></a>";
		$opt .= "</div>";
		echo $opt;
	}
	
	/*
	*@Checking duplication country, state & location before insert on ajax method for adding new client page
	*@Client Controller
	*@Method check_duplication
	*/
	function check_duplication()
	{
		$data = real_escape_array($this->input->post());
		switch ($data['type'])
		{
			case 'country':
				$res = $this->client_model->check_csl($data['type'], 'country_name', $data['country_name']);
			break;
			case 'state':
				$res = $this->client_model->check_csl($data['type'], 'state_name', $data['state_name']);
			break;
			case 'location':
				$res = $this->client_model->check_csl($data['type'], 'location_name', $data['location_name']);
			break;
		}
		if( $res == 0 )
		echo 'userOk';
		else
		echo 'userNo';
	}
	
	/*
	*@Adding Country, State & Location on ajax method for adding new client page
	*@Client Controller
	*@Method ajax_add_csl
	*@csl - Country, State & Location
	*/
	function ajax_add_csl()
	{
		$data 					 = real_escape_array($this->input->post());
		$ins_data 				 = array();
		$ins_data['created_by']  = $this->userdata['userid'];
		$ins_data['modified_by'] = $this->userdata['userid'];
		$ins_data['created']     = date('Y-m-d H:i:s');
		$ins_data['modified']    = date('Y-m-d H:i:s');
		switch ($data['type'])
		{
			case 'country':
				$ins_data['regionid']     = $data['regionid'];
				$ins_data['country_name'] = $data['country_name'];
				
				$res = $this->regionsettings_model->insert_country($ins_data);
				$this->get_countries($data['regionid'], $res);
			break;
			case 'state':
				$ins_data['countryid']    = $data['countryid'];
				$ins_data['state_name'] = $data['state_name'];
				
				$res = $this->regionsettings_model->insert_state($ins_data);
				$this->get_states($data['countryid'], $res);
			break;
			case 'location':
				$ins_data['stateid']      = $data['stateid'];
				$ins_data['location_name'] = $data['location_name'];
				
				$res = $this->regionsettings_model->insert_location($ins_data);
				$this->get_locations($data['stateid'], $res);
			break;
		}
	}
	
}
?>