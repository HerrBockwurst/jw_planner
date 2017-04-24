<?php
namespace Frontend;

class Functions extends \AModule {
	function __construct() {
		$this->PageID = 'functions';
	}
	
	function ContentRequest() {
		echo "Funktionen";
	}
}