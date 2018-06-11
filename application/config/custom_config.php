<?php

/*
*
* CRM specific config variables
* Such as DB table prefix and user levels
* 
*/
$config['crm']['app_name']      = 'eNoah';
$config['crm']['app_full_name'] = 'eNoah Customer Relationship Management';
$config['crm']['app_version']   = '1.2.01';
$config['crm']['app_date']      = '02-08-2016';

$config['crm']['dbpref']        = 'crm_';
$config['crm']['theme']         = 'eNoah';
$config['crm']['data']          = '';

$config['crm']['domain_status'] = array(
                                    0 => 'Not Delegated',
                                    1 => 'Active Hosting',
                                    2 => 'Web Forwarding',
                                    3 => 'Inactive'
                                );
$config['crm']['host_location'] = array(
									'' => 'HOST LOCATION',
                                    1 => 'eNoah Domain'
                                );
$config['crm']['host_status'] = array(
									'' => 'HOST STATUS',
                                    1 => ' Live ',
                                    2 => 'Staging',
                                    3 => 'Hosted by Client',
									4 => 'Plan to Archive',
									5 => 'Archived'
                                );
$config['crm']['domain_ssl_status'] = array(
                                    0 => 'No SSL',
                                    1 => 'Shared SSL',
                                    2 => 'Dedicated SSL'
                                );


$config['crm']['job_complete_status'] = array(
                                    0 => 'Pending Production',
                                    1 => '10%',
                                    2 => '20%',
                                    3 => '30%',
                                    4 => '40%',
                                    5 => '50%',
                                    6 => '60%',
                                    7 => '70%',
									8 => '80%',
                                    9 => '90%',
                                    10 => '100%',
                                    11 => 'Complete'
                                );
								
$config['crm']['milestones_complete_status'] = array(
                                    0 => '0%',
                                    10 => '10%',
                                    20 => '20%',
                                    30 => '30%',
                                    40 => '40%',
                                    50 => '50%',
                                    60 => '60%',
                                    70 => '70%',
									80 => '80%',
                                    90 => '90%',
                                    100 => '100%'
                                );
								
$config['crm']['milestones_status'] 	= array(0 => 'Scheduled',1 => 'In Progress',2 => 'Completed');
								
$config['crm']['billing_type'] 			= array(1 => 'Milestone Based',2 => 'Monthly Based');
$config['crm']['tasks_search']  		= array(0 => 'Work In Progress',1 => 'Completed',-1 => 'All');

$config['crm']['max_allowed_users'] 	= array(0=>1500);
$config['crm']['director_emails']  		= array('Admin' => 'ssriram@enoahisolution.com',);		

$config['crm']['management_emails'] 	= array('Senior Management' => 'ssriram@enoahisolution.com',);
$config['crm']['account_emails'] 		= array('Accounts' => 'kbalaji@enoahisolution.com',);
//$config['crm']['account_emails'] 		= array('Accounts' => 'ssriram@enoahisolution.com',);
/* $config['crm']['account_emails_cc'] 	= array('mukesh' => 'ssriram@enoahisolution.com',);
$config['crm']['bpo_account_emails_cc'] = array('mukesh' => 'ssriram@enoahisolution.com',);
$config['crm']['eads_account_emails_cc']= array('mukesh' => 'ssriram@enoahisolution.com',); */
$config['crm']['its_invoice_emails_cc'] = array('Mukesh' => 'ssriram@enoahisolution.com','Harihara' => 'ssriram@enoahisolution.com',);
$config['crm']['bpo_invoice_emails_cc'] = array('Subbu' => 'ssriram@enoahisolution.com',);
$config['crm']['crm_admin'] 			= array('crm_admin' => 'ssriram@enoahisolution.com');
$config['crm']['fy_months']  			= array('04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec','01'=>'Jan','02'=>'Feb','03'=>'Mar');
# keep in sync with above

?>