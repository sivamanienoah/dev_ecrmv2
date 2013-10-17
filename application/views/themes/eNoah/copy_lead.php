<?php require (theme_url().'/tpl/header.php'); ?>

<link rel="stylesheet" href="assets/css/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<script type="text/javascript">

function copyQuote()
{
	// add id to form
    $('#hidden_custid_fk').val(<?php echo $custid; ?>);
	$('#hidden_leadid_fk').val(<?php echo $leadid; ?>);
	$('#quote-init-form').submit();
	
}

</script>
<style type="text/css">
h3 .small {
	font-weight:normal;
	font-size:14px;
	display:block;
}
</style>
<div id="content">
    <?php include ('tpl/quotation_submenu.php') ?>
    <div class="inner">
    	<div class="q-main-left">
            
            <form action="/welcome/copy_lead/" method="post" id="quote-init-form" name="quote-init-form" 
			class="<?php echo  (isset($view_quotation) || isset($edit_quotation)) ? 'display-none' : '' ?>" >
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
                <input type="hidden" name="custid_fk" id="hidden_custid_fk" />
				<input type="hidden" name="leadid_fk" id="hidden_leadid_fk" />
                <h2>Copy a Lead</h2>
             
                <div class="q-init-details">					
                                        
                    <p><label>Quotation Title</label></p>
                    <p><input type="text" name="job_title" id="job_title" class="textfield width300px" onkeyup="$('.q-quote-items .quote-title span').html(this.value);" /></p>
                   
                    <p><label>Quote Detail</label></p>
                    
					<p><select name="job_id" id="job_id" class="textfield width300px" >
                            <option value="not_select">Please Select</option>
							<?php foreach ($records as $tmpRecDis) { ?>
							<option value="<?php echo $tmpRecDis['jobid']; ?>" /><?php
								echo $tmpRecDis['jobid']." - ".$tmpRecDis['job_title']." - ".
								$tmpRecDis['company']." - ".$tmpRecDis['first_name']." ".$tmpRecDis['last_name'];
							?></option>
							<?php } ?>
                        </select>
                    </p> 
					
				
                    <div class="buttons">
                        <button type="submit" class="positive" onclick="copyQuote();">Copy</button>
                    </div>
                    <p>&nbsp;</p>
                </div>
            </form>
           
			
			<?php
			/**
			 * This will include the select box that changes the status of a job
			 */
			include 'tpl/status_change_menu.php';
			?>
            
			<div class="action-buttons" style="overflow:hidden; margin-top:20px;">
				<?php
				if (isset($quote_data) && isset($userdata) && in_array($userdata['level'], array(0,1,2,4)))
				{
					?>
				<div class="buttons">
						<button type="submit" class="positive" onclick="document.location.href = '<?php echo $this->config->item('base_url') ?>welcome/view_quote/<?php echo $quote_data['jobid'] ?>'">View, Email or Add Logs to this Job</button>
					</div>
					<?php
				}
				?>
			</div>
			
        </div>
		<!-- q main right removal  --->
       <!-- end -->
	</div>
</div>


<?php require (theme_url().'/tpl/footer.php'); ?>
