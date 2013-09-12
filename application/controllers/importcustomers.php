<?php
class ImportCustomers extends CI_Controller {
    
	public $userdata;
	private $import_dryrun = FALSE;
	
    function __construct()
	{
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('customer_model');
        $this->load->model('regionsettings_model');
        $this->load->library('validation');
    }
    
     function index() {	
	  $this->load->view('customer_import_view', $page); 
     }
     
    function import_success(){
	$data['msg']="Successfully Imported";
	$this->load->view('success_import_view',$data);
    }

    
  function importcust()
	{
		$this->login_model->check_login();
		
        $page['error'] = $page['msg'] = '';
        
       // if (isset($_FILES['card_file']) && is_uploaded_file($_FILES['card_file']['tmp_name']))
     if  ($_FILES && $_FILES['card_file']['name'] !== "") 
		{
			
            echo $filename = mt_rand(111, 999) . microtime() . '.csv';
            move_uploaded_file($_FILES['card_file']['tmp_name'], 'vps_temp_data/' . $filename);
            
            $fp = fopen('vps_temp_data/' . $filename, 'r');
            
            $data = fgetcsv($fp);
            
            if ($data && count($data) == 125)
			{
                $customers = array();
                $i = 0;
                while ($data = fgetcsv($fp))
				{
                    if ($data[0] != 'Co./Last Name')
			{
                        $customers[$i]['First Name'] = $data[0];
                        $customers[$i]['Last Name'] = $data[1];
                        $customers[$i]['Company'] = $data[2];
                       /* $customers[$i]['add1_line1'] = $data[4];
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
                        $customers[$i]['www_2'] = $data[33];*/
                    }
                    $i++;    
                }
                
                if ( $result = $this->customer_model->import_list($customers) )
				{
                    $page['msg'] = '<p class="msg">Card File Import Successful!<br />' . $result . ' New cards added to the list.</p>';
                }
				else
				{
					$page['error'] = '<p class="error">Card Import Failed!</p>';
                }
                
            }
	  
            
            fclose($fp);
            
        } 
		else if (isset($_FILES['card_file']))
		{
            $page['error'] = '<p class="error">No File Uploaded!</p>';
        }
	/*else
	   {
                $page['error'] = '<p class="error">Incorrect File Format</p>';
            }*/
        
        $this->load->view('customer_import_view', $page);
        
    }
	
	function import_customers_csv($mode = '', $dryrun = FALSE)
	{
		if ($dryrun == 'dry')
		{
			$this->import_dryrun = TRUE;
		}
		else
		{
			$this->import_dryrun = FALSE;
		}
		
		if ($mode != 'state' && $mode != 'list')
		{
			return;
		}
		
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
		{
			$html .= '<h2>DRY RUN ONLY</h2>';
		}
		
		foreach ($list as $file)
		{
			$html .= "<h4>{$file}</h4>";
			
			$total = $insert = $update = 0;
			
			$fp = fopen($file, 'r');
			while ($row = fgetcsv($fp))
			{
				$total ++; // increment
				
				if ($mode == 'state')
				{
					if (count($row) < 11)
					{
						continue;
					}
					
					if ( ! filter_var($row[10], FILTER_VALIDATE_EMAIL))
					{
						continue;
					}
					
					$name = explode(' ', $row[2]);
					
					$data = array();
					$data['first_name'] = $name[0];
					$data['last_name'] = '';
					
					if (isset($name[1]))
					{
						$data['last_name'] = $name[1];
					}
					
					$data['company'] = $row[0];
					
					$data['phone_1'] = $row[7];
					$data['phone_3'] = $row[9]; // mobile
					$data['phone_4'] = $row[8]; // fax
					$data['email_1'] = $row[10];
					$data['add1_line1'] = $row[3];
					$data['add1_suburb'] = $row[4];
					$data['add1_state'] = $row[5];
					
					$rs = $this->manage_customer($data);
					
					if ($rs == 'UPDATE')
					{
						$update++;
					}
					else if ($rs == 'INSERT')
					{
						$insert++;
					}
				}
				else if ($mode == 'list')
				{
					if (count($row) != 7)
					{
						continue;
					}
					
					if ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $row[5]))
					{
						continue;
					}
					
					$name = explode(' ', $row[0]);
					
					$data = array();
					$data['first_name'] = $name[0];
					$data['last_name'] = '';
					
					if (isset($name[1]))
					{
						$data['last_name'] = $name[1];
					}
					
					$data['position_title'] = $row[6];
					$data['company'] = $row[1];
					
					$data['phone_1'] = $row[3];
					$data['phone_3'] = $row[4]; // mobile
					$data['email_1'] = $row[5];
					$data['company'] = $row[1];
					
					$rs = $this->manage_customer($data);
					
					if ($rs == 'UPDATE')
					{
						$update++;
					}
					else if ($rs == 'INSERT')
					{
						$insert++;
					}
				}
			}
			fclose($fp);
			
			$html .= "<p>Total: {$total} | Inserts: {$insert} | Updates: {$update}</p>";
		}
		
		echo $html;
	}    
    
    
    /*
    function index($limit = 0, $search = false)
	{
		$default = array('last_name', 'asc');
		if (!$this->session->userdata('customer_sort'))
		{
			$this->session->set_userdata('customer_sort', $default);
		}
		
		$current = $this->session->userdata('customer_sort');
		$data['current_sort'] = $current;
		
        $data['customers'] = $this->customer_model->customer_list($limit, rawurldecode($search), $current[0], $current[1]);
        
        $data['pagination'] = '';
        if ($search == false) {
            $this->load->library('pagination');
            
            $config['base_url'] = $this->config->item('base_url') . 'customers/index/';
            $config['total_rows'] = (string) $this->customer_model->customer_count();
            $config['per_page'] = '20';	
            
            $this->pagination->initialize($config);
            
            $data['pagination'] = $this->pagination->create_links();
        }
        $this->load->view('customer_view', $data);
    }
	
	function set_search_order($type = 'last_name', $uri = 'customers')
	{
		$current = $this->session->userdata('customer_sort');
		$order = ($current[1] == 'asc') ? 'desc' : 'asc';
		$new = array($type, $order);
		$this->session->set_userdata('customer_sort', $new);
		redirect(base64_decode($uri));
	}
    
    function add_customer($update = false, $id = false, $ajax = false)
	{
		$data['regions'] = $this->regionsettings_model->region_list($limit, $search);
		
		//echo $this->db->last_query();
		//print_r($data);
		//if (isset($_POST) && count($_POST)) $this->session->set_userdata('post_array', $_POST);
		
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($_POST['delete_customer'])) {
            
            // check to see if this customer has a job on the system before deleting
			$jobs = $this->db->get_where($this->login_model->cfg['dbpref'] . 'jobs', array('custid_fk' => $id));
			$leads = $this->db->get_where($this->login_model->cfg['dbpref'] . '_leads', array('custid_fk' => $id));
			
			if ($jobs->num_rows() > 0)
			{
				$this->session->set_flashdata('login_errors', array('Cannot delete customer with exiting invoice records!'));
				redirect('customers/add_customer/update/' . $id);
				exit();
			}
			
			if ($leads->num_rows() > 0)
			{
				$this->session->set_flashdata('login_errors', array('Cannot delete customer with exiting lead records!'));
				redirect('customers/add_customer/update/' . $id);
				exit();
			}
            
            $this->customer_model->delete_customer($id);
            $this->session->set_flashdata('confirm', array('Customer Record Deleted!'));
            redirect('customers/');
            
        }
        
        $rules['first_name'] = "trim|required";
		$rules['last_name'] = "trim|required";
		$rules['company'] = "trim|required";
		
		$rules['add1_region']    = "selected[add1_region]";
		$rules['add1_country'] = "selected[add1_country]";
		$rules['add1_state'] = "selected[add1_state]";
		$rules['add1_location'] = "selected[add1_location]";
			
		$rules['phone_1'] = "trim|required";
		$rules['add1_postcode'] = "trim";
		$rules['email_1'] = "trim|required|valid_email";
		
		$this->validation->set_rules($rules);
		
		$fields['first_name'] = "First Name";
		$fields['last_name'] = "Last Name";
		$fields['position_title'] = 'Position';
		$fields['company'] = "Company";
		$fields['add1_line1'] = "";
		$fields['add1_line2'] = "";
		$fields['add1_suburb'] = "";		
		$fields['add1_postcode'] = "Postcode";
		$fields['add1_region'] = "Region";
		$fields['add1_country'] = "Country";
		$fields['add1_state'] = "State";
		$fields['add1_location'] = "Location";
		$fields['phone_1'] = "Phone Number";
		$fields['phone_2'] = '';
		$fields['phone_3'] = '';
		$fields['phone_4'] = '';
		$fields['email_1'] = "Primary Email Address";
		$fields['email_2'] = "";
		$fields['email_3'] = "";
		$fields['email_4'] = "";
		$fields['skype_name'] = '';
		$fields['www_1'] = "Primary Web Address";
		$fields['www_2'] = "";
        $fields['comments'] = '';
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		$data['categories'] = $this->customer_model->category_list();
		$data['sales_agents'] = $this->customer_model->sales_agent_list();
        
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_customer'])) {
            $customer = $this->customer_model->get_customer($id);
			$data['category_data'] = $this->customer_model->customer_categories($id);
			$data['sales_agent_data'] = $this->customer_model->customer_sales_agent($id);
			
			if ($this->userdata['level'] == 4 && !in_array($this->userdata['userid'], $data['sales_agent_data']))
			{
				$this->session->set_flashdata('access_error', 'You are not listed with this particular customer!');
				redirect('notallowed');
				exit;
			}
			
			//echo '<!--' . print_r($customer, true) . '-->';
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() == false) {
			
            if ($ajax == false) {
                $this->load->view('customer_add_view', $data);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }
			
		} else {
			
			// all good
            foreach($fields as $key => $val) {
				if (isset($_POST[$key]))
				{
					$update_data[$key] = $_POST[$key];
				}
            }
          //  echo "<pre>"; print_r($update_data);
			$categories = $this->input->post('customer_category');
			$sales_agents = $this->input->post('customer_sales_agent');
			
			
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
                
				// set exported back to NULL so it will be exported to addressbook
				$update_data['exported'] = NULL;
				
                //update
                if ($this->customer_model->update_customer($id, $update_data, $categories, $sales_agents)) {
				
				$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
						<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title>Email Template</title>
						<style type="text/css">
						body {
							margin-left: 0px;
							margin-top: 0px;
							margin-right: 0px;
							margin-bottom: 0px;
						}
						</style>
						</head>

						<body>
						<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
						<tr><td bgcolor="#FFFFFF">
						<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
						  <tr>
							<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" /></td>
						  </tr>
						  <tr>
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Customer Details Modification Notification 
</h3></td>
						  </tr>

						  <tr>
							<td>
							<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
							<p style="background: none repeat scroll 0 0 #4B6FB9;
							border-bottom: 1px solid #CCCCCC;
							color: #FFFFFF;
							margin: 0;
							padding: 4px;">
								<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
							<p style="padding: 4px;">Customer Details Modified -> '.$update_data['first_name']. '  '.$update_data['last_name']. ' - '.$update_data['company']. '<br /><br />
								'.$this->userdata['signature'].'<br />
							</p>
						</div>
						</td>
						  </tr>

						   <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
						  </tr>
						</table>
						</td>
						</tr>
						</table>
						</body>
						</html>';	
						
		$from=$this->userdata['email'];
		$arrEmails = $this->config->item('crm');
		$arrSetEmails=$arrEmails['director_emails'];
		$mangement_email = $arrEmails['management_emails'];
		$mgmt_mail = implode(',',$mangement_email);		
		$admin_mail=implode(',',$arrSetEmails);
		$subject='Customer Details Modification Notification';
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		$this->email->from($from,$user_name);
		$this->email->to($admin_mail.','.$mgmt_mail);
		$this->email->subject($subject);
		$this->email->message($log_email_content);

		$this->email->send(); 
				
				
				
				
                    
                    $this->session->set_flashdata('confirm', array('Customer Details Updated!'));
                    redirect('customers/add_customer/update/' . $id);
                    
                }
                
                
            } else {
                
				# add the sales agent
				if ($this->userdata['level'] == 4)
				{
					$sales_agents = array($this->userdata['userid']);
				}
				
                //insert
                if ($newid = $this->customer_model->insert_customer($update_data, $categories, $sales_agents)) {
				
				    $user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
						<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title>Email Template</title>
						<style type="text/css">
						body {
							margin-left: 0px;
							margin-top: 0px;
							margin-right: 0px;
							margin-bottom: 0px;
						}
						</style>
						</head>

						<body>
						<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
						<tr><td bgcolor="#FFFFFF">
						<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
						  <tr>
							<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" /></td>
						  </tr>
						  <tr>
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Customer Creation Notification 
</h3></td>
						  </tr>

						  <tr>
							<td>
							<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
							<p style="background: none repeat scroll 0 0 #4B6FB9;
							border-bottom: 1px solid #CCCCCC;
							color: #FFFFFF;
							margin: 0;
							padding: 4px;">
								<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
							<p style="padding: 4px;">New Customer Created -'.$update_data['first_name']. '  '.$update_data['last_name']. ' - '.$update_data['company']. '<br /><br />
								'.$this->userdata['signature'].'<br />
							</p>
						</div>
						</td>
						  </tr>

						   <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
						  </tr>
						</table>
						</td>
						</tr>
						</table>
						</body>
						</html>';	
						
		$from=$this->userdata['email'];
		$arrEmails = $this->config->item('crm');
		$arrSetEmails=$arrEmails['director_emails'];
		$mangement_email = $arrEmails['management_emails'];
		$mgmt_mail = implode(',',$mangement_email);
		$admin_mail=implode(',',$arrSetEmails);		
		$varEmailRecipients=implode(',',$arrSetEmails);
		$subject='New Customer Creation Notification';
		$this->load->library('email');
		$this->email->set_newline("\r\n");
		$this->email->from($from,$user_name);
		$this->email->to($mgmt_mail);
		$this->email->bcc($admin_mail);
		$this->email->subject($subject);
		$this->email->message($log_email_content);

		$this->email->send(); 
				
                    
                    if ($ajax == false) {
                        $this->session->set_flashdata('confirm', array('New Customer Added!'));
                        redirect('customers/add_customer/update/' . $newid);
                    } else {
                        $json['error'] = false;
                        $json['custid'] = $newid;
                        $json['cust_name'] = $this->input->post('first_name') . ' ' . $this->input->post('last_name');
                        $json['cust_email'] = $this->input->post('email_1');
                        echo json_encode($json);
                    }
                    
                }
                
            }
			
		}
    }
	function delete_customer($id = false) {
	if ($this->session->userdata('delete')==1){
		$this->customer_model->delete_customer($id);
		$this->session->set_flashdata('confirm', array('Customer Record Deleted!'));
		redirect('customers');
	} else {
		$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
		redirect('customers');
		}
	}
	
    
    function search()
	{
        if (isset($_POST['cancel_submit']))
		{
            redirect('customers/');
        }
		else if ($name = $this->input->post('cust_search'))
		{
            redirect('customers/index/0/' . rawurlencode($name));
        }
		else
		{
            redirect('customers/');
        }
        
    }
	
	function category()
	{
		$this->login_model->check_login(array(0,1));
		
		$data['categories'] = $this->customer_model->category_list();
		$this->load->view('customer_category_view', $data);
	}
    
	function add_category($update = false, $id = false)
	{
		$this->login_model->check_login(array(0,1));
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($_POST['delete_category'])) {
			
            $this->customer_model->delete_category($id);
            $this->session->set_flashdata('confirm', array('Customer Record Deleted!'));
            redirect('customers/');
            
        }
        
        $rules['category_name'] = "trim|required";
		
		$this->validation->set_rules($rules);
		
		$fields['category_name'] = "Category Name";
		$fields['cat_comments'] = "Description";
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_customer'])) {
            $customer = $this->customer_model->get_category($id);
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() == false) {
			
            $this->load->view('customer_category_add_view');
			
		} else {
			
			// all good
            foreach($fields as $key => $val)
			{
                if ($this->input->post($key))
				{
					$update_data[$key] = $this->input->post($key);
				}
            }
			
            
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
				
                //update
                if ($this->customer_model->update_category($id, $update_data)) {
                    
                    $this->session->set_flashdata('confirm', array('Category Details Updated!'));
                    redirect('customers/add_category/update/' . $id);
                    
                }
                
                
            } else {
                
				// check
				if ($this->customer_model->get_category($this->input->post('category_name'), 'category_name'))
				{
					$this->session->set_flashdata('login_errors', array('This category already exists, please select another name!'));
					redirect('customers/add_category/');
					exit();
				}
				
                //insert
                if ($newid = $this->customer_model->insert_category($update_data)) {
                    
                    $this->session->set_flashdata('confirm', array('New Category Added!'));
                    redirect('customers/add_category/update/' . $newid);
                    
                }
                
            }
			
		}
	}
	
	function delete_category($id = false) {
	if ($this->session->userdata('delete')==1){
		$this->customer_model->delete_category($id);
		$this->session->set_flashdata('confirm', array('Category Record Deleted!'));
		redirect('customers/category');
	}else {
		$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
		redirect('customers/category');
		}	
	}	*/
	
  
	
	
    
}
?>
