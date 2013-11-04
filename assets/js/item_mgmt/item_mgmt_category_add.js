/*
 *@Item Management Category Jquery
*/

	$(document).ready(function() {
		$('.errmsg').hide();
	});

	function valid() {
		var catname = $("#cat_name").val();
		var catupdt = $("#cat_updt").val();
		$.ajax({
			url: "item_mgmt/checkcategoryname",
			data: {category: catname, cat_up: catupdt,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
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