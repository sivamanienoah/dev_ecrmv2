<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Data_forms extends crm_controller {

	public $cfg;
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('regionsettings_model');
	}
	
	/*
	 * HTML form outlining the new customer form
	 */
	function new_customer_form()
	{
		$data['regions'] = $this->regionsettings_model->region_list();
		$this->load->view('helper/new_customer_form', $data);
	}
	
	/*
	 * HTML necessary to update a quote item
	 */
	function quote_item_form($id = 0)
	{
		$this->load->helper('text');
		
		$this->db->where('itemid', $id);
		$this->db->select('item_desc, item_price');
		$q = $this->db->get($this->cfg['dbpref'] . 'items');
		
		$data['item_desc'] = $data['item_price'] = $data['itemid'] = '';
			
		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
			
			$desc_content = ascii_to_entities($row[0]['item_desc']);
			if (preg_match("/^\n/", $desc_content))
			{
				$desc_content = "\n" . $desc_content;
			} else {
				$desc_content = "\n" . $desc_content;
			}

			$data['item_desc'] = $desc_content;
			$data['item_price'] = $row[0]['item_price'];
			$data['itemid'] = $id;
		}

		$this->load->view('helper/quote_item_edit', $data);
	}
	
}