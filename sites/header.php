<!DOCTYPE HTML>
<html>
	<head>
		<title><?php printTitle(true); ?></title>
		<link rel="stylesheet" href="<?php printURL();?>/reset.css"></link>
		<link rel="stylesheet" href="<?php printURL();?>/<?php getcss(); ?>"></link>
	</head>
	<body>
		<div id="header">
			<div id="logo"><span style="font-size:3.5em">JW</span><br /><span style="font-size:1.5em">Planner</span></div>
			<div id="menu">
				<ul>
					<li><?php displayMenuLink('menu>calendar', '/calendar'); ?></li>
					<li><?php displayMenuLink('menu>profile', '/profile'); ?></li>
					<li><?php displayMenuLink('menu>admin', '/admin'); ?></li>
					<li><?php displayMenuLink('menu>dev', '/system'); ?></li>
				</ul>
			</div>			
		</div>


<?php
?>