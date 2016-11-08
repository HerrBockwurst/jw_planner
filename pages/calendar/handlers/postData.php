<?php
if(!isset($_POST['pid'])) exit;

global $user, $mysql;

$mysql->where('pid', $_POST['pid']);
$mysql->join(array('posts' => 'cid', 'calendar' => 'cid'));
$mysql->select('posts', array('*', 'calendar.vsid' => 'vsid'), 1);

$postdata = $mysql->fetchRow();
$entrys = json_decode($postdata->entrys);

if($postdata->vsid != $user->vsid) {
	displayString('errors noPerm');
	exit;
}

?>
<div id="c_p_userholder">
	<strong><?php displayString('calendar entrys')?></strong> 
	<?php
	if(count($entrys) == 0) echo "<div class=\"c_p_userrow\">".getString('calendar noEntrys')."</div>";
	
	foreach($entrys AS $entry):
		$mysql->where('uid', $entry);
		$mysql->select('users', array('name'));
		$name = explode(" ", ($mysql->fetchRow()->name));
	?>
	
	<div class="c_p_userrow" data-uid="<?php echo $entry?>">
		<div style="background-color: rgba(<?php echo stringToColorCode(implode(" ", $name))?>)"><?php echo strtoupper(substr($name[0], 0, 1)).strtoupper(substr($name[count($name) - 1], 0, 1)); ?></div>
		<?php echo implode(" ", $name)?>
		<?php
			if($user->hasPerm('calender.entry.other')) {
				echo "<div class=\"clickable deletebutton\">".getString('calendar delete')."</div>";
			}
		?>
	</div>
	
	<?php endforeach; ?>
</div>