<?php
// $this->load->helper('text');
$pt_arr = array();
if(!empty($project_type) && count($project_type)>0){
	foreach($project_type as $prec){
		$pt_arr[$prec->id] = $prec->project_billing_type;
	}
}
?>
<?php #echo "<pre>"; print_r($projects_data); echo "</pre>"; ?>
<div class="customize-sec">
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" id="pjts-data-tbl" width="100%">
	<thead>
		<tr>
			<th>Action</th>
			<th title="Customer & Contact Name">Customer & Contact Name</th>
			<th title="Project Name">Project Name</th>
			<th title="Practice">Practice</th>
			<th title="Project Start Date">Project Start Date</th>
			<th title="Project End Date">Project End Date</th>
			<th title="Project Value">Project Value</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			if (is_array($projects_data) && count($projects_data) > 0) {
				foreach($projects_data as $record) {
		?>			
					<td class="actions" align="center">
						<a title="View" target="_blank" class="view-icon" href="<?php echo base_url(); ?>project/view_project/<?php echo $record['lead_id']; ?>"><img src='assets/img/view.png' alt='view'></a>
					</td>
					<td><?php echo $record['company_name']." - ".$record['customer_contact_name']; ?></td>
					<td><?php echo character_limiter($record['project_name'], 30); ?></td>
					<td><?php echo $record['practice']; ?></td>
					<td><?php echo $record['actual_date_start']; ?></td>
					<td><?php echo $record['actual_date_due']; ?></td>
					<td align="right"><?php echo sprintf('%0.2f', $record['converted_amount']); ?></td>
		<?php
				}
			}
		?>
	</tbody>
	<tfoot><tr><td text align=right colspan="6">Total:</td><td align="right"></td></tr></tfoot>
</table>
</div>

<script type="text/javascript">
$(function() {
	dtPjtTable();
	
	//export to excel
	$('#service_dashboard_export_excel').click(function() {
		var practice   			 = $('#practices').val();
		var excelexporttype   	 = $('#excelexporttype').val();
		// var month_year_from_date = $("#month_year_from_date").val();
		// var month_year_to_date   = $("#month_year_to_date").val();
		// var billable_month   	 = $("#billable_month").val();

		var url = site_base_url+"projects/dashboard/service_dashboard_data/";
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		  '<input id="practice" type="hidden" name="practice" value="'+practice+'" />'+
		  '<input id="clicktype" type="hidden" name="clicktype" value="'+excelexporttype+'" />'+
		  // '<input id="month_year_from_date" type="hidden" name="month_year_from_date" value="'+month_year_from_date+'" />'+
		  // '<input id="month_year_to_date" type="hidden" name="month_year_to_date" value="'+month_year_to_date+'" />'+
		  // '<input id="billable_month" type="hidden" name="billable_month" value="'+billable_month+'" />'+
		  '</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
});	
function dtPjtTable() {
	$('#pjts-data-tbl').dataTable( {
		"aaSorting": [[ 0, "desc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bAutoWidth": false,
		"bDestroy": true,
		"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
			var cost = 0
			for ( var i=0 ; i<aaData.length ; i++ )
			{
				var TotalMarks = aaData[i][6]; 
				//var str = TotalMarks.split(" "); //for USD 1200.00
				//cost += parseFloat(str[1]);//for USD 1200.00
				cost += parseFloat(TotalMarks);
			}
			var nCells = nRow.getElementsByTagName('td');
			//nCells[1].innerHTML = "USD " + cost.toFixed(2); //for USD 1200.00
			nCells[1].innerHTML = cost.toFixed(2);
		}
	});
}
</script>