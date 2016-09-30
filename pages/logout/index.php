<?php
global $mysql;
$mysql->where('sid', session_id());
$mysql->delete('sessions');

echo json_encode(array('redirect' => PROTO.HOME));
?>