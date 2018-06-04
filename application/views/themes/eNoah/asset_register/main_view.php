<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
?>

<div id="content">
	<div class="inner">
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Asset Dashboard</h2>
			
<!--			<a class="choice-box" onclick="advanced_filter();" >
				<span>Advanced Filters</span>
				<img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>-->

<!--			<div class="search-dropdown">
				<a class="saved-search-head">
					<p>Saved Search</p>
				</a>
				<div class="saved-search-criteria" style="display: none; ">
					<img class="dpwn-arw" src="assets/img/drop-down-arrow.png" title="" alt="" />
					<ul class="search-root">
					<li class="save-search-heading"><span>Search Name</span><span>Set Default</span><span>Action</span></li>
					<?php 
//					if(sizeof($saved_search)>0) {
//						foreach($saved_search as $searc) { 
//					?>
							<li class="saved-search-res" id="item_//<?php echo $searc['search_id']; ?>">
								<span><a href="javascript:void(0)" onclick="show_search_results('//<?php echo $searc['search_id'] ?>')"><?php echo $searc['search_name'] ?></a></span>
								<span class='rd-set-default'><input type="radio" value="//<?php echo $searc['search_id'] ?>" <?php if ($searc['is_default']==1) { echo "checked"; } ?> name="set_default_search" class="set_default_search" /></span>
								<span><a title="Delete" href="javascript:void(0)" onclick="delete_save_search('//<?php echo $searc['search_id'] ?>')"><img alt="delete" src="assets/img/trash.png"></a></span>
							</li>
					//<?php 
//						}
//					} else {
//					?>
						<li id="no_record" style="text-align: center; margin: 5px;">No Save & search found</li>
					//<?php
//					}
//					?>
					</ul>
				</div>
			</div>-->

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
				<!--add-->
				<!--export-->
<!--				<div class="buttons export-to-excel">
					a class="export-btn">Export to Excel</a
					<button id="excel_lead" class="positive" type="button" >
						Export to Excel
					</button>
					<input type="hidden" name="search_type" value="" id="search_type" />
				</div>-->
				<!--export-->
			</div>
		</div>
	
		<?php if($this->session->userdata('accesspage')==1) { ?>
			<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			</form>
		
			<div>			
				
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