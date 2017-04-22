<?php
/*************************************************
 * Titan-2 Mini Framework
 * Model Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Database;

use System\Kernel\Import;

class Model
{
	// Model Collection
	private $modelCollection = [];

	/**
	 * Return instance of model
	 *
	 * @param string $model
	 * @param string $namespace
	 * @return object
	 */
	public function run($model, $namespace = null)
	{
		if (array_key_exists($model, $this->modelCollection))
			return $this->modelCollection[$model];
		else {
			$db = Import::model($model, $namespace);
			$this->modelCollection[$model] = new $db;

			return $this->modelCollection[$model];
		}
	}

}