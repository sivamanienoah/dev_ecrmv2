//For Filters functionality.
$("#filter_uc_dashboard").submit(function() {

	$('#uc_filter_submit').hide();
	$('#load').show();
	var form_data = $('#filter_uc_dashboard').serialize();
	
	$.ajax({
		type: "POST",
		url: site_base_url+"projects/service_graphical_dashboard/get_uc_val",
		dataType: "json",
		data: form_data,
		beforeSend:function() {
			$('#uc_container').empty();
			$('#uc_container').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			if(res.result) {
				$('#uc_container').html(res.html);
			} else {
				alert('Something Went Wrong');
			}
			$('#load').hide();
			$('#uc_filter_submit').show();
		}
	});
	return false;  //stop the actual form post !important!
});