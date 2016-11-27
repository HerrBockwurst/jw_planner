<?php
global $mysql, $user;

$mysql->where('sender', 'system');
$mysql->where('expire', time(), '>');
$mysql->orderBy('created', 'DESC');
$mysql->select('messages', null, 1);

if($mysql->countResult() > 0):

$msg = $mysql->fetchRow();
?>

<div id="sysmessage" class="fieldset" style="float: none; background-color: rgba(0,179,9,0.7)">
	<div class="headline" style="text-align: center"><?php displayString('dashboard sysmsg')?></div>
	<div id="sysmsg">
		<p><strong><?php echo $msg->title?></strong></p>
		<p><?php echo $msg->content?></p>
	</div>
</div>
<?php endif;?>

<?php
$mysql->where('recipient', 'all');
$mysql->where('sender', 'system', '!=');
$mysql->where('expire', time(), '>');
$mysql->where('users.vsid', $user->vsid);
$mysql->join(array('messages' => 'sender', 'users' => 'uid'));
$mysql->orderBy('created', 'DESC');
$mysql->select('messages', null, 20);

if($mysql->countResult() > 0 || $user->hasPerm('dashboard.msg')): ?>
	<div id="pubmessage" class="fieldset" style="max-width: 600px;">
		<?php foreach($mysql->fetchAll() AS $msg):?>
		<div class="msg" style="margin-bottom: 20px;">
			<div><?php echo $msg['name'];?><p><?php echo date('d.m.Y', $msg['created'])?></p></div>
			<div><?php echo utf8_encode($msg['content']);?></div>
		</div>
		
		<?php endforeach;?>	
		<?php if($user->hasPerm('dashboard.msg')): ?>
		<div class="msg">
			<div><button><?php displayString('dashboard newMsg')?></button></div>
			<div><textarea id="d_createMsg" style="min-height: 50px; min-width: 440px; max-width: 440px"></textarea></div>
		</div>
		<?php endif; ?>
	</div>
	<script>
		$('#b_addMsg').click(function() {
			$.post('<?php echo PROTO.HOME?>/datahandler/dashboard/addmsg', {msg: $('#d_createMsg').val()}, function(data) {
				if(testJSON(data)) {
					jdata = JSON.parse(data);
					alert(jdata.error);
					return;
				}

				
			});
		});
	</script>
<?php endif;?>