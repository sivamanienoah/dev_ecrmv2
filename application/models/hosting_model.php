<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Hosting_model extends crm_model {

    function Hosting_model() {

        parent::__construct();
        $this->userdata = $this->session->userdata('logged_in_user');
    }

    function account_list($offset, $search) {
        $this->db->order_by($this->cfg['dbpref'] . "hosting.domain_name", "asc");
        $this->db->order_by('expiry_date', 'asc');
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('domain_name', $search);
        }
        $this->db->select('*, ' . $this->cfg['dbpref'] . 'hosting.hostingid');
        $this->db->from($this->cfg['dbpref'] . 'hosting');
        $this->db->join($this->cfg['dbpref'] . 'dns', $this->cfg['dbpref'] . 'hosting.hostingid =' . $this->cfg['dbpref'] . 'dns.hostingid', 'left');
        $this->db->join($this->cfg['dbpref'] . 'subscriptions_type', $this->cfg['dbpref'] . 'subscriptions_type.subscriptions_type_id =' . $this->cfg['dbpref'] . 'hosting.subscriptions_type_id_fk', 'left');
        //$this->db->limit(30,$offset);
        $accounts = $this->db->get();
       // echo $this->db->last_query();exit;
        //$accounts = $this->db->get($this->login_model->cfg['dbpref'] . 'hosting', 20, $offset);
        $list = $accounts->result();
        $delist = array();
        foreach ($list as $key => $val) {
            $val = (array) $val;
            $delist[$key]['customer'] = preg_replace('/\|[0-9]+$/', '', $this->customer_account($val['custid_fk']));
            $delist[$key]['domain_status'] = $this->cfg['domain_status'][$val['domain_status']];
            $delist[$key]['expiry_date'] = date('d-m-Y', strtotime($val['expiry_date']));
            if (($val['domain_expiry']) == "") {
                $delist[$key]['domain_expiry'] = '-';
            } else {
                $delist[$key]['domain_expiry'] = date('d-m-Y', strtotime($val['domain_expiry']));
            }
            $delist[$key]['ssl'] = $this->login_model->cfg['domain_ssl_status'][$val['ssl']];
            $delist[$key]['domain_name'] = $val['domain_name'];
            $delist[$key]['hostingid'] = $val['hostingid'];
            $delist[$key]['go_live_date'] = $val['go_live_date'];
            $delist[$key]['host_location'] = $val['host_location'];
            $delist[$key]['login_url'] = $val['login_url'];
            $delist[$key]['login'] = $val['login'];
            $delist[$key]['subscriptions_type_name'] = $val['subscriptions_type_name'];
            $delist[$key]['email'] = $val['email'];
            $delist[$key]['registrar_password'] = $val['registrar_password'];
            $delist[$key]['cur_smtp_setting'] = $val['cur_smtp_setting'];
            $delist[$key]['cur_pop_setting'] = $val['cur_pop_setting'];
            $delist[$key]['cur_dns_primary_url'] = $val['cur_dns_primary_url'];
            $delist[$key]['cur_dns_primary_ip'] = $val['cur_dns_primary_ip'];
            $delist[$key]['cur_dns_secondary_url'] = $val['cur_dns_secondary_url'];
            $delist[$key]['cur_dns_secondary_ip'] = $val['cur_dns_secondary_ip'];
        }
        return $delist;
    }

    /* function customer_account($id) {

      $customer = $this->db->get_where($this->cfg['dbpref'].'customers', array('custid' => $id), 1);
      if ($customer->num_rows() > 0) {
      $cust = $customer->result_array();
      $cust = $cust[0];
      $company = (trim($cust['company']) == '') ? '' : " - " . $cust['company'];
      return "{$cust['first_name']} {$cust['last_name']}{$company}|{$cust['custid']}";
      } else {
      return false;
      }
      } */

    function customer_account($id) {

        $this->db->select('c.*,cc.*');
        $this->db->from($this->cfg['dbpref'] . 'customers as c');
        $this->db->join($this->cfg['dbpref'] . 'customers_company as cc', 'cc.companyid = c.company_id');
        $this->db->where_in('c.custid', $id);
        $customer = $this->db->get();
        // echo $this->db->last_query(); die;
        if ($customer->num_rows() > 0) {
            $cust = $customer->result_array();
            $cust = $cust[0];
            $company = (trim($cust['company']) == '') ? '' : " - " . $cust['company'];
            return "{$cust['customer_name']} {$company}|{$cust['custid']}";
        } else {
            return FALSE;
        }
    }
    
     function get_customers(){
       // $qry = $this->db->query("SELECT * from hosting a,customers b where a.custid_fk = b.custid group by a.custid_fk order by a.custid_fk asc");
       $this->db->select('a.*,b.*');
        $this->db->from($this->cfg['dbpref'] . 'hosting as a');
        $this->db->join($this->cfg['dbpref'] . 'customers as b', 'a.custid_fk = b.custid');
        $this->db->group_by("a.custid_fk");
        $this->db->order_by("a.custid_fk", "asc");
        $qry = $this->db->get();
        $res = $qry->num_rows();
            if($res){
			return $qry->result_array();
		}
		return false;
    }
    
    function get_subscription_type(){
       // $qry = $this->db->query("SELECT * from hosting a,customers b where a.custid_fk = b.custid group by a.custid_fk order by a.custid_fk asc");
       $this->db->select('b.*');
        //$this->db->from($this->cfg['dbpref'] . 'hosting as a');
        $this->db->from($this->cfg['dbpref'] . 'subscriptions_type as b');
        $this->db->group_by("b.subscriptions_type_id");
        $this->db->order_by("b.subscriptions_type_id", "asc");
        $qry = $this->db->get();
        $res = $qry->num_rows();
            if($res){
			return $qry->result_array();
		}
		return false;
    }
    
    function get_sub_status(){
       // $qry = $this->db->query("SELECT * from hosting a,customers b where a.custid_fk = b.custid group by a.custid_fk order by a.custid_fk asc");
       $this->db->select('*');
        $this->db->from($this->cfg['dbpref'] . 'hosting as a');
       // $this->db->join($this->cfg['dbpref'] . 'subscriptions_type as b', 'a.subscriptions_type_id_fk = b.	subscriptions_type_id');
        $this->db->group_by("a.domain_status");
        $this->db->order_by("a.domain_status", "asc");
        $qry = $this->db->get();
        $res = $qry->num_rows();
            if($res){
			return $qry->result_array();
		}
		return false;
    }
    
    function check_unique($domain) {

        $dom = $this->db->get_where($this->cfg['dbpref'] . 'hosting', array('domain_name' => $domain));
        return ($dom->num_rows() > 0) ? true : false;
    }

    function account_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'hosting');
    }

    function get_account($id) {
        $account = $this->db->get_where($this->cfg['dbpref'] . 'hosting', array('hostingid' => $id), 1);
        return $account->result_array();
    }

    function update_account($id, $data) {
        $this->db->where('hostingid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'hosting', $data);
    }

    function insert_account($data) {
        if ($this->db->insert($this->cfg['dbpref'] . 'hosting', $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    //Below function used for delete a row from table - MAR
    function delete_row($table, $cond, $id) {
        $this->db->where($cond, $id);
        return $this->db->delete($this->cfg['dbpref'] . $table);
    }

    function get_row_bycond($table, $cond, $id) {
        $res = $this->db->get_where($this->cfg['dbpref'] . $table, array($cond => $id));
        return $res->result_array();
    }

    function insert_row($table, $param) {
        $this->db->insert($this->cfg['dbpref'] . $table, $param);
    }

    function get_hosting($custid) {
        $this->db->order_by('domain_name', "asc");
        $res = $this->db->get_where($this->cfg['dbpref'] . 'hosting', array('custid_fk' => $custid));
        return $res->result_array();
    }

    function update_row($table, $data, $cond) {
        $this->db->where($cond);
        $this->db->update($this->cfg['dbpref'] . $table, $data);
    }

    function get_host_hp($hostingid) {
        $this->db->select('*');
        $this->db->from($this->cfg['dbpref'] . 'hosting_package as HP');
        $this->db->join($this->cfg['dbpref'] . 'package as P', 'HP.packageid_fk=P.package_id');
        $cond = array('HP.hostingid_fk' => $hostingid, 'H.hostingid' => $hostingid);
        $this->db->where('jb.lead_id', $cond);
        return $this->db->get()->result_array();
    }

    function get_subscription_names() {
        $this->db->select('*');
        $this->db->from($this->cfg['dbpref'] . 'hosting as h');
        $this->db->group_by("h.domain_name");
        $this->db->order_by("h.domain_name", "asc");
        $qry = $this->db->get();
        $res = $qry->num_rows();
		if($res){
			return $qry->result_array();
		}
		return false;
    }

    /*
     *
     * @ Author eNoah - Mani.S
     * @ Function get_subscription_types
     * @ Table Name: subscriptions_type
     * @ Purpose: Get Subscriptions List To Hosting Page
     * 	
     */

    function get_subscription_types() {
        $this->db->order_by('subscriptions_type_name', 'asc');
        $this->db->select('subscriptions_type_id, subscriptions_type_name, subscriptions_type_flag');
        $this->db->from($this->cfg['dbpref'] . 'subscriptions_type');
        $this->db->where('subscriptions_type_flag', 'active');
        $accounts = $this->db->get();
        $list = $accounts->result_array();
        return $list;
    }

    public function get_filter_results($from_date, $to_date, $sub_name, $customer, $service, $lead_src, $industry, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $lead_status, $lead_indi, $keyword, $proposal_expect_end) {
        // print_r($sub_name);exit;
        $userdata = $this->session->userdata('logged_in_user');
       // print_r($userdata);exit;

        $sub_name = (count($sub_name) > 0) ? explode(',', $sub_name) : '';
        $owner = (count($owner) > 0) ? explode(',', $owner) : '';
        $customer = (count($customer) > 0) ? explode(',', $customer) : '';
        $service = (count($service) > 0) ? explode(',', $service) : '';
        $lead_src = (count($lead_src) > 0) ? explode(',', $lead_src) : '';
        $industry = (count($industry) > 0) ? explode(',', $industry) : '';
        $worth = (count($worth) > 0) ? explode(',', $worth) : ''; //print_r($worth);exit;
        $leadassignee = (count($leadassignee) > 0) ? explode(',', $leadassignee) : '';
        $regionname = (count($regionname) > 0) ? explode(',', $regionname) : '';
        $countryname = (count($countryname) > 0) ? explode(',', $countryname) : '';
        $statename = (count($statename) > 0) ? explode(',', $statename) : '';
        $locname = (count($locname) > 0) ? explode(',', $locname) : '';
        $lead_status = (count($lead_status) > 0) ? explode(',', $lead_status) : '';
        $lead_indi = (count($lead_indi) > 0) ? explode(',', $lead_indi) : '';


        if (isset($proposal_expect_end) && ($proposal_expect_end == 'load_proposal_expect_end')) {
            $proposal_notify_day = get_notify_status(1);
        }
        // echo $this->userdata['role_id'];exit;
        if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
            $this->db->select('*', FALSE);
            $this->db->from($this->cfg['dbpref'] . 'hosting as j');
            $this->db->where('j.hostingid != "null"');
            // $this->db->where('j.pjt_status', 0);
            $this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = j.custid_fk');
            $this->db->join($this->cfg['dbpref'] . 'subscriptions_type as b', 'b.subscriptions_type_id = j.subscriptions_type_id_fk', 'LEFT');
            // $this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
            //    $this->db->join($this->cfg['dbpref'] . 'users as u', ' FIND_IN_SET (u.userid , j.lead_assign) ');
            //     $this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
            //     $this->db->join($this->cfg['dbpref'] . 'users as ub', 'ub.userid = j.belong_to');
            //     $this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
            //      $this->db->join($this->cfg['dbpref'] . 'country as co', 'co.countryid = cc.add1_country');
            //     $this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
            //      $this->db->join($this->cfg['dbpref'] . 'location as locn', 'locn.locationid = cc.add1_location');
            //     $this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage', 'LEFT');
            //       $this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
            // date_created
            if (isset($from_date) && !empty($from_date) && empty($to_date)) {
                $dt_query = 'DATE(j.date_created) >= "' . date('Y-m-d', strtotime($from_date)) . '"';
                $dt_mod_query = 'DATE(j.date_modified) >= "' . date('Y-m-d', strtotime($from_date)) . '"';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            } else if (isset($to_date) && !empty($to_date) && empty($from_date)) {
                $dt_query = 'DATE(j.date_created) <= "' . date('Y-m-d', strtotime($to_date)) . '"';
                $dt_mod_query = 'DATE(j.date_modified) <= "' . date('Y-m-d', strtotime($to_date)) . '"';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            } else if (isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)) {
                $dt_query = '((DATE(j.date_created) >= "' . date('Y-m-d', strtotime($from_date)) . '" AND DATE(j.date_created) <= "' . date('Y-m-d', strtotime($to_date)) . '")';
                $dt_mod_query = '(DATE(j.date_modified) >= "' . date('Y-m-d', strtotime($from_date)) . '" AND DATE(j.date_modified) <= "' . date('Y-m-d', strtotime($to_date)) . '"))';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            }

            if (!empty($sub_name) && count($sub_name) > 0) {
                if ($sub_name[0] != 'null' && $sub_name[0] != 'all') {
                    $this->db->where_in('j.hostingid', $sub_name);
                }
            }
            
            if (!empty($keyword) && count($keyword) > 0) {
                if (!empty($keyword) && $keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null') {
                    $invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR cc.company LIKE '%$keyword%' OR c.customer_name LIKE '%$keyword%' ))";
                    $this->db->where($invwhere);
                }
            }
          // echo $this->db->last_query();exit;
        } else if ($this->userdata['role_id'] == 14) { //for reseller role
            $curusid = $this->session->userdata['logged_in_user']['userid'];
           $this->db->select('*', FALSE);
            $this->db->from($this->cfg['dbpref'] . 'hosting as a');
            $this->db->where('j.hostingid != "null"');
            // $this->db->where('j.pjt_status', 0);
            $this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = a.custid_fk');
            $this->db->join($this->cfg['dbpref'] . 'subscriptions_type as b', 'b.subscriptions_type_id = a.subscriptions_type_id_fkS');
            // $this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
            //    $this->db->join($this->cfg['dbpref'] . 'users as u', ' FIND_IN_SET (u.userid , j.lead_assign) ');
            //     $this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
            //     $this->db->join($this->cfg['dbpref'] . 'users as ub', 'ub.userid = j.belong_to');
            //     $this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
            //      $this->db->join($this->cfg['dbpref'] . 'country as co', 'co.countryid = cc.add1_country');
            //     $this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
            //      $this->db->join($this->cfg['dbpref'] . 'location as locn', 'locn.locationid = cc.add1_location');
            //     $this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage', 'LEFT');
            //       $this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
            // date_created

            if (isset($from_date) && !empty($from_date) && empty($to_date)) {
                $dt_query = 'DATE(j.date_created) >= "' . date('Y-m-d', strtotime($from_date)) . '"';
                $dt_mod_query = 'DATE(j.date_modified) >= "' . date('Y-m-d', strtotime($from_date)) . '"';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            } else if (isset($to_date) && !empty($to_date) && empty($from_date)) {
                $dt_query = 'DATE(j.date_created) <= "' . date('Y-m-d', strtotime($to_date)) . '"';
                $dt_mod_query = 'DATE(j.date_modified) <= "' . date('Y-m-d', strtotime($to_date)) . '"';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            } else if (isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)) {
                $dt_query = '((DATE(j.date_created) >= "' . date('Y-m-d', strtotime($from_date)) . '" AND DATE(j.date_created) <= "' . date('Y-m-d', strtotime($to_date)) . '")';
                $dt_mod_query = '(DATE(j.date_modified) >= "' . date('Y-m-d', strtotime($from_date)) . '" AND DATE(j.date_modified) <= "' . date('Y-m-d', strtotime($to_date)) . '"))';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            }

            if (!empty($stage) && count($stage) > 0) {
                if ($stage[0] != 'null' && $stage[0] != 'all') {
                    $this->db->where_in('j.lead_stage', $stage);
                }
            }
            if (!empty($customer) && count($customer) > 0) {
                if ($customer[0] != 'null' && $customer[0] != 'all') {
                    $this->db->where_in('cc.companyid', $customer);
                }
            }
            if (!empty($service) && count($service) > 0) {
                if ($service[0] != 'null' && $service[0] != 'all') {
                    $this->db->where_in('j.lead_service', $service);
                }
            }
            if (!empty($lead_src) && count($lead_src) > 0) {
                if ($lead_src[0] != 'null' && $lead_src[0] != 'all' && $lead_src[0] != '') {
                    $this->db->where_in('j.lead_source', $lead_src);
                }
            }
            if (!empty($industry) && count($industry) > 0) {
                if ($industry[0] != 'null' && $industry[0] != 'all' && $industry[0] != '') {
                    $this->db->where_in('j.industry', $industry);
                }
            }
           
            if (!empty($keyword) && count($keyword) > 0) {
                if ($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null') {
                    $invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.customer_name LIKE '%$keyword%' ))";
                    $this->db->where($invwhere);
                }
            }
         
          
        } else {
            $curusid = $this->session->userdata['logged_in_user']['userid'];
           $this->db->select('*', FALSE);
            $this->db->from($this->cfg['dbpref'] . 'hosting as a');
            $this->db->where('j.hostingid != "null"');
            // $this->db->where('j.pjt_status', 0);
            $this->db->join($this->cfg['dbpref'] . 'customers as c', 'c.custid = a.custid_fk');
            $this->db->join($this->cfg['dbpref'] . 'subscriptions_type as b', 'b.subscriptions_type_id = a.subscriptions_type_id_fkS');
            // $this->db->join($this->cfg['dbpref'] . 'users as u', 'u.userid = j.lead_assign');
            //    $this->db->join($this->cfg['dbpref'] . 'users as u', ' FIND_IN_SET (u.userid , j.lead_assign) ');
            //     $this->db->join($this->cfg['dbpref'] . 'users as us', 'us.userid = j.modified_by');
            //     $this->db->join($this->cfg['dbpref'] . 'users as ub', 'ub.userid = j.belong_to');
            //     $this->db->join($this->cfg['dbpref'] . 'region as rg', 'rg.regionid = cc.add1_region');
            //      $this->db->join($this->cfg['dbpref'] . 'country as co', 'co.countryid = cc.add1_country');
            //     $this->db->join($this->cfg['dbpref'] . 'state as st', 'st.stateid = cc.add1_state');
            //      $this->db->join($this->cfg['dbpref'] . 'location as locn', 'locn.locationid = cc.add1_location');
            //     $this->db->join($this->cfg['dbpref'] . 'lead_stage as ls', 'ls.lead_stage_id = j.lead_stage', 'LEFT');
            //       $this->db->join($this->cfg['dbpref'] . 'expect_worth as ew', 'ew.expect_worth_id = j.expect_worth_id');
            // date_created

            if (isset($from_date) && !empty($from_date) && empty($to_date)) {
                $dt_query = 'DATE(j.date_created) >= "' . date('Y-m-d', strtotime($from_date)) . '"';
                $dt_mod_query = 'DATE(j.date_modified) >= "' . date('Y-m-d', strtotime($from_date)) . '"';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            } else if (isset($to_date) && !empty($to_date) && empty($from_date)) {
                $dt_query = 'DATE(j.date_created) <= "' . date('Y-m-d', strtotime($to_date)) . '"';
                $dt_mod_query = 'DATE(j.date_modified) <= "' . date('Y-m-d', strtotime($to_date)) . '"';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            } else if (isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)) {
                $dt_query = '((DATE(j.date_created) >= "' . date('Y-m-d', strtotime($from_date)) . '" AND DATE(j.date_created) <= "' . date('Y-m-d', strtotime($to_date)) . '")';
                $dt_mod_query = '(DATE(j.date_modified) >= "' . date('Y-m-d', strtotime($from_date)) . '" AND DATE(j.date_modified) <= "' . date('Y-m-d', strtotime($to_date)) . '"))';
                // echo'<pre>';print_r($dt_query);exit;
                $this->db->where($dt_query);
                $this->db->or_where($dt_mod_query);
            }

            if (!empty($stage) && count($stage) > 0) {
                if ($stage[0] != 'null' && $stage[0] != 'all') {
                    $this->db->where_in('j.lead_stage', $stage);
                    // $this->db->where('j.belong_to', $curusid);
                }
            }
            if (!empty($customer) && count($customer) > 0) {
                if ($customer[0] != 'null' && $customer[0] != 'all') {
                    $this->db->where_in('cc.companyid', $customer);
                }
            }
            if (!empty($service) && count($service) > 0) {
                if ($service[0] != 'null' && $service[0] != 'all') {
                    $this->db->where_in('j.lead_service', $service);
                }
            }
            
     
            if (!empty($keyword) && count($keyword) > 0) {
                if ($keyword != 'Lead No, Job Title, Name or Company' && $keyword != 'null') {
                    $invwhere = "( (j.invoice_no LIKE '%$keyword%' OR j.lead_title LIKE '%$keyword%' OR c.customer_name LIKE '%$keyword%' ))";
                    $this->db->where($invwhere);
                }
            }

           
        }

     //   $this->db->group_by("j.lead_id");
        $this->db->order_by("j.hostingid", "desc");
        $query = $this->db->get();
      //  echo $this->db->last_query(); exit;

        $res = $query->result_array();
        return $res;
    }

}

?>