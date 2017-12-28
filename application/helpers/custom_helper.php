<?php
if ( ! function_exists('get_default_currency'))
{
	function get_default_currency()
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$CI->db->select('expect_worth_id, expect_worth_name');
		$CI->db->where('is_default', 1);
		$query = $CI->db->get($CI->cfg['dbpref'].'expect_worth');
		$num = $query->num_rows();
		$CI->db->reconnect();
		if ($num<1)
			return false;
		else 
			$res = $query->row_array();
			// echo $CI->db->last_query(); exit;
		return $res;
	}
}

function currency_convert()
{
	/* if (get_default_currency()) {
		$default_currency = get_default_currency();
		$to_Currency = $default_currency['expect_worth_name'];
		$to_Currency_id = $default_currency['expect_worth_id'];
	} else {
		$to_Currency = 'USD';
		$to_Currency_id = 1;
	} */
	
	$CI  = get_instance();
	$cfg = $CI->config->item('crm'); /// load config
	
	$query = $CI->db->get($CI->cfg['dbpref'].'expect_worth');
	$res = $query->result();
	
	if(!empty($res)){
		foreach ($res as $curren)
		{
			foreach ($res as $cur)
			{
				// $to_Currency = 'USD';
				$amount = 1;
				$amount = urlencode($amount);
				$from_Currency = urlencode($curren->expect_worth_name);
				$from_Currency_id = urlencode($curren->expect_worth_id);
				// $to_Currency = urlencode($to_Currency);
				$to_Currency = urlencode($cur->expect_worth_name);
				$to_Currency_id = urlencode($cur->expect_worth_id);
				/* $url = "http://www.google.com/ig/calculator?hl=en&q=$amount$from_Currency=?$to_Currency";
				$ch = curl_init();
				$timeout = 0;
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$rawdata = curl_exec($ch);
				curl_close($ch);
				
				$data = explode('"', $rawdata);
				$data = explode(' ', $data['3']); 
				$var = $data['0']; */
				if($from_Currency!=$to_Currency) {
					$url = "https://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
					$amount = urlencode($amount);
					$from_Currency = urlencode($from_Currency);
					$to_Currency = urlencode($to_Currency);
					$get = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency");
					$get = explode("<span class=bld>",$get);
					$get = explode("</span>",$get[1]);  
					$converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
					$conversion_value = round($converted_amount, 3);
					updt_currency($from_Currency_id, $to_Currency_id, $conversion_value);
				} else {
					updt_currency($from_Currency_id, $to_Currency_id, 1);
				}
			}
		}
	}
}

function updt_currency($frm, $to_Currency_id, $conversion_value)
{
	$CI  = get_instance();
	$cfg = $CI->config->item('crm'); /// load config

	$CI->db->where('from', $frm);
	$CI->db->where('to', $to_Currency_id);
	$query1 = $CI->db->get($cfg['dbpref'].'currency_rate');

	$res_num = $query1->num_rows();

	if($res_num>0) {
		if(!empty($conversion_value)){
			$CI->db->where('from', $frm);
			$CI->db->where('to', $to_Currency_id);
			$CI->db->set('value', $conversion_value);
			$CI->db->update($cfg['dbpref'].'currency_rate');
		}
	} else {
		if(!empty($conversion_value)){
			$CI->db->set('from', $frm);
			$CI->db->set('to', $to_Currency_id);
			$CI->db->set('value', $conversion_value);
			$CI->db->insert($cfg['dbpref'].'currency_rate');
		}				
	}
}

 function show_detail_html($label='',$opened=0,$resolved=0,$closed=0,$total=0){
	$opened = isset($opened)?$opened:0;
	$resolved = isset($resolved)?$resolved:0;
	$closed = isset($closed)?$closed:0;
	return '<tr><td><strong>'.$label.'</strong></td><td>'.$opened.'</td><td>'.$resolved.'</td><td>'.$closed.'</td><td>'.$total.'</td></tr>'; 
}

function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}

	return $bytes;
}

if ( ! function_exists('get_book_keeping_rates'))
{
	function get_book_keeping_rates()
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$CI->db->select('expect_worth_id_from, expect_worth_id_to, financial_year, currency_value');
		$query   = $CI->db->get($CI->cfg['dbpref'].'book_keeping_currency_rates');
		$results = $query->result_array();
		$book_keeping_rates   = array();
    	if(!empty($results)) {
    		foreach ($results as $res) {
    			$book_keeping_rates[$res['financial_year']][$res['expect_worth_id_to']][$res['expect_worth_id_from']] = $res['currency_value'];
    		}
    	}
		return $book_keeping_rates;
	}
}

if ( ! function_exists('get_attachments_show'))
{
	function get_attachments_show($expectid)
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); /// load config
		
		$qry = $CI->db->get_where($CI->cfg['dbpref']."expected_payments_attachments",array("expectid" => $expectid));
		$res = $qry->result();
		if($qry->num_rows()>0){
		$list = '';
		  foreach($res as $rs){
			  $list .= anchor(site_url("invoice/download_file/".$rs->file_name),$rs->file_name).'<br>';
		  }
		}
		return $list;
	}
}

if ( ! function_exists('get_dms_access'))
{
	function get_dms_access($dms_type)
	{
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
		$huserdata  = $CI->session->userdata('logged_in_user');
		
		$qry 	    = $CI->db->get_where($CI->cfg['dbpref']."dms_users", array('user_id'=>$huserdata['userid'],'dms_type'=>$dms_type));
		$res 	    = $qry->num_rows();
		$dms_access = 0;
		if($qry->num_rows()>0){
			$dms_access = 1;
		}
		return $dms_access;
	}
}

if ( ! function_exists('get_dms_folder_access'))
{
	function get_dms_folder_access($folder_id)
	{
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
		$huserdata  = $CI->session->userdata('logged_in_user');
		
		$qry 	    = $CI->db->get_where($CI->cfg['dbpref']."dms_users", array('user_id'=>$huserdata['userid'],'dms_type'=>$dms_type));
		$res 	    = $qry->num_rows();
		$dms_access = 0;
		if($qry->num_rows()>0){
			$dms_access = 1;
		}
		return $dms_access;
	}
}
/* Get current financial year*/
function get_current_financial_year($year=false,$month=false)
{
	$financial_year="";
	if($year && $month) {
		$month = date('m', strtotime("$month $year"));
		if($month < '04') {
			$financial_year = ($year-1) ."-".$year;
		} else {
			$financial_year = $year."-".($year + 1);
		}
	} else {
		if(date('m')<'04') {
			$financial_year = date('Y',strtotime('-1 year'))."-".date('Y');
		} else {
			$financial_year = date('Y')."-".date('Y',strtotime('+1 year'));
		}
	}
	
	return $financial_year;
}

// get last financial year
function getLastFiscalYear() 
{
    $currentYear = date('Y');
    // Check if it happened this year, AND it's not in the future.
    $today = new DateTime();
    if (checkdate(3, 31, $currentYear) && $today->getTimestamp() > mktime(0, 0, 0, 3, 31, $currentYear)) {
        return $currentYear;
    }

    while (--$currentYear) {
        if (checkdate(3, 31, $currentYear)) {
            return $currentYear;
        }
    }
    return false;
}

/*Get practice max hours based on practice id*/
if ( ! function_exists('get_practice_max_hours')){
	function get_practice_max_hours($practice_id=false){
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
		if($practice_id){
			$qry 	    = $CI->db->get_where($CI->cfg['dbpref']."practice_max_hours_history", array('practice_id'=>$practice_id));
			$result = $qry->result_array(); 
			if(count($result)>0 && !empty($result)){
				return $result;
			}else{
				return array();
			}
		}	
	}
}
/*Get practice max hours based on practice id and financial year*/
if ( ! function_exists('get_practice_max_hour_by_financial_year')){
	function get_practice_max_hour_by_financial_year($practice_id=false,$financial_year=false){
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
		$result = array();
		if($practice_id && $financial_year){
			$CI->db->order_by("id","desc");
			$qry 	    = $CI->db->get_where($CI->cfg['dbpref']."practice_max_hours_history", array('practice_id'=>$practice_id,'financial_year' => $financial_year))->row();
			
			$result = $qry; 
			if(count($result)>0 && !empty($result)){
				return $result;
			}
		}

		if($practice_id && empty($result)){
			$CI->db->order_by("id","desc");
			$CI->db->limit(1);
			$qry = $CI->db->get_where($CI->cfg['dbpref']."practice_max_hours_history", array('practice_id'=>$practice_id))->row();
			$result = $qry; 
			if(count($result)>0 && !empty($result)){
				return $result;
			}else{
				return array();
			}
		}
	}
}
/*Get timesheet hours based on username, month and year*/
if ( ! function_exists('get_timesheet_hours_by_user')){
	function get_timesheet_hours_by_user($usename=false,$year=false,$month=false,$included_leave=false,$not_included_non_billable=false,$not_included_internal=false){
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
	
		$qry="";
		if($usename && $year && $month){
			
			$qry .="SELECT sum(duration_hours) as hours FROM ".$CI->cfg['dbpref']."timesheet_data WHERE username='$usename' and entry_month='$month' and entry_year=$year";
			if($included_leave && is_array($included_leave)){
				//$included_leave=implode(',',$included_leave);
				$included_leave = "'" . implode("','", $included_leave) . "'";
				$qry .=" and project_code not in ($included_leave)";
			}
			if($not_included_non_billable==true){
				$qry .=" and resoursetype not in ('Non-Billable')";
			}
			if($not_included_internal==true){
				$qry .=" and resoursetype not in ('Internal')";
			}
			$qry_project = $CI->db->query($qry);
			//echo $CI->db->last_query().'<br>';
			if($qry_project->num_rows()>0) {
				$result = $qry_project->result_array();
				return $result[0]['hours'];
			}	
			
		}	
	}
}
/*Get timesheet hours based on username, month and year*/
if ( ! function_exists('get_timesheet_hours_by_user_modified')){
	function get_timesheet_hours_by_user_modified($usename=false,$year=false,$month=false,$included_leave=false){
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
	
		$qry="";
		if($usename && $year && $month){
			
			$qry .="SELECT sum(duration_hours) as hours FROM ".$CI->cfg['dbpref']."timesheet_data WHERE username='$usename' and entry_month='$month' and entry_year=$year";
			if($included_leave && is_array($included_leave)){
				//$included_leave=implode(',',$included_leave);
				$included_leave = "'" . implode("','", $included_leave) . "'";
				$qry .=" and project_code not in ($included_leave)";
			}
			$qry_project = $CI->db->query($qry);
			//echo $CI->db->last_query().'<br>';
			if($qry_project->num_rows()>0) {
				$result = $qry_project->result_array();
				return $result[0]['hours'];
			}	
			
		}	
	}
}

/*Get timesheet hours based on username, month and year*/
if ( ! function_exists('get_timesheet_hours_by_user_frm_month_data')){
	function get_timesheet_hours_by_user_frm_month_data($usename=false,$year=false,$month=false,$included_leave=false){
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
		if($usename && $year && $month) {
			
			$CI->db->select('sum(duration_hours) as hours', false);
			$CI->db->from($CI->cfg['dbpref']. 'timesheet_month_data as t');
			$CI->db->where("t.username", $usename);
			$CI->db->where("t.entry_month", $month);
			$CI->db->where("t.entry_year", $year);
			if($included_leave && is_array($included_leave)) {
				$CI->db->where_not_in("t.project_code", $included_leave);
			}
			$qry_project = $CI->db->get();
			// echo $CI->db->last_query().'<br>';
			if($qry_project->num_rows()>0) {
				$result = $qry_project->result_array();
				return $result[0]['hours'];
			}
		}	
	}
}


function get_leave_hours_by_user($usename=false,$year=false,$month=false)
{
		$CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
	    $qry="";
		if($usename && $year && $month){
			
			$qry .="SELECT sum(duration_hours) as hours FROM ".$CI->cfg['dbpref']."timesheet_data WHERE username='$usename' and entry_month='$month' and entry_year=$year and project_code in ('Leave')";
		    $qry_project = $CI->db->query($qry);
			//echo $CI->db->last_query().'<br>';
			if($qry_project->num_rows()>0) {
				$result = $qry_project->result_array();
				return $result[0]['hours'];
			}	
			
		}
}

function get_hoilday_hours_by_user($usename=false,$year=false,$month=false)
{
	    $CI   	    = get_instance();
		$cfg	    = $CI->config->item('crm'); /// load config
	    $qry="";
		if($usename && $year && $month){
			
			$qry .="SELECT sum(duration_hours) as hours FROM ".$CI->cfg['dbpref']."timesheet_data WHERE username='$usename' and entry_month='$month' and entry_year=$year and project_code in ('Hol')";
		    $qry_project = $CI->db->query($qry);
			//echo $CI->db->last_query().'<br>';
			if($qry_project->num_rows()>0) {
				$result = $qry_project->result_array();
				return $result[0]['hours'];
			}	
			
		}
}



if ( ! function_exists('getFiscalYearForDate'))
{
	function getFiscalYearForDate($inputDate, $fyStart, $fyEnd)
	{
		$date = strtotime($inputDate);
		$inputyear = strftime('%Y',$date);
	 
		$fystartdate = strtotime($fyStart.'/'.$inputyear);
		$fyenddate = strtotime($fyEnd.'/'.$inputyear);
	 
		if($date <= $fyenddate) {
			$fy = intval($inputyear);
		} else {
			$fy = intval(intval($inputyear) + 1);
		}
		return $fy;
	}
}

function converCurrency($amount, $val) 
{
	return round($amount*$val, 2);
}

//for other cost details in IT service dashboard details(click YTD Utilization Cost)
if ( ! function_exists('getOtherCostByProjectId'))
{
	function getOtherCostByProjectId($project_code = false, $default_curr = false)
	{
		$cur_bk_rate = get_book_keeping_rates();
		$CI   	     = get_instance();
		$cfg	     = $CI->config->item('crm'); /// load config
		$result 	 = array();
		$data['value'] = 0;
		if(!empty($project_code)) {
			$CI->db->select("description, cost_incurred_date, currency_type, value");
			$CI->db->from($CI->cfg['dbpref'].'project_other_cost');
			$CI->db->join($CI->cfg['dbpref'].'leads', 'lead_id = project_id');
			$CI->db->where('pjt_id', $project_code);
			$CI->db->order_by('id', 'ASC');
			$query  = $CI->db->get();
			$result = $query->result_array();

			if(count($result)>0 && !empty($result)) {
				$i=0;
				foreach($result as $rec) {
					$conver_value  = 0;
					$curFiscalYear = date('Y'); //set as default current year as fiscal year
					$curFiscalYear = getFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
					$convert_value = converCurrency($rec['value'], $cur_bk_rate[$curFiscalYear][$rec['currency_type']][$default_curr]);
					$data['value'] += $convert_value;
					//for detail
					$data['det'][$i]['desc'] = $rec['description'] ." On ".date('d-m-Y', strtotime($rec['cost_incurred_date']));
					$data['det'][$i]['amt']  = $convert_value;
					$i++;
				}	
			}
		}
		return $data;
	}
}

//for other cost details in IT service dashboard details(click YTD Utilization Cost)
if ( ! function_exists('getOtherCostByProjectIdByDateRange'))
{
	function getOtherCostByProjectIdByDateRange($project_code = false, $default_curr = false, $start_date = false, $end_date = false)
	{
		$cur_bk_rate = get_book_keeping_rates();
		$CI   	     = get_instance();
		$cfg	     = $CI->config->item('crm'); /// load config
		$result 	 = array();
		$data['value'] = 0;
		if(!empty($project_code)) {
			$CI->db->select("description, cost_incurred_date, currency_type, value");
			$CI->db->from($CI->cfg['dbpref'].'project_other_cost');
			$CI->db->join($CI->cfg['dbpref'].'leads', 'lead_id = project_id');
			$CI->db->where('pjt_id', $project_code);
			//cost_incurred_date
			if(!empty($start_date)) {
				$CI->db->where("cost_incurred_date >= ", date('Y-m-d H:i:s', strtotime($start_date)));
			}
			if(!empty($end_date)) {
				$CI->db->where("cost_incurred_date <= ", date('Y-m-d H:i:s', strtotime($end_date)));
			}
			$CI->db->order_by('id', 'ASC');
			$query  = $CI->db->get();
			// echo $CI->db->last_query(); die;
			$result = $query->result_array();

			if(count($result)>0 && !empty($result)) {
				$i=0;
				foreach($result as $rec) {
					$conver_value  = 0;
					$curFiscalYear = date('Y'); //set as default current year as fiscal year
					$curFiscalYear = getFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
					$convert_value = converCurrency($rec['value'], $cur_bk_rate[$curFiscalYear][$rec['currency_type']][$default_curr]);
					$data['value'] += $convert_value;
					//for detail
					$data['det'][$i]['desc'] = $rec['description'] ." On ".date('d-m-Y', strtotime($rec['cost_incurred_date']));
					$data['det'][$i]['amt']  = $convert_value;
					$i++;
				}	
			}
		}
		return $data;
	}
}

//for other cost details in IT service dashboard details(click YTD Utilization Cost)
if ( ! function_exists('getOtherCostByLeadId'))
{
	function getOtherCostByLeadId($lead_id = false, $default_curr = false)
	{
		$cur_bk_rate = get_book_keeping_rates();
		$CI   	     = get_instance();
		$cfg	     = $CI->config->item('crm'); /// load config
		$result 	 = array();
		$value 		 = 0;
		if(!empty($lead_id)) {
			$CI->db->select("cost_incurred_date, currency_type, value");
			$CI->db->from($CI->cfg['dbpref'].'project_other_cost');
			$CI->db->where('project_id', $lead_id);			
			$CI->db->order_by('id', 'ASC');
			$query  = $CI->db->get();
			$result = $query->result_array();

			if(count($result)>0 && !empty($result)) {
				foreach($result as $rec) {
					$conver_value  = 0;
					$curFiscalYear = date('Y'); //set as default current year as fiscal year
					$curFiscalYear = getFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
					$convert_value = converCurrency($rec['value'], $cur_bk_rate[$curFiscalYear][$rec['currency_type']][$default_curr]);
					$value += $convert_value;
				}	
			}
		}
		return $value;
	}
}

//for other cost details in IT service dashboard details(click YTD Utilization Cost)
if ( ! function_exists('getOtherCostByLeadIdByDateRange'))
{
	function getOtherCostByLeadIdByDateRange($lead_id = false, $default_curr = false, $start_date = false, $end_date = false)
	{
		$cur_bk_rate = get_book_keeping_rates();
		$CI   	     = get_instance();
		$cfg	     = $CI->config->item('crm'); /// load config
		$result 	 = array();
		$value 		 = 0;
		if(!empty($lead_id)) {
			$CI->db->select("cost_incurred_date, currency_type, value");
			$CI->db->from($CI->cfg['dbpref'].'project_other_cost');
			$CI->db->where('project_id', $lead_id);
			//cost_incurred_date
			if(!empty($start_date)) {
				$CI->db->where("cost_incurred_date >= ", date('Y-m-d H:i:s', strtotime($start_date)));
			}
			if(!empty($end_date)) {
				$CI->db->where("cost_incurred_date <= ", date('Y-m-d H:i:s', strtotime($end_date)));
			}
			$CI->db->order_by('id', 'ASC');
			$query  = $CI->db->get();
			// echo $CI->db->last_query().'<br>';
			$result = $query->result_array();

			if(count($result)>0 && !empty($result)) {
				foreach($result as $rec) {
					$conver_value  = 0;
					$curFiscalYear = date('Y'); //set as default current year as fiscal year
					$curFiscalYear = getFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
					$convert_value = converCurrency($rec['value'], $cur_bk_rate[$curFiscalYear][$rec['currency_type']][$default_curr]);
					$value += $convert_value;
				}	
			}
		}
		return $value;
	}
}

//for other cost based on project based currency
if ( ! function_exists('getOtherCostByLeadIdBasedProjectCurrency'))
{
	function getOtherCostByLeadIdBasedProjectCurrency($lead_id = false, $project_curr = false)
	{
		$cur_bk_rate = get_book_keeping_rates();
		$CI   	     = get_instance();
		$cfg	     = $CI->config->item('crm'); /// load config
		$result 	 = array();
		$value 		 = 0;
		if(!empty($lead_id)) {
			$CI->db->select("cost_incurred_date, currency_type, value");
			$CI->db->from($CI->cfg['dbpref'].'project_other_cost');
			$CI->db->where('project_id', $lead_id);
			$CI->db->order_by('id', 'ASC');
			$query  = $CI->db->get();
			$result = $query->result_array();

			if(count($result)>0 && !empty($result)) {
				foreach($result as $rec) {
					$conver_value  = 0;
					$curFiscalYear = date('Y'); //set as default current year as fiscal year
					$curFiscalYear = getFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
					$convert_value = converCurrency($rec['value'], $cur_bk_rate[$curFiscalYear][$rec['currency_type']][$project_curr]);
					$value += $convert_value;
				}	
			}
		}
		return $value;
	}
}

/*
*@method calcInvoiceDataByPracticeWiseMonthWise()
*@param array
*/
if( ! function_exists('calcInvoiceDataByPracticeWiseMonthWise'))
{
	function calcInvoiceDataByPracticeWiseMonthWise($invoice_data, $default_cur)
	{
		$trend_array 		= array();
		$bk_rates 			= get_book_keeping_rates();
		
		if(is_array($invoice_data) && !empty($invoice_data) && count($invoice_data)>0) {
			foreach($invoice_data as $ir) {
				if($ir['practices'] == 'Infra Services' || $ir['practices'] == 'Testing') { //infra services & Testing practices values are merged with other practices
					$ir['practices'] = 'Others';
				}
				$mon = date('M', strtotime($ir['for_month_year']));
				$base_conversion_camt = converCurrency($ir['milestone_value'],$bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['expect_worth_id']][$ir['base_currency']]);
				if(isset($trend_array[$ir['practices']][$mon])){
					$trend_array[$ir['practices']][$mon] += converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$default_cur]);
				} else {
					$trend_array[$ir['practices']][$mon] = converCurrency($base_conversion_camt, $bk_rates[getFiscalYearForDate(date('m/d/y', strtotime($ir['for_month_year'])),"4/1","3/31")][$ir['base_currency']][$default_cur]);
				}
			}
		}
		// echo "<pre>"; print_r($trend_array); exit;
		return $trend_array;
	}
}

/*
*@method calcInvoiceDataByPracticeWiseMonthWise()
*@param array
*/
if ( ! function_exists('getOtherCostByProjectCodeByDateRangeByMonthWise'))
{
	function getOtherCostByProjectCodeByDateRangeByMonthWise($project_code = false, $default_curr = false, $start_date = false, $end_date = false)
	{
		$cur_bk_rate = get_book_keeping_rates();
		$CI   	     = get_instance();
		$cfg	     = $CI->config->item('crm'); /// load config
		$result 	 = array();
		$value 		 = array();
		if(!empty($project_code)) {
			$CI->db->select("description, cost_incurred_date, currency_type, value");
			$CI->db->from($CI->cfg['dbpref'].'project_other_cost');
			$CI->db->join($CI->cfg['dbpref'].'leads', 'lead_id = project_id');
			$CI->db->where('pjt_id', $project_code);
			//cost_incurred_date
			if(!empty($start_date)) {
				$CI->db->where("cost_incurred_date >= ", date('Y-m-d H:i:s', strtotime($start_date)));
			}
			if(!empty($end_date)) {
				$CI->db->where("cost_incurred_date <= ", date('Y-m-d H:i:s', strtotime($end_date)));
			}
			$CI->db->order_by('id', 'ASC');
			$query  = $CI->db->get();
			// echo $CI->db->last_query(); die;
			$result = $query->result_array();

			if(count($result)>0 && !empty($result)) {
				foreach($result as $rec) {
					$conver_value  = 0;
					$curFiscalYear = date('Y'); //set as default current year as fiscal year
					$curFiscalYear = getFiscalYearForDate(date("m/d/y", strtotime($rec['cost_incurred_date'])),"4/1","3/31"); //get fiscal year
					$convert_value = converCurrency($rec['value'], $cur_bk_rate[$curFiscalYear][$rec['currency_type']][$default_curr]);
					if(isset($value[date('M', strtotime($rec['cost_incurred_date']))])) {
						$value[date('M', strtotime($rec['cost_incurred_date']))] += $convert_value;
					} else {
						$value[date('M', strtotime($rec['cost_incurred_date']))] = $convert_value;
					}
					
				}
			}
		}
		return $value;
	}
}

function getOtherCostFiles($other_cost_id)
{
	$res = false;
	$CI   	     = get_instance();
	$cfg	     = $CI->config->item('crm'); /// load config
	$CI->db->select("*");
	$CI->db->from($CI->cfg['dbpref'].'other_cost_attach_file');
	$CI->db->where('other_cost_id', $other_cost_id);
	$query  = $CI->db->get();
	$result = $query->result_array();
	if(!empty($result) && count($result)>0) {
		$res = true;
	}
	return $res;
}

if ( ! function_exists('calc_fy_dates'))
{
	function calc_fy_dates($yr, $mon, $dt_type)
	{
		$next_yr_mon_arr = array('04','05','06','07','08','09','10','11','12',);
		if(in_array($mon, $next_yr_mon_arr)) {
			$yr = $yr - 1;
		}
		$dt = '01-'.$mon.'-'.$yr;
		
		$dt = date('Y-m-d', strtotime($dt)); 
		if($dt_type == 'end') {
			$dt = date('Y-m-t', strtotime($dt));
		}
		return $dt;
	}
}
