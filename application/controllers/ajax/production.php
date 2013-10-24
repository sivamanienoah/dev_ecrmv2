<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Production extends crm_controller {

	public $cfg;
	
	public function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('text');
		$this->load->library('email');
		$this->email->initialize($cfg);
		$this->email->set_newline("\r\n");
	}
    
    public function index()
    {
		
    }
	
	
	
	public function set_project_status_date($jobid, $date_status, $date)
	{	//echo $jobid . ' ' . $date_status . ' ' . $date; exit;
		$data['error'] = FALSE;

		$timestamp = strtotime($date);
		
		if ($date_status != 'start' && $date_status != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($date_status == 'start')
			{	
				$this->db->where('jobid',$jobid);
				$this->db->where('date_due <', date('Y-m-d H:i:s', $timestamp));
				$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
				if($query == 1) { 
						$data['error'] = 'Planned Project Start Date Must be Equal or Earlier than the Planned Project End Date!';
				} else {
				$update['date_start'] = date('Y-m-d H:i:s', $timestamp);
				$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
				}
			}
			else
			{	
				if ($date_status == 'end') {
					$dt = date('Y-m-d H:i:s', $timestamp);
					$chk_dt = $this->db->query(" SELECT * FROM (`".$this->cfg['dbpref']."jobs`) WHERE `jobid` = '".$jobid."' ");
					$check_dt = $chk_dt->row_array();
					//echo $check_dt['date_start']; exit;
					if (isset($check_dt['date_start'])) {
						if($check_dt['date_start'] > $dt) {
						$data['error'] = 'Planned Project End Date Must be Equal or Later than the Planned Project Start Date!';
						} else {
						$update['date_due'] = $dt;
						$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
						}
					} else {
						$data['error'] = 'Planned Project Start Date Must be Filled!';
					}
				}
			}
			//$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
		}
		echo json_encode($data);
	}
	
	public function actual_set_project_status_date($jobid, $date_status, $date)
	{
		//echo $jobid . ' ' . $date_status . ' ' . $date; exit;
		
		$data['error'] = FALSE;
		
		$timestamp = strtotime($date);
		
		if ($date_status != 'start' && $date_status != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($date_status == 'start')
			{	
				/* $this->db->where('jobid',$jobid);
				$this->db->where('date_start >', date('Y-m-d H:i:s', $timestamp));
				$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
				if($query == 1) { 
					$data['error'] = 'Actual Project Start Date Must be Equal or Later than the Planned Project Start Date!';
				} else {
					$update['actual_date_start'] = date('Y-m-d H:i:s', $timestamp);
					$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
				} */
				
				$dt = date('Y-m-d H:i:s', $timestamp);
				$chk_act_dt = $this->db->query(" SELECT * FROM (`".$this->cfg['dbpref']."jobs`) WHERE `jobid` = '".$jobid."' ");
				$check_act_dt = $chk_act_dt->row_array();
				// echo $check_act_dt['actual_date_due']; 
				// echo"<br />"; echo $dt; exit;
				if (isset($check_act_dt['date_start'])) {
					if($check_act_dt['date_start'] > $dt) {
						$data['error'] = 'Actual Project Start Date Must be Equal or Later than the Planned Project Start Date!';
					} else {
						if (isset($check_act_dt['actual_date_due'])) {
							if ($check_act_dt['actual_date_due'] < $dt) {
								$data['error'] = 'Actual Project Start Date Must be Equal or Earlier than the Actual Project End Date!';
							}
						} else {
						//echo "update";
							$update['actual_date_start'] = $dt;
							$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
						}
					}
				} else {
					$data['error'] = 'Planned Project Start Date Must be Filled!';
				}
				
			}
			else
			{	
				if ($date_status == 'end') {
					/* $this->db->where('jobid',$jobid);
					$this->db->where('actual_date_start >', date('Y-m-d H:i:s', $timestamp));
					$query = $this->db->get($this->cfg['dbpref'].'jobs')->num_rows();
					if($query == 1) { 
						$data['error'] = 'Actual Project End Date Must be Equal or Later than the Actual Project Start Date!';
					} else {
						$update['actual_date_due'] = date('Y-m-d H:i:s', $timestamp);
						$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
					} */
					
					$dt = date('Y-m-d H:i:s', $timestamp);
					$chk_act_end_dt = $this->db->query(" SELECT * FROM (`".$this->cfg['dbpref']."jobs`) WHERE `jobid` = '".$jobid."' ");
					$check_act_end_dt = $chk_act_end_dt->row_array();
					if (isset($check_act_end_dt['actual_date_start'])) {
						if($check_act_end_dt['actual_date_start'] > $dt) {
							$data['error'] = 'Actual Project End Date Must be Equal or Later than the Actual Project Start Date!';
						} else {
							$update['actual_date_due'] = $dt;
							$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
						}
					} else {
						$data['error'] = 'Actual Project Start Date Must be Filled!';
					}
				}		
			}
			//$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
		}
		echo json_encode($data);
	}
	
	public function set_proposal_date($jobid, $date_status, $date)
	{
		$data['error'] = FALSE;
		
		$timestamp = strtotime($date);
		
		if ($date_status != 'start' && $date_status != 'end')
		{
			$data['error'] = 'Invalid date status supplied!';
		}
		else if ( ! $timestamp)
		{
			$data['error'] = 'Invalid date supplied!';
		}
		else
		{
			if ($date_status == 'start')
			{
				$update['proposal_expected_date'] = date('Y-m-d H:i:s', $timestamp);
			}
			else
			{
				$update['proposal_sent_date'] = date('Y-m-d H:i:s', $timestamp);
			}
			
			$this->db->update($this->cfg['dbpref'].'jobs', $update, array('jobid' => $jobid));
			
		}
		
		echo json_encode($data);
	}
	
}
