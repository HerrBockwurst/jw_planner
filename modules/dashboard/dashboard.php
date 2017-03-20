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
	
	private function getInfoTab() {
		$String = '';
	}
	
	private function getMessageBox() {
		if(!User::getInstance()->hasPerm('dashboard.msg')) return '';
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
			if($Count%5 == 0) 
				$RetVal .= '<a>'.getString('dashboard getMore').'</a><div style="display: none;">';				
			
			$DeleteButton = User::getInstance()->hasPerm('dashboard.admin') ?
				'<span class="Dashboard_Delbutton clickable" data-msgid="'.$Message['msg_id'].'"></span>' : '';
				
			
			$RetVal .= '
				<div class="Dashboard_MessageRow">
					<div>'.$Message['name'].'<br /><span>('.date("d.m.Y", $Message['created']).')</span></div>
					'.$DeleteButton.'
					<div>'.$Message['content'].'</div>					
					<br class="floatbreak" />
				</div>
					';	
			$Count++;
		}
		
		for($i = 0; $i < floor($Count/5); $i++) 
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
		$ToReplace = array('USER' => $User[0], 'SYSMESSAGE' => $SysMessage, 'MESSAGEBOX' => $this->getMessageBox(), 'INFOTAB' => $this->getInfoTab());
		echo replaceLangTags(replacer(loadHtml('Overview.html', $this->ClassPath), $ToReplace));
	}
	
	private function Handler_sendMessage() {
		if(!isset($_POST['text'])) returnErrorJSON(getString('errors formSubmit'));
		if(!User::getInstance()->hasPerm('dashboard.msg')) returnErrorJSON(getString('errors noPerm')); 
		
		$Message = preg_replace('/\\n/', '<br />', preg_replace('/\\n\\r/', '<br />', htmlentities($_POST['text'])));
		MessageManager::sendMessage('all', '', $Message);
		
		echo json_encode(array(
				'html' => $this->getMessageBox()
		));

	}
	
	private function Handler_delMessage() {
		if(!User::getInstance()->hasPerm('dashboard.admin')) returnErrorJSON(getString('errors noPerm'));
		if(!isset($_POST['mid'])) returnErrorJSON(getString('errors formSubmit'));
		
		$Message = MessageManager::getMessage($_POST['mid']);
		if(!$Message) returnErrorJSON(getString('errors formSubmit'));
		
		$Sender = new Foreigner($Message->sender);
		if(!$Sender->Valid) returnErrorJSON(getString('errors formSubmit'));
		
		if(!User::getInstance()->hasVSAccess($Sender->VSID) || $Message->recipient != 'all') returnErrorJSON(getString('errors noPerm'));
		
		MessageManager::delMessage($_POST['mid']);
		
		echo json_encode(array(
				'html' => $this->getMessageBox()
		));
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
		switch(getURL(2)) {
			case 'sendMessage':
				$this->Handler_sendMessage();
				break;
			case 'delMessage':
				$this->Handler_delMessage();
				break;
			default:
				break;
		}	
	}
}