<?php 
if(!defined('index')) exit;
global $mysql, $user;
if(!$user->hasPerm('admin.calendar')) exit;
?>
<div id="calendaradmin" class="modul" style="width: 580px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#calendaradmin')" class="clickable" /></div>
	<div class="inner" id="calendaradmin_inner">
		<div class="relative">
			<fieldset id="cadmin_main_search">
				<legend><?php displayString('admin>search')?></legend>
			</fieldset>
			<div id="button_posts" class="pseudobutton clickable" onclick="loadModule('<?php echo PROTO.HOME ?>/ajax/load/modul/calendaradmin/posts', '#cadmin_posts')">
				<span><?php displayString('admin>posts')?></span>
			</div>
			<div id="button_cal" class="pseudobutton clickable" onclick="loadModule('<?php echo PROTO.HOME ?>/ajax/load/modul/calendaradmin/newcal', '#cadmin_newcal')">
				<span><?php displayString('admin>new_cal')?></span>
			</div>
		</div>
		<fieldset id="cadmin_main_list">
			<legend><?php displayString('admin>cal_own_vs')?></legend>
			<div id="cadmin_main_list_inner" class="relative">
				<?php 
				
				$result = $mysql->execute("SELECT c.*, u.name AS adminname FROM calendar AS c INNER JOIN user AS u ON (c.admin=u.uid)");
				if($result->num_rows == 0):
					?><div class="item darker"><?php displayString('calendar>no_cal_applied')?></div> <?php
				else:
					$i = 0;
					while($row = $result->fetch_assoc):
						?>
						<div class="item clickable relative <?php if(($i % 2) == 0) echo 'darker'; ?>">
							<span style="position:relative; left: 0px;"><?php echo $row['name']?> (#<?php echo $row['cid']?>)</span>
							<span style="position:relative; right: 0px;"><?php echo $row['adminname']?> (<?php echo $row['admin']?>)</span>
						</div>
						<?php 
					endwhile;
				endif;
				
				?>
			</div>
		</fieldset>
	</div>	
</div>
<script class="removeme">$(openModule('#calendaradmin'));</script>