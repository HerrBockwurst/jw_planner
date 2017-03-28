<?php
define('POS_FRONTEND', 1);
define('POS_PLANNER', 2);

interface IElement {
	public static function getInstance(); 
}

abstract class AElement implements IElement {
	protected $ClassPath = NULL;
	protected $Position = NULL;
}