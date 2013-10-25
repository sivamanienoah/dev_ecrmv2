<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Email Template</title>
		<style type="text/css">
			body { margin: 0px; }
		</style>
	</head>

	<body>
		<table width="100%" align="center" border="0" cellspacing="15" cellpadding="10"  bgcolor="#f5f5f5">
			<tr>
				<td bgcolor="#FFFFFF">
			
				<?php 
				if(!empty($created))
				{
					$cnt = 0;
					foreach ($created as $owner_id=>$leads)
					{
				?>
				<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
					<?php
					if($cnt==0){ 
					?>
						<tr>
							<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="<?php echo $this->config->item('base_url').'assets/img/esmart_logo.jpg';?>" /></td>
					  	</tr>
				  	<?php 
					}
				  	?>
				  	<tr>
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;"><?php echo "Lead Assign to ".$leads[0]->assigned_first_name.' '.$leads[0]->assigned_last_name; ?></h3></td>
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
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Lead Indicator</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Status</b> </td>
					  			</tr>
						  		
						  		<?php 
						  		if(!empty($leads))
						  		{
						  			foreach ($leads as $lead) 
									{
						  				if($task->lead_assign!=$cur_user) 
										{
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
											<td style="border-right:1px #CCC solid;"><?php echo empty($lead->lead_indicator)?'':$lead->lead_indicator; ?></td>
											<td style="border-right:1px #CCC solid;">
												<?php 
													switch ($lead->lead_status)
													{
														case 1:
															echo $status = 'Active';
														break;
														case 2:
															echo $status = 'On Hold';
														break;
														case 3:
															echo $status = 'Dropped';
														break;
														case 4:
															echo $status = 'Closed';
														break;
													}
												?>
											</td>
											</tr>
						  		<?php 
						  				}
						  			}
						  		}
						  		?>
							</table>
						</td>	 
				  </tr>	
				  <tr>
					<td>&nbsp;</td>
				  </tr>
				  <?php
				  $cnt++; 
				  if(count($created) == $cnt){
				  ?>
				  <tr>
					<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
				  </tr>
				  <?php } ?>
				</table>
				<?php 
					
					}
				}
				?>
				<?php 
				if(!empty($assigned))
				{
				?>	
				<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
					<tr>
						<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="<?php echo $this->config->item('base_url').'assets/img/esmart_logo.jpg';?>" /></td>
				  	</tr>
				  	<tr>
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;"><?php echo "Pending leads"; ?></h3></td>
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
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Lead Indicator</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="76%" bgcolor="#4B6FB9"><b>Status</b> </td>
					  			</tr>
						  		
						  		<?php 
						  		if(!empty($assigned))
						  		{
						  			foreach ($assigned as $assigned_lead)
									{
						  		?>
							  		<tr>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->invoice_no)?'':$assigned_lead->invoice_no; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->job_title)?'':$assigned_lead->job_title; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $assigned_lead->cust_first_name.' '.$assigned_lead->cust_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->region_name)?'':$assigned_lead->region_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $assigned_lead->owner_first_name.' '.$assigned_lead->owner_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $assigned_lead->assigned_first_name.' '.$assigned_lead->assigned_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->expect_worth_amount)?'':$assigned_lead->expect_worth_name.' '.$assigned_lead->expect_worth_amount; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->date_created)?'':date('d-m-y',strtotime($assigned_lead->date_created)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->date_modified)?'':date('d-m-y',strtotime($assigned_lead->date_modified)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $assigned_lead->modified_first_name.' '.$assigned_lead->modified_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->lead_stage_name)?'':$assigned_lead->lead_stage_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $from_date = empty($assigned_lead->proposal_expected_date)?'':date('d-m-y',strtotime($assigned_lead->proposal_expected_date)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_lead->lead_indicator)?'':$assigned_lead->lead_indicator; ?></td>
										<td style="border-right:1px #CCC solid;">
											<?php 
												switch ($assigned_lead->lead_status)
												{
													case 1:
														echo $status = 'Active';
													break;
													case 2:
														echo $status = 'On Hold';
													break;
													case 3:
														echo $status = 'Dropped';
													break;
													case 4:
														echo $status = 'Closed';
													break;
												}
											?>
											
										</td>
							  		</tr>						  		
							<?php
						  			}
						  		}
							?>
							</table>
						</td>	 
					</tr>	
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
					</tr>
				</table>
				<?php } ?>
				</td>
				</tr>
		</table>
				
				
	</body>
</html>
				
				