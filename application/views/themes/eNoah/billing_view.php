<?php
$hostingid=array();
if(!empty($packages)){
	$pack=array();$pack_price=array();
	foreach($packages as $val){
		$t=$val['package_id'];
		$pack[$t]=$val['package_name'];
		$pack_price[$t]=$val['package_price'];
	}
}
?><?php require ('tpl/header.php'); ?>
<div id="content">
<script type="text/javascript" src="assets/js/j-tip.js?q=8"></script>
<style type="text/css">
#JT {
	position:absolute;
	background:#333;
}
#JT_close_left {
	padding:5px 0 0 10px;
}
#JT_copy {
	padding-left:10px;
}
.myjob {
	background:#222;
}
.csr-option-wrap {
	display:block;
	width:auto;
	overflow:visible;
	position:relative;
}
.csr-option-wrap .in-csr {
	display:block;
	position:absolute;
	top:-2px;
	left:-27px;
}
</style>
<?php
//include ('tpl/invoice_submenu.php');
$controller_uri = 'invoice';
?>
<div class="inner">
<?php if($this->session->userdata('accesspage'==1)) { ?>
	<form action="" method="post" style="float:right;">
	
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
		<table border="0" cellpadding="0" cellspacing="0" class="search-table">
			<tr>
				<td>
					Quotation / Invoice Search
				</td>
				<td>
					<input type="text" name="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Invoice No, Job Title, Name or Company' ?>" class="textfield width180px g-search" />
				</td>
				<td rowspan=2>
					<div class="buttons">
						<button type="submit" class="positive">Search</button>
					</div>
				</td>
			</tr>
			<?php if(!empty($packages)){ ?>
			<tr>
				<td>
					Filter By Package
				</td>
				<td>
				<select name="pack_name" class="textfield width180px">
				<?php
				if(!empty($pack)){
					(isset($_POST['pack_name'])) ? $pk_nm= $_POST['pack_name']:$pk_nm=0;
					($pk_nm==-1)? $s=' selected="selected"':$s='';
					($pk_nm==-2)? $s1=' selected="selected"':$s2='';
					($pk_nm==-3)? $s2=' selected="selected"':$s2='';
					echo '<option value="0">All Packages</option>';
					echo '<option value="-2" '.$s1.'>All Packages Pending payment</option>';
					echo '<option value="-3" '.$s2.'>All Packages Newly generated</option>';
					echo '<option value="-1" '.$s.'>No Packages</option>';
					foreach($pack as $k=>$v) {
						($pk_nm==$k)? $s=' selected="selected"':$s='';
						echo '<option value="'.$k.'" '.$s.'>'.$v.'</option>';
					}
				}					
				?>
				</select>
				</td>
			</tr>
			<?php } ?>
		</table>
	</form>
	<h2><?php echo  $page_heading ?></h2>
	<form id="PTF" name="project-total-form"  action="welcome/generate_invoice/" style="clear:right; overflow:visible;" method="post">
	
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
	<?php if (isset($_POST['pack_name']) && $_POST['pack_name']==-1){/**/}
	else { ?>
	<!--<div style="position:absolute;margin-top:-55px;width:300px;"><div class="buttons" style="float:left;width:80px;"><button type="submit" class="positive" name="generate" value="generate">Generate</button></div>
	<div class="buttons" style="float:left;width:80px;"><button type="submit" class="positive" name="send" value="send" onclick="$('#PTF').attr('target','_blank');">Send</button></div>
	<div class="buttons" style="float:left;width:140px;"><button type="submit" class="positive" name="auto_generate" value="auto_generate">Auto Generate</button></div> -->
	<?php } ?>
	<!--</div> -->
	<table border="0" cellpadding="0" cellspacing="0" class="data-table">
		<thead>
			<tr>
				<th width="85">Quote No.</th>
				<th width="270">Title</th>
				<th class="cust-data">Customer</th>
				<th width="50">Hosting</th>
				<th width="100">Created On</th>
				<th width="100">Actions</th>
				<?php
				if (in_array($userdata['level'], array(0, 1, 4, 5))) {
					echo '<th width="80" style="text-align:right;">Send/Value <input type="checkbox" class="project-cost-toggle"/></th>';
				}
				?>
				<th width="60">Deducted from card</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$grand_total=0;
			$link='invoice/view_quote/';
			//if($NO_Package) $link='invoice/view_quote/';
			//else $link='invoice/preview/';
			if (!isset($quote_section))	{
				$quote_section = '';
			}
			if (is_array($records) && count($records) > 0) { ?>
				<?php
				foreach ($records as $record) {
				
					$log_img_src = 'assets/img/logs.gif';
					if (!strstr($record['log_view_status'], $userdata['userid']))
					{
						$log_img_src = 'assets/img/new_logs.gif?q=1';
					}
					
					?>
				<tr <?php if ($userdata['userid'] == $record['assigned_to']) echo ' class="myjob"' ?>>
					<td class="actions">
						<div class="csr-option-wrap">
							<a href="<?php echo  $link.$record['jobid'].'/package'; ?>"><?php echo  $record['invoice_no'] ?></a> &nbsp;
							<a class="jTip" id="jt-link-<?php echo  $record['jobid'] ?>" href="ajax/request/logs/<?php echo  $record['jobid'] ?>" title="Available Logs" target="_blank"><img src="<?php echo $log_img_src ?>" alt="Logs" /></a>
							<?php if($record['created_by']==-1) echo '<span style="font-size:12px;color:white;">A</span>'; ?>
							<?php if (isset($record['in_csr']) && $record['in_csr'] == 1) echo '<img src="assets/img/dollar-small.png" alt="In CSR" class="in-csr" />' ?>
							
						</div>
					</td>
					<td class="actions"> 
						<?php
							if (is_file(dirname(FCPATH) . '/assets/img/sales/' . $record['belong_to'] . '.jpg'))
							{
								?>
								<img src="assets/img/sales/<?php echo $record['belong_to'] ?>.jpg" title="<?php echo $record['belong_to'] ?>" />
								<?php
							}
							?>
							<a href="<?php echo  $link.$record['jobid'].'/package'; ?>" title="<?php echo  $record['job_title'] ?>"><?php echo character_limiter($record['job_title'], 35) ?></a>
							<?php
							if ($record['invoice_downloaded'] == 1) echo '<img src="assets/img/cab.gif" alt="Invoice Downloaded"/>';
							?>
					</td>
					<td class="cust-data"><span style="color:#f70;"><a href="customers/add_customer/update/<?php echo  $record['custid'] ?>" style="text-decoration:underline;"><?php echo  $record['first_name'] . ' ' . $record['last_name'] ?></a></span> - 
					<?php echo $record['company'] ?></td>
					<td><?php 
					if(!empty($packages)) {
							$c=$record['custid'];
							$h=$record['hostingid'];$j=$record['jobid'];
							if(!empty($hosting[$h])) $color='#00CC00';
							else if(!empty($JOBS[$j])) $color='yellow';
							else $color='red';
							echo '<a href="hosting/hosts/'.$c.'" style="color:'.$color.'">View</a>';
					}
					else {
						$jobid=$record['jobid'];
						if(!empty($hostingid[$jobid])) $color='#00CC00';
						else $color='red';
						if(!empty($record['hostingid'])) echo '<a href="dns/jobs/'.$jobid.'" style="color:'.$color.'">View</a>';
					}
					?></td>
					<td><?php echo  date('d-m-Y H:i', strtotime($record['date_created'])) ?></td>
					<td class="actions" align="center"><a href="<?php echo  $controller_uri ?>/view_quote/<?php echo  $record['jobid'].'/package'; ?>">View</a>
					
					<?php
					$list_location = ($this->uri->segment(3)) ? '/' . $this->uri->segment(3) : '';
					echo (in_array($userdata['level'], array(0,1)) && $record['invoice_downloaded'] != 1) ? ' | <a href="welcome/delete_quote/' . $record['jobid'] . $list_location . '" onclick="return window.confirm(\'Are you sure you want to delete\n' . str_replace("'", "\'", $record['job_title']) . '?\n\nThis will delete all the items\nand logs attached to this job.\');">Delete</a>' : '';
					echo (in_array($userdata['level'], array(5)) && $record['job_status'] > 3) ? ' | <a href="quotation/invoice_data_zip/' . $record['jobid'] . '">Get Invoice</a>' : '' ?></td>
					<?php
					$price=0;$j=$record['jobid'];
					if(!empty($packages)) {
						if(!empty($JOBS[$j])){
							foreach($JOBS[$j] as $v1) {
								if(!empty($hosting[$v1])) {
									foreach($hosting[$v1] as $v1) $price+=$pack_price[$v1];
								}
							}
						}
					}
						$formatted_cost = '$' . number_format($price, 2, '.', ',');
						//echo $record['jobid'].'  '.$formatted_cost.'<br>';
						echo <<< EOD
						<td class="project-cost" align="right">{$formatted_cost}
						<span class="display-none">{$price} </span>
						<em class="display-none">0</em>
						<input type="checkbox" class="project-item-cost-toggle" value="{$j}" name="jobs[]"/></td>
						<td><input type="checkbox"/></td>
EOD;
					$grand_total+=$price;
					?>
					</tr>
					<?php
				} 
				if (in_array($userdata['level'], array(0, 1, 4, 5)))
				{
					echo <<< EOD
				<tr>
					<td colspan="6" align="right"> Total</td>
					<td class="project-cost-total" align="right"></td>
					<td></td>
				</tr>
EOD;
				}
				?>
			<?php } else { ?>
				<tr>
					<td colspan="10" align="center">No records available to be displayed!</td>
				</tr>
			<?php } ?>
		</tbody>
		
	</table>
	</form>
	
</div>
<?php } else { echo "you have no rights to access this page"; }?>
</div>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<script type="text/javascript">
$(function(){
$('.project-cost-toggle').change(function(){
	if ($(this).is(':checked'))	$('.project-item-cost-toggle').attr('checked', true);
	else $('.project-item-cost-toggle').removeAttr('checked', false);
	calculateProjectTotals();
});
calculateProjectTotals();
$('.project-item-cost-toggle, #adjust_for_deposits').change(calculateProjectTotals);
});
function calculateProjectTotals()
{
var adjust_deposits = $('#adjust_for_deposits').is(':checked');
var the_totals = 0;
$('.project-item-cost-toggle:checked').each(function(){
	var tempTotal = parseFloat($(this).siblings('span').text());
	var tempDeposits = parseFloat($(this).siblings('em').text());
	if (adjust_deposits && !isNaN(tempDeposits)) tempTotal -= tempDeposits;
	the_totals += parseFloat(tempTotal);
});
$('.project-cost-total').text('$ ' + number_format(the_totals, 2, '.', ','));
}

function number_format(number, decimals, dec_point, thousands_sep) {
var n = !isFinite(+number) ? 0 : +number, 
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
	toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec);
		return '' + Math.round(n * k) / k;
	};
s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
if (s[0].length > 3) {
	s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
}
if ((s[1] || '').length < prec) {
	s[1] = s[1] || '';
	s[1] += new Array(prec - s[1].length + 1).join('0');
}
return s.join(dec);
}
</script>
<?php require ('tpl/footer.php'); ?>