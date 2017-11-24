<?php
namespace Controllers;

class Functions extends Controller {

	public function thumb($f3) {
		$id = $f3->get('PARAMS.id');
		$size = $f3->get('PARAMS.size');
		if(empty($size))
			$size = 64;

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

	public function status($f3) {
		$output; $return_var;
		exec('/mnt/www/dlna/cmd status', $output, $return_var);
		echo $output[0];
	}

	public function rescan($f3){
		$output; $return_var;
		exec('/mnt/www/dlna/cmd rescan', $output, $return_var);
		echo $output[0];
	}

	public function restart($f3){
		$output; $return_var;
		exec('/mnt/www/dlna/cmd restart', $output, $return_var);
		echo $output[0];
	}
}