/*
*@Request_view
*@
*/

function getFolderdata(ffolder_id) {
	showBreadCrumbs(ffolder_id);
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
					{ 'bSortable': false, 'aTargets': [ 0,6 ] }
				]
			});
			$.unblockUI();
		}
	);
	return false;
}

function ajaxDeleteFile(job_id, file_id, fparent_id) {
	var f_name = $('#filename_'+file_id).val();
	if (window.confirm('Are you sure you want to delete this file?')) 
	{
		var params 				= {'job_id':job_id,'file_id':file_id,'f_name':f_name,'fparent_id':fparent_id};
		params[csrf_token_name] = csrf_hash_token;
		
		$.post(
			site_base_url+'ajax/request/file_delete/',
			params,
			function(data) {
				try {
					var _data;
					eval('_data = ' + data);
					if (!_data.error) {
						getFolderdata(_data.fparent_id);
					} else {
						alert('File could not be deleted!');
					}
				} catch (e) {
					alert(e);
					alert('File could not be deleted!');
				}
			}
		);
	}
}

function ajaxDeleteFolder(lead_id, folder_id, fparent_id) {
	var folder_name = $('#filename_'+folder_id).val();
	if (window.confirm('Are you sure you want to delete this folder?')) 
	{
		var params 				= {'lead_id':lead_id,'folder_id':folder_id,'folder_name':folder_name,'fparent_id':fparent_id};
		params[csrf_token_name] = csrf_hash_token;
		
		$.post(
			site_base_url+'ajax/request/folder_delete/',
			params,
			function(data) {
				try {
					var _data;
					eval('_data = ' + data);
					if (!_data.error) {
						$("#fileupload_msg").html("<span class='ajx_success_msg'>"+_data.msg+"</span>");
						getFolderdata(_data.fparent_id);
						
					} else {
						// alert(_data.msg);
						$("#fileupload_msg").html("<span class='ajx_failure_msg'>"+_data.msg+"</span>");
					}
				} catch (e) {
					// alert(e);
					alert('Folder could not be deleted!');
				}
			setTimeout('timerfadeout()', 3000);
			}
		);
	}
}


function ajaxMoveFile(job_id, file_id, fparent_id) {
	var f_name = $('#filename_'+file_id).val();
	if (window.confirm('Are you sure you want to delete this file?')) 
	{
		var params 				= {'job_id':job_id,'file_id':file_id,'f_name':f_name,'fparent_id':fparent_id};
		params[csrf_token_name] = csrf_hash_token;
		
		$.post(
			site_base_url+'ajax/request/file_delete/',
			params,
			function(data) {
				try {
					var _data;
					eval('_data = ' + data);
					if (!_data.error) {
						getFolderdata(_data.fparent_id);
					} else {
						alert('File could not be deleted!');
					}
				} catch (e) {
					alert(e);
					alert('File could not be deleted!');
				}
			}
		);
	}
}

/*
 * Show as tree structure files for moving file
 */
function moveFile(leadid, file_id, fparent_id) {
	var params				= {'leadid':leadid, 'file_id':file_id, 'fparent_id':fparent_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_file_tree_struct',
		dataType: 'json',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#mlead_id').val(data.lead_id);
			$('#mfile_id').val(data.file_id);
			$('#mfparent_id').val(data.fparent_id);
			$('#mffilename').val($('#mf_'+file_id).val());
			$('#mffiletype').val($('#mftype_'+file_id).val());
			$('#mf_name').html('<strong><h3>Move '+$('#mf_'+file_id).val()+'</h3></strong>');
			$('#file_tree').html(data.tree_struture);
			$.blockUI({ 
				message: $('#move-file'), 
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
	var params				= {'leadid':leadid, 'fparent_id':fparent_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_folder_tree_struct',
		dataType: 'json',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#aflead_id').val(data.lead_id);
			$('#afparent_id').val(data.fparent_id);
			$('#add_file_tree').html(data.tree_struture);
			$.blockUI({
				message: $('#create-folder'), 
				css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - 400) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '400px' } 
			});
		}
	});
	return false;
}

/*
 *Adding folder
 */
function add_folder() {
	var form_data = $('#create-folder').serialize();
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/addFolders',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			// console.info(data);			
			$('#af_successerrmsg').html(data.af_msg);
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

/*
*@method searchFileFolder
*/
function searchFileFolder() {
	var search_input = $('#search_input').val();
	if(search_input == '')
	return false;
	
	var params				= {'search_input':search_input, 'lead_id':curr_job_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/searchFile',
		data: params,
		success: function(data) {
			console.info(data);
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
					{ 'bSortable': false, 'aTargets': [ 0,6 ] }
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


