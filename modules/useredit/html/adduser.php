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
		<fieldset style="width:400px;">
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
			
			<?php
			$bob->addButton(getString('admin>add_user_button'));
			
			?>
		
	</div>
</div>
<script class="removeme">$(openModule('#adduser_window'));</script>