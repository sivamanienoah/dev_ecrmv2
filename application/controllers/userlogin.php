<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Userlogin extends crm_controller {

     public function __construct()
	{
       parent::__construct();	 
        $this->load->model('role_model');  		 
        $this->load->model('regionsettings_model'); 
    }
	
    function Userlogin() 
	{
        parent::__construct();
    }
    
    function index()
	{
		$SSO_Status = $this->config->item('IS_SSO');  
		if(isset($_COOKIE['sso_token']) && $this->session->userdata('logoutType')!='ldb' && $SSO_Status=='1')
		{ 
			$checkCookie=$this->login_model->checkCookie($_COOKIE['sso_token']);
			if($checkCookie['error'])
			{		
				$json['error'] = $checkCookie['error'];
				$json['errormsg']=$checkCookie['errormsg'];
				$this->load->view('login_view');
			}
			elseif($checkCookie['success'])
			{			
				$this->session->set_userdata('loggedType','ldap');
				$username=$checkCookie['username'];
				$this->automatic_login($username);
			}
			else
			{
				$this->load->view('login_view');
			}
		}

		else 
		{
			$this->load->view('login_view');
			$this->session->set_userdata('logoutType', '');
		}
    }
    
    /* function process_login()
	{
		// if ( $userdata = $this->login_model->process_login($this->input->post('email'),  sha1($this->input->post('password'))) ) {
		if ( $userdata  = $this->login_model->process_login($this->input->post('email'),  $this->input->post('password')) ) {
	
			$menu_items = $this->role_model->UserModuleList($userdata[0]['userid']);
			// echo $this->db->last_query();exit;
			$whole = '';
			$val = '';
			for($i=0;$i<count($menu_items);$i++){				 
				  $val = implode(",",$menu_items[$i]);
				  if(!empty($whole)){
					$whole=$whole.'#'.$val;
				}else{
				$whole=$val;
				}
			}
		 	 
            $array = array(
						'logged_in' => TRUE,
						'logged_in_user' => $userdata[0]
                        );
			// echo "<pre>"; print_r($whole); exit;
			$usid = $array['logged_in_user']['userid'];
			$userlevel = $array['logged_in_user']['level'];
			// echo $userlevel; die();
			$array['menu_item_list'] = $whole;			
			$data['customers'] = $this->regionsettings_model->level_map($array['logged_in_user']['level'] , $usid);
			foreach($data['customers'] as $cus){			
				$data['region_id'][]   = $cus['region_id'];			
				$data['countryid'][]  = $cus['countryid'];			
				$data['stateid'][]    = $cus['stateid'];			
				$data['locationid'][] = $cus['locationid'];		
			}
			if ($userlevel == 2) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
			} else if ($userlevel == 3) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
				$array['countryid']  = implode(',',array_unique($data['countryid'])); 
			} else if ($userlevel == 4) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
				$array['countryid']  = implode(',',array_unique($data['countryid']));
				$array['stateid']    = implode(',',array_unique($data['stateid'])); 
			} else if ($userlevel == 5) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
				$array['countryid']  = implode(',',array_unique($data['countryid']));
				$array['stateid']    = implode(',',array_unique($data['stateid']));
				$array['locationid'] = implode(',',array_unique($data['locationid'])); 
			}
					
            $this->session->set_userdata($array);
			
            if ($this->input->post('last_url')) {
                redirect($this->input->post('last_url'));
            } else {
                redirect('dashboard/');
            }
            exit();
        } else {
            $this->session->set_flashdata('login_errors', array('Invalid Username, Password or your account is inactive. Access Denied!'));
            redirect('userlogin/');
            exit();
        }
    } */
	
	function process_login()
	{
		$SSO_Status = $this->config->item('IS_SSO');
		$userdata = $this->login_model->process_login($this->input->post('email'), $this->input->post('password'));

		if($userdata['login_error_code'] > 0) {

			// $this->session->set_flashdata('login_errors', array($userdata['login_error']));
			$this->session->set_flashdata('login_errors', array('Invalid Username, Password or your account is inactive. Access Denied!'));
            redirect('userlogin/');
            exit();
		} else {
			
			$userdata   = $userdata['res'];
			
			//do log for reseller user login
			if($userdata[0]['role_id'] == 14) {
				
				$log_detail = "Logged In: \n";
				$log_detail .= date('d-m-Y H:i:s')."\n";
				
				$log['jobid_fk']      = 0;
				$log['userid_fk']     = $userdata[0]['userid'];
				$log['date_created']  = date('Y-m-d H:i:s');
				$log['log_content']   = $log_detail;
				$log_res = $this->login_model->insert_row("logs", $log);
			}

			$menu_items = $this->role_model->UserModuleList($userdata[0]['userid']);
			$whole = '';
			$val   = '';
			for($i=0;$i<count($menu_items);$i++) {				 
				$val = implode(",",$menu_items[$i]);
				if(!empty($whole)){
					$whole = $whole.'#'.$val;
				} else {
					$whole = $val;
				}
			}
			if($userdata[0]['auth_type']=='0') $loggedType="ldb"; else $loggedType="ldap";
            $array = array(
						'logged_in'=>TRUE,
						'logged_in_user'=>$userdata[0],
						'loggedType'=>$loggedType,
						'SSO_Status'=>$SSO_Status
                        );
			// echo "<pre>"; print_r($whole); exit;
			$usid 	   = $array['logged_in_user']['userid'];
			$userlevel = $array['logged_in_user']['level'];
			// echo $userlevel; die();
			$array['menu_item_list'] = $whole;			
			$data['customers']	     = $this->regionsettings_model->level_map($array['logged_in_user']['level'] , $usid);
			foreach($data['customers'] as $cus){			
				$data['region_id'][]  = $cus['region_id'];			
				$data['countryid'][]  = $cus['countryid'];			
				$data['stateid'][]    = $cus['stateid'];			
				$data['locationid'][] = $cus['locationid'];		
			}
			if ($userlevel == 2) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
			} else if ($userlevel == 3) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
				$array['countryid']  = implode(',',array_unique($data['countryid'])); 
			} else if ($userlevel == 4) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
				$array['countryid']  = implode(',',array_unique($data['countryid']));
				$array['stateid']    = implode(',',array_unique($data['stateid'])); 
			} else if ($userlevel == 5) {
				$array['region_id']  = implode(',',array_unique($data['region_id']));
				$array['countryid']  = implode(',',array_unique($data['countryid']));
				$array['stateid']    = implode(',',array_unique($data['stateid']));
				$array['locationid'] = implode(',',array_unique($data['locationid'])); 
			}
					
		   $this->session->set_userdata($array);
			
            if ($this->input->post('last_url')) {
                redirect($this->input->post('last_url'));
            } else {
			
                redirect('dashboard/');
            }
            exit();
		}
    }
	
	function process_remote_login($user = '', $pass = '')
	{
		if ( $userdata = $this->login_model->process_login($user,$pass) )
		{
			echo json_encode(array('logged_in' => TRUE, 'logged_data' => $userdata[0]));
		}
		else
		{
			echo json_encode(array('error' => TRUE));
		}
	}
	
	function automatic_login($username)
	{
		$userdata = $this->login_model->automatic_process_login($username);
		$SSO_Status = $this->config->item('IS_SSO'); 
		$userdata   = $userdata['res'];
		if(!empty($userdata)){	
			$menu_items = $this->role_model->UserModuleList($userdata[0]['userid']); 
			$whole = '';
			$val   = '';
			if(!empty($menu_items)){
				for($i=0;$i<count($menu_items);$i++) {				 
					$val = implode(",",$menu_items[$i]);
					if(!empty($whole)){
						$whole = $whole.'#'.$val;
					} else {
						$whole = $val;
					}
				}
			}
			
			if($userdata[0]['auth_type']!='0')
			{
				$loggedType="ldap";
				
				$array = array(
							'logged_in' => TRUE,
							'logged_in_user' => $userdata[0],
							'loggedType'=>$loggedType,
							'SSO_Status'=>$SSO_Status
							);
				// echo "<pre>"; print_r($whole); exit;
				$usid 	   = $array['logged_in_user']['userid'];
				$userlevel = $array['logged_in_user']['level'];
				// echo $userlevel; die();
				$array['menu_item_list'] = $whole;			
				$data['customers']	     = $this->regionsettings_model->level_map($array['logged_in_user']['level'] , $usid);
				foreach($data['customers'] as $cus){			
					$data['region_id'][]  = $cus['region_id'];			
					$data['countryid'][]  = $cus['countryid'];			
					$data['stateid'][]    = $cus['stateid'];			
					$data['locationid'][] = $cus['locationid'];		
				}
				if ($userlevel == 2) {
					$array['region_id']  = implode(',',array_unique($data['region_id']));
				} else if ($userlevel == 3) {
					$array['region_id']  = implode(',',array_unique($data['region_id']));
					$array['countryid']  = implode(',',array_unique($data['countryid'])); 
				} else if ($userlevel == 4) {
					$array['region_id']  = implode(',',array_unique($data['region_id']));
					$array['countryid']  = implode(',',array_unique($data['countryid']));
					$array['stateid']    = implode(',',array_unique($data['stateid'])); 
				} else if ($userlevel == 5) {
					$array['region_id']  = implode(',',array_unique($data['region_id']));
					$array['countryid']  = implode(',',array_unique($data['countryid']));
					$array['stateid']    = implode(',',array_unique($data['stateid']));
					$array['locationid'] = implode(',',array_unique($data['locationid'])); 
				}
						
				$this->session->set_userdata($array);
				
				if ($this->input->post('last_url')) {
					redirect($this->input->post('last_url'));
				} else {
				
					redirect('dashboard/');
				}
				//exit();
			}
			$this->load->view('login_view');
		}
		else{
			$this->session->set_flashdata('login_errors', array('Username not found. Access Denied!'));
			redirect('userlogin/automatic_view');
            exit();
		}
	}
	
	function automatic_view() {
		$this->load->view('login_view');
	}
	
	function logout($msg=false)
	{
		$SSO_Status = $this->config->item('IS_SSO');
		/*
        * destroy session*/ //&& ($SSO_Status=='1') $SSO_Status = $this->config->item('IS_SSO');
		$username = $this->session->userdata['logged_in_user']['username'];
		
		$log_user = $this->session->userdata['logged_in_user'];
		
		//do log for reseller user login
		if($log_user['role_id'] == 14) 
		{
			$log_detail = "Logout On: \n";
			$log_detail .= date('d-m-Y H:i:s')."\n";
			
			$log['jobid_fk']      = 0;
			$log['userid_fk']     = $log_user['userid'];
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->login_model->insert_row("logs", $log);
		}
		
		$sessionId = $this->session->userdata('session_id');
		$sql = "DELETE FROM `".$this->cfg['dbpref']."sessions` WHERE `session_id` = ?";
        $this->db->query($sql, array($sessionId));
		if(isset($_COOKIE['floatStat']))
		{
			setcookie('floatStat', '', 1, '/');
		} 
		if(isset($_COOKIE['sso_token']) && $this->session->userdata('loggedType')=='ldap' && ($SSO_Status=='1'))
		{
		   	$this->login_model->logoutCookie($_COOKIE['sso_token'],$username);
		}
		
		//unset the session regionid, countryid, stateid & locationid
		$this->session->unset_userdata('region_id');
		$this->session->unset_userdata('countryid');
		$this->session->unset_userdata('stateid');
		$this->session->unset_userdata('locationid');	
        $this->session->set_userdata('logged_in', FALSE);
        $this->session->set_userdata('logged_in_user', FALSE);
        $this->session->set_userdata('menu_item_list', '');
		$this->session->set_userdata('loggedType', '');
		$this->session->set_userdata('logoutType', 'ldb');
		if($msg == true) {
			$this->session->set_flashdata('confirm', array('User details updated! Please login again.'));
		}
		redirect('userlogin/');
	}
	
	
}

?>
