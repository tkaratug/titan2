<?php
/*************************************************
 * Titan-2 Mini Framework
 * HTTP Request Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Http;

class Request
{
	// 'Get' Variables
	protected $getVars;

	// 'Post' Variables
	protected $postVars;

	// 'Cookie' Variables
	protected $cookieVars;

	// 'File' Variables
	protected $filesVars;

	// 'Server' Variables
	protected $serverVars;

	// 'Global' Variables
	protected $globalVars;

	public function __construct()
	{
		$this->createGlobals();
	}

	// Create Global Variables
	private function createGlobals()
	{
		foreach ($GLOBALS as $key => $value) {
			switch ($key) {
				case '_GET':
					$this->getVars = $value;
					break;
				case '_POST':
					$this->postVars = $value;
					break;
				case '_COOKIE' :
					$this->cookieVars = $value;
					break;
				case '_FILES' :
					$this->filesVars = $value;
					break;
				case '_SERVER' :
					$this->serverVars = $value;
					break;
				case 'GLOBALS' :
					$this->globalVars = $value;
					break;
			}
		}
	}

	/**
	 * Get Server Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function server($param = null)
	{
		if (is_null($param))
			return $this->serverVars;
		else
			return $this->serverVars[$param];
	}

	/**
	 * Get HTTP Headers
	 *
	 * @param string
	 * @return string|array
	 */
	public function headers($param = null)
	{
		$headers = getallheaders();

		if (is_null($param))
			return getallheaders();
		else {
			$headerResponse = [];
			foreach ($headers as $key => $val) {
				$headerResponse[$key] = $val;
			}
			return $headerResponse[ucwords($param)];
		}
	}

	/**
	 * Get Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function get($param = null, $filter = false)
	{
		if (is_null($param))
			return $this->getVars;
		else
			return $this->filter($this->getVars[$param], $filter);
	}

	/**
	 * Post Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function post($param = null, $filter = false)
	{
		if (is_null($param))
			return $this->postVars;
		else
			return $this->filter($this->postVars[$param], $filter);
	}

	/**
	 * Put Variables
	 *
	 * @param string $param
	 * @param boolean $filter
	 */
	public function put($param = null, $filter = false)
	{
		parse_str(file_get_contents("php://input"), $_PUT);

		if ($param == null)
			return $_PUT;
		else
			return $this->filter($_PUT[$param], $filter);
	}

	/**
	 * Delete Variables
	 *
	 * @param string $param
	 * @param boolean $filter
	 */
	public function delete($param = null, $filter = false)
	{
		parse_str(file_get_contents("php://input"), $_DELETE);

		if ($param == null)
			return $_DELETE;
		else
			return $this->filter($_DELETE[$param], $filter);
	}

	/**
	 * Get Cookie Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function cookie($param = null)
	{
		if (is_null($param))
			return $this->cookieVars;
		else
			return $this->cookieVars[$param];
	}

	/**
	 * Get File Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function files($param = null)
	{
		if (is_null($param))
			return $this->filesVars;
		else
			return $this->filesVars[$param];
	}

	/**
	 * Get Globals
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function globals($param = null)
	{
		if (is_null($param))
			return $this->globalVars;
		else
			return $this->globalVars[$param];
	}

	/**
	 * Get Request Method
	 *
	 * @return string
	 */
	public function getRequestMethod()
	{
		return $this->server('REQUEST_METHOD');
	}

	/**
	 * Get Script Name
	 *
	 * @return string
	 */
	public function getScriptName()
	{
		return $this->server('SCRIPT_NAME');
	}

	/**
	 * Get Request Scheme
	 *
	 * @return string
	 */
	public function getScheme()
	{
		return stripos($this->server('SERVER_PROTOCOL'), 'https') === true ? 'https' : 'http';
	}

	/**
	 * Get Http Host
	 *
	 * @return string
	 */
	public function getHost()
	{
		return $this->server('HTTP_HOST');
	}

	/**
	 * Get Request URI
	 *
	 * @return string
	 */
	public function getRequestUri()
	{
		return $this->server('REQUEST_URI');
	}

	/**
	 * Get Base URL
	 *
	 * @param string $url
	 * @return string
	 */
	public function baseUrl($url = null)
	{
 		if (is_null($url))
	    	return $this->getScheme() . '://' . $this->getHost();
		else
			return $this->getScheme() . '://' . rtrim($this->getHost(), '/') . '/' . $url;
	}

	/**
	 * Get URL Segments
	 *
	 * @return array
	 */
	public function segments()
	{
		return explode('/', trim(parse_url($this->getRequestUri(), PHP_URL_PATH), '/'));
	}

	/**
	 * Get specified segment from URL
	 *
	 * @param int $index
	 * @return string
	 */
	public function getSegment($index)
	{
		return $this->segments()[$index];
	}

	/**
	 * Get current URL Segment
	 *
	 * @return string
	 */
	public function currentSegment()
	{
		$numSegment = count($this->segments());
		return $this->getSegment($numSegment - 1);
	}

	/**
	 * Get Query String Elements
	 *
	 * @param boolean $array (If true then return as an array)
	 * @return string|array
	 */
	public function getQueryString($array = false)
	{
		if ($array === false) {
			return $this->server('QUERY_STRING');
		} else {
			$qsParts	= explode('&', $this->server('QUERY_STRING'));
			$qsArray 	= [];

			foreach ($qsParts as $key => $value) {
				$qsItems 				= explode('=', $value);
				$qsArray[$qsItems[0]] 	= $qsItems[1];
			}

			return $qsArray;
		}
	}

	/**
	 * Get Content Type
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return split(',', $this->headers()['Accept'])[0];
	}

	/**
	 * Get Locales
	 *
	 * @return array
	 */
	public function getLocales()
	{
		return explode(',', preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim($this->server('HTTP_ACCEPT_LANGUAGE')))));
	}

	/**
	 * Get the locale
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this->getLocales()[0];
	}

	/**
	 * Check if the requested method is of specified type
	 *
	 * @return string
	 */
	public function isMethod($method)
	{
		return $this->getRequestMethod() === strtoupper($method);
	}


	/**
	 * Check if the request is an ajax request
	 *
	 * @return bool
	 */
	public function isAjax()
	{
		if (null !== $this->server('HTTP_X_REQUESTED_WITH') && strtolower($this->server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest')
			return true;
		else
			return false;
	}

	/**
	 * Check if the http request is secure
	 *
	 * @return bool
	 */
	public function isSecure()
	{
		if (null !== $this->server('https'))
            return true;

        if (null !== $this->server('HTTP_X_FORWARDED_PROTO') && $this->server('HTTP_X_FORWARDED_PROTO') == 'https')
            return true;

        return false;
	}

	/**
	 * Check if the visitor is robot
	 *
	 * @return boolean
	 */
	public function isRobot()
	{
		if (null !== $this->server('HTTP_USER_AGENT') && preg_match('/bot|crawl|slurp|spider/i', $this->server('HTTP_USER_AGENT')))
			return true;
		else
			return false;
	}

	/**
	 * Check if the visitor is mobile
	 *
	 * @return boolean
	 */
	public function isMobile()
	{
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $this->server("HTTP_USER_AGENT"));
	}

	/**
	 * Check is referral
	 *
	 * @return boolean
	 */
	public function isReferral()
	{
		if (null !== $this->server('HTTP_REFERER') || $this->server('HTTP_REFERRER') == '')
			return false;
		else
			return true;
	}

	/**
	 * Return Http Referrer
	 *
	 * @return string
	 */
	public function getReferrer()
	{
		return ($this->isReferral()) ? trim($this->server('HTTP_REFERRER')) : '';
	}

	/**
	 * Get client IP
	 *
	 * @return string
	 */
	public function getIp()
	{
		$ipaddress = '';

		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if (getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if (getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if (getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if (getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if (getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';

		return $ipaddress;
	}

	/**
	 * Filter inputs
	 *
	 * @param string $data
	 * @param boolean $filter
	 * @return string | null
	 */
	public function filter($data = null, $filter = false)
	{
		if (is_null($data))
			return null;
		else
			return ($filter === true) ? $this->xssClean($data) : trim($data);
	}

	/**
	 * Clear XSS
	 *
	 * @param string $data
	 * @return string
	 */
	public function xssClean($data)
	{
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
        	// Remove really unwanted tags
        	$old_data = $data;
        	$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
	}

	/**
	 * Clean HTML
	 *
	 * @param string $data
	 * @return string
	 */
	public function htmlClean($data)
	{
		return strip_tags(htmlentities(trim(stripslashes($data)), ENT_NOQUOTES, "UTF-8"));
	}

}