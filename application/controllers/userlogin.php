<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Userlogin extends crm_controller {

     public function __construct()
	{
       parent::__construct();	 
        $this->load->model('role_model');  		 
        $this->load->model('regionsettings_model'); 
    }
	
    function Userlogin() {
       
        parent::__construct();
        
    }
    
    function index() {
        /*
        * destroy session
        * show login details
        */
        $sessionId = $this->session->userdata('session_id');
		$sql = "DELETE FROM `".$this->cfg['dbpref']."sessions` WHERE `session_id` = ?";
        $this->db->query($sql, array($sessionId));
		if (isset($_COOKIE['floatStat']))
		{
			setcookie('floatStat', '', 1, '/');
		}
		//unset the session regionid, countryid, stateid & locationid
		$this->session->unset_userdata('region_id');
		$this->session->unset_userdata('countryid');
		$this->session->unset_userdata('stateid');
		$this->session->unset_userdata('locationid');	
        $this->session->set_userdata('logged_in', FALSE);
        $this->session->set_userdata('logged_in_user', FALSE);
        $this->session->set_userdata('menu_item_list', '');
        $this->load->view('login_view');
    }
    
    function process_login() {
		if ( $userdata = $this->login_model->process_login($this->input->post('email'),  sha1($this->input->post('password'))) ) {
			$menu_items=$this->role_model->UserModuleList($userdata[0]['userid']);
			// echo $this->db->last_query();exit;
			$whole='';
			$val='';
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
			//echo "<pre>"; print_r($userdata[0]); exit;
			$usid = $array['logged_in_user']['userid'];
			$userlevel = $array['logged_in_user']['level'];
			//echo $userlevel; die();
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
    }
	
	function process_remote_login($user = '', $pass = '')
	{
		if ( $userdata = $this->login_model->process_login($user,  $pass) )
		{
			echo json_encode(array('logged_in' => TRUE, 'logged_data' => $userdata[0]));
		}
		else
		{
			echo json_encode(array('error' => TRUE));
		}
	}
}

?>
