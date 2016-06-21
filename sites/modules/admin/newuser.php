<?php if(!$USER->hasPerm('admin.useredit')): header("Location:".printURL()); exit; endif; ?>

<?php 
/*
 * Code f�r Benutzer anlegen
 */

while(true):
	if(!isset($_POST['submitted'])) break;

	
	/*
	 * Testet ob Benutzer die Berechtigung f�r Versammlung hat
	 */

	if($_POST['versammlung'] != $USER->versammlung &&
		!$USER->hasPerm('admin.useredit.vs.'.$_POST['versammlung']) &&
		!$USER->hasPerm('admin.useredit.global')):
			$ERROR['useradd'] = getLang('errors>noperm');
			break;
	endif;
	

	/*
	 * Teste Permissions
	 */
	
	foreach($_POST AS $key => $value):
		if(strpos($key, 'permission') !== false && !$USER->hasPerm(str_replace('_', '.', substr($key, 11)))):
			$ERROR['useradd'] = getLang('errors>noperm');
			break 2;
		endif;
	endforeach;
	
	/*
	 * Teste auf alles ausgef�llt
	 */
	
	if($_POST['name'] == "" || $_POST['password'] == "" || $_POST['password2'] == "" || $_POST['versammlung'] == ""):
		$ERROR['useradd'] = getLang('errors>emptyfields');
		break;
	endif;
	
	/*
	 * Teste ob Passw�rter �berein stimmen
	 */
	
	if(utf8_decode($_POST['password']) != utf8_decode($_POST['password2'])):
		$ERROR['useradd'] = getLang('errors>passwordnomatch');
		break;
	endif;
	
	/*
	 * Alles OK, eintragen
	 */
	
	$password = hash('sha512', utf8_decode($_POST['password']));
	$username = str_replace(' ', '-', utf8_decode($_POST['name'])); // Replaces all spaces with hyphens.
	$username = str_replace('�', 'ae', $username);
	$username = str_replace('�', 'oe', $username);
	$username = str_replace('�', 'ue', $username);
	$username = str_replace('�', 'ss', $username);
	$username = preg_replace('/[^A-Za-z\-]/', '', $username);
	$username = strtolower($username);
	
	echo $username;
	
	break;
endwhile;
?>


<?php

/*
 * Code f�r Zugriffsrechte auf Versammlung
 */



$result = $mysql->execute("SELECT `id`, `name` FROM `versammlungen`");
$versammlungen = array();
$accessvs = $USER->getSubPerm('admin.useredit.vs.');

if($USER->hasPerm('admin.useredit.global')):

	while($row = $result->fetch_assoc()) $versammlungen[$row['id']] = $row['name'];
	 
elseif($accessvs != false):		
	
	while($row = $result->fetch_assoc()):
		foreach($accessvs as $vs)
			if($row['id'] == $vs) $versammlungen[$row['id']] = $row['name'];
	endwhile;
	if(!in_array($USER->versammlung, $versammlungen)): $versammlungen[$USER->vsid] = $USER->versammlung; endif;
else:
	$versammlungen[$USER->vsid] = $USER->versammlung;	
endif;

?>

<div class="field">
	<div class="headline">Benutzer anlegen</div>
	<?php if(isset($ERROR['useradd'])): ?><div class="error"><?php echo $ERROR['useradd']; ?></div><?php endif;?>
	<form id="useradd" action="<?php printURL(); ?>/<?php echo $url->value(0)?>/<?php echo $url->value(1)?>/add" method="POST">
		<div class="smallspace formrow">
			<label for="versammlung">Versammlung:</label>
			<select name="versammlung" id="versammlung">
				<?php foreach($versammlungen AS $id => $vs): ?>
				<option value="<?php echo $id;?>"><?php echo utf8_encode($vs); ?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="smallspace formrow">
			<label for="name">Name:</label>
			<input type="text" id="name" name="name" />
		</div>
		<div class="smallspace formrow">
			<label for="password">Passwort:</label>
			<input type="password" id="password" name="password" />
			<input type="checkbox" name="noexpire" value="1" style="position:absolute; left: 370px;" id="noexpire">
			<label class="smaller formrow" style="position:absolute; left: 400px" for="noexpire">Laeuft nicht aus</label>
		</div>
		<div class="smallspace formrow">
			<label for="password2">Passwort wiederholen:</label>
			<input type="password" id="password2" name="password2" />
		</div>
		<div class="smallspace formrow">
			<label for="email">EMail:</label>
			<input type="text" id="email" name="email" />
		</div>
		
		<div id="useradd_rights" class="smallspace formrow bordered small">
		<p class="fett">Berechtigungen</p>
			<?php
				$links = round($USER->countPerms() / 2, 0, PHP_ROUND_HALF_UP); 
				$rechts = $USER->countPerms() - $links;
				$perms = $USER->getPerms();
				$order = $lang->getPermOrder();
				$orderedperms = array();
				
				foreach($order AS $key => $orObj) if(in_array($orObj, $perms)) $orderedperms[] = $order[$key];
			?>				
			<ul>				
			<?php for($i=0; $i<($links); $i++):	if(strpos($perms[$i], 'admin.useredit.vs') === false):	//Letze IF ist daf�r da, um die Permissions f�r die Benutzerverwaltung einzelner Versammlungen zu filtern ?> 				
				<li><label><input type="checkbox" name="permission.<?php echo $orderedperms[$i]?>" value="1"><?php displayText('permissions>'.$orderedperms[$i])?></label></li>					
			<?php endif; endfor; ?>
			</ul>
			<ul>
			<?php for($i=$links; $i<($rechts+$links); $i++): if(strpos($perms[$i], 'admin.useredit.vs') === false): ?>				
				<li><label><input type="checkbox" name="permission.<?php echo $orderedperms[$i]?>" value="1"><?php displayText('permissions>'.$orderedperms[$i])?></label></li>	
			<?php endif; endfor; ?>
			</ul>
			<br class="floatbreak" />
		</div>
		<input type="hidden" name="submitted" value="1" />
		<input type="submit" class="inputsubmit" value="Benutzer anlegen" />
	</form>	
</div>

<?php
?>