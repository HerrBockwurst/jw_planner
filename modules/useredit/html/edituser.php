<?php
global $user;
?>

<div id="edituser_window" class="modul" style="width: 600px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#edituser_window')" class="clickable" /></div>
	<div class="inner">
		<div id="edituser_formdiv">
			<fieldset id="edituser_mainfieldset" style="width:400px;">
				<legend><?php displayString('admin>edit_user')?></legend>
				<div id="edituser_warn" class="warn"></div>
				<div id="edituser_error" class="error"></div>
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
							$uperms = $_POST['perms'];
							foreach($uperms AS $key => $perm) if(!$user->hasPerm($perm)) unset($uperms[$key]); //Perms die der Benutzer nicht hat, werden nicht angezeigt
								
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
										$state = 'inactive';
										if(in_array($perm, $activeperms)) $state = 'active';
										?>
										<div id="<?php echo "id_".$counter ?>" class="clickable item <?php echo $state ?>" onclick="toggle('<?php echo $perm?>', '#<?php echo "id_".$counter;?>');">
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
		<?php var_dump($_POST)?>
	</div>
</div>
<script>
	$("#edituser_perms_button").click(function() {
		$("#edituser_perms_list").show(100);
	});
	$("#edituser_perms_button").mouseleave(function() {
		$("#edituser_perms_list").hide(100);
	});

	var permsToDel = [];
	var permsToAdd = [];
 

	function e_toggle(perm, field) {
		if($(field).hasClass('inactive')) {
			$(field).removeClass('inactive').addClass('active');
			perms[perms.length] = perm;
			$('#e_pcount').text(perms.length);
		} else {
			$(field).removeClass('active').addClass('inactive');
			perms.splice(perms.indexOf(perm), 1);
			$('#e_pcount').text(perms.length);
		}
	}
	
</script>
<script class="removeme">$(openModule('#edituser_window'));</script>