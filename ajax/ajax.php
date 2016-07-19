<?php
if(!isset($fromIndex)) exit;
/*
 * Weiterleiten zu Loginseite wenn Session ausgelaufen ist
 */
if($redirect == true): ?>
	<script>
		$(document).ready(function() {
			setTimeout(function() {
				window.location.replace(url);
				}, 3000);
		});
	
	</script>
	<div class="error smallspace"><?php displayText('errors>session_expired')?></div>
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
			require_once 'ajax/test.php';
			break;
	endswitch;
	
	exit;
?>