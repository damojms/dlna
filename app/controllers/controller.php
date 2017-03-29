<?php

namespace Controllers;

class Controller
{
	public function __construct() {
		$f3 = \Base::instance();
		if($f3->{DEBUG} > 0) {
			$debugbar = new \DebugBar\StandardDebugBar();
			$debugbarRenderer = $debugbar->getJavascriptRenderer($f3->{BASE}.'/resources');
			$debugbar->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($f3->{DB}->pdo()));
			$f3->set('DBBar', $debugbar);
			$f3->set('DBBarRender', $debugbarRenderer);
		}
	}

}
