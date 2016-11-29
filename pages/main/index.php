<div id="sidebar">
	<div style="text-align: center;">
		<a id="sidebarlogo" href="<?php echo PROTO.HOME?>">
			JW
			<span>Planner</span>
		</a>
	</div>
	<?php loadSidebar(); ?>
	<div id="footer">
		<ul>
			<li data-id="donate"><a><?php displayString('menu donate')?></a></li>
			<li data-id="about"><a><?php displayString('menu about')?></a></li>
			<li data-id="changelog"><a><?php displayString('menu changelog')?></a></li>
			<li data-notab="true"><a target="_blank" href="<?php echo PROTO.HOME?>/impressum"><?php displayString('common disclaimer')?></a></li>
		</ul>
	</div>
</div>
<ul id="topbar">
</ul>
<div id="thecontent">
</div>
<script>
	var intervals = [];
	var opentabs = {};
	var currOpen = '';

	function switchTab(e) {
		if(e.target.nodeName == 'IMG') { return; }
		var TabId = $(this).attr('data-id');

		if(currOpen == TabId) return;

		opentabs[currOpen] = $('#thecontent').html();
		$('#thecontent').html(opentabs[TabId]);
		currOpen = TabId;
		$('#topbar').children('li').attr('data-active', 0);
		$(this).attr('data-active', 1);
		
	}

	function closeTab() {
		var parent = $(this).parent()
		var TabId = parent.attr('data-id');
		var prev = parent.prev();
		var next = parent.next();
		
		if(prev.length != 0 && parent.attr('data-active') == 1) {
			$('#thecontent').html(opentabs[prev.attr('data-id')]);
			$('#topbar').children("li[data-id='" + prev.attr('data-id') + "']").attr('data-active', 1);
			currOpen = prev.attr('data-id');			
		} else if (next.length != 0 && parent.attr('data-active') == 1) {
			$('#thecontent').html(opentabs[next.attr('data-id')]);
			$('#topbar').children("li[data-id='" + next.attr('data-id') + "']").attr('data-active', 1);
			currOpen = next.attr('data-id');			
		}		
		
		parent.remove();
		delete opentabs[TabId];

		if($('#topbar').find("[data-active='1']").length == 0){
			$('#thecontent').html('');
			currOpen = '';	
		}
	}
	
	$('#sidebar').find('li').click(function() {
		if($(this).attr('data-notab') == 'true') return;
		
		var id = $(this).attr('data-id');
		var readName = $(this).text();

		if(currOpen == id) { return; }
		
		if(typeof opentabs[id] === "undefined") {
			if(currOpen != '') {
				opentabs[currOpen] = $('#thecontent').html();
			}
			currOpen = id;
			
			loadContent('<?php echo PROTO.HOME?>/load/' + id, '#thecontent');
			opentabs[id] = $('#thecontent').html(opentabs[id]);
			$('#topbar').children('li').attr('data-active', 0);
			$('#topbar').append('<li data-active="1" data-id="'+ id +'">'+ readName +'<img src="images/close.png" /></li>');
			$('#topbar').children('li').unbind().bind('click', switchTab);
			$('#topbar').children('li').children('img').unbind().bind('click', closeTab);
		} else {			
			opentabs[currOpen] = $('#thecontent').html();
			$('#thecontent').html(opentabs[id]);
			currOpen = id;
			$('#topbar').children('li').attr('data-active', 0);
			$('#topbar').children("li[data-id='"+ id +"']").attr("data-active", 1)
		}
	});
</script>