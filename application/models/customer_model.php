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
         $this->db->update($this->cfg['dbpref'] . 'customers', $data);
		$this->update_client_details_to_timesheet($data['client_code']);
		return true;
		
    }
    
    function insert_customer($data) {
	
	    if ( $this->db->insert($this->cfg['dbpref'] . 'customers', $data) ) {
            $insert_id = $this->db->insert_id();			
			$client_code = $this->update_client_code($data['first_name'].$data['last_name'], $insert_id);
			$this->update_client_details_to_timesheet($client_code);
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
		
		$this->db->where('client_code ', $client_code);
		$query = $this->db->get($this->cfg['dbpref'].'customers');
		if($query->num_rows == 0)
		{		
			return true;
			
		}else {		
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
		
		$arrClientCodes = $this->create_client_code($client_name, 3);
		$randomCode = $this->create_client_code_randomly($client_name, 3);
		array_push($arrClientCodes, $randomCode);
		$available_code = '';		
		
		if(isset($arrClientCodes) && !empty($arrClientCodes)) {
		
			for($i=0; $i<count($arrClientCodes); $i++) {
			
				$client_code_status = $this->check_client_code_exists($arrClientCodes[$i]);
				
				if($client_code_status == true) {
				
					$available_code = $arrClientCodes[$i];
					break;				
				}			
			}		
		}
		
		if($available_code != '') {		
		$available_code = strtoupper($available_code);
		$data=array('client_code'=>$available_code);
		$this->db->where('custid',$custid);
		$this->db->update($this->cfg['dbpref'] . 'customers', $data);
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
		
		$this->db->select('*');
		if($client_code != false) {		
		$this->db->where('client_code',$client_code);		
		}
		$this->db->where('is_client',1);	
        $customer = $this->db->get($this->cfg['dbpref'].'customers');
        if ($customer->num_rows() > 0) {
            return $customer->result_array();
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
					$sql .=  ' 															`organisation` = "'.$listCustomers['company'].'",
																						`description` = "'.$listCustomers['comments'].'",
																						`address1` = "'.$listCustomers['add1_line1'].'",
																						`city` = "'.$city.'",
																						`state` = "'.$state.'",
																						`country` = "'.$country.'",
																						`postal_code` = "'.$listCustomers['add1_postcode'].'",
																						`contact_first_name` = "'.$listCustomers['first_name'].'",
																						`contact_last_name` = "'.$listCustomers['last_name'].'",
																						`contact_email` = "'.$listCustomers['email_1'].'",
																						`phone_number` = "'.$listCustomers['phone_1'].'",
																						`gsm_number` = "'.$listCustomers['phone_3'].'",
																						`http_url` = "'.$listCustomers['www_1'].'",
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
		
		$arrProjects = $this->get_all_projects($project_code);
		//echo '<pre>'; print_r($arrProjects);exit;
		$timesheet_db = $this->load->database('timesheet',TRUE);
		$econnect_db = $this->load->database('econnect',TRUE);		
		
		if(isset($arrProjects) && !empty($arrProjects)) {
		
			foreach($arrProjects as $listProjects) {
						
			/*
			*@Timesheet to insert project details start here 
			*/
				$timesheet_projects = $this->get_timesheet_project($listProjects['pjt_id']);
				$client_code = $this->get_filed_id_by_name('customers', 'custid', $listProjects['custid_fk'], 'client_code');
				$client = $this->get_client_id_by_code_from_timesheer($client_code);
				
				
				if($listProjects['pjt_status'] == 1) {
				$project_status = "Pending";
				}else if($listProjects['pjt_status'] == 2) {
				$project_status = "Complete";
				}else if($listProjects['pjt_status'] == 3) {
				$project_status = "Started";
				}else if($listProjects['pjt_status'] == 4) {
				$project_status = "Suspended";
				}
				$strt_date = strtotime($listProjects['date_start']);
				$end_date = strtotime($listProjects['date_due']);
				$contract_strt_date = strtotime($listProjects['actual_date_start']);
				$contract_end_date = strtotime($listProjects['actual_date_due']);
				$timesheet_sql = '';
				if($timesheet_projects == false) {
							
					$timesheet_sql =  '  INSERT INTO  '.$timesheet_db->dbprefix('project').'   SET '; $where = '';
								
				}else {
				
					$timesheet_sql =  '  UPDATE  '.$timesheet_db->dbprefix('project').'   SET '; $where = '  WHERE  `project_code` = "'.$listProjects['pjt_id'].'" ';
				
				}
					$timesheet_sql .= 		'											`title` = "'.$listProjects['lead_title'].'",
																						`client_id` = "'.$client['client_id'].'",
																						`project_type_id` = '.$listProjects['project_type'].',
																						`start_date` = "'.date('Y-m-d', $strt_date).'",
																						`deadline` = "'.date('Y-m-d', $end_date).'",
																						`proj_status` = "'.$project_status.'",
																						`project_code` = "'.$listProjects['pjt_id'].'",
																						`proj_total_hours` = "'.$listProjects['estimate_hour'].'"  '.$where.' ';	
																						
																					//	echo $timesheet_sql;
				
					$timesheet_db->query($timesheet_sql);
				
			/*
			*@Timesheet to insert project details end here 
			*/
			
			/*
			*@E-Connect to insert project details start here 
			*/
			
				$econnect_projects = $this->get_econnect_project($listProjects['pjt_id']);
				$client_code = $this->get_filed_id_by_name('customers', 'custid', $listProjects['custid_fk'], 'client_code');
				
				$project_types = $this->get_projecttype_name_by_name_from_timesheet($listProjects['project_type']);
				
				$arrBillCategory = $this->get_billing_type_by_id($listProjects['resource_type']);
				$project_center = $this->get_filed_id_by_name('profit_center', 'id', $listProjects['project_center'], 'profit_center');
				$cost_center = $this->get_filed_id_by_name('cost_center', 'id', $listProjects['cost_center'], 'cost_center');
				$bill_currency = $this->get_filed_id_by_name('expect_worth', 'expect_worth_id', $listProjects['expect_worth_id'], 'expect_worth_name');
				
				if($listProjects['project_category'] == 1) {
				$is_profit = 1;
				$cost = 0;
				$p_category = "Profit Cen";
				}else {
				$is_profit = 0;
				$cost = 1;
				$p_category = "Cost Cen";
				}
				
				if($listProjects['sow_status'] == 1) {
				$sow_status = "Signed";				
				}else {
				$sow_status = "Unsigned";				
				}
				
				$bill_type = '';
				if($listProjects['billing_type'] == 2) {
				$bill_type = "Monthly";
				}else if($listProjects['billing_type'] == 1) {
				$bill_type = "Milestone Driven ";
				}
							
				$econnect_sql = '';
				
				if($econnect_projects == FALSE) {					
				
					$econnect_sql = '  INSERT INTO  '.$econnect_db->dbprefix('project_master').'   SET '; $where_econnect = '';
												
				}else {
				
					$econnect_sql = '  UPDATE  '.$econnect_db->dbprefix('project_master').'   SET  '; $where_econnect = '  WHERE   `ProjectCode` = "'.$listProjects['pjt_id'].'" ';
								
				}
					$econnect_sql .= '													`ClientCode` = "'.$client_code.'",
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
																						`ProjectValue` = "'.$listProjects['actual_worth_amount'].'"  '.$where_econnect.' ';	

					$econnect_db->query($econnect_sql);			
			/*
			*@E-Connect to insert project details end here 
			*/
			
			}		
		}
	}
	
	/*
	 *@Database E-Connect and Timesheet
	 *@method update_date_to_timesheer_econnect
	 *@Update Start date, End date, Actual start date and Actual end date
	 *@Parameter lead_id.
	 *@Author eNoah - Mani.S
	 */
	function update_date_to_timesheer_econnect($lead_id=false)
	{
		$timesheet_db = $this->load->database('timesheet',TRUE);
		$econnect_db = $this->load->database('econnect',TRUE);		
		$arrProjects = $this->get_all_projects(false, $lead_id);
		
		if(isset($arrProjects) && !empty($arrProjects)) {
		
			foreach($arrProjects as $listProjects) {
			
				$project_code = $listProjects['pjt_id'];
				$timesheet_projects = $this->get_timesheet_project($listProjects['pjt_id']);
				if($timesheet_projects == TRUE) {
				
					$strt_date = strtotime($listProjects['date_start']);
					$end_date = strtotime($listProjects['date_due']);				
				
					$timesheet_db->query( '  UPDATE  '.$timesheet_db->dbprefix('project').'   SET 
																					`start_date` = "'.date('Y-m-d', $strt_date).'",
																					`deadline` = "'.date('Y-m-d', $end_date).'"
																					 WHERE `project_code` = "'.$listProjects['pjt_id'].'"');
				
				}
				
				$econnect_projects = $this->get_econnect_project($listProjects['pjt_id']);
				if($econnect_projects == TRUE) {
				
				$strt_date = strtotime($listProjects['date_start']);
				$end_date = strtotime($listProjects['date_due']);
				$contract_strt_date = strtotime($listProjects['actual_date_start']);
				$contract_end_date = strtotime($listProjects['actual_date_due']);
				
				
				$econnect_db->query( '  UPDATE  '.$econnect_db->dbprefix('project_master').'   SET 
																						`ProjectStartDate` = "'.date('Y-m-d', $strt_date).'",
																						`ProjectEndDate` = "'.date('Y-m-d', $end_date).'",
																						`ContractStartdate` = "'.date('Y-m-d', $contract_strt_date).'",
																						`ContractEndDate` = "'.date('Y-m-d', $contract_end_date).'"
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
	public function get_client_id_by_code_from_timesheer($client_code)
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
    
}
/* end of file */