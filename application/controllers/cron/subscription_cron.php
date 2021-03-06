<?php
class subscription_cron extends crm_controller {
    
	public $userdata;
	
    function __construct()
	{
        parent::__construct();
		//$this->login_model->check_login();
		//$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('customer_model');
         $this->load->model('hosting_model');
        $this->load->library('validation');
		$this->load->library('email');
    }
    
    function index($limit = 0, $search = false)
	{	//echo 'hi';exit;
		$today = date('Y-m-d'); 
//                $days_query = $this->db->query("SELECT hostingid, custid_fk, domain_expiry,domain_name, days_check FROM ".$this->cfg['dbpref']."hosting where  domain_status != 3 AND (tracking_status = 1 OR tracking_status = 0) order by hostingid ");
                $days_query = $this->db->query("SELECT hostingid, custid_fk, domain_expiry,domain_name, days_check FROM ".$this->cfg['dbpref']."hosting where  domain_status != 3 AND  (tracking_status = 1 OR tracking_status = 0) order by hostingid ");
		$daysDiff = $days_query->result_array();
		foreach ($daysDiff as $key => $value) {
			$dead_line = ($value['days_check'] > 0)?$value['days_check']:15;
			$endDate = date('Y-m-d', strtotime("+".$dead_line." days"));
		}
		//$endDate = date('Y-m-d', strtotime("+15 days"));
		// $hosting_exp = $this->db->query(" SELECT hostingid, domain_expiry, domain_name, DATEDIFF(domain_expiry, '".$today."') as date_diff, custid_fk FROM ".$this->cfg['dbpref']."hosting where domain_expiry between '".$today."' AND '".$endDate."' ");
		//$hosting_exp = $this->db->query(" SELECT hostingid, domain_name, domain_expiry, DATEDIFF(domain_expiry, '".$today."') as date_diff, custid_fk,created_by FROM ".$this->cfg['dbpref']."hosting where domain_expiry <='".$endDate."' AND domain_status != 3 order by hostingid ");
		//echo $this->db->last_query(); exit;
		//$data['members'] = $hosting_exp->result_array();
                
                $hosting_exp = $this->db->query("SELECT hostingid, custid_fk, domain_name,`ssl`, domain_status, domain_expiry, DATEDIFF(domain_expiry, '".$today."') as date_diff,created_by,alt_users  FROM ".$this->cfg['dbpref']."hosting where domain_expiry <='".$endDate."' AND domain_status != 3  AND (tracking_status = 1 OR tracking_status = 0) order by hostingid ");
		//echo $this->db->last_query(); exit;
		$data['members'] = $hosting_exp->result_array();
              //  echo '<pre>';print_r($data['members']);exit;
                if (!empty($data['members'])) {
		foreach($data['members'] as $member) {
                if($member['date_diff'] % 2 == 1 && $member['date_diff'] > 0):
		
		$user_name = "Webmaster";
		$from='webmaster@enoahprojects.com';
		$subject='Domain Renewal Reminder';
		$this->email->set_newline("\r\n");
		$this->email->from($from,$user_name);
		$this->email->subject($subject);
		$data['failmail'] = 0;
		$data['successmail'] = 0;
            //  echo '<pre>';print_r($member);exit;
                        $package = $this->hosting_model->get_row_bycond('package', 'status', 'active');
			$hostid = $member['hostingid'];
			$cust_id = $member['custid_fk'];
			$exp_dt = $member['domain_expiry'];
			$domainName = $member['domain_name'];
			$domainStatus = $member['domain_status'];
			$ssl = $member['ssl'];
                       // print_r($domainStatus);exit;
                       
                        foreach ($this->login_model->cfg['domain_status'] as $key => $value) {
                                if($domainStatus == $key){
                                     $dom_status = $value;
                                }
                                  
                        }   
                        
                        foreach ($this->login_model->cfg['domain_ssl_status'] as $key => $value) {
                                if($ssl == $key){
                                     $ssl_name = $value;
                                }
                                  
                        }  
                       // echo $dom_status;exit;
                       
                               
                        
                        $sub_owner = $member['created_by'];
			$owner = $this->db->query("select first_name, last_name, username, email from ".$this->cfg['dbpref']."users where userid = $sub_owner");
			$data['sub_holder'] = $owner->row_array();
                       // 
			$cust_name = $data['sub_holder']['first_name'] . " " . $data['sub_holder']['last_name'] ;
			$cust_email = $data['sub_holder']['email'];
                        if(isset($member['alt_users'])):
                        $alt_users_id = $member['alt_users'];
                        $alt_owners = $this->db->query("select first_name, last_name, username, email from ".$this->cfg['dbpref']."users where userid in ($alt_users_id)");
                        $send_alerts_to = $alt_owners->result_array();
                        $cc_alert = array();
                        
                        foreach($send_alerts_to as $cc_alert_users){
                            
                          $cc_alert[] = $cc_alert_users['email'];
                          
                        }
                            $cc_alert = implode(',', $cc_alert);
                        endif;    

			$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Email Template</title>
				<style type="text/css">
				body {
					margin-left: 0px;
					margin-top: 0px;
					margin-right: 0px;
					margin-bottom: 0px;
				}
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
					<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Domain Renewal Reminder</h3>
					</td>
				  </tr>
					
				  <tr>
					<td>
						<table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="96%" align="center" cellspacing="0" cellpadding="4">
						  <tr>
							<td style="border-right:1px #CCC solid; color:#FFF" width="20%" bgcolor="#4B6FB9"><b>Title</b> </td>
							<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Description</b> </td>
						  </tr>
						  <tr>
							<td style="border-right:1px #CCC solid;">Domain</td>
							<td style="border-right:1px #CCC solid;">'.$domainName.'</td>
						  </tr>
                                                  <tr>
							<td style="border-right:1px #CCC solid;">Domain Status</td>
							<td style="border-right:1px #CCC solid;">'.$dom_status.'</td>
						  </tr>
                                                   <tr>
							<td style="border-right:1px #CCC solid;">SSL</td>
							<td style="border-right:1px #CCC solid;">'.$ssl_name.'</td>
						  </tr>
						  <tr>
							<td style="border-right:1px #CCC solid;">Client</td>
							<td style="border-right:1px #CCC solid;">'.$cust_name.'</td>
						  </tr>';
						  if ($member['date_diff']>0) {
						  $log_email_content .= '<tr style="border:1px #CCC solid;">
							<td style="border-right:1px #CCC solid;" >Expiry Info</td>
							<td style="border-right:1px #CCC solid;" > 
							This domain will be expiring within '.$member['date_diff'].' day(s). The current expiry date is '.date("d-m-Y", strtotime($exp_dt)).'</td>
						  </tr>';
						  } else {
							$log_email_content .= '<tr style="border:1px #CCC solid;">
							<td style="border-right:1px #CCC solid;" >Expiry Info</td>
							<td style="border-right:1px #CCC solid;" > 
							This domain had already expired. The expired date is '.date("d-m-Y", strtotime($exp_dt)).'</td>
						  </tr>';
						  }
						  $log_email_content .= ' </table>
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
			$this->email->to($cust_email);	
                           if(isset($member['alt_users'])):
                         $this->email->cc($cc_alert);
                           endif;
			$this->email->message($log_email_content);
			//$this->email->send();
			
			if ($this->email->send())
			{
				$data['successmail']++;
                                if($dead_line >= 3):
					$updata  = array('tracking_status'=>1,'days_check'=>($dead_line-2));
					$this->db->where('hostingid', $hostid);
	        		$this->db->update($this->cfg['dbpref'] . 'hosting', $updata);
	        	endif;
			} else {
				$data['failmail']++;
			}
                         endif;	
		}
		$data['res'] = "E-Mail Sent";
	}
	else {
		$data['res'] = "No Domain Expriy Date found";
	}	
		//$this->load->view('hosting_cron_view', $data);
    }
    
	//For Hosting
    function hosting_reminder()
	{
       // echo 'hi';exit;
		$today = date('Y-m-d'); 
		// $days_check = $this->db->select('days_check')->get('crm_hosting')->row()->days_check;
		$days_query = $this->db->query("SELECT hostingid, custid_fk, domain_name, days_check FROM ".$this->cfg['dbpref']."hosting where  domain_status != 3 AND (tracking_status = 1 OR tracking_status = 0) order by hostingid ");
		$daysDiff = $days_query->result_array();
		foreach ($daysDiff as $key => $value) {
			$dead_line = ($value['days_check'] > 0)?$value['days_check']:15;
			$endDate = date('Y-m-d', strtotime("+".$dead_line." days"));
		}
		

		$hosting_exp = $this->db->query("SELECT hostingid, custid_fk,`ssl`, domain_name,domain_status, expiry_date, DATEDIFF(expiry_date, '".$today."') as date_diff,created_by,alt_users  FROM ".$this->cfg['dbpref']."hosting where expiry_date <='".$endDate."' AND domain_status != 3 AND (tracking_status = 1 OR tracking_status = 0) order by hostingid ");
		// echo $this->db->last_query(); exit;
		$data['members'] = $hosting_exp->result_array();
		//echo '<pre>';print_r($data['members']);die;
                if (!empty($data['members'])) {
		foreach($data['members'] as $member) {
                   
            if($member['date_diff'] % 2 == 1 && $member['date_diff'] > 0):
          // echo '<pre>';print_r($member);
		$user_name = "Webmaster";
		
		$from='webmaster@enoahprojects.com';
		$subject='Hosting Renewal Reminder';
		$this->email->set_newline("\r\n");
		$this->email->from($from,$user_name);
		$this->email->subject($subject);
		$data['failmail'] = 0;
		$data['successmail'] = 0;
                $domainStatus = $member['domain_status'];
                $ssl = $member['ssl'];;
                       // print_r($domainStatus);exit;
                       
                        foreach ($this->login_model->cfg['domain_status'] as $key => $value) {
                                if($domainStatus == $key){
                                     $dom_status = $value;
                                }
                                  
                        }    
                         foreach ($this->login_model->cfg['domain_ssl_status'] as $key => $value) {
                                if($ssl == $key){
                                     $ssl_name = $value;
                                }
                                  
                        }  
		//print_r($data['members']);exit;
	
			$hostid = $member['hostingid'];
			$cust_id = $member['custid_fk'];
			$exp_dt = $member['expiry_date'];
			$domainName = $member['domain_name'];
                        $sub_owner = $member['created_by'];
                        $owner = $this->db->query("select first_name, last_name, username, email from ".$this->cfg['dbpref']."users where userid = $sub_owner");
			$data['sub_holder'] = $owner->row_array();
			$cust_name = $data['sub_holder']['first_name'] . " " . $data['sub_holder']['last_name'] ;
			$cust_email = $data['sub_holder']['email'];
                        if(isset($member['alt_users'])):
                        $alt_users_id = $member['alt_users'];
                        $alt_owners = $this->db->query("select first_name, last_name, username, email from ".$this->cfg['dbpref']."users where userid in ($alt_users_id)");
                        $send_alerts_to = $alt_owners->result_array();
                        $cc_alert = array();
                        foreach($send_alerts_to as $cc_alert_users){
                            
                          $cc_alert[] = $cc_alert_users['email'];
                          
                        }
                            $cc_alert = implode(',', $cc_alert);
                        endif;
                       

			$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Email Template</title>
				<style type="text/css">
				body {
					margin-left: 0px;
					margin-top: 0px;
					margin-right: 0px;
					margin-bottom: 0px;
				}
				</style>
				</head>

				<body>
				<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
				<tr><td bgcolor="#FFFFFF">
				<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
				  <tr>
					<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" /></td>
				  </tr>
				  <tr>
					<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Hosting Renewal Reminder</h3>
					</td>
				  </tr>
					
				  <tr>
					<td>
						<table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="96%" align="center" cellspacing="0" cellpadding="4">
						  <tr>
							<td style="border-right:1px #CCC solid; color:#FFF" width="20%" bgcolor="#4B6FB9"><b>Title</b> </td>
							<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Description</b> </td>
						  </tr>
						  <tr>
							<td style="border-right:1px #CCC solid;">Domain</td>
							<td style="border-right:1px #CCC solid;">'.$domainName.'</td>
						  </tr>
                                                  <tr>
							<td style="border-right:1px #CCC solid;">Domain Status</td>
							<td style="border-right:1px #CCC solid;">'.$dom_status.'</td>
						  </tr>
                                                   <tr>
							<td style="border-right:1px #CCC solid;">SSL</td>
							<td style="border-right:1px #CCC solid;">'.$ssl_name.'</td>
						  </tr>
						  <tr>
							<td style="border-right:1px #CCC solid;">Client</td>
							<td style="border-right:1px #CCC solid;">'.$cust_name.'</td>
						  </tr>';
						  if ($member['date_diff']>0) {
						  $log_email_content .= '<tr style="border:1px #CCC solid;">
							<td style="border-right:1px #CCC solid;" >Expiry Info</td>
							<td style="border-right:1px #CCC solid;" > 
							This hosting will be expiring within '.$member['date_diff'].' day(s). The current expiry date is '.date("d-m-Y", strtotime($exp_dt)).'</td>
						  </tr>';
						  } else {
							$log_email_content .= '<tr style="border:1px #CCC solid;">
							<td style="border-right:1px #CCC solid;" >Expiry Info</td>
							<td style="border-right:1px #CCC solid;" > 
							This hosting had already expired. The expired date is '.date("d-m-Y", strtotime($exp_dt)).'</td>
						  </tr>';
						  }
						  $log_email_content .= ' </table>
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
			$this->email->to($cust_email);	
                         if(isset($member['alt_users'])):
                        $this->email->cc($cc_alert);
                    endif;
			$this->email->message($log_email_content);
			//$this->email->send();
			
			if ($this->email->send())
			{
				$data['successmail']++;
				if($dead_line >= 3):
					$updata  = array('tracking_status'=>1,'days_check'=>($dead_line-2));
					$this->db->where('hostingid', $hostid);
	        		$this->db->update($this->cfg['dbpref'] . 'hosting', $updata);
	        	endif;
			} else {
				$data['failmail']++;
			}	
                    endif;	
		}
		$data['res'] = "E-Mail Sent";
	}
	else {
		$data['res'] = "No Hosting expriy date found";
	}
	
		//$this->load->view('hosting_cron_view', $data);
    }
	
   
}
?>