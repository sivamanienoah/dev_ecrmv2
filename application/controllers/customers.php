<?php
class Customers extends crm_controller {
    
	public $userdata;
	private $import_dryrun = FALSE;
	
	
    function __construct() {
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('customer_model');
		$this->load->model('customer_contact_model');		
        $this->load->model('regionsettings_model');
		$this->load->model('email_template_model');
        $this->load->library('validation');
    }
    
    function index($limit = 0, $search = false) {
		$default = array('last_name', 'asc');
		if (!$this->session->userdata('customer_sort')) {
			$this->session->set_userdata('customer_sort', $default);
		}
		
		$current = $this->session->userdata('customer_sort');
		$data['current_sort'] = $current;
        $data['customers'] = $this->customer_model->customer_list($limit, rawurldecode($search), $current[0], $current[1]);

        if ($search == false) {           
			$config['base_url'] = $this->config->item('base_url') . 'customers/index/';
			$config['total_rows'] = (string) $this->customer_model->customer_count();
        }
        $this->load->view('customer_view', $data);
    }
	
	function set_search_order($type = 'last_name', $uri = 'customers') {
		$current = $this->session->userdata('customer_sort');
		$order = ($current[1] == 'asc') ? 'desc' : 'asc';
		$new = array($type, $order);
		$this->session->set_userdata('customer_sort', $new);
		redirect(base64_decode($uri));
	}
    
    function add_customer($update = false, $id = false, $ajax = false) 
	{
		$data['regions'] = $this->regionsettings_model->region_list();
		
		$arrUsers = $this->session->userdata('logged_in_user');
		
		$data['login_sales_contact_name']  = $arrUsers['first_name'].' '.$arrUsers['last_name'];
		$data['login_sales_contact_email'] = $arrUsers['email'];
		
	

       // $rules['first_name'] = "trim|required";
		// $rules['last_name'] = "trim|required";
		$rules['company'] = "trim|required";
		
		$rules['add1_region']    = "selected[add1_region]";
		$rules['add1_country'] = "selected[add1_country]";
		$rules['add1_state'] = "selected[add1_state]";
		$rules['add1_location'] = "selected[add1_location]";
			
		//$rules['phone_1'] = "trim|required";
		$rules['add1_postcode'] = "trim";
		//$rules['email_1'] = "trim|required|valid_email";
		if ($update == 'update') {
			// $rules['email_1'] = "trim|required|valid_email";
		}
		else {			
			// $rules['email_1']	= "required|valid_email|callback_email_1_check";
		}
		$this->validation->set_rules($rules);	
		
		//Entry to customer company table
		$fields['company'] = "Company";
		$fields['add1_line1'] = "";
		$fields['add1_line2'] = "";
		$fields['add1_suburb'] = "";		
		$fields['add1_postcode'] = "Postcode";
		$fields['add1_region'] = "Region";
		$fields['add1_country'] = "Country";
		$fields['add1_state'] = "State";
		$fields['add1_location'] = "Location";
		
		$fields['phone_2'] = '';
		$fields['phone_3'] = '';
		$fields['phone_4'] = '';
		$fields['email_2'] = "";
		$fields['email_3'] = "";
		$fields['email_4'] = "";
		$fields['skype_name'] = '';
		
		$fields['is_client'] = '';
		$fields['www_1'] = "Primary Web Address";
		$fields['www_2'] = "";
		// $fields['sales_contact_name'] = 'Sales Contact Name';
		$fields['sales_contact_userid_fk'] = 'Sales Contact ID';
		// $fields['sales_contact_email'] = 'Sales Contact Email';
        $fields['comments'] = '';
		$fields['client_code'] = '';
		
		
		/*$fields['first_name'] = "First Name";
		$fields['last_name'] = "Last Name";
		$fields['position_title'] = 'Position';
		$fields['phone_1'] = "Phone Number";
		$fields['email_1'] = "Primary Email Address"; */
		
		
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');

		$pst_data = real_escape_array($this->input->post());
		
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($pst_data['update_customer']))
		{
            $customer = $this->customer_model->get_customer($id);
         
			if($customer[0]['sales_contact_userid_fk']!='0') {
				$data['sales_person_detail'] = $this->customer_model->get_records_by_id('users', array('userid'=>$customer[0]['sales_contact_userid_fk']));
			}
			$data['customer_contacts'] 	= $this->customer_contact_model->get_customer_contacts($id);
			$data['client_projects'] 	= $this->customer_model->get_records_by_num('leads', array('custid_fk'=>$id, 'pjt_status !='=>0));
			
			if($data['client_projects'] !=0) {
				$this->customer_model->customer_update($id, array('is_client'=>1));		
			}
			
			//echo '<!--' . print_r($customer, true) . '-->';
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) 
			{
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
			
			//echo '<pre>'; print_r($this->validation);exit;
        }
		
		if ($this->validation->run() == false) 
		{
			
            if ($ajax == false) {
                $this->load->view('customer_add_view', $data);
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
					$update_data[$key] = $pst_data[$key];
				}
            }

			if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) 
			{
				// set exported back to NULL so it will be exported to addressbook
				$update_data['exported'] = NULL;
				
				//update
				if ($this->customer_model->update_customer($id, $update_data)) 
				{
					if (isset($pst_data['first_name']))
					{
						//echo'<pre>';print_r($pst_data);exit;
						$contact_id	=	$pst_data['contact_id'];
						$first_name	=	$pst_data['first_name'];
						$last_name	=	$pst_data['last_name'];
						$position	=	$pst_data['position_title'];
						$phone_no	=	$pst_data['phone_no'];
						$email		=	$pst_data['email'];
						$batch_insert_data	=	array();
						
						for($i=0;$i<count($first_name);$i++)
						{
							$cust_data					=	array();
							$cust_data['first_name']	=	$first_name[$i];
							$cust_data['last_name']		=	$last_name[$i];
							$cust_data['position_title']=	$position[$i];
							$cust_data['phone_no']		=	$phone_no[$i];
							$cust_data['email']			=	$email[$i];
							//echo $contact_id[$i].'SS<pre>';print_r($cust_data);
							if($contact_id[$i])
							{
								$this->customer_contact_model->update_customer_contacts($cust_data,$contact_id[$i]);
							}else{
								$cust_data['company_id_fk']	=	$id;
								$batch_insert_data[]		=	$cust_data;
							}
							
						}
						
						//echo'<pre>';print_r($batch_insert_data);exit;
						if(count($batch_insert_data))
						{
							$this->customer_contact_model->insert_customer_contacts($batch_insert_data);
						}
					}
					
					
					
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

					$from			 = $this->userdata['email'];
					$arrEmails       = $this->config->item('crm');
					$arrSetEmails 	 = $arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail 		 = implode(',',$mangement_email);		
					$admin_mail		 = implode(',',$arrSetEmails);
					$subject 		 = 'Customer Details Modification Notification';

					//email sent by email template
					$param = array();
					
					$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'first_name'=>$update_data['first_name'],'last_name'=>$update_data['last_name'],'company'=>$update_data['company'],'signature'=>$this->userdata['signature']);

					$param['to_mail'] 	      = $admin_mail.','.$mgmt_mail;
					$param['bcc_mail'] 		  = $admin_mail;
					$param['from_email']	  = $from;
					$param['from_email_name'] = $user_name;
					$param['template_name']	  = "Customer Details Modification Notification";
					$param['subject']		  = $subject;

					$this->email_template_model->sent_email($param);
				
					$this->session->set_flashdata('confirm', array('Customer Details Updated!'));
					redirect('customers');
				}
					
			} 
			else 
			{
			
				//insert
				if ($newid = $this->customer_model->insert_customer($update_data)) 
				{	
					//Entry to customer table
			
					
				if (isset($pst_data['first_name']))
				{
					$first_name	=	$pst_data['first_name'];
					$last_name	=	$pst_data['last_name'];
					$position	=	$pst_data['position_title'];
					$phone_no	=	$pst_data['phone_no'];
					$email		=	$pst_data['email'];
					$batch_insert_data	=	array();
					for($i=0;$i<count($first_name);$i++)
					{
						$cust_data					=	array();
						$cust_data['company_id_fk']	=	$newid;
						$cust_data['first_name']	=	$first_name[$i];
						$cust_data['last_name']		=	$last_name[$i];
						$cust_data['position_title']=	$position[$i];
						$cust_data['phone_no']		=	$phone_no[$i];
						$cust_data['email']			=	$email[$i];
						
						$batch_insert_data[]		=	$cust_data;
					}
					//echo'<pre>';print_r($batch_insert_data);exit;
					$this->customer_contact_model->insert_customer_contacts($batch_insert_data);
				}
			
			
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));	

					$from=$this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail = implode(',',$mangement_email);
					$admin_mail=implode(',',$arrSetEmails);		
					$varEmailRecipients=implode(',',$arrSetEmails);
					$subject='New Customer Creation Notification';

					//email sent by email template
					$param = array();
					
					$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'first_name'=>$update_data['first_name'],'last_name'=>$update_data['last_name'],'company'=>$update_data['company'],'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $mgmt_mail;
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $from;
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "New Customer Creation";
					$param['subject'] = $subject;

					$this->email_template_model->sent_email($param);

					if ($ajax == false) 
					{
						$this->session->set_flashdata('confirm', array('New Customer Added!'));
						redirect('customers');
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
	
	function custom_update_customer()
	{
		$res = array();
		$post_data = real_escape_array($this->input->post());
		// echo "<pre>"; print_r($post_data); exit;
		unset($post_data['sales_contact_name']);
		unset($post_data['sales_contact_email']);
		unset($post_data['addcountry']);
		unset($post_data['addlocation']);
		unset($post_data['addstate']);
		
		$id = $post_data['custid'];
		if ($this->customer_model->update_customer($id, $post_data))
		$res['result'] = 'ok';
		
		echo json_encode($res);
	}
	
	function email_1_check($email) 
	{
		if ($this->customer_model->primary_mail_check($email) == 0) 
		{
			return true;			
		} 
		else 
		{
			$this->validation->set_message('email_1_check', 'The %s is already exist.');
			return false;
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
				$this->customer_model->delete_customer($id);
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
	
    
    function search() {
        if (isset($_POST['cancel_submit'])) {
            redirect('customers/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('customers/index/0/' . rawurlencode($name));
        } else {
            redirect('customers/');
        }
    }	
	
    function import() {
		$this->login_model->check_login();		
        $page['error'] = $page['msg'] = '';
        
        if (isset($_FILES['card_file']) && is_uploaded_file($_FILES['card_file']['tmp_name'])) {
            $filename = mt_rand(111, 999) . microtime() . '.csv';
            move_uploaded_file($_FILES['card_file']['tmp_name'], 'vps_temp_data/' . $filename);            
            $fp = fopen('vps_temp_data/' . $filename, 'r');            
            $data = fgetcsv($fp);            
            if ($data && count($data) == 125) {
                $customers = array();
                $i = 0;
                while ($data = fgetcsv($fp)) {
                    if ($data[0] != 'Co./Last Name') {
                        $customers[$i]['first_name'] = $data[1];
                        $customers[$i]['company'] = $data[0];
                        $customers[$i]['abn'] = $data[112];
                        $customers[$i]['add1_line1'] = $data[4];
                        $customers[$i]['add1_line2'] = $data[5];
                        $customers[$i]['add1_suburb'] = $data[8];
                        $customers[$i]['add1_state'] = $data[9];
                        $customers[$i]['add1_postcode'] = $data[10];
                        $customers[$i]['add1_country'] = $data[11];
                        $customers[$i]['phone_1'] = $data[12];
                        $customers[$i]['phone_2'] = $data[13];
                        $customers[$i]['phone_3'] = $data[14];
                        $customers[$i]['phone_4'] = $data[15];
                        $customers[$i]['email_1'] = $data[16];
                        $customers[$i]['email_2'] = $data[32];
                        $customers[$i]['www_1'] = $data[17];
                        $customers[$i]['www_2'] = $data[33];
                    }
                    $i++;    
                }
                
                if ( $result = $this->customer_model->import_list($customers) ) {
                    $page['msg'] = '<p class="msg">Card File Import Successful!<br />' . $result . ' New cards added to the list.</p>';
                } else {
					$page['error'] = '<p class="error">Card Import Failed!</p>';
                }
                
            } else {
                $page['error'] = '<p class="error">Incorrect File Format</p>';
            }            
            fclose($fp);
            
        } else if (isset($_FILES['card_file'])) {
            $page['error'] = '<p class="error">No File Uploaded!</p>';
        }
        
        $this->load->view('customer_import_view', $page);
        
    }
	
	function import_customers_csv($mode = '', $dryrun = FALSE) {
		if ($dryrun == 'dry')
		$this->import_dryrun = TRUE;
		else
		$this->import_dryrun = FALSE;
		
		if ($mode != 'state' && $mode != 'list')
		return;
	
		$file_source = dirname(FCPATH) . '/customer_import/';
		$processed = $file_source . "processed/";
		
		$list = glob($file_source . "*.csv");
		
		$state_array = array(
						0 => 'Office',
						1 => 'Attention',
						2 => 'Street',
						3 => 'Suburb',
						4 => 'State',
						5 => 'Postcode',
						6 => 'Phone',
						7 => 'Fax',
						8 => 'Mobile',
						9 => 'Email'
		);
		
		$list_array = array(
						0 => 'Attention',
						1 => 'Office',
						2 => 'State',
						3 => 'Phone',
						4 => 'Mobile',
						5 => 'Email',
						6 => 'Position'
		);
		
		$html = '';
		
		if ($this->import_dryrun)
		$html .= '<h2>DRY RUN ONLY</h2>';
		
		foreach ($list as $file) {
			$html .= "<h4>{$file}</h4>";
			
			$total = $insert = $update = 0;
			
			$fp = fopen($file, 'r');
			while ($row = fgetcsv($fp)) {
				$total ++; // increment				
				if ($mode == 'state') {
					if (count($row) < 11)
					continue;					
					if ( ! filter_var($row[10], FILTER_VALIDATE_EMAIL))
					continue;					
					$name = explode(' ', $row[2]);					
					$data = array();
					$data['first_name'] = $name[0];
					$data['last_name'] = '';					
					if (isset($name[1]))
					$data['last_name'] = $name[1];
					$data['company'] = $row[0];
					$data['phone_1'] = $row[7];
					$data['phone_3'] = $row[9];
					$data['phone_4'] = $row[8]; 
					$data['email_1'] = $row[10];
					$data['add1_line1'] = $row[3];
					$data['add1_suburb'] = $row[4];
					$data['add1_state'] = $row[5];
					
					$rs = $this->manage_customer($data);					
					if ($rs == 'UPDATE')
					$update++;
					if ($rs == 'INSERT')
					$insert++;
				} else if ($mode == 'list') {
					if (count($row) != 7)
					continue;					
					if ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $row[5]))
					continue;
					$name = explode(' ', $row[0]);					
					$data = array();
					$data['first_name'] = $name[0];
					$data['last_name'] = '';
					
					if (isset($name[1]))
					$data['last_name'] = $name[1];					
					$data['position_title'] = $row[6];
					$data['company'] = $row[1];					
					$data['phone_1'] = $row[3];
					$data['phone_3'] = $row[4]; 
					$data['email_1'] = $row[5];
					$data['company'] = $row[1];					
					$rs = $this->manage_customer($data);
					
					if ($rs == 'UPDATE')
					$update++;
					if ($rs == 'INSERT')
					$insert++;
				}
			}
			fclose($fp);
			
			$html .= "<p>Total: {$total} | Inserts: {$insert} | Updates: {$update}</p>";
		}
		
		echo $html;
	}
	
	function manage_customer($data) {
		$res = $this->customer_model->get_customer_data($data['email_1']);
		$details = $data;
		//echo $details; exit;
		unset($details['email_1']);
		
		if (!empty($res)) {
			$this->update_customer($res['custid'], $details);			
			return 'UPDATE';
		} else {
			$id = $this->insert_customer($data);
			$this->update_customer($id, $details);			
			return 'INSERT';
		}
	}
	
	function insert_customer($data) {
		if ($this->import_dryrun)
		return TRUE;
		$ins_id = $this->customer_model->get_customer_insert_id($data);
		return ins_id;
	}
	
	function update_customer($id, $data) {
		if ($this->import_dryrun)
		return TRUE;
		$this->customer_model->customer_update($id,$data);
	}
	
	//checking primary_mail in customer table
	function Check_email() {
		$res = $this->customer_model->primary_mail_check($this->input->post('email'));
		if($res == 0)
		echo 'userOk';
		else
		echo 'userNo';
	}
	
	//Function for checking the Existing Country for adding new customers page.
	function getCtyRes($newCty,$regionId){
		$res = $this->customer_model->check_csl('country', 'country_name', $newCty);
		if($res == 0 ) 
		echo 'userOk';
		else 
		echo 'userNo';
	}
	
	//Function for checking the Existing State for adding new customers page.
	function getSteRes($newSte,$cntyId){
		$res = $this->customer_model->check_csl('state', 'state_name', $newSte);
		if($res == 0 ) 
		echo 'userOk';
		else 
		echo 'userNo';
	}
	
	//Function for checking the Existing Location for adding new customers page.
	function getLocRes($newLoc,$stId){
		$res = $this->customer_model->check_csl('location', 'location_name', $newLoc);
		if($res == 0 )  
		echo 'userOk';
		else 
		echo 'userNo';
	}	

	
	/*  Import Load Function this fuction import customer list from CSV, XLS & XLSX files
	 *	Starts here Dated on 29-01-2013
	 */
	function importload() {
		$count = 0;
		$this->load->library('excel_read');
		$this->login_model->check_login();		
	    $page['error'] = $page['msg'] = '';	
		$objReader = new Excel_read();
		if(isset($_FILES['card_file']['tmp_name'])) {
			$strextension=explode(".",$_FILES['card_file']['name']);			
		 	if ($strextension[1]=="csv" || $strextension[1]=="xls" || $strextension[1]=="xlsx" || $strextension[1]=="CSV") {		
			$impt_data = $objReader->parseSpreadsheet($_FILES['card_file']['tmp_name']);
			for($i=2; $i<=count($impt_data); $i++) {
				// if(empty($impt_data[$i]['A']) || empty($impt_data[$i]['B']) || empty($impt_data[$i]['I']) || empty($impt_data[$i]['J']) || empty($impt_data[$i]['K']) || empty($impt_data[$i]['L']) || empty($impt_data[$i]['Q'])) {
				if(empty($impt_data[$i]['A']) || empty($impt_data[$i]['I']) || empty($impt_data[$i]['J']) || empty($impt_data[$i]['K']) || empty($impt_data[$i]['L'])) {
					$empty_error[] = $impt_data[$i]['A'];
				} else {
					// if(!empty($impt_data[$i]['A']) && !empty($impt_data[$i]['B']) && !empty($impt_data[$i]['I']) && !empty($impt_data[$i]['J']) && !empty($impt_data[$i]['K']) && !empty($impt_data[$i]['L']) && !empty($impt_data[$i]['Q'])) {
					if(!empty($impt_data[$i]['A']) && !empty($impt_data[$i]['I']) && !empty($impt_data[$i]['J']) && !empty($impt_data[$i]['K']) && !empty($impt_data[$i]['L'])) {
					
						if (!empty($impt_data[$i]['Q'])) {
							$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
							if(preg_match($regex, $impt_data[$i]['Q'])) {
								//$numrows = $this->customer_model->primary_mail_check($impt_data[$i]['Q']);
								
								// if($numrows != 0){
									// $email_exit[] = $impt_data[$i]['Q'];
								// } else {
									if(!empty($impt_data[$i]['I']))
									$strreg = $this->customer_model->get_rscl_id('', '', 'region', ucwords(strtolower($impt_data[$i]['I'])));
									
									// Country
									if(!empty($impt_data[$i]['J']))
									$strcunt = $this->customer_model->get_rscl_id($strreg, 'regionid', 'country', ucwords(strtolower($impt_data[$i]['J'])));							
									// State
									if(!empty($impt_data[$i]['K']))
									$strstate = $this->customer_model->get_rscl_id($strcunt, 'countryid', 'state', ucwords(strtolower($impt_data[$i]['K'])));							
									// Location
									if(!empty($impt_data[$i]['L']))
									$strlid = $this->customer_model->get_rscl_id($strstate, 'stateid', 'location', ucwords(strtolower($impt_data[$i]['L'])));
									//insert customers here
									$args = array( 'first_name' => $impt_data[$i]['A'], 'last_name' => $impt_data[$i]['B'], 'position_title' => $impt_data[$i]['C'], 'company' => $impt_data[$i]['D'], 'add1_line1' => $impt_data[$i]['E'], 'add1_line2' => $impt_data[$i]['F'], 'add1_suburb' => $impt_data[$i]['G'], 'add1_postcode' => $impt_data[$i]['H'], 'add1_region' => $strreg, 'add1_country' => $strcunt, 'add1_state' => $strstate, 'add1_location' => $strlid, 'phone_1' => $impt_data[$i]['M'], 'phone_2' => $impt_data[$i]['N'], 'phone_3' => $impt_data[$i]['O'], 'phone_4' => $impt_data[$i]['P'], 'email_1' => $impt_data[$i]['Q'], 'email_2' => $impt_data[$i]['R'], 'email_3' => $impt_data[$i]['S'], 'email_4' => $impt_data[$i]['T'], 'skype_name' => $impt_data[$i]['U'], 'www_1' => $impt_data[$i]['V'], 'www_2' => $impt_data[$i]['W'], 'comments' => $impt_data[$i]['X'] );
									$this->customer_model->insert_customer_upload($args);
									$count=$count+1;
								// }
							} else {
								$email_invalid[]= $impt_data[$i]['Q'];
							}
						} else {

						// if($numrows != 0){
							// $email_exit[] = $impt_data[$i]['Q'];
						// } else {
							// $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
							// if(preg_match($regex, $impt_data[$i]['Q'])) {
								// Region
								if(!empty($impt_data[$i]['I']))
								$strreg = $this->customer_model->get_rscl_id('', '', 'region', ucwords(strtolower($impt_data[$i]['I'])));
								
								// Country
								if(!empty($impt_data[$i]['J']))
								$strcunt = $this->customer_model->get_rscl_id($strreg, 'regionid', 'country', ucwords(strtolower($impt_data[$i]['J'])));							
								// State
								if(!empty($impt_data[$i]['K']))
								$strstate = $this->customer_model->get_rscl_id($strcunt, 'countryid', 'state', ucwords(strtolower($impt_data[$i]['K'])));							
								// Location
								if(!empty($impt_data[$i]['L']))
								$strlid = $this->customer_model->get_rscl_id($strstate, 'stateid', 'location', ucwords(strtolower($impt_data[$i]['L'])));
								//insert customers here
								$args = array( 'first_name' => $impt_data[$i]['A'], 'last_name' => $impt_data[$i]['B'], 'position_title' => $impt_data[$i]['C'], 'company' => $impt_data[$i]['D'], 'add1_line1' => $impt_data[$i]['E'], 'add1_line2' => $impt_data[$i]['F'], 'add1_suburb' => $impt_data[$i]['G'], 'add1_postcode' => $impt_data[$i]['H'], 'add1_region' => $strreg, 'add1_country' => $strcunt, 'add1_state' => $strstate, 'add1_location' => $strlid, 'phone_1' => $impt_data[$i]['M'], 'phone_2' => $impt_data[$i]['N'], 'phone_3' => $impt_data[$i]['O'], 'phone_4' => $impt_data[$i]['P'], 'email_1' => $impt_data[$i]['Q'], 'email_2' => $impt_data[$i]['R'], 'email_3' => $impt_data[$i]['S'], 'email_4' => $impt_data[$i]['T'], 'skype_name' => $impt_data[$i]['U'], 'www_1' => $impt_data[$i]['V'], 'www_2' => $impt_data[$i]['W'], 'comments' => $impt_data[$i]['X'] );
								$this->customer_model->insert_customer_upload($args);
								$count=$count+1;
							}
							// } else {
								// $email_invalid[]= $impt_data[$i]['Q'];
							// }
						// }
					}
				}
			}
			$data['invalidemail']=$email_invalid;
			$data['succcount']=$count;
			// $data['dupsemail']=$email_exit;
			$data['empty_error']=$empty_error;				
			//echo "<pre>"; print_r($data); exit;			
			$this->load->view('success_import_view', $data);
		 	} else {
		 		$page['error'] = '<p class="error">Please Upload CSV, XLS File only!</p>';
		    	$this->load->view('customer_import_view', $page);		
		 	}
		} else {
			$page['error'] = '<p class="error">Please Upload the file!</p>';
			$this->load->view('customer_import_view', $page);
		}
	/*Ends here*/ 
	}

	
	/*
	*@Check User Status
	*
	*/
	function ajax_chk_status_customer()
	{
		$data =	real_escape_array($this->input->post()); // escape special characters
		$this->customer_model->check_customer_status($data);
	}
	
}
?>