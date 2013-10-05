<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo baseurl();
?>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<div id="content">
	<div class="inner">
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
		<div id="querylead_form" style="border:0px solid;" >
		<div class="leadstg_note">Logo Dimensions should be Maximum 300px x 50px (width x height).</div>
			<form id="client_logo_upload" name="client_logo_upload" method="post" onsubmit="return clientLogoAjaxFileUpload();">
				<table class="layout add_query" >
					<tr>
						<td width="120">Logo Url :</td>
						<td>
							<input type="text" class="textfield" value="http://" style="width:30px;" readonly /><input type="text" class="textfield width300px" id="client_url" name="client_url" />
						</td>
					</tr>
					<tr>
						<td width="120">Upload Logo :</td>
						<td><input type="file" class="textfield" id="logo_file" name="logo_file" /></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="query_sub" value="Submit" class="positive submitpositive" />
							<input class="logocancel" type="button" name="reset_logo" onclick="reset_logo_confirm()" value="Reset">
							
						</td>
					</tr>
				</table>
				<ul id="proces_img"></ul>
			</form>
		</div>
		<h3>Client Logo:</h3>
		<p></p>
		<div id="files">
			<?php 
				if (!empty($get_client_logo['filename'])) 
				echo "<img src='assets/img/client_logo/".$get_client_logo['filename']."' />";
			?>
		</div>

	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div - close here -->
</div><!--Content div - close here -->
<script>
function clientLogoAjaxFileUpload() {
	$('<li>Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#proces_img');
	var client_url = $('#client_url').val();
	var client_url=client_url.replace(/\//g, "-");
	$.ajaxFileUpload
	(
		{
			url:'client_logo/cliLogoUp/'+encodeURIComponent(client_url),
			secureuri:true,
			fileElementId:'logo_file',
			dataType: 'json',
			success: function (data, status)
			{
				if(typeof(data.error) != 'undefined')
				{
					if(data.error != '')
					{
						if (window.console)
						{
							console.log(data);
						}
						if (data.msg)
						{
							alert(data.msg);
							$('#proces_img').hide('slow').remove();
						}
						else
						{
							alert('File upload failed!');
							$('#proces_img').hide('slow').remove();
						}
						
					}
					else
					{
						if(typeof(data.file_name) != 'undefined')
						{
							if(data.file_name != 'undefined') {
								fname = '<img src=assets/img/client_logo/'+data.file_name+' alt="Smiley face" >';
							}
						} else {
							fname = 'File Not Attached';
						}
						$('#files').html(fname);
						$('#proces_img').hide('slow').remove();
					}
				}
			},
			error: function (data, status, e)
			{
				// alert(status);
				alert('Sorry, the upload failed due to an error!');
				$('#proces_img').hide('slow').remove();
				if (window.console)
				{
					console.log('ajax error\n' + e + '\n' + data + '\n' + status);
					for (i in e) {
					  console.log(e[i]);
					}
				}
			}
		}
	);
	$('#logo_file').val('');
	$('#client_url').val('');
	return false;
}

function reset_logo_confirm()
{
	var r=confirm("Are You Sure Want to Reset the Logo?")
	if (r==true) {
		del_client_logo();
	} else {
		return false;
	}
}

function del_client_logo()
{
	$.ajax({
		type: "POST",
		url: '<?php echo base_url(); ?>client_logo/del_client_logo/',
		dataType:"json",                                                                
		cache: false,
		beforeSend:function(){
			
		},
		success: function(response) {
			if (response == true) {
				$('#files').empty();
				$('#files').html('<span class="ajx_failure_msg">Logo Deleted.</span>');
			}
		}                                                                                       
	});
	return false;
}
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>