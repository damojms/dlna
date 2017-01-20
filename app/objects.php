<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class objects extends Model
{
    public $timestamps = false;
    protected $table = 'OBJECTS';

    public function mostRecent() {
    	return parent::where([
				[ 'CLASS', 'like', 'item.videoItem' ]
			])->orderBy('ID', 'desc')
			->groupBy('DETAIL_ID')
			->take(10)
			->get();
    }
}
