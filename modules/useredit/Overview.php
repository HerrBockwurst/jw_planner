<?php
class UserEdit_Overview {
	static function print() {
		$String = <<<EOF
			<div id="useredit_searchcontent">
				<div id="useredit_searchfield">
					<div class="headline">{useredit Headline}</div>
					<form id="usersearch">
						<label style="float: left; margin-right: 20px;">
							{common name}
							<input type="text" id="iName" />
						</label>
						<label style="float: left; margin-right: 20px;">
							{common versammlung}
							<input type="text" id="iVS" />
						</label>
						<button id="bSearchUser" style="padding: 3px 40px; margin: 5px 40px 5px 0px;">{useredit search}</button>
						<button id="bNewUser" style="padding: 3px 40px; margin: 5px 0px;">{useredit new}</button>
					</form>
				
				</div>
				<div id="useredit_findlist"></div>
			</div>
EOF;
		$String .= UserEdit_EditUser::get();
		echo replaceLangTags($String)."
			<script>				
				$('#useredit_findlist').loadingWheel();

				function searchUser() {
					var name = $('#iName').val();
					var vs = $('#iVS').val();
			
					loadContent('".PROTO.HOME."/load/useredit/searchUser', '#useredit_findlist', {name: name, vs: vs});
				}
			
				$('#usersearch').submit(function (e) {
					e.preventDefault();
					searchUser();
				});
				$(function() {
					searchUser();
				});
			</script>";
	}
}
?>
