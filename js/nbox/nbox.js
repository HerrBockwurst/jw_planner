function nbox(select, param) {
	var select = $(select);	
	var nbox = $($.parseHTML('<div class="nbox"></div>'));
	var nbox_input = $($.parseHTML('<input type="text" data-nobind="true">'));
	var nbox_arrow = $($.parseHTML('<div class="nbox-arrow"></div>'));	
	var nbox_dropdown = $($.parseHTML('<div class="nbox-dropdown"></div>'));
	var options = [];
	
	select.children('option').each(function() {
		options.push({val: $(this).val(), string: $(this).text(), selected: $(this).is(':selected')});
	});
	
	$.each(options, function() {
		var row = $($.parseHTML('<div class="nbox-entry"></div>'));
		row.text(this.string);
		row.attr('data-value', this.val);
		if(this.selected) row.attr('data-selected', 'selected');
		nbox_dropdown.append(row);
	})
	
	nbox_input.val(select.children('option:selected').text())
	
	nbox_arrow.bind('click', function() { //Auf und zuklappen durch Arrow
		$(this).siblings('.nbox-dropdown').slideToggle(100);
		
		if(getRotationDegrees($(this)) > 0) $(this).css({transform: ''});
		else $(this).css({transform: 'rotate(180deg)'});
	});
	
	select.bind('change', function() { //Input Ändern nach Select Change
		$(this).siblings('input').val($(this).children(':selected').text());
		console.log($(this).val());
	});
	
	nbox_dropdown.children('.nbox-entry').bind('click', function() { //Select Change bei Click auf Eintrag
		$(this).parents('.nbox').children('select').val($(this).attr('data-value')).trigger('change');
		$(this).attr('data-selected', 'selected').siblings('.nbox-entry').removeAttr('data-selected');
		nbox_arrow.trigger('click');
	});
	
	nbox_input.bind('focus', function() { //Bei Input Focus aufklappen
		nbox_arrow.css({transform: 'rotate(180deg)'});
		$(this).siblings('.nbox-dropdown').slideDown(100);
	});
	
	nbox_input.bind('keyup', function(e) { //Filtern der Treffer
		var regex = new RegExp($(this).val(), "i");		
		
		$(this).parent('.nbox').children('.nbox-dropdown').children('.nbox-entry').each(function() {
			if(!$(this).text().match(regex)) $(this).slideUp(100);
			else $(this).slideDown(100);
		});
		
		if(e.keyCode == 13) {
			$(this).parent('.nbox').children('.nbox-dropdown').children('.nbox-entry:visible:first').trigger('click');
			$(this).blur();
		}
	});
	
	nbox_input.bind('blur', function() {
		$(this).parent('.nbox').children('.nbox-dropdown').children('.nbox-entry').each(function() {
			$(this).slideDown(0);
		});
	});
	
	//Build
	select.css({display: 'none'});
	var build = nbox;	
	build.append(select.clone(true));
	build.append(nbox_input);
	build.append(nbox_arrow);
	build.append(nbox_dropdown);	
	
	select.after(build);
	select.remove();
	
	
}

$.fn.extend({
	nbox: function(param) {		
		this.each(function() {
			nbox(this, param);
		});
	}
});