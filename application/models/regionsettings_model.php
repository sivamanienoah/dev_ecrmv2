<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Regionsettings_model extends crm_model {
    
    function Regionsettings_model() {
        parent::__construct();
    }
    
	/*
	*@Get region List
	*@Region Settings Model
	*/
	
    public function region_list($offset, $search) { 
        $userdata = $this->session->userdata('logged_in_user');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('region_name', $search);
	    }
	
		if ($userdata['level'] == 2 || $userdata['level'] == 3 || $userdata['level'] == 4 || $userdata['level'] == 5) {
			
			$this->db->select('region_id');
			$this->db->from($this->cfg['dbpref'].'levels_region');
			$this->db->where('level_id',$userdata['level']);
			$this->db->where('user_id',$userdata['userid']);
			$query = $this->db->get();
			$reg_details = $query->result_array();
			if(sizeof($reg_details)>0){
				foreach($reg_details as $reg)
				{
					$regions[] = $reg['region_id'];
				}
			}
			$regions_ids = array_unique($regions);
			$regions_ids = (array_values($regions)); //reset the keys in the array
		}
	
		$this->db->select('creuser.first_name as cfnam,creuser.last_name as clnam,moduser.first_name as mfnam,moduser.last_name as mlnam,reg.*');
		$this->db->from($this->cfg['dbpref'].'region as reg');
		$this->db->join($this->cfg['dbpref'].'users as creuser','creuser.userid='.'reg.created_by ','left');
		$this->db->join($this->cfg['dbpref'].'users as moduser','moduser.userid='. 'reg.modified_by ','left');
		if ($userdata['level'] != 1) {
			$this->db->where_in('reg.regionid',$regions_ids);
		}
		$this->db->order_by('reg.inactive', 'asc');
		$customers = $this->db->get();
		$samle=  $customers->result_array();
		return $samle;
    
	}

	/*
	*@List out Country Record
	*@Region Settings Model
	*/
	
	public function country_list($offset, $search) {
        
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('country_name', $search);
        }
	
		$this->db->select('regg.region_name,creuser.first_name as cfnam,creuser.last_name as clnam,moduser.first_name as mfnam,moduser.last_name as mlnam,coun.*');
		$this->db->from($this->cfg['dbpref'].'country as coun');
		$this->db->join($this->cfg['dbpref'].'users as creuser','creuser.userid='.'coun.created_by ','left');
		$this->db->join($this->cfg['dbpref'].'users as moduser','moduser.userid='. 'coun.modified_by ','left');
		$this->db->join($this->cfg['dbpref'].'region as regg','regg.regionid='. 'coun.regionid ');
		$this->db->order_by('coun.inactive', 'asc');

		$customers = $this->db->get();
		$samle=  $customers->result_array();
		return $samle;
    }
 

 	/*
	*@List Out State Record
	*@Region Settings Model
	*/

	public function state_list($offset, $search) {
       
        if ($search != false) {
            $search = urldecode($search);			
            $this->db->like('state_name', $search);
			
        }
	
		$this->db->select('re.region_name,cn.country_name,cn.regionid,creuser.first_name as cfnam,creuser.last_name as clnam,moduser.first_name as mfnam,moduser.last_name as mlnam,stat.*');
		$this->db->from($this->cfg['dbpref'].'state as stat');
		$this->db->join($this->cfg['dbpref'].'users as creuser','creuser.userid='.'stat.created_by ','left');
		$this->db->join($this->cfg['dbpref'].'users as moduser','moduser.userid='. 'stat.modified_by ','left');
		$this->db->join($this->cfg['dbpref'].'country as cn','cn.countryid='. 'stat.countryid ');
		$this->db->join($this->cfg['dbpref'].'region as re','re.regionid='. 'cn.regionid');
		$this->db->order_by('stat.inactive', 'asc');
		$customers = $this->db->get();   
		$samle=  $customers->result_array();
        return $samle;        
    }

 	/*
	*@List Out Location Record
	*@Region Settings Model
	*/
	
	public function location_list($offset, $search) {
       
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('location_name', $search);
        }

		$this->db->select('st.countryid,cn.country_name,st.state_name,creuser.first_name as cfnam,creuser.last_name as clnam,moduser.first_name as mfnam,moduser.last_name as mlnam,locat.*');
		$this->db->from($this->cfg['dbpref'].'location as locat');
		$this->db->join($this->cfg['dbpref'].'users as creuser','creuser.userid='.'locat.created_by ','left');
		$this->db->join($this->cfg['dbpref'].'users as moduser','moduser.userid='. 'locat.modified_by ','left');
		$this->db->join($this->cfg['dbpref'].'state as st','st.stateid='. 'locat.stateid');
		$this->db->join($this->cfg['dbpref'].'country as cn','cn.countryid='. 'st.countryid ');
		$this->db->order_by('locat.inactive', 'asc');
		$customers = $this->db->get();   
	    $samle=  $customers->result_array();
        return $samle;
    }

 	/*
	*@List Out Level Record
	*@Region Settings Model
	*/
	
	public function level_list($offset, $search) {
       
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('level_name', $search);
        }
		$this->db->select('creuser.first_name as cfnam,creuser.last_name as clnam,moduser.first_name as mfnam,moduser.last_name as mlnam,lev.*');
		$this->db->from($this->cfg['dbpref'].'levels as lev');
		$this->db->join($this->cfg['dbpref'].'users as creuser','creuser.userid='.'lev.created_by ');
		$this->db->join($this->cfg['dbpref'].'users as moduser','moduser.userid='. 'lev.modified_by ');
		$this->db->order_by('lev.inactive', 'asc');
		$customers = $this->db->get();
        $samle=  $customers->result_array();
        return $samle;
        
    }
	
	/*
	*@Level Map
	*@Region Settings Model
	*/
	
	public function level_map($id,$usid) {

		if ($search != false) {
            $search = urldecode($search);
            $this->db->like('level_name', $search);
        }
		
		switch($id){

		  case 1:
				$this->db->select('lrg.region_id,vre.region_name,vce.country_name,vce.countryid,vst.state_name,vst.stateid,vloc.locationid,vloc.location_name');
				$this->db->from($this->cfg['dbpref'] . 'users as lev'); // Changed the Level table name to Users table
				$this->db->join($this->cfg['dbpref']. 'levels_region as lrg','lrg.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref']. 'region as vre','vre.regionid='.'lrg.region_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_country AS con','con.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref']. 'country AS vce','vce.countryid='.'con.country_id','left');
				$this->db->join($this->cfg['dbpref']. 'levels_state AS st','st.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref']. 'state AS vst','vst.stateid='.'st.state_id','left');
				$this->db->join($this->cfg['dbpref']. 'levels_location AS loc','loc.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref']. 'location AS vloc','vloc.locationid='.'loc.location_id','left');
				$this->db->where('lev.level', $id);
			 break;	
				
			case 2:
				$this->db->select('lrg.region_id,vre.region_name,vce.country_name,vce.countryid,vst.state_name,vst.stateid,vloc.locationid,vloc.location_name');
				$this->db->from($this->cfg['dbpref'].'users as lev'); // Changed the Level table name to Users table
				$this->db->join($this->cfg['dbpref'].'levels_region as lrg','lrg.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'].'region as vre','vre.regionid='.'lrg.region_id','left');
				$this->db->join($this->cfg['dbpref'].'levels_country AS con','con.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'].'country AS vce','vce.countryid='.'con.country_id','left');
				$this->db->join($this->cfg['dbpref'].'levels_state AS st','st.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'].'state AS vst','vst.stateid='.'st.state_id','left');
				$this->db->join($this->cfg['dbpref'].'levels_location AS loc','loc.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'].'location AS vloc','vloc.locationid='.'loc.location_id','left');
				$this->db->where('lev.level', $id);
				$this->db->where('lrg.user_id', $usid);
			 break;
			
			 case 3:
				$this->db->select('lrg.region_id,vre.region_name,vce.country_name,vce.countryid,vst.state_name,vst.stateid,vloc.locationid,vloc.location_name');
				$this->db->from($this->cfg['dbpref'] . 'users as lev'); // Changed the Level table name to Users table
				$this->db->join($this->cfg['dbpref'] . 'levels_region as lrg','lrg.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'region as vre','vre.regionid='.'lrg.region_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_country AS con','con.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'country AS vce','vce.countryid='.'con.country_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_state AS st','st.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'state AS vst','vst.stateid='.'st.state_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_location AS loc','loc.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'location AS vloc','vloc.locationid='.'loc.location_id','left');
				//$this->db->order_by('lev.inactive', 'asc');
				$this->db->where('lev.level', $id);
				$this->db->where('lrg.user_id', $usid);
				$this->db->where('con.user_id', $usid);
			break;	
			
			case 4:
				$this->db->select('lrg.region_id,vre.region_name,vce.country_name,vce.countryid,vst.state_name,vst.stateid,vloc.locationid,vloc.location_name');
				$this->db->from($this->cfg['dbpref'] . 'users as lev'); // Changed the Level table name to Users table
				$this->db->join($this->cfg['dbpref'] . 'levels_region as lrg','lrg.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'region as vre','vre.regionid='.'lrg.region_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_country AS con','con.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'country AS vce','vce.countryid='.'con.country_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_state AS st','st.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'state AS vst','vst.stateid='.'st.state_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_location AS loc','loc.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'location AS vloc','vloc.locationid='.'loc.location_id','left');
				//$this->db->order_by('lev.inactive', 'asc');
				$this->db->where('lev.level', $id);
				$this->db->where('lrg.user_id', $usid);
				$this->db->where('con.user_id', $usid);
				$this->db->where('st.user_id', $usid);
			break;
			
			default:	
				$this->db->select('lrg.region_id,vre.region_name,vce.country_name,vce.countryid,vst.state_name,vst.stateid,vloc.locationid,vloc.location_name');
				$this->db->from($this->cfg['dbpref'] . 'users as lev'); // Changed the Level table name to Users table
				$this->db->join($this->cfg['dbpref'] . 'levels_region as lrg','lrg.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'region as vre','vre.regionid='.'lrg.region_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_country AS con','con.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'country AS vce','vce.countryid='.'con.country_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_state AS st','st.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'state AS vst','vst.stateid='.'st.state_id','left');
				$this->db->join($this->cfg['dbpref'] . 'levels_location AS loc','loc.level_id='.'lev.level','left');
				$this->db->join($this->cfg['dbpref'] . 'location AS vloc','vloc.locationid='.'loc.location_id','left');
				//$this->db->order_by('lev.inactive', 'asc');
				$this->db->where('lev.level', $id);
				$this->db->where('lrg.user_id', $usid);
				$this->db->where('con.user_id', $usid);
				$this->db->where('st.user_id', $usid);
				$this->db->where('loc.user_id', $usid);
		   }

			$customers = $this->db->get();
			$samle=  $customers->result_array();
	   
			return $samle;
    }

	/*
	*@Region Update Record
	*@Region Settings Model
	*/
	
    public function update_region($id, $data) {
        $this->db->where('regionid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'region', $data);
    }

	/*
	*@Insert Region Record
	*@Region Settings Model
	*/
    
    public function insert_region($data) {
        
        if ( $this->db->insert($this->cfg['dbpref'] . 'region', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
        
    }

	/*
	*@Update Country Record
	*@Region Settings Model
	*/
	
	public function update_country($id, $data) {
        
        $this->db->where('countryid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'country', $data);

    }

	/*
	*@Insert Country Record
	*@Region Settings Model
	*/
    
    public function insert_country($data) {
        
        if ( $this->db->insert($this->cfg['dbpref'] . 'country', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
		if ( $this->db->insert($this->cfg['dbpref'] . 'region', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
        
    }
		

	/*
	*@Update State Record
	*@Region Settings Model
	*/
		
	public function update_state($id, $data) {
        $this->db->where('stateid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'state', $data);
    }

	/*
	*@Insert State Record
	*@Region Settings Model
	*/
	
	public function insert_state($data) {
        if ( $this->db->insert($this->cfg['dbpref'] . 'state', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

	/*
	*@Update Location Record
	*@Region Settings Model
	*/
	
	public function update_location($id, $data) {
        
        $this->db->where('locationid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'location', $data);
        
    }

	/*
	*@Insert Location Record
	*@Region Settings Model
	*/
    
    public function insert_location($data) {
        if ( $this->db->insert($this->cfg['dbpref'] . 'location', $data) ) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

	/*
	*@Delete Region Record
	*@Region Settings Model
	*/
    
    public function delete_region($id) {
        $this->db->where('regionid', $id);
        return $this->db->delete($this->cfg['dbpref'] . 'region');
    }

	/*
	*@Delete Country Record
	*@Region Settings Model
	*/
	
	public function delete_country($id) {
        $this->db->where('countryid', $id);
        return $this->db->delete($this->cfg['dbpref'] . 'country');
    }

	/*
	*@Delete State Record
	*@Region Settings Model
	*/
	
	public function delete_state($id) {
        $this->db->where('stateid', $id);
        return $this->db->delete($this->cfg['dbpref'] . 'state');
    }

	/*
	*@Delete Location Record
	*@Region Settings Model
	*/
	
	public function delete_location($id) {
        $this->db->where('locationid', $id);
        return $this->db->delete($this->cfg['dbpref']. 'location');
    }

	/*
	*@Count of region Record
	*@Region Settings Model
	*/
	
	public function region_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'region');
    }

	/*
	*@Count of Country Record
	*@Region Settings Model
	*/
	
	public function country_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'country');
    }
	
	/*
	*@Count of State Record
	*@Region Settings Model
	*/

	public function state_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'state');
    }

	/*
	*@Count of Location Record
	*@Region Settings Model
	*/
	
	public function location_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'location');
    }

	/*
	*@Get Region Record
	*@Region Settings Model
	*/
	
	public function get_region($id) {
		if( ! $id )
		{
			return false;
		}else{
			$customer = $this->db->get_where($this->cfg['dbpref'] . 'region', array('regionid' => $id), 1);
			return $customer->result_array();
		}
	}

	/*
	*@Get Country Record
	*@Region Settings Model
	*/

	public function get_country($id) {
		if( ! $id )
		{
			return false;
		}else{
			$customer = $this->db->get_where($this->cfg['dbpref'] . 'country', array('countryid' => $id), 1);
			return $customer->result_array();
		}
	}
	
	/*
	*@Get State Record
	*@Region Settings Model
	*/
	
	public function get_state($id) {
		if( ! $id )
		{
			return false;
		}else{		
			$sql = "SELECT * FROM ".$this->cfg['dbpref']."state
					LEFT JOIN (".$this->cfg['dbpref']."country, ".$this->cfg['dbpref']."region) ON ( ".$this->cfg['dbpref']."country.countryid = ".$this->cfg['dbpref']."state.countryid
					AND ".$this->cfg['dbpref']."country.regionid = ".$this->cfg['dbpref']."region.regionid )
					WHERE ".$this->cfg['dbpref']."state.stateid = $id
					LIMIT 0 , 1";
					
			$customer = $this->db->query($sql);
			return $customer->result_array();
		}
	}

	/*
	*@Get Country Record List
	*@Region Settings Model
	*/
	
	public function getcountry_list($val) {  
		$userdata = $this->session->userdata('logged_in_user');	
		
		//restriction for country
		$this->db->select('country_id');
		$this->db->from($this->cfg['dbpref'].'levels_country');
		$this->db->where('level_id',$userdata['level']);
		$this->db->where('user_id',$userdata['userid']);
		$coun_query   = $this->db->get();
		$coun_details = $coun_query->result_array();
		if(sizeof($coun_details)>0){
			foreach($coun_details as $coun)
			{
				$countries[] = $coun['country_id'];
			}
		}
		$countries_ids = array_unique($countries);
		$countries_ids = (array_values($countries)); //reset the keys in the array
		
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('country_name', 'asc');
		
		$this->db->where('regionid', $val);
		if ($userdata['level'] == 3 || $userdata['level'] == 4 || $userdata['level'] == 5) {
			$this->db->where_in('countryid', $countries_ids);
		}
		$customers = $this->db->get($this->cfg['dbpref'] . 'country');
		return $customers->result_array();	
    }

	/*
	*@Get State Record List
	*@Region Settings Model
	*/
	
	public function getstate_list($val) {       

		$userdata = $this->session->userdata('logged_in_user');		
	
		//restriction for state
		$this->db->select('state_id');
		$this->db->from($this->cfg['dbpref'].'levels_state');
		$this->db->where('level_id',$userdata['level']);
		$this->db->where('user_id',$userdata['userid']);
		$ste_query   = $this->db->get();
		$ste_details = $ste_query->result_array();
		
		if(sizeof($ste_details)>0){
			foreach($ste_details as $ste)
			{
				$states[] = $ste['state_id'];
			}
		}
		$states_ids = array_unique($states);
		$states_ids = (array_values($states)); //reset the keys in the array
		
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('state_name', 'asc');
		
		$this->db->where('countryid', $val);
		if ($userdata['level'] == 4 || $userdata['level'] == 5) {
			$this->db->where_in('stateid', $states_ids);
		}
		$customers = $this->db->get($this->cfg['dbpref'] . 'state');

		return $customers->result_array();	
    }
	
	/*
	*@Get Location List
	*@Region Settings Model
	*/

	public function getlocation_list($val) {
		$userdata = $this->session->userdata('logged_in_user');
		
		//restriction for location		
		$this->db->select('location_id');
		$this->db->from($this->cfg['dbpref'].'levels_location');
		$this->db->where('level_id',$userdata['level']);
		$this->db->where('user_id',$userdata['userid']);
		$loc_query   = $this->db->get();
		$loc_details   = $loc_query->result_array();
		if(sizeof($loc_details)>0){
			foreach($loc_details as $loc)
			{
				$locations[] = $loc['location_id'];
			}
		}
		$locations_ids = array_unique($locations);
		$locations_ids = (array_values($locations)); //reset the keys in the array
		
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('location_name', 'asc');
		
		$this->db->where('stateid', $val);
		if ($userdata['level'] == 5) {
			$this->db->where_in('locationid', $locations_ids);
		}
		$customers = $this->db->get($this->cfg['dbpref'] . 'location');
		
		return $customers->result_array();	
    }
	
	/*
	*@Get Location By ID
	*@Region Settings Model
	*/
	
	public function get_location($id) {
		if( ! $id )
		{
			return false;
		}else{
			$sql = "SELECT * FROM ".$this->cfg['dbpref']."location
					LEFT JOIN (".$this->cfg['dbpref']."state, ".$this->cfg['dbpref']."country, ".$this->cfg['dbpref']."region) ON 
					( ".$this->cfg['dbpref']."state.stateid = ".$this->cfg['dbpref']."location.stateid AND 
					  ".$this->cfg['dbpref']."country.countryid = ".$this->cfg['dbpref']."state.countryid AND
					  ".$this->cfg['dbpref']."country.regionid = ".$this->cfg['dbpref']."region.regionid)
					  WHERE ".$this->cfg['dbpref']."location.locationid = $id
					  LIMIT 0 , 1";
			$customer = $this->db->query($sql);
			return $customer->result_array();		  
		}
	}
	
	/*
	*@Get Location By ID
	*@Region Settings Model
	*/
	
	public function get_level($id) {
		if( ! $id )
		{
			return false;
		}else{
			$customer = $this->db->get_where($this->cfg['dbpref'].'levels', array('level_id' => $id), 1);
			return $customer->result_array();
		}
	}
	
	/*
	*@Get Country By Regionid
	*@Region Settings Model
	*/
	
	public function getcountry_multiplelist($val) { 
	    if(!empty($val))
		$str1     =  explode(',',$val);
		if(!empty($str1))
		$arr_pass = implode("','",$str1);
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'country');
		$this->db->where_in('regionid',$arr_pass);
		$customers   = $this->db->get();
		return $customers->result_array();	
    }
	
	/*
	*@Get State By countryid
	*@Region Settings Model
	*/

	public function getstate_multiplelist($val) { 
	    if(!empty($val))
		$str2       = explode(',',$val);
		if(!empty($str2))
		$state_pass = implode("','",$str2);
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'state  ');
		$this->db->where_in('countryid',$state_pass);
		$customers   = $this->db->get();
		return $customers->result_array();	
    }
	
	/*
	*@Get Location List By Stateid
	*@Region Settings Model
	*/

	public function getlocation_multiplelist($val) { 
		if(!empty($val))
		$str3 = explode(',',$val);
		if(!empty($str3))
		$location_pass = implode("','",$str3);

		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'location  ');
		$this->db->where_in('stateid',$location_pass);
		$customers   = $this->db->get();
		return $customers->result_array();	
    }

	/*
	*@Insert Record for Level
	*@Region Settings Model
	*/

    public function insert_level($data) {

		$dataLevel = array();
		$dataLevel['level_name'] =$data['level_name'] ;
		$dataLevel['created_by'] =$data['created_by'] ;
		$dataLevel['modified_by'] =$data['modified_by'] ;
		$dataLevel['created'] =$data['created'] ;
		$dataLevel['modified'] =$data['modified'] ;
		$dataLevel['inactive'] =$data['inactive'] ; 
			if ( $this->db->insert($this->cfg['dbpref'].'levels', $dataLevel) ) {
				$levelId =$this->db->insert_id();
				$this->level_dependant_insert($levelId,$data);			
				return $levelId;		
			} else {
				return false;
			}
	}	

	/*
	*@Insert Record (Level Region ,Levels Country, Levels State ,Levels location)
	*@Region Settings Model
	*/
	
	public function level_dependant_insert($levelId =null,$data){
	    
		if(!empty($data['region'])) {
				 for($i=0;$i<count($data['region']);$i++){
					$dataRegion = array();
					$dataRegion['region_id'] =$data['region'][$i];
					$dataRegion['level_id'] =$levelId;
					$this->db->insert($this->cfg['dbpref'] . 'levels_region', $dataRegion) ;
				 }
			 }
			 
			 if(!empty($data['country_state'])) {
			 	 for($i=0;$i<count($data['country_state']);$i++){
				$dataRegion = array();
				$dataRegion['country_id'] =$data['country_state'][$i];
				$dataRegion['level_id'] =$levelId;
				$this->db->insert($this->cfg['dbpref'] . 'levels_country', $dataRegion) ;
			 }
			 }
			 if(!empty($data['state_location'])) {
			 	 for($i=0;$i<count($data['state_location']);$i++){
				$dataRegion = array();
				$dataRegion['state_id'] =$data['state_location'][$i];
				$dataRegion['level_id'] =$levelId;
				$this->db->insert($this->cfg['dbpref'] . 'levels_state', $dataRegion) ;
			 }	
			 }
			if(!empty($data['location'])) {			 
			 for($i=0;$i<count($data['location']);$i++){
				$dataRegion = array();
				$dataRegion['location_id'] =$data['location'][$i];
				$dataRegion['level_id'] =$levelId;
				$this->db->insert($this->cfg['dbpref'] . 'levels_location', $dataRegion); 
			 }
		}
	}
	
	/*
	*@Delete Record for Level
	*@Region Settings Model
	*/

   public function delete_level($id) {
		$this->db->where('level_id', $id);
		$this->db->delete($this->cfg['dbpref'] . 'levels') ;
		return $this->delete_level_dependant($id);
	
              
	}

	/*
	*@Delete Level (levels region ,levels country , Levels state, Levels location)
	*@Region Settings Model
	*/
	
    public function delete_level_dependant($id = null){

	    $this->db->where('level_id', $id);
		$this->db->delete($this->cfg['dbpref'].'levels_region') ;
		
		$this->db->where('level_id', $id);
		$this->db->delete($this->cfg['dbpref'].'levels_country') ;
		
		$this->db->where('level_id', $id);
		$this->db->delete($this->cfg['dbpref'].'levels_state') ;
		
		$this->db->where('level_id', $id);
		return $this->db->delete($this->cfg['dbpref'].'levels_location'); 

    }

	/*
	*@Update Record for Level
	*@Region Settings Model
	*/
	
	public function update_level($data,$id) {
	
		$dataLevel = array();
		$dataLevel['level_name'] =$data['level_name'] ; 
		$dataLevel['modified_by'] =$data['modified_by'] ; 
		$dataLevel['modified'] =$data['modified'] ;
		$dataLevel['inactive'] =$data['inactive'] ; 

		$this->db->where('level_id', $id);
		$this->db->update($this->cfg['dbpref'].'levels', $dataLevel);
		$this->delete_level_dependant($id);
		$this->level_dependant_insert($id,$data);		
		return $id;
  	
	}	

}
?>