<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo TITLE ?></title>
		<script src="<?php echo PROTO.HOME; ?>/js/jquery-3.2.1.min.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/js/ScrollToFixed.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/js/functions.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/js/nbox/nbox.js"></script>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/reset.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/style.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/spinner.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/js/nbox/nbox.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/serialPages/footer.css"></link>
		<?php ContentHandler::printCSS() ?>
		<script>
			var lang = {
					yes: '<?php displayString('Common Yes')?>',
					no: '<?php displayString('Common No')?>',
					okay: '<?php displayString('Common Okay')?>'
					};
		</script>
	</head>
	<body>