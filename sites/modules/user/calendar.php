<?php
	/*
	 * Kalenderliste erzeugen
	 */

	$result = $mysql->execute("SELECT * FROM `calendar` WHERE `versammlung` = ?", 's', $USER->vsid);
	
	if($result->num_rows == 0): //Beenden wenn kein Kalender angelegt ist	
		$nocal = true;
		break;
	endif;
	
	$cals = $result->fetch_all(MYSQLI_ASSOC);
	
	var_dump($cals);
?>

<div class="field">
	<div class="headline"><?php displayText('common>calendar')?></div>
	<select id="cal_calSelect">
		
	</select>
</div>
