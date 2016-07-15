<!DOCTYPE HTML>
<html>
	<head>
		<title><?php printTitle(true); ?></title>
		<link rel="stylesheet" href="<?php printURL();?>/reset.css"></link>
		<link rel="stylesheet" href="<?php printURL();?>/<?php getcss(); ?>"></link>
		<meta charset="utf-8">
		<script src="<?php printURL(); ?>/scripts/jquery-3.0.0.min.js"></script>
		<script>
			var url = "<?php printURL(); ?>";
			var udata = { uid: "<?php echo $USER->uid;?>" };
		</script>
		
	</head>
	<body>
		<?php if($url->value(0) != 'login'): //Nur ausgeben, wenn keine Login Maske ?>
		<div id="header">
			<a id="logout" href="<?php printURL(); ?>/logout"><?php displayText("menu>logout")?></a>
			<a id="logo" href="<?php printURL(); ?>"><span style="font-size:3em">JW</span><span style="font-size:1.2em">Planner</span></a>
			<div id="menu">
				<ul>
					<li><?php displayMenuLink('menu>calendar', '/calendar'); ?></li>
					<li><?php displayMenuLink('menu>profile', '/profile'); ?></li>
					<?php if($USER->hasPerm('admin.visible')): ?><li><?php displayMenuLink('menu>admin', '/admin'); ?></li><?php endif; ?>
					<?php if($USER->hasPerm('dev.visible')): ?><li><?php displayMenuLink('menu>dev', '/system'); ?></li><?php endif; ?>
				</ul>
			</div>			
		</div>		
		<div id="wrapper">
		<?php endif; ?>


<?php
?>