<?php
/*************************************************
 * Titan-2 Mini Framework
 * Config Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Kernel;

class Config
{

	/** 
	 * Get config item
	 *
	 * @param string $file
	 * @param string $key
	 * @param string $val
	 * @return array|string
	 */
	public function get($file, $key = null, $val = null)
	{
		$config = Import::config($file);

		if (is_null($key)) {
			return $config;
		} else {
			if (is_null($val)) {
				return $config[$key];
			} else {
				return $config[$key][$val];
			}
		}
	}

}