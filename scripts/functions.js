function closeModule(string) {
	$(string).hide(100);
	setTimeout(function() { $(string).remove(); }, 100);
}

function openModule(string) {
	$(string).disableSelection();
	$(string).draggable({ containment: "#site", cancel: "div.inner", cursor: "move" });
	var x = ($('body').width() - $(string).width()) / 2;
	var y = ($('body').height() - $(string).height()) / 2;
	$(string).css({'left': x, 'top': y});
	$(string).show(100);
	$('.removeme').remove();
}