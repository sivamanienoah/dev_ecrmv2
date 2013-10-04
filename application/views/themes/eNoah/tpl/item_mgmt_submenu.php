	<div id="left-menu">
		<?php
		$loc = trim($this->uri->uri_string(), '/');
        $submenu_items[] = array(
                            'uri' => 'item_mgmt/',
                            'name' => 'Additional Items',
                            'access' => '0:1:2:4:7'
						);
		$submenu_items[] = array(
                            'uri' => 'item_mgmt/add/',
                            'name' => 'Add New Item',
                            'access' => '0:1'
						);
		$submenu_items[] = array(
                            'uri' => 'item_mgmt/category_list',
                            'name' => 'Item Categories',
                            'access' => '0:1'
						);
		$submenu_items[] = array(
                            'uri' => 'item_mgmt/category/',
                            'name' => 'Add Item Category',
                            'access' => '0:1'
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
