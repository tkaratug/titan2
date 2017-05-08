<?php
/*************************************************
 * Titan-2 Mini Framework
 * System Configurations
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
	 * General
	 */
	'general'		=> [
		'languages'					=> ['tr' => 'Turkish', 'en' => 'English'],
		'default_lang'				=> 'tr',
	],

	/*
	 * Session Settings
	 */
	'session'		=> [
		'encryption_key'			=> 'u2LMq1h4oUV0ohL9svqedoB5LebiIE4z',
		'cookie_httponly'			=> true,
		'use_only_cookies'			=> true,
		'lifetime'					=> 3600,
	],

	/*
	 * Cookie Settings
	 */
	'cookie'		=> [
		'encryption_key'			=> 'HXg1wuVjAOxR7AZZz4rGMbfVwN8nTY20',
		'cookie_security'			=> true,
		'http_only'					=> true,
		'secure'					=> false,
		'seperator'					=> '--',
		'path'						=> '/',
		'domain'					=> '',
	],

	/*
	 * Cache Settings
	 */
	'cache'			=> [
		'path'						=> 'Storage/Cache',
		'extension'					=> '.cache',
		'expire'					=> 604800,
	],

	/**
	 * SMTP Settings
	 */
	'email'			=> [
		'server'					=> '',
		'port'						=> 25,
		'username'					=> '',
		'userpass'					=> '',
		'charset'					=> 'utf-8',
	],

];