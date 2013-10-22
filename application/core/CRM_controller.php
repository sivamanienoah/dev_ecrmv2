<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 print "vbcbvcbcbcbcbc123"; exit;

class crm_controller extends CI_Controller {
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