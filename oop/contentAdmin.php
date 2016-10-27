<?php
class ContentEntry {
	public $id, $pages, $handlers, $css, $js, $permission, $type;

	function __construct($id, $pages, $handlers, $css, $js, $permission, $type) {
		$this->id = $id;
		$this->pages = $pages;
		$this->handlers = $handlers;
		$this->css = $css;
		$this->js = $js;
		$this->permission = $permission;
		$this->type = $type;
	}
	
	public function getPageUrl($id) {
		if(!isset($this->pages[$id])) return false;
		return $this->pages[$id];
	}
	
	public function getHandlerUrl($id) {
		if(!isset($this->handlers[$id])) return false;
		return $this->handlers[$id];
	}
}

class ContentAdmin {
	
	private $content;
	
	function __construct() {
		$this->registerAll();
	}
	
	private function registerAll() {
		$dir = scandir('pages');
		foreach($dir as $file) {
			if(strpos($file, '.') !== false) continue; //Datei ist kein Ordner			
			if(!file_exists("pages/$file/info.xml")) continue; //Datei hat keine info.xml
			
			$currPage = simplexml_load_file("pages/$file/info.xml");
			//print_r($currPage);
			
			if(isset($this->content[strval($currPage->id)])) continue; //Wenn identische ID's, dann überspringen
			
			$pages = array();
			$handlers = array();
						
			foreach($currPage->pages->children() AS $key => $page) 
				$pages[strval($key)] = strval($page);
			
			foreach($currPage->handlers->children() AS $key => $handler)
				$handlers[strval($key)] = strval($handler);

			$this->content[strval($currPage->id)] = new ContentEntry(
					strval($currPage->id),
					$pages,
					$handlers,
					strval($currPage->css),
					strval($currPage->js),
					strval($currPage->permission),
					strval($currPage->type)
					); 			
		}
		
	}
	
	public function displayContent($id, $subpage = 'index') {
		if(!isset($this->content[$id])) return;
		/*
		 * Für Direktes laden
		 */
		global $user;		
		
		if(strpos($subpage, ".") !== false) {
			if(strpos($subpage, ".css") !== false) header ('Content-type: text/css; charset=utf-8');
			else header ('Content-type: text/html; charset=utf-8');
			
			require_once "pages/$id/$subpage";
			exit;
		}
		
		if($this->content[$id]->permission != '' && !$user->hasPerm($this->content[$id]->permission)) return;
		
		/*
		 * Für Laden von Unterseiten per Definition
		 */
		$url = $this->content[$id]->getPageUrl($subpage);
		if($url) {
			header ('Content-type: text/html; charset=utf-8');
			require_once "pages/$id/$url";
		}
		else return false;
	}
	
	public function loadHandler($pageid, $handler) {
		global $user;
		
		if(!isset($this->content[$pageid])) return;
		if($this->content[$pageid]->permission != '' && !$user->hasPerm($this->content[$pageid]->permission)) return;
		
		$url = $this->content[$pageid]->getHandlerUrl($handler);
		if($url) require_once "pages/$pageid/$url";
		else return false;
	}
	
	public function displayAll($type) {		
		if($type == 'css') {
			foreach($this->content AS $currContent)
				if($currContent->css != '') 
					echo "<link rel=\"stylesheet\" type=\"text/css\"  href=\"".PROTO.HOME."/load/".$currContent->id."/".$currContent->css."\"></link>";
				
		}
		elseif($type == 'js') { 
			foreach($this->content AS $currContent)
				if($currContent->js != '')
					echo "<script src=\"".PROTO.HOME."/load/".$currContent->id."/".$currContent->js."\"></script>";
		}
	}
	public function getAllContentBy($by, $crit, $testperm = true) {
		global $user;
		$ret = array();
		foreach($this->content AS $content) {
			if($content->$by != $crit) continue;
			
			if($testperm && !$user->hasPerm($content->permission) && $content->permission != '') continue;
			
			$ret[] = $content;
		}
		
		return $ret;
	}
}

$content = new ContentAdmin();