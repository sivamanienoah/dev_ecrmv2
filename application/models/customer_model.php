<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customer_model extends crm_model {
    
    public $userdata;
    
    function __construct()
    {
        parent::__construct();
        $this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/* function customer_list($offset, $search, $order_field = 'last_name', $order_type = 'asc', $limit = false) 
	{
        $restrict = '';
        $restrict_search = '';
		//customer restriction on level based.
		if ($this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4 || $this->userdata['level'] == 5) {
			$cond = array('level_id' => $this->userdata['level'], 'user_id' => $this->userdata['userid']);
			
			$this->db->select('region_id');
		 	$reg_res = $this->db->get_where($this->cfg['dbpref']."levels_region", $cond);
			$reg_details = $reg_res->result_array();
			foreach($reg_details as $reg) {
				$regions[] = $reg['region_id'];
			}
			$regions_ids = array_unique($regions);
			$regions_ids = (array_values($regions)); //reset the keys in the array
			//$regions_ids = implode(",", $regions_ids);
		
			//restriction for country
			$this->db->select('country_id');
			$coun_res = $this->db->get_where($this->cfg['dbpref']."levels_country", $cond);
			$coun_details = $coun_res->result_array();
			foreach($coun_details as $coun) {
				$countries[] = $coun['country_id'];
			}
			if (!empty($countries)) {
				$countries_ids = array_unique($countries);
				$countries_ids = (array_values($countries)); //reset the keys in the array
				//$countries_ids = @implode(",",$countries_ids);
			}
		
			//restriction for state
			$this->db->select('state_id');
			$state_res = $this->db->get_where($this->cfg['dbpref']."levels_state", $cond);
			$ste_details = $state_res->result_array();
			foreach($ste_details as $ste) {
				$states[] = $ste['state_id'];
			}
			if (!empty($states)) {
				$states_ids = array_unique($states);
				$states_ids = (array_values($states)); //reset the keys in the array				
			}
			//$states_ids = implode(",",$states_ids);
		
			//restriction for location
			$this->db->select('location_id');
			$loc_res = $this->db->get_where($this->cfg['dbpref']."levels_location", $cond);
			$loc_details = $loc_res->result_array();
			foreach($loc_details as $loc) {
				$locations[] = $loc['location_id'];
			}
			if (!empty($locations)) {
				$locations_ids = array_unique($locations);
				$locations_ids = (array_values($locations)); //reset the keys in the array
			}
			//$locations_ids = implode(",",$locations_ids);
		}
       
        $offset = $this->db->escape_str($offset);	
		$this->db->select('CUST.*, REG.regionid, REG.region_name, COUN.countryid, COUN.country_name');
		$this->db->from($this->cfg['dbpref'].'customers as CUST');
		$this->db->join($this->cfg['dbpref'].'region as REG', 'CUST.add1_region = REG.regionid', 'left');
		$this->db->join($this->cfg['dbpref'].'country as COUN', 'CUST.add1_country = COUN.countryid', 'left');
        if ($this->userdata['level'] == 2) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
		} else if ($this->userdata['level'] == 3) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
		} else if ($this->userdata['level'] == 4) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
		} else if ($this->userdata['level'] == 5) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
			$this->db->where_in('CUST.add1_location', $locations_ids);
		}
		if($search != false) {
			$search = $this->db->escape_str(urldecode($search));
			$this->db->where("(first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR company LIKE '%$search%' OR email_1 LIKE '%$search%')");
		}
		if(!empty($limit))
		$this->db->limit($limit);
		$customers = $this->db->get();        
        // echo $this->db->last_query();
        return $customers->result_array(); 
    } */
	
	function customer_list($offset, $search, $order_field='last_name', $order_type='asc', $limit = false)
	{
        $restrict = '';
        $restrict_search = '';
		//customer restriction on level based.
		if ($this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4 || $this->userdata['level'] == 5) {
			$cond = array('level_id' => $this->userdata['level'], 'user_id' => $this->userdata['userid']);
			
			$this->db->select('region_id');
		 	$reg_res = $this->db->get_where($this->cfg['dbpref']."levels_region", $cond);
			$reg_details = $reg_res->result_array();
			foreach($reg_details as $reg) {
				$regions[] = $reg['region_id'];
			}
			$regions_ids = array_unique($regions);
			$regions_ids = (array_values($regions)); //reset the keys in the array
		
			//restriction for country
			$this->db->select('country_id');
			$coun_res = $this->db->get_where($this->cfg['dbpref']."levels_country", $cond);
			$coun_details = $coun_res->result_array();
			foreach($coun_details as $coun) {
				$countries[] = $coun['country_id'];
			}
			if (!empty($countries)) {
				$countries_ids = array_unique($countries);
				$countries_ids = (array_values($countries)); //reset the keys in the array
			}
		
			//restriction for state
			$this->db->select('state_id');
			$state_res = $this->db->get_where($this->cfg['dbpref']."levels_state", $cond);
			$ste_details = $state_res->result_array();
			foreach($ste_details as $ste) {
				$states[] = $ste['state_id'];
			}
			if (!empty($states)) {
				$states_ids = array_unique($states);
				$states_ids = (array_values($states)); //reset the keys in the array				
			}
		
			//restriction for location
			$this->db->select('location_id');
			$loc_res = $this->db->get_where($this->cfg['dbpref']."levels_location", $cond);
			$loc_details = $loc_res->result_array();
			foreach($loc_details as $loc) {
				$locations[] = $loc['location_id'];
			}
			if (!empty($locations)) {
				$locations_ids = array_unique($locations);
				$locations_ids = (array_values($locations)); //reset the keys in the array
			}
		}
       
        $offset = $this->db->escape_str($offset);	
		$this->db->select('CUST.*, REG.regionid, REG.region_name, COUN.countryid, COUN.country_name');
		$this->db->from($this->cfg['dbpref'].'customers_company as CUST');
		$this->db->join($this->cfg['dbpref'].'region as REG', 'CUST.add1_region = REG.regionid', 'left');
		$this->db->join($this->cfg['dbpref'].'country as COUN', 'CUST.add1_country = COUN.countryid', 'left');
		if ($this->userdata['level'] == 2) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
		} else if ($this->userdata['level'] == 3) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
		} else if ($this->userdata['level'] == 4) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
		} else if ($this->userdata['level'] == 5) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
			$this->db->where_in('CUST.add1_location', $locations_ids);
		}
		
		if($search != false) {
			$search = $this->db->escape_str(urldecode($search));
			$this->db->where("(company LIKE '%$search%')");
		}
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$this->db->where('CUST.created_by', $this->userdata['userid']);
		}
		if(!empty($limit)){
			$this->db->limit($limit);
		}
		$customers = $this->db->get();
        // echo $this->db->last_query(); exit;
        return $customers->result_array();
    }
    
	function company_list($offset, $search, $order_field = 'last_name', $order_type = 'asc', $limit = false) {
        $restrict = '';
        $restrict_search = '';
		//customer restriction on level based.
		if ($this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4 || $this->userdata['level'] == 5) {
			$cond = array('level_id' => $this->userdata['level'], 'user_id' => $this->userdata['userid']);
			
			$this->db->select('region_id');
		 	$reg_res = $this->db->get_where($this->cfg['dbpref']."levels_region", $cond);
			$reg_details = $reg_res->result_array();
			foreach($reg_details as $reg) {
				$regions[] = $reg['region_id'];
			}
			$regions_ids = array_unique($regions);
			$regions_ids = (array_values($regions)); //reset the keys in the array
			//$regions_ids = implode(",", $regions_ids);
		
			//restriction for country
			$this->db->select('country_id');
			$coun_res = $this->db->get_where($this->cfg['dbpref']."levels_country", $cond);
			$coun_details = $coun_res->result_array();
			foreach($coun_details as $coun) {
				$countries[] = $coun['country_id'];
			}
			if (!empty($countries)) {
				$countries_ids = array_unique($countries);
				$countries_ids = (array_values($countries)); //reset the keys in the array
				//$countries_ids = @implode(",",$countries_ids);
			}
		
			//restriction for state
			$this->db->select('state_id');
			$state_res = $this->db->get_where($this->cfg['dbpref']."levels_state", $cond);
			$ste_details = $state_res->result_array();
			foreach($ste_details as $ste) {
				$states[] = $ste['state_id'];
			}
			if (!empty($states)) {
				$states_ids = array_unique($states);
				$states_ids = (array_values($states)); //reset the keys in the array				
			}
			//$states_ids = implode(",",$states_ids);
		
			//restriction for location
			$this->db->select('location_id');
			$loc_res = $this->db->get_where($this->cfg['dbpref']."levels_location", $cond);
			$loc_details = $loc_res->result_array();
			foreach($loc_details as $loc) {
				$locations[] = $loc['location_id'];
			}
			if (!empty($locations)) {
				$locations_ids = array_unique($locations);
				$locations_ids = (array_values($locations)); //reset the keys in the array
			}
			//$locations_ids = implode(",",$locations_ids);
		}
       
        $offset = $this->db->escape_str($offset);	
		$this->db->select('CUST.*, REG.regionid, REG.region_name, COUN.countryid, COUN.country_name');
		$this->db->from($this->cfg['dbpref'].'customers_company as CUST');
		$this->db->join($this->cfg['dbpref'].'region as REG', 'CUST.add1_region = REG.regionid', 'left');
		$this->db->join($this->cfg['dbpref'].'country as COUN', 'CUST.add1_country = COUN.countryid', 'left');
        if ($this->userdata['level'] == 2) {
			$this->db->where_in('CUST.add1_region', $regions_ids);				
		} else if ($this->userdata['level'] == 3) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
		} else if ($this->userdata['level'] == 4) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
		} else if ($this->userdata['level'] == 5) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
			$this->db->where_in('CUST.add1_location', $locations_ids);
		}
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$this->db->where('CUST.created_by', $this->userdata['userid']);
		}
		if(!empty($limit))
		$this->db->limit($limit);
		$customers = $this->db->get();        
        // echo $this->db->last_query();
        return $customers->result_array(); 
    }
	
 	function customer_count() {
    	$res = $this->db->get($this->cfg['dbpref'].'customers');
        return $res->num_rows();
    }
       
    function get_customer($id) {
        $customer = $this->db->get_where($this->cfg['dbpref'].'customers', array('custid' => $id), 1);
        if ($customer->num_rows() > 0) {
            return $customer->result_array();
        } else {
            return FALSE;
        }
    }
	
	function get_lead_customer($id) {
        // $this->db->select('c.custid,c.first_name,c.last_name,cc.company,cc.add1_region,cc.add1_country,cc.add1_state,cc.add1_location');
        $this->db->select('c.*,cc.*');
		$this->db->from($this->cfg['dbpref'].'customers as c');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->where_in('c.custid', $id);
		$sql = $this->db->get();
		// echo $this->db->last_query(); die;
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return FALSE;
        }
    }
	
	function get_contacts($customer_id) {
		$this->db->select('c.custid,c.customer_name,cc.company,cc.add1_region,cc.add1_country,cc.add1_state,cc.add1_location');
		$this->db->from($this->cfg['dbpref'].'customers as c');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->where_in('c.company_id', $customer_id);
		$sql = $this->db->get();
		// echo $this->db->last_query(); die;
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return FALSE;
        }
    }
	
    function get_company($id) {
        $customer = $this->db->get_where($this->cfg['dbpref'].'customers_company', array('companyid' => $id), 1);
        if ($customer->num_rows() > 0) {
            return $customer->result_array();
        } else {
            return FALSE;
        }
    }

	function get_customer_contacts($id) 
	{
		$this->db->select('c.*');
		$this->db->from($this->cfg['dbpref'].'customers as c');
		$this->db->where_in('c.company_id', $id);
		$this->db->order_by('custid','ASC');
		$sql = $this->db->get();		
		if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return FALSE;
        }
        /* $customer = $this->db->get_where($this->cfg['dbpref'].'customers', array('company_id' => $id));
        if ($customer->num_rows() > 0) {
            return $customer->result_array();
        } else {
            return FALSE;
        } */
    }

    /**
     * List of new or updated customers
     * This will be used to generate the vcards
     */
    public function get_updated_customers($all = FALSE) {
        if ($all == TRUE)
        $qry = $this->db->get($this->cfg['dbpref'].'customers');
        else
        $qry = $this->db->get_where($this->cfg['dbpref'].'customers', array('exported' => NULL));
        if ($qry->num_rows() > 0) {
            return $qry->result_array();
        } else {
            return FALSE;
        }
    }
    
    /**
     * Mark exported customers with a timestamp
     */
    public function update_exported_customers($id_set) {
        if (is_array($id_set) && count($id_set)) {
        	$update_data = array('exported' =>NOW());
        	$this->db->where_in('custid', $id_set);
			$this->db->update($this->cfg['dbpref'].'customers', $update_data);
        }
    }
    
    function update_customer($id, $data) {
        $this->db->where('custid', $id);
		$this->db->update($this->cfg['dbpref'] . 'customers', $data);
		if(!empty($data['client_code'])) {
			$this->update_client_details_to_timesheet($data['client_code']);
		}
		return true;
    }
	
	function update_customer_details($companyid, $custid, $post_data) 
	{
		$customer_arr = array();
		$contact_arr = array();
			
		$customer_arr['company'] = $post_data['company'];
		$customer_arr['add1_line1'] = $post_data['add1_line1'];
		$customer_arr['add1_line2'] = $post_data['add1_line2'];
		$customer_arr['add1_suburb'] = $post_data['add1_suburb'];
		$customer_arr['add1_postcode'] = $post_data['add1_postcode'];
		$customer_arr['add1_region'] = $post_data['add1_region'];
		$customer_arr['add1_country'] = $post_data['add1_country'];
		$customer_arr['add1_state'] = $post_data['add1_state'];
		$customer_arr['add1_location'] = $post_data['add1_location'];
		$customer_arr['phone'] = $post_data['phone'];
		$customer_arr['fax'] = $post_data['fax'];
		$customer_arr['email_2'] = $post_data['email_2'];
		$customer_arr['www'] = $post_data['www'];
		$customer_arr['sales_contact_userid_fk'] = $post_data['sales_contact_userid_fk'];
		
		$contact_arr['customer_name']  = $post_data['customer_name'];
		$contact_arr['email_1'] 	   = $post_data['email_1'];
		$contact_arr['position_title'] = $post_data['position_title'];
		$contact_arr['phone_1']	   	   = $post_data['phone_1'];
		$contact_arr['skype_name']	   = $post_data['skype_name'];
		
		$this->db->where('companyid', $companyid);
		$this->db->update($this->cfg['dbpref'] . 'customers_company', $customer_arr);
		// echo $this->db->last_query();
		
        $this->db->where('custid', $custid);
		$this->db->update($this->cfg['dbpref'] . 'customers', $contact_arr);
		// echo $this->db->last_query(); die;
		
		return true;
    }
	
    function update_company($id, $data) {
        $this->db->where('companyid', $id);
		$this->db->update($this->cfg['dbpref'] . 'customers_company', $data);
		if(!empty($data['client_code'])) {
			$this->update_client_details_to_timesheet($data['client_code']);
		}
		return true;
    }
	
    function update_customer_contacts($data,$contact_id) {
		$condn = array('custid'=>$contact_id);
		$this->db->where($condn);
        $this->db->update($this->cfg['dbpref'].'customers',$data);
		return $this->db->affected_rows();
    }
	
    function insert_customer($data) {
	
	    if ( $this->db->insert($this->cfg['dbpref'] . 'customers', $data) ) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return false;
        }
    }
    
    function insert_batch_customer($data, $data_log) {
	
	    if ( $this->db->insert_batch($this->cfg['dbpref'] . 'customers', $data) ) {
            $insert_id = $this->db->insert_id();
			
			//insert_log
			$this->db->insert_batch($this->cfg['dbpref'] . 'logs', $data_log);
			
            return $insert_id;
        } else {
            return false;
        }
    }
    
    function delete_customer($id) 
	{
		$this->db->where('company_id', $id);
        $this->db->delete($this->cfg['dbpref'] . 'customers');
		
        $this->db->where('companyid', $id);
        $this->db->delete($this->cfg['dbpref'] . 'customers_company');
        return TRUE;
    }
    
    function import_list($customers) {
        if (!is_array($customers)) return false;        
        $i = 0;
        foreach ($customers as $cust) {
            if ( $this->db->insert($this->cfg['dbpref'] . 'customers', $cust) ) {
                $i++;
            }
        }
        return $i;
    }
	
	function primary_mail_check($mail) {
		$this->db->select('email_1');
		$this->db->like('email_1', $mail, 'both');
		$res = $this->db->get($this->cfg['dbpref'].'customers');
        return $res->num_rows();
	}

	function get_customer_data($mail) {
		$q = $this->db->get_where($this->cfg['dbpref'].'customers', array('email_1' => mail));
		return $q->row_array();
	}
	
	function check_csl($table, $cnt_name, $id){
		$this->db->where($cnt_name,$id);
		$num_row = $this->db->get($this->cfg['dbpref'].$table)->num_rows();
		return $num_row;
	}
	
	function get_rscl_id($id, $cond, $table_name, $ch_name){		
		if( empty($id) && empty($cond) ) {
			$whr_cond = array($table_name.'_name'=>$ch_name);
		} else {
			$whr_cond = array($table_name.'_name'=>$ch_name, $cond=>$id);
		}
		$this->db->select($table_name.'id');
		$results = $this->db->get_where($this->cfg['dbpref'].$table_name, $whr_cond)->row_array();
		if(!empty($results)) {
			$strreg = $results[$table_name.'id'];
		} else {
			$user_Detail = $this->session->userdata('logged_in_user');
			if( empty($id) && empty($cond) ) {
				$args = array(
					$table_name.'_name' => $ch_name,
					'created_by' => $user_Detail['userid'],
					'modified_by' => $user_Detail['userid'],
					'created' => date('Y-m-d H:i:s'),
					'modified' => date('Y-m-d H:i:s')
				);
			} else {						
				$args = array(
					$cond => $id,
					$table_name.'_name' => $ch_name,
					'created_by' => $user_Detail['userid'],
					'modified_by' => $user_Detail['userid'],
					'created' => date('Y-m-d H:i:s'),
					'modified' => date('Y-m-d H:i:s')
				);	
			}
			$this->db->insert($this->cfg['dbpref'].$table_name, $args); 
			$strreg = $this->db->insert_id();
		}
		return $strreg;				
	}
	
	function insert_customer_upload($data) {
		$this->db->insert($this->cfg['dbpref'].'customers', $data);
	}
	
	function insert_company($data) {
		$this->db->insert($this->cfg['dbpref'].'customers_company', $data);
		return $this->db->insert_id();
	}
	function get_customer_insert_id($data) {
		$this->db->insert($this->cfg['dbpref'].'customers', $data);
		return $this->db->insert_id();
	}
	
	function customer_update($id, $data) {
		$this->db->where('custid', $id);
		$this->db->update($this->cfg['dbpref'].'customers', $data);
	}
	
	function customer_update_isclient($id, $data) {
		$this->db->where('companyid', $id);
		$this->db->update($this->cfg['dbpref'].'customers_company', $data);
		// echo $this->db->last_query(); exit;
	}
	
	/*
	*@Check Customer Status
	*@Method   check_customer_status
	*@table    leads
	*@return as Json response
	*/
	public function check_customer_status($data=array()) {
		//get custid from customer table
		$id = $data['data'];
		
		$res = array();
		$query = 1;
		
		$this->db->select('custid');
		$this->db->where('company_id', $id);
		$sql = $this->db->get($this->cfg['dbpref'].'customers');
        $custid = $sql->result_array();
		if(!empty($custid)){
			foreach($custid as $rec)
			$custids[]= $rec['custid'];
		}
		
		/* $id = $data['data'];
		$this->db->where('custid_fk', $id);
		$query = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		$res = array(); */
		
		if(!empty($custids)){
			$this->db->where_in('custid_fk', $custids);
			$query = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		} else {
			$res['html'] = "YES";
			echo json_encode($res);
			exit;
		}
		if($query == 0) {
			$res['html'] = "YES";
		} else {
			$res['html'] = "NO";
		}
		echo json_encode($res);
		exit;
    }

	/*
	*@Create Client Code
	*@Method create_client_code
	*@parameter Client name
	*@return as Client code
	*@Author Mani.S
	*/
	public function create_client_code($string, $length) { 
		$strings = $string;
		$strings = preg_replace('/[^A-Za-z0-9\-]/', '', $strings); 
		$strings = strtolower($strings);
		$string_count = strlen($strings);
		$new_array = array();
		
		for($i=0; $i<$string_count; $i++) {
	
			$values = substr($strings, $i, $length);
			
			$string_length = strlen($values);
			
			if((int)$string_length < $length) {	

				if($string_length == 2) $len = 1; else $len = 2;	
				$values .= substr($strings, 0, $len);
			}
			
			$new_array[] = $values;
		}
		
		return $new_array;		
    }
	
	
	/*
	*@Create Client Code With Randomly
	*@Method create_client_code_randomly
	*@parameter Client name
	*@return as Random Client code 
	*@Author Mani.S
	*/
	public function create_client_code_randomly($strings, $length=3) { 
		$validCharacters = $strings.'123456789';

        $validCharNumber = strlen($validCharacters);
		
        $result = "";

        for ($i = 0; $i < $length; $i++) {

            $index = mt_rand(0, $validCharNumber - 1);

            $result .= $validCharacters[$index];

        }
        return $result;	
    }
	
	/*
	*@Check Client Code Exists
	*@Method check_client_code_exists
	*@parameter Client Code
	*@return as True or False
	*@Author Mani.S
	*/
	public function check_client_code_exists($client_code) { 
		
		$this->db->where('client_code', $client_code);
		$query = $this->db->get($this->cfg['dbpref'].'customers_company');
		if($query->num_rows == 0) {
			return true;
		} else {
			return false;
		}
    }	
	
	/*
	*@Update client code by name
	*@Method update_client_code
	*@parameter Client name
	*@return as true or false
	*@Author Mani.S
	*/
	public function update_client_code($client_name, $custid) {
		
		/*
		*same company but different contacts(first name & last name)
		*if the company name is same use the same client code for the client
		*/
		$exist_client   = array();
		$available_code = '';
		
		$this->db->select('client_code');
		$this->db->where('companyid !=', $custid);
		$this->db->where('is_client', 1);
		$this->db->where('company', $client_name);
		$this->db->group_by('client_code');
		$query = $this->db->get($this->cfg['dbpref'].'customers_company');
		// echo $this->db->last_query(); die;
		$result = $query->row_array();
		
		if(!empty($result['client_code'])) {
			// $exist_client   = $result['client_code'];
			$available_code = $result['client_code'];
		}
		
		if(empty($available_code)) {
			$arrClientCodes = $this->create_client_code($client_name, 3);
			$randomCode     = $this->create_client_code_randomly($client_name, 3);
			array_push($arrClientCodes, $randomCode);
			
			if(isset($arrClientCodes) && !empty($arrClientCodes)) {
			
				for($i=0; $i<count($arrClientCodes); $i++) {
				
					$client_code_status = $this->check_client_code_exists($arrClientCodes[$i]);
					
					if($client_code_status == true) {
						$available_code = $arrClientCodes[$i];
						break;				
					}			
				}
			}
		}

		if($available_code != '') {
			$available_code = strtoupper($available_code);
			$data = array('client_code'=>$available_code);
			$this->db->where('companyid',$custid);
			$this->db->update($this->cfg['dbpref'] . 'customers_company', $data);
		}
		return $available_code;
    }
	/*
	*@Get Client Details
	*@Method get_all_customers
	*@parameter client_code
	*@return as result array
	*@Author Mani.S
	*/
	function get_all_customers($client_code=false) {
		
		/* $this->db->select('*');
		if($client_code != false) {		
			$this->db->where('client_code',$client_code);		
		}
		$this->db->where('is_client',1);	
        $customer = $this->db->get($this->cfg['dbpref'].'customers');
        if ($customer->num_rows() > 0) {
            return $customer->result_array();
        } else {
            return FALSE;
        } */
		
		$this->db->select('cc.*');
		$this->db->from($this->cfg['dbpref'].'customers as c');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->where_in('cc.client_code', $client_code);
		$sql = $this->db->get();
		// echo $this->db->last_query(); die;
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return FALSE;
        }
		
    }
	
	/*
	*@Check client information already exit or not
	*@Method get_timesheet_client
	*@parameter client_code
	*@return as true or false
	*@Author Mani.S
	*/
	function get_timesheet_client($client_code=false) {
	
		$timesheet_db = $this->load->database('timesheet',TRUE);	
		$timesheet_db->select('*');
		if($client_code != false) {		
		$timesheet_db->where('client_code',$client_code);		
		}
        $clients = $timesheet_db->get($timesheet_db->dbprefix('client'));
        if ($clients->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
	/*
	*@Update Client Details from E-Crm to Timesheet
	*@Method update_client_details_to_timesheet
	*@parameter client_code - Optional
	*@return --
	*@Author Mani.S
	*/
	function update_client_details_to_timesheet($client_code=false)
	{
		
		$arrCustomers = $this->get_all_customers($client_code);
		$timesheet_db = $this->load->database('timesheet',TRUE);		
		
		if(isset($arrCustomers) && !empty($arrCustomers)) {
		
			foreach($arrCustomers as $listCustomers) {
			
				$timesheet_clients = $this->get_timesheet_client($listCustomers['client_code']);
				$city = $this->get_filed_id_by_name('location', 'locationid', $listCustomers['add1_location'], 'location_name');
				$state = $this->get_filed_id_by_name('state', 'stateid', $listCustomers['add1_state'], 'state_name');
				$country = $this->get_filed_id_by_name('country', 'countryid', $listCustomers['add1_country'], 'country_name');	
				
				if($timesheet_clients == FALSE) {						
				
					$sql =  '  INSERT INTO  '.$timesheet_db->dbprefix('client').'   SET '; $where = '';
																		
				}else {
				
					$sql =  '  UPDATE  '.$timesheet_db->dbprefix('client').'   SET '; $where  = '  WHERE `client_code` = "'.$listCustomers['client_code'].'"';
				
				}
					/* $sql .=  ' 															`organisation` = "'.$listCustomers['company'].'",
																						`description` = "'.$listCustomers['comments'].'",
																						`address1` = "'.$listCustomers['add1_line1'].'",
																						`city` = "'.$city.'",
																						`state` = "'.$state.'",
																						`country` = "'.$country.'",
																						`postal_code` = "'.$listCustomers['add1_postcode'].'",
																						`contact_first_name` = "",
																						`contact_last_name` = "",
																						`contact_email` = "'.$listCustomers['email_2'].'",
																						`phone_number` = "'.$listCustomers['phone'].'",
																						`gsm_number` = "",
																						`http_url` = "",
																						`address2` = "'.$listCustomers['add1_line2'].'",
																						`client_code` = "'.$listCustomers['client_code'].'"
																						  '.$where.''; */
																						  
					$sql .=  ' 															`organisation` = "'.$listCustomers['company'].'",
																						`description` = "'.$listCustomers['comments'].'",
																						`address1` = "'.$listCustomers['add1_line1'].'",
																						`city` = "'.$city.'",
																						`state` = "'.$state.'",
																						`country` = "'.$country.'",
																						`postal_code` = "'.$listCustomers['add1_postcode'].'",
																						`contact_email` = "'.$listCustomers['email_2'].'",
																						`phone_number` = "'.$listCustomers['phone'].'",
																						`address2` = "'.$listCustomers['add1_line2'].'",
																						`client_code` = "'.$listCustomers['client_code'].'"
																						  '.$where.'';
				
				$timesheet_db->query($sql);
			
			}		
		}
	}
	
	/*
	*@Get Project Details
	*@Method get_all_projects
	*@parameter project_code 
	*@return as result array or false
	*@Author Mani.S
	*/
	function get_all_projects($project_code=false, $lead_id=false) {
		
		$this->db->select('*');
		if($project_code != false) {		
		$this->db->where('pjt_id',$project_code);		
		}else {
		$this->db->where('pjt_id !=', '');
		}
		if($lead_id != false) {		
		$this->db->where('lead_id',$lead_id);		
		}		
		$this->db->where('pjt_status !=',0);	
		$this->db->where('lead_status',4);	
        $projects = $this->db->get($this->cfg['dbpref'].'leads');
        if ($projects->num_rows() > 0) {
            return $projects->result_array();
        } else {
            return FALSE;
        }
    }	
	/* 
	*@Check project information already exit or not for timesheet
	*@Method get_timesheet_project
	*@parameter project_code
	*@return as true or false
	*@Author Mani.S
	*/
	function get_timesheet_project($project_code=false) {
	
		$timesheet_db = $this->load->database('timesheet',TRUE);	
		$timesheet_db->select('*');
		if($project_code != false) {		
		$timesheet_db->where('project_code',$project_code);		
		}
        $projects = $timesheet_db->get($timesheet_db->dbprefix('project'));
        if ($projects->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }	
	
	/*
	*@Check project information already exit or not for econnect
	*@Method get_econnect_project
	*@parameter project_code
	*@return as true or false
	*@Author Mani.S
	*/
	function get_econnect_project($ProjectCode=false) {
	
		$econnect_db = $this->load->database('econnect',TRUE);	
		$econnect_db->select('*');
		if($ProjectCode != false) {		
		$econnect_db->where('ProjectCode',$ProjectCode);		
		}
        $projects = $econnect_db->get($econnect_db->dbprefix('project_master'));
        if ($projects->num_rows() > 0) {		
            return true;
        } else {	
            return false;
        }
    }	
	
	/*
	*@Update Project Details from E-Crm to Timesheet and Econnect
	*@Method update_project_details
	*@parameter project_code - Optional
	*@return --
	*@Author Mani.S
	*/
	function update_project_details($project_code=false)
	{
		$error = false;
		$arrProjects = $this->get_all_projects($project_code);
		// echo '<pre>'; print_r($arrProjects);exit;
		$timesheet_db = $this->load->database('timesheet',TRUE);
		$econnect_db  = $this->load->database('econnect',TRUE);		
		
		if(isset($arrProjects) && !empty($arrProjects)) {
		
			foreach($arrProjects as $listProjects) {
			/*
			*@Timesheet to insert project details start here
			*/
			
				//get company id using $listProjects['custid_fk']
				$client_det = $this->get_lead_customer($listProjects['custid_fk']);
			
				$timesheet_projects = $this->get_timesheet_project($listProjects['pjt_id']);
				// $client_code        = $this->get_filed_id_by_name('customers', 'custid', $listProjects['custid_fk'], 'client_code');
				$client_code        = $this->get_filed_id_by_name('customers_company', 'companyid', $client_det[0]['companyid'], 'client_code');
				$client             = $this->get_client_id_by_code_from_timesheet($client_code);
				
				$suspended="NULL"; //suspended date set to null by default
				
				if($listProjects['pjt_status'] == 1) {
					$project_status = "Started";
				} else if($listProjects['pjt_status'] == 2) {
					$project_status = "Complete";
					$suspended = '"'.date('Y-m-d').'"'; //suspended date set to current date of entry
				} else if($listProjects['pjt_status'] == 3) {
					$project_status = "Suspended";
					$suspended = '"'.date('Y-m-d').'"'; //suspended date set to current date of entry
				} else if($listProjects['pjt_status'] == 4) {
					$project_status = "Pending";
				}
				
				$strt_date = strtotime($listProjects['date_start']);
				$end_date  = strtotime($listProjects['date_due']);
				$contract_strt_date = strtotime($listProjects['actual_date_start']);
				$contract_end_date  = strtotime($listProjects['actual_date_due']);
				$project_description = $listProjects['lead_title'] .'; ' .$listProjects['pjt_id'];
				$timesheet_sql = '';
				
				
				
				if($timesheet_projects == false) {
					$timesheet_sql =  '  INSERT INTO  '.$timesheet_db->dbprefix('project').'   SET '; $where = '';
				} else {
					$timesheet_sql =  '  UPDATE  '.$timesheet_db->dbprefix('project').'   SET '; 
					$where = '  WHERE  `project_code` = "'.$listProjects['pjt_id'].'" ';
				}
				$timesheet_sql .= 		'						`title` = "'.$listProjects['lead_title'].'",
																`client_id` = "'.$client['client_id'].'",
																`project_type_id` = '.$listProjects['project_type'].',
																`description` = "'.$project_description.'",
																`start_date` = "'.date('Y-m-d', $strt_date).'",
																`deadline` = "'.date('Y-m-d', $end_date).'",
																`proj_status` = "'.$project_status.'",
																`suspended` = '.$suspended.',
																`project_code` = "'.$listProjects['pjt_id'].'",
																`proj_total_hours` = "'.$listProjects['estimate_hour'].'"  '.$where.' ';
				// echo $timesheet_sql; exit;
				$timesheet_ins = $timesheet_db->query($timesheet_sql);
				
				// update project leader in timesheet			
				if($listProjects['assigned_to'])
				{
					$qry = "select crm_leads.assigned_to,crm_users.username from ".$this->cfg['dbpref']."leads join ".$this->cfg['dbpref']."users on crm_leads.assigned_to = crm_users.userid and crm_leads.assigned_to=".$listProjects['assigned_to']." group by crm_users.username";
					
					$result = $this->db->query($qry);
					$nos = $result->num_rows();
					if($nos){
						$rs = $result->row();
						$proj_leader = strtolower($rs->username);
					}
					
					$timesheet_db->update($timesheet_db->dbprefix('project'),array("proj_leader" => $proj_leader),array("project_code" => $project_code));
				}
				
				//update team members in timesheet
				$crm_lead_id = $listProjects['lead_id'];
				$timesheet_proj_id = '';
				$res_tm = $this->db->get_where($this->cfg['dbpref']."contract_jobs",array("jobid_fk" => $crm_lead_id));
				
				$timesheet = $timesheet_db->get_where($timesheet_db->dbprefix('project'),array("project_code" => $project_code));
				if($timesheet->num_rows()>0){
					$tspid = $timesheet->row();
					$timesheet_proj_id = $tspid->proj_id;
				}
				//$timesheet_db->close();
				
				
				if($res_tm->num_rows()>0 && $timesheet_proj_id){
				// creating default task for new project 13/7/2015
					$defaulttask='Default task';
					$status='Started';
					$assigneddate=date('Y-m-d H:i:s');				
					$timesheet_db->insert($timesheet_db->dbprefix('task'),array("proj_id" =>$timesheet_proj_id,"name" => $defaulttask,"assigned" => $assigneddate,"status"=>$status));
					$rs_tm = $res_tm->result_array();
					$crm_username = array();
					foreach($rs_tm as $tm){
						$user_id = $tm['userid_fk'];
						$this->db->select("username");
						$get_crm_user = $this->db->get_where($this->cfg['dbpref']."users",array("userid" => $user_id ));
						if($get_crm_user->num_rows()>0){
							$crm_user_details = $get_crm_user->row();
							$crm_username  = strtolower($crm_user_details->username);
							
							$tm_nos = $timesheet_db->get_where($timesheet_db->dbprefix('assignments'),array("proj_id" => $timesheet_proj_id,"username" => $crm_username,"rate_id" => 1));
							if(!$tm_nos->num_rows()){
								$timesheet_db->insert($timesheet_db->dbprefix('assignments'),array("proj_id" =>	$timesheet_proj_id,"username" => $crm_username,"rate_id" => 1));	
									//Tasks assigned for project assigned members 13/7/2015
									$sql = "SELECT task_id FROM ".$timesheet_db->dbprefix('task')."  WHERE proj_id = '".$timesheet_proj_id."'";	
									$query = $timesheet_db->query($sql);
									$res = $query->result_array();		
									if(count($res) > 0) {
									foreach($res as $row){				
											$taskid = $row['task_id'];
											$timesheet_db->insert($timesheet_db->dbprefix("task_assignments"), array("task_id"=>$taskid,"proj_id"=>$timesheet_proj_id,"username"=>$crm_username));
										}
									}	
									// Ends here
							}
						}
					}
					
					/* if(count($crm_username) > 0){
						$timesheet_db = $this->load->database('timesheet',TRUE);
						foreach($crm_username as $c_user){
							
						}
					} */
				}
				
				if($timesheet_ins) {
					$error = true;
				}
				
			/*
			*@Timesheet to insert project details end here 
			*/
			
			/*
			*@E-Connect to insert project details start here 
			*/
				$econnect_projects = $this->get_econnect_project($listProjects['pjt_id']);
				// $client_code       = $this->get_filed_id_by_name('customers', 'custid', $listProjects['custid_fk'], 'client_code');
				$client_code        = $this->get_filed_id_by_name('customers_company', 'companyid', $client_det[0]['companyid'], 'client_code');
				$project_types     = $this->get_projecttype_name_by_name_from_timesheet($listProjects['project_type']);
				
				$arrBillCategory = $this->get_billing_type_by_id($listProjects['resource_type']);
				$project_center = $this->get_filed_id_by_name('profit_center', 'id', $listProjects['project_center'], 'profit_center');
				$cost_center 	= $this->get_filed_id_by_name('cost_center', 'id', $listProjects['cost_center'], 'cost_center');
				$bill_currency = $this->get_filed_id_by_name('expect_worth', 'expect_worth_id', $listProjects['expect_worth_id'], 'expect_worth_name');
				
				if($listProjects['project_category'] == 1) {
					$is_profit = 1;
					$cost = 0;
					$p_category = "Profit Cen";
				} else {
					$is_profit = 0;
					$cost = 1;
					$p_category = "Cost Cen";
				}
				
				if($listProjects['sow_status'] == 1)
				$sow_status = "Signed";				
				else
				$sow_status = "Unsigned";				
				
				$bill_type = '';
				if($listProjects['billing_type'] == 2) {
					$bill_type = "Monthly";
				} else if($listProjects['billing_type'] == 1) {
					$bill_type = "Milestone Driven ";
				}

				$econnect_sql = '';
				
				if($econnect_projects == FALSE) {					
					$econnect_sql = '  INSERT INTO  '.$econnect_db->dbprefix('project_master').'   SET '; $where_econnect = '';
				} else {
					$econnect_sql = '  UPDATE  '.$econnect_db->dbprefix('project_master').'   SET  '; $where_econnect = '  WHERE   `ProjectCode` = "'.$listProjects['pjt_id'].'" ';
				}
					$econnect_sql .= '				`ClientCode` = "'.$client_code.'",
													`ProjectName` = "'.$listProjects['lead_title'].'",
													`ProjectCode` = "'.$listProjects['pjt_id'].'",
													`ProjectType` = "'.$project_types['project_type_name'].'",
													`ProjectBillingType` = "'.$arrBillCategory['category'].'",
													`ProjectStartDate` = "'.date('Y-m-d', $strt_date).'",
													`ProjectEndDate` = "'.date('Y-m-d', $end_date).'",
													`ContractStartdate` = "'.date('Y-m-d', $contract_strt_date).'",
													`ContractEndDate` = "'.date('Y-m-d', $contract_end_date).'",
													`Duration` = "'.$listProjects['estimate_hour'].'",
													`ProfitCenter` = "'.$project_center.'",
													`isprofit` = '.$is_profit.',
													`Department_id` = "'.$listProjects['department_id_fk'].'",
													`ProjectCategory` = "'.$p_category.'",
													`CostCentre` = "'.$cost_center.'",
													`ProjectCost` = "'.$cost.'",
													`BillingCurrency` = "'.$bill_currency.'",
													`ProjectStatus` = "'.$project_status.'",
													`SOWStatus` = "'.$sow_status.'",
													`BillingCycle` = "'.$bill_type.'",
													`ProjectValue` = "'.$listProjects['actual_worth_amount'].'"  '.  $where_econnect.' ';	

			$econnect_ins = $econnect_db->query($econnect_sql);
			if($econnect_ins) {
				$error = true;
			}
			return $error;
			/*
			*@E-Connect to insert project details end here 
			*/
			}		
		}
	}
	
	function create_cdefault_folders($lead_id){
		$default_folders = $this->get_default_folders();
		if(count($default_folders)>0){
			$ch_num = $this->db->get_where($this->cfg['dbpref']."file_management",array("lead_id" => $lead_id,"folder_name" => $lead_id));
			if($ch_num->num_rows()>0){
				$ch_res = $ch_num->row();
				$minsert_id = $ch_res->folder_id;
			}else{
				$this->db->insert($this->cfg['dbpref']."file_management",array("lead_id" => $lead_id,"folder_name" => $lead_id,"parent" => 0,"created_by" => $this->userdata['userid'],"created_on" => date("Y-m-d H:i:s")));
				$minsert_id = $this->db->insert_id();
			}
			foreach($default_folders as $dfs){
				$folder_name = $dfs['folder_name'];
				$this->insert_folders($lead_id,$folder_name,$dfs['fid'],$minsert_id);
			}
		}
	}
	
	function insert_folders($lead_id,$folder_name,$parent_id,$minsert_id){
		$this->db->insert($this->cfg['dbpref']."file_management",array("lead_id" => $lead_id,"folder_name" => $folder_name,"parent" => $minsert_id,"created_by" => $this->userdata['userid'],"created_on" => date("Y-m-d H:i:s")));
		$second_insert = $this->db->insert_id();
		$check = $this->check_sub_exist($parent_id);
		if(count($check)>0){
			foreach($check as $ck):
				$this->insert_folders($lead_id,$ck['folder_name'],$ck['fid'],$second_insert);
			endforeach;
		}
	}
	
	function check_sub_exist($folder_id){
		$qry = $this->db->get_where($this->cfg['dbpref']."default_folders",array("parent_id" => $folder_id));
		if($qry->num_rows()>0){
			return $qry->result_array();
		}
		return false;
	}
	
	function get_default_folders(){
		$result = '';
		$qry = $this->db->get_where($this->cfg['dbpref']."default_folders",array("status" => 1,"parent_id" => 0));
		if($qry->num_rows()){
			$result = $qry->result_array();
		}
		return $result;
	}
	
	function assign_default_folders($lead_id){
		$lead_users = $this->db->get_where($this->cfg['dbpref']."contract_jobs",array("jobid_fk" => $lead_id));
		$lead_folders = $this->db->get_where($this->cfg['dbpref']."file_management",array("lead_id" => $lead_id));
		if($lead_users->num_rows()>0 && $lead_folders->num_rows()>0){
			$users_result = $lead_users->result_array();
			$folders_result = $lead_folders->result_array();
			foreach($users_result as $urs){
				$user = $urs['userid_fk'];
				foreach($folders_result as $frs){
					$this->db->insert($this->cfg['dbpref']."project_folder_access",array("folder_id" => $frs['folder_id'],"user_id" => $user,"is_recursive" => 1,"add_access" => 1,"download_access" => 1,"created_on" => date("Y-m-d H:i:s"),"created_by" => $this->userdata['userid']));
				}
			}
		}
	}
	
	/*
	 *@Database E-Connect and Timesheet
	 *@method update_date_to_timesheet_econnect
	 *@Update Start date, End date, Actual start date and Actual end date
	 *@Parameter lead_id.
	 *@Author eNoah - Mani.S
	 */
	function update_date_to_timesheet_econnect($lead_id=false)
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);
		$econnect_db  = $this->load->database('econnect',TRUE);		
		$arrProjects  = $this->get_all_projects(false, $lead_id);
		
		if(isset($arrProjects) && !empty($arrProjects)) {
		
			foreach($arrProjects as $listProjects) {
			
				$project_code 		   = $listProjects['pjt_id'];
				$timesheet_projects    = $this->get_timesheet_project($listProjects['pjt_id']);
				if($timesheet_projects == TRUE) {
					$strt_date = strtotime($listProjects['actual_date_start']);
					$end_date  = strtotime($listProjects['actual_date_due']);
				
					$timesheet_db->query( ' UPDATE  '.$timesheet_db->dbprefix('project').' SET 
											`start_date` = "'.date('Y-m-d', $strt_date).'",
											`deadline`   = "'.date('Y-m-d', $end_date).'"
											WHERE `project_code` = "'.$listProjects['pjt_id'].'"');
				}
				
				$econnect_projects = $this->get_econnect_project($listProjects['pjt_id']);
				
				if($econnect_projects == TRUE) {
					$strt_date = strtotime($listProjects['actual_date_start']);
					$end_date  = strtotime($listProjects['actual_date_due']);
					// $contract_strt_date = strtotime($listProjects['actual_date_start']);
					// $contract_end_date  = strtotime($listProjects['actual_date_due']);
					
					$econnect_db->query( ' UPDATE  '.$econnect_db->dbprefix('project_master').' SET 
											`ProjectStartDate` = "'.date('Y-m-d', $strt_date).'",
											`ProjectEndDate` = "'.date('Y-m-d', $end_date).'"
											WHERE `ProjectCode` = "'.$listProjects['pjt_id'].'"');
				}			
			}
		}
	
	}
	
	/*
	 *@Database E-Crm
	 *@method get_filed_id_by_name
	 *@Use Get individual colum name by value
	 *@Parameter table name, condition field, condition field value, return field name.
	 *@Author eNoah - Mani.S
	 */
	function get_filed_id_by_name($table, $filed_id, $filed_id_value, $files_name)
	{
		$this->db->select('*');
		$this->db->where($filed_id, $filed_id_value);
		$query = $this->db->get($this->cfg['dbpref'].$table);
		$result = $query->row_array();
		return $result[$files_name];
	}
	
	/*
	 *@Database timesheet
	 *@method get_billing_type_by_id
	 *@Use Get billing categories
	 *@Author eNoah - Mani.S
	 */
	public function get_billing_type_by_id($bill_id)
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);		
		$timesheet_db->select('bill_id,category');
		$timesheet_db->where('bill_id', $bill_id);
		$timesheet_db->from($timesheet_db->dbprefix('bill_categories'));		
		$query = $timesheet_db->get();
		return $query->row_array();		
	}
	
	/*
	 *@Database timesheet
	 *@method get_client_id from timesheet database table client table
	 *@Use Get Client code
	 *@Author eNoah - Mani.S
	 */
	public function get_client_id_by_code_from_timesheet($client_code)
	{
		//echo $client_code;exit;
		$timesheet_db = $this->load->database('timesheet',TRUE);		
		$timesheet_db->select('*');
		$timesheet_db->where('client_code', $client_code);
		$timesheet_db->from($timesheet_db->dbprefix('client'));		
		$query = $timesheet_db->get();
		return $query->row_array();		
	}
	
	/*
	 *@Database timesheet
	 *@method get_client_id from timesheet database table client table
	 *@Use Get Client code
	 *@Author eNoah - Mani.S
	 */
	public function get_projecttype_name_by_name_from_timesheet($project_type_id)
	{
	//echo $client_code;exit;
		$timesheet_db = $this->load->database('timesheet',TRUE);		
		$timesheet_db->select('*');
		$timesheet_db->where('project_type_id', $project_type_id);
		$timesheet_db->from($timesheet_db->dbprefix('project_types'));		
		$query = $timesheet_db->get();
		return $query->row_array();		
	}
	
	function get_records_by_num($tbl, $wh_condn)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		$this->db->where($wh_condn);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	function get_records_by_id($tbl, $wh_condn)
	{
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		$this->db->where($wh_condn);
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->row_array();
	}
	
	function check_customer_company($strreg, $strcunt, $strstate, $strlid, $cmp_name, $cmp_email=false)
	{
		// customers_company
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref']."customers_company");
		$this->db->where('add1_region', $strreg);
		$this->db->where('add1_country', $strcunt);
		$this->db->where('add1_state', $strstate);
		$this->db->where('add1_location', $strlid);
		if($cmp_name!="")
		$this->db->where('company', $cmp_name);
		if($cmp_email!="")
		$this->db->where('email_2', $cmp_email);
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->row_array();
	}

	function check_customer_details($company_id, $cust_email=false)
	{
		// customers
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref']."customers");
		$this->db->where('company_id', $company_id);
		if($cust_email!="")
		$this->db->where('email_1', $cust_email);
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->row_array();
	}
    
	function customer_contact_list($customers)
	{
		if(!empty($customers)){
			foreach($customers as $list){
				$company_id[] = $list['companyid'];
			}
		}
		
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref']."customers");
		$this->db->join($this->cfg['dbpref'].'customers_company', $this->cfg['dbpref'].'customers_company.companyid = '.$this->cfg['dbpref'].'customers.company_id');
		if(!empty($company_id))
		$this->db->where_in('company_id',$company_id);
		
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	function delete_customer_contact($custid) 
	{
		$contact_log_data = $this->db->get_where($this->login_model->cfg['dbpref'] . 'customers', array('custid' => $custid))->row_array();

        $this->db->where('custid', $custid);
        $res = $this->db->delete($this->cfg['dbpref'] . 'customers');
		if($res) {
			$company_log_data = $this->db->get_where($this->login_model->cfg['dbpref'] . 'customers_company', array('companyid' => $contact_log_data['company_id']))->row_array();
			$ins_log 					= array();
			$ins_log['jobid_fk']    	= 0;
			$ins_log['userid_fk']   	= $this->userdata['userid'];
			$ins_log['date_created'] 	= date('Y-m-d H:i:s');
			$ins_log['log_content'] 	= $contact_log_data['customer_name']." Contact Deleted for the company ".$company_log_data['company']." On :" . " " . date('M j, Y g:i A');
			$log_res = $this->customer_model->insert_row("logs", $ins_log);
		}
        return TRUE;
    }
	
	function get_companies($name)
	{
		//customer restriction on level based.
		if ($this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4 || $this->userdata['level'] == 5) {
			$cond = array('level_id' => $this->userdata['level'], 'user_id' => $this->userdata['userid']);
			
			$this->db->select('region_id');
		 	$reg_res = $this->db->get_where($this->cfg['dbpref']."levels_region", $cond);
			$reg_details = $reg_res->result_array();
			foreach($reg_details as $reg) {
				$regions[] = $reg['region_id'];
			}
			$regions_ids = array_unique($regions);
			$regions_ids = (array_values($regions)); //reset the keys in the array
		
			//restriction for country
			$this->db->select('country_id');
			$coun_res = $this->db->get_where($this->cfg['dbpref']."levels_country", $cond);
			$coun_details = $coun_res->result_array();
			foreach($coun_details as $coun) {
				$countries[] = $coun['country_id'];
			}
			if (!empty($countries)) {
				$countries_ids = array_unique($countries);
				$countries_ids = (array_values($countries)); //reset the keys in the array
			}
		
			//restriction for state
			$this->db->select('state_id');
			$state_res = $this->db->get_where($this->cfg['dbpref']."levels_state", $cond);
			$ste_details = $state_res->result_array();
			foreach($ste_details as $ste) {
				$states[] = $ste['state_id'];
			}
			if (!empty($states)) {
				$states_ids = array_unique($states);
				$states_ids = (array_values($states)); //reset the keys in the array				
			}
		
			//restriction for location
			$this->db->select('location_id');
			$loc_res = $this->db->get_where($this->cfg['dbpref']."levels_location", $cond);
			$loc_details = $loc_res->result_array();
			foreach($loc_details as $loc) {
				$locations[] = $loc['location_id'];
			}
			if (!empty($locations)) {
				$locations_ids = array_unique($locations);
				$locations_ids = (array_values($locations)); //reset the keys in the array
			}
		}
       	
		$this->db->select('CUST.*');
		$this->db->from($this->cfg['dbpref'].'customers_company as CUST');
		// $this->db->join($this->cfg['dbpref'].'region as REG', 'CUST.add1_region = REG.regionid', 'left');
		// $this->db->join($this->cfg['dbpref'].'country as COUN', 'CUST.add1_country = COUN.countryid', 'left');
        if ($this->userdata['level'] == 2) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
		} else if ($this->userdata['level'] == 3) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
		} else if ($this->userdata['level'] == 4) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
		} else if ($this->userdata['level'] == 5) {
			$this->db->where_in('CUST.add1_region', $regions_ids);
			$this->db->where_in('CUST.add1_country', $countries_ids);
			$this->db->where_in('CUST.add1_state', $states_ids);
			$this->db->where_in('CUST.add1_location', $locations_ids);
		}
		if($this->userdata['role_id'] == 14) { /*Condition for Reseller user*/
			$this->db->where('CUST.created_by', $this->userdata['userid']);
		}
		if(!empty($name))
		$this->db->where("(company LIKE '%$name%')");
		$customers = $this->db->get();
        // echo $this->db->last_query(); exit;
        return $customers->result_array();
    }
	
	function insert_row($tbl, $ins) {
		return $this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
    }
}
/* end of file */