<?php
namespace Frontend;

class Functions extends \AModule {
	function __construct() {
		$this->PageID = 'functions';
		$this->ClassPath = 'frontend';
	}
	
	function ContentRequest() {
		echo "Funktionen";
	}
}