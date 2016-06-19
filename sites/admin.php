

<div class="field">
	<div class="headline">Benutzerverwaltung</div>
	<form id="usersearch" class="bordered" action="<?php printURL(); ?>/<?php echo $url->value(0) ?>/searchuser" method="POST">
		<span style="text-decoration: underline;">Benutzer bearbeiten</span>
		<div class="relative smallspace">
			<label for="name">Name:</label><input id="name" name="name" type="text" />
			<input type="submit" class="inputsubmit" style="left: 300px;" value="Suchen" />
		</div>
	</form>
	<form id="newuser" class="smallspace relative ffheight" action="<?php printURL(); ?>/<?php echo $url->value(0) ?>/newuser" method="POST">
		<input type="submit" class="inputsubmit" value="Neuer Benutzer" />
		&nbsp;
	</form>
</div>

<?php

?>