<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage service
 *
 * @class 		Manage_service
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      User Module
 * @Menu        Service Catalogue
 * @author 		eNoah
 * @Controller
 */

class Manage_service extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@Manage Service
	*/
	
	public function __construct() {
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_service_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Service categories List
	*@Method index
	*/
	
    public function index($limit, $search = FALSE) {
	
        $data['page_heading'] = 'Manage Service / Product';
		$data['job_categories'] = $this->manage_service_model->get_jobscategory($search);		
        $this->load->view('manage_service/manage_service_view', $data); 
    }
	
	/*
	*@Search manage service
	*@Method index
	*/
	
	public function search(){
		$cancel = $this->input->post('cancel_submit');
        if (!empty($cancel)) {
            redirect('manage_service/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('manage_service/index/0/' . rawurlencode($name));
        } else {
            redirect('manage_service/');
        }
    }

	/*
	*@Search Lead Sources 
	*@Method  search_lead
	*/
	
	public function search_lead() {
	
		$cancel = $this->input->post('cancel_submit');
        if (!empty($cancel)) {
            redirect('manage_service/manage_leadSource');
        } else if ($name = $this->input->post('cust_search')) {	
            redirect('manage_service/manage_leadSource/0/' . rawurlencode($name));
        } else {
            redirect('manage_service/manage_leadSource');
        }
    }

	/*
	*@Search Seles divisions
	*@Method  search_sales
	*/
	
	public function search_sales() {
		$cancel = $this->input->post('cancel_submit');
        if (!empty($cancel)) {
            redirect('manage_service/manage_sales');
        } else if ($name = $this->input->post('cust_search')) {	
            redirect('manage_service/manage_sales/0/' . rawurlencode($name));
        } else {
            redirect('manage_service/manage_sales');
        }
    }

	/*
	*@For search currency
	*@Method  search_sales
	*/
	
	public function search_currency() {
		
	   $cancel = $this->input->post('cancel_submit'); 
	   if (!empty($cancel)) {
            redirect('manage_service/manage_expt_worth_cur');
        } else if ($name = $this->input->post('cust_search')) {	
            redirect('manage_service/manage_expt_worth_cur/0/' . rawurlencode($name));
        } else {
            redirect('manage_service/manage_expt_worth_cur');
        }
    }

	/*
	*@For sales divisions listing page
	*@Method  manage_sales
	*/
	
	public function manage_sales($limit, $search = FALSE) {
		$data['page_heading'] = 'Manage Sales Divisions';
		$data['sales_divisions'] = $this->manage_service_model->get_salesDivisions($search);
		$this->load->view('manage_service/manage_sales_divisions', $data);
	}

	/*
	*@For lead source listing page
	*@Method   manage_leadSource
	*/

	public function manage_leadSource($limit, $search = FALSE) {
		$data['page_heading'] = 'Manage Lead Source';
		$data['get_lead_source'] = $this->manage_service_model->get_lead_source($search);		
		$this->load->view('manage_service/manage_lead_source', $data);
	}

	/*
	*@For Expected Worth - Currency Listing Page
	*@Method   manage_expt_worth_cur
	*/
	
	public function manage_expt_worth_cur($limit, $search = FALSE) {
		$data['page_heading'] = 'Manage Currency';		
		$data['getExptWorthCur'] = $this->manage_service_model->get_expect_worth_cur($search);		
		$this->load->view('manage_service/manage_expect_worth_cur', $data);
	}

	/*
	*@For Add Lead Source
	*@Method   ls_add
	*/
	
	public function ls_add($update = false, $id = false) {		
		$this->load->library('validation');
        $data = array();        
		$rules['lead_source_name'] = "trim|required";		
		$this->validation->set_rules($rules);
		$fields['lead_source_name'] = 'Lead Source';
		$fields['status'] = 'Status';		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        $data['cb_status'] = $this->manage_service_model->get_num_row('lead_source', array('lead_source_id' => $id));
        $update_item = $this->input->post('update_item');
		//if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($update_item)) {	
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {	
            $src = $this->manage_service_model->get_row('lead_source', array('lead_source_id' => $id));
            if (isset($src) && is_array($src) && count($src) > 0) foreach ($src[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }		
		if ($this->validation->run() != false) {
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
            if ($update_data['status'] == "") {
				if ($data['cb_status']==0) {
					$update_data['status'] = 0;
				} else {
					$update_data['status'] = 1;
				}
			}
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
                if ($this->manage_service_model->update_row('lead_source', array('lead_source_id' => $id), $update_data)) {
                    $this->session->set_flashdata('confirm', array('Lead Source Details Updated!'));
                    redirect('manage_service/manage_leadSource');
                }
            } else {
                $this->manage_service_model->insert_row('lead_source',$update_data);
                $this->session->set_flashdata('confirm', array('New Lead Source Added!'));
                redirect('manage_service/manage_leadSource');
            }
		}
		$this->load->view('manage_service/manage_lead_source_add', $data);
	}

	/*
	*@For Delete Lead Source
	*@Method   ls_delete
	*/
	
	public function ls_delete($update, $id) {
		if ($this->session->userdata('delete')==1) {
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
				$this->manage_service_model->delete_row('lead_source',array('lead_source_id' => $id));
				$this->session->set_flashdata('confirm', array('Lead Source Deleted!'));
				redirect('manage_service/manage_leadSource');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_service/manage_leadSource');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/manage_leadSource');
		}
	}

	/*
	*@For Add service requirement
	*@Method   ser_add
	*/
	
	public function ser_add($update = false, $id = false) {
	
		$this->load->library('validation');
        $data              = array();
        $post_data         = real_escape_array($this->input->post());
		$rules['category'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['category'] = 'Product';
		$fields['status']   = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('job_category', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
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
			
			$table_name = $this->cfg['dbpref'].'job_categories'; // table Name 
			$get_jobscat = $this->manage_service_model->get_list_active($table_name);
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

	/*
	*@For Delete service requirement
	*@Method   ser_delete
	*/
	
	public function ser_delete($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."job_categories", array('cid' => $id));
				$this->session->set_flashdata('confirm', array('Product Deleted.!'));
				$get_jobscat = $this->manage_service_model->get_list_active($this->cfg['dbpref']."job_categories");
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
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_service/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/');
		}
	}

	/*
	*@For add sales divisions
	*@Method   division_add
	*/
	
	public function division_add($update = false, $id = false) 
	{
		
		$this->load->library('validation');
        $data         = array();
        $post_data    = real_escape_array($this->input->post());
		$rules['division_name'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['division_name'] = 'Division Name';
		$fields['status'] = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('division', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_dvsn']))
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
				fwrite($file, '$config["crm"]["sales_divisions"] = array(');
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
	
	/*
	*@For delete sales divisions
	*@Method   division_add
	*/

	public function division_delete($update, $id) 
	{
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete("sales_divisions", array('div_id' => $id));
				$this->session->set_flashdata('confirm', array('Division Deleted.!'));
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
			else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_service/manage_sales');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/manage_sales');
		}
	}
	
	/*
	*@For ajax check status
	*@Method   ajax_check_status
	*/
	
	public function ajax_check_status() 
	{
		$post_data  = real_escape_array($this->input->post());
		$leadId     = $post_data['data'];
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

	/*
	*@For ajax check status (sales divisions)
	*@Method   ajax_check_status_division
	*/
	
	public function ajax_check_status_division() 
	{
		$post_data  = real_escape_array($this->input->post());
		$id         = $post_data['data'];
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

	/*
	*@For ajax check status (job category)
	*@Method   ajax_check_status_job_category
	*/
	
	public function ajax_check_status_job_category() 
	{
		$post_data  = real_escape_array($this->input->post());
		$id         = $post_data['data'];
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
	
	/*
	*@For ajax check status (currency)
	*@Method   ajax_check_status_currency
	*/
	
	public function ajax_check_status_currency() 
	{
		$post_data  = real_escape_array($this->input->post());
		$id = $post_data['data'];
		$this->db->where('expect_worth_id', $id);
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

	/*
	*@For currency adding
	*@Method   expect_worth_cur_add
	*/
	
	public function expect_worth_cur_add($update = false, $id = false) 
	{
		$this->load->library('validation');
        $data                   = array();
		$post_data              = real_escape_array($this->input->post());
        $data['getAllCurrency'] = $this->manage_service_model->get_all_currency();
		$rules['country_name']  = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['country_name'] = 'Country Name';
		$fields['cur_name']     = 'Currency Name';
		$fields['status']       = 'Status';
		$fields['is_default']   = 'Default Currency';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		if ($this->validation->run() != false)
        {
			// all good
            foreach($fields as $key => $val)
            {
                $update_data[$key] = $this->input->post($key);
            }
            //check before insert.
			$this->db->where('expect_worth_name', $post_data['cur_short_name']);
			$query = $this->db->get($this->cfg['dbpref'].'expect_worth')->num_rows();
			if($query == 0) {
				//insert
				$ins = array();
				$ins['expect_worth_name'] = $post_data['cur_short_name'];
				$ins['cur_name'] = $post_data['cur_name'];
				if (!empty($post_data['status'])) {
					$ins['status'] = $post_data['status'];
				}
				if (!empty($post_data['is_default'])) {
					$ins['is_default'] = $post_data['is_default'];
				}
				$insert_currency = $this->manage_service_model->insert_new_currency($ins);
				$this->session->set_flashdata('confirm', array('New Currency Type Added!'));
			} else {
				$this->session->set_flashdata('login_errors', array('Currency Type Already Exists!'));
				redirect('manage_service/expect_worth_cur_add');
			}
			redirect('manage_service/manage_expt_worth_cur');
		}
		$this->load->view('manage_service/manage_expect_worth_cur_add', $data);
	}
	
	
	/*
	*@For currency type edit
	*@Method   expect_worth_cur_edit
	*/
	
	public function expect_worth_cur_edit($update = false, $id = false) 
	{
				
		$this->load->library('validation');
        $data = array();
        
		$rules['expect_worth_name'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['expect_worth_name'] = 'Currency Type';
		$fields['status'] = 'Status';
		$fields['is_default'] = 'Default Currency';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('expect_worth_id', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_item']))
        {
            $item_data = $this->db->get_where("{$this->cfg['dbpref']}" . 'expect_worth', array('expect_worth_id' => $id));
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
			if ($update_data['is_default'] == 1) {
				$update_data['status'] = 1;
			}
			if ($update_data['is_default']=='') {
				unset($update_data['is_default']);
			}
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
            {
                //update
				$updt_cur = $this->manage_service_model->updt_exist_currency($update_data, $id);
				if ($updt_cur)
                {
                    $this->session->set_flashdata('confirm', array('Currency Type Updated!'));
                    redirect('manage_service/manage_expt_worth_cur');
                }
            }
		}
		$this->load->view('manage_service/manage_expect_worth_cur_edit', $data);
		
	}
	
	/*
	*@For currency type - delete
	*@Method   cur_type_delete
	*/
	
	public function cur_type_delete($update, $id)
	{
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete("{$this->cfg['dbpref']}expect_worth", array('expect_worth_id' => $id));
				$this->db->delete("{$this->cfg['dbpref']}currency_rate", array('from' => $id));
				$this->session->set_flashdata('confirm', array('Currency Type Deleted!'));
				redirect('manage_service/manage_expt_worth_cur');
			}
			else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_service/manage_expt_worth_cur');
			}
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/manage_expt_worth_cur');
		}
	}
	
	/*
	*@Get Currenecy Name
	*@Method   get_cur_name
	*/
	
	public function get_cur_name() 
	{
		$post_data              = real_escape_array($this->input->post());
		$result 				= $this->manage_service_model->getCurName($post_data['data']);
		echo json_encode($result);
		exit;
	}
	
}