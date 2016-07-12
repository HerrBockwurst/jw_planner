<div class="field">
	<div class="headline"><?php displayText('common>calendar')?></div>
	<div class="bordered">
		<div style="text-decoration: underline"><?php displayText('admin>applied_calender')?></div>
		<?php
			while(true):
				$result = $mysql->execute("SELECT * FROM `calendar` WHERE `versammlung` = ?", 's', $USER->vsid);
				if($result == false):
					$log->write("Fehler beim Auslesen der Versammlungen", 'error');
					break;
				endif;
				
				if($result->num_rows == 0):
					displayText('errors>no_calendar_applied');
					break;
				endif;
				
				break;
			endwhile;
		?>
	</div>
</div>