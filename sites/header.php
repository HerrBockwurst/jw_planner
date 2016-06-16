<!DOCTYPE HTML>
<html>
	<head>
		<title><?php printTitle(true); ?></title>
		<link rel="stylesheet" href="<?php printURL();?>/reset.css"></link>
		<link rel="stylesheet" href="<?php printURL();?>/<?php getcss(); ?>"></link>
		<meta charset="utf-8">
	</head>
	<body>
		<?php if($url->value(0) != 'login'): //Nur ausgeben, wenn keine Login Maske ?>
		<div id="header">
			<div id="logo"><span style="font-size:3em">JW</span><span style="font-size:1.2em">Planner</span></div>
			<div id="menu">
				<ul>
					<li><?php displayMenuLink('menu>calendar', '/calendar'); ?></li>
					<li><?php displayMenuLink('menu>profile', '/profile'); ?></li>
					<li><?php displayMenuLink('menu>admin', '/admin'); ?></li>
					<li><?php displayMenuLink('menu>dev', '/system'); ?></li>
				</ul>
			</div>			
		</div>		
		<div id="wrapper">
		<?php endif; ?>


<?php
?>