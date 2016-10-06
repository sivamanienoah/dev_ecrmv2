<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
error_reporting(E_ALL);
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
	}
	
	/*
	 * List all the Leads based on levels
	 * @access public
	 */
	public function index()
	{
		$data['page_heading'] 	= "Reseller";
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
		$data['users'] 			= $this->reseller_model->get_records('users', $wh_condn=array('inactive'=>0), $order=array('first_name'=>'asc'));
		$data['contract_data'] 	= $this->reseller_model->get_contracts_details($id);
		$this->load->view('reseller/reseller_view', $data);
    }
	
	/*
	 * List all the Leads based on levels
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
			$uploaded_files 			= $this->input->post('file_id');
			$ins_map_val    			= array();
			$ins_map_val['contract_id'] = $insert_contract;
			if(is_array($uploaded_files) && !empty($uploaded_files) && count($uploaded_files)>0) {
				/**insert into contract upload mapping table**/
				foreach($uploaded_files as $row_file_id) {
					$ins_map_val['contract_file_upload_id'] = $row_file_id;
					$this->reseller_model->insert_row_return_id('contracts_uploads_mapping', $ins_map_val);
				}
			}
			//do log
			/* $currencies = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			$log_detail  = "Added Other Cost: \n";
			$log_detail .= "\nDescription: ".$this->input->post('description');
			$log_detail .= "\nCost Incurred Date: ".$this->input->post('cost_incurred_date');
			$log_detail .= "\nValue: ".$all_cur[$ins_val['currency_type']].' '.number_format($ins_val['value'], 2);
			$log = array();
			$log['jobid_fk']      = $this->input->post('project_id');
			$log['userid_fk']     = $this->userdata['userid'];
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->project_model->insert_row("logs", $log); */
			echo "success";
		} else {
			echo "error";
		}
	}
	
	public function getResellerActiveProjects()
	{
		$this->load->library('../controllers/projects/dashboard');
	
		$this->db->select('l.lead_id, l.lead_title, l.complete_status, l.estimate_hour, l.pjt_id, l.lead_status, l.pjt_status, l.rag_status, l.practice, l.actual_worth_amount, l.estimate_hour, l.expect_worth_id, l.project_type, l.division');
		$this->db->from($this->cfg['dbpref']. 'leads as l');
		$this->db->where("l.lead_id != ", 'null');
		$this->db->where("l.pjt_id  != ", 'null');
		$this->db->where("l.lead_status", 4);
		$this->db->where("l.pjt_status", 1);
		$reseller_condn = '(`l`.`belong_to` = '.$this->input->post('userid').' OR `l`.`lead_assign` = '.$this->input->post('userid').' OR `l`.`assigned_to` = '.$this->input->post('userid').')';
		$this->db->where($reseller_condn);
		$query = $this->db->get();
		// echo $this->db->last_query(); die;
		$res = $query->result_array();
		
		$data['projects_data'] = $this->dashboard->getProjectsDataByDefaultCurrency($res, '', '');
		$this->db->select('project_billing_type, id');
		$this->db->from($this->cfg['dbpref']. 'project_billing_type');
		$ptquery = $this->db->get();
		$data['project_type'] = $ptquery->result();
		// echo "<pre>"; print_r($data['projects_data']); exit;
		$this->load->view('reseller/projects_drill_data', $data);
	}

	public function getResellerActiveLeads()
	{
		$data['filter_results'] = $this->reseller_model->getLeads($this->input->post('userid'));
		$this->load->view('reseller/leads_drill_data', $data);
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
						$json['res_file'][]					= $insert_file.'~'.$file_up['file_name'];
						
						/* $logs['jobid_fk']	   = $lead_id;
						$logs['userid_fk']	   = $this->userdata['userid'];
						$logs['date_created']  = date('Y-m-d H:i:s');
						$logs['log_content']   = $file_up['file_name'].' is added.';
						$logs['attached_docs'] = $file_up['file_name'];
						$insert_logs 		   = $this->reseller_model->insert_row('logs', $logs); */	
						$i++;
					}
				}
			} else {
				$this->upload_error = strip_tags($this->upload->display_errors());
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
				// return $this->upload_error;					
				// exit;
			}
		}
		echo json_encode($json); 
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
			$data['res'] = 'success';
		} else {
			$data['res'] = 'failure';
		}
		echo json_encode($data);
		exit;
	}
	
	/*
	* editing the other cost
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
		echo "<pre>"; print_r($this->input->post()); exit;
		$updt_val = array();
		$updt_val['description'] 		= $this->input->post('description');
		$updt_val['cost_incurred_date'] = ($this->input->post('cost_incurred_date')!='') ? date('Y-m-d H:i:s', strtotime($this->input->post('cost_incurred_date'))) : '';
		$updt_val['currency_type'] 		= $this->input->post('currency_type');
		$updt_val['value'] 				= $this->input->post('value');
		$updt_val['modified_by'] 		= $this->userdata['userid'];
		$updt_val['modified_on'] 		= date('Y-m-d H:i:s');
		$condn = array('id'=>$this->input->post('cost_id'), 'project_id'=>$this->input->post('project_id'));
		$update_cost = $this->project_model->update_row('project_other_cost', $updt_val, $condn);
		
		if($update_cost){
			echo "success";
			//do log
			$currencies = $this->project_model->get_records('expect_worth', $wh_condn=array('status'=>1), $order=array('expect_worth_id'=>'asc'));
			if(!empty($currencies)) {
				foreach($currencies as $curr){
					$all_cur[$curr['expect_worth_id']] = $curr['expect_worth_name'];
				}
			}
			$log_detail = "Updated the Other Cost: \n";
			$log_detail .= "\nDescription: ".$updt_val['description'];
			$log_detail .= "\nCost Incurred Date: ".$this->input->post('cost_incurred_date');
			$log_detail .= "\nValue: ".$all_cur[$updt_val['currency_type']].' '.number_format($updt_val['value'], 2);
			$log = array();
			$log['jobid_fk']      = $this->input->post('project_id');
			$log['userid_fk']     = $this->userdata['userid'];
			$log['date_created']  = date('Y-m-d H:i:s');
			$log['log_content']   = $log_detail;
			$log_res = $this->project_model->insert_row("logs", $log);
		} else {
			echo "error";
		}
		exit;
	}
	
}
?>