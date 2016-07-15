<?php if(isset($redirect)): ?>
	<script>
		$(document).ready(function() {
			window.location.replace(url);
		});
	
	</script>
<?php endif;?>

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