<?php require ('tpl/header.php'); ?>
<div id="content">
 
<div class="inner hosting-section">
	<h2>Billing Invoice</h2>
	<form action="hosting/billing/1" method=post name="">
	
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-table" id="dmaster">
		<thead>
		<tr>
			<th width=250>Customer</th>
			<th width=220>Domain Name</th>
			<th width=100>Domain Status</th>
			<th width=80>Package Names</th>
			<th width=60>Package Price</th>
			<th width=60>Total</th>
			<th width="60">Send/Value</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($customer as $ck=>$cv){
			$h_cnt=0;$p_arr=array();$p_cnt=0;$total='';$t_tot=array();
			foreach($cv as $t_hk=>$t_hv){	
				if(!is_numeric($t_hk)) continue;
				$t=0;
				if(!empty($pack_host[$t_hk])) { $p_arr[$t_hk]=count($pack_host[$t_hk]); $p_cnt+=count($pack_host[$t_hk]);
					foreach($pack_host[$t_hk] as $t_pk=>$t_pv){
						$total+=$package_arr[$t_pv]['package_price'];
						$t+=$package_arr[$t_pv]['package_price'].'  ';
					}
					$t_tot[$t_hk]=$t;
				}
				else {$p_cnt++; $p_arr[$t_hk]=1;}
			}
			if($p_cnt==0) $p_cnt=1;
			echo '<tr>';			
			echo '<td class="cust-data" rowspan="'.$p_cnt.'"><span style="color:#f70;"><a href="customers/add_customer/update/'.$ck.'" style="text-decoration:underline;">'.$cv['first_name']. ' ' .$cv['last_name'].'</a></span> - '.$cv['company'].'</td>';
			$m=0;$l=0;
			foreach($cv as $hk=>$hv){
				if(!is_numeric($hk)) continue;
				$m++;
				if(!empty($pack_host[$hk])) $PCNT=$p_arr[$hk];
				else $PCNT=1;
				$domain_status=$this->login_model->cfg['domain_status'][$hv['domain_status']];
				echo '<td rowspan="'.$PCNT.'"><a href="hosting/add_account/update/'.$hk.'">'.$hv['domain_name'].'</a></td>';
				echo '<td rowspan="'.$PCNT.'">'.$domain_status.'</td>';
				if(!empty($pack_host[$hk])) { 
					$n=0;
					foreach($pack_host[$hk] as $pk=>$pv){
						$n++;
						echo '<td>'.$package_arr[$pv]['package_name'].'</td>';
						echo '<td align=right>$'.$package_arr[$pv]['package_price'].'</td>';
						if($n==1) { echo '<td rowspan="'.$PCNT.'" align=right>$'.$t_tot[$hk].'</td>';}
						if($l==0) {echo '<td align=right rowspan="'.$p_cnt.'">$'.$total.'&nbsp;<input type=checkbox checked=checked></td>'; $l++;}
						if($n!=$p_arr[$hk]) echo '</tr><tr>';
					}
				}
				else echo '<td></td><td></td>';
				if($l==0 && $total>0) {echo '<td align=right rowspan="'.$p_cnt.'">$'.$total.'</td><td rowspan="'.$p_cnt.'">View</td>'; $l++;}
				else if($l==0) {echo '<td align=right rowspan="'.$p_cnt.'"></td><td rowspan="'.$p_cnt.'"></td>'; $l++;}
				if($l==0 && empty($pack_host[$hk]) ) echo '<td></td>';
				if(count($p_arr)!=$m)	echo '</tr><tr>';
			}
			echo '</tr>';
		}
		?>
		</tbody>
	</table>
</div>
</div>
<?php require ('tpl/footer.php'); ?>