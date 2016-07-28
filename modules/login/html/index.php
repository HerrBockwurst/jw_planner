<div id="login">
	<div class="logo">
		<a href="<?php echo PROTO.HOME;?>">JW<span style="font-size: 0.6em;">Planner</span></a>
	</div>
	<div style="width: 400px; margin: 20px auto;">
	<?php 
	
		$bob->startForm('loginform');

		$bob->createErrorField('loginerror');		
		$bob->addFormRow('username', getString('common>username'), array('text'), NULL);
		$bob->addFormRow('password', getString('common>password'), array('password'), NULL);
		$bob->addButton(getString('common>login'), 'loginbutton');
		$bob->endForm();
		
	
	?>
	<script>
		$('#loginform').submit(function (event) {
			event.preventDefault();
			
			var username = $('#username').val(),
				password = $('#password').val();

			var posting = $.post('<?php displayHandlerURL('login'); ?>', {'username': username, 'password': password});

			posting.done(function( data ) {
				data = data.replace(/\n/g, '');
				var newdata = JSON.parse(data);
				console.log(data);
				if(typeof newdata.error !== "undefined") {
					$('#loginerror').html('').html(newdata.error[0]).show(100).delay(2000).hide(100);
					return;
				}
				$('#login').fadeOut(800);				
				setTimeout(function() {
					$('#site').load(url + '/ajax/load', {page: 'default'});
				}, 800);	
			});
			
		});
	</script>
	
	<br class="floatbreak;"/>
	</div>
</div>
<script>
	$(function() {
		$('#login').delay(300).fadeIn(800);		
	});
</script>
<?php
