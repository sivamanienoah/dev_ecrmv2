<?php
ini_set('display_errors', 0);
error_reporting(0);
$cfg = $this->config->item('crm') ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo  $this->config->item('base_url'); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quotation View</title>
<!--link rel="stylesheet" href="assets/css/base.css" type="text/css" /-->
<style type="text/css">
body {
    background-color:#fff;
	font-family:Helvetica,sans-serif;
	font-size:8px;
	color:#666666;
	margin:0;
}
#content {
    background-color:#fff;
}
td {
    padding:3px;
	padding-bottom:0;
}
.q-quote-items {
	padding: 5px;
	width:444px;
	margin:165px 0px 340px 64px;
}
.q-quote-items td table {
	width:100%
}
.item-desc {
	width:360px;
}
</style>
</head>
<body>
<div id="content">
		<script type="text/php">
		if ( isset($pdf) ) {
			
			// Open the object: all drawing commands will
			// go to the object instead of the current page
			$header = $pdf->open_object();
			
			//$pdf->image("<?php echo  dirname(FCPATH) ?>/assets/img/qlogo.jpg", "JPG", 20, 10, 137, 100);
			$pdf->image("<?php echo  dirname(FCPATH) ?>/assets/pdf/syng/syng-head.jpg", "jpg", 0, 0, 594, 126);
			$pdf->image("<?php echo  dirname(FCPATH) ?>/assets/pdf/syng/syng-bottom.jpg", "jpg", 0, 640, 594, 200);
			$pdf->image("<?php echo  dirname(FCPATH) ?>/assets/pdf/syng/bg-liquid-payments.gif", "gif", 0, 529, 594, 111);
			<?php if (isset($activate_ruler)) { ?>
			//$pdf->image("<?php echo  dirname(FCPATH) ?>/assets/pdf/pixel-ruler.gif", "gif", 0, 0, 594, 840);
			<?php } ?>
			//$pdf->image("<?php echo  dirname(FCPATH) ?>/assets/pdf/a4-grid.gif", "gif", 0, 0, 594, 840);
			
			// All orang bold text
			$font = DOMPDF_FONT_DIR . "Helvetica-Bold";
			$size = 8.0;
			$color = Style::munge_colour('#535353');
			
			// All client quote titles
			$pdf->text(377, 49, "Date", $font, $size, $color);
			$pdf->text(377, 61, "Company", $font, $size, $color);
			$pdf->text(377, 73, "Contact", $font, $size, $color);
			$pdf->text(377, 85, "Email", $font, $size, $color);
			$pdf->text(377, 97, "Service", $font, $size, $color);
			
			// All regular dark gray text
			$font = DOMPDF_FONT_DIR . "Helvetica";
			$size = 8.0;
			$color = Style::munge_colour('#535353');
			
			<?php
			$date_used = $quote_data['date_created'];
			if (in_array($quote_data['job_status'], array(4, 5, 6, 7, 8)) && $quote_data['date_invoiced'] != '')
			{
				$date_used = $quote_data['date_invoiced'];
			}
			?>
			
			// Client and quote details
			$pdf->text(418, 49, "<?php echo  (isset($quote_data)) ? date('d-m-Y', strtotime($date_used)) : date('d-m-Y') ?>", $font, $size, $color);
			$pdf->text(418, 61, "<?php echo  (isset($quote_data)) ? $quote_data['company'] : '' ?>", $font, $size, $color);
			$pdf->text(418, 73, "<?php echo  (isset($quote_data)) ? str_replace('"', '\"', $quote_data['first_name'] . ' ' . $quote_data['last_name']) : '' ?>", $font, $size, $color);
			$pdf->text(418, 85, "<?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?>", $font, $size, $color);
			$pdf->text(418, 97, "<?php echo  (isset($quote_data)) ? $cfg['job_categories'][$quote_data['job_category']] : '' ?>", $font, $size, $color);
			
			
			// Large orange text
			$font = DOMPDF_FONT_DIR . "Helvetica-Bold";
			$size = 15.0;
			$color = Style::munge_colour('#535353');
			
			// Status and invoice number
			$pdf->text(377, 21, "<?php echo  (isset($quote_data)) ? $cfg['job_status_label'][$quote_data['job_status']] : '' ?>", $font, $size, $color);
			$pdf->text(472, 21, "<?php echo  (isset($quote_data)) ? ' #' . $quote_data['invoice_no'] : '' ?>", $font, $size, $color);
			
			
			// Close the object (stop capture)
			$pdf->close_object();
		  
			// Add the object to every page. You can
			// also specify "odd" or "even"
			$pdf->add_object($header, "all");
			
			/**
			 * If last page
			 * we write the bottom line values
			 */
			
			// Open the object: all drawing commands will
			// go to the object instead of the current page
			$bottom_line = $pdf->open_object();
			
			$font = DOMPDF_FONT_DIR . "Helvetica";
			$color = Style::munge_colour('#777777');
			$size = 8.0;
			
			$gst_amount = '<?php echo  $gst_amount ?>';
			$gst_amount_width = Font_Metrics::get_text_width($gst_amount, $font, $size);
			$pdf->page_text(172 - $gst_amount_width, 613, $gst_amount, $font, $size, $color);
			
			$total_sale_amount = '<?php echo  $sale_amount ?>';
			$total_sale_amount_width = Font_Metrics::get_text_width($total_sale_amount, $font, $size);
			$pdf->page_text(172 - $total_sale_amount_width, 600, $total_sale_amount, $font, $size, $color);
			
			$deposits = '$<?php echo  $deposits ?>';
			$deposits_width = Font_Metrics::get_text_width($deposits, $font, $size);
			$pdf->page_text(332 - $deposits_width, 613, $deposits, $font, $size, $color);
			
			$total_inc_gst = '<?php echo  $total_inc_gst ?>';
			$total_inc_gst_width = Font_Metrics::get_text_width($total_inc_gst, $font, $size);
			$pdf->page_text(332 - $total_inc_gst_width, 600, $total_inc_gst, $font, $size, $color);
			
			$font = DOMPDF_FONT_DIR . "Helvetica-Bold";
			$color = Style::munge_colour('#777777');
			$size = 10.0;
			
			$balance = '$<?php echo  $balance ?>';
			$balance_width = Font_Metrics::get_text_width($balance, $font, $size);
			$pdf->page_text(510 - $balance_width, 597, $balance, $font, $size, $color);
			
			$color = Style::munge_colour('#777777');
			
			$balance_due = '$<?php echo  (isset($balance_due)) ? $balance_due : $balance ?>';
			$balance_due_width = Font_Metrics::get_text_width($balance_due, $font, $size);
			$pdf->page_text(510 - $balance_due_width, 612, $balance_due, $font, $size, $color);
			
			$project_name = 'Project Name : <?php echo str_replace("'", "\'", $quote_data['job_title']) ?>';
			$pdf->text(70, 145, $project_name, $font, $size, $color);
			
			
			$font = DOMPDF_FONT_DIR . "Helvetica";
			$color = Style::munge_colour('#777777');
			$size = 8.0;
			
			/**
			 * add page numbers
			 * The logic is in dompdf/include/cpdf_adapter.cls.php
			 * line 728
			 */
			$pagination = 'Page {PAGE_NUM} of {PAGE_COUNT}';
			$pagination_width = Font_Metrics::get_text_width($pagination, $font, $size);
			$pdf->page_text(510 - $pagination_width, 149, $pagination, $font, $size, $color);
			
			/**
			 * add continued if not last page
			 * The logic is in dompdf/include/cpdf_adapter.cls.php
			 * line 734 onwards
			 */
			$pdf->page_text(72, 510, '__continued__', $font, $size, $color);
			
			// Close the object (stop capture)
			$pdf->close_object();
		  
			$pdf->add_object($bottom_line, "all");
			
			
		}
		</script>
		
        
		<div class="q-quote-items">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<?php
				$search = array(
								'<td><table cellpadding="0" cellspacing="0" class="quote-item width565px"><tr>',
								'</tr></table></td>'
							   );
				$replace = array('', '');
				echo str_replace($search, $replace, $quote_items);
				?>
				
			</table>
		</div>
    
</div>

</body>
</html>
