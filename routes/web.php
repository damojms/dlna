<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
use App\objects;

Route::get('/', function () {

	$objs = new objects();
	$ob = $objs->mostRecent();

    return view('welcome')->with('objects', $ob);
});
