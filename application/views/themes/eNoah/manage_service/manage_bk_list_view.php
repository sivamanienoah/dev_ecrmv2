<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<div class="page-title-head">
		<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<?php if($this->session->userdata('add')==1) { ?>
				<div class="section-right">
					<div class="buttons add-new-button">
						<button type="button" class="positive" onclick="add_bk_value()">
							Add New Book Keeping Values
						</button>
					</div>
				</div>
			<?php } ?>
		<div class="clearfix"></div>
	</div>
	<?php $curr_id = array(); #echo "<pre>"; print_r($currencies); exit; ?>
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th style="text-align: center;" rowspan=2>Financial<br> Year</th>
				<th style="text-align: center;" rowspan=2>Base <br>Currency</th>
				<th style="text-align: center;" colspan="<?php echo count($currencies) ?>">Book Keeping Currency Values</th>
				<th style="text-align: center;" rowspan=2>Action</th>
			</tr>
			<tr>
				<?php 
				foreach($currencies as $curre) {
				?>
				<?php $curr_id[$curre['expect_worth_id']] = $curre['expect_worth_name']; ?>
				<th><?php echo $curre['expect_worth_name'] ?> </th>
				<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php krsort($currency_rec); ?>
			<?php foreach($currency_rec as $curr_year=>$to_cur) { ?>
				<?php foreach($to_cur as $from_cur=>$cur_value) { ?>
				<tr>
					<td><?php echo ($curr_year-1). ' - ' .$curr_year;  ?></td>
					<td><?php echo $curr_id[$from_cur] ?> </td>
					<?php foreach($currencies as $cur_id) { ?>
					<td><?php echo $to_cur[$from_cur][$cur_id['expect_worth_id']] ?></td>
					<?php } ?>
					<td>
						<?php if($this->session->userdata('edit')==1) { ?>
							<a title='Edit' onclick="return editCurValue('<?php echo $curr_year ?>','<?php echo $from_cur ?>')"><img src="assets/img/edit.png" alt='Edit'></a>
						<?php } ?>
						<?php if($this->session->userdata('delete')==1) { ?>
							<a title='Delete' onclick="return deleteCurValue('<?php echo $curr_year ?>','<?php echo $from_cur ?>')"><img src="assets/img/trash.png" alt='Delete'></a>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	<?php 
	} else { 
		echo "You have no rights to access this page"; 
	} 
	?>
	</div><!--Inner div-close here -->
</div><!--Content div-close here -->
<div id="edit_currency_container"></div>
<div id="add_currency_container"></div>
<!--script type="text/javascript" src="assets/js/data-tbl.js"></script-->
<?php 
// $no_cur = (!empty($currencies)) ? count($currencies) : '1'; 
// $tot_columns = count($currencies) + 2;
// $aocolumns[0] = '{"sWidth":"8%"}';
// $aocolumns[1] = '{"sWidth":"8%"}';
// foreach($c=2;$c<$tot_columns;$c++){
  // $aocolumns[$c] = '{"sWidth":"7%"}';
// }
// $aocolumns[$c] = '{"sWidth":"10%"}';
// echo "<pre>"; print_r($aocolumns); echo "</pre>";
?>
<script>
// var no_cur = '<?php echo $no_cur ?>';
</script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/manage_service/manage_bk_list_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>