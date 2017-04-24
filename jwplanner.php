<?php
class JWPlanner {
	public function __construct() {
		require_once 'config.php';
		require_once 'functions.php';
		
		//Content Scope setzen
		if(strpos(strtolower($_SERVER['SERVER_NAME']), 'api.') !== FALSE)
			ContentHandler::setScope(SCOPE_API);
		elseif(strpos(strtolower($_SERVER['SERVER_NAME']), 'app.') !== FALSE)
			ContentHandler::setScope(SCOPE_DESKTOP_APP);
		else
			ContentHandler::setScope(SCOPE_FRONTEND);
		
		ContentHandler::loadPages();
		ContentHandler::deliverContent();
	}
}