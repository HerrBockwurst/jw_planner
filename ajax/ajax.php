<?php if($redirect == true): ?>
	<script>
		$(document).ready(function() {
			setTimeout(function() {
				window.location.replace(url);
				}, 3000);
		});
	
	</script>
	<div class="error"><?php displayText('errors>session_expired')?></div>
<?php
	
	exit;
	endif;
?>

<?php
	
	switch($url->value(1)):
		case 'updateCal':
			require_once 'ajax/updateCal.php';
			break;
		default:
			break;
	endswitch;
	
	exit;
?>