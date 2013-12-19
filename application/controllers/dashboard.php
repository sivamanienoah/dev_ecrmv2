<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends crm_controller {
	var $cfg;
	var $userdata;

	/*
	*Method constructor
	*
	*/
	function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->load->model('dashboard_model');
		$this->load->model('report/report_lead_region_model');
		$this->load->model('regionsettings_model');
		$this->load->model('welcome_model');
		$this->load->helper('custom_helper');
		$this->load->helper('text_helper');
		$this->load->helper('lead_stage_helper');
		$this->userdata   = $this->session->userdata('logged_in_user');
		$this->pjt_stg 	  = array(0,1,2,3);
		$this->pjt_stages = @implode("','", $this->pjt_stg);
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
	}
	
	/*
	*Method index
	*
	*/
	function index() {
		$this->load->helper('text');
		$this->load->helper('fix_text');
		
		$userdata = $this->session->userdata('logged_in_user');
		$data  	  = array();
		$filter   = real_escape_array($this->input->post());
		if (isset($filter['advance'])) {
			$data['toggle_stat'] = 1;
			$filter 			 = $filter;
			$data['filter'] 	 = $filter;
		} 
		$cusId = $this->level_restriction();
		
		//Current Pipeline leads
		$data['getLeads'] = $this->dashboard_model->getTotLeads($cusId, $filter);
		
		//Leads by RegionWise - Start here
		$data['getLeadByReg'] = $this->dashboard_model->getLeadsByReg($cusId, $filter);

		$leads 		   = $data['getLeadByReg']['res'];
    	$total_leads   = $data['getLeadByReg']['num'];
		$lead_reg 	   = array();
		// currency_convert();
		$rates 		   = $this->get_currency_rates();
		$data['rates'] = $this->get_currency_rates();
		if($total_leads>0)
    	{	
    		if ($userdata['level'] == 1) {
				if ((!empty($filter['regionname'])) && (empty($filter['countryname'])) && (empty($filter['statename'])) && (empty($filter['locname']))) {
					foreach ($leads as $lead) {
						$country_name			  = trim($lead->country_name);
						$lead_reg[$country_name]  = empty($lead_reg[$country_name])?0:$lead_reg[$country_name];
						$lead_reg[$country_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
					}
				} else if ((!empty($filter['countryname'])) && (empty($filter['statename'])) && (empty($filter['locname']))) {
					foreach ($leads as $lead) {
						$state_name 		    = trim($lead->state_name);
						$lead_reg[$state_name]  = empty($lead_reg[$state_name])?0:$lead_reg[$state_name];
						$lead_reg[$state_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
					}
				} else if (!empty($filter['statename'])) {
					foreach ($leads as $lead) {
						$location_name 			   = trim($lead->location_name);
						$lead_reg[$location_name]  = empty($lead_reg[$location_name])?0:$lead_reg[$location_name];
						$lead_reg[$location_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
					}
				} else {
					foreach ($leads as $lead) {
						$region_name			 = trim($lead->region_name);
						$lead_reg[$region_name]  = empty($lead_reg[$region_name])?0:$lead_reg[$region_name];
						//$lead_reg[$region_name] += $lead->expect_worth_amount;
						$lead_reg[$region_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
					}
				}
			} else {
				switch($userdata['level']) {
					case 2:
						if ((!empty($filter['countryname'])) && (empty($filter['statename'])) && (empty($filter['locname']))) {
							foreach ($leads as $lead) {
								$state_name 		    = trim($lead->state_name);
								$lead_reg[$state_name]  = empty($lead_reg[$state_name])?0:$lead_reg[$state_name];
								$lead_reg[$state_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
							}
						} else if ((!empty($filter['statename']))) {
							foreach ($leads as $lead) {
								$location_name 			   = trim($lead->location_name);
								$lead_reg[$location_name]  = empty($lead_reg[$location_name])?0:$lead_reg[$location_name];
								$lead_reg[$location_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
							}
						} else {
							foreach ($leads as $lead) {
								$country_name			  = trim($lead->country_name);
								$lead_reg[$country_name]  = empty($lead_reg[$country_name])?0:$lead_reg[$country_name];
								$lead_reg[$country_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
							}
						}
					break;
					case 3:
						if ((!empty($filter['statename']))) {
							foreach ($leads as $lead) {
								$location_name 			   = trim($lead->location_name);
								$lead_reg[$location_name]  = empty($lead_reg[$location_name])?0:$lead_reg[$location_name];
								$lead_reg[$location_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
							}
						} else {
							foreach ($leads as $lead) {
								$state_name 		    = trim($lead->state_name);
								$lead_reg[$state_name]  = empty($lead_reg[$state_name])?0:$lead_reg[$state_name];
								$lead_reg[$state_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
							}
						}			
					break;
					case 4:
					case 5:
						foreach ($leads as $lead) {
							$location_name 			   = trim($lead->location_name);
							$lead_reg[$location_name]  = empty($lead_reg[$location_name])?0:$lead_reg[$location_name];
							$lead_reg[$location_name] += $this->conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$this->default_cur_id]);
						}
					break;
				}
			}
    	}
		$data['LeadsRegionwise'] 		= $lead_reg; //Results for leads by regionwise.
   		$data['LeadsRegionwiseTot'] 	= $total_leads; //count of leads
		//Leads  by RegionWise - End here
		$data['getLeadByOwner'] 		= $this->dashboard_model->getLeadsByOwner($cusId, $filter);
		$data['getLeadByAssignee'] 		= $this->dashboard_model->getLeadsByAssignee($cusId, $filter);
		$data['getLeadIndicator'] 		= $this->dashboard_model->getLeadsIndicator($cusId, $filter);
		$data['getLeastLeadCount'] 		= $this->dashboard_model->getLeastLeadsCount($cusId, $filter);
		$data['getCurrentActivityLead'] = $this->dashboard_model->getCurrentActivityLeads($isSelect = 7, $cusId, $filter);
		$data['getLeadAging']   		= $this->dashboard_model->getLeadsAging($cusId, $filter);
		//for Closed Opportunities
		$data['getClosedJobid']			= $this->dashboard_model->getClosedJobids($cusId, $filter);
		$closedMonthArr = array();
		$monthArr 						= array();
		$totalSum						= 0;
		foreach ($data['getClosedJobid'] as $value) {
			$value['expect_worth_amount'] = $this->conver_currency($value['expect_worth_amount'], $rates[$value['expect_worth_id']][$this->default_cur_id]);
			$sql = "SELECT lead_id, dateofchange FROM {$this->cfg['dbpref']}lead_status_history WHERE lead_id = '".$value['lead_id']."' AND changed_status = 4 ORDER BY dateofchange DESC LIMIT 1";
			$rows = $this->db->query($sql);
			$res_query = $rows->row_array();
			if(!empty($res_query)) {
				$mon = date("M" ,strtotime($res_query['dateofchange']));
				// echo $mon."=>".$value['expect_worth_amount'];
				$totalSum = $totalSum + $value['expect_worth_amount'];
				if (in_array($mon, $monthArr)) {
					$expect_worth_amount = $closedMonthArr[$mon] + $value['expect_worth_amount'];
				} else {
					$expect_worth_amount = $value['expect_worth_amount'];
				}
				$closedMonthArr[$mon] 	 = $expect_worth_amount;
				$monthArr[] 			 = $mon;
			}  
		}

		$data['totClosedOppor'] = $totalSum;
		$data['getClosedOppor'] = $closedMonthArr;
		
		//for lead source & service requirement.
		$data['get_Lead_Source'] = $this->dashboard_model->getLeadSource($cusId, $filter);
		$data['get_Service_Req'] = $this->dashboard_model->getServiceReq($cusId, $filter);
		
		//For Tasks & Projects access - Start here (for filter also)
		$data['lead_stage']  = $this->welcome_model->get_lead_stage();
		$data['customers']   = $this->welcome_model->get_customers();
		$leadowner 			 = $this->db->query("SELECT userid, first_name FROM ".$this->cfg['dbpref']."users order by first_name");
		$data['lead_owner']  = $leadowner->result_array(); 
		$data['regions'] 	 = $this->regionsettings_model->region_list();
		$data['serv_requ'] 	 = $this->dashboard_model->get_serv_req();
		$data['lead_sourc']  = $this->dashboard_model->get_lead_sources();
		$data['pm_accounts'] = array();
		//Here "WHERE" condition used for Fetching the Project Managers.
		$users 				 = $this->db->get_where($this->cfg['dbpref'] . 'users',array('role_id'=>3));
		if ($users->num_rows() > 0)
		{
			$data['pm_accounts'] = $users->result_array();
		}
		$taskSql				 = $this->db->query("SELECT `".$this->cfg['dbpref']."tasks`.`created_by` FROM `".$this->cfg['dbpref']."tasks`,`".$this->cfg['dbpref']."users` WHERE `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`");	
		$data['created_by']	   = $taskSql->result_array();	
		$data['user_accounts'] = array();
		$users = $this->db->get($this->cfg['dbpref'] . 'users');
		if ($users->num_rows() > 0)
		{
			$data['user_accounts'] = $users->result_array();
		}
		//For Tasks access - End here
		$this->load->view('dashboard_view', $data);
    }
	
	/*
	*method : get_currency_rates
	*/
	public function get_currency_rates()
	{
		$currency_rates = $this->report_lead_region_model->get_currency_rate();
    	$rates 			= array();
    	if(!empty($currency_rates)){
    		foreach ($currency_rates as $currency)
    		{
    			$rates[$currency->from][$currency->to] = $currency->value;
    		}
    	}
    	return $rates;
	}
	
	public function conver_currency($amount,$val)
	{
		return round($amount*$val);
	}
	
	public function showLeadsDetails() 
	{
		$res  				   = real_escape_array($this->input->post());
		$filters			   = array();
		$filters['stge'] 	   = $res['stge'];
		$filters['cust_id']    = $res['cust_id'];
		$filters['ownr_id']    = $res['ownr_id'];
		$filters['assg_id']    = $res['assg_id'];
		$filters['reg_id']     = $res['reg_id'];
		$filters['cntry_id']   = $res['cntry_id'];
		$filters['stet_id']	   = $res['stet_id'];
		$filters['locn_id']    = $res['locn_id'];
		$filters['servic_req'] = $res['servic_req'];
		$filters['lead_sour']  = $res['lead_sour'];
		$filters['lead_indic'] = $res['lead_indic'];
		
		$type 				   = $res['type']; 
		$data 				   = $res['data'];
		
		$cusId 		= $this->level_restriction();
		$res 		= array();
		$rates 		= $this->get_currency_rates();
		$lead_stage = explode("(",$data[0]);
		// echo $type . " " . $lead_stage[0]; exit;
		switch($type) 
		{
			case "funnel":
				$heading			   = "Current Pipeline Leads";
				$data['getLeadDetail'] = $this->dashboard_model->getLeadsDetails(trim($lead_stage[0]), $cusId, $filters);
			break;
			case "pie1":
				$heading 			   = "Leads for - ".$lead_stage[0];
				$data['getLeadDetail'] = $this->dashboard_model->getRegionLeadsDetails(trim($lead_stage[0]), $cusId, $filters);
			break;
			case "pie2":
				$heading			   = "Leads for - ".$lead_stage[0];
				$data['getLeadDetail'] = $this->dashboard_model->getLeadsDetails_pie2(trim($lead_stage[0]), $cusId, $filters);
			break;
			case "pie3":
				$heading			   = "Leads for - ".$lead_stage[0];
				$data['getLeadDetail'] = $this->dashboard_model->getLeadsDetails_pie3(trim($lead_stage[0]), $cusId, $filters);
			break;
		}
		$res['html'] .= '<div class="dash-section dash-section1"><h5>'.$heading.'</h5><div class="grid-close"></div></div>';
		$res['html'] .= "<div class='dashbrd'>";
	
		switch($type) 
		{
			case "funnel":
				$res['html'] .= '<a id="current-pipeline-export" class="export-btn" name="'.$lead_stage[0].'">Export to Excel</a>';
				$res['html'] .= "<input id='lead-type-name' type='hidden' value='".$type."'/>";	
				$dt_id = "example_funnel";
			break;
			case "pie1":
				$res['html'] .= '<a id="leads-by-region-export" class="export-btn" name="'.$lead_stage[0].'">Export to Excel</a>';
				$res['html'] .= "<input id='lead-by-region' type='hidden' value='".$type."'/>";
				$dt_id = "example_pie1";
			break;
			case "pie2":
				$res['html'] .= '<a id="leads-by-leadsource-export" class="export-btn" name="'.$lead_stage[0].'">Export to Excel</a>';
				$res['html'] .= "<input id='lead-by-leadsource' type='hidden' value='".$type."'/>";
				$dt_id = "example_pie2";
			break;
			case "pie3":
				$res['html'] .= '<a id="leads-by-service-req-export" class="export-btn" name="'.$lead_stage[0].'">Export to Excel</a>';
				$res['html'] .= "<input id='lead-by-service-req' type='hidden' value='".$type."'/>";
				$dt_id = "example_pie3";
			break;
		}	
		$res['html'] .= '<table cellspacing="0" id="'.$dt_id.'" class="dashboard-heads" cellpadding="10px;" border="0" width="100%"><thead><tr><th width=62px;>Lead No.</th><th width=210px;>Lead Title </th><th width=145px;>Customer</th><th width=145px;>Lead Owner</th><th width=145px;>Lead Assignee</th><th width=105px;>Lead Indicator</th><th width=85px;>Expected Worth ('.$this->default_cur_name.')</th><thead><tbody role="alert" aria-live="polite" aria-relevant="all">';
		if (isset($data['getLeadDetail']) && count($data['getLeadDetail'])) :
			foreach($data['getLeadDetail'] as $leadDet) 
			{
			    $amt_converted = $this->conver_currency($leadDet['expect_worth_amount'],$rates[$leadDet['expect_worth_id']][$this->default_cur_id]);
				$res['html'] .='<tr>
								<td><a href="'.base_url().'welcome/view_quote/'.$leadDet['lead_id'].'" target="_blank">'.$leadDet['invoice_no'].'</a></td>
								<td><a href="'.base_url().'welcome/view_quote/'.$leadDet['lead_id'].'" target="_blank">'.character_limiter($leadDet['lead_title'], 35).'</a></td>
								<td>'.$leadDet['first_name'].' '.$leadDet['last_name'].'</td>
								<td>'.$leadDet['owrfname'].' '.$leadDet['owrlname'].'</td>
								<td>'.$leadDet['assifname'].' '.$leadDet['assilname'].'</td>
								<td>'.$leadDet['lead_indicator'].'</td>
								<td text align="right">'.number_format($amt_converted, 2, '.', '').'</td>
								</tr>';
			}		
		endif;
		$res['html'] .= '</tbody>';
		$res['html'] .= '<tfoot><tr><td text align=right colspan="6">Total:</td><td align="right"></td></tr></tfoot>';
		$res['html'] .= '</table>';
		$res['html'] .= '<div class="clear"></div></div>';
		echo json_encode($res);
		exit;
	}
	
	/* Get lead_id, lead title, region, lead owner, lead assigned to, customer, */
	public function getLeadDependency()
	{
		
		$res  				   = real_escape_array($this->input->post());
		$filters			   = array();
		$filters['stge'] 	   = $res['stge'];
		$filters['cust_id']    = $res['cust_id'];
		$filters['ownr_id']    = $res['ownr_id'];
		$filters['assg_id']    = $res['assg_id'];
		$filters['reg_id']     = $res['reg_id'];
		$filters['cntry_id']   = $res['cntry_id'];
		$filters['stet_id']	   = $res['stet_id'];
		$filters['locn_id']    = $res['locn_id'];
		$filters['servic_req'] = $res['servic_req'];
		$filters['lead_sour']  = $res['lead_sour'];
		$filters['lead_indic'] = $res['lead_indic'];

		$userid 			   = $res['userid']; 
		$username 			   = $res['user_name'];
		
		$cusId 							= $this->level_restriction();
		$data['getLeadOwnerDependence'] = $this->dashboard_model->getLeadOwnerDependencies($userid, $cusId, $filters);
		$lead_det 						= array(); 
		$rates 	 						= $this->get_currency_rates();
		$lead_table_output  = '';
		$lead_table_output .= '<div class="dash-section dash-section1"><h5 id="lead-owner-scroll">Lead Owner Opportunities - '.urldecode($username).'</h5><div class="grid-close"></div></div>';
        $lead_table_output .= "<div class='dashbrd charts-info-block'>";	
        $lead_table_output .= "<input id='lead-owner-username' type='hidden' value='".$username."'/>";			
		$lead_table_output .= '<a id="lead-ownner-export" class="export-btn">Export to Excel</a>';
		$lead_table_output .=  '<table name="'.$userid.'" cellspacing="0" id="lead-dependency-table" class="dashboard-heads" cellpadding="10px;" border="0" width="100%"><thead><tr><th>Lead No.</th><th>Lead Title </th><th>Customer</th><th>Lead Owner</th><th>Lead Assignee</th><th>Lead Indicator</th><th>Expected Worth ('.$this->default_cur_name.')</th><thead><tbody role="alert" aria-live="polite" aria-relevant="all">';
		foreach($data['getLeadOwnerDependence']->result() as $lead_info)
		{
			$lead_det['invoice_no']   		 = $lead_info->invoice_no;
			$lead_det['lead_id'] 			 = $lead_info->lead_id;
			$lead_det['lead_title'] 		 = $lead_info->lead_title;
			$lead_det['owrfirst_name'] 		 = $lead_info->ownrfname;	
			$lead_det['usrfname'] 			 = $lead_info->usrfname;
			$lead_det['cflname'] 			 = $lead_info->cfname.' '.$lead_info->clname;		
			$lead_det['expect_worth_amount'] = $lead_info->expect_worth_name." ".$lead_info->expect_worth_amount;	
			$lead_det['lead_indicator'] 	 = $lead_info->lead_indicator;	
			$amt_converted = $this->conver_currency($lead_info->expect_worth_amount,$rates[$lead_info->expect_worth_id][$this->default_cur_id]);
			$lead_table_output .=  "<tr><td><a href='".base_url()."welcome/view_quote/".$lead_det['lead_id']."' target='_blank'>".$lead_det['invoice_no']."</a></td>
			<td><a href='".base_url()."welcome/view_quote/".$lead_det['lead_id']."' target='_blank'>".character_limiter($lead_det['lead_title'], 35)."</a></td>
			<td>".$lead_det['cflname']."</td><td>".$lead_det['owrfirst_name']."</td>
			<td>".$lead_det['usrfname']."</td><td>". $lead_det['lead_indicator'] ."</td><td text align=right>".number_format($amt_converted, 2, '.', '') ."</td>
			</tr>";
		}
		$lead_table_output .=  "</tbody>";
		$lead_table_output .=  '<tfoot><tr><td text align=right colspan="6">Total:</td><td align="right"></td></tr></tfoot>';
		$lead_table_output .=  "</table>";
		$lead_table_output .=  "<div class='clear'></div>";
		$lead_table_output .=  "</div>";
		echo json_encode($lead_table_output);		
	}
	
	public function getLeadsCurrentActivity($lead_id, $leadname) 
	{
		$data['getLeadOwnerDependence'] = $this->dashboard_model->getCurrentLeadActivity($lead_id);
		$lead_det = array(); 
		$rates = $this->get_currency_rates();
		$lead_table_output  = '';
        $lead_table_output .= '<div class="dash-section dash-section1"><h5 id="lead-owner-scroll">Currently Activities Leads - '.character_limiter(urldecode($leadname), 35).'</h5><div class="grid-close"></div></div>';
		$lead_table_output .= "<div class='dashbrd charts-info-block'>";	
		$lead_table_output .= '<a id="lead-current-activity-export" class="export-btn export-btn1" >Export to Excel</a>';
		$lead_table_output .= "<input id='lead-no' type='hidden' name='".$leadname."' value='".$lead_id."'/>";
		$lead_table_output .=  '<table cellspacing="0" id="leads-current-activity-table" class="dashboard-heads" cellpadding="10px;" border="0" width="100%"><thead><tr><th>Lead No.</th><th>Lead Title </th><th>Customer</th><th>Lead Owner</th><th>Lead Assignee</th><th>Lead Indicator</th><th>Expected Worth ('.$this->default_cur_name.')</th><thead><tbody role="alert" aria-live="polite" aria-relevant="all">';
		foreach($data['getLeadOwnerDependence']->result() as $lead_info)
		{
			$lead_det['invoice_no'] 		 = $lead_info->invoice_no;
			$lead_det['lead_id']			 = $lead_info->lead_id;
			$lead_det['lead_title'] 		 = $lead_info->lead_title;
			$lead_det['owrfirst_name'] 	 	 = $lead_info->ownrfname;	
			$lead_det['usrfname'] 			 = $lead_info->usrfname;
			$lead_det['cflname'] 			 = $lead_info->cfname.' '.$lead_info->clname;		
			$lead_det['expect_worth_amount'] = $lead_info->expect_worth_name." ".$lead_info->expect_worth_amount;	
			$lead_det['lead_indicator']		 = $lead_info->lead_indicator;	
			$amt_converted 	= $this->conver_currency($lead_info->expect_worth_amount,$rates[$lead_info->expect_worth_id][$this->default_cur_id]);
			$lead_table_output .=  "<tr>
			<td><a href='".base_url()."welcome/view_quote/".$lead_det['lead_id']."' target='_blank'>".$lead_det['invoice_no']."</a></td>
			<td><a href='".base_url()."welcome/view_quote/".$lead_det['lead_id']."' target='_blank'>".character_limiter($lead_det['lead_title'], 35). "</a></td>
			<td>".$lead_det['cflname']."</td>
			<td>".$lead_det['owrfirst_name']."</td>
			<td>".$lead_det['usrfname']."</td>
			<td>".$lead_det['lead_indicator']."</td>
			<td text align=right>". number_format($amt_converted, 2, '.', '')."</td>
			</tr>";
		}
		$lead_table_output .=  "</tbody>";
		$lead_table_output .=  '<tfoot><tr><td text align=right colspan="6">Total:</td><td align="right"></td></tr></tfoot>';
		$lead_table_output .=  "</table>";
		$lead_table_output .=  "<div class='clear'></div>";
		$lead_table_output .=  "</div>";
		echo $lead_table_output;	
	}
	
	public function getLeadAssigneeDependency()
	{
		$res  				   = real_escape_array($this->input->post());
		$filters			   = array();
		$filters['stge'] 	   = $res['stge'];
		$filters['cust_id']    = $res['cust_id'];
		$filters['ownr_id']    = $res['ownr_id'];
		$filters['assg_id']    = $res['assg_id'];
		$filters['reg_id']     = $res['reg_id'];
		$filters['cntry_id']   = $res['cntry_id'];
		$filters['stet_id']	   = $res['stet_id'];
		$filters['locn_id']    = $res['locn_id'];
		$filters['servic_req'] = $res['servic_req'];
		$filters['lead_sour']  = $res['lead_sour'];
		$filters['lead_indic'] = $res['lead_indic'];
		
		$userid 			   = $res['userid']; 
		$username 			   = $res['user_name'];
	
		$cusId = $this->level_restriction();
		$data['getLeadOwnerDependence'] = $this->dashboard_model->getLeadAssigneeDependencies($userid, $cusId, $filters);
		$lead_det = array(); 
		$rates 	  = $this->get_currency_rates();
		$assignee_table_output  = '';
		$assignee_table_output .= '<div class="dash-section dash-section1"><h5 id="lead-assignee-scroll">Lead Assignee Opportunities - '.urldecode($username).'</h5><div class="grid-close"></div></div>';
        $assignee_table_output .= "<div class='dashbrd charts-info-block'>";
		$assignee_table_output .= "<input id='lead-assignee-username' type='hidden' value='".$username."'/>";		
		$assignee_table_output .= '<a id="lead-assignee-export" class="export-btn">Export to Excel</a>';
		$assignee_table_output .=  '<table name="'.$userid.'" cellspacing="0" id="lead-assignee-table" class="dashboard-heads" cellpadding="10px;" border="0" width="100%"><thead><tr><th>Lead No.</th><th>Lead Title </th><th>Customer</th><th>Lead Owner</th><th>Lead Assignee</th><th>Lead Indicator</th><th>Expected Worth ('.$this->default_cur_name.')</th></thead><tbody role="alert" aria-live="polite" aria-relevant="all">';
		foreach($data['getLeadOwnerDependence']->result() as $lead_info)
		{
			$lead_det['lead_id'] 		= $lead_info->lead_id;
			$lead_det['invoice_no'] 	= $lead_info->invoice_no;
			$lead_det['lead_title'] 	= $lead_info->lead_title;
			$lead_det['owrfirst_name'] 	= $lead_info->ownrfname;	
			$lead_det['usrfname'] 		= $lead_info->usrfname;
			$lead_det['cflname'] 		= $lead_info->cfname.' '.$lead_info->clname;
			$lead_det['lead_indicator'] = $lead_info->lead_indicator;	
			$amt_converted = $this->conver_currency($lead_info->expect_worth_amount,$rates[$lead_info->expect_worth_id][$this->default_cur_id]);
			$assignee_table_output .=  "<tr>
			<td><a href='".base_url()."welcome/view_quote/".$lead_det['lead_id']."' target='_blank'>".$lead_det['invoice_no']."</a></td>
			<td><a href='".base_url()."welcome/view_quote/".$lead_det['lead_id']."' target='_blank'>".character_limiter($lead_det['lead_title'], 35)."</a></td>
			<td>".$lead_det['cflname']."</td>
			<td>".$lead_det['owrfirst_name']."</td>
			<td>".$lead_det['usrfname']."</td>
			<td>". $lead_det['lead_indicator']."</td>
			<td text align=right>".number_format($amt_converted, 2, '.', '')  ."</td></tr>";
		}
		$assignee_table_output .=  "</tbody>";
		$assignee_table_output .=  '<tfoot><tr><td text align=right colspan="6">Total:</td><td align="right"></td></tr></tfoot>';
		$assignee_table_output .=  "</table><div class='clear'></div></div>";
		echo json_encode($assignee_table_output);	
	}
	
	public function get_leads_current_weekly_monthly_report() 
	{
		$res = real_escape_array($this->input->post());
		$weekly_monthly_repo = '';
		$lead_det 	= array();
		$isSelect 	= $res['statusVar'];
		$filter		= $res;
		$cusId 		= $this->level_restriction();
		$data['getCurrentActivityTable'] = $this->dashboard_model->getCurrentActivityLeadsAjax($isSelect, $cusId, $filter);
		// echo "<pre>"; print_r($data['getCurrentActivityTable']); exit;
		$weekly_monthly_repo .= '<table class="dashboard-heads" id="weekly-monthly-table" cellspacing="0" cellpadding="10px;" border="0" width="100%">';
		$rates = $this->get_currency_rates();
		$weekly_monthly_repo .= '<thead><tr><th>Lead Title</th><th>Estimated Worth ('.$this->default_cur_name.')</th><th>Lead Owner</th><th>Lead Assignee</th></tr></thead><tbody>';
		foreach($data['getCurrentActivityTable'] as $lead_info)
		{
			$lead_det['lead_title'] 	     = '<a onclick="getCurrentLeadActivity('. $lead_info['lead_id'].','."'".$lead_info['lead_title']."'".')" >'. character_limiter($lead_info['lead_title'], 35).'</a>';
			$lead_det['owrfirst_name'] 		 = $lead_info['ownrfname']." ".$lead_info['ownrlname'];	
			$lead_det['usrfname'] 			 = $lead_info['usrfname']." ".$lead_info['usrlname'];
			$lead_det['expect_worth_amount'] = number_format(round($rates[$lead_info['expect_worth_id']][$this->default_cur_id]*$lead_info['expect_worth_amount']), 2, '.', '');	
			$weekly_monthly_repo .= "<tr><td>".$lead_det['lead_title']. "</td><td align='right'>". $lead_det['expect_worth_amount']."</td>" ;
			$weekly_monthly_repo .= "<td>".$lead_det['owrfirst_name']."</td><td>".$lead_det['usrfname']."</td></tr>";
		}
		$weekly_monthly_repo .= "</tbody></table>";
		$weekly_monthly_repo .= "<div class='clear'></div>";
		echo $weekly_monthly_repo;
	}

	public function excel_export_lead_owner($userid)
    {
    	// $lead_username	 = $this->uri->segment(4);  
		// $lead_stage_name = $this->uri->segment(4);
		// $lead_id		 = $this->uri->segment(4); 
		// $lead_aging		 = $this->uri->segment(3);
		// $lead_indi		 = $this->uri->segment(3);
		
		$result     = real_escape_array($this->input->post());

		$graph_type = $result['type'];
		$cusId      = $this->level_restriction();
		$expFilter  = real_escape_array($this->input->post());
		$lead_owner_opp  = array();

		switch ($graph_type) 
		{
			case 'funnel':
				$res			= $this->dashboard_model->getLeadsDetails($result['lead_stage_name'], $cusId, $expFilter);
			break;
			case 'pie1':
				$res			= $this->dashboard_model->getRegionLeadsDetails($result['lead_region_name'], $cusId, $expFilter);
			break;
			case 'bar1':
				$res 			= $this->dashboard_model->getIndiLeads($cusId, $result['lead_indi'], $expFilter);
			break;
			case 'leastactive':
				$res 			= $this->dashboard_model->getIndiLeads($cusId, $lead_indi);
			break;
			case 'currentactivity':
				$res 			= $this->dashboard_model->getCurrentLeadActivity($result['lead_no']);	
				$lead_owner_opp = $res->result_array();
			break;
			case 'line1':
				$res 			= $this->dashboard_model->leadAgingLeads($cusId, $result['lead_aging'], $expFilter);
			break;
			case 'line2':
				$res 			= $this->getClosedJobLeadDetail($result['month_id'], $expFilter);
			break;
			case 'pie2':
				$res 			= $this->dashboard_model->getLeadsDetails_pie2($result['lead_source'], $cusId, $expFilter);
			break;
			case 'pie3':
				$res 			= $this->dashboard_model->getLeadsDetails_pie3($result['servic_require'], $cusId, $expFilter);
			break;
			case 'leadowner':
				$res 			= $this->dashboard_model->getLeadOwnerDependencies($result['user_id'], $cusId, $expFilter);
				$lead_owner_opp = $res->result_array();
			break;
			case 'assignee':
				$res 			= $this->dashboard_model->getLeadAssigneeDependencies($result['user_id'], $cusId, $expFilter);
				$lead_owner_opp = $res->result_array();
			break;
		}
			
    	if($res->num_rows > 0 || count($res) > 0)
    	{
    		//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			
			
			switch ($graph_type) {
				case 'funnel':
					$this->excel->getActiveSheet()->setTitle('Current Pipeline Leads');
				break;
				case 'pie1':
					$this->excel->getActiveSheet()->setTitle('Leads By Region');
				break;
				case 'currentactivity':
					$this->excel->getActiveSheet()->setTitle('Currently Active Leads');
				break;
				case 'bar1':
					$this->excel->getActiveSheet()->setTitle('Leads Indicator');
				break;
				case 'leastactive':
					$this->excel->getActiveSheet()->setTitle('Least Active Leads');
				break;
				case 'line1':
					$this->excel->getActiveSheet()->setTitle('Leads Aging');
				break;
				case 'line2':
					$this->excel->getActiveSheet()->setTitle('Closed Opportunities');
				break;
				case 'pie2':
					$this->excel->getActiveSheet()->setTitle('Lead Source');
				break;
				case 'pie3':
					$this->excel->getActiveSheet()->setTitle('Service Requirement');
				break;
				case 'leadowner':
					$this->excel->getActiveSheet()->setTitle('Lead Owner Opportunities');
				break;
				case 'assignee':
					$this->excel->getActiveSheet()->setTitle('Lead Assignee Opportunities');
				break;
			}
			
			//setup width for the report columns
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
			
			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', 'Lead No');
			$this->excel->getActiveSheet()->setCellValue('B1', 'Lead Title');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Customer');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Lead Owner');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Lead Assignee');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Lead Indicator');
			$this->excel->getActiveSheet()->setCellValue('G1', 'Expected Worth ('.$this->default_cur_name.')');
			
			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
			$i=2;
		    $j=0;
    		/*To build columns*/
			$total_amt_converted = 0;
		    $rates = $this->get_currency_rates();
			
			switch ($graph_type) {
				case 'funnel':
				case 'pie1':
				case 'pie2':
				case 'pie3':
					for($j = 0; $j < count($res); $j++) 
					{			
						$this->excel->getActiveSheet()->setCellValue('A'.$i, $res[$j]['invoice_no']);
						$this->excel->getActiveSheet()->setCellValue('B'.$i, $res[$j]['lead_title']);
						$this->excel->getActiveSheet()->setCellValue('C'.$i, $res[$j]['first_name'].' '.$res[$j]['last_name']);
						$this->excel->getActiveSheet()->setCellValue('D'.$i, $res[$j]['owrfname'].' '.$res[$j]['owrlname']);
						$this->excel->getActiveSheet()->setCellValue('E'.$i, $res[$j]['assifname'].' '.$res[$j]['assilname']);
						$this->excel->getActiveSheet()->setCellValue('F'.$i, $res[$j]['lead_indicator']);
						$amt_converted = $this->conver_currency($res[$j]['expect_worth_amount'],$rates[$res[$j]['expect_worth_id']][$this->default_cur_id]);
						$total_amt_converted += $this->conver_currency($res[$j]['expect_worth_amount'],$rates[$res[$j]['expect_worth_id']][$this->default_cur_id]);
						$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($amt_converted, 2, '.', ''));
						$i++;					  
					}
					//Column Alignment
					$this->excel->getActiveSheet()->getStyle('A1:A'.$i)->getNumberFormat()->setFormatCode('00000');
					$this->excel->getActiveSheet()->setCellValue('F'.$i, 'TOTAL');
					$this->excel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
					$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($total_amt_converted, 2, '.', ''));
					$this->excel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
				break;
				
				case 'currentactivity':
					foreach ($lead_owner_opp as $lead)
					{			
						$this->excel->getActiveSheet()->setCellValue('A'.$i, $lead['invoice_no']);
						$this->excel->getActiveSheet()->setCellValue('B'.$i, $lead['lead_title']);
						$this->excel->getActiveSheet()->setCellValue('C'.$i, $lead['cfname'].' '.$lead['clname']);
						$this->excel->getActiveSheet()->setCellValue('D'.$i, $lead['ownrfname'].' '.$lead['ownrlname']);
						$this->excel->getActiveSheet()->setCellValue('E'.$i, $lead['usrfname'].' '.$lead['usrlname']);
						$this->excel->getActiveSheet()->setCellValue('F'.$i, $lead['lead_indicator']);
						$amt_converted = $this->conver_currency($lead['expect_worth_amount'],$rates[$lead['expect_worth_id']][$this->default_cur_id]);
						$total_amt_converted += $this->conver_currency($lead['expect_worth_amount'],$rates[$lead['expect_worth_id']][$this->default_cur_id]);
						$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($amt_converted, 2, '.', ''));
						$i++;
					}
					//Column Alignment
					$this->excel->getActiveSheet()->getStyle('A1:A'.$i)->getNumberFormat()->setFormatCode('00000');
					$this->excel->getActiveSheet()->setCellValue('F'.$i, 'TOTAL');
					$this->excel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
					$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($total_amt_converted, 2, '.', ''));
					$this->excel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
				break;
				
				case 'bar1':
				case 'line1':
				case 'leastactive':
				case 'line2':
					foreach ($res as $lead)
					{			//LeadDetails
						$this->excel->getActiveSheet()->setCellValue('A'.$i, $lead['invoice_no']);
						$this->excel->getActiveSheet()->setCellValue('B'.$i, $lead['lead_title']);
						$this->excel->getActiveSheet()->setCellValue('C'.$i, $lead['first_name'].' '.$lead['last_name']);
						$this->excel->getActiveSheet()->setCellValue('D'.$i, $lead['owrfname'].' '.$lead['owrlname']);
						$this->excel->getActiveSheet()->setCellValue('E'.$i, $lead['assifname'].' '.$lead['assilname']);
						$this->excel->getActiveSheet()->setCellValue('F'.$i, $lead['lead_indicator']);
						$amt_converted = $this->conver_currency($lead['expect_worth_amount'],$rates[$lead['expect_worth_id']][$this->default_cur_id]);
						$total_amt_converted += $this->conver_currency($lead['expect_worth_amount'],$rates[$lead['expect_worth_id']][$this->default_cur_id]);
						$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($amt_converted, 2, '.', ''));
						$i++;
					}
					//Column Alignment
					$this->excel->getActiveSheet()->getStyle('A1:A'.$i)->getNumberFormat()->setFormatCode('00000');
					$this->excel->getActiveSheet()->setCellValue('F'.$i, 'TOTAL');
					$this->excel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
					$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($total_amt_converted, 2, '.', ''));
					$this->excel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
				break;
				
				case 'leadowner':
				case 'assignee':
					foreach ($lead_owner_opp as $lead)
					{			
						$this->excel->getActiveSheet()->setCellValue('A'.$i, $lead['invoice_no']);
						$this->excel->getActiveSheet()->setCellValue('B'.$i, $lead['lead_title']);
						$this->excel->getActiveSheet()->setCellValue('C'.$i, $lead['cfname'].' '.$lead['clname']);
						$this->excel->getActiveSheet()->setCellValue('D'.$i, $lead['ownrfname'].' '.$lead['ownrlname']);
						$this->excel->getActiveSheet()->setCellValue('E'.$i, $lead['usrfname'].' '.$lead['usrlname']);
						/* converting to USD */
						$amt_converted = $this->conver_currency($lead['expect_worth_amount'],$rates[$lead['expect_worth_id']][$this->default_cur_id]);
						$total_amt_converted += $this->conver_currency($lead['expect_worth_amount'],$rates[$lead['expect_worth_id']][$this->default_cur_id]);
						$this->excel->getActiveSheet()->setCellValue('F'.$i, $lead['lead_indicator']);
						$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($amt_converted, 2, '.', ''));
						
						$i++;
					}
					//Column Alignment
					$this->excel->getActiveSheet()->getStyle('A1:A'.$i)->getNumberFormat()->setFormatCode('00000');
					$this->excel->getActiveSheet()->setCellValue('F'.$i, 'TOTAL');
					$this->excel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
					$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($total_amt_converted, 2, '.', ''));
					$this->excel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
				break;
			}
			
    		/*To build columns ends*/
			
    		//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);
			//merge cell A1 until Q1
			//$this->excel->getActiveSheet()->mergeCells('A1:D1');
			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			switch ($graph_type) 
			{
				case 'funnel':
					$filename = 'Current_pipeline_leads.xls';
				break;
				case 'pie1':
					$filename = 'Leads_By_Region.xls';
				break;
				case 'bar1':
					$filename = 'lead_indicator.xls';
				break;
				case 'leastactive':
					$filename = 'least_active_leads.xls';
				break;				
				case 'currentactivity':
					$filename = 'currently_active_leads.xls';
				break;
				case 'line1':
					$filename = 'leads_aging.xls';
				break;
				case 'line2':
					$filename = 'closed_oppor.xls';
				break;
				case 'pie2':
					$filename = 'Leads_By_LeadSource.xls';
				break;
				case 'pie3':
					$filename = 'Leads_By_SerReq.xls';
				break;
				case 'leadowner':
					$filename = 'Lead_owner_report_'.$result['user_name'].'.xls';
				break;
				case 'assignee':
					$filename = 'Lead_assignee_report_'.$result['user_name'].'.xls';
				break;
			}	

			//$filename='Lead_assignee_report.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
						 
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
    	}    	
    	redirect('/dashboard/');
    }

	public function getLeadTitle() 
	{
		$data['getLeastLead'] = $this->dashboard_model->getLeastLeads();
		$least_leads = array();
		foreach($data['getLeastLead'] as $getLeast){
			$least_leads[] = $getLeast['lead_title'];
		}
		$result = real_escape_array($this->input->post());
		$res['html'] = $least_leads[$result['id']];
		echo json_encode($res);
		exit;
		//return $amount*$val;
	}

	public function showLeadDetails() 
	{
		$resu = real_escape_array($this->input->post());
		
		$filters			   = array();	
		$filters['stge'] 	   = $resu['stge'];
		$filters['cust_id']    = $resu['cust_id'];
		$filters['ownr_id']    = $resu['ownr_id'];
		$filters['assg_id']    = $resu['assg_id'];
		$filters['reg_id']     = $resu['reg_id'];
		$filters['cntry_id']   = $resu['cntry_id'];
		$filters['stet_id']	   = $resu['stet_id'];
		$filters['locn_id']    = $resu['locn_id'];
		$filters['servic_req'] = $resu['servic_req'];
		$filters['lead_sour']  = $resu['lead_sour'];
		$filters['lead_indic'] = $resu['lead_indic'];

		$gid  = $resu['gid'];
	    $type = $resu['type'];

		$cusId = $this->level_restriction();
		$rates = $this->get_currency_rates();
		$res = array();
		
		switch($resu['type']){
			case "bar1":
				if ($gid == 0) {
					$ind = 'HOT';
				} else if ($gid == 1) {
					$ind = 'WARM';
				} else {
					$ind = 'COLD';
				}
				$data['leadDeta'] = $this->dashboard_model->getIndiLeads($cusId, $ind, $filters);
				$heading = "Lead Indicator - ".$ind;
				$tid = "example_bar1";
				$linkurl = "welcome/view_quote/";
			break;
			
			case "line1":
				$data['leadDeta'] = $this->dashboard_model->leadAgingLeads($cusId, $gid, $filters);
				$heading = "Leads Aging";
				$tid = "example_line1";
				$linkurl = "welcome/view_quote/";
			break;

		}
		
		$res['html'] .= '<div class="dash-section dash-section1"><h5>'.$heading.'</h5><div class="grid-close"></div></div>';
		$res['html'] .= "<div class='dashbrd charts-info-block'>";
		$rates = $this->get_currency_rates();
		//for excel
		if($type == 'bar1') {
			$res['html'] .= '<a id="least-active-report" class="export-btn" name="'.$ind.'">Export to Excel</a>';
			$res['html'] .= "<input id='least-active-type' type='hidden' value='".$type."'/>";
		}
		if($type == 'line1') {
			$res['html'] .= '<a id="lead-aging-report" class="export-btn" name="'.$gid.'">Export to Excel</a>';
			$res['html'] .= "<input id='lead-aging-type' type='hidden' value='".$type."'/>";
		}
		
		$res['html'] .= '<table cellspacing="0" id='.$tid.' class="dashboard-heads" cellpadding="10px;" border="0" width="100%"><thead><tr><th>Lead No.</th><th>Lead Title </th><th>Customer</th><th>Lead Owner</th><th>Lead Assignee</th><th>Lead Indicator</th><th>Expected Worth ('.$this->default_cur_name.')</th><thead><tbody role="alert" aria-live="polite" aria-relevant="all">';
		
		if (isset($data['leadDeta']) && count($data['leadDeta'])) :
			foreach($data['leadDeta'] as $leadDet) {
				$amt_converted = $this->conver_currency($leadDet['expect_worth_amount'],$rates[$leadDet['expect_worth_id']][$this->default_cur_id]);
				$res['html'] .= '<tr>
								 <td><a href="'.base_url().$linkurl.$leadDet['lead_id'].'" target="_blank">'.$leadDet['invoice_no'].'</a></td>
								 <td><a href="'.base_url().$linkurl.$leadDet['lead_id'].'" target="_blank">'.character_limiter($leadDet['lead_title'], 35).'</a></td>
								 <td>'.$leadDet['first_name'].' '.$leadDet['last_name'].'</td>
								 <td>'.$leadDet['owrfname'].' '.$leadDet['owrlname'].'</td>
								 <td>'.$leadDet['assifname'].' '.$leadDet['assilname'].'</td>
								 <td>'.$leadDet['lead_indicator'].'</td>
								 <td text align="right">'.number_format($amt_converted, 2, '.', '').'</td>
								 </tr>';
			}		
		endif;
		$res['html'] .= '</tbody>';
		$res['html'] .= '<tfoot><tr><td text align=right colspan="6">Total:</td><td align="right"></td></tr></tfoot>';
		$res['html'] .= '</table><div class="clear"></div></div>';
		echo json_encode($res);
		exit;
	}
	
	//for closed opportunities
	public function showLeadDetails_cls() 
	{
		$resu = real_escape_array($this->input->post());
		
		$filters			   = array();
		$filters['stge'] 	   = $resu['stge'];
		$filters['cust_id']    = $resu['cust_id'];
		$filters['ownr_id']    = $resu['ownr_id'];
		$filters['assg_id']    = $resu['assg_id'];
		$filters['reg_id']     = $resu['reg_id'];
		$filters['cntry_id']   = $resu['cntry_id'];
		$filters['stet_id']	   = $resu['stet_id'];
		$filters['locn_id']    = $resu['locn_id'];
		$filters['servic_req'] = $resu['servic_req'];
		$filters['lead_sour']  = $resu['lead_sour'];
		$filters['lead_indic'] = $resu['lead_indic'];
		
		$gid   = $resu['gid'];
	    $type  = $resu['type'];
		
		$cusId = $this->level_restriction();
		$rates = $this->get_currency_rates();
		$res   = array();
		
		$data['leadDeta'] = $this->getClosedJobLeadDetail($gid, $filters);
		$heading 		  = "Closed Opportunities";
		$tid 			  = "example_line2";
		
		$res['html'] .= '<div class="dash-section dash-section1"><h5>'.$heading.'</h5><div class="grid-close"></div></div>';
		$res['html'] .= "<div class='dashbrd charts-info-block'>";
		$rates = $this->get_currency_rates();
		//for excel

		$res['html'] .= '<a id="closed-oppor-report" class="export-btn" name="'.$gid.'">Export to Excel</a>';
		$res['html'] .= "<input id='cls-oppr-type' type='hidden' value='".$type."'/>";

		$res['html'] .= '<table cellspacing="0" id='.$tid.' class="dashboard-heads" cellpadding="10px;" border="0" width="100%"><thead><tr><th>Lead No.</th><th>Lead Title </th><th>Customer</th><th>Lead Owner</th><th>Lead Assignee</th><th>Lead Indicator</th><th>Actual Worth ('.$this->default_cur_name.')</th><thead><tbody role="alert" aria-live="polite" aria-relevant="all">';
		
		if (isset($data['leadDeta']) && count($data['leadDeta'])) :
			foreach($data['leadDeta'] as $leadDet) {
				// echo $leadDet['pjt_status'];
				$amt_converted = $this->conver_currency($leadDet['expect_worth_amount'],$rates[$leadDet['expect_worth_id']][$this->default_cur_id]);
				switch($leadDet['pjt_status']) {
					case 0:
						$linkurl = "welcome/view_quote/";
					break;
					case 1:
					case 2:
					case 3:
						$linkurl = "project/view_project/";
					break;
				} 
				$res['html'] .= '<tr>
								 <td><a href="'.base_url().$linkurl.$leadDet['lead_id'].'" target="_blank">'.$leadDet['invoice_no'].'</a></td>
								 <td><a href="'.base_url().$linkurl.$leadDet['lead_id'].'" target="_blank">'.character_limiter($leadDet['lead_title'], 35).'</a></td>
								 <td>'.$leadDet['first_name'].' '.$leadDet['last_name'].'</td>
								 <td>'.$leadDet['owrfname'].' '.$leadDet['owrlname'].'</td>
								 <td>'.$leadDet['assifname'].' '.$leadDet['assilname'].'</td>
								 <td>'.$leadDet['lead_indicator'].'</td>
								 <td text align="right">'.number_format($amt_converted, 2, '.', '').'</td>
								 </tr>';
			}		
		endif;
		$res['html'] .= '</tbody>';
		$res['html'] .= '<tfoot><tr><td text align=right colspan="6">Total:</td><td align="right"></td></tr></tfoot>';
		$res['html'] .= '</table><div class="clear"></div></div>';
		echo json_encode($res);
		exit;
	}
	
	//level restriction
	public function level_restriction() 
	{
		$userdata = $this->session->userdata('logged_in_user');
		if (($userdata['role_id'] == 1 && $userdata['level'] == 1) || ($userdata['role_id'] == 2 && $userdata['level'] == 1)) 
		{
			$cusId = '';
		}
		else
		{
			$cusIds = array();
			$reg = array();
			$cou = array();
			$ste = array();
			$loc = array();
			$cusIds[] = 0;
			switch($userdata['level']) {
				case 2:
					$regions = $this->dashboard_model->getRegions($userdata['userid'], $userdata['level']); //Get the Regions based on Level
						foreach ($regions as $rgid) {
							$reg[] = $rgid['region_id'];
						}
					$CustomersId = $this->dashboard_model->getCustomersIds($reg); //Get the Customer id based on Regions
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['custid'];
						}
					$cusId = $cusIds;
				break;
				case 3:
					$countries = $this->dashboard_model->getCountries($userdata['userid'], $userdata['level']); //Get the Countries based on Level
						foreach ($countries as $couid) {
							$cou[] = $couid['country_id'];
						}
					$CustomersId = $this->dashboard_model->getCustomersIds($reg,$cou); //Get the Customer id based on Regions & Countries
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['custid'];
						}
					$cusId = $cusIds;
				break;
				case 4:
					$states = $this->dashboard_model->getStates($userdata['userid'], $userdata['level']); //Get the States based on Level
						foreach ($states as $steid) {
							$ste[] = $steid['state_id'];
						}
					$CustomersId = $this->dashboard_model->getCustomersIds($reg,$cou,$ste); //Get the Customer id based on Regions & Countries
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['custid'];
						}
					$cusId = $cusIds;
				break;
				case 5:
					$locations = $this->dashboard_model->getLocations($userdata['userid'], $userdata['level']); //Get the Locations based on Level
						foreach ($locations as $locid) {
							$loc[] = $locid['location_id'];
						}	
					$CustomersId = $this->dashboard_model->getCustomersIds($reg,$cou,$ste,$loc); //Get the Customer id based on Regions & Countries
						foreach ($CustomersId as $cus_id) {
							$cusIds[] = $cus_id['custid'];
						}
					$cusId = $cusIds;
				break;
			}
		}
		return $cusId;
	}
	
	//for save pdf
	public function savePdf() 
	{
		$data = $_POST['img_data'];
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		//header("Content-type: image/png");
		//echo '<img src="data:image/png;base64,' . $data . '" />';
		$html = "";
		
		$html .= '<img src="data:image/png;base64,' . $data . '" height = "300" width= "350"/>';

		//$the_filename = ($name_override != '') ? $name_override : 'output-' . $data['quote_data']['invoice_no'];
		$the_filename = 'graph';
		require('html2pdf/html2fpdf.php');
		$pdf=new HTML2FPDF();
		$pdf->SetFont('Arial','B',16);
		$pdf->AddPage();
		$file_name = '\graph' ;
		$file = realpath('/xampp/htdocs/DEV/dev_ecrm/graphImgs');
		
		$img = $data;
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$f = $file.$file_name . '.jpg';
		$file = $file . $file_name . '.png';
		
		$success = file_put_contents($file, $data);
		$image = imagecreatefrompng($file);
		imagejpeg($image, $f, 100);
		imagedestroy($image);
		$html .='test';
		$html .= '<img src="'. base_url().'graphImgs/graph.jpg' . '" />';
	
		$strContent = $html;
		$pdf->WriteHTML($strContent);

		$pdf->Output($the_filename.".pdf");

		exit;
	}
	
	//for getClosedJobLeadDetail
	public function getClosedJobLeadDetail($mid, $filters=false)
	{
		$cusId 	= $this->level_restriction();
		$months = array('04','05','06','07','08','09','10','11','12','01','02','03');
		$mont 	= $months[$mid];
		$data['lead_id'] = $this->dashboard_model->getClosedJobids($cusId, $filters);
		//echo "<pre>"; print_r($data['lead_id']); exit;
		
		$jb = array();
		foreach ($data['lead_id'] as $value) {
			$sql = "SELECT lead_id, dateofchange FROM {$this->cfg['dbpref']}lead_status_history WHERE lead_id = '".$value['lead_id']."' AND changed_status = 4 ORDER BY dateofchange DESC LIMIT 1";
			$rows = $this->db->query($sql);
			//echo $this->db->last_query(); 
			$res_query = $rows->row_array();
			if ($mont<=3) {
				$Yr = (date("Y")+1);
			} else {
				$Yr = date("Y");
			}
			
			$yer = date("Y" , strtotime($res_query['dateofchange']));
			$mon = date("m" , strtotime($res_query['dateofchange']));
			
			if ($yer == $Yr) {
				if ($mont == $mon) {
					$jb[] = $res_query['lead_id'];
				}
			}
		}
		$leads_res = $this->dashboard_model->closedLeadDet($jb, $filters);
		return $leads_res;
	}
}
