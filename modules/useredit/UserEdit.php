<?php
class UserEdit extends Module {
	
	private function __construct() {
		$this->Permission = "";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'useredit';
		$this->MenuItem = new MenuItem("menu UserEdit", 50, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new UserEdit();
		return $Instance;
	}
	
	public function ActionLoad() {
		switch(getURL(2)) {
			case 'searchUser':
				UserEdit_UserList::print();
				break;
			default:
				UserEdit_Overview::print();
				break;
		}
		
	}
	public function ActionSite() {
		
	}
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'updateFields':
				if(!isset($_POST['uid'])) return;
				UserEdit_EditUser::loadUserData($_POST['uid']);
				break;
			case 'updateUser':
				UserEdit_EditUser::updateUser($_POST);
				break;
			default:
				break;
		}
	}
}