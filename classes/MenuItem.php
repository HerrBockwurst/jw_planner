<?php

define('MENU_ITEM_POS_MAIN', 1);
define('MENU_ITEM_POS_SMALL', 2);

class MenuItem {
	private $String, $Order, $MenuPos, $Permission, $Link;
	
	function __construct($Tree, $Order, $URL, $Permission = "", $Position = MENU_ITEM_POS_MAIN) {
		
		//if(!is_int($Order) || !is_int($Position)) return;
		
		$this->String = getString($Tree);
		$this->Order = $Order;
		$this->MenuPos = $Position;
		$this->Link = $URL;
		$this->Permission = $Permission;
	}
	
	public function getLink() {
		return $this->Link;
	}
	
	public function getString() {
		return $this->String;
	}
	
	public function getOrder() {
		return $this->Order;
	}
	
	public function getMenuPos() {
		return $this->MenuPos;
	}
	
	public function getPermission() {
		return $this->Permission;
	}
	
}