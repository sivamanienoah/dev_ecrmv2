function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}


$('#advance_search').hide();

//For Advance Filters functionality.
$("#advanceFiltersDash").submit(function() {
	
	$('#advance').hide();
	$('#load').show();
	var project  = $("#project").val();
	var customer = $("#customer").val();
	var practice = $("#practice").val();

	$.ajax({
		type: "POST",	
		url: site_base_url+"invoice/index/",
		// dataType: "json",
		data: "filter=filter"+"&project="+project+"&customer="+customer+"&practice="+practice+'&'+csrf_token_name+'='+csrf_hash_token,
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