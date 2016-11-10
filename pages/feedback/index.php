<div  id="feedbackfield" class="fieldset">
	<div class="headline"><?php displayString('menu feedback')?></div>
	<?php displayString('feedback infos')?>
	<table>
		<tr>
			<td><?php displayString('feedback type')?></td>
			<td>
				<div id="type">					
					<span data-value="problem"><?php displayString('feedback problem')?></span>
					<img src="images/arr_down.png" />
					<div>
						<span data-active="1" data-value="problem"><?php displayString('feedback problem')?></span>
						<span data-active="0" data-value="suggestion"><?php displayString('feedback suggestion')?></span>
						<span data-active="0" data-value="other"><?php displayString('feedback other')?></span>
					</div>
				</div>			
			</td>
		</tr>
		<tr>
			<td><?php displayString('common name')?></td>
			<td><input type="text" id="name"/></td>
		</tr>
		<tr>
			<td><?php displayString('feedback contact')?></td>
			<td><input type="text" id="contact" /></td>
		</tr>
		<tr>
			<td><?php displayString('feedback tel')?></td>
			<td><input type="text" id="tel" /></td>
		</tr>
		<tr>
			<td style="vertical-align: top"><?php displayString('feedback message')?></td>
			<td><textarea id="text"></textarea></td>
		</tr>
		<tr>
			<td><button><?php displayString('feedback send')?></button></td>
			<td></td>
		</tr>
	</table>
</div>
<script>
$('#type').children('div').children('span').click(function() {
	$('#type').children('span').attr('data-value', $(this).attr('data-value')).text($(this).text());
	$('#type').children('img').trigger('click');
});

$('#type').children('img').click(function() {	
	$(this).siblings('div').css('display') == 'none' ? $(this).siblings('div').slideDown(100) : $(this).siblings('div').slideUp(100);	
});

$('#feedbackfield').find('button').click(function() {
	postdata = {}
	postdata.type = $('#type').children('span').text() + " (" + $('#type').children('span').attr('data-value') + ")";
	postdata.name = $('#name').val();
	postdata.mail = $('#contact').val();
	postdata.tel = $('#tel').val();
	postdata.msg = $('#text').text();

	$.post('<?php echo PROTO.HOME?>/datahandler/feedback/send', postdata, function(data) {
		console.log(data);
	});
});
</script>