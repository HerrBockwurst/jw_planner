		</main>
		<div id="Footer">
			<a href="/Disclaimer"><?php displayString('Menu Disclaimer')?></a>
			<a href="/About"><?php displayString('Menu About')?></a>
			<a href="/Contact"><?php displayString('Menu Contact')?></a>
		</div>
		<div id="LoadingBox">
			<div id="LoadingBox_Inner">
				<div class="spinner">
					<div class="double-bounce1"></div>
					<div class="double-bounce2"></div>
				</div>
			</div>
		</div>
		<div id="MessageBox">
			<div id="MessageBox_Inner">
			</div>
		</div>
		<script>
			$('nav').scrollToFixed();

			$('a').bind('click', linkClick);

			$(function() {
				history.replaceState({ url: window.location.href, container: 'main' }, '');
				bindInputs();
			});
		</script>
	</body>
</html>