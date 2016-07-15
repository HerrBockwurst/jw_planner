/*
 * Updaten des Kalender-Moduls
 */

$( "#cal_calSelect" ).change(function() {
	var val = $( "#cal_calSelect" ).val();
	var data = {cid: val};
	$( "#cal" ).load(url + "/ajax/updateCal", data);
});