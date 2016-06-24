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
function get_current_financial_year(){
	if(date('m')<'04'){
		$financial_year= date('Y',strtotime('-1 year'))."-".date('Y');
	}else{
		$financial_year= date('Y')."-".date('Y',strtotime('+1 year'));
	}
	return $financial_year;
}
/*Get max hours based on practice id*/
function get_practice_max_hours($practice_id=false){
	$CI   	    = get_instance();
	$cfg	    = $CI->config->item('crm'); /// load config
	if($practice_id){
		$qry 	    = $CI->db->get_where($CI->cfg['dbpref']."practice_max_hours_history", array('practice_id'=>$practice_id));
		
		if(count($qry->result_array())>0 && !empty($qry->result_array())){
			return $qry->result_array();
		}else{
			return array();
		} 
	}	
}