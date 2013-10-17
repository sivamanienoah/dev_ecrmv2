<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
	<?php //include ('tpl/quotation_submenu.php') ?>
	<div class="inner">
	    <h2><?php echo  $page_heading ?></h2>
		
		<table border="0" cellpadding="0" cellspacing="0" class="data-table">
            
            <thead>
                <tr>
                    <th width="70">Quote No.</th>
                    <th width="230">Title</th>
                    <th>Customer</th>
                    <th width="110">Created On</th>
					<th width="100">Actions</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($records) && count($records) > 0) { ?>
                    <?php foreach ($records as $record) { ?>
                    <tr>
                        <td class="actions"><a href="welcome/view_quote/<?php echo  $record['jobid'] ?>"><?php echo  $record['invoice_no'] ?></a></td>
                        <td><img src="assets/img/<?php echo  ($record['belong_to'] == 'AT68') ? 'at68' : 'vt' ?>-icon.gif?q=8" /> <?php echo  $record['job_title'] ?><?php if ($record['invoice_downloaded'] == 1) { ?> <img src="assets/img/cab.gif" alt="Invoice Downloaded" /><?php } ?></td>
                        <td><?php echo  $record['first_name'] . ' ' . $record['last_name'] . ' - ' . $record['company'] ?> <span style="color:#f70;">( <a href="customers/add_customer/update/<?php echo  $record['custid'] ?>" style="text-decoration:underline;">client info</a> )</span></td>
                        <td><?php echo  substr($record['date_created'], 0, 16) ?></td>
						<td class="actions" align="center"><a href="welcome/view_quote/<?php echo  $record['jobid'] ?>">View</a>
						<?php echo (in_array($userdata['level'], array(0,1)) && $record['job_status'] < 4) ? ' | <a href="welcome/edit_quote/' . $record['jobid'] . '">Edit</a>' : '' ?>
						<?php
						$list_location = ($this->uri->segment(3)) ? '/' . $this->uri->segment(3) : '';
						echo (in_array($userdata['level'], array(0,1)) && $record['job_status'] < 4) ? ' | <a href="welcome/delete_quote/' . $record['jobid'] . $list_location . '" onclick="return window.confirm(\'Are you sure?\nThis will delete all the items\nand logs attached to this job.\');">Delete</a>' : '';
						echo (in_array($userdata['level'], array(5)) && $record['job_status'] > 3) ? ' | <a href="quotation/invoice_data_zip/' . $record['jobid'] . '">Get Invoice</a>' : '' ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
            
        </table>
		<!--div id="pager" class="pager">
			<form>
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td><a href="#" class="first">&laquo; first </a></td>
						<td><a href="#" class="prev">&lt; prev </a></td>
						<td><input type="text" class="pagedisplay textfield"/></td>
						<td><a href="#" class="next"> next &gt;</a></td>
						<td><a href="#" class="last">last &raquo;</a></td>
						<td><select class="pagesize textfield">
							<option selected="selected"  value="10">10</option>
							<option value="20">20</option>
							<option value="30">30</option>
							<option  value="40">40</option>
						</select></td>
					</tr>
				</table>
			</form>
		</div-->

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
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>