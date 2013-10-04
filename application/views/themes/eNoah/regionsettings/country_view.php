<div id="content">    
	<div class="inner">
		<div class="in-content"> 
		<?php if(($this->session->userdata('accesspage')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || ($this->session->userdata('edit')==1 && $this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)))) { ?>
			<form action="<?php echo  $this->uri->uri_string() ?>" id="country_form" method="post" onsubmit="return checkForm();">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Country Details</h2>
				    <?php if ($this->validation->error_string != '') { ?>
				    <div class="form_error">
					<?php echo  $this->validation->error_string ?>
				    </div>
				    <?php } ?>
				<p>All mandatory fields marked * must be filled in correctly.</p>
				<table class="layout">
					<tr>
						<td width="100">Region:</td><?php $regid = $this->validation->regionid ?>
						<td width="240"><select id="region_id" name="regionid" class="textfield width200px" ><option value="">Select Region</option><?php if (is_array($regions) && count($regions) > 0) { ?>
							<?php foreach ($regions as $region) { ?><option value="<?php echo $region['regionid']; ?>"<?php if($regid==$region['regionid']) { echo "selected"; } ?>><?php echo  $region['region_name'] ; ?></option><?php } } ?></select> *</td>
						<td class="error" style="color:red;" id="error1">Select Region</td>
					</tr>				
					<tr>	
						<td width="100">Country:</td>
						<td width="240"><input id="country_name" type="text" name="country_name" value="<?php echo  $this->validation->country_name ?>" class="textfield width200px required" /> *</td>
						<td class="error" style="color:red;" id="error2">Country Field required.</td>
					</tr>
					<tr>
						<td>Status:</td>
						<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the country is inactive .</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
						<td colspan="3">Active country cannot be modified! Please use my account to update your details.</td>
						<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
						<td colspan="3">Your country level cannot updated similar levels or levels above you.</td>
						<?php } else { ?>
						<td style="float:left;">
						<div class="buttons">
							<button type="submit" name="update_country" class="positive">
							<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> country
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
						<td colspan="2">
						<?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && 1 == 1) { # 1 == 2 do not delete users ?>
						<!--div class="buttons">
						    <button type="submit" name="delete_country" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
							Delete country
						    </button>
						</div-->
						<?php } else { echo "&nbsp;"; } ?>
						</td>
						<?php } ?>
					</tr>
				</table>
			</form>
	<h2>Country List</h2>
        
        <form action="regionsettings/region_settings/" method="post" id="cust_search_form">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td>
                        Search by Country
                    </td>
                    <td>
                        <input type="text" id="search-vals" name="cust_search" class="textfield width200px" />
                    </td>
                    <td>
                        <div class="buttons">
                            <button type="submit" class="search">Search</button>
                        </div>
                    </td>
                    <?php if ($this->uri->segment(4)) { ?>
                    <td>
                        <div class="buttons">
                            <button type="submit" name="cancel_submit" class="negative">Cancel</button>
                        </div>
                    </td>
                    <?php } ?>
                </tr>
            </table>
	</form>        
        <table id="cntryData-table" class="data-table" border="0" cellpadding="0" cellspacing="0" >            
		<thead>
			<tr>
			    <th>Country Name</th>
				<th>Region Name</th>
				<th>Created Date</th>
			    <th>Created By</th>
			    <!--<th>Modified By</th>			    
			    <th>Modified</th>-->
			    <th>Status</th>
			    <th>Actions</th>
			</tr>
		</thead>            
		<tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                <?php foreach ($customers as $customer) { ?>
                <tr>
                        <td><?php if ($this->session->userdata('edit')==1) {?><a class="edit" href="regionsettings/country/update/<?php echo  $customer['countryid'] ?>"><?php echo  $customer['country_name'] ; ?></a><?php } else { echo $customer['country_name']; } ?></td>
                        <td><?php echo $customer['region_name']; ?></td>
						<td><?php echo  date('d-m-Y', strtotime($customer['created'])); ?></td>
						<td><?php echo  $customer['cfnam'].$customer['clnam']; ?></td>   
						<!--<td><?php echo  $customer['mfnam']. $customer['mlnam']; ?></td>                        
                        <td><?php echo  $customer['modified'];?></td>-->
                        <td>
				<?php 
				if($customer['inactive']==0){
				echo "Active";
				} else { echo "Inactive"; }				
				?>
			</td>  
			<td class="actions">
				<?php if ($this->session->userdata('edit')==1) { ?><a class="edit" href="regionsettings/country/update/<?php echo $customer['countryid']; ?>"><?php echo  "Edit"; ?></a> <?php } else echo "Edit"; ?>
				<?php if ($this->session->userdata('delete')==1) { ?> | <a class="delete" href="regionsettings/country_delete/delete/<?php echo $customer['countryid']; ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a> <?php } ?>
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
		<div id="pager2">
	<a class="first"> First </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <a class="prev"> &laquo; Prev </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <input type="text" size="2" class="pagedisplay"/><?php echo '&nbsp;&nbsp;&nbsp;'; ?> <!-- this can be any element, including an input --> 
    <a class="next"> Next &raquo; </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <a class="last"> Last </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
    <span> No. of Records per page: <?php echo '&nbsp;'; ?> </span><select class="pagesize"> 
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
<!--<script type="text/javascript" src="assets/js/tablesort.min.js"></script>-->
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<script type="text/javascript">
$('.first').addClass('2');
$('.prev').addClass('2');
$('.pagedisplay').addClass('2');
$('.next').addClass('2');
$('.last').addClass('2');
$('.pagesize').addClass('2');

$(document).ready(function() {
 $('.error').hide();
   $('a.edit').click(function() {
    var url = $(this).attr('href');
    $('.in-content').load(url);
    return false;
  });
  $('button.negative').click(function() {
	window.location.href="regionsettings/region_settings/country"
	return false;
	});
  $('button.positive').click(function() {
    $('.error').hide();
	var region  = $('#region_id').val() ;
			if(region == ""){
				$('.error').show();
				return false;
				}
	var country  = $('#country_name').val() ;
			if(country == ""){
				$('td#error2.error').show();
				return false;
				}			
    });
	
	$('button.negative').click(function() {
	window.location.href="<?php echo  base_url(); ?>regionsettings/region_settings/country"
	return false;
	});
	
	$('button.search').click(function() {
		var search = $('#search-vals').val();
		//alert(search);
		var stencode=encodeURIComponent(search);
		var linkUrl = "regionsettings/country_search/0/"+stencode;
		//alert(linkUrl);
		//$('.in-content').load(linkUrl);
		$('#ui-tabs-5').load(linkUrl,function() {
     $('#country_form').attr("action","./regionsettings/country");
});
		return false;
	});
	
$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager2"),positionFixed: false});
});

$(function() {

 
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>