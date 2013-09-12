<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Subscriptions extends CI_Controller {
	
	var $cfg;
	var $userdata;
	
	function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('welcome_model');
		$this->load->model('subscriptions_model');
	}
	
	public function new_item_form($cust_id)
	{
		$data = array(
			'cust_id' => $cust_id,
			'parent_id' => '',
			'recurringitemid' => '',
			'desc' => '',
			'period' => 'month',
			'price' => '',
			'category' => '',
			'cycles_remaining' => ''
			);
		
		$this->load->view('subscriptions/new_recurring_quote_item_form_view', $data);
	}
	
	public function new_discount_form($cust_id, $item_id)
	{
		$data = array(
			'cust_id' => $cust_id,
			'parent_id' => $item_id,
			'recurringitemid' => '',
			'desc' => '',
			'period' => 'month',
			'price' => '',
			'category' => '',
			'cycles_remaining' => ''
			);
		
		$this->load->view('subscriptions/new_recurring_quote_item_form_view', $data);
	}
	
	public function new_recurring_item($cust_id)
	{
		$item_record = array(
				'cust_id' => $cust_id,
				'desc' => $this->input->post('recurring_item_desc'),
				'price' => $this->input->post('recurring_item_price'),
				'period' => $this->input->post('recurring_item_period'),
				'category' => $this->input->post('recurring_category'),
				'parent_id' => $this->input->post('parent_id'),
				'cycles_remaining' => $this->input->post('recurring_item_cycles_remaining')
			);
			
		$item_record['recurringitemid'] = $this->subscriptions_model->add_item_to_customer($item_record);
		
		if ($this->input->post('parent_id') > 0)
		{
			$this->load->view('subscriptions/recurring_discount_on_quote_view', $item_record);
		}
		else
		{
			$this->load->view('subscriptions/recurring_item_on_quote_view', $item_record);
		}
	}
	
	public function edit_recurring_item($item_id)
	{
		$item = $this->db->get_where($this->cfg['dbpref'] . '_recurring_items', array('recurringitemid' => $item_id));
		
		if ($item->num_rows > 0)
		{
			$item = $item->result_array();
			$item = $item[0];
		}
		else
		{
			$item = array();
		}
		
		$this->load->view('subscriptions/new_recurring_quote_item_form_view', $item);
	}
	
	public function update_recurring_item($item_id)
	{
		$item_record = array(
				'recurringitemid' => $this->input->post('recurringitemid'),
				'cust_id' => $this->input->post('cust_id'),
				'desc' => $this->input->post('recurring_item_desc'),
				'price' => $this->input->post('recurring_item_price'),
				'period' => $this->input->post('recurring_item_period'),
				'category' => $this->input->post('recurring_category'),
				'parent_id' => $this->input->post('parent_id'),
				'cycles_remaining' => $this->input->post('recurring_item_cycles_remaining')
			);
		
		$this->subscriptions_model->update_item($item_id, $item_record);

		if ($this->input->post('parent_id') > 0)
		{
			$this->load->view('subscriptions/recurring_discount_on_quote_view', $item_record);
		}
		else
		{
			$this->load->view('subscriptions/recurring_item_on_quote_view', $item_record);
		}
	}
	
	public function delete_recurring_item($item_id)
	{
		$this->db->delete($this->cfg['dbpref'] . '_recurring_items', array('parent_id' => $item_id));
		$this->db->delete($this->cfg['dbpref'] . '_recurring_items', array('recurringitemid' => $item_id));
	}
	
	public function get_recurring_items($cust_id = 0)
	{
		if ($cust_id > 0) {
			$items = $this->subscriptions_model->get_items_for_customer($cust_id);
			$item_html = '';

			foreach ($items as $data)
			{
				$item_html .= $this->load->view('subscriptions/recurring_item_on_quote_view', $data, TRUE);
				$discounts = $this->subscriptions_model->get_discounts_for_item($data['recurringitemid']);

				foreach ($discounts as $dis_data)
				{
					$item_html .= $this->load->view('subscriptions/recurring_discount_on_quote_view', $dis_data, TRUE);
				}
			}

			echo $item_html;
		}
	}
	
	public function get_recurring_item($item_id)
	{
		$item = $this->db->get_where($this->cfg['dbpref'] . '_recurring_items', array('recurringitemid' => $item_id));
		
		if ($item->num_rows > 0)
		{
			$item = $item->result_array();
			$item = $item[0];
		}
		else
		{
			$item = array();
		}
		
		if (!empty($item) && $item['parent_id'] > 0) {
			$this->load->view('subscriptions/recurring_discount_on_quote_view', $item);
		} else {
			$this->load->view('subscriptions/recurring_item_on_quote_view', $item);
		}
		
	}
	
	public function get_subscription_costs($cust_id)
	{
		$sql = "
SELECT
SUM(price) AS total, period
FROM " . $this->cfg['dbpref'] . "_recurring_items
WHERE cust_id = ?
GROUP BY period";

		$q = $this->db->query($sql, array('cust_id' => $cust_id));
		if ($q->num_rows() > 0)
		{
			$result = $q->result();
			
			foreach ($result as $period)
			{
				echo '<div style="width: 224px; padding: 4px; float: left;">Total (' . $period->period . '): $' . $period->total . '</div>';
			}
		}
	}
	
}
