<?php
class Dashboard extends Module {
	private function __construct() {
		$this->Permission = "";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'dashboard';
		$this->MenuItem = new MenuItem("menu Dashboard", 0, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Dashboard();
			return $Instance;
	}
	
	private function getMessageBox() {
		$Messages = MessageManager::getDashboardMessages();
		$RetVal = '
				<div class="Dashboard_MessageRow">
					<div><button style="width: 100px; padding: 5px 0px;">'.getString('dashboard write').'</button></div>
					<div><textarea id="Dashboard_MessageText"></textarea></div>
					<br class="floatbreak" />
				</div>
				';
		$Count = 1;
		foreach($Messages AS $Message) {
			if($Count%5 == 0) {
				$RetVal .= '<a>'.getString('dashboard getMore').'</a><div style="display: none;">';
				
			}
			$RetVal .= '
				<div class="Dashboard_MessageRow">
					<div>'.$Message['name'].'<br /><span>('.date("d.m.Y", $Message['created']).')</span></div>
					<div>'.$Message['content'].'</div>
					<br class="floatbreak" />
				</div>
					';	
			$Count++;
		}
		
		for($i = 1; $i < floor($Count/5); $i++) 
			$RetVal .= '</div>';
		
		return $RetVal;
	}
	
	private function Overview() {
		//Systemnachricht holen
		$Filter = array(
				'sender' => 'system',
				'recipient' => 'all',
		);
		$Order = array('created' => 'DESC');
		
		$SysMessage = MessageManager::getMessageBy($Filter, $Order, 1);
		$SysMessage = '<span>'.$SysMessage->title.'</span>'.$SysMessage->content;
		//Namen anzeigen
		$User = explode(" ", User::getInstance()->Clearname);
		$ToReplace = array('USER' => $User[0], 'SYSMESSAGE' => $SysMessage, 'MESSAGEBOX' => $this->getMessageBox());
		echo replaceLangTags(replacer(loadHtml('Overview.html', $this->ClassPath), $ToReplace));
	}
	
	public function ActionLoad() {
		switch(getURL(2)) {
			default:
				$this->Overview();
				break;
		}
	}
	public function ActionSite() {
	
	}
	public function ActionDataHandler() {
	
	}
}