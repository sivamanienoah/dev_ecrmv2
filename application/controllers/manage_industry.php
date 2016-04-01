<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage industry
 *
 * @class 		manage_industry
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Admin Module
 * @Menu        Manage Industry
 * @author 		eNoah
 * @Controller
 */

class Manage_industry extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@manage_industry
	*/
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_industry_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get industry List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage industry';
		$data['industry'] = $this->manage_industry_model->get_industry($search);
        $this->load->view('manage_industry/manage_industry_view', $data);
    }
	
	/*
	*@Search manage industry
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('manage_industry/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('manage_industry/index/0/' . rawurlencode($name));
        } else {
            redirect('manage_industry/');
        }
    }

	
	/*
	*@For Add industry
	*@Method industry_add
	*/
	public function industry_add($update = false, $id = false) {
	
		$this->load->library('validation');
        $data              = array();
        $post_data         = real_escape_array($this->input->post());
		$rules['industry'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['industry'] = 'industry';
		$fields['status']   = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$data['cb_status'] = '';
		if(!empty($id)) {
			$this->db->where('industry', $id);
			$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."industry", array('id' => $id));
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
                //update
                $this->db->where('id', $id);
                
                if ($this->db->update($this->cfg['dbpref']."industry", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Industry Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."industry", $update_data);
                $this->session->set_flashdata('confirm', array('New Industry Added!'));
                
            }

			redirect('manage_industry/');
		}
		// echo "<pre>"; print_r($data); exit;
		$this->load->view('manage_industry/manage_industry_add_view', $data);
	}

	/*
	*@For Delete industry
	*@Method delete_industry
	*/
	public function delete_industry($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."industry", array('id' => $id));
				$this->session->set_flashdata('confirm', array('Industry Deleted!'));
				redirect('manage_industry/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!"));
				redirect('manage_industry/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!"));
			redirect('manage_industry/');
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
		$this->db->where('industry', $leadId);
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

		$tbl_cont['name'] = 'industry';
		$tbl_cont['id']   = 'id';
		if(empty($post_data['id'])) {
			$condn = array('name'=>$post_data['name']);
			$res = $this->manage_industry_model->check_duplicate($tbl_cont, $condn, 'industry');
		} else {
			$condn = array('name'=>$post_data['name'], 'id'=>$post_data['id']);
			$res = $this->manage_industry_model->check_duplicate($tbl_cont, $condn, 'industry');
		}

		if($res == 0)
		echo json_encode('success');
		else
		echo json_encode('fail');
		exit;
	}
}