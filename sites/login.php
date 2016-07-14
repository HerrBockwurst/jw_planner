<?php

while(true):
	/*
	 * Zuerst kommen Abfragen, ob das Forumlar übergeben wurde und ob es richtig ausgefüllt wurde.
	 * Jede Verneinung bringt einen abbruch des Login-Vorgangs mit sich.
	 */

	if(!isset($_POST['submitted'])) break; //Überhaupt abgesendet?

	if(!$mysql->execute("DELETE FROM `loginfails` WHERE `expires` <= ?", 's', getSQLDate()))
		echo Test;
	
	$result = $mysql->execute("SELECT * FROM `loginfails` WHERE `ip` LIKE ? OR `ip` LIKE ?", "ss", array("%".getIP('REMOTE')."%", "%".getIP('FORWARD')."%")) or die($mysql->error());
	if($result->num_rows >= 10):
		$ERROR['login'] = getLang('errors>login_delay');
		break;
	endif;
	
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
	
	if(hash('sha512',$_POST['password']) != $result['password']):
		/*
		 * Falsches Passwort
		 */
	
		$ERROR['login'] = getLang('errors>login_wrong');
		/*
		 * Login Sperre nach 10 versuchen
		 */

		$mixed = array(getIP(),$_POST['username'],getSQLDate(time()+(60*10)));
		if(!$mysql->execute("INSERT INTO `loginfails` (`ip`, `uid`, `expires`) VALUES (?, ?, ?)", 'sss', $mixed))
			$log->write("Konnte Loginfail nicht eintragen: ".$mysql->error(), 'error');					
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
	$log->write("Benutzer ".$_SESSION['uid']." hat sich erfolgreich angemeldet!");

	/*
	 * Session anlegen (Vorher evtl alte Session löschen)
	 */
	$mysql->execute("DELETE FROM `sessions` WHERE `sid` = ?", 's', $_SESSION['dbid']);
	
	$sessqry = "INSERT INTO `sessions` (`sid`, `user`, `expire`) VALUES (?, ?, ?)";	

	if($mysql->execute($sessqry, 'sss', array($_SESSION['dbid'], $_SESSION['uid'], getSQLDate(time()+($CONFIG['sessiontime']*60))))):
		header("Location:".getURL());
		exit;
	endif;
	
	break;
endwhile;

?>

<div id="loginwrapper">
	<a id="loginlogo" href="<?php printURL(); ?>"><span style="font-size:3.5em">JW</span><span style="font-size:1.5em">Planner</span></a>
	<div id="loginformwrapper">
		<?php if(isset($ERROR['login'])): ?>
		<div class="error relative center" style="margin: 10px auto;"><?php echo $ERROR['login']; ?></div>
		<?php endif; ?>
		<form id="login" action="<?php printURL(); ?>/login" method="POST">
			<div class="relative">
				<label for="username"><?php displayText('login>user'); ?>:</label>
				<input type="text" id="username" name="username" class="loginformfield" value="<?php printIfSet($_POST, "username"); ?>" />
				<img class="loginimage" src="<?php printURL();?>/images/user.png" />
				&nbsp;
			</div>
			<div class="relative" style="margin-top: 20px;">
				<label for="password"><?php displayText('login>password'); ?>:</label>
				<input type="password" id="password" name="password" class="loginformfield"/>
				<img class="loginimage" src="<?php printURL();?>/images/key.png" />
				&nbsp;
			</div>
			<div class="relative center" style="margin-top: 30px; margin-bottom: 10px;">
				<input type="submit" class="loginsubmit" value="<?php displayText('login>submit'); ?>">
			</div>
			<input type="hidden" name="submitted" value="1" />
		</form>
		<a href="<?php printURL() ?>/password_reset" class="smaller" style="float:right"><?php displayText('login>forgotten'); ?></a>
	</div>
	<div class="center small"><a href="mailto:<?php echo $CONFIG['contactmail']; ?>"><?php displayText('login>wantaccess')?></a></div>
</div>

<?php
//TODO
?>