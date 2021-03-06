<?php
class Customers extends crm_controller {
    
	public $userdata;
	private $import_dryrun = FALSE;
	
	
    function __construct() {
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('customer_model');	
        $this->load->model('regionsettings_model');
		$this->load->model('email_template_model');
        $this->load->library('validation');
    }
    
    function index_old($limit = 0, $search = false) {
		$default = array('last_name', 'asc');
		if (!$this->session->userdata('customer_sort')) {
			$this->session->set_userdata('customer_sort', $default);
		}
		
		$current = $this->session->userdata('customer_sort');
		$data['current_sort'] = $current;
        $data['customers'] = $this->customer_model->customer_list($limit, rawurldecode($search), $current[0], $current[1]);

        if ($search == false) {           
			$config['base_url']   = $this->config->item('base_url') . 'customers/index/';
			$config['total_rows'] = (string) $this->customer_model->customer_count();
        }
        $this->load->view('customer_view', $data);
    }
	
	/*
	*Listing all the customers based on level & role
	*/
	function index($limit = 0, $search = false) 
	{
		$default = array('last_name', 'asc');
		if (!$this->session->userdata('customer_sort')) {
			$this->session->set_userdata('customer_sort', $default);
		}
		
		$current = $this->session->userdata('customer_sort');
		$data['current_sort'] = $current;
        $data['customers'] = $this->customer_model->company_list($limit, rawurldecode($search), $current[0], $current[1]);
		// echo $this->db->last_query(); die;

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
		
		$rules['company'] = "trim|required";
		
		$rules['add1_region']   = "selected[add1_region]";
		$rules['add1_country']  = "selected[add1_country]";
		$rules['add1_state']    = "selected[add1_state]";
		$rules['add1_location'] = "selected[add1_location]";
		$rules['add1_postcode'] = "trim";
		//$rules['email_1'] = "trim|required|valid_email";
		if ($update == 'update') {
			// $rules['email_1'] = "trim|required|valid_email";
		}
		else {			
			// $rules['email_1']	= "required|valid_email|callback_email_1_check";
		}
		$this->validation->set_rules($rules);	
		
		$fields['company'] = "Company";
		$fields['add1_line1'] = "";
		$fields['add1_line2'] = "";
		$fields['add1_suburb'] = "";		
		$fields['add1_postcode'] = "Postcode";
		$fields['add1_region'] = "Region";
		$fields['add1_country'] = "Country";
		$fields['add1_state'] = "State";
		$fields['add1_location'] = "Location";
		$fields['phone'] = '';
		// $fields['phone_3'] = '';
		// $fields['phone_4'] = '';
		$fields['email_2'] = "";
		// $fields['email_3'] = "";
		// $fields['email_4'] = "";
		$fields['fax'] = "";
		// $fields['skype_name'] = '';
		$fields['is_client'] = '';
		$fields['www'] = "Web Address";
		// $fields['www_2'] = "";
		// $fields['sales_contact_name'] = 'Sales Contact Name';
		$fields['sales_contact_userid_fk'] = 'Sales Contact ID';
		// $fields['sales_contact_email'] = 'Sales Contact Email';
        $fields['comments'] = '';
		$fields['client_code'] = '';
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');

		$pst_data = real_escape_array($this->input->post());
		
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($pst_data['update_customer']))
		{
            $customer = $this->customer_model->get_company($id);
			$data['customer_contacts'] 	= $this->customer_model->get_customer_contacts($id);
			/* if($customer[0]['sales_contact_userid_fk']!='0') {
				$data['sales_person_detail'] = $this->customer_model->get_records_by_id('users', array('userid'=>$customer[0]['sales_contact_userid_fk']));
			}

			$data['client_projects'] = $this->customer_model->get_records_by_num('leads', array('custid_fk'=>$id, 'pjt_status !='=>0));
			echo $this->db->last_query(); exit;
			if($data['client_projects'] !=0) {
				echo "<pre>"; print_r($data['client_projects']); die;
				// $this->customer_model->customer_update($id, array('is_client'=>1));		
				$this->customer_model->customer_update_isclient($id, array('is_client'=>1));
			} */
			
			//echo '<!--' . print_r($customer, true) . '-->';
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) 
			{
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
			
			//echo '<pre>'; print_r($this->validation);exit;
        }
		
		if ($this->validation->run() == false) 
		{
			//echo'<pre>'; print_r($data);exit;
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
				// $update_data['exported'] = NULL;
				// echo "<pre>"; print_r($update_data);
				$update_data['modified_by'] = $this->userdata['userid'];
				//update
				if ($this->customer_model->update_company($id, $update_data))
				{
					$ins_log 					= array();
					$ins_log['jobid_fk']    	= 0;
					$ins_log['userid_fk']   	= $this->userdata['userid'];
					$ins_log['date_created'] 	= date('Y-m-d H:i:s');
					$ins_log['log_content'] 	= $update_data['company']." Company Detail modified On :" . " " . date('M j, Y g:i A');
					$log_res = $this->customer_model->insert_row("logs", $ins_log);
					
					if (isset($pst_data['customer_name']))
					{
						//echo'<pre>';print_r($pst_data);exit;
						$contact_id	=	$pst_data['contact_id'];
						$name		=	$pst_data['customer_name'];
						$skype		=	$pst_data['skype'];
						$position	=	$pst_data['position'];
						$phone_no	=	$pst_data['phone_no'];
						$email		=	$pst_data['email'];
						$batch_insert_data	= array();
						
						for($i=0;$i<count($name);$i++)
						{
							$cust_data					= array();
							$cust_data['customer_name']	= $name[$i];
							$cust_data['skype_name']	= $skype[$i];
							$cust_data['position_title']= $position[$i];
							$cust_data['phone_1']		= $phone_no[$i];
							$cust_data['email_1']		= $email[$i];
							$cust_data['modified_by'] 	= $this->userdata['userid'];
							// echo $contact_id[$i].'SS<pre>';print_r($cust_data);exit;
							if($contact_id[$i])
							{
								$cust_res = $this->customer_model->update_customer_contacts($cust_data, $contact_id[$i]);
								if($cust_res) {
									$ins_log 					= array();
									$ins_log['jobid_fk']    	= 0;
									$ins_log['userid_fk']   	= $this->userdata['userid'];
									$ins_log['date_created'] 	= date('Y-m-d H:i:s');
									$ins_log['log_content'] 	= $name[$i]." Contact Updated for the company ".$update_data['company']." On :" . " " . date('M j, Y g:i A');
									$log_res = $this->customer_model->insert_row("logs", $ins_log);
								}
								// echo $this->db->last_query();
							}else{
								$cust_data['company_id']				= $id;
								$cust_data['sales_contact_userid_fk'] 	= $this->userdata['userid'];
								$batch_insert_data[]					= $cust_data;
								
								$ins_log['log_content'] 	= $cust_data['customer_name']." Contact Created for the company ".$update_data['company']." On :" . " " . date('M j, Y g:i A');
								$ins_log['jobid_fk']    	= 0;
								$ins_log['date_created'] 	= date('Y-m-d H:i:s');
								$ins_log['userid_fk']   	= $this->userdata['userid'];
							
								$batch_insert_data_log[]	=	$ins_log;
							}
						}
						
						// echo'<pre>';print_r($batch_insert_data);exit;
						if(count($batch_insert_data))
						{
							$this->customer_model->insert_batch_customer($batch_insert_data, $batch_insert_data_log);
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
					
					$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'first_name'=>'','last_name'=>'','company'=>$update_data['company'],'signature'=>$this->userdata['signature']);

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
				$update_data['created_by']  = $this->userdata['userid'];
				$update_data['modified_by'] = $this->userdata['userid'];
				$update_data['created_on']  = date('Y-m-d H:i:s');
				if ($company_id	= $this->customer_model->insert_company($update_data))
				{	
					$ins_log = array();
					$ins_log['log_content'] 	= "Company ".$update_data['company']." Created On :".date('M j, Y g:i A');
					$ins_log['jobid_fk']    	= 0;
					$ins_log['date_created'] 	= date('Y-m-d H:i:s');
					$ins_log['userid_fk']   	= $this->userdata['userid'];
					$insert_log = $this->customer_model->insert_row('logs', $ins_log);
					
					//Entry to customer table
					if (isset($pst_data['customer_name']))
					{
						$customer_name = $pst_data['customer_name'];
						$skype	    =	$pst_data['skype'];
						$position	=	$pst_data['position'];
						$phone_no	=	$pst_data['phone_no'];
						$email		=	$pst_data['email'];
						$batch_insert_data = array();
						$batch_insert_data_log = array();
						for($i=0;$i<count($customer_name);$i++)
						{
							$cust_data					= array();
							//$cust_data				= $update_data;
							$cust_data['company_id']	= $company_id;
							$cust_data['customer_name']	= $customer_name[$i];
							$cust_data['skype_name']	= $skype[$i];
							$cust_data['position_title']= $position[$i];
							$cust_data['phone_1']		= $phone_no[$i];
							$cust_data['email_1']		= $email[$i];
							$cust_data['created_by'] 	= $this->userdata['userid'];
							$cust_data['created_on'] 	= date('Y-m-d H:i:s');
							
							$ins_log['log_content'] 	= $customer_name[$i]." Contact Created for the company ".$update_data['company']." On :" . " " . date('M j, Y g:i A');
							$ins_log['jobid_fk']    	= 0;
							$ins_log['date_created'] 	= date('Y-m-d H:i:s');
							$ins_log['userid_fk']   	= $this->userdata['userid'];
							
							$batch_insert_data[]		=	$cust_data;
							$batch_insert_data_log[]	=	$ins_log;
						}
						$this->customer_model->insert_batch_customer($batch_insert_data, $batch_insert_data_log);
					}
					
					$user_name = $this->userdata['name'];
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
					
					// $param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'first_name'=>$update_data['first_name'],'last_name'=>$update_data['last_name'],'company'=>$update_data['company'],'signature'=>$this->userdata['signature']);

					// $param['to_mail'] = $mgmt_mail;
					// $param['bcc_mail'] = $admin_mail;
					// $param['from_email'] = $from;
					// $param['from_email_name'] = $user_name;
					// $param['template_name'] = "New Customer Creation";
					// $param['subject'] = $subject;

					// $this->email_template_model->sent_email($param);

					if ($ajax == false) 
					{
						$this->session->set_flashdata('confirm', array('New Customer Added!'));
						redirect('customers');
					} 
					else 
					{
						$json['error'] = false;
						$json['custid'] = $newid;
						$json['cust_name1']   = $pst_data['customer_name'] . ' - ' . $pst_data['company'];
						$json['cust_name']    = $pst_data['customer_name'];
						$json['cust_email']   = $pst_data['email_1'];
						$json['cust_company'] = $pst_data['company'];
						$json['cust_reg'] 	  = $pst_data['add1_region'];
						$json['cust_cntry']   = $pst_data['add1_country'];
						$json['cust_ste'] 	  = $pst_data['add1_state'];
						$json['cust_locn']	  = $pst_data['add1_location'];
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
		
		$companyid = $post_data['companyid'];
		$custid    = $post_data['custid'];
		if ($this->customer_model->update_customer_details($companyid, $custid, $post_data))
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
				$this->db->select('custid');
				$this->db->where('company_id', $id);
				$sql = $this->db->get($this->cfg['dbpref'].'customers');
				$custid = $sql->result_array();
				if(!empty($custid)){
					foreach($custid as $rec)
					$custids[]= $rec['custid'];
				}
				
				if(!empty($custids)){
					$this->db->where_in('custid_fk', $custids);
					$leads = $this->db->get($this->cfg['dbpref'].'leads');
				} else {
					$this->customer_model->delete_customer($id);
					$this->session->set_flashdata('confirm', array('Customer Record Deleted!'));
					redirect('customers');
				}
				
				// $leads = $this->db->get_where($this->login_model->cfg['dbpref'] . 'leads', array('custid_fk' => $id));
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
	function Check_email()
	{
		$post_data = $this->input->post();
		
		$this->db->where('company_id', $post_data['company_id']);
		$this->db->where('email_1', $post_data['email']);
		if(0!=$post_data['custid']){
			$this->db->where('custid !=', $post_data['custid']);
		}
		$res = $this->db->get($this->cfg['dbpref'].'customers');
		
		// echo $this->db->last_query(); die;

		if($res->num_rows() == 0)
		echo 'userOk';
		else
		echo 'userNo';
	}


	//checking primary_mail in customer table
	function check_company() 
	{		
		$post_data = $this->input->post();
		
		$this->db->where('company', $post_data['company_name']);
		$this->db->where('add1_region', $post_data['add1_region']);
		$this->db->where('add1_country', $post_data['add1_country']);
		$this->db->where('add1_state', $post_data['add1_state']);
		$this->db->where('add1_location', $post_data['add1_location']);
		if($post_data['company_id']!=0){
			$this->db->where('companyid !=', $post_data['company_id']);
		}
		$res = $this->db->get($this->cfg['dbpref'].'customers_company');

		if($res->num_rows() == 0)
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
	function importload_old() {
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
		$res = $this->customer_model->check_customer_status($data);
		// echo $this->db->last_query(); die;
	}
	
	function import_customers()
	{
		$this->load->library('excel_read');
		
		$page = array();
		$count = $page['update_customers'] = $page['insert_customers'] = $page['update_contacts'] = $page['insert_contacts'] = 0;
	    $page['error'] = $page['msg'] = '';	
		
		$objReader = new Excel_read();
		
		if(isset($_FILES['card_file']['tmp_name'])) {
			
			$strextension=explode(".",$_FILES['card_file']['name']);			
		 	if ($strextension[1]=="csv" || $strextension[1]=="xls" || $strextension[1]=="xlsx" || $strextension[1]=="CSV") {		
				$impt_data = $objReader->parseSpreadsheet($_FILES['card_file']['tmp_name']);
				for($i=2; $i<=count($impt_data); $i++) {
					if(empty($impt_data[$i]['A']) || empty($impt_data[$i]['F']) || empty($impt_data[$i]['G']) || empty($impt_data[$i]['H']) || empty($impt_data[$i]['I']) || empty($impt_data[$i]['O']) || empty($impt_data[$i]['P']) || empty($impt_data[$i]['Q']) || empty($impt_data[$i]['R'])) {
						$empty_error[] = $i." row - ".$impt_data[$i]['A'];
					} else {
						//get the region, country, state & location id
						if(!empty($impt_data[$i]['F']))
						$strreg = $this->customer_model->get_rscl_id('', '', 'region', ucwords(strtolower($impt_data[$i]['F'])));
						// Country
						if(!empty($impt_data[$i]['G']))
						$strcunt = $this->customer_model->get_rscl_id($strreg, 'regionid', 'country', ucwords(strtolower($impt_data[$i]['G'])));
						// State
						if(!empty($impt_data[$i]['H']))
						$strstate = $this->customer_model->get_rscl_id($strcunt, 'countryid', 'state', ucwords(strtolower($impt_data[$i]['H'])));
						// Location
						if(!empty($impt_data[$i]['I']))
						$strlid = $this->customer_model->get_rscl_id($strstate, 'stateid', 'location', ucwords(strtolower($impt_data[$i]['I'])));
						//check customer company is exist or not
						$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
						$valid_email = 0;
						$cmp_email = '';
						$valid_email = preg_match($regex, $impt_data[$i]['L']);
						if($valid_email){
							$cmp_email = $impt_data[$i]['L'];
						} else {
							$page['invalid_email'][] = $i." row - ".$impt_data[$i]['L'];
						}
						$company_exists = $this->customer_model->check_customer_company($strreg, $strcunt, $strstate, $strlid, $impt_data[$i]['A'], $cmp_email);
						
						$cmp_details  = array();
						$cust_details = array();
						$cmp_details['company']  	  = $impt_data[$i]['A'];
						$cmp_details['add1_line1']	  = $impt_data[$i]['B'];
						$cmp_details['add1_line2']	  = $impt_data[$i]['C'];
						$cmp_details['add1_suburb']	  = $impt_data[$i]['D'];
						$cmp_details['add1_region']   = $strreg;
						$cmp_details['add1_country']  = $strcunt;
						$cmp_details['add1_state'] 	  = $strstate;
						$cmp_details['add1_location'] = $strlid;
						$cmp_details['add1_postcode'] = $impt_data[$i]['E'];
						$cmp_details['phone']		  = $impt_data[$i]['J'];
						$cmp_details['fax']			  = $impt_data[$i]['K'];
						if($cmp_email!="")
						$cmp_details['email_2']		  = $cmp_email;
						$cmp_details['www']			  = $impt_data[$i]['M'];
						$cmp_details['comments']	  = $impt_data[$i]['N'];
						$cmp_details['sales_contact_userid_fk'] = $this->userdata['userid'];
						$cmp_details['created_by'] 	  = $this->userdata['userid'];
						$cmp_details['created_on']    = date('Y-m-d H:i:s');
						$cmp_details['modified_by']   = $this->userdata['userid'];
						
						if(!empty($company_exists)) {
							//update the customer company details
							$this->db->where_in('companyid', $company_exists['companyid']);
							$this->db->update($this->cfg['dbpref'].'customers_company', $cmp_details);
							$cust_details['company_id'] = $company_exists['companyid'];
							$page['update_customers'] += 1;
						} else {
							//insert customer company details
							$this->db->insert($this->cfg['dbpref'] . 'customers_company', $cmp_details);
							$cust_details['company_id'] = $this->db->insert_id();
							$page['insert_customers'] += 1;
						}
						
						//insert or update the customer details
						$valid_custemail = 0;
						$cust_email = '';
						$valid_custemail = preg_match($regex, $impt_data[$i]['Q']);
						if($valid_custemail){
							$cust_email = $impt_data[$i]['Q'];
						} else {
							$page['invalid_custemail'][] = $i." row - ".$impt_data[$i]['Q'];
						}
						
						$cust_details['customer_name']  = $impt_data[$i]['O'];
						$cust_details['position_title'] = $impt_data[$i]['P'];
						$cust_details['phone_1']	= $impt_data[$i]['R'];
						if($cust_email!="")
						$cust_details['email_1']    = $cust_email;
						$cust_details['skype_name'] = $impt_data[$i]['S'];
						$cust_details['sales_contact_userid_fk'] = $this->userdata['userid'];
						$cust_details['created_by']    = $this->userdata['userid'];
						$cust_details['created_on']    = date('Y-m-d H:i:s');
						$cust_details['modified_by']   = $this->userdata['userid'];
						
						if($cust_details['company_id']!=''){
							$customer_exists = $this->customer_model->check_customer_details($cust_details['company_id'], $cust_email);
							if(!empty($customer_exists)) {
								//update the customer details
								$this->db->where_in('custid', $customer_exists['custid']);
								$this->db->update($this->cfg['dbpref'].'customers', $cust_details);
								$page['update_contacts'] += 1;
							} else {
								//insert customer details
								$this->db->insert($this->cfg['dbpref'] . 'customers', $cust_details);
								$page['insert_contacts'] += 1;
							}
						}
					}
				}
				$data['page'] = $page;
				$this->load->view('customer_import_view', $data);
			} else {
		 		$page['error'] = '<p class="error">Please Upload CSV, XLS File only!</p>';
				$data['page'] = $page;
		    	$this->load->view('customer_import_view', $data);		
		 	}
		} else {
			$page['error'] = '<p class="error">Please Upload Valid file!</p>';
			$data['page'] = $page;
			$this->load->view('customer_import_view', $data);
		}
	}
	
	function delete_contact()
	{
		$data =	real_escape_array($this->input->post()); // escape special characters
		$res = array();
		$res['html'] = "NO";
		$this->db->where('custid_fk', $data['id']);
		$query = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();

		if($query == 0) {
			
			$contact_log_data = $this->db->get_where($this->login_model->cfg['dbpref'] . 'customers', array('custid' => $data['id']))->row_array();
			
			$this->db->where('custid', $data['id']);
			$del = $this->db->delete($this->cfg['dbpref'] . 'customers');
			if($del){
				$res['html'] = "YES";
				$company_log_data = $this->db->get_where($this->login_model->cfg['dbpref'] . 'customers_company', array('companyid' => $contact_log_data['company_id']))->row_array();
				$ins_log 					= array();
				$ins_log['jobid_fk']    	= 0;
				$ins_log['userid_fk']   	= $this->userdata['userid'];
				$ins_log['date_created'] 	= date('Y-m-d H:i:s');
				$ins_log['log_content'] 	= $contact_log_data['customer_name']." Contact Deleted for the company ".$company_log_data['company']." On :" . " " . date('M j, Y g:i A');
				$log_res = $this->customer_model->insert_row("logs", $ins_log);
			}
		}
		echo json_encode($res);
		exit;
	}
	
	function ajax_company_search() 
	{
        if ($this->input->post('cust_name')) {
            $result = $this->customer_model->get_companies($this->input->post('cust_name'));
			$res = array();
			if (count($result) > 0) {
				$i=0;
				foreach ($result as $rec) {
					$res[$i]['label']     = $rec['company'];
					$res[$i]['companyid'] = $rec['companyid'];
					$res[$i]['add1_line1'] = $rec['add1_line1'];
					$res[$i]['add1_line2'] = $rec['add1_line2'];
					$res[$i]['add1_suburb'] = $rec['add1_suburb'];
					$res[$i]['phone'] = $rec['phone'];
					$res[$i]['fax'] = $rec['fax'];
					$res[$i]['www'] = $rec['www'];
					$res[$i]['email_2'] = $rec['email_2'];
					$res[$i]['regId']     = $rec['add1_region'];
					$res[$i]['cntryId']   = $rec['add1_country'];
					$res[$i]['stId']	  = $rec['add1_state'];
					$res[$i]['locId']     = $rec['add1_location'];
					$i++;
				}
			}
        }
        echo json_encode($res); exit;
    }
	
	function add_custom_customer()
	{
		$post_data = $this->input->post();

		$cust_data['company_id'] = $post_data['company_id'];
		
		if($post_data['company_id']==''){
			//insert company
			$cmp_data['company'] 		= $post_data['company'];
			$cmp_data['add1_line1'] 	= $post_data['add1_line1'];
			$cmp_data['add1_line2'] 	= $post_data['add1_line2'];
			$cmp_data['add1_suburb'] 	= $post_data['add1_suburb'];
			$cmp_data['add1_postcode'] 	= $post_data['add1_postcode'];
			$cmp_data['add1_region'] 	= $post_data['add1_region'];
			$cmp_data['add1_country'] 	= $post_data['add1_country'];
			$cmp_data['add1_state'] 	= $post_data['add1_state'];
			$cmp_data['add1_location']  = $post_data['add1_location'];
			$cmp_data['phone'] 			= $post_data['phone'];
			$cmp_data['fax'] 			= $post_data['fax'];
			$cmp_data['email_2'] 		= $post_data['email_2'];
			$cmp_data['www'] 			= $post_data['www'];
			$cmp_data['sales_contact_userid_fk'] = $this->userdata['userid'];
			$cmp_data['created_by'] 	= $this->userdata['userid'];
			$cmp_data['created_on'] 	= date('Y-m-d H:i:s');
			$cmp_data['modified_by'] 	= $this->userdata['userid'];
			
			$this->db->insert($this->cfg['dbpref'] . 'customers_company', $cmp_data);
			$cust_data['company_id'] = $this->db->insert_id();			
		} 
			
			$cust_data['customer_name']	= $post_data['customer_name'];
			$cust_data['skype_name']	= $post_data['skype_name'];
			$cust_data['position_title']= $post_data['position_title'];
			$cust_data['phone_1']		= $post_data['phone_1'];
			$cust_data['email_1']		= $post_data['email_1'];
			$cust_data['sales_contact_userid_fk'] = $post_data['sales_contact_userid_fk'];
			$cust_data['created_by'] 			  = $this->userdata['userid'];
			$cust_data['modified_by'] 			  = $this->userdata['userid'];
			
			$this->db->insert($this->cfg['dbpref'] . 'customers', $cust_data);
			$contact_id = $this->db->insert_id();
		
		$json['error'] = false;
		$json['custid']       = $contact_id;
		$json['cust_name1']   = $post_data['company'].' - '.$post_data['customer_name'];
		$json['cust_name']    = $post_data['company']." - ".$post_data['customer_name'];
		$json['cust_email']   = $post_data['email_1'];
		$json['cust_company'] = $post_data['company'];
		$json['cust_reg'] 	  = $post_data['add1_region'];
		$json['cust_cntry']   = $post_data['add1_country'];
		$json['cust_ste'] 	  = $post_data['add1_state'];
		$json['cust_locn']	  = $post_data['add1_location'];
		
		echo json_encode($json);
		
	}
	
}
?>