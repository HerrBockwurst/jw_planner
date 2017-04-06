<?php
class Dashboard extends AppModule {
	private function __construct() {
		$this->ClassPath = 'dashboard';
		$this->PageID = 'dashboard';
		$this->Position = POS_PLANNER;
		$this->IsDefaultPage = TRUE;
		$this->MenuItem = new MenuItem(getString('Planner Menu DashboardSub'), PROTO.HOME.'/App/Dashboard', 'dashboard', 0);
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Dashboard();
		return $Self;
	}
	
	public function myContent() {
		echo 'Dashboard';
	}
}