<input type="hidden" id="hidden_cid" value="<?php echo $_POST['cid']?>">
<div class="headline"><?php displayString('calendaradmin repeatingPosts') ?></div>
<div style="width: 708px; margin: 0 auto;">
	<div class="weekentry">
		<span><?php displayString('common monday')?></span>
		<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
	</div>
	<div class="weekentry">
		<span><?php displayString('common tuesday')?></span>
		<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
	</div>
	<div class="weekentry">
		<span><?php displayString('common wednesday')?></span>
		<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
	</div>
	<div class="weekentry">
		<span><?php displayString('common thursday')?></span>
		<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
	</div>
	<div class="weekentry">
		<span><?php displayString('common friday')?></span>
		<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
	</div>
	<div class="weekentry">
		<span><?php displayString('common saturday')?></span>
		<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
	</div>
	<div class="weekentry">
		<span><?php displayString('common sunday')?></span>
		<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
	</div>
	<br class="floatbreak" />
</div>
<script>
$('.post').click(function(event) {
	setTimeout(function() { $('.tooltip').css({top: event.pageY, left: event.pageX}).fadeIn(100) }, 100);
	$('.tooltip').stop().fadeOut(100);
});
</script>