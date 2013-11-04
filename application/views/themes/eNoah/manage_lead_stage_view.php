<?php
ob_start();
require (theme_url(). '/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/manage_lead_stage/manage_lead_stage_view.js"></script>
<div id="content">
	<div class="inner">
		<h2 class="clearfix"><span class="pull-left"><?php echo $page_heading; ?></span>
			<?php if($this->session->userdata('add')==1) { ?>
			<div class="pull-right">
				<button type="button" class="positive btn-leadStgAdd" onclick="location.href='<?php echo base_url(); ?>manage_lead_stage/leadStg_add'">Add New Lead Stage</button>
			</div>
	<?php } ?>
	</h2>

	<?php if($this->session->userdata('accesspage')==1) { ?>
	<div class="leadstg_note">
		To change the order of the lead stage, select and drag the lead stage and drop to the position in which you want the lead stage to appear.
	</div>
	<table cellpadding="0" cellspacing="0" class="lead-stg-list" width="100%">
		<tr>
			<th width="40%">Lead Stage</th>
			<th width="60px">Status</th>
			<th width="55px">Action</th>
			<th></th>
		</tr>
	</table>
	<div class="ls-container" class="clearfix" style="position: relative;">
		<ul id="lead_stg_items"></ul>
	</div>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div - close here -->
</div><!--Content div - close here -->

<script>
	function timerfadeout() {
		$('.dialog-err').empty();
	}
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>