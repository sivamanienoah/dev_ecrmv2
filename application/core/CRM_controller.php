<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

print "dfgdfgfdg"; exit;

class CRM_controller extends CI_Controller {
	/**
	 * Constructor
	 */
	public function __construct()
	{
		echo "CRM_Controller"; exit;
		parent::__construct();
		$this->load->helper('mysql_real_escape');
		$this->cfg = $this->config->item('crm');
	}
}
// END Controller class

/* End of file MY_Controller.php */
/* Location: ./app/core/MY_Controller.php */