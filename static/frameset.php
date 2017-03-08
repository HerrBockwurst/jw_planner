<?php needAuth(); ?>
<div id="MessageBox">
	<div id="MessageBoxContent"></div>
</div>
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
function displayMessageBox(Text, Reload) {
	if(typeof Reload === "undefined") var Reload = false;

	var button = '<button data-redirect="' + Reload + '" style="display: block; margin: 0 auto; margin-top: 1em"><?php displayString('common okay')?></button>';
	
	$('#MessageBoxContent').html(Text + button);
	$('#MessageBoxContent').find('button').each(function () { $(this).bind('click', MsgBoxButtonAction )});
	$('#MessageBox').fadeIn(100);
}

function MsgBoxButtonAction() {
	if($(this).attr('data-redirect') == "true") window.location.replace('<?php echo PROTO.HOME?>');
	else $('#MessageBox').fadeOut(100);
}

$('#lMainMenu').children('li').click(function() {
	loadContent('<?php echo PROTO.HOME?>/load/' + $(this).attr('data-link'), '#Content');
});
</script>