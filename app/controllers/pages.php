<?php
namespace Controllers;
class Pages extends Controller {

	public function beforeroute() {
		$f3 = \Base::instance();
		
		if($f3->get('DEBUG') > 0) {
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
		echo "index";
		$db = $f3->get('DB');
		$res = $db->exec('SELECT * FROM OBJECTS WHERE CLASS="item.videoItem" AND REF_ID IS NULL ORDER BY ID DESC LIMIT 10;');
		// $obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		// $page = $obj->paginate(0, 10, array('CLASS'=>'item.videoItem', 'REF_ID' => 'NULL'), array('order' => 'ID DESC'));
		$f3->set('result', $res);
		$f3->set('content', 'recent.html');
	}

	public function browse($f3) {
		if($f3->DEBUG > 0) {
			$dbBar = $f3->get('DBBar');
			$dbBar['messages']->info('Browsing');
		}
		$id = $f3->get('PARAMS.id');
		if(empty($id)) 
			$id = 0;

		$cpage = $f3->get('PARAMS.page');
		if(empty($cpage))
			$cpage = 1;

		//$cpage = \Pagination::findCurrentPage();
		$filter = array('PARENT_ID=?', $id);
		$options = array('order' => 'CLASS,NAME');

		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		$page = $obj->paginate(($cpage - 1), 8, $filter, $options);

		if($page['count'] > 1) {
			if($f3->DEBUG > 0)
				$dbBar['messages']->addMessage('Getting some pagination');
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

		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		$obj->load([ 'OBJECT_ID LIKE ?', $id ]);

		$det = new \DB\SQL\Mapper($f3->DB, 'DETAILS');
		$det->load(['ID=?', $obj->DETAIL_ID]);

		if(($key = $f3->get('OMDBKEY')) != '') {
			$web = \Web::instance();
			$title = $obj->NAME;
			// Strip year from title?
			$pos = strpos($title, " [");
			if($pos !== FALSE)
				//die(var_dump($pos));
				$title = substr($title, 0, $pos);

			$title = urlencode($title);
			$omdb = $web->request("http://www.omdbapi.com/?t=$title&apikey=$key");
			$f3->set('omdb', json_decode($omdb['body'], true));
		}
		$f3->set('object', $obj);
		$f3->set('detail', $det);
		$f3->set('content', 'detail.html');
	}

	public function search($f3) {
		// Initially get search value from POST then save to hive
		$search = $f3->get('POST.search');
		if($search != '') {
			$f3->set('SESSION.lastSearch', $search);
		}

		// Check if we're paginated

		$cpage = $f3->get('PARAMS.page');
		if(empty($cpage))
			$cpage = 1;
		
		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');

		$filter = [ 'NAME LIKE ?', "%{$f3->get('SESSION.lastSearch')}%" ];
		$option = [ 'group' => 'NAME' ];
		$page = $obj->paginate(($cpage - 1), 8, $filter, $option);

		$f3->set('result', $page);
		$f3->set('search', $f3->get('SESSION.lastSearch'));
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
