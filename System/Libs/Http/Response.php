<?php
/*************************************************
 * Titan-2 Mini Framework
 * Response Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Http;

class Response
{
	// Status Codes
	public $statusCodes = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	];

	/**
	 * Set Http Status Code
	 *
	 * @param int $code
	 * @return boolean
	 */
	public function setStatusCode($code)
	{
		return http_response_code($code);
	}

	/**
	 * Get Http Status Code
	 *
	 * @return int
	 */
	public function getStatusCode()
	{
		return http_response_code();
	}

	/** 
	 * Get Http Status Message
	 *
	 * @param int|null $code
	 * @return string
	 */
	public function getStatusMessage($code = null)
	{
		if (is_null($code))
			return $this->statusCodes[$this->getStatusCode()];
		else
			return $this->statusCodes[$code];
	}

	/**
	 * Json Response
	 *
	 * @param string $data
	 * @param int $code
	 * @return string
	 */
	public function json($data = null, $code = 200)
	{
		// Remove all headers
		header_remove();

		// Set Http status code
		$this->setStatusCode($code);

		header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
		header('Content-type: application/json');
		header('Status: '.$this->statusCodes[$code]);
		
		return json_encode([
			'status'	=> $code < 300,
			'message'	=> $data
		]);
	}

}