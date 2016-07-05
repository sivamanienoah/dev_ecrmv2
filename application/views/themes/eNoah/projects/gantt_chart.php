<script src="assets/js/daypilot-all.min.js" type="text/javascript"></script>

<input type="hidden" id="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

<div id="dp"></div>


<script type="text/javascript">
	var dp = new DayPilot.Gantt("dp");
	// dp.startDate = "2015-01-01";
	// dp.days = 45;
	dp.init();
	dp.columns = [
		{ title: "Task Name", property: "text", width: 80},
		{ title: "Predecessors", property: "predecessor", width: 20},
		{ title: "Start", property: "start"},
		{ title: "Finish", property: "end"},
		{ title: "Duration", property: "duration"},
		{ title: "Assigned To", property: "resource_name"},
		{ title: "complete%", property: "complete"}
	];
	loadTasks();

	function loadTasks() {
		var params = jQuery("#ci_csrf_token").val();
		$.ajax({
			type:'POST',
			data:{'ci_csrf_token':params},
			url:site_base_url+'projects/gantt_chart/getTask',
			dataType:'json',
			success:function(data){
				dp.tasks.list = data;
				dp.update();
				get_init_values();
			}
		});
	}
	
	function get_init_values()
	{
		var project_id=jQuery("#project_id").val();
		var params = jQuery("#ci_csrf_token").val();
		$.ajax({
			type:'POST',
			data:{'ci_csrf_token':params},
			url:site_base_url+'projects/gantt_chart/getProjectInfo?project_id='+project_id,
			dataType:'json',
			success:function(data){
				dp.startDate = data.start;
				dp.days = data.days;
				dp.update();
			}
		});
	}
</script>