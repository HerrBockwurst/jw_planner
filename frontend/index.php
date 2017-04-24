<?php
namespace Frontend;

class Index extends \AModule {
	function __construct() {
		$this->PageID = 'start';
		$this->isDefault = TRUE;
	}
	
	function ContentRequest() {
		echo "Startseite";
	}
}