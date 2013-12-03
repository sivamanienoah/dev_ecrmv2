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
	
	//pre-populate the default region, country, state & location
	if(usr_level >= 2 && cus_updt != 'update' ) {
		getDefaultRegion(usr_level, cus_updt);
	}
	function getDefaultRegion(lvl, upd) {
		var sturl = "regionsettings/getRegDefault/"+lvl+"/"+upd;
		$('#def_reg').load(sturl);
		return false;
	}
	function getDefaultCountry(id, upd) {
		var sturl = "regionsettings/getCntryDefault/"+id+"/"+upd;
		$('#def_cntry').load(sturl);
		return false;	
	}
	function getDefaultState(id, upd) {
		var sturl = "regionsettings/getSteDefault/"+id+"/"+upd;
		$('#def_ste').load(sturl);
		return false;	
	}
	function getDefaultLocation(id, upd) {
		var sturl = "regionsettings/getLocDefault/"+id+"/"+upd;
		$('#def_loc').load(sturl);
		return false;	
	}
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////