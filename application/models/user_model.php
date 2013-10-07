<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends Common_model {
    
    function User_model() {
       parent::__construct();
    }
    
    function user_list($offset, $search, $order_field = 'last_name', $order_type = 'asc') 
	{
        if ($search != false) 
		{	
            $search = urldecode($search);
			
			$sql = "SELECT a.*,b.level_id,b.level_name,c.id,c.name
                    FROM {$this->cfg['dbpref']}users as a
					LEFT JOIN ".$this->cfg['dbpref']."levels as b ON b.level_id = a.level LEFT JOIN ".$this->cfg['dbpref']."roles as c ON c.id = a.role_id
                    WHERE
                    (
                        CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%$search%'
                        OR `first_name` LIKE '%$search%'
                        OR `last_name` LIKE '%$search%'
                    ) ORDER BY a.first_name";
        }
		else
        {			
            $offset = mysql_real_escape_string($offset);
			$sql = "select a.*,b.level_id,b.level_name,c.id,c.name from ".$this->cfg['dbpref']."users as a LEFT JOIN ".$this->cfg['dbpref']."levels as b ON b.level_id = a.level LEFT JOIN ".$this->cfg['dbpref']."roles as c ON c.id = a.role_id ORDER BY a.first_name";
		}	
		
		$customers = $this->db->query($sql);
        return $customers->result_array();
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
		$query = $this->db->query("SELECT `user_id` FROM ".$this->cfg['dbpref']."levels_region WHERE `region_id` = $regid && level_id not in(5,4,3) ");
		$cntryquery = $this->db->query("SELECT `user_id` FROM ".$this->cfg['dbpref']."levels_country WHERE `country_id` = $cntryid && level_id not in(5,4,2) ");
		$stequery = $this->db->query("SELECT `user_id` FROM ".$this->cfg['dbpref']."levels_state WHERE `state_id` = $steid && level_id not in(5,3,2)");
		$locquery = $this->db->query("SELECT `user_id` FROM ".$this->cfg['dbpref']."levels_location WHERE `location_id` = $locid && level_id not in(4,3,2)");
		
		$globalusers = $this->db->query("SELECT userid as user_id FROM ".$this->cfg['dbpref']."users WHERE level in(1)");

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
			$query = $this->db->query("select * from ".$this->cfg['dbpref']."country where regionid='".$regionid."' AND countryid IN ($explode_country)");
			if($query->num_rows() > 0) {
				$flag = 1;
			} 	
		return $flag;
	}
	
	public function checkstatelevel4($countryid,$state_load) 
	{
		$flag = 0;		
			$query = $this->db->query("select * from ".$this->cfg['dbpref']."state where countryid='".$countryid."' AND stateid IN ($state_load)");
			if($query->num_rows() > 0) {
				$flag = 1;
			} 	
		return $flag;
	}
	
	public function checklocationlevel5($stateid,$location_load) 
	{	
		$flag = 0;		
			$query = $this->db->query("select * from ".$this->cfg['dbpref']."location where stateid='".$stateid."' AND locationid IN ($location_load)");
			if($query->num_rows() > 0) {
				$flag = 1;
			} 	
		return $flag;
	}
}

?>
