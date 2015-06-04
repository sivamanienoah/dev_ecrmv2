<?php

//require("class.AuthenticationManager.php");
//require("class.CommandMenu.php");
//include("table_names.inc");
require("class.phpmailer.php");

/*mail settings*/
$mail = new PHPMailer();
  
$mail->IsSMTP();
$mail->SMTPDebug = 1; 
$mail->SMTPAuth = false;
$mail->Host = "localhost";
$mail->Port = 587;
/* 
$mail->Host = "192.168.0.201";
$mail->Port = 25;
$mail->Username = "webmaster@enoahisolution.com";
$mail->Password = "eNoah123#"; */
$mail->From = "webmaster@enoahprojects.com";
 
$mail->FromName = "webmaster";
$email="mthiyagarajan@enoahisolution.com";
//$email="subbiah.pradeep@gmail.com";
//$mail->AddEmbeddedImage('world.png', 'my-worldmap', 'world.png');

$content='<div class="map_wrap">
<img style="margin-top:10px;" src="cid:my-worldmap" alt="World Map" class="magnify" title="World Map" 
value="" id="main_map" align="center" oncontextmenu="return false;" usemap="#Map"/>
<map name="Map"><area shape="poly" coords="540,185,467,149,520,143,557,125,579,124" href="#" alt="china" title="Internal: 25, External: 50">
  <area shape="poly" coords="240,231,258,236,253,248,226,290,218,286,223,269,216,251,199,243,190,248,187,236,200,220,211,214,235,227" href="#" alt="brazil" title="Internal: 10, External: 20">
  <area shape="poly" coords="475,206,462,184,472,158,484,171,509,172" href="#" title="Internal: 50, External: 100" alt="india">
  <area shape="poly" coords="57,64,55,97,84,116,163,123,206,116,219,110,110,52" href="#">
  <area shape="poly" coords="371,208,371,206,373,184,389,183,401,184,394,208" href="#">
</map>
</div><br/><br/>';


$mail->AddAddress($email);
$mail->IsHTML(true);
$mail->Subject = "World Map - test ".date("H:i:s");
$mail->Body = $content;
if ($mail->Send() == true) {
	$message = "send successfully"; 
}
else {
	$message = "Error in sending mail ";
}
echo "<br>".$message ;
exit();

?>