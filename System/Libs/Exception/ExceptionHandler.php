<?php
/*************************************************
 * Titan-2 Mini Framework
 * Exception Handler Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Exception;

use Exception;

class ExceptionHandler
{
	public function __construct($title, $body)
	{
		throw new Exception(strip_tags($title . ': ' . $body), 1);		
	}
}