<?php
namespace Controllers;
class Pages extends Controller {

	public function beforeroute() {
		$f3 = \Base::instance();
		if($f3->{DEBUG} > 0) {
			$debugbar = new \DebugBar\StandardDebugBar();
			$debugbarRenderer = $debugbar->getJavascriptRenderer($f3->{BASE}.'/resources');
			$debugbar->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($f3->{DB}->pdo()));
			$f3->set('DBBar', $debugbar);
			$f3->set('DBBarRender', $debugbarRenderer);
		}
	}

	public function afterroute() {
		echo \Template::instance()->render('index.html');
	}

	public function index($f3) {
		$res = $f3->DB->exec('SELECT * FROM OBJECTS WHERE CLASS="item.videoItem" AND REF_ID IS NULL ORDER BY ID DESC LIMIT 10;');
		// $obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		// $page = $obj->paginate(0, 10, array('CLASS'=>'item.videoItem', 'REF_ID' => 'NULL'), array('order' => 'ID DESC'));
		$f3->set('result', $res);
		$f3->set('content', 'recent.html');
	}

	public function browse($f3) {
		$f3->DBBar['messages']->info('Browsing');
		$id = $f3->get('PARAMS.id');
		if(empty($id)) 
			$id = 0;

		$cpage = $f3->get('PARAMS.page');
		if(empty($cpage))
			$cpage = 0;

		//$cpage = \Pagination::findCurrentPage();
		$filter = array('PARENT_ID=?', $id);
		$options = array('order' => 'CLASS,NAME');

		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		$page = $obj->paginate($cpage, 8, $filter, $options);

		if($page['count'] > 1) {
			$f3->DBBar['messages']->addMessage('Getting some pagination');
			$f3->set('pagination', $this->pagination($f3, $page));
		}
	
		$f3->set('result', $page);

		if(strpos($id, '$') === FALSE) 
			$up = '0';
		else
			$up = substr($id, 0, strrpos($id, '$'));
	
		$f3->set('up', $up);
		$f3->set('content', 'browse.html');
	}


	public function detail($f3) {
		$id = $f3->get('PARAMS.id');
		// $det = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		// $det->load(array('OBJECT_ID', $id));

		// $f3->set('detail', $det);
		$f3->set('content', 'detail.html');
	}

	public function search($f3) {
		$search = $f3->get('PARAMS.search');

		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		$page = $obj->paginate(0, 8, array('NAME=?', $search));

		$f3->set('result', $page);
		$f3->set('content', 'search.html');
	}

	public function error($f3) {
		$f3->set('content', 'error.html');
	}

	private function pagination($f3, &$page) {
		$pg = array();
		
		for($i = 0; $i < $page['count']; $i++) {
			$x = array();
			$x['active'] = ($page['pos'] == $i);
			$x['value'] = $i;
			$x['url'] = $i;
			$pg[] = $x;
		}
		
		return $pg;
	}	
}
