/*
 *@Sale Forecast
 *@Sale Forecast Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspage are global js variable

var params  = {};
params[csrf_token_name] = csrf_hash_token;

var sturl = site_base_url+"dms/get_dms_files/0";
$('#list_file').load(sturl,function(){
	dataTable();
	$('#hfolder_id').val(0);
	showDmsBreadCrumbs(0);
	showDmsFolderOptions(0);
});

function loadExistingFiles(folderid) {
	$.get(
		site_base_url+'dms/get_dms_files/'+folderid,
		{},
		function(data) {
			alert(data);
			$('#list_file').html(data);
			dataTable();
			$('#hfolder_id').val(folderid);
		}
	);
	return false;
}

function getDmsData(ffolder_id) {
	showDmsBreadCrumbs(ffolder_id);
	showDmsFolderOptions(ffolder_id);
	$.get(
		site_base_url+'dms/get_dms_files/'+ffolder_id,
		{},
		function(data) {
			$('#list_file').html(data);
			$('#hfolder_id').val(ffolder_id);
			dataTable();
		}
	);
	return false;
}

function showDmsBreadCrumbs(parent_id) {
	$.get(
		site_base_url+'dms/getDmsBreadCrumbs/'+parent_id,
		{},
		function(data) {
			$('#file_breadcrumb').html(data);
		}
	);
	return false;
}

function showDmsFolderOptions(parent_id) {
	$('#files_actions').empty();
	$.get(
		site_base_url+'dms/getFolderActions/'+parent_id,
		{},
		function(data) {
			$('#files_actions').show();
			$('#files_actions').html(data);
		}
	);
	return false;
}

function runAjaxFileUpload()
{
	var _uid = new Date().getTime();
	$('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#job-file-list');
	var params 				 = {};
	params[csrf_token_name]  = csrf_hash_token;
	var ffid = $('#hfolder_id').val();

	if(ffid == '0') 
	{ 
	alert('You have no permissions to upload files to current locations. Please contact to administrators!.'); 
	return false;
	}

	$.ajaxFileUpload({
		url: 'dms/file_upload/'+ffid,
		secureuri: false,
		fileElementId: 'ajax_file_uploader',
		dataType: 'json',
		data: params,
		success: function (data, status) {
		
			if(typeof(data.error) != 'undefined') {
				if(data.error != '') {
					if (window.console) {
						console.log(data);
					}
					if (data.msg) {
						alert(data.msg);
					} else {
						alert('File upload failed!');
					}
					// $('#'+_uid).hide('slow').remove();
				} else {	
					if(data.msg == 'File successfully uploaded!') {
						// alert(data.msg);
						/*Showing successfull message.*/
						$('#fileupload_msg').html('<span class=ajx_success_msg>'+data.msg+'</span>');
						setTimeout('timerfadeout()', 3000);
						// Again loading existing files with new files
						$('#jv-tab-3').block({
							message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
							css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
						});
						$.get(
							site_base_url+'dms/get_dms_files/'+ ffid,
							{},
							function(data) {
								$('#list_file').html(data);
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
				}
			}
		},
		error: function (data, status, e)
		{
			alert('Sorry, the upload failed due to an error!');
			$('#'+_uid).hide('slow').remove();
			if (window.console)
			{
				console.log('ajax error\n' + e + '\n' + data + '\n' + status);
				for (i in e) {
				  console.log(e[i]);
				}
			}
		}
	});
	$('#ajax_file_uploader').val('');
	return false;
}

/*
 * Adding folders
 */
function create_dms_folder(fparent_id) {
	var params				= {'fparent_id':fparent_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'dms/get_folder_tree_struct',
		dataType: 'json',
		data: params,
		success: function(data) {
			$('#afparent_id').val(data.fparent_id);
			$('#add_file_tree').html(data.tree_struture);
			var ht = $('#create-folder').height();
			ht += 50;
			$.blockUI({
				message: $('#create-folder'), 
				css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '450px',height: ht+'px'} 
			});
			$( "#create-folder" ).parent().addClass( "no-scroll" );
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
		url: site_base_url+'dms/addFolders',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			if(data.err == 'true'){
				alert('Folder Name already exists (Or) you dont have access to write.');
				return false;
			}else {
				$('#af_successerrmsg').html(data.af_msg);
			}
			setTimeout(function() { 
				$.unblockUI({ 
					onUnblock: function(){ getDmsData(data.af_reload),$('.succ_err_msg').empty(),$('#new_folder').val(''); }
				}); 
			}, 2000);
		}
	});
	return false;
}

function download_files_id(file_id) {
	window.location.href = site_base_url+'dms/download_dms_file/'+$("#file_"+file_id).val();
}

function cancelDel() {
    $.unblockUI();
}

function dataTable() {
	$('#list_file_tbl').dataTable({
		"iDisplayLength": 15,
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
}

/*
*Deleting Multiple files
*/
function deleteAllFiles() {

	var ff_id	   = $('#hfolder_id').val();

	var del_folder = '';
	var del_files  = '';
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
	
	var result = confirm("Are you sure you want to delete this files?");
	if (result==false) {
		return false;
	}
	
	var params				= {'del_folder':del_folder, 'del_files':del_files, 'ff_id':ff_id};
	params[csrf_token_name] = csrf_hash_token;

	$.ajax({
		type: 'POST',
		url: site_base_url+'dms/delete_dms_files',
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
			getDmsData(data.folder_parent_id);
		}
	});
	return false;
}

/*
*Moving Multiple files
*/
function moveAllFiles() {
	var mv_folder = '';
	var mv_files  = '';
	$( ".file_chk:checked" ).each(function( index ) {
		if($(this).attr('file-type') == 'folder') {
			mv_folder += $(this).val()+',';
		} else if($(this).attr('file-type') == 'file') {
			mv_files += $(this).val()+',';
		}
	});
	// alert(mv_folder+'+++'+mv_files); return false;
	var current_folder_id = $('#hfolder_id').val();
	
	if((mv_folder=='') && (mv_files=='')) {
		alert('No files or folders selected');
		return false;
	}
	
	var params				= {'mv_folder':mv_folder, 'mv_files':mv_files, 'current_folder_id':current_folder_id};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'dms/get_moveall_file_tree_struct',
		dataType: 'json',
		data: params,
		success: function(data) {
			// console.info(data);
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
function move_all_files() 
{
	var ff_id	  = $('#hfolder_id').val();
	var form_data = $('#moveallfile').serialize();
	$.ajax({
		type: 'POST',
		url: site_base_url+'dms/mapallfiles',
		dataType: 'json',
		data: form_data,
		success: function(data) {
			// console.info(data);
			if( data.result == true ) {
				$('#all_mf_successerrmsg').html(data.mf_msg);
				setTimeout(function() {
					$.unblockUI({ 
						onUnblock: function(){ getDmsData(ff_id), $('.succ_err_msg').empty(); }
					}); 
				}, 2000);
			} else {
				getDmsData(ff_id);
				$('#all_mf_successerrmsg').html(data.mf_msg);
				setTimeout('timerfadeout()', 3000);
			}
		}
	});
	return false;
}

$(document).on('click','#file_chkall',function(){
	if($(this).prop('checked')){
		$('.file_chk:not(:checked)').trigger('click');
	}else{
		$('.file_chk:checked').trigger('click');		
	}
});

function timerfadeout()
{
	$('.succ_err_msg').empty();
}

// Edit folder permissions start.
function editFolderPermissions()
{
	var ht = $('#edit-folder-permissions').height();
	$('#edit-folder-permissions').text('Loading, please wait..');
	$.blockUI({
		message: $('#edit-folder-permissions'),
		css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht) /2 + 'px',left: ($(window).width() - 900) /2 + 'px',width: '900px',height: ht+'px'}
	});
	
	$.get(site_base_url+'dms/get_dms_folder_permissions_ui', {} , function(data)
	{
		$("#edit-folder-permissions").html(data);
		$.blockUI({
			message: $('#edit-folder-permissions'), 
			css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht)/2 + 'px',left: ($(window).width() - 900)/2 + 'px',width: '900px',height: ht+'px'} 
		});
		$( "#edit-folder-permissions" ).parent().addClass( "no-scroll" );
	});	
}
// Edit folder permission end.
$(document).on('change', '.all-chk', function(event){
	var type = $(this).attr('id');
	var uid = $(this).val();
	if($(this).is(':checked')) {
		$('.'+type+'-'+uid).prop('checked',true);
		switch(type){
			case 'rd-read':
				$('#rd-write, #rd-none').prop('checked',false);
			break;
			case 'rd-write':
				$('#rd-read, #rd-none').prop('checked',false);
			break;
			case 'rd-none':
				$('#rd-write, #rd-read').prop('checked',false);
			break;
		}
	} else {
		$('.rd-none-'+uid).prop('checked',true);
	}
});

/*
*@method searchFileFolder
*/
function searchFileFolder() {
	var search_input = $('#search_input').val();
	if(search_input == '')
	return false;
	
	// var params				= {'search_input':search_input, 'lead_id':curr_job_id, 'currently_selected_folder':parent_folder_id};
	var params				= {'search_input':search_input};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'dms/searchFile',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#list_file').html(data);
			dataTable();
			$.unblockUI();
		}
	});
	return false;
}

function load_root(){
	$('#search_input').val('');
	getDmsData(0);
}
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////