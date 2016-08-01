<?php if(!defined('index')) exit; ?>

<?php if(!defined('ajax')): ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo TITLE;?></title>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/reset.css"></link>
		<link rel="stylesheet" href="<?php echo PROTO.HOME; ?>/style.css"></link>
		<?php 
			foreach($ModulHandler->getModules() AS $key => $modul):
				if(file_exists('modules/'.$key.'/style.css')): ?>
					<link rel="stylesheet" href="<?php echo PROTO.HOME.'/modules/'.$key.'/style.css';?>"></link>
		<?php	endif;
			endforeach;
		?>
		<meta charset="utf-8">
		<script src="<?php echo PROTO.HOME; ?>/scripts/jquery-3.0.0.min.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/scripts/jquery-ui/jquery-ui.js"></script> 		
		<script>
			var url = "<?php echo PROTO.HOME; ?>";
		</script>
		<script src="<?php echo PROTO.HOME; ?>/scripts/loader.js"></script>
		<script src="<?php echo PROTO.HOME; ?>/scripts/functions.js"></script>
	</head>
	<body>
	<noscript>
		<div id="noscript">
			<div>
				<p><?php displayString('noscript1')?></p>
				<p><?php displayString('noscript2')?></p>
			</div>
		</div>
	</noscript>
	<div id="site">
	</div>
<?php endif;?>