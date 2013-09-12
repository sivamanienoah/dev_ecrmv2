<?php
class task_alert extends CI_Controller {
    
	public $userdata;
	
    function __construct()
	{
        parent::__construct();
		//$this->login_model->check_login();
		//$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('task_alert_model');
        $this->load->library('validation');
		$this->load->library('email');
		$this->load->helper('form');
    }
    
    public function index()
    {
    	if($this->input->post('update_user')=='update' && $this->set_validation())
    	{
    		$options = array();
    		$options['id'] = $this->input->post('hid_days');
    		$options['task_alert_days'] = $this->input->post('days');
    		
    		if(empty($options['id'])){
    			$this->task_alert_model->addConfig($options);
    		}else{
    			$this->task_alert_model->updateConfig($options);
    		}
    		
    		//$this->task_alert_model->getConfig();
    	}
    	$data['res'] = $this->task_alert_model->getConfig();
    	$this->load->view('tasks/task_alert',$data);
    }

	public function set_validation()
	{
		$rules['days'] = "trim|required|integer|max_length[11]";
		$this->validation->set_rules($rules);
		$fields['days'] = "days";
		$this->validation->set_fields($fields);
		$this->validation->set_error_delimiters('<p class="form-error">', '</p>');
	 	
		if ($this->validation->run() == false)
		{
			return false;
        } 
        return true;	

	}
}