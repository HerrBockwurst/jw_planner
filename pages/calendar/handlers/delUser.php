<?php
if(!isset($_POST['pid']) || !isset($_POST['uid'])) exit;

$mysql->where('uid', $_POST['uid']);