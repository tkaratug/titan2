<?php
/*************************************************
 * Titan-2 Mini Framework
 * Session Flash Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Session;

use System\Libs\Session\Session;

class Flash
{

	/**
	 * Set flash message
	 *
	 * @param string $message
	 * @param string nullable $url
	 * @return void
	 */
	public function set($message, $url = null)
	{
		Session::set('flash', $message);

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
	public function get()
	{
		$flash = Session::get('flash');

		Session::delete('flash');

		return $flash;
	}

	/**
	 * Check if the flash message exists
	 *
	 * @return boolean
	 */
	public function has()
	{
		return Session::has('flash');
	}

}