<?php require (theme_url().'/tpl/header.php'); ?>
<?php //echo "<pre>"; $arr = implode(",", $dupsemail);print_r( $arr);exit; ?>
<div id="content">
    <!--<div id="left-menu">
		<a href="customers/">Back To List</a>
        <a href="customers/add_customer/">Add New Customer</a>
    </div>-->
    <div class="inner">
    	<?php echo "$succcount Customer(s) Succesfully Uploaded"; ?>
	<?php if(!empty($dupsemail)){
	$dupsemail_str = implode('<br/>', $dupsemail); ?>
	<div><b>The Following Customers Not updated in Customer List, because below email ids are exits.</b></div>
	 <?php echo " The Customer Email is Already Exists in Customer List (s) <br /> $dupsemail_str ";
	}
	?> 
	
    <?php if(!empty($empty_error)){
    $empty_error_str = implode('<br/>', $empty_error); ?>
	<div><b>The Following Customers Not updated in Customer List, because </b></div>
	 <?php echo " The Customer First Name or Company or Region or Direct Phone or Email is missing <br /> $empty_error_str";  
	}
	?> 
	<?php if(!empty($invalidemail)){
	 $invalidemail_str = implode('<br/>', $invalidemail); ?>
	<div><b>The Following Customers Not updated in Customer List, because </b></div>
	 <?php echo " The Customer Email is invalid <br /> $invalidemail_str";  
	}
	?> 
	
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>