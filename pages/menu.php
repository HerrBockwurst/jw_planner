<?php if(!defined('index')) exit; ?>

<?php global $ModulHandler, $user; ?>
<div id="mainmenu">
	<?php 
	foreach($ModulHandler->getModules() AS $modul):
		if($modul[2] == true):
		
		if($user->hasPerm($modul[3])):
			?>
			
			<span class="clickable mainmenuentry" onclick="$.get('<?php echo PROTO.HOME?>/ajax/load/modul/<?php echo $modul[0]?>', function(data) { $('#useredit').remove(); $('#site').append(data); })">
				<img src="modules/<?php echo $modul[0] ?>/icon.png" />
			</span>
					
			<?php 
		endif;
		
		endif;
	endforeach;
	
	?>

</div>

<?php
