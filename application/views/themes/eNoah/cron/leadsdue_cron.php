<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
		<table width="100%" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
			<tr><td bgcolor="#FFFFFF">
				<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
					<tr>
						<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="<?php echo $this->config->item('base_url').'assets/img/esmart_logo.jpg';?>" /></td>
				  	</tr>
				  	<tr>
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead</h3></td>
				  	</tr>
					
				  	<tr>
						<td>
							<table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="100%" align="center" cellspacing="0" cellpadding="4">
						  		<tr>
									<td style="border-right:1px #CCC solid; color:#FFF" width="5%" bgcolor="#4B6FB9"><b>Lead No.</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="15%" bgcolor="#4B6FB9"><b>Lead Title</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="15%" bgcolor="#4B6FB9"><b>Customer</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Region</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="15%" bgcolor="#4B6FB9"><b>Lead Owner</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="15%" bgcolor="#4B6FB9"><b>Lead Assigned To</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Expected Worth</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Lead Creation Date</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Updated On</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Updated By</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Lead Stage</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Expected Proposal Date</b> </td>
									<!-- <td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Proposal Sent on</b> </td> -->									
									<!--<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Variance(days)</b> </td>-->									
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Lead Indicator</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Status</b> </td>
					  			</tr>
						  		
						  		<?php 
						  		if(!empty($leads))
						  		{
						  			foreach ($leads as $lead){
						  		?>
							  		<tr>
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->invoice_no)?'':$lead->invoice_no; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->job_title)?'':$lead->job_title; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $lead->cust_first_name.' '.$lead->cust_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->region_name)?'':$lead->region_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $lead->owner_first_name.' '.$lead->owner_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $lead->assigned_first_name.' '.$lead->assigned_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->expect_worth_amount)?'':$lead->expect_worth_name.' '.$lead->expect_worth_amount; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->date_created)?'':date('d-m-y',strtotime($lead->date_created)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->date_modified)?'':date('d-m-y',strtotime($lead->date_modified)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $lead->modified_first_name.' '.$lead->modified_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->lead_stage_name)?'':$lead->lead_stage_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $from_date = empty($lead->proposal_expected_date)?'':date('d-m-y',strtotime($lead->proposal_expected_date)); ?></td>
										<!-- <td style="border-right:1px #CCC solid;"><?php //echo $to_date = empty($lead->proposal_sent_date)?'':date('d-m-y',strtotime($lead->proposal_sent_date)); ?></td> -->
										<?php 
											$date1 = empty($lead->proposal_sent_date)?'':$lead->proposal_sent_date;
											$date2 = empty($lead->proposal_expected_date)?'':$lead->proposal_expected_date;
											if($date1 != '' && $date2 != '')
											{
											$diff = abs(strtotime($date2) - strtotime($date1));
											$years = floor($diff / (365*60*60*24));
											$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
											$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
											$variance = $days;
											} else {
											$variance = '-';
											}
										?>
										<!-- <td style="border-right:1px #CCC solid;"><?php //echo $variance; ?></td> -->
										<td style="border-right:1px #CCC solid;"><?php echo empty($lead->lead_indicator)?'':$lead->lead_indicator; ?></td>
										<td style="border-right:1px #CCC solid;">
										<?php //echo empty($lead->lead_status)?'':$lead->lead_status; ?>
										<?php 
											if($lead->lead_status == 1)
											$status = 'Active';
											else if ($lead->lead_status == 2)
											$status = 'On Hold';
											else 
											$status = 'Dropped';
													
											echo $status; 
										?>
										</td>										
							  		</tr>
						  		
						  		<?php 
						  			}
						  		}
						  		?>
						  <!--  } else {
							$log_email_content .= '<tr style="border:1px #CCC solid;"> 
							<td style="border-right:1px #CCC solid;" >Expiry Info</td>
							<td style="border-right:1px #CCC solid;" > 
							This domain had already expired. The expired date is '.date("d-m-Y", strtotime($exp_dt)).'</td>
						  </tr>';
						  }
						  $log_email_content .= ' --></table>
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
				</html>