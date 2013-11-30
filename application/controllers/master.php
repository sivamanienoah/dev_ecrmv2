<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Master extends crm_controller {
    
	public $userdata;
	
    public function __construct()
	{
	
        parent::__construct();
		$this->login_model->check_login();        
        $this->load->model('master_model');
        $this->load->library('validation');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    public function index($limit = 0, $search = false)
	{
        
		$this->login_model->check_login();
		
        $data['customers'] = $this->master_model->master_list($limit, $search);
        
        $this->load->view('master/master_lists', $data);
        
    }
    
    public function add_master($update = false, $id = false, $ajax = false)
	{
	    $this->login_model->check_login();
		 
		    if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($_POST['delete_user'])) {
            
            // check to see if this customer has a job on the system before deleting
            // to do
            
            $this->master_model->delete_master($id);
            $this->session->set_flashdata('confirm', array('User Account Deleted!'));
            redirect('master/');
        }
        
        $rules['master_name'] = "trim|required";   
        $rules['links_to'] = "trim|required";     
        $rules['controller_name'] = "trim|required";     
		
		$this->validation->set_rules($rules);
		
		
		$fields['master_name'] = "Module Name";		 
		$fields['links_to'] = "Links To";		 
		$fields['inactive'] = 'Inactive';
		$fields['order_id'] = 'Order Id';
		$fields['master_parent_id'] = 'Parent module';
		$fields['controller_name'] = 'Label Name';
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        
        $data = '';
        $data['masters'] = $this->master_model->master_list();
	
	
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_master'])) {
		 
            $customer = $this->master_model->get_master($id);
		
            $data['this_master'] = $customer[0]['masterid'];
             if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() == false) {
			
            if ($ajax == false) {
                $this->load->view('master/add_view', $data);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }
			
		} else {
			
			// all good
 
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
			     
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
		          $user_Detail = $this->session->userdata('logged_in_user');
                //insert
				 $update_data['modified_by']=$user_Detail['userid'];			
				 $update_data['modified']=date('Y-m-d H:i:s');			
                //update
		 
                if ($this->master_model->update_master($id, $update_data)) {
                    
                    $this->session->set_flashdata('confirm', array('Master Details Updated!'));
                    redirect('master/add_master/update/' . $id);                    
                }                
                
            } else {
                
                //insert
				   $user_Detail = $this->session->userdata('logged_in_user');
                //insert
				 $update_data['created_by']=$user_Detail['userid'];			
				 $update_data['modified_by']=$user_Detail['userid'];			
				 $update_data['created']=date('Y-m-d H:i:s');	
				 $update_data['modified']=date('Y-m-d H:i:s');	
				
                if ($newid = $this->master_model->insert_master($update_data)) {
                    
                    if ($ajax == false) {
                        $this->session->set_flashdata('confirm', array('New Master Added!'));
                        redirect('master/add_master/update/' . $newid);
                    } else {
                        $json['error'] = false;
                        $json['custid'] = $newid;
                        $json['cust_name'] = $this->input->post('master_name') ;                        
                        echo json_encode($json);
                    }   
                }
            }
		}
    }
	
	public function delete_master($id = false) {
		if ($this->session->userdata('delete')==1){
			$this->login_model->check_login();
			if ($this->master_model->delete_master($id)) {
					$this->session->set_flashdata('confirm', array('User Account Deleted!'));
					redirect('master');
				}
		}else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('master');
		}
	}
    
    public function search()
	{
        $this->login_model->check_login();
		
        if (isset($_POST['cancel_submit'])) {
            
            redirect('master/');
            
        } else if ($name = $this->input->post('cust_search')) {
            
            redirect('master/index/0/' . $name);
            
        } else {
		
            redirect('master/');
            
        }
        
    }
    
    public function level_check($str)
	{
        if (!preg_match('/^[0-9]+$/', $str)) {
			$this->validation->set_message('level_check', 'Level must be selected.');
			return false;
		} else {
            return true;
        }
    }
	
	public function log_history($master = 0)
	{
		#$this->output->enable_profiler(TRUE);
		$log_master = $this->master_model->get_master($master);
		
		if (count($log_master) > 0)
		{
			if ( ! in_array($this->masterdata['level'], array(0, 1)) && $log_master[0]['masterid'] != $this->masterdata['masterid'])
			{
				$this->session->set_flashdata('login_errors', array('Your access level does not allow access to this area!'));
                redirect('notallowed/');
                exit();
			}
			$log_master = $log_master[0];
		}
		else
		{
			$log_master = $this->masterdata;
		}
		
		$log_date = $this->check_date($this->input->post('log_date'));
		
		if ( ! $log_date)
		{
			$log_date = date('Y-m-d');
		}
		
		$data['current_log_date'] = date('l, jS F y', strtotime($log_date));
		
		# now get the logs for the master on that day
		$sql = "SELECT *, DATE_FORMAT(`".$this->cfg['dbpref']."logs`.`date_created`, '%W, %D %M %y %h:%i%p') AS `fancy_date`
				FROM ".$this->cfg['dbpref']."logs
				LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."leads`.`lead_id` = `".$this->cfg['dbpref']."logs`.`jobid_fk`
				WHERE DATE(`".$this->cfg['dbpref']."logs`.`date_created`) = ?
				AND `masterid_fk` = ?
				ORDER BY `".$this->cfg['dbpref']."logs`.`date_created`";
			
		$q = $this->db->query($sql, array($log_date, $log_master['masterid']));
		$rs = $q->result_array();
		
		$data['log_master_name'] = $log_master['first_name'] . ' ' . $log_master['last_name'];
		
		$data['log_set'] = '';
		
		$time_total = 0;
		
		foreach ($rs as $row)
		{
			$log_content = nl2br($row['log_content']);
			
			$numerc_time = (int) $row['time_spent'];
			
			$time_total += $numerc_time;
			
			if ( ! isset($row['lead_title']))
			{
				$row['lead_title'] = 'General Task';
			}
			
			$row_time_spent = '';
			if ($numerc_time > 0)
			{
				$the_hours = floor($numerc_time / 60);
				$the_mins = $numerc_time % 60;
				if ($the_hours > 0)
				{
					$row_time_spent = " - Time Spent: {$the_hours} Hours";
					if ($the_mins > 0) $row_time_spent .= " {$the_mins} Mins";
				}
				else
				{
					$row_time_spent = " - Time Spent: {$the_mins} Mins";
				}
			}
			
			$data['log_set'] .= <<< EOD
	<div class="log">
		<p class="data">
		    <span>{$row['fancy_date']}</span>
		{$data['log_master_name']} - {$row['lead_title']}
		</p>
		<p class="desc">
		{$log_content}
		</p>
	</div>
EOD;
		}
		
		$hours_spent = floor( $time_total / 60);
		$remainder_mins = $time_total - ($hours_spent * 60);
		
		$mins_spent = '';
		if ($remainder_mins > 0)
		{
			$mins_spent = "{$remainder_mins} Mins";
		}
		
		if ($hours_spent > 0)
		{
			$data['total_time_spent'] = "Total Time: {$hours_spent} Hours {$mins_spent}";
		}
		else
		{
			$data['total_time_spent'] = ($mins_spent != '') ? "Total Time: {$mins_spent}" : '';
		}
		
		if ($data['log_set'] == '')
		{
			$data['log_set'] = '<h4>No logs available for this date!</h4>';
		}
		
		$this->load->view('master/log_list_view', $data);
	}
	
	public function check_date($date)
	{
		if ($date)
		{
			$date_parts = explode('-', $date);
			if (count($date_parts) == 3)
			{
				$time = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]);
				if ($time)
				{
					return $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
				}
			}
		}
		
		return FALSE;
	}
	

	public function masters()
	{	
		$this->login_model->check_login();
		
		//adding masters
		
		$rules['master_name'] = "trim|required";         
		
		$this->validation->set_rules($rules);
		
		$fields['master_name'] = "Master Name";		 
		$fields['inactive'] = 'Inactive';
		
		$this->validation->set_fields($fields);
    
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
			if ($this->validation->run() == false) {
			
            if ($ajax == false) {
                $this->load->view('master/masters_view', $data);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }
			
		} else {
		
		    foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
             }			
		    if ($this->master_model->insert_master($update_data)) {                    
                    $this->session->set_flashdata('confirm', array('Master Details Updated!'));
                    redirect('master/master_list');                    
                }		
			//$this->load->view('master/masters_view');
		}		
	}
		public function roles()
	{
		$this->load->view('master/roles_view');
	}
	function masterlist(){
	
			$this->login_model->check_login();
			
			$data['masters'] = $this->master_model->master_list($limit, $search);
			
			$data['pagination'] = '';
			if ($search == false) {
				$this->load->library('pagination');
				
				$config['base_url'] = $this->config->item('base_url') . 'master/masterlist/';
				$config['total_rows'] = (string) $this->master_model->master_count();
				$config['per_page'] = '35';
				
				$this->pagination->initialize($config);
				
				$data['pagination'] = $this->pagination->create_links();
			}
			
			$this->load->view('master/master_lists', $data);
	}

}

?>
