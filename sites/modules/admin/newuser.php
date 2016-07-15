<?php 
if(!isset($fromIndex)) exit;
if(!$USER->hasPerm('admin.useredit')): header("Location:".getURL()); exit; endif; 
?>

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

$versammlungen = getVersArray();

if(!isset($SUCCESS['useradd'])):
?>

<div class="field">
	<div class="headline"><?php displayText('admin>add_user');?></div>
	<?php if(isset($ERROR['useradd'])): ?><div class="error"><?php echo $ERROR['useradd']; ?></div><?php endif;?>
	<form id="useradd" action="<?php printURL(); ?>/<?php echo $url->value(0)?>/<?php echo $url->value(1)?>/add" method="POST">
		<div class="smallspace formrow">
			<label for="versammlung"><?php displayText('common>vers');?>:</label>
			<select name="versammlung" id="versammlung">
				<?php foreach($versammlungen AS $id => $vs): ?>
				<option value="<?php echo $id;?>"><?php echo $vs; ?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="smallspace formrow">
			<label for="name"><?php displayText('common>name');?>:</label>
			<input type="text" id="name" name="name" />
			<label class="smaller" style="position:absolute; right:0px; line-height: 22px;"><input type="checkbox" name="active" value="1" style="position: absolute; top:2px; left: -20px" checked> <?php displayText('admin>active');?></label>
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
			<input type="text" id="email" name="email" />
		</div>
		
		<div id="useradd_rights" class="smallspace formrow bordered small">
		<p class="fett"><?php displayText('common>permissions');?></p>
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
		<input type="submit" class="inputsubmit" value="<?php displayText('admin>add_user_button')?>" />
	</form>	
</div>

<?php else: ?>

<div class="field">
	<div class="headline"><?php displayText('admin>user_added');?></div>
	<div style="width:38%; float:left;">
		<?php displayText('common>adress');?>:<br />
		<?php displayText('common>username');?>:<br />
		<?php displayText('common>password');?>:<br />
	</div>
	<div style="width:60%; float:left;">
		<?php printURL(); ?><br />
		<?php echo $username; ?><br />
		<?php echo $cleanpw; ?><br />
	</div>
	<br class="floatbreak" />
	<div class="morespace relative">
		<a href="<?php printURL();?>/printdata?u=<?php echo $username;?>&p=<?php echo $cleanpw; ?>" target="_blank"><?php displayText('common>print')?></a>
		<a href="<?php printURL();?>/<?php echo $url->value(0); ?>" style="position:absolute; left:100px"><?php displayText('common>back')?></a>
	</div>
</div>

<?php endif;?>