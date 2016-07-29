/*
 *@Customer Add View Jquery
*/
var id='';
var updt='';

if(document.getElementById('region_update')) {
var reg = document.getElementById('region_update').value;
if (document.getElementById('country_update')){
var cty = document.getElementById('country_update').value;
}
if (document.getElementById('state_update')){
	var st = document.getElementById('state_update').value;
}
if (document.getElementById('location_update')){
	var loc = document.getElementById('location_update').value;
}
if (document.getElementById('varEdit')){
var updt = document.getElementById('varEdit').value;
}
if(reg != 0 && cty != 0)
getCountry(reg,cty,updt);

if(cty != 0 && st != 0)
getState(cty,st,updt);

if(st != 0 && loc != 0)
getLocation(st,loc,updt);
}

function getCountry(val,id,updt) {
	var sturl = "regionsettings/getCountry/"+ val+"/"+id+"/"+updt;	
	//alert("SDfds");
    $('#country_row').load(sturl);
	$('.region_err_msg').empty();
    return false;	
}
function getState(val,id,updt) {
	var sturl = "regionsettings/getState/"+ val+"/"+id+"/"+updt;	
    $('#state_row').load(sturl);	
    return false;	
}
function getLocation(val,id,updt) {
	var sturl = "regionsettings/getLocation/"+ val+"/"+id+"/"+updt;	
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
			var filter = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if(filter.test(username)){
					//getResult(username);
					$('.checkUser').show(); 
					$('.checkUser1').hide();
					$('.checkUser2').hide();
					$("#positiveBtn").removeAttr("disabled");
			} else {
					$('.checkUser').hide();
					$('.checkUser1').hide();
					$('.checkUser2').show();
					$("#positiveBtn").attr("disabled", "disabled");
				}
		}
		return false;
    });

    function getResult(email){
        var baseurl = $('.hiddenUrl').val();
		var email = email
		var params = {};
		params[csrf_token_name] = csrf_hash_token;
		params['email'] = email;
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
	
	
	$('#document_tbl').delegate( '.check_email', 'keyup', function () {
		var thisRow = $(this).parent('td');
		if( $(this).val().length >= 3 )
		{
			var email 	   = $(this).val();
			var company_id = $('#emailupdate').val();
			var custids    = $(this).parent().parent().children().find('.contact_id').val();

			var filter = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if(filter.test(email)){
				if (custids == "")
				var custid 	= 0;
				else
				var custid 	= custids;
			
				var params		= {};
				params[csrf_token_name] = csrf_hash_token;
				params['email'] 		= email;
				params['custid'] 		= custid;
				params['company_id'] 	= company_id;
				$.ajax({
					type: "POST",
					url : site_base_url + 'customers/Check_email/',
					cache : false,
					data : params,
					success : function(response){
						if(response == 'userOk') {
							$("#positiveBtn").removeAttr("disabled");
							$(thisRow).children(".email_err_msg").html("<span class='ajx_success_msg'>Email Available.</span>");
						} else { 
							$(thisRow).children(".email_err_msg").html('Email Already Exists.');
							$("#positiveBtn").attr("disabled", "disabled");
						}
					}
				});
			} else {
				$("#positiveBtn").attr("disabled", "disabled");
				$(thisRow).children(".email_err_msg").html('Not valid email.');
			}
		}
		return false;
    });
	
	$( "#company" ).keyup(function() {
		$('.company_err_msg').empty();
	});
});


//jQuery code added for adding New Country, New State & New Location -- Starts Here
function ajxCty(){
	$("#addcountry").slideToggle("slow");
}

function ajxSaveCty(){
	$(document).ready(function() {
       
		if ($('#newcountry').val() == "") {
			alert("Country Required.");
		}
		else {
			var regionId = $("#add1_region").val();
			var newCty 	 = $('#newcountry').val();
            getCty(newCty, regionId);
		}	

    function getCty(newCty){
			var baseurl = $('.hiddenUrl').val();
			var params = {regionid: $("#add1_region").val(),country_name:$("#newcountry").val(),created_by:(customer_user_id)};
			params[csrf_token_name]      = csrf_hash_token; 

            $.ajax({
            url : baseurl + 'customers/getCtyRes/' + newCty + "/" + regionId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{ 
						$.post("regionsettings/country_add_ajax",params, 
						function(info){$("#country_row").html(info);});
						$("#addcountry").hide();

						//var regId = $("#add1_region").val();
						$("#state_row").load("regionsettings/getState");
					}
                else
					{ 
						alert('Country Exists.'); 
					}
            }
        });
	}
	});	
}


function ajxSt() {
	$("#addstate").slideToggle("slow");
}

function ajxSaveSt() {
	$(document).ready(function() {
        /*if( $('#newstate').val().length > 2 )
            {
              var newSte = $('#newstate').val();
              getSte(newSte);
            }
        return false;*/
	
	if ($('#newstate').val() == "") {
			alert("State Required.");
		}
		else {
			var cntyId = $("#add1_country").val()
			var newSte = $('#newstate').val();
            getSte(newSte,cntyId);
		}	
		
	function getSte(newSte,cntyId) {
			var baseurl = $('.hiddenUrl').val();
			var params = {countryid: $("#add1_country").val(),state_name:$("#newstate").val(),created_by:(customer_user_id)};
			params[csrf_token_name]      = csrf_hash_token; 
			
            $.ajax({
            url : baseurl + 'customers/getSteRes/' + newSte + "/" + cntyId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{
						$.post("regionsettings/state_add_ajax",params, 
						function(info){ $("#state_row").html(info); });
						$("#addstate").hide();

						$("#location_row").load("regionsettings/getLocation");
					}
                else
					{ 
						alert('State Exists.');
					}
            }
        });
	}
	});	
}


function ajxLoc() {
	$("#addLocation").slideToggle("slow");
}

function ajxSaveLoc() {
	$(document).ready(function() {
	if ($('#newlocation').val() == "") {
		alert("Location Required.");
	}
	else {
		var stId = $("#add1_state").val();
		var newLoc = $('#newlocation').val();
		getLoc(newLoc,stId);
	}
		
	function getLoc(newLoc,stId) {
			var baseurl = $('.hiddenUrl').val();
			var params = {stateid: $("#add1_state").val(),location_name:$("#newlocation").val(),created_by:(customer_user_id)};
			params[csrf_token_name]  = csrf_hash_token; 
            $.ajax({
            url : baseurl + 'customers/getLocRes/' + newLoc + '/' +stId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{
						$.post("regionsettings/location_add_ajax",params, 
						function(info){ $("#location_row").html(info); });
						$("#addstate").hide();
					}
                else
					{ 
						alert('Location Exists.');
					}
            }
        });
	}
	});	
}
//jQuery code added for adding New Country, New State & New Location -- Ends Here

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
function getSalescontactDetails(location_id) {

alert('location=='+location_id);

}

//Multiple customer add
//for document format form
$('#document_tbl').delegate( '#addRow', 'click', function () {
	var thisRow = $(this).closest('tr');
	$(this).hide();
	$("#document_tbl tbody tr").find('.del_file').show();	
	var obj = $(thisRow).clone().insertAfter(thisRow);
		obj.find(".contact_id").val("");
		obj.find(".first_name").val("");
		obj.find(".last_name").val("");
		obj.find(".position_title").val("");
		obj.find(".phone").val("");
		obj.find(".email").val("");
		obj.find(".skype").val("");
		obj.find(".hyperfields").css('border','');
		obj.find(".first_name_err_msg").text('');
		obj.find(".last_name_err_msg").text('');
		obj.find(".position_title_err_msg").text('');
		obj.find(".phone_err_msg").text('');
		obj.find(".email_err_msg").text('');
		obj.find(".skype_err_msg").text('');
		obj.find("#deleteRow").attr('hyperid','0');
		obj.find('.createBtn').show();
});

$('#document_tbl').delegate( '.del_file', 'click', function () {
	var thisRow = $(this).parent('td').parent('tr');
	if( $(this).attr('hyperid') !=0 ) {
		var hyperid = $(this).attr('hyperid');
		var x = confirm("Are you Sure want to remove?");
		if(x==true)
		{
			/* $.post(site_base_url+'customers/delete_contact?id='+hyperid,function( data ) {
				$(thisRow).remove();
				if($('#document_tbl tbody tr').length<=1){
					$('#document_tbl .del_file').hide();
					$('#document_tbl .createBtn').show();
				}
			}); */
			var formdata = { 'id':hyperid }
			formdata[csrf_token_name] = csrf_hash_token;
			$.ajax({
				async: false,
				type: "POST",
				url: site_base_url+'customers/delete_contact/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function() {
				},
				success: function(response) {
					if (response.html == 'NO') {
						alert('One or more Leads currently mapped to this customer. This cannot be deleted.');
					} else {
						$(thisRow).remove();
						$("#document_tbl tbody tr:last").find('.createBtn').show();
						// $("#document_tbl tbody tr:last").find('.del_file').hide();
						if($('#document_tbl tbody tr').length<=1){
							$('#document_tbl .del_file').hide();
							$('#document_tbl .createBtn').show();
						}
						alert('Contact Deleted.');
					}
				}          
			});
		}
	} else {
		$(thisRow).remove();
		
		if($('#document_tbl tbody tr').length<=1){
			$('#document_tbl .del_file').hide();
			$('#document_tbl .createBtn').show();
		}
	}
	$("#document_tbl tbody tr").each(function(){
		$("#document_tbl tbody tr:last").find('.createBtn').show();
	})
});


$("#document_tbl tbody tr").each(function(){
	$("#document_tbl tbody tr:last").find('.createBtn').show();
});
if($('#document_tbl tbody tr').length<=1){
	$('#document_tbl .del_file').hide();
	$('#document_tbl .createBtn').show();
}
function validate_customer()
{
	var err 	  = true;
	var empty_err = true;
	var cmpy_err  = true;
	$(".company_err_msg").empty();
	$('.ajx_failure_msg').remove();
	
	if($('#company').val()=="") {
		$(".company_err_msg").html("<span class='ajx_failure_msg'>Company is Required</span>");
		empty_err = false;
	}
	if($('#add1_region').val()==0) {
		$(".region_err_msg").html("<span class='ajx_failure_msg'>Region is Required</span>");
		empty_err = false;
	}
	if($('#add1_country').val()==0) {
		$('#add1_country').after('<span class="ajx_failure_msg">Country is Required</span>')
		empty_err = false;
	}
	if($('#add1_state').val()==0) {
		$('#add1_state').after('<span class="ajx_failure_msg">State is Required</span>');
		empty_err = false;
	}
	if($('#add1_location').val()==0) {
		$('#add1_location').after('<span class="ajx_failure_msg">Location is Required</span>');
		empty_err = false;
	}
	
	if(($('#add1_region').val()==0) && ($('#add1_country').val()==0) && ($('#add1_state').val()==0) && ($('#add1_location').val()==0) && $('#company').val()==""){
		return false;
	} else {
		//validate company name
		var params					= {};
		params[csrf_token_name] 	= csrf_hash_token;
		params['company_name']		= $('#company').val();
		params['add1_region'] 		= $('#add1_region').val();
		params['add1_country'] 		= $('#add1_country').val();
		params['add1_state'] 		= $('#add1_state').val();
		params['add1_location']		= $('#add1_location').val();
		params['company_id']		= $('#emailupdate').val();
		
		$.ajax({
			async: false,
			type: "POST",
			url : site_base_url + 'customers/check_company/',
			cache : false,
			data : params,
			success : function(response){
				if(response == 'userNo') {
					cmpy_err = false;
					$(".company_err_msg").html("<span class='ajx_failure_msg'>The Company Already Exists for this Region, Country, State & Location</span>");
					return false;
				} else {
					// $("#positiveBtn").removeAttr("disabled");
				}
			}
		});
	}	

	
	//First Name
	$('.first_name').each(function(){
		if($(this).val()=="")
		{
			$(this).closest('tr').find('.first_name_err_msg').html("This field is required");
			err=false;
		}else{
			$(this).closest('tr').find('.first_name_err_msg').html(" ");
		}
	});
	
	//First Name
	$('.email').each(function(){
		if($(this).val()=="")
		{
			$(this).closest('tr').find('.email_err_msg').html("This field is required");
			err=false;
		}else{
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			var emailres = regex.test($(this).val());
			if(!emailres){
				err = false;
				$(this).closest('tr').find('.email_err_msg').html("Not a vaild email");
			} else {
				$(this).closest('tr').find('.email_err_msg').html(" ");
			}
		}
	});
	
	//First Name
	$('.phone').each(function(){
		if($(this).val()=="")
		{
			$(this).closest('tr').find('.phone_err_msg').html("This field is required");
			err=false;
		}else{
			$(this).closest('tr').find('.phone_err_msg').html(" ");
		}
	});

	if(false == empty_err) {
		return false;
	}
	if(false == cmpy_err) {
		return false;
	}
	
	if(err == true) {
		$('#formone').submit()
	} else if(err == false) {
		return false;
	}
}
/////////////////