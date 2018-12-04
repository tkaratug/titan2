<?php
/*************************************************
 * Titan-2 Mini Framework
 * Session Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
namespace System\Libs\Session;

class Session
{
	// Session config items
	private $config;

	public function __construct()
	{
		// Getting session config items
		$this->config = config('app.session');

		// Checking cookie_httponly setting
		if ($this->config['cookie_httponly'] === true)
			ini_set('session.cookie_httponly', 1);

		// Checking use_only_cookies setting
		if ($this->config['use_only_cookies'] === true)
			ini_set('session.use_only_cookies', 1);

		// Setting max. lifetime
		ini_set('session.gc_maxlifetime', $this->config['lifetime']);
		session_set_cookie_params($this->config['lifetime']);

		// Initializing
		$this->init();
	}

	/**
	 * Initialize Session
	 *
	 * @return void
	 */
	private function init()
	{
		if (!isset($_SESSION)) {
			session_start();
			$this->set('session_hash', $this->generateHash());
		} else {
			if ($this->get('session_hash') != $this->generateHash())
				$this->destroy();
		}
	}

	/**
	 * Set session variable
	 *
	 * @param string $storage
	 * @param mixed $content
	 * @return void
	 */
	public function set($storage, $content = null)
	{
		if (is_array($storage)) {
			foreach ($storage as $key => $value) {
				$_SESSION[$key] = $value;
			}
		} else {
			$_SESSION[$storage] = $content;
		}
	}

	/**
	 * Get session variable
	 *
	 * @param string $storage
	 * @return mixed
	 */
	public function get($storage = null)
	{
		if (is_null($storage))
			return $_SESSION;

        return $_SESSION[$storage];
	}

	/**
	 * Check if session variable is exist
	 *
	 * @param string $storage
	 * @return boolean
	 */
	public function has($storage)
	{
		return isset($_SESSION[$storage]);
	}

	/**
	 * Delete session
	 *
	 * @param string nullable $storage
	 * @return void
	 */
	public function delete($storage = null)
	{
		if (is_null($storage))
			session_unset();
		else
			unset($_SESSION[$storage]);
	}

	/**
	 * Session destroy
	 *
	 * @return void
	 */
	public function destroy()
	{
		session_destroy();
	}

	/**
	 * Set flash message
	 *
	 * @param string $message
	 * @param string nullable $url
	 * @return void
	 */
	public function setFlash($message, $url = null)
	{
		$this->set('flash', $message);

		if (!is_null($url)) {
			header("Location: $url");
			exit();
		}
	}

	/**
	 * Get flash message
	 *
	 * @return string
	 */
	public function getFlash()
	{
		$flash = $this->get('flash');
		$this->delete('flash');

		return $flash;
	}

	/**
	 * Check if the flash message exists
	 *
	 * @return boolean
	 */
	public function hasFlash()
	{
		return $this->has('flash');
	}

	/**
	 * Generate Hash for Hijacking Security
	 *
	 * @return string
	 */
	private function generateHash()
	{
		if (array_key_exists('REMOTE_ADDR', $_SERVER) && array_key_exists('HTTP_USER_AGENT', $_SERVER))
			return md5(sha1(md5($_SERVER['REMOTE_ADDR'] . $this->config['encryption_key'] . $_SERVER['HTTP_USER_AGENT'])));

        return md5(sha1(md5($this->config['encryption_key'])));
	}

}
