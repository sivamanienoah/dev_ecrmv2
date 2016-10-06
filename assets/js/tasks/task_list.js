$(function() {
	$('.data-tbls').dataTable({
		"language": {
				"search": "Search :",
				},
		"aaSorting": [ ],
		"iDisplayLength": 5,
		"aoColumnDefs": [
  {
     bSortable: false,
     aTargets: [ -1 ]
  },
    {
     bSortable: false,
     aTargets: [ -3 ]
  }
],
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": false,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,
		"bDestroy": true,		
		"bRetrieve": true,		
	});

$('.dataTables_filter label').css( "width", "auto");
$(".dataTables_empty").css( "padding", "10px" );
});

function btnActions(actionType,moduleName,methodName,itemId,formId) {
	
    this.actionType 		= 		actionType;
    this.moduleName 		= 		moduleName;
    this.methodName 		= 		methodName;
    this.itemId	    		= 		itemId;
	this.formId			    = 		formId;
	return false;

} 

btnActions.prototype.doAction = function() {
	var acttype = this.actionType;
		params = {'taskid': this.itemId, 'delete_task': true};
		params[csrf_token_name] = csrf_hash_token;
        var modulename= this.moduleName;	
    $.ajax({
			url: this.moduleName+'/'+'request'+'/'+this.methodName,	
			type: "POST",	
			data: params,		
			cache: false,
			success: function (html){
			 
									$( ".ui-tabs-nav li" ).each(function( index ) {

						if($( this ).attr('aria-controls')=='jv-tab-4')
						{
							
							$('.ui-tabs-nav li').eq(0).find("a").trigger('click');
						    $('.ui-tabs-nav li').eq(index).find("a").trigger('click');
						}

						});
			 }		
	});
};
function deleteItem(actionType,moduleName,methodName,itemId,formId)
{
/* 	$.blockUI({
	message:'<br /><h5>Are you sure you want to delete this task?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="button" class="positive" onclick="valueassign(); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
	css:{width:'440px'}
	}); */
var r = confirm("Are you sure?");
	if (r == true) 
	{
		var myAction = new btnActions(actionType, moduleName, methodName, itemId,"");
		myAction.doAction();
	} 
}

function valueassign(val)
{
	return val;
}


function refreshalltask(val)
{
				$("#taskcompleted").val(val);
				loadExistingTasks();
	
}
