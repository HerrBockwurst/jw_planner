<?php needAuth(); ?>
<div id="MainMenu">
	<div class="logo">JW<span>Planner</span></div>
<?php
	$MenuItems = ContentHandler::getInstance()->getMenuItems();
	echo "<ul id=\"lMainMenu\">";
	foreach($MenuItems AS $MenuItem) {
		// Wenn Permission benötigt und nicht vorhanden -> Skip
		if($MenuItem->getPermission() != "" && !User::getInstance()->hasPerm($MenuItem->getPermission()) )
			continue;
		
		echo "<li data-link=\"".$MenuItem->getLink()."\">".$MenuItem->getString()."</li>";
	}
	echo "</ul>";
?>
</div>
<div id="Content">
	<?php ContentHandler::getInstance()->performLoad('dashboard')?>
</div>
<script>
$('#lMainMenu').children('li').click(function() {
	loadContent('<?php echo PROTO.HOME?>/load/' + $(this).attr('data-link'), '#Content');
});
</script>