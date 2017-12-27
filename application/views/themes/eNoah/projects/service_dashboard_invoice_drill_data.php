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
// echo "<pre>"; print_r($invoices_data['invoices']); die;
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm">Invoices</h2>
	<div class="section-right">
		<div class="buttons export-to-excel">
			<button type="button" id='service_dashboard_inv_export_excel' class="positive excel" onclick="location.href='#'">
			Export to Excel
			</button>
			<input type="hidden" name="practices" id="practices" value="<?php echo $practices_id; ?>">
			<input type="hidden" name="excelexporttype" id="excelexporttype" value="<?php echo $excelexporttype; ?>">
		</div>
	</div>
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
//export to excel
$('#service_dashboard_inv_export_excel').click(function() {
	var practice   			 = $('#practices').val();
	var excelexporttype   	 = $('#excelexporttype').val();
	var month_year_from_date = $("#start_month").val();
	var month_year_to_date   = $("#end_month").val();
	// var billable_month   	 = $("#billable_month").val();
	alert(month_year_from_date+'-'+month_year_to_date); return;
	var url = site_base_url+"projects/dashboard/service_dashboard_data/";
	var form = $('<form action="' + url + '" method="post">' +
	  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	  '<input id="practice" type="hidden" name="practice" value="'+practice+'" />'+
	  '<input id="clicktype" type="hidden" name="clicktype" value="'+excelexporttype+'" />'+
	  '<input id="month_year_from_date" type="hidden" name="month_year_from_date" value="'+month_year_from_date+'" />'+
	  '<input id="month_year_to_date" type="hidden" name="month_year_to_date" value="'+month_year_to_date+'" />'+
	  // '<input id="billable_month" type="hidden" name="billable_month" value="'+billable_month+'" />'+
	  '</form>');
	$('body').append(form);
	$(form).submit();
	return false;
});
</script>
<!--script type="text/javascript" src="assets/js/invoice/invoice_data-tbl.js"></script-->