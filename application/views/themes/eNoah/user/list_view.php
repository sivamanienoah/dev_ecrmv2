<?php require (theme_url().'/tpl/header.php'); ?>
<?php $availed_users = check_max_users(); ?>
<?php  #echo $max_allow_user ." ". $availed_users['avail_users']; ?>
<div id="content">
    <div class="inner">
    <?php if($this->session->userdata('accesspage')==1) { ?>       
        <h2>User Database</h2>
        <p class="pagination"><?php //echo  $pagination ?></p>
        <form action="user/search/" method="post" id="cust_search_form">
			
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td>
                        Search by Name
                    </td>
                    <td>
                        <input type="text" name="cust_search" value="<?php echo  $this->uri->segment(4) ?>" class="textfield width200px" />
                    </td>
                    <td>
                        <div class="buttons">
                            <button type="submit" class="positive">
                                
                                Search
                            </button>
                        </div>
                    </td>
					<?php if($this->session->userdata('add')==1) { ?>
					<td valign="middle";>
						<div class="buttons">
							<button type="button" <?php if($max_allow_user <= $availed_users['avail_users']) { ?> class="negative_disable" onclick="" <?php } else { ?> class="positive" onclick="location.href='user/add_user'" <?php } ?> >
								Add New User
							</button>
						</div>
					</td>
					<?php } ?>
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
        
		<div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
        <table border="0" cellpadding="0" cellspacing="0" class="data-table">
            
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Roles</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if($this->session->userdata('edit')==1){ ?><a href="user/add_user/update/<?php echo  $customer['userid'] ?>"><?php echo  $customer['first_name'] . ' ' . $customer['last_name'] ?></a><?php } else { echo $customer['first_name'] . ' ' . $customer['last_name']; } ?></td>
                        <td>
						<?php echo $customer['email'] ?>
						<?php
						if ($userdata['role_id'] == 1)
						{
							echo '<a href="user/log_history/' . $customer['userid'] . '">View Logs</a>';
						}
						?>
						</td>
                        <td><?php echo  $customer['phone'] ?></td>
                        <td><?php echo  $customer['name'] ?></td>
                        <!--<td><?php //echo $customer['level'] ?></td>-->
                        <td><?php echo  $customer['level_name'] ?></td>
                        <td><?php if ($customer['inactive'] == 0) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?></td>
						<td>
							<?php if($this->session->userdata('edit')==1) { ?>
								<a href="user/add_user/update/<?php echo $customer['userid'] ?>"><?php echo "Edit"; ?></a><?php } else { echo "Edit"; } ?>  
							<?php if($this->session->userdata('delete')==1) { ?> | <a href="javascript:void(0)" onclick="return checkStatus(<?php echo $customer['userid'] ?>);" ><?php echo "Delete"; ?></a><?php } ?>
						</td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" align="center">No records available to be displayed!</td>
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
<script type="text/javascript">
$(function(){
    $(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']})
	.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});

function checkStatus(id) {
	var formdata = { 'data':id, '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }
	$.ajax({
		type: "POST",
		url: '<?php echo base_url(); ?>user/ajax_check_status_user/',
		dataType:"json",                                                                
		data: formdata,
		cache: false,
		beforeSend:function(){
			//$("#loadingImage").show();
			$('#dialog-err-msg').empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				//alert("You can't Delete the Lead source!. \n This Source is used in Leads.");
				$('#dialog-err-msg').show();
				$('#dialog-err-msg').append('One of more leads currently mapped to this user. This cannot be deleted.');
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'user/delete_user/'+id;
				} else {
					return false;
				}
			}
		}          
	});
return false;
}

function timerfadeout() {
	$('.dialog-err').fadeOut();
}
</script>
<?php require (theme_url(). '/tpl/footer.php'); ?>