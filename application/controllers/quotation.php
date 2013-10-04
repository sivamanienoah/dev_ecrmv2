<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Quotation extends crm_controller {
    
    var $cfg;
	var $userdata;
    
	function __construct()
	{
        parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
	}
	
    function index()
	{
	}
	
	function invoice_data_zip($jobid)
	{
		$this->db->where('jobid', $jobid);
		$job_details = $this->db->get($this->cfg['dbpref'] . 'jobs');
		
		if ($job_details->num_rows() > 0)
		{
			$job_details = $job_details->result_array();
			$this->load->library('zip');
			
			$inv_data = $this->invoice_to_csv($jobid);
			$cust_data = $this->customer_to_csv($job_details[0]['custid_fk']);
			
			$this->zip->add_data($job_details[0]['invoice_no'] . '_INVOICE_DATA.csv', $inv_data);
			$this->zip->add_data($job_details[0]['custid_fk'] . '_CUSTOMER_DATA.csv', $cust_data);
			
			$this->db->where('jobid', $jobid);
			$this->db->update($this->cfg['dbpref'] . 'jobs', array('invoice_downloaded' => '1'));
			
			$this->zip->download($job_details[0]['invoice_no'] . '_myob_data.zip'); 
		}
		else
		{
			die('Invalid job id supplied');
		}
	}
	
	function invoice_to_csv($jobid)
	{
		$csv_titles[0] = "Co./Last Name";
		$csv_titles[1] = "First Name";
		$csv_titles[2] = "Invoice #";
		$csv_titles[3] = "Date";
		$csv_titles[4] = "Customer PO";
		$csv_titles[5] = "Inclusive";
		$csv_titles[6] = "Delivery Status";
		$csv_titles[7] = "Detail Date";
		$csv_titles[8] = "Description";
		$csv_titles[9] = "Account #";
		$csv_titles[10] = "Amount";
		$csv_titles[11] = "Inc-Tax Amount";
		$csv_titles[12] = "Job";
		$csv_titles[13] = "Comment";
		$csv_titles[14] = "Journal Memo";
		$csv_titles[15] = "Promised Date";
		$csv_titles[16] = "Referral Source";
		$csv_titles[17] = "Tax Code";
		$csv_titles[18] = "Non-GST Amount";
		$csv_titles[19] = "GST Amount";
		$csv_titles[20] = "LCT Amount";
		$csv_titles[21] = "Sale Status";
		$csv_titles[22] = "Terms - Payment is Due";
		$csv_titles[23] = "- Discount Days";
		$csv_titles[24] = "- Balance Due Days";
		$csv_titles[25] = "- % Discount";
		$csv_titles[26] = "- % Monthly Charge";
		$csv_titles[27] = "Salesperson Last Name";
		$csv_titles[28] = "Salesperson First Name";
		$csv_titles[29] = "Amount Paid";
		$csv_titles[30] = "Payment Method";
		$csv_titles[31] = "Payment Notes";
		$csv_titles[32] = "Name on Card";
		$csv_titles[33] = "Card Number";
		$csv_titles[34] = "Expiry Date";
		$csv_titles[35] = "Authorisation Code";
		$csv_titles[36] = "BSB";
		$csv_titles[37] = "Account Number";
		$csv_titles[38] = "Drawer/Account Name";
		$csv_titles[39] = "Cheque Number";
		$csv_titles[40] = "Category";
		$csv_titles[41] = "Card ID";
		
		$this->db->where('jobid', $jobid);
		$job_details = $this->db->get($this->cfg['dbpref'] . 'jobs');
		
		if ($job_details->num_rows() > 0)
		{
			$job = $job_details->result_array();
			
			# fix the invoice date
			if ($job[0]['date_invoiced'] == '')
			{
				$job[0]['date_invoiced'] = $job[0]['date_created'];
			}
			
			
			$this->db->where('custid', $job[0]['custid_fk']);
			$client_details = $this->db->get($this->cfg['dbpref'] . 'customers');
			$client = $client_details->result_array();
			
			$this->db->order_by('item_position', 'asc');
			$this->db->where('jobid_fk', $jobid);
			$user = $this->db->get($this->cfg['dbpref'] . 'items');
			$item_data = $user->result_array();
			
			$j = 0;
			foreach ($item_data as $item)
			{
				
				$output_item_price = ($item['item_price'] < 0) ? '-$' . (-1 * $item['item_price']) : '$' . $item['item_price'];
				$gst_inc_price = round($item['item_price'] * 1.1, 2);
				$output_gst_inc_price = ($gst_inc_price < 0) ? '-$' . (-1 * $gst_inc_price) : '$' . $gst_inc_price;
				$gst_amount = round($item['item_price'] / 10, 2);
				$output_gst_amount = ($gst_amount < 0) ? '-$' . (-1 * $gst_amount) : '$' . $gst_amount;
				
				// replace line breaks within the descriptions
				$lb_search = array("\r\n", "\r");
				$lb_replace = "\n";
				$item['item_desc'] = str_replace($lb_search, $lb_replace, $item['item_desc']);
				
				$item['item_desc'] = join("\n", str_split($item['item_desc'], 253));
				
				$desc_parts = explode("\n", $item['item_desc']);
				
				for ($i = 0; $i < (count($desc_parts) - 1); $i++)
				{
					
					$cdata[$j][0] = $client[0]['company'];
					$cdata[$j][1] = ''; //$client[0]['first_name'] . ' ' . $client[0]['last_name']; 
					$cdata[$j][2] = $job[0]['invoice_no'];
					$cdata[$j][3] = date('d/m/Y', strtotime($job[0]['date_invoiced'])); // [Date] => 22/07/2008
					$cdata[$j][4] = ''; //[Customer PO] => 
					$cdata[$j][5] = ''; //[Inclusive] => 
					$cdata[$j][6] = 'P'; //[Delivery Status] => P
					$cdata[$j][7] = ''; //[Detail Date] => 
					$cdata[$j][8] = '"' . str_replace('"', '\"', $desc_parts[$i]) . '"'; //[Description] => Total Time = 2hours@155/hr
					$cdata[$j][9] = $item['ledger_code']; //[Account #] => 41000
					$cdata[$j][10] = "$0.00"; // [Amount] => $310.00
					$cdata[$j][11] = "$0.00"; //[Inc-Tax Amount] => $341.00
					$cdata[$j][12] = $job[0]['belong_to']; //[Job] => 
					$cdata[$j][13] = $this->cfg['job_categories'][$job[0]['job_category']]; //[Comment] => Graphic Design
					$cdata[$j][14] = 'Sale;' . $client[0]['company']; //[Journal Memo] => Sale; Ben Ramsay Real Estate
					$cdata[$j][15] = ''; //[Promised Date] =>
					$cdata[$j][16] = ""; // Referral Source =>
					$cdata[$j][17] = "GST"; // [Tax Code] => GST
					$cdata[$j][18] = "$0.00"; // [Non-GST Amount] => $0.00
					$cdata[$j][19] = "$0.00"; // [GST Amount] => $31.00
					$cdata[$j][20] = "$0.00"; // [LCT Amount] => $0.00
					$cdata[$j][21] = "I"; // [Sale Status] => I
					$cdata[$j][22] = "2"; //T [Terms - Payment is Due] => 2
					$cdata[$j][23] = "0"; // [- Discount Days] => 0
					$cdata[$j][24] = "7"; // [- Balance Due Days] => 7
					$cdata[$j][25] = "0"; // [- % Discount] => 0
					$cdata[$j][26] = "0"; // [- % Monthly Charge] => 0
					$cdata[$j][27] = "Nissirios"; // Salesperson Last Name"
					$cdata[$j][28] = "George"; // Salesperson First Name"
					$cdata[$j][29] = "$0.00"; // Amount Paid"
					$cdata[$j][30] = ""; // Payment Method"
					$cdata[$j][31] = ""; //Payment Notes"
					$cdata[$j][32] = ""; //Name on Card"
					$cdata[$j][33] = ""; //Card Number"
					$cdata[$j][34] = ""; //Expiry Date"
					$cdata[$j][35] = ""; //Authorisation Code"
					$cdata[$j][36] = ""; //BSB"
					$cdata[$j][37] = ""; //Account Number"
					$cdata[$j][38] = ""; //Drawer/Account Name"
					$cdata[$j][39] = ""; //Cheque Number"
					$cdata[$j][40] = ""; //Category"
					$cdata[$j][41] = "*None"; // [Card ID] => *None
					
					$j ++;
					
				}
				
				$cdata[$j][0] = $client[0]['company'];
				$cdata[$j][1] = ''; //$client[0]['first_name'] . ' ' . $client[0]['last_name']; 
				$cdata[$j][2] = $job[0]['invoice_no'];
				$cdata[$j][3] = date('d/m/Y', strtotime($job[0]['date_invoiced'])); // [Date] => 22/07/2008
				$cdata[$j][4] = ''; //[Customer PO] => 
				$cdata[$j][5] = ''; //[Inclusive] => 
				$cdata[$j][6] = 'P'; //[Delivery Status] => P
				$cdata[$j][7] = ''; //[Detail Date] => 
				$cdata[$j][8] = $desc_parts[count($desc_parts) - 1]; //[Description] => Total Time = 2hours@155/hr
				$cdata[$j][9] = $item['ledger_code']; //[Account #] => 41000
				$cdata[$j][10] = $output_item_price; // [Amount] => $310.00
				$cdata[$j][11] = $output_gst_inc_price; //[Inc-Tax Amount] => $341.00
				$cdata[$j][12] = $job[0]['belong_to']; //[Job] => 
				$cdata[$j][13] = $this->cfg['job_categories'][$job[0]['job_category']]; //[Comment] => Graphic Design
				$cdata[$j][14] = 'Sale;' . $client[0]['company']; //[Journal Memo] => Sale; Ben Ramsay Real Estate
				$cdata[$j][15] = ''; //[Promised Date] =>
				$cdata[$j][16] = ""; // Referral Source =>
				$cdata[$j][17] = "GST"; // [Tax Code] => GST
				$cdata[$j][18] = "$0.00"; // [Non-GST Amount] => $0.00
				$cdata[$j][19] = $output_gst_amount; // [GST Amount] => $31.00
				$cdata[$j][20] = "$0.00"; // [LCT Amount] => $0.00
				$cdata[$j][21] = "I"; // [Sale Status] => I
				$cdata[$j][22] = "2"; //T [Terms - Payment is Due] => 2
				$cdata[$j][23] = "0"; // [- Discount Days] => 0
				$cdata[$j][24] = "7"; // [- Balance Due Days] => 7
				$cdata[$j][25] = "0"; // [- % Discount] => 0
				$cdata[$j][26] = "0"; // [- % Monthly Charge] => 0
				$cdata[$j][27] = "Nissirios"; // Salesperson Last Name"
				$cdata[$j][28] = "George"; // Salesperson First Name"
				$cdata[$j][29] = "$0.00"; // Amount Paid"
				$cdata[$j][30] = ""; // Payment Method"
				$cdata[$j][31] = ""; //Payment Notes"
				$cdata[$j][32] = ""; //Name on Card"
				$cdata[$j][33] = ""; //Card Number"
				$cdata[$j][34] = ""; //Expiry Date"
				$cdata[$j][35] = ""; //Authorisation Code"
				$cdata[$j][36] = ""; //BSB"
				$cdata[$j][37] = ""; //Account Number"
				$cdata[$j][38] = ""; //Drawer/Account Name"
				$cdata[$j][39] = ""; //Cheque Number"
				$cdata[$j][40] = ""; //Category"
				$cdata[$j][41] = "*None"; // [Card ID] => *None
				
				$j ++;
				
				$cdata[$j][0] = $client[0]['company'];
				$cdata[$j][1] = ''; //$client[0]['first_name'] . ' ' . $client[0]['last_name'];
				$cdata[$j][2] = $job[0]['invoice_no'];
				$cdata[$j][3] = date('d/m/Y', strtotime($job[0]['date_invoiced'])); // [Date] => 22/07/2008
				$cdata[$j][4] = ''; //[Customer PO] => 
				$cdata[$j][5] = ''; //[Inclusive] => 
				$cdata[$j][6] = 'P'; //[Delivery Status] => P
				$cdata[$j][7] = ''; //[Detail Date] => 
				$cdata[$j][8] = ""; //[Description] => Total Time = 2hours@155/hr
				$cdata[$j][9] = $item['ledger_code']; //[Account #] => 41000
				$cdata[$j][10] = "$0.00"; // [Amount] => $310.00
				$cdata[$j][11] = "$0.00"; //[Inc-Tax Amount] => $341.00
				$cdata[$j][12] = $job[0]['belong_to']; //[Job] => 
				$cdata[$j][13] = $this->cfg['job_categories'][$job[0]['job_category']]; //[Comment] => Graphic Design
				$cdata[$j][14] = 'Sale;' . $client[0]['company']; //[Journal Memo] => Sale; Ben Ramsay Real Estate
				$cdata[$j][15] = ''; //[Promised Date] =>
				$cdata[$j][16] = ""; // Referral Source =>
				$cdata[$j][17] = "GST"; // [Tax Code] => GST
				$cdata[$j][18] = "$0.00"; // [Non-GST Amount] => $0.00
				$cdata[$j][19] = "$0.00"; // [GST Amount] => $31.00
				$cdata[$j][20] = "$0.00"; // [LCT Amount] => $0.00
				$cdata[$j][21] = "I"; // [Sale Status] => I
				$cdata[$j][22] = "2"; //T [Terms - Payment is Due] => 2
				$cdata[$j][23] = "0"; // [- Discount Days] => 0
				$cdata[$j][24] = "7"; // [- Balance Due Days] => 7
				$cdata[$j][25] = "0"; // [- % Discount] => 0
				$cdata[$j][26] = "0"; // [- % Monthly Charge] => 0
				$cdata[$j][27] = "Nissirios"; // Salesperson Last Name"
				$cdata[$j][28] = "George"; // Salesperson First Name"
				$cdata[$j][29] = "$0.00"; // Amount Paid"
				$cdata[$j][30] = ""; // Payment Method"
				$cdata[$j][31] = ""; //Payment Notes"
				$cdata[$j][32] = ""; //Name on Card"
				$cdata[$j][33] = ""; //Card Number"
				$cdata[$j][34] = ""; //Expiry Date"
				$cdata[$j][35] = ""; //Authorisation Code"
				$cdata[$j][36] = ""; //BSB"
				$cdata[$j][37] = ""; //Account Number"
				$cdata[$j][38] = ""; //Drawer/Account Name"
				$cdata[$j][39] = ""; //Cheque Number"
				$cdata[$j][40] = ""; //Category"
				$cdata[$j][41] = "*None"; // [Card ID] => *None
				
				$j ++;
			}
			
			ini_set('auto_detect_line_endings', 1);
			
			$temp_file = tempnam($this->config->item('base_url').'vps_temp_data', 'invcsv');
			$fp = fopen($temp_file, 'w');
			fputcsv($fp, $csv_titles, ',', '"');
			foreach ($cdata as $row)
			{
				$this->fputcsv2($fp, $row);
			}
			fclose($fp);
			//header('Content-type:text/plain');
			return file_get_contents($temp_file);
			
		}
		else
		{
			echo 'Invoice does not exist';
		}
	}
	
	function customer_to_csv($id)
	{
		$this->load->model('customer_model');
		$customer = $this->customer_model->get_customer($id);
		
		if (!$customer)
		{
			die('Customer Does not Exist');
			return FALSE;
		}
		else
		{
			$customer = $customer[0];
		}
		
		$cdata[0] = "Co./Last Name";
		$cdata[1] = "First Name";
		$cdata[2] = "Card ID";
		$cdata[3] = "Card Status";
		$cdata[4] = "Addr 1 - Line 1";
		$cdata[5] = "- Line 2";
		$cdata[6] = "- Line 3";
		$cdata[7] = "- Line 4";
		$cdata[8] = "- City";
		$cdata[9] = "- State";
		$cdata[10] = "- Postcode";
		$cdata[11] = "- Country";
		$cdata[12] = "- Phone # 1";
		$cdata[13] = "- Phone # 2";
		$cdata[14] = "- Phone # 3";
		$cdata[15] = "- Fax #";
		$cdata[16] = "- Email";
		$cdata[17] = "- WWW";
		$cdata[18] = "- Contact Name";
		$cdata[19] = "- Salutation";
		$cdata[20] = "Addr 2 - Line 1";
		$cdata[21] = "- Line 2";
		$cdata[22] = "- Line 3";
		$cdata[23] = "- Line 4";
		$cdata[24] = "- City";
		$cdata[25] = "- State";
		$cdata[26] = "- Postcode";
		$cdata[27] = "- Country";
		$cdata[28] = "- Phone # 1";
		$cdata[29] = "- Phone # 2";
		$cdata[30] = "- Phone # 3";
		$cdata[31] = "- Fax #";
		$cdata[32] = "- Email";
		$cdata[33] = "- WWW";
		$cdata[34] = "- Contact Name";
		$cdata[35] = "- Salutation";
		$cdata[36] = "Addr 3 - Line 1";
		$cdata[37] = "- Line 2";
		$cdata[38] = "- Line 3";
		$cdata[39] = "- Line 4";
		$cdata[40] = "- City";
		$cdata[41] = "- State";
		$cdata[42] = "- Postcode";
		$cdata[43] = "- Country";
		$cdata[44] = "- Phone # 1";
		$cdata[45] = "- Phone # 2";
		$cdata[46] = "- Phone # 3";
		$cdata[47] = "- Fax #";
		$cdata[48] = "- Email";
		$cdata[49] = "- WWW";
		$cdata[50] = "- Contact Name";
		$cdata[51] = "- Salutation";
		$cdata[52] = "Addr 4 - Line 1";
		$cdata[53] = "- Line 2";
		$cdata[54] = "- Line 3";
		$cdata[55] = "- Line 4";
		$cdata[56] = "- City";
		$cdata[57] = "- State";
		$cdata[58] = "- Postcode";
		$cdata[59] = "- Country";
		$cdata[60] = "- Phone # 1";
		$cdata[61] = "- Phone # 2";
		$cdata[62] = "- Phone # 3";
		$cdata[63] = "- Fax #";
		$cdata[64] = "- Email";
		$cdata[65] = "- WWW";
		$cdata[66] = "- Contact Name";
		$cdata[67] = "- Salutation";
		$cdata[68] = "Addr 5 - Line 1";
		$cdata[69] = "- Line 2";
		$cdata[70] = "- Line 3";
		$cdata[71] = "- Line 4";
		$cdata[72] = "- City";
		$cdata[73] = "- State";
		$cdata[74] = "- Postcode";
		$cdata[75] = "- Country";
		$cdata[76] = "- Phone # 1";
		$cdata[77] = "- Phone # 2";
		$cdata[78] = "- Phone # 3";
		$cdata[79] = "- Fax #";
		$cdata[80] = "- Email";
		$cdata[81] = "- WWW";
		$cdata[82] = "- Contact Name";
		$cdata[83] = "- Salutation";
		$cdata[84] = "Picture";
		$cdata[85] = "Notes";
		$cdata[86] = "Identifiers";
		$cdata[87] = "Custom List 1";
		$cdata[88] = "Custom List 2";
		$cdata[89] = "Custom List 3";
		$cdata[90] = "Custom Field 1";
		$cdata[91] = "Custom Field 2";
		$cdata[92] = "Custom Field 3";
		$cdata[93] = "Billing Rate";
		$cdata[94] = "Terms - Payment is Due";
		$cdata[95] = "- Discount Days";
		$cdata[96] = "- Balance Due Days";
		$cdata[97] = "- % Discount";
		$cdata[98] = "- % Monthly Charge";
		$cdata[99] = "Tax Code";
		$cdata[100] = "Credit Limit";
		$cdata[101] = "Tax ID No.";
		$cdata[102] = "Volume Discount %";
		$cdata[103] = "Sales/Purchase Layout";
		$cdata[104] = "Payment Method";
		$cdata[105] = "Payment Notes";
		$cdata[106] = "Name on Card";
		$cdata[107] = "Card Number";
		$cdata[108] = "Expiry Date";
		$cdata[109] = "BSB";
		$cdata[110] = "Account Number";
		$cdata[111] = "Account Name";
		$cdata[112] = "A.B.N. ";
		$cdata[113] = "A.B.N. Branch";
		$cdata[114] = "Account";
		$cdata[115] = "Salesperson";
		$cdata[116] = "Salesperson Card ID";
		$cdata[117] = "Comment";
		$cdata[118] = "Shipping Method";
		$cdata[119] = "Printed Form";
		$cdata[120] = "Freight Tax Code";
		$cdata[121] = "Use Customer's Tax Code";
		$cdata[122] = "Receipt Memo";
		$cdata[123] = "Invoice/Purchase Order Delivery";
		$cdata[124] = "Record ID";
		
		
		
		$db_cdata[0] = $customer['company'];
		$db_cdata[1] = "";
		$db_cdata[2] = "";
		$db_cdata[3] = "";
		$db_cdata[4] = $customer['add1_line1'];
		$db_cdata[5] = $customer['add1_line2'];
		$db_cdata[6] = "";
		$db_cdata[7] = "";
		$db_cdata[8] = $customer['add1_suburb'];
		$db_cdata[9] = $customer['add1_state'];
		$db_cdata[10] = $customer['add1_postcode'];
		$db_cdata[11] = $customer['add1_country'];
		$db_cdata[12] = $customer['phone_1'];
		$db_cdata[13] = $customer['phone_2'];
		$db_cdata[14] = $customer['phone_3'];
		$db_cdata[15] = $customer['phone_4'];
		$db_cdata[16] = $customer['email_1'];
		$db_cdata[17] = $customer['www_1'];
		$db_cdata[18] = $customer['first_name'] . $customer['last_name'];
		$db_cdata[19] = "";
		$db_cdata[20] = "";
		$db_cdata[21] = "";
		$db_cdata[22] = "";
		$db_cdata[23] = "";
		$db_cdata[24] = "";
		$db_cdata[25] = "";
		$db_cdata[26] = "";
		$db_cdata[27] = "";
		$db_cdata[28] = "";
		$db_cdata[29] = "";
		$db_cdata[30] = "";
		$db_cdata[31] = "";
		$db_cdata[32] = $customer['email_2'];
		$db_cdata[33] = $customer['www_2'];
		$db_cdata[34] = "";
		$db_cdata[35] = "";
		$db_cdata[36] = "";
		$db_cdata[37] = "";
		$db_cdata[38] = "";
		$db_cdata[39] = "";
		$db_cdata[40] = "";
		$db_cdata[41] = "";
		$db_cdata[42] = "";
		$db_cdata[43] = "";
		$db_cdata[44] = "";
		$db_cdata[45] = "";
		$db_cdata[46] = "";
		$db_cdata[47] = "";
		$db_cdata[48] = $customer['email_3'];
		$db_cdata[49] = "";
		$db_cdata[50] = "";
		$db_cdata[51] = "";
		$db_cdata[52] = "";
		$db_cdata[53] = "";
		$db_cdata[54] = "";
		$db_cdata[55] = "";
		$db_cdata[56] = "";
		$db_cdata[57] = "";
		$db_cdata[58] = "";
		$db_cdata[59] = "";
		$db_cdata[60] = "";
		$db_cdata[61] = "";
		$db_cdata[62] = "";
		$db_cdata[63] = "";
		$db_cdata[64] = $customer['email_4'];
		$db_cdata[65] = "";
		$db_cdata[66] = "";
		$db_cdata[67] = "";
		$db_cdata[68] = "";
		$db_cdata[69] = "";
		$db_cdata[70] = "";
		$db_cdata[71] = "";
		$db_cdata[72] = "";
		$db_cdata[73] = "";
		$db_cdata[74] = "";
		$db_cdata[75] = "";
		$db_cdata[76] = "";
		$db_cdata[77] = "";
		$db_cdata[78] = "";
		$db_cdata[79] = "";
		$db_cdata[80] = "";
		$db_cdata[81] = "";
		$db_cdata[82] = "";
		$db_cdata[83] = "";
		$db_cdata[84] = "";
		$db_cdata[85] = "";
		$db_cdata[86] = "";
		$db_cdata[87] = "";
		$db_cdata[88] = "";
		$db_cdata[89] = "";
		$db_cdata[90] = "";
		$db_cdata[91] = "";
		$db_cdata[92] = "";
		$db_cdata[93] = "";
		$db_cdata[94] = "";
		$db_cdata[95] = "";
		$db_cdata[96] = "";
		$db_cdata[97] = "";
		$db_cdata[98] = "";
		$db_cdata[99] = "";
		$db_cdata[100] = "";
		$db_cdata[101] = "";
		$db_cdata[102] = "";
		$db_cdata[103] = "";
		$db_cdata[104] = "";
		$db_cdata[105] = "";
		$db_cdata[106] = "";
		$db_cdata[107] = "";
		$db_cdata[108] = "";
		$db_cdata[109] = "";
		$db_cdata[110] = "";
		$db_cdata[111] = "";
		$db_cdata[112] = $customer['abn'];
		$db_cdata[113] = "";
		$db_cdata[114] = "";
		$db_cdata[115] = "";
		$db_cdata[116] = "";
		$db_cdata[117] = "";
		$db_cdata[118] = "";
		$db_cdata[119] = "";
		$db_cdata[120] = "";
		$db_cdata[121] = "";
		$db_cdata[122] = "";
		$db_cdata[123] = "";
		$db_cdata[124] = "";
		
		ini_set('auto_detect_line_endings', 1);
		
		$temp_file = tempnam($this->config->item('base_url').'vps_temp_data', 'custcsv');
		$fp = fopen($temp_file, 'w');
		$this->fputcsv2($fp, $cdata); //, ',', '"');
		$this->fputcsv2($fp, $db_cdata); //, ',', '"');
		fclose($fp);
		//header('Content-type:text/plain');
		return file_get_contents($temp_file);
	}
	
	function fputcsv2($fh, array $fields, $delimiter = ',', $enclosure = '"', $mysql_null = FALSE)
	{
		$delimiter_esc = preg_quote($delimiter, '/');
		$enclosure_esc = preg_quote($enclosure, '/');
		
		$output = array();
		foreach ($fields as $field)
		{
			if ($field === null && $mysql_null)
			{
				$output[] = 'NULL';
				continue;
			}
			
			$output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? (
				$enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure
			) : $field;
		}
		fwrite($fh, join($delimiter, $output) . "\r\n");
	} 
    
}
?>
