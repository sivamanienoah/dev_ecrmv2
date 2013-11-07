/*
 *@Add New Customer for Lead
 *@Welcome Controller
*/

	var id='';
	function getCountry(val,id) {
		var sturl = "regionsettings/getCountry/"+ val+"/"+id;	
		$('#country_row').load(sturl);	
		return false;	
	}
	function getState(val,id) {
		var sturl = "regionsettings/getState/"+ val+"/"+id;		
		$('#state_row').load(sturl);	
		return false;	
	}
	
	function getLocation(val,id) {
		var sturl = "regionsettings/getLocation/"+ val+"/"+id;	
		$('#location_row').load(sturl);	
		return false;	
	}

	$(document).ready(function() {
		$('.checkUser').hide();
		$('.checkUser1').hide();
		$('.checkUser2').hide();
		$('#emailval').keyup(function(){
			if( $('#emailval').val().length >= 1 )
			{
				var username = $('#emailval').val();
				//alert(email1);
				var filter = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(filter.test(username)){
						getResult(username);
				}else {
						$('.checkUser2').show();
						$('.checkUser').hide();
						$('.checkUser1').hide();
				}
			}
			return false;
		});
		function getResult(username){
			var baseurl = $('.hiddenUrl').val();
			var email = username
			var params = {};
			params[csrf_token_name] = csrf_hash_token;
			params['email'] = username;
			$.ajax({
				type: "POST",
				url : baseurl + 'customers/Check_email/',
				cache : false,
				data : params,
				success : function(response){
					$('.checkUser').hide();
					if(response == 'userOk') {
						$('.checkUser').show(); 
						$('.checkUser1').hide();
						$('.checkUser2').hide();
						$("#positiveBtn").removeAttr("disabled");
					} else { 
						$('.checkUser').hide(); 
						$('.checkUser2').hide(); 
						$('.checkUser1').show();
						$("#positiveBtn").attr("disabled", "disabled");
					}
				}
			});
		}
	});
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////