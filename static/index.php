<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo TITLE;?></title>
		<meta charset="utf-8">
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/reset.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/style.css"></link>
		<?php $this->getCSS();?>	
		<script src="<?php echo PROTO.HOME; ?>/js/jquery-3.1.1.min.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/js/functions.js"></script>
	</head>
	<body>
		<noscript id="noscript">
			<div id="noscriptBox">
				<?php displayString('noscript')?>
				<p><a href="<?php echo PROTO.HOME?>/site/html_impressum"><?php displayString('common impressum')?></a></p>
			</div>
		</noscript>
		<script>
			$(function() {
				var Login = <?php echo isLoggedIn() ? 'true' : 'false' ?>;

				if(Login)
					loadContent('<?php echo PROTO.HOME?>/load/frameset', 'body');
				else
					loadContent('<?php echo PROTO.HOME?>/load/login', 'body');
			});
		</script>
	</body>
</html>