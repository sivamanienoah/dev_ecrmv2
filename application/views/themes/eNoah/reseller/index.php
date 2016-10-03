<?php
ob_start();
require (theme_url().'/tpl/header.php');
?>
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th>S.No</th>
				<th>Reseller Name</th>				
				<th>Active Leads</th>
				<th>Active Projects</th>
				<th>Agreement Start Date</th>
				<th>Agreement End Date</th>
				<th>Contract Manager</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; $sno = 1; ?>
			<?php if(is_array($reseller) && !empty($reseller) && count($reseller)>0) { ?>			
				<?php foreach($reseller as $row) {  ?>
					<tr>
						<td class="actions">
							<?php echo $sno; ?>
							<!--a href="reseller/edit_reseller/update/<?php #echo $row['userid'] ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'> </a--> 
						</td>
						<td>
							<?php 
								$reseller_name = $row['first_name'];
								if(!empty($row['last_name'])){
									$reseller_name .= " ". $row['last_name'];
								}
							?>
							<a href="reseller/view_reseller/update/<?php echo $row['userid'] ?>" title='View Reseller'><?php echo $reseller_name; ?></a>
						</td>
						<td><?php $get_resller_leads = getResellerActiveLeads($row['userid']); echo isset($get_resller_leads) ? $get_resller_leads : ""; ?></td>
						<td><?php $get_resller_projects = getResellerActiveProjects($row['userid']); echo isset($get_resller_projects) ? $get_resller_projects : ""; ?></td>
						<td><?php  ?></td>
						<td><?php  ?></td>
						<td><?php $get_contract_manager_name = getContractManagerName($row['contract_manager']); echo isset($get_contract_manager_name) ? $get_contract_manager_name : ""; ?></td>
					</tr>
				<?php $i++; $sno++; } ?>
			<?php } ?>
		</tbody>
	</table>
	<?php } else { 
		echo "You have no rights to access this page"; 
	} 
	?>
	</div><!--/Inner div -->
</div><!--/Content div -->
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>