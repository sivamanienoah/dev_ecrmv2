<?php
/*
 *E-Mail configuration
 */
/* 
$config['protocol']  = "mail";	// mail/sendmail/smtp
$config['smtp_host'] = "localhost";	// SMTP Server.  Example: mail.earthlink.net
// $config['smtp_user'] = "webmaster@enoahisolution.com";	// SMTP Username
// $config['smtp_pass'] = "eNoah123#";		// SMTP Password
$config['smtp_port'] = "587";		// SMTP Password
$config['mailtype']  = 'html';  // text/html  Defines email formatting
$config['wordwrap']  = TRUE;  // TRUE/FALSE  Turns word-wrap on/off
$config['charset']   = "utf-8";  // Default char set: iso-8859-1 or us-ascii */

$config['protocol'] = 'sendmail';       
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = TRUE;
$config['mailtype'] = 'html';

?>