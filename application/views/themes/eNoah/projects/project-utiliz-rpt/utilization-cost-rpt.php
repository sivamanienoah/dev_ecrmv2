<?php require (theme_url().'/tpl/header.php'); ?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
.ui-datepicker-calendar { display: none; }
.dept_section{ width:100%; float:left; margin:20px 0 0 0; }
.dept_section div{ width:49%; }
.dept_section div:first-child{ margin-right:2% }
table.bu-tbl th{ text-align:center; }
table.bu-tbl{ width:85%; }
table.bu-tbl-inr th{ text-align:center; }
</style>
<div id="content">
    <div class="inner">
        <?php if($this->session->userdata('viewPjt')==1) { ?>
		<?php $practice_arr = array(); ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			
			<div id="filter_section">
				<div id="advance_search" class="buttons">
					<form name="advanceFilterServiceDashboard" id="advanceFilterServiceDashboard" method="post">
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						<input type="hidden" name="filter" value="filter" />
						<div class="pull-left adv_filter_it_service">
							<div class='pull-left'>
								<label>From</label>
							</div>
							<div class='pull-left' id='fy_start_mon'>
								<input type="text" data-calendar="false" name="month_year_from_date" id="pc_month_year_from_date" class="textfield" value="<?php echo date('F Y',strtotime($start_date)); ?>" />
							</div>
							<div class='pull-left'>
								<label>To</label>
							</div>
							<div class='pull-left' id='fy_end_mon'>
								<input type="text" data-calendar="false" name="month_year_to_date" id="pc_month_year_to_date" class="textfield" value="<?php echo date('F Y',strtotime($end_date)); ?>" />
							</div>
							<div class='pull-left'>
								<span id='show_srch_btn'><input type="submit" class="positive input-font" name="advance" id="advance" value="Search"/></span>
								<span id='show_load_btn' style="display:none;"><img src="<?php echo base_url().'assets/images/loading.gif'; ?>" style="margin-left: 6px; width: 65px;"></span>
							</div>
						</div>
						<input type="hidden" id="filter_area_status" name="filter_area_status" value="" />
					</form>
				</div>				
			</div>
			
			<div class="buttons export-to-excel pull-right mrgin0">
				<button type="button" class="positive" id="btnExportITServices">
					Export to Excel
				</button>
			</div>
		</div>
		<?php #echo "<pre>"; print_r($practice_data); echo "</pre>"; ?>
		<div class="clearfix"></div>
		<div id="default_view">
			<?php 
				$data = array(); 
				$data['practice_data'] = $practice_data;
				$data['projects'] = $projects;
				$data['dashboard_det'] = $dashboard_det;
			?>
			<?php echo $this->load->view('projects/project-utiliz-rpt/utilization-cost-rpt-grid', $data, true); ?>
	
		</div>
		<div class="clearfix"></div>
		<div id="drilldown_data" class="" style="margin:20px 0;display:none;"></div>
		<?php 
		} else {
			echo "You have no rights to access this page";
		}
		?>
	</div>
</div>

<script type="text/javascript">
//For Advance Filters functionality.
$( "#advance_search" ).on( "click", "#advance", function(e) {
	e.preventDefault();
	var form_data = $('#advanceFilterServiceDashboard').serialize();	
	$.ajax({
		type: "POST",
		url: site_base_url+"projects/project_utilization_cost/",
		dataType: "html",
		data: form_data,
		beforeSend:function() {
			$('#show_srch_btn').hide();
			$('#show_load_btn').show();
			$('#default_view, #drilldown_data').empty();
			$('#default_view').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			// $('#advance').show();
			$('#default_view').html(res);
			$('#show_load_btn').hide();
			$('#show_srch_btn').show();
		}
	});
	return false;  //stop the actual form post !important!
});


function getData(practice, clicktype)
{	
	var form_data = $('#advanceFilterServiceDashboard').serialize();
	$.ajax({
		type: "POST",
		url: site_base_url+'projects/project_utilization_cost/project_uc_drill_data/',
		data: form_data+'&practice='+practice+'&clicktype='+clicktype,
		cache: false,
		beforeSend:function() {
			$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		},
		success: function(data) {
			$('#drilldown_data').html(data);
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		}                                                                                   
	});
}
$(function() {
	
	$( "#pc_month_year_from_date, #pc_month_year_to_date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		maxDate: 0,
		showButtonPanel: true,	
		onClose: function(dateText, inst) {
			var month 	= $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var year 	= $("#ui-datepicker-div .ui-datepicker-year :selected").val();         
			$(this).datepicker('setDate', new Date(year, month, 1));
		},
		beforeShow : function(input, inst) {
			$("#filter_area_status").val('1');
			if ((datestr = $(this).val()).length > 0) {
				year = datestr.substring(datestr.length-4, datestr.length);
				month = jQuery.inArray(datestr.substring(0, datestr.length-5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
				$(this).datepicker('setDate', new Date(year, month, 1));    
			}
				var other  = this.id  == "pc_month_year_from_date" ? "#pc_month_year_to_date" : "#pc_month_year_from_date";
				var option = this.id == "pc_month_year_from_date" ? "maxDate" : "minDate";        
			if ((selectedDate = $(other).val()).length > 0) {
				year = selectedDate.substring(selectedDate.length-4, selectedDate.length);
				month = jQuery.inArray(selectedDate.substring(0, selectedDate.length-5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker( "option", option, new Date(year, month, 1));
			}
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
});
</script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>