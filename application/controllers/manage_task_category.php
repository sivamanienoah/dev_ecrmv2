<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage task category
 *
 * @class 		manage_task_category_model
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Admin Module
 * @Menu        Manage Task category
 * @author 		eNoah
 * @Controller
 */

class Manage_task_category extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@manage_task_category
	*/
	public function __construct() 
	{
		echo "test"; die;
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_task_category_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Task Category List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage Task Category';
		$data['task_category'] = $this->manage_task_category_model->get_task_category($search);
        $this->load->view('manage_task_category/manage_task_category_view', $data);
    }
	
	/*
	*@Search manage task_category
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('manage_task_category/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('manage_task_category/index/0/' . rawurlencode($name));
        } else {
            redirect('manage_task_category/');
        }
    }

	
	/*
	*@For Add task_category
	*@Method task_category_add
	*/
	public function add_task_category($update = false, $id = false) {
	
		$this->load->library('validation');
        $data              = array();
        $post_data         = real_escape_array($this->input->post());
		$rules['task_category'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['task_category'] = 'task_category';
		$fields['status']   = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$data['cb_status'] = '';
		if(!empty($id)) {
			$this->db->where('task_category', $id);
			$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'tasks')->num_rows();
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."task_category", array('id' => $id));
            if ($item_data->num_rows() > 0) $src = $item_data->result_array();
			// echo "<pre>"; print_r($item_data); exit;
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
				$update_data['modified_by'] = $this->userdata['userid'];
                //update
                $this->db->where('id', $id);
                if ($this->db->update($this->cfg['dbpref']."task_category", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Task Category Details Updated!'));
                }
            }
            else
            {
				$update_data['created_on'] = date('Y-m-d H:i:s');
				$update_data['created_by'] = $this->userdata['userid'];
                //insert
                $this->db->insert($this->cfg['dbpref']."task_category", $update_data);
                $this->session->set_flashdata('confirm', array('New Task Category Added!'));
            }
			redirect('manage_task_category/');
		}
		// echo "<pre>"; print_r($data); exit;
		$this->load->view('manage_task_category/manage_task_category_add_view', $data);
	}

	/*
	*@For Delete task_category
	*@Method delete_task_category
	*/
	public function delete_task_category($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."task_category", array('id' => $id));
				$this->session->set_flashdata('confirm', array('Task Category Deleted!'));
				redirect('manage_task_category/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!"));
				redirect('manage_task_category/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!"));
			redirect('manage_task_category/');
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
		$this->db->where('task_category', $leadId);
		$query = $this->db->get($this->cfg['dbpref'].'tasks')->num_rows();
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

		$tbl_cont['name'] = 'task_category';
		$tbl_cont['id']   = 'id';
		if(empty($post_data['id'])) {
			$condn = array('name'=>$post_data['name']);
			$res = $this->manage_task_category_model->check_duplicate($tbl_cont, $condn, 'task_category');
		} else {
			$condn = array('name'=>$post_data['name'], 'id'=>$post_data['id']);
			$res = $this->manage_task_category_model->check_duplicate($tbl_cont, $condn, 'task_category');
		}

		if($res == 0)
		echo json_encode('success');
		else
		echo json_encode('fail');
		exit;
	}
}