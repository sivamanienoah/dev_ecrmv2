 <script type="text/javascript">
$('.first').addClass('3');
$('.prev').addClass('3');
$('.pagedisplay').addClass('3');
$('.next').addClass('3');
$('.last').addClass('3');
$('.pagesize').addClass('3');
$(document).ready(function() {
 $('button.stsearch').click(function() {    
    var st = $('#statesearch').val();
	var stencode=encodeURIComponent(st);
    var sturl = "regionsettings/state_search/0/"+ stencode;	
    //$('.in-content').load(sturl);
	$('#ui-tabs-7').load(sturl,function() {
     $('#state_form').attr("action","./regionsettings/state");
});
    return false;
  });
 $('.error').hide();
   $('a.edit').click(function() {
    var url = $(this).attr('href');
    $('.in-content').load(url);
    return false;
  });
  $('button.negative').click(function() {
	window.location.href="<?php echo  base_url(); ?>regionsettings/region_settings/state"
	return false;
	});
  $('.positive').click(function() {
    $('.error').hide();
	var region  = $('#country_id').val() ;
			if(region == ""){
				$('.error').show();
				return false;
				}
	var country  = $('#state_id').val() ;
			if(country == ""){
				$('td#error2.error').show();
				return false;
				}			
    });
	$(".data-table").tablesorter({widthFixed: false, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager3"),positionFixed: false});  
});
$(function() {
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});

var id='';
function getCountryst(val,id) {
//alert(val);
	var sturl = "regionsettings/getCountryst/"+ val+"/"+id;	
    $('#country_row').load(sturl);	
	//$('#test').load(sturl);
	//alert("hi");
    return false;	
}
</script>
<?php
if($this->validation->regionid != 0) 
echo '<input type="hidden" name="region_update" id="region_update" value="'.$this->validation->regionid.'" />';
?>
<div id="content">	
    <div class="inner">
	<div class="in-content">
	<?php if(($this->session->userdata('accesspage')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
		<form action="<?php echo  $this->uri->uri_string() ?>" id="state_form" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> State Details</h2>
			<?php if ($this->validation->error_string != '') { ?>
			<div class="form_error">
				<?php echo  $this->validation->error_string ?>
			</div>
			<?php } ?>
			<p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">Region:</td>
					<td width="240">
					<select name="regionid" class="textfield width200px" onchange="getCountryst(this.value)" class="textfield width200px required">
						<option value="0">Select Region</option>
                            <?php 
							foreach ($regions as $region) { ?>
								<option value="<?php echo  $region['regionid'] ?>"<?php echo  ($this->validation->regionid == $region['regionid']) ? ' selected="selected"' : '' ?>><?php echo  $region['region_name']; ?></option>
							<?php } ?>
					</select> *
					</td>
					<td class="error" id="error1" style="color:red;">Select Region</td>
				</tr>
				<div id="test"></div>
				<tr>
					<td width="100">Country:</td>
					<?php $cid = $this->validation->countryid ?>
					<td id='country_row' width="240">
						<select id="country_id" name="countryid" style="width:210px;">
							<option value="">Select Country</option>
							<?php if (is_array($countrys) && count($countrys) > 0) { ?>
							<?php foreach ($countrys as $country) { ?>
							<option value="<?php echo $country['countryid'];?>" <?php if($cid==$country['countryid']) { echo "selected"; } ?>><?php echo $country['country_name']; ?> </option>
							<?php } } ?>
						</select> *
					</td>
					<td class="error" id="error1" style="color:red;">Select Country</td>
				</tr>
				<tr>
					<td width="100">State:</td>
					<td width="240"><input id="state_id" type="text" name="state_name" value="<?php echo $this->validation->state_name ?>" class="textfield width200px required" /> *</td>
					<td class="error" id="error2" style="color:red;">State Field Required.</td>
				</tr>
				<tr>
					<td>Status:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the state is inactive .</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<td colspan="3">
						Active state cannot be modified! Please use my account to update your details.
					</td>
					<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
					<td colspan="3">
						Your state level cannot updated similar levels or levels above you.
					</td>
					<?php } else { ?>
					<td style="float:left;">
						<div class="buttons">
							<button type="submit" name="update_state" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> state
							</button>
						</div>
					</td>
					<?php if ($this->uri->segment(4)) { ?>
			    <td style="float:left;">
				<div class="buttons">
				    <button type="submit" name="cancel_submit" class="negative">
					Cancel
				    </button>
				</div>
			    </td>
			    <?php } ?>
					<?php } ?>
				</tr>
			</table>
		</form>
    <h2>State List</h2>
        
        <form action="regionsettings/region_settings/" method="post" id="cust_search_form">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<table border="0" cellpadding="0" cellspacing="0" class="search-table">
			<tr>
			    <td>
				Search by State
			    </td>
			    <td>
				<input type="text" id="statesearch" name="cust_search" class="textfield width200px"/> <!--value="<?php echo  $this->uri->segment(4) ?>"-->  
			    </td>
			    <td>
				<div class="buttons">
				    <button type="submit" class="stsearch">
					Search
				    </button>
				</div>
			    </td>
			    <?php if ($this->uri->segment(4)) { ?>
			    <td>
				<div class="buttons">
				    <button type="submit" name="cancel_submit" class="negative">
					Cancel
				    </button>
				</div>
			    </td>
			    <?php } ?>
			</tr>
		</table>
	</form>        
        <table id="steData-table" class="data-table" border="0" cellpadding="0" cellspacing="0" >            
            <thead>
                <tr>
					<th>State Name</th>
					<th>Country Name</th>
					<th>Region Name</th>
					<th>Created Date</th>
					<th>Created By</th>
					<!--th>Modified By</th-->
					<!--th>Modified</th-->
					<th>Status</th>
					<th>Action</th>
					
                </tr>
            </thead>            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if ($this->session->userdata('edit')==1) {?><a class="edit" href="regionsettings/state/update/<?php echo  $customer['stateid'] ?>"><?php echo  $customer['state_name'] ; ?></a><?php } else { echo $customer['state_name']; } ?></td>
						<td><?php echo $customer['country_name']; ?></td>
						<td><?php echo $customer['region_name']; ?></td>
						<td><?php echo date('d-m-Y', strtotime($customer['created'])); ?></td>
						<td><?php echo  $customer['cfnam'].$customer['clnam']; ?></td>   
						<!--td><?php echo  $customer['mfnam']. $customer['mlnam']; ?></td-->
                        <!--td><?php echo  $customer['modified'] ;?></td-->
                        <td>
				<?php 
				if($customer['inactive']==0){
				echo "Active";
				} else { echo "Inactive"; }				
				?>
			</td>                         
					<td class="actions">
						<?php if ($this->session->userdata('edit')==1) {?><a class="edit" href="regionsettings/state/update/<?php echo $customer['stateid']; ?>"><?php echo  "Edit"; ?></a> <?php } else echo "Edit"; ?>                    
						<?php if ($this->session->userdata('delete')==1) {?> | <a class="delete" href="regionsettings/state_delete/delete/<?php echo $customer['stateid']; ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo  "Delete"; ?></a><?php } ?>
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
		<div id="pager3">
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
	<?php } else {
				echo "You have no rights to access this page";
			} 
	?>
    </div>
</div>
</div>