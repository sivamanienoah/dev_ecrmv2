<?php
class ImportCustomers extends crm_controller {
    
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
        
		if ($_FILES && $_FILES['card_file']['name'] !== "") 
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
}
?>
