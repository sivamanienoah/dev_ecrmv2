<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Leads extends CI_Controller {
	
	var $cfg;
	var $userdata;
	
	function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('welcome_model');
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
	}
	
	function index($lead = 0)
	{
		$data = array();
		
		if (isset($_POST['custid_fk']) && is_numeric($_POST['custid_fk']))
		{
			$this->load->plugin('phpmailer');
			$ins['custid_fk'] = $_POST['custid_fk'];
			$ins['date'] = date('Y-m-d H:i:s');
			$ins['description'] = $_POST['description'] . "\n\nLead Added By: {$this->userdata['first_name']} {$this->userdata['last_name']}";
			$ins['belong_to'] = $_POST['job_belong_to'];
			
			$this->db->insert($this->cfg['dbpref'] . '_leads', $ins);
			$insert_id = $this->db->insert_id();
			
			/*$email_body = "Hi ,

This is New Lead notification.
----------------------------------

Client:
{$_POST['cust_form_details']}

{$ins['description']}

http://192.168.0.235:85/vcslocaldev/leads/index/{$insert_id}

----------------------------------
End of Message
";*/
			$to = array();
			$to[] = array('ssriram@enoahisolution.com', 'Sriram');
			//$to[] = array('sarunkumar@enoahisolution.com', 'Arunkumar');
			
			$subject = 'New Lead Notification';
			$from = 'admin@enoahisolution.com';
			$from_name = 'eNoah Admin';
			$this->email->from($from,$from_name);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($email_body);
			$this->email->send();
			
			
			//send_email($to, $subject, $email_body, $from, $from_name);*/
			
			redirect('leads/index/' . $insert_id);
			exit();
		}
		
		$additional = '';
		
		if ($this->userdata['level'] == 4)
		{
			$additional = "AND `belong_to` = '{$this->userdata['sales_code']}'";
		}
		
		$sql = "SELECT *,
					(SELECT `start_date` FROM `crm_lead_tasks` WHERE leadid = leadid_fk AND status < 100 ORDER BY start_date DESC LIMIT 1) AS next_task_date,
					(SELECT `task` FROM `crm_lead_tasks` WHERE leadid = leadid_fk ORDER BY start_date LIMIT 1) AS next_task_action
				FROM `crm_leads`, `crm_customers`
				WHERE `custid` = `custid_fk`
				AND `lead_status` = 1
				{$additional}
				ORDER BY `belong_to`, `next_task_date` ASC, `date` DESC";
				
		$data['leads_list'] = array();
		
		$leads_rs = $this->db->query($sql);
		if ($leads_rs->num_rows() > 0)
		{
			$data['leads_list'] = $leads_rs->result_array();
		}
		
		if (is_numeric($lead) && $lead > 0)
		{
			$data['lead_selected'] = $lead;
			
			if (isset($_POST['job_belong_to_edit']))
			{
				$ins['belong_to'] = $_POST['job_belong_to_edit'];
				
				if ($this->db->update($this->cfg['dbpref'] . '_leads', $ins, array('leadid' => $lead)))
				{
					$this->session->set_flashdata('confirm', array('Lead Service Details Updated!'));
                    redirect('leads/index/' . $lead);
				}
			}
			
			if (isset($_POST['lead_status']))
			{
				$ins['lead_status'] = $_POST['lead_status'];
				
				if ($this->db->update($this->cfg['dbpref'] . '_leads', $ins, array('leadid' => $lead)))
				{
					$this->session->set_flashdata('confirm', array('Lead Status Updated!'));
                    redirect('leads/index/' . $lead);
				}
			}
			
			$owner = FALSE;
			if ($this->userdata['level'] == 4)
			{
				$owner = $this->userdata['sales_code'];
			}
			
			if ($result = $this->welcome_model->get_lead($lead, $owner))
			{
				$data['quote_data'] = $result;
				
				$data['log_html'] = '';
				
				$this->db->where('jobid_fk', $data['quote_data']['leadid']);
				$this->db->order_by('date_created', 'desc');
				$logs = $this->db->get($this->cfg['dbpref'] . '_lead_logs');
				
				if ($logs->num_rows() > 0)
				{
					$log_data = $logs->result_array();
					$this->load->helper('url');
					
					foreach ($log_data as $ld)
					{
						
						$this->db->where('userid', $ld['userid_fk']);
						$user = $this->db->get($this->cfg['dbpref'] . 'users');
						$user_data = $user->result_array();
						
						$log_content = nl2br(auto_link($ld['log_content'], 'url', TRUE));
						
						$fancy_date = date('d-m-Y H:i:s', strtotime($ld['date_created']));
						
						$stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';
						
						$table = <<< HDOC
<div class="log{$stick_class}">
	<p class="data">
		<span>{$fancy_date}</span>
	{$user_data[0]['first_name']} {$user_data[0]['last_name']}
	</p>
	<p class="desc">
		{$log_content}
	</p>
</div>
HDOC;
						$data['log_html'] .= $table;
						unset($table, $user_data, $user, $log_content);
					}
				}
				
				$data['user_accounts'] = array();
				$users = $this->db->get($this->cfg['dbpref'] . 'users');
				if ($users->num_rows() > 0)
				{
					$data['user_accounts'] = $users->result_array();
				}
				
				$fcpath = dirname(FCPATH) . '/';
				$f_dir = $fcpath . 'vps_lead_data/' . $lead . '/';
				$data['job_files_html'] = $this->welcome_model->get_job_files($f_dir, $fcpath);
				
			}
			else // no data
			{
				echo "The Lead does not exist, or you do not own this lead!";
				exit();
			}
		}
		
		$data['active_leads'] = 1;
		$this->load->view('lead_list_view', $data);
    }
	
	function declined()
	{
		$additional = '';
		
		if ($this->userdata['level'] == 4)
		{
			$additional = "AND `belong_to` = '{$this->userdata['sales_code']}'";
		}
		
		$sql = "SELECT *,
					(SELECT `start_date` FROM `crm_lead_tasks` WHERE leadid = leadid_fk AND status < 100 ORDER BY start_date DESC LIMIT 1) AS next_task_date,
					(SELECT `task` FROM `crm_lead_tasks` WHERE leadid = leadid_fk ORDER BY start_date LIMIT 1) AS next_task_action
				FROM `{$this->cfg['dbpref']}_leads`, `{$this->cfg['dbpref']}customers`
				WHERE `custid` = `custid_fk`
				AND `lead_status` = 2
				{$additional}
				ORDER BY `belong_to`, `date` DESC";
				
		$data['leads_list'] = array();
		
		$leads_rs = $this->db->query($sql);
		if ($leads_rs->num_rows() > 0)
		{
			$data['leads_list'] = $leads_rs->result_array();
		}
		
		$data['declined_leads'] = 1;
		$this->load->view('lead_list_view', $data);
	}
	
	/**
	 * Deletes a lead
	 */
	function delete($lead = 0, $ajax = FALSE)
	{
		$this->db->delete("{$this->cfg['dbpref']}_leads", array('leadid' => $lead));
		$this->db->delete("{$this->cfg['dbpref']}_lead_logs", array('jobid_fk' => $lead));
		$this->db->delete("{$this->cfg['dbpref']}_lead_tasks", array('leadid_fk' => $lead));
		$this->session->set_flashdata('confirm', array("Item deleted from the system"));
		if ($ajax != FALSE)
		{
			echo "{msg: 'Lead Deleted'}";
		}
		else
		{
			redirect('leads');
		}
	}
	
	/**
	 * Save the next item
	 */
	function add_next_action($leadid)
	{
		$errors = array();
		
		$json['error'] = FALSE;
		$ins['next_action'] = $_POST['action'];
		$ins['action_hours'] = (int) $_POST['action_hours'];
		$ins['action_mins'] = (int) $_POST['action_mins'];
		
		$action_date = explode('-', trim($_POST['action_date']));
		
		if (count($action_date) != 3 || ! $next_action_date = mktime(0, 0, 0, $action_date[1], $action_date[0], $action_date[2]))
		{
			$errors[] = 'Invalid Action Date!';
		}
		else if ($next_action_date < strtotime(date('Y-m-d')))
		{
			$errors[] = 'Next action date cannot be earlier than today!';
		}
		
		if (count($errors) > 0)
		{
			$json['error'] = TRUE;
			$json['errormsg'] = implode("\n", $errors);
		}
		else
		{
			$ins['next_action_date'] = date('Y-m-d H:i:s', $next_action_date);
			
			$this->db->where('leadid', $leadid);
			$this->db->update("{$this->cfg['dbpref']}_leads", $ins);
		}
		
		echo json_encode($json);
	}
	
	/**
	 * Adds a log to a job
	 * based on post data
	 *
	 */
	function add_log()
    {
        if (isset($_POST['jobid']) && isset($_POST['userid']) && isset($_POST['log_content']))
        {
			
			$this->db->where('leadid', $_POST['jobid']);
			$job_details = $this->db->get($this->cfg['dbpref'] . '_leads');
            
            if ($job_details->num_rows() > 0) 
            {
				$job = $job_details->result_array();
				
                //$this->db->insert_id();
                $this->db->select('first_name, last_name, email');
                $this->db->where('userid', $_POST['userid']);
                $user = $this->db->get($this->cfg['dbpref'] . 'users');
                $user_data = $user->result_array();
				
				
				$this->db->where('custid', $job[0]['custid_fk']);
				$client_details = $this->db->get($this->cfg['dbpref'] . 'customers');
				$client = $client_details->result_array();
				
                $this->load->helper('url');
				
				$emails = trim($_POST['emailto'], ':');
				
				$send_to = array();
				$successful = $received_by = '';
				
				if ($emails != '' || isset($_POST['email_to_customer']))
				{
					$emails = explode(':', $emails);
					$mail_id = array();
					foreach ($emails as $mail)
					{
						$mail_id[] = str_replace('email-log-', '', $mail);
					}
					
					$this->load->plugin('phpmailer');
					
					$data['user_accounts'] = array();
					$this->db->where_in('userid', $mail_id);
					$users = $this->db->get($this->cfg['dbpref'] . 'users');
					
					if ($users->num_rows() > 0)
					{
						$data['user_accounts'] = $users->result_array();
					}
					
					foreach ($data['user_accounts'] as $ua)
					{
						# default email
						$to_user_email = $ua['email'];
						
						if (strstr($ua['add_email'], '@') && ! (isset($_POST['email_to_customer']) && isset($_POST['client_email_address']) && isset($_POST['client_full_name'])))
						{
							/**
							 * Additional email has to exist
							 * Client should not be involved
							 */
							if ($ua['use_both_emails'] == 1)
							{
								$to_user_email = $ua['add_email'];
							}
							else if ($ua['use_both_emails'] == 2)
							{
								$send_to[] = array($ua['add_email'], $ua['first_name'] . ' ' . $ua['last_name']);
							}
						}
						
						$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name']);
						$received_by .= $ua['first_name'] . ' ' . $ua['last_name'] . ', ';
					}
					
					/* update requested on 8/7/2010 - only for Jared */
					if (isset($_POST['george_mobile']))
					{
						//$send_to[] = array('sarunkumar@enoahisolution.com', 'Arunkumar');
						//$received_by .= 'Arunkumar, ';
					}
					
					$successful = 'This log has been emailed to:<br />';
					
					//$log_subject = "VCS Lead log - {$client[0]['first_name']} {$client[0]['last_name']} [lead#{$job[0]['leadid']}] {$client[0]['company']}";
					
					/*$log_email_content = "--enoahisolution.com\n\n" .
											$_POST['log_content'] .
												"\n\n\n{$client[0]['first_name']} {$client[0]['last_name']} - {$client[0]['company']}\n";*/
												
					
					$pdf_file_attach = array();
					
					$json['debug_info'] = '';
					
					
					if (isset($_POST['email_to_customer']) && isset($_POST['client_email_address']) && isset($_POST['client_full_name']))
					{
						// we're emailing the client, so remove the VCS log  prefix
						$log_subject = preg_replace('/^VCS Lead log - /', '', $log_subject);
						
						
						//$json['debug_info'] .= 'email to cust init > ';
						
						for ($cei = 1; $cei < 5; $cei ++)
						{
							if (isset($_POST['client_emails_' . $cei]))
							{
								//$json['debug_info'] .= 'loop through - ' . $cei;
								
								$send_to[] = array($_POST['client_emails_' . $cei], '');
								$received_by .= $_POST['client_emails_' . $cei] . ', ';
							}
						}
						
						if (isset($_POST['additional_client_emails']) && trim($_POST['additional_client_emails']) != '')
						{
							//$json['debug_info'] .= ' > adiitional posts';
							$additional_client_emails = explode(',', trim($_POST['additional_client_emails'], ' ,'));
							if (is_array($additional_client_emails)) foreach ($additional_client_emails as $aces)
							{
								$aces = trim($aces);
								if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $aces))
								{
									//$json['debug_info'] .= ' > adiitional add - ' . $aces;
									
									$send_to[] = array($aces, '');
									$received_by .= $aces . ', ';
								}
							}
						}
						
						// if the email goes to client - and the PDF attached, we need to CC accounts
						if (count($pdf_file_attach))
						{
							//$send_to[] = array('jranand@enoahisolution.com', '');
							//$received_by .= 'jranand@enoahisolution.com, ';
						}
						
					}
					else
					{
						$log_email_content .= "\n".$this->config->item('base_url')."leads/index/{$_POST['jobid']}";
					}
					
					$json['status_updated'] = false;
					
					/*if (send_email($send_to, $log_subject, $log_email_content, $user_data[0]['email'], $user_data[0]['first_name'] . ' ' . $user_data[0]['last_name'], '', '', $pdf_file_attach))
					{
						$successful .= trim($received_by, ', ');
					}*/
					
					
					$this->email->from('jranand@enoahisolution.com','jranand');
					$this->email->to('jranand@enoahisolution.com');
					$this->email->subject($log_subject);
					$this->email->message($log_email_content);
					if ($this->email->send()){
					$successful .= trim($received_by, ', ');
					}
					if ($successful == 'This log has been emailed to:<br />')
					{
						$successful = '';
					}
					else
					{
						$successful = '<br /><br />' . $successful;
					}
				}
				
				$ins['jobid_fk'] = $_POST['jobid'];
				
				// use this to update the view status
				$ins['userid_fk'] = $_POST['userid'];
				
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = $_POST['log_content'] . $successful;
				
				$stick_class = '';
				
				// inset the new log
				$this->db->insert($this->cfg['dbpref'] . '_lead_logs', $ins);
                
                $log_content = nl2br(auto_link($_POST['log_content'], 'url', TRUE)) . $successful;
                
				$fancy_date = date('d-m-Y H:i:s', strtotime($ins['date_created']));
				
                $table = <<< HDOC
<div class="log{$stick_class}" style="display:none;">
    <p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</div>
HDOC;
				
				
                $json['error'] = FALSE;
                $json['html'] = $table;
                echo json_encode($json);
				
            }
            else
            {
                echo "{error:true, errormsg:'Post insert failed'}";
            }
        }
        else
        {
            echo "{error:true, errormsg:'Invalid data supplied'}";
        }
    }
}
