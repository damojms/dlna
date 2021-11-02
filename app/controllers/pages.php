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
        if(empty($cpage) OR $cpage < 0)
            $cpage = 1;

	$limit = 8;
	$cpage--;
	$p = $cpage * $limit;

	$omap = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
	$page['total'] = $omap->count("CLASS='item.videoItem' AND REF_ID IS NULL");
	$page['count'] = $page['total'] / $limit;
	$page['limit'] = $limit;
	$sql = <<<SQL
SELECT o.ID, o.NAME, o.CLASS, o.REF_ID, o.OBJECT_ID, d.TIMESTAMP, d.SIZE, d.TITLE 
FROM OBJECTS AS o 
LEFT JOIN DETAILS AS d ON o.DETAIL_ID = d.ID
WHERE CLASS='item.videoItem' AND REF_ID IS NULL
ORDER BY d.TIMESTAMP DESC
SQL;
	$sql .= " LIMIT {$p}, {$limit}";

	$obj = $f3->DB->exec($sql);
        // $obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
	// die($f3->DB->log());
        // $page = $obj->paginate($cpage, $limit, [ 'CLASS=? AND REF_ID IS NULL', 'item.videoItem' ], ['order' => 'ID DESC']);
        $f3->set('result', $obj);
        $f3->set('page', $page);

        $this->pagination($f3, 'recent/', $page);

        $f3->set('content', 'recent.html');
    }

    public function browse($f3) {
        if($f3->DEBUG > 0) {
            $dbBar = $f3->get('DBBar');
            if(isset($dbBar['messages']))
                $dbBar['messages']->info('Browsing');
        }

        $id = $f3->get('PARAMS.id');
        if(empty($id)) 
            $id = 0;

        $cpage = $f3->get('PARAMS.page');
        if(empty($cpage))
            $cpage = 1;

        $filter = ['PARENT_ID=?', $id ];
        $options = [ 'order' => 'CLASS,NAME' ];

        $obj = new \DB\SQL\Mapper($f3->DB, 'OBJECTS');
        $page = $obj->paginate(($cpage - 1), 8, $filter, $options);
        $link = 'browse/';

        if(!empty($f3->get('PARAMS.id')))
            $link .= $id;

        $this->pagination($f3, $link, $page);

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

        if(($key = $f3->get('OMDBKEY')) != '' && $obj->CLASS == 'item.videoItem') {
            $web = \Web::instance();
            $title = $obj->NAME;
            $uri = "http://www.omdbapi.com/?";
            $matches = [];
            // Extract year from title
            $pos = strpos($title, " [");
            if($pos !== FALSE) {
                //die(var_dump($pos));
                $t = substr($title, 0, $pos);
                $y = substr($title, ($pos + 2), 4);
                if($f3->DEBUG > 0) {
                    $f3->DBBar['messages']->info("Found Movie: {$t}:{$y}");
                }
                $t = urlencode($t);
                $uri .= "t=$t&y=$y";
            } else if(preg_match('/(.*) S(\d+)E(\d+).*/', $title, $matches)) {
                if($f3->DEBUG > 0) {
                    $m = var_export($matches, TRUE);
                    $f3->DBBar['messages']->info("Found Series: {$m}");
                }
                $t = urlencode($matches[1]);
                $uri .= "t={$t}&type=series";
            }

            $uri .= "&apikey=$key";

            if($f3->DEBUG > 0) {
                $f3->DBBar['messages']->info("URI: {$uri}");
            }

            $omdb = $web->request($uri);
            $f3->set('omdb', json_decode($omdb['body'], true));
            if($f3->DEBUG > 0) {
                $o = var_export($omdb, TRUE);
                $f3->DBBar['messages']->info("Found OMDB: {$o}");
            }
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

        $this->pagination($f3, 'search/', $page);

        $f3->set('result', $page);
        $f3->set('search', $f3->get('SESSION.lastSearch'));
        $f3->set('content', 'search.html');
    }

    public function error($f3) {
        $f3->set('content', 'error.html');
    }

    private function pagination($f3, $link, &$page) {
        if($page['count'] > 1) {
            // build page links
            $pages = new \Pagination($page['total'], $page['limit']);
            // add some configuration if needed
            $pages->setTemplate('partials/pagebrowser.html');
            $pages->setLinkPath($link);
            // for template usage, serve generated pagebrowser to the hive
            $f3->set('pagebrowser', $pages->serve());
        }
    }   
}
