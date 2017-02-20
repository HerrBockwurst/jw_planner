<?php
class UserEdit_UserList {
	static function print() {
		$User = isset($_POST['name']) ? $_POST['name'] : "";
		$VS = isset($_POST['vs']) ? $_POST['vs'] : "";
		
		$mysql = MySQL::getInstance();
		
		if($User != "") {
			$mysql->where('name', "%".$User."%", "LIKE");
			$mysql->where('uid',  "%".$User."%", "LIKE", "OR");
		}
		
		if($VS != "") $mysql->where('versammlungen.name', "%".$VS."%", "LIKE");
		
		$mysql->join('users', 'vsid', 'versammlungen', 'vsid');
		$mysql->select('users', array('*', 'vs_name' => 'versammlungen.name'));
		
		
		echo "<table id=\"UserEdit_UserList\">";
		echo "<tr class=\"shader\">
				<td>".getString('common username')."</td>
				<td>".getString('common name')."</td>
				<td>".getString('useredit active')."?</td>
				<td>".getString('useredit role')."</td>
				<td>".getString('common versammlung')."</td>			
			</tr>";
		
		$c = 0;
		foreach($mysql->fetchAll() AS $cUser) {
			$Shader = $c%2 != 0 ? 'class="shader"' : '';
			$Name = $cUser['name'];
			$UID = $cUser['uid'];
			$Aktiv = $cUser['active'] == 1 ? getString('common yes') : getString('common no');
			$Versammlung = $cUser['vs_name'];
			//TODO Rolle
			$Rolle = "Rolle";					
			
			echo "<tr data-uid=\"$UID\" $Shader >
				<td>$UID</td>
				<td>$Name</td>
				<td>$Aktiv</td>
				<td>$Rolle</td>
				<td>$Versammlung</td>			
			</tr>";
			
			$c++;
		}
		
		echo "</table>";
		
		echo "
			<script>
				$('#UserEdit_UserList').find('tr').click(function() {
					console.log(this);
					$('#Content').stop().animate({scrollTop: 0}, 200);
					$('#useredit_searchcontent').animate({left: \"100%\"}, 1000);
					$('#useredit_editusercontent').animate({left: \"0%\"}, 1000);
					
				});
			</script>";
		
	}
}
?>
