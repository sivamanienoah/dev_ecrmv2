<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pending extends CI_Controller {

	function Pending()
	{
		parent::__construct();
		$this->cfg = $this->config->item('crm');
		$this->load->scaffolding($this->cfg['dbpref'].'items');
		//$this->login_model->check_login(array(0,1));
		$this->login_model->check_login();
	}
	
	/*function index()
	{
		
	}*/
}
?>