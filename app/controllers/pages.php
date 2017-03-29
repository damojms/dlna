<?php
namespace Controllers;
class Pages extends Controller {

	public function index($f3) {
		$res = $f3->DB->exec('SELECT * FROM OBJECTS WHERE CLASS="item.videoItem" AND REF_ID IS NULL ORDER BY ID DESC LIMIT 10;');
		// $obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		// $page = $obj->paginate(0, 10, array('CLASS'=>'item.videoItem', 'REF_ID' => 'NULL'), array('order' => 'ID DESC'));
		$f3->set('result', $res);
		$f3->set('content', 'recent.html');
	
		echo \Template::instance()->render('index.html');
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
	
		echo \Template::instance()->render('index.html');
	}

	public function thumb($f3) {
		$id = $f3->get('PARAMS.id');
		$res = $f3->DB->exec('SELECT a.PATH FROM OBJECTS o, DETAILS d, ALBUM_ART a WHERE OBJECT_ID=? AND d.ID=DETAIL_ID AND a.ID=d.ALBUM_ART ', $id);
		$pth = $res[0]['PATH'];

		if(file_exists($pth)) {
			$img = new \Image($pth, false, '');
			$img->resize(64, 64, FALSE, FALSE);
			$img->render();
		} else {
			header('Content-type: image/png');
			readfile('../ui/images/video.png');
		}
	}

	public function detail($f3) {
		$id = $f3->get('PARAMS.id');
		// $det = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		// $det->load(array('OBJECT_ID', $id));

		// $f3->set('detail', $det);
		$f3->set('content', 'detail.html');

		echo \Template::instance()->render('index.html');
	}

	public function search($f3) {
		$search = $f3->get('PARAMS.search');

		$obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
		$page = $obj->paginate(0, 8, array('NAME=?', $search));

		$f3->set('result', $page);

		$f3->set('content', 'search.html');
		echo \Template::instance()->render('index.html');
	}

	public function error($f3) {
		$f3->set('content', 'error.html');
		echo \Template::instance()->render('index.html');
	}

	public function status($f3) {
		$output; $return_var;
		exec('/mnt/www/dlna/cmd status', $output, $return_var);
		echo $output[0];
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
