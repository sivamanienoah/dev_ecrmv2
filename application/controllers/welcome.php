<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() {
		parent::__construct();
		
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('welcome_model');
		$this->load->model('customer_model');
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
		
		redirect('welcome/quotation');
    }
	
	/*
	 * List all the Leads based on levels
	 * @access public
	 */
	public function quotation($type = 'draft', $tab='') {

		$page_label = 'Leads List' ;
		
		$data['lead_stage'] = $this->stg_name;
		$data['customers'] = $this->welcome_model->get_customers();
		$data['lead_owner'] = $this->welcome_model->get_users();
		$data['regions'] = $this->regionsettings_model->region_list($offset = false, $search = false);
		
		$this->load->view('leads/quotation_view', $data);
	}
	
	/*
	 * List all the Leads based on levels with advanced search filter.
	 */
	public function advance_filter_search($stage='null', $customer='null', $worth='null', $owner='null', $leadassignee='null', $regionname='null',$countryname='null', $statename='null', $locname='null', $lead_status='null', $keyword='null') 
	{
		if (count($_POST)>0) {
			$stage = $_POST['stage'];
			$customer = $_POST['customer'];
			$worth = $_POST['worth'];
			$owner = $_POST['owner'];
			$leadassignee = $_POST['leadassignee'];
			$regionname = $_POST['regionname'];
			$countryname = $_POST['countryname'];
			$statename = $_POST['statename'];
			$locname = $_POST['locname'];
			$lead_status = $_POST['lead_status'];
			$keyword = $_POST['keyword'];
			$excel_arr = array();
			foreach ($_POST as $key => $val) {
				$excel_arr[$key] = $val;
			}
			// print_r($excel_arr); exit;
			$this->session->set_userdata(array("excel_download"=>$excel_arr));
		} else {
			$this->session->unset_userdata(array("excel_download"=>''));
		}
		
		$filter_results = $this->welcome_model->get_filter_results($stage, $customer, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status, $keyword);	

		$data['filter_results'] = $filter_results;

		$data['stage'] 		  = $stage;
		$data['customer']	  = $customer;
		$data['worth'] 		  = $worth;
		$data['owner'] 		  = $owner;
		$data['leadassignee'] = $leadassignee;
		$data['regionname']   = $regionname;
		$data['countryname']  = $countryname;
		$data['statename'] 	  = $statename;
		$data['locname'] 	  = $locname;
		$data['lead_status']  = $lead_status;
		$data['keyword'] 	  = $keyword;

		$this->load->view('leads/advance_filter_view', $data);
	}
	
	/*
	 * Display the Lead
	 * @access public
	 * @param int $id - Job Id
	 */
	public function view_quote($id = 0, $quote_section = '') 
	{
        $this->load->helper('text');
		$this->load->helper('fix_text');
		
		$usid = $this->session->userdata('logged_in_user');
		
		$getLeadDet = $this->welcome_model->get_lead_detail($id);
		
		if(!empty($getLeadDet)) {
            $data['quote_data'] = $getLeadDet[0];
            $data['view_quotation'] = true;
			$data['user_accounts'] = $this->welcome_model->get_users();

			if (!strstr($data['quote_data']['log_view_status'], $this->userdata['userid']))
			{
				$log_view_status['log_view_status'] = $data['quote_data']['log_view_status'] . ':' . $this->userdata['userid'];
				$logViewStatus = $this->welcome_model->updt_log_view_status($id, $log_view_status);
			}
            
			$data['log_html'] = '';
			$getLogs = $this->welcome_model->get_logs($id);
            
            if (!empty($getLogs)) {
                $log_data = $getLogs;
                $this->load->helper('url');
                
                foreach ($log_data as $ld) {
					$user_data = $this->welcome_model->get_user_data_by_id($ld['userid_fk']);
					
					if (count($user_data) < 1)
					{
						echo '<!-- ', print_r($ld, TRUE), ' -->';
						continue;
					}
                    
                    $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
                    
					$fancy_date = date('l, jS F y h:iA', strtotime($ld['date_created']));
					
					$stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';					

$table = <<<HDOC
<tr id="log" class="log{$stick_class}">
<td id="log" class="log{$stick_class}">
<p class="data log{$stick_class}">
        <span class="log{$stick_class}">{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc log{$stick_class}">
        {$log_content}
    </p>
</td>
</tr>
HDOC;

                    $data['log_html'] .= $table;
					unset($table, $user_data, $user, $log_content);
                }
            }
			
			/**
			 * Get files associated with this job
			 **/
			$fcpath = UPLOAD_PATH; 
		    $f_dir = $fcpath . 'files/' . $id . '/'; 
			$data['job_files_html'] = $this->welcome_model->get_job_files($f_dir, $fcpath, $data['quote_data']);
			$data['query_files1_html'] = $this->welcome_model->get_query_files_list($id);
			
			/**
			 * Get URLs associated with this job
			 */
			$data['job_urls_html'] = $this->welcome_model->get_job_urls($id);
			
			/*
			//this code will be reuse for calculate the actual worth of project
			$actual_worths = $this->db->query("SELECT SUM(`".$this->cfg['dbpref']."items`.`item_price`) AS `project_cost`
								FROM `{$this->cfg['dbpref']}items`
								WHERE `jobid_fk` = '{$id}' GROUP BY jobid_fk");
			// echo $this->db->last_query(); exit;
			$data['actual_worth'] = $actual_worths->result_array();	
			echo "<pre>"; print_r($data['actual_worth']);
			*/

			$data['lead_stat_history'] = $this->welcome_model->get_lead_stat_history($id);
			
			$data['job_cate'] = $this->welcome_model->get_job_categories();
			
			$this->load->view('leads/welcome_view_quote', $data);
        }
		else 
		{
            echo "Quote does not exist or if you are an account manager you may not be authorised to view this";
        }
    }
	
	 /*
     * provides the list of items
     * that belong to a given job
     * @param jobid
     * @param itemid (latest intsert)
     * @return echo json string
     */
    function ajax_quote_items($jobid = 0, $itemid = 0, $return = false) 
	{
		$this->load->helper('text');
		$this->load->helper('fix_text');
		
		$quote = $this->welcome_model->get_quote_items($jobid);

        if (!empty($quote)) {
            $html = '';
            $sale_amount = 0;
            foreach ($quote as $row) {
			
                if (is_numeric($row['item_price']) && $row['item_price'] != 0) {
                    $sale_amount += $row['item_price'];
					$row['item_price'] = '$' . number_format($row['item_price'], 2, '.', ',');
					$row['item_price'] = preg_replace('/^\$\-/', '-$', $row['item_price']);
				} else {
                    $row['item_price'] = '';
                }
				
                if ($row['hours'] > 0) {
					$row['hours'] = 'Hours : ' . $row['hours'];
				} else {
                    $row['hours'] = '';
                }
				$content_item = nl2br(cleanup_chars(ascii_to_entities($row['item_desc'])));
				if(!empty($row['item_price'])) {
					$html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" width="85%">' . stripslashes($content_item) . '</td><td width="14%" class="item-price width100px" align="right" valign="bottom">' . $row['item_price'] . '</td></tr></table></li>';
				} else {
					$html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" colspan="2">' . stripslashes($content_item) . '</td></tr></table></li>';
				}
            }
            
            $json['sale_amount'] = '$' . number_format($sale_amount, 2, '.', ',');
            $json['gst_amount'] = ($sale_amount > 0) ? '$' . number_format($sale_amount/10, 2, '.', ',') : '$0.00';
			
            $json['total_inc_gst'] = '$' . number_format($sale_amount*1.1, 2, '.', ',');
            $json['numeric_total_inc_gst'] = $sale_amount*1.1;
			
            $json['error'] = false;
            $json['html'] = $html;
        } else {
            $json['sale_amount'] = '0.00';
            $json['gst_amount'] = '0.00';
            $json['total_inc_gst'] = '0.00';
            $json['error'] = false;
            $json['html'] = '';
        }
			$json['itemid'] = $itemid;
			
			if ($return)
			return json_encode($json);
			else
			echo json_encode($json);
    }
	
	
    /*
	 * Create a new quote
	 * Loading just the view
	 * Quotes are created with Ajax functions
	 * @access public
	 */
	public function new_quote($lead = FALSE, $customer = FALSE) 
	{
		/* additional item list */
		// $data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
		$data['categories'] = $this->welcome_model->get_categories();
		$c = count($data['categories']);
		for ($i = 0; $i < $c; $i++) {
			$data['categories'][$i]['records'] = $this->welcome_model->get_cat_records($data['categories'][$i]['cat_id']);
		}
		$data['lead_source'] = $this->welcome_model->get_lead_sources();
		$data['expect_worth'] = $this->welcome_model->get_expect_worths();
		$data['job_cate'] = $this->welcome_model->get_job_categories();
		$data['sales_divisions'] = $this->welcome_model->get_sales_divisions();
		
		$this->load->view('leads/welcome_view', $data);
	}
	
	/**
	 *  Set the quote editing interface
	 */
    function edit_quote($id = 0) 
	{
        if ( ($data['quote_data'] = $this->welcome_model->get_lead_all_detail($id)) !== FALSE )
        {	
            $data['edit_quotation'] = true;

			$data['categories'] = $this->welcome_model->get_categories();
			
			$c = count($data['categories']);

			for ($i = 0; $i < $c; $i++) {
				$data['categories'][$i]['records'] = $this->welcome_model->get_cat_records($data['categories'][$i]['cat_id']);
			}
			
			$data['lead_source_edit'] = $this->welcome_model->get_lead_sources();
			
			$regid = $data['quote_data']['add1_region'];
			$cntryid = $data['quote_data']['add1_country'];
			$steid = $data['quote_data']['add1_state'];
			$locid = $data['quote_data']['add1_location'];
			
			//for new level concept - start here
			$reg_lvl_id = array(5,4,3);
			$cont_lvl_id = array(5,4,2);
			$ste_lvl_id = array(5,3,2);
			$loc_lvl_id = array(4,3,2);
			
			$regUserList = $this->welcome_model->get_lvl_users('levels_region', 'region_id', $regid, $reg_lvl_id);
			$cntryUserList = $this->welcome_model->get_lvl_users('levels_country', 'country_id', $cntryid, $cont_lvl_id);
			$steUserList = $this->welcome_model->get_lvl_users('levels_state', 'state_id', $steid, $ste_lvl_id);
			$locUserList = $this->welcome_model->get_lvl_users('levels_location', 'location_id', $locid, $loc_lvl_id);
			$globalUserList = $this->welcome_model->get_lvlOne_users();

			$userList = array_merge_recursive($regUserList, $cntryUserList, $steUserList, $locUserList, $globalUserList);
			$users[] = 0;
			foreach($userList as $us)
			{
				$users[] = $us['user_id'];
			}	
			
			$userList = array_unique($users);
			$userList = array_values($userList);

			// $userList = implode(',', $userList);
			$data['lead_assign_edit'] = $this->welcome_model->get_userlist($userList);
			//for new level concept - end here
			
			$data['expect_worth'] = $this->welcome_model->get_expect_worths();
			$data['lead_stage'] = $this->welcome_model->get_lead_stage();
			$data['job_cate'] = $this->welcome_model->get_job_categories();
			$data['sales_divisions'] = $this->welcome_model->get_sales_divisions();
			
            $this->load->view('leads/welcome_view', $data);
        }
        else
        {
            $this->session->set_flashdata('header_messages', array("Status Changed Successfully."));
			header('Location: ' . $_SERVER['HTTP_REFERER']);
            //redirect('welcome/quotation');
        }
        
    }
	
	/**
	 * Initiates and create the quote based on an ajax request
	 */
	function ajax_create_quote() {
	
		if (trim($this->input->post('job_title')) == '' || !preg_match('/^[0-9]+$/', trim($this->input->post('job_category'))) || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_source'))) || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_assign'))))
        {
			echo "{error:true, errormsg:'Title and job category are required fields!'}";
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
			$ins['job_title'] = $data['job_title'];
			$ins['custid_fk'] = $data['custid_fk'];
			$ins['job_category'] = $data['job_category'];
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
			$ins['job_status'] = 1;
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
				$lead_hist['jobid'] = $insert_id;
				$lead_hist['dateofchange'] = date('Y-m-d H:i:s');
				$lead_hist['previous_status'] = 1;
				$lead_hist['changed_status'] = 1;
				$lead_hist['lead_status'] = 1;
				$lead_hist['modified_by'] = $this->userdata['userid'];
				$insert_lead_stg_his = $this->welcome_model->insert_row('lead_stage_history', $lead_hist);
				
				//history - lead_status_history
				$lead_stat_hist['jobid'] = $insert_id;
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
                $json['job_title'] = htmlentities($data['job_title'], ENT_QUOTES);
                $json['job_category'] = $data['job_category'];
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
	
	 /*
     * provides details of the customer
     * for a given id
     * @param custid
     * @return string (json formatted)
     */
	function ajax_customer_details($custid)
	{
        $this->load->model('customer_model');
		$result = $this->customer_model->get_customer($custid);
		if (is_array($result) && count($result) > 0)
        {
            echo json_encode($result[0]);
		}
    }
	
	/*
     * provide the list of users
     * for a region id, country id, state id, location id
     * @param regId, cntryId, stId, locId
     * @return string (json formatted)
     */
	function user_level_details($regId, $cntryId, $stId, $locId)
	{
        $this->load->model('user_model');
		$result = $this->user_model->get_userslist($regId, $cntryId, $stId, $locId);
		
		if (is_array($result) && count($result) > 0)
        {
            echo json_encode($result);
		}
    }
	
	/*
	 *Set the Expected proposal date for the lead.
	 *@jobid
	 */
	public function set_proposal_date()
	{
		$updt_data = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;
		
		$timestamp = strtotime($updt_data['date']);
		
		if ($updt_data['date_type'] != 'start')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($updt_data['date_type'] == 'start')
			{
				$updt['proposal_expected_date'] = date('Y-m-d H:i:s', $timestamp);
				$updt_date = $this->welcome_model->update_row('leads', $updt, $updt_data['jobid']);
			}		
		}
		echo json_encode($data);
	}
	
	/*
     * adds an item to the lead based on the ajax request
     */
	function ajax_add_item()
	{
		$data = real_escape_array($this->input->post());
        $errors = '';
        if (trim($data['hours']) != '' && !is_numeric($data['hours']))
        {
			$errors[] = 'Hours can only be numeric values!';
		}
        if (trim($data['item_desc']) == '')
        {
            $errors[] = 'You must provide a description!';
        }
        if (trim($data['item_price']) != '' && !is_numeric($data['item_price']))
        {
			$errors[] = 'Price can only be numeric values!';
		}
        if (!preg_match('/^[0-9]+$/', $data['jobid']))
        {
			$errors[] = 'Lead ID must be numeric!';
		}
        
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
			$data['item_desc'] = @str_replace('\r\n','',$data['item_desc']); 
			$this->quote_add_item($data['jobid'], $data['item_desc'], $data['item_price'], $data['hours']);			
		}
		
	}
	
	/**
	 * Add an item to a quotation (job)
	 * on the system
	 * Accepts direct ajax call as well as calls from other methods
	 */
	function quote_add_item($jobid, $item_desc = '', $item_price = 0, $hours, $ajax = TRUE) {
	
        $ins['item_desc'] = $item_desc;
        $ins['jobid_fk'] = $jobid;
		if(empty($hours)) {
			$ins['hours'] = '0.00';
		} else {
			$ins['hours'] = $hours;
		}
        if(empty($item_price)) {
			$ins['item_price']='0.00';
		} else {
			$ins['item_price'] = $item_price;
		}
        
        if (is_numeric(trim($hours)))
        {
            $ins['hours'] = $hours;
            $ins['item_price'] = $_POST['item_price'] * $hours;
        }
        
		$posn = $this->welcome_model->get_item_position($jobid);
        
        $ins['item_position'] = $posn[0]['item_position']+1;
        
		$insert_item = $this->welcome_model->insert_row_return_id('items', $ins);

        if ($insert_item>0)
        {
            $itemid = $insert_item;
            
            if ($ajax == TRUE)
            {
                $this->ajax_quote_items($ins['jobid_fk'], $itemid);
            }
            else
            {
                return TRUE;
            }
        }
        else
        {
            if ($ajax == TRUE)
            {
                echo "{error:true, errormsg:'Data insert failed!'}";
            }
            else
            {
                return FALSE;
            }
        }
    }
	
	/**
	 * Edits the basic quotation details (title, category etc)
	 * via an ajax request
	 */
	function ajax_edit_quote() {

		$data = real_escape_array($this->input->post());
		
        if (trim($data['job_title']) == '' || !preg_match('/^[0-9]+$/', trim($data['job_category']))) {
			echo "{error:true, errormsg:'Title and job category are required fields!'}";
		} else if ( !preg_match('/^[0-9]+$/', trim($data['jobid_edit'])) ) {
			echo "{error:true, errormsg:'quote ID must be numeric!'}";
		} else {
            $ins['job_title'] = $data['job_title'];
			$ins['division'] = $data['job_division'];
			$ins['job_category'] = $data['job_category'];
			$ins['lead_source'] = $data['lead_source_edit'];
			$ins['expect_worth_id'] = $data['expect_worth_edit'];
			$ins['expect_worth_amount'] = $data['expect_worth_amount'];
			$ins['actual_worth_amount'] = $data['actual_worth'];
			if (empty($data['actual_worth'])) {
				$ins['actual_worth_amount'] = 0.00;
			}
			if($data['actual_worth'] != $data['expect_worth_amount_dup']) {			
				$ins['proposal_adjusted_date'] = date('Y-m-d H:i:s');
			}
		
			if($data['lead_assign_edit_hidden'] == null || $data['lead_assign_edit_hidden'] == 0) {
				$ins['lead_assign'] = $data['lead_assign_edit'];
			} else {
				$ins['lead_assign'] = $data['lead_assign_edit_hidden'];
			}
			
			// for lead status history - starts here
			if($_POST['lead_status'] != $_POST['lead_status_hidden']) {
				$lead_stat_hist['jobid'] = $_POST['jobid_edit'];
				$lead_stat_hist['dateofchange'] = date('Y-m-d H:i:s');
				$lead_stat_hist['changed_status'] = $_POST['lead_status'];
				$lead_stat_hist['modified_by'] = $this->userdata['userid'];
				$insert_lead_stat_his = $this->welcome_model->insert_row('lead_status_history', $lead_stat_hist);
			}
			// for lead status history - ends here	
			
			/* lead owner starts here */
			if($data['lead_owner_edit_hidden'] == null || $data['lead_owner_edit_hidden'] == 0) {
				$ins['belong_to'] = $data['lead_owner_edit'];
			} else {
				$ins['belong_to'] = $data['lead_owner_edit_hidden'];
			}
			/*lead owner ends  here*/
			$ins['lead_indicator'] = $data['lead_indicator'];
			$ins['lead_status'] = $data['lead_status'];
			if($data['job_status'] != '' && $data['job_status'] != 'null')
			$ins['job_status']  = $data['job_status'];			
			$ins['lead_hold_reason'] = $data['reason'];
			$ins['date_modified'] = date('Y-m-d H:i:s');
			$ins['modified_by'] = $this->userdata['userid'];
			/* belong to assigned editing the lead owner */

			/* for onhold reason insert */	
			$inse['log_content'] = "Lead Onhold Reason: "; 
			$inse['log_content'] .= $data['reason'];
            $inse['jobid_fk'] = $data['jobid_edit'];
            $inse['userid_fk'] = $this->userdata['userid'];
			if($data['reason'] != '' && $data['reason'] != 'null')
			$insert_log = $this->welcome_model->insert_row('logs', $inse);
			/* end of onhold reason insert */
		
			/* for proposal adjust date insert */
			$ins_ad['log_content'] = 'Actual Worth Amount Modified On :' . ' ' . date('M j, Y g:i A'); 
			$ins_ad['jobid_fk'] = $data['jobid_edit'];
			$ins_ad['userid_fk'] = $this->userdata['userid'];
			if($data['actual_worth'] != $data['expect_worth_amount_dup']) {
				$insert_log = $this->welcome_model->insert_row('logs', $ins_ad);
			}
			/* end proposal adjust date insert */
			$jobid = $data['jobid_edit'];
			
			$updt_job = $this->welcome_model->update_row('leads', $ins, $data['jobid_edit']);
			if ($updt_job)
			{				
				$his['lead_status'] = $data['lead_status']; //lead_stage_history - lead_status update
				
				$updt_lead_stage_his = $this->welcome_model->update_row('lead_stage_history', $his, $jobid);
				
				if(($data['lead_assign_edit_hidden'] ==  $data['lead_assign_edit'])) 
				{
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk'] = $jobid;
					
					$lead_det = $this->welcome_model->get_lead_det($jobid); //after update.
					$lead_assign_mail = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
					$lead_owner = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
					
					$inserts['userid_fk'] = $this->userdata['userid'];
					$inserts['jobid_fk'] = $jobid;
					$inserts['date_created'] = date('Y-m-d H:i:s');
					$inserts['log_content'] = "Lead has been Re-assigned to: " . $lead_assign_mail[0]['first_name'] .' '.$lead_assign_mail[0]['last_name'] .'<br />'. 'For Lead .' .word_limiter($lead_det['job_title'], 4). ' ';
					
					// inset the new log
					$insert_log = $this->welcome_model->insert_row('logs', $inserts);
					
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail = implode(',',$mangement_email);
					$admin_mail=implode(',',$arrSetEmails);

					//email sent by email template
					$param = array();
					
					$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'log_content'=>$inserts['log_content'],'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $mgmt_mail.','.$lead_assign_mail[0]['email'].','.$lead_owner[0]['email'];
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $this->userdata['email'];
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "Lead Re-assignment Notification";
					$param['subject'] = 'Lead Re-assigned Notification';

					$this->email_template_model->sent_email($param);

				} /* lead owner edit mail notifiction starts here */
				else if(($data['lead_owner_edit_hidden'] ==  $data['lead_owner_edit'])) 
				{
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk'] = $jobid;
					
					$lead_det = $this->welcome_model->get_lead_det($jobid); //after update.
					$lead_assign_mail = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
					$lead_owner = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
					
					$inserts['userid_fk'] = $this->userdata['userid'];
					$inserts['jobid_fk'] = $jobid;
					$inserts['date_created'] = date('Y-m-d H:i:s');
					$inserts['log_content'] = "Lead Owner has been Re-assigned to: " . $lead_owner[0]['first_name'] .' '.$lead_owner[0]['last_name'] .'<br />'. 'For Lead ' .word_limiter($lead_det['job_title'], 4). ' ';
					// insert the new log
					$insert_log = $this->welcome_model->insert_row('logs', $inserts);
					
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

					$from=$this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail = implode(',',$mangement_email);
					$admin_mail=implode(',',$arrSetEmails);
					
					//email sent by email template
					$param = array();
					
					$param['email_data'] = array('print_fancydate'=>$print_fancydate,'user_name'=>$user_name,'log_content'=>$inserts['log_content'],'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $mgmt_mail.','. $lead_owner[0]['email'];
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $this->userdata['email'];
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "Lead Owner Re-assignment Notification";
					$param['subject'] = 'Lead Owner Re-assigned Notification';

					$this->email_template_model->sent_email($param);
				}
				/* lead owener eidt mail notification ends here */

                $json['error'] = false;
                $json['job_title'] = htmlentities($data['job_title'], ENT_QUOTES);
                $json['job_category'] = $data['job_category'];
				
				$this->session->set_flashdata('header_messages', array("Details Updated Successfully."));
				
				echo json_encode($json);
			}
			else
			{
				$json['error'] = true;
				$json['errormsg'] = 'Data update failed!';
				echo json_encode($json);
			}            
		}
	}
	
	/*
     * Update the quote to a given status
     * @access public
     * @param jobid
     * @param status => desired status
     * @return echo json string
     */
    public function ajax_update_quote($jobid = 0, $status, $log_status = '')
    {
		$this->load->model('user_model');	
		$res = array();
        if ($jobid != 0 && preg_match('/^[0-9]+$/', $jobid) && preg_match('/^[0-9]+$/', $status) && $the_job = $this->welcome_model->get_lead_all_detail($jobid))
        {
			if($status>0) {
				//Lead Status History - Start here
				$lead_det = $this->welcome_model->get_lead_det($jobid);
				$lead_his['jobid'] = $jobid;
				$lead_his['dateofchange'] = date('Y-m-d H:i:s');
				$lead_his['previous_status'] = $lead_det['job_status'];
				$lead_his['changed_status'] = $status;
				$lead_his['lead_status'] = $lead_det['lead_status'];
				$lead_his['modified_by'] = $this->userdata['userid'];
				//Lead Status History - End here
				
				//get the actual worth amt for the lead
				$actWorthAmt = $lead_det['actual_worth_amount']; 
							
				$update['job_status'] = $status;
					
				$updt_lead_stg = $this->welcome_model->updt_lead_stg_status($jobid, $update);
				if ($updt_lead_stg) 
				{
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk'] = $jobid;
					
					$disarray = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
					
					$lead_owner = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
					// print_r($lead_owner);exit;
					
					$ins['date_created'] = date('Y-m-d H:i:s');
					
					$status_res = $this->welcome_model->get_lead_stg_name($status);
					$ins['log_content'] = "Status Changed to:" .' '. urldecode($status_res['lead_stage_name']) .' ' . 'Sucessfully for the Lead - ' .word_limiter($lead_det['job_title'], 4). ' ';
					
					$ins_email['log_content_email'] = "Status Changed to:" .' '. urldecode($status_res['lead_stage_name']) .' ' . 'Sucessfully for the Lead - <a href='.$this->config->item('base_url').'welcome/view_quote/'.$jobid.'>' .word_limiter($lead_det['job_title'], 4). ' </a>';
					
					// insert the new log
					$insert_log = $this->welcome_model->insert_row('logs', $ins);
					// insert the lead stage history
					$insert_lead_stage_his = $this->welcome_model->insert_row('lead_stage_history', $lead_his);
					
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					

					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					
					$admin_mail=implode(',',$arrSetEmails);
					
					//email sent by email template
					$param = array();

					$param['email_data'] = array('user_name'=>$user_name, 'print_fancydate'=>$print_fancydate, 'log_content_email'=>$ins_email['log_content_email'], 'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $disarray[0]['email'] .','. $lead_owner[0]['email'];
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $user_data[0]['email'];
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "Lead - Status Change Notification";
					$param['subject'] = "Lead - Status Change Notification";

					$this->email_template_model->sent_email($param);

					$res['error'] = false;
				}
				else 
				{
					$res['error'] = true;
					$res['errormsg'] = 'Database update failed!';
				}	
			} 
			else 
			{
				$res['error'] = false;
			}
        }
		else 
		{
			$res['error'] = true;
			$res['errormsg'] = 'Invalid Lead ID or Stage!';
        }
		echo json_encode($res);
		exit;
    }
	
	/**
	 * Edits an existing item on a lead 
	 */
	function ajax_edit_item() {
		
		$data = real_escape_array($this->input->post());
		
        $errors = '';
        if (trim($data['item_desc']) == '')
        {
			$errors[] = 'You must provide a description!';
        }
        if (trim($data['item_price']) != '' && !is_numeric($data['item_price']))
        {
			$errors[] = 'Price can only be numeric values!';
		}
        if (!preg_match('/^[0-9]+$/', $data['itemid']))
        {
			$errors[] = 'item ID must be numeric!';
		}
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
			$ins['item_desc'] = $data['item_desc'];
			$ins['item_price'] = $data['item_price'];
			
			$updt_item = $this->welcome_model->update_row_item('items', $ins, $data['itemid']);
			$res = array();
			if ($updt_item)
			{
				$res['error'] = false;
			}
			else
			{
				$res['error'] = true;
			}
			echo json_encode($res);
			exit;
        }
    }
	
	 /*
     * deletes the given item from a lead
     * @return echo json string
     */
    function ajax_delete_item() {
		
		$data = real_escape_array($this->input->post());
        $errors = '';
        if (!isset($data['itemid']) || !preg_match('/^[0-9]+$/', $data['itemid']))
        {
            $errors[] = 'A valid item ID is not supplied';
        }
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
            $this->db->where('itemid', $data['itemid']);
            $this->db->select('jobid_fk');
            $q = $this->db->get($this->cfg['dbpref'] . 'items');
            if ($q->num_rows() > 0)
            {
                $jobid = $q->result_array();
                $this->db->where('itemid', $data['itemid']);
                if ( $this->db->delete($this->cfg['dbpref'] . 'items') )
                {
                    $this->ajax_quote_items($jobid[0]['jobid_fk']);
                }
                else
                {
                    $json['error'] = true;
                    $json['errormsg'] = 'Database error! Item not deleted.';
                    echo json_encode($json);
                }
            }
            else
            {
                $json['error'] = true;
                $json['errormsg'] = 'Specified item ID does not exist';
                echo json_encode($json);
            }
        }
    }
	
	 /*
     * saves the new positions items
     * for a given lead
     */
    function ajax_save_item_order() 
	{
		// $data = real_escape_array($this->input->post());
		$data = $_POST;
		
        $errors = '';
        if (!isset($data['qi']) || !is_array($data['qi']))
        {
            $errors[] = 'Incorrect order format!';
        }
        
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
            $when = '';
            foreach ($data['qi'] as $k => $v)
            {
                $when .= "WHEN {$v} THEN {$k} \n";
            }
            $sql = "UPDATE {$this->cfg['dbpref']}items SET `item_position` = CASE `itemid`
                    {$when}
                    ELSE `item_position` END";
            
            if ($this->db->query($sql))
            {	
                $json['error'] = false;
                echo json_encode($json);
            }
            else
            {
                $json['error'] = true;
                $json['errormsg'] = 'Database error occured!';
                echo json_encode($json);
            }
        }
    }
	
	/**
	 *uploading files - creating log
	 */
	public function lead_fileupload_details($jobid, $filename, $userid) {
	   
		$lead_files['lead_files_name'] = $filename;
		$lead_files['lead_files_created_by'] = $userid;
		$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
		$lead_files['jobid'] = $jobid;
		$insert_logs = $this->welcome_model->insert_row('lead_files', $lead_files);
		
		$logs['jobid_fk'] = $jobid;
		$logs['userid_fk'] = $this->userdata['userid'];
		$logs['date_created'] = date('Y-m-d H:i:s');
		$logs['log_content'] = $filename.' is added.';
		$logs['attached_docs'] = $filename;
		$insert_logs = $this->welcome_model->insert_row('logs', $logs);
	}
	
	/**
	 * Deletes lead from the list
	 */
	function delete_quote($id) {

		if ($this->session->userdata('delete')==1) {
			if ($id > 0) {
			
				$lead_det = $this->welcome_model->get_lead_det($id);
				$lead_assign_mail = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
				$lead_owner = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
				
				$delete_job = $this->welcome_model->delete_lead('leads', $id);
				if ($delete_job) 
				{
					$delete_item = $this->welcome_model->delete_row('items', 'jobid_fk', $id);
					$delete_log = $this->welcome_model->delete_row('logs', 'jobid_fk', $id);
					$delete_task = $this->welcome_model->delete_row('tasks', 'jobid_fk', $id);
					$delete_file = $this->welcome_model->delete_row('lead_files', 'jobid', $id);
					$delete_query = $this->welcome_model->delete_row('lead_query', 'job_id', $id);
					
					# Lead Delete Mail Notification
					$ins['log_content'] = 'Lead Deleted Sucessfully - Lead ' .word_limiter($lead_det['job_title'], 4). ' ';

					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$from=$this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$mangement_email = $arrEmails['management_emails'];
					$mgmt_mail = implode(',',$mangement_email);
					$admin_mail=implode(',',$arrSetEmails);
					
					//email sent by email template
					$param = array();
					
					$param['email_data'] = array('user_name'=>$user_name, 'print_fancydate'=>$print_fancydate, 'log_content'=>$ins['log_content'], 'signature'=>$this->userdata['signature']);

					$param['to_mail'] = $mgmt_mail.','.$lead_assign_mail[0]['email'].','.$lead_owner[0]['email'];
					$param['bcc_mail'] = $admin_mail;
					$param['from_email'] = $this->userdata['email'];
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "Lead - Delete Notification Message";
					$param['subject'] = "Lead Delete Notification";

					$this->email_template_model->sent_email($param);
					
					$this->session->set_flashdata('confirm', array("Item deleted from the system"));

					redirect('welcome/quotation');
				}
				else 
				{
					$this->session->set_flashdata('login_errors', array("Error in Deletion."));
					redirect('welcome/quotation');
				}
			}
			else 
			{
				$this->session->set_flashdata('login_errors', array("Quote does not exist or you may not be authorised to delete quotes."));
				redirect('welcome/quotation');
			}
		} 
		else 
		{
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('welcome/quotation');
		}
		
	}
	
	//Closed lead - move to project
	public function ajax_update_lead_status($jobid) 
	{
        if ($jobid != 0 && preg_match('/^[0-9]+$/', $jobid))
        {
			$update['pjt_status'] = 1;
			$update['modified_by'] = $this->userdata['userid'];
			$update['date_modified'] = date('Y-m-d H:i:s');
			
			$updt_job = $this->welcome_model->update_row('leads', $update, $jobid);
			$json = array();
			if ($updt_job) 
			{
				$lead_det = $this->welcome_model->get_lead_det($jobid);
				$ins['userid_fk'] = $this->userdata['userid'];
				$ins['jobid_fk'] = $jobid;
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = 'The Lead "'.word_limiter($lead_det['job_title'], 4).'" is Successfully Moved to Project.';
				$ins_email['log_content_email'] = 'The Lead <a href='.$this->config->item('base_url').'project/view_project/'.$jobid.'> ' .word_limiter($lead_det['job_title'], 4).' </a> is Successfully Moved to Project.';

				$lead_assign_mail = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
				$lead_owner = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
				
				// insert the new log
				$insert_log = $this->welcome_model->insert_row('logs', $ins);
				
				$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				

				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				
				$admin_mail=implode(',',$arrSetEmails);
			
				//email sent by email template
				$param = array();
				
				$param['email_data'] = array('user_name'=>$user_name, 'print_fancydate'=>$print_fancydate, 'log_content_email'=>$ins_email['log_content_email'], 'signature'=>$this->userdata['signature']);

				$param['to_mail'] = $lead_assign_mail[0]['email'] .','. $lead_owner[0]['email'];
				$param['bcc_mail'] = $admin_mail;
				$param['from_email'] = $this->userdata['email'];
				$param['from_email_name'] = $user_name;
				$param['template_name'] = "Lead to Project Change Notification";
				$param['subject'] = "Lead to Project Change Notification";

				$this->email_template_model->sent_email($param);
				
				$json['error'] = false;
				echo json_encode($json);
			}
			else
			{
				$json['error'] = true;
				$json['errormsg'] = 'Database update failed!';
				echo json_encode($json);
			}	
        }
        else
		{
			$json['error'] = true;
			$json['errormsg'] = 'Invalid Lead ID or Stage!';
			echo json_encode($json);
		}
    }
	
	/*
	 *Exporting data(leads) to the excel
	 */
	public function excelExport() {
		
		$stage='null';
		$customer='null';
		$worth='null';
		$owner='null';
		$leadassignee='null';
		$regionname='null';
		$countryname='null';
		$statename='null';
		$locname='null';
		$lead_status='null';
		$keyword='null';

		$exporttoexcel = $this->session->userdata['excel_download'];

		if (count($exporttoexcel)>0) {

			$stage = $exporttoexcel['stage'];
			$customer=$exporttoexcel['customer'];
			$worth=$exporttoexcel['worth'];
			$owner=$exporttoexcel['owner'];
			$leadassignee=$exporttoexcel['leadassignee'];
			$regionname=$exporttoexcel['regionname'];
			$countryname=$exporttoexcel['countryname'];
			$statename=$exporttoexcel['statename'];
			$locname=$exporttoexcel['locname'];
			$lead_status=$exporttoexcel['lead_status'];
			$keyword=$exporttoexcel['keyword'];
		}

		$filter_res = $this->welcome_model->get_filter_results($stage, $customer, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status, $keyword);

		//load our new PHPExcel library
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('DashBoard');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Lead No.');
		$this->excel->getActiveSheet()->setCellValue('B1', 'Lead Title');
		$this->excel->getActiveSheet()->setCellValue('C1', 'Customer');
		$this->excel->getActiveSheet()->setCellValue('D1', 'Region');
		$this->excel->getActiveSheet()->setCellValue('E1', 'Lead Owner');
		$this->excel->getActiveSheet()->setCellValue('F1', 'Lead Assigned To');
		$this->excel->getActiveSheet()->setCellValue('G1', 'Currency Type');
		$this->excel->getActiveSheet()->setCellValue('H1', 'Expected Worth');
		$this->excel->getActiveSheet()->setCellValue('I1', 'Lead Creation Date');
		$this->excel->getActiveSheet()->setCellValue('J1', 'Updated On');
		$this->excel->getActiveSheet()->setCellValue('K1', 'Updated By');
		$this->excel->getActiveSheet()->setCellValue('L1', 'Lead Stage');
		$this->excel->getActiveSheet()->setCellValue('M1', 'Expected Proposal Date');
		//$this->excel->getActiveSheet()->setCellValue('N1', 'Proposal Sent on');
		// $this->excel->getActiveSheet()->setCellValue('O1', 'Variance');
		$this->excel->getActiveSheet()->setCellValue('N1', 'Lead Indicator');
		$this->excel->getActiveSheet()->setCellValue('O1', 'Status');
		
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
		$i=2;
		foreach($filter_res as $excelarr) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $excelarr['invoice_no']);
			$this->excel->getActiveSheet()->setCellValue('B'.$i, $excelarr['job_title']);
			$this->excel->getActiveSheet()->setCellValue('C'.$i, $excelarr['first_name'].' '.$excelarr['last_name'].' - '.$excelarr['company']);
			$this->excel->getActiveSheet()->setCellValue('D'.$i, $excelarr['region_name']);
			$this->excel->getActiveSheet()->setCellValue('E'.$i, $excelarr['ubfn'].' '.$excelarr['ubln']);
			$this->excel->getActiveSheet()->setCellValue('F'.$i, $excelarr['ufname'].' '.$excelarr['ulname']);
			$this->excel->getActiveSheet()->setCellValue('G'.$i, $excelarr['expect_worth_name']);
			$this->excel->getActiveSheet()->setCellValue('H'.$i, $excelarr['expect_worth_amount']);
			//display only date
			$this->excel->getActiveSheet()->setCellValue('I'.$i, date('d-m-Y', strtotime($excelarr['date_created'])));
			$this->excel->getActiveSheet()->setCellValue('J'.$i, date('d-m-Y', strtotime($excelarr['date_modified'])));
			$this->excel->getActiveSheet()->setCellValue('K'.$i, $excelarr['usfname'].' '.$excelarr['uslname']);
			$this->excel->getActiveSheet()->setCellValue('L'.$i, $excelarr['lead_stage_name']);
			if($excelarr['proposal_expected_date'] != null) {
				$this->excel->getActiveSheet()->setCellValue('M'.$i, date('d-m-Y', strtotime($excelarr['proposal_expected_date'])));
			}		
			
			$this->excel->getActiveSheet()->setCellValue('N'.$i, $excelarr['lead_indicator']);
			
			switch ($excelarr['lead_status']) {
				case 1:
					$status = 'Active';
				break;
				case 2:
					$status = 'On Hold';
				break;
				case 3:
					$status = 'Dropped';
				break;
				case 4:
					$status = 'Closed';
				break;
			}
			
			$this->excel->getActiveSheet()->setCellValue('O'.$i, $status);

			$i++;
		}
		
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);
			//merge cell A1 until D1
			//$this->excel->getActiveSheet()->mergeCells('A1:D1');
			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$filename='Lead Dashboard.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
						 
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
	}
	
	/**
	 * Adds a log to a job
	 * based on post data
	 *
	 */
	function add_log()
	{
		$data_log = real_escape_array($this->input->post());
		$res = array();
		$json = array();
        if (isset($data_log['jobid']) && isset($data_log['userid']) && isset($data_log['log_content'])) {
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			$job_details = $this->welcome_model->get_lead_det($data_log['jobid']);
            
            if (count($job_details) > 0) 
            {
				$user_data = $this->welcome_model->get_user_data_by_id($data_log['userid']);

				$client = $this->welcome_model->get_client_data_by_id($job_details['custid_fk']);
				
                $this->load->helper('url');
				
				$emails = trim($data_log['emailto'], ':');
				
				$successful = $received_by = '';
				
				if ($emails != '' || isset($data_log['email_to_customer']))
				{
					$emails = explode(':', $emails);
					$mail_id = array();
					foreach ($emails as $mail)
					{
						$mail_id[] = str_replace('email-log-', '', $mail);
					}

					$data['user_accounts'] = array();
					$this->db->where_in('userid', $mail_id);
					$users = $this->db->get($this->cfg['dbpref'] . 'users');
					
					if ($users->num_rows() > 0)
					{
						$data['user_accounts'] = $users->result_array();
					}
					foreach ($data['user_accounts'] as $ua)
					{
						# default email
						$to_user_email = $ua['email'];
						
						if (strstr($ua['add_email'], '@') && ! (isset($data_log['email_to_customer']) && isset($data_log['client_email_address']) && isset($data_log['client_full_name'])))
						{
							
							if ($ua['use_both_emails'] == 1)
							{
								$to_user_email = $ua['add_email'];
							}
							else if ($ua['use_both_emails'] == 2)
							{
								$send_to[]= array($ua['add_email'], $ua['first_name'] . ' ' . $ua['last_name'],'');
							}
						}

						$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name'],'');
						
						$received_by .= $ua['first_name'] . ' ' . $ua['last_name'] . ', ';
					}
					$successful = 'This log has been emailed to:<br />';
					
					$log_subject = "eSmart Notification - {$job_details['job_title']} [ref#{$job_details['jobid']}] {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}";
							
					$param['email_data'] = array('first_name'=>$client[0]['first_name'],'last_name'=>$client[0]['last_name'],'print_fancydate'=>$print_fancydate,'log_content'=>$data_log['log_content'],'received_by'=>$received_by,'signature'=>$this->userdata['signature']);

					$json['debug_info'] = '0';
					
					if (isset($data_log['email_to_customer']) && isset($data_log['client_email_address']) && isset($data_log['client_full_name']))
					{
						// we're emailing the client, so remove the VCS log  prefix
						$log_subject = preg_replace('/^eSmart Notification \- /', '', $log_subject);
						
						for ($cei = 1; $cei < 5; $cei ++)
						{
							if (isset($data_log['client_emails_' . $cei]))
							{
								$send_to[] = array($data_log['client_emails_' . $cei], '');
								$received_by .= $data_log['client_emails_' . $cei] . ', ';
							}
						}
						
						if (isset($data_log['additional_client_emails']) && trim($data_log['additional_client_emails']) != '')
						{
							$additional_client_emails = explode(',', trim($data_log['additional_client_emails'], ' ,'));
							if (is_array($additional_client_emails)) foreach ($additional_client_emails as $aces)
							{
								$aces = trim($aces);
								if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $aces))
								{
									$send_to[] = array($aces, '');
									$received_by .= $aces . ', ';
								}
							}
						}					
					}
					else
					{
						$dis['date_created'] = date('Y-m-d H:i:s');
						$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

						$param['email_data'] = array('first_name'=>$client[0]['first_name'],'last_name'=>$client[0]['last_name'],'print_fancydate'=>$print_fancydate,'log_content'=>$data_log['log_content'],'received_by'=>$received_by,'signature'=>$this->userdata['signature']);

					}

					foreach($send_to as $recps) 
					{
						$arrRecs[]=$recps[0];
					}
					$senders=implode(',',$arrRecs);
					
					$param['to_mail'] = $senders;
					$param['from_email'] = $user_data[0]['email'];
					$param['from_email_name'] = $user_data[0]['first_name'];
					$param['template_name'] = "Lead Notificatiion Message";
					$param['subject'] = $log_subject;
					
					
					if($this->email_template_model->sent_email($param))
					{
						$successful .= trim($received_by, ', ');
					}
					else
					{
						echo 'failure';
					}
					

					if (isset($full_file_path) && is_file($full_file_path)) unlink ($full_file_path);
					
					if ($successful == 'This log has been emailed to:<br />')
					{
						$successful = '';
					}
					else
					{
						$successful = '<br /><br />' . $successful;
					}
				}
			
				$ins['jobid_fk'] = $data_log['jobid'];
				
				// use this to update the view status
				$ins['userid_fk'] = $upd['log_view_status'] = $data_log['userid'];
				
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = $data_log['log_content'] . $successful;
				
				$stick_class = '';
				if (isset($data_log['log_stickie']))
				{
					$ins['stickie'] = 1;
					$stick_class = ' stickie';
				}
				
				if (isset($data_log['time_spent']))
				{
					$ins['time_spent'] = (int) $data_log['time_spent'];
				}
				
				// inset the new log
				$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
				
				// update the leads table
				$this->db->where('jobid', $ins['jobid_fk']);
				$this->db->update($this->cfg['dbpref'] . 'leads', $upd);
                
                $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $data_log['log_content'])))), 'url', TRUE)) . $successful;
                
				$fancy_date = date('l, jS F y h:iA', strtotime($ins['date_created']));
				
$table = <<<HDOC
<tr id="log" class="log{$stick_class}">
<td id="log" class="log">
<p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</td>
</tr>
HDOC;
				
                $json['error'] = FALSE;
                $json['html'] = $table;			
				
                echo json_encode($json);
				exit;
            }
            else
            {
				$res['error'] = true;
				$res['errormsg'] = 'Post insert failed';
            }
        }
        else
        {
            // echo "{error:true, errormsg:'Invalid data supplied'}";
			$res['error'] = true;
			$res['errormsg'] = 'Invalid data supplied';
			
        }
		exit;
    }
	
	
	
	function request()
	{
		$data['results'] = array();
		if (isset($_POST['keyword']) && trim($_POST['keyword']) != '' && ($_POST['keyword'] != 'Lead No, Job Title, Name or Company'))
		{	
			$keyword = mysql_real_escape_string($_POST['keyword']);
			
					
			$sql = "SELECT * FROM `crms_customers`, `crms_leads` a WHERE `custid_fk` = `custid` AND job_status IN (1,4,2,5,3,7,6,9,10,11,12,13) AND ( `job_title` LIKE '%{$keyword}%' OR `invoice_no` LIKE '%{$keyword}%' OR `custid_fk` IN ( SELECT `custid` FROM `crms_customers` WHERE CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%{$keyword}%' OR `first_name` LIKE '%{$keyword}%' OR `last_name` LIKE '%{$keyword}%' OR `company` LIKE '%{$keyword}%' ) ) ORDER BY `job_status`, `job_title`";
			
			$resul = $this->welcome_model->search_res($keyword);
					
			$q = $this->db->query($sql);
			//echo $this->db->last_query();
			if ($q->num_rows() > 0)
			{				
				$result = $q->result_array();
				$i = 0;
				foreach ($this->cfg['job_status'] as $k => $v)
				{
					while (isset($result[$i]) && $k == $result[$i]['job_status'])
					{
						$data['results'][$k][] = $result[$i];
						$i++;
					}
				}
				
				if (count($result) == 1)
				{
					$this->session->set_flashdata('header_messages', array('Only one result found! You have been redirect to the job.'));
					
					//$status_type = (in_array($result[0]['job_status'], array(4,5,6,7,8,25))) ? 'invoice' : 'welcome';
					//$status_type = (in_array($result[0]['job_status'])) ? 'invoice' : 'welcome';
					
					//redirect($status_type . '/view_quote/' . $result[0]['jobid']);
					redirect('welcome/view_quote/' . $result[0]['jobid'] . '/draft');
				}
				else 
				{	//echo "tljlj";
					$this->session->set_flashdata('header_messages', array('Results found! You have been redirect to the job.'));
					redirect('welcome/view_quote/' . $result[0]['jobid'] . '/draft');
				}
			  
		    }
			else {
				$this->session->set_flashdata('header_messages', array('No record found!'));
				redirect('welcome/view_quote/' . $_POST['quoteid'] . '/draft');
			}
		}
	}
	
	/*
	*Lead from eNoah Website
	*/
	public function add_lead() {
	    //Create Customer 		
		if(sizeof($_POST)==0){
		    echo 0;
			return false;
		}
		
		if(!empty($_POST['contact_us'])){		
			$ins_cus['first_name']     = $_POST['firstname']; 
			$ins_cus['last_name']      = $_POST['lastname']; 
			$ins_cus['company']        = $_POST['organization']; 
			$ins_cus['position_title'] = $_POST['title']; 
			$ins_cus['email_1']        = $_POST['email']; 
			$ins_cus['email_2']        = $_POST['businessemail']; 
			$ins_cus['phone_1']        = $_POST['phonenumber'];
			$ins_cus['add1_line1']     = $_POST['address'];
			$ins_cus['comments']       = $_POST['message'];		
		} else {		
			$ins_cus['first_name']    = $_POST['name']; 
			$ins_cus['email_1']       = $_POST['email']; 
			$ins_cus['company']       = $_POST['company']; 
			$ins_cus['comments']      = $_POST['content'];
	    }
		//insert customer and retrive last insert id
		$insert_id = $this->customer_model->get_customer_insert_id($ins_cus);
		//Create Jobs
		$ins['job_title']           = 'Ask the Expert';
		$ins['custid_fk']           = $insert_id;
		$ins['job_category']        = $_POST['job_category'];
		//$ins['lead_source']       = '';
		$ins['lead_assign']         = 59;
		$ins['expect_worth_id']     = 5;
		$ins['expect_worth_amount'] = '0.00';
		$ins['belong_to']           = 59; // lead owner
		// $ins['division']         = $_POST['job_division'];
		$ins['date_created']        = date('Y-m-d H:i:s');
		$ins['date_modified']       = date('Y-m-d H:i:s');
		$ins['job_status']          = 1;
		// $ins['lead_indicator']   = $_POST['lead_indicator'];
		$ins['created_by']          = 59;
		$ins['modified_by']         = 59;
		$ins['lead_status']         = 1;
		$new_job_id = $this->welcome_model->insert_job($ins);
		if (!empty($new_job_id)) {
			$invoice_no = (int) $new_job_id;
			$invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);
			$up_args = array('invoice_no' => $invoice_no);
			$this->welcome_model->update_job($insert_id, $up_args);
			$this->quote_add_item($insert_id, "\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:", 0, '', FALSE);
		}
		echo 1;
		exit;
	}
	
	//For Countries
	public function loadCountrys($region_id)
	{
	    $output = '';
		$data = $this->welcome_model->getcountry_list($region_id);
		if(!empty($data)) {
		foreach($data as $country) {
		    $output .= '<option value="'.$country['countryid'].'">'.$country['country_name'].'</option>';
		}
		} else {
			$output = '';
		}
		echo $output;exit;
	}
	
	//For States
	public function loadStates($cnt_id)
	{
	    $output = '';
		$data = $this->welcome_model->getstate_list($cnt_id);
		foreach($data as $st) {
		    $output .= '<option value="'.$st['stateid'].'">'.$st['state_name'].'</option>';
		}
		echo $output;
	}
	
	//For Locations
	public function loadLocns($loc_id)
	{
	    $output = '';
		$data = $this->welcome_model->getlocation_list($loc_id);
		//print_r($data);
		foreach($data as $st) {
		    $output .= '<option value="'.$st['locationid'].'">'.$st['location_name'].'</option>';
		}
		echo $output;
	}
	
}
?>