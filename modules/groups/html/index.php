<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('admin.groups')) exit;
?>

<div id="groups" class="modul" style="width: 600px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#groups')" class="clickable" /></div>
	<div class="inner" id="calendaradmin_inner">
		<fieldset>
			<legend><?php displayString('groups>add')?></legend>
		</fieldset>
	</div>
</div>
<script class="removeme">$(openModule('#groups'));</script>