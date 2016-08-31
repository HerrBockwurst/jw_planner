<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.entry')) exit;

if(!isset($_POST['cid'])) exit;

$result = $mysql->execute("SELECT c.vsid , p.*, e.uid AS euid, e.eid, u.name FROM posts AS p
							INNER JOIN calendar AS c ON (p.cid = c.cid)
							LEFT JOIN entrys AS e ON (p.pid = e.pid)
							LEFT JOIN user AS u ON (e.uid = u.uid)
							WHERE e.cid = ? OR p.cid = ? AND p.start > ?
							ORDER BY p.start",
							'iii', array($_POST['cid'], $_POST['cid'], time()));

if($result->num_rows == 0):
	?><div class="error" style="display:block"><?php displayString('calendar>no_posts_applied')?></div><?php
	exit;
endif;


$pid_result = $result->fetch_all(MYSQLI_ASSOC);

$posts = array();

foreach($pid_result AS $row):
	/*
	 * Raus wenn er keine Berechtigung für die VS hat
	 */
	if(!array_key_exists($row['vsid'], getVSArray())) break;
	
	/*
	 * Daten aufarbeiten
	 * Schema:
	 * array[PID]=
	 * 	pid = PID
	 * 	start = START
	 * 	end = END
	 * 	expire = EXPIRE
	 * 	count = COUNT
	 * 	entrys = array(array(eid = EID, uid = UID, name = NAME))
	 */
	if(!array_key_exists($row['pid'], $posts)):
		$posts[$row['pid']] = array(
			'pid' => $row['pid'],
			'start' => $row['start'],
			'end' => $row['end'],
			'count' => $row['count'],
			'entrys' => array()
		);
		if($row['eid'] != ''):
			$posts[$row['pid']]['entrys'][] = array(
				'eid' => $row['eid'],
				'uid' => $row['euid'],
				'name' => $row['name']
		);
		endif;
	else:
		$posts[$row['pid']]['entrys'][] = array(
				'eid' => $row['eid'],
				'uid' => $row['euid'],
				'name' => $row['name']
		);
	endif;
endforeach;

foreach($posts AS $post):
	?>
	<div class="relative" style="float:left" onmouseleave="hidetooltip(<?php echo $post['pid']?>)">
		<div class="post clickable <?php if(count($post['entrys']) == intval($post['count'])) echo "full"?>" onclick="applyme(<?php echo $post['pid'] ?>, <?php echo $_POST['cid'] ?>)">
			<img class="poststar" style="<?php if(in_array_r($user->uid, $post['entrys'])) echo 'display: block;';?>" id="star_<?php echo $post['pid']?>" src="images/star.png"/>
			<div class="date">
				<span class="postheader"><?php displayString('common>start')?></span>
				<div class="bg">
					<span class="smallerDate"><?php echo date("d.m.Y", $post['start'])?></span>
					<span class="time"><?php echo date("H:i", $post['start'])?></span>
				</div>
				<span class="postheader"><?php displayString('common>end')?></span>
				<div class="bg">
					<span class="smallerDate"><?php echo date("d.m.Y", $post['end'])?></span>
					<span class="time"><?php echo date("H:i", $post['end'])?></span>
				</div>
				<div class="count relative" onmouseenter="showtooltip(<?php echo $post['pid']?>)">
					<span id="counter_<?php echo $post['pid']?>"><?php echo count($post['entrys'])?></span>
					<span><?php echo $post['count']?></span>					
				</div>			
			</div>
		</div>
		<div id="tooltip_<?php echo $post['pid']?>" class="tooltip_count">
			<?php echo getTooltip($post['pid'])?>
		</div>
	</div>
	<?php 
endforeach;
?>
