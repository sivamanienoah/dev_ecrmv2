<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
?>
<div id="content">
	<div class="inner">
		<?php if($this->session->userdata('accesspage')==1) { ?>
			<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<table border="0" cellpadding="0" cellspacing="0" class="search-table">
					<tr>
						<td>
							Enquiry Search
						</td>
						<td>
							<input type="text" name="keyword" id="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Lead No, Job Title, Name or Company' ?>" class="textfield width200px g-search" />
						</td>
						<td>
							<div class="buttons">
								<button type="submit" class="positive">Search</button>
							</div>
						</td>
					</tr>
				</table>
			</form>

			<h2>Enquiry Dashboard</h2>
		
			<div>
				
				<div id="advance_search" style="float:left;">
				</div>
				<div id="advance_search_results" style="clear:both" ></div>
			</div>
	<?php 
		} else {
			echo "You have no rights to access this page";
		}
	?>
	</div>
</div>

<script type="text/javascript" src="assets/js/enquiries/enquiries_view.js"></script>
<?php
require (theme_url().'/tpl/footer.php'); 
ob_end_flush();
?>