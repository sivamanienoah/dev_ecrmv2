<?php require (theme_url().'/tpl/header.php'); ?>

<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<!--script type="text/javascript" src="assets/js/easypaginate.js"></script>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<script type="text/javascript">var this_is_home = true;</script>
<!--Code Added for the Pagination in Comments Section-Starts Here-->
<script type="text/javascript">

/*
function validateRequestForm()
{
	var x=document.forms["search_req"]["keyword"].value;
	//alert(x); return false;
	if (x=='Lead No, Job Title, Name or Company') {
		alert("Please provide any values");
		return false;
	}
}
*/
</script>
<div id="content">
    <div class="inner">
					<form method="post" action="myaccount">
			
				<input type="hidden" value="0e821d87866043e648af4d4ba94676ad" name="ci_csrf_token" id="token">
			
				<h2>Enquiry</h2>
				<table class="layout">
					<tbody><tr>
						<td width="100">Enquiry Posted By: </td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_name'] ?></td>
						<td width="100">Email Address:</td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_email'] ?></td>
					</tr>
					<tr>
						<td width="100">Phone Number:</td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_phone'] ?></td>
						<td width="100">Message:</td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_title'] ?></td>
					</tr>
					<tr>
						<td width="100">Date Created:</td>
						<td width="240"><?php ?></td>
						<td width="100">Source:</td>
						<td width="240">Website</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
						<td colspan="4">
										
								<div class="buttons">
								   <button onclick="location.href='<?php echo base_url().'enquiries/enquirieslist' ?>'" class="negative" type="button">Back</button>
								</div>
													</td>
					</tr>
				</tbody></table>
			</form>
			</div>
</div>


<?php require (theme_url().'/tpl/footer.php'); ?>