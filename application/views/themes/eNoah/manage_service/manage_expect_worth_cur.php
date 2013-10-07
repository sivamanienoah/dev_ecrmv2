<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo baseurl();
?>
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	<!--<div style="margin:15px 0 0 0;">
		<div class="pull-left" style="margin: 3px 10px 15px 0;"> <h3> Lead Source </h3> </div>
		<?php if($this->session->userdata('add')==1) { ?>
			<div class="pull-left buttons"> 
				<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/ls_add'">
					Add New Lead Source
				</button>
			</div>
		<?php } ?>
	</div>-->
	
	<form action="manage_service/search_currency/" method="post" id="cust_search_form">
		<table border="0" cellpadding="0" cellspacing="0" class="search-table">
			<tr>
				<td>
					Search by Currency Type
				</td>
				<td>
					<input type="text" name="cust_search" value="<?php echo $this->uri->segment(4) ?>" class="textfield width200px" />
				</td>
				<td>
					<div class="buttons">
						<button type="submit" class="positive">
							Search
						</button>
					</div>
				</td>
				<?php if($this->session->userdata('add')==1) { ?>
				<td valign="middle";>
					<div class="buttons">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>manage_service/expect_worth_cur_add'">
							Add New Currency
						</button>
					</div>
				</td>
				<?php } ?>
				<?php if ($this->uri->segment(4)) { ?>
				<td>
					<div class="buttons">
						<button type="submit" name="cancel_submit" class="negative">
							Cancel
						</button>
					</div>
				</td>
				<?php } ?>
			</tr>
		</table>
	</form>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-table">
		<thead>
			<tr>
				<th width="12%">Action</th>
				<th>Currency</th>
				<th>Currency Name</th>
				<th>Status</th>
				<th>Default Currency</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($getExptWorthCur as $source) { ?>
			<tr>
				<td class="actions">
					<?php if($this->session->userdata('edit')==1) { ?>
						<a href="manage_service/expect_worth_cur_edit/update/<?php echo $source['expect_worth_id'] ?>/">Edit &raquo; </a> 
					<?php } else { echo "Edit &raquo;"; } ?> 
					<?php if(($this->session->userdata('delete')==1) && ($source['expect_worth_id'] != 1)) { ?>
						&nbsp;|&nbsp;
						<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $source['expect_worth_id'] ?>);"> Delete &raquo; </a> 
					<?php } else { echo "&nbsp;|&nbsp; Delete &raquo;"; } ?>
				</td>
				<td><?php echo $source['expect_worth_name']; ?></td>
				<td><?php echo $source['cur_name']; ?></td>
				<td>
					<?php if ($source['status'] == 1) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>					
				</td>
				<td>
					<?php if ($source['is_default']==1) { ?><img src="assets/img/tick.png" alt="Default Currency" style="width:14px; height:14px" /> <?php } ?>
					<div class="dialog-err pull-right" id="dialog-message-<?php echo $source['expect_worth_id'] ?>" style="display:none"></div>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<p><?php echo '&nbsp;'; ?></p>
	<div id="pager">
	<a class="first"> First </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<a class="prev"> &laquo; Prev </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<input type="text" size="2" class="pagedisplay"/><?php echo '&nbsp;&nbsp;&nbsp;'; ?> <!-- this can be any element, including an input --> 
	<a class="next"> Next &raquo; </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<a class="last"> Last </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
	<span>No. of Records per page:<?php echo '&nbsp;'; ?> </span>
	<select class="pagesize"> 
		<option selected="selected" value="10">10</option> 
		<option value="20">20</option> 
		<option value="30">30</option> 
		<option value="40">40</option> 
	</select> 
	</div>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div - close here -->
</div><!--Content div - close here -->
<script>
<?php if($this->session->userdata('accesspage')==1) { ?>
$(function() {
	$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager"),positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
<?php } ?>

function checkStatus(id) {
	var formdata = { 'data':id, '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
	$.ajax({
		type: "POST",
		url: '<?php echo base_url(); ?>manage_service/ajax_check_status_currency/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			//$("#loadingImage").show();
			$('#dialog-message-'+id).empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				//alert("You can't Delete the Lead source!. \n This Source is used in Leads.");
				$('#dialog-message-'+id).show();
				$('#dialog-message-'+id).append('One of more leads currently mapped to this currency. This cannot be deleted.');
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'manage_service/cur_type_delete/update/'+id;
				} else {
					return false;
				}
			}
		}                                                                                       
	});
return false;
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}

</script>

<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>