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

function loadModule(url, divid, postdata = {}) {
	$.post(url, postdata, function(data) {
		$(divid).remove(); $('#site').append(data);
	});
	
}

function validateEmail(email) {
	  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  return re.test(email);
}