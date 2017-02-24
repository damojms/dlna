<?php
use DebugBar\StandardDebugBar;

class Controller
{
	public function __construct() {
		$f3 = \Base::instance();

		$debugbar = new StandardDebugBar();
		$debugbarRenderer = $debugbar->getJavascriptRenderer($f3->{BASE}.'/resources');
		
		$f3->set('DBBar', $debugbar);
		$f3->set('DBBarRender', $debugbarRenderer);
	}

}
