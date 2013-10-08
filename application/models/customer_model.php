<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customer_model extends crm_model {
    
    public $userdata;
    
    function __construct()
    {
        parent::__construct();
        $this->userdata = $this->session->userdata('logged_in_user');
    }
    
    function customer_list($offset, $search, $order_field = 'last_name', $order_type = 'asc')
    {
        $restrict = '';
        $restrict_search = '';
		//echo "<pre>"; print_r($this->userdata);
		//customer restriction on level based.
	if ($this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4 || $this->userdata['level'] == 5) {
		$query = $this->db->query("SELECT region_id FROM ".$this->cfg['dbpref']."levels_region WHERE level_id = '".$this->userdata['level']."' AND user_id = '".$this->userdata['userid']."' ");
		$reg_details = $query->result_array();
		foreach($reg_details as $reg)
		{
			$regions[] = $reg['region_id'];
		}
		$regions_ids = array_unique($regions);
		$regions_ids = (array_values($regions)); //reset the keys in the array
		$regions_ids = implode(",",$regions_ids);
		
		//restriction for country
		$coun_query = $this->db->query("SELECT country_id FROM ".$this->cfg['dbpref']."levels_country WHERE level_id = '".$this->userdata['level']."' AND user_id = '".$this->userdata['userid']."' ");
		$coun_details = $coun_query->result_array();
		foreach($coun_details as $coun)
		{
			$countries[] = $coun['country_id'];
		}
		$countries_ids = array_unique($countries);
		$countries_ids = (array_values($countries)); //reset the keys in the array
		$countries_ids = @implode(",",$countries_ids);
		
		//restriction for state
		$ste_query = $this->db->query("SELECT state_id FROM ".$this->cfg['dbpref']."levels_state WHERE level_id = '".$this->userdata['level']."' AND user_id = '".$this->userdata['userid']."' ");
		$ste_details = $ste_query->result_array();
		foreach($ste_details as $ste)
		{
			$states[] = $ste['state_id'];
		}
		$states_ids = array_unique($states);
		$states_ids = (array_values($states)); //reset the keys in the array
		$states_ids = implode(",",$states_ids);
		
		//restriction for location
		$loc_query = $this->db->query("SELECT location_id FROM ".$this->cfg['dbpref']."levels_location WHERE level_id = '".$this->userdata['level']."' AND user_id = '".$this->userdata['userid']."' ");
		$loc_details = $loc_query->result_array();
		foreach($loc_details as $loc)
		{
			$locations[] = $loc['location_id'];
		}
		$locations_ids = array_unique($locations);
		$locations_ids = (array_values($locations)); //reset the keys in the array
		$locations_ids = implode(",",$locations_ids);
		
		if ($this->userdata['level'] == 2) {
			$restrict = " WHERE CUST.add1_region IN (".$regions_ids.")";
			$restrict_search = " CUST.add1_region IN (".$regions_ids.") AND";
		} else if ($this->userdata['level'] == 3) {
			$restrict = " WHERE CUST.add1_region IN (".$regions_ids.") AND CUST.add1_country IN (".$countries_ids.")";
			$restrict_search = " CUST.add1_region IN (".$regions_ids.") AND CUST.add1_country IN (".$countries_ids.") AND";
		} else if ($this->userdata['level'] == 4) {
			$restrict = " WHERE CUST.add1_region IN (".$regions_ids.") AND CUST.add1_country IN (".$countries_ids.") AND CUST.add1_state IN (".$states_ids.")";
			$restrict_search = " CUST.add1_region IN (".$regions_ids.") AND CUST.add1_country IN (".$countries_ids.") AND CUST.add1_state IN (".$states_ids.") AND ";
		} else if ($this->userdata['level'] == 5) {
			$restrict = " WHERE CUST.add1_region IN (".$regions_ids.") AND CUST.add1_country IN (".$countries_ids.") AND CUST.add1_state IN (".$states_ids.") AND CUST.add1_location IN (".$locations_ids.")";
			$restrict_search = " CUST.add1_region IN (".$regions_ids.") AND CUST.add1_country IN (".$countries_ids.") AND CUST.add1_state IN (".$states_ids.") AND CUST.add1_location IN (".$locations_ids.") AND ";
		}
	}
        if ($search != false)
        {
            $search = mysql_real_escape_string(urldecode($search));
 					
			$sql = "SELECT *
                    FROM ".$this->cfg['dbpref']."customers as CUST
					LEFT JOIN ".$this->cfg['dbpref']."region as REG ON CUST.add1_region = REG.regionid
					LEFT JOIN ".$this->cfg['dbpref']."country as COUN ON CUST.add1_country = COUN.countryid
                    WHERE
                    {$restrict_search}
                    (
                        CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%$search%'
                        OR `first_name` LIKE '%$search%'
                        OR `last_name` LIKE '%$search%'
                        OR `company` LIKE '%$search%'
                        OR `email_1` LIKE '%$search%'
                    )";
        }
        else
        {
            $offset = mysql_real_escape_string($offset);
					
			$sql = "SELECT CUST.*, REG.regionid, REG.region_name, COUN.countryid, COUN.country_name
					FROM ".$this->cfg['dbpref']."customers AS CUST 
					LEFT JOIN ".$this->cfg['dbpref']."region as REG ON CUST.add1_region = REG.regionid
					LEFT JOIN ".$this->cfg['dbpref']."country as COUN ON CUST.add1_country = COUN.countryid {$restrict} ";
        }
        $customers = $this->db->query($sql);
        return $customers->result_array(); //echo "<pre>"; print_r($customers); exit;
    }
    
    function customer_count()
    {
        $restrict = '';
        $restrict_search = '';
        $sql = "SELECT *
                    FROM `".$this->cfg['dbpref']."customers`
                    {$restrict}";
        
        $customers = $this->db->query($sql);

        return count($customers->result_array());
    }
    
    function get_customer($id)
    {
        $this->db->join($this->cfg['dbpref'].'cust_cat_join', 'custid = custid_fk', 'left');
        $customer = $this->db->get_where($this->cfg['dbpref'].'customers', array('custid' => $id), 1);
        if ($customer->num_rows() > 0)
        {
            return $customer->result_array();
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * List of new or updated customers
     * This will be used to generate the vcards
     */
    public function get_updated_customers($all = FALSE)
    {
        $where = 'WHERE `exported` IS NULL';
        if ($all == TRUE)
        {
            $where = '';
        }
        $sql = "SELECT *
                FROM `".$this->cfg['dbpref']."customers`
                $where";
        $rs = $this->db->query($sql);
        
        if ($rs->num_rows() > 0)
        {
            return $rs->result_array();
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * Mark exported customers with a timestamp
     */
    public function update_exported_customers($id_set)
    {
        if (is_array($id_set) && count($id_set))
        {
            $part = implode(', ', $id_set);
            $sql = "UPDATE `".$this->cfg['dbpref']."customers` SET `exported` = NOW() WHERE `custid` IN ({$part})";
            $this->db->query($sql);
        }
    }
    
    function category_list()
    {
        $this->db->order_by("custcatid", "asc"); 
        $customers = $this->db->get($this->cfg['dbpref'].'customer_categories');
        
        $cats = $customers->result_array();
        
        for ($i = 0; $i < count($cats); $i++)
        {
            $this->db->where('custcatid_fk', $cats[$i]['custcatid']);
            $this->db->from($this->cfg['dbpref'].'cust_cat_join');
            $cats[$i]['user_count'] = $this->db->count_all_results();
        }
        
        return $cats;
    }
    
    public function sales_agent_list()
    {
        $config = $this->config->item('crm');
        $sales_codes = implode("', '", array_keys($config['sales_codes']));
        $sql = "SELECT `userid`, `first_name`, `last_name`, `sales_code`
                FROM `".$this->cfg['dbpref']."users`
                WHERE `level` = 4
                AND `sales_code` IN ('{$sales_codes}')";
        $q = $this->db->query($sql);
        if ($q->num_rows() > 0)
        {
            return $q->result_array();
        }
        else
        {
            return array();
        }
    }
    
    public function customer_categories($id)
    {
        $customers = $this->db->get_where($this->cfg['dbpref'].'cust_cat_join', array('custid_fk' => $id));
        $cats = array();
        if ($customers->num_rows() > 0)
        {
            $res = $customers->result_array();
            foreach ($res as $row)
            {
                $cats[] = $row['custcatid_fk'];
            }
        }
        
        return $cats;
        
    }
    
    public function customer_sales_agent($id)
    {
        $customers = $this->db->get_where($this->cfg['dbpref'].'cust_user_join', array('custid_fk' => $id));
        $cats = array();
        if ($customers->num_rows() > 0)
        {
            $res = $customers->result_array();
            foreach ($res as $row)
            {
                $cats[] = $row['userid_fk'];
            }
        }
        
        return $cats;
    }
    
    function get_category($id, $type = 'custcatid')
    {
        $type = ($type == 'custcatid') ? 'custcatid' : 'category_name';
        $customer = $this->db->get_where($this->cfg['dbpref'].'customer_categories', array($type => $id), 1);
        if ($customer->num_rows() > 0)
        {
            return $customer->result_array();
        }
        else
        {
            return FALSE;
        }
    }
    
    function update_customer($id, $data, $categories = array(), $sales_agents = array())
    {
        $this->db->delete($this->cfg['dbpref'] . 'cust_cat_join', array('custid_fk' => $id));
        if ($this->userdata['level'] != 4)
        {    
            $this->db->delete($this->cfg['dbpref'] . 'cust_user_join', array('custid_fk' => $id));
        }
        else
        {
            $this->db->delete($this->cfg['dbpref'] . 'cust_user_join', array('custid_fk' => $id, 'userid_fk' => $this->userdata['userid']));
        }
        
        if (is_array($categories) && count($categories)) foreach ($categories as $category)
        {
            @$this->db->insert($this->cfg['dbpref'] . 'cust_cat_join', array('custid_fk' => $id, 'custcatid_fk' => $category));
        }
        if (is_array($sales_agents) && count($sales_agents)) foreach ($sales_agents as $sa)
        {
            @$this->db->insert($this->cfg['dbpref'] . 'cust_user_join', array('custid_fk' => $id, 'userid_fk' => $sa));
        }
        $this->db->where('custid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'customers', $data);
    }
    
    function update_category($id, $data)
    {
        $this->db->where('custcatid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'customer_categories', $data);
    }
    
    function insert_customer($data, $categories = array(), $sales_agents = array())
    {
        if ( $this->db->insert($this->cfg['dbpref'] . 'customers', $data) )
        {
            $insert_id = $this->db->insert_id();
            if (is_array($categories) && count($categories)) foreach ($categories as $category)
            {
                @$this->db->insert($this->cfg['dbpref'].'cust_cat_join', array('custid_fk' => $insert_id, 'custcatid_fk' => $category));
            }
            if (is_array($sales_agents) && count($sales_agents)) foreach ($sales_agents as $sa)
            {
                @$this->db->insert($this->cfg['dbpref'].'cust_user_join', array('custid_fk' => $insert_id, 'userid_fk' => $sa));
            }
            return $insert_id;
        }
        else
        {
            return false;
        }
    }
    
    function insert_category($data)
    {
        if ( $this->db->insert($this->cfg['dbpref'] . 'customer_categories', $data) )
        {
            return $this->db->insert_id();
        }
        else
        {
            return false;
        }
    }
    
    function delete_customer($id)
    {
        $this->db->where('custid', $id);
        $this->db->delete($this->cfg['dbpref'] . 'customers');
        
        $this->db->where('custid_fk', $id);
        $this->db->delete($this->cfg['dbpref'] . 'cust_cat_join');
        
        $this->db->where('custid_fk', $id);
        $this->db->delete($this->cfg['dbpref'] . 'cust_user_join');
        
        return TRUE;
    }
    
    function delete_category($id)
    {
        $this->db->where('custcatid', $id);
        return $this->db->delete($this->cfg['dbpref'] . 'customer_categories');
    }
    
    function import_list($customers)
    {
        if (!is_array($customers)) return false;
        
        $i = 0;
        foreach ($customers as $cust)
        {
            if ( $this->db->insert($this->cfg['dbpref'] . 'customers', $cust) )
            {
                $i++;
            }
        }
        return $i;
    }
    
}

/* end of file */
