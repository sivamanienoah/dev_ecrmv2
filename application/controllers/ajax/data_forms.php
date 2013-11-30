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
		$data['regions'] = $this->regionsettings_model->region_list($limit, $search);		
		$this->load->view('helper/new_customer_form',$data);
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
			}
			
			$data['item_desc'] = stripslashes($desc_content);
			$data['item_price'] = $row[0]['item_price'];
			$data['itemid'] = $id;
		}
		$this->load->view('helper/quote_item_edit', $data);
	}
	
	//not need
	function lead_stg_form($id = 0)
	{
		$this->load->helper('text');
		
		$this->db->where('lead_stage_id', $id);
		$this->db->select('lead_stage_name, status, is_sale');
		$q = $this->db->get($this->cfg['dbpref'] . 'lead_stage');
		// echo $this->db->last_query(); exit;
		$data['lead_stage_name'] = $data['status'] = $data['is_sale'] = '';
			
		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
			//echo "<pre>"; print_r($row); exit;
			// $desc_content = ascii_to_entities($row['lead_stage_name']);
			
			$data['lead_stage_name'] = $row['lead_stage_name'];
			$data['status'] = $row['status'];
			$data['is_sale'] = $row['is_sale'];
			$data['lead_stage_id'] = $id;
		}
		
		//status
		$this->db->where('lead_stage', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'leads')->num_rows();
		
		$this->load->view('helper/lead_stage_edit', $data);
	}
	
}