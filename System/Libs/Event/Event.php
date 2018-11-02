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

use System\Libs\Exception\ExceptionHandler;

class Event
{
	/**
	 * @var string
	 */
	private $action 	= 'handle';

	/**
	 * @var array
	 */
	private $params 	= [];

	/**
	 * @var string
	 */
	private $listeners 	= null;

	/**
	 * Set listener
	 *
	 * @param string $event
	 * @return $this
	 */
	public function listener($event)
	{
		$listeners 	= config('services.listeners');

		$this->listeners = $listeners[$event];

		return $this;
	}

	/**
	 * Set action
	 *
	 * @param string $action
	 * @return $this
	 */
	public function action($action)
	{
		$this->action = $action;

		return $this;
	}

	/**
	 * Set parameters
	 *
	 * @param array $params
	 * @return $this
	 */
	public function params(array $params)
	{
		$this->params = $params;

		return $this;
	}

	/**
	 * Fire event
	 */
	public function fire()
	{
		foreach ($this->listeners as $listener) {
			if (!class_exists($listener))
				throw new ExceptionHandler('Listener sınıfı bulunamadı.', $listener);

			if (!method_exists($listener, $this->action))
				throw new ExceptionHandler('Listener sınıfına ait method bulunamadı.', $listener . '::' . $this->action . '()');

			call_user_func_array(array(new $listener, $this->action), $this->params);
		}
	}

}
