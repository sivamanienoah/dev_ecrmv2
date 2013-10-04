<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manage_service extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct()
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_service_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    function index($limit, $search = FALSE)
    {
        $data['page_heading'] = 'Manage Service / Product';
		
		//$data['job_categories'] = $this->cfg['job_categories'];
		$data['job_categories'] = $this->manage_service_model->get_jobscategory($search);
		
        $this->load->view('manage_service/manage_service_view', $data); 
    }
	
	function search()
	{
        if (isset($_POST['cancel_submit']))
		{
            redirect('manage_service/');
        }
		else if ($name = $this->input->post('cust_search'))
		{
            redirect('manage_service/index/0/' . rawurlencode($name));
        }
		else
		{
            redirect('manage_service/');
        }
        
    }
	
	function search_lead()
	{
        if (isset($_POST['cancel_submit']))
		{
            redirect('manage_service/manage_leadSource');
        }
		else if ($name = $this->input->post('cust_search'))
		{	
            redirect('manage_service/manage_leadSource/0/' . rawurlencode($name));
        }
		else
		{
            redirect('manage_service/manage_leadSource');
        }
    }
	
	function search_sales()
	{
        if (isset($_POST['cancel_submit']))
		{
            redirect('manage_service/manage_sales');
        }
		else if ($name = $this->input->post('cust_search'))
		{	
            redirect('manage_service/manage_sales/0/' . rawurlencode($name));
        }
		else
		{
            redirect('manage_service/manage_sales');
        }
    }
	
	//for sales divisions listing page
	function manage_sales($limit, $search = FALSE) {
		$data['page_heading'] = 'Manage Sales Divisions';
		
		//$data['sales_divisions'] = $this->cfg['sales_divisions'];
		$data['sales_divisions'] = $this->manage_service_model->get_salesDivisions($search);
		
		$this->load->view('manage_service/manage_sales_divisions', $data);
	}
	
	//for lead source listing page
	function manage_leadSource($limit, $search = FALSE) {
		$data['page_heading'] = 'Manage Lead Source';
		
		//$data['sales_divisions'] = $this->cfg['sales_divisions'];
		$data['get_lead_source'] = $this->manage_service_model->get_lead_source($search);
		
		$this->load->view('manage_service/manage_lead_source', $data);
	}
	
	//for Lead Source
	function ls_add($update = false, $id = false) {
		
		$this->load->library('validation');
        $data = array();
        
		$rules['lead_source_name'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['lead_source_name'] = 'Lead Source';
		$fields['status'] = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('lead_source', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_item']))
        {
            $item_data = $this->db->get_where("{$this->cfg['dbpref']}" . 'lead_source', array('lead_source_id' => $id));
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
                $this->db->where('lead_source_id', $id);
                
                if ($this->db->update("{$this->cfg['dbpref']}" . 'lead_source', $update_data))
                {
                    $this->session->set_flashdata('confirm', array('Lead Source Details Updated!'));
                    redirect('manage_service/manage_leadSource');
                }
            }
            else
            {
                //insert
                $this->db->insert("{$this->cfg['dbpref']}" . 'lead_source', $update_data);
                $this->session->set_flashdata('confirm', array('New Lead Source Added!'));
                redirect('manage_service/manage_leadSource');
            }
		}
		$this->load->view('manage_service/manage_lead_source_add', $data);
	}
	function ls_delete($update, $id)
	{
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete("{$this->cfg['dbpref']}lead_source", array('lead_source_id' => $id));
				$this->session->set_flashdata('confirm', array('Lead Source Deleted!'));
				redirect('manage_service/manage_leadSource');
			}
			else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_service/manage_leadSource');
			}
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/manage_leadSource');
		}
	}
	
	//for service requirement
	function ser_add($update = false, $id = false) {
		$this->load->library('validation');
        $data = array();
        
		$rules['category'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['category'] = 'Product';
		$fields['status'] = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('job_category', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."job_categories", array('cid' => $id));
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
                $this->db->where('cid', $id);
                
                if ($this->db->update($this->cfg['dbpref']."job_categories", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Product Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."job_categories", $update_data);
                $this->session->set_flashdata('confirm', array('New Product Added!'));
                
            }
			//write the array
			$get_jobscat = $this->manage_service_model->get_list_active(job_categories);
			//echo "<pre>"; print_r($data['get_jobscat']);
			$filename = APPPATH."config/job_categories.ini";
			$file = fopen($filename, "w");
			fwrite($file, '<?php');
			fwrite($file, "\n");
			fwrite($file, '$config["crm"]["job_categories"] = array(');
			fwrite($file, "\n");
			for($k=0;$k<count($get_jobscat);$k++) {
				fwrite($file, $get_jobscat[$k]['cid']);
				fwrite($file, ' => "');
				fwrite($file, $get_jobscat[$k]['category']);
				fwrite($file, '",');
				fwrite($file, "\n");
			}
			//fwrite($file, print_r($data['get_jobscat'], TRUE));
			fwrite($file, ');');
			fwrite($file, "\n");
			fwrite($file, '?>');
			fclose($file);
			redirect('manage_service/');
		}
		$this->load->view('manage_service/manage_service_req_add', $data);
	}
	function ser_delete($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."job_categories", array('cid' => $id));
				$this->session->set_flashdata('confirm', array('Product Deleted.!'));
				$get_jobscat = $this->manage_service_model->get_list_active(job_categories);
				//echo "<pre>"; print_r($data['get_jobscat']);
				$filename = APPPATH."config/job_categories.ini";
				$file = fopen($filename, "w");
				fwrite($file, '<?php');
				fwrite($file, "\n");
				fwrite($file, '$config["vps"]["job_categories"] = array(');
				fwrite($file, "\n");
				for($k=0;$k<count($get_jobscat);$k++) {
					fwrite($file, $get_jobscat[$k]['cid']);
					fwrite($file, ' => "');
					fwrite($file, $get_jobscat[$k]['category']);
					fwrite($file, '",');
					fwrite($file, "\n");
				}
				//fwrite($file, print_r($data['get_jobscat'], TRUE));
				fwrite($file, ');');
				fwrite($file, "\n");
				fwrite($file, '?>');
				fclose($file);
				redirect('manage_service/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_service/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/');
		}
	}
	
	//for sales divisions
	function division_add($update = false, $id = false) {
		
		$this->load->library('validation');
        $data = array();
        
		$rules['division_name'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['division_name'] = 'Division Name';
		$fields['status'] = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('division', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_dvsn']))
        {
			$item_data = $this->db->get_where($this->cfg['dbpref']."sales_divisions", array('div_id' => $id));
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
                $this->db->where('div_id', $id);
                
                if ($this->db->update($this->cfg['dbpref']."sales_divisions", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Division Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."sales_divisions", $update_data);
                $this->session->set_flashdata('confirm', array('New Division Added!'));
            }
				//write into array
				$get_salesDiv = $this->manage_service_model->get_list_active(sales_divisions);
				$filename = APPPATH."config/sales_divisions.ini";
				$file = fopen($filename, "w");
				fwrite($file, '<?php');
				fwrite($file, "\n");
				fwrite($file, '$config["vps"]["sales_divisions"] = array(');
				fwrite($file, "\n");
				for($k=0;$k<count($get_salesDiv);$k++) {
					fwrite($file, '"');
					fwrite($file, $get_salesDiv[$k]['div_id']);
					fwrite($file, '"');
					fwrite($file, ' => "');
					fwrite($file, $get_salesDiv[$k]['division_name']);
					fwrite($file, '",');
					fwrite($file, "\n");
				}
				//fwrite($file, print_r($data['get_jobscat'], TRUE));
				fwrite($file, ');');
				fwrite($file, "\n");
				fwrite($file, '?>');
				fclose($file);
				redirect('manage_service/manage_sales');
		}
		$this->load->view('manage_service/manage_sales_division_add', $data);
	}
	function division_delete($update, $id) 
	{
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete("sales_divisions", array('div_id' => $id));
				$this->session->set_flashdata('confirm', array('Division Deleted.!'));
				$get_salesDiv = $this->manage_service_model->get_list_active(sales_divisions);
				//echo "<pre>"; print_r($data['get_jobscat']);
				$filename = APPPATH."config/sales_divisions.ini";
					$file = fopen($filename, "w");
					fwrite($file, '<?php');
					fwrite($file, "\n");
					fwrite($file, '$config["vps"]["sales_divisions"] = array(');
					fwrite($file, "\n");
					for($k=0;$k<count($get_salesDiv);$k++) {
						fwrite($file, '"');
						fwrite($file, $get_salesDiv[$k]['div_id']);
						fwrite($file, '"');
						fwrite($file, ' => "');
						fwrite($file, $get_salesDiv[$k]['division_name']);
						fwrite($file, '",');
						fwrite($file, "\n");
					}
					//fwrite($file, print_r($data['get_jobscat'], TRUE));
					fwrite($file, ');');
					fwrite($file, "\n");
					fwrite($file, '?>');
					fclose($file);
					
					redirect('manage_service/manage_sales');
			}
			else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_service/manage_sales');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/manage_sales');
		}
	}
	
	
	//function for checking the status 
	function ajax_check_status() {
		$leadId = $_POST['data'];
		$this->db->where('lead_source', $leadId);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		$res = array();
		if($query == 0) {
			$res['html'] .= "YES";
		} else {
			$res['html'] .= "NO";
		}
		echo json_encode($res);
		exit;
	}
	
	function ajax_check_status_division() {
		$id = $_POST['data'];
		$this->db->where('division', $id);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		$res = array();
		if($query == 0) {
			$res['html'] .= "YES";
		} else {
			$res['html'] .= "NO";
		}
		echo json_encode($res);
		exit;
	}
	
	function ajax_check_status_job_category() {
		$id = $_POST['data'];
		$this->db->where('job_category', $id);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
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