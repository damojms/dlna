<?php
namespace DMS;
use DebugBar\StandardDebugBar;

class Controller
{
	public function __construct() {
		$debugbar = new StandardDebugBar();
		$debugbarRenderer = $debugbar->getJavascriptRenderer('/resources');
		$f3 = \Base::instance();
		$f3->set('DBBar', $debugbar);
		$f3->set('DBBarRender', $debugbarRenderer);
	}

}