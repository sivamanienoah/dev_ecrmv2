
	<div class="inner">
	<?php if($this->session->userdata('accesspage')==1) {?>
	<table>
		<tbody>
		 <?php
		foreach($level_name as $levname =>$val){
			echo "<h3>".'Mapping Tree For'.' '.$val."</h3><br />";
		}	
		 
		echo " <tr><td><h5>Region Name</h5></td></tr>";
	  	
		foreach($region_name as $regname =>$val){
			echo "<tr><td>".$val."</td></tr>";
		}
		echo "<tr><td></td><td><h5>Country Name</h5></td></tr>";
	  	
		foreach($country_name as $countname =>$val){
			echo "<tr><td></td><td>".$val."</td></tr>";
		}
		echo "<tr><td></td><td></td><td><h5>State Name</h5></td></tr>";
	  	
		foreach($state_name as $statename =>$val){
			echo "<tr><td></td><td></td><td>".$val."</td></tr>";
		}
		echo "<tr><td></td><td></td><td></td><td><h5>Location Name</h5></td></tr>";
	  	
		foreach($location_name as $locname =>$val){
			echo "<tr><td></td><td></td><td></td><td>".$val."</td></tr>";
		}
		?>
		<tr>
		<td>
		<div class="buttons">
			<button type="submit" onclick="ndf_cancel();">Close</button>
		</div>		
		</td>
		</tr>
		</tbody>
	</table>
<?php } else echo "You have no rights to access this page"; ?>	
	</div>
