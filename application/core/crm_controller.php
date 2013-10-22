<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 error_reporting(E_ALL);
class Crm_controller extends CI_Controller {
	echo "Sfasdfasdf"; exit;
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('mysql_real_escape');
		$this->cfg = $this->config->item('crm');
	}
}
// END Controller class

/* End of file MY_Controller.php */
/* Location: ./app/core/MY_Controller.php */