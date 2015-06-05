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
	public function __construct() 
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_service_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get Service categories List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage Service / Product';
		$data['lead_services'] = $this->manage_service_model->get_jobscategory($search);		
        $this->load->view('manage_service/manage_service_view', $data); 
    }
	
	/*
	*@Search manage service
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
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
		if (isset($_POST['cancel_submit'])) {
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
        if (isset($_POST['cancel_submit'])) {
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
	*@For lead source listing page
	*@Method   manage_leadSource
	*/
	public function manage_leadSource($search = FALSE) {
		$data['page_heading'] = 'Manage Lead Source';
		$data['get_lead_source'] = $this->manage_service_model->get_lead_source($search);		
		$this->load->view('manage_service/manage_lead_source', $data);
	}

	/*
	*@For Expected Worth - Currency Listing Page
	*@Method   manage_expt_worth_cur
	*/
	public function manage_expt_worth_cur($search = FALSE) {
		$data['page_heading'] = 'Manage Currency';		
		$data['getExptWorthCur'] = $this->manage_service_model->get_expect_worth_cur($search);		
		$this->load->view('manage_service/manage_expect_worth_cur', $data);
	}
	
	/*
	*@For updating currencies from live - using API
	*@Method   updt_cur_from_live
	*/
	public function updt_cur_from_live() {
		$this->load->helper('custom_helper');
		currency_convert();
		redirect('manage_service/manage_expt_worth_cur');
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
       
	   $data['cb_status'] = $this->manage_service_model->get_num_row('leads', array('lead_source' => $id));

        $update_item = $this->input->post('update_item');
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
		$rules['services'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['services'] = 'Product';
		$fields['status']   = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('lead_service', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."lead_services", array('sid' => $id));
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
                $this->db->where('sid', $id);
                
                if ($this->db->update($this->cfg['dbpref']."lead_services", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Product Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."lead_services", $update_data);
                $this->session->set_flashdata('confirm', array('New Product Added!'));
                
            }
			

			/* //write the array
			$table_name = $this->cfg['dbpref'].'lead_services'; // table Name 
			$get_jobscat = $this->manage_service_model->get_list_active($table_name);
			//echo "<pre>"; print_r($data['get_jobscat']);
			$filename = APPPATH."config/lead_services.ini";
			$file = fopen($filename, "w");
			fwrite($file, '<?php');
			fwrite($file, "\n");
			fwrite($file, '$config["crm"]["lead_services"] = array(');
			fwrite($file, "\n");
			for($k=0;$k<count($get_jobscat);$k++) {
				fwrite($file, $get_jobscat[$k]['sid']);
				fwrite($file, ' => "');
				fwrite($file, $get_jobscat[$k]['services']);
				fwrite($file, '",');
				fwrite($file, "\n");
			}
			//fwrite($file, print_r($data['get_jobscat'], TRUE));
			fwrite($file, ');');
			fwrite($file, "\n");
			fwrite($file, '?>');
			fclose($file); */

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
				$this->db->delete($this->cfg['dbpref']."lead_services", array('sid' => $id));
				$this->session->set_flashdata('confirm', array('Product Deleted.!'));
				$get_jobscat = $this->manage_service_model->get_list_active($this->cfg['dbpref']."lead_services");
				/* $filename = APPPATH."config/lead_services.ini";
				$file = fopen($filename, "w");
				fwrite($file, '<?php');
				fwrite($file, "\n");
				fwrite($file, '$config["crm"]["lead_services"] = array(');
				fwrite($file, "\n");
				for($k=0;$k<count($get_jobscat);$k++) {
					fwrite($file, $get_jobscat[$k]['sid']);
					fwrite($file, ' => "');
					fwrite($file, $get_jobscat[$k]['services']);
					fwrite($file, '",');
					fwrite($file, "\n");
				}
				//fwrite($file, print_r($data['get_jobscat'], TRUE));
				fwrite($file, ');');
				fwrite($file, "\n");
				fwrite($file, '?>');
				fclose($file); */
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
	*@For sales divisions listing page
	*@Method  manage_sales
	*/
	public function manage_sales($search = FALSE) {
		$data['page_heading'] = 'Manage Entity';
		$data['sales_divisions'] = $this->manage_service_model->get_salesDivisions($search);
		$this->load->view('manage_service/manage_sales_divisions', $data);
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
		$rules['base_currency'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['division_name'] = 'Entity Name';
		$fields['base_currency'] = 'Base Currency';
		$fields['status'] = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		//for status
		$this->db->where('division', $id);
		$data['cb_status']  = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		$data['currencies'] = $this->manage_service_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
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
                    $this->session->set_flashdata('confirm', array('Entity Details Updated!'));
                }
            }
            else
            {
                //insert
                $this->db->insert($this->cfg['dbpref']."sales_divisions", $update_data);
                $this->session->set_flashdata('confirm', array('New Entity Added!'));
            }
				/* //write into array
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
				fclose($file); */
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
				// $this->db->delete("sales_divisions", array('div_id' => $id));
				$this->manage_service_model->delete_row("sales_divisions", array('div_id' => $id));
				$this->session->set_flashdata('confirm', array('Entity Deleted!'));
				$get_salesDiv = $this->manage_service_model->get_list_active($this->cfg['dbpref']."sales_divisions");
				/* $filename = APPPATH."config/sales_divisions.ini";
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
					fclose($file); */
					
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

	/*
	*@For ajax check status (sales divisions)
	*@Method   ajax_check_status_division
	*/
	public function ajax_check_status_division() 
	{
		$post_data  = real_escape_array($this->input->post());
		$id         = $post_data['data'];
		$this->db->where('division', $id);
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

	/*
	*@For ajax check status (lead_service)
	*@Method   ajax_check_status_job_category
	*/
	public function ajax_check_status_job_category() 
	{
		$post_data  = real_escape_array($this->input->post());
		$id         = $post_data['data'];
		$this->db->where('lead_service', $id);
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
	
	/*
	*@For ajax check status (currency)
	*@Method   ajax_check_status_currency
	*/
	public function ajax_check_status_currency() 
	{
		$post_data  = real_escape_array($this->input->post());
		$id = $post_data['data'];
		$this->db->where('expect_worth_id', $id);
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
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		
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
	
	
	/**
	 * Check Duplicates for Lead source is already exits or not.
	 */
	function chk_duplicate() {
		$chk_data = real_escape_array($this->input->post());

		$name = $chk_data['name'];
		$id   = $chk_data['id'];
		$type = $chk_data['type'];
		
		$tbl_cont = array();
		
		switch ($type) {
			case 'lead_source':
				$tbl_cont['name'] = 'lead_source_name';
				$tbl_cont['id'] = 'lead_source_id';
				if(empty($id)) {
					$condn = array('name'=>$name);
					$res = $this->manage_service_model->check_duplicate($tbl_cont, $condn, $type);
				} else {
					$condn = array('name'=>$name, 'id'=>$id);
					$res = $this->manage_service_model->check_duplicate($tbl_cont, $condn, $type);
				}
			break;
			case 'sales_divisions':
				$tbl_cont['name'] = 'division_name';
				$tbl_cont['id'] = 'div_id';
				if(empty($id)) {
					$condn = array('name'=>$name);
					$res = $this->manage_service_model->check_duplicate($tbl_cont, $condn, $type);
				} else {
					$condn = array('name'=>$name, 'id'=>$id);
					$res = $this->manage_service_model->check_duplicate($tbl_cont, $condn, $type);
				}
			break;
			case 'lead_services':
				$tbl_cont['name'] = 'services';
				$tbl_cont['id']   = 'sid';
				if(empty($id)) {
					$condn = array('name'=>$name);
					$res = $this->manage_service_model->check_duplicate($tbl_cont, $condn, $type);
				} else {
					$condn = array('name'=>$name, 'id'=>$id);
					$res = $this->manage_service_model->check_duplicate($tbl_cont, $condn, $type);
				}
			break;
		}

		if($res == 0)
			echo json_encode('success');
		else
			echo json_encode('fail');
		exit;
	}
	
	/*
	*@For updating book keeping currency value
	*@Method   updt_bk_currency
	*/
	public function updt_bk_currency()
	{
		$this->load->helper('custom_helper');
		
		if (get_default_currency()) {
			$get_currency = get_default_currency();
			$default_cur_id   = $get_currency['expect_worth_id'];
			$default_cur_name = $get_currency['expect_worth_name'];
		} else {
			$default_cur_id   = '1';
			$default_cur_name = 'USD';
		}
		
		$data = array();
		$data['currency_rec']     = '';
		$data['page_heading'] 	  = 'Manage Book Keeping Currency Values';
		$data['default_cur_id']   = $default_cur_id;
		$data['default_cur_name'] = $default_cur_name;
		$data['currencies'] 	  = $this->manage_service_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		// $records	     		  = $this->manage_service_model->get_records('book_keeping_currency_rates', '', $order=array('financial_year'=>'asc'));
		$records	     		  = $this->manage_service_model->get_records('book_keeping_currency_rates', '', '');
		// echo "<pre>"; print_r($records); exit;
		if(!empty($records) && count($records)>0) {
			foreach($records as $rec)
			$data['currency_rec'][$rec['financial_year']][$rec['expect_worth_id_to']][$rec['expect_worth_id_from']] = $rec['currency_value'];
		}
		// echo "<pre>"; print_r($data['currency_val']); exit;
		$this->load->view('manage_service/manage_bk_list_view', $data);
	}
	
	/*
	*@For Editing Sales forecast
	*@Method edit_sale_forecast
	*/
	public function get_edit_currency_container($curr_year, $curr_id)
	{
		$error = false;
		if ($this->session->userdata('edit')==1)
		{
			if (preg_match('/^[0-9]+$/', $curr_id))
			{
				$data['curr_data'] = $this->manage_service_model->get_bk_curr_records($wh=array('bk.financial_year' => $curr_year, 'bk.expect_worth_id_to'=>$curr_id, 'ew.status'=>1), $sor=array('bk.expect_worth_id_from'=>'asc'));
				$data['financial_year'] = $curr_year;
				$data['convert_to']     = $this->manage_service_model->get_record('expect_worth_id,expect_worth_name,cur_name','expect_worth',array('expect_worth_id'=>$curr_id));
				$data['currencies'] = $this->manage_service_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			} else {
				$error = true;
			}
		}
		if($error==true){
			return false;
		} else {
			$this->load->view('manage_service/manage_bk_edit_view', $data);
		}
	}
	
	/*
	*@For Editing book keeping currency values
	*@Method save_bk_value
	*/
	public function save_bk_value()
	{
		$res = array();
		$res['result'] = '';
		$res['msg'] = '';
		
		$post_data = real_escape_array($this->input->post());
		
		// echo "<pre>"; print_r($post_data); exit;
		// echo "<pre>"; print_r($post_data['expect_worth_id_from']); exit;
		
		$res['result'] = 'ok';
		
		foreach($post_data['expect_worth_id_from'] as $from_id=>$curr_value){
			/* $updt = $this->manage_service_model->update_row("book_keeping_currency_rates", 
			$cond = array('financial_year'=>$post_data['financial_year'], 'expect_worth_id_from'=>$from_id,'expect_worth_id_to'=>$post_data['expect_worth_id_to']),
			$up_dt= array('currency_value'=>$curr_value, 'modified_by'=>$this->userdata['userid'], 'modified_on'=>date("Y-m-d H:i:s"))
			); */
			$data = array('currency_value'=>$curr_value, 'created_by'=>$this->userdata['userid'], 'created_on'=>date("Y-m-d H:i:s"), 'financial_year'=>$post_data['financial_year'], 'expect_worth_id_from'=>$from_id, 'expect_worth_id_to'=>$post_data['expect_worth_id_to']);
			
			$sql = $this->db->insert_string($this->cfg['dbpref'].'book_keeping_currency_rates', $data) . ' ON DUPLICATE KEY UPDATE financial_year='.$post_data['financial_year'].', expect_worth_id_from='.$from_id.', expect_worth_id_to='.$post_data['expect_worth_id_to'].', currency_value='.$curr_value.', modified_by='.$this->userdata['userid'];
			$updt = $this->db->query($sql);
			// echo $this->db->last_query();
			if(!$updt) {
				$res['result'] = 'fail';
				$res['msg'] = 'Some of the book keeping values could not be updated.';
				break;
			}
		}
		
		echo json_encode($res);
		exit;
	}
	
	/*
	*@Get Form for Adding book keeping currency values
	*@Method add_form_bk_values
	*/
	public function add_form_bk_values()
	{
		$data = array();
		$data['currencies'] = $this->manage_service_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		$this->load->view('manage_service/manage_bk_add_view', $data);
	}

	/*
	*@For checking existing currency values
	*@Method check_exist_currency_value
	*/
	public function check_exist_currency_value()
	{
		$post_data = real_escape_array($this->input->post());
	
		$res = array();
		$res['result']  = 'success';
		$res['message'] = '';
		$bk_data = $this->manage_service_model->get_records('book_keeping_currency_rates', $wh_condn=array('financial_year'=>$post_data['financial_year'],'expect_worth_id_to'=>$post_data['expect_worth_id_to']), $order='');
		if(count($bk_data)>0) {
			$res['result']  = 'failure';
			$res['message'] = 'Value Already Exists for this Currency & Year.';
		} else {
			$currencies = $this->manage_service_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			foreach($currencies as $cr) {
				$res['message'] .= '<tr><td style="padding:0 10px 0"><label>'.$cr["expect_worth_name"].'</label>:</td>';
				if($post_data['expect_worth_id_to']!=$cr['expect_worth_id']) {
					$res['message'] .= '<td><input type="text" name="expect_worth_id_from['.$cr['expect_worth_id'].']" onkeypress="return isNumberKey(event)" value="" class="textfield width100px ip_curr" /></td>';
				} else {
					$res['message'] .= '<td><input type="text" readonly name="expect_worth_id_from['.$cr['expect_worth_id'].']" onkeypress="return isNumberKey(event)" value="1" class="textfield width100px ip_curr" /></td>';
				}
				$res['message'] .= '</tr>';
			}
		}
		echo json_encode($res);
		exit;
	}
	
	/*
	*@For Adding book keeping currency values
	*@Method add_bk_values
	*/
	public function add_bk_values()
	{
		$res = array();
		$res['result'] = '';
		$res['msg'] = '';
		
		$post_data = real_escape_array($this->input->post());
		
		// echo "<pre>"; print_r($post_data); exit;
		
		$res['result'] = 'ok';
		
		foreach($post_data['expect_worth_id_from'] as $from_id=>$curr_value){
			$updt = $this->manage_service_model->insert_return_row("book_keeping_currency_rates", $cond=array('expect_worth_id_from'=>$from_id,'expect_worth_id_to'=>$post_data['expect_worth_id_to'],'financial_year'=>$post_data['financial_year'],'currency_value'=>$curr_value,'created_by'=>$this->userdata['userid'],'created_on'=>date("Y-m-d H:i:s"),'modified_by'=>$this->userdata['userid']));
			if(!$updt) {
				$res['result'] = 'fail';
				$res['msg'] = 'Some of the book keeping values could not be added.';
				break;
			}
		}
		
		echo json_encode($res);
		exit;
	}
	
	public function delete_bk_values($curr_year, $curr_id){
		
		if ($this->session->userdata('delete')==1) {
			$this->manage_service_model->delete_row('book_keeping_currency_rates',array('financial_year'=>$curr_year,'expect_worth_id_to'=>$curr_id));
			$this->session->set_flashdata('confirm', array('Book Keeping Currency Values Deleted.'));
			redirect('manage_service/updt_bk_currency');
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_service/updt_bk_currency');
		}
		
	}
}