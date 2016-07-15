<?php
if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

if(!$USER->hasPerm('admin.useredit')) header("Location:".getURL());

if(!isset($_POST['name']) || $_POST['name'] == "")  header("Location:".getURL()."/".$url->value(0));

$versammlungen = getVersArray();

$result = $mysql->execute("SELECT u.`uid`, u.`name`, u.`versammlung`, v.`name` AS `vsname` FROM `users` AS u
							INNER JOIN `versammlungen` AS v ON (u.`versammlung` = v.`id`)
							WHERE u.`uid` LIKE ? OR u.`name` LIKE ?
							ORDER BY u.`versammlung` ASC, u.`name` ASC",
							'ss', array("%".$_POST['name']."%", "%".$_POST['name']."%"));
while(true):
	/*
	 * Testet ob es einen Benutzer mit den Namen gibt
	 */
	$aliste = array(); //Muss vordefiniert werden, damit sie leer ist, falls kein Benutzer gefunden wird
	if($result->num_rows == 0):
		$ERROR['usersearch'] = getLang('errors>nouserfound');
		break;
	endif;
	/*
	 * Lässt nur nutzer durch, für die der Suchende Berechtigungen hat
	 */
	
	$aliste = $result->fetch_all(MYSQLI_ASSOC);
	foreach($aliste AS $key => $user):
		if(!key_exists($user['versammlung'], $versammlungen))
			unset($aliste[$key]);
	endforeach;
	
	/*
	 * Prüft nochmal ob Benutzer gefunden (wurde evtl wegen Berechtigung gefiltert)
	 */
	if(empty($aliste)):
		$ERROR['usersearch'] = getLang('errors>nouserfound');
		break;
	endif;
	break;
endwhile;

?>



<div class="field">
	<div class="headline"><?php displayText('admin>searchuser');?></div>
	<?php if(isset($ERROR['usersearch'])) echo $ERROR['usersearch']; ?>
	<?php
	$darker = false;
	foreach($aliste AS $curr_user):
		if($darker == false) $darker = true; //Für wechselnde Hintergrundfarbe
		elseif($darker == true) $darker = false;
	?>
	<div class="relative colorchangerList" >
		<a href="<?php printURL()?>/<?php echo $url->value(0)?>/useredit/<?php echo $curr_user['uid']?>" <?php if($darker == true):?>class="darkerList"<?php endif; ?> style="display:block;">
		<span style="padding: 2px; position:relative; width:100%;display:block;">
			<span style="position:absolute; left: 10px;"><?php echo utf8_encode($curr_user['name']);?> (<?php echo utf8_encode($curr_user['uid']);?>)</span>
			<span style="position:absolute; right: 10px; text-align:right"><?php echo utf8_encode($curr_user['vsname'])?></span>
			<br />
		</span>
		</a>
	</div>
	<?php endforeach;?>
	<div class="morespace">
		<a href="<?php printURL();?>/<?php echo $url->value(0)?>"><?php displayText('common>back')?></a>
	</div>
</div>