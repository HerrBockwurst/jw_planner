<div id="edituser" class="fieldset">
	<div class="headline"><?php displayString('useredit edituser')?></div>
	<div>
		<label style="display: block; float: left; margin-right: 10px"><?php displayString('common name');?> <input id="usearch_name" type="text"/></label>
		<label style="display: block; float: left; margin-right: 10px"><?php displayString('common vs');?> <input id="usearch_vs" type="text"/></label>
		<button id="b_searchUser" style="height: 21px; padding: 3px 20px; position: relative; top: -3px;"><?php displayString('common search')?></button>
	</div>
	<br  class="floatbreak" />
	<div id="editcontent" style="display: none">
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
		<button id="b_editUser"><?php displayString("useredit edit")?></button>
		<div class="error" style="position: absolute; max-width: 500px; bottom: 10px; right: 10px"></div>
	</div>
</div>

<div class="overlay" id="edituser_selectbox">	
	<div class="inner">
		<div class="headline">Test</div>
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

		inner.offset({ top: ($(window).height() / 2) - 200 , left: ($(window).width() / 2) - (inner.width() / 2) });
		$('#edituser_selectbox').fadeIn(100)
		
	});

});
</script>