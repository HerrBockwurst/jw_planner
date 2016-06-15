<?php
if(!isset($_SESSION['dbid']) && $url->value(0) != 'login' ) header("Location:".getURL()."/login");

?>