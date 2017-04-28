function getRotationDegrees(obj) {
    var matrix = obj.css("-webkit-transform") ||
    obj.css("-moz-transform")    ||
    obj.css("-ms-transform")     ||
    obj.css("-o-transform")      ||
    obj.css("transform");
    if(matrix !== 'none') {
        var values = matrix.split('(')[1].split(')')[0].split(',');
        var a = values[0];
        var b = values[1];
        var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
    } else { var angle = 0; }
    return (angle < 0) ? angle + 360 : angle;
}

function bindInputs() {
	$('body').find('input[type="text"][data-nobind!="true"],input[type="password"][data-nobind!="true"]').each(function() {
		
		if(typeof $(this).attr('data-default') === "undefined")
			$(this).attr('data-default', $(this).val()).addClass('default');
				
		$(this).attr('data-type', $(this).attr('type')).attr('type', 'text');			
		
		$(this).bind('focus', function() {
			if($(this).val() == $(this).attr('data-default'))
				$(this).val('').attr('type', $(this).attr('data-type'));
			$(this).removeClass('default');
		}).bind('focusout', function() {			
			if($(this).val() == '') $(this).val($(this).attr('data-default')).addClass('default').attr('type', 'text');
		});
		if($(this).attr('data-default') !== $(this).val()) $(this).focus().blur();
	});
}

function getJData(data) {
	try {
		var jdata = JSON.parse(data);
		
		if(typeof jdata.redirect !== "undefined") {
			window.location.replace(jdata.redirect);
			return false;
		} else if(typeof jdata.error !== "undefined") {
			MessageBox(jdata.error);
			return false;
		}		
		return jdata;
	} catch (e) {
		return false;
	}
	
}

function linkClick(e) {
	e.preventDefault();
	if(e.target.href.length == 0) return;
	loadPage(e.target.href);
}

function MessageBox(html, DoubleButton, Callback) {
	var box = $('#MessageBox');
	if(typeof DoubleButton === "undefined") DoubleButton = false;
	if(typeof Callback === "undefined") Callback = function() {};
	
	var buttons = '<div id="MessageBox_ButtonRow">';
	
	buttons = DoubleButton ? 
			buttons + '<button id="MessageBox_ButtonYes">' + lang.yes + '</button><button id="MessageBox_ButtonNo">' + lang.no + '</button>' :
			buttons + '<button id="MessageBox_ButtonOk">' + lang.okay + '</button>';
	
	buttons = buttons + '</div>';
	
	html = '<p style="font-weight: bold; margin: 0px 0px 10px 0px; font-size: 18px; border-bottom: 1px solid rgb(40,40,40)">JWPlanner</p>' + html;
	
	box.find('#MessageBox_Inner').html(html + buttons);
	box.find('button').unbind();
	box.find('#MessageBox_ButtonYes').bind('click', Callback);
	box.find('#MessageBox_ButtonNo, #MessageBox_ButtonOk').bind('click', function() {$('#MessageBox').fadeOut(100)});
	box.fadeIn(100);

}

function LoadingBox(Switch) {
	box = $('#LoadingBox');
	if(typeof Switch === "undefined") Switch = box.is(':visible') ? -1 : 1;
	if(Switch == 1)
		box.fadeIn(100)
	else if (Switch == -1)
		box.fadeOut(100)
}

function loadPage(url, container) {
	if(typeof container === "undefined") container = 'main';
	LoadingBox();
	$.post(url, {}, function(data) {
		LoadingBox();
		window.history.pushState({url: url, container: container}, "", url);
		getJData(data);
		$(container).fadeOut(100);
		setTimeout(function() {
			$(container).html(data).fadeIn(100);
			$('a').each(function() {
				$(this).unbind().bind('click', linkClick);
			});
			bindInputs();
		}, 150);
	});
}

$(window).bind('popstate', function(event) {
	LoadingBox();
	
    var state = event.originalEvent.state;
    console.log(state);
    if (state) {
    	$.post(state.url, {isAjax: true}, function(data) {
    		$(state.container).fadeOut(100);
    		setTimeout(function() {
    			$(state.container).html(data).fadeIn(100);
    			LoadingBox();
    			bindInputs();
    		},100);
    	});
    }
    LoadingBox(-1);
});