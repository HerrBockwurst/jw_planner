<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('system.query')) exit;
?>

<div id="sql" class="modul" style="width: 600px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#sql')" class="clickable" /></div>
	<div class="inner" id="calendaradmin_inner">
		<fieldset id="queryfield">
			<legend><?php displayString('sql>query')?></legend>
			<textarea id="query"></textarea>
			<div style="width: 40%; float:left;">
				<div class="relative smallmargin">
					<input type="radio" id="raw" name="resulttype" value="raw" checked="checked" />
					<label for="raw"><?php displayString('sql>raw')?></label>				
				</div>
				
				<div class="relative smallmargin">				
					<input type="radio" id="fetched" name="resulttype" value="fetched" />
					<label for="fetched"><?php displayString('sql>fetched')?></label>
				</div>
			</div>
			<div style="width: 40%; float:left;" class="buttonholder">
				<div class="relative smallmargin">
					<input type="radio" id="dump" name="action" value="dump" checked="checked" />
					<label for="dump"><?php displayString('sql>dump')?></label>				
				</div>
				
				<div class="relative smallmargin">				
					<input type="radio" id="printr" name="action" value="printr" />
					<label for="printr"><?php displayString('sql>printr')?></label>
				</div>
			
			</div>			
			<br class="floatbreak" />
			<div id="querysend" class="pseudobutton clickable"><?php displayString('sql>send')?></div>
			<br class="floatbreak" />
		</fieldset>
		<fieldset>
			<legend><?php displayString('sql>result')?></legend>
			<div id="sqlresult">
			</div>
		</fieldset>		
	</div>
	<script>
	$('#querysend').click(function() {
		var qry = $('#query').val();
		var step2 = $('input[name=resulttype]:checked').val();
		var step3 = $('input[name=action]:checked').val();
		$.post('<?php echo PROTO.HOME?>/ajax/datahandler/query', {qry: qry, step2: step2, step3: step3}, function(data) {
			$('#sqlresult').text(data);
		});
	});
	</script>
</div>
<script class="removeme">$(openModule('#sql'));</script>