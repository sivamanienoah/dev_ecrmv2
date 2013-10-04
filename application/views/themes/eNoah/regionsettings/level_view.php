<?php require (theme_url().'/tpl/header.php'); ?>
<?php   $region_ids = implode(',', $region_id); ?>
<?php    $country_ids = implode(',', $country_id); ?>
<?php    $state_ids = implode(',', $state_id); ?>
<?php    $location_ids = implode(',', $location_id); ?>
 

<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>

<script type="text/javascript">
var nc_form_msg = '<div class="new-cust-form-loader">Loading Content.<br />';
nc_form_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';

$(document).ready(function() {
<?php
 if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) {?>
       var countryValues = '<?php  echo $region_ids.":".$country_ids; ?>';		 
	   var regionUrl = 'regionsettings/getCountryList/'+ countryValues;
	   $('#country_row').load(regionUrl);
	
 <?php }

?>
<?php
 if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) {?>
       var stateValues = '<?php  echo $country_ids.":".$state_ids; ?>';		
		 
    var countryUrl = 'regionsettings/getStateList/'+ stateValues;
	//alert(countryUrl);
	   $('#state_row').load(countryUrl);
	
 <?php }

?>
<?php
 if($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) {?>
       var locationValues = '<?php  echo $state_ids.":".$location_ids; ?>';		
		//alert(locationValues); 
    var locationUrl = 'regionsettings/getLocationList/'+ locationValues;
	//alert(countryUrl);
	   $('#location_row').load(locationUrl);
	   
<?php }

?>


 
  $('.checkUser').hide();
    $('.checkUser1').hide();
    $('#level_id').blur(function(){
        
        if( $('#level_id').val().length >= 3 )
            {
              var username = $('#level_id').val();
              getResult(username); 
            }
        return false;
    });
    function getResult(name){
        var baseurl = $('.hiddenUrl').val();
            $.ajax({
            url : baseurl + 'regionsettings/getResultfromdb/' + name,
            cache : false,
            success : function(response){
                $('.checkUser').hide();
                if(response == 'userOk') {$('.checkUser').show(); $('.checkUser1').hide();}
                else { $('.checkUser').hide(); $('.checkUser1').show();}
            }
        });
	}	
	$('#region_country').change(function() { 
       var countryValues = $('#region_country').val();
	   //alert(countryValues);
	   var regionUrl = 'regionsettings/getCountryList/'+ countryValues;
	   $('#country_row').load(regionUrl);
    });
});
function getStateLists() {
	var stateValues = $('#country_state').val();
    var countryUrl = 'regionsettings/getStateList/'+ stateValues;
	//alert(countryUrl);
	   $('#state_row').load(countryUrl);
	}
function getLocationLists() {
	var locationValues = $('#state_location').val();
	//alert(locationValues);
    var locationUrl = 'regionsettings/getLocationList/'+ locationValues;
	//alert(locationUrl);
	   $('#location_row').load(locationUrl);
	}
function ndf_cancel() {
    $.unblockUI();
    return false;
}
$(function() {
	$('.modal-new-cust').click(function(){
		var url = $(this).attr('href');
		$.blockUI({
					message:nc_form_msg,
					css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default'},
					overlayCSS: {backgroundColor:'#000000', opacity: '0.9', cursor: 'wait'}
				});
		$.get(
			url,
			{},
			function(data){
				$('.new-cust-form-loader').slideUp(500, function(){
					$(this).parent().css({backgroundColor: '#535353', color: '#cccccc'});
					$(this).css('text-align', 'left').html(data).slideDown(500, function(){
						$('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
					});
				})
			}
		);
		return false;
	});
});
$(function() {

	$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager"),positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
function cancel() {
window.history.back();
}

</script>
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('accesspage')==1) || ($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || (($this->session->userdata('edit')==1) && ($this->uri->segment(3) == 'update') && is_numeric($this->uri->segment(4)))) { ?>
		<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();" >
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?>  Levels </h2>
			<?php if ($this->validation->error_string != '') { ?>
					<div class="form_error">
						<?php echo  $this->validation->error_string ?>
					</div>
					<?php } ?>
			<p>All mandatory fields marked * must be filled in correctly.</p>
			
			<table>
				<tbody>
				<tr>
					<td width="100">Level Name:</td>
					<td width="240"><input id="level_id" type="text" name="level_name" value="<?php echo  $this->validation->level_name; ?>" class="textfield width200px required" /> *</td>
					<td class="checkUser" style="color:green" >Level Name Available.</td>
					<td class="checkUser1" style="color:red" >Level Name Already Exists.</td>
					<input type="hidden" class="hiddenUrl"/>
					
				</tr>
				<tr>
					<td>Status:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the region is inactive .</td>			
				</tr>
				</tbody>
			</table>
				<p></p>
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
					<thead>
							<tr>
								<th>Select Region</th>
								<th>Select Country</th>
								<th>Select State</th>
								<th>Select Location</th>
								
							</tr>
					</thead>
					<tbody>
						<tr>
							<td>
							 
							<select id="region_country" name="region[]" multiple="multiple">
							<?php if (is_array($regions) && count($regions) > 0) { ?>
							<?php foreach ($regions as $region) { ?>
							<option value="<?php echo $region['regionid']; ?>"<?php if(in_array($region['regionid'],$region_id)) { echo 'selected="selected"';  } ?>>
							<?php echo  $region['region_name'] ; ?></option><?php } } ?>
							</select>
							
							</td>
							<td id="country_row"><select name="country_state[]"   multiple="multiple"><option value="">Select</option></select></td>
							<td id="state_row"><select name="state_location[]" multiple="multiple"><option value="">Select</option></select></td>
							<td id="location_row"><select name="location[]" multiple="multiple"><option value="">Select</option></select></td>
						</tr>	
					</tbody>
			</table>	<p> </p>
					<div class="buttons">
						<button type="submit" name="update_level" class="positive">	
							<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Level
						</button>
						<button type="reset"  name="reset" class="negative" onclick='window.location.href="regionsettings/level"; return false;'>
                            Reset
                        </button>						
					</div>
					<?php if ($this->uri->segment(4)) { ?>
                    <td style="float:left;">
                        <div class="buttons">
                            <button  name="cancel_submit" class="negative" onclick="cancel(); return false;" >
                                Cancel
                            </button>
                        </div>
                    </td>
                    <?php } ?>
		</form>
		<h2>Levels List</h2>
        
        <form action="regionsettings/level_search/" method="post" id="cust_search_form">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td>
                        Search by Levels
                    </td>
                    <td>
                        <input type="text" id="search-val" name="cust_search" class="textfield width200px" />
                    </td>
                    <td>
                        <div class="buttons">
                            <button type="submit" class="positive">
                                
                                Search
                            </button>
                        </div>
                    </td>
                    <?php if ($this->uri->segment(4)) { ?>
                    <td>
                        <div class="buttons">
                            <button name="cancel_submit" class="negative" onclick="cancel(); return false;" >
                                Cancel
                            </button>
                        </div>
                    </td>
                    <?php } ?>
                </tr>
            </table>
	</form>        
		<table border="0" cellpadding="0" cellspacing="0" class="data-table">
			<thead>
				<tr>
					<th>Level Name</th>
					<th>Created Date</th>
					<th>Created By</th>										
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>            
		<tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if ($this->session->userdata('edit')==1){?><a href="regionsettings/level/update/<?php echo $customer['level_id']; ?>"> <?php echo $customer['level_name'] ; ?></a><?php } else echo $customer['level_name']; ?></td>
						<td><?php echo  date('d-m-Y', strtotime($customer['created'])); ?></td>
						<td><?php echo  $customer['cfnam'].$customer['clnam']; ?></td>   
                        <td><?php if($customer['inactive']==0){ echo "Active"; } else { echo "Inactive"; } ?></td>
                        <td class="actions">
							<?php if($this->session->userdata('accesspage')==1){ ?><a href="regionsettings/levels_view/<?php echo $customer['level_id']; ?>" class="modal-new-cust" onclick="return false;">View</a> <?php } else { echo "View"; } ?> |
							<?php if($this->session->userdata('edit')==1){ ?><a class="update" href="regionsettings/level/update/<?php echo $customer['level_id']; ?>">Edit</a> <?php } else { echo "Edit"; } ?>
							<?php if($this->session->userdata('delete')==1){ ?> | <a class="delete" href="regionsettings/level_delete/delete/<?php echo $customer['level_id']; ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a><?php } ?>                      
						</td>                         
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>            
        </table>
		<p><?php echo '&nbsp;'; ?></p>
		<div id="pager">
	<a class="first"> First </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <a class="prev"> &laquo; Prev </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <input type="text" size="2" class="pagedisplay"/><?php echo '&nbsp;&nbsp;&nbsp;'; ?> <!-- this can be any element, including an input --> 
    <a class="next"> Next &raquo; </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <a class="last"> Last </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <span>No. of Records per page:<?php echo '&nbsp;'; ?> </span><select class="pagesize"> 
        <option selected="selected" value="10">10</option> 
        <option value="20">20</option> 
        <option value="30">30</option> 
        <option value="40">40</option> 
    </select> 
		</div>
	<?php } else{
		echo "You have no rights to access this page";
	}?>
	</div>
</div>

<?php require (theme_url(). '/tpl/footer.php'); ?>
 