<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">	
		<h2><?php echo $page_heading; ?></h2>
		
		<?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons pull-right" style="padding-bottom:10px;">
					<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>email_template/add_email_template'">
						Add New Template
					</button>
				</div>
				
				<div class="buttons pull-right" style="padding-bottom:10px;">
					<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>email_template/add_template_header'">
						Template Header & Footer
					</button>
				</div>
		<?php } ?>
		
		<?php 
		if($this->session->userdata('accesspage')==1) 
		{ 
		?>
		<table border="0" cellpadding="0" cellspacing="0" class="dashboard-heads dataTable" style="width:100%">
			<thead>
				<tr>
					<th>S.No</th>
					<th>Template Name</th>
					<th>Template Title</th>
					<th>Email From</th>
					<th>Modified On</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php $sno = 1; ?>
			<?php foreach($email_template as $emailtemp) { ?>
				<tr>
					<td><?php echo $sno; ?></td>
					<td><?php echo $emailtemp['email_templatename']; ?></td>
					<td><?php echo strip_tags($emailtemp['email_templatesubject']); ?></td>
					<td><?php echo $emailtemp['email_templatefrom']; ?></td>
					<td><?php echo $emailtemp['modified_on']; ?></td>
					<td class="actions">
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="email_template/add_email_template/update/<?php echo $emailtemp['email_tempid'] ?>/">Edit &raquo; </a> 
						<?php } else { echo "Edit"; } ?> 
						<?php if($this->session->userdata('delete')==1) { ?>
							&nbsp;|&nbsp;
							<a class="delete" href="email_template/delete_email_template/update/<?php echo $emailtemp['email_tempid'] ?>"> Delete &raquo; </a> 
						<?php } ?>
					</td>
				</tr>
				<?php $sno++; ?>
			<?php } ?>
			</tbody>
		</table>
		<?php 
		} 
		else 
		{ 
			echo "You have no rights to access this page"; 
		}
		?>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>