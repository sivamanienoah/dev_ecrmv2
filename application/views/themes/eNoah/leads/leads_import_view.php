<?php require (theme_url().'/tpl/header.php'); ?>
<?php $this->userdata = $this->session->userdata('logged_in_user'); ?>
<?php 
	//echo $this->session->userdata('addImpCus');
?>
<div id="content">
    <div class="inner">
	<?php #if($this->session->userdata('addImpCus')==1) { ?>
    	<form action="welcome/importleads" method="post" enctype="multipart/form-data" >
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Import Leads </h2>
            <?php echo  $error; ?>
            <?php echo  $msg; ?>
			<table class="layout">
			 <tr>
				<td colspan="4">
			          <a href="assets/leads list.csv" style="text-decoration: underline"><b>Download CSV Template</b></a>
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
		<?php /* } else{
			echo "You have no rights to access this page";
		} */ ?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>