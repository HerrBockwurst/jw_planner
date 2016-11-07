<div class="fieldset">
	<div class="headline"><?php displayString('menu groups')?></div>
	<div id="groupslist">
		<?php displayString('common loading')?>
	</div>
	<div id="userlist">
		<div class="headline"><?php displayString('groups member')?></div>
		<?php displayString('groups noGroupSelected')?>
	</div>
	<br class="floatbreak" />
	<button id="b_saveGroups" style="margin-top: 10px;"><?php displayString('groups saveGroup')?></button>
	<div class="error" style="float: right; margin: 10px"></div>
	<div class="success" style="float: right; margin: 10px"></div>
</div>
<script>
function loadUser() {
	var gid = $('#groupslist').children("div[data-active='1']").attr('data-gid');

	loadContent('<?php echo PROTO.HOME?>/load/groups/getusers', '#userlist', {gid: gid});
}

function loadGroups() {
	loadContent('<?php echo PROTO.HOME?>/load/groups/getgroups', '#groupslist');
}

$(loadGroups);

$('#b_saveGroups').click(function() {
	var gid = $('#groupslist').find("div[data-active='1']").attr('data-gid');
	var users = [];

	$('#b_saveGroups').siblings('.error').stop().fadeOut(0);

	if(typeof gid === "undefined") {
		alert('<?php displayString('groups noGroupSelected')?>');
		return;
	}

	$('#userlist').find("div[data-active='1']").each(function() {
		users.push($(this).attr('data-uid'));
	});

	$.post('<?php echo PROTO.HOME?>/datahandler/groups/update', {gid: gid, users: users}, function(data) {
		if(testJSON(data)) {			
			jdata = JSON.parse(data);

			if(typeof jdata.error !== "undefined") {
				$('#b_saveGroups').siblings('.error').stop().fadeOut(0).text(jdata.error).fadeIn(100).delay(3000).fadeOut(100);
				return;
			}
			
			$('#b_saveGroups').siblings('.success').stop().fadeOut(0).text(jdata.success).fadeIn(100).delay(3000).fadeOut(100);
		}
	});	
});
</script>