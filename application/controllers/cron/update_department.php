<?php
class Update_department extends crm_controller 
{
    public $cfg;
	public $userdata;
	
    public function __construct()
	{
		parent::__construct();		
		$this->load->model('department_model');
    }
	/*
	*@Get get department datas from econnect and update our ecrm db
	*@Model Department_model
	*@Method index
	*@Parameter --
	*@Author eNoah - Mani.S
	*/
	public function index() {
       $this->department_model->updateDepartments();
    }
}
?>