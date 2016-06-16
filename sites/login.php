<?php 
if(isset($_POST['submitted'])):
	if($_POST['username'] != "" || $_POST['password'] != ""):
		$result = $mysql->execute("SELECT * FROM `users` WHERE `uid` = ".$_POST['username']);

		
		
	endif;
	
	$error = getText('errors>login_wrong');
	return;
endif;

?>

<div id="loginwrapper">
	<a id="loginlogo" href="<?php printURL(); ?>"><span style="font-size:3.5em">JW</span><span style="font-size:1.5em">Planner</span></a>
	<div id="loginformwrapper">
		<form id="login" action="<?php printURL(); ?>/login" method="POST">
			<div class="pos_rel">
				<label for="username"><?php displayText('login>user'); ?>:</label>
				<input type="text" id="username" name="username" class="loginformfield" value="<?php printIfSet($_POST, "username"); ?>" />
				<img class="loginimage" src="<?php printURL();?>/images/user.png" />
				&nbsp;
			</div>
			<div class="pos_rel" style="margin-top: 20px;">
				<label for="password"><?php displayText('login>password'); ?>:</label>
				<input type="password" id="password" name="password" class="loginformfield"/>
				<img class="loginimage" src="<?php printURL();?>/images/key.png" />
				&nbsp;
			</div>
			<div class="pos_rel center" style="margin-top: 30px; margin-bottom: 10px;">
				<input type="submit" class="loginsubmit" value="<?php displayText('login>submit'); ?>">
			</div>
			<input type="hidden" name="submitted" value="1" />
		</form>
		<!--  comming soon <span class="smaller"><?php displayText('login>forgotten'); ?></span> -->
	</div>
	<div class="center small"><a href="mailto:<?php echo $CONFIG['contactmail']; ?>"><?php displayText('login>wantaccess')?></a></div>
</div>

<?php
//TODO
?>