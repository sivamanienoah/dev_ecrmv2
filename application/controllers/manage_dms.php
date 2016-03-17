<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage Practice
 *
 * @class 		Department
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      Admin Module
 * @Menu        Administration -> Department
 * @author 		eNoah - Mani.S
 * @Controller
 */

class Manage_dms extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@department
	*/
	public function __construct()
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('dms_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get department Lists
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage Collateral';
		$data['dms_admin'] = $this->dms_model->get_records($tbl='dms_users', $wh_condn=array('dms_type'=>0), "", "");
		$data['dms_users'] = $this->dms_model->get_records($tbl='dms_users', $wh_condn=array('dms_type'=>1), "", "");
		$data['all_users'] = $this->dms_model->get_all_users();
        $this->load->view('dms/manage_view', $data);
    }
	
	public function set_dms_users()
	{
		// echo "<pre>"; print_r($_POST); exit;
		$data['error'] = FALSE;
		$members = $this->input->post("members");
		$type 	 = $this->input->post("type");
		
		if($type=='dms_admin'){
			$dms_type = 0;
		} else {
			$dms_type = 1;
		}
		
		if ($members == "")
		{
			$data['error'] = 'Select any members!';
		}
		
		$memb = @explode(",",$members);
		$ins  = array();
		$this->db->delete($this->cfg['dbpref']."dms_users",array("dms_type" => $dms_type));
		if(count($memb) > 0){
			$ins['dms_type'] = $dms_type;
			foreach($memb as $rec){
				$ins['user_id'] = $rec;
				$this->dms_model->insert_row("dms_users", $ins);
			}
		}
		echo json_encode($data);	
	}
	
	/*
	*@Search Departments
	*@Method index
	*/
	public function search(){
        if (isset($_POST['cancel_submit'])) {
            redirect('department/');
        } else if ($name = $this->input->post('cust_search')) {
            redirect('department/index/0/' . rawurlencode($name));
        } else {
            redirect('department/');
        }
    }	
	
}