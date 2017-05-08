<?php
/*************************************************
 * Titan-2 Mini Framework
 * SMTP Mail Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Mail;

use Config;

require 'PHPMailerAutoload.php';

class Mail extends \PHPMailer
{

	protected $config = [];

	public function __construct()
	{
		parent::__construct();

		// E-Mail Config
		$this->config = Config::get('app', 'email');

		// Setting SMTP Protocol
		$this->isSMTP();

		// Default SMTP Auth
		$this->SMTPAuth	= true;

		// Default HTML Format
		$this->isHTML(true);

		// SMTP Server
		$this->Host 	= $this->config['server'];

		// Username
		$this->Username = $this->config['username'];

		// User Password
		$this->Password = $this->config['userpass'];

		// Default Port
		$this->Port 	= $this->config['port'];

		// Default Charset
		$this->CharSet 	= $this->config['charset'];
	}

	/**
	 * Mail settings
	 *
	 * @param array $config
	 * @return void
	 */
	public function init($config)
	{
		if(array_key_exists('charset', $config))
			$this->CharSet 	= $config['charset'];

		if(array_key_exists('server', $config))
			$this->Host 	= $config['server'];

		if(array_key_exists('port', $config))
			$this->Port 	= $config['port'];
		
		if(array_key_exists('username', $config))
			$this->Username = $config['username'];

		if(array_key_exists('userpass', $config))
			$this->Password = $config['userpass'];

		if(array_key_exists('isHtml', $config))
			$this->isHTML($config['isHtml']);
	}

	/**
	 * Set SMTP host
	 *
	 * @param string $host
	 * @return void
	 */ 
	public function setHost($host)
	{
		$this->Host = $host;
	}

	/**
	 * Get SMTP host
	 *
	 * @return string
	 */
	public function getHost()
	{
		return $this->Host;
	}

	/**
	 * Set SMTP port
	 *
	 * @param integer $port
	 * @return void
	 */
	public function setPort($port)
	{
		$this->Port = $port;
	}

	/** 
	 * Get SMTP port
	 *
	 * @return integer
	 */
	public function getPort()
	{
		return $this->Port;
	}

	/**
	 * Set SMTP username
	 *
	 * @param string $username
	 * @return void
	 */
	public function setUsername($username)
	{
		$this->Username = $username;
	}

	/** 
	 * Get SMTP username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->Username;
	}

	/**
	 * Set SMTP password
	 *
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password)
	{
		$this->Password = $password;
	}

	/**
	 * Get SMTP password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->Password;
	}

	/**
	 * Set SMTP charset
	 *
	 * @param string $charset
	 * @return void
	 */
	public function setCharset($charset)
	{
		$this->Charset = $charset;
	}

	/** 
	 * Get SMTP charset
	 *
	 * @return string
	 */
	public function getCharset()
	{
		return $this->Charset;
	}

	/**
	 * Set SMTP is Html
	 *
	 * @param boolean $html
	 * @return void
	 */
	public function setHtml($html)
	{
		if (is_bool($html))
			$this->isHTML($html);
	}

	/**
	 * Get SMTP is Html
	 *
	 * @return boolean
	 */
	public function getHtml()
	{
		return $this->isHTML();
	}

	/**
	 * Set mail subject
	 *
	 * @param string $subject
	 * @return void
	 */
	public function subject($subject)
	{
		$this->Subject = $subject;
	}

	/** 
	 * Set body of mail
	 *
	 * @param string $body
	 * @return void
	 */
	public function body($body)
	{
		$this->Body = $body;
	}

	/** 
	 * Set alt body of mail
	 *
	 * @param string $altBody
	 * @return void
	 */
	public function altBody($altBody)
	{
		$this->AltBody = $altBody;
	}

	/** 
	 * Get SMTP errors
	 *
	 * @return string
	 */
	public function getError()
	{
		return $this->ErrorInfo;
	}

	/** 
	 * Call PhpMailer's methods
	 *
	 * @param string $method
	 * @param string $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		return call_user_func_array([$this, $method], $args);
	}

	function __destruct()
	{
		parent::__destruct();
	}

}