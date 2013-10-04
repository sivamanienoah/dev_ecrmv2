<div id="left-menu">
<?php
$loc = trim($this->uri->uri_string(), '/');
$submenu_items[] = array(
					'uri' => 'welcome/new_quote',
					'name' => 'New Quote',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/quotation',
					'name' => 'Drafts',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/quotation/list',
					'name' => 'Estimates',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/quotation/ongoing',
					'name' => 'Ongoing Quotations',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/quotation/quote',
					'name' => 'Quotations',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/quotation/pending',
					'name' => 'Quotes Pending Approval',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/quotation/idle',
					'name' => 'Idle Quotations',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/quotation/declined',
					'name' => 'Declined Quotations',
					'access' => '0:1:2:4:7'
				);
$submenu_items[] = array(
					'uri' => 'welcome/package/quotation',
					'name' => 'Quotation-Package',
					'access' => '0:1:2:4:7'
				);
foreach ($submenu_items as $mi) { 
	if ( $mi['access'] == 'all' || (isset($userdata) && strstr($mi['access'], $userdata['level'])) ) {
?>
	<a href="<?php echo  $mi['uri'] ?>"<?php echo  ($loc == $mi['uri']) ? ' class="selected"' : '' ?>><?php echo  $mi['name'] ?></a>
<?php
	}
}
?>
</div>