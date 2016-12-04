<div class="sysconsole">
	<?php 
	require_once 'changelog.php';
	require_once 'messages.php';
	?>
</div>
<script>

$('.headline').children('img').click(function() {
	var div = $(this).parent().next('div');
	var deg = div.css('display') == 'block' ? "270deg" : "0deg";
	var css = {"-moz-transform": "rotate("+deg+")",
			"-ms-transform": "rotate("+deg+")",
			"-o-transform": "rotate("+deg+")",
			"-webkit-transform": "rotate("+deg+")",
			"transform": "rotate("+deg+")"};
	$(this).css(css);
	
	if(div.css('display') == 'block') div.slideUp(300);
	else div.slideDown(300);
});
</script>