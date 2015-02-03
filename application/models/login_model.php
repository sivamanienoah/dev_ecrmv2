<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends crm_model {
    
    public $cfg;
    
    public function __construct()
	{
        parent::__construct();
		$counter = $this->session->userdata('web_request_count');
		$this->session->set_userdata('web_request_count', $counter + 1);
        
    }
    
    public function check_login($level = FALSE)
	{	
        if ($this->session->userdata('logged_in') == TRUE)
		{
			$this->session->set_userdata('web_request_count', 0);
			
			$data  = $this->session->userdata('logged_in_user');
			$query = $this->process_login($data['email'], $data['password']);
            
			if ($query == FALSE)
			{
				$this->session->set_flashdata('login_errors', array('Security Violation - User Signed Out!'));
				redirect('userlogin/');
				exit();
			}
			/*else if (is_array($level))
			{
                if (!in_array($query[0]['level'], $level))
				{
                    $this->session->set_flashdata('login_errors', array('Your access level does not allow access to this area!'));
                    redirect('notallowed/');
                    exit();
                }
            }*/
		}
		else if ($this->session->userdata('logged_in') != TRUE)
		{
		
            $this->session->set_flashdata('header_messages', array('You are required to be logged in to access this area.'));
            $this->session->set_flashdata('last_url', ltrim($this->uri->uri_string(), '/'));
			redirect('userlogin/');
            exit();
		}
    }
    
	/*for local db only using email & password*/
	/*public function process_login($email, $password)
	{
        $sql = "SELECT * FROM `{$this->cfg['dbpref']}users` as u JOIN `{$this->cfg['dbpref']}roles` AS r ON r.id = u.role_id  WHERE u.`email` = ? AND u.`password` = ? AND u.`inactive` = 0 LIMIT 1";
        $user = $this->db->query($sql, array($email, sha1($password)));
		// echo $this->db->last_query(); exit;
        if ($user->num_rows() > 0)
		{
			$data = $user->result_array();
			$this->mark_attendance($data[0]['userid']);
            return $data;
        }
		else
		{
            return FALSE;
        }
    } */
	

	public function process_login($username, $password)
	{
		/*First time check the CRM DB*/
		$this->db->select('u.userid,u.first_name,u.last_name,u.username,u.password,u.email,u.auth_type,u.level,u.role_id,u.signature,
		u.inactive,r.name');
		$this->db->from($this->cfg['dbpref'].'users u');
		$this->db->join($this->cfg['dbpref'].'roles r', 'r.id = u.role_id');
		$this->db->where('u.username', $username);
		$this->db->limit(1);
        $sql = $this->db->get();
        $data['res'] = $sql->result_array();
		// echo count($data['res']); echo "<br>"; exit;
		$data['login_error_code'] = 0;
		
		if (count($data['res']) > 0) {
		/* if username is exist in CRM DB then it check the inactive & authentication type */
			if($data['res'][0]['inactive'] == 0) {
				switch($data['res'][0]['auth_type']) {
					case 0:	//for ldb authentication
						if(sha1($password) == $data['res'][0]['password']) {
							$this->mark_attendance($data['res'][0]['userid']);
							return $data;
						} else {
							$data['login_error_code'] = 1;
							$data['login_error'] = 'Invalid password supplied';
							return $data;
						}
					break;
					case 1: //for ladp authentication
						$LDAPServerAddress1 = "10.0.9.11"; // <- IP address for your 1st DC

						$LDAPServerPort    = "389";
						$LDAPServerTimeOut = "60";
						$LDAPContainer = "DC=enoah,DC=chn"; // <- your domain info
						
						// $BIND_username = $username;
						$usernameData="enoah\\".$username; // <- added domain name before username
						$BIND_username = $usernameData;
						$BIND_password = $password;
						//$filter = "sAMAccountName=".$this->input->post('email');
						$filter = "sAMAccountName=".$username;
					   
						if(($ds=ldap_connect($LDAPServerAddress1))) {
							ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
							ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
							
							// if($r = @ldap_bind($ds,$BIND_username,$BIND_password)) {
							if($r = @ldap_bind($ds,$BIND_username,$BIND_password)) {
								if($sr = ldap_search($ds, $LDAPContainer, $filter, array('distinguishedName'))) {
									if($info = ldap_get_entries($ds, $sr)) {
										$BIND_username = $info[0]['distinguishedname'][0];
										$BIND_password = $password;
										if($r2=ldap_bind($ds,$BIND_username,$BIND_password)) {
											if($sr2 = @ldap_search($ds,$LDAPContainer,$filter,array("givenName","sn","displayName","mail"))) {
												if($info2 = ldap_get_entries($ds, $sr2)) {
													$data["name"] = $info2[0]["givenname"][0];
												} else {
													$data['login_error'] = "Could not read entries"; $data['login_error_code'] = 2;
												}
											} else {
												$data['login_error'] = "Could not search"; $data['login_error_code'] = 3;
											}
										} else {
											$data['login_error'] = "User password incorrect"; $data['login_error_code'] = 4;
										}
									} else {
										$data['login_error'] = "User name not found"; $data['login_error_code'] = 5;
									}
								} else {
									$data['login_error'] = "Could not search"; $data['login_error_code'] = 6;
								}
							} else {
								$data['login_error'] = "Could not bind"; $data['login_error_code'] = 7;
								/* ldap_error($r);
								if (ldap_get_option($r, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error)) {
								echo "Error Binding to LDAP: $extended_error";
								} else {
									echo "Error Binding to LDAP: No2 additional information is available.";
								} */
							}
						} else {
							$data['login_error'] = "Could not connect"; $data['login_error_code'] = 8;
						}
						return $data;
					break;
				}
			} else {
				$data['login_error'] = 'Your Account has been made Inactive'; $data['login_error_code'] = 9;
				return $data;
			}
		} else {
			/* Here check the username in eConnect db, if username is exist in eConnect DB then insert the user into crm DB */
			$data['login_error'] = 'Username doesnot exist'; $data['login_error_code'] = 10;
			return $data;
		}
	}

    public function check_login_status($level = FALSE)
	{
        if ($this->session->userdata('logged_in') === TRUE)
		{
			$data = $this->session->userdata('logged_in_user');
            $query = $this->process_login($data['email'], $data['password']);
			
			if ($query == FALSE)
			{
				return FALSE;
			}
			/*else if (is_array($level))
			{
                if (!in_array($query[0]['level'], $level))
				{
                   return FALSE;
                }
				else
				{
					return TRUE;
				}
            }*/
			else
			{
                return FALSE;	
            }
		}
		else
		{
            return FALSE;
		}
    }
	
	private function mark_attendance($userid)
	{
		$sql = "INSERT INTO `{$this->cfg['dbpref']}user_attendance`
					(`userid_fk`, `login_date`, `login_time`, `ip_addr`)
				VALUES
					(?, ?, ?, ?)";
		
		$q = $this->db->get_where("`{$this->cfg['dbpref']}user_attendance`", array('userid_fk' => $userid, 'login_date' => date('Y-m-d')));
		if ($q->num_rows() == 0)
		{
			$this->db->query($sql, array($userid, date('Y-m-d'), date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR']));
		}
	}
    
}
?>
