<?php
if(!isset($_SESSION['dbid'])):
	header("Location:".getURL()."/login");
endif;

?>