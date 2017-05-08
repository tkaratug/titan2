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

		if (file_exists($filePath)) {
			extract($data);
			require_once $filePath;
		} else {
			throw new ExceptionHandler('Dosya bulunamadı.', '<b>View : </b>' . $file);			
		}
	}

	/**
	 * Include helper file
	 *
	 * @param string $file
	 * @return void
	 */
	public function helper($file)
	{
		$filePath = APP_DIR . 'Helpers/' . $file . '.php';

		if (file_exists($filePath))
			require_once $filePath;
		else
			throw new ExceptionHandler('Dosya bulunamadı.', '<b>Helper : </b>' . $file);
			
	}

	/**
	 * Include model file
	 *
	 * @param string $file
	 * @return void
	 */
	public static function model($file, $namespace = null)
	{
		if (is_null($namespace)) {
			$filePath 	= MODEL_DIR . $file . '.php';
			$class 		= 'App\\Models\\' . $file;
		} else {
			$filePath 	= MODEL_DIR . ucfirst($namespace) . '/' . $file . '.php';
			$class 		= 'App\\Models\\' . ucfirst($namespace) . '\\' . $file;
		}

		if (file_exists($filePath)) {
			require_once $filePath;
			return new $class;
		} else
			throw new ExceptionHandler('Dosya bulunamadı.', '<b>Model : </b>' . $file);
			
	}

	/**
	 * Include custom file
	 *
	 * @param string $file
	 * @return mixed
	 */
	public static function file($file)
	{
		if (file_exists($file . '.php'))
			return require $file . '.php';
		else
			throw new ExceptionHandler('Dosya bulunamadı.', '<b>File : </b>' . $file . '.php');
			
		
	}

	/**
	 * Include config file
	 *
	 * @param string $file
	 * @return mixed
	 */
	public static function config($file)
	{
		if (file_exists(APP_DIR . 'Config/' . ucwords($file) . '.php'))
			return require APP_DIR . 'Config/' . ucwords($file) . '.php';
		else
			throw new ExceptionHandler('Dosya bulunamadı.', '<b>Config : </b>' . APP_DIR . 'Config/' . $file . '.php');
			
	}

}