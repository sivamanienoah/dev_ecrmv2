<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/manage_practice/manage_practice_add_view.js"></script>
<div id="content">
    <div class="inner">

    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="add_practice" onsubmit="return chk_practice_name();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Practice </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Practice: * </td>
					<td>
						<input type="text" name="practices" id="practices" value="<?php echo $this->validation->practices; ?>" class="textfield width200px" />
						<?php if ($this->uri->segment(3) == 'update') { ?>
							<input type="hidden" id="practice_id" name="practice_id" value="<?php echo $this->uri->segment(4); ?>" />
						<?php } ?>
					</td>
					<td>
						<div id="succes_err_msg"></div>
					</td>
				</tr>
				<tr>
                    <td>Max Hours for resource per month: * </td>
					<td>
						<?php
						if($this->validation->max_hours==0){
							$this->validation->max_hours="";
						}?>
						<input type="text" name="max_hours" id="max_hours" value="<?php echo $this->validation->max_hours; ?>" class="textfield width200px" />
					
					</td>
					<td>
						<div id="hours_succes_err_msg"></div>
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td colspan="2">
						<input type="checkbox" name="status" value="1" <?php if ($this->validation->status == 1) echo ' checked="checked"' ?>
						<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
						<?php if ($cb_status != 0) echo "One or more Projects currently assigned for this Practice. This cannot be made Inactive."; ?>
						<?php if (($this->validation->status == 1) && ($cb_status == 0)) echo "Uncheck if the Practice need to be Inactive."; ?>
						<?php if ($this->validation->status != 1) echo "Check if the Practice need to be Active."; ?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="2">
                        <div class="buttons">
							<button type="submit" name="update_practice" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Practice
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>manage_practice'">
								Cancel
							</button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
		<br/>
		<?php if ($this->uri->segment(3) == 'update') { ?>
					<h2>Practice Maximium Hours History</h2>
					
					<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:50%">
					<thead>
						<tr>
							<th style="width:10%">SL No</th>
							<th>Financial Year</th>
							<th>Max Hours</th>
						</tr>
					</thead>
					<tbody>
					<?php if (is_array($practice_max_hours_history) && count($practice_max_hours_history) > 0 && !empty($practice_max_hours_history)) {
								$i=1;
						?>
						<?php foreach($practice_max_hours_history as $practice_max_hours_historys) { ?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $practice_max_hours_historys['financial_year']; ?></td>
								<td><?php echo $practice_max_hours_historys['practice_max_hours']; ?></td>
							
							</tr>
						<?php $i++;} ?>
					<?php }else{?>
							<tr><td colspan="3">No Results Found</td></tr>
						<?php } ?>
					</tbody>
				</table>
		<?php } ?>
	
	</div><!--Inner div close-->
</div><!--Content div close-->
<?php require (theme_url(). '/tpl/footer.php'); ?>
