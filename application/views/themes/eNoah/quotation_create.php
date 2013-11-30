<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
	<script type="text/javascript" src="assets/js/j-tip.js?q=1"></script>
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
	h4 {
		margin-bottom: 8px;
	}
	</style>
	<?php
	if ($this->uri->segment(1) == 'invoice')
	{
		// include ('tpl/invoice_submenu.php');
		$controller_uri = 'invoice';
	}
	else
	{
		// include ('tpl/quotation_submenu.php');
		$controller_uri = 'welcome';
	}
	?>
	<div class="inner">
		<?php if($this->session->userdata('add')==1) { ?>
		<form action="request" method="post">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td>
                        Search by Job Title, Name or Company Name
                    </td>
                    <td>
                        <input type="text" name="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword'] ?>" class="textfield width200px" />
                    </td>
                    <td>
                        <div class="buttons">
                            <button type="submit" class="positive">
                                
                                Search
                            </button>
                        </div>
                    </td>
                </tr>
            </table>
		</form>
		
		<?php
		if (isset($results) && is_array($results) && count($results))
		{
			foreach ($results as $s_k => $s_v)
			{
				?>
			<h4><?php echo  $cfg['lead_stage'][$s_k] ?></h4>
			
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
				
				<thead>
					<tr>
						<th width="70">Quote No.</th>
						<th width="300">Title</th>
						<th>Customer</th>
						<th width="105">Created On</th>
						<th width="100">Actions</th>
					</tr>
				</thead>
				
				<tbody>
					<?php if (is_array($s_v) && count($s_v) > 0) { ?>
						<?php foreach ($s_v as $record) { ?>
						<tr>
							<td class="actions">
								<a href="<?php echo  $controller_uri ?>/view_quote/<?php echo  $record['lead_id'] ?>"><?php echo  $record['invoice_no'] ?></a> &nbsp;
								<a class="jTip" id="jt-link-<?php echo  $record['lead_id'] ?>" href="ajax/request/logs/<?php echo  $record['lead_id'] ?>" title="Available Logs"><img src="assets/img/logs.gif" alt="Logs" /></a>
							</td>
							<td>
							<?php
							if (is_file(dirname(FCPATH) . '/assets/img/sales/' . $record['belong_to'] . '.jpg'))
							{
							?>
								<img src="assets/img/sales/<?php echo $record['belong_to'] ?>.jpg" title="<?php echo $record['belong_to'] ?>" />
							<?php
							}
							?>
							<?php echo $record['lead_title'] ?><img src="assets/img/cab.gif" alt="Invoice Downloaded" /></td>
							<td>
								<?php echo  $record['first_name'] . ' ' . $record['last_name'] . ' - ' . $record['company'] ?> <span style="color:#f70;">( <?php if($this->session->userdata('edit')==1) { ?><a href="customers/add_customer/update/<?php echo  $record['custid'] ?>" style="text-decoration:underline;">client info</a><?php } else echo "client info"; ?> )</span>
							</td>
							<td>
								<?php echo  date('d-m-Y H:i', strtotime($record['date_created'])) ?>
							</td>
							<td class="actions" align="center">
								<a href="<?php echo  $controller_uri ?>/view_quote/<?php echo  $record['lead_id'] ?>">View</a>
								<?php echo (in_array($userdata['level'], array(0,1)) && $record['invoice_downloaded'] != 1) ? ' | <a href="welcome/edit_quote/' . $record['lead_id'] . '">Edit</a>' : '' ?>
								<?php
								$list_location = ($this->uri->segment(3)) ? '/' . $this->uri->segment(3) : '';
								echo (($this->session->userdata('delete')==1)) ? ' | <a href="welcome/delete_quote/' . $record['lead_id'] . $list_location . '" onclick="return window.confirm(\'Are you sure?\nThis will delete all the items\nand logs attached to this job.\');">Delete</a>' : ''; ?>
							</td>
						</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="5" align="center">No records available to be displayed!</td>
						</tr>
					<?php } ?>
				</tbody>
				
			</table>
			<p>&nbsp;</p>
				<?php
			}
		} else {
		?>
		<h4>No results found!</h4>
		<?php
		}
		?>
<?php } else { echo "You are not authrorized to view this page."; } ?>
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
