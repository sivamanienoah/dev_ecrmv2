/*
 *@Customer Add View Jquery
*/

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
var id='';
var updt='';

function getCountry(val,id,updt) {
	var sturl = "regionsettings/getCountry/"+ val+"/"+id+"/"+updt;	
	//alert("SDfds");
    $('#country_row').load(sturl);	
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
					$('.checkUser2').hide(); 
					$('.checkUser1').show();
					$("#positiveBtn").attr("disabled", "disabled");
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
			var newCty = $('#newcountry').val();
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


/////////////////