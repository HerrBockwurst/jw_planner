		</div><!-- Content -->
		<div id="Footer">
			<a href="/Disclaimer"><?php displayString('Common Disclaimer')?></a>
			<a href="/About"><?php displayString('Common About')?></a>
			<?php if(User::getMyself()->IsLoggedIn): ?>
			<a href="/Donate"><?php displayString('Common Donate')?></a>
			<?php endif; ?>
		</div>
		<script>
		$(function() {
			history.replaceState({ url: window.location.href, container: '#Content' }, '');
			$('#MenuBar').scrollToFixed();			
		});
		$('a').click(function (e) {
			if(e.target.href == '') return;
			linkClick(e.target.href);
			e.preventDefault();
		});
		bindInputs();
		</script>
	</body>
</html>