<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage Practice
 *
 * @class 		project_types
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Admin Module
 * @Menu        Project Type
 * @author 		eNoah - Mani.S
 * @Controller
 */

class Project_types extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@project_types
	*/
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('project_types_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Project Types List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage Project Type';
		$data['project_types'] = $this->project_types_model->get_project_types($search);
        $this->load->view('project_types/project_types_view', $data);
    }
	
	/*
	*@Search manage Project Types
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('project_types/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('project_types/index/0/' . rawurlencode($name));
        } else {
            redirect('project_types/');
        }
    }

	
	/*
	*@For Add Project Types
	*@Method project_type_add
	*/
	public function project_type_add($update = false, $id = false) {
	
		$this->load->library('validation');
        $data              = array();
        $post_data         = real_escape_array($this->input->post());
		$rules['project_types'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['project_types'] = 'Project Type';
		$fields['status']   = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$data['cb_status'] = '';
		if(!empty($id)) {
			$this->db->where('project_types', $id);
			$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."project_types", array('id' => $id));
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
                
                if ($this->db->update($this->cfg['dbpref']."project_types", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Project Type Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."project_types", $update_data);
                $this->session->set_flashdata('confirm', array('New Project Type Added!'));
                
            }

			redirect('project_types/');
		}
		$this->load->view('project_types/project_type_add_view', $data);
	}

	/*
	*@For Delete Project Type
	*@Method delete_project_type
	*/
	public function delete_project_type($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."project_types", array('id' => $id));
				$this->session->set_flashdata('confirm', array('Project Type Deleted!'));
				redirect('project_types/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!"));
				redirect('project_types/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!"));
			redirect('project_types/');
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
		$this->db->where('project_types', $leadId);
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

		$tbl_cont['name'] = 'project_types';
		$tbl_cont['id']   = 'id';
		if(empty($post_data['id'])) {
			$condn = array('project_types'=>$post_data['name']);
			$res = $this->project_types_model->check_duplicate($tbl_cont, $condn, 'project_types');
		} else {
			$condn = array('project_types'=>$post_data['name'], 'id'=>$post_data['id']);
			$res = $this->project_types_model->check_duplicate($tbl_cont, $condn, 'project_types');
		}

		if($res == 0)
		echo json_encode('success');
		else
		echo json_encode('fail');
		exit;
	}
}