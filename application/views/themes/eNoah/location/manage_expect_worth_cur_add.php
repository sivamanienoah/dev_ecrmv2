<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
            <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Currency Type </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Country Name: * </td>
					<td>
						<select name="country_name" id="country_name" class="textfield width300px">
							<option value="">--Select--</option>
							<?php foreach($getAllCurrency as $cur) {?>
							<option value="<?php echo $cur['cur_id']; ?>"><?php echo $cur['country_name']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
                    <td>Currency Name:  </td>
					<td><input type="text" name="cur_name" id="cur_name" value="" class="textfield" readonly ></td>
				</tr>
				<tr>
                    <td>Currency Type:  </td>
					<td><input type="text" name="cur_short_name" id="cur_short_name" value="" class="textfield" readonly ></td>
				</tr>
				<tr>
					<td>Status: </td>
					<td><input type="checkbox" name="status" value="1" onchange="toggleCheckbox(this)" /> Check if the Currency Type need to be Active. </td>
				</tr>
				<tr>
					<td>Default Currency:  </td>
					<td><input type="checkbox" name="is_default" id="is_default" value="1" /> Check if the Currency Type need to be Default Currency. </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons">
                        <div class="buttons">
							<button type="submit" name="update_item" class="positive">
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Currency Type
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>manage_service/manage_expt_worth_cur'">
								Cancel
							</button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->
<?php require (theme_url(). '/tpl/footer.php'); ?>
<script type="text/javascript" src="assets/js/manage_service/manage_expect_worth_cur_add.js"></script>