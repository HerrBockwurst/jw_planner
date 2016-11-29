<div class="fieldset" id="fs_changelog">
	<div class="headline"><?php displayString('menu changelog')?></div>
	<div id="c_changelog">
		<div style="margin-right: 20px;"><input type="text" id="c_changelog_release" /></div>
		<div>
			<span>Deutsch</span>
			<div id="c_changelog_text_de"></div>
		</div>
		<div id="c_createdLogs">
			<?php
			global $mysql, $user;
			$mysql->orderBy('release', "DESC");
			$mysql->select('changelog');
			
			echo "<strong style=\"display: block; margin-bottom: 5px;\">".getString('menu changelog')."</strong><br class=\"floatbreak\" />";
			
			foreach($mysql->fetchAll() AS $cLog):
			$log = json_decode($cLog['changelog'], true);
			?>
			<div data-active="0" class="clickable" data-release="<?php echo $cLog['release']?>">
				<?php echo $cLog['release']?>
				<div style="display: none" data-lang="de"><?php echo $log['de'];?></div>
				<br class="floatbreak" />
			</div>
			<?php 
			endforeach;
			?>
		</div>
		<br class="floatbreak">
		<button id="b_submitChangelog"><?php displayString('system addChangelog')?></button>
	</div>
</div>
<script>
$('#b_submitChangelog').click(function() {
	$.post('<?php echo PROTO.HOME ?>/datahandler/system/addchangelog',
			{id: $('#c_changelog_release').val(), lang_de: $('#c_changelog_text_de').html()},
			function(data) {
				if(testJSON(data)) {
					jdata = JSON.parse(data);
					if(typeof jdata.error !== "undefined") {
						alert(jdata.error);
						return;
					}

					$('#c_changelog_release').val('').attr('value', '');
					$('#c_changelog_text_de').html('');
					$('#c_createdLogs').children('div.clickable').attr('data-active', 0);					
				}
	});
});

$('#c_createdLogs').children('div.clickable').click(function() {
	if($(this).attr('data-active') == 1) {
		$('#c_changelog_release').val('').attr('value', '');
		$('#c_changelog_text_de').html('');
		$(this).attr('data-active', 0);	
		return;
	}
	$('#c_changelog_release').val($(this).attr('data-release')).attr('value', $(this).attr('data-release'));
	$('#c_changelog_text_de').html($(this).children("div[data-lang='de']").html());
	$('#b_submitChangelog').text('<?php displayString('system editChangelog')?>');
	
	$('#c_createdLogs').children('div.clickable').attr('data-active', 0);
	$(this).attr('data-active', 1);
});

if(!$("#c_changelog_text_de").hasClass('trumbowyg-editor'))
	$("#c_changelog_text_de").trumbowyg({
	    btns: [
	           ['viewHTML'],
	           'btnGrp-design',
	           ['link'],
	           'btnGrp-justify',
	           'btnGrp-lists',
	           ['horizontalRule'],
	           ['removeformat']
	       ],
	    removeformatPasted: true,
	    autogrow: true,
	    semantic: false
	   });
</script>