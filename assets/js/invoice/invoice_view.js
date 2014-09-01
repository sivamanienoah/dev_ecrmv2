function advanced_filter() {
	$('#advance_search').slideToggle('slow');
	$('#invoice_note').slideToggle('slow');
}


$('#advance_search').hide();

//For Advance Filters functionality.
$("#advanceFiltersDash").submit(function() {
	
	$('#advance').hide();
	$('#load').show();
	var project   = $("#project").val();
	var customer  = $("#customer").val();
	var practice  = $("#practice").val();
	var from_date = $("#from_date").val();
	var to_date   = $("#to_date").val();

	$.ajax({
		type: "POST",	
		url: site_base_url+"invoice/index/",
		// dataType: "json",
		data: "filter=filter"+"&project="+project+"&customer="+customer+"&practice="+practice+"&from_date="+from_date+"&to_date="+to_date+'&'+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function(){
			$('#results').empty();
			$('#results').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			$('#advance').show();
			$('#results').html(res);
			$('#load').hide();
		}
	});
	return false;  //stop the actual form post !important!
});

$(function() {
	// $('#from_date, #to_date').datepicker({dateFormat: 'dd-mm-yy'});
	$('#from_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
		if($('#to_date').val!='')
		{
			$('#to_date').val('');
		}
		var return_date = $('#from_date').val();
		$('#to_date').datepicker("option", "minDate", return_date);
	}});
	$('#to_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
});