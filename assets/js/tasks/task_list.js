$(function() {
	$('.data-tbls').dataTable({
		"language": {
				"search": "Search :",
		},
		
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
		"aaSorting": [ [0, 'desc'] ],

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
	if( $('#welcome_view_quote').length )
	{
		$("#dashboardcount").css({"right": "14px", "top": "35px"});
	}

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
				if ( $( "#search-job-task" ).length  ||  $('#dashboard').val()==1 ) 
				{
					resetpage();
				}
		 }		
	});
};
function deleteItem(actionType,moduleName,methodName,itemId,formId)
{

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
