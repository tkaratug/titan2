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

	// Default Namespace
	private $namespace		 = null;

	/**
	 * Return instance of model
	 *
	 * @param string $model
	 * @param string $namespace
	 * @return object
	 */
	public function run($model, $namespace = null)
	{
		if ($namespace !== null)
			$this->namespace = $namespace;

		$model = base64_encode($model);

		if ($this->namespace !== null) {
			$namespace	= base64_encode($this->namespace);
			$key 		= $model . '.' . $namespace;
		} else {
			$key		= $model;
		}

		// Reset namespace
		$this->namespace = null;

		if (array_key_exists($key, $this->modelCollection))
			return $this->modelCollection[$key];

        $db = Import::model($key);
        $this->modelCollection[$key] = $db;

        return $this->modelCollection[$key];
	}

	/**
	 * Set namespace
	 * 
	 * @param string $namespace
	 * @return $this
	 */
	public function namespace($namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

}
