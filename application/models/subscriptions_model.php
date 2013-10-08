<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Subscriptions_model extends crm_model {
    
    public $userdata;
    
    function __construct()
    {
        parent::__construct();
        $this->userdata = $this->session->userdata('logged_in_user');
    }

	public function add_item_to_customer($record)
	{
		$this->db->insert($this->cfg['dbpref'] . 'recurring_items', $record);
		
		return $this->db->insert_id();
	}
	
	public function get_items_for_customer($cust_id)
	{
		$q = $this->db->get_where($this->cfg['dbpref'] . 'recurring_items', array('cust_id' => $cust_id, 'parent_id' => 0));
		
		if ($q->num_rows() > 0) {
			$data = $q->result_array();
			return $data;
		} else {
			return array();
		}
	}
	
	public function get_all_customers_with_subscriptions()
	{
		$sql = "
				SELECT customers.*
				FROM ".$this->cfg['dbpref']."customers AS customers,
				".$this->cfg['dbpref']."recurring_items AS items
				WHERE items.`cust_id` = customers.`custid`
				GROUP BY customers.`custid`";
		
		$q = $this->db->query($sql);
		
		if ($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		else
		{
			return array();
		}
		
	}
	
	public function get_discounts_for_item($item_id)
	{
		$q = $this->db->get_where($this->cfg['dbpref'] . 'recurring_items', array('parent_id' => $item_id));
		
		if ($q->num_rows() > 0) {
			$data = $q->result_array();
			return $data;
		} else {
			return array();
		}
	}
	
	public function update_item($item_id, $item_record)
	{
		$this->db->where('recurringitemid', $item_id);
		$this->db->update($this->cfg['dbpref'] . 'recurring_items', $item_record);
	}
	
	public function create_invoice_from_items($cust_id, $items, $customer)
	{
		
		$ins['job_title'] = $customer['first_name'] . ' ' . $customer['last_name'] . ' - Subscriptions Invoice';
		$ins['custid_fk'] = $cust_id;
		$ins['job_category'] = 0;
		$ins['belong_to'] = 'VT';
		$ins['division'] = 'SUBS';
		$ins['date_invoiced'] = date('Y-m-d H:i:s');
		$ins['date_created'] = date('Y-m-d H:i:s');
		$ins['date_modified'] = date('Y-m-d H:i:s');
		$ins['job_status'] = 30;
		$ins['created_by'] = 28;
		
		if ($this->db->insert($this->cfg['dbpref'] . 'jobs', $ins))
		        {
			$insert_id = $this->db->insert_id();
			
			$invoice_no = (int) $insert_id + 35000;
			$invoice_no = str_pad($invoice_no, 7, '0', STR_PAD_LEFT);
			//$invoice_no = 'VTS' . $invoice_no; MYOB CANNOT HANDLE LONGER NUMBERS
			
			$this->db->where('jobid', $insert_id);
			$this->db->update($this->cfg['dbpref'] . 'jobs', array('invoice_no' => $invoice_no));
			
			foreach ($items as $k=>$item)
			{
				$this->add_recurring_item_to_invoice($insert_id, $item);
			}
			
			return $insert_id;
		}
		
		return 0;
	}
	
	public function add_recurring_item_to_invoice($invoice_id, $item)
	{
		
		// find the due date
		switch($item['period'])
		{
			case 'month':
				$due_date = time() - (30 * 24 * 60 * 60); // 30 days OR 1 month
				break;
			case '3 months':
				$due_date = time() - (90 * 24 * 60 * 60); // 90 days OR 3 months
				break;
			case 'year':
				$due_date = time() - (365 * 24 * 60 * 60); // 365 days OR 1 year
				break;
			case '2 years':
				$due_date = time() - (730 * 24 * 60 * 60); // 730 days OR 2 years
				break;
			default:
				$due_date = time() - (30 * 24 * 60 * 60); // 30 days OR 1 month
		}
		
		if ($item['last_sent'] < $due_date)
		{
			// add the item to an invoice
			$item_record = array(
					"jobid_fk" => $invoice_id,
					"item_position" => $this->get_next_invoice_item_position($invoice_id),
					"item_desc" => $item['desc'],
					"item_price" => $item['price'],
					"ledger_code" => str_replace('-', '', $item['category'])
				);
			$this->db->insert($this->cfg['dbpref'] . 'items', $item_record);

			// remove a cycle or expire an item
			if ($item['cycles_remaining'] > 0)
			{
				if ($item['cycles_remaining'] == 1)
				{
					$this->db->delete($this->cfg['dbpref'] . 'recurring_items', array('parent_id' => $item['recurringitemid'])); 
					$this->db->delete($this->cfg['dbpref'] . 'recurring_items', array('recurringitemid' => $item['recurringitemid'])); 
				}
				else
				{
					$this->db->update($this->cfg['dbpref'] . 'recurring_items', array('cycles_remaining' => ($item['cycles_remaining'] - 1)), array('recurringitemid' => $item['recurringitemid']));
				}
			}

			$this->db->update($this->cfg['dbpref'] . 'recurring_items', array('last_sent' => time()), array('recurringitemid' => $item['recurringitemid']));

			// do any discounts
			if (!empty($item['discounts']))
			{
				foreach ($item['discounts'] as $k=>$discount)
				{
					$this->add_recurring_item_to_invoice($invoice_id, $discount);
				}
			}
		}

	}
	
	public function get_next_invoice_item_position($invoice_id)
	{
		$sql = "SELECT MAX(item_position) + 1 AS next_pos FROM " . $this->cfg['dbpref'] . "items WHERE jobid_fk = ?";
		
		$q = $this->db->query($sql, array($invoice_id));
		
		if ($q->num_rows() > 0)
		{
			$data = $q->result_array();
			if (!empty($data[0]['next_pos']))
			{
				return $data[0]['next_pos'];
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
		
	}
    
}

/* end of file */
