<?php
if(!defined('index')) exit;
?>

<div id="usersearch_window" class="modul" style="width: 500px; height: 200px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#usersearch_window')" class="pointer" /></div>
	<div class="inner" id="usersearch_inner">
		<div class="error"><?php //echo $_POST['error'] ?></div>
		
		<?php $x= json_decode($_POST['data'], true); print_r($x[0])?>
	</div>
</div>

<script>
	
</script>
<script class="removeme">$(openModule('#usersearch_window'));</script>