function closeModule(string) {
	$(string).hide(100);
	setTimeout(function() { $(string).remove(); }, 100);
}

function openModule(string) {
	$(string).disableSelection();
	$(string).draggable({ containment: "#site", cancel: "div.inner", cursor: "move" }).css({'position' : 'absolute'});
	var x = ($(window).width() - $(string).width()) / 2;
	var y = ($(window).height() - $(string).height()) / 2;
	$(string).css({'left': x, 'top': y});
	$(string).show(100);
	$('.removeme').remove();
}

function loadModule(url, divid) {
	$.post(url, function(data) {
		$(divid).remove(); $('#site').append(data);
	});
	
}