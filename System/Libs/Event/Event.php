<?php
/*************************************************
 * Titan-2 Mini Framework
 * Event Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Event;

use Config;
use System\Libs\Exception\ExceptionHandler;

class Event
{

	/**
	 * Trigger an event
	 *
	 * @param string $event
	 * @param string $method
	 * @param array $params
	 * @return void
	 */
	public function trigger($event, $method = 'handle', $params = [])
	{
		$listeners 	= Config::get('app', 'listeners');

		foreach ($listeners[$event] as $listener) {

			if (!class_exists($listener))
				throw new ExceptionHandler('Listener sınıfı bulunamadı.', $listener);

			if (!method_exists($listener, $method))
				throw new ExceptionHandler('Listener sınıfına ait method bulunamadı.', $listener . '::' . $method . '()');
				
			call_user_func_array(array(new $listener, $method), $params);
			
		}
	}

}