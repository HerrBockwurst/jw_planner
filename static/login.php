<?php
if(!empty($_POST)) {
	/*
	 * Loginskript
	 */
	
	global $MySQL;
	
	if(empty($_POST['username']) || empty($_POST['password'])) returnErrorJSON(getString('errors WrongFields')); //Ein Feld leergelassen
	
	$MySQL->where('ip', $_SERVER['REMOTE_ADDR']);
	if($MySQL->count('loginfails') > MAX_LOGIN_TRY) returnErrorJSON(preg_replace('/{R}/', BANTIME, getString('errors LoginBan'))); //für Login gesperrt
	
	$MySQL->where('uid', $_POST['username']);
	$MySQL->where('email', $_POST['username'], '=', 'OR');
	$MySQL->select('users', NULL, 1);
	
	$Result = $MySQL->fetchRow();
	
	if(!$Result) returnErrorJSON(getString('errors AuthFail')); //Benutzername nicht gefunden
	
	//Passwortcheck
	$iPasswort = hash('sha512', $_POST['password'].SALT); 
		if($iPasswort != $Result->password) returnErrorJSON(getString('errors AuthFail'));
	
	exit;
}
?>
<div id="dLogin">
	<div class="logo">JW<span>Planner</span></div>
	<form id="fLogin">
		<label>
			<?php displayString('common username')?>
			<input id="iUsername" type="text" />
		</label>
		<label>
			<?php displayString('common password')?>
			<input id="iPassword" type="password" />
		</label>
		<button class="smallspacer"><?php displayString('common login')?></button>
	</form>
	<script>
	function login() {
		var user = $('#iUsername').val();
		var pass = $('#iPassword').val();

		$.post('<?php echo PROTO.HOME?>/site/login', {username: user, password: pass}, function(data) {
			console.log(data);
		});
	}
	$('#fLogin').submit(function (e) {
		e.preventDefault();
		login();
	});
	</script>
</div>