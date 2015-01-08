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

class Department extends crm_controller {
	
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
		$this->load->model('department_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get department Lists
	*@Method index
	*/
    public function index($search = FALSE) 
	{
        $data['page_heading'] = 'Manage Departments';
		$data['departments'] = $this->department_model->get_departments($search);
        $this->load->view('department/department_view', $data);
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