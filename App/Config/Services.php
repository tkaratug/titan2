<?php
/*************************************************
 * Titan-2 Mini Framework
 * Providers, facades, middlewares and listeners
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
return [

	/**
	 * Service Providers
	 */
	'providers'		=> [
		'Import'			=> System\Kernel\Import::class,
		'Config'			=> System\Kernel\Config::class,
		'Request'			=> System\Libs\Http\Request::class,
		'Response'			=> System\Libs\Http\Response::class,
		'Curl'				=> System\Libs\Http\Curl::class,
		'Restful'			=> System\Libs\Http\Restful::class,
		'Jwt'				=> System\Libs\Http\Jwt::class,
		'Event'				=> System\Libs\Event\Event::class,
		'View'				=> System\Libs\View\View::class,
		'Session'			=> System\Libs\Session\Session::class,
		'Cookie'			=> System\Libs\Cookie\Cookie::class,
		'Cache'				=> System\Libs\Cache\Cache::class,
		'Benchmark'			=> System\Libs\Benchmark\Benchmark::class,
		'Log'				=> System\Libs\Log\Log::class,
		'Validation'		=> System\Libs\Validation\Validation::class,
		'Pagination'		=> System\Libs\Pagination\Pagination::class,
		'Mail'				=> System\Libs\Mail\Mail::class,
		'Html'				=> System\Libs\Html\Html::class,
		'Form'				=> System\Libs\Html\Form::class,
		'DB'				=> System\Libs\Database\DB::class,
		'Model'				=> System\Libs\Database\Model::class,
		'Date'				=> System\Libs\Date\Date::class,
		'Upload'			=> System\Libs\Upload\Upload::class,
		'Image'				=> System\Libs\Image\Image::class,
		'Hash'				=> System\Libs\Hashing\Hash::class,
	],

	/**
	 * Facades
	 */
	'facades'		=> [
		'Import'			=> System\Facades\Import::class,
		'Config'			=> System\Facades\Config::class,
		'Request'			=> System\Facades\Request::class,
		'Response'			=> System\Facades\Response::class,
		'Curl'				=> System\Facades\Curl::class,
		'Restful'			=> System\Facades\Restful::class,
		'Jwt'				=> System\Facades\Jwt::class,
		'Event'				=> System\Facades\Event::class,
		'View'				=> System\Facades\View::class,
		'Session'			=> System\Facades\Session::class,
		'Cookie'			=> System\Facades\Cookie::class,
		'Cache'				=> System\Facades\Cache::class,
		'Benchmark'			=> System\Facades\Benchmark::class,
		'Log'				=> System\Facades\Log::class,
		'Validation'		=> System\Facades\Validation::class,
		'Pagination'		=> System\Facades\Pagination::class,
		'Mail'				=> System\Facades\Mail::class,
		'Html'				=> System\Facades\Html::class,
		'Form'				=> System\Facades\Form::class,
		'DB'				=> System\Facades\DB::class,
		'Model'				=> System\Facades\Model::class,
		'Date'				=> System\Facades\Date::class,
		'Upload'			=> System\Facades\Upload::class,
		'Image'				=> System\Facades\Image::class,
		'Hash'				=> System\Facades\Hash::class,
	],

	/**
	 * Middlewares
	 */
	'middlewares'	=> [
		'default'	=> [],

		'manual'	=> [
			'Auth'			=> App\Middlewares\Auth::class,
		]
	],

	/**
	 * Listeners
	 */
	'listeners'		=> [
		'sampleEvent'		=> [
			App\Listeners\SampleListener::class,
		],
	]

];
