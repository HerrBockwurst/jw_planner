function testJSON(str) {
	try {
		JSON.parse(str);			
	} catch (e) {
		return false;
	}
	return true;
}

function loadContent(url, container, postdata) {
	
	if(typeof postdata === "undefined") postdata = {};
	
	$.post(url, postdata, function(data) {
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

var globals;