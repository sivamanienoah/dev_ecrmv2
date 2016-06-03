<?php
$this->load->helper('custom_helper');
$this->load->helper('text_helper');
if (get_default_currency()) {
	$default_currency = get_default_currency();
	$default_cur_id   = $default_currency['expect_worth_id'];
	$default_cur_name = $default_currency['expect_worth_name'];
} else {
	$default_cur_id   = '1';
	$default_cur_name = 'USD';
}
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm">Invoices</h2>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Month & Year</th>
			<th>Project Title</th>
			<th>Project Code</th>
			<th>Milestone Name</th>
			<th>Value(<?php echo $default_cur_name; ?>)</th>
		</tr>
	</thead>
	<tbody>
	<?php
		if (is_array($invoices_data) && count($invoices_data) > 0) { ?>
		<?php foreach($invoices_data['invoices'] as $inv) { ?>
			<tr>
				<td><?php echo ($inv['month_year']!='0000-00-00 00:00:00') ? date('M Y', strtotime($inv['month_year'])) : ''; ?></td>
				<td><a title='View' href="project/view_project/<?php echo $inv['lead_id'] ?>"><?php echo character_limiter($inv['lead_title'], 30); ?></a></td>
				<td><?php echo isset($inv['pjt_id']) ? $inv['pjt_id'] : '-'; ?></td>
				<td><?php echo $inv['milestone_name']; ?></td>
				<td align="right"><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='4' align='right'><strong>Total Value</strong></td><td align='right'><?php echo sprintf('%0.2f', $invoices_data['total_amt']); ?></td>
		</tr>
	</tfoot>
</table>
<script>
$(function() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false
	});
});
</script>
<!--script type="text/javascript" src="assets/js/invoice/invoice_data-tbl.js"></script-->