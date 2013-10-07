<?php require (theme_url().'/tpl/header.php'); 
//print_r($roles);
?>

<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update')|| (($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) && ($this->session->userdata('edit')==1))){ ?>
		
			<form action="<?php echo  $this->uri->uri_string() ?>" method="post" id="frm" onsubmit="return checkForm();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> User Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <!--<p>All mandatory fields marked * must be filled in correctly.</p>-->
			<table class="layout">
				<tr>
					<td width="100">First Name:</td>
					<td width="240"><input type="text" id="first_name" name="first_name" value="<?php echo $this->validation->first_name ?>" class="textfield width200px required" />
						<div class="error" style="color:red;" id="error12">required</div>					
					</td>					
					<td width="100">Last Name:</td>
					<td width="240"><input type="text" id="last_name" name="last_name" value="<?php echo $this->validation->last_name ?>" class="textfield width200px required" /> 
						<div class="error" style="color:red;" id="error2">required</div>
					</td>
				</tr>
				<tr>
					<td>Telephone:</td>
					<td><input type="text" name="phone" value="<?php echo $this->validation->phone ?>" class="textfield width200px required" /></td>
                    <td>Mobile:</td>
					<td><input type="text" name="mobile" value="<?php echo $this->validation->mobile ?>" class="textfield width200px required" /></td>
				</tr>
				<tr>
					<td>Email:</td>
					<td><input type="text" id="email" name="email" value="<?php echo $this->validation->email ?>" class="textfield width200px" /><br/> 
					<span class="error" style="color:red;" id="error4">required</span>
					<span class="error" style="color:red;" id="notvalid">Not a valid e-mail address</span>
					<span class="checkUser" style="color:green">Email Available.</span>
					<span class="checkUser1" id="email-existsval" style="color:red">Email Already Exists.</span>
					<input type="hidden" class="hiddenUrl"/>
					<?php if ($this->uri->segment(3) == 'update') { ?>
						<input type="hidden" value="<?php echo $this->uri->segment(4); ?>" name="email_1" id="email_1" />
					<?php } ?>	
					</td>
                    
					<td>Role:</td>
					<td>
                        <select id="role_id" name="role_id" class="textfield width200px">
                            <option value="">Please Select</option>
							<?php foreach ($roles as $role) { ?>
								<option value="<?php echo $role['id'];?>" <?php echo  ($this->validation->role_id == $role['id']) ? ' selected="selected"' : '' ?>><?php echo $role['name'] ;?></option>
								
							<?php } ?>
                        </select> 
						<div class="error" style="color:red;" id="error3">required</div><input type="hidden" value="0" id="role_change_mail" name="role_change_mail"/>
						<script>
						$('#role_id').change(function() {
							var assign_mail = $('#role_id').val();
							//alert(assign_mail);
							$('#role_change_mail').val(assign_mail);
						});
						</script>
					</td>
				</tr>
				<!--<tr>
					<td>Email:</td>
					<td><input type="text" id="email" name="email" value="<?php //echo $this->validation->email ?>" class="textfield width200px" /><br/> 
					<span class="error" style="color:red;" id="error4">required</span>
					<span class="checkUser" style="color:green">Email Available.</span>
					<span class="checkUser1" id="email-existsval" style="color:red">Email Already Exists.</span>
					<input type="hidden" class="hiddenUrl"/>
					<?php //if ($this->uri->segment(3) == 'update') { ?>
						<input type="hidden" value="<?php //echo $this->uri->segment(4); ?>" name="email_1" id="email_1" />
					<?php //} ?>	
					</td>                    
						<script>						
						</script>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td>Sales Code:</td>
					<td>
						<select name="sales_code" class="textfield width200px">
                            <option value="select">Please Select</option>
							<?php
							//foreach ($cfg['sales_codes'] as $key => $val) {
								?>
								<option value="<?php #echo  $key ?>"<?php #echo  ($this->validation->sales_code == $key) ? ' selected="selected"' : '' ?>><?php #echo  $val ?></option>
							<?php
							//}
							?>
                        </select>
						</td>
				</tr>-->
				<!--<tr>
					<td>Office Key:</td>
					<td><input type="checkbox" name="key" value="1"<?php //if ($this->validation->key == 1) echo ' checked="checked"' ?> /> Staff member has a key to the office.</td>
                    <td>Building Key:</td>
					<td><input type="checkbox" name="bldg_key" value="1"<?php //if ($this->validation->bldg_key == 1) echo ' checked="checked"' ?> /> Staff member has a key to the building.</td>
				</tr>-->
				<tr>
					<td>Password:</td>
					<td><input type="password" id="password" name="password" value="" class="textfield width200px" />
						<?php if ($this->uri->segment(3) != 'update') { ?>
							<div class="error" style="color:red;" id="error5">required</div>
						<?php } ?>
					<?php //if ($this->uri->segment(3) != 'update') echo ' *' ?>
					</td>
                    <td>&nbsp;</td>
                    <td>
						<?php if (($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) && ($this->session->userdata('edit')==1)) { ?>
						
							<input type="checkbox" name="update_password" value="1" /> Update Password?
						<?php } else { ?>
							&nbsp; <input type="hidden" name="new_user" value="1" />
						<?php } ?>
					</td>					
				</tr>
				<tr>
				<!-- Levels and region settings starts here -->
				<td>Level:</td>
					<td>
					<?php if($this->uri->segment(3) != 'update') { ?>
                        <select id="level_id" name="level" class="textfield width200px">
                            <option value="">Please Select</option>
							<?php
							foreach ($levels as $val) {							
								?>
								<option value="<?php echo $val['level_id']; ?>"<?php echo  ($this->validation->level == $val['level_id']) ? ' selected="selected"' : '' ?>><?php echo  $val['level_name']; ?></option>
							<?php
								
							}
							?>
                        </select> <br/>
						<div class="error" style="color:red;" id="error6">required</div><br/> <br/> 
						<div class="level-message"></div>
						
						<?php } else { ?>
						<select id="level_id" name="level" class="textfield width200px">
                            <option value="">Please Select</option>
							<?php
							foreach ($levels as $val) {							
								?>
								<option value="<?php echo $val['level_id']; ?>"<?php echo  ($this->validation->level == $val['level_id']) ? ' selected="selected"' : '' ?>><?php echo  $val['level_name']; ?></option>
							<?php
								
							}
							?>
                        </select><br/> <div class="error" style="color:red;" id="error6">required</div><br/><br/> <div class="level-message"></div>
						<?php } ?>						
						<input type="hidden" value="0" id="level_change_mail" name="level_change_mail"/>
				<!-- Levels and region settings ends here -->	
					<?php if (($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) && ($this->session->userdata('edit')==1)) { ?>
					<td>Inactive User:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the user is inactive (ie. no longer working with us).</td>
					<?php } ?>
				</tr>
				<tr>
				
				<td colspan="5">
					<div class="container-region" style="float:left;display:none;">
							<table border="0" cellpadding="0" cellspacing="0" class="data-tabl-dupl">
								<thead>
										<tr>
											<th><div class="select-region" style="display:none">Select Region</div></th>
											<th><div class="select-country" style="display:none">Select Country</div></th>
											<th><div class="select-state" style="display:none">Select State</div></th>
											<th><div class="select-location" style="display:none">Select Location</div></th>
										</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="region-box" style="display:none">
												<select id="region_load" class="required" name="region[]" multiple="multiple">
											</div>
										</td>
										<td id="country_row">
											<div class="country-box" style="display:none">
											<select name="country[]" class="required" id="country_load" multiple="multiple"><option value="">Select</option></select>
											</div>
										</td>
										<td id="state_row">
											<div class="state-box" style="display:none">
											<select name="state[]" class="required" id="state_load" multiple="multiple"><option value="">Select</option></select>
											</div>
										</td>
										<td id="location_row">
											<div class="location-box" style="display:none">
											<select name="location[]" class="required" id="location_load" multiple="multiple"><option value="">Select</option></select>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
				</td>
				</tr>
				<tr><td><td>&nbsp;</td></td></tr>				
                <tr>
					<td>&nbsp;</td>
					<?php //if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<!--<td colspan="3">
						Active User cannot be modified! Please use my account to update your details.
					</td>-->
					<?php //} else { ?>
					<td>
                        <div class="buttons">
							<button type="submit" onclick="return last();" name="update_user" class="positive" id="checkemail">				
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> User
							</button>
						</div>
                    </td>
                    
					<?php //} ?>
				</tr>
            </table>
		</form>
		<?php } else{
			echo "You have no rights to access this page";
		}?>
	</div>
</div>
<?php require (theme_url(). '/tpl/footer.php'); ?>
<script type="text/javascript"> 
$(document).ready(function() {
$('.error').hide();
<?php if($this->uri->segment(3) != 'update') { ?>
$('button.positive').click(function() {
	var varFirstname=$('#first_name').val();
		if(varFirstname == ""){ 
			$('div#error12.error').show();
			return false;
		}else {
			$('div#error12.error').hide();
		}		
	var varLastname=$('#last_name').val();
		if(varLastname == ""){
			$('div#error2.error').show();
			return false;
		} else {
			$('div#error2.error').hide();
		}
	var varRoleid=$('#role_id').val();
		if(varRoleid == ""){
			$('div#error3.error').show();
			return false;
		} else {
			$('div#error3.error').hide();
		}	
	var varEmail=$('#email').val();		
		var atpos=varEmail.indexOf("@");
		var dotpos=varEmail.lastIndexOf(".");
		if(varEmail == ""){
			$('span#error4.error').show();
			$('span#notvalid.error').hide();
			$('span.checkUser').hide();
			return false;
		} 
		else if (atpos<1 || dotpos<atpos+2 || dotpos+2>=varEmail.length)
		{
			$('span#notvalid.error').show();
			$('span#error4.error').hide();
			$('span.checkUser').hide();
			return false;		  
		} else {
			$('span.checkUser').show();
			$('span#error4.error').hide();
			$('span#notvalid.error').hide();
		}
		
		
	var varPassword=$('#password').val();
		if(varPassword == ""){
			$('div#error5.error').show();
			return false;
		} else if(varPassword.length < 6 ) {
			alert('Password should be 6 characters');
			return false;
		} else {
			$('div#error5.error').hide();
		}
	var varLevelid=$('#level_id').val();
		if(varLevelid == ""){
			$('div#error6.error').show();
			return false;
		} else {
			$('div#error6.error').hide();
		}	
	if(varLevelid == 2) {
		var region_load = $('#region_load').val();	
		if(region_load == null) {
			alert('Please select region');
		}else {
			document.getElementById("frm").submit();
		}
	} else if(varLevelid == 1) {
		$(".level-message").html("Your level has set to Global");
		document.getElementById("frm").submit();
	}else if(varLevelid == 3) {
		var region_load = $('#region_load').val();
		var country_load = $('#country_load').val();		
		if(region_load == null) {
			alert('Please select region');
			return false;
		} else if(country_load == null) {
			alert('Please select country');
			return false;
		}	
		$.ajax({
			type: 'POST',
			url: 'user/checkcountry',
			dataType:'json',
			data: 'region_load='+region_load+'&country_load='+country_load+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
			success:function(data){		
				if(data.msg == 'noans'){
					alert('Please select corresponding country');
					return false;
				} else if(data.msg == 'success'){
					document.getElementById("frm").submit();
				}
				
			}			
        });
	} else if(varLevelid == 4) {
		var region_load = $('#region_load').val();
		var country_load = $('#country_load').val();
		var state_load = $('#state_load').val();
		if(region_load == null) {
			alert('Please select region');
			return false;
		} else if(country_load == null) {
			alert('Please select country');
			return false;
		}else if(state_load == null) {
			alert('Please select state');
			return false;
		}
		$.ajax({
			type: 'POST',
			url: 'user/checkstate',
			dataType:'json',
			data: 'region_load='+region_load+'&country_load='+country_load+'&state_load='+state_load+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
			success:function(data){		
				if(data.countrymsg == 'noans' || data.statemsg == 'nostate'){
					alert('Please select corresponding country/state');
					return false;
				} else if(data.countrymsg == 'success' && data.statemsg == 'success' ){
					document.getElementById("frm").submit();
				}
				
			}			
        });
	} else if(varLevelid == 5) {
		var region_load = $('#region_load').val();
		var country_load = $('#country_load').val();
		var state_load = $('#state_load').val();
		var location_load = $('#location_load').val();
		if(region_load == null) {
			alert('Please select region');
			return false;
		} else if(country_load == null) {
			alert('Please select country');
			return false;
		}else if(state_load == null) {
			alert('Please select state');
			return false;
		}else if(location_load == null) {
			alert('Please select location');
			return false;
		}
		$.ajax({
			type: 'POST',
			url: 'user/checklocation',
			dataType:'json',
			data: 'region_load='+region_load+'&country_load='+country_load+'&state_load='+state_load+'&location_load='+location_load+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
			success:function(data){		
				if(data.countrymsg == 'noans' || data.statemsg == 'nostate'|| data.locationmsg == 'noloc'){
					alert('Please select corresponding country/state/location');
					return false;
				} else if(data.countrymsg == 'success' && data.statemsg == 'success' && data.locationmsg == 'success' ){
					document.getElementById("frm").submit();
				}
				
			}			
        });
	}
return false;	
    });
	<?php } ?>		
//adduser
var addlevelid = $('#level_id').val();
		if($("#level_id").val() == 1) {
		    $('.container-region').hide();
			$(".level-message").html("Your level has set to Global");
			$(".region-box").css('display', 'none');
			$(".country-box").css('display', 'none');	
			$(".state-box").css('display', 'none');
			$(".location-box").css('display', 'none');
		}
		else if($("#level_id").val() == 2) {
		//alert($("#level_id").val());
			loadRegion();		
			$(".level-message").css('display', 'none');
			$(".country-box").css('display', 'none');	
			$(".state-box").css('display', 'none');
			$(".location-box").css('display', 'none');
			$('.container-region').show();
			$('.select-region').show();
			$('.select-country').hide();
			$('.select-state').hide();			
			$('.select-location').hide();	
		}
		else if($("#level_id").val() == 3) {
			var region_id = $("#region_load").val();
			if(region_id != 0) {
				var region_id = '';
			}
			$(".container-region option:selected").removeAttr("selected");
			$('#country_load option').empty();
			//document.write("<option value=''>Select</option>");
			loadRegion();
			$(".level-message").css('display', 'none');
			$(".region-box").css('display', 'block');
			$(".country-box").css('display', 'block');	
			$(".state-box").css('display', 'none');
			$(".state-row").css('display', 'none');
			$(".location-box").css('display', 'none');
			$('.container-region').show();
			$('.select-region').show();
			$('.select-country').show();
			$('.select-state').hide();			
			$('.select-location').hide();			
		}
		else if($("#level_id").val() == 4) {
			$(".container-region option:selected").removeAttr("selected");
			$('#country_load option').empty();
			$('#state_load option').empty();
			$(".level-message").css('display', 'none');
			$(".region-box").css('display', 'block');
			$(".country-box").css('display', 'block');	
			$(".state-box").css('display', 'block');
			$(".location-box").css('display', 'none');
			$('.container-region').show();
			$('.select-region').show();
			$('.select-country').show();
			$('.select-state').show();
			$('.select-location').hide();
			loadRegion();
		}
		else if($("#level_id").val() == 5) {
			$(".container-region option:selected").removeAttr("selected");
			$('#country_load option').empty();
			$('#state_load option').empty();
			$('#location_load option').empty();			
			$(".level-message").css('display', 'none');
			$(".region-box").css('display', 'block');
			$(".country-box").css('display', 'block');	
			$(".state-box").css('display', 'block');		
			$(".location-box").css('display', 'block');
			$('.container-region').show()
			$('.select-region').show();
			$('.select-country').show();
			$('.select-state').show();
			$('.select-location').show();
			loadRegion();
		}	
//end of adduser
var urlsegment =  "<?php echo $this->uri->segment(3); ?>";
	if(urlsegment == 'update') {	
	var editlevelid = $('#level_id').val();	
	
	if(editlevelid == "5") {
		$('.container-region').show();
		$(".level-message").css('display', 'none');
		$(".region-box").css('display', 'block');
		$(".country-box").css('display', 'block');	
		$(".state-box").css('display', 'block');		
		$(".location-box").css('display', 'block');
		$('.container-region').show();
		$('.select-region').show();
		$('.select-country').show();
		$('.select-state').show();
		$('.select-location').show();
		editloadRegion();
	} else if(editlevelid == "4") {
		$('.container-region').show();
		$(".level-message").css('display', 'none');
		$(".region-box").css('display', 'block');
		$(".country-box").css('display', 'block');	
		$(".state-box").css('display', 'block');		
		$(".location-box").css('display', 'none');
		$('.container-region').show();
		$('.select-region').show();
		$('.select-country').show();
		$('.select-state').show();
		$('.select-location').hide();		
		editloadRegion();
	} else if(editlevelid == "3") {
		$('.container-region').show();
		$(".level-message").css('display', 'none');
		$(".region-box").css('display', 'block');
		$(".country-box").css('display', 'block');	
		$(".state-box").css('display', 'none');		
		$(".location-box").css('display', 'none');
		$('.container-region').show();
		$('.select-region').show();
		$('.select-country').show();
		$('.select-state').hide();
		$('.select-location').hide();
		editloadRegion();
	}else if(editlevelid == "2") {
		$('.container-region').show();
		$(".level-message").css('display', 'none');
		$(".region-box").css('display', 'block');
		$(".country-box").css('display', 'none');	
		$(".state-box").css('display', 'none');		
		$(".location-box").css('display', 'none');
		$('.container-region').show();
		$('.select-region').show();
		$('.select-country').hide();
		$('.select-state').hide();
		$('.select-location').hide();
		editloadRegion();	
	} else if(editlevelid == "1") {	
		$(".level-message").html("Your level has set to Global");
	}	
}
	function editloadRegion() {
		$(".region-box").css('display', 'block');
		//var region_id = $("#region_load").val();
		var edit_userid =  "<?php echo $this->uri->segment(4); ?>";
		$.post( 
			'user/editloadRegions/'+edit_userid+'/',
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {
							
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#region_load").html(data);
						var regionid = $("#region_load").val();
						editloadCountry(regionid);
					}
			}
		);
	}
	function editloadCountry(regionid) {
		var region_id = $("#region_load").val();
		var edit_userid =  "<?php echo $this->uri->segment(4); ?>";
		//var country_id = $("#country_load").val();
		$.post( 
			'user/editloadCountrys/'+regionid+'/'+edit_userid+'/',
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {										
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#country_load").html(data);
						var country_id = $("#country_load").val();
						editloadState(country_id);
					}
			}
		);
	}

	function editloadState(country_id) {
		var edit_userid =  "<?php echo $this->uri->segment(4); ?>";
		var country_id = $("#country_load").val();
		//var state_id = $("#state_load").val();
		$.post( 
			'user/editloadStates/'+country_id+'/'+edit_userid+'/',
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {		
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#state_load").html(data);
						var state_id = $("#state_load").val();
						editloadLocation(state_id);
					}
			}
		);
	}
	
	function editloadLocation(state_id) {
		var edit_userid =  "<?php echo $this->uri->segment(4); ?>";
		var state_id = $("#state_load").val();
		//var loc_id = $("#location_load").val();
		$.post( 
			'user/editloadLocations/'+state_id+'/'+edit_userid+'/',
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#location_load").html(data);
					}
			}
		);
	}
	
//$('.container-region').hide();
$('.checkUser').hide();
    $('.checkUser1').hide();
    $('#email').blur(function(){
        if( $('#email').val().length >= 3 )
            {
              var username = $('#email').val();
			  var email1 = $('#email_1').val();
			  //alert(email1);
			  if (email1=='undefined') {
				getResult(username);
			  }
			  else {
				getResult(username, email1); 
				}
            }
        return false;
    });
	
    function getResult(name, email1) {
	//alert(email1);
        var baseurl = $('.hiddenUrl').val();
	      $.ajax({
            url : baseurl + 'user/getUserResult/'+name+'/'+email1,
            cache : false,
            success : function(response){
                $('.checkUser').hide();
                if(response == 'userOk') {	
					$('.checkUser').show(); 
					$('.checkUser1').hide(); 
					$("#checkemail").removeAttr("disabled");
				} else { 
					$('.checkUser').hide(); 
					$('.checkUser1').show();
					$("#checkemail").attr("disabled", "disabled");
				}
            }
        });
	}
	/*
	 Levels and regino setting functions starts
	*/
	function loadRegion() {
		$(".region-box").css('display', 'block');
		//var region_id = $("#region_load").val();
		$.post( 
			'user/loadRegions/',
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {
							
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#region_load").html(data);
					}
			}
		);
	}
	function loadCountry() {
		var region_id = $("#region_load").val();
		//var country_id = $("#country_load").val();
		$.post( 
			'user/loadCountrys/'+ region_id,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {										
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#country_load").html(data);
					}
			}
		);
	}
	function loadState() {
		var country_id = $("#country_load").val();
		//var state_id = $("#state_load").val();
		$.post( 
			'user/loadStates/'+ country_id,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {		
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#state_load").html(data);
					}
			}
		);
	}
	function loadLocation() {
		var state_id = $("#state_load").val();
		//var loc_id = $("#location_load").val();
		$.post( 
			'user/loadLocations/'+ state_id,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$("select#location_load").html(data);
					}
			}
		);
	}
	
	$('#level_id').change(function() {	
		var ff = "<?php echo $this->uri->segment(3); ?>";
		if(ff != '') {
			var success = confirm('Are you sure you want to Change Level? \nThis will make impact on the leads, where this user is assigned to.');
			if(success) {
				var level_assign_mail = $('#level_id').val();
				//alert(level_assign_mail);
				$('#level_change_mail').val(level_assign_mail);
				if($("#level_id").val() == 1) {
					$(".level-message").html("Your level has set to Global");
					$(".level-message").css('display', 'block');
					$('.container-region').hide();			
					$(".region-box").css('display', 'none');
					$(".country-box").css('display', 'none');	
					$(".state-box").css('display', 'none');
					$(".location-box").css('display', 'none');
				}
				else if($("#level_id").val() == 2) {
					loadRegion();		
					$(".level-message").css('display', 'none');
					$(".country-box").css('display', 'none');	
					$(".state-box").css('display', 'none');
					$(".location-box").css('display', 'none');
					$('.container-region').show();
					$('.select-region').show();
					$('.select-country').hide();
					$('.select-state').hide();			
					$('.select-location').hide();	
				}
				else if($("#level_id").val() == 3) {
					var region_id = $("#region_load").val();
					if(region_id != 0) {
						var region_id = '';
					}
					$(".container-region option:selected").removeAttr("selected");
					$('#country_load option').empty();
					//document.write("<option value=''>Select</option>");
					loadRegion();
					$(".level-message").css('display', 'none');
					$(".region-box").css('display', 'block');
					$(".country-box").css('display', 'block');	
					$(".state-box").css('display', 'none');
					$(".location-box").css('display', 'none');
					$('.container-region').show();
					$('.select-region').show();
					$('.select-country').show();
					$('.select-state').hide();			
					$('.select-location').hide();			
				}
				else if($("#level_id").val() == 4) {
					$(".container-region option:selected").removeAttr("selected");
					$('#country_load option').empty();
					$('#state_load option').empty();
					$(".level-message").css('display', 'none');
					$(".region-box").css('display', 'block');
					$(".country-box").css('display', 'block');	
					$(".state-box").css('display', 'block');
					$(".location-box").css('display', 'none');
					$('.container-region').show();
					$('.select-region').show();
					$('.select-country').show();
					$('.select-state').show();
					$('.select-location').hide();
					loadRegion();
				}
				else if($("#level_id").val() == 5) {
					$(".container-region option:selected").removeAttr("selected");
					$('#country_load option').empty();
					$('#state_load option').empty();
					$('#location_load option').empty();			
					$(".level-message").css('display', 'none');
					$(".region-box").css('display', 'block');
					$(".country-box").css('display', 'block');	
					$(".state-box").css('display', 'block');		
					$(".location-box").css('display', 'block');
					$('.container-region').show()
					$('.select-region').show();
					$('.select-country').show();
					$('.select-state').show();
					$('.select-location').show();
					loadRegion();
				}
			} else {
				location.reload(true);
			}
		} else {
			var level_assign_mail = $('#level_id').val();
				//alert(level_assign_mail);
				$('#level_change_mail').val(level_assign_mail);
				if($("#level_id").val() == 1) {
					$(".level-message").html("Your level has set to Global");
					$(".level-message").css('display', 'block');
					$('.container-region').hide();			
					$(".region-box").css('display', 'none');
					$(".country-box").css('display', 'none');	
					$(".state-box").css('display', 'none');
					$(".location-box").css('display', 'none');
				}
				else if($("#level_id").val() == 2) {
					loadRegion();		
					$(".level-message").css('display', 'none');
					$(".country-box").css('display', 'none');	
					$(".state-box").css('display', 'none');
					$(".location-box").css('display', 'none');
					$('.container-region').show();
					$('.select-region').show();
					$('.select-country').hide();
					$('.select-state').hide();			
					$('.select-location').hide();	
				}
				else if($("#level_id").val() == 3) {
					var region_id = $("#region_load").val();
					if(region_id != 0) {
						var region_id = '';
					}
					$(".container-region option:selected").removeAttr("selected");
					$('#country_load option').empty();
					//document.write("<option value=''>Select</option>");
					loadRegion();
					$(".level-message").css('display', 'none');
					$(".region-box").css('display', 'block');
					$(".country-box").css('display', 'block');	
					$(".state-box").css('display', 'none');
					$(".location-box").css('display', 'none');
					$('.container-region').show();
					$('.select-region').show();
					$('.select-country').show();
					$('.select-state').hide();			
					$('.select-location').hide();			
				}
				else if($("#level_id").val() == 4) {
					$(".container-region option:selected").removeAttr("selected");
					$('#country_load option').empty();
					$('#state_load option').empty();
					$(".level-message").css('display', 'none');
					$(".region-box").css('display', 'block');
					$(".country-box").css('display', 'block');	
					$(".state-box").css('display', 'block');
					$(".location-box").css('display', 'none');
					$('.container-region').show();
					$('.select-region').show();
					$('.select-country').show();
					$('.select-state').show();
					$('.select-location').hide();
					loadRegion();
				}
				else if($("#level_id").val() == 5) {
					$(".container-region option:selected").removeAttr("selected");
					$('#country_load option').empty();
					$('#state_load option').empty();
					$('#location_load option').empty();			
					$(".level-message").css('display', 'none');
					$(".region-box").css('display', 'block');
					$(".country-box").css('display', 'block');	
					$(".state-box").css('display', 'block');		
					$(".location-box").css('display', 'block');
					$('.container-region').show()
					$('.select-region').show();
					$('.select-country').show();
					$('.select-state').show();
					$('.select-location').show();
					loadRegion();
				}
		}	
	});
	$('#region_load').change(function() {
		loadCountry();
	});
	$('#country_load').change(function() {
		var cid = $('#country_load option:selected').val();  
		loadState();
	});
	$('#state_load').change(function() {
		loadLocation();
	});
});	
<?php if($this->uri->segment(3) == 'update') { ?>
function last() {
		var varLevelid=$('#level_id').val();				
		if(varLevelid == 2) {
			var region_load = $('#region_load').val();	
			if(region_load == null) {
				alert('Please select region');
				return false;
			} else {
				document.getElementById("frm").submit();
			}			
		} else if(varLevelid == 1) {
		$(".level-message").html("Your level has set to Global");
		document.getElementById("frm").submit();
	}else if(varLevelid == 3) {
		var region_load = $('#region_load').val();
		var country_load = $('#country_load').val();		
		if(region_load == null) {
			alert('Please select region');
			return false;
		} else if(country_load == null) {
			alert('Please select country');
			return false;
		}	
		$.ajax({
			type: 'POST',
			url: 'user/checkcountry',
			dataType:'json',
			data: 'region_load='+region_load+'&country_load='+country_load+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_token_name(); ?>',
			success:function(data){		
				if(data.msg == 'noans'){
					alert('Please select valuable country');
					return false;
				} else if(data.msg == 'success'){
					document.getElementById("frm").submit();
				}
				
			}			
        });
	} else if(varLevelid == 4) {
		var region_load = $('#region_load').val();
		var country_load = $('#country_load').val();
		var state_load = $('#state_load').val();
		if(region_load == null) {
			alert('Please select region');
			return false;
		} else if(country_load == null) {
			alert('Please select country');
			return false;
		}else if(state_load == null) {
			alert('Please select state');
			return false;
		}
		$.ajax({
			type: 'POST',
			url: 'user/checkstate',
			dataType:'json',
			data: 'region_load='+region_load+'&country_load='+country_load+'&state_load='+state_load+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
			success:function(data){		
				if(data.countrymsg == 'noans' || data.statemsg == 'nostate'){
					alert('Please select corresponding country/state');
					return false;
				} else if(data.countrymsg == 'success' && data.statemsg == 'success' ){
					document.getElementById("frm").submit();
				}
				
			}			
        });
	} else if(varLevelid == 5) {
		var region_load = $('#region_load').val();
		var country_load = $('#country_load').val();
		var state_load = $('#state_load').val();
		var location_load = $('#location_load').val();
		if(region_load == null) {
			alert('Please select region');
			return false;
		} else if(country_load == null) {
			alert('Please select country');
			return false;
		}else if(state_load == null) {
			alert('Please select state');
			return false;
		}else if(location_load == null) {
			alert('Please select location');
			return false;
		}
		$.ajax({
			type: 'POST',
			url: 'user/checklocation',
			dataType:'json',
			data: 'region_load='+region_load+'&country_load='+country_load+'&state_load='+state_load+'&location_load='+location_load+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
			success:function(data){		
				if(data.countrymsg == 'noans' || data.statemsg == 'nostate'|| data.locationmsg == 'noloc'){
					alert('Please select corresponding country/state/location');
					return false;
				} else if(data.countrymsg == 'success' && data.statemsg == 'success' && data.locationmsg == 'success' ){
					document.getElementById("frm").submit();
				}
				
			}			
        });
	}
		return false;
	}
	<?php } ?>
</script>