<div class="headline"><?php displayString('groups groups')?></div>
<?php 
global $mysql, $user;

$mysql->where('vsid', $user->vsid);
$mysql->select('groups');

$i = 0;

foreach($mysql->fetchAll() AS $currGrp) {
	$class = $i%2 == 0 ? 'shader' : '';
	echo "<div data-active=\"0\" class=\"clickable $class\" data-gid=\"".$currGrp['gid']."\">".$currGrp['name']."<img src=\"images/close.png\" class=\"clickable\" /></div>";
	
	$i++;
}

?>
<div class="addnew <?php echo $i%2 == 0 ? "shader": ""; ?>" >
	<input type="text" id="g_addnew" />
	<div class="clickable">+</div>
</div>
<div class="error"></div>
<script>
$('.addnew').children('div').click(function() {
	$('#groupslist').find('.error').stop().fadeOut(0);
	
	var name = $(this).siblings('input').val();

	if(name.length == 0) {
		alert('<?php displayString('errors FormfillError')?>');
		return;
	}

	$.post('<?php echo PROTO.HOME?>/datahandler/groups/addnew', {name: name}, function(data) {
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			$('#groupslist').find('.error').stop().fadeOut(0).text(jdata.error).fadeIn(100).delay(3000).fadeOut(100);
			return;
		}

		loadGroups();
	});
});

$('#groupslist').find('img').click(function() {
	var gid = $(this).parent().attr('data-gid');

	if(confirm('<?php displayString('groups delConfirm')?>'))
		$.post('<?php echo PROTO.HOME?>/datahandler/groups/delgrp', {gid: gid}, function(data) {
			if(testJSON(data)) {
				jdata = JSON.parse(data);
				$('#groupslist').find('.error').stop().fadeOut(0).text(jdata.error).fadeIn(100).delay(3000).fadeOut(100);
				return;
			}

			loadGroups();
	});
});

$('#groupslist').children("div[data-active='0']").click(function() {	
	$(this).siblings("div:not('.headline')").attr('data-active', 0);
	$(this).attr("data-active", 1);

	loadUser();
});
</script>