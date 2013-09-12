<div class="q-container">
	<div class="q-details">
		<div class="q-cust">
			<p class="q-date">Date: <span><?php echo  date('Y-m-d') ?></span></p>
			<p class="q-cust-name">ATT: <span></span></p>
			<p class="q-cust-email">Email: <span></span></p>
			<p class="q-service-type">Service: <span></span></p>
		</div>
		<p><img src="assets/img/qlogo.jpg" alt="" /></p>
		<h3 class="q-id">Quotation <span>#</span></h3>
		<h4 class="q-title"></h4>
		<p class="q-desc"></p>
		<div class="q-quote-items">
			<ul id="q-sort-items"></ul>
		</div>
	</div>
</div>
<div class="q-sub-total">
	<table class="width565px" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>Sale Amount <span id="sale_amount"></span></td>
			<td>GST <span id="gst_amount"></span></td>
			<td>&nbsp;</td>
			<td align="right">Total inc GST <span id="total_inc_gst"></span></td>
		</tr>
	</table>
</div>