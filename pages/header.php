<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo TITLE;?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/reset.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/style.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/spinner.css"></link>
		<?php ContentManager::getCSSFiles()?>
		<script src="<?php echo PROTO.HOME; ?>/js/jquery-3.1.1.min.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/js/scrollToFixed.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/js/functions.js"></script>
		<script>
			var lang = {
					yes: '<?php displayString('Common Yes')?>',
					no: '<?php displayString('Common No')?>',
					okay: '<?php displayString('Common Okay')?>'
					};
		</script>
	</head>
	<body>
	<div id="LoadingBox">
		<div id="LoadingBox_Inner">
			<div class="spinner">
				<div class="double-bounce1"></div>
				<div class="double-bounce2"></div>
			</div>
		</div>
	</div>
	<div id="MessageBox">
		<div id="MessageBox_Inner">
		</div>
	</div>
	<div id="MenuBar">
		<?php
			if(ContentManager::$ContentType == CONTENT_TYPE_FRONTEND)
				ContentManager::getCommonPage('MenuFrontend');
			elseif(ContentManager::$ContentType == CONTENT_TYPE_APP)
				ContentManager::getMenuBar();
		?>
	</div>
	<div id="Content">