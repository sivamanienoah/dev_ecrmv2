<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Hosting extends crm_controller {

    function Hosting() {
        parent::__construct();
         $this->userdata = $this->session->userdata('logged_in_user');
        $this->login_model->check_login();
        $this->load->model('hosting_model');
        $this->load->model('customer_model');
        $this->load->model('package_model');
        $this->load->library('validation');
    }

    function index($limit = 0, $search = false) {
        $data['accounts'] = $this->hosting_model->account_list($limit, $search);
        // echo '<pre>';print_r($data['accounts']);exit;
        $data['sub_names'] = $this->hosting_model->get_subscription_names();
        $data['sub_types'] = $this->hosting_model->get_subscription_type();
         $data['customers'] = $this->hosting_model->get_customers();
         $data['sub_status'] = $this->cfg['domain_status'];
     // echo '<pre>';print_r($data['sub_status']);exit; 
        $this->load->view('hosting_view', $data);
    }

    function delete_account($id = false) {
        if ($this->session->userdata('delete') == 1) {
            $this->hosting_model->delete_row('hosting', 'hostingid', $id);
            $this->hosting_model->delete_row('hosting_package', 'hostingid_fk', $id);
            $this->hosting_model->delete_row('dns', 'hostingid', $id);
            $this->session->set_flashdata('confirm', array('Hosting Account Deleted!'));
            redirect('hosting');
        } else {
            $this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
            redirect('hosting');
        }
    }

    function add_account($update = false, $id = false) {

        $data['packageid_fk'] = $this->hosting_model->get_row_bycond('hosting_package', 'hostingid_fk', $id);
        $data['package'] = $this->hosting_model->get_row_bycond('package', 'status', 'active');
        $data['subscription_types'] = $this->hosting_model->get_subscription_types(); // Mani.S



        $rules['customer_id'] = "required|integer|callback_is_valid_customer";
        $rules['domain_name'] = "trim|required|callback_domain_check";
        $rules['subscriptions_type_id_fk'] = "trim|required";

        if (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'ENOAH' && $_POST['domain_mgmt'] != 'CM') {
            //$rules['domain_expiry'] = "trim|required|callback_is_valid_domain_date";
            $rules['domain_expiry'] = "trim|required";
        }
        $this->validation->set_rules($rules);
        $fields['customer_id'] = "Customer Name";
        $fields['domain_name'] = "Subscription Name";
        $fields['domain_expiry'] = "Subscription Name Expiry";
        $fields['expiry_date'] = "Expiry Date";
        $fields['domain_status'] = 'Subscription Status';
        $fields['ssl'] = 'SSL';
        $fields['other_info'] = 'Other information';
        $fields['subscriptions_type_id_fk'] = 'Subscription Type';

        $this->validation->set_fields($fields);
        if (!$this->input->post('expiry_date') && !$update)
            $this->validation->expiry_date = date('d-m-Y', strtotime('+1 year'));
        if (!$this->input->post('domain_name') && !$update)
            $this->validation->domain_name = 'www.';
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        $data['test_data'] = '';
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_account'])) {
            $account = $this->hosting_model->get_account($id);
            if (is_array($account) && count($account) > 0)
                foreach ($account[0] as $k => $v) {
                    if (isset($this->validation->$k)) {
                        if ($k == 'expiry_date')
                            $v = date('d-m-Y', strtotime($v));
                        if ($k == 'domain_expiry' && !is_null($v))
                            $v = date('d-m-Y', strtotime($v));
                        $this->validation->$k = $v;
                    }
                    if ($k == 'custid_fk') {
                        $data['customer_id'] = $v;
                        $data['customer_name'] = preg_replace('/\|[0-9]+$/', '', $this->hosting_model->customer_account($v));
                    }
                }
        }
        if ($this->validation->run() == false) {
            $this->load->view('hosting_add_view', $data);
        } else {
            foreach ($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
            $update_data['custid_fk'] = $this->input->post('customer_id');
            unset($update_data['customer_id']);
            if (!empty($update_data['expiry_date'])) {
                $mdate = explode('-', $update_data['expiry_date']);
                $time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
                $update_data['expiry_date'] = date('Y-m-d', $time);
            }
            if (trim($update_data['domain_expiry']) != '' && isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'ENOAH') {
                $mdate = explode('-', $update_data['domain_expiry']);
                $time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
                $update_data['domain_expiry'] = date('Y-m-d', $time);
            } else {
                $update_data['domain_expiry'] = NULL;
            }
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
                if ($this->hosting_model->update_account($id, $update_data)) {
                    //delete and again inserting into hosting_package - Starts here
                    $this->hosting_model->delete_row('hosting_package', 'hostingid_fk', $id);
                    $packageid_fk = $this->input->post('packageid_fk');
                    if (is_array($packageid_fk))
                        foreach ($packageid_fk as $val) {
                            $duedate = '0000-00-00';
                            foreach ($data['packageid_fk'] as $v) {
                                if ($v['packageid_fk'] == $val)
                                    $duedate = $v['due_date'];
                            }
                            $update_packageid = array(packageid_fk => $val);
                            if ($val != 0) {
                                $this->hosting_model->insert_row('hosting_package', array('hostingid_fk' => $id, 'packageid_fk' => $val, 'due_date' => $duedate));
                            }
                        }
                    $this->session->set_flashdata('confirm', array('Account Details Updated!'));
                    redirect('hosting/');
                }
            } else {
                if ($newid = $this->hosting_model->insert_account($update_data)) {
                    //inserting into hosting_package - Starts here
                    $packageid_fk = $this->input->post('packageid_fk');
                    if (is_array($packageid_fk))
                        foreach ($packageid_fk as $val) {
                            $duedate = '0000-00-00';
                            foreach ($data['packageid_fk'] as $v) {
                                if ($v['packageid_fk'] == $val)
                                    $duedate = $v['due_date'];
                            }
                            if ($val != 0) {
                                $this->hosting_model->insert_row('hosting_package', array('hostingid_fk' => $newid, 'packageid_fk' => $val, 'due_date' => $duedate));
                            }
                        }
                    $this->session->set_flashdata('confirm', array('New Account Added!'));
                    redirect('hosting/');
                }
            }
        }
    }

    function domain_check($domain) {
        if (!preg_match('/^[a-z0-9\-_\.]+\.[a-z]{2,}$/i', $domain)) {
            $this->validation->set_message('domain_check', 'The subscription name specified does not appear to be a properly formatted subscription name.');
            return false;
        } else if ($this->hosting_model->check_unique(trim($domain)) && $this->uri->segment(3) != 'update') {
            $this->validation->set_message('domain_check', 'The subscription name specified already exists! Please supply a different subscription name.');
            return false;
        } else {
            return true;
        }
    }

    function is_valid_date($date) {
        $mdate = explode('-', $date);
        if (is_array($mdate) && count($mdate) == 3) {
            $time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
            if ($time && $time > time()) {
                return TRUE;
            }
        }
        $this->validation->set_message('is_valid_date', 'The expiry date needs to be in a correct format (dd-mm-yyyy) and the date should be a future date.');
        return FALSE;
    }

    function is_valid_domain_date($date) { //echo $date;
        $mdate = explode('-', $date);
        if (is_array($mdate) && count($mdate) == 3) {
            $time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
            if ($time) {
                return TRUE;
            }
        }
        $this->validation->set_message('is_valid_domain_date', 'The expiry date needs to be in a correct format (dd-mm-yyyy) and the date should be a future date.');
        return FALSE;
    }

    function is_valid_customer($id) {
        if ($this->hosting_model->customer_account($id) == false) {
            $this->validation->set_message('is_valid_customer', 'Please enter a existing customer/company name.');
            return false;
        } else {
            return true;
        }
    }

    function search() {
        if (isset($_POST['cancel_submit'])) {
            redirect('hosting/');
        } else if ($name = $this->input->post('account_search')) {
            redirect('hosting/index/0/' . $name);
        } else {
            redirect('hosting/');
        }
    }

    /* function ajax_customer_search() {
      if ($this->input->post('cust_name')) {
      $result = $this->customer_model->customer_list(0, $this->input->post('cust_name'));

      $i=0;
      if (count($result) > 0) foreach ($result as $cust) {
      //$company = (trim($cust['company']) == '') ? '' : " - " . $cust['company'];
      //echo "{$cust['first_name']} {$cust['last_name']}{$company}|{$cust['custid']}|{$cust['add1_region']}|{$cust['add1_country']}|{$cust['add1_state']}|{$cust['add1_location']}\n";
      if(!empty($cust)) {
      $res[$i]['id'] = $cust['custid'];
      $res[$i]['label'] = $cust['first_name'].' '.$cust['last_name'].' - '. $cust['company'];
      $res[$i]['regId'] = $cust['add1_region'];
      $res[$i]['cntryId'] = $cust['add1_country'];
      $res[$i]['stId'] = $cust['add1_state'];
      $res[$i]['locId'] = $cust['add1_location'];
      }
      $i++;
      }
      }
      echo json_encode($res); exit;
      } */

    function ajax_customer_search() {
        if ($this->input->post('cust_name')) {
            $result = $this->customer_model->customer_list(0, $this->input->post('cust_name'));

            $customer_id = array();
            if (count($result) > 0) {
                foreach ($result as $cust) {
                    $customer_id[] = $cust['companyid'];
                }
            }

            if (count($customer_id) > 0) {
                $contacts = $this->customer_model->get_contacts($customer_id);
                if (!empty($contacts)) {
                    $i = 0;
                    foreach ($contacts as $rec) {
                        $res[$i]['id'] = $rec['custid'];
                        $res[$i]['label'] = $rec['company'] . ' - ' . $rec['customer_name'];
                        $res[$i]['regId'] = $rec['add1_region'];
                        $res[$i]['cntryId'] = $rec['add1_country'];
                        $res[$i]['stId'] = $rec['add1_state'];
                        $res[$i]['locId'] = $rec['add1_location'];
                        $i++;
                    }
                }
            }
        }
        echo json_encode($res);
        exit;
    }

    function hosts($custid = '') {
        if ($custid <= 0)
            redirect('hosting/');
        $data['hosting'] = $this->hosting_model->get_hosting($custid);
        $data['hosts'] = 'HOSTS';
        $this->load->view('hosting_view', $data);
    }

    function due_date($hostingid = 0, $packageid = 0) {
        if ($hostingid == 0)
            redirect('hosting/');
        //$data = real_escape_array($_POST);
        $add_duedate = $this->input->post('Add_duedate');
        $duedate = $this->input->post('due_date');
        if (isset($add_duedate) && $add_duedate == 'edit') {
            if ($duedate == '')
                $duedate = '00-00-0000';
            $d = explode('-', $duedate);
            if (sizeof($d) > 0) {
                $due_date = $d[2] . '-' . $d[1] . '-' . $d[0];
                $cond = array('packageid_fk' => $this->input->post('packageid'), 'hostingid_fk' => $hostingid);
                $data = array('due_date' => $due_date);
                $this->hosting_model->update_row('hosting_package', $data, $cond);
                $this->session->set_flashdata('confirm', array('Package Details Updated!'));
                redirect('hosting/due_date/' . $hostingid);
            }
        }
        $data['pack'] = $this->hosting_model->get_host_hp($hostingid);
        $data['hostingid'] = $hostingid;
        $data['packageid'] = $packageid;
        $this->load->view('package_due_date', $data);
    }
    
      public function get_subscription_report() {
    	$data =array();
    	$options = array();
                $options['sub_name'] = $this->input->post('sub_name');
        
                $options['customer'] = $this->input->post('customer');
                $options['start_date'] = $this->input->post('start_date');
		$options['end_date'] = $this->input->post('end_date');
                
		$options['h_start_date'] = $this->input->post('h_start_date');
		$options['h_end_date'] = $this->input->post('h_end_date');
		
                $options['sub_type_name'] = $this->input->post('sub_type_name');
		$options['status'] = $this->input->post('status');
		
		
//print_r($options);exit;
    	$res = $this->hosting_model->getSubscriptionReport($options);
    //	echo '<pre>';            print_r($res);exit;
    	$data['res'] = $res['res'];
    	$data['num'] = $res['num'];
    	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {    	
    		
   			$this->load->view('hosting/subscription_report_view',$data);
		}else{
			
    		return $this->load->view('hosting/subscription_report_view',$data,true);
		}    	
    }


    public function advance_filter_search($search_type = false, $search_id = false) {
      // echo"here";exit;
        // echo'<pre>search_type=>';print_r($search_type);
        // echo'<pre>search_id=>';print_r($search_id);
        $filt = array();
        $sub_name = null;
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
          //  echo 'if';exit;
            $filt = real_escape_array($this->input->post()); //echo'<pre>filt1=>';print_r($filt);
            $this->session->set_userdata("lead_search_by_default", 0);
            $this->session->set_userdata("lead_search_by_id", 0);
            $this->session->set_userdata("lead_search_only", 1);
        } else if ($search_type == 'search' && is_numeric($search_id)) {
         //  echo 'elseif';exit;
            $wh_condn = array('search_id' => $search_id, 'search_for' => 1, 'user_id' => $this->userdata['userid']);
            $get_rec = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
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
        //  echo 'elseif2';exit;
            $this->session->set_userdata("load_proposal_expect_end", 1);
            $proposal_expect_end = 'load_proposal_expect_end';
        } else {
         //  echo 'else';exit;
            $wh_condn = array('search_for' => 1, 'user_id' => $this->userdata['userid'], 'is_default' => 1);
            $get_rec = $this->welcome_model->get_data_by_id('saved_search_critriea', $wh_condn);
            unset($get_rec['search_id']);
            unset($get_rec['search_for']);
            unset($get_rec['search_name']);
            unset($get_rec['user_id']);
            unset($get_rec['is_default']);
            if (!empty($get_rec)) {
                $filt = real_escape_array($get_rec);
                $this->session->set_userdata("lead_search_by_default", 1);
                $this->session->set_userdata("lead_search_only", 0);
                $this->session->set_userdata("lead_search_by_id", 0);
            } else {
                $this->session->set_userdata("lead_search_by_default", 0);
                $this->session->set_userdata("lead_search_only", 1);
                $this->session->set_userdata("lead_search_by_id", 0);
            }
        }
        // echo'<pre>filt2=>';print_r($filt);
        // echo'<pre>';print_r(count($filt));exit;
        if (count($filt) > 0) {
            //echo 'yes';
            $from_date = $filt['from_date'];
            $to_date = $filt['to_date'];
            $sub_name = $filt['sub_name'];
            $customer = $filt['customer'];
            $service = $filt['service'];
            $lead_src = $filt['lead_src'];
            $industry = $filt['industry'];
            $worth = $filt['worth'];
            $owner = $filt['owner'];
            $leadassignee = $filt['leadassignee'];
            $regionname = $filt['regionname'];
            $countryname = $filt['countryname'];
            $statename = $filt['statename'];
            $locname = $filt['locname'];
            $lead_status = $filt['lead_status'];
            $lead_indi = $filt['lead_indi'];
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

        $filter_results = $this->hosting_model->get_filter_results($from_date, $to_date, $sub_name, $customer, $service, $lead_src, $industry, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status, $lead_indi, $keyword, $proposal_expect_end);
        // echo $this->db->last_query(); die;
        $data['filter_results'] = $filter_results;
       // echo '<pre>';print_r($data['filter_results']);exit;
        $data['sub_name'] = $sub_name;
        $data['customer'] = $customer;
        $data['service'] = $service;
        $data['lead_src'] = $lead_src;
        $data['industry'] = $industry;
        $data['worth'] = $worth;
        $data['owner'] = $owner;
        $data['leadassignee'] = $leadassignee;
        $data['regionname'] = $regionname;
        $data['countryname'] = $countryname;
        $data['statename'] = $statename;
        $data['locname'] = $locname;
        $data['lead_status'] = $lead_status;
        $data['lead_indi'] = $lead_indi;
        $data['keyword'] = $keyword;
       
       // print_r($this->userdata['userid']);exit;
        $db_fields = $this->hosting_model->get_subscription_dashboard_field($this->userdata['userid']);
        if (!empty($db_fields) && count($db_fields) > 0) {
            foreach ($db_fields as $record) {
                $data['db_fields'][] = $record['column_name'];
            }
        }
        //print_r($data);exit;
        $this->load->view('hosting/advance_filter_view', $data);
    }

}

?>