<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/tinymce4.5.1/tinymce.min.js"></script>
<div id="content">
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Email Template </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout" style="width:100%">
				<tr>
					<td>Template Name : *</td>
					<td><input type="text" size="50" name="temp_name" id="temp_name" value="<?php echo $this->validation->temp_name; ?>" class="textfield" /></td>
					
				</tr>
				<tr>
					<td>Template Content : *</td>
					<td>
						<textarea name="temp_content" id="temp_content" class="crm_editor" cols="110" rows="15">
							<?php echo $this->validation->temp_content; ?>
						</textarea>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons">
                        <div class="buttons">
							<button type="submit" name="update_item" class="positive">
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Template
							</button>
						</div>
                    </td>
				</tr>
            </table>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->
<script>
tinymce.init({
    selector: '.crm_editor',
	plugins: "code,preview",
    height : "250"
});
</script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
