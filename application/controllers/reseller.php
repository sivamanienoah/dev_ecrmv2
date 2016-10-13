<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
// error_reporting(E_ALL);
ini_set('display_errors', '1');
class Reseller extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() 
	{
		parent::__construct();
		
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('email_template_model');
		$this->load->model('reseller_model');
		$this->load->helper('text');
		$this->load->helper('form');
		$this->load->helper('lead_stage');
		$this->load->helper('custom');
		$this->load->helper('reseller');
		$this->email->set_newline("\r\n");
		$this->load->library('email');
		$this->load->library('upload');
		
		$this->stg 		= getLeadStage();
		$this->stg_name = getLeadStageName();
		$this->stages 	= @implode('","', $this->stg);
	
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$this->reseller_role_id = 14; /**Role Id of Reseller**/
		$this->contract_status = array(0=>'Not started',1=>'Active',2=>'Closed');/**Contract Status of Reseller**/
		$this->lead_status = array(4=>'Closed');/**Lead Status**/
		$this->pjt_status  = array(0=>'Not Started',1=>'Project In Progress',2=>'Project Completed',3=>'Project Onhold',4=>'Project Inactive');/**Project Status**/
	}
	
	/*
	 * List all the Leads based on levels
	 * @access public
	 */
	public function index()
	{
		$data['page_heading'] 	= "Reseller Dashboard";
		$data['reseller'] 		= array();
		$data['reseller'] 		= $this->reseller_model->get_reseller();
		$this->load->view('reseller/index', $data);
    }


	/*
	 * List all the Leads based on levels
	 * @access public
	 * @param reseller user id
	 */
	public function view_reseller($update=false,$id=0)
	{
		$is_int = is_numeric($id);
		if(!$is_int)
		{
			redirect('reseller');
		}
		
		$data['page_heading'] 	= "View Reseller";
		$data['reseller_det'] 	= array();
		$data['reseller_det'] 	= $this->reseller_model->get_reseller($id);
		$data['currencies'] 	= $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		if(!empty($data['currencies'])) {
			foreach($data['currencies'] as $curr){
				$data['currency_arr'][$curr['expect_worth_id']] = $curr['expect_worth_name'];
			}
		}
		$data['users'] 				= $this->reseller_model->get_records('users', $wh_condn=array('inactive'=>0), $order=array('first_name'=>'asc'));
		$data['contract_data'] 		= $this->reseller_model->get_contracts_details($id);
		$data['commission_data'] 	= $this->reseller_model->get_commission_details($id);
		//get the current financial year
		$curFiscalYear 				= getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		//get the closed job ids
		$closed_sale_data 			= $this->reseller_model->getClosedJobids($id);
		
		$data['sales']				= $this->calcClosedJobs($closed_sale_data, $curFiscalYear);
		$data['curFiscalYear']		= $curFiscalYear;
		// echo "<pre>"; print_r($data['sales']); echo "</pre>";
		
		$this->load->view('reseller/reseller_view', $data);
    }
	
	function calcClosedJobs($closed_sale_data, $curFiscalYear)
	{
		$bk_rates = get_book_keeping_rates(); //get all the book keeping rates
		$rates = array();
		$sales = array();
		$i = 0;
		if(is_array($closed_sale_data) && !empty($closed_sale_data) && count($closed_sale_data)>0) {
			foreach ($closed_sale_data as $closed_row) {
				$result						  = array();
				$result						  = $this->reseller_model->getLeadClosedDate($closed_row['lead_id'], $curFiscalYear);
				if(!empty($result) && count($result)>0) {
					$sales[$i]['lead_id'] 			 	= $closed_row['lead_id'];
					$sales[$i]['project_name'] 		 	= $closed_row['lead_title'];
					$sales[$i]['lead_status'] 		 	= $closed_row['lead_status'];
					$sales[$i]['pjt_status'] 		 	= $closed_row['pjt_status'];
					$sales[$i]['expect_worth_id'] 	 	= $closed_row['expect_worth_id'];
					$sales[$i]['actual_worth_amount'] 	= $closed_row['actual_worth_amount'];
					$sales[$i]['converted_amount'] 		= converCurrency($closed_row['actual_worth_amount'], $bk_rates[$curFiscalYear][$closed_row['expect_worth_id']][$this->default_cur_id]);
					$sales[$i]['company_name'] 		 	= $closed_row['company_name'];
					$sales[$i]['customer_contact_name'] = $closed_row['customer_contact_name'];
					$sales[$i]['sale_date']  			= $result['dateofchange'];
					$sales[$i]['sale_by']  				= $result['sale_by'];
				}
				$i++;
			}
		}
		return $sales;
	}
	
	/*
	 * Get Reseller Sale History By Ajax
	 * @access public
	 * @param reseller user id, financial year
	 */
	public function getSaleHistory()
	{
		// echo "<pre>"; print_r($this->input->post()); exit;
		$data				  	= array();
		$closed_sale_data 		= $this->reseller_model->getClosedJobids($this->input->post('reseller_id'));
		$data['sales']			= $this->calcClosedJobs($closed_sale_data, $this->input->post('financial_year'));
		$data['curFiscalYear']	= $this->input->post('financial_year');
		$result 			  	= $this->load->view("reseller/sale_history_grid", $data, true);
		echo $result; exit;
    }
	
	/*
	 * Add Contract Form
	 * @access public
	 * @param reseller user id
	 */
	public function getContractForm($reseller_id)
	{
		$data				  = array();
		$data['reseller_det'] = $this->reseller_model->get_reseller($reseller_id);
		$data['currencies']   = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		$data['users'] 		  = $this->reseller_model->get_records('users', $wh_condn=array('inactive'=>0), $order=array('first_name'=>'asc'));
		$result 			  = $this->load->view("reseller/add_contract_form", $data, true);
		echo $result; exit;
    }
	
	/*
	* Inserting the Reseller Contract
	*/
	public function addResellerContract()
	{
		$ins_val = array();
		$ins_val['contracter_id'] 			= $this->input->post('contracter_id');
		$ins_val['contract_title'] 			= $this->input->post('contract_title');
		$ins_val['contract_manager'] 		= $this->input->post('contract_manager');
		$ins_val['contract_start_date'] 	= ($this->input->post('contract_start_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('contract_start_date'))) : '';
		$ins_val['contract_end_date'] 		= ($this->input->post('contract_end_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('contract_end_date'))) : '';
		$ins_val['renewal_reminder_date'] 	= ($this->input->post('renewal_reminder_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('renewal_reminder_date'))) : '';
		$ins_val['description'] 			= $this->input->post('description');
		$ins_val['contract_signed_date']	= ($this->input->post('contract_signed_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('contract_signed_date'))) : '';
		$ins_val['contract_status'] 		= $this->input->post('contract_status');
		$ins_val['currency'] 				= $this->input->post('currency');
		$ins_val['tax'] 					= $this->input->post('tax');
		$ins_val['created_by'] 				= $this->userdata['userid'];
		$ins_val['created_on'] 				= date('Y-m-d H:i:s');
		$ins_val['modified_by'] 			= $this->userdata['userid'];
		$ins_val['modified_on'] 			= date('Y-m-d H:i:s');

		$insert_contract = $this->reseller_model->insert_row_return_id('contracts', $ins_val);
		if($insert_contract) {
			//if contract manager changed, update the contract manager in user table
			if($this->input->post('contract_manager') != $this->input->post('hidden_contract_manager')) {
				$cm_val = array();
				$cm_val['contract_manager'] = $this->input->post('contract_manager');
				$cm_condn = array('userid'=>$this->input->post('contracter_id'));
				$chge_manager = $this->reseller_model->update_records('users', $cm_condn, '', $cm_val);
			}
			
			$uploaded_files 			= $this->input->post('file_id');			
			$map_files = $this->reseller_model->mapUploadedFiles($uploaded_files, $insert_contract);
			
			//do log
			$contract_log = array();
			$contract_log = $ins_val;
			$contract_log['contract_id'] 	= $insert_contract;
			$contract_log['action_on']  	= date('Y-m-d H:i:s');
			$contract_log['action_by']  	= $this->userdata['userid'];
			$contract_log['action']  		= 0; //For Added
			$log_res = $this->reseller_model->insert_row_return_id("contracts_logs", $contract_log);
			
			//do log in logs table
			$currencies = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			
			$contract_manager_name = $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->input->post('contract_manager')));
			$log_name = $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->userdata['userid']));
			$contract_status_id = $this->input->post('contract_status');
			
			$log_detail = "Contract Added by: ".$log_name['first_name']." ".$log_name['last_name']."\n";
			$log_detail .= "\nContract Manager: ".$contract_manager_name['first_name']." ".$contract_manager_name['last_name'];
			$log_detail .= "\nContract Title: ".$this->input->post('contract_title');
			$log_detail .= "\nContract Start date: ".$this->input->post('contract_start_date');
			$log_detail .= "\nContract End date: ".$this->input->post('contract_end_date');
			$log_detail .= "\nRenewal Reminder date: ".$this->input->post('renewal_reminder_date');
			$log_detail .= "\nDescription: ".$this->input->post('description');
			$log_detail .= "\nContract Signed Date: ".$this->input->post('contract_signed_date');
			$log_detail .= "\nContract Status: ".$this->contract_status[$contract_status_id];
			$log_detail .= "\nCurrency: ".$all_cur[$ins_val['currency']];
			$log_detail .= "\nTax: ".$this->input->post('tax');
			
			$file_details = '';
			$file_arr = array();
			$log_upload['upload_data'] = $this->reseller_model->getUploadsFile($insert_contract);
			
			if(!empty($log_upload['upload_data']) && count($log_upload['upload_data'])>0 && is_array($log_upload['upload_data'])){
				foreach($log_upload['upload_data'] as $rec) {
					$file_arr[] = $rec['file_name'];
				}
				if(!empty($file_arr) && count($file_arr)>0 && is_array($file_arr)){
					$file_details = @implode(",",$file_arr);
				}
			}
			
			$log_detail .= "\nContract Documents: ".rtrim($file_details,",");
			
			$log = array();
			$log['jobid_fk']      = 0;
			$log['userid_fk']     = $this->input->post('contracter_id');
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->reseller_model->insert_row_return_id("logs", $log);
			echo "success";
		} else {
			echo "error";
		}
	}
	
	/*
	* Get the contract details for edit form
	*/
	public function getEditContractData()
	{
		$data 		 = array();
		$editdata 	 = array();
		$data['msg'] = 'error';
		$wh_condn	 = array('id'=>$this->input->post('contract_id'), 'contracter_id'=>$this->input->post('contracter_user_id'));
		$editdata['contract_data'] = $this->reseller_model->get_data_by_id('contracts', $wh_condn);
		if(!empty($editdata['contract_data']) && count($editdata['contract_data'])>0) 
		{
			$editdata['contract_id'] = $this->input->post('contract_id');
			$editdata['currencies']  = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			$editdata['users'] 		 = $this->reseller_model->get_records('users', $wh_condn=array('inactive'=>0), $order=array('first_name'=>'asc'));
			$editdata['upload_data'] = $this->reseller_model->getUploadsFile($this->input->post('contract_id'));
			$data['res'] = $this->load->view("reseller/edit_contract_form", $editdata, true);
			$data['msg'] = 'success';
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	* editing the edit Reseller Contract
	*/
	public function editResellerContract()
	{
		// echo "<pre>"; print_r($this->input->post()); exit;
		$updt_val 		= array();
		$contract_log 	= array();
		$updt_val['contracter_id'] 			= $this->input->post('contracter_id');
		$updt_val['contract_title'] 		= $this->input->post('contract_title');
		$updt_val['contract_manager'] 		= $this->input->post('contract_manager');
		$updt_val['contract_start_date'] 	= ($this->input->post('contract_start_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('contract_start_date'))) : '';
		$updt_val['contract_end_date'] 		= ($this->input->post('contract_end_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('contract_end_date'))) : '';
		$updt_val['renewal_reminder_date'] 	= ($this->input->post('renewal_reminder_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('renewal_reminder_date'))) : '';
		$updt_val['description'] 			= $this->input->post('description');
		$updt_val['contract_signed_date']	= ($this->input->post('contract_signed_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('contract_signed_date'))) : '';
		$updt_val['contract_status'] 		= $this->input->post('contract_status');
		$updt_val['currency'] 				= $this->input->post('currency');
		$updt_val['tax'] 					= $this->input->post('tax');
		$updt_val['modified_by'] 			= $this->userdata['userid'];
		$updt_val['modified_on'] 			= date('Y-m-d H:i:s');
		//**Updating the contract details**//
		$condn 			 = array('id'=>$this->input->post('contract_id'), 'contracter_id'=>$this->input->post('contracter_id'));
		$update_contract = $this->reseller_model->update_records('contracts', $condn, '', $updt_val); //$tbl, $wh_condn, $not_wh_condn, $up_arg
		
		if($update_contract){
			//*mapping uploaded files*//
			$uploaded_files = $this->input->post('file_id');
			$map_files 		= $this->reseller_model->mapUploadedFiles($uploaded_files, $this->input->post('contract_id'));
			
			//if contract manager changed, update the contract manager in user table
			if($this->input->post('contract_manager') != $this->input->post('hidden_contract_manager')) {
				$cm_val = array();
				$cm_val['contract_manager'] = $this->input->post('contract_manager');
				$cm_condn = array('userid'=>$this->input->post('contracter_id'));
				$chge_manager = $this->reseller_model->update_records('users', $cm_condn, '', $cm_val);
			}
			
			//do log
			$contract_log = $updt_val;
			unset($contract_log['id']);
			$contract_log['contract_id'] 	= $this->input->post('contract_id');
			$contract_log['action_on']  	= date('Y-m-d H:i:s');
			$contract_log['action_by']  	= $this->userdata['userid'];
			$contract_log['action']  		= 1;
			$log_res = $this->reseller_model->insert_row_return_id("contracts_logs", $contract_log);
			
			//do log in logs table
			$currencies = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			
			$contract_manager_names = $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->input->post('contract_manager')));
			$log_names = $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->userdata['userid']));
			
			$log_detail = "Contract Updated by: ".$log_names['first_name']." ".$log_names['last_name']."\n";
			$log_detail .= "\nContract Manager: ".$contract_manager_names['first_name']." ".$contract_manager_names['last_name'];
			$log_detail .= "\nContract Title: ".$this->input->post('contract_title');
			$log_detail .= "\nContract Start date: ".$this->input->post('contract_start_date');
			$log_detail .= "\nContract End date: ".$this->input->post('contract_end_date');
			$log_detail .= "\nRenewal Reminder date: ".$this->input->post('renewal_reminder_date');
			$log_detail .= "\nDescription: ".$this->input->post('description');
			$log_detail .= "\nContract Signed Date: ".$this->input->post('contract_signed_date');
			$log_detail .= "\nContract Status: ".$this->contract_status[$this->input->post('contract_status')];
			$log_detail .= "\nCurrency: ".$all_cur[$this->input->post('currency')];
			$log_detail .= "\nTax: ".sprintf('%0.2f', $this->input->post('tax'));
			$file_details = '';
			$file_arr = array();
			$log_upload['upload_data'] = $this->reseller_model->getUploadsFile($this->input->post('contract_id'));
			
			if(!empty($log_upload['upload_data']) && count($log_upload['upload_data'])>0 && is_array($log_upload['upload_data'])){
				foreach($log_upload['upload_data'] as $rec) {
					$file_arr[] = $rec['file_name'];
				}
				if(!empty($file_arr) && count($file_arr)>0 && is_array($file_arr)){
					$file_details = @implode(",",$file_arr);
				}
			}
			
			$log_detail .= "\nContract Documents: ".rtrim($file_details,",");
			$log = array();
			$log['jobid_fk']      = 0;
			$log['userid_fk']     = $this->input->post('contracter_id');
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->reseller_model->insert_row_return_id("logs", $log);
			
			echo "success";
		} else {
			echo "error";
		}
		exit;
	}
	
	/*
	* Deleting the contract
	* @params contract id & contracter user id
	* return json encoded array.
	*/
	public function deleteContractData()
	{
		//save the old values
		$contract_log	= array();
		$wh_condn		= array('id'=>$this->input->post('contract_id'), 'contracter_id'=>$this->input->post('contracter_user_id'));
		$contract_log 	= $this->reseller_model->get_data_by_id('contracts', $wh_condn);
		
		$log_upload['upload_data'] = $this->reseller_model->getUploadsFile($this->input->post('contract_id'));
		
		$data 		= array();
		$wh_condn 	= array('id'=>$this->input->post('contract_id'), 'contracter_id'=>$this->input->post('contracter_user_id'));
		$is_deleted = $this->reseller_model->delete_records('contracts', $wh_condn);
		if($is_deleted) {
			//do log
			unset($contract_log['id']);
			$contract_log['contract_id'] 	= $this->input->post('contract_id');
			$contract_log['action_on']  	= date('Y-m-d H:i:s');
			$contract_log['action_by']  	= $this->userdata['userid'];
			$contract_log['action']  		= 2;
			$log_res = $this->reseller_model->insert_row_return_id("contracts_logs", $contract_log);
			
			//do log in logs table
			$currencies = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			
			$contract_manager_name = $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$contract_log['contract_manager']));
			$log_name = $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->userdata['userid']));
			$contract_status_id = $this->input->post('contract_status');
			
			$log_detail = "Contract Deleted by: ".$log_name['first_name']." ".$log_name['last_name']."\n";
			$log_detail .= "\nContract Manager: ".$contract_manager_name['first_name']." ".$contract_manager_name['last_name'];
			$log_detail .= "\nContract Title: ".$contract_log['contract_title'];
			$log_detail .= "\nContract Start date: ".date('d-m-Y', strtotime($contract_log['contract_start_date']));
			$log_detail .= "\nContract End date: ".date('d-m-Y', strtotime($contract_log['contract_end_date']));
			$log_detail .= "\nRenewal Reminder date: ".date('d-m-Y', strtotime($contract_log['renewal_reminder_date']));
			$log_detail .= "\nDescription: ".$contract_log['description'];
			$log_detail .= "\nContract Signed Date: ".date('d-m-Y', strtotime($contract_log['contract_signed_date']));
			$log_detail .= "\nContract Status: ".$this->contract_status[$contract_log['contract_status']];
			$log_detail .= "\nCurrency: ".$all_cur[$contract_log['currency']];
			$log_detail .= "\nTax: ".$contract_log['tax'];
			
			$file_details = '';
			$file_arr = array();

			if(!empty($log_upload['upload_data']) && count($log_upload['upload_data'])>0 && is_array($log_upload['upload_data'])){
				foreach($log_upload['upload_data'] as $rec) {
					$file_arr[] = $rec['file_name'];
				}
				if(!empty($file_arr) && count($file_arr)>0 && is_array($file_arr)){
					$file_details = @implode(",",$file_arr);
				}
			}
			
			$log_detail .= "\nContract Documents: ".rtrim($file_details,",");
			
			$log = array();
			$log['jobid_fk']      = 0;
			$log['userid_fk']     = $this->input->post('contracter_user_id');
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			// echo "<pre>"; print_r($log); die;
			$log_res = $this->reseller_model->insert_row_return_id("logs", $log);
			
			$data['res'] = 'success';
		} else {
			$data['res'] = 'failure';
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	* Get all active projects
	* @params contract id & contracter user id
	* return json encoded array.
	*/
	public function getResellerActiveProjects()
	{
		// $this->load->library('../controllers/projects/dashboard');
		
		//project billing type
		$this->db->select('project_billing_type, id');
		$this->db->from($this->cfg['dbpref']. 'project_billing_type');
		$ptquery = $this->db->get();
		$data['project_type'] = $ptquery->result();
		
		// for practices
		$this->db->select('id, practices');
		$this->db->from($this->cfg['dbpref']. 'practices');
		$prtquery = $this->db->get();
		$practices = $prtquery->result();
		$data['practices'] = $prtquery->result();
		
		$prt_arr = array();
		if(!empty($practices) && count($practices)>0){
			foreach($practices as $prtrec){
				$prt_arr[$prtrec->id] = $prtrec->practices;
			}
		}
	
		$this->db->select('l.lead_id, l.lead_title, l.complete_status, l.estimate_hour, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.project_type, l.division, l.actual_date_start, l.actual_date_due, c.customer_name AS customer_contact_name, cc.company AS company_name');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
		$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", 4);
		$this->db->where("l.pjt_status", 1);
		$reseller_condn = '(`l`.`belong_to` = '.$this->input->post('userid').' OR `l`.`lead_assign` = '.$this->input->post('userid').' OR `l`.`assigned_to` = '.$this->input->post('userid').')';
		$this->db->where($reseller_condn);
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$res = $query->result_array();
		
		// $data['projects_data'] = $this->dashboard->getProjectsDataByDefaultCurrency($res, '', '');
		
		$bk_rates = get_book_keeping_rates(); //get all the book keeping rates
		$sales = array();
		$i = 0;
		if(is_array($res) && !empty($res) && count($res)>0) {
			foreach ($res as $prow) {
				$result						  = array();
				$result						  = $this->reseller_model->getLeadClosedDateYear($prow['lead_id']);
				// echo "<pre>"; print_r($result); die;
				if(!empty($result) && count($result)>0) {
					$curFiscalYear 	= getFiscalYearForDate(date("m/d/y", strtotime($result['dateofchange'])),"4/1","3/31");
					$curFiscalYear = isset($curFiscalYear) ? $curFiscalYear : getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
					$sales[$i]['lead_id'] 			 	= $prow['lead_id'];
					$sales[$i]['project_name'] 		 	= $prow['lead_title'];
					$sales[$i]['lead_status'] 		 	= $prow['lead_status'];
					$sales[$i]['pjt_status'] 		 	= $prow['pjt_status'];
					$sales[$i]['expect_worth_id'] 	 	= $prow['expect_worth_id'];
					$sales[$i]['actual_worth_amount'] 	= $prow['actual_worth_amount'];
					$sales[$i]['actual_date_start'] 	= (isset($prow['actual_date_start']) && !empty($prow['actual_date_start'])) ? date('d-m-Y', strtotime($prow['actual_date_start'])) : '-';
					$sales[$i]['actual_date_due'] 		= (isset($prow['actual_date_due']) && !empty($prow['actual_date_due'])) ? date('d-m-Y', strtotime($prow['actual_date_due'])) : '-';
					$sales[$i]['converted_amount'] 		= converCurrency($prow['actual_worth_amount'], $bk_rates[$curFiscalYear][$prow['expect_worth_id']][$this->default_cur_id]);
					$sales[$i]['company_name'] 		 	= $prow['company_name'];
					$sales[$i]['customer_contact_name'] = $prow['customer_contact_name'];					
					$sales[$i]['practice'] 				= (isset($prow['practice']) && !empty($prow['practice'])) ? $prt_arr[$prow['practice']] : '';	
				}
				$i++;
			}
		}
		$data['projects_data'] = $sales;

		// echo "<pre>"; print_r($data['projects_data']); exit;
		$this->load->view('reseller/projects_drill_data', $data);
	}
	
	/*
	* Get all get Reseller Active Leads
	* @params contracter user id
	* return html.
	*/
	public function getResellerActiveLeads()
	{
		$data['filter_results'] = $this->reseller_model->getLeads($this->input->post('userid'));
		$this->load->view('reseller/leads_drill_data', $data);
	}

	/*
	* Get all get Reseller Leads
	* @params contracter user id
	* return html.
	*/
	public function getResellerJobs()
	{
		if($this->input->post('type') == 1) {
			$data['filter_results'] = $this->reseller_model->getResellerLeads($this->input->post('userid'));
			$res = $this->load->view('reseller/leads_drill_data', $data, true);
		} else if($this->input->post('type') == 2) {
			
			// $this->load->library('../controllers/projects/dashboard');
			
			$data = array();
			
			//project billing type
			$this->db->select('project_billing_type, id');
			$this->db->from($this->cfg['dbpref']. 'project_billing_type');
			$ptquery = $this->db->get();
			$data['project_type'] = $ptquery->result();
			
			// for practices
			$this->db->select('id, practices');
			$this->db->from($this->cfg['dbpref']. 'practices');
			$prtquery = $this->db->get();
			$practices = $prtquery->result();
			
			$prt_arr = array();
			if(!empty($practices) && count($practices)>0){
				foreach($practices as $prtrec){
					$prt_arr[$prtrec->id] = $prtrec->practices;
				}
			}
			
			$this->db->select('l.lead_id, l.lead_title, l.complete_status, l.estimate_hour, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.project_type, l.division, l.actual_date_start, l.actual_date_due, c.customer_name AS customer_contact_name, cc.company AS company_name');
			$this->db->from($this->cfg['dbpref']. 'leads as l');
			$this->db->join($this->cfg['dbpref'].'customers as c', 'c.custid = l.custid_fk');
			$this->db->join($this->cfg['dbpref'].'customers_company as cc', 'cc.companyid = c.company_id');
			$this->db->where("l.lead_id != ", 'null');
			$this->db->where("l.pjt_id  != ", 'null');
			$this->db->where("l.lead_status", 4);
			$this->db->where_in("l.pjt_status", array(1,2,3,4));
			$reseller_condn = '(`l`.`belong_to` = '.$this->input->post('userid').' OR `l`.`lead_assign` = '.$this->input->post('userid').' OR `l`.`assigned_to` = '.$this->input->post('userid').')';
			$this->db->where($reseller_condn);
			$query = $this->db->get();
			// echo $this->db->last_query(); die;
			$res = $query->result_array();
			
			// $data['projects_data'] = $this->dashboard->getProjectsDataByDefaultCurrency($res, '', '');
			
			$bk_rates = get_book_keeping_rates(); //get all the book keeping rates
			$sales = array();
			$i = 0;
			if(is_array($res) && !empty($res) && count($res)>0) {
				foreach ($res as $prow) {
					$result						  = array();
					$result						  = $this->reseller_model->getLeadClosedDateYear($prow['lead_id']);
					// echo "<pre>"; print_r($result); die;
					if(!empty($result) && count($result)>0) {
						$curFiscalYear 	= getFiscalYearForDate(date("m/d/y", strtotime($result['dateofchange'])),"4/1","3/31");
						$curFiscalYear = isset($curFiscalYear) ? $curFiscalYear : getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
						$sales[$i]['lead_id'] 			 	= $prow['lead_id'];
						$sales[$i]['project_name'] 		 	= $prow['lead_title'];
						$sales[$i]['lead_status'] 		 	= $prow['lead_status'];
						$sales[$i]['pjt_status'] 		 	= $prow['pjt_status'];
						$sales[$i]['expect_worth_id'] 	 	= $prow['expect_worth_id'];
						$sales[$i]['actual_worth_amount'] 	= $prow['actual_worth_amount'];
						$sales[$i]['actual_date_start'] 	= (isset($prow['actual_date_start']) && !empty($prow['actual_date_start'])) ? date('d-m-Y', strtotime($prow['actual_date_start'])) : '-';
						$sales[$i]['actual_date_due'] 		= (isset($prow['actual_date_due']) && !empty($prow['actual_date_due'])) ? date('d-m-Y', strtotime($prow['actual_date_due'])) : '-';
						$sales[$i]['converted_amount'] 		= converCurrency($prow['actual_worth_amount'], $bk_rates[$curFiscalYear][$prow['expect_worth_id']][$this->default_cur_id]);
						$sales[$i]['company_name'] 		 	= $prow['company_name'];
						$sales[$i]['customer_contact_name'] = $prow['customer_contact_name'];					
						$sales[$i]['practice'] 				= (isset($prow['practice']) && !empty($prow['practice'])) ? $prt_arr[$prow['practice']] : '';	
					}
					$i++;
				}
			}
			$data['projects_data'] = $sales;
		
			$res = $this->load->view('reseller/projects_drill_data', $data, true);
		}
		echo $res; exit;
	}
	
	/**
	 * Get All the Audit History for the Reseller
	 * works with the Ajax
	 */
	public function getAuditHistory()
	{
		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->helper('fix_text');
		
		$data['log_html'] = '';
		$getLogsData = $this->reseller_model->getLogs($this->input->post('userid'));
		$data['log_html'] .= '<table width="100%" id="lead_log_list" class="log-container logstbl">';
		$data['log_html'] .= '<thead><tr><th>&nbsp;</th></tr></thead><tbody>';
		
		if (!empty($getLogsData)) {
			$log_data = $getLogsData;

			foreach ($log_data as $ld) 
			{
				$job_name 	 = '';
				$log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
				$job_name 	 = $ld['lead_title'];
				if(empty($job_name)){
					$job_name 	 = $ld['log_user'];
				}
				$fancy_date  = date('l, jS F y h:iA', strtotime($ld['date_created']));
				$stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';
				
				$table ='<tr id="log" class="log'.$stick_class.'"><td id="log" class="log'.$stick_class.'">
						 <p class="data log'.$stick_class.'"><span class="log'.$stick_class.'">'.$fancy_date.'</span>'.$job_name.'</p>
						 <p class="desc log'.$stick_class.'">'.$log_content.'</p></td></tr>';
				$data['log_html'] .= $table;
				unset($table, $job_name, $user, $log_content);
			}
		}
		$data['log_html'] .= '</tbody></table>';
		echo $data['log_html'];
	}
	
	/*
	* Get all get Customer Contacts
	* @params contracter user id
	* return html.
	*/	
	public function getCustomerContact()
	{
		$data['contact'] = $this->reseller_model->customer_contact_list($this->input->post('userid'));
		$res = $this->load->view('reseller/customer_contact_data', $data, true);
		echo $res; exit;
	}
	
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function contractFileUpload($contracter_id)
	{
		$this->load->library('upload');
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['contract_document']['name']);
	
		//creating files folder name
		$f_dir = UPLOAD_PATH.'contracts/';
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		//creating lead_id folder name
		$f_dir = $f_dir.$contracter_id;
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$this->upload->initialize(array(
		   "upload_path" => $f_dir,
		   "overwrite" => FALSE,
		   "remove_spaces" => TRUE,
		   "max_size" => 51000000,
		   "allowed_types" => "*"
		)); 
		// $config['allowed_types'] = '*';
		// "allowed_types" => "gif|png|jpeg|jpg|bmp|tiff|tif|txt|text|doc|docs|docx|oda|class|xls|xlsx|pdf|mpp|ppt|pptx|hqx|cpt|csv|psd|pdf|mif|gtar|gz|zip|tar|html|htm|css|shtml|rtx|rtf|xml|xsl|smi|smil|tgz|xhtml|xht"
		
		$returnUpload = array();
		$json  		  = array();
		$res_file     = array();
		if(!empty($_FILES['contract_document']['name'][0])) {
			if ($this->upload->do_multi_upload("contract_document")) {
			   $returnUpload  = $this->upload->get_multi_upload_data();
			   $json['error'] = FALSE;
			   $json['msg']   = "File successfully uploaded!";
			   $i = 1;
			   if(!empty($returnUpload)) {
					foreach($returnUpload as $file_up) {
						$lead_files['contracter_user_id'] 	= $contracter_id;	
						$lead_files['file_name'] 			= $file_up['file_name'];
						$lead_files['created_by'] 			= $this->userdata['userid'];
						$lead_files['created_on'] 			= date('Y-m-d H:i:s');
						$lead_files['modified_by'] 			= $this->userdata['userid'];
						$lead_files['modified_on'] 			= date('Y-m-d H:i:s');
						$insert_file						= $this->reseller_model->insert_row_return_id('contracts_uploads', $lead_files);
						$insert_file						= base64_encode($insert_file);
						$json['res_file'][]					= $insert_file.'~'.$file_up['file_name'].'~'.microtime();
						$i++;
					}
				}
			} else {
				$this->upload_error = strip_tags($this->upload->display_errors());
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
			}
		}
		echo json_encode($json); 
		exit;
	}
	
	/*
	*Download file by id
	**/
	function download_file()
	{
		$file_id = base64_decode($this->input->post('file_id'));
		$getFile = $this->reseller_model->get_data_by_id('contracts_uploads', $wh_condn = array('id'=>$file_id));
		// echo "<pre>"; print_r($getFile); exit;
		$this->load->helper('download');
		$file_dir 	= UPLOAD_PATH.'contracts/'.$getFile['contracter_user_id'].'/'.$getFile['file_name'];
		$data 		= file_get_contents($file_dir); // Read the file's contents
		force_download($getFile['file_name'], $data);
	}
	
	/*
	* Deleting the Contract uploads by ajax
	* return json_encode
	*/
	public function deleteContractUploads()
	{
		$file_id = base64_decode($this->input->post('file_id'));
		
		$data 		= array();
		$wh_condn 	= array('contract_id'=>$this->input->post('contract_id'), 'contract_file_upload_id'=>$file_id);
		$is_deleted = $this->reseller_model->delete_records('contracts_uploads_mapping', $wh_condn);
		if($is_deleted) {
			//update contracts upload table
			$updt_val = array();
			$updt_val['status'] = 0;
			$condn 			 = array('id'=>$this->input->post('contract_id'), 'contracter_user_id'=>$this->input->post('contracter_user_id'));
			$update_contract = $this->reseller_model->update_records('contracts_uploads', $condn, '', $updt_val); //$tbl, $wh_condn, $not_wh_condn, $up_arg

			$data['res'] = 'success';
		} else {
			$data['res'] = 'failure';
		}
		echo json_encode($data);
		exit;
	}

	/*
	* Loading the contract data by ajax
	* return json_encode
	*/
	public function loadContractGrid()
	{
		$contracts['currencies'] 	= $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		if(!empty($contracts['currencies'])) {
			foreach($contracts['currencies'] as $curr){
				$contracts['currency_arr'][$curr['expect_worth_id']] = $curr['expect_worth_name'];
			}
		}
		$contracts['contract_data'] 	= $this->reseller_model->get_contracts_details($this->input->post('contracter_user_id'));
		$data['res'] = $this->load->view("reseller/contract_grid", $contracts, true);
		
		echo json_encode($data);
		exit;
	}
	
	/*
	 * Add Commission Form
	 * @access public
	 * @param reseller user id
	 */
	public function getCommissionForm($reseller_id)
	{
		$data				  		= array();
		$data['reseller_det'] 		= $this->reseller_model->get_reseller($reseller_id);
		$data['reseller_projects'] 	= $this->reseller_model->get_closed_jobs($reseller_id);
		$data['contracts_det']  	= $this->reseller_model->get_active_contract('contracts', $wh_condn=array('contract_status'=>1,'contracter_id'=>$reseller_id), $order=array('contract_start_date'=>'DESC'), $limit=1);
		$data['currencies']   		= $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		$result 			  = $this->load->view("reseller/add_commission_form", $data, true);
		echo $result; exit;
    }
	
	
	/*
	* Inserting the Commission History Details
	*/
	public function addResellerCommission()
	{
		$ins_val = array();
		$ins_val['contracter_id'] 			= $this->input->post('contracter_id');
		$ins_val['commission_title'] 		= $this->input->post('commission_title');	
		$ins_val['job_id'] 					= $this->input->post('job_id');	
		$ins_val['payment_advice_date'] 	= ($this->input->post('payment_advice_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('payment_advice_date'))) : '';
		$ins_val['for_the_month_year'] 		= ($this->input->post('for_the_month_year')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('for_the_month_year'))) : '';
		$ins_val['commission_milestone_name'] = $this->input->post('commission_milestone_name');
		$ins_val['commission_currency'] 	= $this->input->post('commission_currency');
		$ins_val['commission_value'] 		= $this->input->post('commission_value');
		$ins_val['remarks'] 				= $this->input->post('remarks');
		$ins_val['created_by'] 				= $this->userdata['userid'];
		$ins_val['created_on'] 				= date('Y-m-d H:i:s');
		$ins_val['modified_by'] 			= $this->userdata['userid'];
		$ins_val['modified_on'] 			= date('Y-m-d H:i:s');

		$insert_commission = $this->reseller_model->insert_row_return_id('commission_history', $ins_val);
		if($insert_commission) {
			
			$uploaded_files 			= $this->input->post('file_id');
			$map_files = $this->reseller_model->mapCommissionUploadedFiles($uploaded_files, $insert_commission);
			
			//do log
			$commission_log = array();
			$commission_log = $ins_val;
			$commission_log['commission_id'] = $insert_commission;
			$commission_log['action']  		 = 0; //For Added
			$log_res = $this->reseller_model->insert_row_return_id("commission_history_log", $commission_log);
			
			//do log in logs table
			$currencies = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			
			$project_name 	= $this->reseller_model->get_data_by_id('leads', $wh_condn=array('lead_id'=>$this->input->post('job_id')));
			$log_name 		= $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->userdata['userid']));
			
			$log_detail = "Commission Added by: ".$log_name['first_name']." ".$log_name['last_name']."\n";
			$log_detail .= "\nProject Name: ".$project_name['lead_title'];
			$log_detail .= "\nCommission Title: ".$this->input->post('commission_title');
			$log_detail .= "\nPayment Advice date: ".$this->input->post('payment_advice_date');
			$log_detail .= "\nFor the Month & Year: ".$this->input->post('for_the_month_year');
			$log_detail .= "\nMilestone Name: ".$this->input->post('commission_milestone_name');
			$log_detail .= "\nCurrency: ".$all_cur[$this->input->post('commission_currency')];
			$log_detail .= "\nValue: ".sprintf('%0.2f', $this->input->post('commission_value'));
			$log_detail .= "\nRemarks: ".$this->input->post('remarks');
			
			$file_details = '';
			$file_arr = array();
			$log_upload['upload_data'] = $this->reseller_model->getCommissionUploadsFile($insert_commission);
			
			if(!empty($log_upload['upload_data']) && count($log_upload['upload_data'])>0 && is_array($log_upload['upload_data'])){
				foreach($log_upload['upload_data'] as $rec) {
					$file_arr[] = $rec['file_name'];
				}
				if(!empty($file_arr) && count($file_arr)>0 && is_array($file_arr)){
					$file_details = @implode(",",$file_arr);
				}
			}
			
			$log_detail .= "\nAttachment Documents: ".rtrim($file_details,",");
			
			$log = array();
			$log['jobid_fk']      = 0;
			$log['userid_fk']     = $this->input->post('contracter_id');
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->reseller_model->insert_row_return_id("logs", $log);
			
			echo "success";
		} else {
			echo "error";
		}
	}
	
		/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function commissionFileUpload($contracter_id)
	{
		$this->load->library('upload');
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['attachment_document']['name']);
	
		//creating files folder name
		$f_dir = UPLOAD_PATH.'commission_attachments/';
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		//creating lead_id folder name
		$f_dir = $f_dir.$contracter_id;
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$this->upload->initialize(array(
		   "upload_path" => $f_dir,
		   "overwrite" => FALSE,
		   "remove_spaces" => TRUE,
		   "max_size" => 51000000,
		   "allowed_types" => "*"
		)); 
		// $config['allowed_types'] = '*';
		// "allowed_types" => "gif|png|jpeg|jpg|bmp|tiff|tif|txt|text|doc|docs|docx|oda|class|xls|xlsx|pdf|mpp|ppt|pptx|hqx|cpt|csv|psd|pdf|mif|gtar|gz|zip|tar|html|htm|css|shtml|rtx|rtf|xml|xsl|smi|smil|tgz|xhtml|xht"
		
		$returnUpload = array();
		$json  		  = array();
		$res_file     = array();
		if(!empty($_FILES['attachment_document']['name'][0])) {
			if ($this->upload->do_multi_upload("attachment_document")) {
			   $returnUpload  = $this->upload->get_multi_upload_data();
			   $json['error'] = FALSE;
			   $json['msg']   = "File successfully uploaded!";
			   $i = 1;
			   if(!empty($returnUpload)) {
					foreach($returnUpload as $file_up) {
						$lead_files['contracter_user_id'] 	= $contracter_id;	
						$lead_files['file_name'] 			= $file_up['file_name'];
						$lead_files['created_by'] 			= $this->userdata['userid'];
						$lead_files['created_on'] 			= date('Y-m-d H:i:s');
						$lead_files['modified_by'] 			= $this->userdata['userid'];
						$lead_files['modified_on'] 			= date('Y-m-d H:i:s');
						$insert_file						= $this->reseller_model->insert_row_return_id('commission_uploads', $lead_files);
						$insert_file						= base64_encode($insert_file);
						$json['res_file'][]					= $insert_file.'~'.$file_up['file_name'].'~'.microtime();
						$i++;
					}
				}
			} else {
				$this->upload_error = strip_tags($this->upload->display_errors());
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
			}
		}
		echo json_encode($json); 
		exit;
	}
	
	/*
	* Get the commission details for edit form
	*/
	public function getEditCommissionData()
	{
		$data 		 = array();
		$editdata 	 = array();
		$data['msg'] = 'error';
		$wh_condn	 = array('id'=>$this->input->post('commission_id'), 'contracter_id'=>$this->input->post('contracter_user_id'));
		$editdata['commission_data'] = $this->reseller_model->get_data_by_id('commission_history', $wh_condn);
		if(!empty($editdata['commission_data']) && count($editdata['commission_data'])>0) 
		{
			$editdata['commission_id'] 		= $this->input->post('commission_id');
			$editdata['currencies']  		= $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			$editdata['reseller_projects'] 	= $this->reseller_model->get_closed_jobs($this->input->post('contracter_user_id'));
			$editdata['upload_data'] 		= $this->reseller_model->getCommissionUploadsFile($this->input->post('commission_id'));
			$data['res'] = $this->load->view("reseller/edit_commission_form", $editdata, true);
			$data['msg'] = 'success';
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	* Editing the commission
	* @params commission id & contracter user id
	* return json encoded array.
	*/
	public function editResellerCommission()
	{
		// echo "<pre>"; print_r($this->input->post()); exit;
		$updt_val 			= array();
		$commission_log 	= array();
		
		$updt_val['contracter_id'] 				= $this->input->post('contracter_id');
		$updt_val['commission_title'] 			= $this->input->post('commission_title');	
		$updt_val['job_id'] 					= $this->input->post('job_id');	
		$updt_val['payment_advice_date'] 		= ($this->input->post('payment_advice_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('payment_advice_date'))) : '';
		$updt_val['for_the_month_year'] 		= ($this->input->post('for_the_month_year')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('for_the_month_year'))) : '';
		$updt_val['commission_milestone_name'] 	= $this->input->post('commission_milestone_name');
		$updt_val['commission_currency'] 		= $this->input->post('commission_currency');
		$updt_val['commission_value'] 			= $this->input->post('commission_value');
		$updt_val['remarks'] 					= $this->input->post('remarks');
		$updt_val['modified_by'] 				= $this->userdata['userid'];
		$updt_val['modified_on'] 				= date('Y-m-d H:i:s');
		//**Updating the contract details**//
		$condn 			 = array('id'=>$this->input->post('commission_id'), 'contracter_id'=>$this->input->post('contracter_id'));
		$update_contract = $this->reseller_model->update_records('commission_history', $condn, '', $updt_val); //$tbl, $wh_condn, $not_wh_condn, $up_arg
		
		if($update_contract){
			//*mapping uploaded files*//
			$uploaded_files = $this->input->post('file_id');
			$map_files 		= $this->reseller_model->mapCommissionUploadedFiles($uploaded_files, $this->input->post('commission_id'));
			
			//do log
			$commission_log 				 = $updt_val;
			unset($commission_log['id']);
			$commission_log['commission_id'] = $this->input->post('commission_id');
			$commission_log['action']  		 = 1;
			$log_res = $this->reseller_model->insert_row_return_id("commission_history_log", $commission_log);
			
			//do log in logs table
			$currencies = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			
			$project_name 	= $this->reseller_model->get_data_by_id('leads', $wh_condn=array('lead_id'=>$this->input->post('job_id')));
			$log_name 		= $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->userdata['userid']));
			
			$log_detail = "Commission Added by: ".$log_name['first_name']." ".$log_name['last_name']."\n";
			$log_detail .= "\nProject Name: ".$project_name['lead_title'];
			$log_detail .= "\nCommission Title: ".$this->input->post('commission_title');
			$log_detail .= "\nPayment Advice date: ".$this->input->post('payment_advice_date');
			$log_detail .= "\nFor the Month & Year: ".$this->input->post('for_the_month_year');
			$log_detail .= "\nMilestone Name: ".$this->input->post('commission_milestone_name');
			$log_detail .= "\nCurrency: ".$all_cur[$this->input->post('commission_currency')];
			$log_detail .= "\nValue: ".$this->input->post('commission_value');
			$log_detail .= "\nRemarks: ".$this->input->post('remarks');
			
			$file_details = '';
			$file_arr = array();
			$log_upload['upload_data'] = $this->reseller_model->getCommissionUploadsFile($this->input->post('commission_id'));
			
			if(!empty($log_upload['upload_data']) && count($log_upload['upload_data'])>0 && is_array($log_upload['upload_data'])){
				foreach($log_upload['upload_data'] as $rec) {
					$file_arr[] = $rec['file_name'];
				}
				if(!empty($file_arr) && count($file_arr)>0 && is_array($file_arr)){
					$file_details = @implode(",",$file_arr);
				}
			}
			
			$log_detail .= "\nAttachment Documents: ".rtrim($file_details,",");
			
			$log = array();
			$log['jobid_fk']      = 0;
			$log['userid_fk']     = $this->input->post('contracter_id');
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->reseller_model->insert_row_return_id("logs", $log);
			
			echo "success";
		} else {
			echo "error";
		}
		exit;
	}
	
	/*
	* Deleting the commission
	* @params commission id & contracter user id
	* return json encoded array.
	*/
	public function deleteCommissionData()
	{
		//save the old values
		$commission_log	= array();
		$wh_condn		= array('id'=>$this->input->post('commission_id'), 'contracter_id'=>$this->input->post('contracter_user_id'));
		$commission_log = $this->reseller_model->get_data_by_id('commission_history', $wh_condn);
		$log_upload['upload_data'] = $this->reseller_model->getCommissionUploadsFile($this->input->post('commission_id'));
		
		$data 		= array();
		$wh_condn 	= array('id'=>$this->input->post('commission_id'), 'contracter_id'=>$this->input->post('contracter_user_id'));
		$is_deleted = $this->reseller_model->delete_records('commission_history', $wh_condn);
		if($is_deleted) {
			//do log
			unset($commission_log['id']);
			$commission_log['commission_id'] = $this->input->post('commission_id');
			$commission_log['action']  		 = 2;
			$log_res = $this->reseller_model->insert_row_return_id("commission_history_log", $commission_log);
			
			//do log in logs table
			$currencies = $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			
			$project_name 	= $this->reseller_model->get_data_by_id('leads', $wh_condn=array('lead_id'=>$commission_log['job_id']));
			$log_name 		= $this->reseller_model->get_data_by_id('users', $wh_condn=array('userid'=>$this->userdata['userid']));
			
			$log_detail = "Commission Deleted by: ".$log_name['first_name']." ".$log_name['last_name']."\n";
			$log_detail .= "\nProject Name: ".$project_name['lead_title'];
			$log_detail .= "\nCommission Title: ".$commission_log['commission_title'];
			$log_detail .= "\nPayment Advice date: ".date('d-m-Y', strtotime($commission_log['payment_advice_date']));
			$log_detail .= "\nFor the Month & Year: ".date('F Y', strtotime($commission_log['for_the_month_year']));
			$log_detail .= "\nMilestone Name: ".$commission_log['commission_milestone_name'];
			$log_detail .= "\nCurrency: ".$all_cur[$commission_log['commission_currency']];
			$log_detail .= "\nValue: ".sprintf('%0.2f', $commission_log['commission_value']);
			$log_detail .= "\nRemarks: ".$commission_log['remarks'];
			
			$file_details = '';
			$file_arr = array();
			
			if(!empty($log_upload['upload_data']) && count($log_upload['upload_data'])>0 && is_array($log_upload['upload_data'])){
				foreach($log_upload['upload_data'] as $rec) {
					$file_arr[] = $rec['file_name'];
				}
				if(!empty($file_arr) && count($file_arr)>0 && is_array($file_arr)){
					$file_details = @implode(",",$file_arr);
				}
			}
			
			$log_detail .= "\nAttachment Documents: ".rtrim($file_details,",");
			
			$log = array();
			$log['jobid_fk']      = 0;
			$log['userid_fk']     = $this->input->post('contracter_user_id');
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->reseller_model->insert_row_return_id("logs", $log);
			
			$data['res'] = 'success';
		} else {
			$data['res'] = 'failure';
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	*Download file by id
	**/
	function downloadCommissionFile()
	{
		$file_id = base64_decode($this->input->post('file_id'));
		$getFile = $this->reseller_model->get_data_by_id('commission_uploads', $wh_condn = array('id'=>$file_id));
		// echo "<pre>"; print_r($getFile); exit;
		$this->load->helper('download');
		$file_dir 	= UPLOAD_PATH.'commission_attachments/'.$getFile['contracter_user_id'].'/'.$getFile['file_name'];
		$data 		= file_get_contents($file_dir); // Read the file's contents
		force_download($getFile['file_name'], $data);
	}
	
	/*
	* Load the commission Data by ajax
	* return json_encode
	*/
	public function loadCommissionGrid()
	{
		$commission['currencies'] 	= $this->reseller_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
		if(!empty($commission['currencies'])) {
			foreach($commission['currencies'] as $curr){
				$commission['currency_arr'][$curr['expect_worth_id']] = $curr['expect_worth_name'];
			}
		}
		$commission['commission_data'] 	= $this->reseller_model->get_commission_details($this->input->post('contracter_user_id'));
		$data['res'] = $this->load->view("reseller/commission_grid", $commission, true);
		
		echo json_encode($data);
		exit;
	}

	
	
	
	public function sendTestEmail()
	{
		//email sent by email template
		$param = array();

		$param['to_mail'] 		  = 'ssriram@enoahisolution.com';
		$param['from_email']	  = 'webmaster@enoahprojects.com';
		$param['from_email_name'] = 'Webmaster';
		$param['template_name']	  = "test email";
		$param['subject'] 		  = "test email";

		if($this->email_template_model->sent_email($param)){
			echo "Email Sent";
		} else {
			echo "Email Not Sent";
		}
	}
	
}
?>