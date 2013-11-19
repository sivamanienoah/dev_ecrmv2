<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Regionsettings extends crm_controller {
    
	public $userdata;
	
    public function __construct()
	{
        parent::__construct();
		$this->login_model->check_login();        
        $this->load->model('regionsettings_model');
        $this->load->library('validation');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    public function index()
	{
		$this->login_model->check_login();   
        $this->load->view('regionsettings/region_settings');
    }
	
	/*
	*@Region Settings
	*@User Controller
	*/
	public function region_settings($limit = 0, $search = false)
	{
		$data['tabselected'] = $limit; 
		$this->load->view('regionsettings/regionsettings_view', $data);
	}
	
	/*
	*@Region Settings
	*@User Controller
	*/
    public function levels_view($id=null)
	{
			$data['customers'] = $this->regionsettings_model->level_map($id);
			if(sizeof($data['customers'])>0){
				foreach($data['customers'] as $cus){
					$data['level_name'][]    = $cus['level_name'];
					$data['region_name'][]   = $cus['region_name'];
					$data['country_name'][]  = $cus['country_name'];
					$data['state_name'][]    = $cus['state_name'];
					$data['location_name'][] = $cus['location_name'];		
				}
			}
			$data['level_name']    = array_unique($data['level_name']);
			$data['region_name']   = array_unique($data['region_name']);
			$data['country_name']  = array_unique($data['country_name']);
			$data['state_name']    = array_unique($data['state_name']);
			$data['location_name'] = array_unique($data['location_name']);
			$this->load->view('regionsettings/mapping_view',$data);
	}

	/*
	*@Region Settings
	*@User Controller
	*/
    public function level_check($str)
	{
			if (!preg_match('/^[0-9]+$/', $str)) {
				$this->validation->set_message('level_check', 'Level must be selected.');
				return false;
			} else {
				return true;
			}
    }
	
	
	/*
	*@Region 
	*@User Controller
	*/
	public function region($update = false, $id = false, $ajax = false)
	{
		$data=array();
		$post_data = real_escape_array($this->input->post());
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_region'])) {
			$region = $this->regionsettings_model->get_region($id);
			$data['this_user'] = $region[0]['userid'];
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		$data['customers']  = $this->regionsettings_model->region_list($limit=false, $search=false);

		$this->login_model->check_login();
		
		//adding region
		$rules['region_name']  = "trim|required";         
		$this->validation->set_rules($rules);
		$fields['region_name'] = "Region Name";		 
		$fields['inactive']    = 'Inactive';
		$this->validation->set_fields($fields);
		
		//for Inactive Role
		if($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
			$this->db->where('add1_region', $id);
			$query = $this->db->get($this->cfg['dbpref'].'customers')->num_rows();
			
			$this->db->where('region_id', $id);
			$usrquery = $this->db->get($this->cfg['dbpref'].'levels_region')->num_rows();

			if($query == 0 && $usrquery == 0) {
				$data['cb_status'] = 0;
			} else {
				$data['cb_status'] = 1;
			}
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_region'])) {
            $customer            = $this->regionsettings_model->get_region($id);
            $data['this_region'] = $customer[0]['region_name'];
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		if ($this->validation->run() == false) {
            if ($ajax == false) {
                $this->load->view('regionsettings/region_view', $data);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }
			
		} else {
		    foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
			//for inactive role
			if ($update_data['inactive'] == "") {
				$update_data['inactive'] = 0;
			} else if ($update_data['inactive'] == 1) {
				$update_data['inactive'] = 1;
			} else {
				if ($data['cb_status']==0) {
					$update_data['inactive'] = 0;
				} else {
					$update_data['inactive'] = 1;
				}
			}
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($post_data['update_region'])) {
                //update
				$user_Detail                = $this->session->userdata('logged_in_user');
				$update_data['modified_by'] = $user_Detail['userid'];			
				$update_data['modified']    = date('Y-m-d H:i:s');
				
				$this->db->where('region_name', $update_data['region_name']);
				$this->db->where('regionid !=', $id);
				$dup_reg = $this->db->get($this->cfg['dbpref'].'region')->num_rows();
				
				if ($dup_reg == 0) {
					if ($this->regionsettings_model->update_region($id, $update_data)) {
						$this->session->set_flashdata('confirm', array('Region Details Updated!'));
						redirect('regionsettings/region_settings/region');                  
					}
				} else {
					$this->session->set_flashdata('login_errors', array('Region Name Already Exists!'));
					redirect('regionsettings/region_settings/region');
				}
            }
			$user_Detail                = $this->session->userdata('logged_in_user');
			$update_data['created_by']  = $user_Detail['userid'];			
			$update_data['created']     = date('Y-m-d H:i:s');
			$update_data['modified_by'] = $user_Detail['userid'];
			$update_data['modified']    = date('Y-m-d H:i:s');
				
			$this->db->where('region_name',$update_data['region_name']);
            $query = $this->db->get($this->cfg['dbpref'].'region')->num_rows();
            if($query == 0 ) {
				if ($this->regionsettings_model->insert_region($update_data)) {                    
					$this->session->set_flashdata('confirm', array('Region Details Updated!'));
					redirect('regionsettings/region_settings/region');                    
				}
			} else {
				$this->session->set_flashdata('login_errors', array('Region Name Already Exists!'));
				redirect('regionsettings/region_settings/region');
			}
		}		
	}
	
	
	/*
	*@Get Country
	*@User Controller
	*/
	public function country($update = false, $id = false, $ajax = false)
	{
		$data               = array();
		$post_data          = real_escape_array($this->input->post());
		// echo "<pre>"; print_r($post_data); exit;
		$data['customers']  = $this->regionsettings_model->country_list($limit=false, $search=false);
		$data['regions']    = $this->regionsettings_model->region_list($limit=false, $search=false);
        
		$this->login_model->check_login();
		
		//adding State
		$rules['country_name']  = "trim|required";         
		$this->validation->set_rules($rules);
		$fields['country_name'] = "Country Name";		 
		$fields['inactive']     = 'Inactive';
		$fields['regionid']     = 'regionid';
		$this->validation->set_fields($fields);
		
		//for Inactive Role
		if($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
			$this->db->where('add1_country', $id);
			$query = $this->db->get($this->cfg['dbpref'].'customers')->num_rows();
				
			$this->db->where('country_id', $id);
			$usrquery = $this->db->get($this->cfg['dbpref'].'levels_country')->num_rows();

			if($query == 0 && $usrquery == 0) {
				$data['cb_status'] = 0;
			} else {
				$data['cb_status'] = 1;
			}
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_country'])) {
            $customer = $this->regionsettings_model->get_country($id);
            $data['this_country'] = $customer[0]['country_name'];
            $data['this_country'] = $customer[0]['regionid'];
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
    
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
			if ($this->validation->run() == false) {
            if ($ajax == false) {
                $this->load->view('regionsettings/country_view', $data);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }
		} else {
		    foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
			}
			//for inactive role
			if ($update_data['inactive'] == "") {
				$update_data['inactive'] = 0;
			} else if ($update_data['inactive'] == 1) {
				$update_data['inactive'] = 1;
			} else {
				if ($data['cb_status']==0) {
					$update_data['inactive'] = 0;
				} else {
					$update_data['inactive'] = 1;
				}
			}
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($post_data['update_country'])) {
                //update
				$user_Detail = $this->session->userdata('logged_in_user');
				$update_data['modified_by']=$user_Detail['userid'];			
				$update_data['modified']=date('Y-m-d H:i:s');
				
				$this->db->where('country_name',$update_data['country_name']);
				$this->db->where('countryid != ', $id);
				$dup_cntry = $this->db->get($this->cfg['dbpref'].'country')->num_rows();
				
				if($dup_cntry == 0 ) {
					$updt_res = $this->regionsettings_model->update_country($id, $update_data);
					if ($updt_res == 1) {
						$this->session->set_flashdata('confirm', array('Country Details Updated!'));
						redirect('regionsettings/region_settings/country');                  
					} else {
						$this->session->set_flashdata('login_errors', array("Region Should Not Be Inactive!"));
						redirect('regionsettings/region_settings/country'); 
					}
				} else {
					$this->session->set_flashdata('login_errors', array('Country Name Already Exists!'));
					redirect('regionsettings/region_settings/country');  
				}
            }

			$user_Detail                = $this->session->userdata('logged_in_user');
			$update_data['created_by']  = $user_Detail['userid'];			
			$update_data['created']     = date('Y-m-d H:i:s');
			$update_data['modified_by'] = $user_Detail['userid'];			
			$update_data['modified']    = date('Y-m-d H:i:s');
				
			$this->db->where('country_name',$update_data['country_name']);
            $query = $this->db->get($this->cfg['dbpref'].'country')->num_rows();
            if($query == 0 ){	
				if ($this->regionsettings_model->insert_country($update_data)) {                    
                    $this->session->set_flashdata('confirm', array('Country Details Updated!'));
                    redirect('regionsettings/region_settings/country');          
                }
			}
			else{
					$this->session->set_flashdata('login_errors', array('Country Name Already Exists!'));
                    redirect('regionsettings/region_settings/country');  
			}
		}
	}
	
	/*
	*@Get State 
	*@User Controller
	*/
	public function state($update = false, $id = false, $ajax = false)
	{	
		$data               = array();
		$post_data          = real_escape_array($this->input->post());
		$data['customers']  = $this->regionsettings_model->state_list($limit, $search);
		$data['regions']    = $this->regionsettings_model->region_list($limit, $search);
		$this->login_model->check_login();

		//adding State
		$rules['state_name']  = "trim|required";         
		$this->validation->set_rules($rules);
		$fields['state_name'] = "State Name";		 
		$fields['inactive']   = 'Inactive';
		$fields['countryid']  = 'countryid';
		$fields['regionid']   = 'regionid';
		$this->validation->set_fields($fields);
		
		//for Inactive Role
		if($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
			$this->db->where('add1_state', $id);
			$query = $this->db->get($this->cfg['dbpref'].'customers')->num_rows();

			$this->db->where('state_id', $id);
			$usrquery = $this->db->get($this->cfg['dbpref'].'levels_state')->num_rows();

			if($query == 0 && $usrquery == 0) {
				$data['cb_status'] = 0;
			} else {
				$data['cb_status'] = 1;
			}
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_state'])) {
            $customer           = $this->regionsettings_model->get_state($id);
			$data['countrys']   = $this->regionsettings_model->country_list($limit, $search);
            $data['this_state'] = $customer[0]['state_name']; 
            $data['this_cname'] = $customer[0]['countryid']; 
            $data['this_rname'] = $customer[0]['regionid']; 
            if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		$this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		if ($this->validation->run() == false) {
				if ($ajax == false) {
					$this->load->view('regionsettings/state_view', $data);
				} else {
					$json['error'] = true;
					$json['ajax_error_str'] = $this->validation->error_string;
					echo json_encode($json);
				}
		} else {
			foreach($fields as $key => $val) {
				$update_data[$key] = $this->input->post($key);
			}
			//for inactive role
			if ($update_data['inactive'] == "") {
				$update_data['inactive'] = 0;
			} else if ($update_data['inactive'] == 1) {
				$update_data['inactive'] = 1;
			} else {
				if ($data['cb_status']==0) {
					$update_data['inactive'] = 0;
				} else {
					$update_data['inactive'] = 1;
				}
			}
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($post_data['update_state'])) {
				$user_Detail = $this->session->userdata('logged_in_user');
				$update_data['modified_by']=$user_Detail['userid'];			
				$update_data['modified']=date('Y-m-d H:i:s');
				unset($update_data['regionid']);
				
				$this->db->where('state_name',$update_data['state_name']);
				$this->db->where('stateid != ', $id);
				$dup_ste = $this->db->get($this->cfg['dbpref'].'state')->num_rows();
				
				if($dup_ste == 0 ) {
					$updt_res = $this->regionsettings_model->update_state($id, $update_data);
					if ($updt_res == 1) {
						$this->session->set_flashdata('confirm', array('State Details Updated!'));
						redirect('regionsettings/region_settings/state');              
					} else {
						$this->session->set_flashdata('login_errors', array("Country Should Not Be Inactive!"));
						redirect('regionsettings/region_settings/state'); 
					}
				} else {
					$this->session->set_flashdata('login_errors', array('State Name Already Exists!'));
					redirect('regionsettings/region_settings/state');
				}
			}
			$user_Detail = $this->session->userdata('logged_in_user');
			$update_data['created_by']=$user_Detail['userid'];			
			$update_data['created']=date('Y-m-d H:i:s');
			$update_data['modified_by']=$user_Detail['userid'];			
			$update_data['modified']=date('Y-m-d H:i:s');
			unset($update_data['regionid']);
			
			$this->db->where('state_name', $update_data['state_name']);
			$query = $this->db->get($this->cfg['dbpref'].'state')->num_rows();
			if($query == 0 ) {	
				if ($this->regionsettings_model->insert_state($update_data)) {  
					$this->session->set_flashdata('confirm', array('State Details Updated!'));
					redirect('regionsettings/region_settings/state');                    
				}
			} else {
					$this->session->set_flashdata('login_errors', array('State Name Already Exists!'));
					redirect('regionsettings/region_settings/state');
			}
		}
	}
	
	/*
	*@Get Location List
	*@User Controller
	*/
	public function location($update = false, $id = false, $ajax = false)
	{ 
		$post_data          = real_escape_array($this->input->post());
		$data               = array();
		$data['customers']  = $this->regionsettings_model->location_list($limit, $search);
		$data['regions']    = $this->regionsettings_model->region_list($limit, $search);

		$this->login_model->check_login();
		
		//Adding State
		$rules['location_name']  = "trim|required";         
		$this->validation->set_rules($rules);
		$fields['location_name'] = "Location Name";		 
		$fields['inactive']      = 'Inactive';
		$fields['stateid']       = 'stateid';
		$fields['regionid']      = 'regionid';
		$fields['countryid']     = 'countryid';
		
		$this->validation->set_fields($fields);
		
		//for Inactive Role
		if($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
			$this->db->where('add1_location', $id);
			$query = $this->db->get($this->cfg['dbpref'].'customers')->num_rows();

			$this->db->where('location_id', $id);
			$usrquery = $this->db->get($this->cfg['dbpref'].'levels_location')->num_rows();

			if($query == 0 && $usrquery == 0) {
				$data['cb_status'] = 0;
			} else {
				$data['cb_status'] = 1;
			}
		}
		
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_location'])) {
			$customer = $this->regionsettings_model->get_location($id);
			$data['states']     = $this->regionsettings_model->state_list($limit, $search);
			$data['countrys']   = $this->regionsettings_model->country_list($limit, $search);
            $data['this_location'] = $customer[0]['location_name'];
            $data['this_sid']      = $customer[0]['stateid'];
			if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		if ($this->validation->run() == false) {
            if ($ajax == false) {
                $this->load->view('regionsettings/location_view', $data);
            } else {
                $json['error'] = true;
                $json['ajax_error_str'] = $this->validation->error_string;
                echo json_encode($json);
            }			
		} else {
		     foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
             }
			 //for inactive role
			if ($update_data['inactive'] == "") {
				$update_data['inactive'] = 0;
			} else if ($update_data['inactive'] == 1) {
				$update_data['inactive'] = 1;
			} else {
				if ($data['cb_status']==0) {
					$update_data['inactive'] = 0;
				} else {
					$update_data['inactive'] = 1;
				}
			}
			 
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($post_data['update_location'])) {
                //update
				$user_Detail                = $this->session->userdata('logged_in_user');
				$update_data['modified_by'] = $user_Detail['userid'];			
				$update_data['modified']    = date('Y-m-d H:i:s');
				unset($update_data['regionid']);				
				unset($update_data['countryid']);
				
				$this->db->where('location_name',$update_data['location_name']);
				$this->db->where('locationid !=', $id);
				$dup_loc = $this->db->get($this->cfg['dbpref'].'location')->num_rows();
				
				if($dup_loc == 0) {
					$updt = $this->regionsettings_model->update_location($id, $update_data);
					if ($updt == 1) {
						$this->session->set_flashdata('confirm', array('Location Details Updated!'));
						redirect('regionsettings/region_settings/location');
					} else {
						$this->session->set_flashdata('login_errors', array("State Should Not Be Inactive!"));
						redirect('regionsettings/region_settings/location'); 					
					}
				} else {
					$this->session->set_flashdata('login_errors', array('Location Name Already Exists!'));
					redirect('regionsettings/region_settings/location');
				}
            }
			$user_Detail = $this->session->userdata('logged_in_user');
			$update_data['created_by']=$user_Detail['userid'];			
			$update_data['created']=date('Y-m-d H:i:s');	
			$update_data['modified_by']=$user_Detail['userid'];			
			$update_data['modified']=date('Y-m-d H:i:s');
			unset($update_data['regionid']);				
			unset($update_data['countryid']);	
				
			$this->db->where('location_name',$update_data['location_name']);
            $query = $this->db->get($this->cfg['dbpref'].'location')->num_rows();
            if($query == 0 ) {	
				if ($this->regionsettings_model->insert_location($update_data)) {		
                    $this->session->set_flashdata('confirm', array('Location Details Updated!'));
                    redirect('regionsettings/region_settings/location');                    
                }	
			} else {
				$this->session->set_flashdata('login_errors', array('Location Name Already Exists!'));
				redirect('regionsettings/region_settings/location');
			}
		}
	}
	
	public function region_redirect() {
		redirect('regionsettings/region_settings/region');
	}


	/*
	*@Delete region Record
	*@User Controller
	*/
	public function region_delete($delete = false, $id = false, $ajax = false)
	{ 
		if ($this->session->userdata('deleteAdmin')==1){	
				$this->login_model->check_login();
				if ($delete == 'delete' && preg_match('/^[0-9]+$/', $id)) {
					//delete
					if ($this->regionsettings_model->delete_region($id, $update_data)) {
						$this->session->set_flashdata('confirm', array('Region Deleted!'));
						redirect('regionsettings/region_settings/region');                  
					}                
				}
		} else {
				$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
				redirect('regionsettings/region_settings/region'); 
		}		
	}

	/*
	*@Delete Country Record
	*@User Controller
	*/
	public function country_delete($delete = false, $id = false, $ajax = false)
	{ 
		if ($this->session->userdata('deleteAdmin')==1) {	
			$this->login_model->check_login();
			if ($delete == 'delete' && preg_match('/^[0-9]+$/', $id)) {
				//delete
				if ($this->regionsettings_model->delete_country($id, $update_data)) {
					$this->session->set_flashdata('confirm', array('Country Deleted!'));
					redirect('regionsettings/region_settings/country');                  
				}                
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('regionsettings/region_settings/country');
		}		
	}
	
	/*
	*@Delete State Record
	*@User Controller
	*/
	public function state_delete($delete = false, $id = false, $ajax = false)
	{ 
		if ($this->session->userdata('deleteAdmin')==1) {
		$this->login_model->check_login();
			if ($delete == 'delete' && preg_match('/^[0-9]+$/', $id)) {
                //delete
                if ($this->regionsettings_model->delete_state($id, $update_data)) {
                    $this->session->set_flashdata('confirm', array('State Deleted!'));
                    redirect('regionsettings/region_settings/state');                  
                }                
            }
		}else {
				$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
				redirect('regionsettings/region_settings/state'); 
		}	
	}
	
	/*
	*@Delete Location Record
	*@User Controller
	*/
	public function location_delete($delete = false, $id = false, $ajax = false)
	{
		if ($this->session->userdata('deleteAdmin')==1){
			$this->login_model->check_login();
							 
				if ($delete == 'delete' && preg_match('/^[0-9]+$/', $id)) {
					//delete
					if ($this->regionsettings_model->delete_location($id, $update_data)) {
						$this->session->set_flashdata('confirm', array('Location Deleted!'));
						redirect('regionsettings/region_settings/location');                  
					}                
				}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('regionsettings/region_settings/location'); 
		}			
	}

	/*
	*@Get Country Record for adding Customer Page
	*@User Controller
	*/
	public function getCountry($value,$id,$updt) {
		$data = array();
		$data = $this->regionsettings_model->getcountry_list($value);
		$opt  = '';
		$opt .= '<select name="add1_country" id="add1_country" class="textfield width200px" onchange="getState(this.value)">';
		$opt .= '<option value="0">Select Country</option>';
		if(sizeof($data)>0){
			foreach($data as $country){
				if($id == $country['countryid']) 
				$opt .= '<option value="'.$country['countryid'].'" selected = "selected" >'.$country['country_name'].'</option>';			
				else 
				$opt .= '<option value="'.$country['countryid'].'">'.$country['country_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		//Code for Adding New Country in Customer Page.
		if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2) {
			if ($updt != "update") {
				$opt .= "<a class='addNew' id='addButton' onclick='ajxCty()'></a>";
			}
		}	
		$opt .= "<div id='addcountry' class='addCus'>";
		$opt .= "Add Country: <input type='text' class='textfield width200px required' name='addcountry' id='newcountry'>";
		$opt .= "<a class='addSave' id='savecountry' onclick='ajxSaveCty()'></a>";
		$opt .= "</div>";
		echo $opt;
	}
	
	/*
	*@Get Country Record for adding location
	*@User Controller
	*/
	public function getCountrylo($value,$id) {

		$data = array();
		$data = $this->regionsettings_model->getcountry_list($value);
		$opt  = '';
		$opt .= '<select name="add1_country" id="add1_country" class="textfield width200px" onchange="getStateloc(this.value)">';
		$opt .= '<option value="0">Select Country</option>';
		if(sizeof($data)>0){
			foreach($data as $country){
				if($id == $country['countryid']) 
				$opt .= '<option value="'.$country['countryid'].'" selected = "selected" >'.$country['country_name'].'</option>';			
				else 
				$opt .= '<option value="'.$country['countryid'].'">'.$country['country_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		echo $opt;
	}

	/*
	*@Get Country Record List 
	*@User Controller
	*/
	public function getCountryst($value,$id) {

		$data=array();
		$data = $this->regionsettings_model->getcountry_list($value);
		$opt = '';
		$opt .= '<select name="countryid" id="country_id" class="textfield width200px">';
		$opt .= '<option value="0">Select Country</option>';
		foreach($data as $country){
			if($id == $country['countryid']) 
			$opt .= '<option value="'.$country['countryid'].'" selected = "selected" >'.$country['country_name'].'</option>';			
			else 
			$opt .= '<option value="'.$country['countryid'].'">'.$country['country_name'].'</option>';			
		}
		$opt .= '</select>';
		echo $opt;
	}

	/*
	*@Get State List for adding customer page
	*@User Controller
	*/
	public function getState($value,$id,$updt) {
	
		$data=array();
		$data = $this->regionsettings_model->getstate_list($value);
		$opt = '';
		$opt .= '<select name="add1_state" id="add1_state" onchange="getLocation(this.value)" class="textfield width200px">';
		$opt .= '<option value="0">Select State</option>';

		foreach($data as $state){
			if($id == $state['stateid']) 
			$opt .= '<option value="'.$state['stateid'].'" selected = "selected" >'.$state['state_name'].'</option>';			
			else 
			$opt .= '<option value="'.$state['stateid'].'">'.$state['state_name'].'</option>';			
		}
		$opt .= '</select>';
		//Code for Adding New State in Customer Page.
		if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3) {
			if ($updt != "update") {
				$opt .= "<a class='addNew' id='addStButton' onclick='ajxSt()'></a>";
			}
		}	
		$opt .= "<div id='addstate' class='addCus'>";
		$opt .= "Add State : <input type='text' class='textfield width200px required' name='addstate' id='newstate' />";
		$opt .= "<a class='addSave' id='savestate' onclick='ajxSaveSt()'></a>";
		$opt .= "</div>";
		echo $opt;
	}
	
	/*
	*@Get State List for adding location page
	*@User Controller
	*/
	public function getStateloc($value,$id) {
		$data = array();
		$data = $this->regionsettings_model->getstate_list($value);
		$opt  = '';
		$opt .= '<select name="stateid" id="stateid" onchange="getLocation(this.value)" class="textfield width200px">';
		$opt .= '<option value="0">Select State</option>';
		if(sizeof($data)>0){
			foreach($data as $state){
				if($id == $state['stateid']) 
				$opt .= '<option value="'.$state['stateid'].'" selected = "selected" >'.$state['state_name'].'</option>';			
				else 
				$opt .= '<option value="'.$state['stateid'].'">'.$state['state_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		echo $opt;
	}

	/*
	*@Get Location List
	*@User Controller
	*/
	public function getLocation($value,$id,$updt) {
		$data = array();
		$data = $this->regionsettings_model->getlocation_list($value);
		$opt  = '';
		$opt .= '<select name="add1_location" id="add1_location" class="textfield width200px">';
		$opt .= '<option value="0">Select Location</option>';
		if(sizeof($data)>0){
			foreach($data as $location){
				if($id == $location['locationid']) 
				$opt .= '<option value="'.$location['locationid'].'" selected = "selected" >'.$location['location_name'].'</option>';			
				else 
				$opt .= '<option value="'.$location['locationid'].'">'.$location['location_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		
		//Code for Adding New Location in Customer Page.
		if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4) {
			if ($updt != "update") {
				$opt .= "<a class='addNew' id='addLocButton' onclick='ajxLoc()'></a>";
			}
		}	
		$opt .= "<div id='addLocation' class='addCus'>";
		$opt .= "Add Location: <input type='text' class='textfield width200px required' name='addlocation' id='newlocation' />";
		$opt .= "<a class='addSave' id='savelocation' onclick='ajxSaveLoc()'></a>";
		$opt .= "</div>";
		echo $opt;
	}

	/*
	*@Country Add Ajax for Region Settings
	*@User Controller
	*/
	//Function for adding New Country, New State & New Location in the Customer Details page. -- Starts here.
	public function country_add_ajax($ajax_update) {
		$post_data                   = real_escape_array($this->input->post());
		$ajax_update['country_name'] = $post_data['country_name'];
		$ajax_update['created_by']   = $post_data['created_by'];
		$ajax_update['modified_by']  = $post_data['created_by'];
		$ajax_update['regionid']     = $post_data['regionid'];
		$ajax_update['created']      = date('Y-m-d H:i:s');
		$ajax_update['modified']     = date('Y-m-d H:i:s');
		
		$this->regionsettings_model->insert_country($ajax_update);
		$cid = $this->db->insert_id();
		$this->getCountry($ajax_update['regionid'],$cid);
	}


	/*
	*@Satate Add Ajax for Region Settings
	*@User Controller
	*/
	public function state_add_ajax($ajax_update) {
		
		$post_data                  = real_escape_array($this->input->post());
		$ajax_update['state_name']  = $post_data['state_name'];
		$ajax_update['created_by']  = $post_data['created_by'];
		$ajax_update['modified_by'] = $post_data['created_by'];
		$ajax_update['countryid']   = $post_data['countryid'];
		$ajax_update['created']     = date('Y-m-d H:i:s');
		$ajax_update['modified']    = date('Y-m-d H:i:s');
		
		$this->regionsettings_model->insert_state($ajax_update);
		$stateId = $this->db->insert_id();
		$this->getState($ajax_update['countryid'],$stateId);
	}
	
	/*
	*@Location Add Ajax for Region Settings
	*@User Controller
	*/
	public function location_add_ajax($ajax_update) {

		$post_data = real_escape_array($this->input->post());
	
		$ajax_update['location_name'] = $post_data['location_name'];
		$ajax_update['created_by']    = $post_data['created_by'];
		$ajax_update['modified_by']   = $post_data['created_by'];
		$ajax_update['stateid']       = $post_data['stateid'];
		$ajax_update['created']       = date('Y-m-d H:i:s');
		$ajax_update['modified']      = date('Y-m-d H:i:s');
		
		$this->regionsettings_model->insert_location($ajax_update);
		$locationId = $this->db->insert_id();
		$this->getLocation($ajax_update['stateid'],$locationId);
	}
	//Function for adding New Country, New State & New Location in the Customer Details page. -- Ends here.

	/*
	*@Delete Level Record
	*@User Controller
	*/
	public function level_delete($delete = false, $id = false, $ajax = false)
	{ 
		if ($this->session->userdata('deleteAdmin')==1) {	
			$this->login_model->check_login();
			if ($delete == 'delete' && preg_match('/^[0-9]+$/', $id) ) {
				//delete
				if ($this->regionsettings_model->delete_level($id, $update_data)) {
					$this->session->set_flashdata('confirm', array('Level Deleted!'));
					redirect('regionsettings/level');                  
				}                
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('regionsettings/level'); 
		}			
	}
	

	/*
	*@Level
	*@User Controller
	*/
	public function level($update = false, $id = false, $ajax = false)
	{
		$post_data = real_escape_array($this->input->post());
		$data      = array();
		$data['regionvalue'] = $this->regionsettings_model->level_map($id);
		if(sizeof($data['regionvalue'])>0){
			foreach($data['regionvalue'] as $cus){
				$data['level_name'][]    = $cus['level_name'];
				$data['region_id'][]     = $cus['region_id'];
				$data['country_id'][]    = $cus['countryid'];
				$data['state_id'][]      = $cus['stateid'];
				$data['location_id'][]   = $cus['locationid'];		
			}
		}
		$data['level_name']              = array_unique($data['level_name']);
		$data['region_id']               = array_unique($data['region_id']);
		$data['country_id']              = array_unique($data['country_id']);
		$data['state_id']                = array_unique($data['state_id']);
		$data['location_id']             = array_unique($data['location_id']);
		$data['customers']               = $this->regionsettings_model->level_list($limit, $search);
		$data['regions']                 = $this->regionsettings_model->region_list($limit, $search);
		$this->login_model->check_login();
		
		//adding region
		$rules['level_name']             = "trim|required";
		$rules['region']                 = "required|callback_check_default";	
		$this->validation->set_rules($rules);
		$fields['level_name']            = "Level Name";		 
		$fields['inactive']              = 'Inactive';
		$fields['region']                = 'Region'.' '.array();
		$fields['country_state']         =  array();
		$fields['state_location']        =  array();
		$fields['location']              =  array();
		$this->validation->set_fields($fields);
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_level'])) {
            $customer            = $this->regionsettings_model->get_level($id);
            $data['this_level']  = $customer[0]['level_name'];
            $data['this_region'] = $customer[0]['region_name'];
			if (is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
			
		$this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		if ($this->validation->run() == false) {
			if ($ajax == false) {
			//echo '<pre>'; print_r($data); echo'</pre>';
				$this->load->view('regionsettings/level_view', $data);
			} else {
				$json['error'] = true;
				$json['ajax_error_str'] = $this->validation->error_string;
				echo json_encode($json);
			}
		} else {
			foreach($fields as $key => $val) {
				$update_data[$key] = $this->input->post($key);
			}	
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id)&& isset($post_data['update_level'])) {
			//update
			$user_Detail                = $this->session->userdata('logged_in_user');
			$update_data['modified_by'] = $user_Detail['userid'];			
			$update_data['modified']    = date('Y-m-d H:i:s');
			if ($this->regionsettings_model->update_level($update_data,$id)) {
				$this->session->set_flashdata('confirm', array('Level Details Updated!'));
				redirect('regionsettings/level');                  
			}                
		}
				
		$user_Detail = $this->session->userdata('logged_in_user');
		$update_data['created_by']=$user_Detail['userid'];			
		$update_data['created']=date('Y-m-d H:i:s');	
		$update_data['modified_by']=$user_Detail['userid'];			
		$update_data['modified']=date('Y-m-d H:i:s');
			
		$this->db->where('level_name',$update_data['level_name']);
		$query = $this->db->get($this->cfg['dbpref'].'levels')->num_rows();
		if($query == 0 ) {
			if ($this->regionsettings_model->insert_level($update_data)) { 
					$this->session->set_flashdata('confirm', array('Levels Details Updated!'));
					redirect('regionsettings/level');                    
				}
			} else {
					$this->session->set_flashdata('login_errors', array('Level Name Already Exists!'));
					redirect('regionsettings/level');
			}
	    }		
	}

	
	/*
	*@Search Level Record
	*@User Controller
	*/	
	public function level_search()
	{
		$post_data = real_escape_array($this->input->post());
        if (isset($post_data['cancel_submit'])) {
            redirect('regionsettings/level/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('regionsettings/level_search_view/0/' . $name);
        } else {
            redirect('regionsettings/level/');
        }
 
    }
	
	/*
	*@Search Level View
	*@User Controller
	*/
	public function level_search_view($limit = 0, $search = false)
	{
		$data['regions']   = $this->regionsettings_model->region_list();
        $data['customers'] = $this->regionsettings_model->level_list($limit, $search);

        $data['pagination'] = '';
        if ($search == false) {
            $this->load->library('pagination');
            $config['base_url']   = $this->config->item('base_url') . 'regionsettings/level_search_view/';
            $config['total_rows'] = (string) $this->regionsettings_model->level_count();
            $config['per_page']   = '35';
            $this->pagination->initialize($config);
            $data['pagination']   = $this->pagination->create_links();
        }
        $this->load->view('regionsettings/level_view', $data);
    }

	/*
	*@Get Country List
	*@User Controller
	*/
	public function getCountryList($value) {
		$data  	   = array();
		if(!empty($value))
		$sel   = @explode(":",$value);
		$data  = $this->regionsettings_model->getcountry_multiplelist($value);
		$selected_data = $sel[1];
		if(!empty($selected_data))
		$vals  = @explode(',',$selected_data);
		if(!empty($value))			
		$val   = @explode(',',$value);
		$opt   = '';
		$opt  .= '<select id="country_state" name="country_state[]" multiple="multiple" class="textfield width200px" onclick="getStateLists(this.value)">';
		if(sizeof($data)>0){
			foreach($data as $country){
				if(in_array($country['countryid'],$vals)) { $valp = 'selected="selected"';  } else {$valp = "";  }
				//print_r($valp);
				$opt .= '<option value="'.$country['countryid'].'"'.$valp.'>'.$country['country_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		echo $opt;
	}
	
	/*
	*@Get State List
	*@User Controller
	*/
	public function getStateList($value) {
		$data          = array();
		if(!empty($value)){
			$sel       = explode(":",$value);
		}
		$data          = $this->regionsettings_model->getstate_multiplelist($value);
		$selected_data = $sel[1];
		if(!empty($selected_data)){
			$vals      = explode(',',$selected_data);
		}
		if(!empty($value)){
			$val       = explode(',', $value);
		}
		$opt           = '';
		$opt  .= '<select id="state_location" name="state_location[]" multiple="multiple" class="textfield width200px" onclick="getLocationLists(this.value)">';
		if(sizeof($data)>0){
			foreach($data as $state){
				if(in_array($state['stateid'],$vals)) { $valp = 'selected="selected"';  } else {$valp = "";  }
				$opt .= '<option value="'.$state['stateid'].'"'.$valp.'>'.$state['state_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		echo $opt;
	}

	/*
	*@Get Location List
	*@User Controller
	*/
	public function getLocationList($value) {
		$data=array();
		if(!empty($value))
		$sel = explode(":",$value);
		$data = $this->regionsettings_model->getlocation_multiplelist($sel[0]);
		$selected_data = $sel[1];
		if(!empty($selected_data))
		$vals = explode(',',$selected_data);
		if(!empty($value))
		$val = explode(',', $value);
		$opt = '';
		$opt .= '<select id="location" name="location[]" multiple="multiple" class="textfield width200px">';
		if(sizeof($data)>0){
			foreach($data as $location){
				if(in_array($location['locationid'],$vals)) { $valp = 'selected="selected"';  } else {$valp = "";  }
				$opt .= '<option value="'.$location['locationid'].'"'.$valp.'>'.$location['location_name'].'</option>';			
			}
		}
		$opt .= '</select>';
		echo $opt;
	}
	
	
	/*
	*@Check Exist User Record
	*@User Controller
	*/
	function getResultfromdb($username){
		$this->db->where('level_name',$username);
		$query = $this->db->get($this->cfg['dbpref'].'levels')->num_rows();
		if($query == 0 ) echo 'userOk';
		else echo 'userNo';
    }
	
	/*
	*@Get User Result From Region
	*@User Controller
	*/
	function getResultfromRegion($username){            
		$this->db->where('region_name',$username);
		$query = $this->db->get($this->cfg['dbpref'].'region')->num_rows();
		if($query == 0 ) echo 'userOk';
		else echo 'userNo';
    }
	
	/*
	*@Check Region,Country,State,Location Status
	*
	*/
	function ajax_check_status_rcsl()
	{
		$data =	real_escape_array($this->input->post()); // escape special characters
		$this->regionsettings_model->check_status_rcsl($data);
	}
}

?>
