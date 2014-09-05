<?php
class Create_new_user extends crm_controller 
{
    
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('text');
    }
	
	public function index() {
	
		$crm_email = array();
		$user_failed  = array();
		$user_success = array();
		
		//get crm users emails
		$this->db->select('u.email');
		$this->db->from($this->cfg['dbpref'].'users as u');
		$query = $this->db->get();
		$email_res = $query->result_array();
		
		if(!empty($email_res)) {
			foreach($email_res as $email){
				$crm_email[] = $email['email'];
			}
		}
		
		$this->db->select('v.id,v.username,v.empid,v.email,v.active,v.first_name,v.last_name');
		$this->db->from($this->cfg['dbpref'].'view_econnect_mas as v');
		$this->db->where('v.active',1);
		$this->db->where('v.username !=','');
		$sql = $this->db->get();
		//echo $this->db->last_query();
		$econnect_users = $sql->result_array();
		foreach($econnect_users as $eusers){
			//1.check whether the username exists or not in CRM DB.
			$this->db->select('u.username,u.email');
			$this->db->from($this->cfg['dbpref'].'users as u');
			$this->db->where('u.username',$eusers['username']);
			$query = $this->db->get();
			$res = $query->row_array();
			
			if($query->num_rows() == 0) {
				//check email
				if(!in_array($eusers['email'],$crm_email)) {
					//insert into crm db
					$data = array(
					   'role_id' => '1',
					   'first_name' => $eusers['first_name'],
					   'last_name' => $eusers['last_name'],
					   'username' => $eusers['username'],
					   'password' => sha1('admin123'),
					   'email' => $eusers['email'],
					   'phone' => '',
					   'mobile' => '',
					   'level' => 1,
					   'auth_type' => 1,
					   'signature' => '',
					   'inactive' => 0
					);
					if($this->db->insert($this->cfg['dbpref'].'users', $data)) {
						$user_success[] = $eusers['empid'].' - '.$eusers['username']." - New user created.";
						$crm_email[] = $eusers['email'];
					}
				} else {
					//econnect user cannot be created. Email already exist.
					$user_failed[] = $eusers['empid'].' - '.$eusers['username']." - Email duplication, this user can not be created.";
				}
				
			} else {
				if($eusers['email'] != $res['email']) {
					//econnect user cannot be created. username already exists.
					$user_failed[] = $eusers['empid'].' - '.$eusers['username']." - User Name already exists, this user can not be created.";
				}
			}
		}
	
		echo "<pre>"; print_r($user_success);
		echo "<br>";
		print_r($user_failed);
		
	}

}
?>