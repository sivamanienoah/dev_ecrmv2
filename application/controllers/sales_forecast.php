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
    public function index()
	{
        $data['page_heading']   = 'Sales Forecast';
		
		$data['entity'] 		= $this->sales_forecast_model->get_records('sales_divisions', $wh_condn = array('status'=>1), $order = array("div_id"=>"asc"));
		$this->load->model('customer_model');
		$data['customers']      = $this->customer_model->customer_list();
		$data['leads_data']     = $this->sales_forecast_model->get_records('leads', $wh_condn=array('lead_status'=>1,'pjt_status'=>0), $order = array("lead_id"=>"asc"));
		$data['projects_data']  = $this->sales_forecast_model->get_records('leads', $wh_condn=array('lead_status'=>4,'pjt_status'=>1), $order = array("lead_id"=>"asc"));
		
		$filter   = real_escape_array($this->input->post());
		
		$data['sales_forecast'] = $this->sales_forecast_model->get_sf_milestone_records($filter);

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
	
			if(!empty($sf_category)) {
				if(($sf_category['lead_status']==4) && ($sf_category['pjt_status']==1 || $sf_category['pjt_status']==2 || $sf_category['pjt_status']==3 || $sf_category['pjt_status']==4)) {
					$data['salesforecast_category'] = 2;
				} else if (($sf_category['lead_status']==1) && ($sf_category['pjt_status']==0)) {
					$data['salesforecast_category'] = 1;
				}
				$data['salesforecast_currency'] = $sf_category['expect_worth_name'];
			}
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
				/* unset($forecast_data['created_by']);
				unset($forecast_data['created_on']);
				$forecast_data['modified_by'] = $this->userdata['userid'];
				$sf_updt = $this->sales_forecast_model->update_row('sales_forecast', $wh_condn=array('forecast_id'=>$sf_id), $forecast_data);
				if($sf_updt) {
					$forecast_id = $sf_id;
				} else {
					$data['error'] = true;
					exit;
				} */
				$forecast_id = $sf_id;
			} else {
				$forecast_id = $this->sales_forecast_model->insert_row_return_id('sales_forecast', $forecast_data);
			}
			
			if( !empty($forecast_id) && is_numeric($forecast_id) ) {
			
				if(!empty($post_data['exist_ms'])) {
					$get_insert_data = $this->sales_forecast_model->get_milestone_records($post_data['exist_ms']);
					foreach($get_insert_data as $ins) {
						$ms = array('forecast_id_fk'=>$forecast_id, 'forecast_category'=>$post_data['category'], 'milestone_name'=>$ins['project_milestone_name'], 'milestone_value'=>$ins['amount'], 'milestone_ref_no'=>$ins['expectid'], 'for_month_year'=>date("Y-m-d", strtotime($ins['month_year'])),'created_by'=>$this->userdata['userid'], 'created_on'=>date("Y-m-d H:i:s"));
						$this->sales_forecast_model->insert_row("sales_forecast_milestone", $ms);
						$ms['milestone_id'] = $this->db->insert_id();
						$ms['modified_by']  = $this->userdata['userid'];
						$ms['modified_on']  = date("Y-m-d H:i:s");
						unset($ms['milestone_ref_no']);
						unset($ms['created_by']);
						unset($ms['created_on']);
						unset($ms['forecast_category']);
						$this->sales_forecast_model->insert_row("sales_forecast_milestone_audit_log", $ms);
					}
				}
			
				if($post_data['milestone_name']!="" && $post_data['milestone_value']!="" && $post_data['for_month_year']!="") {
					$milestone_data = array('forecast_id_fk'=>$forecast_id,'forecast_category'=>$post_data['category'],'milestone_name'=>$post_data['milestone_name'],'milestone_value'=>$post_data['milestone_value'],'for_month_year'=>date("Y-m-d", strtotime($post_data['for_month_year'])),'created_by'=>$this->userdata['userid'],'created_on'=>date("Y-m-d H:i:s"));
					$exp_res = $this->sales_forecast_model->insert_row("sales_forecast_milestone", $milestone_data);
					$milestone_data['milestone_id'] = $this->db->insert_id();
					$milestone_data['modified_by']  = $this->userdata['userid'];
					$milestone_data['modified_on']  = date("Y-m-d H:i:s");
					unset($milestone_data['forecast_category']);
					unset($milestone_data['created_by']);
					unset($milestone_data['created_on']);
					$this->sales_forecast_model->insert_row("sales_forecast_milestone_audit_log", $milestone_data);
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
	*@method moveMilestone()
	*@parameter - payment milestone id
	*/
	function moveMilestone()
	{
		$res = array();
		
		if($this->input->post('forecast_id')=='no_id') {
			$forecast_data = array('job_id'=>$this->input->post('job_id'),'customer_id'=>$this->input->post('customer_id'),'created_by'=>$this->userdata['userid'],'created_on'=>date("Y-m-d H:i:s"));
			$forecast_id = $this->sales_forecast_model->insert_row_return_id('sales_forecast', $forecast_data);
		} else {
			$forecast_id = $this->input->post('forecast_id');
		}
		
		$ms_data = $this->sales_forecast_model->get_milestone_records($this->input->post('payment_milestone_id'));

		$ms = array('forecast_id_fk'=>$forecast_id, 
					'forecast_category'=>2, 
					'milestone_name'=>$ms_data[0]['project_milestone_name'], 
					'milestone_value'=>$ms_data[0]['amount'], 
					'milestone_ref_no'=>$ms_data[0]['expectid'], 
					'for_month_year'=>date("Y-m-d", strtotime($ms_data[0]['month_year'])),
					'created_by'=>$this->userdata['userid'], 
					'created_on'=>date("Y-m-d H:i:s"));
					
		$sf_ms_ins = $this->sales_forecast_model->insert_row("sales_forecast_milestone", $ms);
		
		$ms['milestone_id'] = $this->db->insert_id();
		$ms['modified_by']  = $this->userdata['userid'];
		$ms['modified_on']  = date("Y-m-d H:i:s");
		unset($ms['milestone_ref_no']);
		unset($ms['created_by']);
		unset($ms['created_on']);
		unset($ms['forecast_category']);
		
		$this->sales_forecast_model->insert_row("sales_forecast_milestone_audit_log", $ms);
		
		if($sf_ms_ins){
			$res['result']      = true;
			$res['forecast_id'] = $forecast_id;
		} else {
			$res['result'] = false;
		}
		
		echo json_encode($res);
		exit;
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
		
		/* if ($type == 1)
		$wh_condn = array('l.lead_status'=>'1', 'l.pjt_status'=>'0');
		if ($type == 2)
		$wh_condn = array('l.lead_status'=>'4', 'l.pjt_status'=>'1'); */
		
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
	*check_exist_sf_info
	*/
	function check_exist_sf_info()
	{
		$post_data 			= real_escape_array($this->input->post());
		
		$res 				= array();
		$res['redirect']	= false;
		
		//check whether adding milestones for existing leads or projects. If exists redirect to sale forecast edit page
		$check_data 		= $this->sales_forecast_model->get_record('forecast_id','sales_forecast', $cond = array('job_id'=>$post_data['id']));
		
		if(!empty($check_data['forecast_id'])) {
			$res['redirect']    = true;
			$res['forecast_id'] = $check_data['forecast_id'];
		}
		
		echo json_encode($res);
		exit;
	}
	
	
	/*
	*@method getLeadDetail()
	*/
	function getLeadDetail()
	{
		$post_data 			= real_escape_array($this->input->post());
		
		$current_month_year = date('d-m-Y'); 
		$res 				= array();
		$ms_id 				= array('0');
		
		$get_data           = $this->sales_forecast_model->get_lead_detail($post_data['id']);
		
		// echo "<pre>"; print_R($get_data); exit;
		
		if($post_data['category'] == 2) {
			$get_exist_ms = $this->sales_forecast_model->get_exists_ms_records($post_data['sf_id']);
			$get_ms_data  = $this->sales_forecast_model->get_ms_records($post_data['id']);
			if(!empty($get_exist_ms)) {
				foreach($get_exist_ms as $ems) {
					$ms_id[] = $ems['milestone_ref_no'];
				}
			}
		}

		if(!empty($get_data)) {
		
			$res['entity']         .= $get_data['division_name'];
			$res['currency_type']  .= $get_data['expect_worth_name'];
			$res['expected_worth'] .= $get_data['expect_worth_amount'];
			$res['billing_type']   .= isset($get_data['project_billing_type']) ? $get_data['project_billing_type'] : '-';
		
			/* if($post_data['category'] == 1) {
				$res['det'] .= 'Entity - '.$get_data['division_name'] . "<br>";
				$res['det'] .= 'Currency Type - '.$get_data['expect_worth_name'] . "<br>";
				$res['det'] .= 'Estimated Worth - '.$get_data['expect_worth_amount'];
			} else if($post_data['category'] == 2) {
				$res['det'] .= 'Entity - '.$get_data['division_name'] . "<br>";
				$res['det'] .= 'Currency Type - '.$get_data['expect_worth_name'] . "<br>";
				$res['det'] .= 'Estimated Worth - '.$get_data['expect_worth_amount'] . "<br>";
				$res['det'] .= 'Billing Type - '.$get_data['project_billing_type'];
			} */
			
		}
		$row = false;
		
		if(!empty($get_ms_data) && $post_data['category'] == 2) {
			$res['ms_det'] .= '<div class="table-design"><table class="class_ms_det data-tbl dashboard-heads dataTable" cellpadding="0" cellspacing="0"><tr><th>Milestone Name</th><th>Month & Year</th><th>Currency</th><th>Amount</th><th>Action</th></tr>';
			foreach($get_ms_data as $ms) {
				if(!in_array($ms['expectid'], $ms_id)) {
					$milestone_month_year = date('d-m-Y', strtotime($ms['month_year'])); 
					$ms_month_year = ($ms['month_year'] !='0000-00-00 00:00:00') ? date('M-Y', strtotime($ms['month_year'])) : '-';
					$res['ms_det'] .= '<tr>';
					$res['ms_det'] .= '<td>'.$ms['project_milestone_name'].'</td><td>'.$ms_month_year.'</td><td>'.$ms['expect_worth_name'].'</td><td>'.$ms['amount'].'</td><td>';
					if(strtotime($milestone_month_year) > strtotime($current_month_year)) {
						//$res['ms_det'] .= '<input type="checkbox" name="exist_ms[]" value='.$ms['expectid'].'>';
						$res['ms_det'] .= '<a onclick="moveMilestone('.$ms['expectid'].'); return false;" title="Move to Forecast">';
						$res['ms_det'] .= '<img alt="Move" src="assets/img/arrow-move.png">';
						$res['ms_det'] .= '</a>';
					}
					$res['ms_det'] .= '</td></tr>';
					$row = true;
				}
			}
			if($row == false){
				$res['ms_det'] .= '<tr><td colspan=5>No records availble</td></tr>';
			}
			$res['ms_det'] .= '</table></div>';
		} else {
			$res['ms_det'] .= 'No records availble';
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
	public function save_sale_forecast_milestone($id,$fc_id)
	{
		$res = array();
		
		$post_data = real_escape_array($this->input->post());
		
		$ins_data = array('milestone_name'=>$post_data['milestone_name'],'milestone_value'=>$post_data['milestone_value'],'for_month_year'=>date("Y-m-d", strtotime($post_data['for_month_year'])),'modified_by'=>$this->userdata['userid'],'modified_on'=>date("Y-m-d H:i:s"));
		
		// $updt = $this->sales_forecast_model->update_row_return_affected_rows("sales_forecast_milestone", $cond=array("milestone_id"=>$id), $ins_data);
		$updt = $this->sales_forecast_model->update_row_return_affected_rows("sales_forecast_milestone", $id, $ins_data);
		
		if($updt == 1) {
			$res['result'] = 'ok';
			$updt = $this->sales_forecast_model->update_row("sales_forecast_milestone", $cond=array("milestone_id"=>$id), $ins=array('modified_on'=>date("Y-m-d H:i:s")));
			$ins_data['milestone_id'] = $id;
			$ins_data['forecast_id_fk'] = $fc_id;
			$this->sales_forecast_model->insert_row("sales_forecast_milestone_audit_log", $ins_data);
		} else {
			$res['result'] = 'fail';
		}
		
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
					if($record_count>0) {
						redirect('/sales_forecast/add_sale_forecast/update/'.$forecast_id);
					} else {
						$rs = $this->db->delete($this->cfg['dbpref']."sales_forecast", array('forecast_id' => $forecast_id));
						if( $rs ) redirect('/sales_forecast/add_sale_forecast/');
					}
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
	*@For Get the logs for Sales forecast milestones
	*@Method edit_sale_forecast
	*/
	public function get_logs($id) 
	{	
		$error = false;

		if (preg_match('/^[0-9]+$/', $id))
		{
			$data['log_data'] = $this->sales_forecast_model->get_ms_logs($id);
		} else {
			$error = true;
		}
		
		if($error==true) {
			return false;
		} else {
			$this->load->view('sales_forecast/sale_forecast_log_view', $data);
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
	 *Reports
	 */
	public function reports()
	{
		$this->load->helper('custom_helper');
		
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$rates = $this->get_currency_rates();
		
		$data['page_heading'] = 'Sales Forecast Reports';
		
		$data['default_currency'] = $this->default_cur_name;
		
		$data['entity']      = $this->sales_forecast_model->get_records('sales_divisions', $wh_condn = array('status'=>1), $order = array("div_id"=>"asc"));
		$data['customers']   = $this->sales_forecast_model->get_sf_records('customers');
		$data['leads']       = $this->sales_forecast_model->get_sf_records('jobs');
		$curFiscalYear       = $this->sales_forecast_model->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$data['month_array'] = array(04=>'Apr',05=>'May',06=>'Jun',07=>'Jul',08=>'Aug',09=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec',01=>'Jan',02=>'Feb',03=>'Mar');
		
		$filter   			 = real_escape_array($this->input->post());
		
		// echo "<pre>"; print_R($rates); exit;
		
		$sf_data 		     = $this->sales_forecast_model->get_sf_milestone_records($filter);
		
		// echo "<pre>"; print_R($sf_data); exit;
		
		/*Month - Milestone Name|Milestone Value(Milestones Clubbed)-Start*/
		/* $highest_month = date('Y-m-d');
		foreach($sf_data as $sf) {
			$month = date('M', strtotime($sf['for_month_year']));
			$highest_month = ($highest_month > date('Y-m-d', strtotime($sf['for_month_year']))) ? $highest_month : date('Y-m-d', strtotime($sf['for_month_year']));
			$data['report_data'][$sf['forecast_id']]['customer']  = $sf['company'].' - '.$sf['first_name'].' '.$sf['last_name'];
			$data['report_data'][$sf['forecast_id']]['lead_name'] = $sf['lead_title'];
			$data['report_data'][$sf['forecast_id']]['type'] = ($sf['forecast_category']==1)?'Lead':'Project';
			$data['report_data'][$sf['forecast_id']]['milestones'][$month]['ms_name'] = $sf['milestone_name'];
			$data['report_data'][$sf['forecast_id']]['milestones'][$month]['ms_value'] = $sf['milestone_value'];
		} 
		$data['highest_month'] = $highest_month;
		*/
		/*Month - Milestone Name|Milestone Value(Milestones Clubbed)-End*/
		
		/*Month|Milestone Name|Milestone Value(Individual Milestone)*/
		$highest_month = date('Y-m-d');
		foreach($sf_data as $sf) {
			$month = date('Y-m', strtotime($sf['for_month_year']));
			$highest_month = ($highest_month > date('Y-m-d', strtotime($sf['for_month_year']))) ? $highest_month : date('Y-m-d', strtotime($sf['for_month_year']));
			$data['report_data'][$sf['forecast_id']][$sf['milestone_name']]['customer']  = $sf['company'].' - '.$sf['first_name'].' '.$sf['last_name'];
			$data['report_data'][$sf['forecast_id']][$sf['milestone_name']]['lead_name'] = $sf['lead_title'];
			$data['report_data'][$sf['forecast_id']][$sf['milestone_name']]['entity'] = $sf['division_name'];
			$data['report_data'][$sf['forecast_id']][$sf['milestone_name']][$month]['type']      = ($sf['forecast_category']==1)?'Lead':'Project';
			// $data['report_data'][$sf['forecast_id']][$sf['milestone_name']]['ms_name']   = $sf['milestone_name'];
			$data['report_data'][$sf['forecast_id']][$sf['milestone_name']][$month]['ms_value']  = $this->conver_currency($sf['milestone_value'],$rates[$sf['expect_worth_id']][$this->default_cur_id]);
		}
		/*Month|Milestone Name|Milestone Value(Individual Milestone)*/
		
		if(($this->input->post("filter")!="") && $filter['month_year_to_date'])
		$data['highest_month'] = date('Y-m-d', strtotime($filter['month_year_to_date']));
		else
		$data['highest_month'] = $highest_month;
		
		if(($this->input->post("filter")!="") && $filter['month_year_from_date'])
		$data['current_month'] = date('Y-m', strtotime($filter['month_year_from_date']));
		else
		$data['current_month'] = date('Y-m');

		// echo "<pre>"; print_r($data['report_data']); exit;

		if($this->input->post("filter")!="")
		$this->load->view('sales_forecast/sale_forecast_report_view_grid', $data);
		else
		$this->load->view('sales_forecast/sale_forecast_report_view', $data);
	}
	
	/*
	 *variance reports
	 */
	public function variance_reports()
	{
		$this->load->helper('custom_helper');
		
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$rates = $this->get_currency_rates();
		
		$data['page_heading'] = 'Sales Forecast Variance Reports';
		
		$data['default_currency'] = $this->default_cur_name;
		
		$data['entity']      = $this->sales_forecast_model->get_records('sales_divisions', $wh_condn = array('status'=>1), $order = array("div_id"=>"asc"));
		$data['customers']   = $this->sales_forecast_model->get_sf_records('customers');
		$data['leads']       = $this->sales_forecast_model->get_sf_records('jobs');
		$curFiscalYear       = $this->sales_forecast_model->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$data['month_array'] = array(04=>'Apr',05=>'May',06=>'Jun',07=>'Jul',08=>'Aug',09=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec',01=>'Jan',02=>'Feb',03=>'Mar');
		
		$filter   			 = real_escape_array($this->input->post());
		
		$variance_data 		 = $this->sales_forecast_model->get_variance_records($filter);
		// echo '<pre>'; print_r($variance_data); exit;
		
		$highest_month = date('Y-m-d');
		/* foreach($variance_data as $vr) {
			$month = date('Y-m', strtotime($vr['for_month_year']));
			$highest_month = ($highest_month > date('Y-m-d', strtotime($vr['for_month_year']))) ? $highest_month : date('Y-m-d', strtotime($vr['for_month_year']));
			$data['report_data'][$vr['job_id']][$vr['milestone_name']][$month][$vr['type']]['customer']  = $vr['company'].' - '.$vr['first_name'].' '.$vr['last_name'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']][$month][$vr['type']]['lead_name'] = $vr['lead_title'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']][$month][$vr['type']]['ms_name']   = $vr['milestone_name'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']][$month][$vr['type']]['ms_value']  = $this->conver_currency($vr['milestone_value'],$rates[$vr['expect_worth_id']][$this->default_cur_id]);
		} */
		
		foreach($variance_data as $vr) {
			$month = date('Y-m', strtotime($vr['for_month_year']));
			$highest_month = ($highest_month > date('Y-m-d', strtotime($vr['for_month_year']))) ? $highest_month : date('Y-m-d', strtotime($vr['for_month_year']));
			$data['report_data'][$vr['job_id']][$vr['milestone_name']]['customer']   = $vr['company'].' - '.$vr['first_name'].' '.$vr['last_name'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']]['lead_name'] = $vr['lead_title'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']]['entity']    = $vr['division_name'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']][$month][$vr['type']]  = $this->conver_currency($vr['milestone_value'],$rates[$vr['expect_worth_id']][$this->default_cur_id]);
		}
		
		//Set the Highest_month
		if(($this->input->post("filter")!="") && $filter['month_year_to_date'])
		$data['highest_month'] = date('Y-m-d', strtotime($filter['month_year_to_date']));
		else
		$data['highest_month'] = $highest_month;
		
		//Set the Current month
		if(($this->input->post("filter")!="") && $filter['month_year_from_date'])
		$data['current_month'] = date('Y-m', strtotime($filter['month_year_from_date']));
		else
		$data['current_month'] = date('Y-m');
		
		// echo "<pre>"; print_r($data['report_data']); exit;
		
		if($this->input->post("filter")!="")
		$this->load->view('sales_forecast/sale_forecast_var_report_view_grid', $data);
		else
		$this->load->view('sales_forecast/sale_forecast_var_report_view', $data);
	}
	
	/*
	*method : get_currency_rates
	*/
	public function get_currency_rates() 
	{
		$currency_rates = $this->sales_forecast_model->get_currency_rate();
    	$rates 			= array();
    	if(!empty($currency_rates)){
    		foreach ($currency_rates as $currency)
    		{
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}
	
	/*
	*method : conver_currency
	*/
	public function conver_currency($amount,$val) {
		return round($amount*$val);
	}
	
	/*
	*@method: export_excel_forecast
	*/
	function export_excel_forecast()
	{
		$this->load->helper('custom_helper');
		
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$default_currency = $this->default_cur_name;
		$rates = $this->get_currency_rates();
	
		$filter 			  = array();
		
		$entity 			  = $this->input->post('entity');
		$customer			  = $this->input->post('customer');
		$lead_ids			  = $this->input->post('lead_ids');
		$month_year_from_date = $this->input->post('month_year_from_date');
		$month_year_to_date   = $this->input->post('month_year_to_date');
		
		if((!empty($entity)) && $entity!='null')
		$filter['entity'] = $entity;
		
		if((!empty($customer)) && $customer!='null')
		$filter['customer'] = $customer;
		
		if((!empty($lead_ids)) && $lead_ids!='null')
		$filter['lead_ids'] = $lead_ids;
		
		if((!empty($month_year_from_date)) && $month_year_from_date!='null')
		$filter['month_year_from_date'] = $this->input->post('month_year_from_date');
		
		if((!empty($month_year_to_date)) && $month_year_to_date!='null')
		$filter['month_year_to_date'] = $this->input->post('month_year_to_date');
		
		$forecast_data 		     = $this->sales_forecast_model->get_sf_milestone_records($filter);
		$current_month = date('Y-m');
		$highest_month = date('Y-m-d');
		
		foreach($forecast_data as $fc) {
			$month = date('Y-m', strtotime($fc['for_month_year']));
			$highest_month = ($highest_month > date('Y-m-d', strtotime($fc['for_month_year']))) ? $highest_month : date('Y-m-d', strtotime($fc['for_month_year']));
			$report_data[$fc['forecast_id']][$fc['milestone_name']]['customer']         = $fc['company'].' - '.$fc['first_name'].' '.$fc['last_name'];
			$report_data[$fc['forecast_id']][$fc['milestone_name']]['lead_name']        = $fc['lead_title'];
			$report_data[$fc['forecast_id']][$fc['milestone_name']]['entity']           = $fc['division_name'];
			$report_data[$fc['forecast_id']][$fc['milestone_name']][$month]['type']     = ($fc['forecast_category']==1)?'Lead':'Project';
			$report_data[$fc['forecast_id']][$fc['milestone_name']][$month]['ms_value'] = $this->conver_currency($fc['milestone_value'],$rates[$fc['expect_worth_id']][$this->default_cur_id]);
		}
		
		//Set the Current month
		if((!empty($month_year_from_date)) && $month_year_from_date!='null')
		$current_month = date('Y-m', strtotime($filter['month_year_from_date']));
		
		//Set the Highest_month
		if((!empty($month_year_to_date)) && $month_year_to_date!='null')
		$highest_month = date('Y-m-d', strtotime($filter['month_year_to_date']));
		
		// echo "<pre>"; print_r($current_month); exit;
		
    	if(count($report_data)>0) {
		
    		//load our new PHPExcel library
			$this->load->library('excel');
			
			$borderGray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FFB0B0B0'),),),);
			$borderBlack = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FF000000'),),),);
			$borderWhite = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FFFFFFFF'),),),);
			$borderBlackbackGray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FF000000'),),),'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'d9d9d9'),
			),);
			
			$this->excel->getDefaultStyle()->applyFromArray($borderWhite);
			$this->excel->getDefaultStyle()->getFont()->setSize(11);
			
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Salesforecast');

			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', 'Customer');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Customer');
			//Set width for cells
			$this->excel->getActiveSheet()->setCellValue('C1', 'Lead/Project Name');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Milestone');
			$lastcolumn = 'D';
			$row = 1;
			$i = date("Y-m", strtotime($current_month));
			while($i <= date("Y-m", strtotime($highest_month))) {
				// echo date('M', strtotime($i)); 
				$lastcolumn++;
				$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, date('M',strtotime($i)));
				$month_arr[date('Y-m', strtotime($i))] = date('Y-M', strtotime($i));
				$month_no_arr[] = date('Y-m', strtotime($i));
				
				if(substr($i, 5, 2) == "12")
				$i = (date("Y", strtotime($i."-01")) + 1)."-01";
				else
				$i++;
			}
			
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->applyFromArray($borderBlackbackGray);
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(18);
			
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->getFont()->setBold(true);
			
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			
			$row++;
			
			foreach($report_data as $lead_id=>$ms_data) {
				 foreach($ms_data as $ms_name=>$ms_value) { 
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $ms_value['entity']);
					$this->excel->getActiveSheet()->setCellValue('B'.$row, $ms_value['customer']);
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $ms_value['lead_name']);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $ms_name);
					$lastcolumn = 'D';
					if(is_array($month_arr) && count($month_arr)>0) {
						foreach($month_arr as $mon_number=>$mon_val) {
							$lastcolumn++;
							if(array_key_exists($mon_number, $ms_value)) {
								$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, $ms_value[$mon_number]['ms_value']);
								$tot[$mon_number] += $ms_value[$mon_number]['ms_value']; 
							} else { 
								$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, '-');
							} 
						}
						$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->applyFromArray($borderBlack);
						$row++;
					}
				}
			}
			$this->excel->setActiveSheetIndex(0)->mergeCells('A'.$row.':D'.$row);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->setCellValue('A'.$row, 'Overall Total ('.$this->default_cur_name.')');
			if(is_array($month_arr) && count($month_arr)>0) {
				$lastcolumn = 'D';
				foreach($month_arr as $mon_number=>$mon_val) {
					$lastcolumn++;
					$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, $tot[$mon_number]);
				}
			}
			
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->applyFromArray($borderBlack);
			
			$this->excel->getActiveSheet()->getStyle('E2:'.$lastcolumn.$row)->getNumberFormat()->setFormatCode('0.00');
			
			$filename='saleforecast_report.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			///////save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			///////if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			////////force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
    	}   	
    	redirect('/sales_forecast/reports');
	}
	
	/*
	*@method: export_excel_variance
	*/
	function export_excel_variance()
	{
		$this->load->helper('custom_helper');
		
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$default_currency = $this->default_cur_name;
		$rates = $this->get_currency_rates();
	
		$filter 			  = array();
		
		$entity 			  = $this->input->post('entity');
		$customer			  = $this->input->post('customer');
		$lead_ids			  = $this->input->post('lead_ids');
		$month_year_from_date = $this->input->post('month_year_from_date');
		$month_year_to_date   = $this->input->post('month_year_to_date');
		
		if((!empty($entity)) && $entity!='null')
		$filter['entity'] = $entity;
		
		if((!empty($customer)) && $customer!='null')
		$filter['customer'] = $customer;
		
		if((!empty($lead_ids)) && $lead_ids!='null')
		$filter['lead_ids'] = $lead_ids;
		
		if((!empty($month_year_from_date)) && $month_year_from_date!='null')
		$filter['month_year_from_date'] = $this->input->post('month_year_from_date');
		
		if((!empty($month_year_to_date)) && $month_year_to_date!='null')
		$filter['month_year_to_date'] = $this->input->post('month_year_to_date');
		
		$variance_data = $this->sales_forecast_model->get_variance_records($filter);
		
		$current_month = date('Y-m');
		$highest_month = date('Y-m-d');
		
		foreach($variance_data as $vr) {
			$month = date('Y-m', strtotime($vr['for_month_year']));
			$highest_month = ($highest_month > date('Y-m-d', strtotime($vr['for_month_year']))) ? $highest_month : date('Y-m-d', strtotime($vr['for_month_year']));
			$report_data[$vr['job_id']][$vr['milestone_name']]['customer']   = $vr['company'].' - '.$vr['first_name'].' '.$vr['last_name'];
			$report_data[$vr['job_id']][$vr['milestone_name']]['lead_name']  = $vr['lead_title'];
			$report_data[$vr['job_id']][$vr['milestone_name']]['entity']     = $vr['division_name'];
			$report_data[$vr['job_id']][$vr['milestone_name']][$month][$vr['type']] = $this->conver_currency($vr['milestone_value'],$rates[$vr['expect_worth_id']][$this->default_cur_id]);
		}
		
		//Set the Current month
		if((!empty($month_year_from_date)) && $month_year_from_date!='null')
		$current_month = date('Y-m', strtotime($filter['month_year_from_date']));
		
		//Set the Highest_month
		if((!empty($month_year_to_date)) && $month_year_to_date!='null')
		$highest_month = date('Y-m-d', strtotime($filter['month_year_to_date']));
		
    	if(count($variance_data)>0) {
    		//load our new PHPExcel library
			$this->load->library('excel');
			$borderGray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FFB0B0B0'),),),);
			$borderBlack = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FF000000'),),),);
			$borderWhite = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FFFFFFFF'),),),);
			$borderBlackbackGray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FF000000'),),),'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'d9d9d9'),
			),);
			
			$this->excel->getDefaultStyle()->applyFromArray($borderWhite);
			$this->excel->getDefaultStyle()->getFont()->setSize(11);
			
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Salesforecast Variance');

			//set cell A1 content with some text
			$this->excel->setActiveSheetIndex(0)->mergeCells('A1:A2');
			$this->excel->getActiveSheet()->setCellValue('A1', 'Customer');
			$this->excel->setActiveSheetIndex(0)->mergeCells('B1:B2');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Customer');
			$this->excel->setActiveSheetIndex(0)->mergeCells('C1:C2');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Lead/Project Name');
			$this->excel->setActiveSheetIndex(0)->mergeCells('D1:D2');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Milestone');
			$lastcolumn = 'D';
			$row = 1;
			$i = date("Y-m", strtotime($current_month));
			while($i <= date("Y-m", strtotime($highest_month))) {
				// echo date('M', strtotime($i));
				$lastcolumn++;
				$a = $lastcolumn;
				$lastcolumn++;
				$b = $lastcolumn;
				$this->excel->setActiveSheetIndex(0)->mergeCells($a.$row.':'.$b.$row);
				$this->excel->getActiveSheet()->setCellValue($a.$row, date('M',strtotime($i)));
				// $this->excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($borderBlackbackGray);
				$row++;
				$this->excel->getActiveSheet()->setCellValue($a.$row, 'Forecast');
				$this->excel->getActiveSheet()->setCellValue($b.$row, 'Actual');
			
				$month_arr[date('Y-m', strtotime($i))] = date('Y-M', strtotime($i));
				$month_no_arr[] = date('Y-m', strtotime($i));
				
				if(substr($i, 5, 2) == "12")
				$i = (date("Y", strtotime($i."-01")) + 1)."-01";
				else
				$i++;
				
				$row = 1;
			}
			
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->applyFromArray($borderBlackbackGray);
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(18);
			
			$row = 2;
			
			$this->excel->getActiveSheet()->getStyle('E'.$row.':'.$lastcolumn.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('E'.$row.':'.$lastcolumn.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('E'.$row.':'.$lastcolumn.$row)->applyFromArray($borderBlackbackGray);
			$this->excel->getActiveSheet()->getStyle('E'.$row.':'.$lastcolumn.$row)->getFont()->setBold(true);
			// $this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(18);
			
			//Set width for cells
			$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			
			$row++;
			
			foreach($report_data as $lead_id=>$ms_data) {
				foreach($ms_data as $ms_name=>$ms_value) {
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $ms_value['entity']);
					$this->excel->getActiveSheet()->setCellValue('B'.$row, $ms_value['customer']);
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $ms_value['lead_name']);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $ms_name);
					$lastcolumn = 'D';
					if(is_array($month_arr) && count($month_arr)>0) {
						foreach($month_arr as $mon_number=>$mon_val) {
							$lastcolumn++;
							if(array_key_exists($mon_number, $ms_value)) {
								$forecast_val = isset($ms_value[$mon_number]['F']) ? $ms_value[$mon_number]['F'] : '-';
								$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, $forecast_val);
								$tot['F'][$mon_number] += $ms_value[$mon_number]['F'];
								$lastcolumn++;
								$actual_val = isset($ms_value[$mon_number]['A']) ? $ms_value[$mon_number]['A'] : '-';
								$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, $actual_val);
								$tot['A'][$mon_number] += $ms_value[$mon_number]['A'];
							} else { 
								$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, '-');
								$lastcolumn++;
								$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, '-');
							} 
						}
						$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->applyFromArray($borderBlack);
						$row++;
					}
				}
			}
			
			$this->excel->setActiveSheetIndex(0)->mergeCells('A'.$row.':D'.$row);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->setCellValue('A'.$row, 'Overall Total ('.$this->default_cur_name.')');
			if(is_array($month_arr) && count($month_arr)>0) {
				$lastcolumn = 'D';
				foreach($month_arr as $mon_number=>$mon_val) {
					$lastcolumn++;
					$forecast_tot_val = isset($tot['F'][$mon_number]) ? $tot['F'][$mon_number] : '-';
					$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, $forecast_tot_val);
					$lastcolumn++;
					$actual_tot_val = isset($tot['A'][$mon_number]) ? $tot['A'][$mon_number] : '-';
					$this->excel->getActiveSheet()->setCellValue($lastcolumn.$row, $actual_tot_val);
				}
			}
			
			$this->excel->getActiveSheet()->getStyle('A'.$row.':'.$lastcolumn.$row)->applyFromArray($borderBlack);
			
			$this->excel->getActiveSheet()->getStyle('E2:'.$lastcolumn.$row)->getNumberFormat()->setFormatCode('0.00');
			
			// $filename='saleforecast_variance_report'.time().'.xls'   ; //save our workbook as this file name
			$filename='saleforecast_variance_report.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			///////save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			///////if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			////////force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
    	}    	
    	redirect('/sales_forecast/variance_reports');
	}
	
	
	/*
	 *@method: dashboard
	 *@show data in graph
	 */
	public function forecast_dashboard()
	{
		$data['page_heading'] = 'Sales Forecast Dashboard';
		$this->load->helper('custom_helper');
		
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$rates = $this->get_currency_rates();
		
		$data['default_currency'] = $this->default_cur_name;
		
		$data['entity']      = $this->sales_forecast_model->get_records('sales_divisions', $wh_condn = array('status'=>1), $order = array("div_id"=>"asc"));
		$data['customers']   = $this->sales_forecast_model->get_sf_records('customers');
		$data['leads']       = $this->sales_forecast_model->get_sf_records('jobs');
		$curFiscalYear       = $this->sales_forecast_model->calculateFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$data['month_array'] = array(04=>'Apr',05=>'May',06=>'Jun',07=>'Jul',08=>'Aug',09=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec',01=>'Jan',02=>'Feb',03=>'Mar');
		
		$filter   			 = real_escape_array($this->input->post());
		
		$variance_data 		 = $this->sales_forecast_model->get_variance_records($filter);
		// echo '<pre>'; print_r($variance_data); exit;
		
		$highest_month = date('Y-m-d');
		
		foreach($variance_data as $vr) {
			$month = date('Y-m', strtotime($vr['for_month_year']));
			$highest_month = ($highest_month > date('Y-m-d', strtotime($vr['for_month_year']))) ? $highest_month : date('Y-m-d', strtotime($vr['for_month_year']));
			$data['report_data'][$vr['job_id']][$vr['milestone_name']]['customer']   = $vr['company'].' - '.$vr['first_name'].' '.$vr['last_name'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']]['lead_name'] = $vr['lead_title'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']]['entity']    = $vr['division_name'];
			$data['report_data'][$vr['job_id']][$vr['milestone_name']][$month][$vr['type']]  = $this->conver_currency($vr['milestone_value'],$rates[$vr['expect_worth_id']][$this->default_cur_id]);
		}
		
		//Set the Highest_month
		if(($this->input->post("filter")!="") && $filter['month_year_to_date'])
		$data['highest_month'] = date('Y-m-d', strtotime($filter['month_year_to_date']));
		else
		$data['highest_month'] = $highest_month;
		
		//Set the Current month
		if(($this->input->post("filter")!="") && $filter['month_year_from_date'])
		$data['current_month'] = date('Y-m', strtotime($filter['month_year_from_date']));
		else
		$data['current_month'] = date('Y-m');
		
		// echo "<pre>"; print_r($data['report_data']); exit;
		
		if($this->input->post("filter")!="")
		$this->load->view('sales_forecast/sale_forecast_dashboard_view_grid', $data);
		else
		$this->load->view('sales_forecast/sale_forecast_dashboard_view', $data);
	}
}