<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Role extends CI_Controller {
    
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->login_model->check_login();
        $this->load->model('role_model');     
        $this->load->model('user_model');     
        $this->load->model('master_model');     
        $this->load->library('validation');
		$this->userdata = $this->session->userdata('logged_in_role');
		$this->cfg = $this->config->item('crm');

    }
    
    public function index($limit = 0, $search = false)
	{
 
		$this->login_model->check_login();
		
        $data['customers'] = $this->role_model->role_list($limit, $search);

        $data['pagination'] = '';
        if ($search == false) {
            $this->load->library('pagination');
            
            $config['base_url'] = $this->config->item('base_url') . 'role/index/';
            $config['total_rows'] = (string) $this->role_model->role_count();
            $config['per_page'] = '35';
            
            $this->pagination->initialize($config);
            
            $data['pagination'] = $this->pagination->create_links();
        }
	
        $this->load->view('role/list_view', $data);
        
    }
    
    public function add_role($update = false, $id = false, $ajax = false)
	{
        $this->login_model->check_login();		
		 
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($_POST['delete_role'])) {
            
            // check to see if this customer has a job on the system before deleting
            // to do            
            $this->role_model->delete_role($id);
            $this->session->set_flashdata('confirm', array('Role Account Deleted!'));
            redirect('role/roles_list/');            
        }        
        $rules['name'] = "trim|required";
	   
		$this->validation->set_rules($rules);
		
		$fields['name'] = "Role Name"; 
	 
		$fields['inactive'] = 'Inactive';
		$fields['add'] = array();
		$fields['view'] = array();
		$fields['edit'] =array();
		$fields['delete'] = array();
		$fields['masterid'] = array();
		$fields['masreroleid'] = array();
		$fields['roleid'] = array();
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        
		$data = '';						
	
        $data['masterview'] = $this->master_model->master_list($limit, $search);	

        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_role'])) {
		
            $roles = $this->role_model->get_role($id);		 
		
			for($j=0;$j<count($data['masterview']);$j++) {
				for($i=0;$i<count($roles);$i++){			 	
					if($roles[$i]['masterid']==$data['masterview'][$j]['masterid']) {				 
							$data['masterview'][$i]['add']=$roles[$i]['add'];
							$data['masterview'][$i]['edit']=$roles[$i]['edit'];
							$data['masterview'][$i]['delete']=$roles[$i]['delete'];
							$data['masterview'][$i]['view']=$roles[$i]['view'];
							$data['masterview'][$i]['userroleid']=$roles[$i]['id'];
					}
				}
			}	 		 
            $data['this_role'] = $roles['role'][0]['id'];
           
            if (is_array($roles) && count($roles) > 0) foreach ($roles['role'][0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		$data['pageTree'] = $this->role_model->pageTree($id);	
		
	 
		if ($this->validation->run() == false) {
			
            if ($ajax == false) {			
                $this->load->view('role/add_view', $data);
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
			 
            	 $update_data['modified_by']=$user_Detail['userid'];			
				 $update_data['modified']=date('Y-m-d H:i:s');	 
				 
				   
                if ($this->role_model->update_role($id, $update_data)) {
                    
                    $this->session->set_flashdata('confirm', array('Role Details Updated!'));
                    redirect('role/add_role/update/' . $id);
                    
                }
                
            } else {
               $user_Detail = $this->session->userdata('logged_in_user');
                //insert
				 $update_data['userid']=$user_Detail['userid'];			
				 $update_data['modified_by']=$user_Detail['userid'];			
				 $update_data['created']=date('Y-m-d H:i:s');	 
				 $update_data['created_by']=$user_Detail['userid'];			
				 $update_data['modified']=date('Y-m-d H:i:s');						
				 
                if ($newid = $this->role_model->insert_role($update_data)) {
                    
                    if ($ajax == false) {
                        $this->session->set_flashdata('confirm', array('New Role Added!'));
                        redirect('role/add_role/update/' . $newid);
                    } else {
                        $json['error'] = false;
                        $json['id'] = $newid;
                        $json['name'] = $this->input->post('name') ; 
                        echo json_encode($json);
                    }
                    
                }                
            }
			
		}
        
    }
	
	function delete_role($id = false) {
	if ($this->session->userdata('delete')==1) {	
		$this->login_model->check_login();
					
			if ($this->role_model->delete_role($id)) {
				$this->session->set_flashdata('confirm', array('Role Account Deleted!'));
				redirect('role/');
			}
	} else {
		$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
		redirect('role/');
		}
	}
    
    public function search()
	{
        $this->login_model->check_login();
		
        if (isset($_POST['cancel_submit'])) {
            
            redirect('role/');
            
        } else if ($name = $this->input->post('cust_search')) {
            
            redirect('role/index/0/' . $name);
            
        } else {
		
            redirect('role/');
            
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
	
	public function log_history($role = 0)
	{
		#$this->output->enable_profiler(TRUE);
		$log_role = $this->role_model->get_role($role);
		
		if (count($log_role) > 0)
		{
			if ( ! in_array($this->roledata['level'], array(0, 1)) && $log_role[0]['roleid'] != $this->roledata['roleid'])
			{
				$this->session->set_flashdata('login_errors', array('Your access level does not allow access to this area!'));
                redirect('notallowed/');
                exit();
			}
			$log_role = $log_role[0];
		}
		else
		{
			$log_role = $this->roledata;
		}
		
		$log_date = $this->check_date($this->input->post('log_date'));
		
		if ( ! $log_date)
		{
			$log_date = date('Y-m-d');
		}
		
		$data['current_log_date'] = date('l, jS F y', strtotime($log_date));
		
		# now get the logs for the role on that day
		$sql = "SELECT *, DATE_FORMAT(`".$this->cfg['dbpref']."logs`.`date_created`, '%W, %D %M %y %h:%i%p') AS `fancy_date`
				FROM ".$this->cfg['dbpref']."logs
				LEFT JOIN `".$this->cfg['dbpref']."jobs` ON `".$this->cfg['dbpref']."jobs`.`jobid` = `".$this->cfg['dbpref']."logs`.`jobid_fk`
				WHERE DATE(`".$this->cfg['dbpref']."logs`.`date_created`) = ?
				AND `roleid_fk` = ?
				ORDER BY `".$this->cfg['dbpref']."logs`.`date_created`";
			
		$q = $this->db->query($sql, array($log_date, $log_role['roleid']));
		$rs = $q->result_array();
		
		$data['log_role_name'] = $log_role['first_name'] . ' ' . $log_role['last_name'];
		
		$data['log_set'] = '';
		
		$time_total = 0;
		
		foreach ($rs as $row)
		{
			$log_content = nl2br($row['log_content']);
			
			$numerc_time = (int) $row['time_spent'];
			
			$time_total += $numerc_time;
			
			if ( ! isset($row['job_title']))
			{
				$row['job_title'] = 'General Task';
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
		    <span>{$row['fancy_date']} <strong>{$row_time_spent}</strong></span>
		{$data['log_role_name']} - {$row['job_title']}
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
		
		$this->load->view('role/log_list_view', $data);
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
	
	public function region_settings()
	{
		$this->load->view('role/regionsettings_view');
	}
	public function region()
	{
		$this->login_model->check_login();
		
		//adding region
		
		$rules['region'] = "trim|required";         
		
		$this->validation->set_rules($rules);
		
		$fields['region'] = "Region Name";		 
		$fields['inactive'] = 'Inactive';
		
		$this->validation->set_fields($fields);
    
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
			if ($this->validation->run() == false) {
			 
            //if ($ajax == false) {
                $this->load->view('role/region_view', $data);
           /* } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }*/
			
		} else {
		
		    foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
             }			
		    if ($this->region_model->insert_region($update_data)) {                    
                    $this->session->set_flashdata('confirm', array('Region Details Updated!'));
                    redirect('role/region_settings');                    
                }		
			//$this->load->view('role/region_view');
		}	
		
	}

	public function country()
	{
		$this->load->view('role/country_view');
	}
	public function state()
	{
		$this->load->view('role/state_view');
	}
	public function location()
	{
		$this->load->view('role/location_view');
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
                $this->load->view('role/masters_view', $data);
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
                    redirect('role/master_list');                    
                }		
			//$this->load->view('role/masters_view');
		}		
	}
		public function roles()
	{
		$this->load->view('role/roles_view');
	}
 

}

?>
