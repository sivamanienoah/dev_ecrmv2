<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ui-lightness/jquery-ui-1.7.2.custom.css?q=1" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/base.css?q=19" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.2.6-min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jq-ui-1.6b.min.js?q=2"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('input.pick-date').datepicker({dateFormat: 'dd-mm-yy'});
});
</script>
<style>
#content{background-color:#535353;font-family: Arial, Helvetica, sans-serif;height:100%;}
h4{color:#CCC;}
.layout td{font-size:11px;color:#CCC;}
.textfield {background: none repeat scroll 0 0 #CCCCCC;border: 1px solid #333333;color: #333333;font-family: Arial,Helvetica,sans-serif;font-size: 11px;   margin-bottom: 5px;padding: 4px;vertical-align: middle;}
.width180px{width:180px;}
.button {width: 70px;border: 1px solid #f3576e;border-bottom: 1px solid #710011;border-right: 1px solid #710011;background-color: #f60;font-size: 95%;font-weight: bold;color: #fff;}
.buttons a, .buttons button{display:block;float:left;margin:0 7px 0 0;background-color:#d8d8d8;border:1px solid #ccc;font-family:"Lucida Grande", Tahoma,Arial, Verdana, sans-serif;font-size:100%;line-height:130%;text-decoration:none;font-weight:bold;color:#565656;cursor:pointer;padding:5px 10px 6px 7px; /* Links */}
.buttons button{width:auto;overflow:visible;padding:4px 10px 3px 7px; /* IE6 */}
.buttons button[type]{padding:2px 6px 2px 6px; /* Firefox */line-height:17px; /* Safari */}
*:first-child+html button[type]{padding:4px 10px 3px 7px; /* IE7 */}
.buttons button img, .buttons a img{margin:0 3px 0px 0 !important;padding:0;border:none;width:13px;height:13px;}
button:hover, .buttons a:hover{background-color:#dff4ff;border:1px solid #c2e1ef;color:#336699;}
.buttons a:active{background-color:#6299c5;border:1px solid #6299c5;color:#fff;}
button.positive, .buttons a.positive{color:#529214;}
.buttons a.positive:hover, button.positive:hover{background-color:#E6EFC2;border:1px solid #C6D880;color:#529214;}
.buttons a.positive:active{background-color:#529214;border:1px solid #529214;color:#fff;}
.buttons a.negative, button.negative{color:#d12f19;}
.buttons a.negative:hover, button.negative:hover{background:#fbe3e4;border:1px solid #fbc2c4;color:#d12f19;}
.buttons a.negative:active{background-color:#d12f19;border:1px solid #d12f19;color:#fff;}
</style>
<body>
<div id="content">
<div class="inner">
<form action="<?php echo base_url(); ?>hosting/due_date/<?php echo $hostingid; ?>" method="post">
        
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

		<h4>Package Due Date</h4>
  		<table class="layout">
				<tr>
					<td>Domain Status:</td>
					<td>
						<select name="packageid" class="textfield width180px" onchange="self.location='<?php echo base_url(); ?>hosting/due_date/<?php echo $hostingid; ?>/'+this.value">
						<option value="" disabled="disabled" selected="selected">Select</option>
						<?php
							foreach ($pack as $key => $value) {
								($value['packageid_fk']==$packageid?$s=' selected="selected"':$s='');
								echo '<option value="'.$value['packageid_fk'].'"'.$s.'>'.$value['package_name'].'</option>';
							} ?>
						</select> *
					</td>
                </tr>
				<tr>
					<td>Hosting Due Date:</td>
					<?php
					$t='';
					foreach ($pack as $key => $value) {
						if($value['packageid_fk']!=$packageid) continue;
						if($value['due_date']=='0000-00-00') continue;
						if(strtotime($value['due_date'])==0) $t='';
						else $t=date('d-m-Y',strtotime($value['due_date']));
						
					}
					?>
					<td><input type="text" id="due_date" name="due_date" class="textfield width180px pick-date" value="<?php echo $t; ?>" autocomplete="off"/> </td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="Add_duedate" class="positive" value="edit">
								Update
							</button>
						</div>
                    </td>
    			</tr>
            </table>
		</form>
</div></div>
</body>