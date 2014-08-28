<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">	
			<div style="padding-bottom: 10px;">
				<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
				<div class="clearfix"></div>
				</div>
			</div>
		
		<?php 
		if($this->session->userdata('accesspage')==1)
		{ 
		?>
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
				<thead>
					<tr>
						<th>Lead Title</th>
						<th>Project Code</th>
						<th>Customer Name</th>
						<th>Milestone Name</th>
						<th>Actual Value</th>
						<th>Value(<?php echo $default_currency; ?>)</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
				<?php if (is_array($invoices) && count($invoices) > 0) { ?>
					<?php foreach($invoices as $inv) { ?>
						<tr>
							<td><?php echo character_limiter($inv['lead_title'], 30); ?></td>
							<td><?php echo isset($inv['pjt_id']) ? $inv['pjt_id'] : '-'; ?></td>
							<td><?php echo $inv['customer']; ?></td>
							<td><?php echo $inv['project_milestone_name']; ?></td>
							<td><?php echo $inv['actual_amt']; ?></td>
							<td><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td> 
							<td><?php echo date('d-m-Y', strtotime($inv['invoice_generate_notify_date'])); ?></td>
						</tr>
					<?php } ?>
				<?php } ?>
				</tbody>
			</table>
		<?php 
		}
		else { 
			echo "You have no rights to access this page"; 
		}
		?>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>