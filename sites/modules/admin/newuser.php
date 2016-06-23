<?php if(!$USER->hasPerm('admin.useredit')): header("Location:".printURL()); exit; endif; ?>

<?php 
/*
 * Code für Benutzer anlegen
 */

while(true):
	if(!isset($_POST['submitted'])) break;

	if($url->value(2) == 'add') require_once 'libs/adduser.php';
		
	break;
endwhile;
?>


<?php

/*
 * Code für Zugriffsrechte auf Versammlung
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
			<label class="smaller" style="position:absolute; right:0px; line-height: 22px;"><input type="checkbox" name="active" value="1" style="position: absolute; top:2px; left: -20px" checked> Aktiv</label>
		</div>
		<div class="smallspace formrow">
			<label for="password">Passwort:</label>
			<input type="password" id="password" name="password" />		
			<label class="smaller" style="position:absolute; right:0px; line-height: 22px;"><input type="checkbox" name="noexpire" value="1" style="position: absolute; top:2px; left: -20px"> Laeuft nicht aus</label>
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
			<?php for($i=0; $i<($links); $i++):	if(strpos($perms[$i], 'admin.useredit.vs') === false):	//Letze IF ist dafür da, um die Permissions für die Benutzerverwaltung einzelner Versammlungen zu filtern ?> 				
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