<?php require (theme_url().'/tpl/header.php'); ?>
<?php $this->userdata = $this->session->userdata('logged_in_user'); ?>
<?php 
	// echo "<pre>"; print_r($page); echo "</pre>"; exit;
?>
<div id="content">
    <div id="left-menu">
		<a href="customers/">Back To List</a>
    </div>
    <div class="inner">
	<?php if($this->session->userdata('addImpCus')==1) { ?>
    	<form action="customers/import_customers" method="post" enctype="multipart/form-data" >
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Import Customer List </h2>
			
            <?php 
				if(isset($page) && ($page['insert_customers']!=0))
				echo $page['insert_customers']." customer(s) Succesfully inserted. <br />";
			?>
			
			<?php 
				if(isset($page) && ($page['update_customers']!=0))
				echo $page['update_customers']." customer(s) Succesfully updated. <br />"; 
			?>
			<?php 
				if(isset($page) && ($page['insert_contacts']!=0))
				echo $page['insert_contacts']." contact(s) Succesfully inserted. <br />"; 
			?>
			<?php 
				if(isset($page) && ($page['update_contacts']!=0))
				echo $page['update_contacts']." contact(s) Succesfully updated. <br />"; 
			?>
			<?php 
				if(isset($page) && ($page['error']!=0))
				echo $page['error']; 
				echo "<br>";
				if(isset($page) && !empty($page['invalid_email']) && count($page['invalid_email'])>0){
					$invalid_email = implode('<br/>', $page['invalid_email']);
					echo " The following companies having <b>Invalid emails</b> <br /> $invalid_email";
				}
				echo "<br>";
				if(isset($page) && !empty($page['invalid_custemail']) && count($page['invalid_custemail'])>0){
					$invalid_custemail = implode('<br/>', $page['invalid_custemail']);
					echo " The following customers having <b>Invalid emails</b> <br /> $invalid_custemail";
				}
			?>
			<table class="layout">
			 <tr>
				<td colspan="4">
			          <a href="assets/customer list.csv" style="text-decoration: underline"><b>Download CSV Template</b></a>
			    </td>
			    </tr> 
				<tr><td colspan="4">&nbsp;</td></tr>
			    <tr>
					<td colspan="4">
						<b>Note:</b><br />
						Company Name<br />
						Region<br />
						Country<br />
						State<br />
						Location<br />
						Customer Name<br />
						Customer Position<br />
						Customer Email<br />
						Customer Phone<br />
						<b>The Above mentioned fields are Mandatory Fields and the other fields are optional.</b><br />
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
<?php require (theme_url().'/tpl/footer.php'); ?>