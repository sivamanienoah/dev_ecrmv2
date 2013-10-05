<?php require (theme_url().'/tpl/header.php'); ?>
<style type="text/css">
.choice-box {
    width:260px;
    padding:15px;
    -moz-border-radius:8px;
    -webkit-border-radius:8px;
    background:#a8cb17;
    float:left;
    margin:0 35px 30px 0;
	color:#a8cb17;
	cursor:pointer;
	position:relative;
	color:#fefffd;
	font-weight:bold;
}
.choice-box img {
	position:absolute;
	right:5px;
	top:-20px;
}
</style>
<?php $controller_uri = 'invoice'; ?>
<div id="content">
	<div class="inner">
		<?php  	if($this->session->userdata('accesspage')==1) {   ?>
		<form action="" method="post" style="float:right;">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
					<td>
                        Project Search
                    </td>
					<td>
                        <input type="text" name="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Project No, Project Title, Name or Company' ?>" class="textfield width210px pjt-search" />
                    </td>
                    <td rowspan=2>
                        <div class="buttons">
                            <button type="submit" class="positive">Search</button>
                        </div>
                    </td>
                </tr>
            </table>
		</form>
		
	    <h2><?php echo $page_heading ?></h2>
		
		<a class="choice-box" onclick="advanced_filter_pjt();" >
			Advanced Filters
			<img src="assets/img/icon_view_leads.png" class="icon leads" />
		</a>
		
		<div id="advance_search_pjt" style="float:left; width:100%;" >
		
		<form name="advanceFilters_pjt" id="advanceFilters_pjt"  method="post">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
			<thead>
				<tr>
					<th>By Project Status Wise</th>
					<th>By Project Manager Wise</th>
					<th>By Customer Wise</th>
				</tr>	
			</thead>
			<tbody>
			<tr>	
				<td>
					<select style="width:230px;" multiple="multiple" id="pjt_stage" name="pjt_stage[]">
						<option value="1">Project In Progress</option>
						<option value="2">Project Completed</option>
						<option value="3">Project Onhold</option>
						<option value="4">Inactive</option>
					</select> 
				</td>
				
				<td>
					<select style="width:230px;" multiple="multiple" id="pm_acc" name="pm_acc[]">
						<?php foreach($pm_accounts as $pm_acc) {?>
						<option value="<?php echo $pm_acc['userid']; ?>">
						<?php echo $pm_acc['first_name'].' '.$pm_acc['last_name']?></option>	
						<?php } ?>
					</select> 
				</td>
				
				<td>
					<select style="width:230px;" multiple="multiple" id="customer1" name="customer1[]">
						<?php foreach($customers as $customer) {?>
						<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr align="right" >
				<td colspan="5"><input type="reset" class="positive" name="advance_pjt" value="Reset" />
				<input type="submit" class="positive" name="advance_pjt" value="Search" /></td>
			
			</tr>
			</tbody>
			</table>
		</form>
	</div>
		<div class="clearfix"></div>
		
		<form name="project-total-form" onsubmit="return false;" style="clear:right; overflow:visible;">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		<div id="ad_filter" style="overflow:scroll; height:400px;" >
		<table border="0" cellpadding="0" cellspacing="0" class="data-table" style="width:1200px !important;">
            
            <thead>
				<th width="60">Action</th>
				<th width="50">Project No.</th>
				<th width="70">Project ID</th>
				<th width="120">Project Title</th>
				<th width="120" class="cust-data">Customer</th>
				<th width="120">Project Manager</th>
				<th width="60">Planned Start Date</th>
				<th width="60">Planned End Date</th>	
				<th width="40">Project Completion</th>
				<th width="90">Project Status</th>
            </thead>
            
            <tbody>
				<?php
				if (!isset($quote_section))
				{
					$quote_section = '';
				}
				
					if (is_array($records) && count($records) > 0) { ?>
                    <?php
					foreach ($records as $record) {
						
					?>
                    <tr>
						<td class="actions" align="center">
							<a href="<?php echo  $controller_uri ?>/view_project/<?php echo  $record['jobid'], '/', $quote_section ?>">
								View
							</a>
							<?php
								echo ($this->session->userdata('deletePjt') == 1) ? ' | <a href="welcome/delete_quote/' . $record['jobid'] . $list_location . '" onclick="return window.confirm(\'Are you sure you want to delete\n' . str_replace("'", "\'", $record['job_title']) . '?\n\nThis will delete all the items\nand logs attached to this job.\');">Delete</a>' : '';
							?>
						</td>
						
                        <td class="actions">
							<div>
								<a style="color:#A51E04; text-decoration:none;" href="<?php echo  $controller_uri ?>/view_project/<?php echo  $record['jobid'], '/', $quote_section ?>"><?php echo  $record['invoice_no'] ?></a> &nbsp;
							</div>
						</td>
						
						<td class="actions">
							<?php if (isset ($record['pjt_id'])) { echo $record['pjt_id']; } else { echo "-"; } ?>
							<!--<a href="<?php //echo $controller_uri ?>/view_project/<?php //echo $record['jobid'], '/', $quote_section ?>" title="<?php //echo $record['pjt_id'] ?>"> <?php //if (isset ($record['pjt_id'])) { echo $record['pjt_id']; } else { ?> </a><?php //echo "-"; } ?>-->
						</td>
						
                        <td class="actions">
							<!--<a href="<?php echo  $controller_uri ?>/view_project/<?php echo  $record['jobid'], '/', $quote_section ?>" title="<?php echo  $record['job_title'] ?>"><?php echo character_limiter($record['job_title'], 35) ?></a>-->
							<?php echo character_limiter($record['job_title'], 35) ?>
						</td>
						
                        <td class="cust-data">
							<span style="color:none">
							<!--<a href="customers/add_customer/update/<?php //echo $record['custid'] ?>" style="text-decoration:underline;"><?php //echo $record['cfname'] . ' ' . $record['clname'] ?></a>-->
							<?php echo $record['cfname'] . ' ' . $record['clname'] ?>
							</span> - 
							<?php echo $record['company'] ?>
						</td>
						
						<td class="cust-data">
							<?php echo $record['fnm'] . ' ' . $record['lnm']; ?>
						</td>
			
						<td><?php if ($record['date_start'] == "") { echo "-"; } else { echo  date('d-m-Y', strtotime($record['date_start'])); } ?></td>
						
						<td><?php if ($record['date_due'] == "") echo "-"; else echo  date('d-m-Y', strtotime($record['date_due'])) ?></td>
						
						<td class="actions" align="center"><?php if (isset($record['complete_status'])) echo ($record['complete_status']) . " %"; else echo "-"; ?></td>
						
						<td class="actions" align="center">
							<?php
							switch ($record['pjt_status'])
								{
									case 1:
										$pjtstat = 'Project In Progress';
									break;
									case 2:
										$pjtstat = 'Project Completed ';
									break;
									case 3:
										$pjtstat = 'Project Onhold';
									break;
									case 4:
										$pjtstat = 'Inactive';
									break;
								}
							 echo $pjtstat;
							 ?>
						</td>
					</tr>
						<?php
					} 
					?>
                <?php } else { ?>
                    <tr>
                        <td align="center" colspan="9">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
            
        </table>
		</div>
		</form>
		<?php } else { 
			echo "You have no rights to access this page";
			}
		?>
	</div>
</div>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<script type="text/javascript">
$(function(){
    $(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']});
	//.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});

//For Projects
var pjtstage = $("#pjt_stage").val(); 
var pm_acc = $("#pm_acc").val(); 
var cust = $("#customer1").val(); 
var keyword = $("#keywordpjt").val(); 
//alert(keyword);
if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';

if (document.getElementById('advance_search_pjt'))
	document.getElementById('advance_search_pjt').style.display = 'none';

	function advanced_filter_pjt(){
	$('#advance_search_pjt').slideToggle('slow');
	var  keyword = $("#keywordpjt").val();
	var status = document.getElementById('advance_search_pjt').style.display;
	
	if(status == 'none') {
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val(); 
	}
	else   {
		$("#pjt_stage").val("");
		$("#pm_acc").val("");
		$("#customer1").val("");
	}
}

$('#advanceFilters_pjt').submit(function() {	
	var pjtstage = $("#pjt_stage").val(); 
	var pm_acc = $("#pm_acc").val(); 
	var cust = $("#customer1").val(); 
	var  keyword = $("#keywordpjt").val(); 
	if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';
	document.getElementById('ad_filter').style.display = 'block';	
	var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
	//alert(sturl);
	$('#ad_filter').load(sturl);	
	return false;
});

$('#pjt_search_form').submit(function() {	
		var  keyword = $("#keywordpjt").val(); 
		if(keyword == "Project No, Project Title, Name or Company")
		keyword = 'null';
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val();  
		//document.getElementById('ad_filter').style.display = 'block';
		var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
		$('#ad_filter').load(sturl);
		return false;
});

</script>
<?php require ('tpl/footer.php'); ?>
