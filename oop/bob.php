<?php
class bob {
	
	function __construct() {
		global $index, $ModulHandler;
		
		if(SSL) define('PROTO', 'https://');
		else define('PROTO', 'http://');
		
		define('MODUL', 1);
		define('PAGE', 2);
		define('DIRECT', 3);

		if(!isset($_POST['noheader']))
			require_once 'pages/header.php';
		
	}
	
	public function redirect($url) {
		header("Location:".$url);
		exit;
	}
	
	public function build($data) {
		global $index;
		
		switch($data[0]):
			case 1:
				$this->buildModul($data);
				break;
			case 2:
				$this->buildPage($data);
				break;
			case 3:
				$this->buildDirect($data[1]);
				break;
			default:
				break;
		endswitch;
	}
	
	private function buildModul($data) {
		global $index, $ModulHandler, $bob;
		$moduldata = $ModulHandler->getData($data[1]);
		require_once 'modules/'.$moduldata[0].'/'.$moduldata[1];
		
	}
	
	private function buildPage($data) {
		global $index, $bob;
		require_once 'pages/'.$data[1].'.php';
	
	}
	
	private function buildDirect($string) {
		global $index, $bob;
		if(file_exists($string)) require_once $string;
		else die(getString('error>filenotfound'));
	}
}

$bob = new bob();