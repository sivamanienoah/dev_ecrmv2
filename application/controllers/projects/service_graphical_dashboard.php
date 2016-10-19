<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
class Service_graphical_dashboard extends crm_controller 
{
	function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->helper('custom');
		$this->load->helper('lead_stage');
		$this->load->helper('url'); 
		$this->load->model('projects/dashboard_model');
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		
	}
	

	public function index()
	{
		if(in_array($this->userdata['role_id'], array('8', '9', '11', '13', '14'))) {
			redirect('project');
		}
		$data  				  = array();
		$data['page_heading'] = "Utilization Cost Dashboard";
		
		// echo "<pre>"; print_R($this->input->post());
		
		$curFiscalYear = getFiscalYearForDate(date("m/d/y"),"4/1","3/31");
		$start_date    = ($curFiscalYear-1)."-04-01";  //eg.2013-04-01
		$end_date  	   = date('Y-m-d'); //eg.2014-03-01
		
		//default billable_month
		$month = date('Y-m-01 00:00:00');

		$data['bill_month'] = $month;
		$data['start_date'] = $start_date;
		$data['end_date']   = $end_date;
		
		$this->db->select('p.practices, p.id');
		$this->db->from($this->cfg['dbpref']. 'practices as p');
		$this->db->where('p.status', 1);
		//BPO practice are not shown in IT Services Dashboard
		$this->db->where_not_in('p.id', 6);
		$pquery = $this->db->get();
		$pres = $pquery->result();
		$data['practice_data'] = $pquery->result();	

		//get values from services dashboard table
		$this->db->select('practice_name, ytd_billable');
		$this->db->from($this->cfg['dbpref']. 'services_graphical_dashboard');
		$sql = $this->db->get();
		$graph_res = $sql->result_array();

		$graph_val = array();
		if(!empty($graph_res)){
			foreach($graph_res as $key=>$val) {
				if($val['practice_name'] == 'Infra Services'){
					continue;
				}
				$graph_id = strtolower($val['practice_name']);
				$graph_id = str_replace(' ', '_', $graph_id);
				// $graph_val[$graph_id] = $val['ytd_billable'];
				$graph_val[$graph_id] = $val;
			}
		}
		$data['graph_val'] = $graph_val;
	
		if($this->input->post("filter")!="") {
			$this->load->view('projects/service_graphical_dashboard_view', $data);
		} else {
			$this->load->view('projects/service_graphical_dashboard', $data);
		}
	}
	
	
}
/* End of dms resource_availability file */