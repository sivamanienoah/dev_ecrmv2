<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Package extends crm_controller {
	function Package()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->load->model('package_model');
        $this->load->library('validation');
	}
	function index($limit = 0, $search = false){
		$data['accounts'] = $this->package_model->result_list($limit, $search);
        $this->load->view('package', $data);
	}
	function add($id = false){
		$r=$this->package_model->active();
		if(!empty($id)){
			$account=$this->package_model->get_pack($id);
			$account=$account[0];
			$account['toDB']='update';
			$rules['package_name'] = "trim|required";
		}
		else { 
			$rules['package_name'] = "trim|required|callback_is_available"; 
			$account['toDB']='';$account['package_name']='';$account['package_group']='';$account['type_months']='';
			$account['package_price']='';$account['typeid_fk']='';$account['status']='';
		}
		$account['type']=$r;
		$rules['type_months'] = "trim|numeric";
		$rules['package_price'] = "trim|required|numeric";
		$rules['typeid_fk'] = "trim|required";
		$rules['status'] = "trim|required";
		$rules['duration'] = "trim|required";
		
		$this->validation->set_rules($rules);
		
		$fields['package_name'] = 'Package Name';
		$fields['package_price'] = 'Package Price';
		$fields['typeid_fk'] = 'Package Type';
		$fields['status'] = "status";
		$fields['duration'] = 'Duration';
		$fields['details'] = 'Details';
		$this->validation->set_fields($fields);
		$this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		if ($this->validation->run() == false) {
			$this->load->view('package_add',$account);
		}
		else {
			foreach($fields as $key=>$val)	$data[$key]=$this->input->post($key);
			unset($data['toDB']);
			if ($this->input->post('toDB') == 'update') {
				if ($this->package_model->update_pack($id, $data)) {
                    $this->session->set_flashdata('confirm', array('Package Type Updated!'));
                    redirect('package/');
                }
            } else {
                if ($newid = $this->package_model->insert_pack($data)) {
                    $this->session->set_flashdata('confirm', array('Package Type Added!'));
                    redirect('package/');
                }
            }
		}
	}
	function is_available($package_name){
		$query = $this->db->query("SELECT * FROM ".$this->cfg['dbpref']."package WHERE package_name='{$package_name}'");
		if($query->num_rows()>0) {$this->validation->set_message('is_available', 'Package name already available in database'); return false;}
		else return true;
	}
	
	function type($limit = 0, $search = false){
		$data['accounts'] = $this->package_model->list_result($limit, $search);
        $data['pagination'] = '';
        if ($search == false) {
            $this->load->library('pagination');
            
            $config['base_url'] = $this->config->item('base_url') . 'package/index/';
            $config['total_rows'] = (string) $this->package_model->account_count();
            $config['per_page'] = 20;
			$config['uri_segment'] = 3;
            
            $this->pagination->initialize($config);
            
            $data['pagination'] = $this->pagination->create_links();
        }
		$this->load->view('package_view', $data);
	}
	function type_search(){
		if (isset($_POST['cancel_submit'])) {
            redirect('package/type');
        } else if ($name = $this->input->post('account_search')) {
            redirect('package/type/0/' . $name);
        } else {
            redirect('package/type');
        }
	}
	
    function update($id = false){
		if(!empty($id)){
			$account=$this->package_model->get_package($id);
			$account=$account[0];
			$account['toDB']='update';
			$rules['package_name'] = "trim|required";
		}
		else { 
			$rules['package_name'] = "trim|required|callback_isavailable"; 
			$account['toDB']='';$account['package_name']='';$account['type_months']='';$account['package_flag']='';
		}
		$rules['type_months'] = "trim|numeric";
		$rules['package_flag'] = "trim|required";
		
		$this->validation->set_rules($rules);
		
		$fields['package_name'] = 'Package Name';
		$fields['type_months'] = "Months";
		$fields['package_flag'] = "Flag";
		$this->validation->set_fields($fields);
		$this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		if ($this->validation->run() == false) {
			$this->load->view('package_add_view',$account);
		}
		else {
			foreach($fields as $key=>$val)	$data[$key]=$this->input->post($key);
			unset($data['toDB']);
			if ($this->input->post('toDB') == 'update') {
				
				if ($this->package_model->update($id, $data)) {
					if($this->input->post('package_flag')=='inactive'){ $this->db->query("UPDATE ".$this->cfg['dbpref']."package SET status='inactive' WHERE typeid_fk='{$id}'");}
                    $this->session->set_flashdata('confirm', array('Package Type Updated!'));
                    redirect('package/type');
                }
            } else {
                if ($newid = $this->package_model->insert($data)) {
                    $this->session->set_flashdata('confirm', array('Package Type Added!'));
                    redirect('package/type');
                }
            }
		}
	}
	
	function delete($id = false)
	{	
	if ($this->session->userdata('delete')==1){
		$this->login_model->check_login();
					
			if ($this->package_model->delete($id, $data)) {
				$this->session->set_flashdata('confirm', array('Package Type Deleted!'));
				redirect('package/type');
			}
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('package/type');
		}
	}
	
	function delete_packagename($id = false)
	{
	if ($this->session->userdata('delete')==1){
		$this->login_model->check_login();	
			if ($this->package_model->delete_packagename($id, $data)) {
				$this->session->set_flashdata('confirm', array('Package Type Deleted!'));
				redirect('package');
			}
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
			redirect('package');
		}
	}
	
	function isavailable($package_name){
		$query = $this->db->query("SELECT * FROM ".$this->cfg['dbpref']."package_type WHERE package_name='{$package_name}'");
		if($query->num_rows()>0) {$this->validation->set_message('isavailable', 'Package name already available in database'); return false;}
		else return true;
	}
	function search()
    {
        if (isset($_POST['cancel_submit'])) {
            redirect('package/');
        } else if ($name = $this->input->post('account_search')) {
            redirect('package/index/0/' . $name);
        } else {
            redirect('package/');
        }
    }
	
}
?>