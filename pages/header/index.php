<?php global $content ?>
<html>
	<head>
		<title><?php echo TITLE;?></title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/reset.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/style.css"></link>
		<?php $content->displayAll('css')?>
		
		<script src="<?php echo PROTO.HOME; ?>/js/jquery-3.1.1.min.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/js/jquery-ui/jquery-ui.js"></script>
		<?php $content->displayAll('js')?>
	</head>
	<body>
		<noscript id="noscript">
			<div id="noscriptBox">
				<?php displayString('noscript')?>
				<a href="<?php echo PROTO.HOME?>/impressum"><?php displayString('common disclaimer')?></a>
			</div>
		</noscript>
		<script>
			$(function() {
				loadContent('<?php echo PROTO.HOME?>/load/login', 'body');
			});
		</script>
	</body>
</html>
<?php
