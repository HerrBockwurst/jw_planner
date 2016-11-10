<?php
if(!isset($_POST)) returnErrorJSON('No Data given!');

$mail = $_POST['mail'];
$name = $_POST['name'];
$tel = $_POST['tel'];
$type = $_POST['type'];
$msg = $_POST['msg'];

if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) returnErrorJSON(getString('feedback noValidMail'));

$betr = MAIL_BETR.$type;

$msg = "Name: $name \nTel: $tel \n$msg";

if(!@mail('chef@herrbockwurst.de', $betr, $msg, 'From: <'.$name.'>'.$mail.'\r\n'.MAIL_HEADER)) returnErrorJSON(getString('feedback mailproblem'));