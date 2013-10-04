<?php require ('inc/header.php'); ?>
<script type="text/javascript">
/*$(document).ready(function(){
	$("input:not(#emailval)").focus(function(){
		if($.trim($("#emailval").val()) == '' || $("#emailval").val().indexOf('@') < 0){
			alert('Please fill your correct email address first!');
			$('#emailval').focus();
		}else{
			
		}
	});
});*/

var dataLoaded;
var st;

function checkExisting(email){

	if($.trim($("#emailval").val()) == '' || $("#emailval").val().indexOf('@') < 0){
			alert('Please fill your correct email address first!');
			$('#emailval').focus();
			return false;
	}
	
	$.post('ajax/checkexisting/', {'address' : email,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, function(data){
		if(data != 'NO'){
			eval(data);
			if(typeof (dataLoaded) == 'object'){
				loadExisting();
				$('#emailaddress').html(email);
				//$('#checkExisting').slideDown('fast');
			}
		}else{
			$('#secondstep').slideDown('fast');
			$('#emailaddress').html(email);
			//$('#emailval').css("display: none");
		}
	});
	$('#firststep').slideUp('fast');
}

function loadExisting(){
	if(typeof (dataLoaded) == 'object'){
		$.each(dataLoaded, function(i,n){
			if(i != 'state'){
				$("input[@name="+i+"]").val(n);
			}else{
				switch (n) {
					case 'NSW':
						st = 0;
						break;
					case 'QLD':
						st = 1;
						break;
					case 'SA':
						st = 2;
						break;
					case 'VIC':
						st = 3;
						break;
					case 'NT':
						st = 4;
						break;
					case 'ACT':
						st = 5;
						break;
					case 'TAS':
						st = 6;
						break;
					case 'WA':
						st = 7;
						break;
					default:
						st = 0;
				}
				document.getElementById('userState').selectedIndex = st;
			}
		});
		$('#checkExisting').slideUp('fast');
		$('#secondstep').slideDown('fast');
	}
}

function checkForm(){
	var error = false;
	$('.required').each(function(){
		if($.trim(this.value) == ''){
			error = true;
		}
	});
	if(error == true){
		alert('Please fill in all the required fields!');
		return false;
	}else{
		return true;
	}
}
	
</script>
<div id="content">
    <div class="inner">
		<?php 
			$attributes = array('onsubmit' => "return checkForm();");
			echo form_open('newjob', $attributes); 
		?>
		
	   <div style="float:left; width:400px;">
            <h2 style="padding-top:0;"><img src="vision/img/agents.jpg" alt="Attn: realestate agents" /></h2>
            <h3><a href="promo/" onclick="window.open(this.href); return false;">Click here to see our sample banners</a></h3>
                <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				
				<tr>
					<td colspan="2">
					<div id="firststep">
						<label style="width:115px; height:43px; display:block; float:left;">Email:</label>
					
					<input style="display:inline;" type="text" name="email1" id="emailval" value="<?php echo $this->input->post('email1')?>" class="textfield width200px required" />
					*<br />
					<input type="button" class="button" value="Continue" name="ChkExisting" onclick="checkExisting(this.form.email1.value);" />
					
					</div>
				  </td>
				</tr>
				
				<tr><td colspan="2">
					<div id="secondstep" style="display:none;">
						<table class="layout">
							<tr>
								<td colspan="2"><div id="emailaddress"></div></td>
							</tr>
							<tr>
								<td width="115">Secondary Email :</td>
								<td><input type="text" name="secondaryemail" value="<?php if(isset($_POST['secondaryemail'])){ echo htmlspecialchars ($_POST['secondaryemail']);} ?>" class="textfield width200px" /></td>
							</tr>
							<tr>
								<td>First name:</td>
								<td><input type="text" name="firstname" value="<?php if(isset($_POST['firstname'])){ echo htmlspecialchars ($_POST['firstname']);} ?>" class="textfield width200px required" />
									*</td>
							</tr>
							<tr>
								<td>Surname:</td>
								<td><input type="text" name="surname" value="<?php if(isset($_POST['surname'])){ echo htmlspecialchars ($_POST['surname']);} ?>" class="textfield width200px required" />
									*</td>
							</tr>
							<tr>
								<td>Company:</td>
								<td><input type="text" name="company" value="<?php if(isset($_POST['company'])){ echo htmlspecialchars ($_POST['company']);} ?>" class="textfield width200px required" />
								*</td>
							</tr>
							<tr>
								<td>Address:</td>
								<td><input type="text" name="address" value="<?php if(isset($_POST['address'])){ echo htmlspecialchars ($_POST['address']);} ?>" class="textfield width200px" /></td>
							</tr>
							<tr>
								<td>Suburb:</td>
								<td><input type="text" name="suburb" value="<?php if(isset($_POST['suburb'])){ echo htmlspecialchars ($_POST['suburb']);} ?>" class="textfield width200px" /></td>
							</tr>
							<tr>
								<td>State:</td>
								<td>
								<select name="state" class="textfield width200px" id="userState">
								<?php if(is_array($this->au_states->aus['state'])){ 
									foreach($this->au_states->aus['state'] as $k => $v){ ?>
										<option<?php if (isset($_POST['state']) && $_POST['state'] == $k) echo ' selected="selected"' ?> value="<?php echo $k?>"><?php echo $v?></option>
								<?php } } ?>
								</select>
									*</td>
							</tr>
							<tr>
								<td>Post code:</td>
								<td><input type="text" name="postcode" value="<?php if(isset($_POST['postcode'])){ echo htmlspecialchars ($_POST['postcode']);} ?>" class="textfield width200px" /></td>
							</tr>
							<tr>
								<td>Phone:</td>
								<td><input type="text" name="phone" value="<?php if(isset($_POST['phone'])){ echo htmlspecialchars ($_POST['phone']);} ?>" class="textfield width200px required" />
									*</td>
							</tr>
							<tr>
								<td>Mobile:</td>
								<td><input type="text" name="mobile" value="<?php if(isset($_POST['mobile'])){ echo htmlspecialchars ($_POST['mobile']);} ?>" class="textfield width200px" /></td>
							</tr>
													<tr>
								<td>&nbsp;</td>
								<td>
									<div style="margin-right:50px; float:right;"><a style="padding:1px 8px;" href="." class="button">Back</a></div>
									<input name="userid" type="hidden" id="userid" value="<?php if(isset($_POST['userid'])){ echo htmlspecialchars ($_POST['userid']);} ?>" />
									<input name="register" type="submit" class="button" id="register" value="Continue" />
								</td>
							</tr>
					 </table>
					</div>
				</td></tr>
			</table>
      </div>
	  <div id="servicesinfo">
		<p class="services">
		<span>What we'll do for you...</span>
			&raquo; Concept and storyboarding of your Flash&reg; banner<br />
			&raquo; Production of your Flash&reg; banner from creative<br />
			&raquo; Link your Flash&reg; banner to a website of your chioce<br />
			&raquo; Submission of your Flash&reg; banner to online publication
		</p>
		<p class="services">
			<span>What we need you to do...</span>
			&raquo; Place your order with us by completing all form fields<br />
			&raquo; Make payment of your Flash&reg; banner - Pay by Credit Card<br />
			&raquo; Upload all logos and photos you wish to include<br />
			&raquo; Approve your Flash&reg; banner upon completion
		</p>
		<p class="services">
			<span>Pay by Credit Card &raquo;</span>
			Pay for your banners by completing the <a href="download/pdf/flashbanners-creditcard-payment-form.pdf" onclick="window.open(this.href); return false;">payment form</a><br />
			and faxing it back to us. We accept:<br />
			<img src="img/creditcards.jpg" alt="accepted credit cards" /><br />
			<a href="download/pdf/flashbanners-creditcard-payment-form.pdf" onclick="window.open(this.href); return false;">Click Here to download the Payment Form</a>      </p>
		<p class="services">
			<span>Preferred Supplier &raquo;</span>
			Visiontech is the preferred supplier<br />
			of Flash&reg; banner ads for:<br />
			<img src="img/domain.jpg" alt="domain.com.au" />
		</p>
	</div>
	<p class="clear-both">&nbsp;</p>
	</form>
	</div>
</div>
<?php require ('inc/footer.php'); ?>