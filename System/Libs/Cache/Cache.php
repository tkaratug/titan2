<?php
/*************************************************
 * Titan-2 Mini Framework
 * Cache Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
namespace System\Libs\Cache;

use System\Kernel\Exception\ExceptionHandler;

class Cache
{

	// Name of cache file
	private $filename;

	// Cache path
	private $path;

	// Cache file extension
	private $extension;

	// Expire time
	private $expire;

	public function __construct()
	{
		// Getting cache config items
		$config				= config('app.cache');

		// Inıtializing
		$this->path 		= APP_DIR . $config['path'];
		$this->extension 	= $config['extension'];
		$this->expire 		= $config['expire'];
		$this->filename     = $config['filename'];
	}

	/**
	 * Write data into the cache file
	 *
	 * @param string $key
	 * @param string $data
	 * @param int $expiration
	 * @return void
	 */
	public function save($key, $data, $expiration = null)
	{
		if(is_null($expiration))
			$expiration = $this->expire;

		$storedData = [
			'time'		=> time(),
			'expire'	=> $expiration,
			'data'		=> serialize($data)
		];

		$cacheContent = $this->_loadCache();

		if (is_array($cacheContent) === true) {
			$cacheContent[$key] = $storedData;
		} else {
			$cacheContent = [$key => $storedData];
		}

		$cacheContent = json_encode($cacheContent);
		file_put_contents($this->_getCacheDir(), $cacheContent);
	}

	/**
	 * Read data from the cache file
	 *
	 * @param string $key
	 * @return string
	 */
	public function read($key, $filename = null)
	{
		$cacheContent = $this->_loadCache($filename);
		if (!isset($cacheContent[$key]['data']))
			return null;
		else
			return unserialize($cacheContent[$key]['data']);
	}

	/**
	 * Delete an item from cache file
	 *
	 * @param string $key
	 * @return string|bool
	 */
	public function delete($key)
	{
		$cacheContent = $this->_loadCache();

		if (is_array($cacheContent)) {
			if (isset($cacheContent[$key])) {
				unset($cacheContent[$key]);
				$cacheContent = json_encode($cacheContent);
				file_put_contents($this->_getCacheDir(), $cacheContent);
			} else {
				throw new ExceptionHandler("Hata", "delete() - Key {" . $key . "} bulunamadı");
			}
		}
	}

	/**
	 * Delete expired cached data
	 *
	 * @return int
	 */
	public function deleteExpiredCache()
	{
		$counter = 0;
		$cacheContent = $this->_loadCache();
		if (is_array($cacheContent)) {
			foreach ($cacheContent as $key => $value) {
				if ($this->_isExpired($value['time'], $value['expire']) === true) {
					unset($cacheContent[$key]);
					$counter++;
				}
			}

			if($counter > 0) {
				$cacheContent = json_encode($cacheContent);
				file_put_contents($this->_getCacheDir(), $cacheContent);
			}
		}
		return $counter;
	}

	/**
	 * Delete all cached datas
	 *
	 * @return void
	 */
	public function clear()
	{
		if (file_exists($this->_getCacheDir())) {
			$file = fopen($this->_getCacheDir(), 'w');
			fclose($file);
		}
	}

	/**
	 * Check if cached datas with $key exists
	 *
	 * @param string $key
	 * @return bool
	 */
	public function isCached($key)
	{
		$this->deleteExpiredCache();
		if ($this->_loadCache() != false) {
			$cacheContent = $this->_loadCache();
			return isset($cacheContent[$key]['data']);
		}
	}

	public function setFileName($filename)
	{
		$this->filename = $filename;
	}

	public function getFileName()
	{
		return $this->filename;
	}

	public function setPath($path)
	{
		$this->path = APP_DIR . $path;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * Check if cache directory exists
	 *
	 * @return string|bool
	 */
	private function _checkCacheDir()
	{
		if (!is_dir($this->getPath()) && !mkdir($this->getPath(), 0775, true)) {
			throw new ExceptionHandler("Hata", "Cache dizini oluşturulamadı" . $this->getPath());
		} elseif (!is_readable($this->getPath()) || !is_writable($this->getPath())) {
			if (!chmod($this->getPath(), 0775)) {
				throw new ExceptionHandler("Hata", $this->getPath() . " dizini okuma ve yazma izinlerine sahip olmalıdır");
			}
		} else {
			return true;
		}
	}

	/**
	 * Get cache directory
	 *
	 * @return string|bool
	 */
	private function _getCacheDir($filename = null)
	{
        if ($this->_checkCacheDir() !== true)
            return false;

        if (is_null($filename))
            $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($this->getFileName()));

        return $this->getPath() . '/' . $this->_hashFile($filename) . $this->getExtension();
	}

	/**
	 * Load cached file content
	 *
	 * @return string|bool
	 */
	private function _loadCache($filename = null)
	{
        if ($this->_getCacheDir() === false)
            return false;

        if (!file_exists($this->_getCacheDir($filename)))
            return false;

        $file = file_get_contents($this->_getCacheDir($filename));

        return json_decode($file, true);
	}

	/**
	 * Chech if cached data expired
	 *
	 * @param int $time
	 * @param int $expiration
	 * @return bool
	 */
	private function _isExpired($time, $expiration)
	{
        if ($expiration === 0)
            return false;

        return time() - $time > $expiration;
	}

	/**
	 * Hash cache file name
	 *
	 * @param string $filename
	 * @return string
	 */
	private function _hashFile($filename)
	{
		return md5($filename);
	}

}
