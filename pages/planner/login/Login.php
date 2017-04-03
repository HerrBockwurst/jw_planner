<?php
class Login extends AppModule {
	private function __construct() {
		$this->ClassPath = 'login';
		$this->PageID = 'login';
		$this->Position = POS_PLANNER;
		$this->IsDefaultPage = TRUE;
		$this->CSSFile = 'style.css';
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Login();
		return $Self;
	}
	
	public function myContent() {
		
	}
}