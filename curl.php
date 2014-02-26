<?php

  $curl_handle=curl_init();
  curl_setopt($curl_handle,CURLOPT_URL,'http://10.0.9.36/dev/projects/ecrmv2/api_leads/example/user/id/1/format/json/X-API-KEY/12345');
  curl_setopt($curl_handle, CURLOPT_POST, true);
  curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "name=enoahcrm&email=sdsdsd@sss.com&phone=111111&enquiry=We have two options here, CURLOPT_POST which turns HTTP POST on, and CURLOPT_POSTFIELDS which contains an array of our post data to submit. This can be used to submit data to ");
  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($curl_handle,CURLOPT_HTTPHEADER,array('domainname:10.0.9.36'));
  curl_setopt($curl_handle,CURLOPT_AUTOREFERER,1);
   curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, "http://www.google.com");
  $buffer = curl_exec($curl_handle);
  curl_close($curl_handle);
  if (empty($buffer)){
      print "Nothing returned from url.<p>";
  }else{
      print $buffer;
  }
?>