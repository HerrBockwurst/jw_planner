function testJSON(str) {
	try {
		JSON.parse(str);			
	} catch (e) {
		return false;
	}
	return true;
}

function loadContent(url, container) {
	$.post(url, {}, function(data) {
		if(testJSON(data)) {
			console.log(url);
			jdata = JSON.parse(data);
			if(typeof jdata.redirect !== "undefined") {
				window.location.replace(jdata.redirect);
			}
		} else {
			$(container).stop().fadeOut(100).fadeIn(100)
			setTimeout(function() {
				$(container).html(data);				
			}, 100);
		}
	});
}