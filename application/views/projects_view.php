<?php

$hostingid=array();
if(!empty($packages)) {
	
}
else {
	if(!empty($hosting)){
		foreach($hosting as $val){
			$v=$val['hostingid_fk'];$k=$val['jobid_fk'];
			$hostingid[$k]=$v;
		}
	}
}
if(!empty($packages)){
	$pack=array();
	foreach($packages as $val){
		$t=$val['package_id'];
		$pack[$t]=$val['package_name'];	
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
	if ($this->uri->segment(1) == 'invoice')
	{
		include ('tpl/invoice_submenu.php');
		$controller_uri = 'invoice';
	}
	elseif ($this->uri->segment(1) == 'subscription')
	{
		include ('tpl/subscription_submenu.php');
		$controller_uri = 'subscription';
	}
	elseif ($this->uri->segment(1) == 'production')
	{
		include ('tpl/production_submenu.php');
		$controller_uri = 'production/welcome';
	}
	else
	{
		include ('tpl/quotation_submenu.php');
		$controller_uri = 'welcome';
	}
	?>
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
						echo '<option value="0">All Packages</option>';
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
		
	    <h2><?php echo $page_heading ?></h2>
		
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
					<th width="50">Project Status</th>
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
	$('.project-cost-total').text('$' + number_format(the_totals, 2, '.', ','));
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
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
