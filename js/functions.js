function bindInputs() {
	$('body').find('input[type="text"],input[type="password"]').each(function() {		
		if(typeof $(this).attr('data-default') === "undefined")
			$(this).attr('data-default', $(this).val());
				
		$(this).attr('data-type', $(this).attr('type')).attr('type', 'text');			
		
		$(this).unbind().bind('focus', function() {
			if($(this).val() == $(this).attr('data-default'))
				$(this).val('').attr('type', $(this).attr('data-type'));
			$(this).css({color: 'rgb(40,40,40)'});
		}).bind('focusout', function() {			
			if($(this).val() == '') $(this).val($(this).attr('data-default')).css({color: 'rgb(100,100,100)'}).attr('type', 'text');
		});
		if($(this).attr('data-default') !== $(this).val()) $(this).focus().blur();
	});
}

function LoadingBox(action) {	
	if(typeof action === "undefined") action = $('#LoadingBox').is(':visible') ? -1 : 1;
	/*  1 = zeigen
	 * -1 = verbergen */
	if(action == 1)
		$('#LoadingBox').fadeIn(100);
	else if (action == -1)
		$('#LoadingBox').fadeOut(100);	
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

function linkClick(href, container) {
	if(typeof href === "undefined") return;
	container = typeof container === "undefined" ? "#Content" : container;
	LoadingBox();
	$.post(href, {}, function(data) {
		getJData(data);
		$(container).fadeOut(100);
		window.history.pushState({url: href, container: container}, "", href);
		setTimeout(function() {
			$(container).html(data).fadeIn(100);
			LoadingBox();
			bindInputs();
		},100);
		
	});	
}

function getJData(data) {
	try {
		jdata = JSON.parse(data);
		if(typeof jdata.redirect !== "undefined") {
			window.location.replace(jdata.redirect);
			return false;
		}
		else if(typeof jdata.error !== "undefined") {
			MessageBox(jdata.error);
			return false;
		}
		return jdata;
	} catch (e) {
		return false;
	}
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
    		},100);
    	});
    }
    LoadingBox(-1);
});