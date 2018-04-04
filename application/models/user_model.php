<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Model
 *
 * @class 		User_model
 * @extends		crm_model (application/core/CRM_Model.php)
 * @author 		eNoah
 * @Model
 */
class User_model extends crm_model {
    
	/*
	*@Constructor
	*@User Model
	*/
    public function User_model() {
       parent::__construct();
    }
    
	
	/*
	*@Get User list
	*@Method  user_list
	*/
    public function user_list($offset, $search) 
	{
        if ($search != false) {
			$search = urldecode($search);
			$where = "(CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%$search%' OR `first_name` LIKE '%$search%' OR `last_name` LIKE '%$search%') ORDER BY a.first_name";

			$this->db->select('a.*, b.level_id, b.level_name, c.id, c.name, d.department_name, s.name as skill_name');
			$this->db->from($this->cfg['dbpref']."users as a");
			$this->db->join($this->cfg['dbpref'].'levels as b', 'b.level_id = a.level', 'left');
			$this->db->join($this->cfg['dbpref'].'roles as c', 'c.id = a.role_id', 'left');
			$this->db->join($this->cfg['dbpref'].'department as d', 'd.department_id = a.department_id', 'left');
			$this->db->join($this->cfg['dbpref'].'skills_set as s', 's.id = a.skill_id', 'left');
			$this->db->where($where); 
        } else {			
            $offset = $this->db->escape_str($offset);
			$this->db->select('a.*, b.level_id, b.level_name, c.id, c.name, d.department_name, s.name as skill_name');
			$this->db->from($this->cfg['dbpref']."users as a");
			$this->db->join($this->cfg['dbpref'].'levels as b', 'b.level_id = a.level', 'left');
			$this->db->join($this->cfg['dbpref'].'roles as c', 'c.id = a.role_id', 'left');
			$this->db->join($this->cfg['dbpref'].'department as d', 'd.department_id = a.department_id', 'left');
			$this->db->join($this->cfg['dbpref'].'skills_set as s', 's.id = a.skill_id', 'left');
		}
		$this->db->order_by("a.first_name", "asc");
		$this->db->order_by("a.inactive", "desc");
		$query = $this->db->get();
		$customers = $query->result_array();	
        return $customers;
    }
	
	/*
	*@Get User list
	*@Method  user_list
	*/
    public function getUserLists($type) 
	{
		$this->db->select('u.userid,u.role_id,u.first_name,u.last_name,u.username,u.emp_id');
		$this->db->from($this->cfg['dbpref']."users as u");
		if($type == 'active'){
			$this->db->where('inactive', 0);
		}
		$this->db->order_by("u.first_name", "asc");	

		$query = $this->db->get();		
		$customers = $query->result_array();	
        return $customers;
    }

	/*
	*@Get User Count
	*@Method  user_count
	*/
    public function user_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'] . 'users');
    }
    
	/*
	*@Get User By id
	*@Method  get_user
	*/
    public function get_user($id) {
        if(!$id) {
            return false;
        } else {
			$sql = $this->db->get_where($this->cfg['dbpref'] . 'users', array('userid' => $id), 1);
			return $sql->result_array();
		}
    }

	/*
	*@Get User By id
	*@Method  get_user_det
	*/
    public function get_user_det($id) 
	{
        $this->db->select('u.userid,u.first_name,u.last_name,u.username,u.password,u.email,u.auth_type,u.level,u.role_id,u.signature,u.inactive,r.name');
		$this->db->from($this->cfg['dbpref'].'users u');
		$this->db->join($this->cfg['dbpref'].'roles r', 'r.id = u.role_id');
		$this->db->where('u.userid', $id);
		$this->db->limit(1);
		$sql = $this->db->get();
		return $sql->row_array();
    }
    
	/*
	*@Update User Details By corresponding User ID
	*@Method  update_user
	*/
    public function update_user($id, $data) {
        $this->db->where('userid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'users', $data);
    }

	/*
	*@Insert User Details
	*@Method  insert_user
	*/
    public function insert_user($data) {
		$availed_users = check_max_users();
		if ( $this->cfg['max_allowed_users'][0] > $availed_users['avail_users'] ) {
			/* 
			$sql = "INSERT INTO ".$this->cfg['dbpref']."users (role_id,first_name,last_name,password,email,add_email,use_both_emails,phone,mobile, 	level,signature,inactive)	VALUES ('".$data['role_id']."','".$data['first_name']."','".$data['last_name']."','".$data['password']."','".$data['email']."','".$data['add_email']."','".$data['use_both_emails']."','".$data['phone']."','".$data['mobile']."','".$data['level']."','".$data['signature']."','".$data['inactive']."') ON DUPLICATE KEY UPDATE email='".$data['email']."' ";	
			$query = $this->db->query($sql);  
			if ( $query == 1 )
			{	
				echo "asddf asdf"; exit;
				return $this->db->insert_id();
			}
			*/
		
			$res = $this->chk_unique_emails($data['email']);
			
			if ($res == 1)
			{
				$res_user = "emailexist";
				return $res_user;
			}
			if ( $this->db->insert($this->cfg['dbpref']. 'users', $data) ) {
				return $this->db->insert_id();
			}
		} else {
			$max_user = "maxusers";
			return $max_user;
		}
    }
	
	/*
	*check unique emails
	*/
	function chk_unique_emails($email) {
        $chk = $this->db->get_where($this->cfg['dbpref'] . 'users', array('email'=>$email));
		return $chk->num_rows();
	}

	/*
	*@For new level settings concepts
	*@Method  insert_level_settings
	*/
	public function insert_level_settings($level_data, $user_id, $levelId) {
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


	/*
	*@Get UserList By region id,country id & state id
	*@Method  get_userslist
	*/
	public function get_userslist($regid, $cntryid, $steid, $locid) {
		$two_not = array(5,4,3);
		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_region");
		$this->db->where('region_id',$regid); 
		$this->db->where_not_in('level_id', $two_not);
		$query = $this->db->get();
		
		$three_not = array(5,4,2);
		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_country");
		$this->db->where('country_id',$cntryid); 
		$this->db->where_not_in('level_id',$three_not);
		$cntryquery = $this->db->get();

		$four_not = array(5,3,2);
		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_state");
		$this->db->where('state_id',$steid); 
		$this->db->where_not_in('level_id',$four_not);
		$stequery = $this->db->get();

		$five_not = array(4,3,2);
		$this->db->select('user_id');
		$this->db->from($this->cfg['dbpref']."levels_location");
		$this->db->where('location_id',$locid); 
		$this->db->where_not_in('level_id',$five_not);
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

	/*
	*@Delete User by corresponding user id
	*@Method  delete_user
	*/
    public function delete_user($id) {
        $this->db->where('userid', $id);
        return $this->db->delete($this->cfg['dbpref'] . 'users');
    }
	
	/*
	*@For ACL
	*@Method  has_role
	*/
	public function has_role( $user, $role ) {
        $this->db->join( $this->cfg['dbpref'].'user_roles ur', 'users.id = ur.users_id' );
        $this->db->join( $this->cfg['dbpref'].'roles r', 'r.id = ur.roles_id' );
        return $this->get_by( array( 'r.name' => $role, 'ur.users_id' => $user ) );
    }

	/*
	*@Check exists fields
	*@Method  has_role
	*@table   user_table
	*/
    public function field_exists( $field )
    {
        return $this->db->field_exists( $field, $this->config->item('user_table', 'acl_auth') );
    }

	/*
	*@Get levels for Add users
	*@Method  has_role
	*@table   levels
	*/
	public function get_levels() {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'] . 'levels');
		$this->db->limit('5','0');
        $list_levels = $this->db->get();
        $lists=  $list_levels->result_array();
		return $lists;
    }
	
	/*
	*@Check reset token
	*@Method  has_role
	*/
    public function check_token( $token ) {
        return ( $token === $this->reset_code );
    }

	/*
	*@add remarks 
	*@Method  addremarks
	*@table   taskremarks
	*/
	public function addremarks($remarks,$userid,$taskid) {
		$this->db->query("INSERT INTO `".$this->cfg['dbpref']."taskremarks`(`remarks`,`taskid`,`userid`,`createdon`) VALUES('".$remarks."','".$taskid."','".$userid."',now())");
	}


	/*
	*@Get 'created_by' BY taskid
	*@Method  updatedby
	*@table   tasks
	*/
	public function updatedby($taskid) {
		$this->db->select('created_by');
		$this->db->from($this->cfg['dbpref'].'tasks');
		$this->db->where('taskid',$taskid);
        $idd = $this->db->get();
        $id=  $idd->result_array();
		return $id;
	}
	
	/*
	*@Update level 
	*@Method  update_level
	*@table   tasks
	*/
    public function update_level($data,$id,$levelid) {		
		$this->delete_level_dependant($id);
		$this->level_dependant_insert($id,$data,$levelid);		
		return $id;
  	}


	/*
	*@Delete Level by corresponding user id 
	*@Method   delete_level_dependant
	*@table    levels_region,levels_country,levels_state,levels_location
	*/
	public function delete_level_dependant($id = null) {
		$this->db->where('user_id',$id);
		$this->db->delete($this->cfg['dbpref'] . 'levels_region') ;
		$this->db->where('user_id', $id);
		$this->db->delete($this->cfg['dbpref']. 'levels_country') ;
		$this->db->where('user_id', $id);
		$this->db->delete($this->cfg['dbpref'] . 'levels_state') ;
		$this->db->where('user_id', $id);
		return $this->db->delete($this->cfg['dbpref'] . 'levels_location'); 		
	}

	/*
	*@Insert Level by corresponding user id 
	*@Method   level_dependant_insert
	*@table    levels_region,levels_country,levels_state,levels_location
	*/
    public function level_dependant_insert($userId = null,$data,$levelid) {		
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
	
	/*
	*@count country record by region id & country id
	*@Method   checkcountrylevel3
	*@table    Country
	*/
	public function checkcountrylevel3($regionid, $explode_country) {
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
	
	/*
	*@count state record by country id & state id
	*@Method   checkstatelevel4
	*@table    state
	*/
	public function checkstatelevel4($countryid,$state_load) {
		$flag = 0;		
		
		$this->db->select("*");
		$this->db->from($this->cfg['dbpref']."state");
		$this->db->where("countryid", $countryid); 
		$this->db->where_in("stateid", $state_load);
		// $this->db->where('stateid IN('.$state_load.')'); 
		$query = $this->db->get();	

		if($query->num_rows() > 0) {
			$flag = 1;
		} 	
		return $flag;
	}
	
	/*
	*@count location record by state id & location id
	*@Method   checklocationlevel5
	*@table    location
	*/
	public function checklocationlevel5($stateid,$location_load) {	
		$flag = 0;		
		
		$this->db->select("*");
		$this->db->from($this->cfg['dbpref']."location");
		$this->db->where("stateid", $stateid); 
		$this->db->where_in("locationid", $location_load);
		// $this->db->where('locationid IN('.$location_load.')');
		$query = $this->db->get();	
		
		if($query->num_rows() > 0) {
			$flag = 1;
		} 	
		return $flag;
	}
	
	/*
	*@Find exist Email Address
	*@Method   find_exist_email
	*@table    users
	*/
	public function find_exist_email($data) {

		$email  =  $data['email'];
		$update =  $data['email1'];
		if ($update != 'undefined') {
			// $emailid = $this->db->query("select email from ".$this->cfg['dbpref']."users where email = '".$email."' and userid != '".$update."' ");
			$this->db->where('email', $email);
			$this->db->where('userid !=', $update);
			$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
			if ($query == 0) {
				echo 'userOk';
			} else {
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
	*@Get User for Lead Assigned To
	*@Method   getUserLeadAssigned
	*@table    users
	*/
	public function getUserLeadAssigned($users) {	
		$this->db->select("userid, first_name, last_name");
		$this->db->from($this->cfg['dbpref']."users");
		$this->db->where("userid in (".$users.")");
		$this->db->where("inactive", 0);
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
	*@Method   check_user_status
	*@table    leads
	*@return as Json response
	*/
	public function check_user_status($data=array()) {
		$id = $data['data'];
		// $where = "(belong_to=".$id." or lead_assign=".$id." or assigned_to =".$id.") AND pjt_status = 0"; 
		$where = "(belong_to=".$id." or lead_assign=".$id." or assigned_to =".$id.")"; 
		$this->db->where($where);
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
	*@Select Log History 
	*@Method   log_history
	*@table    logs,leads,
	*/
	public function log_history($log_date,$log_user) {
	
		# now get the logs for the user on that day
		$sql = "SELECT *, DATE_FORMAT(`".$this->cfg['dbpref']."logs`.`date_created`, '%W, %D %M %y %h:%i%p') AS `fancy_date`
				FROM ".$this->cfg['dbpref']."logs
				LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."leads`.`lead_id` = `".$this->cfg['dbpref']."logs`.`jobid_fk`
				WHERE DATE(`".$this->cfg['dbpref']."logs`.`date_created`) = ?
				AND `userid_fk` = ?
				ORDER BY `".$this->cfg['dbpref']."logs`.`date_created`";
			
		$q  = $this->db->query($sql, array($log_date, $log_user['userid']));
		$rs = $q->result_array();
		return $rs;
	
	}

	
	/*
	*@Select all Regions for add users
	*@Method   get_regions
	*@table    region
	*/
	public function get_regions() {
	
	    $output       	= '';
		$this->db->select('regionid, region_name');
		$this->db->where('inactive', 0);
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
	*@Select region By user id for edit users
	*@Method   get_loadregionsByuserId
	*@table    levels_region,region
	*/
	public function get_loadregionsByuserId($uid) {
   
		$uid = (int)$uid; // User ID
		
		$output       	= '';
		
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
		$this->db->where("inactive", 0);
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
	*@Select Country By Region id for edit users
	*@Method    get_loadCountrysByRegionid
	*@tables    region,country
   */
   public function get_loadCountrysByRegionid($region_id) {
		
		$output = '';
		$this->db->select('countryid,country_name');
		$this->db->from($this->cfg['dbpref'].'region r');
		$this->db->join($this->cfg['dbpref'].'country c', 'r.regionid = c.regionid');
		$this->db->where('c.regionid IN('.$region_id.')'); 
		$this->db->where('c.inactive', 0); 
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
	*@Select Country By Region id for edit users
	*@Method   edit_loadCountrys
	*@table    levels_country,country
    */
	public function edit_loadCountrys($regionid, $uid) {
		
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
		if(sizeof($country_result)>0){
			foreach ($country_result as $countries)
			{	
				if(in_array($countries->countryid,$user_con)){
					$output .= '<option value="'.$countries->countryid.'" selected = "selected" >'.$countries->country_name.'</option>';
				}else{
					$output .= '<option value="'.$countries->countryid.'">'.$countries->country_name.'</option>';
				}
			}
		}
		echo $output;	
	}
	

	/*
	*@Select state By country_id
	*@Method   get_load_state
	*@table    state,country
    */
	public function get_load_state($country_id) {
		$output = '';
		
		$this->db->select('stateid,state_name');
		$this->db->from($this->cfg['dbpref'].'state r');
		$this->db->join($this->cfg['dbpref'].'country c', 'r.countryid = c.countryid');
		// $this->db->where_in("c.countryid", $country_id);
		$this->db->where('c.countryid IN('.$country_id.')'); 
		$this->db->where("r.inactive", 0);
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
	*@Get State By country_id & user id
	*@Method   get_load_state
	*@table    state,country
    */
	public function edit_loadstate($countryid,$uid) {    
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
	*@Get Locations By state id
	*@Method   get_loadLocations
	*@table    location,state
    */
	public function get_loadLocations($state_id) {
		$output = '';

		$this->db->select('locationid,location_name');
		$this->db->from($this->cfg['dbpref']."location r");
		$this->db->join($this->cfg['dbpref'].'state c', 'r.stateid = c.stateid');
		// $this->db->where_in("c.stateid", $state_id);
		$this->db->where('c.stateid IN('.$state_id.')'); 
		$this->db->where("r.inactive", 0);	
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
	*@Get Locations By state id & user id
	*@Method   editloadLocations
	*@tables    levels_location,location
    */
	public function editloadLocations($state_id,$uid) {
	
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
	*@Get User 
	*@Method    getUserfromdb
	*@tables    users
    */
	public function getUserfromdb() {
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
	
	/*
	*@add log for my profile
	*@Method    add_log
	*@tables    logs
    */
	public function add_log($data) {
	   if ($this->db->insert($this->cfg['dbpref'].'logs', $data))
		{
			echo "{error: false}";
		}
		else
		{
			echo "{error: true}";
		}
	}
	
	/*
	*@Get User 
	*@Method check_username
	*@tables users
    */
	public function check_username($username,$updt) {
		$this->db->where('username', $username);
		if($updt != 'noupdate'){
		$this->db->where('userid !=', $updt);
		}
		$query = $this->db->get($this->cfg['dbpref'].'users')->num_rows();
		if( $query == 0 )
		echo 'userOk';
		else 
		echo 'userNo';
	}
	
	/*
	*@Get Logs for User
	*@Method get_logs
	*/
	function get_logs($id)
	{
		$this->db->select('ul.username, ul.emp_id, ul.active, ul.created_on, r.name, d.department_name, s.name as skill_name');
		$this->db->from($this->cfg['dbpref'].'users_logs as ul');
		$this->db->join($this->cfg['dbpref'].'roles as r', 'r.id = ul.role_id', 'left');
		$this->db->join($this->cfg['dbpref'].'department as d', 'd.department_id = ul.department_id', 'left');
		$this->db->join($this->cfg['dbpref'].'skills_set as s', 's.id = ul.skill_id', 'left');
		$this->db->where('ul.user_id', $id);
		$this->db->order_by('created_on', 'asc');
		$query = $this->db->get();
 		return $query->result_array();
	}
	
	function getSkill()
	{
		$this->db->select('id, name');
		$this->db->from($this->cfg['dbpref'].'skills_set');
		$this->db->order_by('id');
		$query = $this->db->get();
 		return $query->result_array();
	}
	
	function getDept()
	{
		$this->db->select('department_id, department_name');
		$this->db->from($this->cfg['dbpref'].'department');
		$this->db->order_by('department_id');
		$query = $this->db->get();
 		return $query->result_array();
	}
  
}

?>
