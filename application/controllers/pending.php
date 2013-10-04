<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pending extends crm_controller {

	function Pending()
	{
		parent::__construct();
		$this->load->scaffolding($this->cfg['dbpref'].'items');
		//$this->login_model->check_login(array(0,1));
		$this->login_model->check_login();
	}
	
	/*function index()
	{
		
	}*/
}
?>