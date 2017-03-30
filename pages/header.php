<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo TITLE;?></title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/reset.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/style.css"></link>
		<script src="<?php echo PROTO.HOME; ?>/js/jquery-3.1.1.min.js"></script>
	</head>
	<body>
	<div id="MenuBar">
		<?php ContentManager::getCommonPage('MenuFrontend')?>
	</div>
	<div id="Content">