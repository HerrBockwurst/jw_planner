$( "#p_type" ).change(function() {
	var val = $( "#p_type" ).val();
	if(val == 'weekly') {
		$( "#p_weekly" ).show();
		$( "#p_monthly" ).hide();
	} else {
		$( "#p_weekly" ).hide();
		$( "#p_monthly" ).show();
	}
});