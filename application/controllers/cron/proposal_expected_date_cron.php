<?php
class proposal_expected_date_cron extends crm_controller 
{
    
	public $userdata;
	
    public function __construct()
	{
		parent::__construct();
		//$this->login_model->check_login();
		//$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('customer_model');
        $this->load->library('validation');
		$this->load->library('email');
		$this->load->helper('text');
    }
    
    function index()
	{
		
		$today = date('Y-m-d'); 
		// $tomorrow = date('Y-m-d', strtotime("+1 day"));
		
		$usrs = $this->db->query("SELECT `userid`,`cron_id`,`email_notify_status`,`no_of_days` FROM `".$this->cfg['dbpref']."crons_notificatons` WHERE `email_notify_status` =  1 AND `cron_id`=1");

		$result = $usrs->num_rows();
		if ($result>0)
		{
			$result = $usrs->result_array();
			// echo "<pre>"; print_r($result); exit;
			
			foreach ($result as $res) {
				$expe = $this->db->query(" SELECT jb.lead_id, jb.lead_title, jb.belong_to, jb.lead_assign, DATEDIFF(jb.proposal_expected_date, '".$today."') as date_diff , jb.proposal_expected_date, CONCAT(own.first_name, ' ', own.last_name) as owners
				FROM `".$this->cfg['dbpref']."leads` as jb 
				LEFT JOIN `".$this->cfg['dbpref']."users` as own ON `own`.`userid` = `jb`.`belong_to`
				LEFT JOIN `".$this->cfg['dbpref']."users` as ass ON `FIND_IN_SET` (`ass`.`userid` , `jb`.`lead_assign`)
				where jb.proposal_expected_date between CURDATE() AND DATE(DATE_ADD(CURDATE(), INTERVAL '".$res['no_of_days']."' DAY)) AND jb.lead_status = 1 AND FIND_IN_SET('".$res['userid']."', jb.lead_assign) order by jb.lead_id ");

				$data['members'] = $expe->result_array();
				// echo "<pre>"; print_r($data['members']); exit;
				$user_name = "Webmaster";

				$from='webmaster@enoahprojects.com';
				$subject='Proposal Expected Date - Reminder';
				$this->email->set_newline("\r\n");
				$this->email->from($from,$user_name);
				$this->email->subject($subject);
				$print_fancydate = date('l, jS F y h:iA', strtotime(date('Y-m-d H:i:s')));

				if (!empty($data['members'])) 
				{
					foreach($data['members'] as $member) 
					{
						$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
							<title>Email Template</title>
							<style type="text/css">
							body { margin: 0px; }
							</style>
							</head>
							<body>
							<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
							<tr><td bgcolor="#FFFFFF">
							<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
							  <tr>
								<td style="padding:15px; border-bottom:2px #5A595E solid;"><img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" /></td>
							  </tr>
							  <tr>
								<td style="padding-top:12px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Proposal Expected Date - Reminder</h3>
								</td>
							  </tr>
							  <tr>
								<td>
									<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
										<p style="background: none repeat scroll 0 0 #4B6FB9; border-bottom: 1px solid #CCCCCC; color: #FFFFFF; margin: 0; padding: 4px;">
										<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;
										</p>
										<p style="padding: 4px;">';
										$log_email_content .= 'Dear Members, <br /><br />';
										$log_email_content .= 'The proposal expected date for the lead "<a href='.$this->config->item('base_url').'welcome/view_quote/'.$member['lead_id'].'>'.$member['lead_title'].'</a>" is going to end on '.date('d-m-Y', strtotime($member['proposal_expected_date'])).'';
									  $log_email_content .= '<br /><br />
									  Thanks & Regards,<br />Webmaster.
									  </p>
									</div>
								</td>
							  </tr>
							  <tr>
								<td>&nbsp;</td>
							  </tr>
							  <tr>
								<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
							  </tr>
							</table>
							</td>
							</tr>
							</table>
							</body>
							</html>';
						$assign_email  = get_lead_assigne_email($member['lead_assign']);
						$this->email->to($assign_email);	
						$this->email->message($log_email_content);
						$this->email->send();
						
					}
				}
			}
		}
    }   
}
?>