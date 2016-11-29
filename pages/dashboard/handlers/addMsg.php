<?php
global $user, $mysql;
if(!isset($_POST['msg']) || ($_POST['msg']) == '') exit;

$msg = preg_replace('/\\n/', '<br />', preg_replace('/\\n\\r/', '<br />', htmlentities($_POST['msg'])));

if(!$mysql->insert('messages', array('content' => $msg,
		'sender' => $user->uid, 
		'recipient' => 'all', 
		'created' => time(), 
		'expire' => PHP_INT_MAX)))
	returnErrorJSON(getString('errors sql'));
?>
<div class="msg" style="display: none">
	<div><?php echo $user->name?><p><?php echo date("d.m.Y")?></p></div>
	<div><?php echo $msg?></div>
</div>