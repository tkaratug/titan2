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

Route::namespace('frontend')->group(function(){
	Route::get('/', 'Home@index');
});

Route::prefix('frontend')->namespace('frontend')->group(function(){
	Route::get('/', 'Home@index');
	Route::get('/home', 'Home@index');
});

Route::prefix('backend')->namespace('backend')->middleware(['auth'])->group(function(){
	Route::get('/', 'Dashboard@index');
	Route::get('/dashboard', 'Dashboard@index');
});