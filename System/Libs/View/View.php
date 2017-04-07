<?php
/*************************************************
 * Titan-2 Mini Framework
 * View Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\View;

use Windwalker\Edge\Cache\EdgeFileCache;
use Windwalker\Edge\Edge;
use Windwalker\Edge\Loader\EdgeFileLoader;

class View
{
	/**
	 * Render View File
	 *
	 * @param string $file
	 * @param array $vars
	 * @param boolean $cache
	 * @return void
	 */
	public function render($file, $vars = [], $cache = false)
	{
		$paths 	= [APP_DIR . 'Views'];

		$loader = new EdgeFileLoader($paths);
		$loader->addFileExtension('.blade.php');

		if ($cache === false)
			$edge = new Edge($loader);
		else
			$edge = new Edge($loader, null, new EdgeFileCache(APP_DIR . '/Storage/Cache'));

		echo $edge->render($file, $vars);

	}
}