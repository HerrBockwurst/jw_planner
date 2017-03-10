<?php needAuth(); ?>
<div id="MessageBox">
	<div id="MessageBoxContent"></div>
</div>
<div id="LoadingBox">
	<div id="LoadingBoxContent"></div>
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
	function displayMessageBox(Text, Reload, DoubleButton) {
		if(typeof Reload === "undefined") var Reload = false;

		if(	typeof DoubleButton !== "undefined" &&
			typeof DoubleButton.yes !== "undefined" &&
			typeof DoubleButton.no !== "undefined" &&
			typeof DoubleButton.callback === "function") {
				var fB = '<button style="display: inline-block; margin-right: 10px">'+DoubleButton.yes+'</button>';
				var sB = '<button data-redirect="' + Reload + '" style="display: inline-block">'+DoubleButton.no+'</button>';
				var button = '<div style="margin-top: 1em; text-align: center">' + fB + sB + '</div>';
		}
		else {
			var button = '<button data-redirect="' + Reload + '" style="display: block; margin: 0 auto; margin-top: 1em"><?php displayString('common okay')?></button>';
		}
		
		$('#MessageBoxContent').html(Text + button);
		$('#MessageBoxContent').find('button').each(function () {
			if($(this).is(':first-of-type') && $(this).parent().children('button').length == 2) {
				$(this).bind('click', {cb : DoubleButton.callback}, MsgBoxButtonAction )
			}
			else {
				$(this).bind('click', MsgBoxButtonAction )
			}
		});
		$('#MessageBox').fadeIn(100);
	}
	
	function MsgBoxButtonAction(callback) {
		if($(this).parent().children('button').length == 2) {
			//2 Button abfrage			
			if($(this).is(':first-of-type')) {				
				$('#MessageBox').fadeOut(100);
				callback.data.cb();
			}
			else {
				if($(this).attr('data-redirect') == "true") window.location.replace('<?php echo PROTO.HOME?>');
				else $('#MessageBox').fadeOut(100);
			}
		} else {
			//Keine 2 Button abfrage
			if($(this).attr('data-redirect') == "true") window.location.replace('<?php echo PROTO.HOME?>');
			else $('#MessageBox').fadeOut(100);
		}
	}
	
	function displayLoadingBox() {
		$('#LoadingBox').stop().fadeIn(100);
	}
	
	function hideLoadingBox() {
		$('#LoadingBox').finish().fadeOut(100);
	}
	
	$('#lMainMenu').children('li').click(function() {
		loadContent('<?php echo PROTO.HOME?>/load/' + $(this).attr('data-link'), '#Content');
	});
	$('#LoadingBoxContent').loadingWheel();
</script>