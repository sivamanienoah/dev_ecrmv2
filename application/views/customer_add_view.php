<?php require ('tpl/header.php'); ?>
<?php

if($this->validation->add1_region != 0) 
echo '<input type="hidden" name="region_update" id="region_update" value="'.$this->validation->add1_region.'" />';
if($this->validation->add1_country != 0)
echo '<input type="hidden" name="country_update" id="country_update" value="'.$this->validation->add1_country.'" />';
if($this->validation->add1_state != 0)
echo '<input type="hidden" name="state_update" id="state_update" value="'.$this->validation->add1_state.'" />';
if($this->validation->add1_location != 0)
echo '<input type="hidden" name="location_update" id="location_update" value="'.$this->validation->add1_location.'" />';
//When user edit the customer details the add button will not appear for the country, state & Location -starts here
if($this->uri->segment(3)=='update')
echo '<input type="hidden" name="varEdit" id="varEdit" value="update" />';
//When user edit the customer details the add button will not appear for the country, state & Location -Ends here
$usernme = $this->session->userdata('logged_in_user');
?>
<div id="content">
    <!--<div id="left-menu">
		<a href="customers/">Back To Customer List</a>
		<?php #if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) { ?>
		<a href="customers/view_subscriptions/<?php #echo $this->uri->segment(4); ?>">Subscriptions</a>
		<?php #} ?>
	</div>--> <?php //echo '<pre>'; print_r($_POST); echo '<?pre>'; ?>
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
    	<form id="formone" action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Customer Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">First name:*</td>
					<td width="240"><input type="text" name="first_name" value="<?php echo  $this->validation->first_name ?>" class="textfield width200px required" /> </td>
					<td width="100">Last Name:*</td>
					<td width="240"><input type="text" name="last_name" value="<?php echo  $this->validation->last_name ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Position:</td>
					<td><input type="text" name="position_title" value="<?php echo  $this->validation->position_title ?>" class="textfield width200px required" /></td>
                    <td>Company:*</td>
					<td><input type="text" name="company" value="<?php echo  $this->validation->company ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Address Line 1:</td>
					<td><input type="text" name="add1_line1" value="<?php echo  $this->validation->add1_line1 ?>" class="textfield width200px" /></td>
                    <td>Address Line 2:</td>
					<td><input type="text" name="add1_line2" value="<?php echo  $this->validation->add1_line2 ?>" class="textfield width200px" /></td>
				</tr>
				<?php //print_r($regions); ?>
				<tr>
					<td>Suburb:</td>
					<td><input type="text" name="add1_suburb" value="<?php echo  $this->validation->add1_suburb ?>" class="textfield width200px" /></td>
                    <td>Post code:</td>
					<td><input type="text" name="add1_postcode" value="<?php echo  $this->validation->add1_postcode ?>" class="textfield width200px" /></td>
					
				</tr>
				
				<tr>
				<td width="100">Region:*</td>
					<td width="240">
                        <select id="add1_region" name="add1_region" class="textfield width200px" onchange="getCountry(this.value)" class="textfield width200px required">
						<option value="0">Select Region</option>
                            <?php 
							foreach ($regions as $region) { ?>
								<option value="<?php echo  $region['regionid'] ?>"<?php echo  ($this->validation->add1_region == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo  $region['region_name']; ?></option>
							<?php } ?>
                        </select>
					</td>
				<td width="100">Country:*</td>
                    <td width="240" id='country_row'>
						<select id="add1_country" class="textfield width200px required" >
						<option value="0">Select Country</option>                           
                        </select>
						<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2) { ?>
							<a class="addNew" id="addButton"></a> <!--Display the Add button-->
						<?php } ?>	
					</td>
					
				</tr>
				
				<tr>
				<td width="100">State:*</td>
					<td width="240" id='state_row'>
                        <select id="add1_state" name="add1_state" class="textfield width200px required">
						<option value="0">Select State</option>                           
                        </select>
						<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3) { ?>
							<a id="addStButton" class="addNew"></a> <!--Display the Add button-->
						<?php } ?>
					</td>
				<td width="100">Location:*</td>
                    <td width="240" id='location_row'>
                        <select id="add1_location" name="add1_location" class="textfield width200px required">
						<option value="0">Select Location</option>                           
                        </select>
						<?php if ($this->userdata['level'] == 1 || $this->userdata['level'] == 2 || $this->userdata['level'] == 3 || $this->userdata['level'] == 4) { ?>
							<a id="addLocButton" class="addNew"></a> <!--Display the Add button-->
						<?php } ?>	
					</td>
					
                   
				</tr>
				<tr>
					<td>Direct Phone:</td>
					<td><input type="text" name="phone_1" value="<?php echo  $this->validation->phone_1 ?>" class="textfield width200px" />
						</td>
                    
					<td>Work Phone:</td>
					<td><input type="text" name="phone_2" value="<?php echo  $this->validation->phone_2 ?>" class="textfield width200px" /></td>
				</tr>
                    <tr>
					<td>Mobile Phone:</td>
					<td><input type="text" name="phone_3" value="<?php echo  $this->validation->phone_3 ?>" class="textfield width200px required" />
						</td>
                    
					<td>Fax Line:</td>
					<td><input type="text" name="phone_4" value="<?php echo  $this->validation->phone_4 ?>" class="textfield width200px" /></td>
				</tr>
                <tr>
					<td>Email:*</td>
					<td><input type="text" name="email_1" id="emailval"  autocomplete="off" value="<?php echo  $this->validation->email_1 ?>" class="textfield width200px required" /> 
					
					<div><span class="checkUser" style="color:green">Email Available.</span></div>
					<div><span class="checkUser1" id="email-existsval" style="color:red">Email Already Exists.</span></div>
					<div><span class="checkUser2" id="email-existsval" style="color:red">Invalid Email.</span></div>
					
					<input type="hidden" class="hiddenUrl"/>
					<?php if ($this->uri->segment(3) == 'update') { ?>
						<input type="hidden" value="<?php echo $this->uri->segment(4); ?>" name="emailupdate" id="emailupdate" />
					<?php } ?>
				</td>	
				
				
                    <td>Secondary Email:</td>
					<td><input type="text" name="email_2" value="<?php echo  $this->validation->email_2 ?>" class="textfield width200px required" /> 
					</td>
				</tr>
				<tr>
					<td>Email 3:</td>
					<td><input type="text" name="email_3" value="<?php echo  $this->validation->email_3 ?>" class="textfield width200px required" />
					</td>
                    <td>Email 4:</td>
					<td><input type="text" name="email_4" value="<?php echo  $this->validation->email_4 ?>" class="textfield width200px required" /> 
					</td>
				</tr>
				<tr>
					<td>Skype Name:</td>
					<td><input type="text" name="skype_name" value="<?php echo  $this->validation->skype_name ?>" class="textfield width200px required" /></td>
                    <td colspan="2">&nbsp;</td>
				</tr>
                <tr>
					<td>Web:</td>
					<td><input type="text" name="www_1" value="<?php echo  $this->validation->www_1 ?>" class="textfield width200px required" />
					</td>
                    <td>Secondary Web:</td>
					<td><input type="text" name="www_2" value="<?php echo  $this->validation->www_2 ?>" class="textfield width200px required" />
					</td>
				</tr>
                <tr>
					<td valign="top">Comments:</td>
					<td colspan="3"><textarea name="comments" class="textfield width200px" style="width:544px;" rows="2" cols="25"><?php echo  $this->validation->comments ?></textarea></td>
				</tr>
				
				
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_customer" id="positiveBtn" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Customer
							</button>
						</div>
                    </td>
					<td>&nbsp;</td>
                    <!--<td>
                        <?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && $userdata['level'] < 2) { ?>
						<?php if ($this->session->userdata('delete')==1) { ?>
                        <div class="buttons">
                            <button type="submit" name="delete_customer" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Customer
                            </button>
                        </div>
						<?php } ?>
                        <?php } else { echo "&nbsp;"; } ?>
                    </td>-->
				</tr>
            </table>
		</form>
		<?php } else{
			echo "You have no rights to access this page";
		}?>
	</div>
</div>
<?php require ('tpl/footer.php'); ?>
<script>
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
					getResult(username);
			} else {
					$('.checkUser2').show();
					$('.checkUser').hide();
					$('.checkUser1').hide();
					//$("#positiveBtn").attr("disabled", "disabled");
				}
		}
		return false;
    });
    function getResult(username){
        var baseurl = $('.hiddenUrl').val();
		$.ajax({
	    url : baseurl + 'customers/Check_email/'+username,
            cache : false,
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
        /*if( $('#newcountry').val().length > 2 )
            {
              var newCty = $('#newcountry').val();
              getCty(newCty);
            }
        return false;*/
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
            $.ajax({
            url : baseurl + 'customers/getCtyRes/' + newCty + "/" + regionId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{ 
						$.post("regionsettings/country_add_ajax",{regionid: $("#add1_region").val(),country_name:$("#newcountry").val(),created_by:(<?php echo $usernme['userid']?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
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
            $.ajax({
            url : baseurl + 'customers/getSteRes/' + newSte + "/" + cntyId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{
						$.post("regionsettings/state_add_ajax",{countryid: $("#add1_country").val(),state_name:$("#newstate").val(),created_by:(<?php echo $usernme['userid']?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
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
        /*if( $('#newlocation').val().length > 2 )
            {
              var newLoc = $('#newlocation').val();
              getLoc(newLoc);
            }
        return false;*/
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
            $.ajax({
            url : baseurl + 'customers/getLocRes/' + newLoc + '/' +stId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{
						$.post("regionsettings/location_add_ajax",{stateid: $("#add1_state").val(),location_name:$("#newlocation").val(),created_by:(<?php echo $usernme['userid']?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
						function(info){ $("#location_row").html(info); });
						$("#addstate").hide();
						//var steId = $("#add1_state").val();
						//$("#location_row").load("regionsettings/getLocation/" +steId);
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
</script>