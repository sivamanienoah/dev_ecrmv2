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
    
    public function process_login($email, $password)
	{
        $sql = "SELECT * FROM `{$this->cfg['dbpref']}users` as u JOIN `{$this->cfg['dbpref']}roles` AS r ON r.id = u.role_id  WHERE u.`email` = ? AND u.`password` = ? AND u.`inactive` = 0 LIMIT 1";
        $user = $this->db->query($sql, array($email, $password));
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
