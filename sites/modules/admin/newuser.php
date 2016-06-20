<?php if(!$USER->hasPerm('admin.useredit')): header("Location:".printURL()); exit; endif; ?>

<?php 
/*
 * Code für Benutzer anlegen
 */

while(true):
	if(!isset($_POST['submitted'])) break;

	
	/*
	 * Testet ob Benutzer die Berechtigung für Versammlung hat
	 */

	if($_POST['versammlung'] != $USER->versammlung &&
		!$USER->hasPerm('admin.useredit.vs.'.$_POST['versammlung']) &&
		!$USER->hasPerm('admin.useredit.global')):
			$ERROR['useradd'] = getLang('errors>noperm');
			break;
	endif;
	
	/*
	 * Teste auf alles ausgefüllt
	 */
	
	if($_POST[''] == "" ):
	endif;
	
	/*
	 * Teste ob Passwörter überein stimmen
	 */
		
	/*
	 * Teste Permissions
	 */
	
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
		</div>
		<div class="smallspace formrow">
			<label for="password">Passwort:</label>
			<input type="password" id="password" name="password" />
			<input type="checkbox" name="noexpire" value="1" style="position:absolute; left: 370px;" id="noexpire">
			<label class="smaller formrow" style="position:absolute; left: 400px" for="noexpire">Laeuft nicht aus</label>
		</div>
		<div class="smallspace formrow">
			<label for="password">Passwort wiederholen:</label>
			<input type="password" id="password" name="password" />
		</div>
		<div class="smallspace formrow">
			<label for="email">EMail:</label>
			<input type="text" id="email" name="email" />
		</div>
		
		<div id="useradd_rights" class="smallspace formrow bordered small">
		<p class="fett">Berechtigungen</p>
			<ul>
				<li><label><input type="checkbox" name="" value="">permission</label></li>
				<li><label><input type="checkbox" name="" value="">permisdsdssdssion</label></li>
				<li><label><input type="checkbox" name="" value="">permission</label></li>
				<li><label><input type="checkbox" name="" value="">permission</label></li>
			</ul>
			<ul>
				<li><label><input type="checkbox" name="" value="">permission</label></li>
				<li><label><input type="checkbox" name="" value="">permission</label></li>
				<li><label><input type="checkbox" name="" value="">permission</label></li>
				<li><label><input type="checkbox" name="" value="">permission</label></li>
			</ul>
			<br class="floatbreak" />
		</div>
		<input type="hidden" name="submitted" value="1" />
		<input type="submit" class="inputsubmit" value="Benutzer anlegen" />
	</form>	
</div>

<?php
?>