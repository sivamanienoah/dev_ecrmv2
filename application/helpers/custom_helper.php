<?php
function currency_convert()
{
	$CI  = get_instance();
	$cfg = $CI->config->item('crm'); /// load config
	
	$query = $CI->db->get($CI->cfg['dbpref'].'expect_worth');
	$res = $query->result();
	if(!empty($res)){
		foreach ($res as $cur)
		{			
			$to_Currency = 'USD';
			$amount = 1;
			$amount = urlencode($amount);
			$from_Currency = urlencode($cur->expect_worth_name);
			$to_Currency = urlencode($to_Currency);
			$url = "http://www.google.com/ig/calculator?hl=en&q=$amount$from_Currency=?$to_Currency";
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
			$var = $data['0'];
			$conversion_value = round($var,3);
			
			$CI->db->where('from',$cur->expect_worth_id);
			$CI->db->where('to',1);
			$query1 = $CI->db->get($cfg['dbpref'].'currency_rate');
			$res_num = $query1->num_rows();
			
			if($res_num>0)
			{
				if(!empty($conversion_value)){
					$CI->db->where('from',$cur->expect_worth_id);
					$CI->db->where('to',1);
					$CI->db->set('value',$conversion_value);
					$CI->db->update($cfg['dbpref'].'currency_rate');
				}
			}else{
				if(!empty($conversion_value)){
					$CI->db->set('from',$cur->expect_worth_id);
					$CI->db->set('to',1);
					$CI->db->set('value',$conversion_value);
					$CI->db->insert($cfg['dbpref'].'currency_rate');
				}				
			}
			
		}	
	}
}