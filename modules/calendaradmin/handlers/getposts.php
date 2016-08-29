<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('admin.calendar')) exit;

deleteOldPosts();

/*
 * Teste Berechtigung für Kalender
 */

$cal = $mysql->execute("SELECT vsid FROM calendar WHERE cid = ?", 'i', intval($_POST['cid']));

if($cal->num_rows != 1):
	displayString('errors>noCal');
	exit;
endif;

$cal = $cal->fetch_assoc();

if(!array_key_exists($cal['vsid'], getVSArray())):
	displayString('errors>noPerm');
	exit;
endif;

/*
 * Daten holen
 */

$posts = $mysql->execute("SELECT * FROM posts WHERE cid = ? ORDER BY start ASC", 'i', intval($_POST['cid']));

if($posts->num_rows == 0):
	displayString('calendar>no_posts_applied');
	exit;
endif;

$posts = $posts->fetch_all(MYSQLI_ASSOC);

/*
 * Daten auswerten
 */

foreach($posts AS $post):
	?>
	<div class="cadmin_posts_post relative">
		<div class="cadmin_posts_post_start">
			<span class="postheader"><?php displayString('common>start') ?></span>
			<span class="smallerDate"><?php echo date("d.m.Y", $post['start'])?></span><br />
			<span class="time"><?php echo date("H:i", $post['start'])?></span>
		</div>
		<div class="cadmin_posts_post_end">
			<span class="postheader"><?php displayString('common>end') ?></span>
			<span class="smallerDate"><?php echo date("d.m.Y", $post['end'])?></span><br />
			<span class="time"><?php echo date("H:i", $post['end'])?></span>
		</div>
		<div class="cadmin_posts_post_expire">
			<span class="postheader"><?php displayString('admin>expire') ?></span>
			<span class="smallerDate"><?php echo date("d.m.Y", $post['expire'])?></span><br />
			<span class="time"><?php echo date("H:i", $post['expire'])?></span>
		</div>
		<div class="cadmin_posts_post_count relative">
			<span class="postheader"><?php displayString('common>entrys')?></span>
			<span class="cadmin_posts_post_count_content" style="border-bottom: 1px solid #444">0</span>
			<span class="cadmin_posts_post_count_content"><?php echo $post['count']?></span>
		</div>
		<div class="postdeletebutton clickable" onclick="deletepost(<?php echo $post['pid']?>)">
			<img src="<?php echo PROTO.HOME?>/images/postdelete.png" />
		</div>
	</div>
	<?php 
endforeach;
?>

