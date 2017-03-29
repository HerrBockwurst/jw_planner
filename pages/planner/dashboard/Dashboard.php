<?php
class Dashboard extends AppModule {
	private function __construct() {
		$this->ClassPath = 'dashboard';
		$this->PageID = 'dashboard';
		$this->Position = POS_PLANNER;
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Dashboard();
		return $Self;
	}
}