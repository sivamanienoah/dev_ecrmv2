<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

// Fourth Database added
// $active_group = "support";
// $active_record = false;
$db['support']['hostname'] = 'localhost';
$db['support']['username'] = 'dev_support';
$db['support']['password'] = 'Su996thj';
$db['support']['database'] = 'dev_support';
$db['support']['dbdriver'] = 'mysql';
$db['support']['dbprefix'] = 'mantis_';
$db['support']['pconnect'] = FALSE;
$db['support']['db_debug'] = TRUE;
$db['support']['cache_on'] = FALSE;
$db['support']['cachedir'] = '';
$db['support']['char_set'] = 'utf8';
$db['support']['dbcollat'] = 'utf8_general_ci';
$db['support']['swap_pre'] = '';
$db['support']['autoinit'] = TRUE;
$db['support']['stricton'] = FALSE;

// Third Database
// $active_group = "econnect";
// $active_record = false;
$db['econnect']['hostname'] = 'localhost';
$db['econnect']['username'] = 'dev_econnectv9';
$db['econnect']['password'] = 'ecoNewv9';
$db['econnect']['database'] = 'dev_econnectv9';
$db['econnect']['dbdriver'] = 'mysql';
$db['econnect']['dbprefix'] = '';
$db['econnect']['pconnect'] = FALSE;
$db['econnect']['db_debug'] = TRUE;
$db['econnect']['cache_on'] = FALSE;
$db['econnect']['cachedir'] = '';
$db['econnect']['char_set'] = 'utf8';
$db['econnect']['dbcollat'] = 'utf8_general_ci';
$db['econnect']['swap_pre'] = '';
$db['econnect']['autoinit'] = TRUE;
$db['econnect']['stricton'] = FALSE;

// Second Database
$active_group = "timesheet";
$active_record = TRUE;
$db['timesheet']['hostname'] = 'localhost';
$db['timesheet']['username'] = 'dev_timesheet';
$db['timesheet']['password'] = 'tim@Dev4';
$db['timesheet']['database'] = 'dev_timesheet';
$db['timesheet']['dbdriver'] = 'mysql';
$db['timesheet']['dbprefix'] = 'enoah_';
$db['timesheet']['pconnect'] = FALSE;
$db['timesheet']['db_debug'] = TRUE;
$db['timesheet']['cache_on'] = FALSE;
$db['timesheet']['cachedir'] = '';
$db['timesheet']['char_set'] = 'utf8';
$db['timesheet']['dbcollat'] = 'utf8_general_ci';
$db['timesheet']['swap_pre'] = '';
$db['timesheet']['autoinit'] = TRUE;
$db['timesheet']['stricton'] = FALSE;


$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'dev_ecrmv2';
$db['default']['password'] = 'Ecr@vm2';
$db['default']['database'] = 'dev_ecrmv2';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;




/* End of file database.php */
/* Location: ./application/config/database.php */