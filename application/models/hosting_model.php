<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hosting_model extends crm_model {
    
    function Hosting_model() {

        parent::__construct();
        
    }
    
    function account_list($offset, $search) {
        $this->db->order_by($this->cfg['dbpref']."hosting.domain_name", "asc"); 
        $this->db->order_by('expiry_date', 'asc');
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('domain_name', $search);
        }
		$this->db->select('*, '.$this->cfg['dbpref'] . 'hosting.hostingid');
		$this->db->from($this->cfg['dbpref'] . 'hosting');
		$this->db->join($this->cfg['dbpref'] . 'dns', $this->cfg['dbpref'] . 'hosting.hostingid ='. $this->cfg['dbpref'] . 'dns.hostingid','left');
		$this->db->join($this->cfg['dbpref'] . 'subscriptions_type', $this->cfg['dbpref'] . 'subscriptions_type.subscriptions_type_id ='. $this->cfg['dbpref'] . 'hosting.subscriptions_type_id_fk','left');
		//$this->db->limit(30,$offset);
		$accounts=$this->db->get();
        //$accounts = $this->db->get($this->login_model->cfg['dbpref'] . 'hosting', 20, $offset);
        $list = $accounts->result();
		$delist=array();
		foreach($list as $key=>$val){
			$val=(array)$val;
			$delist[$key]['customer']=preg_replace('/\|[0-9]+$/', '', $this->customer_account($val['custid_fk']));
			$delist[$key]['domain_status']=$this->cfg['domain_status'][$val['domain_status']];
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
    
    function customer_account($id) {
        
        $customer = $this->db->get_where($this->cfg['dbpref'].'customers', array('custid' => $id), 1);
        if ($customer->num_rows() > 0) {
            $cust = $customer->result_array();
            $cust = $cust[0];
            $company = (trim($cust['company']) == '') ? '' : " - " . $cust['company'];
            return "{$cust['first_name']} {$cust['last_name']}{$company}|{$cust['custid']}";
        } else {
            return false;
        }
    }
    function check_unique($domain) {
        $dom = $this->db->get_where($this->cfg['dbpref'] . 'hosting', array('domain_name' => $domain));
        return ($dom->num_rows() > 0) ? true : false;
    }
    function account_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'].'hosting');
    }
    function get_account($id) {
        $account = $this->db->get_where($this->cfg['dbpref'].'hosting', array('hostingid' => $id), 1);
        return $account->result_array();
    }
    function update_account($id, $data) {
        $this->db->where('hostingid', $id);
        return $this->db->update($this->cfg['dbpref'].'hosting', $data);
    }
    function insert_account($data) {
        if ( $this->db->insert($this->cfg['dbpref'].'hosting', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    
    //Below function used for delete a row from table - MAR
    function delete_row($table, $cond, $id) {
        $this->db->where($cond, $id);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }
    
    function get_row_bycond($table, $cond, $id) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, array($cond => $id));
        return $res->result_array();
    }
    function insert_row($table, $param) {
    	$this->db->insert($this->cfg['dbpref'].$table, $param);
    }
    
    function get_hosting($custid) {
    	$this->db->order_by('domain_name', "asc");
    	$res = $this->db->get_where($this->cfg['dbpref'].'hosting', array('custid_fk' => $custid));
        return $res->result_array();
    }
	
    function update_row($table, $data, $cond) {
    	$this->db->where($cond);
		$this->db->update($this->cfg['dbpref'].$table, $data);
    }
    
    function get_host_hp($hostingid) {
    	$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'hosting_package as HP');
		$this->db->join($this->cfg['dbpref'].'package as P', 'HP.packageid_fk=P.package_id');
		$cond = array('HP.hostingid_fk' => $hostingid, 'H.hostingid' => $hostingid);
		$this->db->where('jb.lead_id', $cond);
    	return $this->db->get()->result_array();
    }
	
	/*
	*
	*@ Author eNoah - Mani.S
	*@ Function get_subscription_types
	*@ Table Name: subscriptions_type
	*@ Purpose: Get Subscriptions List To Hosting Page
	*	
	*/
	
	function get_subscription_types() {        
        $this->db->order_by('subscriptions_type_name', 'asc');        
		$this->db->select('subscriptions_type_id, subscriptions_type_name, subscriptions_type_flag');
		$this->db->from($this->cfg['dbpref'] . 'subscriptions_type');
		$this->db->where('subscriptions_type_flag', 'active');
		$accounts=$this->db->get();
        $list = $accounts->result_array();
		return $list;
    }
	
}
?>