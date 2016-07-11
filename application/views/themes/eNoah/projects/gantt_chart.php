<script src="assets/js/gantt-chart/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="assets/css/gantt-chart/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">

<style type="text/css">
html, body{ height:100%; padding:0px; margin:0px; }
.weekend{ background: #f4f7f4 !important;}
.gantt_selected .weekend{ background:#FFF3A1 !important; }
.well {
text-align: right;
}
@media (max-width: 991px) {
.nav-stacked>li{ float: left;}
}
.container-fluid .row {
margin-bottom: 10px;
}
.container-fluid .gantt_wrapper {
height: 700px;
width: 100%;
}
.gantt_container {
border-radius: 4px;
}
.gantt_grid_scale { background-color: transparent; }
.gantt_hor_scroll { display: none !important;margin-bottom: 1px; }

.gantt_grid {
	width: 436.25px !important;
	
    overflow-x: auto;
    white-space: nowrap;
}
.gantt_task {
	width: 699px !important;
	
	overflow-x: auto;
    white-space: nowrap;
}
.gantt_grid_head_resource_name{
	text-align: left !important;
}
.gantt_grid_head_progress{
	text-align: right !important;
}
</style>

<div class="container-fluid">
	<div class="row">
		<input type="hidden" id="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		<input type="hidden" name="project_id" id="project_id" value="<?php echo $this->uri->segment('3'); ?>" />
		<div class="col-md-10 col-md-pull-2">
			<div class="gantt_wrapper panel" id="gantt_here" style="height:500px;"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
var data = (function () {
	var project_id=jQuery("#project_id").val();
    var json = null;
    $.ajax({
        'async': false,
        'global': false,
        'url': site_base_url+"projects/gantt_chart/getTask?project_id="+project_id,
        'dataType': "json",
        'success': function (data) {
            json = data;
        }
    });
    return json;
})(); 
var getListItemHTML = function (type, count, active) {
		return '<li'+(active?' class="active"':'')+'><a href="#">'+type+'s <span class="badge">'+count+'</span></a></li>';
	};

var updateInfo = function () {
	var state = gantt.getState(),
	tasks = gantt.getTaskByTime(state.min_date, state.max_date),
	types = gantt.config.types,
	result = {},
	html = "",
	active = false;

	// get available types
	for (var t in types) {
		result[types[t]] = 0;
	}
	// sort tasks by type
	for (var i=0, l=tasks.length; i<l; i++) {
		if (tasks[i].type && result[tasks[i].type] != "undefined")
			result[tasks[i].type] += 1;
		else
			result[types.task] += 1;
	}
	// render list items for each type
	for (var j in result) {
		if (j == types.task)
			active = true;
		else
			active = false;
		html += getListItemHTML(j, result[j], active);
	}

	// document.getElementById("gantt_info").innerHTML = html;
};

gantt.templates.scale_cell_class = function(date){
	if(date.getDay()==0||date.getDay()==6){
		return "weekend";
	}
};
gantt.templates.task_cell_class = function(item,date){
	if(date.getDay()==0||date.getDay()==6){
		return "weekend" ;
	}
};


gantt.config.columns = [
	{name:"text",label:"Task name",width:300,tree:true},
	{name:"start_date",label:"Start Date",template:function(obj){
		return gantt.templates.date_grid(obj.start_date);
	},align: "center",width:80},
	{name:"end_date",label:"End Date",template:function(obj){
		return gantt.templates.date_grid(obj.end_date);
	},align: "center",width:80},
	{name:"duration",label:"Duration",align:"center",width:60},
	{name:"resource_name",label:"&nbsp;&nbsp;Assigned To",align:"left",width:120},
	{name:"progress",label:"%Complete",template:function(obj){
		var progress=obj.progress*100;return Math.round(progress);
	},align:"right",width:80}
];

gantt.config.grid_width = 390;
gantt.config.date_grid = "%d-%m-%Y";
gantt.config.scale_height  = 60;
gantt.config.subscales = [
	{ unit:"month", step:1, date:"%M"}
];
gantt.config.date_scale = "%d";

gantt.templates.task_text=function(start,end,task){
    var progress=task.progress*100;
	progress=Math.round(progress);
	return "("+(progress)+"%)"+" "+task.text;
};


gantt.config.order_branch = true;

gantt.config.drag_move = false;
gantt.config.drag_resize = false;
gantt.config.drag_progress = false;
gantt.config.drag_links = false;
// gantt.config.details_on_dblclick = false;

gantt.config.lightbox.sections = [
    { name:"description", height:200, map_to:"text", type:"my_editor", focus:true},
];

gantt.config.buttons_left = ["dhx_cancel_btn"];
gantt.config.buttons_right = [];

gantt.attachEvent("onLinkDblClick", function(id,e){return false;});
gantt.attachEvent("onTaskCreated", function(id,e){return false;});	  
gantt.init("gantt_here");
gantt.parse(data);
</script>