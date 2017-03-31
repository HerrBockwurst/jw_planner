<?php
abstract class AppModule extends AElement {	
	protected $Permission = NULL;
	protected $CSSFile = NULL;
	
	public final function getMyContent() {
		$this->myContent();
	}
}