<?php
if(!isset($fromIndex)) exit;

class calendar {
	
	private $cdata, $meta, $iPosts, $days, $counter;
	
	function __construct() {
		global $_POST, $USER, $mysql;
		
		$result = $mysql->execute("SELECT * FROM `calendar` WHERE `versammlung` = ?", 's', $USER->vsid);
		
		$this->cdata = $result->fetch_all(MYSQLI_ASSOC);
		
		foreach ($this->cdata AS $cdata):
		
			$this->meta[$cdata['cid']] = json_decode($cdata['meta'], true);
			if($this->meta[$cdata['cid']] == NULL) $this->meta[$cdata['cid']] = array();
			
		endforeach;
		$this->iPosts = array();
		$this->days = array("monday" => 1, "tuesday" => 2, "wednesday" => 3, "thursday" => 4, "friday" => 5,
							"saturday" => 6, "sunday" => 7);
		$this->counter = 0;
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
				<?php if($cDayinWeek == 1):?><tr style="cursor: pointer" onclick="$('#cal').load('/ajax/updateCal', {csel: '<?php echo $cstamp; ?>'});" class="<?php echo $trparam; ?>"><?php endif;?>
					
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
	
	public function createMainCal($csel) {
		
		if($csel == "") $csel = time();
		if(!is_numeric($csel)):
		?><div class="error"><?php displayText('errors>unknown')?></div><?php
			return;
		endif;
		$csel = intval($csel);
		
		
		$this->getPosts($csel);
		
		
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
			<?php for($i=0; $i<24; $i++): ?>
			<tr class="small <?php if($i != 23) echo "timeb"; else echo "lasttimeb"?> <?php if(($i % 2) != 0) echo "lightgreen"; ?> <?php $this->checkTime($i);?>">
				<td class="timeline relative"><?php $this->printTime($i); ?></td>
				<td class="relative"><?php $this->printPost(1, $i); ?></td>
				<td class="relative"><?php $this->printPost(2, $i); ?></td>
				<td class="relative"><?php $this->printPost(3, $i); ?></td>
				<td class="relative"><?php $this->printPost(4, $i); ?></td>
				<td class="relative"><?php $this->printPost(5, $i); ?></td>
				<td class="relative"><?php $this->printPost(6, $i); ?></td>
				<td class="relative"><?php $this->printPost(7, $i); ?></td>
			</tr>
			<?php endfor; ?>
		</table>
		
		<?php 
	}
	
	private function checkTime($time) {
		$found = false;
		foreach($this->iPosts AS $post):
			if($post['start'] == $time) $found = true;
			if($time > $post['start'] && $time < ($post['start'] + ($post['height'] / 30))) $found = true;
		endforeach;
		
		
		if($found != true) echo "noentry";
	}
	
	private function printTime($time) {
		$string = "";
		if($time < 10) $string .= "0";
		$string .= $time.":00 ".getLang('common>clock');
		echo $string;
	}

	private function gimmeColor() {
		$colors = array("#7dff66", "#E57373", "#2cddbe", "#e4fdc4", "#c1a8e9", "#bababa");
		
		if($this->counter > sizeof($colors)) $this->counter = 0;
		$retval = $colors[$this->counter];
		$this->counter++;
		return $retval;
	}
	
	private function getPosts($csel) {
		
		$csel = strtotime(date("j.n.Y", $csel)); //Timestamp vom Tag aber von 0:00 Uhr
		
		foreach($this->meta AS $cidmeta):
			$color = $this->gimmeColor();
			foreach($cidmeta AS $id => $cmeta) :
				while(true):
					/*
					 * Stop, wenn Startdatum von Post noch nicht erreicht oder die Sichtbarkeit nicht erreicht wurde
					 */
					if(!key_exists("type", $cmeta)) break;
					if(($csel + (60*60*24*7)) < $cmeta['startdate'] || $csel > (time() + $cmeta['visibility'])) break;
					
					if($cmeta['type'] == 'weekly'):
						
						$start = explode(":", $cmeta['start']);
						$end = explode(":", $cmeta['end']);
						$duration = (($end[0] - $start[0]) * 60) + ($end[1] - $start[1]);
					
						$arr = array("type" => "weekly", "day" => $this->days[$cmeta['patternA']], "start" => $start[0],
									 "end" => $end[0], "top" => round((30 / 60) * $start[1]), "height" => round((30 / 60) * $duration),
									 "color" => $color
						);
						
						$this->iPosts[$id] = $arr;
					endif;
					
					
					break;
				endwhile;
			endforeach;
		endforeach;
	}
	
	private function printPost($day, $time) {
		foreach($this->iPosts AS $key => $post):
			while(true):
				if($post['type'] == 'weekly'):
					if($post['day'] != $day || $post['start'] != $time) break; //Abbrechen wenn nicht richtiger Tag oder Zeit
					
					?> <div class="post" style="background-color:<?php echo $post['color'];?>; top:<?php echo $post['top']?>px; height:<?php echo $post['height'];?>px"></div> <?php 
					//TODO Bild von Männel anzeigen
					//TODO Anzahl der verfügbaren Plätze erstellen + anzeigen
				endif;
				break;
			endwhile;
		endforeach;
	}

}

$calendar = new calendar();