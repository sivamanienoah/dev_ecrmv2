<?php

error_reporting(E_ALL);
ini_set('memory_limit', '384M');
#echo ini_get('memory_limit');
#exit;
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @package vps3.0
 * @author Asanka Dewage
 * @copyright Copyright (c) 2008 Visiontech Solutions
 */
class Backup extends CI_Controller {
	
	var $cfg;
	var $userdata;
	
	function __construct()
	{
		parent::__construct();
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
	}
	
	function index()
	{
		$result = $this->db->query('SHOW TABLES');
			$tables = $result->result_array();
			//$result = $this->db->query('SELECT * FROM '.$tables['Tables_in_vcsvi_db']);
			//$row = $result->row_array();
		echo '<pre>', print_r($tables, TRUE), '</pre>';
    }
	
	public function backup_tables($tables = '*')
	{
		error_reporting(E_ALL);
		$this->load->library('zip');
		//$this->load->plugin('phpmailer');
		
		//get all of the tables
		if($tables == '*')
		{
			$tables = array();
			$result = $this->db->query('SHOW TABLES');
			$tables = $result->result_array();
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		
		$filename = 'vcs-db-backup-'.time().'.sql';
		$filepath = dirname(FCPATH) . '/vps_temp_data/' . $filename;
		$handle = fopen($filepath,'w+');
		
		//cycle through
		foreach($tables as $table)
		{
			foreach($table as $table_k => $table_v)
			{
				$result = $this->db->query('SELECT * FROM '.$table_v);
				$num_fields = $result->num_fields();
				
				$tbl_sql = $this->db->query('SHOW CREATE TABLE '.$table_v);
				$row2 = $tbl_sql->row_array();
				$create_type = 'Create Table';
				if ( ! isset($row2['Create Table']))
				{
					if (isset($row2['Create View']))
					{
						$create_type = 'Create View';
					}
					else
					{
						continue;
					}
				}
				fwrite($handle, "\n\n".$row2[$create_type].";\n\n");
				
				foreach ($result->result_array() as $row)
				{
					fwrite($handle, 'INSERT INTO ' . $table_v . ' VALUES(');
					
					$i = 0;
					foreach ($row as $k => $v)
					{
						if ($i > 0)
						{
							fwrite($handle, ',');
						}
						$v = mysql_real_escape_string($v);
						$v = preg_replace("/\n/","\\n",$v);
						fwrite($handle, '"'.$v.'"');
						$i++;
					}
					
					fwrite($handle, ");\n");
				}
			}
			fwrite($handle, "\n\n\n");
		}
		
		fclose($handle);
		
		//email file
//		$to[] = array('asanka@visiontechdigital.com', 'Asanka Dewage');
		//$to[] = array('george@visiontechdigital.com', 'George Nissirios');
		$to[] = array('jranand@enoahisolution.com','JR Anand');
		$subject = 'ePMS daily DB Backup';
		$message = 'See attached files';
		$from = 'admin@enoahisolution.com';
		//$from_name = 'Asanka Dewage';
		
		$from_name='S Arunkumar';
		$cc = array();
		$bcc = array();
		$attachments = array();
		$this->email->from($from,$from_name);
		$this->email->to($to);
		$this->email->cc($cc);
		$this->email->bcc($bcc);
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
		
		if (is_file($filepath))
		{
			$this->zip->read_file($filepath);
			$this->zip->archive($filepath . '.zip');
			
			$attachments[] = array($filepath . '.zip', $filename . '.zip');
		}
		$this->email->attach($attachments);


		chmod($filepath, 0777);
		chmod($filepath . '.zip', 0777);
		
	   $send = $this->email->send();
		
	//	$sent = send_email($to, $subject, $message, $from, $from_name, $cc, $bcc, $attachments);
		
		if (is_file($filepath))
		{
			unlink($filepath);
		}
		
		if (is_file($filepath . '.zip'))
		{
			unlink($filepath . '.zip');
		}
		
		var_dump($sent);
	}
    
	/**
	 * Export and email new and updated customer records
	 */
	public function vcard_export($full = '')
	{
		$globs = glob(dirname(FCPATH) . '/vps_data/*.vcards.vcf');
		if (is_array($globs)) foreach ($globs as $glob)
		{
			if (is_file($glob))
			{
				@unlink($glob);
			}
		}
		
		$this->load->model('customer_model');
		
		$download_all = FALSE;
		if ($full == 'full_download')
		{
			$this->login_model->check_login();
			$download_all = TRUE;
		}
		
		$customers = $this->customer_model->get_updated_customers($download_all);
		
		if ($customers !== FALSE)
		{
			$temp_file_path = date('Ymd.Hi') . '.vcards.vcf';
			$full_file_path = dirname(FCPATH) . '/vps_data/' . $temp_file_path;
			
			$id_set = array();
			
			$fp = fopen($full_file_path, 'w+');
			
			foreach ($customers as $row)
			{
				# capture ids for update
				$id_set[] = $row['custid'];
				
				foreach ($row as $field => $value)
				{
					$row[$field] = $this->cleanup_vcard_vars($value);
				}
				
				$vcard = '';
				
				$vcard .= "BEGIN:VCARD\r\nVERSION:3.0\r\nN:{$row['last_name']};{$row['first_name']};;;";
				$vcard .= "\r\nFN:{$row['first_name']} {$row['last_name']}\r\nORG:{$row['company']};";
				$vcard .= "\r\nEMAIL;type=INTERNET;type=WORK;type=pref:{$row['email_1']}";
				
				if ($row['email_2'] != '')
				{
					$vcard .= "\r\nEMAIL;type=INTERNET;type=WORK:{$row['email_2']}";
				}
				
				$vcard .= "\r\nTEL;type=WORK;type=pref:{$row['phone_1']}";
				
				if ($row['phone_2'] != '')
				{
					$vcard .= "\r\nTEL;type=WORK:{$row['phone_2']}";
				}
				
				if ($row['phone_3'] != '')
				{
					$vcard .= "\r\nTEL;type=CELL:{$row['phone_3']}";
				}
				
				if ($row['phone_4'] != '')
				{
					$vcard .= "\r\nTEL;type=WORK;type=FAX:{$row['phone_4']}";
				}
				
				if ($row['add1_line1'] != '' || $row['add1_line2'] != '' || $row['add1_suburb'] != '' || $row['add1_state'] != '' || $row['add1_postcode'] != '')
				{
					$vcard .= "\r\nADR;type=WORK;type=pref:;;{$row['add1_line1']}";
					
					if ($row['add1_line2'] != '') $vcard .= "\, {$row['add1_line2']}";
					
					$vcard .= ";{$row['add1_state']};{$row['add1_postcode']};{$row['add1_country']}";
				}
				
				if ($row['comments'] != '')
				{
					$vcard .= "\r\nNOTE:" . str_replace(array("\r\n", "\r", "\n"), ' ', $row['comments']);
				}
				
				if ($row['www_1'] != '')
				{
					$vcard .= "\r\nURL;type=WORK;type=pref:{$row['www_1']}";
				}
				
				$vcard .= "\r\nEND:VCARD\r\n\r\n";
				
				fwrite($fp, $vcard);
				
			}
			
			fclose($fp);
			
			if ($download_all)
			{
				$this->load->helper('download');
				$data = file_get_contents($full_file_path);
				force_download($temp_file_path, $data);
				exit;
			}
			
			$this->customer_model->update_exported_customers($id_set);
			
			$this->load->plugin('phpmailer');
			
			//$send_to[] = array('george@visiontechdigital.com', 'George Nissirios');
			//$send_to[] = array('bojan@visiontechdigital.com', 'Bojan Ristevski');
			//$send_to[] = array('adrian@visiontechdigital.com', 'Adrian Hennelly');
			//$send_to[] = array('jared@visiontechdigital.com', 'Jared Codling');
			
			$send_to[]=array('ssriram@enoahisolution.com','Sriram');
			$send_to[]=array('bhramji@enoahisolution.com','Ramji');
			$subject = "Contacts exported from eNoah";
			
			$from = 'admin@enoahisolution.com';
			$from_name = 'eNoah Admin';
			$cc = array();
			$bcc = array();
			
			$attachments = array();
			if (is_file($full_file_path))
			{
				$attachments[] = array($full_file_path, $temp_file_path);
			}
			
			$timestamp = date('d-m-Y H:i');
			$message = "eNoah Contact Export
====================

Date : {$timestamp}

Any contact that has been added or updated has been listed on this file.

Save the attached '.VCF' file to the desktop.

On Address Book, use 'File' > 'Import' and select the latest file to import.
Address Book will prompt to manage any conflicting entries (these needs to be manually approved).

Tip:
Once imported, on Address Book, use 'Card' > 'Look for Duplicates' to find and merge duplicate cards you might  after importing updated contacts

--
VCS";
			
			//send_email($send_to, $subject, $message, $from, $from_name, $cc, $bcc, $attachments);
			
		
			$this->email->from($from,$from_name);
			$this->email->to($send_to);
			$this->$this->email->cc($cc);
			$this->email->bcc($bcc);
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->attach($attachments);
			$this->email->send();
			if (is_file($full_file_path))
			{
				@unlink($full_file_path);
			}
		}
	}
	
	/**
	 * Clean up variables for vcard
	 * escape commas
	 */
	private function cleanup_vcard_vars($var)
	{
		return str_replace(
						   array(',', '\r\n'),
						   array('\,', '\n'),
						   trim($var)
						   );
	}
	
	/**
	 * Process incoming emails to VCS
	 * Handle reply messages to VCS logs and add them as a log
	 */
	public function process_email_records($callee = '')
	{
		$check_time = (int) date('i');
		if ($callee == 'office_cron' && ($check_time == 0 || $check_time % 15 == 0 ))
		{
			return;
		}
		
		$server = 'mail.webflowbos.com';
		$user = 'vcs@webflowbos.com';
		$pass = 'qwerty123#';
		
		//$server = 'mail.visiontechdigital.com';
		//$user = 'vcs@visiontechdigital.com';
		//$pass = '10vcsvi';
		
		$mbox = imap_open("{{$server}:143/notls}INBOX", "{$user}", "{$pass}");
        
        $headers = imap_headers($mbox);
		
		$affected_jobs = '';
		
		$i = 1;
		$j = 0;
		foreach ($headers as $msg)
		{
			//if ($i > 10) break;
			
			echo "<pre>";
			print_r ($msg);
			
			$info = imap_headerinfo($mbox, $i);
			 echo $info->Msgno;
			
			if ( ! preg_match('/^VCS log \-/', $info->subject) && preg_match('/\[ref#([0-9]+)\]/', $info->subject, $matches))
			{
				# matched job
				
				$this->db->where('jobid', $matches[1]);
				$job_details = $this->db->get($this->cfg['dbpref'] . 'jobs');
				
				if ($job_details->num_rows() > 0) 
				{
					$struct = imap_fetchstructure($mbox, $info->Msgno);
					
					$body = imap_fetchbody($mbox, $info->Msgno, '1.1');
					
					if ($body == '')
					{
						$body = imap_fetchbody($mbox, $info->Msgno, '1');
					}
					
					/**
					 * encoding types
					  	0	7BIT
						1	8BIT
						2	BINARY
						3	BASE64
						4	QUOTED-PRINTABLE
						5	OTHER
					**/
					if ($struct->encoding == 3) # base64
					{
						$body = base64_decode(str_replace("\n", '', $body));
					}
					else
					{
						$body = quoted_printable_decode($body);
					}
					
					#echo '<pre>' . print_r($body, true) . '</pre>';
					
				//$log_data = explode('--visiontechdigital.com', $body);
					$log_data=explode('--enoahisolution.com', $body)
					//print_r($info);
					
					if (trim($log_data[0]) != '')
					{
						$log_data[0] = strip_tags($log_data[0]);
						$ins['jobid_fk'] = $matches[1]; # job id
						$ins['userid_fk'] = 28;			# VCS auto admin
						$ins['date_created'] = date('Y-m-d H:i:s'); # date
						$ins['log_content'] = "From: {$info->fromaddress}\n\n{$log_data[0]}";
						
						# check for duplicates
						$this->db->where('log_content', $ins['log_content']);
						$q = $this->db->get($this->cfg['dbpref'] . 'logs');
						
						if ($q->num_rows() < 1)
						{
							$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
							$j++;
							
							# for logging
							$affected_jobs .= $matches[1] . '[' . $struct->encoding . '],';
						}
					}
				}
			}
			else if (! preg_match('/^VCS Lead log \-/', $info->subject) && preg_match('/\[lead#([0-9]+)\]/', $info->subject, $matches))
			{
				# matched lead
				
				$this->db->where('leadid', $matches[1]);
				$job_details = $this->db->get($this->cfg['dbpref'] . '_leads');
				
				if ($job_details->num_rows() > 0) 
				{
					$struct = imap_fetchstructure($mbox, $info->Msgno);
					
					$body = imap_fetchbody($mbox, $info->Msgno, '1.1');
					
					if ($body == '')
					{
						$body = imap_fetchbody($mbox, $info->Msgno, '1');
					}
					
					if ($struct->encoding == 3) # base64
					{
						$body = base64_decode(str_replace("\n", '', $body));
					}
					else
					{
						$body = quoted_printable_decode($body);
					}
					
					#echo '<pre>' . print_r($body, true) . '</pre>';
					
					//$log_data = explode('--visiontechdigital.com', $body);
					$log_data = explode('--enoahisolution.com', $body);
					//print_r($info);
					
					if (trim($log_data[0]) != '')
					{
						$log_data[0] = strip_tags($log_data[0]);
						$ins['jobid_fk'] = $matches[1]; # lead id
						$ins['userid_fk'] = 28;			# VCS auto admin
						$ins['date_created'] = date('Y-m-d H:i:s'); # date
						$ins['log_content'] = "From: {$info->fromaddress}\n\n{$log_data[0]}";
						
						# check for duplicates
						$this->db->where('log_content', $ins['log_content']);
						$q = $this->db->get($this->cfg['dbpref'] . '_lead_logs');
						
						if ($q->num_rows() < 1)
						{
							$this->db->insert($this->cfg['dbpref'] . '_lead_logs', $ins);
							$j++;
							
							# for logging
							$affected_jobs .= $matches[1] . '[' . $struct->encoding . '],';
						}
					}
				}
			}
			
			imap_delete($mbox, $info->Msgno);
			
			$i++;
		}
		
		imap_expunge($mbox);
		
        imap_close($mbox);
		
		/* log activity */
		if ($j > 0)
		{
			$file = rtrim(dirname(FCPATH), '/') . '/vps_temp_data/email_access_file.txt';
			$fp = fopen($file, 'a+');
			$time = date('H:i:s Y-m-d');
			
			$numemails = $i - 1;
			
			$affected_jobs = ' -> affected_jobs - ' . rtrim($affected_jobs, ',');
			
			fwrite($fp, "{$time} -> processed {$numemails} and added {$j} to logs.{$affected_jobs}\n");
			fclose($fp);
		}
		
		echo "completed";
		
	}
	
	public function to_the_cloud()
	{
		$this->load->view('cloud_backup');
	}
	
	public function load_notification_customers()
	{
		#return false;
		
		$sql = "SELECT CONCAT_WS(' ', first_name, last_name) AS name, 
email_1, email_2, email_3, email_4 FROM crm_customers c
JOIN crm_jobs j ON custid = custid_fk AND job_status IN (2, 3, 4, 5, 6, 15, 30, 31, 32)";
		$q = $this->db->query($sql);
		$data = $q->result();
		foreach ($data as $row)
		{
			for ($i = 1; $i < 5; $i++)
			{
				$var = "email_{$i}";
				if (trim($row->{$var}) != '')
				{
					$c = $this->db->get_where('crm_customer_notify', array('email' => $row->{$var}));
					if ($c->num_rows() < 1)
					{
						$this->db->insert('crm_customer_notify', array('name' => $row->name, 'email' => $row->{$var}));
					}
					
					unset($c);
				}
			}
		}
	}
	
	public function send_customer_notification()
	{
		$message = <<< EOD
Dear valued customers,


Due to an unexpected power outage in our Alexandria, Sydney production studio today at approximately 2pm we expect to experience some delays in attending to your email and telephone enquiry. Although our production schedule will resume as per usual on Monday via our contingency plans, we will not be back in full operation in our Alexandria, Sydney studio until Wednesday 19th January 2010.

In summary it is business as usual for eNoah  iSolution (Sydney) however during this time we appreciate your patience and support and expect to make a full recovery from the power outage within the next couple of days.

Thank you and have a great weekend,


Regards

	
George Nissirios
Managing Director
1300 130 656
EOD;
		
		# get mailer class
		//require BASEPATH . 'plugins/phpmailer/class.phpmailer.php';
		
		//$mail = new PHPMailer();
		
		/*$mail->FromName = 'eNoah  iSolution';
		$mail->From = 'syd@visiontechdigital.com';
		$mail->Subject = 'eNoah  iSolution Operations Disruption';
		
		$mail->Host = 'mail4.ilisys.com.au';
		$mail->IsSMTP();
		$mail->Body = $message;
		*/
		# get list
		$from = 'admin@enoahisolution.com';
		$from_name='eNoah i Solution';
		$to = 'jranand@enoahisolution.com';
		$subject = 'eNoah  iSolution Operations Disruption';
		$this->email->from($from,$from_name);
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
		
		$this->db->group_by("email"); 
		$q = $this->db->get_where('crm_customer_notify', array('sent' => 0, 'notification' => 1));
		$data = $q->result();
		
		$sent = $count = 0;
		$email_list = '';
		foreach ($data as $row)
		{
			$mail->AddAddress($row->email);
			if ($mail->Send())
			{
				$this->db->where('email', $row->email);
				$this->db->update('crm_customer_notify', array('sent' => 1));
				$sent++;
				$email_list .= "{$row->email} ($row->name)\n<br />";
			}
			
			$count++;
			
			# clear addresses
			//$mail->ClearAllRecipients();
		}
		
		echo "All: $count, Sent: $sent \n<br />$email_list";
	}
}
