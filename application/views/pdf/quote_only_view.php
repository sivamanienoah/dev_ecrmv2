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
	font-size:10px;
	margin-left:20px;
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
	width:550px;
	margin:135px 0px 165px 5px;
}

.q-quote-items td table {
	width:100%
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
			
			// Draw the box
			$color = Style::munge_colour('#000000');
			$pdf->line(20, 140, 20, 610, $color, 1);
			$pdf->line(20, 140, 590, 140, $color, 1);
			$pdf->line(590, 140, 590, 610, $color, 1);
			$pdf->line(20, 610, 590, 610, $color, 1);
			
			$pdf->image("<?php echo  dirname(FCPATH) ?>/assets/img/qlogo.jpg", "JPG", 20, 10, 137, 100);
			
			// All orang bold text
			$font = DOMPDF_FONT_DIR . "Helvetica-Bold";
			$size = 11.0;
			$color = Style::munge_colour('#FF6600');
			
			// Address titles
			$pdf->text(180, 10, "Call Australia-wide 1300 130 656", $font, $size, $color);
			$pdf->text(180, 30, "Sydney |", $font, $size, $color);
			$pdf->text(180, 83, "Melbourne |", $font, $size, $color);
			
			// All regular black text
			$color = Style::munge_colour('#000000');
			
			// All client quote titles
			$pdf->text(380, 10, "Date", $font, $size, $color);
			$pdf->text(380, 40, "Client", $font, $size, $color);
			$pdf->text(380, 70, "Email", $font, $size, $color);
			$pdf->text(380, 100, "Service", $font, $size, $color);
			
			// Footer policy title
			$pdf->text(30, 620, "Visiontech Payment Policy (effective date: July 1 2007)", $font, $size, $color);
			
			// Footer amount titles
			$pdf->text(20, 690, "Terms:", $font, $size, $color);
			$pdf->text(100, 690, "GST:", $font, $size, $color);
			$pdf->text(180, 690, "Sale Amount:", $font, $size, $color);
			$pdf->text(420, 690, "GST:", $font, $size, $color);
			$pdf->text(420, 712, "Total Inc GST:", $font, $size, $color);
			$pdf->text(420, 734, "Deposit Paid:", $font, $size, $color);
			$pdf->text(420, 756, "Balance Due:", $font, $size, $color);
			
			// All regular dark gray text
			$font = DOMPDF_FONT_DIR . "Helvetica";
			$size = 10.0;
			$color = Style::munge_colour('#333333');
			
			// Address details
			/*$pdf->text(180, 42, "Level 2, Suite 18/110 Botany Road", $font, $size, $color);
			$pdf->text(180, 52, "Alexandria NSW 2015", $font, $size, $color);
			$pdf->text(180, 62, "e: syd@visiontechdigital.com", $font, $size, $color);*/
			
			// Address details
			/*$pdf->text(180, 95, "Level 1, 530 Little Collins Street", $font, $size, $color);
			$pdf->text(180, 105, "Melbourne VIC 3000", $font, $size, $color);
			$pdf->text(180, 115, "e: melb@visiontechdigital.com", $font, $size, $color);*/
			
			// Client and quote details
			$pdf->text(380, 22, "<?php echo  (isset($quote_data)) ? date('d-m-Y', strtotime(substr($quote_data['date_created'], 0, 10))) : date('d-m-Y') ?>", $font, $size, $color);
			$pdf->text(380, 52, "<?php echo  (isset($quote_data)) ? str_replace('"', '\"', $quote_data['first_name'] . ' ' . $quote_data['last_name']) : '' ?>", $font, $size, $color);
			$pdf->text(380, 82, "<?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?>", $font, $size, $color);
			$pdf->text(380, 112, "<?php echo  (isset($quote_data)) ? $cfg['job_categories'][$quote_data['job_category']] : '' ?>", $font, $size, $color);
			
			// ABN Details
			#$pdf->text(20, 665, "visiontechdigital.com ABN: 80 093 904 299", $font, $size, $color);
			
			$size = 9.0;
			
			// Footer policy details
			$pdf->text(20, 640, "Orders under $1000 require upfront payment before production commences. Orders in excess of $1000 require a 50% upfront", $font, $size, $color);
			$pdf->text(20, 650, "payment deposit before production commences and remainder 50% prior to go-live with your website and/or releasing your final artwork.", $font, $size, $color);
			
			// Banking Details
			$bank_details = array(
								0 => "Please make all payments",
								1 => "via EFT to the following account:",
								2 => "Westpac Banking Corporation",
								3 => "Visiontech Solutions Pty Ltd",
								4 => "BSB: 032 159",
								5 => "ACC: 175239"
							);
			
			
			$pdf->text(280, 690, $bank_details[0], $font, $size, $color);
			$pdf->text(280, 700, $bank_details[1], $font, $size, $color);
			$pdf->text(280, 710, $bank_details[2], $font, $size, $color);
			$pdf->text(280, 720, $bank_details[3], $font, $size, $color);
			$pdf->text(280, 730, $bank_details[4], $font, $size, $color);
			$pdf->text(280, 740, $bank_details[5], $font, $size, $color);
			
			
			
			// Large orange text
			$font = DOMPDF_FONT_DIR . "Helvetica-Bold";
			$size = 16.0;
			$color = Style::munge_colour('#FF4400');
			
			// Status and invoice number
			$pdf->text(500, 10, "<?php echo  (isset($quote_data)) ? $cfg['job_status_label'][$quote_data['job_status']] : '' ?>", $font, $size, $color);
			$pdf->text(500, 30, "#<?php echo  (isset($quote_data)) ? $quote_data['invoice_no'] : '' ?>", $font, $size, $color);
			
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
			$color = Style::munge_colour('#000000');
			$size = 12.0;
			
			$gst_amount = '<?php echo  $gst_amount ?>';
			$gst_amount_width = Font_Metrics::get_text_width($gst_amount, $font, $size);
			
			$total_inc_gst = '<?php echo  $total_inc_gst ?>';
			$total_inc_gst_width = Font_Metrics::get_text_width($total_inc_gst, $font, $size);
			
			//bottom right
			$pdf->page_text(585 - $gst_amount_width, 690, $gst_amount, $font, $size, $color);
			$pdf->page_text(585 - $total_inc_gst_width, 712, $total_inc_gst, $font, $size, $color);
			
			$total_sale_amount = '<?php echo  $sale_amount ?>';
			
			//bottom
			$pdf->page_text(20, 712, 'Net 7', $font, $size, $color);
			$pdf->page_text(82, 712, $gst_amount, $font, $size, $color);
			$pdf->page_text(196, 712, $total_sale_amount, $font, $size, $color);
			
			// Close the object (stop capture)
			$pdf->close_object();
		  
			$pdf->add_object($bottom_line, "even");
			
			
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
