<?php checkIndex(); ?>
<div class="field">
	<div class="headline"><?php displayText('admin>ucp_head')?></div>
	<form id="usersearch" class="bordered" action="<?php printURL(); ?>/<?php echo $url->value(0) ?>/searchuser" method="POST">
		<span style="text-decoration: underline;"><?php displayText('admin>edit_user')?></span>
		<div class="formrow smallspace">
			<label for="name"><?php displayText('common>name')?>:</label><input id="name" name="name" type="text" />
			<input type="submit" class="inputsubmit" style="left: 300px;" value="<?php displayText('common>search')?>" />
		</div>
	</form>
	<form id="newuser" class="smallspace relative ffheight" action="<?php printURL(); ?>/<?php echo $url->value(0) ?>/newuser" method="POST">
		<input type="submit" class="inputsubmit" value="<?php displayText('admin>add_user')?>" />
		&nbsp;
	</form>
</div>