/*
 * Updaten des Kalender-Moduls
 */

$(function() {
	alert('hi');
	if(typeof csel === "undefined") { var csel = null; }
	var data = {csel: csel};
	$( "#cal" ).load(url + "/ajax/updateCal", data);
	
});