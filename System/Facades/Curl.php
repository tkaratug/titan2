<?php
/*************************************************
 * Titan-2 Mini Framework
 * Facade of Curl Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Facades;

use System\Facades\Facade;

class Curl extends Facade
{

	/**
	 * Get the registered name of the component.
	 * 
	 * @param string
	 */
	protected static function getFacadeAccessor()
	{
		return 'Curl';
	}

}