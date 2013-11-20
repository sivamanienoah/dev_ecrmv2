<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Item_mgmt extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct() {
        parent::__construct();
        $this->login_model->check_login();
        $this->load->model('item_mgmt_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    /**
     * Quote Items are listing in dashboard
     * @param table name - $type
     */
    function index($type = 'additional') {
        $data_type = ($type == 'saved') ? 'additional_items' : 'additional_items ';
        $data['table_in_use'] = $type;        
        $data['page_heading'] = 'Manage additional items';
        $data['records'] = array();		
		$data['categories'] = $this->item_mgmt_model->get_category_list();		
		
		for ($i = 0; $i < count($data['categories']); $i++) {
			$data['categories'][$i]['records'] = $this->item_mgmt_model->get_row_bycond($data_type, array('item_type' => $data['categories'][$i]['cat_id']));
		}
        $this->load->view('item_mgmt_view', $data);
    }
	
    /**
     * Category listing in category dashboad page
     */
	function category_list() {
		$data['page_heading'] = 'Additional item categories';		
		$data['records'] = $this->item_mgmt_model->get_category_list();        
        $this->load->view('item_mgmt_category_view', $data);
	}
	
	/**
	 * Create New category and update category function
	 * @param if $update is true means, it will update category otherwise insert new category
	 * @param category id - $id
	 */
	function category($update = false, $id = false) {

		$data['page_heading'] = 'Additional item categories';		
		$this->load->library('validation');
        $data = array();        
		$rules['cat_name'] = "trim|required";		
		$this->validation->set_rules($rules);		
		$fields['cat_name'] = 'Category Name';		
		$this->validation->set_fields($fields);        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');

        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($update_item)) {
        	$customer = $this->item_mgmt_model->get_row_bycond('additional_cats', array('cat_id' => $id));
        	if (isset($customer) && is_array($customer) && count($customer) > 0) {
        		foreach ($customer[0] as $k => $v) {
	                if (isset($this->validation->$k)) 
	                $this->validation->$k = $v;
        	   	}
        	}
        }
		if ($this->validation->run() != false) {			
            foreach($fields as $key => $val) {			
                $update_data[$key] = $this->input->post($key);				
            }
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
                if ($this->item_mgmt_model->update_row('additional_cats', array('cat_id' => $id), $update_data)) {
                    $this->session->set_flashdata('confirm', array('Item Category Updated!'));
                    //echo $this->db->last_query(); exit;
                    redirect('item_mgmt/category_list');
                }    
            } else {
                $this->item_mgmt_model->insert_row('additional_cats', $update_data);
                $this->session->set_flashdata('confirm', array('New Category Added!'));
                redirect('item_mgmt/category_list');				
            }
		}
        $this->load->view('item_mgmt_category_add', $data);
	}
	
	/**
	 * Check category name is already exits or not.
	 */
	function checkcategoryname() {
		$catname = $this->input->post('category');
		$cat_up = $this->input->post('cat_up');
		if(empty($cat_up)) {
			$res = $this->item_mgmt_model->get_row_bycond('additional_cats', array('cat_name' =>$catname));
		} else {
			$cond = array('cat_name' =>$catname, 'cat_id !=' => $cat_up);
			$res = $this->item_mgmt_model->get_row_bycond('additional_cats', $cond);
		}
		if(empty($res)) 
		echo json_encode('success');
		else 
		echo json_encode('fail');
		exit;
	}
    
	/**
	 * Add new quote item here.
	 * @param $update - update quote item if already exits.
	 * @param $id - quote item id
	 * @param $type - table name
	 */
    function add($update = false, $id = false, $type = 'additional') {

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
		
        $data['categories'] = $this->item_mgmt_model->get_category_list();        
		$table_type = 'additional';
        
        if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_item'])) {
        	$customer = $this->item_mgmt_model->get_row_bycond('additional_items', array('itemid' =>$id));
            if (isset($customer) && is_array($customer) && count($customer) > 0) 
            foreach ($customer[0] as $k => $v) {
                if (isset($this->validation->$k)) $this->validation->$k = $v;
            }
        }
		
		if ($this->validation->run() != false) {
            foreach($fields as $key => $val) {
                $update_data[$key] = $this->input->post($key);
            }
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {        
                if ($this->item_mgmt_model->update_row('additional_items', array('itemid' => $id), $update_data)) {
                    $this->session->set_flashdata('confirm', array('Item Details Updated!'));
                    redirect('item_mgmt/add/update/' . $id . '/' . $type);
                }
            } else {
            	$this->item_mgmt_model->insert_row('additional_items', $update_data);
                $this->session->set_flashdata('confirm', array('New Item Added!'));
                redirect('item_mgmt/add/update/' . $this->db->insert_id() . '/' . $type);
            }
		}
        $this->load->view('item_mgmt_add', $data);
    }
	
    /**
     * Delete quote item
     * @param $update
     * @param $id
     * @param $type
     */
	function item_delete($update = false, $id = false, $type = 'additional') {
		if ($this->session->userdata('delete')==1){
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id)) {
				$this->item_mgmt_model->delete_row('additional_items', array('itemid' => $id));
				$this->session->set_flashdata('confirm', array('Item Record Deleted!'));
				redirect('item_mgmt');
			}
		} else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('item_mgmt');
		}
	}
	
	/**
	 * Delete category from additional cats table
	 * @param $id
	 */
	function delete_category($id = false) {
		if ($this->session->userdata('delete')==1){
			$this->item_mgmt_model->delete_row('additional_cats', array('cat_id' => $id));
			$this->session->set_flashdata('confirm', array('Category Record Deleted!'));
			redirect('item_mgmt/category_list');
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('item_mgmt/category_list');
		}	
	}
}
