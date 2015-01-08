<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cost Center
 *
 * @class 		cost_center
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Admin Module
 * @Menu        Cost Center
 * @author 		eNoah - Mani.S
 * @Controller
 */

class Cost_center extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@cost_center
	*/
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('cost_center_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Cost Center List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage Cost Center';
		$data['cost_center'] = $this->cost_center_model->get_cost_center($search);
        $this->load->view('cost_center/cost_center_view', $data);
    }
	
	/*
	*@Search manage Cost Center
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('cost_center/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('cost_center/index/0/' . rawurlencode($name));
        } else {
            redirect('cost_center/');
        }
    }

	
	/*
	*@For Add Cost Center
	*@Method cost_center_add
	*/
	public function cost_center_add($update = false, $id = false) {
	
		$this->load->library('validation');
        $data              = array();
        $post_data         = real_escape_array($this->input->post());
		$rules['cost_center'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['cost_center'] = 'Cost Center';
		$fields['status']   = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$data['cb_status'] = '';
				
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."cost_center", array('id' => $id));
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
                
                if ($this->db->update($this->cfg['dbpref']."cost_center", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Cost Center Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."cost_center", $update_data);
                $this->session->set_flashdata('confirm', array('New Cost Center Added!'));
                
            }

			redirect('cost_center/');
		}
		$this->load->view('cost_center/cost_center_add_view', $data);
	}

	/*
	*@For Delete Cost Center
	*@Method delete_cost_center
	*/
	public function delete_cost_center($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."cost_center", array('id' => $id));
				$this->session->set_flashdata('confirm', array('Cost Center Deleted!'));
				redirect('cost_center/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!"));
				redirect('cost_center/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!"));
			redirect('cost_center/');
		}
	}	
	
	/**
	 * Check Duplicates for Lead source is already exits or not.
	 */
	function chk_duplicate() {
		$post_data = real_escape_array($this->input->post());

		$tbl_cont = array();

		$tbl_cont['name'] = 'cost_center';
		$tbl_cont['id']   = 'id';
		if(empty($post_data['id'])) {
			$condn = array('cost_center'=>$post_data['name']);
			$res = $this->cost_center_model->check_duplicate($tbl_cont, $condn, 'cost_center');
		} else {
			$condn = array('cost_center'=>$post_data['name'], 'id'=>$post_data['id']);
			$res = $this->cost_center_model->check_duplicate($tbl_cont, $condn, 'cost_center');
		}

		if($res == 0)
		echo json_encode('success');
		else
		echo json_encode('fail');
		exit;
	}
	
	/*
	*@For ajax check status
	*@Method ajax_check_status
	*/
	public function ajax_check_status() 
	{
		$post_data  = real_escape_array($this->input->post());
		$leadId     = $post_data['data'];
		$this->db->where('cost_center', $leadId);
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

}