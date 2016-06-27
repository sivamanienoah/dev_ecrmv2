<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage Practice
 *
 * @class 		manage_practice
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Admin Module
 * @Menu        Practice
 * @author 		eNoah
 * @Controller
 */

class Manage_practice extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@Manage_practice
	*/
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_practice_model');
		$this->load->helper('custom_helper');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get practice List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
		$test=get_practice_max_hour_by_financial_year(15,get_current_financial_year());	
		echo "<pre>";
		print_r($test);
		echo "</pre>";
		
	    $data['page_heading'] = 'Manage Practice';
		$data['practices'] = $this->manage_practice_model->get_practices($search);
        $this->load->view('manage_practice/manage_practice_view', $data);
    }
	
	/*
	*@Search manage practice
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('manage_practice/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('manage_practice/index/0/' . rawurlencode($name));
        } else {
            redirect('manage_practice/');
        }
    }

	
	/*
	*@For Add practices
	*@Method practice_add
	*/
	public function practice_add($update = false, $id = false) {
	
		$this->load->library('validation');
        $data              = array();
        $post_data         = real_escape_array($this->input->post());
		$rules['practices'] = "trim|required";
		$rules['max_hours'] = "required";
		
		$this->validation->set_rules($rules);
		$fields['practices'] = 'Practices';
		$fields['status']   = 'Status';
		$fields['max_hours']   = 'Maximum Hours';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$data['cb_status'] = '';
		$data['practice_max_hours_history']='';
		if(!empty($id)) {
			$this->db->where('practice', $id);
			$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
			$data['practice_max_hours_history']=get_practice_max_hours($id);
		}
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."practices", array('id' => $id));
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
                
                if ($this->db->update($this->cfg['dbpref']."practices", $update_data))
                {	
					//Update history table
					$financial_year=get_current_financial_year();
					$practice_hours_history_data = $this->db->get_where($this->cfg['dbpref']."practice_max_hours_history", array('financial_year' => $financial_year,'practice_id'=>$id))->row();
					
					$update_practice_hours_history = array();
					$update_practice_hours_history['practice_id'] = $id;
					$update_practice_hours_history['practice_max_hours'] = $update_data['max_hours'];
					$update_practice_hours_history['financial_year']   = $financial_year;
						
					if (count($practice_hours_history_data) > 0 && !empty($practice_hours_history_data)) 
					{	
						$this->db->where('id',$practice_hours_history_data->id);
						$this->db->update($this->cfg['dbpref']."practice_max_hours_history", $update_practice_hours_history);
					
					}else{
						$this->db->insert($this->cfg['dbpref']."practice_max_hours_history", $update_practice_hours_history);
					}
					
                    $this->session->set_flashdata('confirm', array('Practice Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."practices", $update_data);
				
				if($this->db->affected_rows() > 0)
				{
					$practice_id = $this->db->insert_id();
					
					if($practice_id>0){
						$practice_hours_history['practice_id'] = $practice_id;
						$practice_hours_history['practice_max_hours'] = $update_data['max_hours'];
						$practice_hours_history['financial_year']   = get_current_financial_year();
						$this->db->insert($this->cfg['dbpref']."practice_max_hours_history", $practice_hours_history);
					}
				}
                $this->session->set_flashdata('confirm', array('New Practice Added!'));
                
            }

			redirect('manage_practice/');
		}
		$this->load->view('manage_practice/manage_practice_add_view', $data);
	}

	/*
	*@For Delete Practice
	*@Method delete_practice
	*/
	public function delete_practice($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."practices", array('id' => $id));
				$this->session->set_flashdata('confirm', array('Practice Deleted!'));
				redirect('manage_practice/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!"));
				redirect('manage_practice/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!"));
			redirect('manage_practice/');
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
		$this->db->where('practice', $leadId);
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

		$tbl_cont['name'] = 'practices';
		$tbl_cont['id']   = 'id';
		if(empty($post_data['id'])) {
			$condn = array('name'=>$post_data['name']);
			$res = $this->manage_practice_model->check_duplicate($tbl_cont, $condn, 'practices');
		} else {
			$condn = array('name'=>$post_data['name'], 'id'=>$post_data['id']);
			$res = $this->manage_practice_model->check_duplicate($tbl_cont, $condn, 'practices');
		}

		if($res == 0)
		echo json_encode('success');
		else
		echo json_encode('fail');
		exit;
	}
}