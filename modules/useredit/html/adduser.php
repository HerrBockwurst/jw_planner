<?php global $bob, $user, $mysql; ?>

<?php 
/*
 * Liste der möglichen VS zusammenstellen
 */

$vs = array();

$vsperms = $user->getSubPerm('admin.useredit.vs');

if(!$vsperms): $vs[$user->vsid] = $user->versammlung;
else:
	$clearperms = array();
	
	foreach($vsperms AS $perm):
		$tmp = explode('.', $perm);
		$clearperms[] = $tmp[count($tmp) - 1];
	endforeach;
	
	$result = $mysql->execute("SELECT * FROM versammlungen");
	$result = $result->fetch_all(MYSQLI_ASSOC);

	while($row = current($result)):
		if(!in_array($row['vsid'], $clearperms) && !in_array('*', $clearperms)):
			unset($result[key($result)]);
		else:
			$vs[$row['vsid']] = utf8_encode($row['name']);
			next($result);
		endif;
	endwhile;
	
endif;
?>

<div id="adduser_window" class="modul" style="width: 600px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#adduser_window')" class="clickable" /></div>
	<div class="inner moremargin">
		<fieldset id="adduser_mainfieldset" style="width:400px;">
			<legend><?php displayString('admin>add_user')?></legend>
			<?php 
			$bob->startForm('adduser');
			$bob->addFormRow('name', getString('common>name'), array('text'));
			$bob->addFormRow('password', getString('common>password'), array('password'));
			$bob->addFormRow('password_rp', getString('common>password_repeat'), array('password'));
			$bob->addFormRow('email', getString('common>email'), array('text'));
			$bob->addFormRow('versammlung', getString('common>versammlung'), array("select", $vs, $user->vsid));
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
			$bob->addButton(getString('admin>add_user_button'));
			
			?>
		
	</div>
	<script>
		$("#adduser_perms_button").click(function() {
			$("#adduser_perms_list").show(100);
		});
		$("#adduser_perms_list").mouseleave(function() {
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
		
	</script>
</div>
<script class="removeme">$(openModule('#adduser_window'));</script>