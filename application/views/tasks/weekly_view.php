<?php
ob_start();
$cfg = $this->config->item('crm');

	$userdata = $this->session->userdata('logged_in_user');
	#define the users who can see the prices
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo  $this->config->item('base_url'); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
	<?php
	if (isset($quote_data['job_title']) && is_string($quote_data['job_title'])) echo htmlentities($quote_data['job_title'], ENT_QUOTES), ' - ';
	if (isset($page_heading) && is_string($page_heading)) echo $page_heading, ' - ';
	echo $cfg['app_full_name'];
	?>
</title>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" href="assets/css/base.css?q=18" type="text/css" />
<link rel="stylesheet" href="assets/css/quote.css?q=18" type="text/css" />
<link rel="stylesheet" href="assets/css/smoothness/ui.all.css?q=2" type="text/css" />
<link rel="stylesheet" href="assets/css/ui-lightness/jquery-ui-1.7.2.custom.css?q=1" type="text/css" />
<!-- link rel="stylesheet" media="screen" href="assets/css/jquery.timepickr.css" type="text/css" / -->
<script type="text/javascript" src="assets/js/jquery-1.2.6-min.js"></script>
<script type="text/javascript" src="assets/js/jq-ui-1.6b.min.js?q=2"></script>

<style type="text/css">
h2 {
	margin-bottom:20px;
}
#task-page {
	padding:20px 40px;
	font-family:'Lucida Grande', Helevetica, Arial, sans-serif;
	font-size:16px;
}


html, body {
	height:100%;
}
#main-frame {
	position:relative;
	width:1815px;
	height:100%;
	overflow:hidden;
}
.day-column {
	position:absolute;
	width:326px;
	height:100%;
	top:0;
	border-left:1px solid #999;
}
.day-column h3 {
	text-align:center;
	background:#333;
	padding:10px 0;
	border-top:1px solid #999;
	border-bottom:1px solid #999;
}
.day-column h3 span {
	font-size:14px;
	font-weight:normal;
}
.day-column.monday {
	left:180px;
}
.day-column.tuesday {
	left:507px;
}
.day-column.wednesday {
	left:834px;
}
.day-column.thursday {
	left:1161px;
}
.day-column.friday {
	left:1488px;
}
.user {
	width:100%;
	overflow:hidden;
	border-top:1px solid #999;
	position:relative;
}
.user .task {
	background:#444;
	padding:4px;
	font-size:90%;
	margin-bottom:2px;
	height:30px;
	overflow:hidden;
	position:relative;
	cursor:pointer;
}
.full-details-hover {
	position:absolute;
	background:lightGoldenRodYellow;
	color:#333;
	width:327px;
	height:auto;
	display:none;
	z-index:60;
	padding:5px;
	font-size:90%;
}
h2 span {
	color: darkOrange;
}

.user .task.due-today {
	background:orange;
	color:#333;
}
.user .task.late {
	background:pink;
	color:#444;
}
.user .task.late-2 {
	background:url(assets/css/bg/late-over-day.jpg);
	color:#444;
}
.user .task.late-more {
	background:url(assets/css/bg/late-over-days.jpg);
	color:#444;
}
.user .task.complete {
	background:limeGreen;
	color:#333;
}
.user .task.continued-task {
	border-left:4px dotted #000;
}
.user .task.late .end-date {
	background:red;
	color:white;
}
#sub-frame {
	margin-top:43px;
	position:relative;
}
.user h4 {
	position:absolute;
	margin:10px 0;
}
.height-expander {
	position:absolute;
	display:block;
	height:20px;
	width:auto;
	left:0;
	top:30px;
	display:none;
	outline:none;
}
.weekend {
	position:absolute;
	top:0px;
	width:4px;
	background:darkRed;
}
.task.unapproved {
	opacity:0.4;
	-moz-opacity:0.4;
	filter:alpha(opacity=40);
}
.due-today a {
	color:maroon;
	text-decoration:underline;
}
</style>

</head>
<body>

<div id="task-page">

<h2><?php echo $page_title ?></h2>

<div id="main-frame">
	
	<?php
	$week = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
	
	$day = date('l', $start_date_stamp);
	
	$offset = array_search($day, $week);
	
	$new_week = array_merge(array_slice($week, $offset), array_slice($week, 0, $offset));
	
	$i = 0;
	foreach ($new_week as $day)
	{
		$date = ($i == 0) ? date('jS F', $start_date_stamp) : date('jS F', strtotime('next ' . $day));
		$day_class = strtolower($week[$i]);
		echo <<< EOD
	<div class="day-column {$day_class}">
		<h3>{$day} <span>{$date}</span></h3>
	</div>
EOD;
		$i++;
	}
	?>

	<!-- helper -->
	<div class="full-details-hover"></div>
	
	<!-- job frame starts -->
	<div id="sub-frame">
<?php
# weekend offset
$wend_offset = 0;
if (date('l', $start_date_stamp) == 'Monday')
{
	$wend_offset = 2;
}
else if (date('l', $start_date_stamp) == 'Tuesday')
{
	$wend_offset = 1;
}

$user_tasks = array();
foreach ($results as $result)
{
	$user_tasks[$result['userid_fk']]['user_name'] = $result['user_label'];
	$user_tasks[$result['userid_fk']]['tasks'][$result['taskid']] = array(
																			'task' => $result['task'],
																			'company' => ($result['company'] == NULL) ? 'General Task' : $result['company'],
																			'hours' => str_pad($result['hours'], 2, '0', STR_PAD_LEFT),
																			'mins' => str_pad($result['mins'], 2, '0', STR_PAD_RIGHT),
																			'status' => $result['status'],
																			'start_date' => $result['start_date'],
																			'end_date' => $result['end_date'],
																			'delayed' => ( (int) $result['delayed'] * -1 ) - $wend_offset,
																			'due_today' => $result['due_today'],
																			'duration' => $result['duration'],
																			'start_offset' => $result['start_offset'],
																			'approved' => (int) $result['approved'],
																			'jobid' => $result['jobid'],
																			'leadid' => $result['leadid']
																		);
}
	#echo '<!-- ', print_r($user_tasks[1], TRUE), ' -->';
	$i = 0;
	foreach ($user_tasks as $ut)
	{
		echo <<< EOD
		<div class="user">
			<h4>{$ut['user_name']}</h4>
EOD;
		$total_time = 0;
		foreach ($ut['tasks'] as $task)
		{
			$format_task = explode("\n", $task['task']);
			$format_task = $format_task[0];
			$full_task = nl2br($task['task']);
			
			$complete = '';
			if ($task['status'] == '100')
			{
				$complete = ' complete';
			}
			else
			{
				$total_time += (int) $task['hours'] * 60;
				$total_time += (int) $task['mins'];
			}
			
			$delayed = '';
			if ($task['delayed'] == 1)
			{
				$delayed = ' late';
			}
			else if ($task['delayed'] == 2)
			{
				$delayed = ' late-2';
			}
			else if ($task['delayed'] > 2)
			{
				$delayed = ' late-more';
			}
			
			$unapproved = '';
			if ($task['approved'] == 0)
			{
				$unapproved = ' unapproved';
			}
			
			$due_today = ($task['due_today'] == '1') ? ' due-today' : '';
			
			$wend_offset = 0;
			
			$current_date_label = date('l');
			$start_date_label = date('l', strtotime($task['start_date']));
			
			if (
					($current_date_label == 'Tuesday' && $start_date_label == 'Monday')
					||
					($current_date_label == 'Wednesday' && in_array($start_date_label, array('Monday', 'Tuesday')))
					||
					($current_date_label == 'Thursday' && in_array($start_date_label, array('Monday', 'Tuesday', 'Wednesday')))
					||
					($current_date_label == 'Friday' && in_array($start_date_label, array('Monday', 'Tuesday', 'Wednesday', 'Thursday')))
			   )
				{
					$wend_offset = 327 * 2;
				}
			
			$task_width = ((int) $task['duration'] * 327) - 8;
			$task_offset = ((int) $task['start_offset'] * 327 * -1) + 180 - $wend_offset;
			
			$continued = '';
			if ($task_offset < 0)
			{
				$task_width += ($task_offset - 180);
				$task_offset = 176; // accounted for 4px border
				$continued = ' continued-task';
			}
			
			if ($task['company'] != 'General Task')
			{
				if ($task['leadid'] == 'YES')
				{
					$company_link = "<a href=\"{$this->config->item('base_url')}leads/index	/{$task['jobid']}\">{$task['company']}</a>";
				}
				else
				{
					$company_link = "<a href=\"{$this->config->item('base_url')}welcome/view_quote/{$task['jobid']}\">{$task['company']}</a>";
				}
			}
			else
			{
				$company_link = $task['company'];
			}
			
			echo <<< EOD
			<div class="task{$delayed}{$complete}{$due_today}{$continued}{$unapproved}" style="width:{$task_width}px; margin-left:{$task_offset}px;">
				{$company_link} | {$format_task}<br />
				{$task['hours']}:{$task['mins']} | {$task['status']}%
				<div class="full-details">
				<strong>{$task['company']}</strong><br />
				{$full_task}<br />
				<strong>Time: {$task['hours']}:{$task['mins']} | Status: {$task['status']}%</strong><br />
				<small><strong>Start: {$task['start_date']} | End: {$task['end_date']}</strong></small>
				</div>
			</div>
EOD;
		}
		
		$mins = $total_time % 60;
		$hours = floor($total_time / 60);
		
		$total_hours = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_RIGHT) ;
		
		echo <<< EOD
			<a href="#" class="height-expander">Expand</a>
		</div>
EOD;
		$i++;
	}
	?>
	</div><!-- end #sub-frame -->
</div><!-- end #main-frame -->

	
</div>

<script type="text/javascript">
//setTimeout(function(){ document.location.href = document.location.href; }, 1000*60*2);
$(function(){
	$('.user .task').click(function(){
		
		var pos = $(this).offset();
		var data = $('.full-details', $(this)).html();
		
		var divtop = parseInt(pos.top) - 65;
		var divleft = parseInt(pos.left) - 40;
		
		$('.full-details-hover').stop().hide('fast', function(){
			$('.full-details-hover').html(data).css({'top': divtop + 'px', 'left': divleft + 'px'}).show('fast');
		});
	});
	$('.full-details-hover').click(function(){
		$(this).hide('fast');
	});
	$('.user').each(function(i){
		var task_count = $(this).find('.task').size();
		if (task_count > 3)
		{
			$(this).data('oheight', $(this).height()).data('tc', task_count)
				.height(40 * 3)
				.find('.height-expander').data('expand', 'yes').text('Show All ' + task_count).show();
		}
	});
	
	$('.height-expander').click(function(){
		var p_el = $(this).parent();
		var c_el = $(this);
		
		if (c_el.data('expand') == 'yes')
		{
			c_el.data('expand', 'no');
			p_el.stop().animate({'height': p_el.data('oheight')}, 400, function(){
				c_el.text('Hide');
			});
		}
		else
		{
			c_el.data('expand', 'yes');
			p_el.stop().animate({'height': 40 * 3}, 400, function(){
				c_el.text('Show All ' + p_el.data('tc'));
			});
		}
		return false;
	});
	
	var tt = $('.day-column:contains("Friday")').offset();
	var bar_left = tt.left + 326 - 40 - 2; // + width of column - container margin - width offset
	
	var bar_height = $('#main-frame').height();
	
	$('#main-frame').append('<div class="weekend"></div>');
	$('#main-frame .weekend').css({'left': bar_left, 'height': bar_height});
});
</script>
</body>
</html>
<?php
ob_end_flush();
