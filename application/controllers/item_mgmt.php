<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Item_mgmt extends CI_Controller {
	
	public $cfg;
	public $userdata;
	
	function __construct()
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    function index($type = 'additional')
    {
        $data_type = ($type == 'saved') ? 'additional_items' : 'additional_items ';

        $data['table_in_use'] = $type;
        
        $data['page_heading'] = 'Manage additional items';
        $data['records'] = array();
		
		$data['categories'] = $this->get_category_list();
		
		$c = count($data['categories']);
		
		for ($i = 0; $i < $c; $i++)
		{
			$this->db->where('item_type', $data['categories'][$i]['cat_id']);
			$q = $this->db->get($this->cfg['dbpref'] . $data_type);
			//echo $this->db->last_query();
			$data['categories'][$i]['records'] = $q->result_array();
		}
        //echo "<pre>"; print_r($data); exit;
        $this->load->view('item_mgmt_view', $data);
    }
	
	function category_list()
	{
		$data['page_heading'] = 'Additional item categories';
		
		$data['records'] = $this->get_category_list();
        
        $this->load->view('item_mgmt_category_view', $data);
	}
	
	function get_category_list()
	{
		$this->db->order_by('cat_id');
		$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
		return $q->result_array();
	}
	
	function category($update = false, $id = false)
	{
		$data['page_heading'] = 'Additional item categories';
		
		$this->load->library('validation');
        $data = array();
        
		$rules['cat_name'] = "trim|required";
		
		$this->validation->set_rules($rules);
		
		$fields['cat_name'] = 'Category Name';
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_item']))
        {
            $item_data = $this->db->get_where("{$this->cfg['dbpref']}additional_cats", array('cat_id' => $id));
            if ($item_data->num_rows() > 0) $customer = $item_data->result_array();
            if (isset($customer) && is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v)
            {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() != false)
        {
			
			// all good
            foreach($fields as $key => $val)
            {			
                $update_data[$key] = $this->input->post($key);				
            }
            
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
            {
                
                //update
                $this->db->where('cat_id', $id);
                 
                if ($this->db->update("{$this->cfg['dbpref']}additional_cats", $update_data))
                {
                    
                    $this->session->set_flashdata('confirm', array('Item Category Updated!'));
                    redirect('item_mgmt/category/update/' . $id);
                    
                }
                
                
            }
            else
            {
                //insert
                $this->db->insert("{$this->cfg['dbpref']}additional_cats", $update_data);
                $this->session->set_flashdata('confirm', array('New Category Added!'));
                //redirect('item_mgmt/category/update/' . $this->db->insert_id());				
                redirect('item_mgmt/category_list');				
            }
			
		}
        
        $this->load->view('item_mgmt_category_add', $data);
	}
	//mychanges
	function checkcategoryname() {
		$catname = $_POST['category'];
		$cat_up = $_POST['cat_up'];
		//echo $cat_up; exit;
		if($cat_up == 'undefined') {
			$this->db->where('cat_name', $catname);
			$query = $this->db->get($this->cfg['dbpref'].'additional_cats')->num_rows();
		} else {
			$this->db->where('cat_name', $catname);
			$this->db->where('cat_id !=', $cat_up);
			$query = $this->db->get($this->cfg['dbpref'].'additional_cats')->num_rows();
		}
		//echo $this->db->last_query();
		if($query == 0 ) 
			echo 'success';
		else 
			echo 'fail';
			
		/*echo $catname = $_POST['catname'];
		$query = $this->db->query("select cat_name from {$this->cfg['dbpref']}additional_cats where cat_name='".$catname."'");
		if($query->num_rows() > 0) { 
			$json['msg'] = 'fail';
		} else {
			$json['msg'] = 'success';
		}
		echo json_encode($json); exit; */
	}
    
    function add($update = false, $id = false, $type = 'additional')
    {
       
        $this->load->library('validation');
        $data = array();
        
        $rules['item_price'] = "trim|required|numeric";
		$rules['item_desc'] = "required";
		$rules['item_type'] = "required";
		
		$this->validation->set_rules($rules);
		
		$fields['item_price'] = "item Price";
		$fields['item_desc'] = 'Item Details';
		$fields['item_type'] = 'Item Category';
		
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
        $data['categories'] = $this->get_category_list();
        
		$table_type = 'additional';
        
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_item']))
        {
            $item_data = $this->db->get_where("{$this->cfg['dbpref']}_" . $table_type . 'items', array('itemid' => $id));
            if ($item_data->num_rows() > 0) $customer = $item_data->result_array();
            if (isset($customer) && is_array($customer) && count($customer) > 0) foreach ($customer[0] as $k => $v)
            {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() != false)
        {
			
			// all good
            foreach($fields as $key => $val)
            {
                $update_data[$key] = $this->input->post($key);
            }
            
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
            {
                
                //update
                $this->db->where('itemid', $id);
                 
                if ($this->db->update("{$this->cfg['dbpref']}_" . $table_type . 'items', $update_data))
                {
                    
                    $this->session->set_flashdata('confirm', array('Item Details Updated!'));
                    redirect('item_mgmt/add/update/' . $id . '/' . $type);
                    
                }
                
                
            }
            else
            {
                
                //insert
                $this->db->insert("{$this->cfg['dbpref']}_" . $table_type . 'items', $update_data);
                $this->session->set_flashdata('confirm', array('New Item Added!'));
                redirect('item_mgmt/add/update/' . $this->db->insert_id() . '/' . $type);
                
            }
			
		}
        
        $this->load->view('item_mgmt_add', $data);
    }
	
	function item_delete($update = false, $id = false, $type = 'additional')
	{
		if ($this->session->userdata('delete')==1){
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete("{$this->cfg['dbpref']}additional_items", array('itemid' => $id));
				$this->session->set_flashdata('confirm', array('Item Record Deleted!'));
				redirect('item_mgmt');
			}
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('item_mgmt');
		}
	}
	
	function delete_category($id = false)
	{
		if ($this->session->userdata('delete')==1){
			$this->db->delete("{$this->cfg['dbpref']}additional_cats", array('cat_id' => $id));
			$this->session->set_flashdata('confirm', array('Category Record Deleted!'));
			redirect('item_mgmt/category_list');
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('item_mgmt/category_list');
		}	
	}
}
