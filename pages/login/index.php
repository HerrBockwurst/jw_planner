<?php 
global $user, $content;

if($user->uid != NULL) {
	//Wenn Benutzer bereits angemeldet ist
	$content->displayContent('main');
	exit;
}
?>

<div id="login">	
	<div id="logo">JW<span style="font-size: 0.5em">Planner</span></div>
	<form style="margin: 20px 70px;" class="formrowcontainer" id="loginform">
		<div class="error"></div>
		<div class="formrow">
			<label><?php displayString("common username")?></label>
			<input type="text" id="login_username">
		</div>
		<div class="formrow">
			<label><?php displayString("common password")?></label>
			<input type="password" id="login_password">
		</div>
		<button id="loginbutton"><?php displayString('common login')?></button>
	</form>
	<div id="disclaimer">
		<a href="<?php echo PROTO.HOME?>/impressum"><?php displayString('common disclaimer')?></a>
		&nbsp;|&nbsp;
		<a id="wannaUse"><?php displayString('wannaUse wannaUse')?></a>
	</div>
	<div class="overlay" style="text-align: left;">
		<div class="inner">
			<img class="clickable" src="images/close.png" />
			<p>
				<h2><?php displayString('wannaUse functions')?></h2>
				<ul>
				<?php foreach(explode('|', getString('wannaUse funcList')) AS $func) echo "<li>$func</li>";	?>
				</ul>
			</p>
			<p>
				<h2><?php displayString('wannaUse commingSoon')?></h2>
				<ul>
				<?php foreach(explode('|', getString('wannaUse commingSoonList')) AS $func) echo "<li>$func</li>";	?>
				</ul>
			</p>
			<p><?php displayString('wannaUse text')?></p>
			<p><?php displayString('wannaUse contact')?> <a href="mailto:<?php echo MAIL_CONTACT; ?>"><?php echo MAIL_CONTACT; ?></a></p>
		</div>
	</div>
</div>
<script>
function login() {
	var username = $('#login_username').val();
	var password = $('#login_password').val();
	var errordiv = $('#loginform').children('.error');

	errordiv.slideUp(100); 
	
	if(username.length == 0 || password.length == 0) {
			
		errordiv.text('<?php displayString("errors FormfillError")?>');
		setTimeout(function() {				
			errordiv.stop().slideDown(100).delay(3000).slideUp(100);
		}, 100);			
		return;
	}

	$.post('<?php echo PROTO.HOME?>/datahandler/login/login', {username: username, password: password}, function(data) {

		if(testJSON(data)) {
			jdata = JSON.parse(data);

			if(typeof jdata.error !== "undefined") {
				errordiv.stop().text(jdata.error).slideDown(100).delay(3000).slideUp(100);
				return;
			}
		}

		loadContent('<?php echo PROTO.HOME?>/load/main', 'body');			
	});
}

$('#loginform').submit(function (e) {
	e.preventDefault();
	login();
});

$('#wannaUse').click(function() {
	var inner = $('#login').children('.overlay').children('.inner');

	inner.css({ top: ($(window).height() / 2) - 200 , left: ($(window).width() / 2) - ((inner.width() + 40) / 2) });
	$('#login').children('.overlay').fadeIn(100);
});
$('#login').children('.overlay').click(function(event) {
	if(!$(event.target).is('div.overlay')) return;
	$('#login').children('.overlay').children('.inner').children('img').trigger('click');
});
$('#login').children('.overlay').children('.inner').children('img').click(function() {
	$('#login').children('.overlay').fadeOut(100);
});
</script>
<?php
