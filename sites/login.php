<div id="loginwrapper">
	<div id="loginlogo"><span style="font-size:3.5em">JW</span><span style="font-size:1.5em">Planner</span></div>
	<div id="loginformwrapper">
		<form id="login" action="#">
			<div class="pos_rel">
				<label for="username"><?php displayText('login>user'); ?>:</label>
				<input type="text" id="username" class="loginformfield"/>
				<img class="loginimage" src="<?php printURL();?>/images/user.png" />
				&nbsp;
			</div>
			<div class="pos_rel" style="margin-top: 20px;">
				<label for="password"><?php displayText('login>password'); ?>:</label>
				<input type="password" id="password" class="loginformfield"/>
				<img class="loginimage" src="<?php printURL();?>/images/key.png" />
				&nbsp;
			</div>
			<div class="pos_rel center" style="margin-top: 30px; margin-bottom: 10px;">
				<input type="submit" class="loginsubmit" value="<?php displayText('login>submit'); ?>">
			</div>			
		</form>
		<!--  comming soon <span class="smaller"><?php displayText('login>forgotten'); ?></span> -->
	</div>
	<div class="center small"><a href="mailto:<?php echo $CONFIG['contactmail']; ?>"><?php displayText('login>wantaccess')?></a></div>
</div>

<?php
//TODO
?>