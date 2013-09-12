<?php  
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	class Common_model extends CI_Model {
		public function __construct()
		{
		   parent::__construct();
		   $this->cfg = $this->config->item('crm');
		}
	}
?>
