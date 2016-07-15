<?php
if(!isset($fromIndex)) exit;

require_once 'libs/calendar.php';

$result = $mysql->execute("SELECT * FROM `calendar` WHERE `cid` = ? AND `versammlung` = ? LIMIT 1", 'ss',
							array($_POST['cid'], $USER->vsid));
if($result->num_rows != 1):
	?><div class="error smallspace"><?php displayText('errors>noperm');?></div><?php
	exit;
endif;

/*
 * Anzuzeigenden Monat festlegen
 * Entweder aktueller(bei neuer Kalenderauswahl, sonst per Post übergebener Kalender
 */

if(!isset($_POST['month'])) $month = intval(date("n"));
else $month = intval($_POST['month']);

/*
 * Dasselbe mit Jahr
 */

if(!isset($_POST['year'])) $year = intval(date("Y"));
else $year = intval($_POST['year']);

/*
 * Tage im Monat auslesen
 */

$maxdays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

/*
 * Monatsraster erstellen
 */

$DayCounter = 1;
$DayOfWeekC = 1;
$WeekC = 1;

?>

<div id="calendar">
	<table>
		<tr>
			<th><?php displayText('common>monday')?></th>
			<th><?php displayText('common>tuesday')?></th>
			<th><?php displayText('common>wednesday')?></th>
			<th><?php displayText('common>thursday')?></th>
			<th><?php displayText('common>friday')?></th>
			<th><?php displayText('common>saturday')?></th>
			<th><?php displayText('common>sunday')?></th>
		</tr>
		<?php 
		while($DayCounter <= $maxdays):
			if($DayOfWeekC == 1): ?><tr><?php endif;
			
			$id = $DayOfWeekC."_".$WeekC;
			$class = "relative";
			$WochenTagFromDate = intval(date("N", strtotime($DayCounter.".".$month.".".$year)));
			
			if($WochenTagFromDate != $DayOfWeekC) $class .= " dgrey";
			
			?>
			<td id="<?php echo $id; ?>" class="<?php echo $class; ?>"></td>
			
			<?php 
			if($WochenTagFromDate == $DayOfWeekC):
			//TODO
			endif;
			?>
			
			<?php if($DayOfWeekC == 6): ?></tr><?php endif;?>
			<?php 
		endwhile;
		
		?>
		
	</table>
</div>

