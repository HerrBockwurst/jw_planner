function nbox(select, param) {
	var select = $(select);
	var options = $.parseHTML(select.html());
	var nbox = $.parseHTML('<div class="nbox"></div>');
	var nbox_input = $.parseHTML('<input>');
	var nbox_arrow = $.parseHTML('<div class="nbox-arrow"></div>');
	console.log(nbox_input);
	//Build
	var build = nbox.html(select.html() + nbox_input.html() + nbox_arrow.html());
	
}

$.fn.extend({
	nbox: function(param) {		
		this.each(function() {
			nbox(this, param);
		});
	}
});