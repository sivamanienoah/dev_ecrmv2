<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customer_model extends crm_model {
    
    public $userdata;
    
    function __construct()
    {
        parent::__construct();
        $this->userdata = $this->session->userdata('logged_in_user');
    }
    
    function customer_list($offset, $search, $order_field = 'last_name', $order_type = 'asc') {
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
       
        $offset = mysql_real_escape_string($offset);		
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
			$search = mysql_real_escape_string(urldecode($search));
			$this->db->where("(first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR company LIKE '%$search%' OR email_1 LIKE '%$search%')");
		}
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
        return $this->db->update($this->cfg['dbpref'] . 'customers', $data);
    }
    
    function insert_customer($data) {
        if ( $this->db->insert($this->cfg['dbpref'] . 'customers', $data) ) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return false;
        }
    }
    
    function delete_customer($id) 
	{
        $this->db->where('custid', $id);
        $this->db->delete($this->cfg['dbpref'] . 'customers');               
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
	
	function get_customer_insert_id($data) {
		$this->db->insert($this->cfg['dbpref'].'customers', $data);
		return $this->db->insert_id();
	}
	
	function customer_update($id, $data) {
		$this->db->where('custid', $id);
		$this->db->update($this->cfg['dbpref'].'customers', $data);	
	}
	
	/*
	*@Check Customer Status
	*@Method   check_customer_status
	*@table    leads
	*@return as Json response
	*/
	public function check_customer_status($data=array()) { 
		$id = $data['data'];
		$this->db->where('custid_fk', $id);
		$query = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		$res = array();
		if($query == 0) {
			$res['html'] = "YES";
		} else {
			$res['html'] = "NO";
		}
		echo json_encode($res);
		exit;
    }
	
    
}

/* end of file */
