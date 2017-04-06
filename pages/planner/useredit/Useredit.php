<?php
class Useredit extends AppModule {
	private function __construct() {
		$this->ClassPath = 'useredit';
		$this->PageID = 'useredit';
		$this->Position = POS_PLANNER;
		$this->CSSFile = 'style.css';
		$this->MenuItem = new MenuItem(getString('Planner Menu Useredit'), PROTO.HOME.'/App/Useredit', 'user', 10);
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Useredit();
		return $Self;
	}
	
	public function myContent() {
		switch(getURL(2)) {
			default:
				$html = new HTMLContent('UserList.html', $this->ClassPath);
				$html->replaceLangTags();
				$html->display();
				break;
		}
	}
}