/*
 *@Manage expect worth currency add
 *@Manage Service Controller
*/

	//site_base_url,csrf_token_name & csrf_hash_token is global javascript variable 

	$( "#country_name" ).change(function() {
		var cur_id = $("#country_name").val();
		var formdata = { 'data':cur_id }
			formdata[csrf_token_name] = csrf_hash_token;
		$.ajax({
			type: "POST",
			url: site_base_url+'manage_service/get_cur_name/',
			dataType:"json",                                                                
			data: formdata,
			cache: false,
			beforeSend:function(){
				$('#cur_name').empty();
				$('#cur_short_name').empty();
			},
			success: function(response) {
				// alert(response.cur_name);
				$('#cur_name').val(response.cur_name);
				$('#cur_short_name').val(response.cur_short_name);
			}                                                                                       
		});
		return false;
	});
	
	document.getElementById("is_default").disabled = true;
	
	function toggleCheckbox(obj) {
		// if(obj.checked){document.getElementById("is_default").disabled = false;}
		// else{document.getElementById("is_default").disabled = true;} 
	}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////