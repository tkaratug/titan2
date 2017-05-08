<?php
/*************************************************
 * Titan-2 Mini Framework
 * Routes
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
use System\Libs\Router\Router as Route;

Route::set404(function(){
	header('HTTP/1.1 404 Not Found');
	View::render('errors.404');
});

Route::get('/', 'Home@index', ['namespace' => 'Frontend']);

Route::group('/frontend', function(){

	Route::get('/', 'Home@index', ['namespace' => 'Frontend']);
	Route::get('/home', 'Home@index', ['namespace' => 'Frontend']);

});

Route::group('/backend', function(){

	Route::get('/', 'Dashboard@index', ['namespace' => 'Backend']);
	Route::get('/dashboard', 'Dashboard@index', ['namespace' => 'Backend']);

}, ['middleware' => ['Auth']]);