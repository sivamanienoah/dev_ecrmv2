<?php require ('tpl/header.php'); ?>

<div id="content">
    <!--<div id="left-menu">
		<a href="customers/">Back To List</a>
        <a href="customers/add_customer/">Add New Customer</a>
    </div>-->
    <div class="inner">
    	<?php echo "$succcount Customer(s) Succesfully Uploaded"; ?>
	<?php if(!empty($dupsemail)){?>
	<div><b>The Following Customers Not updated in Customer List, because </b></div>
	 <?php echo " The Customer Email is Already Exists in Customer List (s) <br /> $dupsemail ";
	}
	?> 
	
    <?php if(!empty($required)){?>
	<div><b>The Following Customers Not updated in Customer List, because </b></div>
	 <?php echo " The Customer First Name or Last Name or Company or Region or Direct Phone or Email is missing <br /> $required ";  
	}
	?> 
	<?php if(!empty($invalidemail)){?>
	<div><b>The Following Customers Not updated in Customer List, because </b></div>
	 <?php echo " The Customer Email is invalid <br /> $invalidemail ";  
	}
	?> 
	
</div>
<?php require ('tpl/footer.php'); ?>