<?php


while(true):
	/*
	 * Zuerst kommen Abfragen, ob das Forumlar �bergeben wurde und ob es richtig ausgef�llt wurde.
	 * Jede Verneinung bringt einen abbruch des Login-Vorgangs mit sich.
	 */
	if(!isset($_POST['submitted'])) break; //�berhaupt abgesendet?
	
	if($_POST['username'] == "" || $_POST['password'] == ""): //Felder leer gelassen?
		$ERROR['login'] = getLang('errors>login_wrong');
		break;
	endif;
	
	$result = $mysql->execute("SELECT * FROM `users` WHERE `uid` = ? LIMIT 1", "s", $_POST['username']);
	$result = $result->fetch_assoc();
	
	if($result == NULL): //Wenn Benutzer nicht existiert
		$ERROR['login'] = getLang('errors>login_wrong');
		break;
	endif;
	
	if(password_hash($_POST['password'], PASSWORD_DEFAULT, array('salt' => $CONFIG['password_salt'])) != $result['password']):
		/*
		 * Falsches Passwort
		 */
	
		$ERROR['login'] = getLang('errors>login_wrong');
		/*
		 * Login Sperre nach 10 versuchen
		 */
		
		
		break;
	endif;
	
	if($result['status'] != 'active'): //Wenn Benutzer gesperrt wurde
		$ERROR['login'] = getLang('errors>login_inactive');
		break;
	endif;

	
	/*
	 * Login Erfolgreich:
	 */
	
	$_SESSION['uid'] = $result['uid'];
	$_SESSION['dbid'] = session_id();

	/*
	 * Session anlegen
	 */
	
	$sessqry = "INSERT INTO `sessions` (`sid`, `user`, `expire`) VALUES (?, ?, ?)";	

	if($mysql->execute($sessqry, 'sss', array($_SESSION['dbid'], $_SESSION['uid'], date("Y-m-d H:i:s",time()+($CONFIG['sessiontime']*60)))))
		header("Location:".getURL());
	
	
	
	break;
endwhile;

?>

<div id="loginwrapper">
	<a id="loginlogo" href="<?php printURL(); ?>"><span style="font-size:3.5em">JW</span><span style="font-size:1.5em">Planner</span></a>
	<div id="loginformwrapper">
		<?php if(isset($ERROR['login'])): ?>
		<div class="error pos_rel center" style="margin: 10px auto;"><?php echo $ERROR['login']; ?></div>
		<?php endif; ?>
		<form id="login" action="<?php printURL(); ?>/login" method="POST">
			<div class="pos_rel">
				<label for="username"><?php displayText('login>user'); ?>:</label>
				<input type="text" id="username" name="username" class="loginformfield" value="<?php printIfSet($_POST, "username"); ?>" />
				<img class="loginimage" src="<?php printURL();?>/images/user.png" />
				&nbsp;
			</div>
			<div class="pos_rel" style="margin-top: 20px;">
				<label for="password"><?php displayText('login>password'); ?>:</label>
				<input type="password" id="password" name="password" class="loginformfield"/>
				<img class="loginimage" src="<?php printURL();?>/images/key.png" />
				&nbsp;
			</div>
			<div class="pos_rel center" style="margin-top: 30px; margin-bottom: 10px;">
				<input type="submit" class="loginsubmit" value="<?php displayText('login>submit'); ?>">
			</div>
			<input type="hidden" name="submitted" value="1" />
		</form>
		<!--  comming soon <span class="smaller"><?php displayText('login>forgotten'); ?></span> -->
	</div>
	<div class="center small"><a href="mailto:<?php echo $CONFIG['contactmail']; ?>"><?php displayText('login>wantaccess')?></a></div>
</div>

<?php
//TODO
?>