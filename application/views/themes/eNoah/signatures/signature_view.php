<?php //error_reporting(E_ALL);
     //ini_set('display_errors', 1);
	 
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">	
			
		<div style="padding-bottom: 10px;">
				<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
					
						<div class="buttons pull-right">
							<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>signatures/add_signature'">
								Add New Signature
							</button>
						</div>
					
				<div class="clearfix"></div>
				</div>
			</div>
		<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
			<thead>
				<tr>
					<th>S.No</th>
					<th>Signature Name</th>
					<th>Modified On</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php $sno = 1; ?>
			<?php if (is_array($signatures) && count($signatures) > 0) { ?>
				<?php foreach($signatures as $signature) { ?>
					<tr>
						<td><?php echo $sno; ?></td>
						<td>
							<a href="signatures/add_signature/update/<?php echo $signature['sign_id'] ?>"><?php echo $signature['sign_name']; ?> </a> 
									
						</td>
						<td><?php echo $signature['modified_on']; ?></td>
						<td class="actions">
						<a href="signatures/add_signature/update/<?php echo $signature['sign_id'] ?>">Edit &raquo; </a> 
						<a class="delete" href="signatures/delete_signature/update/<?php echo $signature['sign_id'] ?>"> Delete &raquo; </a>
						</td>
					</tr>
					<?php $sno++; ?>
			<?php } }?>
		
			</tbody>
		</table>
	
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>