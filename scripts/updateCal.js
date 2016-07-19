/*
 * Updaten des Kalender-Moduls
 */

$( "#cal_calSelect" ).change(function() {
	var val = $( "#cal_calSelect" ).val();
	if(typeof csel === "undefined") { var csel = null; }
	var data = {cid: val, csel: csel};
	$( "#cal" ).load(url + "/ajax/updateCal", data);
});