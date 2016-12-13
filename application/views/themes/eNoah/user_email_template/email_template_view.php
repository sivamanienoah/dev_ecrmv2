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
							<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>user_email_template/add_email_template'">
								Add New Template
							</button>
						</div>
					
				<div class="clearfix"></div>
				</div>
			</div>
		<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
			<thead>
				<tr>
					<th>S.No</th>
					<th>Template Name</th>
					<th>Modified On</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php $sno = 1; ?>
			<?php if (is_array($email_template) && count($email_template) > 0) { ?>
				<?php foreach($email_template as $emailtemp) { ?>
					<tr>
						<td><?php echo $sno; ?></td>
						<td>
							<a href="user_email_template/add_email_template/update/<?php echo $emailtemp['temp_id'] ?>"><?php echo $emailtemp['temp_name']; ?> </a> 
									
						</td>
						<td><?php echo $emailtemp['modified_on']; ?></td>
						<td class="actions">
							
								<a href="user_email_template/add_email_template/update/<?php echo $emailtemp['temp_id'] ?>">Edit &raquo; </a> 
							
							<?php //if($this->session->userdata('delete')==1) { ?>
								<!--&nbsp;|&nbsp;
								<a class="delete" href="email_template/delete_email_template/update/<?php echo $emailtemp['email_tempid'] ?>"> Delete &raquo; </a--> 
							<?php //} ?>
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