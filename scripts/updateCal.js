/*
 * Updaten des Kalender-Moduls
 */

$( "#cal_calSelect" ).change(function() {
	var val = $( "#cal_calSelect" ).val();
	$( "#cal" ).load(url + "/ajax/updateCal.php", udata);
});