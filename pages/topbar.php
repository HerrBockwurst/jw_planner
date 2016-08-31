<?php if(!defined('index')) exit;?>
<?php global $user;?>

<div id="topbar">
	<div id="time"><?php echo date("d.m.Y H:i", time())?></div>
	<div id="usericon"><?php echo $user->name ?> <img src="images/guy.png" /></div>
</div>
<script>
setInterval(function() {
	var d = new Date();
	var day = d.getDate(),
	month = d.getMonth(),
	year = d.getFullYear(),
	hour = d.getHours(),
	min = d.getMinutes();

	month = month + 1;
	if(month < 10) {
		month = "0" + month;
	}
	if(day < 10) {
		day = "0" + day;
	}
	if(hour < 10) {
		hour = "0" + hour;
	}
	if(min < 10) {
		min = "0" + hour;
	}
	
	var datestring = day + "." + month + "." + year + " " + hour + ":" + min;
	$('#time').text(datestring);
}, 1000);
</script>
