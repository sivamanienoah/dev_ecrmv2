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
	*@For Add SalesForecast
	*@Method add_sale_forecast
	*/
	public function add_sale_forecast($update = false, $id = false)
	{
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
		{
            $data['salesforecast_data'] = $this->sales_forecast_model->get_record('*',"sales_forecast",array('forecast_id' => $id));
			$data['milestone_data']     = $this->sales_forecast_model->get_records("sales_forecast_milestone",array('forecast_id_fk' => $id),array('for_month_year' => 'desc'));
			$sf_category = $this->sales_forecast_model->get_sf_category($id);
			if(!empty($sf_category))
			$data['salesforecast_category'] = $sf_category['forecast_category'];
        }
		$this->load->view('sales_forecast/sales_forecast_add_view', $data);
		
	}
	
	/*
	*@For Add SalesForecast
	*@Method add_sale_forecast
	*/
	public function save_sale_forecast($sf_id = false)
	{

		$data = array();
		
		$data['error'] = false;
		
		$post_data = real_escape_array($this->input->post());
		
		if (!empty($post_data))
        {
			
			$post_data['created_by'] = $this->userdata['userid'];
			$post_data['created_on'] = date("Y-m-d H:i:s");
			
			$forecast_data = array('job_id'=>$post_data['job_id'],'customer_id'=>$post_data['customer_id'],'created_by'=>$this->userdata['userid'],'created_on'=>date("Y-m-d H:i:s"));
			if(isset($sf_id) && is_numeric($sf_id)) {
				unset($forecast_data['created_by']);
				unset($forecast_data['created_on']);
				$forecast_data['modified_by'] = $this->userdata['userid'];
				$sf_updt = $this->sales_forecast_model->update_row('sales_forecast', $wh_condn=array('forecast_id'=>$sf_id), $forecast_data);
				if($sf_updt) {
					$forecast_id = $sf_id;
				} else {
					$data['error'] = true;
					exit;
				}
			} else {
				$forecast_id = $this->sales_forecast_model->insert_row_return_id('sales_forecast', $forecast_data);
			}
			
			if( !empty($forecast_id) && is_numeric($forecast_id) ) {
			
				if(!empty($post_data['exist_ms'])) {
					$get_insert_data = $this->sales_forecast_model->get_milestone_records($post_data['exist_ms']);
					foreach($get_insert_data as $ins) {
						$ms = array('forecast_id_fk'=>$forecast_id, 'forecast_category'=>$post_data['category'], 'milestone_name'=>$ins['project_milestone_name'], 'milestone_value'=>$ins['amount'], 'milestone_ref_no'=>$ins['expectid'], 'for_month_year'=>date("Y-m-d", strtotime($ins['month_year'])),'created_by'=>$this->userdata['userid'], 'created_on'=>date("Y-m-d H:i:s"));
						$this->sales_forecast_model->insert_row("sales_forecast_milestone", $ms);
					}
				}
			
				if($post_data['milestone_name']!="" && $post_data['milestone_value']!="" && $post_data['for_month_year']!="") {
					$milestone_data = array('forecast_id_fk'=>$forecast_id,'forecast_category'=>$post_data['category'],'milestone_name'=>$post_data['milestone_name'],'milestone_value'=>$post_data['milestone_value'],'for_month_year'=>date("Y-m-d", strtotime($post_data['for_month_year'])),'created_by'=>$this->userdata['userid'],'created_on'=>date("Y-m-d H:i:s"));
					$exp_res = $this->sales_forecast_model->insert_row("sales_forecast_milestone", $milestone_data);
				}
				$data['forecast_id'] = $forecast_id;
			}
			if(!$exp_res) {
				$data['error'] = true;
			}	
			echo json_encode($data);
		}
		
	}
	/*
	*@method getCustomerRecords()
	*/
	function getCustomerRecords($type = false, $id = false)
	{
	
		$data     = array();
		$wh_condn = array(); 
		
		$post_data = real_escape_array($this->input->post());
		
		$order     = array('company'=>'asc');
		
		if ($type == 1)
		$wh_condn = array('l.lead_status'=>'1', 'l.pjt_status'=>'0');
		if ($type == 2)
		$wh_condn = array('l.lead_status'=>'4', 'l.pjt_status'=>'1');
		
		$customer_data = $this->sales_forecast_model->get_customers($wh_condn, $order);
		
		$data['customers'] = '<option value="">Select</option>';
		
		if(!empty($customer_data)) {
		
			foreach($customer_data as $cs) {
				$selected = "";
				if( $id != '' && is_numeric($id) && ($cs['custid'] == $id) ) {
					$selected = "selected='selectd'";
				}
				$data['customers'] .= '<option value='.$cs['custid'].' '.$selected.'>'.stripslashes($cs['company']).' - '.stripslashes($cs['first_name']).' '.stripslashes($cs['last_name']).'</option>';
			}
			
		}
		
		echo json_encode($data);
		exit;

	}
	
	/*
	*@method getRecords()
	*/
	function getRecords($job_id)
	{
		$post_data = real_escape_array($this->input->post());
		
		$data = array();
	
		$order = array('lead_title'=>'asc');
		
		if($post_data['category'] == 1)
		$wh_condn = array('custid_fk'=>$post_data['custid'], 'lead_status'=>'1', 'pjt_status'=>'0');
		else if ($post_data['category'] == 2)
		$wh_condn = array('custid_fk'=>$post_data['custid'], 'lead_status'=>'4', 'pjt_status'=>'1');
		
		$get_data = $this->sales_forecast_model->get_records('leads', $wh_condn, $order);

		$data['records'] = '<option value="">Select</option>';
		
		if(!empty($get_data)) {
		
			foreach($get_data as $dt) {
				$selected = '';
				if( $job_id != '' && is_numeric($job_id) && ($dt['lead_id'] == $job_id) ) {
					$selected = "selected='selectd'";
				}
				$data['records'] .= '<option value='.$dt['lead_id'].' '.$selected.'>'.stripslashes($dt['lead_title']).'</option>';
			}
			
		}
		
		echo json_encode($data); 
		exit;
		
	}
	
	
	/*
	*@method getLeadDetail()
	*/
	function getLeadDetail()
	{
		$post_data = real_escape_array($this->input->post());
		
		$current_month_year   = date('d-m-Y'); 
		$data = array();
		$ms_id = array('0');
		
		$get_data = $this->sales_forecast_model->get_lead_detail($post_data['id']);
		
		// echo "<pre>"; print_R($get_data); exit;
		
		if($post_data['category'] == 2) {
			$get_exist_ms = $this->sales_forecast_model->get_exists_ms_records($post_data['sf_id']);
			$get_ms_data = $this->sales_forecast_model->get_ms_records($post_data['id']);
			if(!empty($get_exist_ms)) {
				foreach($get_exist_ms as $ems) {
					$ms_id[] = $ems['milestone_ref_no'];
				}
			}
		}

		if(!empty($get_data)) {
		
			if($post_data['category'] == 1) {
				$res['det'] .= 'Entity - '.$get_data['division_name'] . "<br>";
				$res['det'] .= 'Currency Type - '.$get_data['expect_worth_name'] . "<br>";
				$res['det'] .= 'Estimated Worth - '.$get_data['expect_worth_amount'];
			} else if($post_data['category'] == 2) {
				$res['det'] .= 'Entity - '.$get_data['division_name'] . "<br>";
				$res['det'] .= 'Currency Type - '.$get_data['expect_worth_name'] . "<br>";
				$res['det'] .= 'Estimated Worth - '.$get_data['expect_worth_amount'] . "<br>";
				$res['det'] .= 'Billing Type - '.$get_data['project_billing_type'];
			}
			
		}
		
		if(!empty($get_ms_data) && $post_data['category'] == 2) {
			$res['ms_det'] .= '<table><tr><th>Milestone Name</th><th>Month & Year</th><th>Currency</th><th>Amount</th><th>Action</th></tr>';
			foreach($get_ms_data as $ms) {
				if(!in_array($ms['expectid'], $ms_id)) {
					$milestone_month_year = date('d-m-Y', strtotime($ms['month_year'])); 
					$ms_month_year = ($ms['month_year'] !='0000-00-00 00:00:00') ? date('M-Y', strtotime($ms['month_year'])) : '-';
					$res['ms_det'] .= '<tr>';
					$res['ms_det'] .= '<td>'.$ms['project_milestone_name'].'</td><td>'.$ms_month_year.'</td><td>'.$ms['expect_worth_name'].'</td><td>'.$ms['amount'].'</td><td>';
					if(strtotime($milestone_month_year) > strtotime($current_month_year)) {
						$res['ms_det'] .= '<input type="checkbox" name="exist_ms[]" value='.$ms['expectid'].'>';
					}
					$res['ms_det'] .= '</td></tr>';
				}
			}
			$res['ms_det'] .= '</table>';
			
		}
		
		echo json_encode($res);
		exit;
		
	}

	/*
	*@For Editing Sales forecast
	*@Method edit_sale_forecast
	*/
	public function edit_sale_forecast($id) 
	{	
		$error = false;
		if ($this->session->userdata('edit')==1)
		{
			if (preg_match('/^[0-9]+$/', $id))
			{
				$data['sf_data'] = $this->sales_forecast_model->get_record('*',"sales_forecast_milestone",array('milestone_id' => $id));
			} else {
				$error = true;
			}
		}
		if($error==true){
			return false;
		} else {
			$this->load->view('sales_forecast/sale_forecast_edit_view', $data);
		}
	}

	/*
	*@For Editing Sales forecast
	*@Method edit_sale_forecast
	*/
	public function save_sale_forecast_milestone($id)
	{
		$res = array();
		
		$post_data = real_escape_array($this->input->post());
		
		$ins_data = array('milestone_name'=>$post_data['milestone_name'],'milestone_value'=>$post_data['milestone_value'],'for_month_year'=>date("Y-m-d", strtotime($post_data['for_month_year'])),'modified_by'=>$this->userdata['userid'],'modified_on'=>date("Y-m-d H:i:s"));
		
		$updt = $this->sales_forecast_model->update_row("sales_forecast_milestone", $cond=array("milestone_id"=>$id), $ins_data);
		
		if($updt)
		$res['result'] = 'ok';
		else
		$res['result'] = 'fail';
		
		echo json_encode($res);
		exit;
	}
	
	/*
	*@For Delete Sales forecast
	*@Method delete_sale_forecast
	*/
	public function delete_sale_forecast($update, $milestone_id, $forecast_id) 
	{
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $milestone_id) && preg_match('/^[0-9]+$/', $forecast_id))
			{
				$del_res = $this->db->delete($this->cfg['dbpref']."sales_forecast_milestone", array('milestone_id' => $milestone_id));
				if($del_res) {
					$record_count = $this->sales_forecast_model->get_num_row('sales_forecast_milestone', $cond = array('forecast_id_fk'=>$forecast_id));
					$this->session->set_flashdata('confirm', array('Sale Forecast Milestone Deleted!'));
					if($record_count>0)
					redirect('/sales_forecast/add_sale_forecast/update/'.$forecast_id);
					else
					redirect('/sales_forecast/add_sale_forecast/');
				} else {
					$this->session->set_flashdata('login_errors', array("Error has been an occured!"));
					redirect('sales_forecast/add_sale_forecast/update/'.$forecast_id);
				}
			} else {
				$this->session->set_flashdata('login_errors', array("Error has been an occured!"));
				redirect('sales_forecast/add_sale_forecast/update/'.$forecast_id);
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to delete!"));
			redirect('/sales_forecast/add_sale_forecast/update/'.$forecast_id);
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
					$res[$i]['reg']   = $cust['add1_region'];
					$res[$i]['cty']   = $cust['add1_country'];
					$res[$i]['ste']   = $cust['add1_state'];
					$res[$i]['loc']   = $cust['add1_location'];
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