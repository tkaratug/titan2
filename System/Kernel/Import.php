<?php
/*************************************************
 * Titan-2 Mini Framework
 * Import Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
namespace System\Kernel;

use System\Libs\Exception\ExceptionHandler;

class Import
{

	/**
	 * Include view file
	 *
	 * @param string $file
	 * @param array $data
	 * @return void
	 */
	public function view($file, $data = [])
	{
		$filePath = VIEW_DIR . $file . '.php';

		if (!file_exists($filePath))
            throw new ExceptionHandler('Dosya bulunamadı.', '<b>View : </b>' . $file);

        extract($data);
        require_once $filePath;
	}

	/**
	 * Include helper file
	 *
	 * @param string $file
	 * @return void
	 */
	public function helper($file)
	{
		$filePath = APP_DIR . 'Helpers/' . ucfirst($file) . '.php';

		if (!file_exists($filePath))
            throw new ExceptionHandler('Dosya bulunamadı.', '<b>Helper : </b>' . ucfirst($file));

        require_once $filePath;

	}

	/**
	 * Include model
	 *
	 * @param string $key
	 * @return void
	 */
	public static function model($key)
	{
		if (strpos($key, '.')) {
			$model 		= explode('.', $key);
			$file		= base64_decode($model[0]);
			$namespace	= base64_decode($model[1]);

			$filePath 	= MODEL_DIR . ucfirst($namespace) . '/' . ucfirst($file) . '.php';
			$class 		= 'App\\Models\\' . ucfirst($namespace) . '\\' . ucfirst($file);
		} else {
			$file		= base64_decode($key);
			$filePath 	= MODEL_DIR . ucfirst($file) . '.php';
			$class 		= 'App\\Models\\' . ucfirst($file);
		}

		if (!file_exists($filePath))
            throw new ExceptionHandler('Dosya bulunamadı.', '<b>Model : </b>' . ucfirst($file));

        require_once $filePath;
        return new $class;
	}

	/**
	 * Include custom file
	 *
	 * @param string $file
	 * @return mixed
	 */
	public static function file($file)
	{
		if (!file_exists($file . '.php'))
            throw new ExceptionHandler('Dosya bulunamadı.', '<b>File : </b>' . $file . '.php');

		return require $file . '.php';
	}

	/**
	 * Include config file
	 *
	 * @param string $file
	 * @return mixed
	 */
	public static function config($file)
	{
		if (!file_exists(APP_DIR . 'Config/' . ucwords($file) . '.php'))
            throw new ExceptionHandler('Dosya bulunamadı.', '<b>Config : </b>' . APP_DIR . 'Config/' . ucfirst($file) . '.php');

		return require APP_DIR . 'Config/' . ucwords($file) . '.php';
	}

}
