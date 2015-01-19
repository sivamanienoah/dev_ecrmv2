<?php require (theme_url().'/tpl/header.php'); ?>
<style>
.hide-calendar .ui-datepicker-calendar {
   display: none;
}
</style>
<div id="content">
    <div class="inner">

    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="add_sales_forecast" >
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Sale Forecast </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Entity: * </td>
					<td>
						<!--input type="text" name="entity" id="entity" value="<?php #echo $this->validation->entity; ?>" class="textfield width200px" /-->
						<select name="entity" id="entity" class="textfield width200px" >
							<option value=''>Select</option>
							<?php if(!empty($entity)) { ?>
								<?php foreach($entity as $ent) { ?>
									<option value="<?php echo $ent['div_id']; ?>" <?php echo $this->validation->entity == $ent['div_id'] ? 'selected="selected"' : ''; ?>><?php echo $ent['division_name']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
						<?php if ($this->uri->segment(3) == 'update') { ?>
							<input type="hidden" id="forecast_id" name="forecast_id" value="<?php echo $this->uri->segment(4); ?>" />
						<?php } ?>
					</td>
					<td><div id="succes_err_msg"></div></td>
				</tr>
				<tr>
					<td>Customer: * </td>
					<td>
						<input type="text" name="customer_name" id="customer_name" value="<?php echo $this->validation->customer_name; ?>" class="textfield width200px" />
					</td>
				</tr>
				<tr>
					<td>Lead/Project: * </td>
					<td>
						<input type="text" name="lead_name" id="lead_name" value="<?php echo $this->validation->lead_name; ?>" class="textfield width200px" />
					</td>
				</tr>
				<tr>
					<td>Milestone: * </td>
					<td>
						<input type="text" name="milestone" id="milestone" value="<?php echo $this->validation->milestone; ?>" class="textfield width200px" />
					</td>
				</tr>
				<tr>
					<td>For the Month & Year: * </td>
					<td>
						<input type="text" data-calendar="false" name="for_month_year" autocomplete="off" id="for_month_year" value="<?php if(!empty($this->validation->for_month_year)) echo date('F Y', strtotime($this->validation->for_month_year)); ?>" class="textfield width200px" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="2">
                        <div class="buttons">
							<button type="submit" name="update_practice" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Sale Forecast
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>sales_forecast'">
								Cancel
							</button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->
<script>
	var token_name  ="<?php echo $this->security->get_csrf_token_name(); ?>";
	var token_value ="<?php echo $this->security->get_csrf_hash(); ?>";
</script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_add_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
