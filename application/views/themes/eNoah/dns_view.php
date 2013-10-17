<?php require (theme_url().'/tpl/header.php'); ?>
<script>
$(function(){
	$('input.pick-date').datepicker({dateFormat: 'dd-mm-yy'});
});
</script>
<div id="content">
    <div id="left-menu">
		<a href="hosting/">Back To Hosting</a>
	</div>
    <div class="inner">
	<?php
	if(!empty($jobs)) {
	if($jobs=='JOBS'){ 
		echo '<form action="dns/submit" method="post">';
		echo '<input type="hidden" name="'.$this->security->get_csrf_token_name().'" value="'. $this->security->get_csrf_hash().'" />';
		echo '<select name="hostings" class="textfield" style="width:298px;">';
		foreach($hosting as $key=>$val){
			echo '<option value="'.$val['hostingid'].'">'.$val['domain_name'].'</option>';
		}
		echo '</select>';
		echo '</form>';
		}
	}
	else {	
	?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Update <?php echo $domain_name; ?></h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
			<table class="layout">
			<tr><td valign=top>
			<table class="layout">
			<tr><td colspan=2><h4>Current Hosting Config</h4></td></tr>
			<tr><td colspan=2><p>All mandatory fields marked * must be filled in correctly.</p></td></tr>
				<tr>
					<td width="120">Host Location:*</td>
					<td><input type="hidden" name="dns" value="<?php echo $dns; ?>" class="textfield width200px required" />
					<input type="hidden" name="hostingid" value="<?php echo $hostingid; ?>" class="textfield width200px required" />
					<input type="text" name="host_location" value="<?php echo $host_location; ?>" class="textfield width200px required" />
					<!--<select name="host_location" class="textfield width200px">
						<?php
							/* foreach ($this->login_model->cfg['host_location'] as $key => $value) {
								$selected = ($this->validation->host_location == $key || $host_location==$key) ? ' selected="selected"' : ''; */ ?>
								<option value="<?php //echo $key ?>"<?php //echo $selected ?>><?php //echo $value ?></option>
						<?php	//} ?>
					</select> -->
					</td>
				</tr>
				<tr>
					<td width="120">Host Status:*</td>
					<td>
					<!--
					<input type="text" name="host_status" value="<?php echo $host_status; ?>" class="textfield width200px required" />
					-->
					<select name="host_status" class="textfield width200px">
						<?php
							 foreach ($this->login_model->cfg['host_status'] as $key => $value) {
								$selected = ($this->validation->host_status == $key || $host_status==$key) ? ' selected="selected"' : '';  ?>
								<option value="<?php echo  $key ?>"<?php echo $selected ?>><?php echo  $value ?></option>
						<?php	} ?>
					</select>				
					</td>
				</tr>
				<tr>
					<td>Login URL:</td>
					<td><input type="text" name="login_url" value="<?php echo  (!empty($login_url)?$login_url:$this->validation->login_url); ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Registrar Login:</td>
					<td><input type="text" name="login" value="<?php echo  (!empty($login)?$login:$this->validation->login); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>Registrar Password:</td>
					<td><input type="text" name="registrar_password" value="<?php echo (!empty($registrar_password)?$registrar_password:$this->validation->registrar_password); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>Tech Contact Number:</td>
					<td><input type="text" name="tech_contact" value="<?php echo  (!empty($tech_contact)?$tech_contact:$this->validation->tech_contact); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>Tech Email-id:</td>
					<td><input type="text" name="tech_email" value="<?php echo  (!empty($tech_email)?$tech_email:$this->validation->tech_email); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>Tech Name:</td>
					<td><input type="text" name="tech_name" value="<?php echo  (!empty($tech_name)?$tech_name:$this->validation->tech_name); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>Client Contact Number:</td>
					<td><input type="text" name="client_contact" value="<?php echo  (!empty($client_contact)?$client_contact:$this->validation->client_contact); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>Clienct Email-id:</td>
					<td><input type="text" name="client_email" value="<?php echo  (!empty($client_email)?$client_email:$this->validation->client_email); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>Client Name:</td>
					<td><input type="text" name="client_name" value="<?php echo  (!empty($client_name)?$client_name:$this->validation->client_name); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>Email:</td>
					<td><input type="text" name="email" value="<?php echo  (!empty($email)?$email:$this->validation->email); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<!--<tr>
					<td>Go Live Date:</td>
					<td><input type="text" name="go_live_date" value="<?php $stt=strtotime((!empty($go_live_date)?$go_live_date:$this->validation->go_live_date)); echo ($stt==0 || $stt==''?date('d-m-Y',(time()+(3086400))):date('d-m-Y',$stt)); ?>" class="textfield width200px pick-date" /></td>
				</tr>-->
				</table>
			</td>
			<td width=50>&nbsp;</td>
			<td  valign=top><table class="layout">
			<tr><td colspan=2><h4>Current Email Config</h4></td></tr>
			<!--<tr>
				<td colspan=2>Is there a change of email settings: &nbsp;<input type="checkbox" name="email_change"<?php (!empty($email_change)?'checked="checked"':''); ?> class="textfield" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Is Client notified of new settings: &nbsp;<input type="checkbox" name="client_notified" <?php (!empty($client_notified)?'checked="checked"':''); ?> class="textfield" /></td>
			</tr>-->
				<tr>
					<td width="120">SMTP Setting:</td>
					<td><input type="text" name="cur_smtp_setting" value="<?php echo  (!empty($cur_smtp_setting)?$cur_smtp_setting:$this->validation->cur_smtp_setting); ?>" class="textfield width200px required" /></td>
				</tr>
				<tr>
					<td>POP Setting:</td>
					<td><input type="text" name="cur_pop_setting" value="<?php echo  (!empty($cur_pop_setting)?$cur_pop_setting:$this->validation->cur_pop_setting); ?>" class="textfield width200px required" /></td>
				</tr>
				<tr>
					<td>Webmail URL:</td>
					<td><input type="text" name="cur_webmail_url" value="<?php echo  (!empty($cur_webmail_url)?$cur_webmail_url:$this->validation->cur_webmail_url); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>Control Panel URL:</td>
					<td><input type="text" name="cur_controlpanel_url" value="<?php echo (!empty($cur_controlpanel_url)?$cur_controlpanel_url:$this->validation->cur_controlpanel_url); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>STATS Panel URL:</td>
					<td><input type="text" name="cur_statspanel_url" value="<?php echo  (!empty($cur_statspanel_url)?$cur_statspanel_url:$this->validation->cur_statspanel_url); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Primary URL:</td>
					<td><input type="text" name="cur_dns_primary_url" value="<?php echo  (!empty($cur_dns_primary_url)?$cur_dns_primary_url:$this->validation->cur_dns_primary_url); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Primary IP:</td>
					<td><input type="text" name="cur_dns_primary_ip" value="<?php echo  (!empty($cur_dns_primary_ip)?$cur_dns_primary_ip:$this->validation->cur_dns_primary_ip); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Secondary URL:</td>
					<td><input type="text" name="cur_dns_secondary_url" value="<?php echo  (!empty($cur_dns_secondary_url)?$cur_dns_secondary_url:$this->validation->cur_dns_secondary_url); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Secondary IP:</td>
					<td><input type="text" name="cur_dns_secondary_ip" value="<?php echo  (!empty($cur_dns_secondary_ip)?$cur_dns_secondary_ip:$this->validation->cur_dns_secondary_ip); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>Record Setting:</td>
					<td><input type="text" name="cur_record_setting" value="<?php echo  (!empty($cur_record_setting)?$cur_record_setting:$this->validation->cur_record_setting); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>MX Record Setting:</td>
					<td><input type="text" name="cur_mx_record" value="<?php echo  (!empty($cur_mx_record)?$cur_mx_record:$this->validation->cur_mx_record); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<!--<tr><td colspan=2><h4>Future Email Config</h4></td></tr>
				<tr>
					<td width="120">SMTP Setting:</td>
					<td><input type="text" name="fut_smtp_setting" value="<?php echo  (!empty($fut_smtp_setting)?$fut_smtp_setting:$this->validation->fut_smtp_setting); ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>POP Setting:</td>
					<td><input type="text" name="fut_pop_setting" value="<?php echo  (!empty($fut_pop_setting)?$fut_pop_setting:$this->validation->fut_pop_setting); ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Webmail URL:</td>
					<td><input type="text" name="fut_webmail_url" value="<?php echo  (!empty($fut_webmail_url)?$fut_webmail_url:$this->validation->fut_webmail_url); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>Control Panel URL:</td>
					<td><input type="text" name="fut_controlpanel_url" value="<?php echo (!empty($fut_controlpanel_url)?$fut_controlpanel_url:$this->validation->fut_controlpanel_url); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>STATS Panel URL:</td>
					<td><input type="text" name="fut_statspanel_url" value="<?php echo  (!empty($fut_statspanel_url)?$fut_statspanel_url:$this->validation->fut_statspanel_url); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Primary URL:</td>
					<td><input type="text" name="fut_dns_primary_url" value="<?php echo  (!empty($fut_dns_primary_url)?$fut_dns_primary_url:$this->validation->fut_dns_primary_url); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Primary IP:</td>
					<td><input type="text" name="fut_dns_primary_ip" value="<?php echo  (!empty($fut_dns_primary_ip)?$fut_dns_primary_ip:$this->validation->fut_dns_primary_ip); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Secondary URL:</td>
					<td><input type="text" name="fut_dns_secondary_url" value="<?php echo  (!empty($fut_dns_secondary_url)?$fut_dns_secondary_url:$this->validation->fut_dns_secondary_url); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>DNS Secondary IP:</td>
					<td><input type="text" name="fut_dns_secondary_ip" value="<?php echo  (!empty($fut_dns_secondary_ip)?$fut_dns_secondary_ip:$this->validation->fut_dns_secondary_ip); ?>" class="textfield width200px" /> </td>
                    
				</tr>
				<tr>
					<td>Record Setting:</td>
					<td><input type="text" name="fut_record_setting" value="<?php echo  (!empty($fut_record_setting)?$fut_record_setting:$this->validation->fut_record_setting); ?>" class="textfield width200px" /></td>
                    
				</tr>
				<tr>
					<td>MX Record Setting:</td>
					<td><input type="text" name="fut_mx_record" value="<?php echo (!empty($fut_mx_record)?$fut_mx_record:$this->validation->fut_mx_record); ?>" class="textfield width200px" /></td>
                    
				</tr>

				<tr>
					<td>Date Switched Over:</td>
					<td><input type="text" name="date_handover" value="<?php $stt=strtotime((!empty($date_handover)?$date_handover:$this->validation->date_handover)); echo ($stt==0 || $stt==''?date('d-m-Y',(time()+(30*86400))):date('d-m-Y',$stt)); ?>" class="textfield width200px pick-date" /></td>
				</tr>-->
				
            </table></td>
			</tr>
			<tr>
				<td colspan="3" align="center">
					<div class="buttons">
						<button type="submit" name="update_customer" class="positive" style="float:none !important;">
							<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> DNS
						</button>
					</div>
				</td>
			</tr>
			</table>
				
		</form>
		<?php } ?>
	</div> <!--sriram-->
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>