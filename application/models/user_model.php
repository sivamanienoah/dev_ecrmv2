<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends crm_model {
    
    function User_model() {
       parent::__construct();
    }
    
    function user_list($offset, $search, $order_field = 'last_name', $order_type = 'asc') 
	{
        if ($search != false) 
		{	
			$search = urldecode($search);
			$where = "(CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%$search%' OR `first_name` LIKE '%$search%' OR `last_name` LIKE '%$search%') ORDER BY a.first_name";

			$this->db->select('a.*,b.level_id,b.level_name,c.id,c.name');
			$this->db->from($this->cfg['dbpref']."users as a");
			$this->db->join($this->cfg['dbpref'].'levels as b', 'b.level_id = a.level', 'left');
			$this->db->join($this->cfg['dbpref'].'roles as c', 'c.id = a.role_id', 'left');
			$this->db->where($where); 
        }
		else
        {			
            $offset = mysql_real_escape_string($offset);
			$this->db->select('a.*,b.level_id,b.level_name,c.id,c.name');
			$this->db->from($this->cfg['dbpref']."users as a");
			$this->db->join($this->cfg['dbpref'].'levels as b', 'b.level_id = a.level', 'left');
			$this->db->join($this->cfg['dbpref'].'roles as c', 'c.id = a.role_id', 'left');
			$this->db->order_by("a.first_name", "asc"); 
			
		}	

		$query = $this->db->get();		
		$customers = $query->result_array();	
        return $customers;
    }
    
    function user_count() 
	{
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'users');
    }
    
    function get_user($id) 
	{
        if(!$id)
        {
            return false;
        }
		else
		{
			$customer = $this->db->get_where($this->cfg['dbpref'] . 'users', array('userid' => $id), 1);
			return $customer->result_array();
		}
    }
    
    function update_user($id, $data) 
	{
        $this->db->where('userid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'users', $data);
    }
    
    function insert_user($data)
	{
		$availed_users = check_max_users();
		if ( $this->cfg['max_allowed_users'][0] > $availed_users['avail_users'] )
		{
			if ( $this->db->insert($this->cfg['dbpref']. 'users', $data) ) {
				return $this->db->insert_id();
			} 
			else 
			{
				return false;
			}
		}
		else 
		{
			$max_user = "max_users";
			return $max_user;
		}
    }
	
	//for new level settings concepts
	function insert_level_settings($level_data, $user_id, $levelId)
	{
		$data['region'] = $level_data['region'];		
		if(!empty($data['region'])) {
			 for($i=0;$i<count($data['region']);$i++){
				$dataRegion = array();
				$dataRegion['region_id'] = $data['region'][$i];
				$dataRegion['level_id'] = $levelId;
				$dataRegion['user_id'] = $user_id;
				$this->db->insert($this->cfg['dbpref'] . 'levels_region', $dataRegion) ;
			 }
		 }
		 $data['country_state'] = $level_data['country'];
		 if(!empty($data['country_state'])) {		
			 for($i=0;$i<count($data['country_state']);$i++){
			$dataRegion = array();
			$dataRegion['country_id'] = $data['country_state'][$i];
			$dataRegion['level_id'] = $levelId;
			$dataRegion['user_id'] = $user_id;
			$this->db->insert($this->cfg['dbpref']. 'levels_country', $dataRegion) ;
		 }
		 }
		 $data['state_location'] = $level_data['state'];
		 if(!empty($data['state_location'])) {
			 for($i=0;$i<count($data['state_location']);$i++){
			$dataRegion = array();
			$dataRegion['state_id'] = $data['state_location'][$i];
			$dataRegion['level_id'] = $levelId;
			$dataRegion['user_id'] = $user_id;
			$this->db->insert($this->cfg['dbpref'] . 'levels_state', $dataRegion) ;
		 }	
		 }
		 $data['location'] = $level_data['location'];
		 if(!empty($data['location'])) {			 
		 for($i=0;$i<count($data['location']);$i++){
			$dataRegion = array();
			$dataRegion['location_id'] =$data['location'][$i];
			$dataRegion['level_id'] = $levelId;
			$dataRegion['user_id'] = $user_id;
			$this->db->insert($this->cfg['dbpref'] . 'levels_location', $dataRegion); 
		 }
		 }
	}
	
	function get_userslist($regid, $cntryid, $steid, $locid)
    {
		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_region");
		$this->db->where('region_id',$regid); 
		$this->db->where_not_in('level_id','5,4,3');
		$query = $this->db->get();		

		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_country");
		$this->db->where('country_id',$cntryid); 
		$this->db->where_not_in('level_id','5,4,2');
		$cntryquery = $this->db->get();		

		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_state");
		$this->db->where('state_id',$steid); 
		$this->db->where_not_in('level_id','5,3,2');
		$stequery = $this->db->get();		

		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_location");
		$this->db->where('location_id',$locid); 
		$this->db->where_not_in('level_id','4,3,2');
		$locquery = $this->db->get();		

		$this->db->select('userid as user_id');
		$this->db->from($this->cfg['dbpref']."users");
		$this->db->where_in("level", 1); 
		$globalusers = $this->db->get();		

		$regUserList = $query->result_array();
		$cntryUserList = $cntryquery->result_array();
		$steUserList = $stequery->result_array();
		$locUserList = $locquery->result_array();
		$globalUserList = $globalusers->result_array();

		$userList = array_merge_recursive($regUserList, $cntryUserList, $steUserList, $locUserList, $globalUserList);
		$users[] = 0;
		foreach($userList as $us)
		{
			$users[] = $us['user_id'];
		}	
		
		$userList = array_unique($users);
		$userList = (array_values($userList));
		return $userList;
    }
	
    function delete_user($id) 
	{
        $this->db->where('userid', $id);
        return $this->db->delete($this->cfg['dbpref'] . 'users');
    }
	
	//function for ACL\
	
	public function has_role( $user, $role )
    {
        $this->db->join( $this->cfg['dbpref'].'user_roles ur', 'users.id = ur.users_id' );
        $this->db->join( $this->cfg['dbpref'].'roles r', 'r.id = ur.roles_id' );
        return $this->get_by( array( 'r.name' => $role, 'ur.users_id' => $user ) );
    }

    public function field_exists( $field )
    {
        return $this->db->field_exists( $field, $this->config->item('user_table', 'acl_auth') );
    }

	public function get_levels()
    {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'levels');
		$this->db->limit('5','0');
        $list_levels = $this->db->get();
        $lists=  $list_levels->result_array();
		return $lists;
    }

    public function check_token( $token )
    {
        return ( $token === $this->reset_code );
    }
	
	public function addremarks($remarks,$userid,$taskid)
	{
		$this->db->query("INSERT INTO `".$this->cfg['dbpref']."taskremarks`(`remarks`,`taskid`,`userid`,`createdon`) VALUES('".$remarks."','".$taskid."','".$userid."',now())");
	}
	
	public function updatedby($taskid) 
	{
		$this->db->select('created_by');
		$this->db->from($this->cfg['dbpref'].'tasks');
		$this->db->where('taskid',$taskid);
        $idd = $this->db->get();
        $id=  $idd->result_array();
		return $id;
	}
	
    function update_level($data,$id,$levelid) 
	{		
		$this->delete_level_dependant($id);
		$this->level_dependant_insert($id,$data,$levelid);		
		return $id;
  	}
	
	function delete_level_dependant($id = null)
	{
		$this->db->where('user_id',$id);
		$this->db->delete($this->cfg['dbpref'] . 'levels_region') ;
		$this->db->where('user_id', $id);
		$this->db->delete($this->cfg['dbpref']. 'levels_country') ;
		$this->db->where('user_id', $id);
		$this->db->delete($this->cfg['dbpref'] . 'levels_state') ;
		$this->db->where('user_id', $id);
		return $this->db->delete($this->cfg['dbpref'] . 'levels_location'); 		
	}
	
    function level_dependant_insert($userId = null,$data,$levelid)
	{		
		if(!empty($data['region'])) {	
				for($i=0;$i<count($data['region']);$i++){				
					$dataRegion = array();
					$dataRegion['region_id'] = $data['region'][$i];
					$dataRegion['user_id'] = $userId;
					$dataRegion['level_id'] = $levelid; 
					if($levelid != 1) {
						$this->db->insert($this->cfg['dbpref'] . 'levels_region', $dataRegion);
					}
				}
		}
			 
		if(!empty($data['country'])) {
			for($i=0;$i<count($data['country']);$i++)
			{
				$dataRegion = array();
				$dataRegion['country_id'] =$data['country'][$i];
				$dataRegion['user_id'] =$userId;
				$dataRegion['level_id'] = $levelid;
				if($levelid != 1) {
					$this->db->insert($this->cfg['dbpref'] . 'levels_country', $dataRegion);
				}
			}
		}
		
		if(!empty($data['state'])) {
			for($i=0;$i<count($data['state']);$i++)
			{
				$dataRegion = array();
				$dataRegion['state_id'] =$data['state'][$i];
				$dataRegion['user_id'] =$userId;
				$dataRegion['level_id'] = $levelid;
				if($levelid != 1) {
					$this->db->insert($this->cfg['dbpref'] . 'levels_state', $dataRegion);
				}
			}	
		}
		
		if(!empty($data['location'])) {			 
			for($i=0;$i<count($data['location']);$i++)
			{
				$dataRegion = array();
				$dataRegion['location_id'] =$data['location'][$i];
				$dataRegion['user_id'] =$userId;
				$dataRegion['level_id'] = $levelid;
				if($levelid != 1) {
					$this->db->insert($this->cfg['dbpref']. 'levels_location', $dataRegion); 
				}
			}
		}
	
	}
	
	public function checkcountrylevel3($regionid,$explode_country) 
	{
		$flag = 0;		
		//$query = $this->db->query("select * from ".$this->cfg['dbpref']."country where regionid='".$regionid."' AND countryid IN ($explode_country)");
		
		$this->db->select("*");
		$this->db->from($this->cfg['dbpref']."country");
		$this->db->where("regionid", $regionid); 
		$this->db->where_in("countryid", $explode_country); 
		$query = $this->db->get();		
		if($query->num_rows() > 0) {
			$flag = 1;
		} 	
		return $flag;
	}
	
	public function checkstatelevel4($countryid,$state_load) 
	{
			$flag = 0;		
			
			$this->db->select("*");
			$this->db->from($this->cfg['dbpref']."state");
			$this->db->where("countryid", $countryid); 
			$this->db->where_in("stateid", $state_load);
			$query = $this->db->get();	
			
			if($query->num_rows() > 0) {
				$flag = 1;
			} 	
		return $flag;
	}
	
	public function checklocationlevel5($stateid,$location_load) 
	{	
			$flag = 0;		
			
			$this->db->select("*");
			$this->db->from($this->cfg['dbpref']."location");
			$this->db->where("stateid", $stateid); 
			$this->db->where_in("locationid", $location_load);
			$query = $this->db->get();	
			
			if($query->num_rows() > 0) {
				$flag = 1;
			} 	
			return $flag;
	}
	
	/*
	* @ Find exist Email Address
	*
	*/

	public function find_exist_email($data){	

		$email  =  $data['email'];
		$update =  $data['email1'];
	
		if ($update != 'undefined') {
			
			$emailid = $this->db->query("select email from ".$this->cfg['dbpref']."users where email = '".$email."' and userid != '".$update."' ");
			if ($emailid == 1){ 
				echo 'userOk';
			}else{ 
				echo 'userNo';
			}

		} else {
			$this->db->where('email',$email);
			$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
			if ($query == 0) 
				echo 'userOk';
			else 
				echo 'userNo';
		}	
	}
	
	/*
	* @Get User for Lead Assigned To
	*
	*/

   public function getUserLeadAssigned($users)
   {
   
   		//$query = $this->db->query("select userid, first_name, last_name from ".$this->cfg['dbpref']."users where userid in ($users) ORDER BY first_name");
		
		$this->db->select("userid, first_name, last_name");
		$this->db->from($this->cfg['dbpref']."users");
		//$this->db->where('userid in ('.$users.')'); 
		$this->db->where_in("userid", $users); 
		$this->db->order_by("first_name"); 		
		$query = $this->db->get();
		$user_res = $query->result_array();
		$res = '';
		$res .= "<option value='not_select'>Please Select</option>";
		foreach($user_res as $user) {
			$res .= "<option value=".$user['userid'].">".$user['first_name']." ".$user['last_name']."</option>";
		}
		echo $res;
   }
  
	/*
	*@Check User Status
	*
	*/
  
	public function check_user_status($data=array()){
		$id = $data['data'];
		$where = "(belong_to=".$id." or lead_assign=".$id." or assigned_to =".$id.")"; 
		$this->db->where($where);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		$res = array();
		if($query == 0) {
			$res['html'] .= "YES";
		} else {
			$res['html'] .= "NO";
		}
		echo json_encode($res);
		exit;
    }
	
	/*
	*@Log History 
	*
	*/
	
	public function log_history($log_date,$log_user){
	
		# now get the logs for the user on that day
		$sql = "SELECT *, DATE_FORMAT(`".$this->cfg['dbpref']."logs`.`date_created`, '%W, %D %M %y %h:%i%p') AS `fancy_date`
				FROM ".$this->cfg['dbpref']."logs
				LEFT JOIN `".$this->cfg['dbpref']."jobs` ON `".$this->cfg['dbpref']."jobs`.`jobid` = `".$this->cfg['dbpref']."logs`.`jobid_fk`
				WHERE DATE(`".$this->cfg['dbpref']."logs`.`date_created`) = ?
				AND `userid_fk` = ?
				ORDER BY `".$this->cfg['dbpref']."logs`.`date_created`";
			
		$q  = $this->db->query($sql, array($log_date, $log_user['userid']));
		$rs = $q->result_array();
		return $rs;
	
	}

	/*
	*@Get Regions 
	*
	*/
	
	public function get_regions(){
	
	    $output       	= '';
		$this->db->select('regionid,region_name');
		$this->db->from($this->cfg['dbpref']."region");
		$query = $this->db->get();
		$region_results = $query->result();
		
		if(sizeof($region_results)>0){
				foreach ($region_results as $regions)
				{
					if($id == $regions->regionid)
						$output .= '<option value="'.$regions->regionid.'" selected = "selected" >'.$regions->region_name.'</option>';
					else
						$output .= '<option value="'.$regions->regionid.'">'.$regions->region_name.'</option>';
				}
		}
		echo $output;
	}
	
   /*
	*@Edit Load Regions 
	*
	*/
  
   public function get_loadregionsByuserId($uid){
   
		$uid = (int)$uid; // User ID

		$this->db->select('region_id');
		$this->db->from($this->cfg['dbpref']."levels_region");
		$this->db->where('user_id', $uid); 
		$query  = $this->db->get();		
		$r_ids  = $query->result();

		$user_reg = array();
		foreach($r_ids as $reg_id) {
			$user_reg[] = $reg_id->region_id;
		}

		$this->db->select('regionid,region_name');
		$this->db->from($this->cfg['dbpref']."region");
		$query         = $this->db->get();		
		$region_query  = $query->result();
		
		foreach ($region_query as $regions)
		{		
			if(in_array($regions->regionid,$user_reg)){
				$output .= '<option value="'.$regions->regionid.'" selected = "selected" >'.$regions->region_name.'</option>';
			}else{
				$output .= '<option value="'.$regions->regionid.'">'.$regions->region_name.'</option>';
			}
		}

		echo $output;
   }
  
   /*
	*@Load Country By Regionid
	*
	*/
  
   public function get_loadCountrysByRegionid($region_id){
		
		$output = '';
		$this->db->select('countryid,country_name');
		$this->db->from($this->cfg['dbpref'].'region r');
		$this->db->join($this->cfg['dbpref'].'country c', 'r.regionid = c.regionid');
		$this->db->where('c.regionid IN('.$region_id.')'); 
		$query          = $this->db->get();
		$country_result = $query->result();
		foreach ($country_result as $countrys)
		{
		    if($cid == $countrys->countryid)
				$output .= '<option value="'.$countrys->countryid.'" selected = "selected">'.$countrys->country_name.'</option>';
			else 
				$output .= '<option value="'.$countrys->countryid.'">'.$countrys->country_name.'</option>';
			
		}
		echo $output;
   
   }
   
    /*
	*@Edit Load Country 
	*
	*/

	public function edit_loadCountrys($regionid,$uid){
		
		$output = '';
		$this->db->select('country_id');
		$this->db->from($this->cfg['dbpref'].'levels_country');
		$this->db->where('user_id',$uid); 
		$query = $this->db->get();
		$c_ids = $query->result();
		$user_con = array();
		foreach($c_ids as $con_id) {
			$user_con[] = $con_id->country_id;
		}
		
		$this->db->select('countryid,country_name');
		$this->db->from($this->cfg['dbpref'].'country');
		$this->db->where('regionid IN('.$regionid.')'); 
		$country_query    = $this->db->get();
		$country_result   = $country_query->result();
		foreach ($country_result as $countries)
		{	
			if(in_array($countries->countryid,$user_con)){
				$output .= '<option value="'.$countries->countryid.'" selected = "selected" >'.$countries->country_name.'</option>';
			}else{
				$output .= '<option value="'.$countries->countryid.'">'.$countries->country_name.'</option>';
			}
		}
		echo $output;	
	}
	
	/*
	*@Get Load State 
	*
	*/
	
	public function get_load_state($country_id){
	
		$output = '';
		
		$this->db->select('stateid,state_name');
		$this->db->from($this->cfg['dbpref'].'state r');
		$this->db->join($this->cfg['dbpref'].'country c', 'r.countryid = c.countryid');
		$this->db->where_in("c.countryid", $country_id);		
		$country_query    = $this->db->get();
		$country_result   = $country_query->result();
		foreach ($country_result as $states)
		{
			if($sid == $states->stateid)
				$output .= '<option value="'.$states->stateid.'" selected = "selected">'.$states->state_name.'</option>';
			else
			    $output .= '<option value="'.$states->stateid.'">'.$states->state_name.'</option>';
		}
		echo $output;
	
	} 
	
	/*
	*@Get Load State 
	*
	*/
	
	public function edit_loadstate($countryid,$uid)
	{    
		$output = '';
		$this->db->select('state_id');
		$this->db->from($this->cfg['dbpref']."levels_state");
		$this->db->where('user_id', $uid); 
		$query     = $this->db->get();		
		$s_ids     = $query->result();
		$user_stat = array();
		foreach($s_ids as $sta_id) {
			$user_stat[] = $sta_id->state_id;
		}			
		$this->db->select('stateid,state_name');
		$this->db->from($this->cfg['dbpref']."state");
		$this->db->where_in("countryid", $countryid);		
		$query       = $this->db->get();		
		$state_query = $query->result();
		foreach ($state_query as $states)
		{	
			if(in_array($states->stateid,$user_stat)){
				$output .= '<option value="'.$states->stateid.'" selected = "selected" >'.$states->state_name.'</option>';
			}else{
				$output .= '<option value="'.$states->stateid.'">'.$states->state_name.'</option>';
				}
		}
		echo $output;
	}

	/*
	*@Get location 
	*
	*/
	
   public function get_loadLocations($state_id){
		$output = '';

		$this->db->select('locationid,location_name');
		$this->db->from($this->cfg['dbpref']."location r");
		$this->db->join($this->cfg['dbpref'].'state c', 'r.stateid = c.stateid');
		$this->db->where_in("c.stateid", $state_id);				
		$query     			= $this->db->get();		
		$location_result    = $query->result();
		foreach ($location_result as $location)
		{
		    if($loc_id == $location->locationid)	
				$output .= '<option value="'.$location->locationid.'" selected = "selected">'.$location->location_name.'</option>';
			else 
				$output .= '<option value="'.$location->locationid.'">'.$location->location_name.'</option>';
		}
		echo $output;      
   }
   
   	/*
	*@Get location 
	*
	*/
	
	public function editloadLocations($state_id,$uid){
	
		$output = '';
		
		$this->db->select('location_id');
		$this->db->from($this->cfg['dbpref']."levels_location");
		$this->db->where('user_id',$uid);
		$query       = $this->db->get();
		$l_ids = $query->result();	
		$user_loc = array();
		if(sizeof($l_ids)>0){
			foreach($l_ids as $l_id) {
				$user_loc[] = $l_id->location_id;
			}
		}		
		
		$this->db->select('locationid,location_name');
		$this->db->from($this->cfg['dbpref']."location");
		$this->db->where_in("stateid", $state_id);		
		$query     		  = $this->db->get();
		$location_result  = $query->result();
		foreach ($location_result as $locations)
		{	
			if(in_array($locations->locationid,$user_loc)){
				$output .= '<option value="'.$locations->locationid.'" selected = "selected" >'.$locations->location_name.'</option>';
			}else{
				$output .= '<option value="'.$locations->locationid.'">'.$locations->location_name.'</option>';
			}
		}
		echo $output;
	}
	
	/*
	*@Get User from Database 
	*
	*/
	
	public function getUserfromdb(){
	
			if ($update != 'undefined') {
			
				$where = "email = '".$username."' AND `userid` != '".$update."' ";
				$this->db->where($where);
				$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
				//echo $this->db->last_query();
				if ($query == 0) {
					echo 'userOk';
				}
				else {
					echo 'userNo';
				}
			}
			else {	
				$this->db->where('email',$username);
				$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
				if( $query == 0 ) echo 'userOk';
				else echo 'userNo';
			}	
	
	}
  
}

?>
