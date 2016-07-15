<?php
while(true):
	/*
	 * Kalenderliste erzeugen
	 */

	$result = $mysql->execute("SELECT * FROM `calendar` WHERE `versammlung` = ?", 's', $USER->vsid);
	
	if($result->num_rows == 0): //Beenden wenn kein Kalender angelegt ist	
		$nocal = true;
		break;
	endif;
	
	$cals = $result->fetch_all(MYSQLI_ASSOC);
	
	
	
	break;
endwhile;	
?>

<div class="field">
	<div class="headline"><?php displayText('common>calendar')?></div>
	<?php if(isset($nocal)): ?>
		<div class="morespace"><?php displayText('common>no_cal_applied')?></div>
	<?php else: ?>
	
		<select id="cal_calSelect">
			<?php foreach($cals AS $cal): ?>
				<option value="<?php echo $cal['cid']?>"><?php echo $cal['name']?></option>
			<?php endforeach;?>			
		</select>
		
	
	
	<?php endif;?>
	<div id="cal"></div>
	<script src="<?php printURL(); ?>/scripts/updateCal.js"></script>
</div>
