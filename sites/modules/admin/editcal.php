<?php if(!$USER->hasPerm('admin.calendar')): header("Location:".getURL()); exit; endif; ?>
<?php

while(true):
	if(isset($ERROR['editcal'])) break; //Abbrechen, wenn bereits ein Error geworfen wurde
	
	if(isset($_POST['headsubmit'])):
		require_once 'libs/c_headeredit.php';
	elseif(isset($_POST['psubmit'])):
		require_once 'libs/c_postedit.php';
	endif;
	break;
endwhile;

while(true):
	/*
	 *  Kalenderinformation sammeln
	 */
	
	$result = $mysql->execute("SELECT * FROM `calendar` WHERE `cid` = ? LIMIT 1", 's', $url->value(2));
	if($result->num_rows != 1):
		$ERROR['editcal'] = getLang('errors>calinvalid');
		break;
	endif;
	
	$result = $result->fetch_assoc();
	
	/*
	 * Sicherheitsprüfung ob Kalender auch aus selber Versammlung kommt
	 */
	
	if($USER->vsid != $result['versammlung']):
		$ERROR['editcal'] = getLang('errors>noperm');
		break;
	endif;
	
	/*
	 * Meta auslesen und erstellen
	 */
	
	if($result['meta'] != "") $meta = json_decode($result['meta'], true);
	else $meta = array();

	break;
endwhile;

?>

<div class="field"> <!-- Linkes Feld -->
	<div class="headline"><?php displayText('admin>edit_cal')?></div>
	<?php if(isset($ERROR['editcal']) && !isset($SUCCESS['editcal'])): $noform = true; ?><div class="error"><?php echo $ERROR['editcal'];?></div><?php endif;?>
	<?php if(isset($SUCCESS['editcal'])): ?><div class="success"><?php echo $SUCCESS['editcal']?></div><?php endif;?>
	
	
	<?php if(!isset($noform)):?>
	<form action="<?php printURL();?>/<?php echo $url->value(0);?>/<?php echo $url->value(1);?>/<?php echo $result['cid'] ?>" method="POST" id="editcal">
		<div class="formrow smallspace">
			<label for="name"><?php displayText('admin>c_name')?></label>
			<input type="text" id="name" name="name" value="<?php echo $result['name'];?>" />
		</div>
		<div class="formrow smallspace">
			<label for="id"><?php displayText('admin>c_id')?></label>
			<input type="text" id="id" name="id" value="<?php echo $result['cid'];?>" disabled/>
		</div>
		<div class="formrow smallspace">
			<label for="type"><?php displayText('admin>c_type')?></label>
			<input type="text" id="type" value="<?php displayText("admin>c_type_".$result['type'])?>" disabled/>
		</div>
		<div class="formrow smallspace">
			<label for="delete"><?php displayText('admin>delete_cal')?></label>
			<input type="checkbox" name="delete" id="delete" value="1" />
		</div>
		<input type="hidden" name="headsubmit" value="1" />
		<input type="submit" class="inputsubmit" value="<?php displayText('admin>update_cal')?>" />
		
	</form>
	<?php endif;?>
	
	<div class="morespace">
		<a href="<?php printURL();?>/<?php echo $url->value(0); ?>"><?php displayText('common>back')?></a>
	</div>
</div>
<?php if(!isset($noform)): ?>
<div class="field"> <!-- Rechtes Feld -->
	<div class="headline"><?php displayText('admin>c_posts')?></div>	
	
	<?php
	if(empty(array_filter($meta))): displayText('admin>no_posts_applied');
	else:
		$darker = false;
		foreach($meta AS $id => $cmeta): 
			if($darker == false) $darker = true;
			else $darker = false;
		
		?>
		
		
		<div class="relative <?php if($darker) echo "darkerList"; else echo "lightList"; ?>" style="padding: 2px;">
			<?php displayText('admin>p_'.$cmeta['type']) ?>
			<?php if($cmeta['type'] == 'weekly'): ?>
				<span style="position:absolute; left: 120px;"><?php displayText('admin>p_every')?> <?php displayText('common>'.$cmeta['patternA'])?></span>
			<?php else:?>
				<span style="position:absolute; left: 120px;"><?php displayText('admin>p_every')?> <?php displayText('admin>p_'.$cmeta['patternA'])?> <?php displayText('common>'.$cmeta['patternB'])?></span>
			<?php endif; ?>
				<span style="position:absolute; left: 280px;">
					<?php displayText('admin>p_from')?> <?php echo $cmeta['start']?> <?php displayText('admin>p_to')?> <?php echo $cmeta['end'] ?> 
				</span>
				<span style="position:absolute; right: 2px;">
					<a href="<?php printURL()?>/<?php echo $url->value(0)?>/deletepost/<?php echo $url->value(2)?>/<?php echo $id?>">
						<img alt="delete" src="<?php printURL()?>/images/del.png" />
					</a>
				</span>
		</div>
				
	 	<?php 
	 	endforeach;
	endif; ?>
	<form class="bordered smallspace" id="addpost" method="POST" action="<?php printURL();?>/<?php echo $url->value(0);?>/<?php echo $url->value(1);?>/<?php echo $result['cid'] ?>">
		<div style="font-weight: bold;"><?php displayText('admin>add_post')?></div>
		<?php if(isset($ERROR['postserror'])): ?><div class="error"><?php echo $ERROR['postserror'];?></div><?php endif;?>
		<?php if(isset($SUCCESS['posts'])): ?><div class="success"><?php echo $SUCCESS['posts']; ?></div><?php endif;?>
		<div class="formrow smallspace">
			<label for="p_type"><?php displayText('admin>p_type')?></label>
			<select id="p_type" name="type">
				<option value="weekly"><?php displayText('admin>p_weekly');?></option>
				<option value="monthly"><?php displayText('admin>p_monthly');?></option>				
			</select>
		</div>
		<div class="formrow smallspace">
			<label for="startdate"><?php displayText('admin>p_startdate')?></label>
			<input type="text" name="startdate" id="startdate" value="<?php echo date("d.m.Y"); ?>" />
		</div>
		<div class="formrow smallspace">
			<label for="visibility1"><?php displayText('admin>p_visibility')?></label>
			<input type="text" id="visibility1" name="visibility1" value="1" style="min-width: 20px; width: 20px;" />
			<select name="visibility2" style="left: 180px; min-width: 100px; width: 100px;">
				<option value="week"><?php displayText('common>week')?></option>
				<option value="month"><?php displayText('common>month')?></option>
			</select>
		</div>
		<div class="bordered relative">
			<div id="p_weekly">
				<div class="formrow">
					<?php displayText('admin>p_every')?>
					<select name="w_tag">
						<option value="monday"><?php displayText('common>monday')?></option>
						<option value="tuesday"><?php displayText('common>tuesday')?></option>
						<option value="wednesday"><?php displayText('common>wednesday')?></option>
						<option value="thursday"><?php displayText('common>thursday')?></option>
						<option value="friday"><?php displayText('common>friday')?></option>
						<option value="saturday"><?php displayText('common>saturday')?></option>
						<option value="sunday"><?php displayText('common>sunday')?></option>
					</select>			
				</div>
				<div class="formrow smallspace">
					<?php displayText('admin>p_from')?>
					<input type="text" name="w_from" value="<?php echo date("H:i")?>" />
				</div>
				<div class="formrow smallspace">
					<?php displayText('admin>p_to')?>
					<input type="text" name="w_to" value="<?php echo date("H:i", time() + (60*60))?>" />
				</div>
			</div>
			<div id="p_monthly" style="display:none">
				<div class="formrow">
					<?php displayText('admin>p_every')?>
					<select name="m_every">
						<option value="first"><?php displayText('admin>p_first')?></option>
						<option value="second"><?php displayText('admin>p_second')?></option>
						<option value="third"><?php displayText('admin>p_third')?></option>
						<option value="fourth"><?php displayText('admin>p_fourth')?></option>
					</select>
				</div>
				<div class="formrow smallspace">
					&nbsp;
					<select name="m_tag">
						<option value="monday"><?php displayText('common>monday')?></option>
						<option value="tuesday"><?php displayText('common>tuesday')?></option>
						<option value="wednesday"><?php displayText('common>wednesday')?></option>
						<option value="thursday"><?php displayText('common>thursday')?></option>
						<option value="friday"><?php displayText('common>friday')?></option>
						<option value="saturday"><?php displayText('common>saturday')?></option>
						<option value="sunday"><?php displayText('common>sunday')?></option>
					</select>
				</div>
				<div class="formrow smallspace">
					<?php displayText('admin>p_from')?>
					<input type="text" name="m_from" value="<?php echo date("H:i")?>" />
				</div>
				<div class="formrow smallspace">
					<?php displayText('admin>p_to')?>
					<input type="text" name="m_to" value="<?php echo date("H:i", time() + (60*60))?>" />
				</div>
			</div>
			<input type="hidden" name="psubmit" value="1" />
			<input type="submit" class="inputsubmit" value="<?php displayText('admin>add_post')?>" />
		</div>
	</form>
</div>
<script src="<?php printURL(); ?>/scripts/updatePosts.js"></script>
<?php endif;?>