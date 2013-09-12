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
		<table width="100%" align="center" border="0" cellspacing="15" cellpadding="10"  bgcolor="#f5f5f5" >
		<!-- bgcolor="#f5f5f5" -->
			<tr><td bgcolor="#FFFFFF">
			
				<?php 
				 $cnt = 0;
				if(!empty($created)){
					foreach ($created as $owner_id=>$tasks){
						
						//echo "<h1>Task Assign to ".$tasks[0]->assigned_first_name.' '.$tasks[0]->assigned_last_name."</h1>";
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
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;"><?php echo "Task Assign to ".$tasks[0]->assigned_first_name.' '.$tasks[0]->assigned_last_name; ?></h3></td>
				  	</tr>
					
				  	<tr>
						<td>
							<table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="100%" align="center" cellspacing="0" cellpadding="4">
						  		<tr>						  		 	 	 	 	 	 	 	 	 	
									<td style="border-right:1px #CCC solid; color:#FFF" width="20%" bgcolor="#4B6FB9"><b>Task Description</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Task Remarks</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="15%" bgcolor="#4B6FB9"><b>Assigned By</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Assigned To</b> </td>									
									<td style="border-right:1px #CCC solid; color:#FFF" width="5%" bgcolor="#4B6FB9"><b>Planned Start Date</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="5%" bgcolor="#4B6FB9"><b>Planned End Date</b> </td>									
									<td style="border-right:1px #CCC solid; color:#FFF" width="5%" bgcolor="#4B6FB9"><b>Actual Start Date</b> </td>
									<!-- <td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Actual End Date</b> </td> -->
									<td style="border-right:1px #CCC solid; color:#FFF" width="3%" bgcolor="#4B6FB9"><b>Status</b> </td> 
																	
					  			</tr>
						  		
						  		<?php 
						  		if(!empty($tasks))
						  		{
						  			foreach ($tasks as $task){
						  				if($task->userid_fk!=$cur_user){
						  		?>
							  		<tr>
										<td style="border-right:1px #CCC solid;"><?php echo empty($task->task)?'':$task->task; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($task->remarks)?'':$task->remarks; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $task->owner_first_name.' '.$task->owner_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $task->assigned_first_name.' '.$task->assigned_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($task->start_date)?'':date('d-m-y',strtotime($task->start_date)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($task->end_date)?'':date('d-m-y',strtotime($task->end_date)); ?></td>
										<td style="border-right:1px #CCC solid;">
											<?php echo (empty($task->actualstart_date) || $task->actualstart_date==0)?'':date('d-m-y',strtotime($task->actualstart_date)); ?>
										</td>
										<!-- <td style="border-right:1px #CCC solid;"><?php //echo empty($task->actualend_date)?'':date('d-m-y',strtotime($task->actualend_date)); ?></td> -->
										<td style="border-right:1px #CCC solid;"><?php echo empty($task->status)?'':$task->status.'%'; ?></td> 
							  		</tr>
						  		<?php 
						  				}
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
				
				<!-- Assinged To me -->

				
				<?php 
				if(!empty($assigned))
				{
				?>				
			
				
				<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
					<tr>
						<td style="padding:15px; border-bottom:2px #5a595e solid;"><img src="<?php echo $this->config->item('base_url').'assets/img/esmart_logo.jpg';?>" /></td>
				  	</tr>
				  	<tr>
						<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;"><?php echo "Pending tasks"; ?></h3></td>
				  	</tr>
					
				  	<tr>
						<td>
							<table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="100%" align="center" cellspacing="0" cellpadding="4">
						  		<tr>						  		 	 	 	 	 	 	 	 	 	
									<td style="border-right:1px #CCC solid; color:#FFF" width="20%" bgcolor="#4B6FB9"><b>Task Description</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="15%" bgcolor="#4B6FB9"><b>Task Remarks</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Assigned By</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Assigned To</b> </td>									
									<td style="border-right:1px #CCC solid; color:#FFF" width="5%" bgcolor="#4B6FB9"><b>Planned Start Date</b> </td>
									<td style="border-right:1px #CCC solid; color:#FFF" width="5%" bgcolor="#4B6FB9"><b>Planned End Date</b> </td>									
									<td style="border-right:1px #CCC solid; color:#FFF" width="5%" bgcolor="#4B6FB9"><b>Actual Start Date</b> </td>
									<!-- <td style="border-right:1px #CCC solid; color:#FFF" width="10%" bgcolor="#4B6FB9"><b>Actual End Date</b> </td> -->
									 <td style="border-right:1px #CCC solid; color:#FFF" width="3%" bgcolor="#4B6FB9"><b>Status</b> </td> 
																	
					  			</tr>
						  		
						  		<?php 
						  		if(!empty($assigned))
						  		{
						  			foreach ($assigned as $assigned_task){	
						  				//foreach ($tasks as $task) {
						  		?>
							  		<tr>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_task->task)?'':$assigned_task->task; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_task->remarks)?'':$assigned_task->remarks; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $assigned_task->owner_first_name.' '.$assigned_task->owner_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo $assigned_task->assigned_first_name.' '.$assigned_task->assigned_last_name; ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_task->start_date)?'':date('d-m-y',strtotime($assigned_task->start_date)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo empty($assigned_task->end_date)?'':date('d-m-y',strtotime($assigned_task->end_date)); ?></td>
										<td style="border-right:1px #CCC solid;"><?php echo (empty($assigned_task->actualstart_date) ||  $assigned_task->actualstart_date==0)?'':date('d-m-y',strtotime($assigned_task->actualstart_date)); ?></td>
										<!-- <td style="border-right:1px #CCC solid;"><?php //echo empty($task->actualend_date)?'':date('d-m-y',strtotime($task->actualend_date)); ?></td>-->
										<td style="border-right:1px #CCC solid;"><?php echo empty($task->status)?'':$task->status."%"; ?></td> 
							  		</tr>						  		
						  		<?php 
						  				//}
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
				
				
				
				<?php } ?>
				</td>
				</tr>
				</table>
				
				
				</body>
				</html>
				
				