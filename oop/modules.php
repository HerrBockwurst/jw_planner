<?php
if(!defined('index')) exit;

class ModulHandler {
	private $modules;
	
	function __construct() {
		$this->modules = array();
	}
	
	public function check($name) {
		if(in_array($name, $this->modules)) return true;
		return false;
	}
	
	public function registerModul($data) {
		$this->modules[$data[0]] = $data; 
	}
	
	public function listModules() {
		print_r($this->modules);
	}
	
	public function getModules() {
		return $this->modules;
	}
	
	public function getData($name) {
		if(isset($this->modules[$name])) return $this->modules[$name];
		return false;
	}
}

$ModulHandler = new ModulHandler();

loadModules();