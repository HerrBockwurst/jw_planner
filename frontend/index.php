<?php
namespace Frontend;

class Index extends \AModule {
	function __construct() {
		$this->PageID = 'start';
		$this->isDefault = TRUE;
		$this->ClassPath = 'frontend';
	}
	
	function ContentRequest() {
		echo "Startseite";
	}
}