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
		if (is_null($param)) {
			return $this->serverVars;
		}
		
		return $this->serverVars[$param] ?? null;
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

		if (is_null($param)) {
			return getallheaders();
		}

        $headerResponse = [];

        foreach ($headers as $key => $val) {
            $headerResponse[$key] = $val;
        }

        return $headerResponse[ucwords($param)];
	}

	/**
	 * Get All Inputs
	 *
	 * @param boolean $filter
	 * @return array
	 */
	public function all($filter = true)
	{
		return $this->filter($_REQUEST, $filter);
	}

	/**
	 * Get Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function get($param = null, $filter = true)
	{
		if (is_null($param)) {
			return $this->getVars;
		}

        return isset($this->getVars[$param]) ? $this->filter($this->getVars[$param], $filter) : false;
	}

	/**
	 * Post Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function post($param = null, $filter = true)
	{
		if (is_null($param)) {
			return $this->postVars;
		}

        return isset($this->postVars[$param]) ? $this->filter($this->postVars[$param], $filter) : false;
	}

	/**
	 * Put Variables
	 *
	 * @param string $param
	 * @param boolean $filter
	 */
	public function put($param = null, $filter = true)
	{
		parse_str(file_get_contents("php://input"), $_PUT);

		if ($param == null) {
			return $_PUT;
		}

        return isset($_PUT[$param]) ? $this->filter($_PUT[$param], $filter) : false;
	}

	/**
	 * Patch Variables
	 *
	 * @param string $param
	 * @param boolean $filter
	 * @return string|array
	 */
	public function patch($param = null, $filter = true)
	{
		parse_str(file_get_contents('php://input'), $_PATCH);

		if ($param == null) {
			return $_PATCH;
		}

        return isset($_PATCH[$param]) ? $this->filter($_PATCH[$param], $filter) : false;
	}

	/**
	 * Delete Variables
	 *
	 * @param string $param
	 * @param boolean $filter
	 */
	public function delete($param = null, $filter = true)
	{
		parse_str(file_get_contents("php://input"), $_DELETE);

		if ($param == null) {
			return $_DELETE;
		}

        return isset($_DELETE[$param]) ? $this->filter($_DELETE[$param], $filter) : false;
	}

	/**
	 * Get Cookie Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function cookie($param = null)
	{
		if (is_null($param)) {
			return $this->cookieVars;
		}

        return isset($this->cookieVars[$param]) ? $this->cookieVars[$param] : false;
	}

	/**
	 * Get File Variables
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function files($param = null)
	{
		if (is_null($param)) {
			return $this->filesVars;
		}

        return isset($this->filesVars[$param]) ? $this->filesVars[$param] : false;
	}

	/**
	 * Get Globals
	 *
	 * @param string $param
	 * @return string|array
	 */
	public function globals($param = null)
	{
		if (is_null($param)) {
			return $this->globalVars;
		}

        return isset($this->globalVars[$param]) ? $this->globalVars[$param] : false;
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
 		if (is_null($url)) {
	    	return $this->getScheme() . '://' . $this->getHost();
 		}

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
	 * @return string|null
	 */
	public function getSegment($index)
	{
		return isset($this->segments()[$index]) ? $this->segments()[$index] : null;
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
		}

        $qsParts	= explode('&', $this->server('QUERY_STRING'));
        $qsArray 	= [];

        if (!empty(array_filter($qsParts))) {
	        foreach ($qsParts as $key => $value) {
	            $qsItems 				= explode('=', $value);
	            $qsArray[$qsItems[0]] 	= $qsItems[1];
	        }
	    }

        return $qsArray;
	}

	/**
	 * Make query string from a multi-dimensional array.
	 * 
	 * @param array $array
	 * @return string|null
	 */
	public function makeQueryString($array) {
		if (!is_array($array)) {
			return null;
		}

		$queryString = '';
		foreach ($array as $key => $val) {
			$queryString .= "{$key}={$val}&";
		}

		return rtrim($queryString, '&');
	}

	/**
	 * Get Content Type
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return explode(',', $this->headers()['Accept'])[0];
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
     * @param string $method
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
		return null !== $this->server('HTTP_X_REQUESTED_WITH') && strtolower($this->server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
	}

	/**
	 * Check if the http request is secure
	 *
	 * @return bool
	 */
	public function isSecure()
	{
		if (null !== $this->server('HTTPS')) {
            return true;
		}

        if (null !== $this->server('HTTP_X_FORWARDED_PROTO') && $this->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            return true;
        }

        return false;
	}

	/**
	 * Check if the visitor is robot
	 *
	 * @return boolean
	 */
	public function isRobot()
	{
		return null !== $this->server('HTTP_USER_AGENT') && preg_match('/bot|crawl|slurp|spider/i', $this->server('HTTP_USER_AGENT'));
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
		if (null !== $this->server('HTTP_REFERER') || $this->server('HTTP_REFERER') == '') {
			return false;
		}

        return true;
	}

	/**
	 * Return Http Referrer
	 *
	 * @return string
	 */
	public function getReferrer()
	{
		return ($this->isReferral()) ? trim($this->server('HTTP_REFERER')) : '';
	}

	/**
	 * Get client IP
	 *
	 * @return string
	 */
	public function getIp()
	{
		if (getenv('HTTP_CLIENT_IP')) {
			return getenv('HTTP_CLIENT_IP');
		}

		if (getenv('HTTP_X_FORWARDED_FOR')) {
			return getenv('HTTP_X_FORWARDED_FOR');
		}

		if (getenv('HTTP_X_FORWARDED')) {
			return getenv('HTTP_X_FORWARDED');
		}

		if (getenv('HTTP_FORWARDED_FOR')) {
			return getenv('HTTP_FORWARDED_FOR');
		}

		if (getenv('HTTP_FORWARDED')) {
			return getenv('HTTP_FORWARDED');
		}

		if (getenv('REMOTE_ADDR')) {
			return getenv('REMOTE_ADDR');
		}

		return 'UNKNOWN';
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
		if (is_null($data)) {
			return null;
		}

        if (is_array($data)) {
            return $filter === true ? array_map([$this, 'xssClean'], $data) : array_map('trim', $data);
        }

        return $filter === true ? $this->xssClean($data) : trim($data);
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
