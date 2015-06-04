<?php

require("class.phpmailer.php");

/*mail settings*/
$mail = new PHPMailer();
$mail->Host = "localhost";
//$mail->Port = 587;
$mail->From = "webmaster@enoahprojects.com";
$mail->FromName = "Timesheet Admin";
$email="mthiyagarajan@enoahisolution.com";
$content='<strong>1</strong>This is for testing the timesheet cron';
$mail->AddAddress($email);
$mail->IsHTML(true);
$mail->Subject = "Timesheet Mail Test";
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