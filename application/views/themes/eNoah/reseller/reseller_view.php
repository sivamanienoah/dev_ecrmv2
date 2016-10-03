<?php
ob_start();
require (theme_url().'/tpl/header.php');
?>
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
		<?php if(is_array($reseller_det) && !empty($reseller_det) && count($reseller_det)>0) { ?>
			<p>
				<label>Reseller Name :</label>
				<?php
					$reseller_name = $reseller_det[0]['first_name'];
					if(!empty($reseller_det[0]['last_name'])){
						$reseller_name .= " ". $reseller_det[0]['last_name'];
					}
					echo $reseller_name;
				?>
			</p>
			
			<p>
				<label>Contract Manager Name :</label>
				<?php
					$get_contract_manager_name = getContractManagerName($reseller_det[0]['contract_manager']);
					echo isset($get_contract_manager_name) ? $get_contract_manager_name : "";
				?>
			</p>
		<?php } ?> <!--If condition - end-->
		
		<!-- Tabs --->
		<div id="reseller_tabs" style="width:99.5%;float:left;margin:10px 0 0 0;">
			<div>
				<ul id="reseller_view_tabs">
					<li><a href="<?php echo current_url() ?>#rt-tab-1">Contracts</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-2">Commission History</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-3">Sales History</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-4">Leads</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-5">Projects</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-6">Contacts</a></li>
					<li><a href="<?php echo current_url() ?>#rt-tab-7">Audit History</a></li>
				</ul>
			</div>
			<div id="rt-tab-1"></div>
			<div id="rt-tab-2"></div>
			<div id="rt-tab-3"></div>
			<div id="rt-tab-4"></div>
			<div id="rt-tab-5"></div>
			<div id="rt-tab-6"></div>
			<div id="rt-tab-7"></div>
		</div><!--reseller_tabs-end-->
	<?php } else { 
		echo "You have no rights to access this page"; 
	} 
	?>
	</div><!--/Inner div -->
</div><!--/Content div -->
<script type="text/javascript" src="assets/js/reseller/reseller_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>