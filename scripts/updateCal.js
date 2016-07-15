/*
 * Updaten des Kalender-Moduls
 */

$( "#cal_calSelect" ).change(function() {
	var val = $( "#cal_calSelect" ).val();
	var data = udata;
	data.cid = val;
	$( "#cal" ).load(url + "/ajax/updateCal", data);
});