<?php
/*************************************************
 * Titan-2 Mini Framework
 * Cookie Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Cookie;

use System\Kernel\Import;
use System\Kernel\Exception\ExceptionHandler;

class Cookie
{

	// Config variable
	private $config;

	// Security seperator
	private $seperator;

	// Cookie path
	private $path;

	// Cookie domain
	private $domain;

	// Cookie secure (https)
	private $secure;

	// Cookie http only
	private $httpOnly;

	public function __construct()
	{
		// Get cookie config
		$this->config = Import::config('app');

		// Initializing settings
		$this->seperator 	= $this->config['cookie']['seperator'];
		$this->path 		= $this->config['cookie']['path'];
		$this->domain 		= $this->config['cookie']['domain'];
		$this->secure 		= $this->config['cookie']['secure'];
		$this->httpOnly		= $this->config['cookie']['http_only'];
	}

	/**
	 * Set cookie path
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function path($path)
	{
		if (!is_string($path))
			return false;

		$this->path = $path;
	}

	/**
	 * Set domain
	 *
	 * @param string $domain
	 * @return void
	 */
	public function domain($domain)
	{
		if (!is_string($domain))
			return false;

		$this->domain = $domain;
	}

	/**
	 * Set secure (https)
	 *
	 * @param string $domain
	 * @return void
	 */
	public function secure($secure = false)
	{
		if (!is_bool($secure))
			return false;

		$this->secure = $secure;
	}

	/**
	 * Set http only
	 *
	 * @param string $domain
	 * @return void
	 */
	public function httpOnly($http = false)
	{
		if (!is_bool($http))
			return false;

		$this->httpOnly = $http;
	}

	/**
	 * Set cookie
	 *
	 * @param string $name
	 * @param string $value
	 * @param integer $time
	 * @return void
	 */
	public function set($name, $value, $time = 0)
	{
		if ($time > 0)
			$time = time() + ($time*60*60);

		if ($this->config['cookie']['cookie_security'] === true)
			setcookie($name, $value . $this->seperator . md5($value . $this->config['cookie']['encryption_key']), $time, $this->path, $this->domain, $this->secure, $this->httpOnly);
		else
			setcookie($name, $value, $time, $this->path, $this->domain, $this->secure, $this->httpOnly);
	}

	/**
	 * Get cookie value
	 *
	 * @param string $name
	 * @return string
	 */
	public function get($name)
	{
		if ($this->has($name)) {
			if ($this->config['cookie']['cookie_security'] === true) {
				$parts = explode($this->seperator, $_COOKIE[$name]);
				if (md5($parts[0] . $this->config['cookie']['encryption_key']) == $parts[1])
					return $parts[0];
				else
					throw new ExceptionHandler("Hata", "Cookie içeriği değiştirilmiş");
			} else {
				return $_COOKIE[$name];
			}
		}
	}

	/**
	 * Delete cookie
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function delete($name)
	{
		if ($this->has($name)) {
			unset($_COOKIE[$name]);
			setcookie($name, '', time() - 3600, $this->path, $this->domain);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if cookie exist
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function has($name)
	{
		if (isset($_COOKIE[$name]))
			return true;
		else
			return false;
	}

}