<div id="adduser" class="fieldset">
	<div class="headline"><?php displayString('useredit adduser')?></div>
	<fieldset id="useredit_vsSelector" style="float:left">
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
	<fieldset id="useredit_groupSelector" style="float:left">
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
				<div class="hint"><?php displayString('useredit selectVSfirst'); ?></div>
				<?php foreach($result AS $row):	?>
				<div style="display:none" class="clickable entry <?php if($i % 2 != 0) echo "shader"; ?>" data-vsid="<?php echo $row['vsid']?>" data-active="0" data-id="<?php echo $row['gid']?>"><?php echo $row['name']?></div>
				<?php
				endforeach;
			endif;?>
			
		</div>
	</fieldset>
	<div class="formrowcontainer" style="float:left; margin: 0px 10px 10px 0px;">
			<label class="formrow"><?php displayString('common name')?> <input type="text" id="adduser_name" /></label>			
			<label class="formrow"><?php displayString('common password')?> <input type="password" id="adduser_password" /></label>
			<label class="formrow"><?php displayString('common email')?> <input type="text" id="adduser_email" /></label>
			<label class="formrow"><?php displayString('common active')?> <input type="checkbox" id="adduser_active" checked/></label>
	</div>
	<fieldset id="useredit_permSelector" style="float:left;">
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
	<button id="b_addUser"><?php displayString("useredit add")?></button>
	<div class="error" style="position: absolute; max-width: 500px; bottom: 10px; right: 10px"></div>
</div>
<?php $content->displayContent('useradmin', 'useredit'); ?>
<script>
	/*
	 * Benutzer anlegen
	 */

	$('#b_addUser').click(function() {
		var errordiv = $('#adduser').children('.error');
		errordiv.fadeOut(100);

		var vsid = $('#useredit_vsSelector').find(".entry[data-active='1']:first").attr('data-id');
		var groups = [];
		var perms = [];
		var name = $('#adduser').find('#adduser_name').val();
		var password = $('#adduser').find('#adduser_password').val();
		var email = $('#adduser').find('#adduser_email').val();
		var active = $('#adduser').find('#adduser_active').prop('checked') ? 1 : 0;

		$('#useredit_groupSelector').find(".entry[data-active='1']").each(function() {
			groups.push($(this).attr('data-id'));
		});

		$('#useredit_permSelector').find(".entry[data-active='1']").each(function() {
			perms.push($(this).attr('data-id'));
		});
		
		var postdata = {
				vsid: vsid,
				name: name,
				password: password,
				email: email,
				active: active,
				groups: groups,
				perms: perms
				};

		$.post('<?php echo PROTO.HOME?>/datahandler/useradmin/adduser', postdata, function (data) {
			if(testJSON(data)) {
				jdata = JSON.parse(data);

				$('#adduser').find('.error').stop().fadeOut(0).text(jdata.error).fadeIn(100).delay(3000).fadeOut(100);
				return;
			}

			console.log(data);
		});
	});
</script>
<script>
	$('#useredit_vsSelector').find('.entry').click(function() {
		$('#useredit_vsSelector').find('.entry').attr('data-active', 0);
		$(this).attr('data-active', 1);
		$('#useredit_groupSelector').find(".entry[data-active=1]:not([data-vsid='"+ $(this).attr('data-id') +"'])").attr('data-active', 0);

		$('#useredit_groupSelector').find(".entry[data-vsid='"+$(this).attr('data-id')+"']").slideDown(100);
		$('#useredit_groupSelector').find(".entry:not([data-vsid='"+$(this).attr('data-id')+"'])").slideUp(100);
		$('#useredit_groupSelector').find(".hint").slideUp(100);
	});

	$('input').change(function() {
		$(this).attr('value', $(this).val());
	});

	
	$('#useredit_groupSelector, #useredit_permSelector').find('.entry').click(function() {
		if($(this).attr('data-active') == 0) {
			$(this).attr('data-active', 1);
		} else {
			$(this).attr('data-active', 0);
		}		
	});
</script>