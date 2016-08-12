<?php 
if(!defined('index')) exit;
global $user;
if(!$user->hasPerm('calendar.admin')) exit;
?>
<div id="cadmin_newcal" class="modul" style="width: 580px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#cadmin_newcal')" class="clickable" /></div>
	<div class="inner relative">
		<fieldset id="cadmin_add_infos">
			<legend><?php displayString('admin>cal_infos')?></legend>
			<?php 
			$bob->startForm('cadmin_add');
			$bob->addFormRow('name', getString('admin>c_name'), array('text'));
			$bob->addFormRow('vs', getString('common>versammlung'), array('select', getVSArray(), $user->vsid));
			$bob->endForm();
			
			?>
		</fieldset>
		<div style="display:inline; float: right; width: 240px;">
			<fieldset id="cadmin_add_list_u">
				<legend><?php displayString('admin>c_bl_wl_u')?></legend>
			</fieldset>
			<fieldset id="cadmin_add_list_g">
				<legend><?php displayString('admin>c_bl_wl_g')?></legend>
				Funktion noch nicht verf&uuml;gbar
			</fieldset>
		</div>
	</div>
</div>
<script class="removeme">$(openModule('#cadmin_newcal'));</script>

<?php
