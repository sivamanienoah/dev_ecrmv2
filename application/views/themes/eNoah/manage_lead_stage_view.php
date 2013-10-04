<?php
ob_start();
require (APPPATH . 'views/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo baseurl();
?>

<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>

<script>
$(document).ready(function() {
	$('#lead_stg_items').sortable({axis:'y', cursor:'move', update:prepareSortedLeadStg});

	<?php if($this->session->userdata('edit')==1) { ?>
		$('#lead_stg_items li').livequery(function(){ 
			// use the helper function hover to bind a mouseover and mouseout event 
				$(this) 
					.hover(function() { 
						// leadStgOverEdit($(this)); 
					}, function() { 
						// leadStgOutEdit($(this)); 
					}); 
		}, function() { 
				// unbind the mouseover and mouseout events 
				$(this) 
					.unbind('mouseover') 
					.unbind('mouseout'); 
		});
	<?php } ?>
	
	<?php if($this->session->userdata('delete')==1) { ?>
		$('#lead_stg_items li').livequery(function(){ 
			// use the helper function hover to bind a mouseover and mouseout event 
				$(this) 
					.hover(function() { 
						// leadStgOverDel($(this)); 
					}, function() { 
						// leadStgOutDel($(this)); 
					}); 
		}, function() { 
				// unbind the mouseover and mouseout events 
				$(this) 
					.unbind('mouseover') 
					.unbind('mouseout'); 
		});
	<?php } ?>
		
	$('#lead_stg_items li .ip-edit').livequery(function(){
		$(this).click(function(){
			leadStgEdit($(this));
		});
	});
		
	$('#lead_stg_items li .ip-delete').livequery(function(){
		$(this).click(function(){
			leadStgDelete($(this));
		});
	});
});


populateLeadStage();

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

//for Edit
function leadStgOverEdit (obj) {
    obj.append('<a class="ip-edit">edit</a>');
}
function leadStgOutEdit (obj) {
    $('.ip-edit', obj).remove();
}

//for Delete
function leadStgOverDel (obj) {
    obj.append('<a class="ip-delete">delete</a>');
}
function leadStgOutDel (obj) {
    $('.ip-delete', obj).remove();
}

var msg = '<div class="q-modal-leadstg-edit">Loading Content.<br />';
msg += '<img src="assets/img/indicator.gif" alt="wait" /></div>';

function leadStgEdit(obj) {
    // var lead_stg_id = obj.parent().attr('id').replace(/^leadst\-/, '');
    var lead_stg_id = obj;
    $.blockUI({
        message:msg,
        css: {width: '550px', marginLeft: '50%', left: '-250px', padding: '20px 0 20px 20px', top: '25%', border: 'none', cursor: 'default'},
        overlayCSS: {backgroundColor:'#000000', opacity: '0.9', cursor: 'wait'}
    });
    $.get(
        'ajax/data_forms/lead_stg_form/'+lead_stg_id,
        {},
        function(data){
            $('.q-modal-leadstg-edit').slideUp(500, function(){
                $(this).parent().css({backgroundColor: '#535353', color: '#cccccc'});
                $(this).css('text-align', 'left').html(data).slideDown(500, function() {
                    $('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
                });
            })
        }
    );
    return false;
}

function processLeadStgEdit() {
    $('.q-modal-leadstg-edit').parent().block({message:'<p>Processing...</p>'});
	var form_data = $('#lead_stage_edit_form').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
    $.post(
        'manage_lead_stage/ajax_edit_leadstg',
        form_data,
        function(data){
            if (typeof(data) == 'object') {
                if (!data.error) {
                    $('.q-modal-leadstg-edit').unblock();
                    populateLeadStage();
                    cancelDelEdit();
                } else {
                    $('.q-modal-leadstg-edit').unblock();
                    alert(data.errormsg);
                    $('.q-modal-leadstg-edit').parent().unblock();
                }
            } else {
                alert('Database error!');
                cancelDelEdit();
            }
            
        },
        'json'
    );
}

function leadStgDelete(obj) {
    // var lead_stg_id = obj.parent().attr('id').replace(/^leadst\-/, '');
    var lead_stg_id = obj;
    $.blockUI({
        message:'<br /><h5>Are you sure?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processLeadStgDelete('+lead_stg_id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDelEdit(); return false;">No</button></div></div>',
		css:{width:'440px'}
    });
}

function processLeadStgDelete(id) {
    var params = {'lead_stage_id':id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'};
    $.post(
        'manage_lead_stage/ajax_delete_leadStg',
        params,
        function(res){
            if (typeof (res) == 'object') {
                if (res.error) {
                    // alert(res.errormsg);
					$('#errmsg-'+id).show();
					$('#errmsg-'+id).html(res.errormsg);
					setTimeout('timerfadeout()', 4000);
                } else {
                    populateLeadStage();
                }
            } else {
                alert('Your session timedout!');
            }
            cancelDelEdit();
        },
        'json'
    );
}

function checkStatus(id) {
	var formdata = {'data':id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'};
	$.ajax({
		type: "POST",
		url: '<?php echo base_url(); ?>manage_lead_stage/ajax_check_status_lead_stage/',
		dataType:"json",                                                                
		data: formdata,
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


function cancelDelEdit() {
    $.unblockUI();
}
</script>

<div id="content">
	<div class="inner">

	<h2 class="clearfix"><span class="pull-left"><?php echo $page_heading; ?></span>
	<?php if($this->session->userdata('add')==1) { ?>
			<div class="pull-right">
				<button type="button" class="positive btn-leadStgAdd" onclick="location.href='<?php echo base_url(); ?>manage_lead_stage/leadStg_add'">
					Add New Lead Stage
				</button>
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
require (APPPATH . 'views/tpl/footer.php');
ob_end_flush();
?>