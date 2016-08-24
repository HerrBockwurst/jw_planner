<?php 
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.admin')) exit;

$result = $mysql->execute("SELECT * FROM calendar WHERE cid = ? LIMIT 1" , 'i', intval(getURL(5)));

/*
 * Teste Rechte
 */

$result = $result->fetch_assoc();

if(!array_key_exists($result['vsid'], getVSArray())):
	$name = '';
	$vsid = $user->vsid;
	$error = getString('errors>noPerm');
else:
	$name = $result['name'];
	$vsid = $result['vsid'];
endif;

?>

<div id="cadmin_editcal" class="modul" style="width: 580px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#cadmin_editcal')" class="clickable" /></div>
	<div class="inner relative">
		<div id="c_editcal_error" class="error smallmargin"></div>
		<div id="c_editcal_success" class="success smallmargin"></div>
		<fieldset id="cadmin_edit_infos">
			<legend><?php displayString('admin>cal_infos')?></legend>
			<?php 
			$bob->startForm('cadmin_edit');
			$bob->addFormRow('c_edit_name', getString('admin>c_name'), array('text'), $name);
			$bob->addFormRow('c_edit_vs', getString('common>versammlung'), array('select', getVSArray(), $vsid));
			$bob->addFormRow('c_edit_delete', getString('admin>c_delete'), array('checkbox'));
			$bob->endForm();
			
			?>
		</fieldset>
		<div style="display:inline; float: right; width: 240px;">
			<fieldset id="cadmin_edit_list_u" class="smaller">
				<legend><?php displayString('admin>c_bl_wl_u')?></legend>
				Funktion noch nicht verf&uuml;gbar
				<!--  <input type="text" id="c_list_u" value=""/> -->
			</fieldset>
			<fieldset id="cadmin_edit_list_g" class="smaller">
				<legend><?php displayString('admin>c_bl_wl_g')?></legend>
				Funktion noch nicht verf&uuml;gbar
			</fieldset>
		</div>
		<br class="floatbreak" />
		<?php $bob->addButton(getString('admin>c_edit_cal'), 'c_editcal_button', 'formrow', "$('#cadmin_edit').submit();")?>
	</div>
	<script>
	$('#cadmin_edit').submit(function (event) {
		
		event.preventDefault();
	
		$('#c_editcal_error').hide(100);
		$('#c_editcal_success').hide(100);
		
		var c_edit_name = $('#c_edit_name').val(),
			c_edit_vs = $('#c_edit_vs').val();
			c_edit_del = 0;

		if($('#c_edit_delete').prop('checked')) {
			c_edit_del = 1;
		}
	
		if(c_edit_name == '') {
			$('#c_editcal_error').text('<?php displayString('errors>invalidFormSubmit')?>').show(100);
			return;
		}
	
		var posting = $.post('<?php echo PROTO.HOME?>/ajax/datahandler/editcal', {cid: <?php echo getURL(5)?>, name: c_edit_name, vs: c_edit_vs, del: c_edit_del});
		posting.done(function(data) {
			var jdata = JSON.parse(data);
			if(typeof jdata.error !== "undefined") {
				$('#c_editcal_error').text(jdata.error[0]).show(100);
				return;
			}

			$('#c_editcal_success').text(jdata.success[0]).show(100);
			
			if(typeof jdata.deleted !== "undefined") {
				$('#cadmin_editcal').find('input').prop('disabled', true);
				setTimeout(function() {
					closeModule('#cadmin_editcal');
					$.get('<?php echo PROTO.HOME?>/ajax/load/modul/calendaradmin', function(data) { $('#calendaradmin').remove(); $('#site').append(data); })
				}, 2000);
			}
			
		});
	
	});
	</script>
</div>

<script class="removeme">$(openModule('#cadmin_editcal'));</script>

<?php
