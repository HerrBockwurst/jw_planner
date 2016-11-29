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
		<p><?php $msg->content?></p>
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
$mysql->select('messages');

if($mysql->countResult() > 0 || $user->hasPerm('dashboard.msg')): ?>
	<div id="pubmessage" class="fieldset" style="max-width: 600px;">
		<?php if($user->hasPerm('dashboard.msg')): ?>
		<div class="msg">
			<div><button id="b_addMsg"><?php displayString('dashboard newMsg')?></button></div>
			<div><textarea id="d_createMsg" style="min-height: 50px; min-width: 440px; max-width: 440px"></textarea></div>
		</div>
		<?php endif; ?>
		<?php $c = 0;?>
		<div>
		<?php while($msg = $mysql->fetchRow(true)):?>	
			<?php if($c < 3): ?>	
			<div class="msg" data-id="<?php echo $msg['msg_id']?>" style="margin-bottom: 20px;">
				<div><?php echo $msg['name'];?><p><?php echo date('d.m.Y', $msg['created'])?></p></div>
				<div>
					<?php if($msg['sender'] == $user->uid || $user->hasPerm('dashboard.admin')) echo "<img src=\"images/close.png\">"?>
					<?php echo utf8_encode($msg['content']);?>				
				</div>
			</div>
			<?php $c++; ?>		
			<?php else: ?>
				<a><?php displayString('dashboard more')?></a></div>
				<div style="display: none">
					<div class="msg" data-id="<?php echo $msg['msg_id']?>" style="margin-bottom: 20px;">
						<div><?php echo $msg['name'];?><p><?php echo date('d.m.Y', $msg['created'])?></p></div>
						<div>
							<?php if($msg['sender'] == $user->uid || $user->hasPerm('dashboard.admin')) echo "<img src=\"images/close.png\">"?>
							<?php echo utf8_encode($msg['content']);?>				
						</div>
					</div>
					<?php $c = 0; ?>
					<?php while($msg = $mysql->fetchRow(true)):?>
						<?php if($c >= 4):?>
						<?php $c = 0;?>
						<a><?php displayString('dashboard more')?></a></div><div style="display: none">
						<?php endif; ?>
						
						<div class="msg" data-id="<?php echo $msg['msg_id']?>" style="margin-bottom: 20px;">
							<div><?php echo $msg['name'];?><p><?php echo date('d.m.Y', $msg['created'])?></p></div>
							<div>
								<?php if($msg['sender'] == $user->uid || $user->hasPerm('dashboard.admin')) echo "<img src=\"images/close.png\">"?>
								<?php echo utf8_encode($msg['content']);?>				
							</div>
						</div>
						<?php $c++; ?>
					<?php endwhile;?>
				</div>
			<?php endif;?>
		<?php endwhile;?>
	</div>
	<script>
		$('#pubmessage').find('a').click(function() {
			$(this).slideUp(100);
			$(this).parent().next().slideDown(500);			
		});
		$('.msg').find('img').click(function() {
			var parent = $(this).parents('div.msg');
			var id = parent.attr('data-id');
			
			$.post('<?php echo PROTO.HOME?>/datahandler/dashboard/delmsg', {msg_id: id}, function(data) {
				if(testJSON(data)) {
					jdata = JSON.parse(data);
					if(typeof jdata.error !== "undefined") {
						alert(jdata.error);
						return;
					}
					
					parent.slideUp(100);
				}				
			});

		});
		
		$('#b_addMsg').click(function() {			
			$.post('<?php echo PROTO.HOME?>/datahandler/dashboard/addmsg', {msg: $('#d_createMsg').val()}, function(data) {
				if(testJSON(data)) {
					jdata = JSON.parse(data);
					alert(jdata.error);
					return;
				}

				$(data).insertAfter($('#pubmessage').children('div.msg:first'));
				$('#pubmessage').children('div.msg:first').next().slideDown(500);
				$('#d_createMsg').val('');
			});
		});
	</script>
<?php endif;?>