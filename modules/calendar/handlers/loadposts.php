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
	<div class="post clickable <?php if(count($post['entrys']) == intval($post['count'])) echo "full"?>" onclick="applyme(<?php ?>)">
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
			<div class="count relative" onmouseenter="showtooltip(<?php echo $post['pid']?>)" onmouseleave="hidetooltip(<?php echo $post['pid']?>)">
				<span><?php echo count($post['entrys'])?></span>
				<span><?php echo $post['count']?></span>
				<div id="<?php echo $post['pid']?>" class="tooltip_count <?php if($user->hasPerm('calendar.admin')) echo 'adminview'?>">
					<?php 
					if(empty($post['entrys'])):
						displayString('calendar>no_entrys_applied');
					endif;
					?>
					<?php foreach($post['entrys'] AS $entry): ?>
					<div class="tooltip_count_row floatbreak relative">
						<div class="pic"><?php echo strtoupper(substr($entry['name'], 0, 1))?></div>
						<div class="text"><?php echo $entry['name'];?></div>
						<?php
						if($user->hasPerm('calendar.admin') || $entry['uid'] == $user->uid):
						?>
							<div class="deleteentry clickable" onclick="deleteentry(<?php echo $entry['eid']?>)">
								<img src="<?php echo PROTO.HOME?>/images/postdelete.png" />
							</div>
						<?php 
						endif;
						?>
					</div>
					<?php endforeach;?>
				</div>
			</div>			
		</div>
		<script>

		</script>
	</div>
	<?php 
endforeach;
?>
