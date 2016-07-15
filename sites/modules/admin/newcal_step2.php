<?php 
if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

require_once 'libs/cadd_step2.php';
?>
<div class="field">
	<div class="headline"><?php displayText('admin>add_cal')?></div>
	
	<?php if(isset($ERROR['caladd'])): $noform=true;?><div class="error"><?php echo $ERROR['caladd']; ?></div><?php endif;?>
	
	<?php if(!isset($noform)): ?>
		<?php if(isset($SUCCESS['caladd'])): ?><div class="success"><?php echo $SUCCESS['caladd']; ?></div><?php endif; ?>
		<form class="morespace" id="caladd" action="<?php printURL(); ?>/<?php echo $url->value(0); ?>/editcal/<?php echo $cname; ?>" method="post">
			<div class="formrow smallspace">
				<label for="cid"><?php displayText('admin>c_id')?></label>
				<input type="text" id="cid" value="<?php echo $cname; ?>" disabled />							
			</div>
			<div class="formrow smallspace">
				<label for="type"><?php displayText('admin>c_type')?></label>
				<input type="text" id="type" value="<?php displayText("admin>c_type_".$_POST['type']) ?>" disabled />							
			</div>
			<input type="hidden" name="submitted" value="1" />
			<div class="smallspace"><?php displayText('help>c_dtype');?></div>
			<input class="inputsubmit" type="submit" value="<?php displayText('common>next')?>"/>
		</form>
	
	<?php endif; ?>
	<div class="morespace">
		<a href="<?php printURL();?>/<?php echo $url->value(0); ?>"><?php displayText('common>back')?></a>
	</div>
</div>

