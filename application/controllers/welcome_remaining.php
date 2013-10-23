<?

	/*
	 * Alias for quotation - above
	 * fix the navigation, so that the correct tab is highlighted
	 */
	public function invoice($type = 'draft')
	{
		$this->quotation($type);
	}
	/*
	 * Alias for quotation - above
	 * fix the navigation, so that the correct tab is highlighted
	 */
	public function subscription($type = 's_pending')
    {
		$this->quotation($type);
	}
    
    /*
     * Create the quote based on the submission from web_Dev form
     */
	// unwanted function
    public function ajax_webdev_quote()
    {
        if (isset($_POST['web_number_of_pages']) && (int) $_POST['web_number_of_pages'] > 0 && isset($_POST['jobid']) && $_POST['jobid'] > 0)
        {
            $np = (int) $_POST['web_number_of_pages'];
            $jobid = (int) $_POST['jobid'];
            
            if ($_POST['prep_gui'])
            {
                $prep_gui = $this->cfg['our_products'][4]['name'] . "
" . $this->cfg['our_products'][4]['desc'] . "";
                $this->quote_add_item($jobid, $prep_gui, $this->cfg['our_products'][4]['price'], '', FALSE);
            }
            
            $cms = $forms = 0;
            $applications = $app_cms = $app_np = $app_vs = $hosting = $domain = FALSE;
            
            $this->quote_add_item($jobid, "\nXHTML / CSS CODING\nFrom the approved design concepts for the user interface and master content pages, we will code in standards compliant XHTML/CSS the following page(s):", 0, '', FALSE);
            
            for ($i = 0; $i < $np; $i++)
            {
                $page_desc = $cms_page = $form_page = FALSE;
                if (trim($_POST['web_pages_' . $i]) != '')
                {
                    $page_desc = trim($_POST['web_pages_' . $i]);
                    if (isset($_POST['editablepage_' . $i]))
                    {
                        $page_desc .= '';
                        $cms++;
                    }
                    else
                    {
                        $page_desc .= '';
                    }
                    // if (isset($_POST['formpage_' . $i])) $forms++;
                    $this->quote_add_item($jobid, $page_desc, 350, '', FALSE);
                }
                
            }
            
            if ($cms > 0)
            {
                $cms_data = "
CMS PROGRAMMING
We will connect {$cms} page(s) to our WebPublisherCMS allowing the text and image content of those pages editable by the client.";
                $cms_price = $cms * $this->cfg['hourly_rate'] * 0.25;
                $this->quote_add_item($jobid, $cms_data, $cms_price, '', FALSE);
                $app_cms = "
" . $this->cfg['our_products'][0]['name'] . "
" . $this->cfg['our_products'][0]['desc'] . "";
            }
            
            if ($forms > 0)
            {
                $forms_data = "
FORMS
We will generate forms on {$forms} page(s)";
                $forms_price = $forms * $this->cfg['hourly_rate'];
                $this->quote_add_item($jobid, $forms_data, $forms_price, '', FALSE);
            }
            
			/*
            if ($app_cms || $app_vs || $app_np)
            {
                $this->quote_add_item($jobid, "\nWEB APPLICATIONS >", 0, '', FALSE);
            }
			*/
			
            if (isset($_POST['prep_vs']) && $_POST['prep_vs'] == 1)
            {
                $app_cms = "
" . $this->cfg['our_products'][2]['name'] . "
" . $this->cfg['our_products'][2]['desc'] . "";
                $this->quote_add_item($jobid, $app_cms, $this->cfg['our_products'][2]['price'], '', FALSE);
            }
            else if ($app_cms)
            {
                $this->quote_add_item($jobid, $app_cms, $this->cfg['our_products'][0]['price'], '', FALSE);
            }
            
            if (isset($_POST['prep_np']) && $_POST['prep_np'] == 1)
            {
                $app_np = "
" . $this->cfg['our_products'][1]['name'] . "
" . $this->cfg['our_products'][1]['desc'] . "";
                $this->quote_add_item($jobid, $app_np, $this->cfg['our_products'][1]['price'], '', FALSE);
            }
            
            if (isset($_POST['prep_hosting']) && $_POST['prep_hosting'] == 1)
            {
                $hosting = "
" . $this->cfg['our_products'][5]['name'] . "
" . $this->cfg['our_products'][5]['desc'] . "";
                $this->quote_add_item($jobid, $hosting, $this->cfg['our_products'][5]['price'], '', FALSE);
            }
            
            if (isset($_POST['prep_domain']) && $_POST['prep_domain'] == 1 && isset($_POST['prep_domain_name']))
            {
                $domain = "
DOMAIN NAME REGISTRATION 
On behalf of the client and from supplied business details including official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter.
" . $_POST['prep_domain_name'] . "";
                $this->quote_add_item($jobid, $domain, 140, '', FALSE);
            }
            
            echo "{error:false}";
            
        }
        else
        {
            echo "{error:true, errormsg:'Invalid number of pages or jobid'}";
        }
    }
    
    /*
	 * Duplicate an existing quote
	 * @access public
	 * @param int $jobid - Job Id
	 * @param int $quote - Existing quote to duplicate
	 */
	public function ajax_duplicate_quote($jobid = 0, $quote = 83)
	{
        $this->db->where('jobid_fk', $quote);
        $this->db->order_by('item_position', 'asc');
        $q = $this->db->get($this->cfg['dbpref'] . 'items');
        
        if ($q->num_rows() > 0)
        {
            $insert = $q->result_array();
            foreach ($insert as $ins)
            {
                $this->quote_add_item($jobid, $ins['item_desc'], $ins['item_price'], '', FALSE);
            }
            
            echo "{error:false}";
            
        }
        else
        {
            echo "{error:true, errormsg:'Error retrieving data from database!'}";
        }
    }
	
	

    /*
	*Mail converstations send to customer and user
	*
	*/
	public function send_mail_query($jobid, $filename, $msg) {
		$qry = "SELECT first_name, last_name, email FROM ".$this->cfg['dbpref']."users WHERE userid=".$this->session->userdata['logged_in_user']['userid'];
		$users = $this->db->query($qry);
		$user = $users->result_array();
		
		$qry1 = "SELECT email_1 FROM ".$this->cfg['dbpref']."customers WHERE custid = (SELECT custid_fk FROM ".$this->cfg['dbpref']."jobs WHERE jobid=".$jobid.")";
		$customers = $this->db->query($qry1);
		$customer = $customers->result_array();
		
		$query = "INSERT INTO ".$this->cfg['dbpref']."lead_query (job_id,query_msg,query_file_name,query_sent_date,query_sent_to,query_from) 
		VALUES(".$jobid.",'".$msg."','".$filename."','".date('Y-m-d H:i:s')."','".$customer[0]['email_1']."','".$user[0]['email']."')";		
		$q = $this->db->query($query);
	}
	
	/*
	 * View the quote by itself
	 * Optionally generate PDF
	 */
	 
	/* 
    public function view_plain_quote($id = 0, $pdf = FALSE, $stream_pdf = TRUE, $invoice = FALSE, $name_override = '', $template = '', $content_policy = TRUE)
    {
		$this->login_model->check_login();
        $this->load->helper('fix_text');
		
        $restrict = '';
        //if ($this->userdata['level'] == 4)
		//{
			//$restrict = " AND `belong_to` = '{$this->userdata['sales_code']}'";
        //}
        $sql = "SELECT *
                FROM `{$this->cfg['dbpref']}jobs`, `{$this->cfg['dbpref']}customers`
                WHERE `custid` = `custid_fk` AND `jobid` = '{$id}' {$restrict}";
        
        $q = $this->db->query($sql);
		
        if ($q->num_rows() > 0)
        {
            $result = $q->result_array();
            $data['quote_data'] = $result[0];
            $data['view_quotation'] = true;
            
            $items = $this->ajax_quote_items($result[0]['jobid'], 0, TRUE);
            $items = json_decode($items);
			
			$this->db->where('jobid_fk', $result[0]['jobid']);
			$this->db->select_sum('amount');
			$query = $this->db->get($this->cfg['dbpref'] . 'deposits');
			
			$data['deposits'] = 0;
			
			if ($query->num_rows() > 0)
			{
				$query = $query->result_array();
				$data['deposits'] = (float) $query[0]['amount'];
			}
            
			$tsearch[0] = '&#8482;';
			$treplace[0] = '<sup><small>TM</small></sup>';
			
			$tsearch[1] = '&trade;';
			$treplace[1] = '<sup><small>TM</small></sup>';
			
			$tsearch[2] = '&bull;';
			$treplace[2] = '&#149;';
			

            $htm = str_replace($tsearch, $treplace, cleanup_special_chars($items->html));
			
            $data['quote_items'] .= preg_replace(array('/<li id="qi\-[0-9]{1,}">/', '/<\/li>/'), array('<tr><td>', '</td></tr>'), $htm);
			//print_r($data['quote_items']);
            $data['sale_amount'] = $items->sale_amount;
            $data['gst_amount'] = $items->gst_amount;
            $data['total_inc_gst'] = $items->total_inc_gst;
			
			$numeric_total = str_replace(array('$', ','), '', $items->total_inc_gst);
			
			$data['balance'] = number_format((float) $numeric_total - $data['deposits'], 2, '.', ',');
			$data['deposits'] = number_format((float) $data['deposits'], 2, '.', ',');
            
			
			//$log_path = BASEPATH . 'logs/quote_request_log.txt';
			//$fp = fopen($log_path, 'a+');
			
			//fwrite($fp, "request call came in\n");
			if ($pdf == FALSE)
            {
				//fwrite($fp, "display only request\n");
                $this->load->view('pdf/new_quote_only_view', $data);
            }
            else
            {
				//fwrite($fp, "PDF request\n");
				
				if ($template == 'ruler')
				{
					$data['activate_ruler'] = TRUE;
				}
				
                $data['pdf_view'] = TRUE;
                $this->load->plugin('to_pdf');
				$this->load->helper('file');
				# for PDF we add the content disclaimer
				if ($content_policy === TRUE)
				{
					$data['quote_items'] .= '</table>
					
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;"><br />PROJECT COMPLETION POLICY<br />1.1 A project is deemed \'Complete\' and ready for \'Invoice\' or \'Final Payment\' when all services or items listed in the client approved lead have been carried out to a state where they are 100% operational and ready for use online. In approving this lead you agree and acknowledge our definition of \'Complete\' and agree to pay the remainder outstanding balance of your invoice prior to your \'Completed Project\' going live to web.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">1.2 Content is the responsibility of the client. eNoah  iSolution will not allow any project in its schedule to be held up due to the late provision of client content. Content includes: images, text, disclaimers, etc. If the client fails to provide this information to our studio before the project completion date they will be invoiced for the remainder 50% payment regardless as we cannot afford to have the provision of content holding back our production deadlines and delivery dates nor can we afford to have clients holding back final payments due to the absence of content on their part.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;" >\'Dumby\' or \'Mock\' content will be used in place of live content until such time the client provides the necessary content to go live. You will not be charged to have the live content uploaded in place when it is submitted to our studio for replacement of the \'Dumby\' or \'Mock\' content.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">1.3 In approving this lead, you agree to our Project Completion Policy and acknowledge that the provision of content is your sole responsibility and in no way can the absence or delayed provision of content form part of or all of a case to delay the project development from our own production perspective.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">You agree that withholding final payment for the services deemed \'Complete\' by eNoah iSolution Pvt Ltd you are in breach of our agreement to provide our services to your organisation as a result of your acceptance of our project lead and this will result in eNoah iSolution Pvt Ltd handing over a scenario of non or delayed payment without authority or approval by the Managing Director to our debt recovery agency (Baycorp Collection Services) without delay which can have a negative consequence to your credit rating.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">1.4 By approving this lead; submitting an upfront payment deposit you have agreed, understood and will adhere to our Project Completion Policy.</p>
					';
					
				}

				$html = '<table width=100%; border=0 cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#fff8f2">
					<tr>
					<td width="15%"><div style="font-weight:bold;font-size:small;color:#ff4323; "><em>Company</em></div></td>
					<td width="35%" align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; color:#333333;">'.$data['quote_data']['company'].'</td>
					<td width="15%" align="left" valign="top"><div style="font-family:Arial, Helvetica, sans-serif; font-size:small; font-weight:bold;color:#ff4323; "><em>Lead #</em></div></td>
                    <td width="35%" align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$data['quote_data']['invoice_no'].'</td>
					</tr>
					<tr>
					<td width="15%"><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Contact</em></div></td>
					<td width="35%" align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$data['quote_data']['first_name'].' '.$data['quote_data']['last_name'].'</td>
					<td width="15%" align="left" valign="top"><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Date</em></div></td>
                    <td width="35%"align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.date('d/m/Y',strtotime($data['quote_data']['date_created'])).'</td>
					
					</tr>
					<tr>
					<td width="15%"align="left" valign="top"><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Email</em></div></td>
                    <td width="35%"align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$data['quote_data']['email_1'].'</td>
					<td width="15%"align="left" valign="top" ><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Service</em></div></td>
                    <td width="35%"align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$this->cfg['job_categories'][$data['quote_data']['job_category']].'</td>
					</tr>
					</table>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:14px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">
					<b>Project Name : '.$data['quote_data']['job_title'].'</b></p>';
				 
				# set the custom variables
				if (isset($_POST['balance_due']))
				{
					$data['balance_due'] = number_format((float) $_POST['balance_due'], 2, '.', ',');
				}
				
				if (isset($_POST['use_custom_date']))
				{
					$data['use_custom_date'] = $_POST['use_custom_date'];
				}
				
				if (isset($_POST['custom_description']))
				{
					$data['custom_description'] = $_POST['custom_description'];
				}
				if($data['quote_data']['created_by']==-1) {
					
					$html .= $this->load->view('pdf/subs_quote_only_view', $data, true);
				}
				else 

				switch($data['quote_data']['division']) {
					# page info here, db calls, etc.
					case 'SYNG':
						$html .= $this->load->view('pdf/syng_quote_only_view', $data, true);
						break;
					case 'SUBS':
						$html .= $this->load->view('pdf/subs_quote_only_view', $data, true);
						break;
					case 'RT':
						$html .= $this->load->view('pdf/real_quote_only_view', $data, true);
						break;
					default:
						$html .= $this->load->view('pdf/new_quote_only_view', $data, true);
				}

				
				$html .= '<table cellspacing="0" cellpadding="0" border="0" style="border:2px solid #000;"bgcolor="#ff4323" width="100%">
						<tbody><tr>
							<td width="30%">Sale Amount <span id="sale_amount">'.$items->sale_amount.'</span></td>
							<td align="right" width="30%">GST <span id="gst_amount">'.$items->gst_amount.'</span></td>
							<td width="10%">&nbsp;</td>
							<td align="right" width="30%">Total inc GST <span id="total_inc_gst">'.$items->total_inc_gst.'</span></td>
						</tr>
					
					
					</tbody></table>';

				$the_filename = ($name_override != '') ? $name_override : 'output-' . $data['quote_data']['invoice_no'];
				require('html2pdf/html2fpdf.php');
				$pdf=new HTML2FPDF();
				$pdf->SetFont('Arial','B',16);
				$pdf->AddPage();
				$strContent = $html;
				$pdf->WriteHTML($strContent);
				if($stream_pdf==FALSE){
					$full_pdf_path = dirname(FCPATH) . '/vps_data/'.$the_filename.".pdf";
				   $pdf->Output($full_pdf_path, 'F');
				}else{
				$pdf->Output($the_filename.".pdf");
				}
// print a block of text using Write()
				//fwrite($fp, "PDF variables - stream : {$stream_pdf}, invoice : {$invoice}, override: {$name_override}\n");
				//pdf_create($html, $the_filename, $stream_pdf, $invoice);
				//fwrite($fp, "PDF function called\n");
            }
			//fclose($fp);
        }
        else
        {
            echo "Quote does not exist or if you are an account manager you may not be authorised to view this";
        }
        
    }
	*/
	
	
	
  
	/*
	 * Create a copy_quote
	 * Loading just the view	 
	 * @access public
	 */
	//unwanted function
	public function copy_quote($id = 0,$copy=NULL,$lead = FALSE, $customer = FALSE)
	{
		if (empty($copy))	
		{	
			if (is_numeric($lead))
			{
				$lead_details = $this->welcome_model->get_lead($lead);
				$data['existing_lead'] = $lead;
				$data['existing_lead_service'] = $lead_details['belong_to'];
			}
			
			if (is_numeric($customer))
			{
				$data['lead_customer'] = $customer;
			}
			
			/* additional item list */
			$data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
			
			$this->db->order_by('cat_id');
			$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
			$data['categories'] = $q->result_array();
			
			$c = count($data['categories']);
			
			for ($i = 0; $i < $c; $i++)
			{
				$this->db->where('item_type', $data['categories'][$i]['cat_id']);
				$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
				$data['categories'][$i]['records'] = $q->result_array();
			}
			$qa = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}package WHERE status='active'");
			$data['package'] = $qa->result_array();
			
			$data['qid'] = $id;

			$this->load->view('copy_view', $data);
			
		}	
		
		if (!empty($copy))
		{
			$sql = "SELECT *
                FROM `{$this->cfg['dbpref']}jobs`
                WHERE `jobid` = '{$id}'";
        
			$q = $this->db->query($sql);
			if ($q->num_rows() > 0)
			{
				$job_data = $q->result_array();				
				
				$sql_insert = "insert into `{$this->cfg['dbpref']}jobs` (`job_title`,`job_category`,`lead_source`,`lead_assign`,`expect_worth_id`,`expect_worth_amount`,`custid_fk`,
				`job_status`,`created_by`,`modified_by`,`account_manager`,`in_csr`,`belong_to`,`division`,`payment_terms`,
				`invoice_downloaded`,`packageid_fk`,`date_created`,`date_modified`) 
				values 
				('".$_POST['job_title']."','".$job_data[0]['job_category']."','".$_POST['custid_fk']."',
				'0','{$this->userdata['userid']}','{$this->userdata['userid']}','".$job_data[0]['account_manager']."',
				'".$job_data[0]['in_csr']."','".$job_data[0]['belong_to']."','".$job_data[0]['division']."',
				'".$job_data[0]['payment_terms']."','0','".$job_data[0]['packageid_fk']."',NOW(),NOW()) ";
				
				$q = $this->db->query($sql_insert);
				
				$insert_id = $this->db->insert_id();
				
				$invoice_no = (int) $insert_id ;
				$invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);
				
				$this->db->where('jobid', $insert_id);
				$this->db->update($this->cfg['dbpref'] . 'jobs', array('invoice_no' => $invoice_no));
				
				$sql_items = "SELECT *
							  FROM `{$this->cfg['dbpref']}items`
					          WHERE `jobid_fk` = '{$id}'";
							  
				  $q = $this->db->query($sql_items);
				  
				  if ($q->num_rows() > 0)
				  {
						$item_data = $q->result_array();
											
						foreach ($item_data as $tmpitemdata)
						{
							$sqlItemInsert = "INSERT INTO `{$this->cfg['dbpref']}items` (`jobid_fk`,
							`item_position`,`item_desc`,`item_price`,`hours`,`ledger_code`) values 
							('".$insert_id."','".$tmpitemdata['item_position']."',
							'".addslashes($tmpitemdata['item_desc'])."',
							'".$tmpitemdata['item_price']."','".$tmpitemdata['hours']."',
							'".$tmpitemdata['ledger_code']."')	";
							
							$q = $this->db->query($sqlItemInsert);
						}	
				  }
				
			}
				
				$id = "";
				
				$id = $insert_id;
				
				$this->login_model->check_login();
			
				if ( ($data['quote_data'] = $this->welcome_model->get_lead_all_detail($id)) !== FALSE )
				{
					$data['edit_quotation'] = true;
					/*
					if ($this->userdata['level'] == 4 && $data['quote_data']['belong_to'] != $this->userdata['sales_code'])
					{
						$this->session->set_flashdata('login_errors', array("You are not allwed to view/edit this document!"));
						$referer = (preg_match('/^http/', $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->config->item('base_url') . 'welcome/quotation';
						header('Location:' . $referer);
						exit();
					}
					*/
					/**
					 * Check to see if this has already been downloaded by accounts
					 */
					if ($data['quote_data']['invoice_downloaded'] == 1)
					{
						$this->session->set_flashdata('login_errors', array("Reconciled Invoices cannot be edited!"));
						$referer = (preg_match('/^http/', $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->config->item('base_url') . 'welcome/quotation';
						header('Location:' . $referer);
						exit();
					}
					
					/* additional item list */
					$data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
					
					$this->db->order_by('cat_id');
					$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
					$data['categories'] = $q->result_array();
					
					$c = count($data['categories']);
					$data['hosting']=$this->ajax_hosting_load($id);
					for ($i = 0; $i < $c; $i++)
					{
						$this->db->where('item_type', $data['categories'][$i]['cat_id']);
						$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
						$data['categories'][$i]['records'] = $q->result_array();
					}
					$qa = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}package WHERE status='active'");
					$data['package'] = $qa->result_array();
					//$add = $this->db->get("{$this->cfg['dbpref']}additional_items");
					//if ($add->num_rows() > 0) $data['item_mgmt_add_list'] = $add->result_array();
					
					$this->load->view('welcome_view', $data);
				}
				else
				{
					$this->session->set_flashdata('login_errors', array("Quote does not exist or you may not be authorised to view this."));
					redirect('welcome/quotation');
				}	
		}
		
		
	}
	
	

   
	
   
	
	
	
	
	

	
	// TO BE MOVED TO A CRON JOB - unwanted function
	public function create_subscription_invoices()
	{
		error_reporting(E_ALL);
		$this->load->model('subscriptions_model');
		
		$customers = $this->subscriptions_model->get_all_customers_with_subscriptions();
		
		foreach($customers as $k=>$customer)
		{
			$this->create_subscription_invoice_for_customer($customer['custid'], $customer);
		}
	}
	// unwanted function
	public function create_subscription_invoice_for_customer($cust_id, $customer)
	{
		$this->load->model('subscriptions_model');
		
		if ($cust_id > 0) {
			$items = $this->subscriptions_model->get_items_for_customer($cust_id);
			$discounts = array();
			foreach ($items as $k=>$data)
			{
				$discounts = $this->subscriptions_model->get_discounts_for_item($data['recurringitemid']);
				$items[$k]['discounts'] = $discounts;
			}
			
			if (!empty($items))
			{
				$invoice_id = $this->subscriptions_model->create_invoice_from_items($cust_id, $items, $customer);
			}
			
		}
	}
	
	// unwanted function
	public function ajax_hosting_load($jobid=false) {
		$query = $this->db->query("SELECT hostingid_fk FROM ".$this->cfg['dbpref']."hosting_job WHERE jobid_fk='{$jobid}'");
		$t=array();
		foreach($query->result_array() as $v) $t[]=$v['hostingid_fk'];
		$sql="SELECT * FROM ".$this->cfg['dbpref']."hosting as H, ".$this->cfg['dbpref']."jobs as J WHERE J.custid_fk=H.custid_fk && J.jobid={$jobid}";
		$query = $this->db->query($sql);
		
		$temp='';
		foreach($query->result_array() as $val){
			if(in_array($val['hostingid'],$t)) $s=' selected="selected"'; else $s=' ';
			$temp.= '<option value="'.$val['hostingid'].'" '.$s.'>'.$val['domain_name'].'</option>';
		}
		return $temp;
	}
	
	// unwanted function
	function package($tab='') {
		switch($tab){
			case 'quotation':
				$arr=array(0,1,2,3,15,21,22);break;
			case 'invoice':
				$arr=array(4,5,6,7,25);break;
			case 'subscription':
				$arr=array(30,31,32);break;
			case 'production';
				$arr=array(4,5,15);break;
			default:
				$arr=array(0,1,2,3,4,15,21,22);break;
		}
		$arr=implode(',',$arr);
		$data['page_heading'] = $tab;
		$search='';
		if(isset($_POST['keyword']) && strlen($_POST['keyword'])>0 && $_POST['keyword']!='Invoice No, Job Title, Name or Company') {
			$search.=" AND (J.invoice_no='{$_POST['keyword']}' || J.job_title LIKE '%{$_POST['keyword']}%' || C.company LIKE '%{$_POST['keyword']}%' || C.first_name LIKE '%{$_POST['keyword']}%' || C.last_name='{$_POST['keyword']}' )";
		}
		$sql = "SELECT *, SUM(`".$this->cfg['dbpref']."items`.`item_price`) AS `project_cost`,
					(SELECT SUM(`amount`) FROM `".$this->cfg['dbpref']."deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
                FROM `".$this->cfg['dbpref']."items`, `".$this->cfg['dbpref']."customers` AS C, `".$this->cfg['dbpref']."jobs` AS J, ".$this->cfg['dbpref']."hosting as H
				WHERE J.job_status IN ({$arr}) AND C.`custid` = J.`custid_fk` AND `jobid` = `".$this->cfg['dbpref']."items`.`jobid_fk` && H.custid_fk=C.custid
					{$search}
                GROUP BY `jobid`
				ORDER BY `belong_to`, `date_created`";
		
		$rows = $this->db->query($sql);
		$records=$data['records'] = $rows->result_array();
		$temp[]=0;
		foreach($records as $val) { $temp[]=$val['custid'];}
		$temp=implode(',',$temp);
		$sql="SELECT * FROM `".$this->cfg['dbpref']."hosting_package` as P, ".$this->cfg['dbpref']."hosting as H WHERE P.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})";
		$rows = $this->db->query($sql);
		$hosting=$rows->result_array();
		
		$rows = $this->db->query("SELECT * FROM `".$this->cfg['dbpref']."hosting_job` as J, ".$this->cfg['dbpref']."hosting as H WHERE J.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})");
		$jobs=$rows->result_array();
		
		$j_temp=array();
		foreach($jobs as $key=>$val){
			$v=$val['jobid_fk'];
			$j_temp[$v][]=$val['hostingid_fk'];
		}
		$data['JOBS']=$j_temp;
		
		$rows = $this->db->query("SELECT * FROM `".$this->cfg['dbpref']."package`  WHERE  status='active'");
		$data['packages']=$rows->result_array();
		$p_temp=array();
		foreach($hosting as $key=>$val){
				$v=$val['hostingid'];$q=$val['packageid_fk'];
				$p_temp[$v][$q]=$val['packageid_fk'];
		}
		//$data['hosting']=$p_temp;
		foreach($records as $key=>$val){
			$v=$val['jobid'];
			if(isset($_POST['pack_name']) && $_POST['pack_name']==-1) {
				if(!empty($j_temp[$v])) unset($data['records'][$key]);
			}
			else {
				if(empty($j_temp[$v])) { unset($data['records'][$key]);continue;}
				if(isset($_POST['pack_name']) && $_POST['pack_name']>0) {
					$i=0;
					foreach($j_temp[$v] as $k1=>$v1){
						if(empty($p_temp[$v1])) continue;
						if(array_search($_POST['pack_name'],$p_temp[$v1])>0) $i++;
					}				
					if($i==0) { unset($data['records'][$key]);continue;}
				}
			}
		}
		//echo '<pre>';print_r($p_temp);print_r($j_temp);echo '</pre>';
		$this->load->view('quotation_view',$data);
	}
	// unwanted function
	function generate_invoice(){
		if(isset($_POST['auto_generate']) && $_POST['auto_generate']=='auto_generate'){
			$sql1="SELECT *, DATE_SUB(DATE_ADD( NOW() , INTERVAL P.duration MONTH ) ,INTERVAL 1 DAY) AS expiry FROM ".$this->cfg['dbpref']."package P, ".$this->cfg['dbpref']."hosting as H
					RIGHT JOIN ".$this->cfg['dbpref']."hosting_package HP ON HP.hostingid_fk=H.hostingid
					WHERE P.package_id=HP.packageid_fk && P.status='active' && HP.due_date<NOW()
					";
			$rows1=$this->db->query($sql1);
			$sql = "SELECT * FROM `".$this->cfg['dbpref']."customers` AS C, `".$this->cfg['dbpref']."jobs` AS J, ".$this->cfg['dbpref']."hosting as H
					RIGHT JOIN ".$this->cfg['dbpref']."hosting_job HJ ON HJ.hostingid_fk=H.hostingid
					WHERE J.job_status IN (4,5,6,7,25) AND C.`custid` = J.`custid_fk`  && H.custid_fk=C.custid  
					";
			$rows = $this->db->query($sql);
			$h=array();$h1=array();
			foreach($rows->result_array() as $val){
				if(in_array($val['hostingid'],$h)) continue;
				$h[]=$val['hostingid'];
			}
			foreach($rows1->result_array() as $val){
				if(in_array($val['hostingid'],$h1)) continue;
				$h1[]=$val['hostingid'];
			}
			$h2=array_diff($h1,$h);
			if(sizeof($h2)>0){
				$q="INSERT INTO `".$this->cfg['dbpref']."jobs` (`jobid`, `job_title`, `job_desc`, `job_category`, `invoice_no`, `custid_fk`, `date_quoted`, `date_invoiced`, `job_status`, `complete_status`, `assigned_to`, `date_start`, `date_due`, `date_created`, `date_modified`, `created_by`, `account_manager`, `in_csr`, `belong_to`, `division`, `payment_terms`, `invoice_downloaded`, `log_view_status`, `invoice_status`) VALUES ";
				$i=1;$s2=array();
				$q1="INSERT INTO `".$this->cfg['dbpref']."hosting_job` (`jobid_fk`, `hostingid_fk`) VALUES ";
				$q2="INSERT INTO `".$this->cfg['dbpref']."items` (`itemid` ,`jobid_fk` ,`item_position` ,`item_desc` ,`item_price` ,`hours` ,`ledger_code`) VALUES ";
				$tq=$this->db->query("SELECT (SELECT MAX(jobid) FROM ".$this->cfg['dbpref']."jobs) as maxid,(SELECT MAX(invoice_no) FROM ".$this->cfg['dbpref']."jobs) as maxinv");
				$tqr=$tq->result_array();
				$t=array();
				foreach($rows1->result_array() as $val){
					if(!in_array($val['hostingid'],$h2) || in_array($val['hostingid'],$t)) continue;
					$jobid=$tqr[0]['maxid']+$i;$t[]=$val['hostingid'];
					$invoice_no=(float)$tqr[0]['maxinv']+$i;
					$s[]='('.$jobid.', "Website Hosting for '.$val['domain_name'].'", NULL, 0, "00'.$invoice_no.'", '.$val['custid_fk'].', NULL, "'.date('Y-m-d H:i:s').'", 4, NULL, NULL, "'.date('Y-m-d H:i:s').'", "'.$val['expiry'].'", "'.date('Y-m-d H:i:s').'", NULL, "-1", NULL, 0, "VT", "VTD", 0, 0, NULL, 0)';
					$s1[]='('.$jobid.','.$val['hostingid'].')';
					$j=1;
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Thank you for entrusting eNoah  iSolution with your web technology requirements.
\nPlease see below an itemised breakdown of our service offering to you:",0, NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Domain Name : '.$val['domain_name'].'",0 , NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Package Name : '.$val['package_name'].'",'.$val['package_price'].' , NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Period of Package : '.date('d-M-y').' to '.date('d-M-y',strtotime($val['expiry'])).'", 0, NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"'.mysql_escape_string($val['details']).'",0 , NULL, 0)';
					$i++;
				}
			$q.=implode(',',$s);
			$q1.=implode(',',$s1);
			$q2.=implode(',',$s2);
			$this->db->query($q);
			$this->db->query($q1);
			$this->db->query($q2);
			}
			$j=array();
			foreach($rows->result_array() as $val){
				if((strtotime($val['date_due'])-strtotime(date('Y-m-d H:i:s')))>0) continue;
				if($val['invoice_status']!=0) continue;
				if(strpos(strtolower($val['job_title']), 'hosting') == false) continue;
				$j[$val['jobid']]=$val['hostingid'];
				
			}
			//print_r($j);exit;
			$h=array();
			foreach($rows1->result_array() as $val){
				$h[$val['hostingid_fk']]=$val['hostingid_fk'];
			}
			if(sizeof($j)>0){
				$JOBS=array();
				foreach($j as $k=>$v){
					if(in_array($v,$h)) $JOBS[]=$k;	
				}
			}
		}
		if(isset($_POST['generate']) && $_POST['generate']=='generate'){
			if(isset($_POST['jobs']))	$JOBS=$_POST['jobs'];
		}
		if(!empty($JOBS) && sizeof($JOBS)>0) {
			$jobs=implode(',',$JOBS);
			
			$r=$this->db->query("SELECT * FROM  ".$this->cfg['dbpref']."hosting_job H LEFT JOIN ".$this->cfg['dbpref']."jobs J ON J.jobid=H.jobid_fk WHERE J.jobid IN ({$jobs}) && H.jobid_fk IN ({$jobs});");
			$temp_arr=array();
			if(sizeof($r->result_array())>0)
			foreach($r->result_array() as $v1){
				$v=$v1['jobid_fk'];
				$temp_arr[$v][]=$v1['hostingid_fk'];
			}
			
			$dumm_job=array();
			foreach($r->result_array() as $v){
				if(in_array($v['jobid'],$dumm_job)) continue;
				$dumm_job[]=$v['jobid'];
				$tq=$this->db->query("SELECT (SELECT MAX(jobid) FROM ".$this->cfg['dbpref']."jobs) as maxid,(SELECT MAX(invoice_no) FROM ".$this->cfg['dbpref']."jobs) as maxinv");
				$tqr=$tq->result_array();
				$jobid=$v['jobid'];
				
				$ins['invoice_no']='00'.((float)$tqr[0]['maxinv']+1);
				if(!empty($temp_arr[$jobid])){
					$tem=implode(',',$temp_arr[$jobid]);
					$tq1=$this->db->query("SELECT * FROM ".$this->cfg['dbpref']."package P LEFT JOIN ".$this->cfg['dbpref']."hosting_package HP ON HP.packageid_fk=P.package_id LEFT JOIN ".$this->cfg['dbpref']."hosting H ON H.hostingid=HP.hostingid_fk  WHERE HP.hostingid_fk IN ({$tem}) && P.status='active' ");
					$tqr1=$tq1->result_array();
				}
				$domain=array();
				if(sizeof($tqr1)>0)
				foreach($tqr1 as $val){
				if(in_array($val['hostingid'],$domain)) continue;
				$domain[]=$val['hostingid'];
				$Hosting=$val['domain_name'];
				
				$ins['jobid']=++$tqr[0]['maxid'];
				$ins['job_title']=$v['job_title'];
				$ins['job_status']=4;
				$ins['job_category']=$v['job_category'];
				
				$ins['custid_fk']=$v['custid_fk'];
				$ins['belong_to']=$v['belong_to'];
				$ins['division']=$v['division'];
				$ins['date_invoiced']=date('Y-m-d H:i:s');
				$ins['date_start']=date('Y-m-d H:i:s');
				$ins['date_due']=date('Y-m-d H:i:s',(time()+($tqr1[0]['duration']*30*24*60*60)-86400));
				$ins['date_created']=date('Y-m-d H:i:s');
				$ins['created_by']=-1;
				$this->db->insert($this->cfg['dbpref'].'jobs', $ins) ;
				
				if(!empty($temp_arr[$jobid])){
				$query='INSERT INTO '.$this->cfg['dbpref'].'hosting_job (jobid_fk, hostingid_fk) VALUES ';
				$s=array();
				$this->db->delete($this->cfg['dbpref']."hosting_job", array('jobid_fk' => $ins['jobid']));
				$s[]=' ('.$ins['jobid'].','.$val['hostingid'].')';
			
				$s=implode(',',$s);
				$query.=$s;
				if(strlen($query)>0) $this->db->query($query);
				
				$i=1;$t=array();$s1=array();
				$q1="INSERT INTO `".$this->cfg['dbpref']."items` (`itemid` ,`jobid_fk` ,`item_position` ,`item_desc` ,`item_price` ,`hours` ,`ledger_code`) VALUES ";
				$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Thank you for entrusting eNoah  iSolution with your web technology requirements.
\nPlease see below an itemised breakdown of our service offering to you:",0, NULL, 0)';
				foreach($tqr1 as $tk){
					if(in_array($tk['package_id'],$t)) continue;
					$t[]=$tk['package_id'];
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Domain Name : '.$Hosting.'",0 , NULL, 0)';
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Package Name : '.$tk['package_name'].'",'.$tk['package_price'].' , NULL, 0)';
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Period of Package : '.date('d-M-y').' to '.date('d-M-y',(time()+($tk['duration']*30*24*60*60)-86400)).'",0, NULL, 0)';
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"'.mysql_escape_string($tk['details']).'",0 , NULL, 0)';
				}
				$q1.=implode(',',$s1);
				$this->db->query($q1);
				}
				}
			}
			$this->db->query("UPDATE ".$this->cfg['dbpref']."jobs SET invoice_status=1, date_due=NOW() WHERE jobid IN ({$jobs})");
		}
		if(isset($_POST['send']) && $_POST['send']=='send'){
			if(isset($_POST['jobs'])) {
				foreach($_POST['jobs'] as $val){
					$this->db->where('jobid', $val);
					$job_details = $this->db->get($this->cfg['dbpref'] . 'jobs');
					
					if ($job_details->num_rows() > 0) 
					{
						$job = $job_details->result_array();
						$this->db->where('custid', $job[0]['custid_fk']);
						$client_details = $this->db->get($this->cfg['dbpref'] . 'customers');
						$client = $client_details->result_array();
				
						$this->load->plugin('phpmailer');
						//$send_to=$client[0]['email_1'];
						$send_to='jranand@enoahisolution.com';
						$pdf_file_attach = array();
						
						$log_subject = "eNoah log - {$job[0]['job_title']} [ref#{$job[0]['jobid']}] {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}";
						/*$log_email_content = "--visiontechdigital.com\n\n" .
												"\n\n--\n" . $this->userdata['signature'];*/
						$log_email_content = "--enoahisolution.com\n\n" .
												"\n\n--\n" . $this->userdata['signature'];
						$temp_file_prefix = 'invoice';
						$temp_file_name = $temp_file_prefix . '-' . $job[0]['invoice_no'];
						$temp_file_path = microtime(true) . $temp_file_name;
						$full_file_path = dirname(FCPATH) . '/vps_data/' . $temp_file_path . '.pdf';
						$content_policy = TRUE;
						if (isset($_POST['ignore_content_policy']))	{
							$content_policy = FALSE;
						}
						// $this->view_plain_quote($job[0]['jobid'], TRUE, FALSE, FALSE, $temp_file_path, '', false);
						if (file_exists($full_file_path)) {
							$pdf_file_attach[] = array($full_file_path, $temp_file_name . '.pdf');
						}
						$successful='';
					    $this->email->from('jranand@enoahisolution.com','Anand');
						$this->email->to($send_to);
						$this->email->subject($log_subject);
						$this->email->message($log_email_content);
						$this->email->attach($pdf_file_attach);
						if($this->email->send()){
								$successful = 'This log has been emailed to:<br />'.$send_to;

						}
						/*if (send_email($send_to, $log_subject, $log_email_content,'', '', '', $pdf_file_attach)) {
							$successful = 'This log has been emailed to:<br />'.$send_to;
						}
						*/
						
						$ins['jobid_fk'] = $val;
						$ins['userid_fk'] = '';
						$ins['custid_fk'] = $client[0]['custid'];
						$ins['invoice_no'] = $job[0]['invoice_no'];
						$ins['date_created'] = date('Y-m-d H:i:s');
						$ins['log_detail'] =  $successful;
						$stick_class = '';
						if (isset($_POST['log_stickie'])){
							$ins['stickie'] = 1;
							$stick_class = ' stickie';
						}
						$this->db->insert($this->cfg['dbpref'] . '_invoice_logs', $ins);
					}
				}
			}
		}
		redirect('invoice/billing/');
	}
	// unwanted function
	function billing($tab=''){
			$arr=array(4,5,6,7,25);
			$arr=implode(',',$arr);
		$data['page_heading'] = 'Invoice Billing';
		$search='';
		$criteria='';
		if(isset($_POST['pack_name']) && $_POST['pack_name']==-2) {
			$arr=4;
		}
		else if(isset($_POST['pack_name']) && $_POST['pack_name']==-3) {
			$criteria='  AND  J.invoice_status=0';
		}
		if(isset($_POST['keyword']) && strlen($_POST['keyword'])>0 && $_POST['keyword']!='Invoice No, Job Title, Name or Company') {
			$search.=" AND (J.invoice_no='{$_POST['keyword']}' || J.job_title LIKE '%{$_POST['keyword']}%' || C.email_1 LIKE '%{$_POST['keyword']}%'|| C.company LIKE '%{$_POST['keyword']}%' || C.first_name LIKE '%{$_POST['keyword']}%' || C.last_name='{$_POST['keyword']}' )";
		}
		$sql = "SELECT * FROM `".$this->cfg['dbpref']."customers` AS C, `".$this->cfg['dbpref']."jobs` AS J, ".$this->cfg['dbpref']."hosting as H
				WHERE J.job_status IN ({$arr}) AND C.`custid` = J.`custid_fk`  && H.custid_fk=C.custid
					{$search} {$criteria}
                GROUP BY `jobid`
				ORDER BY jobid DESC,`belong_to`, `date_created`";
		$rows = $this->db->query($sql);
		$records=$data['records'] = $rows->result_array();
		$temp[]=0;
		foreach($records as $val) { $temp[]=$val['custid'];}
		$temp=implode(',',$temp);
		$sql="SELECT * FROM `".$this->cfg['dbpref']."hosting_package` as P, ".$this->cfg['dbpref']."hosting as H WHERE P.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})";
		$rows = $this->db->query($sql);
		$hosting=$rows->result_array();
		$rows = $this->db->query("SELECT * FROM `".$this->cfg['dbpref']."hosting_job` as J, ".$this->cfg['dbpref']."hosting as H WHERE J.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})");
		$jobs=$rows->result_array();
		$j_temp=array();
		foreach($jobs as $key=>$val){
			$v=$val['jobid_fk'];
			$j_temp[$v][]=$val['hostingid_fk'];
		}
		$data['JOBS']=$j_temp;
		$rows = $this->db->query("SELECT * FROM `".$this->cfg['dbpref']."package`  WHERE  status='active'");
		$data['packages']=$rows->result_array();
		$p_temp=array();
		foreach($hosting as $key=>$val){
				$v=$val['hostingid'];$q=$val['packageid_fk'];
				$p_temp[$v][$q]=$q;
		}
		$data['hosting']=$p_temp;
		foreach($records as $key=>$val){
			$v=$val['jobid'];
			if(isset($_POST['pack_name']) && $_POST['pack_name']==-1) {
				if(!empty($j_temp[$v])) unset($data['records'][$key]);
			}
			else {
				if(empty($j_temp[$v])) { unset($data['records'][$key]);continue;}
				if(isset($_POST['pack_name']) && $_POST['pack_name']>0) {
					$i=0;
					foreach($j_temp[$v] as $k1=>$v1){
						if(empty($p_temp[$v1])) continue;
						if(array_search($_POST['pack_name'],$p_temp[$v1])>0) $i++;
					}				
					if($i==0) { unset($data['records'][$key]);continue;}
				}
			}
		}
		$data['NO_Package']=false;
		if(isset($_POST['pack_name']) && $_POST['pack_name']==-1) $data['NO_Package']=true;
		//echo '<pre>';print_r($data);print_r($p_temp);print_r($j_temp);echo '</pre>';
		$this->load->view('billing_view',$data);
	}
	

	?>