<?php if(!isset($fromIndex)) exit;?>
<div class="field">
	<div class="headline"><?php displayText('common>calendar')?></div>
	<div class="bordered">
		<div style="text-decoration: underline"><?php displayText('admin>applied_calender')?></div>
		<?php
			while(true):
				$result = $mysql->execute("SELECT c.`cid`, c.`name`, v.`name` AS vsname FROM `calendar` AS c
											INNER JOIN `versammlungen` AS v ON (c.`versammlung` = v.`id`)
											WHERE `versammlung` = ?", 's', $USER->vsid);
				if($result == false):
					$log->write("Fehler beim Auslesen der Versammlungen", 'error');
					break;
				endif;
				
				if($result->num_rows == 0):
					displayText('errors>no_calendar_applied');
					break;
				endif;
				
				?> <div class="relative colorchangerList smallspace" > <?php
				
				$darker = false;
				while($res = $result->fetch_assoc()):
				
					if($darker == false) $darker = true; //Für wechselnde Hintergrundfarbe
					elseif($darker == true) $darker = false;
					?>
					
				
					<a href="<?php printURL()?>/<?php echo $url->value(0)?>/editcal/<?php echo $res['cid']?>" <?php if($darker == true):?>class="darkerList"<?php endif; ?> style="display:block;">
						<span style="padding: 2px; position:relative; width:100%;display:block;">
							<span style="position:absolute; left: 10px;"><?php echo utf8_encode($res['name']);?></span>
							<span style="position:absolute; right: 10px; text-align:right"><?php echo utf8_encode($res['vsname'])?></span>
							<br />
						</span>
					</a>
				
					
					<?php 					
				endwhile;
				?> </div> <?php //ColorChangerlist 
				
				break;
			endwhile;
		?>	
	</div>
	<form id="newuser" class="smallspace relative ffheight" action="<?php printURL(); ?>/<?php echo $url->value(0) ?>/newcal" method="POST">
		<input type="submit" class="inputsubmit" value="<?php displayText('admin>new_cal')?>" />
		&nbsp;
	</form>
</div>