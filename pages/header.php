<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo TITLE;?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/reset.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/style.css"></link>
		<?php ContentManager::getCSSFiles()?>
		<script src="<?php echo PROTO.HOME; ?>/js/jquery-3.1.1.min.js"></script>
	</head>
	<body>
	<div id="MenuBar">
		<?php
			if(ContentManager::$ContentType == CONTENT_TYPE_FRONTEND)
				ContentManager::getCommonPage('MenuFrontend');
		?>
	</div>
	<div id="Content">