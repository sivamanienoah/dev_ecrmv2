<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);
define('SSOURL', "http://sso.enoahisolution.com/"); 
define('EXPIRYDAYS', 3); 
define('CLR_CACHE', 'Hgrsilo'); 
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');
define('VIEWPATH',APPPATH.'/views/');
define('UPLOAD_PATH',SITE_FILE_PATH.'/crm_data/');
define('PDF_TEMP_PATH',SITE_FILE_PATH.'/crm_data/pdf_temp'); //for exporting charts as pdfs

define('IS_ZERO', 0); 
define('CONST_ZERO',0); 
define('CONST_ONE',1); 
define('CONST_HUNDRED',100); 
define('CONST_TEN_LAKH',1000000);
//Levels
define('LVL_GLOBAL_ACCESS', 1);
//Roles
define('ROLE_ADMIN', 1);
define('ROLE_MGMT', 2);
define('ROLE_FINANCE', 4);
define('ROLE_RESELLER', 14);


/* End of file constants.php */
/* Location: ./application/config/constants.php */