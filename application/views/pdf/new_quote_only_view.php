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
<link rel="stylesheet" href="assets/css/base.css" type="text/css" />
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
   
		<div class="q-quote-items">
			<table border="1" cellpadding="0" cellspacing="0" width="100%" >
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
