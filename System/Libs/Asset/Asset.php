<?php
/*************************************************
 * Titan-2 Mini Framework
 * Asset Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Asset;

use System\Libs\Exception\ExceptionHandler;

class Asset
{

	// Asset Selector
	protected $assets = [];

	/**
	 * Set css file
	 *
	 * @param string $file
	 * @param boolean $remote
	 * @return void
	 */
	public function css($file, $remote = false)
	{
		if ($remote === true) {
			$url 	= $file;
			$this->assets['header']['css'][] = '<link rel="stylesheet" type="text/css" href="' . $url . '">';
		} else {
			$part	= explode('.css', $file);
			$url 	= RESOURCES_DIR . 'css/' . $part[0] . '.css' . $part[1];

			// Check if css file is exist
			if (!file_exists(RESOURCES_DIR . 'css/' . $part[0] . '.css'))
				throw new ExceptionHandler('Dosya bulunamadı', '<b>CSS : </b>' . $file);

			$this->assets['header']['css'][] = '<link rel="stylesheet" type="text/css" href="' . $url . '">';
		}
	}

	/**
	 * Set js file
	 *
	 * @param string $file
	 * @param string $location
	 * @param boolean $remote
	 * @return void
	 */
	public function js($file, $location = 'footer', $remote = false)
	{
		if ($remote === true) {
			$url = $file;
			$this->assets[$location]['js'][] = '<script type="text/javascript" src="' . $url . '"></script>';
		} else {
			$url = RESOURCES_DIR . 'js/' . $file;

			if (!file_exists($url))
				throw new ExceptionHandler('Dosya bulunamadı', '<b>JS : </b>' . $file);

			$this->assets[$location]['js'][] = '<script type="text/javascript" src="' . $url . '"></script>';
		}
	}

	/**
	 * Set meta tags
	 *
	 * @param string $meta_name
	 * @param string $meta_content
	 * @return void
	 */
	public function meta($meta_name, $meta_content)
	{
		$this->assets['header']['meta'][] = '<meta name="' . $meta_name . '" content="' . $meta_content . '">';
	}

	/**
	 * Set Page Title
	 *
	 * @param string $title
	 * @return void
	 */
	public function title($title)
	{
		$this->assets['header']['title'] = '<title>' . $title . '</title>';
	}

	/**
	 * Get css files
	 *
	 * @return array | null
	 */
	public function getCss()
	{
		if (array_key_exists('header', $this->assets) && array_key_exists('css', $this->assets['header']))
			return $this->assets['header']['css'];
		else
			return null;
	}

	/**
	 * Get js files
	 *
	 * @param string $location
	 * @return array | null
	 */
	public function getJs($location = 'footer')
	{
		if (array_key_exists($location, $this->assets) && array_key_exists('js', $this->assets[$location]))
			return $this->assets[$location]['js'];
		else
			return null;
	}

	/**
	 * Get meta tags
	 *
	 * @return array | null
	 */
	public function getMeta()
	{
		if (array_key_exists('header', $this->assets) && array_key_exists('meta', $this->assets['header']))
			return $this->assets['header']['meta'];
		else
			return null;
	}
	
	/**
	 * Get page title
	 *
	 * @return string | null
	 */
	public function getTitle()
	{
		if (array_key_exists('header', $this->assets) && array_key_exists('title', $this->assets['header']))
			return $this->assets['header']['title'];
		else
			return null;
	}

}