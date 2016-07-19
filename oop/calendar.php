<?php
if(!isset($fromIndex)) exit;

class calendar {
	
	private $cdata, $meta, $iTimes, $iPosts;
	
	function __construct() {
		global $_POST, $USER, $mysql;
		
		$result = $mysql->execute("SELECT * FROM `calendar` WHERE `cid` = ? AND `versammlung` = ? LIMIT 1", 'ss',
				array($_POST['cid'], $USER->vsid));
		
		$this->cdata = $result->fetch_assoc();
		
		$this->meta = json_decode($this->cdata['meta'], true);
		if($this->meta == NULL) $this->meta = array();
		unset($this->cdata['meta']);
		var_dump($this->meta);
	}

	private function makeSmallCal($month, $year, $sel) {
		
		$daysToCreate = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$daysCreate = 1;
		$cDayinWeek = 1;
		
		?>
		<div class="center floatleft" style="margin: 5px;"> <span class="ffheight"><?php displayText('common>'.strtolower(date("M", strtotime("1.".$month.".".$year))))?> <?php echo $year; ?></span>
			<table>
	
		<?php 
		while(true):
		
			if($daysCreate > $daysToCreate && $cDayinWeek == 1) break;
		
			if($daysCreate <= $daysToCreate): //Muss abgefragt werden, weil sonst er immer einen Tag mehr ausgibt
			
				/*
				 * Parameter für Anzeige des Monats erstellen
				 */
			
				$trparam = "";
				
				if($cDayinWeek == 1): //min < sel > max
					
					/*
					 * Montag aus sel ermitteln
					 */
					$min = null;
				
					if(date("N", $sel) == "1"): //Falls sel = montag
						$min = strtotime(date("j.n.Y", $sel));
					else:
						$tmpsel = $sel;
						while($min == null):
							if(date("N", $tmpsel) == "1"):
								$min = strtotime(date("j.n.Y", $tmpsel));
								break;
							endif;
							$tmpsel = $tmpsel - (60*60*24);
													
						endwhile;					
					endif;
					
					/*
					 * Sonntag aus sel ermitteln
					 */
					
					$max = null;
						
					if(date("N", $sel) == "7"): //Falls sel = montag
						$max = strtotime(date("j.n.Y", $sel));
					else:
						$tmpsel = $sel;
						while($max == null):
							if(date("N", $tmpsel) == "7"):
								$max = strtotime(date("j.n.Y", $tmpsel));
								break;
							endif;
							$tmpsel = $tmpsel + (60*60*24);
							
						endwhile;
					endif;
					
					$cstamp = strtotime($daysCreate.".".$month.".".$year);
					
					if($cstamp >= $min && $cstamp <= $max) $trparam = "sel";
					
				endif;
				
			?>
				<?php if($cDayinWeek == 1):?><tr style="cursor: pointer" onclick="$('#cal').load('/ajax/updateCal', {cid: '<?php echo $this->cdata['cid'];?>', csel: '<?php echo $cstamp; ?>'});" class="<?php echo $trparam; ?>"><?php endif;?>
					
					<?php
					$wochentag = date("N", strtotime($daysCreate.".".$month.".".$year));
					if($wochentag == $cDayinWeek):
						?> <td class=""><?php echo $daysCreate; ?></td> <?php
						$daysCreate++;
					else:
						?> <td class="darkerList">&nbsp;</td> <?php 
					endif;
					
					?>
			<?php else: ?>
				<td class="darkerList">&nbsp;</td>
			<?php endif; ?>
			
			<?php if($cDayinWeek == 7):
						?></tr><?php $cDayinWeek = 1;
					else:
						$cDayinWeek++;
					endif;?>
			<?php 
			
			
		endwhile;
		?> </table> </div><?php
		
	}
	
	private function displayDate($tag, $csel) {
		if($csel == null) $csel = time();
		
		if($tag == intval(date("N", $csel))):
			echo date("d.m.Y", $csel);
		elseif($tag < intval(date("N", $csel))): 
			while(true):
				$csel = $csel - (60*60*24);
				if($tag == intval(date("N", $csel))):
					echo date("d.m.Y", $csel);
					break;
				endif;
			endwhile;
		elseif($tag > intval(date("N", $csel))):
			while(true):
			$csel = $csel + (60*60*24);
				if($tag == intval(date("N", $csel))):
					echo date("d.m.Y", $csel);
					break;
				endif;
			endwhile;
		endif;
		
	}
	
	public function createSmallCalTables($csel) {
		
		if($csel == "null") $csel = null;
		if(!is_numeric($csel) && $csel != null):
			?><div class="error"><?php displayText('errors>unknown')?></div><?php
			return;
		endif;
		$csel = intval($csel);
		
		if($csel == null):
			$cmonth = intval(date("n"));
			$cyear = intval(date("Y"));
			$csel = time();
		else:				
			$cmonth = intval(date("n", $csel));
			$cyear = intval(date("Y", $csel));
		endif;
		
		for($i = -2; $i <= 3; $i++):
			$tmpMonth = $cmonth + $i;
			if($tmpMonth < 1):
				$tmpYear = $cyear - 1;
				$tmpMonth = $tmpMonth + 12;
			elseif($tmpMonth > 12):
				$tmpYear = $cyear + 1;
				$tmpMonth = $tmpMonth - 12;
			else:
				$tmpYear = $cyear;
			endif;
			
			$this->makeSmallCal($tmpMonth, $tmpYear, $csel);
			
			if($i == 0): ?> <div class="floatbreak">&nbsp;</div> <?php endif;
			
		endfor; 
		?> <div class="floatbreak">&nbsp;</div> <?php
	}
	
	public function createMainCal($csel, $cid) {
		
		if($csel == "") $csel = time();
		if(!is_numeric($csel)):
		?><div class="error"><?php displayText('errors>unknown')?></div><?php
			return;
		endif;
		$csel = intval($csel);
		
		?>
		<table>
			<tr>
				<th></th>
				<th><?php displayText('common>monday')?><br /><span class="dateD"><?php $this->displayDate(1, $csel); ?></span></th>
				<th><?php displayText('common>tuesday')?><br /><span class="dateD"><?php $this->displayDate(2, $csel); ?></span></th>
				<th><?php displayText('common>wednesday')?><br /><span class="dateD"><?php $this->displayDate(3, $csel); ?></span></th>
				<th><?php displayText('common>thursday')?><br /><span class="dateD"><?php $this->displayDate(4, $csel); ?></span></th>
				<th><?php displayText('common>friday')?><br /><span class="dateD"><?php $this->displayDate(5, $csel); ?></span></th>
				<th><?php displayText('common>saturday')?><br /><span class="dateD"><?php $this->displayDate(6, $csel); ?></span></th>
				<th><?php displayText('common>sunday')?><br /><span class="dateD"><?php $this->displayDate(7, $csel); ?></span></th>
			</tr>
			<tr class="timeb">
				<td id="timeline" class="relative"><?php $this->getTimeline($cid, $csel) ?></td>
				<td id="c_monday"></td>
				<td id="c_tuesday">d</td>
				<td id="c_wednesday">m</td>
				<td id="c_thursday">d</td>
				<td id="c_friday">f</td>
				<td id="c_saturday">s</td>
				<td id="c_sunday">s</td>
			</tr>
		</table>
		
		<?php 
	}
	
	private function getTimeline($cid, $csel) {
		
		$csel = strtotime(date("j.n.Y", $csel)); //Timestamp vom Tag aber von 0:00 Uhr

		foreach($this->meta AS $id => $cmeta) :
			while(true):
				/*
				 * Stop, wenn Startdatum von Post noch nicht erreicht oder die Sichtbarkeit nicht erreicht wurde
				 */
				if(time() < $cmeta['startdate'] || $csel > (time() + $cmeta['visibility'])) break;
		
				
				if($cmeta['type'] == 'weekly'):
					var_dump($cmeta);
				endif;
				
				
				break;
			endwhile;
		endforeach;
	}
	
	private function getPosts($cid) {
		
	}

}

$calendar = new calendar();