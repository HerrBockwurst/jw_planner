<?php
class Dashboard_welcome {
	public static function print() {
		echo "<h1 id=\"WelcomeTXT\">".getString('dashboard Welcome').User::getInstance()->Clearname."</h1>";
	}
}