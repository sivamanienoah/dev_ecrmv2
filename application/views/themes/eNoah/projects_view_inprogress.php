<?php
	
	//echo "<pre>"; print_r($pjts_data); exit
?>
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
		
		<form name="project-total-form" onsubmit="return false;" style="clear:right; overflow:visible;">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<?php //echo"<pre>"; print_r($data); ?>
		<table border="0" cellpadding="0" cellspacing="0" style="width:1200px !important;" class="data-table">
            <?php $controller_uri = 'invoice'; ?>
            <thead>
                <tr>
                    <th width="50">Project No.</th>
                    <th width="70">Project ID</th>
                    <th width="120">Project Title</th>
                    <th width="120" class="cust-data">Customer</th>
					<th width="120">Project Manager</th>
					<th width="60">Planned Start Date</th>
					<th width="60">Planned End Date</th>
					<th width="60">Action</th>
					<th width="50">Project Status</th>
					<?php

						/*echo <<< EOD
					<th width="70" style="text-align:right;">Value <input type="checkbox" class="project-cost-toggle" checked="checked" /></th>
EOD;
	*/				?>
                </tr>
            </thead>
            
            <tbody>
				<?php
					if (is_array($pjts_data) && count($pjts_data) > 0) { ?>
                    <?php
					foreach ($pjts_data as $record) {
						?>
                    <tr>
                        <td class="actions">
							<div>
							
								<a style="color:#A51E04; text-decoration:none;" href="<?php echo  $controller_uri ?>/view_project/<?php echo  $record['jobid'], '/', $quote_section ?>"><?php echo  $record['invoice_no'] ?></a>
							</div>
						</td>
						<td class="actions">
							<?php if (isset ($record['pjt_id'])) { echo $record['pjt_id']; } else { echo "-"; } ?>
							<!--<a href="<?php #echo $controller_uri ?>/view_project/<?php #echo $record['jobid'], '/', $quote_section ?>" title="<?php #echo $record['pjt_id'] ?>"> <?php #if (isset ($record['pjt_id'])) { echo $record['pjt_id']; } else { ?> </a> 
							<?php #echo "-"; } ?>-->
						</td>
                        <td class="actions">
							<?php echo character_limiter($record['job_title'], 35) ?>
							<!--<a href="<?php #echo  $controller_uri ?>/view_project/<?php #echo  $record['jobid'], '/', $quote_section ?>" title="<?php #echo $record['job_title'] ?>"><?php #echo character_limiter($record['job_title'], 35) ?></a>-->
						</td>
						
                        <td class="cust-data">
							<span>
								<?php echo $record['cfname'] . ' ' . $record['clname']; ?>
							</span> 
							<?php echo " - " . $record['company'] ?>
						</td>
						
						<td class="cust-data">
							<?php echo $record['fnm'] . ' ' . $record['lnm']; ?>
						</td>
						
						<td><?php if ($record['date_start'] == "") { echo "-"; } else { echo  date('d-m-Y', strtotime($record['date_start'])); } ?></td>
						<td><?php if ($record['date_due'] == "") echo "-"; else echo  date('d-m-Y', strtotime($record['date_due'])) ?></td>
						<td class="actions" align="center"><a href="<?php echo  $controller_uri ?>/view_project/<?php echo  $record['jobid'], '/', $quote_section ?>">View</a>
						<?php
						echo ($this->session->userdata('deletePjt') == 1) ? ' | <a href="welcome/delete_quote/' . $record['jobid'] . $list_location . '" onclick="return window.confirm(\'Are you sure you want to delete\n' . str_replace("'", "\'", $record['job_title']) . '?\n\nThis will delete all the items\nand logs attached to this job.\');">Delete</a>' : '';
						?>
						</td>
						<td class="actions" align="center"><?php if (isset($record['complete_status'])) echo ($record['complete_status']) . " %"; else echo "-"; ?></td>
						<?php
						/*
							$formatted_cost = '$' . number_format($record['project_cost'], 2, '.', ',');
							echo <<< EOD
							<td class="project-cost" align="right">{$formatted_cost}
							<span class="display-none">{$record['project_cost']} </span>
							<em class="display-none">{$record['deposits']}</em>
							<input type="checkbox" class="project-item-cost-toggle" checked="checked" /></td>
EOD;
*/
						?>
						</tr>
						<?php
					} 

						/*echo <<< EOD
					<tr>
						<td colspan="5" align="right">&nbsp;&nbsp; Total</td>
						<td class="project-cost-total" align="right"></td>
					</tr>
EOD;*/
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
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']});
 $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
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

