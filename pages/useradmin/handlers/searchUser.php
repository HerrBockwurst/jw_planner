<?php

global $mysql;

if(!empty($_POST['u'])) $mysql->where('users.name', "%".$_POST['u']."%", 'LIKE');
if(!empty($_POST['v'])) $mysql->where('users.vsid', "%".$_POST['v']."%", 'LIKE');
$mysql->join(array("users" => "vsid", "versammlungen" => "vsid"));

$mysql->select('users', array('name', 'uid', 'vsid', 'versammlungen.name' => 'vsname'));

if($mysql->countResult() == 0):
	displayString('errors noUsersFound');
	exit;
endif;

$i = 1;
foreach($mysql->fetchAll() AS $row):

	/*
	 * Benutzer filtern, auf die der sucher kein zugriff hat
	 */

	if(!array_key_exists($row['vsid'], getVSAccess('useredit'))) continue;
?>

<div class="clickable searchentry <?php if($i % 2 == 0) echo 'shader'?>" data-uid="<?php echo $row['uid'] ?>">
	<span><?php echo utf8_encode($row['name'])?> (<?php echo $row['uid'] ?>)</span>
	<span><?php echo utf8_encode($row['vsname']) ?></span>
</div>
<?php
$i++;
endforeach;
?>
<script>
$('.searchentry').click(function() {
	$('#edituser_selectbox').fadeOut(100);

	$.post('<?php echo PROTO.HOME?>/datahandler/useradmin/getuserdata', {uid: $(this).attr('data-uid')}, function(data) {
		var jdata = JSON.parse(data);

		if(typeof jdata.error !== "undefined") {
			alert(jdata.error);
			return;
		}

		jdata.perms = JSON.parse(jdata.perms);

		//Versammlung auswählen
		
		$('#edituser_vsSelector').find('.entry').each(function() {
			if( $(this).attr('data-id') == jdata.vsid ) {
				$('#edituser_vsSelector').find('.entry').attr('data-active', 0);
				$(this).attr('data-active', 1);

				$('#edituser_groupSelector').find(".entry[data-active=1]:not([data-vsid='"+ $(this).attr('data-id') +"'])").attr('data-active', 0);

				$('#edituser_groupSelector').find(".entry[data-vsid='"+$(this).attr('data-id')+"']").slideDown(100);
				$('#edituser_groupSelector').find(".entry:not([data-vsid='"+$(this).attr('data-id')+"'])").slideUp(100);
			}
		});

		//Gruppe selektieren
		
		$('#edituser_groupSelector').find(".entry").attr('data-active', 0);
		jdata.groups.forEach(function(val) {
			$('#edituser_groupSelector').find(".entry[data-id='"+val+"']").attr('data-active', 1);
		});

		//Permission selektieren

		$('#edituser_permSelector').find(".entry").attr('data-active', 0);
		jdata.perms.forEach(function(val) {			
			$('#edituser_permSelector').find(".entry[data-id='"+val+"']").attr('data-active', 1);
		});

		//Felder ausfüllen

		$('#edituser_name').val(jdata.name).attr('value', jdata.name);
		$('#edituser_email').val(jdata.email).attr('value', jdata.email);;
		$('#edituser_uid').val(jdata.uid).attr('value', jdata.uid);

		if(jdata.active == 1) { $('#edituser_active').attr('checked', 'checked'); }
		else {$('#edituser_active').attr('checked', false);}

		//Einblenden

		$('#editcontent').stop().fadeIn(100);
	});
});
</script>