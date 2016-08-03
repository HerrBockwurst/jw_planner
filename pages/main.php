<script class="removeme">
$(function() {
	$("body").css({"background-image": "url(\"<?php echo PROTO.HOME; ?>/images/default_bg.png\")",
						"background-repeat": "no-repeat",
						"background-position": "right top"});
	var y = $(window).height() - $('#topbar').height();
	$("#site").css({'min-height': y});
	});
</script>
<?php
