<?php
checkIndex();
if(!$USER->hasPerm('admin.calendar')) header("Location:".getURL());
?>

<?php
switch($url->value(2)):
	case '':
?>
	<div class="field">
		<div class="headline"><?php displayText('admin>add_cal')?></div>
		<form id="caladd" method="POST" action="<?php printURL();?>/<?php echo $url->value(0);?>/<?php echo $url->value(1);?>/step2">
			<div class="smallspace formrow">
				<label for="cid"><?php displayText('admin>c_id');?>:</label>
				<input name="cid" id="cid" type="text" />
			</div>
			<div class="smallspace formrow">
				<label for="name"><?php displayText('admin>c_name');?>:</label>
				<input name="name" id="name" type="text" />
			</div>
			<div class="smallspace formrow">
				<label for="type"><?php displayText('admin>c_type');?>:</label>
				<select name="type" id="type">
					<option value="full" disabled><?php displayText('admin>c_type_full');?></option>
					<option value="selective"><?php displayText('admin>c_type_selective');?></option>
					<option value="once" disabled><?php displayText('admin>c_type_once');?></option>
				</select>
			</div>
			<input type="submit" class="inputsubmit" value="<?php displayText('admin>add_cal');?>"/>
		
		</form>
	</div>

<?php
		break;
	case 'step2':
		require_once 'sites/modules/admin/newcal_step2.php';
		break;
endswitch;

?>




