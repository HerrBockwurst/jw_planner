<?php 
if(!defined('index')) exit;
global $user;
if(!$user->hasPerm('admin.calendar')) exit;

?>
<div id="cadmin_newcal" class="modul" style="width: 580px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#cadmin_newcal')" class="clickable" /></div>
	<div class="inner relative">
		<div id="c_newcal_error" class="error smallmargin"></div>
		<div id="c_newcal_success" class="success smallmargin"></div>
		<fieldset id="cadmin_add_infos">
			<legend><?php displayString('admin>cal_infos')?></legend>
			<?php 
			$bob->startForm('cadmin_add');
			$bob->addFormRow('c_add_name', getString('admin>c_name'), array('text'));
			$bob->addFormRow('c_add_vs', getString('common>versammlung'), array('select', getVSArray(), $user->vsid));
			$bob->endForm();
			
			?>
		</fieldset>
		<div style="display:inline; float: right; width: 240px;">
			<fieldset id="cadmin_add_list_u" class="smaller">
				<legend><?php displayString('admin>c_bl_wl_u')?></legend>
				Funktion noch nicht verf&uuml;gbar
				<!--  <input type="text" id="c_list_u" value=""/> -->
			</fieldset>
			<fieldset id="cadmin_add_list_g" class="smaller">
				<legend><?php displayString('admin>c_bl_wl_g')?></legend>
				Funktion noch nicht verf&uuml;gbar
			</fieldset>
		</div>
		<br class="floatbreak" />
		<?php $bob->addButton(getString('admin>c_add_cal'), 'c_newcal_button', 'formrow', "$('#cadmin_add').submit();")?>
	</div>
	<script>
	$('#cadmin_add').submit(function (event) {
		
		event.preventDefault();
	
		$('#c_newcal_error').hide(100);
		$('#c_newcal_success').hide(100);
		
		var c_add_name = $('#c_add_name').val(),
			c_add_vs = $('#c_add_vs').val();
	
		if(c_add_name == '') {
			$('#c_newcal_error').text('<?php displayString('errors>invalidFormSubmit')?>').show(100);
			return;
		}
	
		var posting = $.post('<?php echo PROTO.HOME?>/ajax/datahandler/addcal', {name: c_add_name, vs: c_add_vs});
		posting.done(function(data) {
			var jdata = JSON.parse(data);
			console.log(jdata);
			if(typeof jdata.error !== "undefined") {
				$('#c_newcal_error').text(jdata.error).show(100);
				return;
			}

			$('#c_newcal_success').text(jdata.success).show(100);
			
		});
	
	});
	</script>
</div>

<script class="removeme">$(openModule('#cadmin_newcal'));</script>

<?php
