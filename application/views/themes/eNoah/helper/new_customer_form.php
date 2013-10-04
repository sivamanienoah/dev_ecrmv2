<p>All mandatory fields marked * must be filled in correctly.</p><p class="error-cont" style="display:none;">&nbsp;</p>
		<form name="customer_detail_form" id="customer_detail_form" method="post" onsubmit="return false;">
		
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<table class="layout">
				<tr>
					<td width="100">First name:*</td>
					<td width="240"><input type="text" name="first_name" value="" class="textfield width200px required" /> </td>
					<td width="100">Last Name:*</td>
					<td width="240"><input type="text" name="last_name" value="" class="textfield width200px required" /></td>
				</tr>
				<tr>
					<td>Position:</td>
					<td><input type="text" name="position_title" value="" class="textfield width200px required" /></td>
                    <td>Company:*</td>
					<td><input type="text" name="company" value="" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Address Line 1:</td>
					<td><input type="text" name="add1_line1" value="" class="textfield width200px" /></td>
                    <td>Address Line 2:</td>
					<td><input type="text" name="add1_line2" value="" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td>Suburb:</td>
					<td><input type="text" name="add1_suburb" value="" class="textfield width200px" /></td>
                    <td>Post code:</td>
					<td><input type="text" name="add1_postcode" value="" class="textfield width200px" /></td>
					
				</tr>
				<tr>
				<td>Region:*</td>
					<td>
                        <select name="add1_region" id="add1_region" class="textfield width200px" onchange="getCountry(this.value)" class="textfield width200px required">
						<option value="0">Select Region</option>
                            <?php 
							foreach ($regions as $region) { ?>
								<option value="<?php echo  $region['regionid'] ?>"><?php echo  $region['region_name']; ?></option>
							<?php } ?>
                        </select>
					</td>
				<td>Country:*</td>
                    <td id='country_row'>
                        <select id="add1_country" name="add1_country" class="textfield width200px required" >
						<option value="0">Select Country</option>                           
                        </select>
					<a class="addNew" id="addButton" style ="display:none;"></a>	
					</td>
					
				</tr>
				<tr>
				<td>State:*</td>
					<td id='state_row'>
                        <select id="add1_state" name="add1_state" class="textfield width200px required">
							<option value="0">Select State</option>                           
                        </select>
					<a id="addStButton" class="addNew" style ="display:none;"></a>
					</td>
					<td>Location:*</td>
                    <td id='location_row'>
                        <select name="add1_location" class="textfield width200px required">
						<option value="0">Select Location</option>                           
                        </select>
					<a id="addLocButton" class="addNew" style ="display:none;"></a>
					</td>
					
                   
				</tr>
				<tr>
					<td>Direct Phone:</td>
					<td><input type="text" name="phone_1" value="" class="textfield width200px" />
						</td>
                    
					<td>Work Phone:</td>
					<td><input type="text" name="phone_2" value="" class="textfield width200px" /></td>
				</tr>
                    <tr>
					<td>Mobile Phone:</td>
					<td><input type="text" name="phone_3" value="" class="textfield width200px required" />
						</td>
                    
					<td>Fax Line:</td>
					<td><input type="text" name="phone_4" value="" class="textfield width200px" /></td>
				</tr>
                <tr>
					<td>Email:*</td>
					<td><input type="text" name="email_1" id="emailval" autocomplete="off" value="" class="textfield width200px required" /> 
					<div><span class="checkUser" style="color:green">Email Available.</span></div>
					<div><span class="checkUser1" id="email-existsval" style="color:red">Email Already Exists.</span></div>
					<div><span class="checkUser2" id="email-existsval" style="color:red">Invalid Email.</span></div>
					<input type="hidden" class="hiddenUrl"/>
					<?php if ($this->uri->segment(3) == 'update') { ?>
						<input type="hidden" value="<?php echo $this->uri->segment(4); ?>" name="email_1" id="email_1" />
					<?php } ?>
					</td>
                    <td>Secondary Email:</td>
					<td><input type="text" name="email_2" value="" class="textfield width200px required" /> 
					</td>
				</tr>
				<tr>
					<td>Web:</td>
					<td><input type="text" name="www_1" value="" class="textfield width200px required" />
					</td>
                    <td>Secondary Web:</td>
					<td><input type="text" name="www_2" value="" class="textfield width200px required" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
                        <div id="subBtn" class="buttons">
							<button type="submit" class="positive" id="positiveBtn" onclick="ndf_add(); return false;">Add</button>
						</div>
						<div class="buttons">
							<button type="submit" onclick="ndf_cancel();">Cancel</button>
						</div>
                    </td>
                    <td>
						
					</td>
                    <td>&nbsp;</td>
				</tr>
			</table>
		</form>
<script>
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

/*$("#emailval").keyup(function(){
    if($(this).val()) {
        alert('hi');
    } else {
        $(".checkUser").show();
    }    
}); */

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
</script>