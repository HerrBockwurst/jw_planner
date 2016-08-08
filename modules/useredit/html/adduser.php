<?php global $bob, $user, $mysql; ?>

<?php 
/*
 * Liste der möglichen VS zusammenstellen
 */

$vs = getVSArray();
?>

<div id="adduser_window" class="modul" style="width: 600px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#adduser_window')" class="clickable" /></div>
	<div class="inner moremargin">
		<div id="a_print" class="moremargin">
			<?php displayString('admin>useradded')?>
			<div class="moremargin relative" id="useradd_print">
				<div class="formrow"><?php displayString('common>url')?><span><?php echo PROTO.HOME ?></span></div>
				<div class="formrow"><?php displayString('common>username')?><span id="print_username"></span></div>
				<div class="formrow"><?php displayString('common>password')?><span id="print_password"></span></div>				
			</div>
			<a style="margin-left: 200px;" class="clickable" onclick="window.print()"><?php displayString('common>print')?></a>
		</div>
		<div id="adduser_formdiv">
			<fieldset id="adduser_mainfieldset" style="width:400px;">
				<legend><?php displayString('admin>add_user')?></legend>
				<div id="adduser_warn" class="warn"></div>
				<div id="adduser_error" class="error"></div>
				<?php 
				$bob->startForm('adduser');
				$bob->addFormRow('a_name', getString('common>name'), array('text'));
				$bob->addFormRow('a_password', getString('common>password'), array('password'));
				$bob->addFormRow('a_password_rp', getString('common>password_repeat'), array('password'));
				$bob->addFormRow('a_email', getString('common>email'), array('text'));
				$bob->addFormRow('a_active', getString('admin>active'), array('checkbox'), 'checked');
				$bob->addFormRow('a_versammlung', getString('common>versammlung'), array("select", $vs, $user->vsid));
				$bob->endForm();
				?>
				</fieldset>
				<fieldset id="adduser_perms">
					<legend><?php displayString('common>permissions')?></legend>
					<div id="adduser_perms_button" class="clickable pseudobutton relative">
						<span id="pcount">0</span> <?php displayString('common>permissions')?>
						<div id="adduser_perms_list">
							<?php 
							
							/*
							 * Liste der Permissions erstellen
							 */
							
							$perms = array("admin" => $user->getSubPerm('admin.'), "calendar" => $user->getSubPerm('calendar.'), "system" => $user->getSubPerm('system.'));
							$counter = 1;
							
							foreach($perms AS $key => $mainperms):
								if($mainperms == false) break;
								?>
								<fieldset id="adduser_perms_<?php echo $key;?>" class="adduser_perms_subset">
									<legend><?php displayString('common>'.$key)?></legend>
									<?php
									
									foreach($mainperms AS $perm):
										?>
										<div id="<?php echo "id_".$counter ?>" class="clickable item inactive" onclick="toggle('<?php echo $perm?>', '#<?php echo "id_".$counter;?>');">
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
				
				<?php
				$bob->addButton(getString('admin>add_user_button'), '', 'formrow', "$('#adduser').submit();");
				
				?>
			</div>
	</div>
	<script>
		$("#adduser_perms_button").click(function() {
			$("#adduser_perms_list").show(100);
		});
		$("#adduser_perms_button").mouseleave(function() {
			$("#adduser_perms_list").hide(100);
		});

		var perms = [];
		
		function toggle(perm, field) {
			if($(field).hasClass('inactive')) {
				$(field).removeClass('inactive').addClass('active');
				perms[perms.length] = perm;
				$('#pcount').text(perms.length);
			} else {
				$(field).removeClass('active').addClass('inactive');
				perms.splice(perms.indexOf(perm), 1);
				$('#pcount').text(perms.length);
			}
		}

		var mailempty = false;

		$('#adduser').submit(function (event) {
			event.preventDefault();
			$('#adduser_warn').hide(100);
			$('#adduser_error').hide(100);
			
			var name = $('#a_name').val(),
				password = $('#a_password').val(),
				password2 = $('#a_password_rp').val(), 
				email = $('#a_email').val(),
				vs = $('#a_versammlung').val(),
				active = 0;

			if($('#a_active').prop('checked')) {
				active = 1;
			}

			
			if(email == '' && mailempty == false) {
				$('#adduser_warn').text('<?php displayString('warn>emptyMail') ?>');
				$('#adduser_warn').show(100);
				mailempty = true;
				return;
			}

			if(!validateEmail(email) && email != '') {
				$('#adduser_error').text('<?php displayString('errors>invalidEmail') ?>');
				$('#adduser_error').show(100);
				return;
			}

			if(password != password2) {
				$('#adduser_error').text('<?php displayString('errors>passwordNoMatch') ?>');
				$('#adduser_error').show(100);
				return;
			}
			if(name == '' || password == '' || password2 == '' || vs == '') {
				$('#adduser_error').text('<?php displayString('errors>invalidFormSubmit') ?>');
				$('#adduser_error').show(100);
				return;
			}

			var posting = $.post('<?php echo PROTO.HOME; ?>/ajax/datahandler/adduser', {name: name, pw1: password, pw2: password2, email: email, vs: vs, perms: perms, active: active});
			posting.done(function (data) {
				jdata = JSON.parse(data);

				if(typeof jdata.error !== "undefined") {
					$('#adduser_error').text(jdata.error[0]);
					$('#adduser_error').show(100);
					return;	
				}

				$('#print_username').text(jdata.username);
				$('#print_password').text(jdata.password);
				$('#adduser_formdiv').hide(100);
				$('#a_print').delay(100).show(100);
				
			});

		});
		
	</script>
</div>
<script class="removeme">$(openModule('#adduser_window'));</script>