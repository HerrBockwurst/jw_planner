<?php if(!defined('index')) exit;?>
<?php global $user;?>

<div id="topbar">
	<div id="time"><?php echo date("d.m.Y H:i", time())?></div>
	<div id="usericon"><?php echo $user->name ?><img src="images/guy.png" /></div>
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
	month = "0" + month;
	day = "0" + day;
	hour = "0" + hour;
	min = "0" + hour;
	
	var datestring = day.substring(day.length -2) + "." + month.substring(month.length -2) + "." + year + " " + hour.substring(hour.length -2) + ":" + min.substring(min.length -2);
	$('#time').text(datestring);
}, 1000);

$('#usericon').click(function() {
	
});
</script>
