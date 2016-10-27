<div id="edituser" class="fieldset">
	<div class="headline"><?php displayString('useredit edituser')?></div>
	<div>
		<label style="display: block; float: left; margin-right: 10px"><?php displayString('common name');?> <input id="usearch_name" type="text"/></label>
		<label style="display: block; float: left; margin-right: 10px"><?php displayString('common vs');?> <input id="usearch_vs" type="text"/></label>
		<button id="b_searchUser" style="height: 21px; padding: 3px 20px; position: relative; top: -3px;"><?php displayString('common search')?></button>
	</div>
	<br  class="floatbreak" />
	<div id="editcontent">
		<fieldset id="edituser_vsSelector" style="float:left">
			<legend><?php displayString('useredit selectVS')?></legend>
			<div class="scrollable">
				<?php
				$i = 0;
				foreach(getVSAccess('useredit') AS $vsid => $vsname):
					?>
					<div class="clickable entry <?php if($i % 2 != 0) echo "shader"; ?>" data-id="<?php echo $vsid?>"><?php echo $vsname?></div>
					<?php
					$i++;
				endforeach;
				?>
			</div>
		</fieldset>
		<fieldset id="edituser_groupSelector" style="float:left">
			<legend><?php displayString('useredit selectGroups')?></legend>
			<div class="scrollable">
				<?php 
				global $mysql, $user, $content;
				if(!$user->getSubPerm('useredit.vs.*')) 
					foreach(getVSAccess('useredit') AS $vsid => $vsname) {
						$mysql->where('vsid', $vsid);
					}
				$mysql->select('groups');
				$result = $mysql->fetchAll();
				
				if(empty($result)):
					displayString('useredit noGroupsAssigned'); 
				else:
				?>	
					<?php foreach($result AS $row):	?>
					<div style="display:none" class="clickable entry <?php if($i % 2 != 0) echo "shader"; ?>" data-vsid="<?php echo $row['vsid']?>" data-active="0" data-id="<?php echo $row['gid']?>"><?php echo $row['name']?></div>
					<?php
					endforeach;
				endif;?>
				
			</div>
		</fieldset>
		<div class="formrowcontainer" style="float:left; margin: 0px 10px 10px 0px;">
				<label class="formrow"><?php displayString('common name')?> <input type="text" id="edituser_name" /></label>			
				<label class="formrow"><?php displayString('common password')?> <input type="password" id="edituser_password" /></label>
				<label class="formrow"><?php displayString('common email')?> <input type="text" id="edituser_email" /></label>
				<label class="formrow"><?php displayString('common active')?> <input type="checkbox" id="edituser_active" checked/></label>
		</div>
		<input id="edituser_uid" type="hidden" value="" />
		<fieldset id="edituser_permSelector" style="float:left;">
			<legend><?php displayString('useredit permissions')?></legend>
			<div class="scrollable">
				<?php
				$perms = $user->getAllPerms();
				sort($perms);
				
				$i = 0;
				foreach($perms AS $perm):
				?><div class="entry clickable <?php if($i % 2 != 0) echo "shader"; ?>" data-active="0" data-id="<?php echo $perm?>"><?php displayString('permissions '.$perm)?></div><?php
				$i++;
				endforeach;
				
				?>
			</div>
		</fieldset>
		<br class="floatbreak" />
		<button id="b_editUser"><?php displayString("useredit edit")?></button>
		<button id="b_delUser"><?php displayString("useredit delete")?></button>
		<div class="error" style="position: absolute; max-width: 500px; bottom: 10px; right: 10px"></div>
	</div>
</div>

<div class="overlay" id="edituser_selectbox">	
	<div class="inner">
		<div class="headline"><?php displayString('useredit searchresult')?></div>
		<img src="images/close.png" class="clickable" />
		<div id="serach_container">
		</div>
	</div>
</div>

<script>
$('#edituser_selectbox').find('img').click(function() {
	$(this).parent().parent().fadeOut(100);
});

$('#b_searchUser').click(function() {
	var uid = $('#usearch_name').val();
	var vs = $('#usearch_vs').val();

	$.post('<?php echo PROTO.HOME?>/datahandler/useradmin/searchuser',  {u: uid, v: vs}, function(data) {
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			data = jdata.error;
		}

		$('#serach_container').html(data);
		var inner = $('#edituser_selectbox').children('.inner');

		inner.css({ top: ($(window).height() / 2) - 200 , left: ($(window).width() / 2) - (inner.width() / 2) });
		$('#edituser_selectbox').fadeIn(100);
		
	});

});

/*
 * Script zum laden der Benutzerdaten in die Maske ist in der Search PHP
 */
</script>
<script>
/*
 * Script für clickbare Entrys
 */
	$('#edituser_vsSelector').find('.entry').click(function() {
		$('#edituser_vsSelector').find('.entry').attr('data-active', 0);
		$(this).attr('data-active', 1);
		$('#edituser_groupSelector').find(".entry[data-active=1]:not([data-vsid='"+ $(this).attr('data-id') +"'])").attr('data-active', 0);

		$('#edituser_groupSelector').find(".entry[data-vsid='"+$(this).attr('data-id')+"']").slideDown(100);
		$('#edituser_groupSelector').find(".entry:not([data-vsid='"+$(this).attr('data-id')+"'])").slideUp(100);
	});

	$('input').change(function() {
		$(this).attr('value', $(this).val());
	});

	$('#edituser_groupSelector, #edituser_permSelector').find('.entry').click(function() {
		$(this).attr('data-active') == 0 ? $(this).attr('data-active', 1) : $(this).attr('data-active', 0);  
	});
</script>
<script>
/*
 * Daten updaten
 */

$('#b_editUser').click(function() {

	var vsid = $('#edituser_vsSelector').find(".entry[data-active='1']:first").attr('data-id');
	var groups = [];
	var perms = [];
	var name = $('#edituser').find('#edituser_name').val();
	var password = $('#edituser').find('#edituser_password').val();
	var email = $('#edituser').find('#edituser_email').val();
	var active = $('#edituser').find('#edituser_active').prop('checked') ? 1 : 0;
	var uid = $('#edituser').find('#edituser_uid').val();

	$('#edituser_groupSelector').find(".entry[data-active='1']").each(function() {
		groups.push($(this).attr('data-id'));
	});

	$('#edituser_permSelector').find(".entry[data-active='1']").each(function() {
		perms.push($(this).attr('data-id'));
	});
	
	var postdata = {
			vsid: vsid,
			name: name,
			password: password,
			email: email,
			active: active,
			groups: groups,
			perms: perms,
			uid: uid
			};

	$.post('<?php echo PROTO.HOME?>/datahandler/useradmin/updateuser', postdata, function (data) {
		
		if(testJSON(data)) {
			jdata = JSON.parse(data);

			
			$('#edituser').find('.error').stop().fadeOut(0).text(jdata.error).fadeIn(100).delay(3000).fadeOut(100);
			return;
		}
		$('#editcontent').stop().fadeOut(100);
		$('#editcontent').find('.entry').attr('data-active', 0);
		$('#editcontent').find('input').val('').attr('value', "");
		setTimeout(function() { alert("<?php displayString('useredit editSuccess')?>"); }, 200);
	});


});

$('#b_delUser').click(function() {
	$.post('<?php echo PROTO.HOME?>/datahandler/useradmin/deluser', {uid: $('#edituser').find('#edituser_uid').val()}, function(data) {	
		if(testJSON(data)) {
			jdata = JSON.parse(data);

			
			$('#edituser').find('.error').stop().fadeOut(0).text(jdata.error).fadeIn(100).delay(3000).fadeOut(100);
			return;
		}
		$('#editcontent').stop().fadeOut(100);
		$('#editcontent').find('.entry').attr('data-active', 0);
		$('#editcontent').find('input').val('').attr('value', "");
		setTimeout(function() { alert("<?php displayString('useredit delSuccess')?>"); }, 200);
	});
	

});
</script>