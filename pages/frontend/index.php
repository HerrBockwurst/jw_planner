<?php
class Frontend_Index extends StaticPage {
	private function __construct() {
		$this->ClassPath = 'index.php';
		$this->PageID = 'Start';
		$this->Position = POS_FRONTEND;
		$this->IsDefaultPage = TRUE;
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Frontend_Index();
		return $Self;
	}
	
	public function myContent() {
		echo $_SERVER['HTTP_X_REQUESTED_WITH'];
	}
}