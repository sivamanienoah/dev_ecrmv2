<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hosting extends crm_controller {

	function Hosting()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->load->model('hosting_model');
        $this->load->model('customer_model');
		$this->load->model('package_model');
        $this->load->library('validation');
	}
	
	function index($limit = 0, $search = false) {
		$data['accounts'] = $this->hosting_model->account_list($limit, $search);
		$this->load->view('hosting_view', $data);
	}
	
	function delete_account($id = false) {
		if ($this->session->userdata('delete')==1) {
			$this->hosting_model->delete_row('hosting', 'hostingid', $id);
			$this->hosting_model->delete_row('hosting_package', 'hostingid_fk', $id);
			$this->hosting_model->delete_row('dns', 'hostingid', $id);
			$this->session->set_flashdata('confirm', array('Hosting Account Deleted!'));
			redirect('hosting');
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('hosting');
		}
	}
	
	function add_account($update = false, $id = false) { 
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && isset($_POST['delete_account'])) {
            $this->hosting_model->delete_row('hosting', 'hostingid', $id);
            $this->session->set_flashdata('confirm', array('Hosting Account Deleted!'));
            redirect('hosting/');
        }

		$data['packageid_fk'] = $this->hosting_model->get_row_bycond('hosting_package', 'hostingid_fk', $id);
		$data['package'] = $this->hosting_model->get_row_bycond('package', 'status', 'active');
        $rules['domain_name'] = "trim|required|callback_domain_check";
		//$rules['expiry_date'] = "trim|required";
        $rules['customer_id'] = "required|integer|callback_is_valid_customer";
		if (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'ENOAH' && $_POST['domain_mgmt'] != 'CM') {	
			//$rules['domain_expiry'] = "trim|required|callback_is_valid_domain_date";
			$rules['domain_expiry'] = "trim|required";
		}
		$this->validation->set_rules($rules);
		$fields['domain_name'] = "Domain Name";
		$fields['domain_expiry'] = "Domain Name Expiry";
		$fields['expiry_date'] = "Expiry Date";
        $fields['customer_id'] = "Customer/Business Name";
        $fields['domain_status'] = 'Domain Status';
		$fields['ssl'] = 'SSL';
		$fields['other_info'] = 'Other information';
		$this->validation->set_fields($fields);
        if (!$this->input->post('expiry_date') && !$update)
        $this->validation->expiry_date = date('d-m-Y', strtotime('+1 year'));
        if (!$this->input->post('domain_name') && !$update)
        $this->validation->domain_name = 'www.';
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        $data['test_data'] = '';
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_account'])) {
            $account = $this->hosting_model->get_account($id);
            if (is_array($account) && count($account) > 0) foreach ($account[0] as $k => $v) {
                if (isset($this->validation->$k)) {
					if ($k == 'expiry_date') $v = date('d-m-Y', strtotime($v));
					if ($k == 'domain_expiry' && !is_null($v)) $v = date('d-m-Y', strtotime($v));
					$this->validation->$k = $v;
				}
                if ($k == 'custid_fk') {
                    $data['customer_id'] = $v;
                    $data['customer_name'] = preg_replace('/\|[0-9]+$/', '', $this->hosting_model->customer_account($v));
                }
            }
        }
		if ($this->validation->run() == false) {            
			$this->load->view('hosting_add_view', $data);
		} else {
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
            $update_data['custid_fk'] = $this->input->post('customer_id');
            unset($update_data['customer_id']);
			if(!empty($update_data['expiry_date'])) {
			$mdate = explode('-', $update_data['expiry_date']);
			$time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
			$update_data['expiry_date'] = date('Y-m-d', $time);
			}
			if (trim($update_data['domain_expiry']) != '' && isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'ENOAH') {
				$mdate = explode('-', $update_data['domain_expiry']);
				$time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
				$update_data['domain_expiry'] = date('Y-m-d', $time);
			} else {
				$update_data['domain_expiry'] = NULL;
			}
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
                if ($this->hosting_model->update_account($id, $update_data)) {
					//delete and again inserting into hosting_package - Starts here
					$this->hosting_model->delete_row('hosting_package', 'hostingid_fk', $id);
					$packageid_fk = $this->input->post('packageid_fk');
					if(is_array($packageid_fk))
					foreach($packageid_fk as $val){
						$duedate='0000-00-00';
						foreach($data['packageid_fk'] as $v){
							if($v['packageid_fk'] == $val) $duedate = $v['due_date'];
						}
						$update_packageid = array(packageid_fk => $val);
						if ($val != 0) { 
							$this->hosting_model->insert_row('hosting_package', array('hostingid_fk'=>$id, 'packageid_fk'=>$val, 'due_date'=>$duedate));
						}
					} 
                    $this->session->set_flashdata('confirm', array('Account Details Updated!'));
                    redirect('hosting/');
                }
            } else {
                if ($newid = $this->hosting_model->insert_account($update_data)) {
					//inserting into hosting_package - Starts here
					$packageid_fk=$this->input->post('packageid_fk');
					if(is_array($packageid_fk))
					foreach($packageid_fk as $val){
						$duedate='0000-00-00';
						foreach($data['packageid_fk'] as $v){
							if($v['packageid_fk']==$val) $duedate=$v['due_date'];
						}
						if ($val != 0) { 
							$this->hosting_model->insert_row('hosting_package', array('hostingid_fk'=>$newid, 'packageid_fk'=>$val, 'due_date'=> $duedate));
						}
					}
                    $this->session->set_flashdata('confirm', array('New Account Added!'));
					redirect('hosting/');
                }
            }
		}
	}
	
    function domain_check($domain) {
        if (!preg_match('/^[a-z0-9\-_\.]+\.[a-z]{2,}$/i', $domain)) {
            $this->validation->set_message('domain_check', 'The domain name specified does not appear to be a properly formatted domain name.');
			return false;
        } else if ( $this->hosting_model->check_unique(trim($domain)) && $this->uri->segment(3) != 'update') {
            $this->validation->set_message('domain_check', 'The domain name specified already exists! Please supply a different domain name.');
			return false;
        } else {
            return true;
        }
    }
    
    function is_valid_date($date) {
		$mdate = explode('-', $date);
		if (is_array($mdate) && count($mdate) == 3) {
			$time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
			if ($time && $time > time()) {
				return TRUE;
			}
		}
		$this->validation->set_message('is_valid_date', 'The expiry date needs to be in a correct format (dd-mm-yyyy) and the date should be a future date.');
		return FALSE;
    }
    
	function is_valid_domain_date($date) {	//echo $date;
		$mdate = explode('-', $date);
		if (is_array($mdate) && count($mdate) == 3) {
			$time = mktime(0, 0, 0, $mdate[1], $mdate[0], $mdate[2]);
			if ($time) {
				return TRUE;
			}
		}
		$this->validation->set_message('is_valid_domain_date', 'The expiry date needs to be in a correct format (dd-mm-yyyy) and the date should be a future date.');
		return FALSE;
	}
	
    function is_valid_customer($id) {
        if ($this->hosting_model->customer_account($id) == false) {
            $this->validation->set_message('is_valid_customer', 'Please enter a existing customer/company name.');
			return false;
        } else {
            return true;
        }
    }
    
	function search() {
        if (isset($_POST['cancel_submit'])) {
            redirect('hosting/');
        } else if ($name = $this->input->post('account_search')) {
            redirect('hosting/index/0/' . $name);
        } else {
            redirect('hosting/');
        }
    }
   
	function ajax_customer_search() {
        if ($this->input->post('cust_name')) {
            $result = $this->customer_model->customer_list(0, $this->input->post('cust_name'));
            $i=0;
            if (count($result) > 0) foreach ($result as $cust) {
                //$company = (trim($cust['company']) == '') ? '' : " - " . $cust['company'];
                //echo "{$cust['first_name']} {$cust['last_name']}{$company}|{$cust['custid']}|{$cust['add1_region']}|{$cust['add1_country']}|{$cust['add1_state']}|{$cust['add1_location']}\n";
				if(!empty($cust)) {
					$res[$i]['id'] = $cust['custid'];
					$res[$i]['label'] = $cust['first_name'].' '.$cust['last_name'].' - '. $cust['company'];
					$res[$i]['regId'] = $cust['add1_region'];
					$res[$i]['cntryId'] = $cust['add1_country'];
					$res[$i]['stId'] = $cust['add1_state'];
					$res[$i]['locId'] = $cust['add1_location'];
				}
		 		$i++;
            }
        }
        echo json_encode($res); exit;
    }
	
	/* function ajax_customer_search()
    {
        if ($this->input->post('q')) {
            $result = $this->customer_model->customer_list(0, $this->input->post('q'));
            if (count($result) > 0) foreach ($result as $cust) {
                $company = (trim($cust['company']) == '') ? '' : " - " . $cust['company'];
                echo "{$cust['first_name']} {$cust['last_name']}{$company}|{$cust['custid']}|{$cust['add1_region']}|{$cust['add1_country']}|{$cust['add1_state']}|{$cust['add1_location']}\n";
            }
        }
    } */
    
	function hosts($custid='') {
		if($custid<=0) redirect('hosting/');
		$data['hosting']=$this->hosting_model->get_hosting($custid);
		$data['hosts']='HOSTS';
		$this->load->view('hosting_view', $data);
	}
	
	function due_date($hostingid=0,$packageid=0) {
		if($hostingid==0) 
		redirect('hosting/');
		//$data = real_escape_array($_POST);
		$add_duedate = $this->input->post('Add_duedate');
		$duedate = $this->input->post('due_date');
		if(isset($add_duedate) && $add_duedate == 'edit') {
			if($duedate == '') 
			$duedate = '00-00-0000';
			$d=explode('-',$duedate);
			if(sizeof($d)>0){
				$due_date=$d[2].'-'.$d[1].'-'.$d[0];
				$cond = array('packageid_fk' => $this->input->post('packageid'), 'hostingid_fk' => $hostingid);
				$data = array('due_date' => $due_date);
				$this->hosting_model->update_row('hosting_package', $data, $cond);
				$this->session->set_flashdata('confirm', array('Package Details Updated!'));
				redirect('hosting/due_date/'.$hostingid);
			}
		}
		$data['pack'] = $this->hosting_model->get_host_hp($hostingid);
		$data['hostingid']=$hostingid;
		$data['packageid']=$packageid;
		$this->load->view('package_due_date',$data);
	}
}
?>