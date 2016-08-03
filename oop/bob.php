<?php
class bob {
	
	function __construct() {
		global $ModulHandler;
		
		if(SSL) define('PROTO', 'https://');
		else define('PROTO', 'http://');

		require_once 'pages/header.php';
		
	}
	
	public function buildFooter() {
		require_once 'pages/footer.php';
	}
	
	public function redirect($url) {
		header("Location:".$url);
		exit;
	}
	
	public function build($data) {
		global $DataHandler;
		if(defined('stopbob')) return;
		switch($data[0]):
			case MODUL:
				$this->buildModul($data);
				break;
			case PAGE:
				$this->buildPage($data);
				break;
			case DIRECT:
				$this->buildDirect($data[1]);
				break;
			default:
				break;
		endswitch;
	}
	
	private function buildModul($data) {
		global $ModulHandler, $bob, $DataHandler, $user;		
		$moduldata = $ModulHandler->getData($data[1]);
		if(isset($moduldata[3]) && !$user->hasPerm($moduldata[3])) exit; //Modul nur bauen, wenn Benutzer Permission hat
		require_once 'modules/'.$moduldata[0].'/'.$moduldata[1];
		
	}
	
	private function buildPage($data) {
		global $bob, $DataHandler;
		require_once 'pages/'.$data[1].'.php';
	
	}
	
	private function buildDirect($string) {
		global $bob, $DataHandler;
		if(file_exists($string)) require_once $string;
		else die(getString('error>filenotfound'));
	}
	
	public function startForm($id, $class = "", $target = "", $method = "POST") {
		echo "<form id=\"$id\" class=\"$class\" action=\"$target\" method=\"$method\">";
	}
	public function endForm() {
		echo "</form>";
	}
	public function addFormRow($id, $label, $fielddata, $predata = '', $class = "formrow") {
		
		if($fielddata[0] == 'hidden'):
			echo "<input type=\"hidden\" name=\"$id\" value=\"$fielddata[1]\" />";
			return;
		endif;
		
		$string = "<div class=\"$class\">
			<label for=\"$id\">$label</label>";
		switch($fielddata[0]):
			case 'text': 
				$string .= "<input type=\"text\" id=\"$id\" name=\"$id\" value=\"$predata\" />";
				break;
			case 'password':
				$string .= "<input type=\"password\" id=\"$id\" name=\"$id\" value=\"$predata\" />";
				break;
		endswitch;
		
		$string .= "<br class=\"floatbreak\" /></div>";
		echo $string;
				
	}
	
	public function addButton($name, $id = '', $rowclass = 'formrow') {
		echo "<div class=\"$rowclass\"><input type=\"submit\" value=\"$name\" id=\"$id\" /></div>";
	}
	
	public function createErrorField($id) {
		echo "<div class=\"error\" id=\"$id\"></div>";
	}
}

$bob = new bob();