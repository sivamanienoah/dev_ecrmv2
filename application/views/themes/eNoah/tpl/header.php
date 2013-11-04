<?php
$cfg = $this->config->item('crm');

$vid=$this->session->userdata['logged_in_user']['role_id'];
$viewLeads = getAccess(51, $vid);
$viewTasks = getAccess(108, $vid);

// for floating div
$proposal_notify_status = get_notify_status(1);
if($proposal_notify_status) {
	$proposal_notify_msg = proposal_expect_end_msg($proposal_notify_status);
}
$task_notify_status = get_notify_status(2);
if($task_notify_status) {
	$task_notify_msg = task_end_msg($task_notify_status);
}
// for floating div

if ($this->session->userdata('logged_in') == TRUE) {
 	$userdata = $this->session->userdata('logged_in_user');
	$menu_itemsmod = $this->session->userdata('menu_item_list');
	$menulist =  formMenuList($menu_itemsmod,true,NULL);
	$sensitive_information_allowed = TRUE;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $this->config->item('base_url'); ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
	<?php
	if (isset($quote_data['job_title']) && is_string($quote_data['job_title'])) echo htmlentities($quote_data['job_title'], ENT_QUOTES), ' - ';
	if (isset($page_heading) && is_string($page_heading)) echo $page_heading, ' - ';
	echo $cfg['app_full_name'];
	?>
</title>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" href="assets/css/base.css?q=19" type="text/css" />
<link rel="stylesheet" href="assets/css/datatable.css" type="text/css" />
<link rel="stylesheet" href="assets/css/demo_table.css" type="text/css" />
<link rel="stylesheet" href="assets/css/quote.css?q=21" type="text/css" />
<link rel="stylesheet" href="assets/css/smoothness/ui.all.css?q=2" type="text/css" />
<link rel="stylesheet" href="assets/css/jquery-ui-1.10.3.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="assets/css/jquery.jqplot.min.css" />
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="assets/js/excanvas.min.js"></script><![endif]-->
	<!--[if IE]>
    	<script src="assets/js/html5shiv.js"></script>
	<![endif]-->
<script type="text/javascript" src="assets/js/jquery-1.9.1-min.js"></script>
<script type="text/javascript" src="assets/js/jquery.dataTables.min.js"></script>
<script language="javascript" type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="assets/js/jquery-ui-1.10.3.js"></script>

<script language="javascript">
	$(document).ready(function() {
		$(window).scroll(function () {
			set = $(document).scrollTop()+"px";
			$('#floatNotifyDiv').animate({top:set}, {duration:1000,queue:false});
		});
		
		$( ".grid-close" ).bind( "click", function() { 
			// $('#floatNotifyDiv').slideUp('slow', function () { $lead.css('display','none'); });
			$('#floatNotifyDiv').hide();
		});
	});
	
	url_segment      = []; /// URL segments 
	csrf_token_name  = "<?php echo $this->security->get_csrf_token_name(); ?>";  //Assign Token Name
	csrf_hash_token  = "<?php echo $this->security->get_csrf_hash(); ?>";   //Assign Hash Token 
	site_base_url    = "<?php echo base_url(); ?>";   //Site Base URL
	accesspage       = "<?php echo $this->session->userdata('accesspage'); ?>";   //Site Base URL
	viewlead         = "<?php echo $this->session->userdata('viewlead'); ?>";   //Site Base URL
	viewPjt			 = "<?php echo $this->session->userdata('viewPjt'); ?>";
	
	/// Site URL segment 
	<?php foreach($this->uri->segments as $key=>$value){ ?>
			url_segment['<?php echo $key; ?>'] = '<?php echo $value; ?>';
	<?php } ?>
	
</script>


</head>
<body>

<div id="page">

<div class="header">
	<div id="logo"><a href="dashboard"><img src="assets/img/esmart_logo.jpg" alt=""/></a></div>
	<div class="row-two">
		<div class="client-logo">
			<?php 
			if (getClientLogo()) {
				$cilentLogo = getClientLogo();
			?>
			<a href="http://<?php echo $cilentLogo['client_url']; ?>" target="_blank"><img src="assets/img/client_logo/<?php echo $cilentLogo['filename']; ?>" alt="client-logo" /></a>
			<?php	
			} else {
			?>
			<a href="dashboard"><img src="" /></a>
			<?php } ?>
		</div>
		<div id="user-status">
			<?php if ($this->session->userdata('logged_in') == TRUE) { ?>
				<p id="user"><?php echo  ucfirst($userdata['first_name']) . ' ' . ucfirst($userdata['last_name']) ?> | <?php echo  $userdata['name'] ?> &nbsp; <a href="userlogin/">Sign out?</a></p>
			<?php } else { ?>
				<p id="user"><a href="userlogin/">Login</a></p>
			<?php } ?>
			<p class="date-time"><?php echo  date('l jS F Y') ?> <!--span class="msg-highlight"></span--></p>
		</div>
	</div>
</div>
	
	<?php
	$notify = $this->session->flashdata('notify_msg');
	$messages = $this->session->flashdata('header_messages');
	
	if (isset($userdata['signature']) && trim($userdata['signature']) == '')
	{
		$messages[] = 'Your signature for the eCRM is not complete, please update the signature by visiting <a href="myaccount/">your account</a>.';
	}
	
	if ($this->uri->segment(1) == 'welcome' || $this->uri->segment(1) == 'dashboard') {
		if (!empty($proposal_notify_msg)) {
			$notify[] = "<span class=notify_high>Leads</span>";
			foreach ($proposal_notify_msg as $arr){
				$notify[] = 'The proposal expected date for the lead "<a href="'.base_url().'welcome/view_quote/'.$arr['jobid'].'/">'.$arr['job_title'].'</a>" is going to end on <span class=notify_high>'.date('d-m-Y', strtotime($arr['dt'])).'</span>.';
			}
		}
	}
	// echo "<pre>"; print_r($task_notify_msg); exit;
	
	if ($this->uri->segment(1) == 'tasks' || $this->uri->segment(1) == 'dashboard') {
		if (!empty($task_notify_msg)) {
			$notify[] = "<span class=notify_high>Tasks</span>";
			foreach ($task_notify_msg as $arr){
				$task_desc = word_limiter($arr['task'], 4);
				$notify[] = 'The task "<a href="'.base_url().'tasks/all/">'.$task_desc.'</a>" is going to end on <span class=notify_high>'.date('d-m-Y', strtotime($arr['end_date'])).'</span>.';
			}
		}
	}
	
	if (is_array($notify) && count($notify) > 0 &&  ($this->session->userdata('logged_in') == TRUE)) { ?>
			<div id="floatNotifyDiv">
				<div class="grid-close"></div>
				<p><?php for ($i = 0; $i < count ($notify);  $i ++) { echo $notify[$i]; if (isset($notify[$i + 1])) echo '<br />'; } ?></p>
			</div>
	<?php } ?>
	
	
	<?php if (is_array($messages) && count($messages) > 0 ) { ?>
		<div id="messages">
			<p><?php for ($i = 0; $i < count ($messages);  $i ++) { echo $messages[$i]; if (isset($messages[$i + 1])) echo '<br />'; } ?></p>
		</div>
	<?php }
    
    $confirm = $this->session->flashdata('confirm');
	if (is_array($confirm) && count($confirm) > 0 ) { ?>
		<div id="confirm">
			<p>
				<?php for ($i = 0; $i < count ($confirm);  $i ++) { echo $confirm[$i]; if (isset($confirm[$i + 1])) echo '<br />'; } ?>
			</p>
		</div>
	<?php }

	$errors = $this->session->flashdata('login_errors');
	if (is_array($errors) && count($errors) > 0 ) { ?>
		<div id="errors">
			<p>
				<?php for ($i = 0; $i < count ($errors);  $i ++) { echo $errors[$i]; if (isset($errors[$i + 1])) echo '<br />'; } ?>
			</p>
		</div>
	<?php } ?>
    
    
	<?php
		if ($this->session->userdata('logged_in') == TRUE)
		{
			echo $menulist ;      
		} 
	?>
   
<?php 
	$menulist_access = explode('#',$menu_itemsmod);
	$menulist_access=array_reverse($menulist_access);

	for($j=0;$j<count($menulist_access);$j++)
	{
		$menu_items_vals[] = explode(',',$menulist_access[$j]);
	}		 

	$qurystring =explode('/',$_SERVER['REQUEST_URI']);
	$Qstring='';
	for($e=2;$e<count($qurystring);$e++) 
	{
		if($Qstring==''){
			$Qstring =$qurystring[$e];
		}else{
			$Qstring .='/'.$qurystring[$e];
		}
	}
	//echo $Qstring;
	$access_limit = array();
	$parent_id='';
		
	$i=0;
	//$access_limit= array();
	// echo "<pre>"; print_r($menu_items_vals); 
	foreach($menu_items_vals as $menu_items) {		 
		//echo "<pre>";print_r($menu_items);
		$strcmp = strcmp(strtolower($this->uri->segment(1)), strtolower($menu_items[3]));	
		if(($strcmp==0 && $i==0 )) 
		{ 
			$i+=1;
			$parent_id = $menu_items['1'];
			$master_id = $menu_items['0'];
			$access_limit['view'] =$menu_items[8];
			$access_limit['add'] =$menu_items[9];
			$access_limit['edit'] =$menu_items[10];
			$access_limit['delete'] =$menu_items[11];
			$access_limit['links'] =$menu_items[4];
			$access_limit['name'] =$menu_items[2];
		}
		if($menu_items['0'] == 51)  //leads
		{ 
		   $viewLead = $menu_items[8];
		   $addLead = $menu_items[9];
		   $editLead = $menu_items[10];
		   $deleteLead = $menu_items[11];
		}
		if($menu_items['0'] == 108) //Tasks
		{ 
		   $viewTask = $menu_items[8];
		   $addTask = $menu_items[9];
		   $editTask = $menu_items[10];
		   $deleteTask = $menu_items[11];
		}
		if($menu_items['0'] == 110) //Projects
		{ 
		   $viewPjt = $menu_items[8];
		   $addPjt = $menu_items[9];
		   $editPjt = $menu_items[10];
		   $deletePjt = $menu_items[11];
		}
		if($menu_items['0'] == 84) //customer
		{ 
		   $addImpCus = $menu_items[9];
		}
	}  	 
	//echo $this->uri->segment(1);
	if(!isset($master_id)) 
	{
		$masters=formMasterDetail($this->uri->segment(1), $userdata['role_id']);		  
		$master_id= $masters[0]['master_parent_id'];
		$access_limit['view'] =$masters[0]['view'];
		$access_limit['add'] =$masters[0]['add'];
		$access_limit['edit'] =$masters[0]['edit'];
		$access_limit['delete'] =$masters[0]['delete'];
	}
		
	echo $menulistss =  formSubMenuList($master_id);
		 
	$array= array();
	$array['accesspage']= $access_limit['view'];
	$array['add']= $access_limit['add']; 
	$array['edit']= $access_limit['edit']; 
	$array['delete']= $access_limit['delete']; 
	$array['viewlead'] = $viewLead;
	$array['addlead'] = $addLead;
	$array['editlead'] = $editLead;
	$array['deletelead'] = $deleteLead;
	$array['viewtask'] = $viewTask;
	$array['addtask'] = $addTask;
	$array['edittask'] = $editTask;
	$array['deletetask'] = $deleteTask;
	$array['viewPjt'] = $viewPjt;
	$array['addPjt'] = $addPjt;
	$array['editPjt'] = $editPjt;
	$array['deletePjt'] = $deletePjt;
	$array['addImpCus'] = $addImpCus;
	$this->session->set_userdata($array);
		
	?>