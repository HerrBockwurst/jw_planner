<?php

global $mysql;

if(!empty($_POST['u'])) $mysql->where('users.name', "%".$_POST['u']."%", 'LIKE');
if(!empty($_POST['v'])) $mysql->where('users.vsid', "%".$_POST['v']."%", 'LIKE');
$mysql->join(array("users" => "vsid", "versammlungen" => "vsid"));

$mysql->select('users', array('name', 'uid', 'vsid', 'versammlungen.name' => 'vsname'));

if($mysql->countResult() == 0)
	returnErrorJSON(getString('error noUsersFound'));

foreach($mysql->fetchAll() AS $row):

	/*
	 * Benutzer filtern, auf die der sucher kein zugriff hat
	 */
	
	if(!array_key_exists($row['vsid'], getVSAccess('useradmin'))) continue;
?>

<div class="searchentry">
	<span><?php echo $row['name']?> (<?php echo $row['uid'] ?>)</span>
	<span><?php echo $row['vsname'] ?></span>
</div>
<?php 
endforeach;
?>
