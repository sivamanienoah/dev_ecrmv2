<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales Forecast
 *
 * @class 		sales_forecast
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      -
 * @Menu        Parent - Sales Forecast
 * @author 		eNoah
 * @Controller
 */

class Sales_forecast extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@sales_forecast
	*/
	public function __construct()
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('sales_forecast_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get practice List
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading']   = 'Sales Forecast';
		$data['entity'] 		= $this->sales_forecast_model->get_records('sales_divisions', $wh_condn = array('status'=>1), $order = array("div_id"=>"asc"));
		
		$filter   = real_escape_array($this->input->post());
		
		$data['sales_forecast'] = $this->sales_forecast_model->get_sale_records($filter);
		
		if($this->input->post("filter")!="")
		$this->load->view('sales_forecast/sales_forecast_view_grid', $data);
		else
		$this->load->view('sales_forecast/sales_forecast_view', $data);	
    }
	
	/*
	*@Search manage practice
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('sales_forecast/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('sales_forecast/index/0/' . rawurlencode($name));
        } else {
            redirect('sales_forecast/');
        }
    }

	
	/*
	*@For Add practices
	*@Method practice_add
	*/
	public function add_sale_forecast($update = false, $id = false) {
	
		$this->load->library('validation');
        $data               = array();
		$data['entity'] 	= $this->sales_forecast_model->get_records('sales_divisions', $wh_condn = array('status'=>1), $order = array("div_id"=>"asc"));
        $post_data          = real_escape_array($this->input->post());
		$rules['entity']    = "trim|required";
		$rules['customer_name']  = "trim|required";
		$rules['lead_name']	     = "trim|required";
		$rules['milestone']      = "trim|required";
		$rules['for_month_year'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['entity'] 		  = 'Entity';
		$fields['customer_name']  = 'Customer';
		$fields['lead_name'] 	  = 'Lead/Project';
		$fields['milestone'] 	  = 'Milestone';
		$fields['for_month_year'] = 'For the Month & Year';

		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($post_data['update_pdt']))
        {
            $item_data = $this->db->get_where($this->cfg['dbpref']."sales_forecast", array('forecast_id' => $id));
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
			$update_data['for_month_year'] = date('Y-m-d', strtotime($update_data['for_month_year']));
			$update_data['modified_by']    = $this->userdata['userid'];
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
            {
                //update
                $this->db->where('forecast_id', $id);
				
                if ($this->db->update($this->cfg['dbpref']."sales_forecast", $update_data))
                {	
                    $this->session->set_flashdata('confirm', array('Forecast Details Updated!'));
                }
            }
            else
            {
                //insert
				$update_data['created_by']     = $this->userdata['userid'];
				$update_data['created_on']     = date("Y-m-d H:i:s");
                $this->db->insert($this->cfg['dbpref']."sales_forecast", $update_data);
                $this->session->set_flashdata('confirm', array('New Forecast Added!'));
            }

			redirect('sales_forecast/');
		}
		$this->load->view('sales_forecast/sales_forecast_add_view', $data);
	}

	/*
	*@For Delete Sales forecast
	*@Method delete_sale_forecast
	*/
	public function delete_sale_forecast($update, $id) 
	{	
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete($this->cfg['dbpref']."sales_forecast", array('forecast_id' => $id));
				$this->session->set_flashdata('confirm', array('Sale Forecast Deleted!'));
				redirect('sales_forecast/');
			} else {
				$this->session->set_flashdata('login_errors', array("Error Occured!"));
				redirect('sales_forecast/');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!"));
			redirect('sales_forecast/');
		}
	}
	
	/*
	*@method ajax_customer_search()
	*/
	function ajax_customer_search() {
		$this->load->model('customer_model');
        if ($this->input->post('cust_name')) {
            $result = $this->customer_model->customer_list(0, $this->input->post('cust_name'), '','', 10);
            $i=0;
			$res = array();
            if (count($result) > 0) foreach ($result as $cust) {
				if(!empty($cust)) {
					$res[$i]['id']    = $cust['custid'];
					$res[$i]['label'] = $cust['company'];
				}
		 		$i++;
            }
        }
        echo json_encode($res); exit;
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
			$res = $this->sales_forecast_model->check_duplicate($tbl_cont, $condn, 'practices');
		} else {
			$condn = array('name'=>$post_data['name'], 'id'=>$post_data['id']);
			$res = $this->sales_forecast_model->check_duplicate($tbl_cont, $condn, 'practices');
		}

		if($res == 0)
		echo json_encode('success');
		else
		echo json_encode('fail');
		exit;
	}
}