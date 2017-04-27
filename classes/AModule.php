<?php
interface IModule {
	function ContentRequest();
}

abstract class AModule implements IModule {
	public $PageID = NULL, $isDefault = FALSE, $ClassPath = NULL, $MenuItem = NULL, $Permission = NULL, $CSSFile = NULL, $Scope = SCOPE_FRONTEND;
	
	public final function getMyContent() {
		if(	(!is_null($this->Permission) && !User::getMyself()->hasPermission($this->Permission)) ||
			($this->Scope == SCOPE_DESKTOP_APP && !User::getMyself()->Valid)) {
				if(testAjax()) echo json_encode(array('redirect' => PROTO.HOME));
				else header("Location:".PROTO.HOME);
				exit;
		}
			
		$this->ContentRequest();
	}
}