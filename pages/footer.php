		</div><!-- Content -->
		<div id="Footer">
			<a href="/Disclaimer"><?php displayString('Common Disclaimer')?></a>
			<a href="/About"><?php displayString('Common About')?></a>
			<?php if(User::getMyself()->IsLoggedIn): ?>
			<a href="/Donate"><?php displayString('Common Donate')?></a>
			<?php endif; ?>
		</div>
		<script>
		$('a').click(function (e) {
			console.log(e.target.href);
			e.preventDefault();
		});
		</script>
	</body>
</html>