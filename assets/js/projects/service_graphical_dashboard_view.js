//For Filters Utilization cost
$("#filter_uc_dashboard").submit(function() {

	$('#uc_filter_submit').hide();
	$('#load').show();
	var form_data = $('#filter_uc_dashboard').serialize();
	
	$.ajax({
		type: "POST",
		url: site_base_url+"projects/service_graphical_dashboard/getUcVal",
		dataType: "json",
		data: form_data,
		beforeSend:function() {
			$('#uc_container').empty();
			$('#uc_container').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			// console.info(res); return;
			$('#load').hide();
			$('#uc_filter_submit').show();
			if(res.result) {
				$('#uc_container').html(res.html);
			} else {
				alert('Something Went Wrong');
			}
		}
	});
	return false;  //stop the actual form post !important!
});

//filter for revenue
$( "#inv_filter" ).on( "click", ".inv_filter_by", function() {
	var filter_val = $(this).val();
	var form_data = {};
	form_data[csrf_token_name] = csrf_hash_token;
	form_data['inv_filter_by'] = filter_val;
	$.ajax({
		type: "POST",
		url: site_base_url+"projects/service_graphical_dashboard/getInvoiceFilter",
		dataType: "json",
		data: form_data,
		beforeSend:function() {
			$('#inv_filter').empty();
			$('#inv_filter').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			// console.info(res); return;
			if(res.result) {
				$('#inv_filter').html(res.html);
			} else {
				alert('Something Went Wrong');
			}
		}
	});
	return false;  //stop the actual form post !important!
})