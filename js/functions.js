function testJSON(str) {
	try {
		JSON.parse(str);			
	} catch (e) {
		return false;
	}
	return true;
}

function testRedirect(data) {
	if(testJSON(data)) {
		jdata = JSON.parse(data);
		if(typeof jdata.redirect !== "undefined") {
			window.location.replace(jdata.redirect);
			return true;
		}
		return false;
	}
	return false;
}

function loadContent(url, container, postdata) {
	
	if(typeof postdata === "undefined") postdata = {};
	
	$.post(url, postdata, function(data) {
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			if(typeof jdata.redirect !== "undefined") {
				window.location.replace(jdata.redirect);
			}
		} else {			
			$(container).stop().fadeOut(100).fadeIn(300)
			setTimeout(function() {
				$(container).html(data);				
			}, 100);
		}
	});
}