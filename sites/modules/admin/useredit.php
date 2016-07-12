<?php if(!$USER->hasPerm('admin.useredit')): header("Location:".printURL()); exit; endif; ?>

<?php 
/*
 * Wenn Formular übergeben
 */

while(true):
	if(isset($_POST['submitted'])) require_once 'libs/edituser.php';
		
	break;
endwhile;
	
?>

<?php
/*
 * Prefill Daten für Formular
 */
while(true):
	if(isset($SUCCESS['userdel'])) break;

	$result = $mysql->execute("SELECT * FROM `users` WHERE `uid` = ? LIMIT 1", 's', $url->value(2));
	if($result->num_rows == 0):
		$ERROR['useredit'] = getLang('errors>userinvalid');
		break;
	endif;
		
	$euserdata = $result->fetch_assoc();
	$result = $mysql->execute("SELECT * FROM `permissions` WHERE `uid` = ?", 's', $url->value(2));
	
	$euserperms = array();
	foreach($result->fetch_all(MYSQLI_ASSOC) AS $row)
		$euserperms[] = $row['perm'];
	
	$versammlungen = getVersArray();
	
	if(!array_key_exists($euserdata['versammlung'], $versammlungen)):
		$ERROR['useredit'] = getLang('errors>noperm');
		break;
	endif;
	
	
	
	
	break;
endwhile;


?>

<div class="field">
	<div class="headline"><?php displayText('admin>edit_user');?></div>
	<?php if(isset($ERROR['useredit'])): $noform = true; ?><div class="error"><?php echo $ERROR['useredit']; ?></div><?php endif;?>
	<?php if(isset($SUCCESS['userdel'])): $noform = true; ?><div class="success"><?php displayText('admin>userdeleted') ?></div><?php endif;?>
	<?php if(isset($SUCCESS['useredit'])): ?><div class="success"><?php displayText('admin>user_edited') ?></div><?php endif;?>
	
	
	<?php if(!isset($noform)): //Formular nur anzeigen, wenn benutzer existiert ?> 
	
	<form id="useradd" action="<?php printURL(); ?>/<?php echo $url->value(0)?>/<?php echo $url->value(1)?>/<?php echo $url->value(2)?>" method="POST">
		<div class="smallspace formrow">
			<label for="versammlung"><?php displayText('common>vers');?>:</label>
			<select name="versammlung" id="versammlung">
				<?php foreach($versammlungen AS $id => $vs): ?>
				<option value="<?php echo $id;?>" <?php if($euserdata['versammlung'] == $id) echo "selected"; ?>><?php echo $vs; ?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="smallspace formrow">
			<label for="username"><?php displayText('common>username');?>:</label>
			<input type="text" id="username" name="username" value="<?php echo $euserdata['uid'];?>" disabled />
			<label class="smaller" style="position:absolute; right:0px; line-height: 22px;"><input type="checkbox" name="delete" value="1" style="position: absolute; top:2px; left: -20px"> <?php displayText('admin>userdelete');?></label>
		</div>
		<div class="smallspace formrow">
			<label for="name"><?php displayText('common>name');?>:</label>
			<input type="text" id="name" name="name" value="<?php echo $euserdata['name'];?>" />
			<label class="smaller" style="position:absolute; right:0px; line-height: 22px;"><input type="checkbox" name="active" value="1" style="position: absolute; top:2px; left: -20px" <?php if($euserdata['status'] == 'active') echo "checked"; ?>> <?php displayText('admin>active');?></label>
		</div>
		<div class="smallspace formrow">
			<label for="password"><?php displayText('common>password');?>:</label>
			<input type="password" id="password" name="password" />		
			<label class="smaller" style="position:absolute; right:0px; line-height: 22px;"><input type="checkbox" name="noexpire" value="1" style="position: absolute; top:2px; left: -20px"> <?php displayText('admin>noexpire');?></label>
		</div>
		<div class="smallspace formrow">
			<label for="password2"><?php displayText('admin>password_repeat');?>:</label>
			<input type="password" id="password2" name="password2" />
		</div>
		<div class="smallspace formrow">
			<label for="email"><?php displayText('common>mail');?>:</label>
			<input type="text" id="email" name="email" value="<?php echo $euserdata['email']; ?>" />
		</div>
		
		<div id="useradd_rights" class="smallspace formrow bordered small">
		<p class="fett"><?php displayText('common>permissions');?></p>
			<?php
				$perms = $USER->getPerms();
				foreach ($euserperms AS $perm)
					if(!in_array($perm, $perms))
						$perms[] = $perm;
				
				
				$links = round(count($perms) / 2, 0, PHP_ROUND_HALF_UP); 
				$rechts = count($perms) - $links;
				$order = $lang->getPermOrder();
				$orderedperms = array();
				
				foreach($order AS $key => $orObj) if(in_array($orObj, $perms)) $orderedperms[] = $order[$key];
			?>
			<ul>				
			<?php for($i=0; $i<($links); $i++):	if(strpos($perms[$i], 'admin.useredit.vs') === false):	//Letze IF ist dafür da, um die Permissions für die Benutzerverwaltung einzelner Versammlungen zu filtern ?> 				
				<li><label><input type="checkbox" name="permission.<?php echo $orderedperms[$i]?>" value="1" <?php if(in_array($orderedperms[$i], $euserperms)) echo "checked"; ?>><?php displayText('permissions>'.$orderedperms[$i])?></label><input type="hidden" value="1" name="hidden.<?php echo $orderedperms[$i]?>" /></li>
			<?php endif; endfor; ?>
			</ul>
			<ul>
			<?php for($i=$links; $i<($rechts+$links); $i++): if(strpos($perms[$i], 'admin.useredit.vs') === false): ?>				
				<li><label><input type="checkbox" name="permission.<?php echo $orderedperms[$i]?>" value="1" <?php if(in_array($orderedperms[$i], $euserperms)) echo "checked"; ?>><?php displayText('permissions>'.$orderedperms[$i])?></label><input type="hidden" value="1" name="hidden.<?php echo $orderedperms[$i]?>" /></li>	
			<?php endif; endfor; ?>
			</ul>
			<br class="floatbreak" />
		</div>
		<input type="hidden" name="submitted" value="1" />
		<input type="submit" class="inputsubmit" value="<?php displayText('admin>edit_user_button')?>" />
	</form>	
	<?php else: ?>
	<div class="morespace">
		<a href="<?php printURL();?>/<?php echo $url->value(0); ?>"><?php displayText('common>back')?></a>
	</div>
	<?php endif;?>
</div> 