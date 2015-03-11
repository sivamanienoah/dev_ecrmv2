<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage_project_billing_type
 *
 * @class 		Manage_project_billing_type
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Admin Module
 * @Menu        Practice
 * @author 		eNoah
 * @Controller
 */

class Manage_project_billing_type extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@Manage_project_billing_type
	*/
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_project_billing_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get project_billing_type List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage Project Billing Type';
		$data['billing_type'] = $this->manage_project_billing_model->get_project_billing_type($search);
        $this->load->view('manage_project_billing_type/manage_project_billing_view', $data);
    }
	
	/*
	*@Search manage practice
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('manage_project_billing_type/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('manage_project_billing_type/index/0/' . rawurlencode($name));
        } else {
            redirect('manage_project_billing_type/');
        }
    }

	
	/*
	*@For Add project_billing_type
	*@Method add_project_billing_type
	*/
	public function add_project_billing_type($update = false, $id = false) {
	
		$this->load->library('validation');
        $data               = array();
        $post_data          = real_escape_array($this->input->post());
		$rules['project_billing_type'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['project_billing_type'] = 'Project Billing Type';
		$fields['status']   		    = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$data['cb_status'] = '';
		if(!empty($id)) {
			$this->db->where('project_type', $id);
			$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."project_billing_type", array('id' => $id));
            if ($item_data->num_rows() > 0) $src = $item_data->result_array();
            if (isset($src) && is_array($src) && count($src) > 0) foreach ($src[0] as $k => $v)
            {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() != false)
        {
			// all good
            foreach($fields as $key => $val)
            {
                $update_data[$key] = $this->input->post($key);
            }
			if ($update_data['status'] == "") {
				if ($data['cb_status']==0) {
					$update_data['status'] = 0;
				} else {
					$update_data['status'] = 1;
				}
			}
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
            {
                //update
                $this->db->where('id', $id);
                
                if ($this->db->update($this->cfg['dbpref']."project_billing_type", $update_data))
                {	
					$update_data['id'] = $id;
					$updt_timesheet  = $this->manage_project_billing_model->updt_timesheet_db($update_data);
					if(!$updt_timesheet) {
						$this->session->set_flashdata('login_errors', array('Timesheet cannot be updated!'));
					}
                    $this->session->set_flashdata('confirm', array('Details Updated!'));
                }
            }
            else
            {
                //insert
				$new_id = $this->manage_project_billing_model->insert_row('project_billing_type', $update_data);
				if(!empty($new_id)) {
					$update_data['id'] = $new_id;
					$insert_timesheet  = $this->manage_project_billing_model->insert_timesheet_db($update_data);
					if(!$insert_timesheet) {
						$this->session->set_flashdata('login_errors', array('Cannot Inserted in Timesheet DB!.'));
					}
				} else {
					$this->session->set_flashdata('login_errors', array('Error occured!.'));
				}
                $this->session->set_flashdata('confirm', array('New Type Added!'));
            }
			redirect('manage_project_billing_type/');
		}
		$this->load->view('manage_project_billing_type/manage_project_billing_add_view', $data);
	}

	/*
	*@For Delete Project Billing Type
	*@Method delete_project_billing_type
	*/
	public function delete_project_billing_type($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."project_billing_type", array('id' => $id));
				$this->session->set_flashdata('confirm', array('Project Billing Type Deleted!'));
				redirect('manage_project_billing_type/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!"));
				redirect('manage_project_billing_type/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!"));
			redirect('manage_project_billing_type/');
		}
	}
	
	
	/*
	*@For ajax check status
	*@Method ajax_check_status
	*/
	public function ajax_check_status() 
	{
		$post_data  = real_escape_array($this->input->post());
		$leadId     = $post_data['data'];
		$this->db->where('project_type', $leadId);
		$query = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		$res = array();
		if($query == 0) {
			$res['html'] .= "YES";
		} else {
			$res['html'] .= "NO";
		}
		echo json_encode($res);
		exit;
	}

	/**
	 * Check Duplicates for Lead source is already exits or not.
	 */
	function chk_duplicate() {
		$post_data = real_escape_array($this->input->post());

		$tbl_cont = array();

		$tbl_cont['name'] = 'project_billing_type';
		$tbl_cont['id']   = 'id';
		if(empty($post_data['id'])) {
			$condn = array('name'=>$post_data['name']);
			$res = $this->manage_project_billing_model->check_duplicate($tbl_cont, $condn, 'project_billing_type');
		} else {
			$condn = array('name'=>$post_data['name'], 'id'=>$post_data['id']);
			$res = $this->manage_project_billing_model->check_duplicate($tbl_cont, $condn, 'project_billing_type');
		}

		if($res == 0)
		echo json_encode('success');
		else
		echo json_encode('fail');
		exit;
	}
}