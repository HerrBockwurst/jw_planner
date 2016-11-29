<div id="changelog">
<div>
	<div><?php  displayString('changelog release')?></div>
	<div><?php  displayString('changelog changelog')?></div>
	<br class="floatbreak" />
</div>
<?php 
global $mysql;
$mysql->orderBy('release', 'DESC');
$mysql->select('changelog');

foreach($mysql->fetchAll() AS $cLog):
	$log = json_decode($cLog['changelog'], true);
	?>
	<div>
		<div><?php echo $cLog['release']?></div>
		<div><?php echo $log['de'];?></div>
		<br class="floatbreak" />
	</div>
<?php endforeach; ?>
</div>