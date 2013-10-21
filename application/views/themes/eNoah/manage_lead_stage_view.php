<?php
ob_start();
require (theme_url(). '/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script>
$(document).ready(function() {
	$('#lead_stg_items').sortable({axis:'y', cursor:'move', update:prepareSortedLeadStg});
	populateLeadStage();
});

//populateLeadStage();

function populateLeadStage(nosort) {
		$('.ls-container').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif />',
			css: { background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333' }
        });
        $.get('manage_lead_stage/ajax_leadstg_list/',{},function(res) {
                if (typeof (res) == 'object') {
                    if (res.error == false) {
                        $('#lead_stg_items').empty().append(res.html);
                        if (nosort != true) $('#lead_stg_items').sortable('refresh');
                    } else {
                        alert(res.errormsg);
                    }
                } else {
                    alert('Error receiving data!');
                }
                $('.ls-container').unblock();
            },
            'json'
        )
}

function prepareSortedLeadStg() {
    item_sort_order = $('#lead_stg_items').sortable('serialize')+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
	$('.ls-container').block({message:'<h5>Processing</h5>'});
    $.post(
        'manage_lead_stage/ajax_save_lead_sequence',item_sort_order,
        function(data) {
			$('.ls-container').unblock();
            if (data.error) {
                alert(data.errormsg);
            } else {
                //$('.q-save-order').slideUp();
				populateLeadStage();
            }
        },
        'json'
    );
}


function checkStatus(id) {
	var formdata = { 'data':id }
	$.ajax({
		url: '<?php echo base_url(); ?>manage_lead_stage/ajax_check_status_lead_stage/',
		data: { 'data': id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		type: "POST",
		dataType:"json",                                                                
		cache: false,
		beforeSend:function(){
			$('#dialog-message-'+id).empty();
		},
		success: function(response) {
			if (response.html == 'NO') {
				$('#errmsg-'+id).show();
				$('#errmsg-'+id).append("One or more leads currently assigned for this Lead Stage. This cannot be deleted.");
				setTimeout('timerfadeout()', 4000);
			} else {
				var r=confirm("Are You Sure Want to Delete?")
				if (r==true) {
				  window.location.href = 'manage_lead_stage/leadStg_delete/update/'+id;
				} else {
					return false;
				}
			}
		}                                                                                       
	});
	return false;
}
</script>

<div id="content">
	<div class="inner">
		<h2 class="clearfix"><span class="pull-left"><?php echo $page_heading; ?></span>
			<?php if($this->session->userdata('add')==1) { ?>
			<div class="pull-right">
				<button type="button" class="positive btn-leadStgAdd" onclick="location.href='<?php echo base_url(); ?>manage_lead_stage/leadStg_add'">Add New Lead Stage</button>
			</div>
	<?php } ?>
	</h2>

	<?php if($this->session->userdata('accesspage')==1) { ?>
	<div class="leadstg_note">
		To change the order of the lead stage, select and drag the lead stage and drop to the position in which you want the lead stage to appear.
	</div>
	<table cellpadding="0" cellspacing="0" class="lead-stg-list" width="100%">
		<tr>
			<th width="40%">Lead Stage</th>
			<th width="60px">Status</th>
			<th width="55px">Action</th>
			<th></th>
		</tr>
	</table>
	<div class="ls-container" class="clearfix" style="position: relative;">
		<ul id="lead_stg_items"></ul>
	</div>
	<?php } else { echo "You have no rights to access this page"; } ?>
	</div><!--Inner div - close here -->
</div><!--Content div - close here -->

<script>
	function timerfadeout() {
		$('.dialog-err').empty();
	}
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>