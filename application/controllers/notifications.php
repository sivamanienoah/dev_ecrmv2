<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notifications extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct()
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('notifications_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    function index($limit = false, $search = FALSE)
    {		
		$data['page_heading'] = 'Manage Notifications';
		
		$data['getAllCrons'] = $this->notifications_model->get_all_crons($search, $this->userdata['userid']);
		// echo "<pre>"; print_r($data['getAllCrons']); exit;
		
        $this->load->view('notify/notifications_view', $data);
    }
	
	function search()
	{
        if (isset($this->input->post['cancel_submit']))
		{
            redirect('notifications/');
        }
		else if ($name = $this->input->post('cust_search'))
		{
            redirect('notifications/index/0/' . rawurlencode($name));
        }
		else
		{
            redirect('notifications/');
        }
        
    }
	
	//for currency type edit
	function crons_edit($update = false, $id = false)
	{
		// echo "<pre>"; print_r($_POST); exit;
		$this->load->library('validation');
        $data = array();
		
		$rules['cron_name'] = "trim|required";

		if((isset($_POST['onscreen_notify_status']) && $_POST['onscreen_notify_status'] == 1) || (isset($_POST['email_notify_status']) && $_POST['email_notify_status'] == 1)){
			$rules['no_of_days'] = "trim|required";
		}
		
		$this->validation->set_rules($rules);
		
		$fields['cron_name'] = 'Cron Name';
		$fields['onscreen_notify_status'] = 'Onscreen Notify Status';
		$fields['email_notify_status'] = 'Email Notify Status';
		$fields['no_of_days'] = 'No. of Days';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_item']))
        {
            $src = $this->notifications_model->get_crons($id, $this->userdata['userid']);
			// echo "<pre>"; print_r($src); exit;
			// $item_data = $this->db->get_where("{$this->cfg['dbpref']}" . '_crons', array('cron_id' => $id));
            // if ($item_data->num_rows() > 0) $src = $item_data->result_array();
            if (isset($src) && is_array($src) && count($src) > 0) foreach ($src[0] as $k => $v)
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
                // update
				unset($update_data['cron_name']);
				$update_data['userid'] = $this->userdata['userid'];
				$update_data['cron_id'] = $id;
				if (($update_data['onscreen_notify_status']=='') && ($update_data['email_notify_status']==''))
				{
					$update_data['no_of_days'] = 0;
				}
				// echo "<pre>"; print_r($update_data); exit;
				$updt_cur = $this->notifications_model->updt_crons($update_data);
				if ($updt_cur)
                {
                    $this->session->set_flashdata('confirm', array('Notifications Updated!'));
                    redirect('notifications');
                }
            }
		}
		$this->load->view('notify/notifications_edit_view', $data);
	}
	
}