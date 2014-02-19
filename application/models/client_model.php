<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Client_model extends crm_model 
{
    
    public $userdata;
    
    function __construct()
    {
        parent::__construct();
        $this->userdata = $this->session->userdata('logged_in_user');
    }
	
	/*
	*@Get User list
	*@Method  user_list
	*/
	function client_list()
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
       	
		$this->db->select('CT.client_id, CT.client_name, CT.region_id, CT.country_id, CT.state_id, CT.location_id, CT.created_by, CT.website, CT.created_on, REG.region_name, COUN.country_name, STA.state_name, LOC.location_name, UR.first_name');
		$this->db->from($this->cfg['dbpref'].'clients as CT');
		$this->db->join($this->cfg['dbpref'].'region as REG', 'REG.regionid = CT.region_id', 'left');		
		$this->db->join($this->cfg['dbpref'].'country as COUN', 'COUN.countryid = CT.country_id', 'left');
		$this->db->join($this->cfg['dbpref'].'state as STA', 'STA.stateid = CT.state_id', 'left');
		$this->db->join($this->cfg['dbpref'].'location as LOC', 'LOC.locationid = CT.location_id', 'left');
		$this->db->join($this->cfg['dbpref'].'users as UR', 'UR.userid = CT.created_by', 'left');
        if ($this->userdata['level'] == 2) {
			$this->db->where_in('CT.region_id', $regions_ids);				
		} else if ($this->userdata['level'] == 3) {
			$this->db->where_in('CT.region_id', $regions_ids);
			$this->db->where_in('CT.country_id', $countries_ids);
		} else if ($this->userdata['level'] == 4) {
			$this->db->where_in('CT.region_id', $regions_ids);
			$this->db->where_in('CT.country_id', $countries_ids);
			$this->db->where_in('CT.state_id', $states_ids);
		} else if ($this->userdata['level'] == 5) {
			$this->db->where_in('CT.region_id', $regions_ids);
			$this->db->where_in('CT.country_id', $countries_ids);
			$this->db->where_in('CT.state_id', $states_ids);
			$this->db->where_in('CT.location_id', $locations_ids);
		}
		$res = $this->db->get();
        return $res->result_array();
    }
	
	/*
	*@Get Client Details
	*@Method get_client
	*@Parameter client_id
	*/
	function get_client($client_id)
	{
		$query = $this->db->get_where($this->cfg['dbpref'].'clients', array('client_id' => $client_id), 1);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
	}
	
	/*
	*@Insert Client Details
	*@Method  insert_client
	*/
	function insert_client($data) 
	{
        if ( $this->db->insert($this->cfg['dbpref'] . 'clients', $data) ) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return false;
        }
    }
	
	/*
	*check duplication
	*csl - Country, State & Location
	*/
	function check_csl($tbl_name, $tbl_field, $new_data)
	{
		$this->db->where($tbl_field, $new_data);
		$num_row = $this->db->get($this->cfg['dbpref'].$tbl_name)->num_rows();
		return $num_row;
	}
    
}

/* end of file */
