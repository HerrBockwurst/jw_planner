<?php if(!defined('index')) exit;?>
<?php global $user;?>

<div id="topbar">
	<div id="time"><?php echo date("d.m.Y H:i", time())?></div>
	<div id="usericon"><?php echo $user->name ?> <img src="images/guy.png" /></div>
</div>

