<?php require ('tpl/header.php'); 
$p=array();
if(!empty($packageid_fk)){
	foreach($packageid_fk as $val){
		$k=$val['packageid_fk'];
		$p[$k]=$val['due_date'];
	}
}
$usernme = $this->session->userdata('logged_in_user');
?>
<style type="text/css">
#domain-expiry-date {
		display:none;
}
</style>
<link rel="stylesheet" href="assets/css/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<input type="hidden" class="hiddenUrl"/>
<script type="text/javascript">
var nc_form_msg = '<div class="new-cust-form-loader">Loading Content.<br />';
nc_form_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';

$(document).ready(
	function(){
		$("#cust_name").autocomplete("hosting/ajax_customer_search/", { minChars:2 }).result(function(event, data, formatted) {
			$('#cust_id').val(data[1]);
		});
		$('input.pick-date').datepicker({dateFormat: 'dd-mm-yy'});
		
		$('input[name="domain_mgmt"]').change(function(){
				if ($('input[name="domain_mgmt"]:checked').val() == 'ENOAH') {
						$('#domain-expiry-date:hidden').show();
				} else {
						$('#domain-expiry-date:visible').hide();
				}
		});
		
		if ($('input[name="domain_mgmt"]:checked').val() == 'ENOAH') {
				$('#domain-expiry-date:hidden').show();
		} else {
				$('#domain-expiry-date:visible').hide();
		}
		
		$('.modal-new-cust').click(function(){
				$.blockUI({
							message:nc_form_msg,
							css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default'},
							overlayCSS: {backgroundColor:'#EAEAEA', opacity: '0.9', cursor: 'wait'}
						});
				$.get(
					'ajax/data_forms/new_customer_form',
					{},
					function(data){
						$('.new-cust-form-loader').slideUp(500, function(){
							$(this).parent().css({backgroundColor: '#fff', color: '#333'});
							$(this).css('text-align', 'left').html(data).slideDown(500, function(){
								$('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
							});
						})
					}
				);
			return false;
		});
	}
);
</script>
<div id="content">
    <!--<div id="left-menu">
		<a href="hosting">Back To Hosting</a>
	</div>-->
    <div class="inner"> <?php //$usrid = $this->session->userdata('userid'); ?>
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3)!= 'update') || (($this->session->userdata('edit')==1) && ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Hosting Account Details</h2>
            <?php if (!$this->input->post('domain_name') && $this->uri->segment(3) != 'update') { ?>
            <p class="notice">If this is a new customer, please be sure to <a href="#" class="modal-new-cust" >add the customer</a> to the database before adding the hosting account.</p>
			<?php } ?>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="120">Customer Name:*</td>
					<td width="300">
                        <input type="text" name="customer_name" id="cust_name" value="<?php echo  (isset($customer_name)) ? $customer_name : '' ?>" class="textfield width200px" /> 
                        <input type="hidden" name="customer_id" id="cust_id" value="<?php echo  (isset($customer_id)) ? $customer_id : '' ?>" />
                    </td>
				</tr>
				<tr>
					<td>Domain Name:*</td>
					<td><input type="text" name="domain_name" value="<?php echo  $this->validation->domain_name ?>" class="textfield width200px required" /> </td>
				</tr>
				<tr>
					<td>Domain Management:</td>
					<td>
						<input type="radio" name="domain_mgmt" value="ENOAH"<?php echo ((!isset($_POST['domain_mgmt']) && !is_null($this->validation->domain_expiry)) || (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'ENOAH')) ? ' checked="checked"' : '' ?> /> eNoahiSolution &nbsp;&nbsp;
						<input type="radio" name="domain_mgmt" value="CM"<?php echo ((!isset($_POST['domain_mgmt']) && is_null($this->validation->domain_expiry)) || (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'CM')) ? ' checked="checked"' : '' ?> /> Client Managed &nbsp;&nbsp;
					</td>
				</tr>
				<tr id="domain-expiry-date">
					<td>Domain Expiry Date:*</td>
					<td><input type="text" name="domain_expiry" value="<?php echo  $this->validation->domain_expiry ?>" class="textfield width200px pick-date" /> </td>
                    
				</tr>
				<tr>
					<td>Domain Status:*</td>
					<td>
						<select name="domain_status" class="textfield width200px">
						<?php
							foreach ($this->login_model->cfg['domain_status'] as $key => $value) {
								$selected = ($this->validation->domain_status == $key) ? ' selected="selected"' : ''; ?>
								<option value="<?php echo  $key ?>"<?php echo  $selected ?>><?php echo  $value ?></option>
						<?php	} ?>
						</select> 
					</td>
                </tr>
				<tr>
					<td>Package Name:*</td>
					<td>
						<select name="packageid_fk[]" class="textfield" size=6 multiple=multiple style="width:300px;">
						<option value="">Select Package</option>
						<?php
						if(!empty($package)){
						foreach ($package as $val) {
							if(!empty($p[$val['package_id']])) { 
								$s= ' selected="selected"'; 
								if(strtotime($p[$val['package_id']])>0) $k=' - ('.date('d-m-Y',strtotime($p[$val['package_id']])).')';
								else $k='';
							}
							else { $s=''; $k='';}
							echo '<option value="'.$val['package_id'].'"'.$s.'>'.$val['package_name'].$k.'</option>';
					 } }?>
						</select> 
					</td>
                </tr>
				<tr>
					<td>Hosting Expiry Date:</td>
					<td><input type="text" name="expiry_date" value="<?php echo  $this->validation->expiry_date ?>" class="textfield width200px pick-date" /> </td>
                    
				</tr>
				<tr>
					<td>SSL:</td>
					<td>
						<?php foreach ($this->login_model->cfg['domain_ssl_status'] as $key => $value) { ?>
						<input type="radio" name="ssl" value="<?php echo $key ?>"<?php echo ($this->validation->ssl == $key) ? ' checked="checked"' : '' ?> /> <?php echo $value ?> &nbsp;&nbsp;
						<?php } ?>
					</td>
                    
				</tr>
				<tr>
					<td>Other information:</td>
					<td><textarea name="other_info" class="textfield width200px"><?php echo  $this->validation->other_info ?></textarea></td>
                    
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_customer" class="positive">
								
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Account
							</button>
						</div>
                    </td>
                   <!--<td>
                        <?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && $userdata['level'] < 1) { ?>
                        <div class="buttons">
                            <button type="submit" name="delete_account" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Account
                            </button>
                        </div>
                        <?php } else { echo "&nbsp;"; } ?>
                    </td>-->
                    <td>&nbsp;</td>
				</tr>
            </table>
		</form>
		<?php } else{
			echo "You have no rights to access this page";
		}?>
	</div>
</div>
<?php require ('tpl/footer.php'); ?>
<script type="text/javascript">
function ndf_cancel() {
    $.unblockUI();
    return false;
}

function ndf_add() {
    $('.new-cust-form-loader .error-handle:visible').slideUp(300);
    var form_data = $('#customer_detail_form').serialize();
	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
    $.post(
        'customers/add_customer/false/false/true',
        form_data,
        function(res) {
            if (typeof (res) == 'object') {
                if (res.error == false) {
                    ex_cust_id = res.custid;
                    // $("#ex-cust-name").val(res.cust_name1);
                    $("#cust_name").val(res.cust_name1);
					$("#cust_id").val(res.custid);
                    $.unblockUI();	
                    $('.notice').slideUp(400);
                    showMSG('<div id=confirm>New Customer Added!</div>');
                    // $('.q-cust-name span').html(res.cust_name);
                    // $('.q-cust-email span').html(res.cust_email);
					// $('.q-cust-company span').html(res.cust_company);
					// getUserForLeadAssign(res.cust_reg,res.cust_cntry,res.cust_ste,res.cust_locn);
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

/*
 *Functions for adding New Country, New State & New Location in the New Lead Creation page -- Starts Here
 */
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
        return false;
		*/
		if ($('#newcountry').val() == "") {
			alert("Country Required.");
		}
		else {
			var regionId = $("#add1_region").val();
			var newCty = $('#newcountry').val();
            getCty(newCty, regionId);
		}
		
    function getCty(newCty, regionId){
        var baseurl = $('.hiddenUrl').val();
            $.ajax({
            url : baseurl + 'customers/getCtyRes/' + newCty + "/" + regionId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{ 
						$.post("regionsettings/country_add_ajax",{regionid: $("#add1_region").val(),country_name:$("#newcountry").val(),created_by:(<?php echo $usernme['userid']; ?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
						function(info){$("#country_row").html(info);});
						$("#addcountry").hide();
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
        return false;
		*/
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
						$.post("regionsettings/state_add_ajax",{countryid: $("#add1_country").val(),state_name:$("#newstate").val(),created_by:(<?php echo $usernme['userid']; ?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
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
	function getLoc(newLoc, stId) {
		var baseurl = $('.hiddenUrl').val();
            $.ajax({
            url : baseurl + 'customers/getLocRes/' + newLoc + '/' +stId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{
						$.post("regionsettings/location_add_ajax",{stateid: $("#add1_state").val(),location_name:$("#newlocation").val(),created_by:(<?php echo $usernme['userid']; ?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
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


/*
 *Functions for adding New Country, New State & New Location in the New Lead Creation page -- Ends Here.
 */
</script>