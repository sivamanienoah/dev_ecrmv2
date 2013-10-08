<?php require ('tpl/header.php'); ?>
<?php $this->userdata = $this->session->userdata('logged_in_user'); ?>
<?php 
	//echo $this->session->userdata('addImpCus');
?>
<div id="content">
    <div id="left-menu">
		<a href="customers/">Back To List</a>
    </div>
    <div class="inner">
	<?php if($this->session->userdata('addImpCus')==1) { ?>
    	<form action="customers/importload" method="post" enctype="multipart/form-data" >
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Import Customer List </h2>
            <?php echo  $error; ?>
            <?php echo  $msg; ?>
			<table class="layout">
			 <tr>
				<td colspan="4">
			          <a href="assets/customer list.csv" style="text-decoration: underline"><b>Download CSV Template</b></a>
			    </td>
			    </tr> 
				<tr><td colspan="4">&nbsp;</td></tr>
			    <tr>
					<td colspan="4"><b>Note:</b>
					<br />
					Please Follow the csv format
					<br />
					FirstName,Lastname,Position,Company,Address Line 1,
					Address Line 2,Suburb,Postcode,Region,
					Country,State,Location,Direct Phone,Work Phone,Mobile Phone,Fax Line,Primary Email,
					secondary Email1,secondary Email2,secondary Email3,Skype Name,Web,Secondary Web,Comments
</td>
					
				</tr>
			    <tr><td colspan="4">&nbsp;</td></tr>
				<tr>
					<td width="100">Upload Customer List:</td>
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
								Import Customer List
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
		}?>
	</div>
</div>
<?php require ('tpl/footer.php'); ?>