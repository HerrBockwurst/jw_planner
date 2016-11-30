<?php 
global $mysql;

$mysql->where(('sender'), 'system');
$mysql->select('messages');
if(!$mysql->countResult() != 0) 
	$msg = $mysql->fetchRow(true);
$title = isset($msg) ? $msg['title'] : '';
$text = isset($msg) ? $msg['content'] : '';
?>
<div class="fieldset">
	<div class="headline"><img src="images/arr_down.png" /><?php displayString('dashboard sysmsg')?></div>
	<div id="s_sysmsg">
		<div contenteditable="true" class="trumbowyg-box" id="s_sysmsg_headline"><?php echo $title ?></div>
		<div id="s_sysmsg_editor"><?php echo $text ?></div>
		<button id="b_sysmsg"><?php displayString('system setMsg')?></button>
	</div>
</div>
<br class="floatbreak" />
<script>
$('#b_sysmsg').click(function() {
	$.post('<?php echo PROTO.HOME?>/datahandler/system/setmsg', {title: $('#s_sysmsg_headline').html(), msg: $('#s_sysmsg_editor').html()}, function(data) {
		console.log(data);
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			if(typeof jdata.error !== "undefined") {
				alert(jdata.error);
				return;
			}
			
		}
	});
});
		
if(!$("#s_sysmsg_editor").hasClass('trumbowyg-editor'))
	$("#s_sysmsg_editor").trumbowyg({
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