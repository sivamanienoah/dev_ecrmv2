/*
*@Request_view
*@
*/

function getFolderdata(ffolder_id) {

	// if(ffolder_id == 0) var ffolder_id  = 'Files' 
	// else var ffolder_id  = ffolder_id;

	showBreadCrumbs(ffolder_id);
	showFolderOptions(ffolder_id);
	$('#jv-tab-3').block({
		message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
		css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
	});
	$.get(
		site_base_url+'ajax/request/get_project_files/'+curr_job_id+'/'+ffolder_id,
		{},
		function(data) {
			$('#list_file').html(data);
			$('#filefolder_id').val(ffolder_id);			
			$('#parent_folder_id').val($('#filefolder_id').val());			
			$('#jv-tab-3').unblock();
			$('#list_file_tbl').dataTable({			
				"iDisplayLength": 10,
				"sPaginationType": "full_numbers",
				"bInfo": true,
				"bPaginate": true,
				"bProcessing": true,
				"bServerSide": false,
				"bLengthChange": true,
				"bSort": true,
				"bFilter": false,
				"bAutoWidth": false,
				"bDestroy": true,
				"aoColumnDefs": [
					{ 'bSortable': false, 'aTargets': [ 0 ] }
				]
			});
			$.unblockUI();
		}
	);
	return false;
}

/*
 *move files
 */
function move_files() {
	var form_data = $('#move-file').serialize();
	// alert(form_data); return false;
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/mapfiles',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			// console.info(data);
			if( data.result == true ) {
				$('#mf_successerrmsg').html(data.mf_msg);
				setTimeout(function() {
					$.unblockUI({ 
						onUnblock: function(){ getFolderdata(data.mf_reload),$('.succ_err_msg').empty(); }
					}); 
				}, 2000);
			} else {
				$('#mf_successerrmsg').html(data.mf_msg);
				setTimeout('timerfadeout()', 3000);
			}
		}
	});
	return false;
}

/*
 * Adding folders
 */
function create_folder(leadid, fparent_id) {
	
	var parent_folder_id = $('#parent_folder_id').val();
	var params				= {'leadid':leadid, 'fparent_id':fparent_id,'parent_folder_id': parent_folder_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_folder_tree_struct',
		dataType: 'json',
		data: params,
		success: function(data) {
			$('#aflead_id').val(data.lead_id);
			$('#afparent_id').val(data.fparent_id);
			$('#add_file_tree').html(data.tree_struture);
			var ht = $('#create-folder').height();
			ht += 50;
			$('.js_checkbox').prop('checked',false);
			$.blockUI({
				message: $('#create-folder'), 
				css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '450px',height: ht+'px'} 
			});
			$( "#create-folder" ).parent().addClass( "folder-scroll" );
		}
	});
	return false;
}

/*
 *Adding folder
 */
function add_folder() {
	if($("#new_folder").val() == "") {
		alert("Folder Name cannot be empty");
		return false;
	}
	var form_data = $('#create-folder').serialize();
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/addFolders',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			if(data.err == 'true'){
				alert('Folder Name already exists (Or) you dont have access to write.');
			}else {
				$('#af_successerrmsg').html(data.af_msg);
			}
			setTimeout(function() { 
				$.unblockUI({ 
					onUnblock: function(){ getFolderdata(data.af_reload),$('.succ_err_msg').empty(),$('#new_folder').val(''); }
				}); 
			}, 2000);
		}
	});
	return false;
}

function timerfadeout() {
	$('.succ_err_msg').empty();
}

/*method to show the permission for the users for the respective folder */
function show_permissions(leadid, fparent_id) {
	var params				= {'leadid':leadid, 'fparent_id':fparent_id};
	params[csrf_token_name] = csrf_hash_token; 
 
	$('.js_checkbox').prop('checked',false);
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_assigned_users',
		dataType: 'json',
		data: params,
		success: function(data) {
			//console.info(data);
			$('#cplead_id').val(data.lead_id);
			$('#folder_name').text(data.folder_name);
			$('#cpparent_id').val(data.fparent_id);
			
			var obj = data.result_set;
			var html = '';
			var recur_checked,add_checked,down_checked;
			
			if(obj.length){
				html += '<table class="dashboard-heads create_permissions" cellpadding="0" cellspacing="0">';
				html += '<tr><th>Users</th><th>Is Recursive?</th><th>Add Access</th><th>Download Access</th></tr>';
				for(var i=0; i<obj.length;i++){
					recur_checked = (obj[i].is_recursive != 0)?'checked="checked"':' ';
					add_checked = (obj[i].add_access != 0)?'checked="checked"':' ';
					down_checked = (obj[i].download_access != 0)?'checked="checked"':' ';
					
					html += '<tr><td><input type="hidden" name="pjt_users_id[]" value="'+obj[i].user_id+'" />'+obj[i].first_name+' '+obj[i].last_name+'</td><td><input class=js_checkbox" type="checkbox" name="is_recursive['+obj[i].user_id+']"   value="1" '+recur_checked+' /></td><td><input class="js_checkbox" type="checkbox" name="add_access['+obj[i].user_id+']" value="1" '+add_checked+' /></td><td><input class="js_checkbox" type="checkbox" name="download_access['+obj[i].user_id+']" value="1" '+down_checked+' /></td></tr>';
				}
				$('#add_users_tree_1').html(html);
			}else if(obj==''){
				$('.assign_permissions').html("<div>No Users Available.</div>")
				$('.assign_permissions').css('display','block');
			}else{
				$('.assign_permissions').css('display','block');
			}
			var ht = $('#check-permissions').height();
			ht += 50;
			
			$.blockUI({
				message: $('#check-permissions'), 
				css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '450px',height: ht+'px'} 
				
			});
			 $('.blockOverlay').attr('title','Click to unblock').click($.unblockUI); 
		}
	});
	return false;
}

/* Method used to assign the users to the already created folders */
function assign_folder(){
	var form_data = $('#check-permissions').serialize();
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/assignFolders',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			// console.info(data);			
			$('#af_successerrmsg-1').html(data.af_msg);
			setTimeout(function() { 
				$.unblockUI({ 
					message: $('#check-permissions'), 
					onUnblock: function(){ getFolderdata(data.af_reload),$('.succ_err_msg').empty() }
				}); 
			}, 2000);
		}
	});
	return false;	
}


/*
*@method searchFileFolder
*/
function searchFileFolder() {
	var search_input = $('#search_input').val();
	if(search_input == '')
	return false;
	
	// var parent_folder_id = $('#parent_folder_id').val();
	
	// var params				= {'search_input':search_input, 'lead_id':curr_job_id, 'currently_selected_folder':parent_folder_id};
	var params				= {'search_input':search_input, 'lead_id':curr_job_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/searchFile',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#list_file').html(data);
			$('#jv-tab-3').unblock();
			$('#list_file_tbl').dataTable({
				"iDisplayLength": 10,
				"sPaginationType": "full_numbers",
				"bInfo": true,
				"bPaginate": true,
				"bProcessing": true,
				"bServerSide": false,
				"bLengthChange": true,
				"bSort": true,
				"bFilter": false,
				"bAutoWidth": false,
				"bDestroy": true,
				"aoColumnDefs": [
					{ 'bSortable': false, 'aTargets': [ 0 ] }
				]
			});
			$.unblockUI();
		}
	});
	return false;
}



//Selecting Multiple files
$(document).on('click','#file_chkall',function(){
	if($(this).prop('checked')){
		$('.file_chk:not(:checked)').trigger('click');
	}else{
		$('.file_chk:checked').trigger('click');		
	}
});

/*
*Moving Multiple files
*/
function moveAllFiles() {
	var mv_folder = '';
	var mv_files = '';
	$( ".file_chk:checked" ).each(function( index ) {
		if($(this).attr('file-type') == 'folder') {
			mv_folder += $(this).val()+',';
		} else if($(this).attr('file-type') == 'file') {
			mv_files += $(this).val()+',';
		}
	});
	// alert(mv_folder+'+++'+mv_files); return false;
	
		/*
		*Mani Changes Start Here
		*/
	    /* var ffid = $('#filefolder_id').val();		
		if(ffid == 'Files') 
		{ 
		alert('Please open root folder and continue your actions!'); 
		return false;
		} */
		/*
		*Mani Changes End Here
		*/
	
	if((mv_folder=='') && (mv_files=='')) {
		alert('No files or folders selected');
		return false;
	}
	
	var params				= {'mv_folder':mv_folder, 'mv_files':mv_files, 'curr_job_id':curr_job_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_moveall_file_tree_struct',
		dataType: 'json',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#mall_lead_id').val(data.lead_id);
			$('#mov_folder').val(mv_folder);
			$('#mov_file').val(mv_files);
			$('#file_tree_all').html(data.tree_struture);
			$.blockUI({ 
				message: $('#moveallfile'),
				css: {
					border: '2px solid #999',
					color:'#333',
					padding:'8px',
					top:  ($(window).height() - 400) /2 + 'px', 
					left: ($(window).width() - 400) /2 + 'px', 
					width: '400px' 
				} 
			});
		}
	});
	return false;
	
}

/*
 *Move Multiple files
 */
function move_all_files() {
	var form_data = $('#moveallfile').serialize();
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/mapallfiles',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			// console.info(data);
			if( data.result == true ) {
				$('#all_mf_successerrmsg').html(data.mf_msg);
				setTimeout(function() {
					$.unblockUI({ 
						onUnblock: function(){ getFolderdata(data.mf_reload),$('.succ_err_msg').empty(); }
					}); 
				}, 2000);
			} else {
				$('#all_mf_successerrmsg').html(data.mf_msg);
				setTimeout('timerfadeout()', 3000);
			}
		}
	});
	return false;
}

/*
*Moving Multiple files
*/
function deleteAllFiles() {

	var ff_id = $('#filefolder_id').val();
	var del_folder = '';
	var del_files = '';
	$( ".file_chk:checked" ).each(function( index ) {
		if($(this).attr('file-type') == 'folder') {
			del_folder += $(this).val()+',';
		} else if($(this).attr('file-type') == 'file') {
			del_files += $(this).val()+',';
		}
	});
	// alert(del_folder+'+++'+del_files); return false;
	
	
	
	if((del_folder=='') && (del_files=='')) {
		alert('No files or folders selected');
		return false;
	}
	
		/*
		*Mani Changes Start Here
		*/
	   	/* if(ff_id == 'Files') 
		{ 
		alert('You have no permissions to delete root folder!'); 
		return false;
		} */
		/*
		*Mani Changes End Here
		*/
	
	var result = confirm("Are you sure you want to delete this files?");
	if (result==false) {
		return false;
	}
	
	var params				= {'del_folder':del_folder, 'del_files':del_files, 'curr_job_id':curr_job_id, 'ff_id':ff_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/delete_files',
		dataType: 'json',
		data: params,
		success: function(data) {
			// console.info(data);
			var delete_status = '';
			if(data.folder_del_status!='no_folder_del'){
				$.each(data.folder_del_status, function(i, item) {
					delete_status += item+"\n";
				});
			}
			if(data.file_del_status!='no_file_del'){
				$.each(data.file_del_status, function(i, item) {
					delete_status += item+"\n";
				});
			}
			alert(delete_status);
			getFolderdata(data.folder_parent_id);
		}
	});
	return false;
}


function showBreadCrumbs(parent_id) {
	$.get(
		site_base_url+'ajax/request/getBreadCrumbs/'+curr_job_id+'/'+parent_id,
		{},
		function(data) {
			$('#file_breadcrumb').html(data);
		}
	);
	return false;
}

function showFolderOptions(parent_id) {
	$('#files_actions').empty();
	$.get(
		site_base_url+'ajax/request/getFolderActions/'+curr_job_id+'/'+parent_id,
		{},
		function(data) {
			$('#files_actions').show();
			$('#files_actions').html(data);
		}
	);
	return false;
}

function download_files_id(job_id,file_id) {

	window.location.href = site_base_url+'project/download_file/'+job_id+'/'+$("#file_"+file_id).val();
}

/*
* folder access rights
*/
function folderAccess() {
	var fa_folder = '';
	var fa_files  = '';
	var parent_folder_id = '';
	
	$( ".file_chk:checked" ).each(function( index ) {
		if($(this).attr('file-type') == 'folder') {
			fa_folder += $(this).val()+',';
		} else if($(this).attr('file-type') == 'file') {
			fa_files += $(this).val()+',';
		}
	});
	
	//alert(fa_folder+'+++'+fa_files); return false;
	
	var parent_folder_id = $('#filefolder_id').val();
	
	//alert(parent_folder_id); return false;
	
	/*if((fa_folder=='') && (fa_files=='')) {
		alert('No files or folders selected');
		return false;
	}*/
	
	var params				= {'fa_folder':fa_folder, 'fa_files':fa_files, 'curr_job_id':curr_job_id, 'parent_folder_id':parent_folder_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/getProjectMembers',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#fa_lead_id').val(curr_job_id);
			$('#fa_folder').val(fa_folder);
			$('#fa_file').val(fa_files);
			$('#accessStruct').html(data);
			$.blockUI({ 
				message: $('#folderAccessRights'),
				css: {
					border: '2px solid #999',
					color:'#333',
					padding:'8px',
					top:  ($(window).height() - 400) /2 + 'px', 
					left: ($(window).width() - 400) /2 + 'px', 
					width: '400px' 
				} 
			});
		}
	});
	return false;
}

function savefolderAccess() {
	var form_data = $('#folderAccessRights').serialize();
	var ffolder_id = $('#filefolder_id').val();
	$('#load_save_folder_access').css('display', 'block');
	$('#folder_access_save').css('display', 'none');
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/saveAccessRights',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			// console.info(data);			
			$('#folder_access_save').css('display', 'block');
			$('#load_save_folder_access').css('display', 'none');
			if( data.result == true ) {
				//$('#all_mf_successerrmsg').html(data.msg);
				alert(data.msg);
				setTimeout(function() {
					$.unblockUI({ 
						onUnblock: function(){ getFolderdata(ffolder_id),$('.succ_err_msg').empty(); }
					}); 
				}, 1000);
			} else {
				alert(data.msg);
				//$('#all_mf_successerrmsg').html(data.msg);
				setTimeout('timerfadeout()', 1000);
			}
		}
	});
	
}

function isPaymentVal(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 45 && charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	else
	return true;
}

function add_tags(lead_id, file_id)
{
	var params				= {'lead_id':lead_id, 'file_id':file_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/add_tags',
		dataType: 'json',
		data: params,
		beforeSend:function(){
			$('#tags').html();
		},
		success: function(data) {
			console.info(data);
			$('#tags').html("");
			$('#tag_lead_id').val(data.lead_id);
			$('#tag_file_id').val(data.file_id);
			var ht = 50;
			$.blockUI({
				message: $('#add-tags'),
				css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '450px',height: ht+'px'} 
			});
			$( "#add-tags" ).parent().addClass( "folder-scroll no-scroll" );
			var tag_array = data.tag_names.split(',');
			// var tag_array = ;
			$.each(tag_array, function( index, value ) {
				// alert(value)
				if(value) {
					$("#tags").append( "<span>"+value+"</span>");
				}
			});
		}
	});
	return false;
}

$(function(){ // DOM ready
	$("#tags_input").on({
		focusout : function() {
			var txt= this.value.replace(/[^a-z0-9\+\-\.\#]/ig,''); // allowed characters
			//get the ajax call
			saveTags($('#tag_lead_id').val(), $('#tag_file_id').val());
			// if(txt) $("<span/>",{text:txt.toLowerCase(), insertBefore:this});
			if(txt){
				$("#tags").append( "<span>"+txt.toLowerCase()+"</span>");
			}
			this.value="";
		},
		keyup : function(ev) {
			// if: comma|enter (delimit more keyCodes with | pipe)
			if(/(188|13|9)/.test(ev.which)) $(this).focusout();
		}
	});
	$('#tags').on('click', 'span', function() {
		if(confirm("Remove "+ $(this).text() +"?")) {
			$(this).remove();
			saveTags($('#tag_lead_id').val(), $('#tag_file_id').val());
		}
	});
	
});

function saveTags(lead_id, file_id)
{
	var arr = [];
	$("#tags span").each(function(index, elem){
		arr.push( $(this).text() );
	});
	
	var params				= {'lead_id':lead_id, 'file_id':file_id, 'tags':arr};
	params[csrf_token_name] = csrf_hash_token;
	
	var currentRequest = $.ajax({
			url: site_base_url+'ajax/request/save_tags',
			data: params,
			type: "POST",
			dataType: "json",
			beforeSend: function () {
				if (currentRequest != null) {
					currentRequest.abort();
				}
			}
		})
		.done(function (res, status) {
			console.info(res);
			// return;
            /* ht = 50;
			$.blockUI({
				message: $('#add-tags'),
				css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '450px',height: ht+'px'} 
			});
			$( "#add-tags" ).parent().addClass( "folder-scroll" ); */
			// return false;

		})
		.fail(function (xhr, status, errorThrown) {
			if (xhr.status == 403) {
				window.location.reload();
			} else {
				alert('Fail');
				return;
			}
		})
	return false;
}

$(document).on('click','.close_icon',function(){
	$.unblockUI();
	return false;
});