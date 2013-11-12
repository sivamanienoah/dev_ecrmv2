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
	if (get_default_currency()) {
		$default_currency = get_default_currency();
		$to_Currency = $default_currency['expect_worth_name'];
		$to_Currency_id = $default_currency['expect_worth_id'];
	} else {
		$to_Currency = 'USD';
		$to_Currency_id = 1;
	}
	
	$CI  = get_instance();
	$cfg = $CI->config->item('crm'); /// load config
	
	$query = $CI->db->get($CI->cfg['dbpref'].'expect_worth');
	$res = $query->result();
	if(!empty($res)){
		foreach ($res as $cur)
		{			
			// $to_Currency = 'USD';
			$amount = 1;
			$amount = urlencode($amount);
			$from_Currency = urlencode($cur->expect_worth_name);
			$to_Currency = urlencode($to_Currency);
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

			$url = "https://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";

			$amount = urlencode($amount);
			$from_Currency = urlencode($from_Currency);
			$to_Currency = urlencode($to_Currency);
			$get = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency");
			$get = explode("<span class=bld>",$get);
			$get = explode("</span>",$get[1]);  
			$converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
			
			$conversion_value = round($converted_amount, 3);
			updt_currency($cur->expect_worth_id, $to_Currency_id, $conversion_value);
			
		}
		updt_currency($to_Currency_id, $to_Currency_id, 1);
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