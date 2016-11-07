<div class="headline"><?php displayString('groups member')?></div>
<?php

if(!isset($_POST)) exit;

global $mysql, $user;

$mysql->where('gid', intval($_POST['gid']));
$mysql->select('groups', null, 1);

if($mysql->countResult() == 0) {
	displayString('groups noGroupSelected');
	exit;
}
$groups = $mysql->fetchRow();

if($groups->vsid != $user->vsid) {
	displayString('errors noPerm');
	exit;
}

$userInGroup = json_decode($groups->members);

$mysql->where('vsid', $groups->vsid);
$mysql->select('users');

$i = 0;

foreach($mysql->fetchAll() AS $currUser) {
	
	$active = in_array($currUser['uid'], $userInGroup) ? 1 : 0; 
	$shader = $i%2 == 0 ? 'shader' : '';
	
	echo "<div data-active=\"$active\" data-uid=\"".$currUser['uid']."\" class=\"clickable $shader\">".$currUser['name']."</div>";
	$i++;
}
?>
<script>
$('#userlist').children('div').click(function() {
	var newVal = $(this).attr('data-active') == 1 ? 0 : 1;

	$(this).attr('data-active', newVal);
});
</script>