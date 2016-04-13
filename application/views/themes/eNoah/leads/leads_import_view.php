<?php require (theme_url().'/tpl/header.php'); ?>
<?php $this->userdata = $this->session->userdata('logged_in_user'); ?>
<?php #echo $this->session->userdata('add'); ?>
<div id="content">
    <div class="inner">
	<?php if($this->session->userdata('add')==1) { ?>
    	<form action="welcome/importleads" method="post" enctype="multipart/form-data" >
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Import Leads </h2>
            <?php echo  $error; ?>
            <?php echo  $msg; ?>
			<table class="layout">
			 <tr>
				<td colspan="4">
					<a href="assets/lead_import_instructions.xls" style="text-decoration: underline">
						<b>Instructions for Importing leads</b>
					</a>
					<br />
					<br />
					<a href="assets/crm_masters.xls" style="text-decoration: underline">
						<b>Download Masters</b>
					</a>
					<br />
					<br />
					<a href="assets/leads_import_template.xls" style="text-decoration: underline">
						<b>Download Import Template</b>
					</a>
			    </td>
			    </tr> 
				<tr><td colspan="4">&nbsp;</td></tr>

			    <tr><td colspan="4">&nbsp;</td></tr>
				<tr>
					<td width="100">Upload List:</td>
					<td width="240">
                        <input type="file" name="card_file" class="textfield width200px required" />
                        <input type="hidden" name="card_upload" />
                    </td>
					<td width="100">&nbsp;</td>
					<td width="240">&nbsp;</td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" class="positive">								
								Import Leads List
							</button>
						</div>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
				</tr>
            </table>
		</form>
		<?php } else{
			echo "You have no rights to access this page";
		} ?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>