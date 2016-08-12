<?php
if(!defined('index')) exit;
global $user;
if(!$user->hasPerm('admin.useredit')) exit;
?>

<div id="edituser_window" class="modul" style="width: 600px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#edituser_window')" class="clickable" /></div>
	<div class="inner">
		<div id="edituser_formdiv">
			<fieldset id="edituser_mainfieldset" style="width:400px;">
				<legend><?php displayString('admin>edit_user')?></legend>
				<div id="edituser_warn" class="warn"></div>
				<div id="edituser_error" class="error"></div>
				<div id="edituser_success" class="success"></div>
				<?php
				
				$checked = "";
				if($_POST['active'] == 'active') $checked = 'checked';
				
				$bob->startForm('edituser');
				$bob->addFormRow('e_uid', getString('common>username'), array("text", "disabled" => true), $_POST['uid']);
				$bob->addFormRow('e_name', getString('common>name'), array('text'), $_POST['name']);
				$bob->addFormRow('e_email', getString('common>email'), array('text'), $_POST['email']);
				$bob->addFormRow('e_p1', getString('common>password'), array('password'));
				$bob->addFormRow('e_p2', getString('common>password_rp'), array('password'));
				$bob->addFormRow('e_active', getString('admin>active'), array('checkbox'), $checked);
				$bob->addFormRow('e_delete', getString('admin>delete'), array('checkbox'));
				$bob->addFormRow('e_versammlung', getString('common>versammlung'), array("select", getVSArray(), $_POST['vsid']));
				$bob->endForm();
				?>
			</fieldset>
			<fieldset id="edituser_perms">
					<legend><?php displayString('common>permissions')?></legend>
					<div id="edituser_perms_button" class="clickable pseudobutton relative">
						<?php 
							/*
							 * Liste der Permissions erstellen
							 */
							$uperms = array();
							if(isset($_POST['perms'])) $uperms = $_POST['perms']; 
							
							 
							foreach($uperms AS $key => $perm):
								if(!$user->hasPerm($perm) || strpos($perm, '.vs.') !== false) unset($uperms[$key]); //Perms die der Benutzer nicht hat, werden nicht angezeigt

							endforeach;
								
							$activeperms = array();
							
							foreach($user->getPerms() AS $cperm) if(in_array($cperm, $uperms)) $activeperms[] = $cperm; //Liste mit aktiven Permissions erstellen
								
							$myperms = array("admin" => $user->getSubPerm('admin.'), "calendar" => $user->getSubPerm('calendar.'), "system" => $user->getSubPerm('system.'));
							$counter = 1;
						?>
						<span id="e_pcount"><?php echo count($uperms)?></span> <?php displayString('common>permissions')?>
						<div id="edituser_perms_list">
							<?php 
														
							foreach ($myperms AS $key => $cperm):
								if($cperm == false) break;
								?>
								
								<fieldset id="edituser_perms_<?php echo $key;?>" class="edituser_perms_subset">
									<legend><?php displayString('common>'.$key)?></legend>
									<?php 
									foreach($cperm AS $perm):
										if(strpos($perm, '.vs.') !== false) break;
										$state = 'inactive';
										if(in_array($perm, $activeperms)) $state = 'active';
										?>
										<div id="<?php echo "e_id_".$counter ?>" class="clickable item <?php echo $state ?>" onclick="e_toggle('<?php echo $perm?>', '#<?php echo "e_id_".$counter;?>');">
											<?php displayString('permissions>'.$perm)?>
										</div>
										<?php
										$counter++;
									endforeach;
									?>
								</fieldset>
								
								<?php 
							endforeach;
							
							?>
						</div>
					</div>
			</fieldset>
			<?php $bob->addButton(getString('admin>edit_user_button'), '', 'formrow', "$('#edituser').submit();")?>
		</div>
	</div>
	<?php reset($uperms); ?>
	<script>
	$("#edituser_perms_button").click(function() {
		$("#edituser_perms_list").show(100);
	});
	$("#edituser_perms_button").mouseleave(function() {
		$("#edituser_perms_list").hide(100);
	});

	var e_perms = [<?php while($cperm = current($uperms)): echo "\"$cperm\""; if(next($uperms)) echo ","; endwhile;?>]; <?php reset($uperms) ?>

	function e_toggle(perm, field) {
		if($(field).hasClass('inactive')) {
			$(field).removeClass('inactive').addClass('active');
			e_perms[e_perms.length] = perm;
			$('#e_pcount').text(e_perms.length);
		} else {
			$(field).removeClass('active').addClass('inactive');
			e_perms.splice(e_perms.indexOf(perm), 1);
			$('#e_pcount').text(e_perms.length);
		}
	}
	
	var e_mailempty = false;
	
	$('#edituser').submit(function(event) {
		event.preventDefault();
		$('#edituser_warn').hide(100);
		$('#edituser_error').hide(100);

		var e_name = $('#e_name').val(),
			e_uid = $('#e_uid').val(),
			e_email = $('#e_email').val(),
			e_p1 = $('#e_p1').val(),
			e_p2 = $('#e_p2').val(),
			e_del = 0,
			e_vs = $('#e_versammlung').val(),
			e_active = 0;

		if($('#e_active').prop('checked')) {
			e_active = 1;
		}

		if($('#e_delete').prop('checked')) {
			e_del = 1;
		}

		if(e_email == '' && e_mailempty == false && e_del == 0) {
			$('#edituser_warn').text('<?php displayString('warn>emptyMail') ?>');
			$('#edituser_warn').show(100);
			e_mailempty = true;
			return;
		}

		if(!validateEmail(e_email) && e_email != '' && e_del == 0) {
			$('#edituser_error').text('<?php displayString('errors>invalidEmail') ?>');
			$('#edituser_error').show(100);
			return;
		}

		if(e_p1 != e_p2 && e_del == 0) {
			$('#edituser_error').text('<?php displayString('errors>passwordNoMatch') ?>');
			$('#edituser_error').show(100);
			return;
		}
		if(e_name == '' || e_vs == '' && e_del == 0) {
			$('#edituser_error').text('<?php displayString('errors>invalidFormSubmit') ?>');
			$('#edituser_error').show(100);
			return;
		}

		var obj = {uid: e_uid, name: e_name, email: e_email, p1: e_p1, p2: e_p2, del: e_del, vs: e_vs, active: e_active, perms: e_perms};

		var posting = $.post('<?php echo PROTO.HOME?>/ajax/datahandler/updateuser', obj);

		posting.done(function (data) {
			console.log(data);
			jdata = JSON.parse(data);

			if(typeof jdata.error !== "undefined") {
				$('#edituser_error').text(jdata.error[0]).show(100);
				return;
			}
			if(typeof jdata.success !== "undefined") {
				if(typeof jdata.deleted !== "undefined") {
					$('#edituser').find('input').prop("disabled", true);
					$('#edituser').find('input').prop("select", true);
					$('#edituser_success').text(jdata.success[0]).show(100).delay(3000).hide(100);
					setTimeout(function() {
						closeModule('#edituser_window');
						closeModule('#usersearch_window');
					}, 3500);
				}
				$('#edituser_success').text(jdata.success[0]).show(100).delay(3000).hide(100); 
				return;
			}
				
		});
	});
	
	</script>
</div>

<script class="removeme">$(openModule('#edituser_window'));</script>