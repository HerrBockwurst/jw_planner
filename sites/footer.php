<?php if(!isset($fromIndex)): header("Location:".getURL()); exit; endif; ?>
		<?php if($url->value(0) != 'login'): //Nur ausgeben, wenn keine Login Maske ?>			
		</div> <!--  Wrapper -->
		<?php endif; ?>
		<div id="footer">
			<div style="float:left; height: 14px; margin: 8px; font-size:14px;">
				<a href="mailto:<?php echo $CONFIG['errormail']; ?>"><?php displayText('common>reportError');?></a>
				<!-- evtl. <span style="margin: 0px 10px;">|</span>
				<a href="#"><?php displayText('common>donate');?></a> -->
			</div>
			<div style="float:right; height: 14px; margin: 8px; font-size:14px;">
				<a href="<?php printURL(); ?>/impressum"><?php displayText('common>about');?></a>
				<span style="margin: 0px 10px;">|</span>
				<a href="<?php printURL(); ?>/datenschutz"><?php displayText('common>datasec');?></a>
			</div>
		</div>
	</body>
</html>
<?php
?>