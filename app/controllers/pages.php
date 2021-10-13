<?php
namespace Controllers;
class Pages extends Controller {

	public function beforeroute() {
		$f3 = \Base::instance();
		
		if($f3->get('DEBUG') > 0) {
			$debugbar = new \DebugBar\StandardDebugBar();
			$debugbarRenderer = $debugbar->getJavascriptRenderer($f3->BASE.'/resources');
			$debugbar->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($f3->DB->pdo()));
			$f3->set('DBBar', $debugbar);
			$f3->set('DBBarRender', $debugbarRenderer);
		}

	}

	public function afterroute() {
		echo \Template::instance()->render('index.html');
	}

	public function recent($f3) {
		$cpage = $f3->get('PARAMS.page');
		if(empty($cpage))
			$cpage = 1;

		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		$page = $obj->paginate(($cpage - 1), 8, [ 'CLASS=? AND REF_ID IS NULL', 'item.videoItem' ], ['order' => 'ID DESC']);
		$f3->set('result', $page);

		if($page['count'] > 1) {
			// build page links
			$pages = new \Pagination($page['total'], $page['limit']);
			// add some configuration if needed
			$pages->setTemplate('partials/pagebrowser.html');
			$pages->setLinkPath('recent/');
			// for template usage, serve generated pagebrowser to the hive
			$f3->set('pagebrowser', $pages->serve());
		}
		$f3->set('content', 'recent.html');
	}

	public function browse($f3) {
		if($f3->DEBUG > 0) {
			$dbBar = $f3->get('dbBar');
			if(isset($dbBar['messages']))
				$dbBar['messages']->info('Browsing');
		}

		$id = $f3->get('PARAMS.id');
		if(empty($id)) 
			$id = 0;

		$cpage = $f3->get('PARAMS.page');
		if(empty($cpage))
			$cpage = 1;

		//$cpage = \Pagination::findCurrentPage();
		$filter = ['PARENT_ID=?', $id ];
		$options = [ 'order' => 'CLASS,NAME' ];

		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		$page = $obj->paginate(($cpage - 1), 8, $filter, $options);

		if($page['count'] > 1) {
			// build page links
			$pages = new \Pagination($page['total'], $page['limit']);
			// add some configuration if needed
			$pages->setTemplate('partials/pagebrowser.html');
			$link = 'browse/';
			if(!empty($f3->get('PARAMS.id')))
				$link .= $id;
			$pages->setLinkPath($link);
			// for template usage, serve generated pagebrowser to the hive
			$f3->set('pagebrowser', $pages->serve());
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

		
		if($page['count'] > 1) {
			// build page links
			$pages = new \Pagination($page['total'], $page['limit']);
			// add some configuration if needed
			$pages->setTemplate('partials/pagebrowser.html');
			$link = 'search/';
			$pages->setLinkPath($link);
			// for template usage, serve generated pagebrowser to the hive
			$f3->set('pagebrowser', $pages->serve());
		}

		$f3->set('result', $page);
		$f3->set('search', $f3->get('SESSION.lastSearch'));
		$f3->set('content', 'search.html');
	}

	public function error($f3) {
		$f3->set('content', 'error.html');
	}

	private function pagination($f3, &$page) {
		$pg = [];
		
		for($i = 0; $i < $page['count']; $i++) {
			$x = [];
			$x['active'] = ($page['pos'] == $i);
			$x['value'] = $i;
			$x['url'] = $i;
			$pg[] = $x;
		}
		
		return $pg;
	}	
}
