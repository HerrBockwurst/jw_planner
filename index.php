<?php
session_start();
spl_autoload_register(function($Class) {
	require_once "classes/{$Class}.php";
});

require_once 'jwplanner.php';
new JWPlanner();