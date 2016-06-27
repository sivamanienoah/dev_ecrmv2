<style>
.ui-autocomplete{ z-index: 2147483647 !important; width: 204px; !important; }
.clr-icon{ display:none; }
.text-danger { color: red; }
</style>
<?php $usernme = $this->session->userdata('logged_in_user'); ?>
<p>All mandatory fields marked * must be filled in correctly.</p><p class="error-cont" style="display:none;">&nbsp;</p>
		<form name="customer_detail_form" id="customer_detail_form" method="post" onsubmit="return false;">
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<table class="layout">
				<!--tr>
					<td width="100">First name:*</td>
					<td width="240"><input type="text" name="first_name" value="" class="textfield width200px required" /> </td>
					<td width="100">Last Name:</td>
					<td width="240"><input type="text" name="last_name" value="" class="textfield width200px required" /></td>
				</tr-->
				<tr>
					<!--td>Position:</td>
					<td><input type="text" name="position_title" value="" class="textfield width200px required" /></td-->
                    <td>Company:*</td>
					<td>
						<input type="text" id="company_name" name="company" value="" class="textfield width200px required" /><a class="clr-icon"></a>
						<input type="hidden" id="company_id" name="company_id" value="" class="textfield width200px required" />
						<br />
						<span class="company_name_err_msg text-danger"></span>
					</td>
				</tr>
				<tr>
					<td>Address Line 1:</td>
					<td><input type="text" id="add1_line1" name="add1_line1" value="" class="textfield width200px" /></td>
                    <td>Address Line 2:</td>
					<td><input type="text" id="add1_line2" name="add1_line2" value="" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td>Suburb:</td>
					<td><input type="text" id="add1_suburb" name="add1_suburb" value="" class="textfield width200px" /></td>
                    <td>Post code:</td>
					<td><input type="text" id="add1_postcode" name="add1_postcode" value="" class="textfield width200px" /></td>
				</tr>
				<tr>
					<td>Region:*</td>
						<?php if (($usernme['level']>=2) && ($this->uri->segment(3)!='update')) { ?>
							<td width="240" id="def_reg"></td> <!--pre-populate the default region, country, state & location-->
						<?php } else { ?>
							<td>
								<select name="add1_region" id="add1_region" class="textfield width200px" onchange="getCountry(this.value)" class="textfield width200px required">
								<option value="0">Select Region</option>
									<?php 
									foreach ($regions as $region) { ?>
										<option value="<?php echo  $region['regionid'] ?>"><?php echo  $region['region_name']; ?></option>
									<?php } ?>
								</select>
							</td>
						<?php } ?>
					<td>Country:*</td>
					<?php if (($usernme['level']>=3) && ($this->uri->segment(3)!='update')) { ?>
						<td width="240" id="def_cntry"></td>
					<?php } else { ?>
						<td id='country_row'>
							<select id="add1_country" name="add1_country" class="textfield width200px required" >
							<option value="0">Select Country</option>                           
							</select>
						<a class="addNew" id="addButton" style ="display:none;"></a>
						<br />
						</td>
					<?php } ?>
				</tr>
				<tr>
					<td>State:*</td>
					<?php if (($usernme['level']>=4) && ($this->uri->segment(3)!='update')) { ?>
							<td width="240" id="def_ste"></td>
						<?php } else { ?>
						<td id='state_row'>
							<select id="add1_state" name="add1_state" class="textfield width200px required">
								<option value="0">Select State</option>                           
							</select>
						<a id="addStButton" class="addNew" style ="display:none;"></a>
						<br />
						<span class="add1_state_err_msg text-danger"></span>
						</td>
					<?php } ?>
					<td>Location:*</td>
					<?php if (($usernme['level']>=5) && ($this->uri->segment(3)!='update')) { ?>
						<td width="240" id="def_loc"></td> 
					<?php } else { ?>
						<td id='location_row'>
							<select name="add1_location" id="add1_location" class="textfield width200px required">
							<option value="0">Select Location</option>                           
							</select>
						<a id="addLocButton" class="addNew" style ="display:none;"></a>
						<br />
						<span class="add1_location_err_msg text-danger"></span>
						</td>
						
					<?php } ?>
				</tr>
				<tr>                  
					<td>Work Phone:</td>
					<td><input type="text" id="phone" name="phone" value="" class="textfield width200px" /></td>
					<td>Fax Line:</td>
					<td><input type="text" id="fax" name="fax" value="" class="textfield width200px" /></td>
                <tr>
					<td>Email:</td>
					<td><input type="text" id="email_2" name="email_2" id="emailval" autocomplete="off" value="" class="textfield width200px required" /> 
					<div><span class="checkUser" style="color:green">Valid Email.</span></div>
					<div><span class="checkUser1" id="email-existsval" style="color:red">Email Already Exists.</span></div>
					<div><span class="checkUser2" id="email-existsval" style="color:red">Invalid Email.</span></div>
					<input type="hidden" class="hiddenUrl"/>
					</td>
                   <td>Web:</td>
					<td><input type="text" id="www" name="www" value="" class="textfield width200px required" />
				</tr>
				<tr>
					<td>Sales Contact Name:</td>
					<td>
						<input type="text" name="sales_contact_name" value="<?php echo $usernme['first_name'].' '.$usernme['last_name']; ?>" class="textfield width200px" readonly />
						<input type="hidden" name="sales_contact_userid_fk" value="<?php echo $usernme['userid']; ?>" class="textfield width200px" readonly />
					</td>
                    <td>Sales Contact Email:</td>
					<td>
					<input type="text" name="sales_contact_email" value="<?php echo $usernme['email']; ?>" class="textfield width200px" readonly />
					</td>
				</tr>
				<tr>
					<tr>
						<td colspan='4'>
							<table class="table websiteBrd data-tbl dashboard-heads dataTable" id="document_tbl" >
								<thead>
									<tr class="bg-blue">
										<td>Name</td>
										<td>Email ID</td>
										<td>Position</td>
										<td>Contact No</td>
										<td>Skype</td>
									</tr>
								</thead>
								<tr>
									<td>
										<input type="hidden" name="custid" value="" class="textfield contact_id required" />
										<input type="text" name="customer_name" id="contact_first_name" value="" class="first_name textfield width150px required" />
										<span class="first_name_err_msg text-danger"></span>
									</td>
									<td>
									   <input type="text" name="email_1" id="contact_email" value="" class="textfield email width150px required" />
										<span class="email_err_msg text-danger"></span>
									</td>
									<td>
									   <input type="text" name="position_title" value="" class="position_title textfield width80px required" />
										<span class="position_title_err_msg text-danger"></span>
									</td>
									<td>
									   <input type="text" name="phone_1" id="contact_phone" value="" class="textfield phone width150px required" />
										<span class="phone_err_msg text-danger"></span>
									</td>
									<td>
									   <input type="text" name="skype_name" value="" class="textfield skype width110px required" />
										<span class="skype_err_msg text-danger"></span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tr>
				<tr></tr>
				<tr></tr>
				<tr></tr>
				<tr>	
					<td></td>
					<td style="float:right">
						<div id="subBtn" class="buttons">
							<button type="submit" class="positive" id="positiveBtn" onclick="add_customer(); return false;">Add</button>
						</div>
					</td>
					<td>
						<div class="buttons">
							<button type="submit" onclick="ndf_cancel();">Cancel</button>
						</div>
                    </td>
					<td></td>
				</tr>
			</table>
</form>

<script>
var usr_level 		 = "<?php echo $usernme['level']; ?>";
var cus_updt		 = "<?php echo ($this->uri->segment(3) == 'update') ? 'update' : 'no_update' ?>";
$(document).ready(function() {
		//autocomplete for fetching the company name
		$( "#company_name" ).autocomplete({
			minLength: 2,
			source: function(request, response) {
				$.ajax({ 
					url: "customers/ajax_company_search",
					data: { 'cust_name': $("#company_name").val(),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
					type: "POST",
					dataType: 'json',
					async: false,
					success: function(data) {
						response( data );
					}
				});
			},
			select: function(event, ui) {
				$('.clr-icon').show();
				$('#company_id').val(ui.item.companyid);
				$('#company_name').attr('readonly', 'true');
				$('#add1_region').val(ui.item.regId);
				$('#add1_line1').val(ui.item.add1_line1);
				$('#add1_line2').val(ui.item.add1_line2);
				$('#add1_suburb').val(ui.item.add1_suburb);
				$('#add1_postcode').val(ui.item.add1_postcode);
				$('#phone').val(ui.item.phone);
				$('#fax').val(ui.item.fax);
				$('#email_2').val(ui.item.email_2);
				$('#www').val(ui.item.www);
				getCountry(ui.item.regId,ui.item.cntryId);
				getState(ui.item.cntryId,ui.item.stId);
				getLocation(ui.item.stId,ui.item.locId);
			}
		});		
});

//form submit
function add_customer() 
{
	var err=false;
	$('.add1_location_err_msg, .add1_region_err_msg, .add1_country_err_msg, .add1_state_err_msg').remove();
	
	if($('#company_name').val()==""){
		err = true;
		$('.company_name_err_msg').html("This field is required");
	} else {
		$('.company_name_err_msg').html('');
	}
	if($('#add1_region').val()==0){
		err = true;
		// $('.add1_region_err_msg').html("This field is required");
		$("#add1_region").parent().append('<div class="add1_region_err_msg text-danger" style="width: 100%; display: inline-block;">This field is required</div>');
	} else {
		$('.add1_region_err_msg').html('');
	}
	if($('#add1_country').val()==0){
		err = true;
		$("#add1_country").parent().append('<div class="add1_country_err_msg text-danger" style="width: 100%; display: inline-block;">This field is required</div>');
	} else {
		$('.add1_country_err_msg').html('');
	}
	if($('#add1_state').val()==0){
		err = true;
		$("#add1_state").parent().append('<div class="add1_state_err_msg text-danger" style="width: 100%; display: inline-block;">This field is required</div>');
	} else {
		$('.add1_state_err_msg').html('');
	}
	if($('#add1_location').val()==0){
		err = true;
		$("#add1_location").parent().append('<div class="add1_location_err_msg text-danger" style="width: 100%; display: inline-block;">This field is required</div>');
	} else {
		$('.add1_location_err_msg').html('');
	}
	if($('#contact_first_name').val()==""){
		err = true;
		$('.first_name_err_msg').html("This field is required");
	} else {
		$('.first_name_err_msg').html('');
	}
	if($('#contact_email').val()==""){
		err = true;
		$('.email_err_msg').html("This field is required");
	} else {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var emailres = regex.test($('#contact_email').val());
		if(!emailres){
			err = true;
			$('.email_err_msg').html("Not a vaild email");
		} else {
			$('.email_err_msg').html('');
		}
	}
	if($('#contact_phone').val()==""){
		err = true;
		$('.phone_err_msg').html("This field is required");
	} else {
		$('.phone_err_msg').html('');
	}
	
	if(err == true){
		return false;
	}
	
    $('.new-cust-form-loader .error-handle:visible').slideUp(300);
    var form_data = $('#customer_detail_form').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
    $.post(
        'customers/add_custom_customer',
        form_data,
        function(res) {
            if (typeof (res) == 'object') {
                if (res.error == false) {
                    ex_cust_id = res.custid;
                    $("#ex-cust-name").val(res.cust_name1);
                    $.unblockUI();	
                    $('.notice').slideUp(400);
                    showMSG('<div id=confirm>New Customer Added!</div>');
                    $('.q-cust-name span').html(res.cust_name);
                    $('.q-cust-email span').html(res.cust_email);
					$('.q-cust-company span').html(res.cust_company);
					getUserForLeadAssign(res.cust_reg,res.cust_cntry,res.cust_ste,res.cust_locn);
                } else {
					$('.blockUI .layout').unblock();
                    $('.error-cont').html(res.ajax_error_str).slideDown(400);
					
                }
            } else {
                $('.error-cont').html('<p class="form-error">Your session timed out!</p>').slideDown(400);
            }
        },
		"json"
    )
    return false;
}

$('#customer_detail_form').delegate( '.clr-icon', 'click', function () {
	$('#customer_detail_form')[0].reset();
	$("#company_name").removeAttr("readonly");
	$('#add1_country').prop('selectedIndex',0);
	$('#add1_state').prop('selectedIndex',0);
	$('#add1_location').prop('selectedIndex',0);
	$('.clr-icon').hide();
});
</script>
<script type="text/javascript" src="assets/js/helper/new_customer_form.js"></script>