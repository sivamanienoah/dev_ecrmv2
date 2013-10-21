<?php
require ('sales_divisions.ini');
require ('job_categories.ini');

/*
*
* CRM specific config variables
* Such as DB table prefix and user levels
* 
*/
$config['crm']['app_name']      = 'eNoah';
$config['crm']['app_full_name'] = 'eNoah Customer Relationship Management';
$config['crm']['app_version']   = '1.0.17';
$config['crm']['app_date']      = '16.08.2013';

$config['crm']['dbpref']        = 'crms_';
$config['crm']['theme']         = 'eNoah';
$config['crm']['data']          = 'vps3_data';

$config['crm']['sales_codes']   = array(
									'MS' => 'Manoj Sherman',
									'BS' => 'Balasubramanian',
									'RM' => 'Ramesh Kumar',
									'MV' => 'Mukesh Vaidyanathan',
									'JK' => 'Jakathish Kumar',
									'KU' => 'Kumaran',
									'SS' => 'Sreevidya Sribabu'
									);
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
								
$config['crm']['tasks_search']  = array(
                                    0 => 'Work In Progress',
                                    1 => 'Completed',
									-1 => 'All'
                                );

$config['crm']['max_allowed_users'] = array(
											0=>100
											);
											
$config['crm']['director_emails']  = array(
                                    'Admin' => 'rshankar@enoahisolution.com',
                                   );		

$config['crm']['management_emails'] = array(
									'Senior Management' => 'rshankar@enoahisolution.com',
								    );

# keep in sync with above

$config['crm']['our_products']     = array(
                                    0 => array(
                                               'name' => 'CONTENT MANAGEMENT',
                                               'desc' => "WebPublisher Content Management System
WebPublisherCMS is an easy to use content management system residing on the back-end of your website. With site administrators access to designated pages, you can manage your website content yourself including text and images. WebPublisherCMS (1 x lifetime user license per domain).",
                                               'price' => 990
                                            ),
                                    1 => array(
                                               'name' => 'EMAIL MARKETING',
                                               'desc' => "NewsletterPRO Email Marketing System 
An affordable and value for money marketing solution for just over $1 dollar a day. Communicate directly to your entire client base with customised, content rich html based email. With Opt-in / Opt-out SPAM Act compliance, NewsletterPRO is the easiest way to send content rich html newsletters and promotional material to your customers, when you want... where you want... how you want!

*$175.00 once off setup and installation, including template customisation 
**$39.95 per month direct debit from valid credit card 
***minimum commitment of 24 months",
                                               'price' => 175
                                            ),
                                    2 => array(
                                               'name' => 'E-COMMERCE',
                                               'desc' => "V-Shop Online Shopping System 
If you are selling a product online and are seeking an eCommerce solution that can be managed from anywhere in the world and with very little web experience, then V-Shop Online Shopping System is the choice without a doubt for your business. V-Shop easily connects to an existing static website and takes you to the eCommerce arena accepting the entire transaction from your website.
Features Included: 
- Content Management System 
- Product Catalogue 
- Shopping Cart facility + Checkout 
- Connection to PayPal directly from Checkout 
- Setup, installation and customisation 
- Business hours technical support 
- 1 hour of in-studio training

*For an additional fee, V-Shop can be connected to a payment gateway of your choice. 
**V-Shop is a scalable product and additional functionality required is available for a fee.",
                                               'price' => 1990
                                            ),
                                    3 => array(
                                               'name' => 'WebFlow Workflow Management',
                                               'desc' => "",
                                               'price' => 1990
                                            ),
                                    4 => array(
                                                'name' => 'INTERFACE DESIGN',
                                                'desc' => "We will design a GUI (Graphical User Interface) and establish the new web page layout rules for master content pages and subsequent information pages. A new primary navigation panel and information architecture will be considered during this process. eNoah  iSolution to establish a minimum of 3 master layout page designs including: home content layout, master content layout #1, master content layout #2 which will form the basis of the remainder of the website and house the entire website content thereafter.",
                                                'price' => 2800
                                            ),
                                    5 => array(
                                                'name' => 'BUSINESS GRADE WEBSITE HOSTING',
                                                'desc' => "The Power Cluster delivers faster response times, faster processing of dynamic content and faster downloads than other hosting platforms.

Our Power Cluster Hosting Package includes: 
5 GB Storage, 100 GB Downloads, 10 GB Uploads, PHP 5 + Apache 2, MySQL database, Unlimited email, Load-balanced servers, Hourly backups, Free instant restores, Spam filtering, Email virus blocking, Performance Compression, Reliable Session Management, Service level guarantee (http://www.ilisys.com.au/explore/sla/) and unlimited phone support.

Web Hosting is billed annually.",
                                                'price' => 300
                                            )
                                );
$config['crm']['item_section'] = array(
                                    0 => array(
                                                'type' => 'Programming : CMS connections',
                                                'hours' => 0.5
                                            ),
                                    1 => array(
                                               'type' => 'Programming : XML connections',
                                               'hours' => 1
                                            ),
                                    2 => array(
                                                'type' => 'Programming : General Forms',
                                                'hours' => 1
                                            ),
                                    3 => array(
                                                'type' => 'programming : Complex Forms',
                                                'hours' => 3
                                            )
                                );
$config['crm']['item_inventory'] = array(
                                    0 => array(
                                                'desc' => '',
                                                'hours' => '',
                                                'belong_to' => ''
                                            ),
                                    1 => array(
                                                'desc' => 'Standard XHTML page',
                                                'hours' => 2,
                                                'belong_to' => ''
                                            ),
                                    2 => array(
                                                'desc' => 'Editable, CMS connected page',
                                                'hours' => 2,
                                                'belong_to' => 0
                                            ),
                                    3 => array(
                                                'desc' => 'XML connected page',
                                                'hours' => 2,
                                                'belong_to' => 1
                                            ),
                                    4 => array(
                                                'desc' => 'General Form up to 10 fields (email details to a specified address)',
                                                'hours' => 2,
                                                'belong_to' => 2
                                            ),
                                    5 => array(
                                                'desc' => 'Custom Form (can be multipage, save details to a database)',
                                                'hours' => 3,
                                                'belong_to' => 3
                                            )
                                );
$config['crm']['fixed_item_inventory'] = array(
                                    0 => array(
                                               'name' => 'User Management System',
                                               'desc' => "Online contact database system providing the ability to create individual user 
accounts for visitors interested in gaining access to the protected
sections of the website.",
                                               'price' => 2000
                                            ),
                                    1 => array(
                                               'name' => 'Lightbox Image Gallery',
                                               'desc' => "Image thumbnails will be enlarged on an overlay
that would mask the existing page and the enlarged image would be
animated into place and displayed with controls
navigate through the gallery.",
                                               'price' => 175
                                            ),
                                );
								
print "dfgdgdgdgdd------------";exit;
?>