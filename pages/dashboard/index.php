<?php
global $mysql, $user;

$mysql->where('sender', 'system');
$mysql->where('expire', time(), '>');
$mysql->orderBy('created', 'DESC');
$mysql->select('messages', null, 1);

if($mysql->countResult() > 0):

$msg = $mysql->fetchRow();
?>

<div id="sysmessage" class="fieldset" style="max-width: 600px; background-color: rgba(0,179,9,0.7)">
	<div class="headline" style="text-align: center"><?php displayString('dashboard sysmsg')?></div>
	<div id="sysmsg">
		<p><strong><?php echo $msg->title?></strong></p>
		<p><?php echo $msg->content?></p>
	</div>
</div>
<br class="floatbreak" />
<?php endif;?>

<?php
$mysql->where('recipient', 'all');
$mysql->where('expire', time(), '>');
$mysql->where('messages.vsid', $user->vsid);
$mysql->join(array('messages' => 'sender', 'users' => 'uid'));
$mysql->orderBy('created', 'DESC');
$mysql->select('messages');

if($mysql->countResult() > 0): ?>
	<div id="pubmessage" class="fieldset" style="min-width: 400px; max-width: 600px;">
		<div class="headline" style="text-align: center"><?php displayString('dashboard pubmsg')?></div>
		<?php foreach($mysql->fetchAll() AS $msg): ?>
		<div class="msg" style="margin-bottom: 20px;">
			<p><?php echo $msg['name'];?>: <strong><?php echo $msg['title'];?></strong></p>
			<p><?php echo $msg['content'];?></p>
		</div>
		
		<?php endforeach;?>	
	</div>
<?php endif;?>