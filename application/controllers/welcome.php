<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() {
		parent::__construct();
		
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('welcome_model');
		$this->load->model('request_model');
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
		
		$data['lead_stage']   = $this->stg_name;
		$data['customers']    = $this->welcome_model->get_customers();
		$data['lead_owner']   = $this->welcome_model->get_users();
		$data['regions']      = $this->regionsettings_model->region_list();
		$data['saved_search'] = $this->welcome_model->get_saved_search($this->userdata['userid'], $search_for=1);
		// echo "<pre>"; print_r($data['saved_search']); exit;
		
		$this->load->view('leads/quotation_view', $data);
	}
	
	/*
	 * List all the Leads based on levels with advanced search filter.
	 */
	// public function advance_filter_search($stage='null', $customer='null', $worth='null', $owner='null', $leadassignee='null', $regionname='null',$countryname='null', $statename='null', $locname='null', $lead_status='null', $lead_indi='null', $keyword='null') 
	public function advance_filter_search($search_type = false, $search_id = false)
	{
		$filt = array();
		$stage	      ='null'; 
		$customer	  ='null'; 
		$worth		  ='null'; 
		$owner	 	  ='null'; 
		$leadassignee ='null'; 
		$regionname	  ='null';
		$countryname  ='null'; 
		$statename	  ='null'; 
		$locname	  ='null'; 
		$lead_status  ='null'; 
		$lead_indi    ='null'; 
		$keyword      ='null';
		
		if($search_type == 'search' && $search_id == false) {
			$filt = real_escape_array($this->input->post());
		} else if ($search_type == 'search' && is_numeric($search_id)) {
			$wh_condn = array('search_id'=>$search_id, 'search_for'=>1, 'user_id'=>$this->userdata['userid']);
			$get_rec  = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
			unset($get_rec['search_id']);
			unset($get_rec['search_for']);
			unset($get_rec['search_name']);
			unset($get_rec['user_id']);
			unset($get_rec['is_default']);
			if(!empty($get_rec))
			$filt	  = real_escape_array($get_rec);
		} else {
			$wh_condn = array('search_for'=>1, 'user_id'=>$this->userdata['userid'], 'is_default'=>1);
			$get_rec  = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
			unset($get_rec['search_id']);
			unset($get_rec['search_for']);
			unset($get_rec['search_name']);
			unset($get_rec['user_id']);
			unset($get_rec['is_default']);
			if(!empty($get_rec))
			$filt	  = real_escape_array($get_rec);
		}
		
		if (count($filt)>0) {
			$stage 		  = $filt['stage'];
			$customer 	  = $filt['customer'];
			$worth   	  = $filt['worth'];
			$owner 		  = $filt['owner'];
			$leadassignee = $filt['leadassignee'];
			$regionname   = $filt['regionname'];
			$countryname  = $filt['countryname'];
			$statename 	  = $filt['statename'];
			$locname 	  = $filt['locname'];
			$lead_status  = $filt['lead_status'];
			$lead_indi 	  = $filt['lead_indi'];
			$keyword 	  = !empty($filt['keyword']) ? $filt['keyword'] : 'null';
			$excel_arr 	  = array();
			foreach ($filt as $key => $val) {
				$excel_arr[$key] = $val;
			}
		//	echo '<pre>'; print_r($excel_arr); exit;
			$this->session->set_userdata($excel_arr);
		} else {
			$this->session->unset_userdata(array("excel_download"=>''));
		}
		// print_r($this->session->userdata('excel_download'); exit;

		$filter_results = $this->welcome_model->get_filter_results($stage, $customer, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status, $lead_indi, $keyword);
		// echo $this->db->last_query(); exit;
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
		$data['lead_indi']    = $lead_indi;
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

		$arrLeadInfo = $this->request_model->get_lead_info($id);
		
		if(!empty($getLeadDet)) {
            $data['quote_data'] = $getLeadDet[0];
            $data['view_quotation'] = true;
			$data['user_accounts'] = $this->welcome_model->get_users();
			
			$data['user_roles']		= $usid['role_id']; 
			$data['login_userid']		= $usid['userid']; 
			$data['project_belong_to']		= $arrLeadInfo['belong_to']; 
			$data['project_assigned_to']		= $arrLeadInfo['assigned_to']; 
			$data['project_lead_assign']		= $arrLeadInfo['lead_assign']; 

			if (!strstr($data['quote_data']['log_view_status'], $this->userdata['userid']))
			{
				$log_view_status['log_view_status'] = $data['quote_data']['log_view_status'] . ':' . $this->userdata['userid'];
				$logViewStatus = $this->welcome_model->updt_log_view_status($id, $log_view_status);
			}
            
			/**
			 * Get files associated with this job
			 **/
			$fcpath = UPLOAD_PATH; 
		    $f_dir = $fcpath . 'files/' . $id . '/'; 
			// $data['job_files_html'] = $this->welcome_model->get_job_files($f_dir, $fcpath, $data['quote_data']);
			$get_parent_folder_id = $this->request_model->getParentFfolderId($id,$parent=0);
			
			if(!empty($get_parent_folder_id)){
				$data['parent_ffolder_id'] = $get_parent_folder_id['folder_id'];
			} else {
			
				//creating files folder name
				$f_dir = UPLOAD_PATH.'files/';
				if (!is_dir($f_dir)) {
					mkdir($f_dir);
					chmod($f_dir, 0777);
				}
				
				//creating lead_id folder name
				$f_dir = $f_dir.$id;
				if (!is_dir($f_dir)) {
					mkdir($f_dir);
					chmod($f_dir, 0777);
				}
			
				$ins    = array('lead_id'=>$id,'folder_name'=>$id,'parent'=>0,'created_by'=>$this->userdata['userid']);
				$data['parent_ffolder_id'] = $this->request_model->get_id_by_insert_row('file_management', $ins);
				
			
				$project_members = $this->request_model->get_project_members($id); // This array to get a project normal members(Developers) details.
				$project_leaders = $this->request_model->get_project_leads($id); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
				$arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
				$arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
				$arrLeadInfo = $this->request_model->get_lead_info($id); // This function to get a current lead informations.		

				/* if(isset($arrProjectMembers) && !empty($arrProjectMembers)) { 

					foreach($arrProjectMembers as $members){
						
						$arrLeadExistFolderAccess= $this->request_model->check_lead_file_access_by_id($id, 'folder_id', $data['parent_ffolder_id'], $members['userid']);						
						
						if(empty($arrLeadExistFolderAccess)) {
						
								$read_access = 0;
								$write_access = 0;
								$delete_access = 0;									
								// Check this user is "Lead Owner", "Lead Assigned to", ""Project Manager"
								if($arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || $arrLeadInfo['lead_assign'] == $members['userid']) {
								$read_access = 1;
								$write_access = 1;
								$delete_access = 1;								
								}
								
								

							$folder_permissions_contents  = array('userid'=>$members['userid'],'lead_id'=>$id,'folder_id'=>$data['parent_ffolder_id'],'lead_file_access_read'=>$read_access,'lead_file_access_delete'=>$delete_access,'lead_file_access_write'=>$write_access,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>0);
							$insert_folder_permissions   = $this->request_model->insert_new_row('lead_file_access', $folder_permissions_contents); //Mani
							
						}							
					}
				} */
					
			}
			
			$data['query_files1_html'] = $this->welcome_model->get_query_files_list($id);

			/**
			 * Get URLs associated with this job
			 */
			$data['job_urls_html'] = $this->welcome_model->get_job_urls($id);
			
			//this code will be reuse for calculate the actual worth of project
			/*
			$actual_worths = $this->db->query("SELECT SUM(`".$this->cfg['dbpref']."items`.`item_price`) AS `project_cost`
								FROM `{$this->cfg['dbpref']}items`
								WHERE `jobid_fk` = '{$id}' GROUP BY jobid_fk");
			$data['actual_worth'] = $actual_worths->result_array();	
			echo "<pre>"; print_r($data['actual_worth']);
			*/

			$data['lead_stat_history'] = $this->welcome_model->get_lead_stat_history($id);
			
			$data['job_cate'] = $this->welcome_model->get_lead_services();
			
			$this->load->view('leads/welcome_view_quote', $data);
        }
		else 
		{
            // echo "Lead does not exist or you may not be authorised to view this";
			$this->session->set_flashdata('login_errors', array("Lead does not exist or you may not be authorised to view this."));
			redirect('welcome/quotation');
        }
    }
	
	/*
	*Get the logs
	*/
	function getLogs($id){
	
		$data['log_html'] = '';
		$getLogsData = $this->welcome_model->get_logs($id);
		// echo "<pre>"; print_r($getLogsData); exit;
		$data['log_html'] .= '<table width="100%" id="lead_log_list" class="log-container logstbl">';
		$data['log_html'] .= '<thead><tr><th>&nbsp;</th></tr></thead><tbody>';
		
		if (!empty($getLogsData)) {
			$log_data = $getLogsData;
			$this->load->helper('url');
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			foreach ($log_data as $ld) {
				// $wh_condn = array('userid'=>$ld['userid_fk']);
				$user_data = $this->welcome_model->get_user_data_by_id($ld['userid_fk']);
				
				if (count($user_data) < 1) {
					echo '<!-- ', print_r($ld, TRUE), ' -->'; 
					continue;
				}
				
				$log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
				
				$fancy_date = date('l, jS F y h:iA', strtotime($ld['date_created']));
				
				$stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';
				
				$table ='<tr id="log" class="log'.$stick_class.'"><td id="log" class="log'.$stick_class.'">
						 <p class="data log'.$stick_class.'"><span class="log'.$stick_class.'">'.$fancy_date.'</span>'.$user_data[0]['first_name'].' '.$user_data[0]['last_name'].'</p>
						 <p class="desc log'.$stick_class.'">'.$log_content.'</p></td></tr>';
				$data['log_html'] .= $table;
				unset($table, $user_data, $user, $log_content);
			}
		}
		$data['log_html'] .= '</tbody></table>';
		echo $data['log_html'];
	}
	
	 /*
     * provides the list of items
     * that belong to a given job
     * @param lead_id
     * @param itemid (latest intsert)
     * @return echo json string
     */
    function ajax_quote_items($lead_id = 0, $itemid = 0, $return = false) 
	{
		$this->load->helper('text');
		$this->load->helper('fix_text');
		
		$quote = $this->welcome_model->get_quote_items($lead_id);

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
				// $content_item = nl2br(cleanup_chars(ascii_to_entities($row['item_desc'])));

				if(!empty($row['item_price'])) {
					$html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" width="85%">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td><td width="14%" class="item-price width100px" align="right" valign="bottom">' . $row['item_price'] . '</td></tr></table></li>';
				} else {
					$html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" colspan="2">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td></tr></table></li>';
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
		$data['job_cate'] = $this->welcome_model->get_lead_services();
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
			$data['job_cate'] = $this->welcome_model->get_lead_services();
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
	
	function custom_update_users(){
		$res 	   = array();
		$post_data = real_escape_array($this->input->post());
		$res['result'] = 'error';
		if($post_data['project_lead_id']){
			// update project manager
			$this->db->update($this->cfg['dbpref']."leads",array("assigned_to" => $post_data['project_manager']),array("lead_id" => $post_data['project_lead_id']));
			$project_team_members = $this->input->post('project_team_members');
			$stake_members = $this->input->post('stake_members');
			
			//update project team members
			if(count($project_team_members) > 0)
			{
				$this->db->delete($this->cfg['dbpref']."contract_jobs",array("jobid_fk" => $post_data['project_lead_id']));
				foreach($project_team_members as $pusers){
					$this->db->insert($this->cfg['dbpref']."contract_jobs",array("jobid_fk" => $post_data['project_lead_id'],"userid_fk" => $pusers));
				}
			}
			
			//update project stake holders
			if(count($stake_members) > 0)
			{
				$this->db->delete($this->cfg['dbpref']."stake_holders",array("lead_id" => $post_data['project_lead_id']));
				foreach($stake_members as $susers){
					$this->db->insert($this->cfg['dbpref']."stake_holders",array("lead_id" => $post_data['project_lead_id'],"user_id" => $susers));
				}
			}
			$res['result'] = 'ok';			
		}
		echo json_encode($res);
	}
	
	/**
	 * Initiates and create the quote based on an ajax request
	 */
	function ajax_create_quote() {
	
		if (trim($this->input->post('lead_title')) == '' || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_service'))) || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_source'))) || !preg_match('/^[0-9]+$/', trim($this->input->post('lead_assign'))))
        {
			echo "{error:true, errormsg:'Title and Lead Service are required fields!'}";
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
			$ins['lead_title'] = $data['lead_title'];
			$ins['custid_fk'] = $data['custid_fk'];
			$ins['lead_service'] = $data['lead_service'];
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
			$ins['lead_stage'] = 1;
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
				$lead_hist['lead_id'] = $insert_id;
				$lead_hist['dateofchange'] = date('Y-m-d H:i:s');
				$lead_hist['previous_status'] = 1;
				$lead_hist['changed_status'] = 1;
				$lead_hist['lead_status'] = 1;
				$lead_hist['modified_by'] = $this->userdata['userid'];
				$insert_lead_stg_his = $this->welcome_model->insert_row('lead_stage_history', $lead_hist);
				
				//history - lead_status_history
				$lead_stat_hist['lead_id'] = $insert_id;
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
                $json['lead_title'] = htmlentities($data['lead_title'], ENT_QUOTES);
                $json['lead_service'] = $data['lead_service'];
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
	 *@lead_id
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
				$updt_date = $this->welcome_model->update_row('leads', $updt, $updt_data['lead_id']);
			}		
		}
		echo json_encode($data);
	}
	
	/*
	 *Change the Lead Creation for the lead.
	 *@lead_id
	 */
	public function set_lead_creation_date()
	{
		$updt_data = real_escape_array($this->input->post());
		
		$data['error'] = FALSE;
		
		$timestamp = strtotime($updt_data['date']);
		
		if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			$updt['date_created'] = date('Y-m-d H:i:s', $timestamp);
			$updt_date = $this->welcome_model->update_row('leads', $updt, $updt_data['lead_id']);
		}
		echo json_encode($data);
	}
	/*
     * adds an item to the lead based on the ajax request
     */
	function ajax_add_item()
	{
        $errors = '';
        if (trim($_POST['hours']) != '' && !is_numeric($_POST['hours']))
        {
			$errors[] = 'Hours can only be numeric values!';
		}
        if (trim($_POST['item_desc']) == '')
        {
            $errors[] = 'You must provide a description!';
        }
        if (trim($_POST['item_price']) != '' && !is_numeric($_POST['item_price']))
        {
			$errors[] = 'Price can only be numeric values!';
		}
        if (!preg_match('/^[0-9]+$/', $_POST['lead_id']))
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
			if (!preg_match('/^\n/', $_POST['item_desc']))
			{
				// $_POST['item_desc'] = "\n" . $_POST['item_desc'];
			}
			$this->quote_add_item($_POST['lead_id'], $_POST['item_desc'], $_POST['item_price'], $_POST['hours']);
			
		}
		
	}
	
	/**
	 * Add an item to a quotation (job)
	 * on the system
	 * Accepts direct ajax call as well as calls from other methods
	 */
	function quote_add_item($lead_id, $item_desc = '', $item_price = 0, $hours, $ajax = TRUE) {
		
		$ins['item_desc'] = $item_desc;
        $ins['jobid_fk'] = $lead_id;
		
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
        
		$posn = $this->welcome_model->get_item_position($lead_id);
        
        $ins['item_position'] = $posn[0]['item_position']+1;

		// $ins = real_escape_array($ins);
		// $ins['item_desc'] = @str_replace('\r\n', '', $ins['item_desc']);

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
	 * Edits the basic quotation details (title, services etc)
	 * via an ajax request
	 */
	function ajax_edit_quote() {

		$data = real_escape_array($this->input->post());
		
		// echo "<pre>"; print_r($data); exit;
		
        if (trim($data['lead_title']) == '' || !preg_match('/^[0-9]+$/', trim($data['lead_service']))) {
			echo "{error:true, errormsg:'Title and Lead Service are required fields!'}";
		} else if ( !preg_match('/^[0-9]+$/', trim($data['jobid_edit'])) ) {
			echo "{error:true, errormsg:'quote ID must be numeric!'}";
		} else {
            $ins['lead_title'] 			= $data['lead_title'];
			$ins['division']			= $data['job_division'];
			$ins['lead_service']		= $data['lead_service'];
			$ins['lead_source']			= $data['lead_source_edit'];
			$ins['expect_worth_id']		= $data['expect_worth_edit'];
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
			if($_POST['lead_status'] 			 != $_POST['lead_status_hidden']) {
				$lead_stat_hist['lead_id'] 		  = $_POST['jobid_edit'];
				$lead_stat_hist['dateofchange']   = date('Y-m-d H:i:s');
				$lead_stat_hist['changed_status'] = $_POST['lead_status'];
				$lead_stat_hist['modified_by']	  = $this->userdata['userid'];
				$insert_lead_stat_his 			  = $this->welcome_model->insert_row('lead_status_history', $lead_stat_hist);
			}
			// for lead status history - ends here	
			
			/* lead owner starts here */
			if($data['lead_owner_edit_hidden'] == null || $data['lead_owner_edit_hidden'] == 0) {
				$ins['belong_to'] = $data['lead_owner_edit'];
			} else {
				$ins['belong_to'] = $data['lead_owner_edit_hidden'];
			}
			/*lead owner ends  here*/
			$ins['lead_indicator']   = $data['lead_indicator'];
			$ins['lead_status'] 	 = $data['lead_status'];
			if($data['lead_stage']  != '' && $data['lead_stage'] != 'null')
			$ins['lead_stage'] 		 = $data['lead_stage'];			
			$ins['lead_hold_reason'] = $data['reason'];
			$ins['date_modified'] 	 = date('Y-m-d H:i:s');
			$ins['modified_by']		 = $this->userdata['userid'];
			/* belong to assigned editing the lead owner */

			/* for onhold reason insert */	
			$inse['log_content']  = "Lead Onhold Reason: "; 
			$inse['log_content'] .= $data['reason'];
            $inse['jobid_fk']     = $data['jobid_edit'];
            $inse['userid_fk']    = $this->userdata['userid'];
			if($data['reason']   != '' && $data['reason'] != 'null')
			$insert_log			  = $this->welcome_model->insert_row('logs', $inse);
			/* end of onhold reason insert */
		
			/* for proposal adjust date insert */
			$ins_ad['log_content'] = 'Actual Worth Amount Modified On :' . ' ' . date('M j, Y g:i A'); 
			$ins_ad['jobid_fk']    = $data['jobid_edit'];
			$ins_ad['userid_fk']   = $this->userdata['userid'];
			if($data['actual_worth'] != $data['expect_worth_amount_dup']) {
				$insert_log = $this->welcome_model->insert_row('logs', $ins_ad);
			}
			/* end proposal adjust date insert */
			$lead_id = $data['jobid_edit'];
			
			$updt_job = $this->welcome_model->update_row('leads', $ins, $data['jobid_edit']);
			
			if ($updt_job)
			{				
				$his['lead_status'] = $data['lead_status']; //lead_stage_history - lead_status update
				
				//update customer isclient
				if($data['lead_status'] == 4) {
					$update_cus = array('is_client'=>1);
					$updt_isclient = $this->welcome_model->updtCustomerIsClient($data['customer_id'], $update_cus);
				} else if($data['lead_status'] != 4 && $data['lead_status_hidden'] == 4) {
					$chk_isclient = $this->welcome_model->check_isclient_stat($data['customer_id']);
					if($chk_isclient==0) {
						$update_cus = array('is_client'=>0);
						$updt_isclient = $this->welcome_model->updtCustomerIsClient($data['customer_id'], $update_cus);
					}
				}
				
				$updt_lead_stage_his = $this->welcome_model->update_row('lead_stage_history', $his, $lead_id);
				
				if(($data['lead_assign_edit_hidden'] ==  $data['lead_assign_edit'])) 
				{
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk']  = $lead_id;
					
					$lead_det		  = $this->welcome_model->get_lead_det($lead_id); //after update.
					$lead_assign_mail = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
					$lead_owner       = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
					
					$inserts['userid_fk'] = $this->userdata['userid'];
					$inserts['jobid_fk']  = $lead_id;
					$inserts['date_created'] = date('Y-m-d H:i:s');
					$inserts['log_content']  = "Lead has been Re-assigned to: " . $lead_assign_mail[0]['first_name'] .' '.$lead_assign_mail[0]['last_name'] .'<br />'. 'For Lead .' .word_limiter($lead_det['lead_title'], 4). ' ';
					
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

					$param['to_mail']		  = $mgmt_mail.','.$lead_assign_mail[0]['email'].','.$lead_owner[0]['email'];
					$param['bcc_mail']		  = $admin_mail;
					$param['from_email']	  = $this->userdata['email'];
					$param['from_email_name'] = $user_name;
					$param['template_name']	  = "Lead Re-assignment Notification";
					$param['subject']		  = 'Lead Re-assigned Notification';

					$this->email_template_model->sent_email($param);

				} /* lead owner edit mail notifiction starts here */
				else if(($data['lead_owner_edit_hidden'] ==  $data['lead_owner_edit'])) 
				{
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk'] = $lead_id;
					
					$lead_det = $this->welcome_model->get_lead_det($lead_id); //after update.
					$lead_assign_mail = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
					$lead_owner = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
					
					$inserts['userid_fk'] = $this->userdata['userid'];
					$inserts['jobid_fk'] = $lead_id;
					$inserts['date_created'] = date('Y-m-d H:i:s');
					$inserts['log_content'] = "Lead Owner has been Re-assigned to: " . $lead_owner[0]['first_name'] .' '.$lead_owner[0]['last_name'] .'<br />'. 'For Lead ' .word_limiter($lead_det['lead_title'], 4). ' ';
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
                $json['lead_title'] = htmlentities($data['lead_title'], ENT_QUOTES);
                $json['lead_service'] = $data['lead_service'];
				
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
     * @param lead_id
     * @param status => desired status
     * @return echo json string
     */
    public function ajax_update_quote($lead_id = 0, $status, $log_status = '')
    {
		$this->load->model('user_model');	
		$res = array();
        if ($lead_id != 0 && preg_match('/^[0-9]+$/', $lead_id) && preg_match('/^[0-9]+$/', $status) && $the_job = $this->welcome_model->get_lead_all_detail($lead_id))
        {
			if($status>0) {
				//Lead Status History - Start here
				$lead_det = $this->welcome_model->get_lead_det($lead_id);
				$lead_his['lead_id'] = $lead_id;
				$lead_his['dateofchange'] = date('Y-m-d H:i:s');
				$lead_his['previous_status'] = $lead_det['lead_stage'];
				$lead_his['changed_status'] = $status;
				$lead_his['lead_status'] = $lead_det['lead_status'];
				$lead_his['modified_by'] = $this->userdata['userid'];
				//Lead Status History - End here
				
				//get the actual worth amt for the lead
				$actWorthAmt = $lead_det['actual_worth_amount']; 
							
				$update['lead_stage'] = $status;
					
				$updt_lead_stg = $this->welcome_model->updt_lead_stg_status($lead_id, $update);
				if ($updt_lead_stg) 
				{
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk'] = $lead_id;
					
					$disarray = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
					
					$lead_owner = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
					// print_r($lead_owner);exit;
					
					$ins['date_created'] = date('Y-m-d H:i:s');
					
					$status_res = $this->welcome_model->get_lead_stg_name($status);
					$ins['log_content'] = "Status Changed to:" .' '. urldecode($status_res['lead_stage_name']) .' ' . 'Sucessfully for the Lead - ' .word_limiter($lead_det['lead_title'], 4). ' ';
					
					$ins_email['log_content_email'] = "Status Changed to:" .' '. urldecode($status_res['lead_stage_name']) .' ' . 'Sucessfully for the Lead - <a href='.$this->config->item('base_url').'welcome/view_quote/'.$lead_id.'>' .word_limiter($lead_det['lead_title'], 4). ' </a>';
					
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
        if (trim($_POST['item_desc']) == '')
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
			$ins['item_desc'] = $_POST['item_desc'];
			$ins['item_price'] = $data['item_price'];
			// echo "<pre>"; print_r($ins); exit;
			
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
                $lead_id = $q->result_array();
                $this->db->where('itemid', $data['itemid']);
                if ( $this->db->delete($this->cfg['dbpref'] . 'items') )
                {
                    $this->ajax_quote_items($lead_id[0]['jobid_fk']);
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
	public function lead_fileupload_details($lead_id, $filename, $userid) {
	   
		$lead_files['lead_files_name'] = $filename;
		$lead_files['lead_files_created_by'] = $userid;
		$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
		$lead_files['lead_id'] = $lead_id;
		$insert_logs = $this->welcome_model->insert_row('lead_files', $lead_files);
		
		$logs['jobid_fk'] = $lead_id;
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
					$delete_file = $this->welcome_model->delete_row('lead_files', 'lead_id', $id);
					$delete_query = $this->welcome_model->delete_row('lead_query', 'lead_id', $id);
					
					# Lead Delete Mail Notification
					$ins['log_content'] = 'Lead Deleted Sucessfully - Lead ' .word_limiter($lead_det['lead_title'], 4). ' ';

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
					
					$this->session->set_flashdata('confirm', array("Lead deleted from the system"));

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
	
	public function update_project_info($project_id)
	{
		$res 	   = array();
		$post_data = real_escape_array($this->input->post());
		
		// echo "<pre>"; print_r($post_data); exit;
		
		// [project_name] => tetst lead ititle
		// [timesheet_project_types]=[project_type] => 1 //billing_type
		// [project_types] => 1
		// [department_id_fk] => 3
		// [cost_center_value] => 3|Cost Center
		// [project_center_value] => 1|BPO
		// [project_category] => 1
		// [sow_status] => 1
		// [resource_type] => 1
		
		$update['lead_title']    	= $post_data['project_name'];
		$update['project_type']  	= $post_data['timesheet_project_types'];
		$update['project_types']  	= $post_data['project_types'];
		$update['department_id_fk'] = $post_data['department_id_fk'];
		
		if($post_data['project_category'] == 1) {
			$project_center 		  = explode('|', $post_data['project_center_value']);
			$update['project_center'] = $project_center[0];
			$code 					  = substr($project_center[1], 0, 3);
		} else if($post_data['project_category'] == 2) {
			$cost_center 			  = explode('|', $post_data['cost_center_value']);
			$update['cost_center'] 	  = $cost_center[0];
			$code 					  = substr($cost_center[1], 0, 3);
		}
		
		$update['date_start']	       = date('Y-m-d H:i:s', strtotime($post_data['date_start']));
		$update['date_due']	           = date('Y-m-d H:i:s', strtotime($post_data['date_due']));
		$update['actual_worth_amount'] = $post_data['actual_worth_amount'];
		$update['project_category']    = $post_data['project_category'];
		$update['practice']    	       = $post_data['practice'];
		$update['rag_status']    	   = 3;
		$update['sow_status']	       = $post_data['sow_status'];
		$update['resource_type']	   = $post_data['resource_type'];
		$update['modified_by']         = $this->userdata['userid'];
		$update['date_modified']       = date('Y-m-d H:i:s');
		
		$updt_project_info = $this->welcome_model->update_row('leads', $update, $project_id);
		if($updt_project_info) {
			$res['result'] = 'ok';
		}else{
			$res['result'] = 'not updated';
		}
		echo json_encode($res);
		exit;
	}
	
	/* testing purpose used dont use in live
	function test_insert($project_id){
		$this->customer_model->create_cdefault_folders($project_id);
		$this->customer_model->assign_default_folders($project_id);
	} */
	
	/*
	*@confirm_project
	*set milestones
	*@param project_id
	*/
	public function confirm_project($project_id)
	{
		$res 	   = array();
		$ins 	   = array();
		
		$lead_det = $this->welcome_model->get_lead_det($project_id);
		
		if( ($lead_det['project_category'] == 0) || ($lead_det['department_id_fk'] == 0) || ($lead_det['resource_type'] == 0) || ($lead_det['project_type'] == 0) || ($lead_det['date_start'] == "") || ($lead_det['date_due'] == "") || ($lead_det['actual_worth_amount'] == '0.00') || ($lead_det['practice'] == "") ) {
		
			$res['error'] = true;
			$res['errortype'] = 1;
			if($lead_det['department_id_fk'] == 0) {
				$res['errormsg'][] = 'Department is Required! <br />';
			}
			if($lead_det['project_category'] == 0) {
				$res['errormsg'][] = 'Project Category is Required! <br />';
			}
			if($lead_det['resource_type'] == 0) {
				$res['errormsg'][] = 'Resource Type is Required! <br />';
			}
			if($lead_det['project_type'] == 0) {
				$res['errormsg'][] = 'Project billing type is Required! <br />';
			}
			if($lead_det['project_types'] == 0) {
				$res['errormsg'][] = 'Project type is Required! <br />';
			}
			if($lead_det['actual_worth_amount'] == '0.00') {
				$res['errormsg'][] = 'SOW Value should be greater than Zero! <br />';
			}
			if($lead_det['date_start'] == "") {
				$res['errormsg'][] = 'SOW Start Date is required! <br />';
			}
			if($lead_det['date_due'] == "") {
				$res['errormsg'][] = 'SOW End Date is required! <br />';
			}
			if($lead_det['practice'] == "") {
				$res['errormsg'][] = 'Practice is required!';
			}
			
			echo json_encode($res);
			exit;
			
		}

		$project_category_code = '';
		$post_data = real_escape_array($this->input->post());
		
		if(!empty($post_data)) {
			$currency_type = $post_data['currency_type'];
			foreach($post_data['project_milestone_name'] as $key=>$value) {
				$ins[$key]['jobid_fk']               = $project_id;
				$ins[$key]['project_milestone_name'] = $value;
				$ins[$key]['expected_date']          = date('Y-m-d', strtotime($post_data['expected_date'][$key]));
				$ins[$key]['month_year']             = date('Y-m-d', strtotime($post_data['month_year'][$key]));
				$ins[$key]['amount']                 = $post_data['amount'][$key];
			}
		}

		if(!empty($ins)) {
			foreach($ins as $row) {
				$log = array();
				$exp_res = '';
				if(!empty($row['project_milestone_name'])) {
					$exp_res = $this->welcome_model->insert_row("expected_payments", $row);
					
					if($exp_res)
					$res['result'] = 'ok';
					else
					$res['result'] = 'fail';
				
					$log_detail = 'Project Milestone Name: '.$row['project_milestone_name'].'  Amount: '.$currency_type.' '.$row['amount'].' Expected Date: '.date('Y-m-d', strtotime($row['expected_date']));
					$log['jobid_fk']      = $project_id;
					$log['userid_fk']     = $this->userdata['userid'];
					$log['date_created']  = date('Y-m-d H:i:s');
					$log['log_content']   = $log_detail;
					$log_res = $this->welcome_model->insert_row("logs", $log);

				}
			}
		}
		
		$customer = $this->customer_model->get_customer($lead_det['custid_fk']);
		
		$client_code = $customer[0]['client_code'];
		
		if($client_code == '') {
			$client_code = $this->customer_model->update_client_code($customer[0]['company'], $lead_det['custid_fk']);
		}
		
		//update client code to this lead - for project count
		$this->db->where('lead_id', $project_id);
		$this->db->update($this->cfg['dbpref'] . 'leads', array('client_code'=>$client_code));
		
		if( $lead_det['project_category'] !=0 ) {
			switch($lead_det['project_category']) {
				case 1:
					$pc_code = $this->welcome_model->get_data_by_id("profit_center", array('id'=>$lead_det['project_center']));
					$project_category_code = $pc_code['profit_center'];
				break;
				case 2:
					$cc_code = $this->welcome_model->get_data_by_id("cost_center", array('id'=>$lead_det['cost_center']));
					$project_category_code = $cc_code['cost_center'];
				break;
			}
		}
		
		$project_category_code = substr($project_category_code, 0, 3);
		
		$project_category_code = strtoupper($project_category_code);
		
		$month_year 		   = date('my');
		
		$client_projects_count = $this->customer_model->get_records_by_num('leads', array('pjt_status !='=>0, 'client_code'=>$client_code));
		
		$total_projects        = sprintf("%02d", (int)$client_projects_count+1);
		
		$project_code          = $project_category_code.'-'.$client_code.'-'.$total_projects.'-'.$month_year;
		
		$update['pjt_id']        = $project_code;
		$update['pjt_status']    = 1;
		$update['modified_by']   = $this->userdata['userid'];
		$update['date_modified'] = date('Y-m-d H:i:s');
	
		$updt_job = $this->welcome_model->update_row('leads', $update, $project_id);
		$pjt_id   = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $project_id, 'pjt_id');

		$this->customer_model->customer_update($lead_det['custid_fk'], array('is_client'=>1));		
		$this->customer_model->update_client_details_to_timesheet($client_code);
		
		// give default folder access to the assigned users.
		//$this->customer_model->create_cdefault_folders($project_id);
		//$this->customer_model->assign_default_folders($project_id);
		
		
		if($updt_job) {
			$createTimesheet = $this->customer_model->update_project_details($pjt_id);
		}

		if ($createTimesheet) {
			$log_ins['userid_fk']           = $this->userdata['userid'];
			$log_ins['jobid_fk']            = $project_id;
			$log_ins['date_created']        = date('Y-m-d H:i:s');
			$log_ins['log_content']         = 'The Lead "'.$lead_det['lead_title'].'" is Successfully Moved to Project.';
			$ins_email['log_content_email'] = 'The Lead <a href='.$this->config->item('base_url').'project/view_project/'.$project_id.'> ' .word_limiter($lead_det['lead_title'], 4).' </a> is Successfully Moved to Project.';

			$lead_assign_mail = $this->welcome_model->get_user_data_by_id($lead_det['lead_assign']);
			$lead_owner       = $this->welcome_model->get_user_data_by_id($lead_det['belong_to']);
			
			// insert the new log
			$insert_log          = $this->welcome_model->insert_row('logs', $log_ins);
			
			$user_name           = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
			$dis['date_created'] = date('Y-m-d H:i:s');
			$print_fancydate     = date('l, jS F y h:iA', strtotime($dis['date_created']));

			$arrEmails    = $this->config->item('crm');
			$arrSetEmails = $arrEmails['director_emails'];
			
			$admin_mail = implode(',',$arrSetEmails);
		
			//email sent by email template
			$param = array();
			
			$param['email_data'] = array('user_name'=>$user_name, 'print_fancydate'=>$print_fancydate, 'log_content_email'=>$ins_email['log_content_email'], 'signature'=>$this->userdata['signature']);

			$param['to_mail'] 		  = $lead_assign_mail[0]['email'] .','. $lead_owner[0]['email'];
			$param['bcc_mail'] 		  = $admin_mail;
			$param['from_email'] 	  = $this->userdata['email'];
			$param['from_email_name'] = $user_name;
			$param['template_name']   = "Lead to Project Change Notification";
			$param['subject']         = "Lead to Project Change Notification";

			$this->email_template_model->sent_email($param);
			
			$res['error'] = false;
		} else {
			$res['error'] = true;
			$res['errormsg'] = 'eConnect or Timesheet updation failed!';
		}	
		
		echo json_encode($res);
		exit;
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
		$lead_indi='null';
		$keyword='null';

		$exporttoexcel = $this->session->userdata('excel_download');
		
		 echo "<pre>"; 
		 echo $this->session->userdata('stage');
		  print_r($this->session->userdata('excel_download'));
		 print_r($exporttoexcel); exit;

		if (count($exporttoexcel)>0) {

			$stage 		  = $exporttoexcel['stage'];
			$customer 	  = $exporttoexcel['customer'];
			$worth		  = $exporttoexcel['worth'];
			$owner		  = $exporttoexcel['owner'];
			$leadassignee = $exporttoexcel['leadassignee'];
			$regionname	  = $exporttoexcel['regionname'];
			$countryname  = $exporttoexcel['countryname'];
			$statename    = $exporttoexcel['statename'];
			$locname      = $exporttoexcel['locname'];
			$lead_status  = $exporttoexcel['lead_status'];
			$lead_indi	  = $exporttoexcel['lead_indi'];
			$keyword      = $exporttoexcel['keyword'];
		}

		$filter_res = $this->welcome_model->get_filter_results($stage, $customer, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status, $lead_indi, $keyword);

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
		// $this->excel->getActiveSheet()->setCellValue('N1', 'Proposal Sent on');
		// $this->excel->getActiveSheet()->setCellValue('O1', 'Variance');
		$this->excel->getActiveSheet()->setCellValue('N1', 'Lead Indicator');
		$this->excel->getActiveSheet()->setCellValue('O1', 'Status');
		
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
		$i=2;
		foreach($filter_res as $excelarr) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $excelarr['invoice_no']);
			$this->excel->getActiveSheet()->setCellValue('B'.$i, $excelarr['lead_title']);
			$this->excel->getActiveSheet()->setCellValue('C'.$i, $excelarr['first_name'].' '.$excelarr['last_name'].' - '.$excelarr['company']);
			$this->excel->getActiveSheet()->setCellValue('D'.$i, $excelarr['region_name']);
			$this->excel->getActiveSheet()->setCellValue('E'.$i, $excelarr['ubfn'].' '.$excelarr['ubln']);
			$this->excel->getActiveSheet()->setCellValue('F'.$i, $excelarr['ufname'].' '.$excelarr['ulname']);
			$this->excel->getActiveSheet()->setCellValue('G'.$i, $excelarr['expect_worth_name']);
			$this->excel->getActiveSheet()->setCellValue('H'.$i, $excelarr['expect_worth_amount']);
			//display only date
			$this->excel->getActiveSheet()->setCellValue('I'.$i, date('d-m-Y', strtotime($excelarr['date_created'])));
			$this->excel->getActiveSheet()->setCellValue('J'.$i, date('d-m-Y', strtotime($excelarr['date_modified'])));
			$this->excel->getActiveSheet()->setCellValue('K'.$i, $excelarr['usfname'].' '.$excelarr['usslname']);
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
		
		//Set width for cells
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);			
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		
		//cell format
		$this->excel->getActiveSheet()->getStyle('A2:A'.$i)->getNumberFormat()->setFormatCode('00000');
		
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
		$data_log['log_content'] = str_replace('\n', "", $data_log['log_content']);
		$ins['log_content'] = str_replace('\n', "", $data_log['log_content']);
		
		$break = 120;
		$data_log['log_content'] =  implode(PHP_EOL, str_split($data_log['log_content'], $break));

		$res = array();
		$json = array();
        if (isset($data_log['lead_id']) && isset($data_log['userid']) && isset($data_log['log_content'])) {
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			$job_details = $this->welcome_model->get_lead_det($data_log['lead_id']);
            
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

						$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name'],'');
						
						$received_by .= $ua['first_name'] . ' ' . $ua['last_name'] . ', ';
					}
					$successful = 'This log has been emailed to:<br />';
					
					$log_subject = "eSmart Notification - {$job_details['lead_title']} [ref#{$job_details['lead_id']}] {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}";
							
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
			
				$ins['jobid_fk'] = $data_log['lead_id'];
				
				// use this to update the view status
				$ins['userid_fk'] = $upd['log_view_status'] = $data_log['userid'];
				
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = $ins['log_content'] . $successful;
				
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
				$this->db->where('lead_id', $ins['jobid_fk']);
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
			
					
			$sql = "SELECT * FROM '".$this->cfg['dbpref']."'customers,'".$this->cfg['dbpref']."'leads a WHERE `custid_fk` = `custid` AND lead_stage IN (1,4,2,5,3,7,6,9,10,11,12,13) AND ( `lead_title` LIKE '%{$keyword}%' OR `invoice_no` LIKE '%{$keyword}%' OR `custid_fk` IN ( SELECT `custid` FROM '".$this->cfg['dbpref']."'customers WHERE CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%{$keyword}%' OR `first_name` LIKE '%{$keyword}%' OR `last_name` LIKE '%{$keyword}%' OR `company` LIKE '%{$keyword}%' ) ) ORDER BY `lead_stage`, `lead_title`";
			
			$resul = $this->welcome_model->search_res($keyword);
					
			$q = $this->db->query($sql);
			//echo $this->db->last_query();
			if ($q->num_rows() > 0)
			{				
				$result = $q->result_array();
				$i = 0;
				foreach ($this->cfg['lead_stage'] as $k => $v)
				{
					while (isset($result[$i]) && $k == $result[$i]['lead_stage'])
					{
						$data['results'][$k][] = $result[$i];
						$i++;
					}
				}
				
				if (count($result) == 1)
				{
					$this->session->set_flashdata('header_messages', array('Only one result found! You have been redirect to the job.'));
					
					//$status_type = (in_array($result[0]['lead_stage'], array(4,5,6,7,8,25))) ? 'invoice' : 'welcome';
					//$status_type = (in_array($result[0]['lead_stage'])) ? 'invoice' : 'welcome';
					
					//redirect($status_type . '/view_quote/' . $result[0]['lead_id']);
					redirect('welcome/view_quote/' . $result[0]['lead_id'] . '/draft');
				}
				else 
				{	//echo "tljlj";
					$this->session->set_flashdata('header_messages', array('Results found! You have been redirect to the job.'));
					redirect('welcome/view_quote/' . $result[0]['lead_id'] . '/draft');
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
		$ins['lead_title']           = 'Ask the Expert';
		$ins['custid_fk']           = $insert_id;
		$ins['lead_service']        = $_POST['lead_service'];
		//$ins['lead_source']       = '';
		$ins['lead_assign']         = 59;
		$ins['expect_worth_id']     = 5;
		$ins['expect_worth_amount'] = '0.00';
		$ins['belong_to']           = 59; // lead owner
		// $ins['division']         = $_POST['job_division'];
		$ins['date_created']        = date('Y-m-d H:i:s');
		$ins['date_modified']       = date('Y-m-d H:i:s');
		$ins['lead_stage']          = 1;
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
	public function loadCountrys()
	{
		$data = real_escape_array($this->input->post());
		$region_id = join(",", $data['region_id']);

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
	public function loadStates()
	{
		$data = real_escape_array($this->input->post());
		$cnt_id = join(",", $data['coun_id']);
		
	    $output = '';
		$data = $this->welcome_model->getstate_list($cnt_id);
		foreach($data as $st) {
		    $output .= '<option value="'.$st['stateid'].'">'.$st['state_name'].'</option>';
		}
		echo $output;
	}
	
	//For Locations
	public function loadLocns()
	{
		$data = real_escape_array($this->input->post());
		$loc_id = join(",", $data['st_id']);
		
	    $output = '';
		$data = $this->welcome_model->getlocation_list($loc_id);
		//print_r($data);
		foreach($data as $st) {
		    $output .= '<option value="'.$st['locationid'].'">'.$st['location_name'].'</option>';
		}
		echo $output;
	}
	
	//For Saving the search criteria
	public function save_search($type)
	{
		$post_data = real_escape_array($this->input->post());
		// echo "<pre>"; print_r($post_data); exit;
		$ins = array();
		
		$ins['search_for']   = $type;
		$ins['search_name']  = $post_data['search_name'];
		$ins['user_id']	  	 = $this->userdata['userid'];
		$ins['is_default']	 = $post_data['is_default'];
		$ins['stage'] 		 = $post_data['stage'];
		$ins['customer']	 = $post_data['customer'];
		$ins['worth']		 = $post_data['worth'];
		$ins['owner']	  	 = $post_data['owner'];
		$ins['leadassignee'] = $post_data['leadassignee'];
		$ins['regionname'] 	 = $post_data['regionname'];
		$ins['countryname']	 = $post_data['countryname'];
		$ins['statename']	 = $post_data['statename'];
		$ins['locname']	  	 = $post_data['locname'];
		$ins['lead_status']  = $post_data['lead_status'];
		$ins['lead_indi']	 = $post_data['lead_indi'];
		$ins['created_on'] 	 = date('Y-m-d H:i:s');
		// echo "<pre>"; print_r($ins); exit;
		$last_ins_id = $this->welcome_model->insert_row_return_id('saved_search_critriea', $ins);
		if($last_ins_id) {
			if($ins['is_default'] == 1) {
				$updt['is_default'] = 0;
				$this->db->where('search_id != ', $last_ins_id);
				$this->db->where('user_id', $this->userdata['userid']);
				$this->db->where('search_for', $type);
				$this->db->update($this->cfg['dbpref'] . 'saved_search_critriea', $updt);
			}
			
			$saved_search = $this->welcome_model->get_saved_search($this->userdata['userid'], $search_for=1);
			
			$result['res'] = true;
			$result['msg'] = 'Search Criteria Saved.';
			
			$result['search_div'] .= '<li id="item_'.$last_ins_id.'" class="saved-search-res"><span><a href="javascript:void(0)" onclick="show_search_results('.$last_ins_id.')">'.$post_data['search_name'].'</a></span>';
			$result['search_div'] .= '<span class="rd-set-default">';
			$result['search_div'] .= '<input type="radio" name="set_default_search" class="set_default_search" value="'.$last_ins_id.'" ';
			if($searc['is_default']==1) { 
				$result['search_div'] .= 'checked="checked"';
			}
			$result['search_div'] .= '/>';
			$result['search_div'] .= '</span>';
			$result['search_div'] .= '<span><a title="Set Default" href="javascript:void(0)" onclick="delete_save_search('.$last_ins_id.')" ><img alt="delete" src="assets/img/trash.png"></a></span></li>';

		} else {
			$result['res'] = false;
			$result['msg'] = 'Search Criteria cant be Saved.';
		}
		echo json_encode($result);
		exit;
	}
	
	public function get_search_name_form() {
		$html = '<table><tr>';
		$html .= '<td><label>Search Name:</label></td>';
		$html .= '<td><input type="text"  class="textfield width160px" name="search_name" id="search_name" value="" /></td></tr><tr>';
		$html .= '<td><label>Is Default:</label></td>';
		$html .= '<td><input type="checkbox" name="is_default" id="is_default" value="1" /></td></tr><tr><td colspan=2>';
		$html .= '<div class="buttons"><button onclick="save_search(); return false;" class="positive" type="submit">Save</button>
		<button onclick="save_cancel(); return false;" class="negative" type="submit">Cancel</button></div></td></tr></table>';
		echo json_encode($html);
		exit;
	}
	
	public function set_default_search($search_id, $type) {
		
		$result = array();
		
		$tbl = 'saved_search_critriea';
		$wh_condn = array('search_for'=>1, 'user_id'=>$this->userdata['userid']);
		
		$updt = $this->welcome_model->update_records($tbl,$wh_condn,'',$up_arg=array('is_default'=>0));
		$updt_condn = $this->welcome_model->update_records($tbl,$wh_condn=array('search_id'=>$search_id),'',$up_arg=array('is_default'=>1));

		if($updt_condn) {
			$result['resu'] = 'updated';
		}
		
		echo json_encode($result);
		exit;
	}

	public function delete_save_search($search_id, $type) {
		
		$result = array();
		
		$tbl = 'saved_search_critriea';
		$wh_condn = array('search_for'=>1, 'search_id'=>$search_id);

		if($this->welcome_model->delete_records($tbl, $wh_condn)) {
			$result['resu'] = 'deleted';
		}
		
		echo json_encode($result);
		exit;
	}
	
}
?>