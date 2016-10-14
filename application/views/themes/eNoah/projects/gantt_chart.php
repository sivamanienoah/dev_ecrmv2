<script src="assets/js/gantt-chart/dhtmlx.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/js/gantt-chart/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/js/gantt-chart/dhtmlxgantt_tooltip.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="assets/css/gantt-chart/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">
<link rel="stylesheet" href="assets/css/gantt-chart/dhtmlx.css" type="text/css" media="screen" title="no title" charset="utf-8">

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
	.dhx_calendar_cont input {
		width: 96px;
		padding: 0;
		margin: 3px 10px 10px 10px;
		font-size: 11px;
		height: 17px;
		text-align: center;
		border: 1px solid #ccc;
		color: #646464;
	}
	.dhtmlxcalendar_dhx_skyblue, .dhtmlxcalendar_dhx_web, .dhtmlxcalendar_dhx_terrace {
		z-index: 999999 !important;
	}
	.gantt_slider {
		width: 530px;
		height: 20px;
		margin-left: 10px;
		display: inline-block;
	}
	.gantt_slider input{
		width: 34px;
		height: 18px;
		border: none;
	}
	.gantt_slider div:first-child, .gantt_slider .gantt_slider_value{
		display: inline-block;
		vertical-align: middle;
		line-height: 13px;
	}
	.gantt_slider .gantt_slider_value{
		font-size: 15px;
		color: black;
		margin: 5px 10px;
	}
	.gantt_time_selects{
		display:none;
	}
	#duration{
		display:none;
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

	gantt.config.date_grid = "%d-%m-%Y";
	gantt.config.subscales = [
		{ unit:"month", step:1, date:"%M"}
	];
	//gantt.config.scale_unit = "hour";
	gantt.config.duration_unit = "day";
	gantt.config.date_scale = "%d";
	gantt.config.details_on_create = true;

	gantt.templates.task_class = function(start, end, obj){
		return "resource_"+obj.owner;
	}

	gantt.config.columns = [
		{name:"text",label:"Task Name",width:300,tree:true},
		{name:"start_date",label:"Start Date",align:"center",width:80},
		{name:"enddate",label:"End Date",align:"center",width:80},
		{name:"hours",label:"Work hours",align:"center",width:80},
		{name:"resource",label:"Resource Name &nbsp;&nbsp;&nbsp;",align:"left",width:120},
		{name:"progress",label:"% Complete &nbsp;",template:function(obj){
			var progress=obj.progress*100;return Math.round(progress);
		},align:"right",width:80},
		{name:"add",label:"",width:44}
	];
	gantt.config.grid_width = 380;

	

	gantt.templates.task_text=function(start,end,task){
		var progress=task.progress*100;
		progress=Math.round(progress);
		return "("+(progress)+"%)"+" "+task.text;
	};
	
	
	var duration = function (a, b, c) {
		var res = gantt.calculateDuration(a.getDate(false), b.getDate(false));
		c.innerHTML = res + ' days';
	};
	var calendar_init = function (id, data, date) {
		var obj = new dhtmlXCalendarObject(id);
		obj.setDateFormat(data.date_format ? data.date_format : '');
		obj.setDate(date ? date : (new Date()));
		obj.hideTime();
		if (data.skin)
		obj.setSkin(data.skin);
		return obj;
	};
	gantt.form_blocks["dhx_calendar"] = {
		render: function (sns) {
			return "<div class='dhx_calendar_cont'><input type='text' readonly='true' id='calendar1'/> &#8211; "
			+ "<input type='text' readonly='true' id='calendar2'/><label id='duration'></label></div>";
		},
		set_value: function (node, value, task, data) {
			var a = node._cal_start = calendar_init('calendar1', data, task.start_date);
			var b = node._cal_end = calendar_init('calendar2', data, task.enddate);
			var c = node.lastChild;
			b.setInsensitiveRange(null, new Date(a.getDate(false) - 86400000));
			var a_click = a.attachEvent("onClick", function (date) {
			b.setInsensitiveRange(null, new Date(date.getTime() - 86400000));
			duration(a, b, c);
			});
			var b_click = b.attachEvent("onClick", function (date) {
			duration(a, b, c);
			});
			var a_time_click = a.attachEvent("onChange", function (d) {
			b.setInsensitiveRange(null, new Date(d.getTime() - 86400000));
			duration(a, b, c);
			});
			var b_time_click = b.attachEvent("onChange", function (d) {
			duration(a, b, c);
			});
			var id = gantt.attachEvent("onAfterLightbox", function () {
			a.detachEvent(a_click);
			a.detachEvent(a_time_click);
			a.unload();
			b.detachEvent(b_click);
			b.detachEvent(b_time_click);
			b.unload();
			a = b = null;
			this.detachEvent(id);
			});

			document.getElementById('calendar1').value = a.getDate(true);
			document.getElementById('calendar2').value = b.getDate(true);
			duration(a, b, c);
		},
		get_value: function (node, task) {
			task.start_date = node._cal_start.getDate(false);
			task.enddate = node._cal_end.getDate(false);
			return task;
		},
		focus: function (node) {
		}
	};
	
	gantt.form_blocks["dhx_slider"] = {
		render: function (sns) {
			return '<div class="gantt_slider"><div><input type="text" readonly="true"/></div></div>';
		},
		set_value: function (node, value, task, data) {
			if (!node._slider) {
				node._slider = new dhtmlXSlider({
				parent: node,
				size: 270,
				max: 100,
				tooltip: true,
				step: data.step ? data.step : 1,
				skin: data.skin ? data.skin : ''
				});

				node._count = document.createElement('div');
				node._count.className = "gantt_slider_value";

				node.appendChild(node._count);
				var slider_id = node._slider.attachEvent("onChange", function (newValue, sliderObj) {
				node._count.innerHTML = newValue + "%";
				});
				var id = gantt.attachEvent("onAfterLightbox", function () {
				node._slider.detachEvent(slider_id);
				node._slider.unload();
				node._slider = null;
				this.detachEvent(id);
				});
			}
			if (task.progress || task.progress == 0) {
				node._slider.setValue(parseInt(task.progress * 100));
				node._count.innerHTML = parseInt(task.progress * 100) + "%";
			}
		},
		get_value: function (node, task) {
			return node._slider ? node._slider.getValue() / 100 : 0;
		},
		focus: function (node) {
		}
	};

	gantt.locale.labels["section_owner"] = "Resource";
	gantt.locale.labels["section_time"] = "Start Date";
	gantt.locale.labels["section_progress"] = "Progress";
	gantt.locale.labels["section_date"] = "Date";
	gantt.locale.labels["section_hours"] = "Duration";
	
	gantt.config.lightbox.sections = [
		{name: "description",height:38,map_to:"text",type:"textarea",focus:true},
		{name: "owner",height:38,map_to:"resource",type:"textarea"},
		{name: "progress",type:"dhx_slider",map_to:"progress",step:6},
		{name: "hours",height:28,map_to:"hours",type:"textarea"},
		{name: "date",type:"dhx_calendar",map_to:"auto",skin:'',date_format:'%d-%m-%Y'},
	];
	
	gantt.attachEvent("onLoadEnd", function(){
		var first = gantt.getTaskByTime()[0];
		gantt.showLightbox(first.id);
	});

	gantt.attachEvent("onAfterTaskUpdate", function(id, task, is_new){
		var dateToStr = gantt.date.date_to_str("%Y-%m-%d %H:%i:%s");
		var csrf_token=jQuery("#ci_csrf_token").val();
		var project_id=jQuery("#project_id").val();
		$.post(site_base_url+"projects/gantt_chart/updateTask",{
		ci_csrf_token:csrf_token,
		id:id,
		project_id:project_id,
		hours:task.hours,
		task_name:task.text,
		progress:task.progress,
		start_date:dateToStr(task.start_date),
		end_date:dateToStr(task.enddate),
		resource:task.resource,
		},function(data){
			refresh();
		}) ;
		return true;
	});
	
	function refresh(){
		
		gantt.refreshData();
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
		gantt.init("gantt_here");
		gantt.parse(data);
	}

	gantt.attachEvent("onAfterTaskDelete", function(id, task, is_new){
		var csrf_token=jQuery("#ci_csrf_token").val();
		var project_id=jQuery("#project_id").val();
		$.post(site_base_url+"projects/gantt_chart/deleteTask",{
		ci_csrf_token:csrf_token,
		id:id,
		project_id:project_id,
		},function(data){
			refresh();
		}) ;
		return true;
	});
	gantt.attachEvent("onAfterTaskAdd", function(id,item){
		var csrf_token=jQuery("#ci_csrf_token").val();
		var project_id=jQuery("#project_id").val();
		var dateToStr = gantt.date.date_to_str("%Y-%m-%d %H:%i:%s");
		$.post(site_base_url+"projects/gantt_chart/addTask",{
			ci_csrf_token:csrf_token,
			id:id,
			parent_id:item.parent,
			project_id:project_id,
			hours:item.hours,
			task_name:item.text,
			progress:item.progress,
			start_date:dateToStr(item.start_date),
			end_date:dateToStr(item.enddate),
			resource:item.resource,
		},function(data){
			//gantt.changeTaskId(id, data);
			refresh();
		}) ;
		return true;
	});
	
	gantt.config.order_branch = true;
	gantt.config.drag_move = false;
	gantt.config.drag_resize = false;
	gantt.config.drag_progress = false;
	gantt.config.drag_links = false;
	
	gantt.init("gantt_here");
	
	gantt.parse(data);
</script>