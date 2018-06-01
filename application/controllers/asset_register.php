<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); 
class Asset_register extends crm_controller {

    public $cfg;
    public $userdata;

    function __construct() {
        parent::__construct();

        $this->login_model->check_login();
        $this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('asset_model');
        $this->load->model('dashboard_model');
        $this->load->model('request_model');
        $this->load->model('asset_model');
        $this->load->model('asset_location_model');
        $this->load->model('manage_service_model');

        $this->load->model('customer_model');
        $this->load->model('regionsettings_model');
        $this->load->model('email_template_model');
        $this->load->helper('text');
        $this->email->set_newline("\r\n");

        $this->load->helper('lead_stage_helper');
        $this->stg = getLeadStage(); //lead_stage_helper location
        $this->stg_name = getLeadStageName(); //lead_stage_helper location
        $this->stages = @implode('","', $this->stg);
    }

    /*
     * Redirect user to quotation list
     */

    public function index() {
        redirect('asset_register/quotation');
    }

    /*
     * List all the Leads based on levels
     * @access public
     */

    public function quotation($type = 'draft', $tab = '') {
//        print_r($_POST);exit;
        $page_label = 'Leads List';

        if (isset($_POST) && isset($_POST['type']) && $_POST['type'] == 'load_proposal_expect_end') {
            $data['load_proposal_expect_end'] = 'load_proposal_expect_end';
        } else {
            $data['load_proposal_expect_end'] = '';
        }

        $data['lead_stage'] = $this->stg_name;
//               / print_r($data['lead_stage']);exit;
        $data['customers'] = $this->asset_model->get_customers();
        $data['lead_owner'] = $this->asset_model->get_users();
        //  echo'<pre>';print_r($data['lead_owner']);exit;
        $data['regions'] = $this->regionsettings_model->region_list();
        $data['services'] = $this->asset_model->get_lead_services();
        $data['sources'] = $this->asset_model->get_lead_sources();
        $data['industry'] = $this->asset_model->get_industry();
        $data['saved_search'] = $this->asset_model->get_saved_search($this->userdata['userid'], $search_for = 1);

        $this->load->view('asset_register/main_view', $data);
    }

    /*
     * List all the Leads based on levels with advanced search filter.
     */

    // public function advance_filter_search($stage='null', $customer='null', $worth='null', $owner='null', $leadassignee='null', $regionname='null',$countryname='null', $statename='null', $locname='null', $lead_status='null', $lead_indi='null', $keyword='null') 
    public function advance_filter_search($search_type = false, $search_id = false) {
    // echo"here";exit;
   //echo'<pre>search_type=>';print_r($search_type);exit;
        // echo'<pre>search_id=>';print_r($search_id);
        $filt = array();
        $stage = null;
        $from_date = null;
        $to_date = null;
        $customer = null;
        $service = null;
        $lead_src = null;
        $industry = null;
        $worth = null;
        $owner = null;
        $leadassignee = null;
        $regionname = null;
        $countryname = null;
        $statename = null;
        $locname = null;
        $lead_status = null;
        $lead_indi = null;
        $keyword = null;
        $proposal_expect_end = null;

        $this->session->unset_userdata('load_proposal_expect_end');

        if ($search_type == 'search' && $search_id == false) {
           // echo 'if';exit;
            $filt = real_escape_array($this->input->post()); //echo'<pre>filt1=>';print_r($filt);
            $this->session->set_userdata("lead_search_by_default", 0);
            $this->session->set_userdata("lead_search_by_id", 0);
            $this->session->set_userdata("lead_search_only", 1);
        } else if ($search_type == 'search' && is_numeric($search_id)) {
         //   echo 'elsif';exit;
            $wh_condn = array('search_id' => $search_id, 'search_for' => 1, 'user_id' => $this->userdata['userid']);
            $get_rec = $this->asset_model->get_data_by_id('saved_search_critriea', $wh_condn);
            unset($get_rec['search_id']);
            unset($get_rec['search_for']);
            unset($get_rec['search_name']);
            unset($get_rec['user_id']);
            unset($get_rec['is_default']);
            if (!empty($get_rec))
                $filt = real_escape_array($get_rec);
            $this->session->set_userdata("lead_search_by_default", 0);
            $this->session->set_userdata("lead_search_by_id", $search_id);
            $this->session->set_userdata("lead_search_only", 0);
        } else if ($search_type == 'load_proposal_expect_end' && $search_id == false) {
         //   echo 'elsif2';exit;
            $this->session->set_userdata("load_proposal_expect_end", 1);
            $proposal_expect_end = 'load_proposal_expect_end';
        } else {
        // echo 'else';exit;
            //print_r($this->userdata['userid']);exit;
          //  $wh_condn = array('search_for' => 1, 'user_id' => $this->userdata['userid'], 'is_default' => 1);
          //  $get_rec = $this->asset_model->get_data_by_id('saved_search_critriea', $wh_condn);
          //  unset($get_rec['search_id']);
          //  unset($get_rec['search_for']);
          //  unset($get_rec['search_name']);
          //  unset($get_rec['user_id']);
          //  unset($get_rec['is_default']);
         //   if (!empty($get_rec)) {
         //       $filt = real_escape_array($get_rec);
        //  //      $this->session->set_userdata("lead_search_by_default", 1);
        //        $this->session->set_userdata("lead_search_only", 0);
        //        $this->session->set_userdata("lead_search_by_id", 0);
        //    } else {
      //          $this->session->set_userdata("lead_search_by_default", 0);
       //         $this->session->set_userdata("lead_search_only", 1);
      //          $this->session->set_userdata("lead_search_by_id", 0);
      //      }
        }
        // echo'<pre>filt2=>';print_r($filt);
        // echo'<pre>';print_r(count($filt));exit;
        if (count($filt) > 0) {
// echo 'yes';exit;
            $department_id = $filt['department_id'];
            $project_id = $filt['project_id'];
            $asset_name = $filt['asset_name'];
            $asset_type = $filt['asset_type'];
            $storage_mode = $filt['storage_mode'];
            $location = $filt['location'];
            $asset_owner = $filt['asset_owner'];
            $labelling = $filt['labelling'];
            $confidentiality = $filt['confidentiality'];
            $integrity = $filt['integrity'];
            $availability = $filt['availability'];
           
            //$keyword 	  = !empty($filt['keyword']) ? $filt['keyword'] : '';
            /* $excel_arr 	  = array();
              foreach ($filt as $key => $val) {
              $excel_arr[$key] = $val;
              } */
        } else {
            $this->session->unset_userdata(array("excel_download" => ''));
        }

        if ($this->input->post("keyword")) {
            $filt['keyword'] = $this->input->post("keyword");
            $keyword = $this->input->post("keyword");
            $this->session->set_userdata("search_keyword", $keyword);
        } else {
            $this->session->set_userdata("search_keyword", '');
        }

        $filter_results = $this->asset_model->get_filter_results($department_id, $project_id, $asset_name, $asset_type, $storage_mode, $location, $asset_owner, $labelling, $confidentiality, $integrity, $availability);
        // echo $this->db->last_query(); die;
        $data['filter_results'] = $filter_results;
       // echo '<pre>';print_r($data['filter_results']);exit;
        $data['department_id'] = $department_id;
       //  echo '<pre>';print_r($data['department_id']);exit;
        $data['project_id'] = $project_id;
        $data['asset_name'] = $asset_name;
        $data['asset_type'] = $asset_type;
        $data['storage_mode'] = $storage_mode;
        $data['location'] = $location;
        $data['asset_owner'] = $asset_owner;
        $data['labelling'] = $labelling;
        $data['confidentiality'] = $confidentiality;
        $data['integrity'] = $integrity;
        $data['availability'] = $availability;
//        $data['locname'] = $locname;
//        $data['lead_status'] = $lead_status;
//        $data['lead_indi'] = $lead_indi;
//        $data['keyword'] = $keyword;

       // $db_fields = $this->asset_model->get_lead_dashboard_field($this->userdata['userid']);
      //  if (!empty($db_fields) && count($db_fields) > 0) {
      //      foreach ($db_fields as $record) {
       //         $data['db_fields'][] = $record['column_name'];
     //       }
      //  }
    //  echo '<pre>';      print_r($data);exit;
        $this->load->view('asset_register/advance_filter_view', $data);
    }

    /*
     * Display the Lead
     * @access public
     * @param int $id - Job Id
     */

    public function view_quote($id = 0, $quote_section = '') {
       // print_r($id);exit;
        $this->load->helper('text');
        $this->load->helper('fix_text');

        $usid = $this->session->userdata('logged_in_user');

        $getLeadDet = $this->asset_model->get_asset_detail($id);
     //   print_r($getLeadDet);exit;
       // $arrLeadInfo = $this->request_model->get_lead_info($id);

        if (!empty($getLeadDet)) {
           
            $this->load->view('leads/welcome_view_quote', $data);
        } else {
            // echo "Lead does not exist or you may not be authorised to view this";
            $this->session->set_flashdata('login_errors', array("Lead does not exist or you may not be authorised to view this."));
            redirect('welcome/quotation');
        }
    }

    /*
     * Get the logs
     */

    function getLogs($id) {

        $data['log_html'] = '';
        $getLogsData = $this->asset_model->get_logs($id);
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
                $user_data = $this->asset_model->get_user_data_by_id($ld['userid_fk']);

                if (count($user_data) < 1) {
                    echo '<!-- ', print_r($ld, TRUE), ' -->';
                    continue;
                }

                $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));

                $fancy_date = date('l, jS F y h:iA', strtotime($ld['date_created']));

                $stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';

                $table = '<tr id="log" class="log' . $stick_class . '"><td id="log" class="log' . $stick_class . '">
						 <p class="data log' . $stick_class . '"><span class="log' . $stick_class . '">' . $fancy_date . '</span>' . $user_data[0]['first_name'] . ' ' . $user_data[0]['last_name'] . '</p>
						 <p class="desc log' . $stick_class . '">' . stripslashes($log_content) . '</p></td></tr>';
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

    function ajax_quote_items($lead_id = 0, $itemid = 0, $return = false) {
        $this->load->helper('text');
        $this->load->helper('fix_text');

        $quote = $this->asset_model->get_quote_items($lead_id);

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

                if (!empty($row['item_price'])) {
                    $html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" width="85%">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td><td width="14%" class="item-price width100px" align="right" valign="bottom">' . $row['item_price'] . '</td></tr></table></li>';
                } else {
                    $html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" colspan="2">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td></tr></table></li>';
                }
            }

            $json['sale_amount'] = '$' . number_format($sale_amount, 2, '.', ',');
            $json['gst_amount'] = ($sale_amount > 0) ? '$' . number_format($sale_amount / 10, 2, '.', ',') : '$0.00';

            $json['total_inc_gst'] = '$' . number_format($sale_amount * 1.1, 2, '.', ',');
            $json['numeric_total_inc_gst'] = $sale_amount * 1.1;

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
     * Create a new asset
     * Loading just the view
     * Quotes are created with Ajax functions
     * @access public
     */

    public function new_asset($lead = FALSE, $customer = FALSE) {
        /* additional item list */
        // $data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
        $data['categories'] = $this->asset_model->get_categories();
        $c = count($data['categories']);
        for ($i = 0; $i < $c; $i++) {
            $data['categories'][$i]['records'] = $this->asset_model->get_cat_records($data['categories'][$i]['cat_id']);
        }
        $data['lead_source'] = $this->asset_model->get_lead_sources();
        $data['expect_worth'] = $this->asset_model->get_expect_worths();
        $data['job_cate'] = $this->asset_model->get_lead_services();
        $data['project_listing_ls'] = $this->asset_model->ListActiveprojects();
        $data['sales_divisions'] = $this->asset_model->get_sales_divisions();
        $data['industry'] = $this->asset_model->get_industry();

        $this->load->view('asset_register/asset_register', $data);
    }

    /**
     *  Set the quote editing interface
     */
    function edit_quote($id = 0) {
        if (($data['quote_data'] = $this->asset_model->get_lead_all_detail($id)) !== FALSE) {
            $data['edit_quotation'] = true;

            $data['categories'] = $this->asset_model->get_categories();

            $c = count($data['categories']);

            for ($i = 0; $i < $c; $i++) {
                $data['categories'][$i]['records'] = $this->asset_model->get_cat_records($data['categories'][$i]['cat_id']);
            }

            $data['lead_source_edit'] = $this->asset_model->get_lead_sources();

            $regid = $data['quote_data']['add1_region'];
            $cntryid = $data['quote_data']['add1_country'];
            $steid = $data['quote_data']['add1_state'];
            $locid = $data['quote_data']['add1_location'];

            //for new level concept - start here
            $reg_lvl_id = array(5, 4, 3);
            $cont_lvl_id = array(5, 4, 2);
            $ste_lvl_id = array(5, 3, 2);
            $loc_lvl_id = array(4, 3, 2);

            $regUserList = $this->asset_model->get_lvl_users('levels_region', 'region_id', $regid, $reg_lvl_id);
            $cntryUserList = $this->asset_model->get_lvl_users('levels_country', 'country_id', $cntryid, $cont_lvl_id);
            $steUserList = $this->asset_model->get_lvl_users('levels_state', 'state_id', $steid, $ste_lvl_id);
            $locUserList = $this->asset_model->get_lvl_users('levels_location', 'location_id', $locid, $loc_lvl_id);
            $globalUserList = $this->asset_model->get_lvlOne_users();
            $getcustomerdetail = $this->asset_model->get_customer_name_by_lead($id);

            // echo "<pre>"; print_r($getcustomerdetail); die;

            $userList = array_merge_recursive($regUserList, $cntryUserList, $steUserList, $locUserList, $globalUserList);
            $users[] = 0;
            foreach ($userList as $us) {
                $users[] = $us['user_id'];
            }

            $userList = array_unique($users);
            $userList = array_values($userList);

            // $userList = implode(',', $userList);
            $data['lead_assign_edit'] = $this->asset_model->get_userlist($userList);
            //for new level concept - end here

            $data['expect_worth'] = $this->asset_model->get_expect_worths();
            $data['lead_stage'] = $this->asset_model->get_lead_stage();
            $data['job_cate'] = $this->asset_model->get_lead_services();
            $data['sales_divisions'] = $this->asset_model->get_sales_divisions();
            $data['industry'] = $this->asset_model->get_industry();

            $this->load->view('leads/welcome_view', $data);
        } else {
            $this->session->set_flashdata('header_messages', array("Status Changed Successfully."));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            //redirect('welcome/quotation');
        }
    }

    function custom_update_users() {
        $res = array();
        $post_data = real_escape_array($this->input->post());
        $res['result'] = 'error';
        if ($post_data['project_lead_id']) {
            // update project manager
            $this->db->update($this->cfg['dbpref'] . "leads", array("assigned_to" => $post_data['project_manager']), array("lead_id" => $post_data['project_lead_id']));
            $project_team_members = $this->input->post('project_team_members');
            $stake_members = $this->input->post('stake_members');

            //update project team members
            if (count($project_team_members) > 0) {
                $this->db->delete($this->cfg['dbpref'] . "contract_jobs", array("jobid_fk" => $post_data['project_lead_id']));
                foreach ($project_team_members as $pusers) {
                    $this->db->insert($this->cfg['dbpref'] . "contract_jobs", array("jobid_fk" => $post_data['project_lead_id'], "userid_fk" => $pusers, "modified_by" => $this->userdata['userid']));
                }
            }

            //update project stake holders
            if (count($stake_members) > 0) {
                $this->db->delete($this->cfg['dbpref'] . "stake_holders", array("lead_id" => $post_data['project_lead_id']));
                foreach ($stake_members as $susers) {
                    $this->db->insert($this->cfg['dbpref'] . "stake_holders", array("lead_id" => $post_data['project_lead_id'], "user_id" => $susers));
                }
            }
            $res['result'] = 'ok';
        }
        echo json_encode($res);
    }

    /**
     * Initiates and create the quote based on an ajax request
     */
    function ajax_create_quote() 
    {
       // echo 'hi';exit;
        $data = real_escape_array($this->input->post());
  //  print_r($data);exit;
        $ins['asset_name'] = $data['asset_name'];
        $chkAssetName = $this->asset_model->checkAssetName($ins['asset_name']);
        if (is_array($chkAssetName) && count($chkAssetName) > 0) {
            $json['error'] = false;
            $json['errormsg'] = 'Asset already registered';
            echo json_encode($json);
       
        } else {
         //   echo 'else';exit;
            $ins['department_id'] = $data['department'];
            $ins['project_id'] = $data['Project'];
            $ins['asset_type'] = $data['asset_type'];
            $ins['storage_mode'] = $data['storage_mode'];
            $ins['location'] = $data['location'];
            $ins['asset_owner'] = $data['username'];
            $ins['labelling'] = $data['labelling'];
            $ins['confidentiality'] = $data['confidentiality'];
            $ins['integrity'] = $data['integrity'];
            $ins['availability'] = $data['availability'];
            $ins['created_by'] = $data['username'];
            //print_r($ins);exit;
            $insert_asset = $this->asset_model->insert_row_return_id('asset_register', $ins);
           // print_r($insert_asset);exit;
           // $insert_asset = $this->db->insert_id();
                $json['error'] = true;
                $json['insert_id'] = $insert_asset;
                echo json_encode($json);
             //  print_r($insert_asset);
            //insert logs
            $ins_log = array();
            $ins_log['log_content'] = "Asset Created For the " . $customer['company'] . " - " . $customer['customer_name'] . " On :" . " " . date('M j, Y g:i A');
            $ins_log['log_content'] .= "\n Lead Title " . $get_det['lead_title'];
            $ins_log['jobid_fk'] = $insert_asset;
            $ins_log['date_created'] = date('Y-m-d H:i:s');
            $ins_log['userid_fk'] = $this->userdata['userid'];
            $insert_log = $this->asset_model->insert_row_return_id('logs', $ins_log);
//        /  print_r($insert_log);
          //  redirect('asset_register/quotation');
            
        }
    }

    /*
     * provides details of the customer
     * for a given id
     * @param custid
     * @return string (json formatted)
     */

    function ajax_customer_details($custid) {
        $this->load->model('customer_model');
        $result = $this->customer_model->get_customer($custid);
        if (is_array($result) && count($result) > 0) {
            echo json_encode($result[0]);
        }
    }

    /*
     * provide the list of users
     * for a region id, country id, state id, location id
     * @param regId, cntryId, stId, locId
     * @return string (json formatted)
     */

    function user_level_details($regId, $cntryId, $stId, $locId) {
        $this->load->model('user_model');
        $result = $this->user_model->get_userslist($regId, $cntryId, $stId, $locId);

        if (is_array($result) && count($result) > 0) {
            echo json_encode($result);
        }
    }

    /*
     * Set the Expected proposal date for the lead.
     * @lead_id
     */

    public function set_proposal_date() {
        $updt_data = real_escape_array($this->input->post());

        $data['error'] = FALSE;

        $timestamp = strtotime($updt_data['date']);

        if ($updt_data['date_type'] != 'start') {
            $data['error'] = 'Invalid date status supplied!';
        } else if (!$timestamp) {
            $data['error'] = 'Invalid date supplied!';
        } else {
            if ($updt_data['date_type'] == 'start') {
                $updt['proposal_expected_date'] = date('Y-m-d H:i:s', $timestamp);
                $updt_date = $this->asset_model->update_row('leads', $updt, $updt_data['lead_id']);
            }
        }
        echo json_encode($data);
    }

    /*
     * Change the Lead Creation for the lead.
     * @lead_id
     */

    public function set_lead_creation_date() {
        $updt_data = real_escape_array($this->input->post());

        $data['error'] = FALSE;

        $timestamp = strtotime($updt_data['date']);

        if (!$timestamp) {
            $data['error'] = 'Invalid date supplied!';
        } else {
            $updt['date_created'] = date('Y-m-d H:i:s', $timestamp);
            $updt_date = $this->asset_model->update_row('leads', $updt, $updt_data['lead_id']);
        }
        echo json_encode($data);
    }

    /*
     * adds an item to the lead based on the ajax request
     */

    function ajax_add_item() {
        $errors = '';
        if (trim($_POST['hours']) != '' && !is_numeric($_POST['hours'])) {
            $errors[] = 'Hours can only be numeric values!';
        }
        if (trim($_POST['item_desc']) == '') {
            $errors[] = 'You must provide a description!';
        }
        if (trim($_POST['item_price']) != '' && !is_numeric($_POST['item_price'])) {
            $errors[] = 'Price can only be numeric values!';
        }
        if (!preg_match('/^[0-9]+$/', $_POST['lead_id'])) {
            $errors[] = 'Lead ID must be numeric!';
        }

        if (is_array($errors)) {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        } else {
            if (!preg_match('/^\n/', $_POST['item_desc'])) {
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

        if (empty($hours)) {
            $ins['hours'] = '0.00';
        } else {
            $ins['hours'] = $hours;
        }
        if (empty($item_price)) {
            $ins['item_price'] = '0.00';
        } else {
            $ins['item_price'] = $item_price;
        }

        if (is_numeric(trim($hours))) {
            $ins['hours'] = $hours;
            $ins['item_price'] = $_POST['item_price'] * $hours;
        }

        $posn = $this->asset_model->get_item_position($lead_id);

        $ins['item_position'] = $posn[0]['item_position'] + 1;

        // $ins = real_escape_array($ins);
        // $ins['item_desc'] = @str_replace('\r\n', '', $ins['item_desc']);

        $insert_item = $this->asset_model->insert_row_return_id('items', $ins);

        if ($insert_item > 0) {
            $itemid = $insert_item;

            if ($ajax == TRUE) {
                $this->ajax_quote_items($ins['jobid_fk'], $itemid);
            } else {
                return TRUE;
            }
        } else {
            if ($ajax == TRUE) {
                echo "{error:true, errormsg:'Data insert failed!'}";
            } else {
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

        if (trim($data['lead_title']) == '' || !preg_match('/^[0-9]+$/', trim($data['lead_service']))) {
            echo "{error:true, errormsg:'Title and Lead Service are required fields!'}";
        } else if (!preg_match('/^[0-9]+$/', trim($data['jobid_edit']))) {
            echo "{error:true, errormsg:'quote ID must be numeric!'}";
        } else {
            $ins['custid_fk'] = $data['customer_id'];
            $ins['lead_title'] = $data['lead_title'];
            $ins['division'] = $data['job_division'];
            $ins['industry'] = $data['industry'];
            $ins['lead_service'] = $data['lead_service'];
            $ins['lead_source'] = $data['lead_source_edit'];
            $ins['expect_worth_id'] = $data['expect_worth_edit'];
            $ins['expect_worth_amount'] = $data['expect_worth_amount'];
            $ins['actual_worth_amount'] = $data['actual_worth'];
            $ins['lead_stage'] = $data['lead_stage'];
            if (empty($data['actual_worth'])) {
                $ins['actual_worth_amount'] = 0.00;
            }
            if ($data['actual_worth'] != $data['expect_worth_amount_dup']) {
                $ins['proposal_adjusted_date'] = date('Y-m-d H:i:s');
            }
            /* if($data['lead_assign_edit_hidden'] == null || $data['lead_assign_edit_hidden'] == 0) {
              $ins['lead_assign'] = $data['lead_assign_edit'];
              } else {
              $ins['lead_assign'] = $data['lead_assign_edit_hidden'];
              } */

            // $ins['lead_assign']     = @implode(",",$data['lead_assign_edit_hidden']);
            $ins['lead_assign'] = $data['lead_assign_edit_hidden'];

            // for lead status history - starts here
            if ($_POST['lead_status'] != $_POST['lead_status_hidden']) {
                $lead_stat_hist['lead_id'] = $_POST['jobid_edit'];
                $lead_stat_hist['dateofchange'] = date('Y-m-d H:i:s');
                $lead_stat_hist['changed_status'] = $_POST['lead_status'];
                $lead_stat_hist['modified_by'] = $this->userdata['userid'];
                $insert_lead_stat_his = $this->asset_model->insert_row('lead_status_history', $lead_stat_hist);
            }
            // for lead status history - ends here	

            /* lead owner starts here */
            if ($data['lead_owner_edit_hidden'] == null || $data['lead_owner_edit_hidden'] == 0) {
                $ins['belong_to'] = $data['lead_owner_edit'];
            } else {
                $ins['belong_to'] = $data['lead_owner_edit_hidden'];
            }
            /* lead owner ends  here */
            $ins['lead_indicator'] = $data['lead_indicator'];
            $ins['lead_status'] = $data['lead_status'];
            if ($data['lead_stage'] != '' && $data['lead_stage'] != 'null')
                $ins['lead_stage'] = $data['lead_stage'];
            $ins['lead_hold_reason'] = $data['reason'];
            $ins['date_modified'] = date('Y-m-d H:i:s');
            $ins['modified_by'] = $this->userdata['userid'];
            /* belong to assigned editing the lead owner */

            /* for onhold reason insert */
            $inse['log_content'] = "Lead Onhold Reason: ";
            $inse['log_content'] .= $data['reason'];
            $inse['jobid_fk'] = $data['jobid_edit'];
            $inse['date_created'] = date('Y-m-d H:i:s');
            $inse['userid_fk'] = $this->userdata['userid'];
            if ($data['reason'] != '' && $data['reason'] != 'null')
                $insert_log = $this->asset_model->insert_row('logs', $inse);
            /* end of onhold reason insert */

            /* for proposal adjust date insert */
            $ins_ad1['log_content'] = 'Expected Worth Amount Modified On :' . ' ' . date('M j, Y g:i A');
            $ins_ad1['jobid_fk'] = $data['jobid_edit'];
            $ins_ad1['date_created'] = date('Y-m-d H:i:s');
            $ins_ad1['userid_fk'] = $this->userdata['userid'];
            if ($data['expect_worth_amount'] != $data['hidden_expect_worth_amount']) {
                $insert_log = $this->asset_model->insert_row('logs', $ins_ad1);
            }
            //insert log for expect worth amount changes
            $ins_ad['log_content'] = 'Actual Worth Amount Modified On :' . ' ' . date('M j, Y g:i A');
            $ins_ad['jobid_fk'] = $data['jobid_edit'];
            $ins_ad['date_created'] = date('Y-m-d H:i:s');
            $ins_ad['userid_fk'] = $this->userdata['userid'];
            if ($data['actual_worth'] != $data['expect_worth_amount_dup']) {
                $insert_log = $this->asset_model->insert_row('logs', $ins_ad);
            }
            /* end proposal adjust date insert */
            $lead_id = $data['jobid_edit'];

            $updt_job = $this->asset_model->update_row('leads', $ins, $data['jobid_edit']);

            if ($data['customer_id'] != $data['customer_id_old']) {
                $inser['log_content'] = "Customer has changed from ' " . $data['customer_company_name_old'] . " ' to ' " . $data['customer_company_name'] . " '";
                $inser['jobid_fk'] = $data['jobid_edit'];
                $inser['userid_fk'] = $this->userdata['userid'];
                $insert_log = $this->asset_model->insert_row('logs', $inser);
            }

            if ($updt_job) {
                $his['lead_status'] = $data['lead_status']; //lead_stage_history - lead_status update

                if ($data['lead_stage'] != $data['lead_stage_hidden']) {
                    $this->ajax_update_quote($data['jobid_edit'], $data['lead_stage']);
                }

                $whee_condn = array('custid' => $data['customer_id']);
                $results = $this->db->get_where($this->cfg['dbpref'] . 'customers', $whee_condn)->row_array();
                // echo $this->db->last_query(); die;
                //update customer isclient
                if ($data['lead_status'] == 4) {
                    if (count($results) > 0) {
                        $update_cus = array('is_client' => 1);
                        $this->db->where('companyid', $results['company_id']);
                        $this->db->update($this->cfg['dbpref'] . 'customers_company', $update_cus);
                    }
                    // $updt_isclient = $this->asset_model->updtCustomerIsClient($data['customer_id'], $update_cus);
                } else if ($data['lead_status'] != 4 && $data['lead_status_hidden'] == 4) {
                    //get all contacts
                    $whee_condn = array('company_id' => $results['company_id']);
                    $getcontacts = $this->db->get_where($this->cfg['dbpref'] . 'customers', $whee_condn)->result_array();
                    if (!empty($getcontacts)) {
                        $custids = array();
                        foreach ($getcontacts as $re) {
                            $custids[] = $re['custid'];
                        }
                    }
                    $chk_isclient = 1;
                    if (!empty($custids)) {
                        $chk_isclient = $this->asset_model->check_isclient_stat($custids);
                    }
                    if ($chk_isclient == 0) {
                        $update_cust = array('is_client' => 0);
                        $this->db->where('companyid', $results['company_id']);
                        $this->db->update($this->cfg['dbpref'] . 'customers_company', $update_cust);
                        // $updt_isclient = $this->asset_model->updtCustomerIsClient($data['customer_id'], $update_cus);
                    }
                }

                $updt_lead_stage_his = $this->asset_model->update_row('lead_stage_history', $his, $lead_id);

                /* if(($data['lead_assign_edit_hidden'] == $data['lead_assign_edit'])) 
                  {
                  $ins['userid_fk'] = $this->userdata['userid'];
                  $ins['jobid_fk']  = $lead_id;

                  $lead_det		  = $this->asset_model->get_lead_det($lead_id); //after update.
                  $lead_assign_mail = $this->asset_model->get_user_data_by_id($lead_det['lead_assign']);
                  $lead_owner       = $this->asset_model->get_user_data_by_id($lead_det['belong_to']);

                  $inserts['userid_fk'] = $this->userdata['userid'];
                  $inserts['jobid_fk']  = $lead_id;
                  $inserts['date_created'] = date('Y-m-d H:i:s');
                  $inserts['log_content']  = "Lead has been Re-assigned to: " . $lead_assign_mail[0]['first_name'] .' '.$lead_assign_mail[0]['last_name'] .'<br />'. 'For Lead .' .word_limiter($lead_det['lead_title'], 4). ' ';

                  // inset the new log
                  $insert_log = $this->asset_model->insert_row('logs', $inserts);

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

                  } */
                /* lead owner edit mail notifiction starts here */
                if (($data['lead_owner_edit_hidden'] == $data['lead_owner_edit'])) {
                    $ins['userid_fk'] = $this->userdata['userid'];
                    $ins['jobid_fk'] = $lead_id;

                    $lead_det = $this->asset_model->get_lead_det($lead_id); //after update.
                    $lead_assign_mail = $this->asset_model->get_user_data_by_id($lead_det['lead_assign']);
                    $lead_owner = $this->asset_model->get_user_data_by_id($lead_det['belong_to']);

                    $inserts['userid_fk'] = $this->userdata['userid'];
                    $inserts['jobid_fk'] = $lead_id;
                    $inserts['date_created'] = date('Y-m-d H:i:s');
                    $inserts['log_content'] = "Lead Owner has been Re-assigned to: " . $lead_owner[0]['first_name'] . ' ' . $lead_owner[0]['last_name'] . '<br />' . 'For Lead ' . word_limiter($lead_det['lead_title'], 4) . ' ';
                    // insert the new log
                    $insert_log = $this->asset_model->insert_row('logs', $inserts);

                    $user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
                    $dis['date_created'] = date('Y-m-d H:i:s');
                    $print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

                    $from = $this->userdata['email'];
                    $arrEmails = $this->config->item('crm');
                    $arrSetEmails = $arrEmails['director_emails'];
                    $mangement_email = $arrEmails['management_emails'];
                    $mgmt_mail = implode(',', $mangement_email);
                    $admin_mail = implode(',', $arrSetEmails);

                    //email sent by email template
                    $param = array();

                    $param['email_data'] = array('print_fancydate' => $print_fancydate, 'user_name' => $user_name, 'log_content' => $inserts['log_content'], 'signature' => $this->userdata['signature']);

                    $param['to_mail'] = $mgmt_mail . ',' . $lead_owner[0]['email'];
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
            } else {
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

    public function ajax_update_quote($lead_id = 0, $status, $log_status = '') {
        $this->load->model('user_model');
        $res = array();
        if ($lead_id != 0 && preg_match('/^[0-9]+$/', $lead_id) && preg_match('/^[0-9]+$/', $status) && $the_job = $this->asset_model->get_lead_all_detail($lead_id)) {
            if ($status > 0) {
                //Lead Status History - Start here
                $lead_det = $this->asset_model->get_lead_det($lead_id);
                $lead_his['lead_id'] = $lead_id;
                $lead_his['dateofchange'] = date('Y-m-d H:i:s');
                $lead_his['previous_status'] = $lead_det['lead_stage'];
                $lead_his['changed_status'] = $status;
                $lead_his['lead_status'] = $lead_det['lead_status'];
                $lead_his['modified_by'] = $this->userdata['userid'];
                //Lead Status History - End here
                //get the actual worth amt for the lead
                $actWorthAmt = $lead_det['actual_worth_amount'];

                // $update['lead_stage'] = $status;
                // $updt_lead_stg = $this->asset_model->updt_lead_stg_status($lead_id, $update);

                $ins['userid_fk'] = $this->userdata['userid'];
                $ins['jobid_fk'] = $lead_id;

                $disarray = $this->asset_model->get_user_data_by_id($lead_det['lead_assign']);

                $lead_owner = $this->asset_model->get_user_data_by_id($lead_det['belong_to']);
                // print_r($lead_owner);exit;

                $ins['date_created'] = date('Y-m-d H:i:s');

                $status_res = $this->asset_model->get_lead_stg_name($status);
                $ins['log_content'] = "Status Changed to:" . ' ' . urldecode($status_res['lead_stage_name']) . ' ' . 'Sucessfully for the Lead - ' . word_limiter($lead_det['lead_title'], 4) . ' ';

                $ins_email['log_content_email'] = "Status Changed to:" . ' ' . urldecode($status_res['lead_stage_name']) . ' ' . 'Sucessfully for the Lead - <a href=' . $this->config->item('base_url') . 'welcome/view_quote/' . $lead_id . '>' . word_limiter($lead_det['lead_title'], 4) . ' </a>';

                // insert the new log
                $insert_log = $this->asset_model->insert_row('logs', $ins);
                // insert the lead stage history
                $insert_lead_stage_his = $this->asset_model->insert_row('lead_stage_history', $lead_his);

                $user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
                $dis['date_created'] = date('Y-m-d H:i:s');
                $print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));


                $arrEmails = $this->config->item('crm');
                $arrSetEmails = $arrEmails['director_emails'];

                $admin_mail = implode(',', $arrSetEmails);

                //email sent by email template
                $param = array();

                $param['email_data'] = array('user_name' => $user_name, 'print_fancydate' => $print_fancydate, 'log_content_email' => $ins_email['log_content_email'], 'signature' => $this->userdata['signature']);

                $param['to_mail'] = $disarray[0]['email'] . ',' . $lead_owner[0]['email'];
                $param['bcc_mail'] = $admin_mail;
                $param['from_email'] = $user_data[0]['email'];
                $param['from_email_name'] = $user_name;
                $param['template_name'] = "Lead - Status Change Notification";
                $param['subject'] = "Lead - Status Change Notification";

                $this->email_template_model->sent_email($param);
            }
        }
        return true;
    }

    /**
     * Edits an existing item on a lead 
     */
    function ajax_edit_item() {

        $data = real_escape_array($this->input->post());
        $errors = '';
        if (trim($_POST['item_desc']) == '') {
            $errors[] = 'You must provide a description!';
        }
        if (trim($data['item_price']) != '' && !is_numeric($data['item_price'])) {
            $errors[] = 'Price can only be numeric values!';
        }
        if (!preg_match('/^[0-9]+$/', $data['itemid'])) {
            $errors[] = 'item ID must be numeric!';
        }
        if (is_array($errors)) {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        } else {
            $ins['item_desc'] = $_POST['item_desc'];
            $ins['item_price'] = $data['item_price'];
            // echo "<pre>"; print_r($ins); exit;

            $updt_item = $this->asset_model->update_row_item('items', $ins, $data['itemid']);
            $res = array();
            if ($updt_item) {
                $res['error'] = false;
            } else {
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
        if (!isset($data['itemid']) || !preg_match('/^[0-9]+$/', $data['itemid'])) {
            $errors[] = 'A valid item ID is not supplied';
        }
        if (is_array($errors)) {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        } else {
            $this->db->where('itemid', $data['itemid']);
            $this->db->select('jobid_fk');
            $q = $this->db->get($this->cfg['dbpref'] . 'items');
            if ($q->num_rows() > 0) {
                $lead_id = $q->result_array();
                $this->db->where('itemid', $data['itemid']);
                if ($this->db->delete($this->cfg['dbpref'] . 'items')) {
                    $this->ajax_quote_items($lead_id[0]['jobid_fk']);
                } else {
                    $json['error'] = true;
                    $json['errormsg'] = 'Database error! Item not deleted.';
                    echo json_encode($json);
                }
            } else {
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

    function ajax_save_item_order() {
        // $data = real_escape_array($this->input->post());
        $data = $_POST;

        $errors = '';
        if (!isset($data['qi']) || !is_array($data['qi'])) {
            $errors[] = 'Incorrect order format!';
        }

        if (is_array($errors)) {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        } else {
            $when = '';
            foreach ($data['qi'] as $k => $v) {
                $when .= "WHEN {$v} THEN {$k} \n";
            }
            $sql = "UPDATE {$this->cfg['dbpref']}items SET `item_position` = CASE `itemid`
                    {$when}
                    ELSE `item_position` END";

            if ($this->db->query($sql)) {
                $json['error'] = false;
                echo json_encode($json);
            } else {
                $json['error'] = true;
                $json['errormsg'] = 'Database error occured!';
                echo json_encode($json);
            }
        }
    }

    /**
     * uploading files - creating log
     */
    public function lead_fileupload_details($lead_id, $filename, $userid) {
        $lead_files['lead_files_name'] = $filename;
        $lead_files['lead_files_created_by'] = $userid;
        $lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
        $lead_files['lead_id'] = $lead_id;
        $insert_logs = $this->asset_model->insert_row('lead_files', $lead_files);

        $logs['jobid_fk'] = $lead_id;
        $logs['userid_fk'] = $this->userdata['userid'];
        $logs['date_created'] = date('Y-m-d H:i:s');
        $logs['log_content'] = $filename . ' is added.';
        $logs['attached_docs'] = $filename;
        $insert_logs = $this->asset_model->insert_row('logs', $logs);
    }

    /**
     * Deletes lead from the list
     */
    function delete_quote() {

        $id = isset($_POST['id']) ? $this->input->post('id') : 0;

        if ($this->session->userdata('delete') == 1) {
            if ($id > 0) {

                $lead_det = $this->asset_model->get_lead_det($id);
                $lead_assign_mail = $this->asset_model->get_user_data_by_id($lead_det['lead_assign']);
                $lead_owner = $this->asset_model->get_user_data_by_id($lead_det['belong_to']);

                $delete_job = $this->asset_model->delete_lead('leads', $id);
                if ($delete_job) {
                    $delete_item = $this->asset_model->delete_row('items', 'jobid_fk', $id);
                    $delete_log = $this->asset_model->delete_row('logs', 'jobid_fk', $id);
                    $delete_task = $this->asset_model->delete_row('tasks', 'jobid_fk', $id);
                    $delete_file = $this->asset_model->delete_row('lead_files', 'lead_id', $id);
                    $delete_query = $this->asset_model->delete_row('lead_query', 'lead_id', $id);

                    # Lead Delete Mail Notification
                    $ins['log_content'] = 'Lead Deleted Sucessfully - Lead ' . word_limiter($lead_det['lead_title'], 4) . ' ';

                    $user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
                    $dis['date_created'] = date('Y-m-d H:i:s');
                    $print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

                    $from = $this->userdata['email'];
                    $arrEmails = $this->config->item('crm');
                    $arrSetEmails = $arrEmails['director_emails'];
                    $mangement_email = $arrEmails['management_emails'];
                    $mgmt_mail = implode(',', $mangement_email);
                    $admin_mail = implode(',', $arrSetEmails);

                    //email sent by email template
                    $param = array();

                    $param['email_data'] = array('user_name' => $user_name, 'print_fancydate' => $print_fancydate, 'log_content' => $ins['log_content'], 'signature' => $this->userdata['signature']);

                    $param['to_mail'] = $mgmt_mail . ',' . $lead_assign_mail[0]['email'] . ',' . $lead_owner[0]['email'];
                    $param['bcc_mail'] = $admin_mail;
                    $param['from_email'] = $this->userdata['email'];
                    $param['from_email_name'] = $user_name;
                    $param['template_name'] = "Lead - Delete Notification Message";
                    $param['subject'] = "Lead Delete Notification";

                    $this->email_template_model->sent_email($param);

                    // $this->session->set_flashdata('confirm', array("Lead deleted from the system"));
                    $res['error'] = false;
                    $res['msg'] = 'Lead deleted from the system';

                    //redirect('welcome/quotation');
                } else {
                    // $this->session->set_flashdata('login_errors', array("Error in Deletion."));
                    // redirect('welcome/quotation');
                    $res['error'] = true;
                    $res['msg'] = 'Error in Deletion.';
                }
            } else {
                // $this->session->set_flashdata('login_errors', array("Quote does not exist or you may not be authorised to delete quotes."));
                // redirect('welcome/quotation');
                $res['error'] = true;
                $res['msg'] = 'Lead does not exist or you may not be authorised to delete the leads.';
            }
        } else {
            // $this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
            // redirect('welcome/quotation');
            $res['error'] = true;
            $res['msg'] = 'You have no rights to delete this lead.';
        }

        echo json_encode($res);
        exit;
    }

    public function update_project_info($project_id) {
        $res = array();
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

        $update['lead_title'] = $post_data['project_name'];
        $update['project_type'] = $post_data['timesheet_project_types'];
        $update['project_types'] = $post_data['project_types'];
        $update['department_id_fk'] = $post_data['department_id_fk'];

        if ($post_data['project_category'] == 1) {
            $project_center = explode('|', $post_data['project_center_value']);
            $update['project_center'] = $project_center[0];
            $code = substr($project_center[1], 0, 3);
        } else if ($post_data['project_category'] == 2) {
            $cost_center = explode('|', $post_data['cost_center_value']);
            $update['cost_center'] = $cost_center[0];
            $code = substr($cost_center[1], 0, 3);
        }

        $update['date_start'] = date('Y-m-d H:i:s', strtotime($post_data['date_start']));
        $update['date_due'] = date('Y-m-d H:i:s', strtotime($post_data['date_due']));
        $update['actual_worth_amount'] = $post_data['actual_worth_amount'];
        $update['project_category'] = $post_data['project_category'];
        $update['practice'] = $post_data['practice'];
        $update['rag_status'] = 3;
        $update['sow_status'] = $post_data['sow_status'];
        $update['customer_type'] = $post_data['customer_type'];
        $update['resource_type'] = $post_data['resource_type'];
        $update['modified_by'] = $this->userdata['userid'];
        $update['date_modified'] = date('Y-m-d H:i:s');

        $updt_project_info = $this->asset_model->update_row('leads', $update, $project_id);
        // echo $this->db->last_query(); die;
        if ($updt_project_info) {
            $res['result'] = 'ok';
        } else {
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
     * @confirm_project
     * set milestones
     * @param project_id
     */

    public function confirm_project($project_id) {
        $res = array();
        $ins = array();

        $lead_det = $this->asset_model->get_lead_det($project_id);

        if (($lead_det['project_category'] == 0) || ($lead_det['department_id_fk'] == 0) || ($lead_det['resource_type'] == 0) || ($lead_det['project_type'] == 0) || ($lead_det['date_start'] == "") || ($lead_det['date_due'] == "") || ($lead_det['actual_worth_amount'] == '0.00') || ($lead_det['practice'] == "")) {

            $res['error'] = true;
            $res['errortype'] = 1;
            if ($lead_det['department_id_fk'] == 0) {
                $res['errormsg'][] = 'Department is Required! <br />';
            }
            if ($lead_det['project_category'] == 0) {
                $res['errormsg'][] = 'Project Category is Required! <br />';
            }
            if ($lead_det['resource_type'] == 0) {
                $res['errormsg'][] = 'Resource Type is Required! <br />';
            }
            if ($lead_det['project_type'] == 0) {
                $res['errormsg'][] = 'Project billing type is Required! <br />';
            }
            if ($lead_det['project_types'] == 0) {
                $res['errormsg'][] = 'Project type is Required! <br />';
            }
            if ($lead_det['actual_worth_amount'] == '0.00') {
                $res['errormsg'][] = 'SOW Value should be greater than Zero! <br />';
            }
            if ($lead_det['date_start'] == "") {
                $res['errormsg'][] = 'SOW Start Date is required! <br />';
            }
            if ($lead_det['date_due'] == "") {
                $res['errormsg'][] = 'SOW End Date is required! <br />';
            }
            if ($lead_det['practice'] == "") {
                $res['errormsg'][] = 'Practice is required!';
            }

            echo json_encode($res);
            exit;
        }

        $project_category_code = '';
        $post_data = real_escape_array($this->input->post());

        if (!empty($post_data)) {
            $currency_type = $post_data['currency_type'];
            foreach ($post_data['project_milestone_name'] as $key => $value) {
                $ins[$key]['jobid_fk'] = $project_id;
                $ins[$key]['project_milestone_name'] = $value;
                $ins[$key]['expected_date'] = date('Y-m-d', strtotime($post_data['expected_date'][$key]));
                $ins[$key]['month_year'] = date('Y-m-d', strtotime($post_data['month_year'][$key]));
                $ins[$key]['amount'] = $post_data['amount'][$key];
            }
        }

        if (!empty($ins)) {
            foreach ($ins as $row) {
                $log = array();
                $exp_res = '';
                if (!empty($row['project_milestone_name'])) {
                    $exp_res = $this->asset_model->insert_row("expected_payments", $row);

                    if ($exp_res)
                        $res['result'] = 'ok';
                    else
                        $res['result'] = 'fail';

                    $log_detail = 'Project Milestone Name: ' . $row['project_milestone_name'] . '  Amount: ' . $currency_type . ' ' . $row['amount'] . ' Expected Date: ' . date('Y-m-d', strtotime($row['expected_date']));
                    $log['jobid_fk'] = $project_id;
                    $log['userid_fk'] = $this->userdata['userid'];
                    $log['date_created'] = date('Y-m-d H:i:s');
                    $log['log_content'] = $log_detail;
                    $log_res = $this->asset_model->insert_row("logs", $log);
                }
            }
        }

        // $customer = $this->customer_model->get_customer($lead_det['custid_fk']);
        $customer = $this->customer_model->get_lead_customer($lead_det['custid_fk']);

        // echo "<pre>"; print_r($customer); exit;

        $client_code = $customer[0]['client_code'];

        if ($client_code == '') {
            $client_code = $this->customer_model->update_client_code($customer[0]['company'], $customer[0]['companyid']);
        }

        // echo $client_code; die;
        //update client code to this lead - for project count
        $this->db->where('lead_id', $project_id);
        $this->db->update($this->cfg['dbpref'] . 'leads', array('client_code' => $client_code));

        if ($lead_det['project_category'] != 0) {
            switch ($lead_det['project_category']) {
                case 1:
                    $pc_code = $this->asset_model->get_data_by_id("profit_center", array('id' => $lead_det['project_center']));
                    $project_category_code = $pc_code['profit_center'];
                    break;
                case 2:
                    $cc_code = $this->asset_model->get_data_by_id("cost_center", array('id' => $lead_det['cost_center']));
                    $project_category_code = $cc_code['cost_center'];
                    break;
            }
        }

        $project_category_code = substr($project_category_code, 0, 3);

        $project_category_code = strtoupper($project_category_code);

        $month_year = date('my');

        $client_projects_count = $this->customer_model->get_records_by_num('leads', array('pjt_status !=' => 0, 'client_code' => $client_code));

        $total_projects = sprintf("%02d", (int) $client_projects_count + 1);

        $project_code = $project_category_code . '-' . $client_code . '-' . $total_projects . '-' . $month_year;

        $update['pjt_id'] = $project_code;
        $update['pjt_status'] = 1;
        $update['move_to_project_status'] = 1;
        $update['modified_by'] = $this->userdata['userid'];
        $update['date_modified'] = date('Y-m-d H:i:s');

        $updt_job = $this->asset_model->update_row('leads', $update, $project_id);
        $pjt_id = $this->customer_model->get_filed_id_by_name('leads', 'lead_id', $project_id, 'pjt_id');

        $this->customer_model->customer_update_isclient($customer[0]['companyid'], array('is_client' => 1));
        $this->customer_model->update_client_details_to_timesheet($client_code);

        // give default folder access to the assigned users.
        //$this->customer_model->create_cdefault_folders($project_id);
        //$this->customer_model->assign_default_folders($project_id);


        if ($updt_job) {
            $createTimesheet = $this->customer_model->update_project_details($pjt_id);
        }

        if ($createTimesheet) {
            $log_ins['userid_fk'] = $this->userdata['userid'];
            $log_ins['jobid_fk'] = $project_id;
            $log_ins['date_created'] = date('Y-m-d H:i:s');
            $log_ins['log_content'] = 'The Lead "' . $lead_det['lead_title'] . '" is Successfully Moved to Project.';
            $ins_email['log_content_email'] = 'The Lead <a href=' . $this->config->item('base_url') . 'project/view_project/' . $project_id . '> ' . word_limiter($lead_det['lead_title'], 4) . ' </a> is Successfully Moved to Project.';

            $lead_assign_mail = $this->asset_model->get_user_data_by_id($lead_det['lead_assign']);
            $lead_owner = $this->asset_model->get_user_data_by_id($lead_det['belong_to']);

            // insert the new log
            $insert_log = $this->asset_model->insert_row('logs', $log_ins);

            $user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
            $dis['date_created'] = date('Y-m-d H:i:s');
            $print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

            $arrEmails = $this->config->item('crm');
            $arrSetEmails = $arrEmails['director_emails'];

            $admin_mail = implode(',', $arrSetEmails);

            //email sent by email template
            $param = array();

            $param['email_data'] = array('user_name' => $user_name, 'print_fancydate' => $print_fancydate, 'log_content_email' => $ins_email['log_content_email'], 'signature' => $this->userdata['signature']);

            $param['to_mail'] = $lead_assign_mail[0]['email'] . ',' . $lead_owner[0]['email'];
            $param['bcc_mail'] = $admin_mail;
            $param['from_email'] = $this->userdata['email'];
            $param['from_email_name'] = $user_name;
            $param['template_name'] = "Lead to Project Change Notification";
            $param['subject'] = "Lead to Project Change Notification";

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
     * Exporting data(leads) to the excel
     */

    public function excelExport() {//echo'here';exit;
        ini_set('memory_limit', '-1');
        ob_clean();

        $from_date = null;
        $to_date = null;
        $stage = null;
        $customer = null;
        $service = null;
        $lead_src = null;
        $industry = null;
        $worth = null;
        $owner = null;
        $leadassignee = null;
        $regionname = null;
        $countryname = null;
        $statename = null;
        $locname = null;
        $lead_status = null;
        $lead_indi = null;
        $keyword = null;

        /* master */
        $services = $this->asset_model->get_lead_services();
        $sources = $this->asset_model->get_lead_sources();
        $entity = $this->asset_model->get_sales_divisions();
        $industry = $this->asset_model->get_industry();

        $leadServices = array();
        $leadSources = array();
        $leadEntity = array();
        $leadIndustry = array();

        if (!empty($services)) {
            foreach ($services as $ser)
                $leadServices[$ser['sid']] = $ser['services'];
        }
        if (!empty($services)) {
            foreach ($sources as $srcs)
                $leadSources[$srcs['lead_source_id']] = $srcs['lead_source_name'];
        }
        if (!empty($entity)) {
            foreach ($entity as $en)
                $leadEntity[$en['div_id']] = $en['division_name'];
        }
        if (!empty($industry)) {
            foreach ($industry as $ind)
                $leadIndustry[$ind['id']] = $ind['industry'];
        }

        $proposal_expect_end = null;
        if (isset($this->session->userdata) && $this->session->userdata('load_proposal_expect_end') == 1) {
            $proposal_expect_end = 'load_proposal_expect_end';
        }
        /* master */

        //$exporttoexcel = $this->session->userdata('excel_download');
        // echo '<pre>';print_r($this->session->userdata); die;
        $exporttoexcel = real_escape_array($this->input->post());
        // echo '<pre>qwqw';print_r($exporttoexcel); exit;
        if ($this->session->userdata("lead_search_by_default") || $this->session->userdata("lead_search_by_id")) {
            if ($this->session->userdata("lead_search_by_id")) {
                $wh_condn = array('search_id' => $this->session->userdata("lead_search_by_id"), 'search_for' => 1, 'user_id' => $this->userdata['userid']);
            } else {
                $wh_condn = array('search_for' => 1, 'user_id' => $this->userdata['userid'], 'is_default' => 1);
            }
            $get_rec = $this->asset_model->get_data_by_id('saved_search_critriea', $wh_condn);
            unset($get_rec['search_id']);
            unset($get_rec['search_for']);
            unset($get_rec['search_name']);
            unset($get_rec['user_id']);
            unset($get_rec['is_default']);
            if (!empty($get_rec))
                $exporttoexcel = real_escape_array($get_rec);
        }

        if ($this->session->userdata("search_keyword")) {
            $exporttoexcel['keyword'] = $this->session->userdata("search_keyword");
        } else {
            $exporttoexcel['keyword'] = '';
        }
        // echo '<pre>';print_r($this->session->userdata);print_r($exporttoexcel);exit;

        if (count($exporttoexcel) > 0) {

            $from_date = $exporttoexcel['from_date'];
            $to_date = $exporttoexcel['to_date'];
            $stage = $exporttoexcel['stage'];
            $customer = $exporttoexcel['customer'];
            $service = $exporttoexcel['service'];
            $lead_src = $exporttoexcel['lead_src'];
            $industry = $exporttoexcel['industry'];
            $worth = $exporttoexcel['worth'];
            $owner = $exporttoexcel['owner'];
            $leadassignee = $exporttoexcel['leadassignee'];
            $regionname = $exporttoexcel['regionname'];
            $countryname = $exporttoexcel['countryname'];
            $statename = $exporttoexcel['statename'];
            $locname = $exporttoexcel['locname'];
            $lead_status = $exporttoexcel['lead_status'];
            $lead_indi = $exporttoexcel['lead_indi'];
            $keyword = (!empty($exporttoexcel['keyword']) ? $exporttoexcel['keyword'] : '');
        }

        $filter_res = $this->asset_model->get_filter_results($from_date, $to_date, $stage, $customer, $service, $lead_src, $industry, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status, $lead_indi, $keyword, $proposal_expect_end);

        // echo "<pre>"; print_r($filter_res); exit;
        //load our new PHPExcel library
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('DashBoard');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Lead ID');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Lead Title');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Company Name');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Region');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Country');
        $this->excel->getActiveSheet()->setCellValue('F1', 'State');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Location');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Customer Name');
        $this->excel->getActiveSheet()->setCellValue('I1', 'Customer Position');
        $this->excel->getActiveSheet()->setCellValue('J1', 'Customer Phone');
        $this->excel->getActiveSheet()->setCellValue('K1', 'Customer Email');
        $this->excel->getActiveSheet()->setCellValue('L1', 'Customer Skype.');
        $this->excel->getActiveSheet()->setCellValue('M1', 'Lead Source');
        $this->excel->getActiveSheet()->setCellValue('N1', 'Lead Service');
        $this->excel->getActiveSheet()->setCellValue('O1', 'Lead Industry');
        $this->excel->getActiveSheet()->setCellValue('P1', 'Currency Type');
        $this->excel->getActiveSheet()->setCellValue('Q1', 'Expected Worth Amt');
        $this->excel->getActiveSheet()->setCellValue('R1', 'Entity');
        $this->excel->getActiveSheet()->setCellValue('S1', 'Lead Indicator');
        $this->excel->getActiveSheet()->setCellValue('T1', 'Proposal Expected Date');
        $this->excel->getActiveSheet()->setCellValue('U1', 'Lead Stage');
        $this->excel->getActiveSheet()->setCellValue('V1', 'Lead Status');
        $this->excel->getActiveSheet()->setCellValue('W1', 'Latest Comments');

        //change the font size
        $this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
        $i = 2;
        foreach ($filter_res as $excelarr) {
            $phone_no = (isset($excelarr['phone_1']) && (!empty($excelarr['phone_1']))) ? str_replace('=', '-', $excelarr['phone_1']) : '';
            $lead_source = isset($excelarr['lead_source']) ? $leadSources[$excelarr['lead_source']] : '';
            $lead_service = isset($excelarr['lead_service']) ? $leadServices[$excelarr['lead_service']] : '';
            $lead_industry = isset($excelarr['industry']) ? $leadIndustry[$excelarr['industry']] : '';
            $lead_entity = isset($excelarr['division']) ? $leadEntity[$excelarr['division']] : '';
            $this->excel->getActiveSheet()->setCellValue('A' . $i, $excelarr['lead_id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $i, stripslashes($excelarr['lead_title']));
            $this->excel->getActiveSheet()->setCellValue('C' . $i, stripslashes($excelarr['company']));
            $this->excel->getActiveSheet()->setCellValue('D' . $i, $excelarr['region_name']);
            $this->excel->getActiveSheet()->setCellValue('E' . $i, $excelarr['country_name']);
            $this->excel->getActiveSheet()->setCellValue('F' . $i, $excelarr['state_name']);
            $this->excel->getActiveSheet()->setCellValue('G' . $i, $excelarr['location_name']);
            $this->excel->getActiveSheet()->setCellValue('H' . $i, stripslashes($excelarr['customer_name']));
            $this->excel->getActiveSheet()->setCellValue('I' . $i, stripslashes($excelarr['position_title']));
            $this->excel->getActiveSheet()->setCellValue('J' . $i, $phone_no);
            $this->excel->getActiveSheet()->setCellValue('K' . $i, $excelarr['email_1']);
            $this->excel->getActiveSheet()->setCellValue('L' . $i, stripslashes($excelarr['skype_name']));
            $this->excel->getActiveSheet()->setCellValue('M' . $i, $lead_source);
            $this->excel->getActiveSheet()->setCellValue('N' . $i, $lead_service);
            $this->excel->getActiveSheet()->setCellValue('O' . $i, $lead_industry);
            $this->excel->getActiveSheet()->setCellValue('P' . $i, $excelarr['expect_worth_name']);
            $this->excel->getActiveSheet()->setCellValue('Q' . $i, $excelarr['expect_worth_amount']);
            $this->excel->getActiveSheet()->setCellValue('R' . $i, $lead_entity);
            $this->excel->getActiveSheet()->setCellValue('S' . $i, $excelarr['lead_indicator']);
            if ($excelarr['proposal_expected_date'] != null) {
                $this->excel->getActiveSheet()->setCellValue('T' . $i, date('d-m-Y', strtotime($excelarr['proposal_expected_date'])));
            }
            $this->excel->getActiveSheet()->setCellValue('U' . $i, $excelarr['lead_stage_name']);
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
            $this->excel->getActiveSheet()->setCellValue('V' . $i, $status);
            $last_log = $this->get_last_log($excelarr['lead_id']);
            $this->excel->getActiveSheet()->setCellValue('W' . $i, $last_log);
            $i++;
        }
        // $this->excel->getActiveSheet()->getStyle('W2:W'.$i)->getAlignment()->setWrapText(true);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1:W1')->getFont()->setBold(true);
        //merge cell A1 until D1
        //$this->excel->getActiveSheet()->mergeCells('A1:D1');
        //set aligment to center for that merged cell (A1 to D1)
        //Set width for cells
        /* $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
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
          $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(10); */

        foreach (range('A', 'V') as $columnID) {
            $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(80);
        //cell format
        // $this->excel->getActiveSheet()->getStyle('A2:A'.$i)->getNumberFormat()->setFormatCode('00000');

        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $filename = 'Lead Dashboard.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    function get_last_log($id) {
        $logs = '';
        $getLog = $this->asset_model->get_last_logs($id);
        if (!empty($getLog) && count($getLog) > 0) {
            $logs = stripslashes($getLog['log_content']);
            $logs = str_replace(array('\r\n', '\n', '<br />', '<br>', '<br/>'), "\r", $logs);
            $logs = str_replace('&#8230;', '', $logs);
        }

        return $logs;
    }

    /**
     * Adds a log to a job
     * based on post data
     *
     */
    function add_log() {
        $data_log = real_escape_array($this->input->post());
        $data_log['log_content'] = str_replace('\n', "", $data_log['log_content']);
        $ins['log_content'] = str_replace('\n', "", $data_log['log_content']);

        $break = 120;
        $data_log['log_content'] = implode(PHP_EOL, str_split($data_log['log_content'], $break));

        $res = array();
        $json = array();
        if (isset($data_log['lead_id']) && isset($data_log['userid']) && isset($data_log['log_content'])) {
            $this->load->helper('text');
            $this->load->helper('fix_text');

            $job_details = $this->asset_model->get_lead_det($data_log['lead_id']);

            if (count($job_details) > 0) {
                $user_data = $this->asset_model->get_user_data_by_id($data_log['userid']);

                $client = $this->asset_model->get_client_data_by_id($job_details['custid_fk']);

                $this->load->helper('url');

                $emails = trim($data_log['emailto'], ':');

                $successful = $received_by = '';

                if ($emails != '' || isset($data_log['email_to_customer'])) {
                    $emails = explode(':', $emails);
                    $mail_id = array();
                    foreach ($emails as $mail) {
                        $mail_id[] = str_replace('email-log-', '', $mail);
                    }

                    $data['user_accounts'] = array();
                    $this->db->where_in('userid', $mail_id);
                    $users = $this->db->get($this->cfg['dbpref'] . 'users');

                    if ($users->num_rows() > 0) {
                        $data['user_accounts'] = $users->result_array();
                    }
                    foreach ($data['user_accounts'] as $ua) {
                        # default email
                        $to_user_email = $ua['email'];

                        $send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name'], '');

                        $received_by .= $ua['first_name'] . ' ' . $ua['last_name'] . ', ';
                    }
                    $successful = 'This log has been emailed to:<br />';

                    $log_subject = "eSmart Notification - {$job_details['lead_title']} [ref#{$job_details['lead_id']}] {$client[0]['customer_name']} {$client[0]['last_name']} {$client[0]['company']}";

                    $param['email_data'] = array('first_name' => $client[0]['customer_name'], 'last_name' => $client[0]['last_name'], 'print_fancydate' => $print_fancydate, 'log_content' => $data_log['log_content'], 'received_by' => $received_by, 'signature' => $this->userdata['signature']);

                    $json['debug_info'] = '0';

                    if (isset($data_log['email_to_customer']) && isset($data_log['client_email_address']) && isset($data_log['client_full_name'])) {
                        // we're emailing the client, so remove the VCS log  prefix
                        $log_subject = preg_replace('/^eSmart Notification \- /', '', $log_subject);

                        for ($cei = 1; $cei < 5; $cei ++) {
                            if (isset($data_log['client_emails_' . $cei])) {
                                $send_to[] = array($data_log['client_emails_' . $cei], '');
                                $received_by .= $data_log['client_emails_' . $cei] . ', ';
                            }
                        }

                        if (isset($data_log['additional_client_emails']) && trim($data_log['additional_client_emails']) != '') {
                            $additional_client_emails = explode(',', trim($data_log['additional_client_emails'], ' ,'));
                            if (is_array($additional_client_emails))
                                foreach ($additional_client_emails as $aces) {
                                    $aces = trim($aces);
                                    if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $aces)) {
                                        $send_to[] = array($aces, '');
                                        $received_by .= $aces . ', ';
                                    }
                                }
                        }
                    } else {
                        $dis['date_created'] = date('Y-m-d H:i:s');
                        $print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

                        $param['email_data'] = array('first_name' => $client[0]['customer_name'], 'last_name' => $client[0]['last_name'], 'print_fancydate' => $print_fancydate, 'log_content' => $data_log['log_content'], 'received_by' => $received_by, 'signature' => $this->userdata['signature']);
                    }

                    foreach ($send_to as $recps) {
                        $arrRecs[] = $recps[0];
                    }
                    $senders = implode(',', $arrRecs);

                    $param['to_mail'] = $senders;
                    $param['from_email'] = $user_data[0]['email'];
                    $param['from_email_name'] = $user_data[0]['first_name'];
                    $param['template_name'] = "Lead Notificatiion Message";
                    $param['subject'] = $log_subject;

                    if ($this->email_template_model->sent_email($param)) {
                        $successful .= trim($received_by, ', ');
                    } else {
                        echo 'failure';
                    }


                    if (isset($full_file_path) && is_file($full_file_path))
                        unlink($full_file_path);

                    if ($successful == 'This log has been emailed to:<br />') {
                        $successful = '';
                    } else {
                        $successful = '<br /><br />' . $successful;
                    }
                }

                $ins['jobid_fk'] = $data_log['lead_id'];

                // use this to update the view status
                $ins['userid_fk'] = $upd['log_view_status'] = $data_log['userid'];

                $ins['date_created'] = date('Y-m-d H:i:s');
                $ins['log_content'] = $ins['log_content'] . $successful;

                $stick_class = '';
                if (isset($data_log['log_stickie'])) {
                    $ins['stickie'] = 1;
                    $stick_class = ' stickie';
                }

                if (isset($data_log['time_spent'])) {
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
            } else {
                $res['error'] = true;
                $res['errormsg'] = 'Post insert failed';
            }
        } else {
            // echo "{error:true, errormsg:'Invalid data supplied'}";
            $res['error'] = true;
            $res['errormsg'] = 'Invalid data supplied';
        }
        exit;
    }

    function request() {
        $data['results'] = array();
        if (isset($_POST['keyword']) && trim($_POST['keyword']) != '' && ($_POST['keyword'] != 'Lead No, Job Title, Name or Company')) {
            $keyword = $this->db->escape_str($_POST['keyword']);


            $sql = "SELECT * FROM '" . $this->cfg['dbpref'] . "'customers,'" . $this->cfg['dbpref'] . "'leads a WHERE `custid_fk` = `custid` AND lead_stage IN (1,4,2,5,3,7,6,9,10,11,12,13) AND ( `lead_title` LIKE '%{$keyword}%' OR `invoice_no` LIKE '%{$keyword}%' OR `custid_fk` IN ( SELECT `custid` FROM '" . $this->cfg['dbpref'] . "'customers WHERE CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%{$keyword}%' OR `first_name` LIKE '%{$keyword}%' OR `last_name` LIKE '%{$keyword}%' OR `company` LIKE '%{$keyword}%' ) ) ORDER BY `lead_stage`, `lead_title`";

            $resul = $this->asset_model->search_res($keyword);

            $q = $this->db->query($sql);
            //echo $this->db->last_query();
            if ($q->num_rows() > 0) {
                $result = $q->result_array();
                $i = 0;
                foreach ($this->cfg['lead_stage'] as $k => $v) {
                    while (isset($result[$i]) && $k == $result[$i]['lead_stage']) {
                        $data['results'][$k][] = $result[$i];
                        $i++;
                    }
                }

                if (count($result) == 1) {
                    $this->session->set_flashdata('header_messages', array('Only one result found! You have been redirect to the job.'));

                    //$status_type = (in_array($result[0]['lead_stage'], array(4,5,6,7,8,25))) ? 'invoice' : 'welcome';
                    //$status_type = (in_array($result[0]['lead_stage'])) ? 'invoice' : 'welcome';
                    //redirect($status_type . '/view_quote/' . $result[0]['lead_id']);
                    redirect('welcome/view_quote/' . $result[0]['lead_id'] . '/draft');
                } else { //echo "tljlj";
                    $this->session->set_flashdata('header_messages', array('Results found! You have been redirect to the job.'));
                    redirect('welcome/view_quote/' . $result[0]['lead_id'] . '/draft');
                }
            } else {
                $this->session->set_flashdata('header_messages', array('No record found!'));
                redirect('welcome/view_quote/' . $_POST['quoteid'] . '/draft');
            }
        }
    }

    /*
     * Lead from eNoah Website
     */

    public function add_lead() {
        //Create Customer 		
        if (sizeof($_POST) == 0) {
            echo 0;
            return false;
        }

        if (!empty($_POST['contact_us'])) {
            $ins_cus['first_name'] = $_POST['firstname'];
            $ins_cus['last_name'] = $_POST['lastname'];
            $ins_cus['company'] = $_POST['organization'];
            $ins_cus['position_title'] = $_POST['title'];
            $ins_cus['email_1'] = $_POST['email'];
            $ins_cus['email_2'] = $_POST['businessemail'];
            $ins_cus['phone_1'] = $_POST['phonenumber'];
            $ins_cus['add1_line1'] = $_POST['address'];
            $ins_cus['comments'] = $_POST['message'];
        } else {
            $ins_cus['first_name'] = $_POST['name'];
            $ins_cus['email_1'] = $_POST['email'];
            $ins_cus['company'] = $_POST['company'];
            $ins_cus['comments'] = $_POST['content'];
        }
        //insert customer and retrive last insert id
        $insert_id = $this->customer_model->get_customer_insert_id($ins_cus);
        //Create Jobs
        $ins['lead_title'] = 'Ask the Expert';
        $ins['custid_fk'] = $insert_id;
        $ins['lead_service'] = $_POST['lead_service'];
        //$ins['lead_source']       = '';
        $ins['lead_assign'] = 59;
        $ins['expect_worth_id'] = 5;
        $ins['expect_worth_amount'] = '0.00';
        $ins['belong_to'] = 59; // lead owner
        // $ins['division']         = $_POST['job_division'];
        $ins['date_created'] = date('Y-m-d H:i:s');
        $ins['date_modified'] = date('Y-m-d H:i:s');
        $ins['lead_stage'] = 1;
        // $ins['lead_indicator']   = $_POST['lead_indicator'];
        $ins['created_by'] = 59;
        $ins['modified_by'] = 59;
        $ins['lead_status'] = 1;
        $new_job_id = $this->asset_model->insert_job($ins);
        if (!empty($new_job_id)) {
            $invoice_no = (int) $new_job_id;
            $invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);
            $up_args = array('invoice_no' => $invoice_no);
            $this->asset_model->update_job($insert_id, $up_args);
            $this->quote_add_item($insert_id, "\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:", 0, '', FALSE);
        }
        echo 1;
        exit;
    }

    //For Countries
    public function loadCountrys() {
        $data = real_escape_array($this->input->post());
        $region_id = join(",", $data['region_id']);

        $output = '';
        $data = $this->asset_model->getcountry_list($region_id);
        if (!empty($data)) {
            foreach ($data as $country) {
                $output .= '<option value="' . $country['countryid'] . '">' . $country['country_name'] . '</option>';
            }
        } else {
            $output = '';
        }
        echo $output;
        exit;
    }

    //For States
    public function loadStates() {
        $data = real_escape_array($this->input->post());
        $cnt_id = join(",", $data['coun_id']);

        $output = '';
        $data = $this->asset_model->getstate_list($cnt_id);
        foreach ($data as $st) {
            $output .= '<option value="' . $st['stateid'] . '">' . $st['state_name'] . '</option>';
        }
        echo $output;
    }

    //For Locations
    public function loadLocns() {
        $data = real_escape_array($this->input->post());
        $loc_id = join(",", $data['st_id']);

        $output = '';
        $data = $this->asset_model->getlocation_list($loc_id);
        //print_r($data);
        foreach ($data as $st) {
            $output .= '<option value="' . $st['locationid'] . '">' . $st['location_name'] . '</option>';
        }
        echo $output;
    }

    //For Saving the search criteria
    public function save_search($type) {
        $post_data = real_escape_array($this->input->post());
        // echo "<pre>"; print_r($post_data); exit;
        $ins = array();

        $ins['search_for'] = $type;
        $ins['search_name'] = $post_data['search_name'];
        $ins['user_id'] = $this->userdata['userid'];
        $ins['is_default'] = $post_data['is_default'];
        $ins['stage'] = isset($post_data['stage']) ? $post_data['stage'] : '';
        $ins['customer'] = isset($post_data['customer']) ? $post_data['customer'] : '';
        $ins['service'] = isset($post_data['service']) ? $post_data['service'] : '';
        $ins['lead_src'] = isset($post_data['lead_src']) ? $post_data['lead_src'] : '';
        $ins['industry'] = isset($post_data['industry']) ? $post_data['industry'] : '';
        $ins['worth'] = isset($post_data['worth']) ? $post_data['worth'] : '';
        $ins['owner'] = isset($post_data['owner']) ? $post_data['owner'] : '';
        $ins['leadassignee'] = isset($post_data['leadassignee']) ? $post_data['leadassignee'] : '';
        $ins['regionname'] = isset($post_data['regionname']) ? $post_data['regionname'] : '';
        $ins['countryname'] = isset($post_data['countryname']) ? $post_data['countryname'] : '';
        $ins['statename'] = isset($post_data['statename']) ? $post_data['statename'] : '';
        $ins['locname'] = isset($post_data['locname']) ? $post_data['locname'] : '';
        $ins['lead_status'] = isset($post_data['lead_status']) ? $post_data['lead_status'] : '';
        $ins['lead_indi'] = isset($post_data['lead_indi']) ? $post_data['lead_indi'] : '';
        $ins['created_on'] = date('Y-m-d H:i:s');
        // echo "<pre>"; print_r($ins); exit;
        $last_ins_id = $this->asset_model->insert_row_return_id('saved_search_critriea', $ins);
        if ($last_ins_id) {
            if ($ins['is_default'] == 1) {
                $updt['is_default'] = 0;
                $this->db->where('search_id != ', $last_ins_id);
                $this->db->where('user_id', $this->userdata['userid']);
                $this->db->where('search_for', $type);
                $this->db->update($this->cfg['dbpref'] . 'saved_search_critriea', $updt);
            }

            $saved_search = $this->asset_model->get_saved_search($this->userdata['userid'], $search_for = $type);

            $result['res'] = true;
            $result['msg'] = 'Search Criteria Saved.';
            $result['search_div'] = '';
            $result['search_div'] .= '<li id="item_' . $last_ins_id . '" class="saved-search-res"><span><a href="javascript:void(0)" onclick="show_search_results(' . $last_ins_id . ')">' . $post_data['search_name'] . '</a></span>';
            $result['search_div'] .= '<span class="rd-set-default">';
            $result['search_div'] .= '<input type="radio" name="set_default_search" class="set_default_search" value="' . $last_ins_id . '" ';
            if ($ins['is_default'] == 1) {
                $result['search_div'] .= 'checked="checked"';
            }
            $result['search_div'] .= '/>';
            $result['search_div'] .= '</span>';
            $result['search_div'] .= '<span><a title="Set Default" href="javascript:void(0)" onclick="delete_save_search(' . $last_ins_id . ')" ><img alt="delete" src="assets/img/trash.png"></a></span></li>';

            $this->session->set_userdata("lead_search_by_default", 0);
            $this->session->set_userdata("lead_search_by_id", $last_ins_id);
            $this->session->set_userdata("lead_search_only", 0);
        } else {
            $result['res'] = false;
            $result['msg'] = 'Search Criteria cannot be Saved.';
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
        $wh_condn = array('search_for' => $type, 'user_id' => $this->userdata['userid']);

        $updt = $this->asset_model->update_records($tbl, $wh_condn, '', $up_arg = array('is_default' => 0));
        $updt_condn = $this->asset_model->update_records($tbl, $wh_condn = array('search_id' => $search_id), '', $up_arg = array('is_default' => 1));
        // $updt_condn = $this->asset_model->update_records($tbl,$wh_condn=array('search_id'=>$search_id),'',$up_arg=array('is_default'=>$is_def_val));
        // echo $this->db->last_query(); exit;

        if ($updt_condn) {
            $result['resu'] = 'updated';
        }

        echo json_encode($result);
        exit;
    }

    public function delete_save_search($search_id, $type) {

        $result = array();

        $tbl = 'saved_search_critriea';
        $wh_condn = array('search_for' => $type, 'search_id' => $search_id);

        if ($this->asset_model->delete_records($tbl, $wh_condn)) {
            $result['resu'] = 'deleted';
        }

        echo json_encode($result);
        exit;
    }

    public function importcsv() {
        $this->load->view('leads/leads_import_view');
    }

    /*  Import Load Function this fuction import customer list from CSV, XLS & XLSX files
     * 	Starts here Dated on 15-04-2016
     */

    function importleads() {
        $expect_worth = $this->asset_model->get_expect_worths();
        $sources = $this->asset_model->get_lead_sources();
        $services = $this->asset_model->get_lead_services();
        $industry = $this->asset_model->get_industry();
        $entity = $this->asset_model->get_sales_divisions();
        $stages = $this->asset_model->get_lead_stages();

        $leadExpectWorth = array();
        $leadSources = array();
        $leadServices = array();
        $leadIndustry = array();
        $leadEntity = array();
        $leadStages = array();
        $leadStatus = array('active' => 1, 'on hold' => 2, 'dropped' => 3);
        $empty_errors = array();
        $empty_source = array();
        $empty_service = array();
        $empty_industry = array();
        $empty_entity = array();
        $empty_stages = array();
        $empty_status = array();
        $no_access = array();

        if (!empty($expect_worth)) {
            foreach ($expect_worth as $ew)
                $leadExpectWorth[strtolower(trim($ew['expect_worth_name']))] = $ew['expect_worth_id'];
        }
        if (!empty($sources)) {
            foreach ($sources as $srcs)
                $leadSources[strtolower(trim($srcs['lead_source_name']))] = $srcs['lead_source_id'];
        }
        if (!empty($services)) {
            foreach ($services as $ser)
                $leadServices[strtolower(trim($ser['services']))] = $ser['sid'];
        }
        if (!empty($industry)) {
            foreach ($industry as $ind)
                $leadIndustry[strtolower(trim($ind['industry']))] = $ind['id'];
        }
        if (!empty($entity)) {
            foreach ($entity as $en)
                $leadEntity[strtolower(trim($en['division_name']))] = $en['div_id'];
        }
        if (!empty($stages)) {
            foreach ($stages as $st)
                $leadStages[strtolower(trim($st['lead_stage_name']))] = $st['lead_stage_id'];
        }

        $count = $updt_count = 0;
        $this->load->library('excel_read');

        $page['error'] = $page['msg'] = '';
        $objReader = new Excel_read();
        if (isset($_FILES['card_file']['tmp_name'])) {
            $strextension = explode(".", $_FILES['card_file']['name']);
            if ($strextension[1] == "csv" || $strextension[1] == "xls" || $strextension[1] == "xlsx" || $strextension[1] == "CSV") {
                $impt_data = $objReader->parseSpreadsheet($_FILES['card_file']['tmp_name']);

                for ($i = 2; $i <= count($impt_data); $i++) {

                    $ewdt = '';
                    if (!empty($impt_data[$i]['T'])) {
                        $ewdt = date('Y-m-d H:i:s', strtotime($impt_data[$i]['T']));
                    }
                    if (!empty($impt_data[$i]['B']) || !empty($impt_data[$i]['C']) || !empty($impt_data[$i]['D']) || !empty($impt_data[$i]['E']) || !empty($impt_data[$i]['F']) || !empty($impt_data[$i]['G']) || !empty($impt_data[$i]['H']) || !empty($impt_data[$i]['M']) || !empty($impt_data[$i]['N']) || !empty($impt_data[$i]['O']) || !empty($impt_data[$i]['P']) || !empty($impt_data[$i]['Q']) || !empty($impt_data[$i]['R']) || !empty($impt_data[$i]['S']) || !empty($impt_data[$i]['U']) || !empty($impt_data[$i]['V'])) {

                        $ldSource = $leadSources[strtolower($impt_data[$i]['M'])];
                        $ldService = $leadServices[strtolower($impt_data[$i]['N'])];
                        $ldIndustry = $leadIndustry[strtolower($impt_data[$i]['O'])];
                        $ldExpworth = $leadExpectWorth[strtolower($impt_data[$i]['P'])];
                        $ldEntity = $leadEntity[strtolower($impt_data[$i]['R'])];
                        $ldStages = $leadStages[strtolower($impt_data[$i]['U'])];
                        $ldStatus = $leadStatus[strtolower($impt_data[$i]['V'])];

                        // ECHO $ldSource ." ".$ldService ." ".$ldIndustry ." ".$ldExpworth ." ".$ldEntity ." ".$ldStages ." ".$ldStatus; EXIT;

                        if (!empty($ldSource) && !empty($ldService) && !empty($ldIndustry) && !empty($ldExpworth) && !empty($ldEntity) && !empty($ldStages) && !empty($ldStatus)) {


                            if (!empty($impt_data[$i]['K'])) {
                                $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
                                if (!preg_match($regex, $impt_data[$i]['K'])) {
                                    $email_invalid[] = $impt_data[$i]['K'] . "( " . $i . " row)";
                                }
                            }
                            $chk_leads = array();
                            //check leads exists or not in our crm by id
                            if (($impt_data[$i]['A'] != '') && is_numeric($impt_data[$i]['A'])) {
                                $chk_lead_condn = array('lead_id' => $impt_data[$i]['A']);
                                $chk_leads = $this->asset_model->get_data_by_id('leads', $chk_lead_condn);
                            }

                            if (isset($chk_leads) && !empty($chk_leads)) {
                                //**go for update**//
                                //updating customers
                                /* $updt_cus = array('first_name'=>$impt_data[$i]['C'],'company'=>$impt_data[$i]['E']);
                                  if($impt_data[$i]['D']!='')
                                  $updt_cus['last_name']=$impt_data[$i]['D'];
                                  if($impt_data[$i]['J']!='')
                                  $updt_cus['email_1']=$impt_data[$i]['J'];
                                  if($impt_data[$i]['K']!='')
                                  $updt_cus['phone_1']=$impt_data[$i]['K'];
                                  if($impt_data[$i]['L']!='')
                                  $updt_cus['phone_2']=$impt_data[$i]['L'];
                                  $this->db->where('custid', $chk_leads['custid_fk']);
                                  $this->db->update($this->cfg['dbpref'].'customers', $updt_cus); */

                                //get the customer company
                                $chk_cc_condn = array('custid' => $chk_leads['custid_fk']);
                                $chk_cc = $this->asset_model->get_data_by_id('customers', $chk_cc_condn);
                                // echo $this->db->last_query(); die;
                                // echo "<pre>"; print_r($chk_cc); die;

                                if (!empty($impt_data[$i]['D']))
                                    $strreg = $this->asset_model->get_rscl('', '', 'region', strtolower($impt_data[$i]['D']));
                                if (!empty($impt_data[$i]['E']))
                                    $strcunt = $this->asset_model->get_rscl($strreg, 'regionid', 'country', strtolower($impt_data[$i]['E']));
                                if (!empty($impt_data[$i]['F']))
                                    $strstate = $this->asset_model->get_rscl($strcunt, 'countryid', 'state', strtolower($impt_data[$i]['F']));
                                if (!empty($impt_data[$i]['G']))
                                    $strlid = $this->asset_model->get_rscl($strstate, 'stateid', 'location', strtolower($impt_data[$i]['G']));

                                //update the customer company
                                $updt_cmp = array();
                                $updt_cmp['company'] = $impt_data[$i]['C'];
                                $updt_cmp['add1_region'] = $strreg;
                                $updt_cmp['add1_country'] = $strcunt;
                                $updt_cmp['add1_state'] = $strstate;
                                $updt_cmp['add1_location'] = $strlid;

                                $this->db->where('companyid', $chk_cc['company_id']);
                                $this->db->update($this->cfg['dbpref'] . 'customers_company', $updt_cmp);
                                // echo $this->db->last_query(); die;
                                //updating the contacts
                                $updt_cus = array();
                                $updt_cus = array('customer_name' => $impt_data[$i]['H']);
                                if ($impt_data[$i]['I'] != '')
                                    $updt_cus = array('position_title' => $impt_data[$i]['I']);
                                if ($impt_data[$i]['J'] != '')
                                    $updt_cus = array('phone_1' => $impt_data[$i]['J']);
                                if ($impt_data[$i]['K'] != '')
                                    $updt_cus = array('email_1' => $impt_data[$i]['K']);
                                if ($impt_data[$i]['L'] != '')
                                    $updt_cus = array('skype_name' => $impt_data[$i]['L']);
                                $updt_cus = array('company_id' => $chk_cc['company_id']);

                                $this->db->where('custid', $chk_leads['custid_fk']);
                                $this->db->update($this->cfg['dbpref'] . 'customers', $updt_cus);
                                // echo $this->db->last_query(); die;
                                //updating leads
                                $updt_leads['lead_title'] = $impt_data[$i]['B'];
                                $updt_leads['lead_source'] = $ldSource;
                                $updt_leads['lead_service'] = $ldService;
                                $updt_leads['industry'] = $ldIndustry;
                                $updt_leads['expect_worth_id'] = $ldExpworth;
                                $updt_leads['expect_worth_amount'] = $impt_data[$i]['Q'];
                                $updt_leads['division'] = $ldEntity;
                                $updt_leads['lead_indicator'] = strtoupper($impt_data[$i]['S']);
                                $updt_leads['lead_assign'] = $this->userdata['userid'];
                                $updt_leads['lead_stage'] = $ldStages;
                                $updt_leads['lead_status'] = $ldStatus;
                                $updt_leads['date_modified'] = date('Y-m-d H:i:s');
                                $updt_leads['proposal_expected_date'] = ($ewdt != '') ? $ewdt : date('Y-m-d H:i:s');

                                $lead_id = $chk_leads['lead_id'];

                                if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2 || $this->userdata['userid'] == $chk_leads['belong_to'] || $this->userdata['userid'] == $chk_leads['lead_assign']) {

                                    $this->db->where('lead_id', $lead_id);
                                    $this->db->update($this->cfg['dbpref'] . 'leads', $updt_leads);

                                    //log for lead stages
                                    $cur_stage = empty($chk_leads['lead_stage']) ? 1 : $chk_leads['lead_stage'];
                                    $new_stage = $ldStages;
                                    if ($cur_stage != $new_stage) {
                                        $stgHist = array();
                                        $stgHist['lead_id'] = $lead_id;
                                        $stgHist['dateofchange'] = date('Y-m-d H:i:s');
                                        $stgHist['previous_status'] = $cur_stage;
                                        $stgHist['changed_status'] = $new_stage;
                                        $stgHist['lead_status'] = 1;
                                        $stgHist['modified_by'] = $this->userdata['userid'];
                                        $insStgHis = $this->asset_model->insert_row('lead_stage_history', $stgHist);
                                    }

                                    //log for lead status
                                    $cur_status = empty($chk_leads['lead_status']) ? 1 : $chk_leads['lead_status'];
                                    $new_status = $ldStatus;
                                    if ($cur_status != $new_status) {
                                        $statHist = array();
                                        $statHist['lead_id'] = $lead_id;
                                        $statHist['dateofchange'] = date('Y-m-d H:i:s');
                                        $statHist['changed_status'] = $new_status;
                                        $statHist['modified_by'] = $this->userdata['userid'];
                                        $insStatHis = $this->asset_model->insert_row('lead_status_history', $statHist);
                                    }

                                    $updt_count = $updt_count + 1;

                                    //For Logs
                                    if (!empty($impt_data[$i]['W'])) {
                                        $log_ins['jobid_fk'] = $lead_id;
                                        $log_ins['userid_fk'] = $this->userdata['userid'];
                                        // $log_ins['date_created'] = isset($impt_data[$i]['S']) ? date('Y-m-d H:i:s', strtotime($impt_data[$i]['S'])) : date('Y-m-d H:i:s');
                                        $log_ins['date_created'] = date('Y-m-d H:i:s');
                                        $log_ins['log_content'] = $this->db->escape_str($impt_data[$i]['W']);
                                        $insert_log = $this->asset_model->insert_row('logs', $log_ins);
                                    }
                                } else {

                                    $no_access[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                                }
                            } else {
                                //**go for insert**//
                                //Region
                                if (!empty($impt_data[$i]['D']))
                                    $strreg = $this->asset_model->get_rscl('', '', 'region', strtolower($impt_data[$i]['D']));
                                //Country
                                if (!empty($impt_data[$i]['E']))
                                    $strcunt = $this->asset_model->get_rscl($strreg, 'regionid', 'country', strtolower($impt_data[$i]['E']));
                                //State
                                if (!empty($impt_data[$i]['F']))
                                    $strstate = $this->asset_model->get_rscl($strcunt, 'countryid', 'state', strtolower($impt_data[$i]['F']));
                                //Location
                                if (!empty($impt_data[$i]['G']))
                                    $strlid = $this->asset_model->get_rscl($strstate, 'stateid', 'location', strtolower($impt_data[$i]['G']));

                                if ($strreg != 'no_id' && $strcunt != 'no_id' && $strstate != 'no_id' && $strlid != 'no_id') {
                                    $compid = $this->chk_customers($strreg, $strcunt, $strstate, $strlid, $impt_data[$i]['C']);
                                    $custid = 0;
                                    if ($compid == 'no_customer') {
                                        $compid = $this->create_customer($strreg, $strcunt, $strstate, $strlid, $impt_data[$i]['C']);

                                        $customer_name = $impt_data[$i]['H'];
                                        $position = ($impt_data[$i]['I'] != "") ? $impt_data[$i]['I'] : '';
                                        $phone_1 = ($impt_data[$i]['J']) ? $impt_data[$i]['J'] : '';
                                        $email_1 = ($impt_data[$i]['K']) ? $impt_data[$i]['K'] : '';
                                        $skype_name = ($impt_data[$i]['L']) ? $impt_data[$i]['L'] : '';
                                        // $updt_cus = array('company_id'=>$chk_leads['company_id']);

                                        $custid = $this->create_contacts($compid, $customer_name, $position, $phone_1, $email_1, $skype_name);
                                    } else {
                                        // $custid = $cust_res;
                                        //check contact
                                        $customer_name = $impt_data[$i]['H'];
                                        $position = ($impt_data[$i]['I'] != "") ? $impt_data[$i]['I'] : '';
                                        $phone_1 = ($impt_data[$i]['J']) ? $impt_data[$i]['J'] : '';
                                        $email_1 = ($impt_data[$i]['K']) ? $impt_data[$i]['K'] : '';
                                        $skype_name = ($impt_data[$i]['L']) ? $impt_data[$i]['L'] : '';
                                        $custid = $this->chk_contacts($compid, $customer_name, $email_1);
                                        if ($custid == 'no_contacts') {
                                            $custid = $this->create_contacts($compid, $customer_name, $position, $phone_1, $email_1, $skype_name);
                                        }
                                    }
                                } else {
                                    //Region
                                    if (!empty($impt_data[$i]['D']))
                                        $strreg = $this->asset_model->get_rscl_return_id('', '', 'region', strtolower($impt_data[$i]['D']));
                                    //Country
                                    if (!empty($impt_data[$i]['E']))
                                        $strcunt = $this->asset_model->get_rscl_return_id($strreg, 'regionid', 'country', strtolower($impt_data[$i]['E']));
                                    //State
                                    if (!empty($impt_data[$i]['F']))
                                        $strstate = $this->asset_model->get_rscl_return_id($strcunt, 'countryid', 'state', strtolower($impt_data[$i]['F']));
                                    //Location
                                    if (!empty($impt_data[$i]['G']))
                                        $strlid = $this->asset_model->get_rscl_return_id($strstate, 'stateid', 'location', strtolower($impt_data[$i]['G']));
                                    $custid = 0;
                                    // $custid = $this->create_customer($strreg,$strcunt,$strstate,$strlid,$impt_data[$i]['C'],$impt_data[$i]['D'],$impt_data[$i]['E'],$impt_data[$i]['J'],$impt_data[$i]['K'],$impt_data[$i]['L']);
                                    $compid = $this->create_customer($strreg, $strcunt, $strstate, $strlid, $impt_data[$i]['C']);

                                    $customer_name = $impt_data[$i]['H'];
                                    $position = ($impt_data[$i]['I'] != "") ? $impt_data[$i]['I'] : '';
                                    $phone_1 = ($impt_data[$i]['J']) ? $impt_data[$i]['J'] : '';
                                    $email_1 = ($impt_data[$i]['K']) ? $impt_data[$i]['K'] : '';
                                    $skype_name = ($impt_data[$i]['L']) ? $impt_data[$i]['L'] : '';
                                    // $updt_cus = array('company_id'=>$chk_leads['company_id']);

                                    $custid = $this->create_contacts($compid, $customer_name, $position, $phone_1, $email_1, $skype_name);
                                }

                                if ($custid != 0) {
                                    //insert leads here.
                                    $ins_leads = array();
                                    $ins_leads['custid_fk'] = $custid;
                                    $ins_leads['lead_title'] = $impt_data[$i]['B'] . ' - ' . $impt_data[$i]['C'];
                                    $ins_leads['lead_source'] = $ldSource;
                                    $ins_leads['lead_service'] = $ldService;
                                    $ins_leads['industry'] = $ldIndustry;
                                    $ins_leads['expect_worth_id'] = $ldExpworth;
                                    $ins_leads['division'] = $ldEntity;
                                    $ins_leads['expect_worth_amount'] = $impt_data[$i]['Q'];
                                    $ins_leads['lead_indicator'] = strtoupper($impt_data[$i]['S']);
                                    $ins_leads['lead_stage'] = 1;
                                    $ins_leads['lead_assign'] = $this->userdata['userid'];
                                    $ins_leads['date_created'] = date('Y-m-d H:i:s');
                                    $ins_leads['date_modified'] = date('Y-m-d H:i:s');
                                    $ins_leads['proposal_expected_date'] = isset($ewdt) ? $ewdt : date('Y-m-d H:i:s');
                                    $ins_leads['created_by'] = $this->userdata['userid'];
                                    $ins_leads['modified_by'] = $this->userdata['userid'];
                                    $ins_leads['belong_to'] = $this->userdata['userid'];
                                    $this->db->insert($this->cfg['dbpref'] . 'leads', $ins_leads);
                                    $new_id = $this->db->insert_id();
                                    if ($new_id) {
                                        $lead_id = (int) $new_id;
                                        $invoice_no = (int) $new_id;
                                        $invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);
                                        $updt_arr = array('invoice_no' => $invoice_no);
                                        $this->db->where('lead_id', $new_id);
                                        $this->db->update($this->cfg['dbpref'] . 'leads', $updt_arr);

                                        //log - lead_stage_history
                                        $lead_hist['lead_id'] = $new_id;
                                        $lead_hist['dateofchange'] = date('Y-m-d H:i:s');
                                        $lead_hist['previous_status'] = 1;
                                        $lead_hist['changed_status'] = 1;
                                        $lead_hist['lead_status'] = 1;
                                        $lead_hist['modified_by'] = $this->userdata['userid'];
                                        $insert_lead_stg_his = $this->asset_model->insert_row('lead_stage_history', $lead_hist);

                                        //log - lead_status_history
                                        $lead_stat_hist['lead_id'] = $new_id;
                                        $lead_stat_hist['dateofchange'] = date('Y-m-d H:i:s');
                                        $lead_stat_hist['changed_status'] = 1;
                                        $lead_stat_hist['modified_by'] = $this->userdata['userid'];
                                        $insert_lead_stat_his = $this->asset_model->insert_row('lead_status_history', $lead_stat_hist);

                                        //folder name entry start
                                        //creating files folder name
                                        $f_dir = UPLOAD_PATH . 'files/';
                                        if (!is_dir($f_dir)) {
                                            mkdir($f_dir);
                                            chmod($f_dir, 0777);
                                        }
                                        //creating lead_id folder name
                                        $f_dir = $f_dir . $new_id;
                                        if (!is_dir($f_dir)) {
                                            mkdir($f_dir);
                                            chmod($f_dir, 0777);
                                        }
                                        $this->asset_model->insert_default_folder($new_id, $ins_leads['lead_title']);

                                        //For Logs
                                        if (!empty($impt_data[$i]['W'])) {
                                            $log_ins['jobid_fk'] = $lead_id;
                                            $log_ins['userid_fk'] = $this->userdata['userid'];
                                            // $log_ins['date_created'] = isset($impt_data[$i]['S']) ? date('Y-m-d H:i:s', strtotime($impt_data[$i]['S'])) : date('Y-m-d H:i:s');
                                            $log_ins['date_created'] = date('Y-m-d H:i:s');
                                            $log_ins['log_content'] = $this->db->escape_str($impt_data[$i]['W']);
                                            $insert_log = $this->asset_model->insert_row('logs', $log_ins);
                                        }
                                    }
                                    $count = $count + 1;
                                }
                            }
                        } else {

                            if (empty($ldService)) {
                                $empty_service[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                            }
                            if (empty($ldSource)) {
                                $empty_source[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                            }
                            if (empty($ldExpworth)) {
                                $empty_currency[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                            }
                            if (empty($ldEntity)) {
                                $empty_entity[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                            }
                            if (empty($ldStages)) {
                                $empty_stages[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                            }
                            if (empty($ldIndustry)) {
                                $empty_industry[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                            }
                            if (empty($ldStatus)) {
                                $empty_status[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                            }
                        }
                    } else {

                        if (!empty($impt_data[$i]['C']))
                            $empty_errors[] = $impt_data[$i]['C'] . "( " . $i . " row)";
                    }
                } //for loop
                $data['invalidemail'] = $email_invalid;
                $data['updated_leads'] = $updt_count;
                $data['succcount'] = $count;
                $data['empty_errors'] = $empty_errors;
                $data['empty_service'] = $empty_service;
                $data['empty_source'] = $empty_source;
                $data['empty_currency'] = $empty_currency;
                $data['empty_entity'] = $empty_entity;
                $data['empty_industry'] = $empty_industry;
                $data['empty_status'] = $empty_status;
                $data['empty_stages'] = $empty_stages;
                $data['no_access'] = $no_access;
                // echo "<pre>"; print_r($data); exit;
                $this->load->view('leads/success_import_view', $data);
            } else {
                $page['error'] = '<p class="error">Please Upload CSV, XLS File only!</p>';
                $this->load->view('leads/leads_import_view', $page);
            }
        } else {
            $page['error'] = '<p class="error">Please Upload the file!</p>';
            $this->load->view('leads/leads_import_view', $page);
        }
        /* Ends here */
    }

    function chk_customers($rid, $cid, $sid, $lid, $companyname) {
        $res = 'no_customer';
        $whr_cond = array('add1_region' => $rid, 'add1_country' => $cid, 'add1_state' => $sid, 'add1_location' => $lid, 'company' => $companyname);
        $results = $this->db->get_where($this->cfg['dbpref'] . 'customers_company', $whr_cond)->row_array();
        // echo $this->db->last_query(); exit;
        if (!empty($results)) {
            $res = $results['companyid'];
        }
        return $res;
    }

    function chk_contacts($cust_res, $customer_name, $email_1 = false) {
        $res = 'no_contacts';
        $whr_cond = array('company_id' => $cust_res, 'customer_name' => $customer_name);
        if ($email_1 != '')
            $whr_cond['email_1'] = $email_1;
        $results = $this->db->get_where($this->cfg['dbpref'] . 'customers', $whr_cond)->row_array();
        // echo $this->db->last_query(); exit;
        if (!empty($results)) {
            $res = $results['custid'];
        }
        return $res;
    }

    function create_customer($rid, $cid, $sid, $lid, $companyname) {
        $res = 0;
        $ins = array('add1_region' => $rid, 'add1_country' => $cid, 'add1_state' => $sid, 'add1_location' => $lid, 'company' => $companyname);

        $this->db->insert($this->cfg['dbpref'] . 'customers_company', $ins);
        $res = $this->db->insert_id();
        return $res;
    }

    function create_contacts($company_id, $customer_name, $position = false, $phone_1 = false, $email_1 = false, $skype_name = false) {
        $res = 0;
        $ins = array('company_id' => $company_id, 'customer_name' => $customer_name);
        if ($position != '') {
            $ins['position_title'] = $position;
        }
        if ($phone_1 != '') {
            $ins['phone_1'] = $phone_1;
        }
        if ($email != '') {
            $ins['email_1'] = $email_1;
        }
        if ($skype_name != '') {
            $ins['skype_name'] = $skype_name;
        }
        $this->db->insert($this->cfg['dbpref'] . 'customers', $ins);
        $res = $this->db->insert_id();
        return $res;
    }

    public function getCustomers($id) {
        $result = $this->asset_model->get_lead_detail($id);

        $data['quote_data'] = $result[0];
        $data['chge_access'] = 0;
        // $this->userdata
        // echo "<pre>"; print_r($data['quote_data']['companyid']); die;
        if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
            $data['chge_access'] = 1;
        } else {
            $data['chge_access'] = $this->asset_model->get_access($id, $this->userdata['userid']);
        }
        //get customers & company
        $data['company_det'] = $this->asset_model->get_company_det($data['quote_data']['companyid']);
        $data['contact_det'] = $this->asset_model->get_contact_det($data['quote_data']['companyid']);

        $this->load->view('leads/load_customer_det', $data);
    }

    public function update_customer() {
        $updt = real_escape_array($this->input->post());

        $data['error'] = FALSE;

        if ($updt['customer_id'] != $updt['customer_id_old']) {
            $inser['log_content'] = "Customer has changed from ' " . $updt['customer_company_name_old'] . " ' to ' " . $updt['customer_company_name'] . " '";
            $inser['jobid_fk'] = $updt['lead_id'];
            $inser['userid_fk'] = $this->userdata['userid'];
            $insert_log = $this->asset_model->insert_row('logs', $inser);
        }

        if (($updt['customer_id'] == "") or ( $updt['lead_id'] == "")) {
            $data['error'] = 'Error in Updation';
        } else {
            $wh_condn = array('lead_id' => $updt['lead_id']);
            $updata = array('custid_fk' => $updt['customer_id']);

            $this->db->where($wh_condn);
            $updt_id = $this->db->update($this->cfg['dbpref'] . 'leads', $updata);

            if (!$updt_id) {
                $data['error'] = 'Error in Updation';
            }
        }
        // echo "<pre>"; print_r($data); die;
        echo json_encode($data);
    }

    public function closed_opportunities() {
        $data = array();
        $cusId = '';
        $cusId = $this->level_restriction();

        $filter = $this->input->post();
        // echo "<pre>"; print_r($cusId); die;

        $data['customers'] = $this->asset_model->get_customers();
        $data['lead_owner'] = $this->asset_model->get_users();
        $data['regions'] = $this->regionsettings_model->region_list();
        $data['services'] = $this->asset_model->get_lead_services();
        $data['sources'] = $this->asset_model->get_lead_sources();
        $data['industry'] = $this->asset_model->get_industry();

        $data['closed_jobs'] = $this->asset_model->getClosedJobids($cusId, $filter);

        if ($this->input->post("filter") != "")
            $this->load->view('leads/closed_opportunities_view_grid', $data);
        else
            $this->load->view('leads/closed_opportunities_view', $data);
    }

    public function excelExportClosedLeads() {
        /* master */
        /* $services = $this->asset_model->get_lead_services();
          $sources  = $this->asset_model->get_lead_sources();
          $entity	  = $this->asset_model->get_sales_divisions();
          $industry = $this->asset_model->get_industry();

          $leadServices = array();
          $leadSources  = array();
          $leadEntity   = array();
          $leadIndustry = array();

          if(!empty($services)) {
          foreach($services as $ser)
          $leadServices[$ser['sid']] = $ser['services'];
          }
          if(!empty($services)) {
          foreach($sources as $srcs)
          $leadSources[$srcs['lead_source_id']] = $srcs['lead_source_name'];
          }
          if(!empty($entity)) {
          foreach($entity as $en)
          $leadEntity[$en['div_id']] = $en['division_name'];
          }
          if(!empty($industry)) {
          foreach($industry as $ind)
          $leadIndustry[$ind['id']] = $ind['industry'];
          } */
        /* master */

        $filter = real_escape_array($this->input->post());

        $cusId = $this->level_restriction();

        $filter_res = $this->asset_model->getClosedJobids($cusId, $filter);

        // echo "<pre>"; print_r($filter_res); exit;
        //load our new PHPExcel library
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Closed Leads');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Project No.');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Project Title');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Customer');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Currency Type');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Actual Worth Amount');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Region');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Lead Owner');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Lead Assigned To');
        $this->excel->getActiveSheet()->setCellValue('I1', 'Status');

        //change the font size
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setSize(10);
        $i = 2;
        foreach ($filter_res as $excelarr) {
            // $lead_source   = isset($excelarr['lead_source'])?$leadSources[$excelarr['lead_source']]:'';
            // $lead_service  = isset($excelarr['lead_service'])?$leadServices[$excelarr['lead_service']]:'';
            // $lead_industry = isset($excelarr['industry'])?$leadIndustry[$excelarr['industry']]:'';
            // $lead_entity   = isset($excelarr['division'])?$leadEntity[$excelarr['division']]:'';

            $this->excel->getActiveSheet()->setCellValue('A' . $i, $excelarr['invoice_no']);
            $this->excel->getActiveSheet()->setCellValue('B' . $i, stripslashes($excelarr['lead_title']));
            $this->excel->getActiveSheet()->setCellValue('C' . $i, stripslashes($excelarr['company'] . ' - ' . stripslashes($excelarr['customer_name'])));
            $this->excel->getActiveSheet()->setCellValue('D' . $i, $excelarr['expect_worth_name']);
            $this->excel->getActiveSheet()->setCellValue('E' . $i, $excelarr['actual_worth_amount']);
            $this->excel->getActiveSheet()->setCellValue('F' . $i, $excelarr['region_name']);
            $this->excel->getActiveSheet()->setCellValue('G' . $i, $excelarr['ubfn'] . ' ' . $excelarr['ubln']);
            $this->excel->getActiveSheet()->setCellValue('H' . $i, $excelarr['ufname'] . ' ' . $excelarr['ulname']);

            switch ($excelarr['pjt_status']) {
                case 1:
                    $status = 'Project In Progress';
                    break;
                case 2:
                    $status = 'Project Completed';
                    break;
                case 3:
                    $status = 'Inactive';
                    break;
                case 4:
                    $status = 'Project Onhold';
                    break;
            }
            $this->excel->getActiveSheet()->setCellValue('I' . $i, $status);
            $i++;
        }
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);

        foreach (range('A', 'I') as $columnID) {
            $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        $this->excel->getActiveSheet()->getStyle('A2:A' . $i)->getNumberFormat()->setFormatCode('00000');

        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $filename = 'closed_leads.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    public function level_restriction() {
        $userdata = $this->session->userdata('logged_in_user');
        // if (($userdata['role_id'] == 1 && $userdata['level'] == 1) || ($userdata['role_id'] == 2 && $userdata['level'] == 1)) {
        if ($userdata['level'] == 1) {
            $cusId = '';
        } else {
            $cusIds = array();
            $reg = array();
            $cou = array();
            $ste = array();
            $loc = array();
            $cusIds[] = 0;
            switch ($userdata['level']) {
                case 2:
                    $regions = $this->dashboard_model->getRegions($userdata['userid'], $userdata['level']); //Get the Regions based on Level
                    foreach ($regions as $rgid) {
                        $reg[] = $rgid['region_id'];
                    }
                    $CustomersId = $this->dashboard_model->getCustomersIds($reg); //Get the Customer id based on Regions
                    foreach ($CustomersId as $cus_id) {
                        $cusIds[] = $cus_id['companyid'];
                    }
                    $cusId = $cusIds;
                    break;
                case 3:
                    $countries = $this->dashboard_model->getCountries($userdata['userid'], $userdata['level']); //Get the Countries based on Level
                    foreach ($countries as $couid) {
                        $cou[] = $couid['country_id'];
                    }
                    $CustomersId = $this->dashboard_model->getCustomersIds($reg, $cou); //Get the Customer id based on Regions & Countries
                    foreach ($CustomersId as $cus_id) {
                        $cusIds[] = $cus_id['companyid'];
                    }
                    $cusId = $cusIds;
                    break;
                case 4:
                    $states = $this->dashboard_model->getStates($userdata['userid'], $userdata['level']); //Get the States based on Level
                    foreach ($states as $steid) {
                        $ste[] = $steid['state_id'];
                    }
                    $CustomersId = $this->dashboard_model->getCustomersIds($reg, $cou, $ste); //Get the Customer id based on Regions & Countries
                    foreach ($CustomersId as $cus_id) {
                        $cusIds[] = $cus_id['companyid'];
                    }
                    $cusId = $cusIds;
                    break;
                case 5:
                    $locations = $this->dashboard_model->getLocations($userdata['userid'], $userdata['level']); //Get the Locations based on Level
                    foreach ($locations as $locid) {
                        $loc[] = $locid['location_id'];
                    }
                    $CustomersId = $this->dashboard_model->getCustomersIds($reg, $cou, $ste, $loc); //Get the Customer id based on Regions & Countries
                    foreach ($CustomersId as $cus_id) {
                        $cusIds[] = $cus_id['companyid'];
                    }
                    $cusId = $cusIds;
                    break;
            }
        }
        return $cusId;
    }

    public function get_base_currency() {
        $post_data = $this->input->post();
        $result = $this->asset_model->get_record('sales_divisions', $wh_condn = array('div_id' => $post_data['division']));
        $res = array();
        if (!empty($result)) {
            $res['base_cur'] = $result['base_currency'];
        }
        echo json_encode($res);
        exit;
    }

    public function get_lead_fields() {
        $fields = array();
        $fields['CN'] = 'Customer Name';
        $fields['EW'] = 'Expected Worth';
        $fields['REG'] = 'Region';
        $fields['LO'] = 'Lead Owner';
        $fields['LAT'] = 'Lead Assigned To';
        $fields['STG'] = 'Lead Stage';
        $fields['IND'] = 'Lead Indicator';
        $fields['STAT'] = 'Status';
        $data['fields'] = $fields;

        $oldfields = $this->asset_model->get_lead_dashboard_field($this->userdata['userid']);
        $remove_select = array();

        $old_select = $base_select = '';
        if (!empty($oldfields) && count($oldfields) > 0) {
            $cl_checked1 = ' selected="selected"';
            foreach ($oldfields as $record) {
                $old_select .= '<option value="' . $record['column_name'] . '"' . $cl_checked1 . '>' . $fields[$record['column_name']] . '</option>';
                $remove_select[] = $record['column_name'];
            }
        }

        foreach ($fields as $key => $val) {
            if (!in_array($key, $remove_select)) {
                $base_select .= '<option value="' . $key . '">' . $val . '</option>';
            }
        }
        $data['base_select'] = $base_select;
        $data['old_select'] = $old_select;

        $this->load->view('leads/set_lead_fields', $data);
    }

    function save_lead_fields() {
        $existfields = $this->asset_model->get_records('lead_dashboard_fields', $arr = array('user_id' => $this->userdata['userid']), $ord = array('column_order' => 'ASC'));

        $i = 0;
        $res = array();
        $wh_condn = array('user_id' => $this->userdata['userid']);
        $del = $this->db->delete($this->cfg['dbpref'] . 'lead_dashboard_fields', $wh_condn);
        $newselect = $this->input->post('new_select');

        if (!empty($newselect) && count($newselect) > 0) {
            foreach ($newselect as $rec) {
                $ins_arr = array('user_id' => $this->userdata['userid']);
                $ins_arr['column_name'] = $rec;
                $ins_arr['column_order'] = $i;
                $i++;
                $insert = $this->asset_model->insert_row('lead_dashboard_fields', $ins_arr);
            }
        }
        if ($insert) {
            $res['result'] = 'success';
        } else {
            $res['result'] = 'error';
        }
        echo json_encode($res);
        exit;
    }
    
    public function add_location(){
      // echo 'hi';exit;
        $this->load->library('validation');
        $data = array();
        $post_data = real_escape_array($this->input->post());
//       / print_r($post_data);exit;
        $rules['asset_location'] = "trim|required";
        $rules['status'] = "trim|required";

        $this->validation->set_rules($rules);
        $fields['asset_location'] = 'Location Name';
      //  $fields['base_currency'] = 'Base Currency';
        $fields['status'] = 'Status';

        $this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');

        //for status
        $this->db->where('loc_id', $id);
        $data['cb_status'] = $this->db->get($this->cfg['dbpref'] . 'asset_location')->num_rows();
       // $data['currencies'] = $this->manage_service_model->get_records('expect_worth', $wh_condn = array('status' => 1), $order = array('expect_worth_id' => 'asc'));
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_dvsn'])) {
            $item_data = $this->db->get_where($this->cfg['dbpref'] . "asset_location", array('loc_id' => $id));
            if ($item_data->num_rows() > 0)
                $src = $item_data->result_array();
            if (isset($src) && is_array($src) && count($src) > 0)
                foreach ($src[0] as $k => $v) {
                    if (isset($this->validation->$k))
                        $this->validation->$k = $v;
                }
        }

        if ($this->validation->run() != false) {
            // all good
            foreach ($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
            if ($update_data['status'] == "") {
                if ($data['cb_status'] == 0) {
                    $update_data['status'] = 0;
                } else {
                    $update_data['status'] = 1;
                }
            }
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
                //update
                $this->db->where('div_id', $id);

                if ($this->db->update($this->cfg['dbpref'] . "sales_divisions", $update_data)) {
                    $this->session->set_flashdata('confirm', array('Entity Details Updated!'));
                }
            } else {
                //insert
                $this->db->insert($this->cfg['dbpref'] . "asset_location", $update_data);
                $this->session->set_flashdata('confirm', array('New Entity Added!'));
            }
            redirect('manage_service/manage_sales');
        }
        $this->load->view('location/add_location', $data);
    }
    
     /**
     * Check Duplicates for Lead source is already exits or not.
     */
    function chk_duplicate() {

        $chk_data = real_escape_array($this->input->post());
        $name = $chk_data['name'];
//       / print_r($name);exit;
        $tbl_name = 'asset_location';
        $tbl_cont['name'] = 'asset_location';
      //  $tbl_cont['id'] = 'lead_source_id';
        if (empty($id)) {
        echo 'hi';exit;
         //   $condn = array('asset_location' => $name);
            $res = $this->asset_location_model->check_duplicate($tbl_cont, $name, $tbl_name);
        } else {
            $condn = array('asset_location' => $name, 'id' => $id);
            $res = $this->asset_location_model->check_duplicate($tbl_cont, $name, $tbl_name);
        }
        
        if ($res == 0)
            echo json_encode('success');
        else
            echo json_encode('fail');
        exit;
    }

}

?>