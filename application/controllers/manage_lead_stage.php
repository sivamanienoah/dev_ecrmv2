<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manage_lead_stage extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	function __construct()
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('manage_lead_stage_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
    function index($limit, $search = FALSE)
    {	
        $data['page_heading'] = 'Manage Lead Stage';
		
		//$data['job_categories'] = $this->cfg['job_categories'];
		$data['lead_stage'] = $this->manage_lead_stage_model->get_leadStage($search);
		
        $this->load->view('manage_lead_stage_view', $data); 
    }
	
	function search()
	{
        if (isset($_POST['cancel_submit']))
		{
            redirect('manage_lead_stage/');
        }
		else if ($name = $this->input->post('cust_search'))
		{
            redirect('manage_lead_stage/index/0/' . rawurlencode($name));
        }
		else
		{
            redirect('manage_lead_stage/');
        }
        
    }
	
	//for Lead Source
	function leadStg_add($update = false, $id = false) {
		// echo "<pre>"; print_r($_POST);
		$this->load->library('validation');
        $data = array();
        
		$rules['lead_stage_name'] = "trim|required";
		
		$this->validation->set_rules($rules);
		$fields['lead_stage_name'] = 'Lead Stage';
		$fields['sequence'] = 'Sequence';
		$fields['status'] = 'Status';
		
		$this->validation->set_fields($fields);
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
		
		$data['lead_stage'] = $this->manage_lead_stage_model->get_leadStage($search = false);
		//for status
		$this->db->where('job_status', $id);
		$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		
		if ($update == 'update' && preg_match('/^[0-9]+$/', $id) && !isset($_POST['update_item']))
        {
            $item_data = $this->db->get_where("{$this->cfg['dbpref']}" . 'lead_stage', array('lead_stage_id' => $id));
            if ($item_data->num_rows() > 0) $src = $item_data->result_array();
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
            if ($update_data['status'] == "") {
				if ($data['cb_status']==0) {
					$update_data['status'] = 0;
				} else {
					$update_data['status'] = 1;
				}
			}
            if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
            {
                unset($update_data['sequence']);
				//update
                $this->db->where('lead_stage_id', $id);
                if ($this->db->update("{$this->cfg['dbpref']}" . 'lead_stage', $update_data))
                {
                    $this->session->set_flashdata('confirm', array('Lead Stage Details Updated!'));
                    redirect('manage_lead_stage');
                }
            }
            else
            {	
				$update_data['sequence'] = '0';
				//Get the sequence no.
				$this->db->select('sequence');
				$this->db->order_by("sequence", "DESC");
				$this->db->limit('1');
				$query = $this->db->get($this->cfg['dbpref'].'lead_stage');
				
				if($query->num_rows() > 0) {
					$res = $query->row_array(); //return the row as an associative array
					$update_data['sequence'] = $res['sequence'] + 1;
				}
				
				// echo $this->db->last_query(); print_r($update_data); exit;
                //insert
                $this->db->insert("{$this->cfg['dbpref']}" . 'lead_stage', $update_data);
                $this->session->set_flashdata('confirm', array('New Lead Stage Added!'));
                redirect('manage_lead_stage');
            }
		}
		$this->load->view('manage_lead_stage_add', $data);
	}
	
	function leadStg_delete($update, $id)
	{
		if ($this->session->userdata('delete')==1)
		{
			if ($update == 'update' && preg_match('/^[0-9]+$/', $id))
			{
				$this->db->delete("{$this->cfg['dbpref']}lead_source", array('lead_source_id' => $id));
				$this->session->set_flashdata('confirm', array('Lead Stage Deleted!'));
				redirect('manage_lead_stage_view');
			}
			else {
				$this->session->set_flashdata('login_errors', array("Error Occured!."));
				redirect('manage_lead_stage');
			}
		}
		else {
			$this->session->set_flashdata('login_errors', array("You have no rights to access this page!."));
			redirect('manage_lead_stage');
		}
	}


	
	function ajax_leadstg_list($return = false)
    {
		$this->load->helper('text');
		$this->load->helper('fix_text');
        
        $this->db->order_by('sequence', 'asc');
        $query = $this->db->get($this->cfg['dbpref'] . 'lead_stage');
        
        if ($query->num_rows() > 0)
        {
            $html = '';
            foreach ($query->result_array() as $row)
            {
				if(!empty($row['lead_stage_name'])) {
					if ($row['status'] == 1) $stat = "Active"; else $stat = "Inactive";
					if ($this->session->userdata('edit')==1)
						$edit = "<a href='manage_lead_stage/leadStg_add/update/".$row['lead_stage_id']."'>Edit</a>";
					else 
						$edit = "Edit";
					if ($this->session->userdata('delete')==1)
						$dele = "<a href='javascript:void(0)' onclick='checkStatus(".$row['lead_stage_id'].");'>Delete</a>";
					else 
						$dele = "Delete";
					$html .= '<li id="leadst-' . $row['lead_stage_id'] . '"><table cellpadding="0" cellspacing="0" class="data-table btm-none" width="100%"><tr><td class="lead-stage" width="40%">' . nl2br(cleanup_chars(ascii_to_entities($row['lead_stage_name']))) . '</td><td width="60px">' . $stat . '</td><td width="55px">'. $edit .' | '. $dele .'</td><td class="dialog-err" id="errmsg-' . $row['lead_stage_id'] . '"></td></tr></table></li>';
				} else {
					$html .= '<li id="leadst-' . $row['lead_stage_id'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" colspan="2">' . nl2br(cleanup_chars(ascii_to_entities($row['lead_stage_name']))) . '</td></tr></table></li>';
				}
            }
            $json['error'] = false;
            $json['html'] = $html;
        }
        else
        {
            $json['error'] = false;
            $json['html'] = '';
        }
		
        if ($return)
        {
            return json_encode($json);
        }
        else
        {
            echo json_encode($json);
        }
        
    }
	
	/*
     * saves the new positions of lead stage
     * for a given job
     */
    function ajax_save_lead_sequence()
    {
		//print_r($_POST);
        $errors = '';
        if (!isset($_POST['leadst']) || !is_array($_POST['leadst']))
        {
            $errors[] = 'Incorrect order format!';
        }
        
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
            $when = '';
            foreach ($_POST['leadst'] as $k => $v)
            {
                $when .= "WHEN {$v} THEN {$k} \n";
            }
            
            $sql = "UPDATE {$this->cfg['dbpref']}lead_stage SET `sequence` = CASE `lead_stage_id`
                    {$when}
                    ELSE `sequence` END";
            
            if ($this->db->query($sql))
            {
                $json['error'] = false;
                echo json_encode($json);
            }
            else
            {
                $json['error'] = true;
                $json['errormsg'] = 'Database error occured!';
                echo json_encode($json);
            }
        }
    }
	
	/**
	 * Edits an existing Lead Stage
	 */
	function ajax_edit_leadstg()
	{
        $errors = '';
        if (trim($_POST['lead_stage_name']) == '')
        {
			$errors[] = 'You must provide a Lead Stage Name!';
        }
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
			//for status
			$this->db->where('job_status', $_POST['lead_stage_id']);
			$data['cb_status'] = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
			
			if ($_POST['status'] == "") {
				if ($data['cb_status']==0) {
					$ins['status'] = 0;
				} else {
					$ins['status'] = 1;
				}
			} else {
				$ins['status'] = $_POST['status'];
			}
			
			$ins['lead_stage_name'] = $_POST['lead_stage_name'];
			
			$this->db->where('lead_stage_id', $_POST['lead_stage_id']);
			if ($this->db->update($this->cfg['dbpref'] . 'lead_stage', $ins))
			{
				echo "{error:false}";
				//echo $this->db->last_query();
			}
			else
			{
				echo "{error:true, errormsg:'Update failed!'}";
			}
        }
    }
	
	 /*
     * deletes the Lead Stage
     * @return echo json string
     */
    function ajax_delete_leadStg()
    {	
		$errors = '';
		if ($this->session->userdata('delete') == 1) {
			if (!isset($_POST['lead_stage_id']) || !preg_match('/^[0-9]+$/', $_POST['lead_stage_id']))
			{
				$errors[] = 'A valid Lead Stage ID is not supplied';
			}
			if (is_array($errors))
			{
				$json['error'] = true;
				$json['errormsg'] = implode("\n", $errors);
				echo json_encode($json);
			}
			else
			{
				$this->db->where('job_status', $_POST['lead_stage_id']);
				$stat = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
				if ($stat==0)
				{
					$this->db->where('lead_stage_id', $_POST['lead_stage_id']);
					if ( $this->db->delete($this->cfg['dbpref'] . 'lead_stage') )
					{
						//$this->ajax_quote_items($jobid[0]['jobid_fk']);
						echo "{error:false}";
					}
					else
					{
						$json['error'] = true;
						$json['errormsg'] = 'Database error! Lead Stage Not deleted.';
						echo json_encode($json);
					}
				}
				else
				{
					$json['error'] = true;
					$json['errormsg'] = 'One or more Lead Stage currently assigned for some leads. This cannot be deleted.';
					echo json_encode($json);
				}
			}
		} else {
			$json['error'] = true;
			$json['errormsg'] = 'You have no rights to access this page.';
			echo json_encode($json);
		}
    }
	
	function ajax_check_status_lead_stage() {
		$id = $_POST['data'];
		$this->db->where('job_status', $id);
		$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
		$res = array();
		if($query == 0) {
			$res['html'] .= "YES";
		} else {
			$res['html'] .= "NO";
		}
		echo json_encode($res);
		exit;
	}

}