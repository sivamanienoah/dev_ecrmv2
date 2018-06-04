<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
?>

<div id="content">
	<div class="inner">
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Asset Dashboard</h2>
			

			<div class="section-right">
				<!--search-->
				<div class="form-cont search-table">
					<form id="asset_search_form" name="asset_search_form" method="post">
						<input type="text" name="keyword" id="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo '' ?>" class="textfield width200px g-search" />
						<button type="submit" class="positive">Asset Search</button>			
					</form>
				</div>
				<!--search-->
				<!--add-->
				<?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons add-new-button">
					<button onclick="location.href='<?php echo base_url(); ?>asset_register/new_asset'" class="positive" type="button">
						Add New Asset
					</button>
				</div>
				<?php } ?>
		
			</div>
		</div>
	
		<?php if($this->session->userdata('accesspage')==1) { ?>
			<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			</form>
		
			<div>			
				<div id="advance_search" style="float:left;width:100%;">
					<form name="advanceFilters" id="advanceFilters" method="post" style="overflow:auto; height:314px; width:100%;">
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						
					</form>
				</div>
				<div id="advance_search_results" style="clear:both" ></div>
			</div>
	<?php 
		} else {
			echo "You have no rights to access this page";
		}
	?>
	</div>
</div>
<div id='popupGetSearchName'></div>
<script>
var query_type = '<?php echo isset($load_proposal_expect_end) ? $load_proposal_expect_end : '' ?>';
$(function() {
	$('#from_date').datepicker({ 
		dateFormat: 'dd-mm-yy', 
		changeMonth: true, 
		changeYear: true, 
		onSelect: function(date) {
			if($('#to_date').val!='')
			{
				$('#to_date').val('');
			}
			var return_date = $('#from_date').val();
			$('#to_date').datepicker("option", "minDate", return_date);
		},
		beforeShow: function(input, inst) {
			/* if ((selDate = $(this).val()).length > 0) 
			{
				iYear = selDate.substring(selDate.length - 4, selDate.length);
				iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
				$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
			} */
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
	$('#to_date').datepicker({ 
		dateFormat: 'dd-mm-yy', 
		changeMonth: true, 
		changeYear: true,
		beforeShow: function(input, inst) { 
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
});
</script>
    <script type="text/javascript" src="assets/js/asset_register/quotation_view.js"></script>
<?php
require (theme_url().'/tpl/footer.php'); 
ob_end_flush();
?>