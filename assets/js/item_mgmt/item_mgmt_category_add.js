/*
 *@Item Management Category Jquery
*/

$(document).ready(function() {
	$('.errmsg').hide();
});

function valid() {
	var catname 			= $("#cat_name").val();
	var catupdt 			= $("#cat_updt").val();
	var params 				= {category: catname, cat_up: catupdt};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		url: "item_mgmt/checkcategoryname",
		data: params,
		type: "POST",
		dataType: 'json',
		success: function(data){
			if(data == 'fail') {
				$('.errmsg').show();
				return false;
			} else {
				document.formone.submit();
			}
		}		
	});
	return false;
}


/////////////////